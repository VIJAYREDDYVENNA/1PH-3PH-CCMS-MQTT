<?php
require_once 'config-path.php';
require_once '../session/session-manager.php';
SessionManager::checkSession();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lighting Management Dashboard</title>
    <style>
        /* Base styles */
        body {
            background-color: #f8f9fa;
            overflow-x: hidden;
        }

        /* Compact card styles */
        .card {
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
            height: 100%;
            margin-bottom: 0.75rem;
        }

        .card-header {
            padding: 0.5rem 1rem;
            background-color: #fff;
            border-bottom: 1px solid rgba(0, 0, 0, 0.125);
        }

        .card-body {
            padding: 0.75rem;
        }

        .card-title {
            margin-bottom: 0;
            font-size: 1rem;
            font-weight: 500;
        }

        /* Compact chart container */
        .chart-container {
            height: 100px;
            position: relative;
            margin-top: 0.5rem;
        }

        /* Stats display */
        .stats-display {
            display: flex;
            justify-content: space-between;
            margin-bottom: 0.5rem;
        }

        .stat-item {
            text-align: center;
            flex: 1;
        }

        .stat-value {
            font-size: 1.25rem;
            font-weight: 600;
            margin: 0;
        }

        .stat-label {
            font-size: 0.75rem;
            color: #6c757d;
            margin: 0;
        }

        /* Status indicators */
        .status-indicator {
            padding: 0.35rem;
            border-radius: 0.25rem;
            text-align: center;
        }

        .bg-success-light {
            background-color: rgba(25, 135, 84, 0.1);
        }

        .bg-danger-light {
            background-color: rgba(220, 53, 69, 0.1);
        }

        .bg-primary-light {
            background-color: rgba(13, 110, 253, 0.1);
        }

        .bg-secondary-light {
            background-color: rgba(108, 117, 125, 0.1);
        }

        .text-success {
            color: #198754;
        }

        .text-danger {
            color: #dc3545;
        }

        .text-primary {
            color: #0d6efd;
        }

        .text-secondary {
            color: #6c757d;
        }

        /* Map container */
        .map-container {
            height: 200px;
            background-color: #f1f5f9;
            border-radius: 0.25rem;
            margin-bottom: 0.5rem;
        }

        /* Updates panel */
        .updates-container {
            max-height: 250px;
            overflow-y: auto;
        }

        .update-item {
            padding: 0.5rem;
            margin-bottom: 0.5rem;
            border-radius: 0.25rem;
            display: flex;
            align-items: flex-start;
        }

        .update-icon {
            margin-right: 0.5rem;
            font-size: 1rem;
        }

        .update-content {
            flex: 1;
        }

        .update-message {
            font-weight: 500;
            margin-bottom: 0.125rem;
            font-size: 0.85rem;
        }

        .update-timestamp {
            font-size: 0.7rem;
            color: #6c757d;
        }

        .update-warning {
            background-color: rgba(255, 193, 7, 0.1);
        }

        .update-info {
            background-color: rgba(13, 110, 253, 0.1);
        }

        .update-success {
            background-color: rgba(25, 135, 84, 0.1);
        }

        /* Map legend */
        .map-legend {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
            font-size: 0.7rem;
        }

        .legend-item {
            display: flex;
            align-items: center;
        }

        .legend-icon {
            margin-right: 0.25rem;
        }

        /* Responsive grid */
        .dashboard-grid {
            display: grid;
            grid-template-columns: repeat(12, 1fr);
            grid-gap: 0.75rem;
        }

        .grid-col-4 {
            grid-column: span 4;
        }

        .grid-col-8 {
            grid-column: span 8;
        }

        .grid-col-12 {
            grid-column: span 12;
        }

        /* Responsive adjustments */
        @media (max-width: 992px) {
            .grid-col-4, .grid-col-8 {
                grid-column: span 12;
            }
        }

        /* Form elements */
        .form-select {
            display: block;
            width: auto;
            padding: 0.25rem 2.25rem 0.25rem 0.75rem;
            font-size: 0.875rem;
            font-weight: 400;
            line-height: 1.5;
            color: #212529;
            background-color: #fff;
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%23343a40' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M2 5l6 6 6-6'/%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: right 0.75rem center;
            background-size: 16px 12px;
            border: 1px solid #ced4da;
            border-radius: 0.25rem;
            appearance: none;
        }

        /* Utility classes */
        .d-flex {
            display: flex;
        }

        .justify-content-between {
            justify-content: space-between;
        }

        .align-items-center {
            align-items: center;
        }

        .text-center {
            text-align: center;
        }

        .mb-0 {
            margin-bottom: 0;
        }

        .mb-1 {
            margin-bottom: 0.25rem;
        }

        .mb-2 {
            margin-bottom: 0.5rem;
        }

        .mt-1 {
            margin-top: 0.25rem;
        }

        .mt-2 {
            margin-top: 0.5rem;
        }

        .p-0 {
            padding: 0;
        }

        .p-2 {
            padding: 0.5rem;
        }

        .rounded {
            border-radius: 0.25rem;
        }

        .text-muted {
            color: #6c757d;
        }

        .text-warning {
            color: #ffc107;
        }

        .text-purple {
            color: #6f42c1;
        }

        .pointer {
            cursor: pointer;
        }

        /* Compact layout specific styles */
        .compact-stats {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .compact-stats-value {
            font-size: 1.5rem;
            font-weight: 600;
            margin: 0;
        }

        .compact-status {
            display: flex;
            gap: 0.5rem;
        }

        .status-pill {
            padding: 0.25rem 0.5rem;
            border-radius: 1rem;
            font-size: 0.75rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 0.25rem;
        }

        .status-pill-success {
            background-color: rgba(25, 135, 84, 0.1);
            color: #198754;
        }

        .status-pill-danger {
            background-color: rgba(220, 53, 69, 0.1);
            color: #dc3545;
        }

        .compact-chart {
            height: 80px;
        }
    </style>
</head>

<body>
    <title>Device Dashboard</title>
    <?php
    include(BASE_PATH . "assets/html/start-page.php");
    ?>
    <div class="d-flex flex-column flex-shrink-0 p-3 main-content">
        <div class="container-fluid">
            <div class="row d-flex align-items-center">
                <div class="col-12 p-0">
                    <p class="m-0 p-0"><span class="text-body-tertiary">Pages / </span><span>Device Dashboard</span></p>
                </div>
            </div>
            <?php
            include(BASE_PATH . "dropdown-selection/device-list.php");
            ?>
            
            <!-- No Data Container -->
            <div id="noDataContainer" class="text-center py-5 my-5" style="display: none;">
                <img src="<?php echo BASE_PATH; ?>assets/images/no-data.svg" alt="No Data Available" class="img-fluid mb-4" style="max-width: 250px;">
                <h3 class="text-muted">No Data Available</h3>
                <p class="text-muted">There is no data available for the selected time period. Please try a different selection.</p>
            </div>
            
            <!-- Dashboard Content -->
            <div id="dashboardContent" class="container-fluid py-2">
                <div class="dashboard-grid">
                    <!-- Top Row: Stats Cards -->
                    <div class="grid-col-4">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title">Lights</h5>
                            </div>
                            <div class="card-body">
                                <div class="compact-stats">
                                    <div>
                                        <h2 class="compact-stats-value" id="total-lights">1250</h2>
                                        <p class="text-muted mb-0" style="font-size: 0.75rem;">Total Lights Installed</p>
                                    </div>
                                    <div class="compact-status">
                                        <div class="status-pill status-pill-success">
                                            <span id="lights-on-percentage">78%</span>
                                            <span>On</span>
                                        </div>
                                        <div class="status-pill status-pill-danger">
                                            <span id="lights-off-percentage">22%</span>
                                            <span>Off</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="chart-container compact-chart">
                                    <canvas id="lights-chart"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="grid-col-4">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title">CCMS Devices</h5>
                            </div>
                            <div class="card-body">
                                <div class="compact-stats">
                                    <div>
                                        <h2 class="compact-stats-value" id="total-ccms">45</h2>
                                        <p class="text-muted mb-0" style="font-size: 0.75rem;">Total CCMS Devices</p>
                                    </div>
                                    <div class="compact-status">
                                        <div class="status-pill status-pill-success">
                                            <span id="ccms-on">38</span>
                                            <span>Online</span>
                                        </div>
                                        <div class="status-pill status-pill-danger">
                                            <span id="ccms-off">7</span>
                                            <span>Offline</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="chart-container compact-chart">
                                    <canvas id="ccms-chart"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="grid-col-4">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title">Connected Load</h5>
                            </div>
                            <div class="card-body">
                                <div class="compact-stats">
                                    <div>
                                        <h2 class="compact-stats-value" id="cumulative-load">2.5 W</h2>
                                        <p class="text-muted mb-0" style="font-size: 0.75rem;">Cumulative Load</p>
                                    </div>
                                    <div class="compact-status">
                                        <div class="status-pill status-pill-success">
                                            <span id="installed-load">3.2 W</span>
                                            <span>Installed</span>
                                        </div>
                                        <div class="status-pill status-pill-danger">
                                            <span id="active-load">2.5 W</span>
                                            <span>Active</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="chart-container compact-chart">
                                    <canvas id="load-chart"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Middle Row: Map and Updates -->
                    <div class="grid-col-8">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h5 class="card-title">Device Map</h5>
                                <select class="form-select pointer" id="locationsDropdown" name="locationsDropdown"style="width: auto;">
                                    <!-- Options will be populated by JavaScript -->
                                </select>
                            </div>
                            <div class="card-body p-0">
                                <div id="map" class="map-container"></div>
                                <div class="map-legend p-2">
                                    <div class="legend-item">
                                        <i class="bi bi-geo-alt-fill text-danger legend-icon"></i>
                                        <span>Lights OFF</span>
                                    </div>
                                    <div class="legend-item">
                                        <i class="bi bi-geo-alt-fill text-success legend-icon"></i>
                                        <span>Lights ON</span>
                                    </div>
                                    <div class="legend-item">
                                        <i class="bi bi-geo-alt-fill text-warning legend-icon"></i>
                                        <span>Poor Network</span>
                                    </div>
                                    <div class="legend-item">
                                        <i class="bi bi-geo-alt-fill text-purple legend-icon"></i>
                                        <span>Comm. Loss</span>
                                    </div>
                                    <div class="legend-item">
                                        <i class="bi bi-geo-alt-fill text-primary legend-icon"></i>
                                        <span>Power Fail</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="grid-col-4">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title">Updates</h5>
                            </div>
                            <div class="card-body p-0">
                                <div class="updates-container p-2" id="updates-container">
                                    <!-- Updates will be inserted here by JavaScript -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Sample data - in a real application, this would come from an API
            const lightsData = {
                total: 1250,
                onPercentage: 78,
                offPercentage: 22
            };

            const ccmsData = {
                total: 45,
                onDevices: 38,
                offDevices: 7
            };

            const loadData = {
                cumulativeLoad: "2.5 W",
                installedLoad: "3.2 W",
                activeLoad: "2.5 W"
            };

            const updates = [{
                    id: 1,
                    message: "CCMS Device #23 went offline",
                    timestamp: "2 minutes ago",
                    type: "warning",
                    icon: "fa-triangle-exclamation"
                },
                {
                    id: 2,
                    message: "Sector B lights turned on",
                    timestamp: "15 minutes ago",
                    type: "info",
                    icon: "fa-circle-info"
                },
                {
                    id: 3,
                    message: "Maintenance completed on CCMS #12",
                    timestamp: "1 hour ago",
                    type: "success",
                    icon: "fa-circle-check"
                },
                {
                    id: 4,
                    message: "Power fluctuation detected in Sector C",
                    timestamp: "3 hours ago",
                    type: "warning",
                    icon: "fa-triangle-exclamation"
                },
            ];

            // Initialize the dashboard
            initializeLightsCard(lightsData);
            initializeCcmsCard(ccmsData);
            initializeLoadCard(loadData);
            initializeUpdatesPanel(updates);
            
            // Check for data availability
            const hasData = true; // Replace with actual check
            toggleDataVisibility(hasData);
        });
        
        // Function to toggle between data view and no-data view
        function toggleDataVisibility(hasData) {
            const dashboardContent = document.getElementById('dashboardContent');
            const noDataContainer = document.getElementById('noDataContainer');
            
            if (hasData) {
                // Show dashboard, hide no-data message
                if (dashboardContent) dashboardContent.style.display = 'block';
                if (noDataContainer) noDataContainer.style.display = 'none';
            } else {
                // Hide dashboard, show no-data message
                if (dashboardContent) dashboardContent.style.display = 'none';
                if (noDataContainer) noDataContainer.style.display = 'block';
            }
        }

        // Lights Card
        function initializeLightsCard(data) {
            // Update DOM elements
            document.getElementById('total-lights').textContent = data.total;
            document.getElementById('lights-on-percentage').textContent = data.onPercentage + '%';
            document.getElementById('lights-off-percentage').textContent = data.offPercentage + '%';

            // Create pie chart
            const ctx = document.getElementById('lights-chart');
            if (ctx) {
                new Chart(ctx, {
                    type: 'doughnut',
                    data: {
                        labels: ['On', 'Off'],
                        datasets: [{
                            data: [data.onPercentage, data.offPercentage],
                            backgroundColor: ['#198754', '#dc3545'],
                            borderWidth: 1,
                            borderColor: '#fff'
                        }]
                    },
                    options: createChartOptions('Lights Status')
                });
            }
        }

        // CCMS Card
        function initializeCcmsCard(data) {
            // Update DOM elements
            document.getElementById('total-ccms').textContent = data.total;
            document.getElementById('ccms-on').textContent = data.onDevices;
            document.getElementById('ccms-off').textContent = data.offDevices;

            // Calculate percentages
            const onPercentage = Math.round((data.onDevices / data.total) * 100);
            const offPercentage = Math.round((data.offDevices / data.total) * 100);

            // Create pie chart
            const ctx = document.getElementById('ccms-chart');
            if (ctx) {
                new Chart(ctx, {
                    type: 'doughnut',
                    data: {
                        labels: ['Online', 'Offline'],
                        datasets: [{
                            data: [onPercentage, offPercentage],
                            backgroundColor: ['#198754', '#dc3545'],
                            borderWidth: 1,
                            borderColor: '#fff'
                        }]
                    },
                    options: createChartOptions('CCMS Devices Status')
                });
            }
        }

        // Load Card
        function initializeLoadCard(data) {
            // Update DOM elements
            document.getElementById('cumulative-load').textContent = data.cumulativeLoad;
            document.getElementById('installed-load').textContent = data.installedLoad;
            document.getElementById('active-load').textContent = data.activeLoad;

            // Extract numeric values for calculation
            const installedValue = parseFloat(data.installedLoad.split(' ')[0]);
            const activeValue = parseFloat(data.activeLoad.split(' ')[0]);

            // Calculate percentages
            const activePercentage = Math.round((activeValue / installedValue) * 100);
            const inactivePercentage = 100 - activePercentage;

            // Create pie chart
            const ctx = document.getElementById('load-chart');
            if (ctx) {
                new Chart(ctx, {
                    type: 'doughnut',
                    data: {
                        labels: ['Active', 'Inactive'],
                        datasets: [{
                            data: [activePercentage, inactivePercentage],
                            backgroundColor: ['#0d6efd', '#6c757d'],
                            borderWidth: 1,
                            borderColor: '#fff'
                        }]
                    },
                    options: createChartOptions('Load Distribution')
                });
            }
        }

        // Updates Panel
        function initializeUpdatesPanel(updates) {
            const container = document.getElementById('updates-container');
            if (!container) return;

            updates.forEach(update => {
                const updateItem = document.createElement('div');
                updateItem.className = `update-item update-${update.type}`;

                updateItem.innerHTML = `
                    <div class="update-icon">
                        <i class="fas ${update.icon} ${update.type === 'warning' ? 'text-warning' : 
                                                    update.type === 'success' ? 'text-success' : 'text-primary'}"></i>
                    </div>
                    <div class="update-content">
                        <div class="update-message">${update.message}</div>
                        <div class="update-timestamp">${update.timestamp}</div>
                    </div>
                `;

                container.appendChild(updateItem);
            });
        }

        // Helper function to create chart options
        function createChartOptions(title) {
            return {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    title: {
                        display: false,
                        text: title
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return `${context.label}: ${context.raw}%`;
                            }
                        }
                    }
                },
                cutout: '60%'
            };
        }
    </script>
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCvlom5_AlCYoIgXu94yl_VyRRRBc0xSFQ&callback=initMap" async defer></script>
    <script src="<?php echo BASE_PATH; ?>assets/js/project/map.js"></script>
    <script src="<?php echo BASE_PATH; ?>assets/js/sidebar-menu.js"></script>
</body>

</html>
<?php
include(BASE_PATH . "assets/html/body-end.php");
include(BASE_PATH . "assets/html/html-end.php");
?>

