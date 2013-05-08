//<![CDATA[
///////////////////////////////////////////////////////////////////
// 
// Global Variables
//
///////////////////////////////////////////////////////////////////
var refreshInterval = 2; // Every two minutes.
var infoWindowHeight = 600;  // Needs to match networkmap settings.  Stylesheet will also need to be tweaked if this is changed.
var infoWindowWidth = 600;   // Needs to match networkmap settings.  Stylesheet will also need to be tweaked if this is changed.
var inventoryUrl = 'model/getinventory.php';
var currentInventorySort;


/** 
 * Load the inventory data
 */
function loadInventory() 
{
	log.info( "inventory.js: loading inventory data." );
	
	// Get the current filter and scrollposition so we can restore later.
	var filterValue = $("#filter_inventory_text").val();
	var scrollValue = $("#inventory_table").scrollTop();	
	
	$.ajax({
		url: inventoryUrl,
		cache: false,
		success: function( jsonData ) 
	{
		var inventoryText = '<div id="inventory_table" class="inventory_panel">';

		log.info( "inventory.js: json data received: " + jsonData );
		if ( jsonData.indexOf( "ERROR" ) != -1 )
		{
			inventoryText += "Error getting inventory data from the database.";
		}
		else
		{
			inventoryText += ' Filter <input name="filter_inventory" id="filter_inventory_text" value="" maxlength="110" size="110" type="text">';
			inventoryText += '<input id="filter_inventory_clear" type="submit" value="Clear"/>';		
			inventoryText += '<table id="inventoryTable" class="tablesorter"><thead><tr><th>Device ID</th><th>Device Name</th><th>Device IP</th><th>Device type</th></tr></thead><tbody>';

			var inventory = jQuery.parseJSON( jsonData );
			log.info( "inventory.js: json data parsed: " + inventory );			
			
			for( index in inventory.inventoryList )
			{
				var device_id = inventory.inventoryList[index].device_id;
				var display_name = inventory.inventoryList[index].display_name;
				var ip_address = inventory.inventoryList[index].ip_address;			
				var device_type = inventory.inventoryList[index].device_type;
				var device_state = inventory.inventoryList[index].device_state;
				var severity_id = inventory.inventoryList[index].severity_id;
				
			// If the device state is empty or if the state is currently unknown, set it to .5 (unknown state).
			if ( severity_id.length == 0 || device_state == "unknown" )
			{
				severity_id = .5;
			}				
				
				var tdTag = '<td style="background-color:' + getColorForSeverityId( severity_id ) + ';">';
				var ahref = '<a href="#" onclick="launchDeviceSheet(\'' + device_id + '\', \'' + device_type + '.php\');">';
				var rowString = '<tr>' + tdTag + ahref + device_id + '</a></td>' + tdTag + display_name + '</td>' + tdTag + ip_address + '</td>' + tdTag + device_type + '</td></tr>';
				inventoryText += rowString;
			}
			inventoryText += "</tbody></table>";
		}
		inventoryText += "</div>";
		$('#inventoryTable').remove();  // Mark all elements below table tags as removable to free memory.		
		$('#inventory_table').replaceWith( inventoryText );
		delete( inventoryText );
		delete( jsonData );		
	
		// Add the table sorter and recover current sorting to use later.
		$("#inventoryTable")
			.tablesorter( 
			{ 
				headers: { 
					0:{sorter:"text"}, 
					1:{sorter:"text"}, 
					2:{sorter:"ipAddress"}, 
					3:{sorter:"text"} 
				},
				sortList: currentInventorySort 
			} )
			.bind( "sortEnd", function( sorter ) 
			{
				currentInventorySort = sorter.target.config.sortList;
			})
			.tablesorterFilter(
			{
				filterContainer: $("#filter_inventory_text"),
				filterClearContainer: $("#filter_inventory_clear"),
				filterColumns: [0, 1, 2, 3],
				filterCaseSensitive: false
			});		

		// Place the original filter back and retrigger it.
		$("#filter_inventory_text").val( filterValue );
		$("#inventoryTable").trigger( "doFilter" );
		$("#inventory_table").scrollTop( scrollValue )		
			
		log.info( "inventory.js: inventory loaded." );
	}
	});
}


/**
 * Refresh the Inventory.
 */
function refreshInventory()
{
	loadInventory();
}

/** 
 * ready function called when the document has been loaded and is available for processing. 
 */
$(document).ready(function() {
	// Refresh on a tab click.
	$("#showInventory").click( function() 
	{
		refreshInventory();
	});
	refreshInventory();
	
	// Setup periodic refresh.
	setInterval( function() 
	{
		refreshInventory();
		log.info( "inventory.js: refreshed inventory view" );
	}, refreshInterval * 60000 );
});
//]]>