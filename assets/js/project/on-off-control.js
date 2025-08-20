// Retrieve the selected device ID from localStorage
let device_id = localStorage.getItem("SELECTED_ID");
if (!device_id) {
	device_id = document.getElementById('device_id').value;
}


/*========================================================================*/
var group_name=localStorage.getItem("GroupNameValue")
if(group_name==""||group_name==null)
{
	group_name="ALL";
}
if (group_name !== "" && group_name !== null) {
	update_device_on_off_modes(group_name);
	$("#pre-loader").css('display', 'block');
}


let group_list = document.getElementById('group-list');

group_list.addEventListener('change', function() {
	let group_name = group_list.value;
	if (group_name !== "" && group_name !== null) {
		update_device_on_off_modes(group_name);
		refresh_data();		
		$("#pre-loader").css('display', 'block');
	}
});

/*document.addEventListener('DOMContentLoaded', function() {
	refresh_data();	
});*/
setInterval(refresh_data_update, 50);
setInterval(refresh_data, 20000);
function refresh_data_update() {
	if (typeof update_frame_time === "function") {
		device_id = document.getElementById('device_id').value;
		update_frame_time(device_id);
	} 
}
function refresh_data() {
	if (typeof update_frame_time === "function") {
		device_id = document.getElementById('device_id').value;
		update_frame_time(device_id);
	} 
	let group_name = group_list.value;
	if (group_name !== "" && group_name !== null) {
		var scrollPosition = document.querySelector('.table-responsive').scrollTop;
		if(scrollPosition<=5)
		{

			update_device_on_off_modes(group_name);
		}
	}
}



/*========================================================================*/

// Function to turn on the lights
function lights_on() {
	var hr = document.getElementById('on_off_hours').value;    
	var mins = document.getElementById('on_off_mins').value;

	hr = hr ? hr * 60 : 0;
	var time = Number(hr) + Number(mins);

	if (time === "" || time === null || time === 0) {
		switchfunction("ON", "0");
	} else if (time > 0 && time < 1440) {
		switchfunction("ON", time);
	} else {
		alert("Turn ON lights up to 24 Hrs, not more than that");
	}
}

// Function to turn off the lights
function lights_off() {
	var hr = document.getElementById('on_off_hours').value;    
	var mins = document.getElementById('on_off_mins').value;

	hr = hr ? hr * 60 : 0;
	var time = Number(hr) + Number(mins);

	if (time === "" || time === null || time === 0) {
		switchfunction("OFF", "0");
	} else if (time > 0 && time < 1440) {
		switchfunction("OFF", time);
	} else {
		alert("Turn OFF lights up to 24 Hrs, not more than that");
	}
}

// Function to handle the light switch operations
function switchfunction(mode, time) {

	device_id = document.getElementById('device_id').value;
	var multipleValues = $("#multi_selection_device_id").val() || [];
	var selected_devices = multipleValues.join(",");

	if (selected_devices.length > 0) {
		device_id = selected_devices;
	}

	if (confirm(`Are you sure you want to ${mode} the Light?`)) {
		$("#pre-loader").css('display', 'block'); 
		$.ajax({
			type: "POST",
			url: '../on-off-control/code/on-off-operation.php',
			traditional: true,
			data: { D_ID: device_id, ON_OFF_MODE: mode, TIME: time },
			dataType: "json",
			success: function(response) {
				$("#pre-loader").css('display', 'none');
				alert(response.message);
			},
			error: function(jqXHR, textStatus, errorThrown) {
				$("#pre-loader").css('display', 'none');
				alert(`Error: ${textStatus}, ${errorThrown}`);
			}
		});
	}
}


function update_schedule_time()
{

	var onHours = document.getElementById('onHours').value;    
	var onMinutes = document.getElementById('onMinutes').value;
	var offHours = document.getElementById('offHours').value;
	var offMinutes = document.getElementById('offMinutes').value;

	var on_time=onHours+":"+onMinutes+":00";
	var off_time=offHours+":"+offMinutes+":00";

	device_id = document.getElementById('device_id').value;
	var multipleValues = $("#multi_selection_device_id").val() || [];
	var selected_devices = multipleValues.join(",");

	if (selected_devices.length > 0) {
		device_id = selected_devices;
	}

	if (confirm(`Confirm updating the schedule mode on the device(s)?`)) {
		$("#pre-loader").css('display', 'block'); 
		$.ajax({
			type: "POST",
			url: '../on-off-control/code/schedule-mode.php',
			traditional: true,
			data: { D_ID: device_id, ON_TIME: on_time, OFF_TIME: off_time },
			dataType: "json",
			success: function(response) {
				$("#pre-loader").css('display', 'none');
				if(response.status=="success")
				{
					//document.getElementById('mode_status_update').innerText = response.message;
					alert("SCHEDULE mode updated successfully");
				}
				else
				{
					alert(response.message);
				}
			},
			error: function(jqXHR, textStatus, errorThrown) {
				$("#pre-loader").css('display', 'none');
				alert(`Error: ${textStatus}, ${errorThrown}`);
			}
		});
	}
}


function operational_mode(mode)
{
	device_id = document.getElementById('device_id').value;
	var multipleValues = $("#multi_selection_device_id").val() || [];
	var selected_devices = multipleValues.join(",");

	if (selected_devices.length > 0) {
		device_id = selected_devices;
	}
	if (confirm(`Confirm updating the ${mode} mode on the device(s)?`)) {
		$("#pre-loader").css('display', 'block'); 
		$.ajax({
			type: "POST",
			url: '../on-off-control/code/on-off-modes.php',
			traditional: true,
			data: { D_ID: device_id, ON_OFF_MODE:mode },
			dataType: "json",
			success: function(response) {
				$("#pre-loader").css('display', 'none');
				if(response.status=="success")
				{
					//document.getElementById('mode_status_update').innerText = response.message;
					alert(mode+" mode updated successfully");
				}
				else
				{
					alert(response.message);
				}
			},
			error: function(jqXHR, textStatus, errorThrown) {
				$("#pre-loader").css('display', 'none');
				alert(`Error: ${textStatus}, ${errorThrown}`);
			}
		});
	}
}

function update_device_on_off_modes(group_name){
	$.ajax({
		type: "POST",
		url: '../on-off-control/code/on-off-modes-table.php',
		traditional: true,
		data: { GROUP_ID: group_name },
		dataType: "json",
		success: function(response) {
			$("#pre-loader").css('display', 'none');

			$("#operational_mode_table").html("");
			$("#operational_mode_table").html(response);
			
		},
		error: function(jqXHR, textStatus, errorThrown) {
			$("#pre-loader").css('display', 'none');
			alert(`Error: ${textStatus}, ${errorThrown}`);
		}
	});
}


function fetch_more_records()
{

	$("#pre-loader").css('display', 'block');
	let group_name = group_list.value;
	$.ajax({
		type: "POST",
		url: '../on-off-control/code/on-off-modes-table.php',
		traditional: true,
		data: { GROUP_ID: group_name, FETCH_MORE:"MORE"},
		dataType: "json",
		success: function(response) {
			$("#pre-loader").css('display', 'none');

			if(response!="")
			{
				$("#operational_mode_table").append(response);
			}
			
		},
		error: function(jqXHR, textStatus, errorThrown) {
			$("#pre-loader").css('display', 'none');
			alert(`Error: ${textStatus}, ${errorThrown}`);
		}
	});
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

	var modalFooter = document.querySelector('.footer'); // Get the modal footer element

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



/*function openScheduleModal(){
	var openSchedule=document.getElementById("schedulemodal");
	var bootstrapSchedule=new bootstrap.Modal(openSchedule);
	bootstrapSchedule.show();
}*/