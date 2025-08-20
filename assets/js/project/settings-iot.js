let device_id = localStorage.getItem("SELECTED_ID");
if (!device_id) {
	device_id = document.getElementById('device_id').value;

}

let device_id_list = document.getElementById('device_id');
device_id_list.addEventListener('change', function () {
	$("#pre-loader").css('display', 'block');
	device_id = document.getElementById('device_id').value;
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


function device_id_change() {

	var new_deviceid = document.getElementById('new_deviceid_change');
	var change_device_id = new_deviceid.value;

	if (change_device_id == "" || change_device_id == null) {
		new_deviceid.classList.add('border-danger');
		alert("Please Enter Valid device ID..");
		return false;
	}
	new_deviceid.classList.remove('border-danger');
	var formData = new FormData();
	formData.append('PARAMETER_VALUE', change_device_id);
	formData.append('UPDATED_STATUS', 'CHANGE_DEVICE_ID');
	formData.append('D_ID', device_id);
	if (confirm(`Are you sure ?`)) {
		$("#pre-loader").css('display', 'block');
		$.ajax({
			type: "POST",
			url: '../settings/code/iot-settings.php',
			data: formData,
			processData: false,
			contentType: false,
			dataType: "json",
			success: function (response) {
				$("#pre-loader").css('display', 'none');
				alert(response.message);
			},
			error: function (jqXHR, textStatus, errorThrown) {
				$("#pre-loader").css('display', 'none');
				alert(`Error: ${textStatus}, ${errorThrown}`);
			}
		});
	}

}
function serial_id_change() {

	var new_serial_id = document.getElementById('new_device_serial_id');
	var updating_paramter = new_serial_id.value;

	if (updating_paramter == "" || updating_paramter == null) {
		new_serial_id.classList.add('border-danger');
		alert("Please Enter valid serial ID..");
		return false;
	}
	new_serial_id.classList.remove('border-danger');
	var formData = new FormData();
	formData.append('PARAMETER_VALUE', updating_paramter);
	formData.append('UPDATED_STATUS', 'CHANGE_SERIAL_ID');
	formData.append('D_ID', device_id);
	if (confirm(`Are you sure ?`)) {
		$("#pre-loader").css('display', 'block');
		$.ajax({
			type: "POST",
			url: '../settings/code/iot-settings.php',
			data: formData,
			processData: false,
			contentType: false,
			dataType: "json",
			success: function (response) {
				$("#pre-loader").css('display', 'none');
				alert(response.message);
			},
			error: function (jqXHR, textStatus, errorThrown) {
				$("#pre-loader").css('display', 'none');
				alert(`Error: ${textStatus}, ${errorThrown}`);
			}
		});
	}

}

function update_hysteresis() {

	var hysteresis_value = document.getElementById('hysteresis_value');
	var updating_paramter = hysteresis_value.value;

	if (updating_paramter == "" || updating_paramter == null) {
		hysteresis_value.classList.add('border-danger');
		alert("Please Enter valid value..");
		return false;
	}
	hysteresis_value.classList.remove('border-danger');

	var multipleValues = $("#multi_selection_device_id").val() || [];
	var selected_devices = multipleValues.join(",");
	var mult_sel = "Are you sure you want to proceed with the selected device?";
	if (selected_devices.length > 0) {
		device_id = selected_devices;

		mult_sel = "You have chosen multiple devices. Would you like to continue?";
	}


	var formData = new FormData();
	formData.append('PARAMETER_VALUE', updating_paramter);
	formData.append('UPDATED_STATUS', 'HYSTERESIS');
	formData.append('D_ID', device_id);
	if (confirm(`${mult_sel}`)) {
		$("#pre-loader").css('display', 'block');
		$.ajax({
			type: "POST",
			url: '../settings/code/iot-settings.php',
			data: formData,
			processData: false,
			contentType: false,
			dataType: "json",
			success: function (response) {
				$("#pre-loader").css('display', 'none');
				alert(response.message);
			},
			error: function (jqXHR, textStatus, errorThrown) {
				$("#pre-loader").css('display', 'none');
				alert(`Error: ${textStatus}, ${errorThrown}`);
			}
		});
	}

}


function on_off_inverval_update() {

	var on_off_interval_time_value = document.getElementById('on_off_interval_time_value');
	var updating_paramter = on_off_interval_time_value.value;

	if (updating_paramter == "" || updating_paramter == null) {
		on_off_interval_time_value.classList.add('border-danger');
		alert("Please Enter valid Time..");
		return false;
	}
	on_off_interval_time_value.classList.remove('border-danger');

	var multipleValues = $("#multi_selection_device_id").val() || [];
	var selected_devices = multipleValues.join(",");
	var mult_sel = "Are you sure you want to proceed with the selected device?";
	if (selected_devices.length > 0) {
		device_id = selected_devices;

		mult_sel = "You have chosen multiple devices. Would you like to continue?";
	}
	var formData = new FormData();
	formData.append('PARAMETER_VALUE', updating_paramter);
	formData.append('UPDATED_STATUS', 'ON_OFF_INTERVAL');
	formData.append('D_ID', device_id);
	if (confirm(`${mult_sel}`)) {
		$("#pre-loader").css('display', 'block');
		$.ajax({
			type: "POST",
			url: '../settings/code/iot-settings.php',
			data: formData,
			processData: false,
			contentType: false,
			dataType: "json",
			success: function (response) {
				$("#pre-loader").css('display', 'none');
				alert(response.message);
			},
			error: function (jqXHR, textStatus, errorThrown) {
				$("#pre-loader").css('display', 'none');
				alert(`Error: ${textStatus}, ${errorThrown}`);
			}
		});
	}

}


function reset_energy_values() {

	var energy_kwh = document.getElementById('energy_kwh');
	var energy_kvah = document.getElementById('energy_kvah');
	var kwh = energy_kwh.value;
	var kvah = energy_kvah.value;

	if (kwh == "" || kwh == null) {
		energy_kwh.classList.add('border-danger');
		alert("Please Enter valid kwh values..");
		return false;
	}

	if (kvah == "" || kvah == null) {
		energy_kvah.classList.add('border-danger');
		alert("Please Enter valid kvah values..");
		return false;
	}
	var updating_paramter = kwh + "," + kvah;
	energy_kwh.classList.remove('border-danger');
	energy_kvah.classList.remove('border-danger');

	var multipleValues = $("#multi_selection_device_id").val() || [];
	var selected_devices = multipleValues.join(",");
	var mult_sel = "Are you sure you want to proceed with the selected device?";
	if (selected_devices.length > 0) {
		device_id = selected_devices;

		mult_sel = "You have chosen multiple devices. Would you like to continue?";
	}


	var formData = new FormData();
	formData.append('PARAMETER_VALUE', updating_paramter);
	formData.append('UPDATED_STATUS', 'RESET_ENERGY');
	formData.append('D_ID', device_id);

	if (confirm(`${mult_sel}`)) {
		$("#pre-loader").css('display', 'block');
		$.ajax({
			type: "POST",
			url: '../settings/code/iot-settings.php',
			data: formData,
			processData: false,
			contentType: false,
			dataType: "json",
			success: function (response) {
				$("#pre-loader").css('display', 'none');
				alert(response.message);
			},
			error: function (jqXHR, textStatus, errorThrown) {
				$("#pre-loader").css('display', 'none');
				alert(`Error: ${textStatus}, ${errorThrown}`);
			}
		});
	}

}


function update_wifi_credentials() {

	var ssid = document.getElementById('ssid');
	var password = document.getElementById('password');
	var ssid_str = ssid.value;
	var pwd = password.value;

	if (ssid_str == "" || ssid_str == null) {
		ssid.classList.add('border-danger');
		alert("Please Enter valid ssid values..");
		return false;
	}

	if (pwd == "" || pwd == null) {
		password.classList.add('border-danger');
		alert("Please Enter valid pwd values..");
		return false;
	}
	var updating_paramter = ssid_str + "," + pwd;
	ssid.classList.remove('border-danger');
	password.classList.remove('border-danger');
	var formData = new FormData();
	formData.append('PARAMETER_VALUE', updating_paramter);
	formData.append('UPDATED_STATUS', 'WIFI_CREDENTIALS');
	formData.append('D_ID', device_id);

	if (confirm(`Are you sure ?`)) {
		$("#pre-loader").css('display', 'block');
		$.ajax({
			type: "POST",
			url: '../settings/code/iot-settings.php',
			data: formData,
			processData: false,
			contentType: false,
			dataType: "json",
			success: function (response) {
				$("#pre-loader").css('display', 'none');
				alert(response.message);
			},
			error: function (jqXHR, textStatus, errorThrown) {
				$("#pre-loader").css('display', 'none');
				alert(`Error: ${textStatus}, ${errorThrown}`);
			}
		});
	}

}


function read_iot_settings() {

	var formData = new FormData();
	formData.append('PARAMETER_VALUE', "READ");
	formData.append('UPDATED_STATUS', 'READ_SETTINGS');
	formData.append('D_ID', device_id);
	if (confirm(`Are you sure ?`)) {
		$("#pre-loader").css('display', 'block');
		$.ajax({
			type: "POST",
			url: '../settings/code/iot-settings.php',
			data: formData,
			processData: false,
			contentType: false,
			dataType: "json",
			success: function (response) {
				$("#pre-loader").css('display', 'none');
				alert(response.message);
			},
			error: function (jqXHR, textStatus, errorThrown) {
				$("#pre-loader").css('display', 'none');
				alert(`Error: ${textStatus}, ${errorThrown}`);
			}
		});
	}

}

function reset_iot_device() {

	var multipleValues = $("#multi_selection_device_id").val() || [];
	var selected_devices = multipleValues.join(",");
	var mult_sel = "Are you sure you want to proceed with the selected device?";
	if (selected_devices.length > 0) {
		device_id = selected_devices;

		mult_sel = "You have chosen multiple devices. Would you like to continue?";
	}

	var formData = new FormData();
	formData.append('PARAMETER_VALUE', "RESET");
	formData.append('UPDATED_STATUS', 'RESET_DEVICE');
	formData.append('D_ID', device_id);
	if (confirm(`${mult_sel}`)) {
		$("#pre-loader").css('display', 'block');
		$.ajax({
			type: "POST",
			url: '../settings/code/iot-settings.php',
			data: formData,
			processData: false,
			contentType: false,
			dataType: "json",
			success: function (response) {
				$("#pre-loader").css('display', 'none');
				alert(response.message);
			},
			error: function (jqXHR, textStatus, errorThrown) {
				$("#pre-loader").css('display', 'none');
				alert(`Error: ${textStatus}, ${errorThrown}`);
			}
		});
	}

}




function update_address() {

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

	if (isValid) {

		const street = document.getElementById('street').value;
		const area = document.getElementById('area').value;
		const city = document.getElementById('city').value;
		const district = document.getElementById('district').value;
		const state = document.getElementById('state').value;
		const pincode = document.getElementById('pincode').value;
		const country = document.getElementById('country').value;
		const landmark = document.getElementById('landmark').value;

		var formData = new FormData();
		formData.append('PARAMETER_VALUE', country);
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
				success: function (response) {
					$("#pre-loader").css('display', 'none');
					alert(response.message);
				},
				error: function (jqXHR, textStatus, errorThrown) {
					$("#pre-loader").css('display', 'none');
					alert(`Error: ${textStatus}, ${errorThrown}`);
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

function btn_refresh_data(parameter) {
	$("#pre-loader").css('display', 'block');
	update_data_table(parameter);
	refresh_data();
}


function cancel_update(parameter) {
	if (confirm(`Are you sure you want to Cancel the ${parameter} Update ?`)) {
		$("#pre-loader").css('display', 'block');
		$.ajax({
			type: "POST",
			url: '../settings/code/pending-actions.php',
			traditional: true,
			data: { D_ID: device_id, KEY: parameter, CANCEL_PARAMTER: parameter },
			dataType: "json",
			success: function (response) {
				$("#pre-loader").css('display', 'none');

				$("#pending-action-table").html("");
				$("#pending-action-table").html(response);
			},
			error: function (jqXHR, textStatus, errorThrown) {
				$("#pre-loader").css('display', 'none');
				$("#pending-action-table").html("");
				error_message_text.textContent = "Error getting the data";
				error_toast.show();

			}
		});
	}

}




function update_data_table(parameter) {
	var device_id = document.getElementById('device_id').value;
	var modalLabel = document.getElementById('statusModalLabel'); // Get the modal title element

	if (modalLabel && parameter) {
		modalLabel.innerText = parameter.toUpperCase() + " UPDATE STATUS"; // Include "Update Status"
	} else {
		console.error('Modal label or parameter is missing');
	}

	// Dynamically create the "Refresh" button
	var modalFooterHTML = `
        <button type="button" id="dynamicRefreshButton" class="btn btn-primary me-3">Refresh</button>
        <button type="button" class="btn btn-secondary" id="closeModalBtn" data-bs-dismiss="modal">Close</button>
    `;

	var modalFooter = document.querySelector('.modal-footer'); // Get the modal footer element

	modalFooter.innerHTML = "";

	modalFooter.insertAdjacentHTML('beforeend', modalFooterHTML);

	var refreshButton = document.getElementById('dynamicRefreshButton');
	refreshButton.onclick = function () {
		btn_refresh_data(parameter.toUpperCase()); 
	};

	// Perform your AJAX call or other operations
	$.ajax({
		type: "POST",
		url: '../settings/code/pending-actions.php',
		traditional: true,
		data: { D_ID: device_id, KEY: parameter },
		dataType: "json",
		success: function (response) {
			$("#pre-loader").css('display', 'none');
			$("#pending-action-table").html(response);
		},
		error: function (jqXHR, textStatus, errorThrown) {
			$("#pre-loader").css('display', 'none');
			$("#pending-action-table").html("");
			error_message_text.textContent = "Error getting the data";
			error_toast.show();
		}
	});
}
