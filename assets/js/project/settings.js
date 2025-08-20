let error_message = document.getElementById('error-message');
let error_message_text = document.getElementById('error-message-text');
let success_message = document.getElementById('success-message');
let success_message_text = document.getElementById('success-message-text');

const error_toast = bootstrap.Toast.getOrCreateInstance(error_message);
const success_toast = bootstrap.Toast.getOrCreateInstance(success_message);


let device_id = localStorage.getItem("SELECTED_ID");
if (!device_id) {
	device_id = document.getElementById('device_id').value;
}
load_threshold_settings(device_id)
let device_id_list = document.getElementById('device_id');
device_id_list.addEventListener('change', function () {
	device_id = document.getElementById('device_id').value;
	load_threshold_settings(device_id);
	
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


document.addEventListener('DOMContentLoaded', function () {
	const submitBtn = document.getElementById('voltage_values_btn');

	// Define error message containers
	const errorMessages = {
		r_lower_volt: '',
		y_lower_volt: '',
		b_lower_volt: '',
		r_upper_volt: '',
		y_upper_volt: '',
		b_upper_volt: ''
	};

	// Validation function
	function validateInput(inputId) {

		const input = document.getElementById(inputId);
		if (!input.value || input.value < 1 || input.value > 750) {
			input.classList.add('border-danger');
			return false;
		} else {
			input.classList.remove('border-danger');
			return true;
		}
	}

	// Event listener for keyup
	['r_lower_volt', 'y_lower_volt', 'b_lower_volt', 'r_upper_volt', 'y_upper_volt', 'b_upper_volt'].forEach(id => {
		document.getElementById(id).addEventListener('keyup', function () {
			validateInput(id);
		});
	});

	// Event listener for form submission
	submitBtn.onclick = function () {
		let isValid = true;
		Object.keys(errorMessages).forEach(id => {
			if (!validateInput(id)) {
				isValid = false;
			}
		});

		if (isValid) {

			var multipleValues = $("#multi_selection_device_id").val() || [];
			var selected_devices = multipleValues.join(",");

			if (selected_devices.length > 0) {

				var selectedphaseValue = document.getElementById('phase-selection').value;
				if (selectedphaseValue === "ALL") {
					alert("Updating values for multiple single-phase and 3-phase devices at the same time is not allowed. Please select either single-phase or 3-phase devices for updating.");
					return false;
				}
				device_id = selected_devices;
			}

			var formData = new FormData();
			formData.append('LR', document.getElementById("r_lower_volt").value);
			formData.append('LY', document.getElementById("y_lower_volt").value);
			formData.append('LB', document.getElementById("b_lower_volt").value);
			formData.append('UR', document.getElementById("r_upper_volt").value);
			formData.append('UY', document.getElementById("y_upper_volt").value);
			formData.append('UB', document.getElementById("b_upper_volt").value);
			formData.append('D_ID', device_id);

			if (confirm(`Are you sure ?`)) {
				$("#pre-loader").css('display', 'block');
				$.ajax({
					type: "POST",
					url: '../settings/code/voltage-thresholds.php',
					data: formData,
					processData: false,
					contentType: false,
					dataType: "json",
					success: function (response) {
						$("#pre-loader").css('display', 'none');

						success_message_text.textContent = response.message;
						success_toast.show();
					},
					error: function (jqXHR, textStatus, errorThrown) {
						$("#pre-loader").css('display', 'none');

						error_message_text.textContent = "Error getting the data";
						error_toast.show();

					}
				});
			}

		}
	};
});



document.addEventListener('DOMContentLoaded', function () {
	const submitBtn = document.getElementById('current_values_btn');

	// Define error message containers
	const errorMessages = {
		r_current: '',
		y_current: '',
		b_current: ''

	};

	// Validation function
	function validateInput(inputId) {

		const input = document.getElementById(inputId);
		if (!input.value || input.value < 1 || input.value > 5000) {
			input.classList.add('border-danger');
			return false;
		} else {
			input.classList.remove('border-danger');
			return true;
		}
	}

	// Event listener for keyup
	['r_current', 'y_current', 'b_current'].forEach(id => {
		document.getElementById(id).addEventListener('keyup', function () {
			validateInput(id);
		});
	});

	// Event listener for form submission
	submitBtn.onclick = function () {
		let isValid = true;
		Object.keys(errorMessages).forEach(id => {
			if (!validateInput(id)) {
				isValid = false;
			}
		});

		if (isValid) {

			var multipleValues = $("#multi_selection_device_id").val() || [];
			var selected_devices = multipleValues.join(",");

			if (selected_devices.length > 0) {
				var selectedphaseValue = document.getElementById('phase-selection').value;
				if (selectedphaseValue === "ALL") {
					alert("Updating values for multiple single-phase and 3-phase devices at the same time is not allowed. Please select either single-phase or 3-phase devices for updating.");
					return false;
				}
				device_id = selected_devices;
			}

			var formData = new FormData();
			formData.append('IR', document.getElementById("r_current").value);
			formData.append('IY', document.getElementById("y_current").value);
			formData.append('IB', document.getElementById("b_current").value);

			formData.append('D_ID', device_id);

			if (confirm(`Are you sure ?`)) {
				$("#pre-loader").css('display', 'block');
				$.ajax({
					type: "POST",
					url: '../settings/code/current-thresholds.php',
					data: formData,
					processData: false,
					contentType: false,
					dataType: "json",
					success: function (response) {
						$("#pre-loader").css('display', 'none');

						success_message_text.textContent = response.message;
						success_toast.show();
					},
					error: function (jqXHR, textStatus, errorThrown) {
						$("#pre-loader").css('display', 'none');

						error_message_text.textContent = "Error getting the data";
						error_toast.show();

					}
				});
			}

		}
	};
});



function check_validation(id, min, max) {
	const input = document.getElementById(id);
	if (!input.value || input.value < min || input.value >= max) {
		input.classList.add('border-danger');
		return false;
	} else {
		input.classList.remove('border-danger');
		return true;
	}
}

function update_capcity() {
	isValid = true;
	if (!check_validation('unit_capacity', 1, 5000)) {
		isValid = false;
		return false;
	}
	if (isValid) {
		const value = document.getElementById('unit_capacity').value;
		update_settings_value("CAPACITY", value)
	}
}
function frame_interval_update() {

	isValid = true;
	if (!check_validation('frame_time', 1, 86400)) {
		isValid = false;
		return false;
	}
	if (isValid) {
		const value = document.getElementById('frame_time').value;
		update_settings_value("FRAME_TIME", value)
	}

}
function update_pf() {
	isValid = true;
	if (!check_validation('pf_settings', 0.1, 0.99)) {
		isValid = false;
		return false;
	}
	if (isValid) {
		const value = document.getElementById('pf_settings').value;
		update_settings_value("PF", value)
	}


}
function update_ct_ratio() {
	isValid = true;
	if (!check_validation('ctratio', 1, 2000)) {
		isValid = false;
		return false;
	}
	if (isValid) {
		const value = document.getElementById('ctratio').value;
		update_settings_value("CT_RATIO", value)
	}

}

function update_settings_value(parameter, update_value) {

	var multipleValues = $("#multi_selection_device_id").val() || [];
	var selected_devices = multipleValues.join(",");

	if (selected_devices.length > 0) {
		device_id = selected_devices;
	}

	var formData = new FormData();
	formData.append('PARAMETER', parameter);
	formData.append('UPDATED_VALUE', update_value);
	formData.append('D_ID', device_id);

	if (confirm(`Are you sure ?`)) {
		$("#pre-loader").css('display', 'block');
		$.ajax({
			type: "POST",
			url: '../settings/code/update-prameters.php',
			data: formData,
			processData: false,
			contentType: false,
			dataType: "json",
			success: function (response) {
				$("#pre-loader").css('display', 'none');

				success_message_text.textContent = response.message;
				success_toast.show();
			},
			error: function (jqXHR, textStatus, errorThrown) {
				$("#pre-loader").css('display', 'none');

				error_message_text.textContent = "Error getting the data";
				error_toast.show();

			}
		});
	}
}

function load_threshold_settings(device_id) {
	console.log("hi");

	$("#pre-loader").css('display', 'block');
	$.ajax({
		type: "POST", // Method type
		url: '../settings/code/load-threshold-values.php',	 // PHP script URL
		data: {
			D_ID: device_id // Optional data to send to PHP script
		},
		dataType: "json", // Expected data type from PHP script
		success: function (response) {
			// Update HTML elements with response data

			var selectedValue = response.phase;
			
			if (selectedValue === "1PH") {
				$('.singlePhaseDisable').hide();
			} else {
				$('.singlePhaseDisable').show();
			}

			if (response.success) {

				$("#r_lower_volt").val(response.data.l_r || 1);
				$("#y_lower_volt").val(response.data.l_y || 1);
				$("#b_lower_volt").val(response.data.l_b || 1);
				$("#r_upper_volt").val(response.data.u_r || 1);
				$("#y_upper_volt").val(response.data.u_y || 1);
				$("#b_upper_volt").val(response.data.u_b || 1);


				$("#r_current").val(response.data.i_r || 1);
				$("#y_current").val(response.data.i_y || 1);
				$("#b_current").val(response.data.i_b || 1);

				$("#pf_settings").val(response.data.pf || 1);
				$("#unit_capacity").val(response.data.capacity || 1);

				$("#ctratio").val(response.data.ct_ratio || 1);
				$("#frame_time").val(response.data.frame_time || 20).change()
			} else {
				// Handle error message if success is false

				error_message_text.textContent = response.message;
				error_toast.show();
			}
		},
		error: function (xhr, status, error) {
			error_message_text.textContent = "Error getting the data";
			error_toast.show();
			$("#pre-loader").css('display', 'none');
		}
	});
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



