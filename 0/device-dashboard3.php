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
        body {
            background-color: #f8f9fa;
            margin: 0;
            padding: 0;
            min-height: 100vh;
        }

        .main-content {
            padding: 0.5rem !important;
        }

        .chart-container {
            height: 60px;
            position: relative;
        }

        .updates-container {
            height: 200px;
            overflow-y: auto;
        }

        .update-item {
            padding: 6px;
            margin-bottom: 6px;
            border-radius: 4px;
            display: flex;
            align-items: flex-start;
        }

        .update-icon {
            margin-right: 6px;
            font-size: 0.875rem;
        }

        .update-content {
            flex: 1;
        }

        .update-message {
            font-weight: 500;
            margin-bottom: 1px;
            font-size: 0.75rem;
        }

        .update-timestamp {
            font-size: 0.7rem;
            color: #6c757d;
        }

        .update-warning { background-color: rgba(255, 193, 7, 0.1); }
        .update-info { background-color: rgba(13, 110, 253, 0.1); }
        .update-success { background-color: rgba(25, 135, 84, 0.1); }

        .card {
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
            margin-bottom: 0.5rem;
        }

        .card-header {
            padding: 0.5rem;
            border-bottom: 1px solid rgba(0,0,0,0.05);
        }

        .card-body {
            padding: 0.5rem;
        }

        .map-container {
            height: 180px;
            background-color: #f1f5f9;
            border-radius: 0.25rem;
            margin-bottom: 0.25rem;
        }

        h2 {
            font-size: 1.25rem !important;
            margin-bottom: 0.125rem !important;
        }

        h4 {
            font-size: 0.875rem !important;
        }

        h5.card-title {
            font-size: 0.875rem !important;
            margin: 0 !important;
        }

        .text-muted {
            font-size: 0.7rem !important;
        }

        .p-2 {
            padding: 0.25rem !important;
        }

        .mb-3 {
            margin-bottom: 0.5rem !important;
        }

        .py-4 {
            padding-top: 0.5rem !important;
            padding-bottom: 0.5rem !important;
        }

        small {
            font-size: 0.65rem;
        }

        .breadcrumb {
            font-size: 0.75rem;
            margin-bottom: 0.5rem;
        }

        .form-select-sm {
            font-size: 0.75rem;
            padding: 0.25rem;
            height: auto;
        }

        .stat-box {
            background: #fff;
            border-radius: 4px;
            padding: 0.5rem;
            height: 100%;
        }

        .row {
            margin-left: -0.25rem;
            margin-right: -0.25rem;
        }

        .col-md-4, .col-md-8, .col-12 {
            padding-left: 0.25rem;
            padding-right: 0.25rem;
        }

        .mt-2 {
            margin-top: 0.25rem !important;
        }

        .g-2 {
            gap: 0.25rem !important;
        }
    </style>
</head>

<body>
    <?php include(BASE_PATH . "assets/html/start-page.php"); ?>
    
    <div class="d-flex flex-column flex-shrink-0 main-content">
        <div class="container-fluid">
            <p class="breadcrumb m-0 p-0">
                <span class="text-body-tertiary">Pages / </span>
                <span>Device Dashboard</span>
            </p>
            
            <?php include(BASE_PATH . "dropdown-selection/device-list.php"); ?>
            
            <div class="container-fluid py-4">
                <div class="row">
                    <div class="col-md-8">
                        <div class="row">
                            <!-- Lights Card -->
                            <div class="col-md-4">
                                <div class="stat-box">
                                    <h5 class="card-title">Lights</h5>
                                    <div class="text-center">
                                        <h2 id="total-lights">1250</h2>
                                        <p class="text-muted mb-2">Total Lights</p>
                                        <div class="row g-2">
                                            <div class="col-6">
                                                <div class="p-2 bg-success bg-opacity-10 rounded">
                                                    <h4 id="lights-on-percentage" class="text-success mb-0">78%</h4>
                                                    <small>On</small>
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <div class="p-2 bg-danger bg-opacity-10 rounded">
                                                    <h4 id="lights-off-percentage" class="text-danger mb-0">22%</h4>
                                                    <small>Off</small>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="chart-container">
                                            <canvas id="lights-chart"></canvas>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- CCMS Devices Card -->
                            <div class="col-md-4">
                                <div class="stat-box">
                                    <h5 class="card-title">CCMS Devices</h5>
                                    <div class="text-center">
                                        <h2 id="total-ccms">45</h2>
                                        <p class="text-muted mb-2">Total Devices</p>
                                        <div class="row g-2">
                                            <div class="col-6">
                                                <div class="p-2 bg-success bg-opacity-10 rounded">
                                                    <h4 id="ccms-on" class="text-success mb-0">38</h4>
                                                    <small>Online</small>
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <div class="p-2 bg-danger bg-opacity-10 rounded">
                                                    <h4 id="ccms-off" class="text-danger mb-0">7</h4>
                                                    <small>Offline</small>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="chart-container">
                                            <canvas id="ccms-chart"></canvas>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Connected Load Card -->
                            <div class="col-md-4">
                                <div class="stat-box">
                                    <h5 class="card-title">Connected Load</h5>
                                    <div class="text-center">
                                        <h2 id="cumulative-load">2.5 W</h2>
                                        <p class="text-muted mb-2">Cumulative</p>
                                        <div class="row g-2">
                                            <div class="col-6">
                                                <div class="p-2 bg-primary bg-opacity-10 rounded">
                                                    <h4 id="installed-load" class="text-primary mb-0">3.2 W</h4>
                                                    <small>Installed</small>
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <div class="p-2 bg-secondary bg-opacity-10 rounded">
                                                    <h4 id="active-load" class="text-secondary mb-0">2.5 W</h4>
                                                    <small>Active</small>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="chart-container">
                                            <canvas id="load-chart"></canvas>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Map Card -->
                        <div class="row mt-2">
                            <div class="col-12">
                                <div class="stat-box">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <h5 class="card-title">Device Map</h5>
                                        <select class="form-select form-select-sm" id="locationsDropdown" style="width: auto">
                                            <option>All Locations</option>
                                        </select>
                                    </div>
                                    <div id="map" class="map-container"></div>
                                    <div class="d-flex gap-2 flex-wrap mt-1">
                                        <small><i class="bi bi-geo-alt-fill text-danger"></i> OFF</small>
                                        <small><i class="bi bi-geo-alt-fill text-success"></i> ON</small>
                                        <small><i class="bi bi-geo-alt-fill text-warning"></i> Poor Network</small>
                                        <small><i class="bi bi-geo-alt-fill text-purple"></i> No Communication</small>
                                        <small><i class="bi bi-geo-alt-fill text-primary"></i> Power Fail</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Updates Panel -->
                    <div class="col-md-4">
                        <div class="stat-box">
                            <h5 class="card-title mb-1">Updates</h5>
                            <small class="text-muted">Recent notifications</small>
                            <div class="updates-container mt-2" id="updates-container"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
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

            initializeLightsCard(lightsData);
            initializeCcmsCard(ccmsData);
            initializeLoadCard(loadData);
            initializeUpdatesPanel(updates);
        });

        function createChartOptions(title) {
            return {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    title: {
                        display: false
                    }
                },
                cutout: '70%'
            };
        }

        function initializeLightsCard(data) {
            document.getElementById('total-lights').textContent = data.total;
            document.getElementById('lights-on-percentage').textContent = data.onPercentage + '%';
            document.getElementById('lights-off-percentage').textContent = data.offPercentage + '%';

            const ctx = document.getElementById('lights-chart');
            if (ctx) {
                new Chart(ctx, {
                    type: 'doughnut',
                    data: {
                        labels: ['On', 'Off'],
                        datasets: [{
                            data: [data.onPercentage, data.offPercentage],
                            backgroundColor: ['#198754', '#dc3545'],
                            borderWidth: 0
                        }]
                    },
                    options: createChartOptions('Lights Status')
                });
            }
        }

        function initializeCcmsCard(data) {
            document.getElementById('total-ccms').textContent = data.total;
            document.getElementById('ccms-on').textContent = data.onDevices;
            document.getElementById('ccms-off').textContent = data.offDevices;

            const onPercentage = Math.round((data.onDevices / data.total) * 100);
            const offPercentage = Math.round((data.offDevices / data.total) * 100);

            const ctx = document.getElementById('ccms-chart');
            if (ctx) {
                new Chart(ctx, {
                    type: 'doughnut',
                    data: {
                        labels: ['Online', 'Offline'],
                        datasets: [{
                            data: [onPercentage, offPercentage],
                            backgroundColor: ['#198754', '#dc3545'],
                            borderWidth: 0
                        }]
                    },
                    options: createChartOptions('CCMS Devices Status')
                });
            }
        }

        function initializeLoadCard(data) {
            document.getElementById('cumulative-load').textContent = data.cumulativeLoad;
            document.getElementById('installed-load').textContent = data.installedLoad;
            document.getElementById('active-load').textContent = data.activeLoad;

            const installedValue = parseFloat(data.installedLoad.split(' ')[0]);
            const activeValue = parseFloat(data.activeLoad.split(' ')[0]);
            const activePercentage = Math.round((activeValue / installedValue) * 100);
            const inactivePercentage = 100 - activePercentage;

            const ctx = document.getElementById('load-chart');
            if (ctx) {
                new Chart(ctx, {
                    type: 'doughnut',
                    data: {
                        labels: ['Active', 'Inactive'],
                        datasets: [{
                            data: [activePercentage, inactivePercentage],
                            backgroundColor: ['#0d6efd', '#6c757d'],
                            borderWidth: 0
                        }]
                    },
                    options: createChartOptions('Load Distribution')
                });
            }
        }

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
        /* Custom styles to complement Bootstrap */
        body {
            background-color: #f8f9fa;
        }

        .chart-container {
            height: 120px;
            position: relative;
        }

        .updates-container {
            max-height: 400px;
            overflow-y: auto;
        }

        .update-item {
            padding: 10px;
            margin-bottom: 10px;
            border-radius: 6px;
            display: flex;
            align-items: flex-start;
        }

        .update-icon {
            margin-right: 10px;
            font-size: 1.2rem;
        }

        .update-content {
            flex: 1;
        }

        .update-message {
            font-weight: 500;
            margin-bottom: 2px;
        }

        .update-timestamp {
            font-size: 0.8rem;
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

        /* Make sure cards have consistent height */
        .card {
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        }

        /* Responsive adjustments */
        @media (min-width: 992px) {
            .col-lg-4 {
                order: 4;
            }
        }
        .map-container {
            height: 300px;
            background-color: #f1f5f9;
            border-radius: 0.5rem;
            margin-bottom: 0.5rem;
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
            <div class="container-fluid py-4">


                <div class="row ">
                    <!-- Lights Card -->
                    <div class="col-md-8">
                        <div class="row ">
                            <div class="col-md-4">
                                <div class="card h-100">
                                    <div class="card-header">
                                        <h5 class="card-title mb-0">Lights</h5>
                                        <!-- <small class="text-muted">Status of installed lights</small> -->
                                    </div>
                                    <div class="card-body d-flex flex-column">
                                        <div class="text-center mb-3">
                                            <h2 id="total-lights">1250</h2>
                                            <p class="text-muted mb-3">Total Lights Installed</p>

                                            <div class="row g-2">
                                                <div class="col-6">
                                                    <div class="p-2 bg-success bg-opacity-10 rounded">
                                                        <h4 id="lights-on-percentage" class="text-success mb-0">78%</h4>
                                                        <small>On</small>
                                                    </div>
                                                </div>
                                                <div class="col-6">
                                                    <div class="p-2 bg-danger bg-opacity-10 rounded">
                                                        <h4 id="lights-off-percentage" class="text-danger mb-0">22%</h4>
                                                        <small>Off</small>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="chart-container mt-auto">
                                            <canvas id="lights-chart"></canvas>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- CCMS Devices Card -->
                            <div class="col-md-4">
                                <div class="card h-100">
                                    <div class="card-header">
                                        <h5 class="card-title mb-0">CCMS Devices</h5>
                                        <!-- <small class="text-muted">Status of control devices</small> -->
                                    </div>
                                    <div class="card-body d-flex flex-column">
                                        <div class="text-center mb-3">
                                            <h2 id="total-ccms">45</h2>
                                            <p class="text-muted mb-3">Total CCMS Devices</p>

                                            <div class="row g-2">
                                                <div class="col-6">
                                                    <div class="p-2 bg-success bg-opacity-10 rounded">
                                                        <h4 id="ccms-on" class="text-success mb-0">38</h4>
                                                        <small>Online</small>
                                                    </div>
                                                </div>
                                                <div class="col-6">
                                                    <div class="p-2 bg-danger bg-opacity-10 rounded">
                                                        <h4 id="ccms-off" class="text-danger mb-0">7</h4>
                                                        <small>Offline</small>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="chart-container mt-auto">
                                            <canvas id="ccms-chart"></canvas>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Connected Load Card -->
                            <div class="col-md-4">
                                <div class="card h-100">
                                    <div class="card-header">
                                        <h5 class="card-title mb-0">Connected Load</h5>
                                        <!-- <small class="text-muted">Power consumption metrics</small> -->
                                    </div>
                                    <div class="card-body d-flex flex-column">
                                        <div class="text-center mb-3">
                                            <h2 id="cumulative-load">2.5 W</h2>
                                            <p class="text-muted mb-3">Cumulative Load</p>

                                            <div class="row g-2">
                                                <div class="col-6">
                                                    <div class="p-2 bg-primary bg-opacity-10 rounded">
                                                        <h4 id="installed-load" class="text-primary mb-0">3.2 W</h4>
                                                        <small>Installed</small>
                                                    </div>
                                                </div>
                                                <div class="col-6">
                                                    <div class="p-2 bg-secondary bg-opacity-10 rounded">
                                                        <h4 id="active-load" class="text-secondary mb-0">2.5 W</h4>
                                                        <small>Active</small>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="chart-container mt-auto">
                                            <canvas id="load-chart"></canvas>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <div class="card mt-2">
                                    <div class="card-header d-flex justify-content-between align-items-center">
                                        <h5 class="card-title mb-0">Device Map</h5>
                                        <select class="form-select pointer" id="locationsDropdown" name="locationsDropdown" style="width: auto;">

                                        </select>
                                    </div>
                                    <div class="card-body p-0">
                                        <div class="col-12 rounded mt-2 p-0 ">
                                            <div id="map" class="map-container" ></div>
                                        </div>
                                        <div class="col-12 mt-2">

                                            <small>* <i class="bi bi-geo-alt-fill text-danger h5" aria-hidden="true"></i> Lights are Turned OFF </small>
                                            <small>* <i class="bi bi-geo-alt-fill text-success h5" aria-hidden="true"></i> Lights are turned ON </small>
                                            <small>* <i class="bi bi-geo-alt-fill text-warning h5" aria-hidden="true"></i> Poor Network Units </small>
                                            <small>* <i class="bi bi-geo-alt-fill text-purple h5" aria-hidden="true"></i> Communication Loss Units </small>
                                            <small>* <i class="bi bi-geo-alt-fill text-primary h5" aria-hidden="true"></i> Power Fail Units </small>

                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Updates Panel -->
                    <div class="col-md-4">

                        <div class="card h-100">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Updates</h5>
                                <small class="text-muted">Recent system notifications</small>
                            </div>
                            <div class="card-body">
                                <div class="updates-container" id="updates-container">
                                    <!-- Updates will be inserted here by JavaScript -->
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

            <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

            <!-- Custom JavaScript -->
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
                });

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
                            type: 'pie',
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
                            type: 'pie',
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
                            type: 'pie',
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
                                    label: function(context) {
                                        return `${context.label}: ${context.raw}%`;
                                    }
                                }
                            }
                        },
                        cutout: '30%'
                    };
                }
            </script>
</body>

</html>
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCvlom5_AlCYoIgXu94yl_VyRRRBc0xSFQ&callback=initMap" async defer></script>
<script src="<?php echo BASE_PATH; ?>assets/js/project/map.js"></script>
<script src="<?php echo BASE_PATH; ?>assets/js/sidebar-menu.js"></script>
<?php
include(BASE_PATH . "assets/html/body-end.php");
include(BASE_PATH . "assets/html/html-end.php");
?>