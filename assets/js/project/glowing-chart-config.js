// document.addEventListener('DOMContentLoaded', function () {
//     // Function to be called when the DOM is fully loaded
//     get_phase();
// });
// var phase;
// function get_phase() {
//     // $.ajax({
//     //     url: '../device-reports/code/glowing-nonglowing-hours.php',
//     //     method: 'get', // Use GET method
//     //     dataType: 'json',
//     //     data: {
//     //         D_ID: device_id // Send the device ID via GET
//     //     },
//     //     success: function (response) {
//     //         phase = response;
//     //         console.log(phase);
//     //         // console.log(response);
//     //         // Handle response here
//     //     },
//     //     error: function (xhr, status, error) {
//     //         console.error('AJAX Error:', status, error);
//     //     }
//     // });
// }

initializeDateRangePicker("#date-range", 90);
const today = new Date();
const sevenDaysAgo = new Date(today);
sevenDaysAgo.setDate(today.getDate() - 7);
const formattedToday = today.toISOString().split('T')[0];
const formattedSevenDaysAgo = sevenDaysAgo.toISOString().split('T')[0];

let device_id = localStorage.getItem("SELECTED_ID");
if (!device_id) {
    device_id = document.getElementById('device_id').value;
}

load_chart(device_id);
let device_id_list = document.getElementById('device_id');
device_id_list.addEventListener('change', function () {

    device_id = document.getElementById('device_id').value;
    // get_phase();
    load_chart(device_id);
    refresh_data();
});

setTimeout(refresh_data, 50);
setInterval(refresh_data, 20000);
function refresh_data() {

    if (typeof update_frame_time === "function") {
        device_id = document.getElementById('device_id').value;
        // get_phase();
        update_frame_time(device_id);
    }
}

function getSelectedDateRange() {
    const selectedDates = window.fp.selectedDates;
    if (selectedDates.length === 2) {
        const [startDate, endDate] = selectedDates;
        return {
            startDate: startDate,
            endDate: endDate
        };
    } else {
        return null;
    }
}

function load_chart(device_id) {

    updateChart("LATEST", device_id)
    // fetchPieChartData(endDate, device_id);
}


// Ensure the plugin is registered (for module environments)
/*if (typeof Chart !== 'undefined') {
    Chart.register(ChartZoom); // For plugin version 1.x
}
*/
// Get context of the canvas element we want to select
var ctx = document.getElementById('stackedPhaseChart').getContext('2d');

// Sample data for the last 10 days
var last10Days = [];

// Glowing and non-glowing hours for each phase
var phaseR_glowingHours = [];
var phaseR_nonGlowingHours = [];

var phaseY_glowingHours = [];
var phaseY_nonGlowingHours = [];

var phaseB_glowingHours = [];
var phaseB_nonGlowingHours = [];

var activeHours = [];
var inactiveHours = [];

// Create the stacked bar chart
var stackedPhaseChart = new Chart(ctx, {
    type: 'bar', // Bar chart
    data: {
        labels: last10Days, // Labels for x-axis (last 10 days)
        datasets: []
    },
    options: {
        maintainAspectRatio: false, // Disable automatic aspect ratio for better height control
        scales: {
            x: {
                stacked: true, // Group bars by day, but stack for each phase
                title: {
                    display: true,
                    text: 'Days'
                }
            },
            y: {
                stacked: true, // Stack bars on the y-axis
                beginAtZero: true,
                title: {
                    display: true,
                    text: 'Hours'
                }
            }
        },
        responsive: true,
        plugins: {
            zoom: {
                pan: {
                    enabled: true,
                    mode: 'x' // Pan in the x-direction
                },
                zoom: {
                    enabled: true,
                    wheel: {
                        enabled: true,
                    },
                    drag: {
                        enabled: true,
                    },

                    mode: 'x',  // Zoom in the x-direction
                    speed: 0.1  // Adjust zooming speed (optional)
                }
            },
            legend: {
                position: 'top',
            }

        }
    }
});

// Function to reset chart zoom and pan
function resetChartZoom() {
    if (stackedPhaseChart && stackedPhaseChart.resetZoom) {
        stackedPhaseChart.resetZoom();
    }
}

// Function to reinitialize chart (alternative approach)
function reinitializeChart() {
    if (stackedPhaseChart) {
        stackedPhaseChart.destroy();
    }
    
    // Recreate the chart
    stackedPhaseChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: [],
            datasets: []
        },
        options: {
            maintainAspectRatio: false,
            scales: {
                x: {
                    stacked: true,
                    title: {
                        display: true,
                        text: 'Days'
                    }
                },
                y: {
                    stacked: true,
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Hours'
                    }
                }
            },
            responsive: true,
            plugins: {
                zoom: {
                    pan: {
                        enabled: true,
                        mode: 'x'
                    },
                    zoom: {
                        enabled: true,
                        wheel: {
                            enabled: true,
                        },
                        drag: {
                            enabled: true,
                        },
                        mode: 'x',
                        speed: 0.1
                    }
                },
                legend: {
                    position: 'top',
                }
            }
        }
    });
}

function updateChart(type, device_id) {

    $.ajax({
        url: '../device-reports/code/glowing-nonglowing-hours.php',
        method: 'POST',
        dataType: "json",
        data: {
            TYPE: type,
            D_ID: device_id
        },
        success: function (response) {
            // console.log(response[1])
            chart_update(response);
        }
    });
}
function updateChartCustom(type, startDay, endDay, device_id) {
    $.ajax({
        url: '../device-reports/code/glowing-nonglowing-hours.php',
        method: 'POST',
        dataType: "json",
        data: {
            TYPE: type,
            D_ID: device_id,
            STARTDATE: startDay,
            ENDDATE: endDay
        },
        success: function (response) {
            chart_update(response);
        }
    });
}
function chart_update(response_data) {

    let data = response_data[0];
    let phase = response_data[1];

    const stackedPhaseChartElement = document.getElementById('stackedPhaseChart').parentElement; // The chart container
    const errorMessage = document.getElementById('errorMessage'); // Error message div

    if (data.length > 0) {
        var labels = data.map(item => item.day);
        var phaseR_glowing = data.map(item => parseFloat(item.glowing_hours_phaseR));
        var phaseR_nonGlowing = data.map(item => parseFloat(item.non_glowing_hours_phaseR));
        var phaseY_glowing = data.map(item => parseFloat(item.glowing_hours_phaseY));
        var phaseY_nonGlowing = data.map(item => parseFloat(item.non_glowing_hours_phaseY));
        var phaseB_glowing = data.map(item => parseFloat(item.glowing_hours_phaseB));
        var phaseB_nonGlowing = data.map(item => parseFloat(item.non_glowing_hours_phaseB));
        var TotalActiveHours = data.map(item => parseFloat(item.TotalActiveHours));
        var TotalInActiveHours = data.map(item => parseFloat(item.TotalInActiveHours));

        // SOLUTION 1: Reset zoom before updating chart data
        resetChartZoom();

        // Update chart data
        stackedPhaseChart.data.labels = labels;
        if (phase === '1PH') {
            stackedPhaseChart.data.datasets = [
                {
                    label: 'Total Active Hours',
                    data: TotalActiveHours,
                    backgroundColor: 'rgba(5, 153, 0, 0.8)',
                    borderColor: 'rgba(5, 153, 0, 1)',
                    borderWidth: 1,
                    stack: 'Active Hours'
                },
                {
                    label: 'Total InActive Hours',
                    data: TotalInActiveHours,
                    backgroundColor: 'rgba(192, 192, 192, 0.2)',
                    borderColor: 'rgba(192, 192, 192, 0.5)',
                    borderWidth: 1,
                    stack: 'Active Hours'
                },
                {
                    label: 'Glowing Hours (Phase)',
                    data: phaseR_glowing,
                    backgroundColor: 'rgba(255, 71, 71, 0.8)',
                    borderColor: 'rgba(255, 71, 71, 1)',
                    borderWidth: 1,
                    stack: 'Phase'
                },
                {
                    label: 'Non-Glowing Hours (Phase)',
                    data: phaseR_nonGlowing,
                    backgroundColor: 'rgba(192, 192, 192, 0.2)',
                    borderColor: 'rgba(192, 192, 192, 0.5)',
                    borderWidth: 1,
                    stack: 'Phase'
                }
            ];
        } else { // 3PH
            stackedPhaseChart.data.datasets = [
                {
                    label: 'Total Active Hours',
                    data: TotalActiveHours,
                    backgroundColor: 'rgba(5, 153, 0, 0.8)',
                    borderColor: 'rgba(5, 153, 0, 1)',
                    borderWidth: 1,
                    stack: 'Active Hours'
                },
                {
                    label: 'Total InActive Hours',
                    data: TotalInActiveHours,
                    backgroundColor: 'rgba(192, 192, 192, 0.2)',
                    borderColor: 'rgba(192, 192, 192, 0.5)',
                    borderWidth: 1,
                    stack: 'Active Hours'
                },
                {
                    label: 'Glowing Hours (Phase R)',
                    data: phaseR_glowing,
                    backgroundColor: 'rgba(255, 71, 71, 0.8)',
                    borderColor: 'rgba(255, 71, 71, 1)',
                    borderWidth: 1,
                    stack: 'Phase R'
                },
                {
                    label: 'Non-Glowing Hours (Phase R)',
                    data: phaseR_nonGlowing,
                    backgroundColor: 'rgba(192, 192, 192, 0.2)',
                    borderColor: 'rgba(192, 192, 192, 0.5)',
                    borderWidth: 1,
                    stack: 'Phase R'
                },
                {
                    label: 'Glowing Hours (Phase Y)',
                    data: phaseY_glowing,
                    backgroundColor: 'rgba(255, 140, 0, 0.6)',
                    borderColor: 'rgba(255, 140, 0, 1)',
                    borderWidth: 1,
                    stack: 'Phase Y'
                },
                {
                    label: 'Non-Glowing Hours (Phase Y)',
                    data: phaseY_nonGlowing,
                    backgroundColor: 'rgba(192, 192, 192, 0.2)',
                    borderColor: 'rgba(192, 192, 192, 0.5)',
                    borderWidth: 1,
                    stack: 'Phase Y'
                },
                {
                    label: 'Glowing Hours (Phase B)',
                    data: phaseB_glowing,
                    backgroundColor: 'rgba(30, 144, 255, 0.6)',
                    borderColor: 'rgba(30, 144, 255, 1)',
                    borderWidth: 1,
                    stack: 'Phase B'
                },
                {
                    label: 'Non-Glowing Hours (Phase B)',
                    data: phaseB_nonGlowing,
                    backgroundColor: 'rgba(192, 192, 192, 0.2)',
                    borderColor: 'rgba(192, 192, 192, 0.5)',
                    borderWidth: 1,
                    stack: 'Phase B'
                }
            ];
        }

        // Display phase info
        if (phase == "3PH") {
            document.getElementById('selcted_phase_txt').innerHTML = "(Phase R, Y, B)";
        }
        else {
            document.getElementById('selcted_phase_txt').innerHTML = "(Phase)";
        }

        // Update the chart
        stackedPhaseChart.update();

        // Show chart and hide error message
        stackedPhaseChartElement.style.display = 'block';
        errorMessage.style.display = 'none';
    } else {
        // Clear chart data
        stackedPhaseChart.data.labels = [];
        stackedPhaseChart.data.datasets = [];
        stackedPhaseChart.update();

        // Hide chart and show error message
        stackedPhaseChartElement.style.display = 'none';
        errorMessage.innerText = 'No data available for the selected date range.';
        errorMessage.style.display = 'flex';
    }
}


// Event listener for selecting options
document.getElementById('typeSelect').addEventListener('change', function () {
    // get_phase();
    var selectedType = this.value;  // Get selected type (e.g., last week, current week)
    var selectedId = document.getElementById('device_id').value;  // Get selected ID
    const customRangeContainer = document.getElementById('customRangeContainer');
    if (selectedType === 'CUSTOMRANGE') {

        customRangeContainer.style.display = 'block';
    }
    else {
        customRangeContainer.style.display = 'none';
        updateChart(selectedType, selectedId);
    }
});

document.getElementById('customRangeButton').addEventListener('click', function () {
    // get_phase();
    const dateRange = getSelectedDateRange();
    device_id = document.getElementById('device_id').value;
    if (dateRange) {
        const options = { year: 'numeric', month: '2-digit', day: '2-digit' };    
        const startDateFormatted = formatDateToYMD(dateRange.startDate);
        const endDateFormatted = formatDateToYMD(dateRange.endDate);
        // console.log(startDateFormatted+"--"+endDateFormatted);

        updateChartCustom("CUSTOMRANGE", startDateFormatted, endDateFormatted, device_id);

    }
    else {
        alert("Please select date range");
    }

});

function formatDateToYMD(date) {
  const year = date.getFullYear();
  const month = String(date.getMonth() + 1).padStart(2, '0'); // Months are 0-based
  const day = String(date.getDate()).padStart(2, '0');
  return `${year}-${month}-${day}`;
}