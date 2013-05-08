//<![CDATA[
///////////////////////////////////////////////////////////////////
// 
// Global Variables
//
///////////////////////////////////////////////////////////////////
var refreshInterval = 2; // Every 2 minutes.
var dashletWidth = 395;
var dashletHeight = 280;	// Must equal .dashpanel height in cambium.css
var dashletChartArea = 200;
var dashletPieChartArea = 300;

var allEventsBySeverityChart = "";
var allSeveritiesByTypeChart = "";
var maxDeviceEventsBySeverityChart = "";
var maxDeviceSeveritiesByTypeChart = "";

// Load graph libs and initialize panels.
google.load('visualization', '1', { 'packages':['corechart'] } );
google.setOnLoadCallback( initializeAllEventsBySeverityPanel );
google.setOnLoadCallback( initializeAllSeveritiesByTypePanel );	
google.setOnLoadCallback( initializeMaxDeviceEventsBySeverityPanel );
google.setOnLoadCallback( initializeMaxDeviceSeveritiesByTypePanel );	


/**
 * Callback to initialize the chart and seed with initial data.
 */
function initializeAllEventsBySeverityPanel() 
{
	allEventsBySeverityChart = new google.visualization.PieChart( document.getElementById('all_events_by_severity') );
	refreshAllEventsBySeverity();
}


/**
 * Callback to initialize the chart and seed with initial data.
 */
function initializeAllSeveritiesByTypePanel() 
{
	allSeveritiesByTypeChart = new google.visualization.ColumnChart( document.getElementById('all_severities_by_type') );
	refreshAllSeveritiesByType();
}


/**
 * Callback to initialize the chart and seed with initial data.
 */
function initializeMaxDeviceEventsBySeverityPanel() 
{
	maxDeviceEventsBySeverityChart = new google.visualization.PieChart( document.getElementById('max_device_events_by_severity') );
	refreshMaxDeviceEventsBySeverity();
}


/**
 * Callback to initialize the chart and seed with initial data.
 */
function initializeMaxDeviceSeveritiesByTypePanel() 
{
	maxDeviceSeveritiesByTypeChart = new google.visualization.ColumnChart( document.getElementById('max_device_severities_by_type') );
	refreshMaxDeviceSeveritiesByType();
}


/**
 * Build pie chart with statuses of the devices.
 */
function refreshAllEventsBySeverity() 
{
	log.info( "dashboard.js: loading device status panel" );
	
	var jsonData = $.ajax({
		url: "model/getseveritycounts.php",
		dataType:"json",
		async: false,
		cache: false
	}).responseText;

	log.info( "dashboard.js: json data received: " + jsonData );
	var severities = jQuery.parseJSON( jsonData );
	log.info( "dashboard.js: json data parsed: " + severities );			
	
	// Iterate through the status list and build a per type count map.
	var severityCountMap = new Object();
	for( index in severities.severityCounts )
	{
		var severity_id = severities.severityCounts[index].severity_id;
		var count = severities.severityCounts[index].count;	
		severityCountMap[severity_id] = parseInt( count );
	}
	
	var data = new google.visualization.DataTable();
	data.addColumn( 'string', 'Status' );
	data.addColumn( 'number', 'Count' );
	
	for( var i = 5; i >= 0; i-- )
	{
		if ( typeof severityCountMap[i] == 'undefined' )
		{
			severityCountMap[i] = 0;
		}
		data.addRow( [ getNameForSeverityId( i ), severityCountMap[i] ] );
		log.debug( "dashboard.js: adding row for severity " + getNameForSeverityId( i ) + " - " + severityCountMap[i] );
	} 
	
	var options = { title: 'All Severities', 
					width: dashletWidth, 
					height: dashletHeight, 
					titleTextStyle: { color: waGraphTitleColor, fontSize: 20 }, 
					colors: [ waRed, waOrange, waYellow, waCyan, waGrey, waGreen, waBlack ], 
					chartArea: { width:dashletPieChartArea },
					pieSliceText: 'none', 
					is3D: false };
	allEventsBySeverityChart.draw(data, options);
	delete( severityCountMap );
	delete( options );
	delete( data );
	
	log.info( "dashboard.js: device status panel loaded." );
}


/**
 * Build up bar chart with status broken out by device type.
 */
function refreshAllSeveritiesByType()
{
	log.info( "dashboard.js: loading status by device panel" );

	var jsonData = $.ajax({
		url: "model/getseveritycountsbydevicetype.php",
		dataType:"json",
		async: false,
		cache: false		
	}).responseText;

	log.info( "dashboard.js: json data received: " + jsonData );
	var statusByType = jQuery.parseJSON( jsonData );
	log.info( "dashboard.js: json data parsed: " + statusByType );		
	
	// Iterate through the status list and build a per type count map.
	var typeSeverityCountMap = new Object();
	for( index in statusByType.severityCountsByType )
	{
		var type = statusByType.severityCountsByType[index].type;

		var severity_id = statusByType.severityCountsByType[index].severity_id;
		var count = statusByType.severityCountsByType[index].count;
		
		// Locate type object - if this is the first time, initialize
		// and set the counters to zero.
		if ( (type in typeSeverityCountMap) == false )
		{
			typeSeverityCountMap[type] = new Object();
			// Load counters initially to 0.
			for ( var i = 0; i <= 5; i++ )
			{
				typeSeverityCountMap[type][i] = 0;
			}
		}

		var obj = typeSeverityCountMap[type];
		obj[severity_id] = parseInt( count );
		typeSeverityCountMap[type] = obj;
	}

	var data = new google.visualization.DataTable();
	data.addColumn( 'string', 'Type' );
	data.addColumn( 'number', 'Critical' );
	data.addColumn( 'number', 'Major' );
	data.addColumn( 'number', 'Minor' );
	data.addColumn( 'number', 'Warning' );	
	data.addColumn( 'number', 'Info' );
	data.addColumn( 'number', 'Clear' );
	
	for( index in typeSeverityCountMap )
	{
		log.debug( "dashboard.js: adding row: " + index + ", " + typeSeverityCountMap[index][5] + ", " + typeSeverityCountMap[index][4] + ", " + typeSeverityCountMap[index][3] + ", " + typeSeverityCountMap[index][2] + ", " + typeSeverityCountMap[index][1] + ", " + typeSeverityCountMap[index][0] );
		data.addRow( [index, typeSeverityCountMap[index][5], typeSeverityCountMap[index][4], typeSeverityCountMap[index][3], typeSeverityCountMap[index][2], typeSeverityCountMap[index][1], typeSeverityCountMap[index][0] ] );
	}
	
	// Create and draw the visualization.
	var options = { title:"All Severities by Type", 
					width:dashletWidth, 
					height:dashletHeight, 
					titleTextStyle: { color: waGraphTitleColor, fontSize: 20 }, 
	                isStacked: true,
					hAxis: { slantedText: true, slantedTextAngle: 45 },
					series: [{color: waRed}, {color: waOrange}, {color: waYellow}, {color: waCyan}, {color: waGrey }, {color:waGreen}, { color: waBlack} ], 
					chartArea: { width:dashletChartArea } };
	allSeveritiesByTypeChart.draw( data, options ); 
	delete( typeSeverityCountMap );
	delete( options );
	delete( data );
	
	log.info( "dashboard.js: status by panel loaded." );
}


/**
 * Build pie chart with statuses of the devices.
 */
function refreshMaxDeviceEventsBySeverity()
{
	log.info( "dashboard.js: loading device status panel" );
	
	var jsonData = $.ajax({
		url: "model/getmaxdeviceseveritycounts.php",
		dataType:"json",
		async: false,
		cache: false		
	}).responseText;

	log.info( "dashboard.js: json data received: " + jsonData );
	var severities = jQuery.parseJSON( jsonData );
	log.info( "dashboard.js: json data parsed: " + severities );			
	
	// Iterate through the status list and build a per type count map.
	var severityCountMap = new Object();
	for( index in severities.severityCounts )
	{
		var severity_id = severities.severityCounts[index].severity_id;
		var count = severities.severityCounts[index].count;	
		severityCountMap[severity_id] = parseInt( count );
	}
	
	var data = new google.visualization.DataTable();
	data.addColumn( 'string', 'Status' );
	data.addColumn( 'number', 'Count' );
	
	for( var i = 5; i >= 0; i-- )
	{
		if ( typeof severityCountMap[i] == 'undefined' )
		{
			severityCountMap[i] = 0;
		}
		data.addRow( [ getNameForSeverityId( i ), severityCountMap[i] ] );
		log.debug( "dashboard.js: adding row for severity " + getNameForSeverityId( i ) + " - " + severityCountMap[i] );
	} 
	
	var options = { title: 'Max Device Severities', 
					width: dashletWidth, 
					height: dashletHeight, 
					titleTextStyle: { color: waGraphTitleColor, fontSize: 20 }, 
					colors: [ waRed, waOrange, waYellow, waCyan, waGrey, waGreen, waBlack ], 
					chartArea: { width:dashletPieChartArea },
					pieSliceText: 'none', 
					is3D: false };
	maxDeviceEventsBySeverityChart.draw(data, options);
	delete( severityCountMap );
	delete( options );
	delete( data );
	
	log.info( "dashboard.js: device status panel loaded." );
}


/**
 * Build up bar chart with status broken out by device type.
 */
function refreshMaxDeviceSeveritiesByType()
{

	log.info( "dashboard.js: loading status by device panel" );

	var jsonData = $.ajax({
		url: "model/getmaxdeviceseveritycountsbydevicetype.php",
		dataType:"json",
		async: false,
		cache: false		
	}).responseText;

	log.info( "dashboard.js: json data received: " + jsonData );
	var statusByType = jQuery.parseJSON( jsonData );
	log.info( "dashboard.js: json data parsed: " + statusByType );		


	// Iterate through the status list and build a per type count map.
	var typeSeverityCountMap = new Object();
	for( index in statusByType.severityCountsByType )
	{
		var type = statusByType.severityCountsByType[index].type;

		var severity_id = statusByType.severityCountsByType[index].severity_id;
		var count = statusByType.severityCountsByType[index].count;
		
		// Locate type object - if this is the first time, initialize
		// and set the counters to zero.
		if ( (type in typeSeverityCountMap) == false )
		{
			typeSeverityCountMap[type] = new Object();
			// Load counters initially to 0.
			for ( var i = 0; i <= 5; i++ )
			{
				typeSeverityCountMap[type][i] = 0;
			}
		}

		var obj = typeSeverityCountMap[type];
		obj[severity_id] = parseInt( count );
		typeSeverityCountMap[type] = obj;
	}

	var data = new google.visualization.DataTable();
	data.addColumn( 'string', 'Type' );
	data.addColumn( 'number', 'Critical' );
	data.addColumn( 'number', 'Major' );
	data.addColumn( 'number', 'Minor' );
	data.addColumn( 'number', 'Warning' );	
	data.addColumn( 'number', 'Info' );
	data.addColumn( 'number', 'Clear' );
	
	for( index in typeSeverityCountMap )
	{
		log.debug( "dashboard.js: adding row: " + index + ", " + typeSeverityCountMap[index][5] + ", " + typeSeverityCountMap[index][4] + ", " + typeSeverityCountMap[index][3] + ", " + typeSeverityCountMap[index][2] + ", " + typeSeverityCountMap[index][1] + ", " + typeSeverityCountMap[index][0] );
		data.addRow( [index, typeSeverityCountMap[index][5], typeSeverityCountMap[index][4], typeSeverityCountMap[index][3], typeSeverityCountMap[index][2], typeSeverityCountMap[index][1], typeSeverityCountMap[index][0] ] );
	}

	// Create and draw the visualization.
	var options = { title:"Max Device Severities by Type", 
					width:dashletWidth, 
					height:dashletHeight, 
					titleTextStyle: { color: waGraphTitleColor, fontSize: 20 }, 
	                isStacked: true,
					hAxis: { slantedText: true, slantedTextAngle: 45 },
					series: [{color: waRed}, {color: waOrange}, {color: waYellow}, {color: waCyan}, {color: waGrey }, {color:waGreen}, { color: waBlack} ], 
					chartArea: { width:dashletChartArea } };
	maxDeviceSeveritiesByTypeChart.draw( data, options ); 
	delete( typeSeverityCountMap );
	delete( options );
	delete( data );
	
	log.info( "dashboard.js: status by panel loaded." );

}


/**
 * Refresh the dashboards.
 */
function refreshDashboard()
{
	refreshAllEventsBySeverity();
	refreshAllSeveritiesByType();
	refreshMaxDeviceEventsBySeverity();
	refreshMaxDeviceSeveritiesByType();	
}

/**
 * JQuery Ready function.  When the page is ready, load up the chart libs and panels.
 */
$(document).ready(function() {
	// Refresh on a tab click.
	$("#showDashboard").click( function() 
	{
		refreshDashboard();
	});

	// Setup periodic refresh.
	setInterval( function() 
	{
		refreshDashboard();
		log.info( "dashboard.js: refreshed dashboard view" );
	}, refreshInterval * 60000 );
});
//]]>