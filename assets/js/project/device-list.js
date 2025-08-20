let error_message = document.getElementById('error-message');
let error_message_text = document.getElementById('error-message-text');
let success_message = document.getElementById('success-message');
let success_message_text = document.getElementById('success-message-text');

const error_toast = bootstrap.Toast.getOrCreateInstance(error_message);
const success_toast = bootstrap.Toast.getOrCreateInstance(success_message);


var group_name = localStorage.getItem("GroupNameValue")
if (group_name == "" || group_name == null) {
    group_name = "ALL";
}
$("#pre-loader").css('display', 'block');
add_device_list(group_name);

let group_list = document.getElementById('group-list');

group_list.addEventListener('change', function () {
    let group_name = group_list.value;
    if (group_name !== "" && group_name !== null) {
        $("#pre-loader").css('display', 'block');
        add_device_list(group_name);

    }
});
var interval_Id;
//setTimeout(refresh_data, 50);

interval_Id = setInterval(refresh_data, 60000);

function refresh_data() {
    /*if (typeof update_frame_time === "function") {
        device_id = document.getElementById('device_id').value;
        update_frame_time(device_id);
    } */
    let group_name = group_list.value;
    if (group_name !== "" && group_name !== null) {
        add_device_list(group_name);
    }
}


function add_device_list(group_id) {
    console.log(group_id);
    if (group_id !== "" && group_id !== null) {

        $.ajax({
            type: "POST",
            url: '../device-list/code/device-list-table.php',
            traditional: true,
            data: { GROUP_ID: group_id },
            dataType: "json",
            success: function (data) {
                const device_list_table = document.getElementById('device_list_table');
                device_list_table.innerHTML = '';

                if (Object.keys(data).length) {


                    for (var i = 0; i < data.length; i++) {
                        if (data[i].ACTIVE_STATUS == 1) {
                            var newRow = document.createElement('tr');
                            newRow.innerHTML =
                                '<td>' + data[i].D_ID + '</td>' +
                                '<td>' + data[i].D_NAME + '</td>' +
                                '<td>' + data[i].INSTALLED_STATUS + '</td>' +
                                '<td>' + data[i].INSTALLED_DATE + '</td>' +
                                '<td>' + data[i].KW + '</td>' +
                                '<td class="col-size-1">' + data[i].DATE_TIME + '</td>' +
                                '<td>' + data[i].ON_OFF_STATUS + '</td>' +
                                '<td>' + data[i].OPERATION_MODE + '</td>' +
                                '<td>' + data[i].WORKING_STATUS + '</td>' +
                                '<td>' + data[i].LMARK + '</td>' +
                                '<td>' + data[i].INSTALLED_LIGHTS + '</td>' +
                                '<td>' +
                                '<i class="bi bi-trash-fill text-danger pointer h5" onclick="delete_device_id(this, \'' + data[i].REMOVE + '\')"></i>' +
                                '<i class="bi bi-pencil-square text-primary pointer h5 ms-3" onclick="openEditModal(\'' + data[i].D_ID + '\', \'' + data[i].D_NAME + '\')"></i>' +
                                '</td>';
                            device_list_table.appendChild(newRow);
                        }


                    }
                    for (var i = 0; i < data.length; i++) {
                        if (data[i].ACTIVE_STATUS == 0) {
                            var newRow = document.createElement('tr');
                            newRow.innerHTML =
                                '<td>' + data[i].D_ID + '</td>' +
                                '<td>' + data[i].D_NAME + '</td>' +
                                '<td>' + data[i].INSTALLED_STATUS + '</td>' +
                                '<td>' + data[i].INSTALLED_DATE + '</td>' +
                                '<td>' + data[i].KW + '</td>' +
                                '<td class="col-size-1">' + data[i].DATE_TIME + '</td>' +
                                '<td>' + data[i].ON_OFF_STATUS + '</td>' +
                                '<td>' + data[i].OPERATION_MODE + '</td>' +
                                '<td>' + data[i].WORKING_STATUS + '</td>' +
                                '<td>' + data[i].LMARK + '</td>' +
                                '<td>' + data[i].INSTALLED_LIGHTS + '</td>' +
                                '<td>' +
                                '<i class="bi bi-trash-fill text-danger pointer h5" onclick="delete_device_id(this, \'' + data[i].REMOVE + '\')"></i>' +
                                '<i class="bi bi-pencil-square text-primary pointer h5 ms-3" onclick="openEditModal(\'' + data[i].D_ID + '\', \'' + data[i].D_NAME + '\')"></i>' +
                                '</td>';
                            device_list_table.appendChild(newRow);
                        }
                    }
                }
                else {
                    var newRow = document.createElement('tr');
                    newRow.innerHTML = '<td class="text-danger" colspan="12">Device List not found</td>';
                    device_list_table.appendChild(newRow);
                }
                $("#pre-loader").css('display', 'none');
            },
            error: function (textStatus, errorThrown) {
                error_message_text.textContent = "Error getting the data";
                error_toast.show();
                $("#pre-loader").css('display', 'none');
            },
            failure: function () {
                error_message_text.textContent = "Failed to get the data";
                error_toast.show();

                error_message_text.textContent = "Failed to get the data";
                error_toast.show();
                $("#pre-loader").css('display', 'none');
            }
        });
    }
}

// Event listener for input in the new device name field
document.getElementById('newdeviceName').addEventListener('input', function () {
    var oldDeviceName = document.getElementById('olddeviceName').value;
    var newDeviceName = document.getElementById('newdeviceName').value;

    if (oldDeviceName === newDeviceName) {
        // Show warning in orange if names match
        showWarningMessage("Device name is the same as the old name!", "warning");
    } else {
        // Show success message in green if names don't match
        showWarningMessage("Device name looks good!", "success");
    }
});

// Function to display warning or success messages
function showWarningMessage(message, type) {
    var messageBox = document.getElementById('messageBox');

    if (type === "warning") {
        messageBox.className = 'alert alert-warning';
    } else if (type === "success") {
        messageBox.className = 'alert alert-success';
    }

    messageBox.innerHTML = message;
    messageBox.style.display = 'block';
}

// Hide the message when the modal is closed
document.getElementById('editDeviceModal').addEventListener('hidden.bs.modal', function () {
    var messageBox = document.getElementById('messageBox');
    messageBox.style.display = 'none'; // Hide the message when modal closes
});

// Function to update the device name
function updateDeviceName() {
    var deviceId = document.getElementById('deviceId').value;
    var newDeviceName = document.getElementById('newdeviceName').value;
    var messageBox = document.getElementById('messageBox');
    if (newDeviceName == "") {
        messageBox.style.display = 'block';
        messageBox.innerHTML = "Enter New Name the field shouldn't be Empty";
        messageBox.classList.add('alert-danger');
    }
    // Clear any previous messages in the messageBox
    else {


        messageBox.style.display = 'none';  // Hide initially
        messageBox.classList.remove('alert-success', 'alert-danger', 'alert-warning');

        // Perform the device name update logic with an AJAX call
        $.ajax({
            url: '../device-list/code/updatenewdevicename.php',
            type: 'POST',
            data: { deviceId: deviceId, deviceName: newDeviceName },
            success: function (response) {
                console.log(response);  // Log to check the response
                var result;

                try {
                    result = JSON.parse(response);  // Parse the JSON response

                    // Handle different status cases
                    if (result.status === 'success') {
                        messageBox.innerHTML = result.message;
                        messageBox.classList.add('alert-success');
                    } else if (result.status === 'warning') {
                        messageBox.innerHTML = result.message;
                        messageBox.classList.add('alert-warning');
                    } else if (result.status === 'error') {
                        messageBox.innerHTML = result.message;
                        messageBox.classList.add('alert-danger');
                    } else {
                        // Handle unexpected statuses
                        messageBox.innerHTML = 'Unexpected response from the server.';
                        messageBox.classList.add('alert-danger');
                    }

                    messageBox.style.display = 'block';  // Show the message box
                    let group_name = group_list.value;

                    add_device_list(group_name);
                } catch (e) {
                    console.error('Error parsing JSON response:', e);
                    messageBox.innerHTML = 'Invalid response from server.';
                    messageBox.classList.add('alert-danger');
                    messageBox.style.display = 'block';  // Show error message
                }
            },
            error: function (xhr, status, error) {
                console.error('AJAX error:', status, error);
                messageBox.innerHTML = 'An error occurred while updating the device. Please try again.';
                messageBox.classList.add('alert-danger');
                messageBox.style.display = 'block';  // Show error message
            }
        });
    }

    // DO NOT close the modal. The user will manually close it after reviewing the message.
}




// Function to open the modal and populate device info
function openEditModal(deviceId, oldDeviceName) {
    // Set the values for device ID and old device name in the modal fields
    document.getElementById('deviceId').value = deviceId;
    document.getElementById('olddeviceName').value = oldDeviceName;
    document.getElementById('newdeviceName').value = ''; // Clear the new device name field

    // Show the modal
    var editDeviceModal = new bootstrap.Modal(document.getElementById('editDeviceModal'));
    editDeviceModal.show();
}


function delete_device_id(element, device_id) {
    if (device_id != "" && device_id != null) {
        if (confirm('Confirm ' + device_id + ' deletion: Deleting the device will remove it from your list. Proceed?')) {
            $("#pre-loader").css('display', 'block');
            $(function () {
                $.ajax({
                    type: "POST",
                    url: '../device-list/code/remove-device.php',
                    traditional: true,
                    data: { D_ID: device_id },
                    dataType: "json",
                    success: function (data) {
                        if (data == "Device deleted successfully") {
                            element.closest('tr').remove();
                            alert(data);
                        }

                        $("#pre-loader").css('display', 'none');
                    },
                    error: function (textStatus, errorThrown) {
                        error_message_text.textContent = "Error getting the data";
                        error_toast.show();

                        $("#pre-loader").css('display', 'none');
                    },
                    failure: function () {
                        error_message_text.textContent = "Failed to get the data";
                        error_toast.show();
                        $("#pre-loader").css('display', 'none');
                    }
                });
            });
        }
    }
    else {
        alert("Please Enter Device ID");
    }
}

function addDevice() {
    var deviceName = document.getElementById('deviceName').value;
    var device_id = document.getElementById('deviceID').value;
    var activationCode = document.getElementById('activationCode').value;
    /* var groupArea = document.getElementById('groupArea').value;
     var capacity = document.getElementById('capacity').value;*/

    if (device_id != "" && device_id != null) {
        if (confirm('Do you want to proceed and add the device?')) {
            $("#pre-loader").css('display', 'block');
            $(function () {
                $.ajax({
                    type: "POST",
                    url: '../device-list/code/add-device.php',
                    traditional: true,
                    data: { D_ID: device_id, D_NAME: deviceName, ACTIVATION_CODE: activationCode },
                    dataType: "json",
                    success: function (data) {
                        alert(data);
                        $("#pre-loader").css('display', 'none');
                    },
                    error: function (textStatus, errorThrown) {
                        error_message_text.textContent = "Error getting the data";
                        error_toast.show();

                        $("#pre-loader").css('display', 'none');
                    },
                    failure: function () {
                        error_message_text.textContent = "Failed to get the data";
                        error_toast.show();

                        $("#pre-loader").css('display', 'none');
                    }
                });
            });
        }
    }
    else {
        alert("Please Enter Device ID");
    }
}

//Installed Lights Column Buttons Open Function
function openLightsModal(device_id, device_name) {
    document.getElementById('lightsModalLabel').innerText = 'Installed Lights -' + device_name + " - (" + device_id + ")";
    var lightsTableBody = document.getElementById('lightsTableBody');
    lightsTableBody.innerHTML = '';
    if (device_id != "" && device_id != null) {
        localStorage.setItem('device_id_save_lights', device_id);
        localStorage.setItem('device_name_save_lights', device_name);
        $("#pre-loader").css('display', 'block');
        $(function () {
            $.ajax({
                type: "POST",
                url: '../lights-info/code/lights-details-table.php',
                data: { D_ID: device_id }, // Assuming device_id is defined somewhere in your JavaScript
                dataType: "json",
                success: function (data) {
                    if (data.success) {
                        if (data.data.length > 0) {
                            for (var i = 0; i < data.data.length; i++) {
                                var row = lightsTableBody.insertRow();

                                if ((data.data.length - 1) == i) {
                                    row.innerHTML = '<td class="bg-success-subtle">' + data.data[i].brand_name + '</td><td class="bg-success-subtle">' + data.data[i].wattage + '</td><td class="fw-bold bg-success-subtle">' + data.data[i].total_lights + '</td><td class="fw-bold bg-success-subtle">' + data.data[i].total_wattage + '</td><td class="bg-success-subtle"></td>';
                                }
                                else {
                                    row.innerHTML = '<td>' + data.data[i].brand_name + '</td><td>' + data.data[i].wattage + '</td><td>' + data.data[i].total_lights + '</td><td>' + data.data[i].total_wattage + '</td><td><i class="bi bi-trash-fill text-danger pointer" onclick="remove_Lights(this, \'' + data.data[i].device_id + '\', \'' + data.data[i].id + '\')"></i></td>';
                                }
                            }
                        } else {
                            var row = lightsTableBody.insertRow();
                            row.innerHTML = '<td colspan="5">No data available</td>';
                        }
                    }
                    else {
                        var row = lightsTableBody.insertRow();
                        row.innerHTML = '<td colspan="5">Error: ' + data.message + '</td>';

                    }
                    $("#pre-loader").css('display', 'none');
                },
                error: function (xhr, textStatus, errorThrown) {
                    var row = lightsTableBody.insertRow();
                    row.innerHTML = '<td colspan="5" class="text-left text-danger">Error: ' + textStatus + '</td>';
                    $("#pre-loader").css('display', 'none');
                }
            });
        });
    }
    else {
        alert("Please Enter Device ID");
    }
    var lightsModal = new bootstrap.Modal(document.getElementById('lightsModal'));
    lightsModal.show();
}

// Function to show the add lights form
function showAddLightsForm() {
    var addLightModal = new bootstrap.Modal(document.getElementById('addLightModal'));
    addLightModal.show();
}

// Function to add a new light 
function addLight() {

    // Retrieve the device ID from localStorage
    var device_id = localStorage.getItem('device_id_save_lights');
    var device_name = localStorage.getItem('device_name_save_lights');


    // Retrieve the values from input fields
    var brandName = document.getElementById('brandName').value.trim();
    var wattage = document.getElementById('wattage').value.trim();
    var lights = document.getElementById('lights').value.trim();

    // Calculate total watts


    // Function to validate input values
    function validateInputs() {
        if (!device_id) {
            alert("Device ID not found. Please try again.");
            return false;
        }
        if (!brandName) {
            alert("Brand name cannot be empty.");
            return false;
        }
        if (!wattage || isNaN(wattage) || wattage <= 0) {
            alert("Please enter a valid wattage.");
            return false;
        }
        if (!lights || isNaN(lights) || lights <= 0) {
            alert("Please enter a valid number of lights.");
            return false;
        }
        return true;
    }
    var totalWatts = lights * wattage;

    // Proceed if the inputs are valid
    if (validateInputs()) {
        if (confirm('Do you want to proceed and add the details?')) {
            $("#pre-loader").css('display', 'block');
            $.ajax({
                type: "POST",
                url: '../lights-info/code/add-light_details.php',
                traditional: true,
                data: {
                    D_ID: device_id,
                    BRAND: brandName,
                    WATT: wattage,
                    LIGHTS: lights
                },
                dataType: "json",
                success: function (response) {
                    $("#pre-loader").css('display', 'none');
                    if (response.status === "success") {
                        alert(response.message);
                        document.getElementById('addLightsForm').reset();
                        var backdrops = document.querySelectorAll('.modal-backdrop');
                        backdrops.forEach(function (backdrop) {
                            backdrop.parentNode.removeChild(backdrop);
                        });
                        var addLightModal = bootstrap.Modal.getInstance(document.getElementById('addLightModal'));
                        addLightModal.hide();

                        openLightsModal(device_id, device_name);
                    } else {
                        alert(response.message);
                    }
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    $("#pre-loader").css('display', 'none');
                    error_message_text.textContent = "Error getting the data";
                    error_toast.show();

                },
                failure: function () {
                    error_message_text.textContent = "Failed to get the data";
                    error_toast.show();

                    $("#pre-loader").css('display', 'none');
                }
            });
        }
    }
    // Hide the add lights form


    // Reset the form


}

// Function to delete a light row
function remove_Lights(element, device_id, record_id) {
    /* element.closest('tr').remove();
     */

    // Function to validate input values
    function validateInputs() {
        if (!record_id) {
            alert("Record ID not found. Please try again.");
            return false;
        }
        if (!device_id) {
            alert("Device ID not found. Please try again.");
            return false;
        }
        return true;
    }

    // Proceed if the inputs are valid
    if (validateInputs()) {
        if (confirm('Do you want to proceed and remove the details?')) {
            $("#pre-loader").css('display', 'block');
            $.ajax({
                type: "POST",
                url: '../lights-info/code/remove-light_details.php',
                traditional: true,
                data: {
                    D_ID: device_id,
                    RECORD: record_id
                },
                dataType: "json",
                success: function (response) {
                    $("#pre-loader").css('display', 'none');
                    if (response.status === "success") {
                        alert(response.message);
                        var device_name = localStorage.getItem('device_name_save_lights');

                        var backdrops = document.querySelectorAll('.modal-backdrop');
                        backdrops.forEach(function (backdrop) {
                            backdrop.parentNode.removeChild(backdrop);
                        });
                        openLightsModal(device_id, device_name);
                    } else {
                        alert(response.message);
                    }
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    $("#pre-loader").css('display', 'none');
                    error_message_text.textContent = "Error getting the data";
                    error_toast.show();

                },
                failure: function () {
                    error_message_text.textContent = "Failed to get the data";
                    error_toast.show();
                    $("#pre-loader").css('display', 'none');
                }
            });
        }
    }

}
