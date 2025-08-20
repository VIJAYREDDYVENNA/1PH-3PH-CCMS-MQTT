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
    <title>Device Dashboard</title>
    <!-- Bootstrap CSS -->
    <!-- Bootstrap JavaScript -->

    <style>
        /* Custom styles to complement Bootstrap */

        .chart-container {
            height: 150px;
            position: relative;
        }

        .updates-container {
            max-height: none;
            overflow-y: auto;
            height: 100%;
        }

        .inactive-updates-container {
            max-height: none;
            overflow-y: auto;
            height: 100%;
        }

        .alert-item {
            /* background: #fff; */
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            padding: 12px;
            margin-bottom: 10px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
            /* Remove list-group-item conflicting styles */
            position: relative;
            display: block;
            color: inherit;
            text-decoration: none;
        }

        .device-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 8px;
        }

        .device-name {
            color: #2563eb;
            font-size: 0.9rem;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .timestamp {
            color: #6b7280;
            font-size: 0.8rem;
            display: flex;
            align-items: center;
            gap: 4px;
        }

        .alert-message {
            color: #374151;
            font-size: 0.9rem;
            margin-bottom: 8px;
            line-height: 1.4;
        }

        .contact-info {
            display: flex;
            align-items: center;
            gap: 16px;
            /* border-top: 1px solid #f3f4f6; */
            padding-top: 8px;
            flex-wrap: wrap;
            /* Allow wrapping for small screens */
        }

        .electrician-info,
        .phone-number {
            display: flex;
            align-items: center;
            gap: 6px;
            color: #059669;
            font-size: 0.85rem;
            text-decoration: none;
            white-space: nowrap;
            /* Prevent breaking in larger screens */
        }

        .phone-number:hover {
            text-decoration: underline;
        }

        .bi {
            font-size: 1rem;
        }

        @media (max-width: 576px) {
            .contact-info {
                flex-direction: column;
                /* Stack name and phone number */
                align-items: flex-start;
                gap: 4px;
            }

            .electrician-info {
                font-size: 0.8rem;
                /* Increase size for readability */
                font-weight: bold;
            }

            .phone-number {
                font-size: 0.9rem;
            }
        }

        .map-container {
            height: 400px;
            background-color: #f1f5f9;
            border-radius: 0.5rem;
            /* margin-bottom: 0.5rem; */
        }

        /* Updated styles for new layout */
        .top-cards-row {
            height: auto;
        }

        .map-and-updates-row {
            margin-top: 1rem;
        }

        /* Right panel styles */
        #updates-panel .row {
            height: 100%;
        }

        .inactive-updates-card {
            height: auto;
            max-height: 300px;
        }

        .inactive-updates-card .card-body {
            padding: 0.75rem;
            display: flex;
            flex-direction: column;
            min-height: 0;
        }

        .inactive-updates-card .inactive-updates-container {
            flex: 1;
            min-height: 0;
        }

        .updates-card {
            height: 100%;
            max-height: 576px;
        }

        .updates-card .card-body {
            padding: 0.75rem;
            display: flex;
            flex-direction: column;
            min-height: 0;
        }

        .updates-card .updates-container {
            flex: 1;
            min-height: 0;
        }

        /* Flex container for right panel */
        .updates-panel-container {
            display: flex;
            flex-direction: column;
            height: 100%;
        }

        .updates-card {
            max-height: none;
            flex-grow: 1;
        }

        .inactive-updates-card {
            height: auto;
        }
    </style>
</head>

<body>
    <title>Device Dashboard</title>
    <?php
    include(BASE_PATH . "assets/html/start-page.php");
    ?>
    <div class="d-flex flex-column flex-shrink-0 p-3 main-content ">
        <div class="container-fluid">
            <div class="row d-flex align-items-center mb-2">
                <div class="col-12 p-0">
                    <p class="m-0 p-0"><span class="text-body-tertiary">Pages / </span><span>Device Dashboard</span></p>
                </div>
            </div>

            <?php include(BASE_PATH . "dropdown-selection/device-list.php"); ?>

            <div class="row mt-3">
                <!-- Left Section (Cards + Map) -->
                <div class="col-lg-8">
                    <div class="row pe-0 pe-lg-2">
                        <!-- Top Row: 3 Cards -->
                        <div class="col-12 rounded mt-2 p-0">
                            <div class="row g-3 top-cards-row">
                                <!-- Lights Card -->
                                <div class="col-sm-12 col-md-6 col-lg-4">
                                    <div class="card h-100">
                                        <div class="card-header d-flex align-items-center">
                                            <h6 class="card-title mb-0 fw-semibold">
                                                <i class="bi bi-brightness-high me-2"></i>Lights
                                            </h6>
                                        </div>

                                        <div class="card-body d-flex flex-column ">
                                            <div class="text-center mb-3">
                                                <h2 id="total-lights">1250</h2>
                                                <p class="text-muted mb-3">Total Lights Installed</p>
                                                <div class="row g-2">
                                                    <div class="col-6">
                                                        <div class="p-2 bg-success bg-opacity-10 rounded">
                                                            <h4 id="lights-on-percentage" class="text-success-emphasis mb-0">78%</h4>
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
                                <div class="col-sm-12 col-md-6 col-lg-4">
                                    <div class="card h-100">
                                        <div class="card-header d-flex align-items-center">
                                            <h6 class="card-title mb-0 fw-semibold">
                                                <i class="bi bi-cpu me-2"></i>CCMS Devices
                                            </h6>
                                        </div>

                                        <div class="card-body d-flex flex-column pointer">
                                            <div class="text-center mb-3">
                                                <h2 id="total-ccms">45</h2>
                                                <p class="text-muted mb-3">Total CCMS Devices</p>
                                                <div class="row g-2">
                                                    <div class="col-6">
                                                        <div class="p-2 bg-success bg-opacity-10 rounded cursor-pointer" onclick="activeModal()">
                                                            <h4 id="ccms-on" class="text-success-emphasis mb-0">38</h4>
                                                            <small>Active</small>
                                                        </div>
                                                    </div>
                                                    <div class="col-6">
                                                        <div class="p-2 bg-danger bg-opacity-10 rounded cursor-pointer" onclick="openNonActiveModal()">
                                                            <h4 id="ccms-off" class="text-danger mb-0">7</h4>
                                                            <small>InActive</small>
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
                                <div class="col-sm-12 col-md-6 col-lg-4">
                                    <div class="card h-100">
                                        <div class="card-header d-flex align-items-center">
                                            <h6 class="card-title mb-0 fw-semibold">
                                                <i class="bi bi-lightning-charge-fill me-2"></i>Connected Load (kW)
                                            </h6>
                                        </div>

                                        <div class="card-body d-flex flex-column ">
                                            <div class="text-center mb-3">
                                                <h2 id="cumulative-load"></h2>
                                                <p class="text-muted mb-3">Installed Load</p>
                                                <div class="row g-2">
                                                    <!-- Active Load -->
                                                    <div class="col-12 col-md-6">
                                                        <div class="p-2 bg-primary bg-opacity-10 rounded d-flex align-items-center justify-content-center h-100 text-center">
                                                            <div>
                                                                <h4 id="installed-load" class="text-primary mb-0"></h4>
                                                                <small>Active Load</small>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <!-- Inactive Load -->
                                                    <div class="col-12 col-md-6" id="inactive-load-container">
                                                        <!-- Content injected by JS -->
                                                        <div class="p-2 bg-secondary bg-opacity-10 rounded d-flex align-items-center justify-content-center h-100 text-center">
                                                            <div>
                                                                <h4 id="active-load" class="text-secondary mb-0"></h4>
                                                                <small>Inactive Load</small>
                                                            </div>
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
                        </div>
                    </div>
                    <!-- Bottom Row: Map -->
                    <div class="row mt-3 ">
                        <div class="col-12 ps-0 pe-2">
                            <div class="card" id="map-card">
                                <div class="card-header">
                                    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center">
                                        <!-- Title -->
                                        <h6 class="card-title mb-2 mb-sm-0">
                                            <i class="bi bi-geo-alt-fill"></i> Device Map
                                        </h6>

                                        <!-- Controls container - same line on larger screens -->
                                        <div class="d-flex align-items-center gap-2 mt-2 mt-sm-0">
                                            <button type="button" class="btn btn-primary btn-sm" onclick="refreshMap()">
                                                Refresh
                                            </button>
                                            <select class="form-select form-select-sm" id="locationsDropdown" style="min-width: 150px;"></select>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body p-0">

                                    <div class="map-container" id="map"></div>
                                    <div class="col-12 mt-2">
                                        <small>* <i class="bi bi-geo-alt-fill text-danger"></i> Lights are Turned OFF</small>
                                        <small>* <i class="bi bi-geo-alt-fill text-success"></i> Lights are turned ON</small>
                                        <small>* <i class="bi bi-geo-alt-fill text-warning"></i> Poor Network Units</small>
                                        <small>* <i class="bi bi-geo-alt-fill text-purple"></i> Communication Loss Units</small>
                                        <small>* <i class="bi bi-geo-alt-fill text-primary"></i> Power Fail Units</small>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
                <!-- Right Section: Updates Panel -->
                <div class="col-lg-4" id="updates-panel">
                    <div class="row ps-0 ps-lg-2 flex-grow-1">
                        <div class="col-12 rounded mt-4 mt-lg-2 p-0 d-flex flex-column h-100">
                            <!-- Inactive Devices Updates Card -->
                            <div class="card inactive-updates-card mb-3" id="inactive-updates-card">
                                <div class="card-header fw-bold">
                                    <i class="bi bi-exclamation-triangle-fill text-warning me-2"></i>InActive Devices
                                </div>
                                <div class="card-body">
                                    <div class="inactive-updates-container overflow-y-auto" id="inactive-updates-container"></div>
                                </div>
                            </div>

                            <!-- Regular Updates Card -->
                            <div class="card updates-card flex-fill" id="updates-card">
                                <div class="card-header fw-bold">
                                    <i class="bi bi-chat-dots-fill"></i> Updates
                                </div>
                                <div class="card-body">
                                    <div class="updates-container overflow-y-auto" id="updates-container"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <!-- Custom JavaScript -->
    <script>
        function alignCardsHeights() {
            const mapCard = document.getElementById('map-card');
            const updatesCard = document.getElementById('updates-card');
            const inactiveCard = document.getElementById('inactive-updates-card');

            // Reset heights
            updatesCard.style.height = 'auto';
            inactiveCard.style.height = 'auto';

            // Get total height for right panel
            const totalRightHeight = mapCard.offsetHeight;

            // Decide how much height to give to inactive + updates
            const inactiveHeight = inactiveCard.offsetHeight;

            // Give remaining height to updates card
            updatesCard.style.height = (totalRightHeight - inactiveHeight - 16) + 'px'; // 16 = margin between cards
        }

        document.addEventListener('DOMContentLoaded', () => {
            setTimeout(alignCardsHeights, 300);
            window.addEventListener('resize', alignCardsHeights);
        });

        window.addEventListener('load', () => {
            setTimeout(alignCardsHeights, 500);
        });
    </script>


    <?php
    include(BASE_PATH . "dashboard/dashboard_modals.php");
    ?>

    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCvlom5_AlCYoIgXu94yl_VyRRRBc0xSFQ&callback=initMap" async defer></script>
    <script src="<?php echo BASE_PATH; ?>assets/js/sidebar-menu.js"></script>
    <script src="<?php echo BASE_PATH; ?>assets/js/project/map.js"></script>
    <script src="<?php echo BASE_PATH; ?>assets/js/project/device-dashboard.js"></script>

    <?php
    include(BASE_PATH . "assets/html/body-end.php");
    include(BASE_PATH . "assets/html/html-end.php");
    ?>
</body>

</html>