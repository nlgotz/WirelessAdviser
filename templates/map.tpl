{% extends "base.tpl" %}
{% block container %}

<script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=true"></script>
<script type="text/javascript" src="{{name.site}}assets/js/jquery.gmaps.js"></script>

<script>
var map;

function loadSites (data) {
	var items, markers_data = [];
	var points = [];
	if (data.length > 0) {
		items = data;
		console.log(items[0]);
		/* Start testing */
		for (var i = 0; i < items.length; i++) {
			var item = items[i];
			var deviceInfo = new Object();
			deviceInfo.device_id = item.device_id;
			deviceInfo.cluster = false;
			deviceInfo.device_state = item.device_state;
			deviceInfo.display_name = item.display_name;
			deviceInfo.point = new google.maps.LatLng(item.latitude, item.longitude);
			points.push(deviceInfo);
		}
		console.log("points:");
		console.log(points.length);

		var clusterPoints = [];

		for (var p =0; p < points.length; p++) {
			for (q in points) {
				if(points[p].device_id != points[q].device_id) {
					if(points[p].point.kb == points[q].point.kb &&
						points[p].point.lb == points[q].point.lb) {
						console.log(points[p].device_id+", "+points[q].device_id);
					var deviceInfo = new Object();
					deviceInfo.cluster = true;
					deviceInfo.clusterItems = [points[p],points[q]];
					deviceInfo.point = points[p].point;
					if(points[p].device_state=="down" || points[q].device_state=="down") {
						deviceInfo.device_state = "down";
					} else {
						deviceInfo.device_state = "up";
					}
					clusterPoints.push(deviceInfo);
					points.splice(q,1);
					points.splice(p,1);
				}	
			}
		}
	}
	//we need to add the cluster locations to the points array
	points = points.concat(clusterPoints);

	/* End testing */

	for(i in points) {
		var item = points[i];
		if(item.cluster == false) {
			var icon;
			if(item.device_state==="up") {
				icon = 'assets/img/Default_up.png';
			} else {
				icon = 'assets/img/Default_down.png';
			}

			function url(e) {
				window.open("http://"+item.display_name,'_blank');
			}

			markers_data.push({
				position : item.point,
				title : item.display_name,
				icon : {
					scaledSize : new google.maps.Size(24, 24),
					origin : new google.maps.Point(0,0),
					anchor : new google.maps.Point(12,12),
					url : icon,
				},
				details : "{{name.site}}device/"+item.device_id,
				click: function(e) {
					window.open(e.details, '', 'width=500,height=550');
				}
			});
		} else {
			var icon;
			if(item.device_state==="up") {
				icon = 'assets/img/Cluster_up.png';
			} else {
				icon = 'assets/img/Cluster_down.png';
			}

			var content = "<h4>Devices</h4><hr/>";
			content += "<ul>";
			for(i in item.clusterItems) {
				;
				content += "<li><a href=\"device/"+item.clusterItems[i].device_id+"\" onclick=\"window.open(this.href, \'\', \'width=500,height=550\');return false;\">"+item.clusterItems[i].display_name+"</a></li>";
			}
			content += "</ul>";

			markers_data.push({
				position : item.point,
				title : "Multiple Devices",
				icon : {
					scaledSize : new google.maps.Size(24, 24),
					origin : new google.maps.Point(0,0),
					anchor : new google.maps.Point(12,12),
					url : icon,
				},
				infoWindow: {
					content: content,
				}
			});
		}
	}
}

map.addMarkers(markers_data);
}

function loadPaths (data) {
	var items, polylines_data = [];
	if (data.length > 0) {
		items = data;

		for (var i = 0; i < items.length; i++) {
			var item = items[i];

			if (item.p_lat != undefined && item.p_long != undefined && item.c_lat != undefined && item.c_long != undefined) {
				var circuitCoordinates = [
				new google.maps.LatLng(item.p_lat, item.p_long),
				new google.maps.LatLng(item.c_lat, item.c_long),
				];

				var color;
				if(item.status==="up") {
					color="#12ff00";
				} else {
					color="#ff0012";
				}
				// if (item.categories.length > 0) {
				// 	icon = item.categories[0].icon;
				// }
				map.drawPolyline({
					path : circuitCoordinates,
					strokeColor : color,
					strokeOpacity : 1.0,
					strokeWeight: 2
				});
			}
		}
	}

	
}

$(document).ready(function(){
	map = new GMaps({
		div: '#map-canvas',
		lat: 42.84314,
		lng: -87.8302,
		mapTypeId: google.maps.MapTypeId.TERRAIN,
	});

	map.on('marker_added', function (marker) {
		var index = map.markers.indexOf(marker);

		if (index == map.markers.length - 1) {
			map.fitZoom();
		}
	});

	var sites = $.getJSON('{{name.site}}api.php?q=mapPoints');
	sites.done(loadSites);

	var paths = $.getJSON('{{name.site}}api.php?q=mapPaths');
	paths.done(loadPaths);

});
</script>


<div class="row-fluid">
	<div class="span12">
		<div id="map-canvas"></div>
	</div>
</div>
{% endblock %}