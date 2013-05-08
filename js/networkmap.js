//<![CDATA[

///////////////////////////////////////////////////////////////////
// 
// Global Variables
//
///////////////////////////////////////////////////////////////////
var map;
var bounds;
var mapHeight = '575px';
var mapWidth = '800px';
var networkMapUrl = 'model/getdevicesformap.php';
var networkLinksUrl = 'model/getlinksformap.php';

var deviceLatLngMap = []; // Keep track of links end points discovered during device placement.
var deviceMarkerMap = []; // Keep track of markers.
var deviceSeverityMap = []; // Keep track of device severities.

var deviceSeverityCircleMap = []; // Keep track of severity circles on map for subsequent updates.
var deviceLinkMap = []; // Keep track of links.

var zoomToRadiusMap = [ 32768, 16384, 8192, 4096, 2048, 1024, 512, 256, 128, 64, 32, 16, 8, 4, 2, 1 ];	// Adjust radius circle based on zoom.
var radiusScale = 50;		 // Size of status circle drawn around icons.

var infoWindowHeight = 600;  // Needs to match inventory settings.  Stylesheet will also need to be tweaked if this is changed.
var infoWindowWidth = 200;   // Needs to match inventory settings.  Stylesheet will also need to be tweaked if this is changed.

var mapRefreshInterval = 2; // Every 2 minutes.

var defaultZoom = 9;
var initialMapLoad = true;

	
/**
 * Initialize the map. 
 */	 
function initialize() 
{
	var myOptions = {
		zoom: defaultZoom,
		mapTypeId: google.maps.MapTypeId.TERRAIN
	}
	
	map = new google.maps.Map(document.getElementById("map_canvas"), myOptions);
	bounds = new google.maps.LatLngBounds();
}

	
/**
 * Create a circle around a marker indicating the severity of the marker.
 *
 * TODOs
 * 1) Investigate if we can use the shadow icon of a marker to create the severity circles.
 */
function updateSeverityCircles() 
{
	log.info( "networkmap.js: updateSeverityCircles called." );
	for ( var device_id in deviceMarkerMap ) 
	{
		var marker = deviceMarkerMap[ device_id ];
		// Clear out previous status circles.
		var circleToClear = deviceSeverityCircleMap[ marker.id ];
		if ( circleToClear != null ) 
		{
			circleToClear.setMap( null );
		}
			
		var statusColor = getColorForSeverityId( marker.severity_id );
		var center = marker.position;
		var radius = zoomToRadiusMap[ map.getZoom() ] * radiusScale;
		log.info( "networkmap.js created severity circle for " + marker.id + ", color: " + statusColor + ", center: " + center + ", radius: " + radius );
			
		statusCircle = new google.maps.Circle({
			fillColor: statusColor,
			fillOpacity: .5,
			center: center,
			radius: radius,
			strokeColor: statusColor,
			strokeWeight: 1
		});
		deviceSeverityCircleMap[ marker.id ] = statusCircle;
		statusCircle.setMap( map );
	}
}


/**
 * Load the markers specified by the json data returned from getdevicesformap.php.  Will also
 * register click listeners so that the device pages can be loaded as well.
 */
function loadMarkers() 
{
	log.info( "networkmap.js: loading map markers." );
	
	var currentZoom = map.getZoom();
	var currentBounds = map.getBounds();
	
	$.ajax({
		url: networkMapUrl,
		cache: false,
		success: function( jsonData ) 
	{	
		log.info( "networkmap.js: json data received: " + jsonData );
		if ( jsonData.indexOf( "ERROR" ) != -1 )
		{
			document.getElementById("map_canvas").innerHTML = "Error getting device and mapping data from the database.";
			return;
		}		
		var devices = jQuery.parseJSON( jsonData );
		log.info( "networkmap.js: json data parsed: " + devices );			
		
		// Reset the pointMarker Map.
		var devicePointMap = [];
		
		// Recover the devices and build a device info object.
		for( index in devices.deviceList )
		{
			var deviceInfo = new Object();
			deviceInfo.device_id = devices.deviceList[index].device_id;
			deviceInfo.display_name = devices.deviceList[index].display_name;			
			deviceInfo.device_type = devices.deviceList[index].device_type;
			deviceInfo.severity_id = devices.deviceList[index].severity_id;
			deviceInfo.device_state = devices.deviceList[index].device_state
			
			// If the device state is empty or if the state is currently unknown, set it to .5 (unknown state).
			if ( deviceInfo.severity_id.length == 0 || deviceInfo.device_state == "unknown" )
			{
				deviceInfo.severity_id = .5;
			}
			
			deviceInfo.point = new google.maps.LatLng(parseFloat( devices.deviceList[index].latitude ),parseFloat( devices.deviceList[index].longitude ));

			// Register device info object into the maps
			deviceLatLngMap[ deviceInfo.device_id ] = deviceInfo.point;
			if ( typeof devicePointMap[ deviceInfo.point ] === "undefined" ) 
			{
				devicePointMap[ deviceInfo.point ] = new Array();
			}			
			devicePointMap[ deviceInfo.point ].push( deviceInfo );
			deviceSeverityMap[ deviceInfo.device_id ] = deviceInfo.severity_id;
		}

		// Now build markers and place them on the map.  Walkthrough the point map.  
		// If it's a cluster, route to the cluster handler, otherwise just create and register 
		// a regular marker.
		for ( point in devicePointMap ) 
		{
			if ( devicePointMap[ point ].length > 1 ) 
			{
				log.info( "networkmap.js: cluster of " + devicePointMap[ point ].length + " detected at point " + point );
				
				var clusterId = "";
				var clusterName = "";
				var clusterPoint = "";
				var infoText = "<b>Devices at this Cluster:</b><hr>";		

				infoText += "<table>";
				var clusterSeverity = 0; // Will cause an initial unknown state (between clear and info).
				for ( index in devicePointMap[ point ] )
				{
					var deviceInfo = devicePointMap[ point ][index];
					clusterId += deviceInfo.device_id + ":";
					clusterName += deviceInfo.display_name + ", ";
					clusterPoint = deviceInfo.point;
					var severityColor = getColorForSeverityId( deviceInfo.severity_id );
					infoText += '<tr><td style="background-color:' + severityColor + '">&nbsp;&nbsp;&nbsp;</td>';
					infoText += '<td><img src="images/' + deviceInfo.device_type + '.png" height="24" width="24"/></td>';
					infoText += '<td><a href="#" onclick="launchDeviceSheet(\'' + deviceInfo.device_id + '\', \'' + deviceInfo.device_type + '.php\');">' + deviceInfo.display_name + '</a></td></tr>';					
					if ( clusterSeverity < deviceInfo.severity_id )
					{
						clusterSeverity = deviceInfo.severity_id;
					}
				}
				infoText += "</table>";
				
				clusterId = clusterId.substring( 0, clusterId.length-1 );	// Trim trailing ":".
				clusterName = clusterName.substring( 0, clusterName.length-2 );	// Trim trailing "' ".				
				log.info( "networkmap.js: created cluster " + clusterId + " with severity " + clusterSeverity );
								
				// Create the marker and add listners.
				var markerIcon = getIconForMarker( "Cluster" );
				var marker = new google.maps.Marker( 
				{
					position: clusterPoint,
					icon: markerIcon,
					title: clusterName,
					type: "Cluster",
					severity_id: clusterSeverity,
					id: clusterId,
					infoTextContent: infoText,
					optimized: !(jQuery.browser.msie && jQuery.browser.version < 9)
				});
				
				// Register listener to launch popup window.
				var infoWindow = new google.maps.InfoWindow;
				
				bindInfoW(marker, this.infoTextContent, infoWindow);

				
				google.maps.event.addListener( marker, 'click', function() 
				{
					infoWindow.content = this.infoTextContent;
					infoWindow.open( map, this );
				});

				// Remove any other markers related to this id and then add the new marker to the map.
				var oldMarker = deviceMarkerMap[ clusterId ];
				if ( oldMarker != null ) 
				{
					oldMarker.setMap( null );
				}				
				deviceMarkerMap[ clusterId ] = marker;

				// Add the marker to the map and adjust the bound box.
				marker.setMap( map );
				bounds.extend( marker.position );				
			} 
			else 
			{
				var deviceInfo = devicePointMap[ point ][0];
				log.info( "networkmap.js creating marker for non-clustered device " + deviceInfo.device_id )	
				
				// Create the marker and add listners.
				var markerIcon = getIconForMarker( deviceInfo.device_type );
				var marker = new google.maps.Marker(
				{
					position: deviceInfo.point,
					icon: markerIcon,
					title: deviceInfo.display_name,
					type: deviceInfo.device_type,
					page: deviceInfo.device_type + ".php",
					severity_id: deviceInfo.severity_id,
					id: deviceInfo.device_id,
					optimized: !(jQuery.browser.msie && jQuery.browser.version < 9)
				});

				// Register listener to launch popup window.
				google.maps.event.addListener( marker, 'click', function() 
				{
					launchDeviceSheet( this.id, this.page );
					if (event.stopPropagation)
					{
						event.stopPropagation();
					}
					else if(window.event)
					{
						window.event.cancelBubble=true;
					}					
				});

				// Remove any other markers related to this id and then add the new marker to the map.
				var oldMarker = deviceMarkerMap[ deviceInfo.device_id ];
				if ( oldMarker != null ) 
				{
					oldMarker.setMap( null );
				}	
				deviceMarkerMap[ deviceInfo.device_id ] = marker;

				// Add the marker to the map and adjust the bound box.
				marker.setMap( map );
				bounds.extend( marker.position );
			}
		}
		
		// Resize map to fit all the devices - override if the user has zoomed in.
		if ( initialMapLoad == true )
		{
			map.fitBounds(bounds);
			initialMapLoad = false;
		}
		
		log.info( "networkmap.js: markers loaded." );

		// Call loadLinks from here - this insures that all devices have been loaded prior to
		// linking.
		loadLinks(); 
	}
	});
}


/**
 * Load links and update their status on the map.
 */
function loadLinks() 
{
	log.info( "networkmap.js: loading links." );
	
	$.ajax({
		url: networkLinksUrl,
		cache: false,
		success: function( jsonData ) 
	{		
		log.info( "networkmap.js: json data received: " + jsonData );
		if ( jsonData.indexOf( "ERROR" ) != -1 )
		{
			document.getElementById("map_canvas").innerHTML = "Error getting link data from the database.";
			return;
		}		
		var links = jQuery.parseJSON( jsonData );
		log.info( "networkmap.js: json data parsed: " + links );			
		
		for( index in links.linkList )
		{
			var parent_id = links.linkList[index].parent_id;
			var child_id = links.linkList[index].child_id;			
			var link_id = parent_id + ":" + child_id;
			
			// Grab the link ends from the appropriate map and build the link points.
			if ( ( parent_id in deviceLatLngMap == false ) || ( child_id in deviceLatLngMap == false ) )
			{
				continue;
			}
			var parentPoint = deviceLatLngMap[ parent_id ];
			var childPoint = deviceLatLngMap[ child_id ];
			var linkPoints = [ parentPoint, childPoint ];

			// Determine severity of link based on worst case of parent or child.
			var severity_id = deviceSeverityMap[ parent_id ];
			if ( deviceSeverityMap[ child_id ] > severity_id )
			{
				severity_id = deviceSeverityMap[ child_id ];
			}
			var linkColor = getColorForSeverityId( severity_id );
			log.debug( "networkmap.js: parent severity: " + deviceSeverityMap[ parent_id ] + ", child severity: " + deviceSeverityMap[ child_id ] + ", overall severity: " + severity_id + " Link color: " + linkColor );
			log.info( "networkmap.js: linking " + parent_id + "->" + child_id + ":" + getNameForSeverityId( severity_id ) + "(" + linkColor + ")" );

			var link = new google.maps.Polyline( 
			{
				path: linkPoints, 
				strokeColor: linkColor, 
				strokeOpacity: 1.0, 
				strokeWeight: 2,
				id: link_id
			});
		
			// Remove any other links related to this id and then add the new links to the map.
			var oldLink = deviceLinkMap[ link_id ];
			if ( oldLink != null ) 
			{
				oldLink.setMap( null );
			}
			deviceLinkMap[ link_id ] = link;
			link.setMap( map );				
		}  

		log.info( "networkmap.js: links loaded." );			
	}
	});
}	


/**
 * Get the icon to be used for each marker.  Note that this function causes us to enforce a naming convention
 * of 'device_type'.png - i.e. ap.png, ptp.png, etc.
 */
function getIconForMarker( type ) 
{
	var imageName = 'images/' + type + '.png';
	var size = new google.maps.Size( 23, 24 );
	var markerIcon = new google.maps.MarkerImage( imageName, size, null, new google.maps.Point( 12, 12 ), size );
	return markerIcon;
}


/**
 * Refresh the map.
 */
function refreshNetworkMap()
{
	loadMarkers();
	updateSeverityCircles();
}

function bindInfoW(marker, contentString, infowindow)
{
        google.maps.event.addListener(marker, 'click', function() {
            infowindow.setContent(contentString);
            infowindow.open(map, marker);
        });
}

	
/**
 * JQuery Ready function.  Build up the map, intialize it, and load up the markers and links from the DAO.
 */
$(document).ready(function() {
   
	// Function added to help reset map and container boundaries
	$("#showMap").click( function() 
	{
		refreshNetworkMap();
	});
        
	initialize(); 
	refreshNetworkMap();
	
	// Create listener for zoom events to handle repaints of the status circle.
	google.maps.event.addListener( map, 'zoom_changed', updateSeverityCircles );	
	
	// Setup periodic refresh.
	setInterval( function() 
	{
		refreshNetworkMap();
		log.info( "networkmap.js: refreshed map view" );
	}, mapRefreshInterval * 60000 );
});
//]]>