//  let waterFlowTrendsChart, platformDistributionChart, motorRuntimeChart,
//                     modeComparisonChart, reliabilityChart;

//                 // Chart data cache
//                 const chartData = {
//                     waterFlowTrends: {},
//                     platformDistribution: null,
//                     motorRuntime: null,
//                     modeComparison: null,
//                     reliability: null
//                 };

//                 // Flag to prevent continuous updates
//                 let isUpdating = false;

//                 document.addEventListener('DOMContentLoaded', function() {
//                     // Initialize date range picker
//                     initializeDateRangePicker();

//                     // Fetch initial data
//                     fetchChartData();

//                     // Setup event listeners
//                     setupEventListeners();
//                 });

//                 // Initialize date range picker
//                 function initializeDateRangePicker() {
//                     // Ensure $ and moment are available
//                     if (typeof $ === 'undefined' || typeof moment === 'undefined') {
//                         console.error('jQuery and/or Moment.js are not loaded. Date range picker will not function.');
//                         return;
//                     }

//                     $('#dateRangePicker').daterangepicker({
//                         startDate: moment().subtract(29, 'days'),
//                         endDate: moment(),
//                         ranges: {
//                             'Today': [moment(), moment()],
//                             'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
//                             'Last 7 Days': [moment().subtract(6, 'days'), moment()],
//                             'Last 30 Days': [moment().subtract(29, 'days'), moment()],
//                             'This Month': [moment().startOf('month'), moment().endOf('month')],
//                             'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
//                         }
//                     });
//                 }

//                 // Setup event listeners
//                 function setupEventListeners() {
//                     // Update report button
//                     document.getElementById('updateReportBtn').addEventListener('click', function() {
//                         if (isUpdating) return;

//                         // Simulate loading data
//                         showLoadingState();
//                         isUpdating = true;

//                         setTimeout(() => {
//                             // Fetch and update charts with new data
//                             fetchChartData();
//                             hideLoadingState();
//                             isUpdating = false;
//                         }, 1000);
//                     });

//                     // Export report button
//                     document.getElementById('exportReportBtn').addEventListener('click', function() {
//                         alert('Report export functionality would be implemented here.');
//                     });

//                     // Period buttons for water flow trends
//                     document.querySelectorAll('[data-period]').forEach(button => {
//                         button.addEventListener('click', function() {
//                             if (isUpdating) return;

//                             // Remove active class from all buttons
//                             document.querySelectorAll('[data-period]').forEach(btn => {
//                                 btn.classList.remove('active');
//                             });

//                             // Add active class to clicked button
//                             this.classList.add('active');

//                             // Update chart based on selected period
//                             updateWaterFlowTrendsChart(this.dataset.period);
//                         });
//                     });
//                 }

//                 // Fetch all chart data
//                 function fetchChartData() {
//                     // In a real application, this would make API calls to fetch data
//                     // For this demo, we'll generate random data

//                     // Generate data for all periods at once
//                     chartData.waterFlowTrends = {
//                         daily: {
//                             labels: Array.from({
//                                 length: 30
//                             }, (_, i) => moment().subtract(29 - i, 'days').format('MMM D')),
//                             autoMode: generateRandomData(30, 40, 60),
//                             oemMode: generateRandomData(30, 50, 70)
//                         },
//                         weekly: {
//                             labels: Array.from({
//                                 length: 12
//                             }, (_, i) => `Week ${i + 1}`),
//                             autoMode: generateRandomData(12, 45, 65),
//                             oemMode: generateRandomData(12, 55, 75)
//                         },
//                         monthly: {
//                             labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
//                             autoMode: generateRandomData(12, 50, 70),
//                             oemMode: generateRandomData(12, 60, 80)
//                         }
//                     };

//                     chartData.platformDistribution = {
//                         labels: ['MOTOR 1', 'MOTOR 2', 'MOTOR 3', 'MOTOR 4', 'MOTOR 5', 'MOTOR 6'],
//                         data: [25, 20, 18, 12, 10, 15]
//                     };

//                     chartData.motorRuntime = {
//                         labels: ['MOTOR 1', 'MOTOR 2', 'MOTOR 3', 'MOTOR 4', 'MOTOR 5', 'MOTOR 6'],
//                         runtime: [342.5, 298.2, 312.7, 156.3, 142.8, 187.5]
//                     };

//                     chartData.modeComparison = {
//                         labels: [
//                             'Water Flow Rate',
//                             'Energy Efficiency',
//                             'Operational Cost',
//                             'Maintenance Needs',
//                             'Response Time',
//                             'Reliability'
//                         ],
//                         autoMode: [70, 85, 75, 90, 65, 80],
//                         oemMode: [90, 65, 60, 70, 95, 75]
//                     };

//                     chartData.reliability = {
//                         labels: Array.from({
//                             length: 12
//                         }, (_, i) => moment().subtract(11 - i, 'months').format('MMM')),
//                         reliability: generateRandomData(12, 85, 98)
//                     };

//                     // Initialize or update charts
//                     initializeCharts();
//                 }

//                 // Initialize all charts
//                 function initializeCharts() {
//                     initializeWaterFlowTrendsChart();
//                     initializePlatformDistributionChart();
//                     initializeMotorRuntimeChart();
//                     // initializeModeComparisonChart();
//                     initializeReliabilityChart();
//                 }

//                 // Show loading state
//                 function showLoadingState() {
//                     // Add loading overlay or spinner
//                     const loadingOverlay = document.createElement('div');
//                     loadingOverlay.id = 'loadingOverlay';
//                     loadingOverlay.className = 'position-fixed top-0 start-0 w-100 h-100 d-flex justify-content-center align-items-center bg-white bg-opacity-75';
//                     loadingOverlay.style.zIndex = '9999';
//                     loadingOverlay.innerHTML = `
//                             <div class="spinner-border text-primary" role="status">
//                                 <span class="visually-hidden">Loading...</span>
//                             </div>
//                         `;
//                     document.body.appendChild(loadingOverlay);
//                 }

//                 // Hide loading state
//                 function hideLoadingState() {
//                     const loadingOverlay = document.getElementById('loadingOverlay');
//                     if (loadingOverlay) {
//                         loadingOverlay.remove();
//                     }
//                 }

//                 // Initialize Water Flow Trends Chart
//                 function initializeWaterFlowTrendsChart() {
//                     const ctx = document.getElementById('waterFlowTrendsChart').getContext('2d');
//                     const data = chartData.waterFlowTrends.daily;

//                     // Destroy existing chart if it exists
//                     if (waterFlowTrendsChart) {
//                         waterFlowTrendsChart.destroy();
//                     }

//                     waterFlowTrendsChart = new Chart(ctx, {
//                         type: 'line',
//                         data: {
//                             labels: data.labels,
//                             datasets: [{
//                                     label: 'Average Water Flow',
//                                     data: data.autoMode,
//                                     borderColor: '#0d6efd',
//                                     backgroundColor: 'rgba(13, 110, 253, 0.1)',
//                                     borderWidth: 2,
//                                     fill: true,
//                                     tension: 0.4
//                                 },
//                                 // {
//                                 //     label: 'OEM Mode',
//                                 //     data: data.oemMode,
//                                 //     borderColor: '#ffc107',
//                                 //     backgroundColor: 'rgba(255, 193, 7, 0.1)',
//                                 //     borderWidth: 2,
//                                 //     fill: true,
//                                 //     tension: 0.4
//                                 // }
//                             ]
//                         },
//                         options: {
//                             responsive: true,
//                             maintainAspectRatio: false,
//                             scales: {
//                                 y: {
//                                     beginAtZero: true,
//                                     title: {
//                                         display: true,
//                                         text: 'Flow Rate (LPM)'
//                                     }
//                                 }
//                             },
//                             plugins: {
//                                 tooltip: {
//                                     mode: 'index',
//                                     intersect: false
//                                 },
//                                 legend: {
//                                     position: 'top'
//                                 }
//                             },
//                             animation: {
//                                 duration: 1000 // Controlled animation duration
//                             }
//                         }
//                     });
//                 }

//                 // Update Water Flow Trends Chart based on period
//                 function updateWaterFlowTrendsChart(period) {
//                     if (!waterFlowTrendsChart || !chartData.waterFlowTrends[period]) return;

//                     const data = chartData.waterFlowTrends[period];

//                     waterFlowTrendsChart.data.labels = data.labels;
//                     waterFlowTrendsChart.data.datasets[0].data = data.autoMode;
//                     // waterFlowTrendsChart.data.datasets[1].data = data.oemMode;
//                     waterFlowTrendsChart.update();
//                 }

//                 // Initialize Platform Distribution Chart
//                 function initializePlatformDistributionChart() {
//                     const ctx = document.getElementById('platformDistributionChart').getContext('2d');
//                     const data = chartData.platformDistribution;

//                     // Destroy existing chart if it exists
//                     if (platformDistributionChart) {
//                         platformDistributionChart.destroy();
//                     }

//                     platformDistributionChart = new Chart(ctx, {
//                         type: 'doughnut',
//                         data: {
//                             labels: data.labels,
//                             datasets: [{
//                                 data: data.data,
//                                 backgroundColor: [
//                                     '#0d6efd',
//                                     '#6610f2',
//                                     '#6f42c1',
//                                     '#d63384',
//                                     '#fd7e14',
//                                     '#20c997'
//                                 ],
//                                 borderWidth: 1
//                             }]
//                         },
//                         options: {
//                             responsive: true,
//                             maintainAspectRatio: false,
//                             plugins: {
//                                 legend: {
//                                     position: 'right'
//                                 },
//                                 tooltip: {
//                                     callbacks: {
//                                         label: function(context) {
//                                             const label = context.label || '';
//                                             const value = context.raw || 0;
//                                             return `${label}: ${value}% of total water distributed`;
//                                         }
//                                     }
//                                 }
//                             },
//                             animation: {
//                                 duration: 1000 // Controlled animation duration
//                             }
//                         }
//                     });
//                 }

//                 // Initialize Motor Runtime Chart - Only showing runtime hours
//                 function initializeMotorRuntimeChart() {
//                     const ctx = document.getElementById('motorRuntimeChart').getContext('2d');
//                     const data = chartData.motorRuntime;

//                     // Destroy existing chart if it exists
//                     if (motorRuntimeChart) {
//                         motorRuntimeChart.destroy();
//                     }

//                     motorRuntimeChart = new Chart(ctx, {
//                         type: 'bar',
//                         data: {
//                             labels: data.labels,
//                             datasets: [{
//                                 label: 'Runtime Hours',
//                                 data: data.runtime,
//                                 backgroundColor: '#0d6efd',
//                             }]
//                         },
//                         options: {
//                             responsive: true,
//                             maintainAspectRatio: false,
//                             scales: {
//                                 y: {
//                                     beginAtZero: true,
//                                     title: {
//                                         display: true,
//                                         text: 'Runtime Hours'
//                                     }
//                                 }
//                             },
//                             plugins: {
//                                 tooltip: {
//                                     callbacks: {
//                                         label: function(context) {
//                                             return `Runtime: ${context.raw} hours`;
//                                         }
//                                     }
//                                 },
//                                 legend: {
//                                     display: true,
//                                     position: 'top'
//                                 }
//                             },
//                             animation: {
//                                 duration: 1000 // Controlled animation duration
//                             }
//                         }
//                     });
//                 }

//                 // Initialize Mode Comparison Chart
//                 function initializeModeComparisonChart() {
//                     const ctx = document.getElementById('modeComparisonChart').getContext('2d');
//                     const data = chartData.modeComparison;

//                     // Destroy existing chart if it exists
//                     if (modeComparisonChart) {
//                         modeComparisonChart.destroy();
//                     }

//                     modeComparisonChart = new Chart(ctx, {
//                         type: 'radar',
//                         data: {
//                             labels: data.labels,
//                             datasets: [{
//                                     label: 'Auto Mode',
//                                     data: data.autoMode,
//                                     backgroundColor: 'rgba(13, 110, 253, 0.2)',
//                                     borderColor: '#0d6efd',
//                                     pointBackgroundColor: '#0d6efd',
//                                     pointBorderColor: '#fff',
//                                     pointHoverBackgroundColor: '#fff',
//                                     pointHoverBorderColor: '#0d6efd'
//                                 },
//                                 {
//                                     label: 'OEM Mode',
//                                     data: data.oemMode,
//                                     backgroundColor: 'rgba(255, 193, 7, 0.2)',
//                                     borderColor: '#ffc107',
//                                     pointBackgroundColor: '#ffc107',
//                                     pointBorderColor: '#fff',
//                                     pointHoverBackgroundColor: '#fff',
//                                     pointHoverBorderColor: '#ffc107'
//                                 }
//                             ]
//                         },
//                         options: {
//                             responsive: true,
//                             maintainAspectRatio: false,
//                             scales: {
//                                 r: {
//                                     angleLines: {
//                                         display: true
//                                     },
//                                     suggestedMin: 0,
//                                     suggestedMax: 100
//                                 }
//                             },
//                             animation: {
//                                 duration: 1000 // Controlled animation duration
//                             }
//                         }
//                     });
//                 }

//                 // Initialize Reliability Chart - Only showing reliability, no maintenance
//                 function initializeReliabilityChart() {
//                     const ctx = document.getElementById('reliabilityChart').getContext('2d');
//                     const data = chartData.reliability;

//                     // Destroy existing chart if it exists
//                     if (reliabilityChart) {
//                         reliabilityChart.destroy();
//                     }

//                     reliabilityChart = new Chart(ctx, {
//                         type: 'line',
//                         data: {
//                             labels: data.labels,
//                             datasets: [{
//                                 label: 'System Reliability (%)',
//                                 data: data.reliability,
//                                 borderColor: '#0d6efd',
//                                 backgroundColor: 'rgba(13, 110, 253, 0.1)',
//                                 borderWidth: 2,
//                                 fill: true,
//                                 tension: 0.4
//                             }]
//                         },
//                         options: {
//                             responsive: true,
//                             maintainAspectRatio: false,
//                             scales: {
//                                 y: {
//                                     beginAtZero: false,
//                                     min: 80,
//                                     max: 100,
//                                     title: {
//                                         display: true,
//                                         text: 'Reliability (%)'
//                                     }
//                                 }
//                             },
//                             plugins: {
//                                 tooltip: {
//                                     callbacks: {
//                                         label: function(context) {
//                                             return `Reliability: ${context.raw}%`;
//                                         }
//                                     }
//                                 }
//                             },
//                             animation: {
//                                 duration: 1000 // Controlled animation duration
//                             }
//                         }
//                     });
//                 }

//                 // Helper function to generate random data
//                 function generateRandomData(length, min, max) {
//                     return Array.from({
//                         length
//                     }, () => Math.floor(Math.random() * (max - min + 1)) + min);
//                 }

let waterFlowTrendsChart, platformDistributionChart, motorRuntimeChart, motorEnergyChart;

// Chart data cache
const chartData = {
    waterFlowTrends: {},
    platformDistribution: null,
    motorRuntime: null,
    motorEnergy: null
};

// Flag to prevent continuous updates
let isUpdating = false;

// Date range values
let startDate, endDate;

document.addEventListener('DOMContentLoaded', function () {
    // Initialize date range picker
    initializeDateRangePicker();

    // Fetch initial data
    updateDateRange();
    fetchAllData();

    // Setup event listeners
    setupEventListeners();
});

// Initialize date range picker
function initializeDateRangePicker() {
    // Ensure $ and moment are available
    if (typeof $ === 'undefined' || typeof moment === 'undefined') {
        console.error('jQuery and/or Moment.js are not loaded. Date range picker will not function.');
        return;
    }

    $('#dateRangePicker').daterangepicker({
        startDate: moment().subtract(29, 'days'),
        endDate: moment(),
        ranges: {
            'Today': [moment(), moment()],
            'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
            'Last 7 Days': [moment().subtract(6, 'days'), moment()],
            'Last 30 Days': [moment().subtract(29, 'days'), moment()],
            'This Month': [moment().startOf('month'), moment().endOf('month')],
            'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
        }
    });
}

// Setup event listeners
function setupEventListeners() {
    // Update report button
    document.getElementById('updateReportBtn').addEventListener('click', function () {
        if (isUpdating) return;

        // Update date range values
        updateDateRange();

        // Simulate loading data
        showLoadingState();
        isUpdating = true;

        // Fetch all data from API
        fetchAllData();
    });

    // Export report button
    document.getElementById('exportReportBtn').addEventListener('click', function () {
        alert('Report export functionality would be implemented here.');
    });

    // Period buttons for water flow trends
    document.querySelectorAll('[data-period]').forEach(button => {
        button.addEventListener('click', function () {
            if (isUpdating) return;

            // Remove active class from all buttons
            document.querySelectorAll('[data-period]').forEach(btn => {
                btn.classList.remove('active');
            });

            // Add active class to clicked button
            this.classList.add('active');

            // Update chart based on selected period
            // fetchWaterFlowTrendsData(this.dataset.period);
                        fetchWaterFlowTrendsData();

        });
    });
}

// Update date range values from the date picker
function updateDateRange() {
    const dateRange = $('#dateRangePicker').data('daterangepicker');
    startDate = dateRange.startDate.format('YYYY-MM-DD');
    endDate = dateRange.endDate.format('YYYY-MM-DD');
}

// Fetch all data from the API
function fetchAllData() {
    // Fetch summary metrics
    fetchSummaryMetrics();

    // Fetch chart data
    // const activePeriod = document.querySelector('[data-period].active').dataset.period;
    // fetchWaterFlowTrendsData(activePeriod);
        fetchWaterFlowTrendsData();

    fetchDistributionByMotorsData();
    fetchMotorRuntimeData();
    fetchMotorEnergyData();
    fetchPerformanceMetricsData();
}

// Show loading state
function showLoadingState() {
    // Add loading overlay or spinner
    const loadingOverlay = document.createElement('div');
    loadingOverlay.id = 'loadingOverlay';
    loadingOverlay.className = 'position-fixed top-0 start-0 w-100 h-100 d-flex justify-content-center align-items-center bg-white bg-opacity-75';
    loadingOverlay.style.zIndex = '9999';
    loadingOverlay.innerHTML = `
        <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
    `;
    document.body.appendChild(loadingOverlay);
}

// Hide loading state
function hideLoadingState() {
    const loadingOverlay = document.getElementById('loadingOverlay');
    if (loadingOverlay) {
        loadingOverlay.remove();
    }
    isUpdating = false;
}

// Fetch summary metrics from API
function fetchSummaryMetrics() {
    fetch('../motor-performance-data/code/motor_metrics_data.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            action: 'getSummaryMetrics',
            startDate: startDate,
            endDate: endDate
        })
    })
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                console.error('Error fetching summary metrics:', data.error);
                return;
            }
            document.getElementById("total_water").innerHTML = `${numberWithCommas(data.total_water)} L`;
            document.getElementById("total_energy").innerHTML = (data.total_energy.toFixed(2))+ " kWh";

        })
        .catch(error => {
            console.error('API call error:', error);
        });
}

// Fetch water flow trends data from API
function fetchWaterFlowTrendsData() {
    fetch('../motor-performance-data/code/motor_metrics_data.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            action: 'getWaterFlowTrends',
            startDate: startDate,
            endDate: endDate
            
        })
    })
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                console.error('Error fetching water flow trends:', data.error);
                return;
            }

            // Store the data
            chartData.waterFlowTrends = data;

            // Update or initialize the chart
            if (waterFlowTrendsChart) {
                updateWaterFlowTrendsChart();
            } else {
                initializeWaterFlowTrendsChart();
            }
        })
        .catch(error => {
            console.error('API call error:', error);
        });
}

// Fetch distribution by motors data from API
function fetchDistributionByMotorsData() {
    fetch('../motor-performance-data/code/motor_metrics_data.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            action: 'getDistributionByMotors',
            startDate: startDate,
            endDate: endDate
        })
    })
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                console.error('Error fetching distribution by motors:', data.error);
                return;
            }

            // Store the data
            chartData.platformDistribution = data;

            // Update or initialize the chart
            if (platformDistributionChart) {
                updatePlatformDistributionChart();
            } else {
                initializePlatformDistributionChart();
            }
        })
        .catch(error => {
            console.error('API call error:', error);
        });
}

// Fetch motor runtime data from API
function fetchMotorRuntimeData() {
    fetch('../motor-performance-data/code/motor_metrics_data.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            action: 'getMotorRuntimeComparison',
            startDate: startDate,
            endDate: endDate
        })
    })
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                console.error('Error fetching motor runtime data:', data.error);
                return;
            }

            // Store the data
            chartData.motorRuntime = data;

            // Update or initialize the chart
            if (motorRuntimeChart) {
                updateMotorRuntimeChart();
            } else {
                initializeMotorRuntimeChart();
            }
        })
        .catch(error => {
            console.error('API call error:', error);
        });
}

// Fetch motor energy data from API
function fetchMotorEnergyData() {
    fetch('../motor-performance-data/code/motor_metrics_data.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            action: 'getMotorEnergyComparison',
            startDate: startDate,
            endDate: endDate
        })
    })
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                console.error('Error fetching motor energy data:', data.error);
                return;
            }

            // Store the data
            chartData.motorEnergy = data;

            // Update or initialize the chart
            if (motorEnergyChart) {
                updateMotorEnergyChart();
            } else {
                initializeMotorEnergyChart();
            }
        })
        .catch(error => {
            console.error('API call error:', error);
        });
}

// Fetch performance metrics data from API
function fetchPerformanceMetricsData() {
    fetch('../motor-performance-data/code/motor_metrics_data.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            action: 'getPerformanceMetrics',
            startDate: startDate,
            endDate: endDate
        })
    })
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                console.error('Error fetching performance metrics:', data.error);
                hideLoadingState();
                return;
            }

            // Update the metrics table
            updatePerformanceMetricsTable(data.metrics);

            // All data fetched, hide loading state
            hideLoadingState();
        })
        .catch(error => {
            console.error('API call error:', error);
            hideLoadingState();
        });
}

// Initialize Water Flow Trends Chart
function initializeWaterFlowTrendsChart() {
    const ctx = document.getElementById('waterFlowTrendsChart').getContext('2d');
    // const period = document.querySelector('[data-period].active').dataset.period;
    const data = chartData.waterFlowTrends;

    if (!data) return;

    // Destroy existing chart if it exists
    if (waterFlowTrendsChart) {
        waterFlowTrendsChart.destroy();
    }

    waterFlowTrendsChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: data.labels,
            datasets: [{
                label: 'Average Water Flow',
                data: data.flowData,
                borderColor: '#0d6efd',
                backgroundColor: 'rgba(13, 110, 253, 0.1)',
                borderWidth: 2,
                fill: true,
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Flow Rate (LPM)'
                    }
                }
            },
            plugins: {
                tooltip: {
                    mode: 'index',
                    intersect: false
                },
                legend: {
                    position: 'top'
                }
            },
            animation: {
                duration: 1000 // Controlled animation duration
            }
        }
    });
}

// Update Water Flow Trends Chart based on period
function updateWaterFlowTrendsChart() {
    if (!waterFlowTrendsChart ) return;

    const data = chartData.waterFlowTrends;

    waterFlowTrendsChart.data.labels = data.labels;
    waterFlowTrendsChart.data.datasets[0].data = data.flowData;
    waterFlowTrendsChart.update();
}

// Initialize Platform Distribution Chart
function initializePlatformDistributionChart() {
    const ctx = document.getElementById('platformDistributionChart').getContext('2d');
    const data = chartData.platformDistribution;

    if (!data) return;

    // Destroy existing chart if it exists
    if (platformDistributionChart) {
        platformDistributionChart.destroy();
    }

    platformDistributionChart = new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: data.labels,
            datasets: [{
                data: data.data,
                backgroundColor: [
                    '#0d6efd',
                    '#6610f2',
                    '#6f42c1',
                    '#d63384',
                    '#fd7e14',
                    '#20c997'
                ],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'right'
                },
                tooltip: {
                    callbacks: {
                        label: function (context) {
                            const label = context.label || '';
                            const value = context.raw || 0;
                            return `${label}: ${value}% of total water distributed`;
                        }
                    }
                }
            },
            animation: {
                duration: 1000 // Controlled animation duration
            }
        }
    });
}

// Update Platform Distribution Chart
function updatePlatformDistributionChart() {
    if (!platformDistributionChart || !chartData.platformDistribution) return;

    const data = chartData.platformDistribution;

    platformDistributionChart.data.labels = data.labels;
    platformDistributionChart.data.datasets[0].data = data.data;
    platformDistributionChart.update();
}

// Initialize Motor Runtime Chart
function initializeMotorRuntimeChart() {
    const ctx = document.getElementById('motorRuntimeChart').getContext('2d');
    const data = chartData.motorRuntime;

    if (!data) return;

    // Destroy existing chart if it exists
    if (motorRuntimeChart) {
        motorRuntimeChart.destroy();
    }

    motorRuntimeChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: data.labels,
            datasets: [{
                label: 'Runtime Hours',
                data: data.runtime,
                backgroundColor: '#0d6efd',
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Runtime Hours'
                    }
                }
            },
            plugins: {
                tooltip: {
                    callbacks: {
                        label: function (context) {
                            return `Runtime: ${context.raw} hours`;
                        }
                    }
                },
                legend: {
                    display: true,
                    position: 'top'
                }
            },
            animation: {
                duration: 1000 // Controlled animation duration
            }
        }
    });
}

// Update Motor Runtime Chart
function updateMotorRuntimeChart() {
    if (!motorRuntimeChart || !chartData.motorRuntime) return;

    const data = chartData.motorRuntime;

    motorRuntimeChart.data.labels = data.labels;
    motorRuntimeChart.data.datasets[0].data = data.runtime;
    motorRuntimeChart.update();
}

// Initialize Motor Energy Chart
function initializeMotorEnergyChart() {
    // Find the canvas with id that ends with 'motorRuntimeChart' in the energy comparison section
    const energyChartCanvas = document.querySelector('.col-12.col-lg-6:nth-of-type(2) .chart-container canvas');

    if (!energyChartCanvas || !chartData.motorEnergy) return;

    const ctx = energyChartCanvas.getContext('2d');
    const data = chartData.motorEnergy;

    // Destroy existing chart if it exists
    if (motorEnergyChart) {
        motorEnergyChart.destroy();
    }

    motorEnergyChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: data.labels,
            datasets: [{
                label: 'Energy Consumption (kWh)',
                data: data.energy,
                backgroundColor: '#20c997',
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Energy (kWh)'
                    }
                }
            },
            plugins: {
                tooltip: {
                    callbacks: {
                        label: function (context) {
                            return `Energy: ${context.raw} kWh`;
                        }
                    }
                },
                legend: {
                    display: true,
                    position: 'top'
                }
            },
            animation: {
                duration: 1000 // Controlled animation duration
            }
        }
    });
}

// Update Motor Energy Chart
// Update Motor Energy Chart
function updateMotorEnergyChart() {
    if (!motorEnergyChart || !chartData.motorEnergy) return;

    const data = chartData.motorEnergy;

    motorEnergyChart.data.labels = data.labels;
    motorEnergyChart.data.datasets[0].data = data.energy;
    motorEnergyChart.update();
}

// Update Performance Metrics Table
function updatePerformanceMetricsTable(metrics) {
    const tbody = document.querySelector('#performanceMetricsTable');
    if (!tbody) return;

    // Clear existing rows
    tbody.innerHTML = '';

    // Add new rows
    metrics.forEach(metric => {
        const row = document.createElement('tr');

        // Add cells
        row.innerHTML = `
            <td>${metric.motor_id.toUpperCase()}</td>
            <td>${metric.total_runtime} hrs</td>
            <td>${numberWithCommas(metric.total_water)} L</td>
            <td>${numberWithCommas(metric.total_energy)} kWh</td>
        
        `;
        tbody.appendChild(row);
    });
    // <td>${calculateEfficiency(metric.total_water, metric.total_energy).toFixed(2)} L/kWh</td>
}

// Calculate efficiency (water delivered per kWh)
function calculateEfficiency(water, energy) {
    if (!energy || energy === 0) return 0;
    return water / energy;
}

// Format numbers with commas for thousands separator
function numberWithCommas(x) {
    return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
}

// Helper function to generate random data (for testing only)
function generateRandomData(length, min, max) {
    return Array.from({ length }, () => Math.floor(Math.random() * (max - min + 1)) + min);
}

// Fallback function if the API is not available
function handleApiFailure() {
    console.warn("API connection failed. Falling back to simulated data for demonstration purposes.");

    // Generate simulated data
    // chartData.waterFlowTrends = {
    //     daily: {
    //         labels: Array.from({ length: 30 }, (_, i) => moment().subtract(29 - i, 'days').format('MMM D')),
    //         flowData: generateRandomData(30, 40, 60)
    //     },
    //     weekly: {
    //         labels: Array.from({ length: 12 }, (_, i) => `Week ${i + 1}`),
    //         flowData: generateRandomData(12, 45, 65)
    //     },
    //     monthly: {
    //         labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
    //         flowData: generateRandomData(12, 50, 70)
    //     }
    // };
       chartData.waterFlowTrends = {
       
            labels: Array.from({ length: 30 }, (_, i) => moment().subtract(29 - i, 'days').format('MMM D')),
            flowData: generateRandomData(30, 40, 60)
       
    };

    chartData.platformDistribution = {
        labels: ['MOTOR 1', 'MOTOR 2', 'MOTOR 3', 'MOTOR 4', 'MOTOR 5', 'MOTOR 6'],
        data: [25, 20, 18, 12, 10, 15]
    };

    chartData.motorRuntime = {
        labels: ['MOTOR 1', 'MOTOR 2', 'MOTOR 3', 'MOTOR 4', 'MOTOR 5', 'MOTOR 6'],
        runtime: [342.5, 298.2, 312.7, 156.3, 142.8, 187.5]
    };

    chartData.motorEnergy = {
        labels: ['MOTOR 1', 'MOTOR 2', 'MOTOR 3', 'MOTOR 4', 'MOTOR 5', 'MOTOR 6'],
        energy: [456.8, 387.3, 421.5, 198.2, 175.6, 231.9]
    };

    // Update document with simulated values
    document.querySelector('.card:nth-of-type(1) h2').textContent = '1,450,324 L';
    document.querySelector('.card:nth-of-type(2) h2').textContent = '1,871 kWh';

    // Generate performance metrics
    const metrics = [];
    for (let i = 1; i <= 6; i++) {
        const runtime = parseFloat((Math.random() * 300 + 100).toFixed(1));
        const water = Math.floor(Math.random() * 300000 + 100000);
        const energy = Math.floor(Math.random() * 400 + 100);
        const speed = parseFloat((Math.random() * 500 + 1500).toFixed(1));

        metrics.push({
            motor_id: `MOTOR ${i}`,
            total_runtime: runtime,
            total_water: water,
            total_energy: energy,
            avg_speed: speed
        });
    }

    // Update table with simulated metrics
    updatePerformanceMetricsTable(metrics);

    // Initialize charts with simulated data
    initializeWaterFlowTrendsChart();
    initializePlatformDistributionChart();
    initializeMotorRuntimeChart();
    initializeMotorEnergyChart();

    // Hide loading state
    hideLoadingState();
}

// Error handling for API calls
function handleApiError(error, retryCount = 0) {
    if (retryCount < 3) {
        console.warn(`API call failed, retrying (${retryCount + 1}/3)...`);
        setTimeout(() => {
            fetchAllData(retryCount + 1);
        }, 1000);
    } else {
        console.error('API calls failed after multiple attempts. Switching to simulated data.');
        handleApiFailure();
    }
}

// Add event listener for window resize to redraw charts
window.addEventListener('resize', function () {
    if (waterFlowTrendsChart) waterFlowTrendsChart.resize();
    if (platformDistributionChart) platformDistributionChart.resize();
    if (motorRuntimeChart) motorRuntimeChart.resize();
    if (motorEnergyChart) motorEnergyChart.resize();
});