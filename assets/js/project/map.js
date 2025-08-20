

/* function initMap() {

	   var options = {
		   zoom: 8,
		   center: { lat: 17.8850, lng: 78.8867 }
	   };

	   var map = new google.maps.Map(document.getElementById('map'), options);
	   var marker = new google.maps.Marker({
		   position: { lat: 17.8850, lng: 76.4867 },
		   map: map
	   });
 }*/
function initMap() {

}

var loc_lat = "17.890307";
var loc_long = "79.863593";
let zoom_level = 7;
var user_map = "";
var group = "";
var modal_event = 0;

let group_list_map = document.getElementById('group-list');
var group_name = localStorage.getItem("GroupNameValue")
if (group_name == "" || group_name == null) {
	group_name = group_list_map.value;
}
gps_initMaps(group_name);
group_list_map.addEventListener('change', function () {
	group_name = group_list_map.value;
	gps_initMaps(group_name);
});
function refreshMap()
{
	let group_list_map = document.getElementById('group-list');
	group_name = group_list_map.value;
	gps_initMaps(group_name);
}
// document.addEventListener('DOMContentLoaded', function () {
//     let group_list = document.getElementById('group-list');
//     let group_name = group_list.value;
//     gps_initMaps(group_name);

// });
function gps_initMaps(group_name) {

	$("#loader").css('display', 'block');
	$(function () {
		$.ajax({
			type: "POST",
			url: '../devices/code/gis-locations.php',
			traditional: true,
			data: { GROUP_ID: group_name },
			dataType: "json",
			success: function (data) {
				$("#loader").css('display', 'none');
				on_success(data[0], data[1]);

			},
			failure: function (response) {
				alert(response.responseText);
			},
			error: function (response) {
				alert(response.responseText);
			}
		});
	});
}
function on_success(data, location) {
	var json = data;
	locations = [];
	var subinfoWindow = new google.maps.InfoWindow();

	for (var i = 0; i < json.length; i++) {
		locations.push([json[i].va, json[i].l1, json[i].l2, json[i].icon, json[i].id]);
	}

	// Use LatLngBounds to auto-center and auto-zoom
	let bounds = new google.maps.LatLngBounds();

	for (let i = 0; i < locations.length; i++) {
		let lat = Number(locations[i][1]);
		let lng = Number(locations[i][2]);
		if (lat !== 0 && lng !== 0) {
			bounds.extend(new google.maps.LatLng(lat, lng));
		}
	}

	// Fallback if no valid points
	if (bounds.isEmpty()) {
		loc_lat = "17.890307";
		loc_long = "79.863593";
		zoom_level = 7;
	} else {
		var center = bounds.getCenter();
		loc_lat = center.lat();
		loc_long = center.lng();
	}

	var map = new google.maps.Map(document.getElementById('map'), {
		zoom: zoom_level,
		center: new google.maps.LatLng(loc_lat, loc_long),
		mapTypeId: google.maps.MapTypeId.ROADMAP,
		gestureHandling: 'cooperative'
	});

	if (!bounds.isEmpty()) {
		map.fitBounds(bounds); // Auto zoom and center
	}

	var infowindow = new google.maps.InfoWindow();
	var marker, i;
	var markers = [];

	var image = "";
	var image_red = 'https://maps.gstatic.com/mapfiles/ms2/micons/red-dot.png';
	var image_green = 'https://maps.gstatic.com/mapfiles/ms2/micons/green-dot.png';
	var image_yellow = 'https://maps.gstatic.com/mapfiles/ms2/micons/yellow-dot.png';
	var image_blue = 'https://maps.gstatic.com/mapfiles/ms2/micons/blue-dot.png';
	var image_orange = 'https://maps.gstatic.com/mapfiles/ms2/micons/orange-dot.png';
	var image_purple = 'https://maps.gstatic.com/mapfiles/ms2/micons/purple-dot.png';

	for (i = 0; i < locations.length; i++) {
		image = "";

		if (locations[i][3] == "1") {
			image = image_green;
		} else if (locations[i][3] == "2") {
			image = image_yellow;
		} else if (locations[i][3] == "3") {
			image = image_blue;
		} else if (locations[i][3] == "4") {
			image = image_purple;
		} else {
			image = image_red;
		}

		marker = new google.maps.Marker({
			position: new google.maps.LatLng(locations[i][1], locations[i][2]),
			map: map,
			icon: image
		});

		google.maps.event.addListener(marker, 'click', (function (marker, i) {
			return function () {
				infowindow.setContent(locations[i][0]);
				infowindow.open(map, marker);
				if (subinfoWindow) {
					subinfoWindow.close();
				}
			};
		})(marker, i));

		google.maps.event.addListener(map, 'click', function (event) {
			if (infowindow) {
				infowindow.close();
			}
			if (subinfoWindow) {
				subinfoWindow.close();
			}
		});

		markers.push(marker);
	}

	function populateDropdown() {
		const dropdown = document.getElementById('locationsDropdown');
		$("#locationsDropdown").empty();

		const option = document.createElement('option');
		option.value = "";
		option.textContent = "Find Device Location";
		dropdown.appendChild(option);

		locations.forEach((location, index) => {
			const option = document.createElement('option');
			option.value = index.toString();
			option.textContent = location[4];
			dropdown.appendChild(option);
		});

		dropdown.addEventListener('change', function () {
			const selectedIndex = parseInt(this.value, 10);
			if (!isNaN(selectedIndex)) {
				highlightMarker(selectedIndex);
			}
		});
	}

	function highlightMarker(index) {
		markers.forEach((marker, i) => {
			if (i === index) {
				marker.setAnimation(google.maps.Animation.BOUNCE);
				map.setCenter(marker.getPosition());
				map.setZoom(16);

				infowindow.setContent(locations[i][0]);
				infowindow.open(map, marker);
				if (subinfoWindow) {
					subinfoWindow.close();
				}
				setTimeout(function () {
					marker.setAnimation(null);
				}, 2000);
			} else {
				marker.setAnimation(null);
			}
		});
	}

	populateDropdown();
}



