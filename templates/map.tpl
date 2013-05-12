{% extends "base.tpl" %}
{% block container %}

<script>

$(window).resize(function () {
	var h = $(window).height(),
        offsetTop = 500; // Calculate the top offset

        $('#map-canvas').css('height', (h - offsetTop));
    }).resize();
function initialize() {
	var mapOptions = {
		zoom: 9,
		center: new google.maps.LatLng(42.84314, -87.8302),
		mapTypeId: google.maps.MapTypeId.TERRAIN
	};

	var map = new google.maps.Map(document.getElementById('map-canvas'),
		mapOptions);

	var mapPoints = (function () {
		var mapPoints = null;
		$.ajax({
			'async': false,
			'global': false,
			'url': "{{name.site}}api.php?q=mapPoints",
			'dataType': "json",
			'success': function (data) {
				json = data;
			}
		});
		return json;
	})();

	var mapPaths = (function () {
		var mapPaths = null;
		$.ajax({
			'async': false,
			'global': false,
			'url': "{{name.site}}api.php?q=mapPaths",
			'dataType': "json",
			'success': function (data) {
				json = data;
			}
		});
		return json;
	})();

for (var i = 0, length = mapPoints.length; i < length; i++) {
	var data = mapPoints[i];
	var latLng = new google.maps.LatLng(data.latitude, data.longitude);


	var shadowIcon;
	if(data.device_state==='up') {
		shadowIcon = 'assets/img/icon_bg_up.png';
	} else {
		shadowIcon = 'assets/img/icon_bg_down.png';
	}

	var image = {
		url: 'assets/img/Default.png',
    	// This marker is 20 pixels wide by 32 pixels tall.
    	scaledSize: new google.maps.Size(16, 16),
    	// The origin for this image is 0,0.
    	origin: new google.maps.Point(0,0),
    	// The anchor for this image is the base of the flagpole at 0,32.
    	anchor: new google.maps.Point(8, 8)
    };

    var shadow = {
    	url: shadowIcon,
    	// The shadow image is larger in the horizontal dimension
    	// while the position and offset are the same as for the main image.
    	scaledSize: new google.maps.Size(20, 20),
    	origin: new google.maps.Point(0,0),
    	anchor: new google.maps.Point(10, 10)
    };

  // Creating a marker and putting it on the map
  var marker = new google.maps.Marker({
  	position: latLng,
  	map: map,
  	title: data.display_name,
  	icon: image,
  	shadow: shadow,
  	url: "http://google.com"
  });



  google.maps.event.addListener(marker, 'click', function() {
  	window.location.href = this.url;
  });
}
for (var i = 0, length = mapPaths.length; i < length; i++) {
	var data = mapPaths[i];
	var circuitCoordinates = [
	new google.maps.LatLng(data.p_lat, data.p_long),
	new google.maps.LatLng(data.c_lat, data.c_long),
	];

	var color;
	if (data.status=="up") {
		color="#12ff00";
	} else {
		color="#ff0012";
	};

	var radioPath = new google.maps.Polyline({
		path: circuitCoordinates,
		strokeColor: color,
		strokeOpacity: 1.0,
		strokeWeight: 2
	});

	radioPath.setMap(map);
}
}




function loadScript() {
	var script = document.createElement('script');
	script.type = 'text/javascript';
	script.src = 'https://maps.googleapis.com/maps/api/js?v=3.exp&sensor=false&' +
	'callback=initialize';
	document.body.appendChild(script);
}





window.onload = loadScript;
window.resize;



</script>


<div class="row-fluid">
	<div class="span12">
		<div id="map-canvas"></div>
	</div>
</div>
<div class="row-fluid">
	<div class="span12">
		hi
	</div>
</div>

{% endblock %}