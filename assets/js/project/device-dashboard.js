// document.addEventListener('DOMContentLoaded', function () {
//     let group_list = document.getElementById('group-list');
//     let group_name = group_list.value;
//     gps_initMaps(group_name);

// });
var group_name = localStorage.getItem("GroupNameValue")
if (group_name == "" || group_name == null) {
    group_name = "ALL";
}

if (group_name !== "" && group_name !== null) {
    update_switchPoints_status(group_name);
    update_alerts(group_name);
    update_inActiveDevicesAlerts(group_name);
    // gps_initMaps(group_name);
    $("#pre-loader").css('display', 'block');
}

function initMap() {

}
let group_list = document.getElementById('group-list');

group_list.addEventListener('change', function () {
    let group_name = group_list.value;
    if (group_name !== "" && group_name !== null) {
        update_switchPoints_status(group_name);
        update_alerts(group_name);
        update_inActiveDevicesAlerts(group_name);
        // gps_initMaps(group_name);

        $("#pre-loader").css('display', 'block');
    }
});

// setInterval(refresh_data, 20000);
// function refresh_data() {
//     console.log("val");
//     let group_name = group_list.value;
//     if (group_name !== "" && group_name !== null) {
//         update_switchPoints_status(group_name);
//         update_alerts(group_name);
//     }
// }

// Lights Card
var lightsChartInstance = null; // Store chart instance globally

function initializeLightsCard(data) {
    // Update DOM elements
    document.getElementById('total-lights').textContent = data.total;
    document.getElementById('lights-on-percentage').textContent = data.onPercentage + '%';
    document.getElementById('lights-off-percentage').textContent = data.offPercentage + '%';

    // Get the chart canvas element
    const ctx = document.getElementById('lights-chart').getContext('2d');

    // Destroy the previous chart instance if it exists
    if (lightsChartInstance) {
        lightsChartInstance.destroy();
    }

    // Create a new pie chart
    lightsChartInstance = new Chart(ctx, {
        type: 'pie',
        data: {
            labels: ['On', 'Off'],
            datasets: [{
                data: [data.onPercentage, data.offPercentage],
                backgroundColor: ['#198754', '#dc3545'], // Green for On, Red for Off
                borderWidth: 1,
                borderColor: '#fff'
            }]
        },
        options: createChartOptions('Lights Status')
    });
}

function update_alerts(group_id) {

    $.ajax({
        type: "POST", // Method type
        url: "../dashboard/code/update_dashboard_alerts.php", // PHP script URL
        data: {
            GROUP_ID: group_id // Optional data to send to PHP script
        },
        dataType: "json", // Expected data type from PHP script
        success: function (response) {
            // Update HTML elements with response data
            $("#updates-container").html("");
            $("#updates-container").html(response);
            //$("#pre-loader").css('display', 'none');       	
        },
        error: function (xhr, status, error) {
            $("#updates-container").html("");
            console.error("Error:", status, error);
            $("#pre-loader").css('display', 'none');
            // Handle errors here if necessary
        }
    });
}

function update_inActiveDevicesAlerts(group_id) {

    $.ajax({
        type: "POST", // Method type
        url: "../dashboard/code/update_inactive_alerts.php", // PHP script URL
        data: {
            GROUP_ID: group_id // Optional data to send to PHP script
        },
        dataType: "json", // Expected data type from PHP script
        success: function (response) {
            // Update HTML elements with response data
            $("#inactive-updates-container").html("");
            $("#inactive-updates-container").html(response);
            //$("#pre-loader").css('display', 'none');       	
        },
        error: function (xhr, status, error) {
            $("#inactive-updates-container").html("");
            console.error("Error:", status, error);
            $("#pre-loader").css('display', 'none');
            // Handle errors here if necessary
        }
    });
}
// CCMS Card
var ccmsChartInstance = null; // Store chart instance globally

function initializeCcmsCard(data) {
    // Update DOM elements
    document.getElementById('total-ccms').textContent = data.total;
    document.getElementById('ccms-on').textContent = data.onDevices;
    document.getElementById('ccms-off').textContent = data.offDevices;

    // Calculate percentages
    const onPercentage = Math.round((data.onDevices / data.total) * 100);
    const offPercentage = Math.round((data.offDevices / data.total) * 100);

    // Get the chart canvas element
    const ctx = document.getElementById('ccms-chart').getContext('2d');

    // Destroy the previous chart instance if it exists
    if (ccmsChartInstance) {
        ccmsChartInstance.destroy();
    }

    // Create a new pie chart
    ccmsChartInstance = new Chart(ctx, {
        type: 'pie',
        data: {
            labels: ['Active', 'Inactive'],
            datasets: [{
                data: [onPercentage, offPercentage],
                backgroundColor: ['#198754', '#dc3545'], // Green for Online, Red for Offline
                borderWidth: 1,
                borderColor: '#fff'
            }]
        },
        options: createChartOptions('CCMS Devices Status')
    });
}


// Load Card
let loadChart; // Declare a global variable

function initializeLoadCard(data) {
    // Extract numeric values for calculation
    const cumulativeValue = parseFloat(data.cumulativeLoad) / 1000;
    const installedValue = parseFloat(data.installedLoad) / 1000;
    let nonActiveValue = parseFloat(data.inactiveLoad) / 1000;
    
  

    // Ensure the values are valid numbers
    if (isNaN(cumulativeValue) || isNaN(installedValue) || isNaN(nonActiveValue)) {
        console.error("Error: Cumulative Load, Installed Load, or Active Load is not a valid number.");
        return; // Exit the function if the data is invalid
    }

    // Update DOM elements
    document.getElementById('cumulative-load').textContent = cumulativeValue;
    document.getElementById('installed-load').textContent = installedValue;
    document.getElementById('active-load').textContent = nonActiveValue;

    const inactiveLoadContainer = document.getElementById('inactive-load-container');

    let activePercentage, inactivePercentage;

    if (nonActiveValue < 0) {
        // Overconsumption detected, show alert UI
        // nonActiveValue=0-nonActiveValue;

        inactiveLoadContainer.innerHTML = `
        <div class="p-2 bg-danger bg-opacity-10 border border-danger rounded d-flex flex-column align-items-center justify-content-center h-100 text-center w-100">
          <div class="d-flex align-items-center flex-wrap justify-content-center">
            <h4 id="active-load" class="text-danger mb-0 me-2 text-break">${-nonActiveValue}</h4>
            <a tabindex="0" role="button"
              class="text-danger"
              id="info-icon"
              data-bs-container="body"
              data-bs-toggle="popover"
              data-bs-placement="top"
              data-bs-content="Possible causes include power theft / excessive power consumption by lights / more lights connected than recorded / faulty wiring or loose connections / and low voltage supply issues."
              data-bs-trigger="click">
              <i class="bi bi-info-circle"></i>
            </a>
          </div>
          <small class="mt-1 text-danger text-wrap w-100">OverLoad</small> 
        </div>
      `;


        // Attach an event listener to show an alert message when clicked


        // Set chart to 100% active (avoid negative inactive values)
        activePercentage = 100;
        inactivePercentage = 0;
        const popoverTrigger = document.querySelector("#info-icon");
        if (popoverTrigger) {
            const popover = new bootstrap.Popover(popoverTrigger);

            // Close popover when clicking elsewhere
            document.addEventListener("click", function (event) {
                if (!popoverTrigger.contains(event.target) && !event.target.closest('.popover')) {
                    popover.hide();
                }
            });
        }
    } else {
        // Normal case: show inactive load
        inactiveLoadContainer.innerHTML = `
            <div class="p-2 bg-secondary bg-opacity-10 rounded d-flex align-items-center justify-content-center h-90 text-center fixed-size">
                <div>
                    <h4 id="active-load" class=" mb-0">${nonActiveValue} </h4>
                    <small>Inactive Load</small>
                </div>
            </div>
        `;

        // Normal percentage calculation
        if (cumulativeValue !== 0) {
            activePercentage = Math.round((installedValue / cumulativeValue) * 100);
            inactivePercentage = Math.round((nonActiveValue / cumulativeValue) * 100);
        } else {
            activePercentage = 0;
            inactivePercentage = 100; // Default when no load
        }
    }

    // Get chart canvas
    const ctx = document.getElementById('load-chart');

    // Destroy existing chart instance if it exists to avoid overlap
    if (loadChart) {
        loadChart.destroy();
        loadChart = null; // Reset the variable after destroying the chart
    }

    if (ctx) {
        // Create a new chart instance with dynamic data
        loadChart = new Chart(ctx, {
            type: 'pie',
            data: {
                labels: ['Active', 'Inactive'],
                datasets: [{
                    data: [activePercentage, inactivePercentage], // Updated values
                    backgroundColor: ['#0d6efd', '#6c757d'],
                    borderWidth: 1,
                    borderColor: '#fff'
                }]
            },
            options: createChartOptions('Load Distribution')
        });
    }
}






// Helper function to create chart options
function createChartOptions(title) {
    return {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'bottom',
                labels: {
                    boxWidth: 12,
                    padding: 15
                }
            },
            title: {
                display: false,
                text: title
            },
            tooltip: {
                callbacks: {
                    label: function (context) {
                        return `${context.label}: ${context.raw}%`;
                    }
                }
            }
        },
        cutout: '30%'
    };
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
            $("#installed_load").text("Installed Lights Load = " + response.INSTALLED_LOAD);
            $("#active_load").text(response.ACTIVE_LOAD);
            $("#total_consumption_units").text(response.KWH);
            $("#energy_saved_units").text(response.SAVED_UNITS);
            $("#amount_saved").text(response.SAVED_AMOUNT);
            $("#co2_saved").text(response.SAVED_CO2);

            var total_units = response.SWITCH_POINTS;
            var active_devices = response.ACTIVE_SWITCH;
            var poor_nw = Number(response.POOR_NW);  // Convert to number
            var power_fail = Number(response.POWER_FAILURE);  // Convert to number
            var faulty = Number(response.FAULTY_SWITCH);  // Convert to number
            var poor_nw = Number(response.POOR_NW);  // Convert to number
            var installed_load = Number(response.INSTALLED_LOAD);
            var active_load = Number(response.ACTIVE_LOAD);
            var inactive_load = installed_load - active_load;
            // Perform numeric addition
            var off_devices = poor_nw + power_fail + faulty;

            var totalLights = response.TOTAL_LIGHTS;
            var onLights = response.ON_LIGHTS;
            var offLights = response.OFF_LIGHT;

            var activeLoad = response.ACTIVE_LOAD; // Assuming this key exists in your JSON response
            var installedLoad = response.INSTALLED_LOAD; // Assuming this key exists in your JSON response

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

            const lightsData = {
                total: totalLights,
                onPercentage: onLights,
                offPercentage: offLights
            };
            const ccmsData = {
                total: total_units,
                onDevices: active_devices,
                offDevices: off_devices
            };
            const loadData = {
                cumulativeLoad: Number(response.INSTALLED_LOAD),
                installedLoad: Number(response.ACTIVE_LOAD),
                inactiveLoad: Number(inactive_load) // Ensure it's a valid number
            };

            initializeLightsCard(lightsData);
            initializeCcmsCard(ccmsData);
            initializeLoadCard(loadData);

        },
        error: function (xhr, status, error) {
            console.error("AJAX Error:", status, error);
            $("#pre-loader").css('display', 'none');
        }
    });
}


function openNonActiveModal() {
    var modal = new bootstrap.Modal(document.getElementById('openNonActiveDevicesModal'));
    modal.show();
}
function activeModal() {
    // let group_list = document.getElementById('group-list');
    // let group_name = group_list.value;
    installed_devices_status(group_name, "ACTIVE_DEVICES")

    var modal = new bootstrap.Modal(document.getElementById('activeModal'));
    modal.show();
}
function openPoorNetwork() {
    installed_devices_status(group_name, "POOR_NW_DEVICES");
    var modal = new bootstrap.Modal(document.getElementById('activePoorNetworkModal'));
    modal.show();
}
function openPowerFail() {
    installed_devices_status(group_name, "POWER_FAIL_DEVICES");
    var modal = new bootstrap.Modal(document.getElementById('powerfailureModal'));
    modal.show();
}
function openFaulty() {
    installed_devices_status(group_name, "FAULTY_DEVICES");
    var modal = new bootstrap.Modal(document.getElementById('faultModal'));
    modal.show();
}


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

function handlePhoneClick(event, phoneNumber) {
    // Detect if it's a mobile device or desktop
    if (window.innerWidth <= 768) {
        // Mobile: It will open the dialer directly
        window.location.href = 'tel:' + phoneNumber;
    } else {
        // Desktop: Attempt to open the phone app or show an action
        event.preventDefault(); // Prevent the default link action
        const isAppInstalled = window.matchMedia('(-webkit-app-region: drag)').matches;
        if (isAppInstalled) {
            // If the phone app is detected, open it
            window.location.href = 'tel:' + phoneNumber;
        } else {
            // If no app detected, show a message or some prompt
            alert('Click the phone number to initiate a call or use a mobile device.');
        }
    }
}