let error_message = document.getElementById('error-message');
let error_message_text = document.getElementById('error-message-text');
let success_message = document.getElementById('success-message');
let success_message_text = document.getElementById('success-message-text');

const error_toast= bootstrap.Toast.getOrCreateInstance(error_message);
const success_toast= bootstrap.Toast.getOrCreateInstance(success_message);

let device_id = localStorage.getItem("SELECTED_ID");
if (!device_id) {
	device_id = document.getElementById('device_id').value;

}
load_saved_settings(device_id)
let device_id_list=document.getElementById('device_id');
device_id_list.addEventListener('change', function() {
	
	device_id = document.getElementById('device_id').value;
	load_saved_settings(device_id);
	refresh_data();

});

setTimeout(refresh_data, 50);
setInterval(refresh_data, 20000);
function refresh_data() {
	if (typeof update_frame_time === "function") {
		device_id = document.getElementById('device_id').value;
		update_frame_time(device_id);
	} 
}


function update_coordinates(){

	var coordinates = document.getElementById('coordinates').value;
	var latLongPattern = /^-?([1-8]?\d(\.\d+)?|90(\.0+)?),\s*-?((1[0-7]\d(\.\d+)?|180(\.0+)?)|((\d|[1-9]\d)(\.\d+)?))$/;

	if (!latLongPattern.test(coordinates)) {
		document.getElementById('coordinates').classList.add('border-danger');
		
		error_message_text.textContent="Invalid coordinates. Please enter valid latitude and longitude values in the format 'latitude,longitude'.";
		error_toast.show();
		return false; 
	}
	document.getElementById('coordinates').classList.remove('border-danger');

	var formData = new FormData();
	formData.append('PARAMETER', coordinates);
	formData.append('UPDATED_STATUS', 'COORDINATES');
	formData.append('D_ID', device_id);
	if (confirm(`Are you sure ?`)) {
		$("#pre-loader").css('display', 'block'); 
		$.ajax({
			type: "POST",
			url: '../settings/code/update-location-details.php',					
			data: formData,
			processData: false,
			contentType: false,
			dataType: "json",
			success: function(response) {
				$("#pre-loader").css('display', 'none');
				success_message_text.textContent=response.message;
				success_toast.show();
			},
			error: function(jqXHR, textStatus, errorThrown) {
				$("#pre-loader").css('display', 'none');
				error_message_text.textContent="Error getting the data";
				error_toast.show();
			}
		});
	}
	
}

document.addEventListener('DOMContentLoaded', (event) => {
	const checkbox = document.getElementById('enable_static_location');
	checkbox.addEventListener('change', function() {

		var gps_status=0;
		if (this.checked) {			
			var gps_status=1;
		} 
		var formData = new FormData();
		formData.append('PARAMETER', gps_status);
		formData.append('UPDATED_STATUS', 'COORDINATES_CHANGE');
		formData.append('D_ID', device_id);
		if (confirm(`Are you sure ?`)) {
			$("#pre-loader").css('display', 'block'); 
			$.ajax({
				type: "POST",
				url: '../settings/code/update-location-details.php',					
				data: formData,
				processData: false,
				contentType: false,
				dataType: "json",
				success: function(response) {
					$("#pre-loader").css('display', 'none');
					if(response.status!=="success")
					{
						if(gps_status==1)
						{
							checkbox.checked = false;
						}
						else{
							checkbox.checked = true;
						}
					}
					success_message_text.textContent=response.message;
					success_toast.show();
				},
				error: function(jqXHR, textStatus, errorThrown) {
					$("#pre-loader").css('display', 'none');
					error_message_text.textContent="Error getting the data";
					error_toast.show();
				}
			});
		}
		else
		{
			if(gps_status==1)
			{
				checkbox.checked = false;
			}
			else{
				checkbox.checked = true;
			}
		}

	});
});

function load_saved_settings(device_id)
{
	$("#pre-loader").css('display', 'block'); 
	$.ajax({
            type: "POST", // Method type
            url: '../settings/code/load-location-details.php',	 // PHP script URL
            data: {
               D_ID: device_id // Optional data to send to PHP script
            },
            dataType: "json", // Expected data type from PHP script
            success: function(response) {
                // Update HTML elements with response data
            	$("#pre-loader").css('display', 'none');
            	if(response.success) {

            		
            		coords=response.data.latitude +","+response.data.longitude;
            		$("#coordinates").val(coords || 0);
            		if(response.data.update_status==1)
            		{
            			document.getElementById('enable_static_location').checked = true;
            		}
            		else
            		{
            			document.getElementById('enable_static_location').checked = false;
            		}
            		var loc="https://www.google.com/maps?q="+coords;
            		document.getElementById('check_loc').innerHTML = '<a class="link-offset-2 link-underline link-underline-opacity-0" href="' + loc + '" target="_blanck"> Check </a>';
            		


            		$("#street").val(response.data.street);
            		$("#area").val(response.data.town);
            		$("#city").val(response.data.city);
            		$("#district").val(response.data.district);
            		$("#state").val(response.data.state);
            		$("#pincode").val(response.data.pincode);
            		$("#country").val(response.data.country);
            		$("#landmark").val(response.data.landmark);
            		
            		
            	} else {
            		// Handle error message if success is false
            		
            		error_message_text.textContent=response.message;
            		error_toast.show();

            	}	
            },
            error: function(xhr, status, error) {
            	error_message_text.textContent="Error getting the data";
            	error_toast.show();
            	$("#pre-loader").css('display', 'none');
            }
         });
}

function update_address(){

	isValid = true;
	if (!check_validation('street')) {
		isValid = false;
		return false;
	}
	if (!check_validation('area')) {
		isValid = false;
		return false;
	}
	if (!check_validation('city')) {
		isValid = false;
		return false;
	}
	if (!check_validation('district')) {
		isValid = false;
		return false;
	}
	if (!check_validation('state')) {
		isValid = false;
		return false;
	}
	if (!check_validation('pincode')) {
		isValid = false;
		return false;
	}
	if (!check_validation('country')) {
		isValid = false;
		return false;
	}
	if (!check_validation('landmark')) {
		isValid = false;
		return false;
	}

	if(isValid)
	{
		
		const street = document.getElementById('street').value;
		const area = document.getElementById('area').value;
		const city = document.getElementById('city').value;
		const district = document.getElementById('district').value;
		const state = document.getElementById('state').value;
		const pincode = document.getElementById('pincode').value;
		const country = document.getElementById('country').value;
		const landmark = document.getElementById('landmark').value;

		var formData = new FormData();
		formData.append('PARAMETER', country);
		formData.append('STREET', street);
		formData.append('AREA', area);
		formData.append('CITY', city);
		formData.append('DISTRICT', district);
		formData.append('STATE', state);
		formData.append('PINCODE', pincode);
		formData.append('LANDMARK', landmark);
		formData.append('UPDATED_STATUS', 'ADDRESS');
		formData.append('D_ID', device_id);
		if (confirm(`Are you sure ?`)) {
			$("#pre-loader").css('display', 'block'); 
			$.ajax({
				type: "POST",
				url: '../settings/code/update-location-details.php',					
				data: formData,
				processData: false,
				contentType: false,
				dataType: "json",
				success: function(response) {
					$("#pre-loader").css('display', 'none');
					success_message_text.textContent=response.message;
					success_toast.show();
				},
				error: function(jqXHR, textStatus, errorThrown) {
					$("#pre-loader").css('display', 'none');
					error_message_text.textContent="Error getting the data";
					error_toast.show();
				}
			});
		}


	}
}


function check_validation(id, min, max) {
	const input = document.getElementById(id);
	if (!input.value) {
		input.classList.add('border-danger');
		return false;
	} else {
		input.classList.remove('border-danger');
		return true;
	}
}

