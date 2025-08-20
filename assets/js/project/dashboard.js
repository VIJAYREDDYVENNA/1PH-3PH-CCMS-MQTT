var group_name = localStorage.getItem("GroupNameValue")
if (group_name == "" || group_name == null) {
    group_name = "ALL";
}

if (group_name !== "" && group_name !== null) {
    update_switchPoints_status(group_name);
    update_alerts(group_name);

    $("#pre-loader").css('display', 'block');
}


let group_list = document.getElementById('group-list');

group_list.addEventListener('change', function () {
    let group_name = group_list.value;
    if (group_name !== "" && group_name !== null) {
        update_switchPoints_status(group_name);
        update_alerts(group_name);
        $("#pre-loader").css('display', 'block');
    }
});


setInterval(refresh_data, 20000);
function refresh_data() {
    let group_name = group_list.value;
    if (group_name !== "" && group_name !== null) {
        update_switchPoints_status(group_name);
        update_alerts(group_name);
    }
}


function update_switchPoints_status(group_id) {

    $.ajax({
        type: "POST", // Method type
        url: "../dashboard/code/switchpoint_details.php", // PHP script URL
        data: {
            GROUP_ID: group_id // Optional data to send to PHP script
        },
        dataType: "json", // Expected data type from PHP script
        success: function (response) {
            // Update HTML elements with response data
            $("#total_devices").text(response.TOTAL_UNITS);
            $("#installed_devices").text(response.SWITCH_POINTS);
            $("#not_installed_devices").text(response.UNISTALLED_UNITS);
            $("#active_devices").text(response.ACTIVE_SWITCH);
            $("#poornetwork").text(response.POOR_NW);
            $("#input_power_fail").text(response.POWER_FAILURE);
            $("#faulty").text(response.FAULTY_SWITCH);
            $("#auto_on").text(response.ON_UNITS);
            $("#manual_on").text(response.MANUAL_ON);
            $("#off").text(response.OFF);
            $("#installed_lights").text(response.TOTAL_LIGHTS);
            $("#installed_lights_on").text(response.ON_LIGHTS);
            $("#installed_lights_off").text(response.OFF_LIGHT);
            $("#installed_load").text("Installed Lights Load = " + (response.INSTALLED_LOAD / 1000).toFixed(3));
            $("#active_load").text((response.ACTIVE_LOAD / 1000).toFixed(3));
            $("#total_consumption_units").text(response.KWH);
            $("#energy_saved_units").text(response.SAVED_UNITS);
            $("#amount_saved").text(response.SAVED_AMOUNT);
            $("#co2_saved").text(response.SAVED_CO2);


            var totalLights = response.TOTAL_LIGHTS;
            var onLights = response.ON_LIGHTS;
            var offLights = response.OFF_LIGHT;

            var activeLoad = (response.ACTIVE_LOAD / 1000).toFixed(3); // Assuming this key exists in your JSON response
            var installedLoad = (response.INSTALLED_LOAD / 1000).toFixed(3); // Assuming this key exists in your JSON response
            if (activeLoad == 0.000) {
                activeLoad=0;
            }
            // Calculate the percentage for the active load
            if (installedLoad > 0)
                var activeLoadPercentage = (activeLoad / installedLoad) * 100;

            // Update progress bar for installed lights ON
            $('#installed_lights_on').css('width', onLights + '%');
            $('#installed_lights_on').attr('aria-valuenow', onLights);
            $('#installed_lights_on').text(onLights + '%-ON');

            // Update progress bar for installed lights OFF
            $('#installed_lights_off').css('width', offLights + '%');
            $('#installed_lights_off').attr('aria-valuenow', offLights);
            $('#installed_lights_off').text(offLights + '%-OFF');

            // Update progress bar for active load
            $('#active_load').css('width', activeLoadPercentage + '%');
            $('#active_load').attr('aria-valuenow', activeLoadPercentage);
            $('#active_load').text('Active - ' + activeLoad);
        },
        error: function (xhr, status, error) {
            console.error("AJAX Error:", status, error);
            $("#pre-loader").css('display', 'none');
        }
    });
}


function update_alerts(group_id) {

    $.ajax({
        type: "POST", // Method type
        url: "../dashboard/code/update_alerts.php", // PHP script URL
        data: {
            GROUP_ID: group_id // Optional data to send to PHP script
        },
        dataType: "json", // Expected data type from PHP script
        success: function (response) {
            // Update HTML elements with response data
            $("#alerts_list").html("");
            $("#alerts_list").html(response);
            //$("#pre-loader").css('display', 'none');       	
        },
        error: function (xhr, status, error) {
            $("#alerts_list").html("");
            console.error("Error:", status, error);
            $("#pre-loader").css('display', 'none');
            // Handle errors here if necessary
        }
    });
}

document.getElementById('total_device').onclick = function () {

    let group_id = group_list.value;
    if (group_id !== "" && group_id !== null) {
        get_devices_status(group_id, "ALL")
    }
};
document.getElementById('installed_devices_list').onclick = function () {


    let group_id = group_list.value;
    if (group_id !== "" && group_id !== null) {
        get_devices_status(group_id, "INSTALLED")
    }
};


document.getElementById('not_installed_devices_list').onclick = function () {

    let group_id = group_list.value;
    if (group_id !== "" && group_id !== null) {
        get_devices_status(group_id, "NOTINSTALLED")
    }
};

function get_devices_status(group_id, status) {

    $("#pre-loader").css('display', 'block');
    $.ajax({
        type: "POST", // Method type
        url: "../dashboard/code/dashboard_device_list.php", // PHP script URL
        data: {
            GROUP_ID: group_id, STATUS: status // Optional data to send to PHP script
        },
        dataType: "json", // Expected data type from PHP script
        success: function (response) {
            $("#pre-loader").css('display', 'none');
            $("#not_installed_device_list_table").html("");
            $("#total_device_table").html("");
            $("#installed_device_list_table").html("");
            document.querySelectorAll('.select_all').forEach(el => el.checked = false);
            document.querySelectorAll('.selected_count').forEach(el => el.textContent = 0);
            if (status == "ALL") {
                $("#total_device_table").html(response);
                setupCheckboxListeners();
            }
            else if (status == "INSTALLED") {
                $("#installed_device_list_table").html(response);
                setupCheckboxListeners();
            }
            else {
                $("#not_installed_device_list_table").html(response);
                setupCheckboxListeners();
            }

        },
        error: function (xhr, status, error) {
            $("#pre-loader").css('display', 'none');
            if (status == "ALL") {
                $("#total_device_table").html("");
            }
            else if (status == "INSTALLED") {
                $("#installed_device_list_table").html("");
            }
            else {
                $("#not_installed_device_list_table").html("");
            }

            // Handle errors here if necessary
        }
    });
}

document.getElementById('active_device_list').onclick = function () {

    let group_id = group_list.value;
    if (group_id !== "" && group_id !== null) {
        installed_devices_status(group_id, "ACTIVE_DEVICES")
    }
};

document.getElementById('poor_nw_device_list').onclick = function () {

    let group_id = group_list.value;
    if (group_id !== "" && group_id !== null) {
        installed_devices_status(group_id, "POOR_NW_DEVICES")
    }
};
document.getElementById('power_failure_device_list').onclick = function () {

    let group_id = group_list.value;
    if (group_id !== "" && group_id !== null) {
        installed_devices_status(group_id, "POWER_FAIL_DEVICES")
    }
};
document.getElementById('faulty_device_list').onclick = function () {

    let group_id = group_list.value;
    if (group_id !== "" && group_id !== null) {
        installed_devices_status(group_id, "FAULTY_DEVICES")
    }
};

function installed_devices_status(group_id, status) {

    $("#pre-loader").css('display', 'block');
    $.ajax({
        type: "POST", // Method type
        url: "../dashboard/code/installed_devices_status.php", // PHP script URL
        data: {
            GROUP_ID: group_id, STATUS: status // Optional data to send to PHP script
        },
        dataType: "json", // Expected data type from PHP script
        success: function (response) {



            if (status == "ACTIVE_DEVICES") {
                $("#active_device_list_update_table").html("");
                $("#active_device_list_update_table").html(response);
            }
            else if (status == "POOR_NW_DEVICES") {
                $("#poor_nw_list_table").html("");
                $("#poor_nw_list_table").html(response);
            }
            else if (status == "POWER_FAIL_DEVICES") {
                $("#power_fail_devices_table").html("");
                $("#power_fail_devices_table").html(response);
            }

            else if (status == "FAULTY_DEVICES") {
                $("#faulty_device_list_table").html("");
                $("#faulty_device_list_table").html(response);
            }
            $("#pre-loader").css('display', 'none');
        },
        error: function (xhr, status, error) {
            $("#total_device_table").html("");
            console.error("Error:", status, error);
            $("#pre-loader").css('display', 'none');
            // Handle errors here if necessary
        }
    });
}


document.getElementById('auto_on_devices_list').onclick = function () {

    let group_id = group_list.value;
    if (group_id !== "" && group_id !== null) {
        active_device_status(group_id, "ON_LIGHTS")
    }
};


document.getElementById('off_devices_list').onclick = function () {

    let group_id = group_list.value;
    if (group_id !== "" && group_id !== null) {
        active_device_status(group_id, "OFF_LIGHTS")
    }
};
document.getElementById('manual_on_devices_list').onclick = function () {

    let group_id = group_list.value;
    if (group_id !== "" && group_id !== null) {
        active_device_status(group_id, "MANUAL_ON")
    }
};


function active_device_status(group_id, status) {

    $("#pre-loader").css('display', 'block');
    $.ajax({
        type: "POST", // Method type
        url: "../dashboard/code/active_device_lights_status.php", // PHP script URL
        data: {
            GROUP_ID: group_id, STATUS: status // Optional data to send to PHP script
        },
        dataType: "json", // Expected data type from PHP script
        success: function (response) {



            if (status == "ON_LIGHTS") {
                $("#on_devices_table").html("");
                $("#on_devices_table").html(response);
            }
            else if (status == "OFF_LIGHTS") {
                $("#off_device_table").html("");
                $("#off_device_table").html(response);
            }

            else if (status == "MANUAL_ON") {
                $("#manual_on_devices_table").html("");
                $("#manual_on_devices_table").html(response);
            }
            $("#pre-loader").css('display', 'none');
        },
        error: function (xhr, status, error) {
            $("#total_device_table").html("");
            console.error("Error:", status, error);
            $("#pre-loader").css('display', 'none');
            // Handle errors here if necessary
        }
    });
}

function openOpenviewModal(device_id) {

    $("#pre-loader").css('display', 'block');
    $.ajax({
        type: "POST", // Method type
        url: "../dashboard/code/device_latest_values_update.php", // PHP script URL
        data: {
            DEVICE_ID: device_id // Optional data to send to PHP script
        },
        dataType: "json", // Expected data type from PHP script
        success: function (data) {


            if (data.PHASE == "3PH") {
                $('#total_light').text(data.LIGHTS);
                $('#on_percentage').text(data.LIGHTS_ON);
                $('#off_percentage').text(data.LIGHTS_OFF);
                $('#on_off_status').html(data.ON_OFF_STATUS);
                $('#v_r').text(data.V_PH1);
                $('#v_y').text(data.V_PH2);
                $('#v_b').text(data.V_PH3);
                $('#i_r').text(data.I_PH1);
                $('#i_y').text(data.I_PH2);
                $('#i_b').text(data.I_PH3);
                $('#watt_r').text(data.KW_R);
                $('#watt_y').text(data.KW_Y);
                $('#watt_b').text(data.KW_B);
                $('#kwh').text(data.KWH);
                $('#kvah').text(data.KVAH);
                $('#record_date_time').text(data.DATE_TIME);
                $("#pre-loader").css('display', 'none');
                var openviewModal = document.getElementById('openview');
                var bootstrapModal = new bootstrap.Modal(openviewModal);
                bootstrapModal.show();
            } else {
                $('#1ph_total_light').text(data.LIGHTS);
                $('#1ph_on_percentage').text(data.LIGHTS_ON);
                $('#1ph_off_percentage').text(data.LIGHTS_OFF);
                $('#1ph_on_off_status').html(data.ON_OFF_STATUS);
                $('#1ph_v_r').text(data.V_PH1);

                $('#1ph_i_r').text(data.I_PH1);

                $('#1ph_watt_r').text(data.KW);
                $('#1ph_kva_r').text(data.KVA);
                $('#1ph_kwh').text(data.KWH);
                $('#1ph_kvah').text(data.KVAH);
                $('#1ph_record_date_time').text(data.DATE_TIME);
                $("#pre-loader").css('display', 'none');
                var openviewModal = document.getElementById('openview1ph');
                var bootstrapModal = new bootstrap.Modal(openviewModal);
                bootstrapModal.show();

            }

        },
        error: function (xhr, status, error) {
            $("#total_device_table").html("");
            console.error("Error:", status, error);
            $("#pre-loader").css('display', 'none');
            // Handle errors here if necessary
        }
    });


}


function select_devices(select_all_id, count_id) {


    const isChecked = document.getElementById(select_all_id).checked;

    document.querySelectorAll('input[name="selectedDevice"]').forEach(function (checkbox) {
        checkbox.checked = isChecked;
    });
    const allChecked = document.querySelectorAll('input[name="selectedDevice"]:checked').length;
    document.getElementById(count_id).textContent = allChecked;
}

function setupCheckboxListeners() {
    // For total devices table
    const totalCheckboxes = document.querySelectorAll('#total_device_table input[name="selectedDevice"]');
    totalCheckboxes.forEach(checkbox => {
        checkbox.removeEventListener('change', updateTotalCount); // Remove any existing event listeners
        checkbox.addEventListener('change', updateTotalCount);
    });

    // For installed devices table
    const installedCheckboxes = document.querySelectorAll('#installed_device_list_table input[name="selectedDevice"]');
    installedCheckboxes.forEach(checkbox => {
        checkbox.removeEventListener('change', updateInstalledCount); // Remove any existing event listeners
        checkbox.addEventListener('change', updateInstalledCount);
    });

    // For not installed devices table
    const uninstalledCheckboxes = document.querySelectorAll('#not_installed_device_list_table input[name="selectedDevice"]');
    uninstalledCheckboxes.forEach(checkbox => {
        checkbox.removeEventListener('change', updateUninstalledCount);
        checkbox.addEventListener('change', updateUninstalledCount);
    });
}

// Functions to update counts for each table
function updateTotalCount() {
    const allCheckboxes = document.querySelectorAll('#total_device_table input[name="selectedDevice"]');
    const checkedCheckboxes = document.querySelectorAll('#total_device_table input[name="selectedDevice"]:checked');

    document.getElementById('selected_count-total').textContent = checkedCheckboxes.length;
    document.getElementById('selectAll-total').checked = (checkedCheckboxes.length === allCheckboxes.length && allCheckboxes.length > 0);
}

function updateInstalledCount() {
    const allCheckboxes = document.querySelectorAll('#installed_device_list_table input[name="selectedDevice"]');
    const checkedCheckboxes = document.querySelectorAll('#installed_device_list_table input[name="selectedDevice"]:checked');

    document.getElementById('selected_count-installed').textContent = checkedCheckboxes.length;
    document.getElementById('selectAll-installed').checked = (checkedCheckboxes.length === allCheckboxes.length && allCheckboxes.length > 0);
}

function updateUninstalledCount() {
    const allCheckboxes = document.querySelectorAll('#not_installed_device_list_table input[name="selectedDevice"]');
    const checkedCheckboxes = document.querySelectorAll('#not_installed_device_list_table input[name="selectedDevice"]:checked');

    const countElement = document.getElementById('selected_count-uninstalled');
    const selectAllCheckbox = document.getElementById('selectAll-uninstalled');

    if (countElement) {
        countElement.textContent = checkedCheckboxes.length;

        if (selectAllCheckbox) {
            selectAllCheckbox.checked = (checkedCheckboxes.length === allCheckboxes.length && allCheckboxes.length > 0);
        }
    }
}

function check_uncheck_fun(element) {

    const allChecked = document.querySelectorAll('input[name="selectedDevice"]:checked').length;
    const nearestCountElement = element.closest('.modal-body').querySelector('.selected_count');
    if (nearestCountElement) {
        nearestCountElement.textContent = allChecked;
    }
    const checkAll = element.closest('.modal-body').querySelector('.select_all');
    if (checkAll) {
        checkAll.checked = false;
    }
}

function openBatchConfirmModal(action, tableId) {
    const table = document.getElementById(tableId);

    if (!table) {
        alert(`Table with ID "${tableId}" not found.`);
        return false;
    }

    const selectedDevices = table.querySelectorAll('input[name$="Device"]:checked');

    if (selectedDevices.length === 0) {
        alert("Please select at least one device.");
        return false;
    }

    const selectedDeviceIds = [];

    selectedDevices.forEach((checkbox) => {
        const row = checkbox.closest('tr');
        const cellText = row.cells[1].textContent.trim(); // Adjust the index (1) based on the column
        selectedDeviceIds.push(cellText);
    });

    const actionText = action === 'install' ? 'install' : 'uninstall';
    document.getElementById('confirmActionText').innerText = `Are you sure you want to ${actionText} the following devices?`;
    const deviceList = document.getElementById('selectedDevicesList');
    deviceList.innerHTML = '';
    selectedDevices.forEach(device => {
        const li = document.createElement('li');
        li.textContent = device.parentElement.nextElementSibling.textContent; // Get the device ID from the next table cell
        deviceList.appendChild(li);
    });

    document.getElementById('confirmActionButton').onclick = function () {
        confirmAction(action, selectedDeviceIds, tableId, selectedDevices);
    };

    const confirmModal = new bootstrap.Modal(document.getElementById('confirmActionModal'));
    confirmModal.show();
}

function confirmAction(action, selectedDeviceIds, tableId, selectedDevices) {

    const actionDate = document.getElementById('actionDate').value;
    if (actionDate === "" || actionDate === null) {
        alert("Please select the action Date");
        document.getElementById('actionDate').focus();
        return false;
    }

    if (selectedDeviceIds.length <= 0) {
        alert("Please select Devices");
        return false;
    }

    // Convert the array to a JSON string
    const selectedDevicesJson = JSON.stringify(selectedDeviceIds);
    if (confirm("Please confirm ?")) {
        $.ajax({
            type: "POST",
            url: "../dashboard/code/update_installation_status.php",
            data: {
                DEVICES: selectedDevicesJson,
                ACTION_DATE: actionDate,
                STATUS: action
            },
            dataType: "json",
            success: function (response) {
                if (response.success) {
                    alert("Devices updated successfully!");
                    update_list(action, selectedDevices, tableId, actionDate);
                    update_switchPoints_status(group_name);
                } else {
                    alert("Error: " + response.message);
                }
            },
            error: function (xhr, status, error) {
                console.error("Error:", status, error);
            }
        });
    }
}

function update_list(action, selectedDevices, tableId, actionDate) {
    selectedDevices.forEach(device => {
        const row = device.closest('tr');
        const statusCell = row.querySelector('td:nth-child(4)');
        let dateCell = row.querySelector('td:nth-child(5)'); // Assuming date cell is the fourth column in Total Modal
        if ((tableId === 'installedDeviceTable') && action === 'uninstall') {
            row.remove();
            const countElement = document.getElementById('selected_count-installed');
            countElement.innerHTML = 0;
        }
        else if (tableId === 'notinstalledDeviceTable' && action === 'install') {
            row.remove();
            const countElement = document.getElementById('selected_count-uninstalled');
            countElement.innerHTML = 0;
        }
        else {
            if (action === 'install') {
                statusCell.textContent = 'Installed';
                statusCell.classList.remove('text-danger');
                statusCell.classList.add('text-success');
                dateCell.textContent = actionDate;
            } else if (action === 'uninstall') {
                statusCell.textContent = 'Not Installed';
                statusCell.classList.remove('text-success');
                statusCell.classList.add('text-danger');
            }
        }
        device.checked = false;
    });

    /*const confirmModal = bootstrap.Modal.getInstance(document.getElementById('confirmActionModal'));
    confirmModal.hide();*/
}



















