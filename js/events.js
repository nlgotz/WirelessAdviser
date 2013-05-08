//<![CDATA[
///////////////////////////////////////////////////////////////////
// 
// Global Variables
//
///////////////////////////////////////////////////////////////////
var refreshInterval = 2; // Every 2 minutes.
var eventsUrl = 'model/getallevents.php';
var currentEventSort;
var checkedEventList = new Array();

/**
 * Load the events from the database.
 */
function loadEvents() 
{
	log.info( "events.js: loading events.  Calling url " + eventsUrl );

	// Get the current filter and scrollposition so we can restore later.
	var filterValue = $("#filter_event_text").val();
	var scrollValue = $("#event_table").scrollTop();
	
	$.ajax({
		url: eventsUrl,
		cache: false,
		success: function( jsonEventData ) 
	{
		var eventText = '<div id="event_table" class="event_panel">';

		log.info( "events.js: json data received: " + jsonEventData );
		if ( jsonEventData.indexOf( "ERROR" ) != -1 )
		{
			eventText += "Error getting event data from the database.";
		}
		else
		{
			var events = jQuery.parseJSON( jsonEventData );
			log.info( "events.js: json data parsed: " + events );

			// Remember selections.
			$("input:checkbox:checked").each( function() 
			{
				checkedEventList.push( this.name );
			});

			eventText += ' Filter <input name="filter" id="filter_event_text" value="" maxlength="110" size="110" type="text">';
			eventText += '<input id="filter_event_clear" type="submit" value="Clear"/>';
			eventText += '<table id="eventTable" class="tablesorter"><thead><tr><th>Time</th><th>Device ID</th><th>Device Name</th><th>Severity</th><th>Description</th><th>Clear</th></tr></thead><tbody>';
			
			for( index in events.eventList )
			{
				var event_time = events.eventList[index].event_time;
				var device_id = events.eventList[index].device_id;
				var display_name = events.eventList[index].display_name;
				var device_type = events.eventList[index].device_type;			
				var severity_id = events.eventList[index].severity_id;
				var description = events.eventList[index].description;
				var display_time = event_time.match( /^(\d+-\d+-\d+ \d+:\d+:\d+)/ )[0];
							
				var tdTag = '<td style="background-color:' + getColorForSeverityId( severity_id ) + ';">';		
				var ahref = '<a href="#" onclick="launchDeviceSheet(\'' + device_id + '\', \'' + device_type + '.php\');">';
				var rowString = '<tr>' + tdTag + display_time + '</td>' + tdTag + ahref + device_id + '</a></td>' + tdTag + display_name + '</td>';
				rowString += tdTag + getNameForSeverityId( severity_id ) + '</td>' + tdTag + description + '</td>';
				rowString += tdTag + '<input type="checkbox" name="' + event_time + "__" + device_id + '"/></td></tr>';
				eventText += rowString;
			}
			eventText += "</tbody></table>";
		}
		
		eventText += "</div>";	
		$('#eventTable').remove();  // Mark all elements below table tags as removable to free memory.
		$('#event_table').replaceWith( eventText );
		delete( eventText );
		delete( jsonEventData );
		
		// Add the table sorter and recover current sorting to use later.
		$("#eventTable")
			.tablesorter( 
			{ 
				headers: { 
					0:{ sorter:"shortDate"},
					1:{ sorter:"text"},
					2:{ sorter:"text"},
					3:{ sorter:"text"},
					4:{ sorter:"text"},
					5:{ sorter:false}
				},
				sortList: currentEventSort 
			} )
			.bind( "sortEnd", function( sorter ) 
			{
				currentEventSort = sorter.target.config.sortList;
			})
			.tablesorterFilter(
			{
				filterContainer: $("#filter_event_text"),
				filterClearContainer: $("#filter_event_clear"),
				filterColumns: [0, 1, 2, 3, 4],
				filterCaseSensitive: false
			});
			
		// Place the original filter back and retrigger it.
		$("#filter_event_text").val( filterValue );
		$("#eventTable").trigger( "doFilter" );
		$("#event_table").scrollTop( scrollValue )		
		
		// Finally, reapply check marks.
		var eventName;
		while( (eventName = checkedEventList.pop() ) != null )
		{
			log.debug( "events.js: reapplying checkbox to " + eventName );
			$('input[name="' + eventName + '"]').prop( "checked", true );
		}
		
		log.info( "events.js: events loaded." );
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
			log.info( "events.js: calling delete url: " + url );
			$.ajax({
				url: 'model/deleteevent.php?event_id=' + this.name,
				cache: false,
				success: function( jsonEventData ) 
			{
				if ( jsonEventData.indexOf( "ERROR" ) != -1 ) 
				{
					alert( "Error deleting rows..." );
				}
				else 
				{
					log.info( "events.php: Delete was successful." );
				}
				deleteCount--;
				if ( deleteCount <= 0 )
				{
					// Refresh the view.
					loadEvents('model/getallevents.php');
					$('body').css( 'cursor', 'auto' );				
				}
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
 * Refresh the Events.
 */
function refreshEvents()
{
	loadEvents();
}


/**
 * JQuery Ready function.  When the page is ready, load up the events from the DAO.
 */
$(document).ready(function() 
{
	$("#showEvents").click( function() 
	{
		refreshEvents();
	});
	refreshEvents();	

	// Setup periodic refresh.
	setInterval( function() 
	{
		refreshEvents();
		log.info( "events.js: refreshed event view" );
	}, refreshInterval * 60000 );
});

//]]>