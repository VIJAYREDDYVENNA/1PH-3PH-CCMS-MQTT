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

let device_id_list=document.getElementById('device_id');
device_id_list.addEventListener('change', function() {
	device_id = document.getElementById('device_id').value;
});


function updateStatus()
{
	$("#pre-loader").css('display', 'block'); 
	$.ajax({
		url: '../settings/code/software-update-status.php',
		method: 'POST',
		data: { D_ID: device_id },

		success: function(response) {
			let tableBody = $('#sw-status-tbl');
			$("#pre-loader").css('display', 'none'); 
			tableBody.empty();
			if (response) {
				tableBody.append(response);
			} else {
				tableBody.append('<tr><td colspan="3">No data available</td></tr>');
			}
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

