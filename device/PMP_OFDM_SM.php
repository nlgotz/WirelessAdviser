<?php
header( "Cache-Control: no-cache, must-revalidate" );
header( "Expires: Sat, 3 Jul 1965 23:50:00 GMT" );
?>

<html>
<head>

<link rel="shortcut icon" href="images/cambium-icon.png"/>
<link rel="stylesheet" type="text/css" media="screen" href="../cambium.css"/>

<script type="text/javascript" language="javascript" src="../libs/wafe_utils.js"></script>

<script type="text/javascript" language="javascript" src="https://www.google.com/jsapi"></script>

<script type="text/javascript" language="javascript" src="../libs/jquery-1.6.3.js"></script>
<script type="text/javascript" language="javascript" src="../libs/jquery.tablesorter-2.0.3.js"></script> 
<script type="text/javascript" language="javascript" src="../libs/jquery_ready_handler.js"></script>

<script type="text/javascript" language="javascript" src="../libs/log4javascript.js"></script>

<script type="text/javascript" language="javascript">	
///////////////////////////////////////////////////////////////////
// 
// Global Variables
//
///////////////////////////////////////////////////////////////////
// Global variables for functions.
<?php echo 'var device_id = "' . $_GET['device_id'] . '";' ?>

var refreshTime = 60;	// In seconds.
var statusChartHeight = 370;
var statusChartWidth = 545;
var probeChartHeight = 80;
var probeChartWidth = 525;

var basicInfoUrl = "model/getbasicinfofordevice.php?device_id=" + device_id;
var detailedInfoUrl = "model/getdetailedinfofordevice.php?device_id=" + device_id;
var eventsUrl = "model/geteventsfordevice.php?device_id=" + device_id;
var statusUrl = "model/getstatusfordevice.php?device_id=" + device_id;
var probeUrl = "model/getprobesfordevice.php?device_id=" + device_id;

var chartCache = new Array();
var currentEventSort;
var checkedEventList = new Array();

// Load up key utilities used by the page.
google.load('visualization', '1', { 'packages':['corechart'] } );

var log = log4javascript.getNullLogger();
// NOTE: To enable logging (will show a popup window with log messages), uncomment the following line.
// log = log4javascript.getDefaultLogger();


// Organization of group data.
var generalInformationGrouping = new Array();

generalInformationGrouping[ "Basic Info" ] = new Array();
generalInformationGrouping[ "Basic Info" ][ "sysName" ] = "Name";
generalInformationGrouping[ "Basic Info" ][ "sysLocation" ] = "Location";
generalInformationGrouping[ "Basic Info" ][ "sysContact" ] = "Contact";

generalInformationGrouping[ "Version Info" ] = new Array();
generalInformationGrouping[ "Version Info" ][ "softwareVersion" ] = "Software Version";
generalInformationGrouping[ "Version Info" ][ "hardwareVersion" ] = "Hardware Version";
generalInformationGrouping[ "Version Info" ][ "boardType" ] = "Board Type";

generalInformationGrouping[ "Status" ] = new Array();
generalInformationGrouping[ "Status" ][ "ethernetLinkStatus" ] = "Ethernet Link Status";
generalInformationGrouping[ "Status" ][ "rfLinkStatus" ] = "RF Link Status";

generalInformationGrouping[ "Device" ] = new Array();
generalInformationGrouping[ "Device" ][ "macAddress" ] = "MAC Address";
generalInformationGrouping[ "Device" ][ "registeredTo" ] = "Registered To";

generalInformationGrouping[ "Radio" ] = new Array();
generalInformationGrouping[ "Radio" ][ "rfColorCode" ] = "RF Color Code";
generalInformationGrouping[ "Radio" ][ "rfFrequency" ] = "RF Center Frequency (MHz)";
generalInformationGrouping[ "Radio" ][ "radioPowerLevel" ] = "RF Power Level";
generalInformationGrouping[ "Radio" ][ "bandwidthDownlinkBurstAllocation" ] = "Band D/L Burst Allocation (Kbits)";
generalInformationGrouping[ "Radio" ][ "bandwidthDownlinkSustainedRate" ] = "Band D/L Sustained Rate (Kbps)";
generalInformationGrouping[ "Radio" ][ "bandwidthUplinkBurstAllocation" ] = "Band U/L Burst Allocation (Kbits)";
generalInformationGrouping[ "Radio" ][ "bandwidthUplinkSustainedRate" ] = "Band U/L Sustained Rate (Kbps)";


//Organization of probe data.
var probeDataGrouping = new Array();
probeDataGrouping[ "ethernetRxRate" ] = "Ethernet Rx Rate";
probeDataGrouping[ "ethernetRxErrors" ] = "Ethernet Rx Errors";
probeDataGrouping[ "ethernetRxDiscards" ] = "Ethernet Rx Discards";
probeDataGrouping[ "ethernetTxRate" ] = "Ethernet Tx Rate";
probeDataGrouping[ "ethernetTxErrors" ] = "Ethernet Tx Errors";
probeDataGrouping[ "ethernetTxDiscards" ] = "Ethernet Tx Discards";
probeDataGrouping[ "rfRxRate" ] = "RF Rx Rate";
probeDataGrouping[ "rfRxErrors" ] = "RF Rx Errors";
probeDataGrouping[ "rfRxDiscards" ] = "RF Rx Discards";
probeDataGrouping[ "rfTxRate" ] = "RF Tx Rate";
probeDataGrouping[ "rfTxErrors" ] = "RF Tx Errors";
probeDataGrouping[ "rfTxDiscards" ] = "RF Tx Discards";
probeDataGrouping[ "airDelay" ] = "Air Delay";



///////////////////////////////////////////////////////////////////
// 
// Functions
//
///////////////////////////////////////////////////////////////////
/**
 * Create the necessary div tags based on the probeDataGrouping array.
 */
function initializeProbes() 
{
	log.info( "device.php: device " + device_id + ", intialize probe charts." );
	for ( var internalProbeName in probeDataGrouping )
	{
		$('#probe_charts').append( '<div id="' + internalProbeName + '"></div><hr>' );
	}
}
	

/**
 * Update the device page header information with latest status and basic info.
 */
function refreshHeaderInfo()
{
	log.info( "device.php: device " + device_id + ", updating header information.  Calling url " + basicInfoUrl );

	$.ajax({
		url: basicInfoUrl,
		cache: false,
		success: function( jsonData ) 
	{	
		log.info( "device.php: device " + device_id + ", basic info json data received: " + jsonData );
		var headerInfo = jQuery.parseJSON( jsonData );
		log.info( "device.php: device " + device_id + ", basic info json data parsed: " + headerInfo );			
		
		// Other useful info.
		var headerText = '<table bgcolor="' + getColorForSeverityId( headerInfo.severity_id ) + '" width="100%">';
		headerText += '<tr border="0"><td width="40" align="center" valign="top"><img src="../images/' + headerInfo.device_type + '.png"/></td>';
		headerText += '<td><b>' + headerInfo.display_name + '</b>';
		headerText += '<br><span style="font-size:x-small;"><b>Device ID: </b>' + headerInfo.device_id + ', <b>Device IP: </b><a href="http://' + headerInfo.ip_address + '">' + headerInfo.ip_address + '</a><b>, Device Type: </b>' + headerInfo.device_type + '</i></span></td></tr>';
		headerText += '</table>';
		
		// Update the header div.
		$(document).attr( "title", headerInfo.display_name );
		$('#device_title').replaceWith( '<div id="device_title" class="device_header">' + headerText + '</div>' );
		
		delete( headerText );
	}
	});
}


/**
 * Update general info tab with the "report" or snapshot information.
 */
function refreshGeneralInfo() 
{
	log.info( "device.php: device " + device_id + ", update general information.  Calling url " + detailedInfoUrl );

	// Get the scrollposition so we can restore later.
	var scrollValue = $("#general").scrollTop();

	// Call the DAO object.
	$.ajax({
		url: detailedInfoUrl,
		cache: false,
		success: function( jsonData ) 
	{
	
		log.info( "device.php: device " + device_id + ", general info json data received: " + jsonData );
		var deviceInfo = jQuery.parseJSON( jsonData );
		log.info( "device.php: device " + device_id + ", general info json data parsed: " + deviceInfo );

		if ( deviceInfo.length == 0 )
		{
			log.warn( "device.php: No general data is available for " + device_id );
			$('#general').replaceWith( '<div id="general" class="device_panel">No general data is available at this time.</div>' );
			return;
		}	

		$('#general').replaceWith( '<div id="general" class="device_panel"></div>' );
		var tableString = '<table class="tablesorter">';
		for ( var groupName in generalInformationGrouping )
		{
			tableString += '<thead><tr><th colspan="2">' + groupName + '</th></tr></thead>';
			tableString += '<tbody>';
			for( var internalFieldName in generalInformationGrouping[ groupName ] )
			{
				tableString += '<tr><td id="' + internalFieldName + '">' + generalInformationGrouping[ groupName ][ internalFieldName ] + '</td><td>' + deviceInfo [ internalFieldName ] + '</td></tr>';		
			}
			tableString += '</tbody>';			
		}
		tableString += '</table>';		

		$('#general').replaceWith(  '<div id="general" class="device_panel">' + tableString + '</div>' );
		$("#general").scrollTop( scrollValue );
		
		delete( tableString );
	}
	});
}
		

/**
 * List of events filtered by the device.
 */
function refreshEvents()
{
	log.info( "device.php: loading events.  Calling url " + eventsUrl );

	// Get the scrollposition so we can restore later.
	var scrollValue = $("#event_table").scrollTop();	
	
	$.ajax({
		url: eventsUrl,
		cache: false,
		success: function( jsonData ) 
	{
		var eventText = '<div id="event_table" class="device_panel">';

		log.info( "device.php: json data received: " + jsonData );
		if ( jsonData.indexOf( "ERROR" ) != -1 )
		{
			eventText += "Error getting event data from the database.";
		}
		else
		{
			var events = jQuery.parseJSON( jsonData );
			log.info( "device.php: json data parsed: " + events );

			if ( events.eventList.length == 0 )
			{
				log.warn( "device.php: No event data is available for " + device_id );
				$('#event_table').replaceWith( '<div id="event_table" class="device_panel">No event data is available at this time.</div>' );
				return;
			}						
			
			// Remember selections.
			$("input:checkbox:checked").each( function() 
			{
				checkedEventList.push( this.name );
			});

			eventText += '<table id="eventTable" class="tablesorter"><thead><tr><th>Time</th><th>Severity</th><th>Description</th><th>Clear</th></tr></thead><tbody>';
			
			for( index in events.eventList )
			{
				var event_time = events.eventList[index].event_time;
				var device_id = events.eventList[index].device_id;
				var severity_id = events.eventList[index].severity_id;
				var description = events.eventList[index].description;
				var display_time = event_time.match( /^(\d+-\d+-\d+ \d+:\d+:\d+)/ )[0];

				log.debug( "device.php: device " + device_id + ", adding row to table: " + event_time + ", " + device_id + ", " + severity_id + ", " + description + ", " + display_time );
				
				var tdTag = '<td style="background-color:' + getColorForSeverityId( severity_id ) + ';">';		
				var rowString = '<tr>' + tdTag + display_time + '</td>';
				rowString += tdTag + getNameForSeverityId( severity_id ) + '</td>' + tdTag + description + '</td>';
				rowString += tdTag + '<input type="checkbox" name="' + event_time + "__" + device_id + '"/></td></tr>';
				eventText += rowString;
			
			}
			eventText += "</tbody></table>";
		}
		eventText += "</div>";
		log.debug( "device.php: device " + device_id + ", updated event table text is: " + eventText);	
		
		$('#event_table').replaceWith( eventText );
		delete( eventText );
		
		// Add the table sorter and recover current sorting to use later.
		$("#eventTable").tablesorter( 
		{ 
			headers: { 
				0:{ sorter:"shortDate"},
				1:{ sorter:"text"},
				2:{ sorter:"text"},
				3:{ sorter:false}
			},
			sortList: currentEventSort 
		} ).bind( "sortEnd", function( sorter ) 
		{
			currentEventSort = sorter.target.config.sortList;
		});
		
		// Finally, reapply checks and scroll value.
		var eventName;
		while( (eventName = checkedEventList.pop() ) != null )
		{
			log.debug( "device.php: reapplying checkbox to " + eventName );
			$('input[name="' + eventName + '"]').prop( "checked", true );
		}
		$("#event_table").scrollTop( scrollValue );
		
		log.info( "device.php: events loaded." );
	}
	});
}

/**
 * Delete all selected events.
 */
function deleteSelectedEventRows() 
{
	var deleteCount = 0;
	
	if ( $("input:checkbox:checked").length > 0 )
	{
		$('body').css( 'cursor', 'wait' );	
		$("input:checkbox:checked").each( function() 
		{
			deleteCount++;
			var url = 'model/deleteevent.php?event_id=' + this.name;
			log.info( "device.php: calling delete url: " + url );
			$.get( 'model/deleteevent.php?event_id=' + this.name, function( jsonData ) 
			{
				if ( jsonData.indexOf( "ERROR" ) != -1 ) 
				{
					alert( "Error deleting rows..." );
				} 
				else 
				{
					log.info( "device.php: device " + device_id + " delete was successful." );
				}
				deleteCount--;
				log.debug( "device.php: device " + device_id + " delete count is now " + deleteCount );
				if ( deleteCount <= 0 )
				{
					// Refresh the view.
					refreshEvents();
					refreshHeaderInfo();	
					$('body').css( 'cursor', 'auto' );					
				}
			});
		});
	}
}


/**
 * Select all events
 */
function selectAllEventRows() 
{
	$("input[type=checkbox]").attr('checked', true );
}	


/**
 * Deselect all events.
 */
function deSelectAllEventRows() 
{
	$("input[type=checkbox]").attr('checked', false );
}


/**
 * Refresh status info for the devices.
 */
function refreshStatus() 
{	
	log.info( "device.php: device " + device_id + ", refreshing status.  Calling url " + statusUrl );

	var statusData = $.ajax({
		url: statusUrl,
		dataType:"json",
		async: false
	}).responseText;
	
	log.info( "device.php: device " + device_id + ", status json data received: " + statusData );
	var statusDataJson = jQuery.parseJSON( statusData );
	log.info( "device.php: device " + device_id + ", status json data parsed: " + statusDataJson );			

	if ( statusDataJson.statusList.length == 0 )
	{
		log.warn( "device.php: No status data is available for " + device_id );
		$('#status').replaceWith( '<div id="status" class="device_panel">No status data is available at this time.</div>' );
		return;
	}	
	
	// Setup the data table.
	var data = new google.visualization.DataTable();
	data.addColumn( 'string', 'Timestamp' );
	data.addColumn( 'number', 'Up' );
	data.addColumn( 'number', 'Down' );

	// Using a stacked chart here, so if the status is good, mark up as 1, and down as 0.
	// If the status is bad, up is 0, and down is 1.
	for( index in statusDataJson.statusList )
	{
		var display_time = statusDataJson.statusList[index].timestamp.split( "." )[0];
		log.debug( "device.php: device " + device_id + ", " + display_time + ", " + statusDataJson.statusList[index].value );
		if( statusDataJson.statusList[index].value == 1 )
		{
			data.addRow( [ display_time, 1, 0 ] );
		} 
		else
		{
			data.addRow( [ display_time, 0, 1 ] );
		}
	}

	// To avoid memory leaks, reuse the chart object.  Load from cache - create if not there.
	var chartHandle;
	if ( "status" in chartCache )
	{
		chartHandle = chartCache[ 'status' ];
	}
	else
	{
		chartHandle = new google.visualization.ColumnChart( document.getElementById('status') );
		chartCache[ 'status' ] = chartHandle;
	}
	
	// Draw the chart.
	chartHandle.draw( data, { height:statusChartHeight, width:statusChartWidth, title: 'Status Poll (last 25 polls)', hAxis: { textPosition: 'none' }, legend: 'right', isStacked: true,	colors: [ waGreen, waRed ], vAxis: { textColor: '#ffffff' } } );
	delete( data );
}		
		 

/**
 * Refresh the probe data.
 * 
 */
function refreshProbes() 
{
	log.info( "device.php: device " + device_id + ", refreshing probe charts.  Calling url " + probeUrl );

	// Get the scrollposition so we can restore later.
	var scrollValue = $("#probe_container").scrollTop();	
	
	var statsData = $.ajax({
		url: probeUrl,
		dataType:"json",
		async: false
	}).responseText;
	
	log.info( "device.php: device " + device_id + ", probe json data received: " + statsData );
	var statsDataJson = jQuery.parseJSON( statsData );
	log.info( "device.php: device " + device_id + ", probe json data parsed: " + statsDataJson );	

	// Warn the user if there is no data available.
	if ( statsDataJson.stats.length == 0 )
	{
		log.warn( "device.php: device " + device_id + ",  No probe data is available for " + device_id );
		$('#probe_messages').text( "No probe data is available at this time." );
	}
	else
	{
		// reinitialize the probe divs.
		$('#probe_messages').text( "" );
	}
	
	for( index in statsDataJson.stats )
	{
		var pollId = statsDataJson.stats[index].pollId;
		if ( document.getElementById( pollId ) == null )
		{
			log.error( "device.php: No div target available for " + pollId );
			continue;
		}
		var pollData = statsDataJson.stats[index].pollData;
		log.debug( "device.php: device " + device_id + ", " + pollId );
		log.debug( "device.php: device " + device_id + ", " + pollData );
			
		var data = new google.visualization.DataTable();
		data.addColumn( 'string', 'Timestamp' );
		data.addColumn( 'number', pollId );
		for ( index in pollData )
		{
			log.debug( "device.php: device " + device_id + ", " + pollData[index].timestamp + ", " + pollData[index].value );
			data.addRow( [ convertEpochToReadableTime( pollData[index].timestamp ), parseInt( pollData[index].value) ] );			
		}
		
		if ( data.getNumberOfRows() == 0 )
		{
			log.warn( "device.php: 0 rows of probe data for: " + pollId );
		}
		
		// To avoid memory leaks, reuse the chart object.  Load from cache - create if not there.
		var chartHandle = null;
		if ( pollId in chartCache )
		{
			chartHandle = chartCache[ pollId ];
			log.debug( "device.php: found chart in cache for " + pollId );
		}
		else
		{
			chartHandle =  new google.visualization.AreaChart( document.getElementById( pollId ) );
			chartCache[ pollId ] = chartHandle;
			log.debug( "device.php: No chart found in cache for " + pollId + ", created." );			
		}		

		chartHandle.draw(data, { height: probeChartHeight, width: probeChartWidth, title: probeDataGrouping[ pollId ], hAxis: { textPosition: 'none' }, legend: 'none', colors: [ waGreen ] });	
		delete( data );
	}
	
	$("#probe_container").scrollTop( scrollValue );
}


/**
 * Refresh all page info.
 */
function refreshPage()
{
	refreshHeaderInfo(); 
	refreshGeneralInfo(); 
	refreshEvents();	
	refreshStatus();
	refreshProbes();
}


/**
 * Do any initialization necessary.
 */
function initializePage()
{
	initializeProbes();
}


/**
 * Setup tab structure and initialize individual panels.
 */
$(document).ready( function() 
{	
	// Refresh on a tab click.
	$("#showGeneralInfo").click( function() 
	{
		refreshHeaderInfo();
		refreshGeneralInfo();
	});	
	
	$("#showStatus").click( function() 
	{
		refreshHeaderInfo();	
		refreshStatus();
	});	

	$("#showEvents").click( function() 
	{
		refreshHeaderInfo();	
		refreshEvents();
	});	
	
	$("#showProbes").click( function() 
	{
		refreshHeaderInfo();	
		refreshProbes();
	});		
	

	// Refresh all data in the tabs.
	log.info( "device.php: device " + device_id + ", gathering all info for device " + device_id );
	initializePage();
	refreshPage();

	// Periodically refresh.
	setInterval( function() 
	{		
		log.debug( "device.php: device " + device_id + ", refreshing all views for device " + device_id );
		log.debug( "device.php: device " + device_id + "===================" );
		log.debug( "device.php: device " + device_id + "There are now " + $('*').size() + " DOM objects." );
		log.debug( "device.php: device " + device_id + "There are now " + $("div").size() + " div objects" );
		log.debug( "device.php: device " + device_id + "There are now " + $("table").size() + " table objects." );
		log.debug( "device.php: device " + device_id + "There are now " + $("tr").size() + " tr objects." );
		log.debug( "device.php: device " + device_id + "There are now " + $("td").size() + " td objects." );
		log.debug( "device.php: device " + device_id + "Total page size is " + document.documentElement.innerHTML.length + " bytes." );
		refreshPage();
	}, refreshTime * 1000 );
});

</script>

</head>
<body class="device_body">
<div class="device_container">

<div id="device_title" class="device_header"></div>
<ul class="tabs">
	<li><a href="#tab1" id="showGeneralInfo">General Information</a></li>
	<li><a href="#tab2" id="showStatus">Poll Status</a></li>	
	<li><a href="#tab3" id="showEvents">Events</a></li>
	<li><a href="#tab4" id="showProbes">Probes</a></li>
</ul>

<div class="device_tab_container">

	<!-- Device Info Panel -->
	<div id="tab1" class="tab_content">
		<div id="general" class="device_panel"></div>
		<input type="submit" value="Refresh" onclick="refreshGeneralInfo();refreshHeaderInfo();"/>
	</div>
	
	<!-- Status Panel -->
	<div id="tab2" class="tab_content">
		<div id="status" class="device_panel"></div>
		<input type="submit" value="Refresh" onclick="refreshStatus();refreshHeaderInfo();"/>		
	</div>
		
	<!-- Event Panel -->
    <div id="tab3" class="tab_content">
		<div id="event_table" class="device_panel"></div>
		<input type="submit" value="Refresh" onclick="refreshEvents();refreshHeaderInfo();"/>
		<input type="submit" value="Select All Rows" onclick="selectAllEventRows();"/>
		<input type="submit" value="Deselect All Rows" onclick="deSelectAllEventRows();"/>
		<input type="submit" value="Delete Selected Rows" onclick="deleteSelectedEventRows();refreshHeaderInfo();"/>		
	</div>
    	
	<!-- Probe Panel -->
	<div id="tab4" class="tab_content">
		<div id="probe_container" class="device_panel">
			<div id="probe_messages"></div>
			<div id="probe_charts"></div>
		</div>
		<input type="submit" value="Refresh" onclick="refreshProbes();refreshHeaderInfo();"/>		
	</div>
</div>

</div>

</body>
</html>
