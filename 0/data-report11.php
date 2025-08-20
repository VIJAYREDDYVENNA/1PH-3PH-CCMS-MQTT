<?php
require_once 'config-path.php';
require_once '../session/session-manager.php';
SessionManager::checkSession();

$sessionVars = SessionManager::SessionVariables();
$devices = isset($_SESSION["DEVICES_LIST"]) ? json_decode($_SESSION["DEVICES_LIST"], true) : [];

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Report </title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --header-bg: #4a6da7;
            --phase-r: #e74c3c;
            --phase-y: #f39c12;
            --phase-b: #3498db;
            --phase-total: #27ae60;
            --energy: #9b59b6;
        }

        /* body {
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
            background-color: #f8f9fa;
            padding: 20px;
        } */

        .breadcrumb-text {
            color: #6c757d;
            font-size: 0.9rem;
            margin-bottom: 20px;
        }

        .table-container {
            border-radius: 12px;
            /* background: white; */
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            max-height: 70vh;
            overflow: auto;
            position: relative;
        }

        .motor-report-table {
            width: 100%;
            font-size: 0.85rem;
            border-collapse: collapse;
            border-spacing: 0;
        }

        .motor-report-table th,
        .motor-report-table td {
            padding: 0.5rem;
            text-align: center;
            vertical-align: middle;
            border: 1px solid #eaeaea;
            white-space: nowrap;
        }

        .motor-report-table thead tr th:first-child {
            border: 1px solid #ffffff !important;
        }

        .motor-report-table th {
            border: 1px solid #ffffff !important;
            /* Add white borders to all table headers */
        }

        .table-container thead th {
            position: sticky;
            z-index: 10;
            box-shadow: 0 1px 0 #eaeaea;
        }

        .table-container thead tr:first-child th {
            top: 0;
            background-color: var(--header-bg);
            z-index: 20;
        }

        .table-container thead tr:nth-child(2) th {
            top: 38px;
            /* background-color: white; */
            z-index: 15;
        }

        .border {
            background-color: var(--header-bg);
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-size: 0.8rem;
            color: white;
        }

        .phase-r {
            background-color: var(--phase-r) !important;
            color: white !important;
        }

        .phase-y {
            background-color: var(--phase-y) !important;
            color: white !important;
        }

        .energy-cell {
            background-color: var(--energy) !important;
            color: white !important;
        }

        .phase-r-bg {
            background-color: rgba(231, 76, 60, 0.1) !important;
        }

        .phase-y-bg {
            background-color: rgba(243, 156, 18, 0.1) !important;
        }

        .energy-bg {
            background-color: rgba(155, 89, 182, 0.1) !important;
        }

        .table-row-even {
            background-color: rgba(248, 249, 250, 0.5);
        }

        .table-row-odd {
            background-color: rgba(255, 255, 255, 0.5);
        }

        .status-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 4px;
            padding: 0.25rem 0.6rem;
            font-size: 0.75rem;
            font-weight: 600;
            border-radius: 50px;
            color: white;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            min-width: 70px;
        }

        .status-on {
            background: linear-gradient(135deg, #28a745, #20c997);
        }

        .status-off {
            background: linear-gradient(135deg, #dc3545, #e74c3c);
        }

        .spinner-border {
            width: 3rem;
            height: 3rem;
        }

        .table-header-row11 {
            background-color: var(--header-bg);
            color: white !important;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-size: 0.8rem;
            border: 1px solid #ffffff !important;
        }

        /* Fix for second row headers */
        .motor-report-table thead tr:nth-child(2) th {
            background-color: #f8f9fa;
            color: black !important;
            border: 1px solid #dddddd !important;
        }
    </style>
</head>

<body>
    <?php
    include(BASE_PATH . "assets/html/start-page.php");
    ?>
    <div class="d-flex flex-column flex-shrink-0 p-4 main-content">
        <div class="container-fluid">

            <div class="row mb-3">
                <div class="col-12">
                    <p class="breadcrumb-text m-0">
                        <i class="bi bi-clipboard-data"></i> Pages / <span class="fw-medium">Data Report</span>
                    </p>
                </div>
            </div>
            <!-- Add this empty container in your HTML just above the table -->
            <!-- <div id="notification-area"></div> -->

            <div class="row mb-4">
                <div class="col-md-6 mb-3 mb-md-0">
                    <div class="card">
                        <div class="card-body">
                            <label class="form-label text-muted mb-2">Select Motor</label>
                            <div class="input-group">
                                <span class="input-group-text ">
                                    <i class="fas fa-cog text-primary"></i>
                                </span>
                                <select class="form-select" id="motor-list">
                                    <?php
                                    if (!empty($devices)) {
                                        foreach ($devices as $device) {
                                            $id = htmlspecialchars($device["D_ID"]);
                                            $name = htmlspecialchars($device["D_NAME"]);
                                            echo "<option value=\"$id\">$name</option>";
                                        }
                                    } else {
                                        echo '<option disabled>No devices available</option>';
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="card">
                        <div class="card-body">
                            <label class="form-label text-muted mb-2">Select Date</label>
                            <div class="input-group">
                                <span class="input-group-text ">
                                    <i class="fas fa-calendar-alt text-primary"></i>
                                </span>
                                <input type="date" class="form-control" id="search_date">
                                <button class="btn btn-primary" type="button" id="search-button" onclick="search_records()">
                                    <i class="fas fa-search me-1"></i> Search
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <div class="table-container">
                        <div class="table-responsive">
                            <table class="table table-bordered motor-report-table mb-0">
                                <thead>
                                    <tr>
                                        <th class="table-header-row11" style="color:white;">Motor ID</th>
                                        <th class="table-header-row11" style="color:white;">Updated at</th>
                                        <th class="table-header-row11" style="color:white;"colspan="3"> Pressure (Kg/cm<sup>2</sup>)</th>
                                        <!-- <th class="table-header-row11" style="color:white;">Outlet Pressure1</th>
                                        <th class="table-header-row11" style="color:white;">Outlet Pressure2</th> -->
                                        <th class="table-header-row11" style="color:white;">On/Off</th>
                                        <th class="table-header-row11" style="color:white;"colspan="3">Line Voltage (V)</th>
                                        <!-- <th class="table-header-row11" style="color:white;">Y_B Voltage </th>
                                        <th class="table-header-row11" style="color:white;">B_R Voltage </th> -->
                                        <th class="table-header-row11 phase-r">Motor Voltage </th>
                                        <th class="table-header-row11 phase-y">Motor Current </th>
                                        <th class="table-header-row11 energy-cell">Energy</th>
                                        <th class="table-header-row11" style="color:white;">Flow Rate</th>
                                        <th class="table-header-row11" style="color:white;">Speed</th>
                                        <th class="table-header-row11" style="color:white;">Reference Frequency</th>
                                        <th class="table-header-row11" style="color:white;">Frequency</th>
                                        <th class="table-header-row11" style="color:white;">Running Hours</th>
                                        <th class="table-header-row11" style="color:white;"colspan="6">Platform Valve status </th>
                                        <!-- <th class="table-header-row11" style="color:white;">PF 3&4 </th>
                                        <th class="table-header-row11" style="color:white;">PF 5&6 </th>
                                        <th class="table-header-row11" style="color:white;">PF 7 </th>
                                        <th class="table-header-row11" style="color:white;">PF 8 </th>
                                        <th class="table-header-row11" style="color:white;">PF 9&10 </th> -->
                                    </tr>
                                    <tr>
                                        <th></th>
                                        <th></th>
                                        <th>Inlet</th>
                                        <th>Outlet 1</th>
                                        <th>Outlet 2</th>
                                        <th>Status</th>
                                        <th>R_Y </th>
                                        <th>Y_B </th>
                                        <th>B_R </th>
                                        <th>(V)</th>
                                        <th>(A)</th>
                                        <th>kWh</th>
                                        <th>Liters/Minute (LPM)</th>
                                        <th>(RPM)</th>
                                        <th>(Hz)</th>
                                        <th>(Hz)</th>
                                        <th>(hrs)</th>
                                        <th>PF 1&2</th>
                                        <th>PF 3&4</th>
                                        <th>PF 5&6</th>
                                        <th>PF 7</th>
                                        <th>PF 8</th>
                                        <th>PF 9&10</th>
                                    </tr>
                                </thead>
                                <tbody id="frame_data_table">
                                    <tr>
                                        <td colspan="21" class="text-center py-4">
                                            <div class="spinner-border text-primary" role="status">
                                                <span class="visually-hidden">Loading...</span>
                                            </div>
                                            <p class="mt-2 text-muted">Loading motor data...</p>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 d-flex justify-content-end">
                <button class="btn btn-secondary btn-sm mt-2" id="btn_add_more" onclick="add_more_records()">+ More Records</button>
            </div>
        </div>


        <script src="<?php echo BASE_PATH; ?>assets/js/sidebar-menu.js"></script>
        <script src="<?php echo BASE_PATH; ?>assets/js/project/motor_data_report.js"></script>

        <?php
        include(BASE_PATH . "assets/html/body-end.php");
        include(BASE_PATH . "assets/html/html-end.php");
        ?>
</body>

</html>