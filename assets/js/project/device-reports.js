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
    load_chart(device_id);
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

const ctxBar = document.getElementById('deviceHoursChart').getContext('2d');
const deviceHoursChart = new Chart(ctxBar, {
    type: 'bar',
    data: {
        labels: [],
        datasets: [{
            label: 'Uptime Hours',
            data: [],
            backgroundColor: 'rgba(54, 162, 235, 0.6)',
            borderColor: 'rgba(54, 162, 235, 1)',
            borderWidth: 1,
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
            y: {
                beginAtZero: true,
                stepSize: 4,
                title: {
                    display: true,
                    text: 'Hours'
                }
            },
            x: {
                title: {
                    display: true,
                    text: 'Date'
                }
            }
        },
        plugins: {
            zoom: {
                pan: {
                    enabled: true,
                    mode: 'x',
                    speed: 10
                },
                zoom: {
                    wheel: {
                        enabled: true,
                    },
                    drag: {
                        enabled: true,
                    },
                    mode: 'x',
                }
            }
        }
    }
});

// Function to reset zoom and update chart
function resetZoomAndUpdate() {
    // Reset zoom to default view
    deviceHoursChart.resetZoom();
}

// Fetch bar chart data for a date range
function fetchBarChartData(startDate, endDate, d_id) {
    fetch(`../device-reports/code/uptime-downtime-data.php?startDate=${startDate}&endDate=${endDate}&D_ID=${d_id}`)
        .then(response => response.json())
        .then(data => {
            const chartCanvas = document.getElementById('deviceHoursChart');
            const errorMessage = document.getElementById('errorMessage');

            if (data.dates.length > 0) {
                // Update chart data when data is available
                deviceHoursChart.data.labels = data.dates;
                deviceHoursChart.data.datasets[0].data = data.uptimeHours;
                deviceHoursChart.update();

                // Reset zoom after updating data
                resetZoomAndUpdate();

                // Show the chart and hide the error message
                chartCanvas.style.display = 'block';
                errorMessage.style.display = 'none';
            } else {
                // Clear the chart data
                deviceHoursChart.data.labels = [];
                deviceHoursChart.data.datasets[0].data = [];
                deviceHoursChart.update();

                // Reset zoom for empty chart too
                resetZoomAndUpdate();

                // Hide the chart and display the error message
                chartCanvas.style.display = 'none';
                errorMessage.style.display = 'flex';
            }
        })
        .catch(error => {
            console.error('Error fetching chart data:', error);
        });
}

// Handle bar chart click to display pie chart data for the selected date
ctxBar.canvas.addEventListener('click', function (event) {
    const points = deviceHoursChart.getElementsAtEventForMode(event, 'nearest', { intersect: true }, false);
    if (points.length) {
        const firstPoint = points[0];
        const selectedDate = deviceHoursChart.data.labels[firstPoint.index];
        device_id = document.getElementById('device_id').value;
        // fetchPieChartData(selectedDate, device_id);
    }
});

// Event listener for time range dropdown selection
const timeRangeSelect = document.getElementById('timeRangeSelect1');
timeRangeSelect.addEventListener('change', function (e) {
    const selectedRange = e.target.value;
    const customRangeContainer1 = document.getElementById('customRangeContainer1');
    let todayDayOfWeek = today.getDay();
    
    if (selectedRange === 'customRange') {
        customRangeContainer1.style.display = 'block';
    } else {
        customRangeContainer1.style.display = 'none';
        let startDate, endDate;
        
        switch (selectedRange) {
            case 'currentWeek':
                const firstDayOfCurrentWeek = new Date(today);
                firstDayOfCurrentWeek.setDate(today.getDate() - (todayDayOfWeek === 0 ? 7 : todayDayOfWeek));
                startDate = firstDayOfCurrentWeek.toISOString().split('T')[0];
                endDate = formattedToday;
                break;

            case 'lastWeek':
                const lastWeekStart = new Date(today);
                lastWeekStart.setDate(today.getDate() - todayDayOfWeek - 7);
                const lastWeekEnd = new Date(lastWeekStart);
                lastWeekEnd.setDate(lastWeekStart.getDate() + 6);
                startDate = lastWeekStart.toISOString().split('T')[0];
                endDate = lastWeekEnd.toISOString().split('T')[0];
                break;

            case 'thisMonth':
                const firstDayOfMonth = new Date(today.getFullYear(), today.getMonth(), 2);
                startDate = firstDayOfMonth.toISOString().split('T')[0];
                endDate = formattedToday;
                break;

            case 'lastMonth':
                const firstDayOfLastMonth = new Date(today.getFullYear(), today.getMonth() - 1, 2);
                const lastDayOfLastMonth = new Date(today.getFullYear(), today.getMonth(), 1);
                startDate = firstDayOfLastMonth.toISOString().split('T')[0];
                endDate = lastDayOfLastMonth.toISOString().split('T')[0];
                break;

            default:
                startDate = formattedSevenDaysAgo;
                endDate = formattedToday;
        }
        
        device_id = document.getElementById('device_id').value;
        fetchBarChartData(startDate, endDate, device_id);
    }
});

// Event listener for custom date range picker
document.getElementById('customRangeButton1').addEventListener('click', function () {
    const dateRange = getSelectedDateRange();
    device_id = document.getElementById('device_id').value;
    
    if (dateRange) {
        const startDateFormatted = formatDateToYMD(dateRange.startDate);
        const endDateFormatted = formatDateToYMD(dateRange.endDate);
        fetchBarChartData(startDateFormatted, endDateFormatted, device_id);
    } else {
        load_chart(device_id);
    }
});

function formatDateToYMD(date) {
    const year = date.getFullYear();
    const month = String(date.getMonth() + 1).padStart(2, '0');
    const day = String(date.getDate()).padStart(2, '0');
    return `${year}-${month}-${day}`;
}

function load_chart(device_id) {
    startDate = formattedSevenDaysAgo;
    endDate = formattedToday;
    fetchBarChartData(startDate, endDate, device_id);
}