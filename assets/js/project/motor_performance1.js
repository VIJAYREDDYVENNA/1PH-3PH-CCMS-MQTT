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

    // Update date range values
    updateDateRange();
    
    // Generate random data just for water flow trends and platform distribution
    generateRandomDataOnLoad();
    
    // Fetch other data from API except for the charts with random data
    fetchSummaryMetrics();
    fetchMotorRuntimeData();
    fetchMotorEnergyData();
    fetchPerformanceMetricsData();

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

        // Fetch all data except for water flow trends and platform distribution
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

            // No need to update water flow trends chart as it uses random data
        });
    });
}

// Update date range values from the date picker
function updateDateRange() {
    const dateRange = $('#dateRangePicker').data('daterangepicker');
    startDate = dateRange.startDate.format('YYYY-MM-DD');
    endDate = dateRange.endDate.format('YYYY-MM-DD');
}

// Fetch all data from the API except for the two charts with random data
function fetchAllData() {
    // Fetch summary metrics
    fetchSummaryMetrics();

    // Only fetch and update motor runtime and motor energy data
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
            // document.getElementById("total_water").innerHTML = `${numberWithCommas(data.total_water)} L`;
            document.getElementById("total_energy").innerHTML = (data.total_energy.toFixed(2))+ " kWh";

        })
        .catch(error => {
            console.error('API call error:', error);
        });
}

// Fetch water flow trends data from API - not used with random data
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

// Fetch distribution by motors data from API - not used with random data
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

// Update Water Flow Trends Chart
function updateWaterFlowTrendsChart() {
    if (!waterFlowTrendsChart) return;

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

// Function to generate random data only for water flow trends and platform distribution charts
function generateRandomDataOnLoad() {
    console.log("Generating random data for water flow trends and platform distribution");
    
    // Generate random total water value (between 800,000 and 1,500,000)
    const randomTotalWater = Math.floor(Math.random() * 700000) + 800000;
    document.getElementById("total_water").innerHTML = `${numberWithCommas(randomTotalWater)} L`;
    
    // Generate random water flow trends data
    chartData.waterFlowTrends = {
        labels: Array.from({ length: 30 }, (_, i) => moment().subtract(29 - i, 'days').format('MMM D')),
        flowData: generateRandomData(30, 40, 60)
    };
    
    // Generate random platform distribution data
    chartData.platformDistribution = {
        labels: ['MOTOR 1', 'MOTOR 2', 'MOTOR 3', 'MOTOR 4', 'MOTOR 5', 'MOTOR 6'],
        data: [25, 20, 18, 12, 10, 15]
    };
    
    // Initialize only the water flow trends and platform distribution charts
    initializeWaterFlowTrendsChart();
    initializePlatformDistributionChart();
    
    // Hide loading state if it was showing
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
        console.error('API calls failed after multiple attempts.');
        hideLoadingState();
    }
}

// Add event listener for window resize to redraw charts
window.addEventListener('resize', function () {
    if (waterFlowTrendsChart) waterFlowTrendsChart.resize();
    if (platformDistributionChart) platformDistributionChart.resize();
    if (motorRuntimeChart) motorRuntimeChart.resize();
    if (motorEnergyChart) motorEnergyChart.resize();
});