<?php
require_once 'config-path.php';
require_once '../session/session-manager.php';
SessionManager::checkSession();
require_once '../config_db/config.php';

?>
<!DOCTYPE html>
<html lang="en" data-bs-theme="auto">

<head>
    <title>Dashboard</title>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-zoom@1.2.1/dist/chartjs-plugin-zoom.min.js"></script>
    <style>
        .title {
            font-size: 24px;
            margin-bottom: 10px;
        }

        .status {
            font-size: 18px;
            margin-bottom: 15px;
        }

        .chart-container {
            margin-top: 30px;
            position: relative;
            height: 500px;
            /* Default height */
        }

        /* #errorMessage {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            text-align: center;
            z-index: 10;
            display: none;
        } */


        canvas {
            width: 100% !important;
            /* Ensure full width of canvas */
            height: 100% !important;
            /* Ensure the canvas fills the container */
        }
    </style>
    <?php
    include(BASE_PATH . "assets/html/start-page.php");
    ?>
    <div class="d-flex flex-column flex-shrink-0 p-3 main-content ">
        <div class="container-fluid">
            <div class="row d-flex align-items-center">
                <div class="col-12 p-0">
                    <p class="m-0 p-0"><span class="text-body-tertiary">Pages / </span><span>Device Uptime vs Down Time</span></p>
                </div>
            </div>
            <?php
            $phase = "";
            include_once("../common-files/fetch-device-phase.php");
            $phase = $device_phase;
            ?>
            <?php
            include(BASE_PATH . "dropdown-selection/group-device-list.php");
            ?>
            <div class="row">
                <div class="col-md-12 p-0 ">
                    <div class="container-fluid mt-3 p-0">
                        <div class="row justify-content-start align-items-center">
                            <div class="col-auto d-flex align-items-center flex-wrap">
                                <div class="form-group d-flex align-items-center mb-2  ">
                                    <label for="actionDate" class="mr-2 mb-0">Select:</label>
                                    <select class="form-select mx-2" id="typeSelect">
                                        <option value="LATEST">Latest Data</option>
                                        <option value="LAST_WEEK">Last Week</option>
                                        <option value="CURRENT_WEEK">Current Week</option>
                                        <option value="LAST_MONTH">Last Month</option>
                                        <option value="PRESENT_MONTH">Present Month</option>
                                        <option value="CUSTOMRANGE">Custom range</option>
                                    </select>
                                </div>
                                <div class="mb-2 ps-2 custom-size">
                                    <div id="customRangeContainer" style="display: none;">
                                        <div class="input-daterange input-group " id="datepicker1">
                                            <input type="date" id="date-range" class="form-select" name="date-range" placeholder="Select Date Range">
                                            <button id="customRangeButton" class="btn btn-primary ">Submit</button>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xl-12">
                            <div class="card mt-2">
                                <div class="card-body">
                                    <div class="title text-center">Street Light Glowing and Non-Glowing Hours Comparison</div>
                                    <div class="status text-center">Street Light Status <span id="selcted_phase_txt"></span> : <strong>IoT Monitored</strong></div>

                                    <!-- Stacked Bar Chart for All Phases -->
                                    <div class="chart-container" style="position: relative;">
                                        <canvas id="stackedPhaseChart"></canvas>
                                       
                                    </div>
                                    <div id="errorMessage" class="alert alert-danger" style="display: none;">
                                            No data available for the selected date range.
                                        </div>

                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modals links -->
    </main>
    <script src="<?php echo BASE_PATH; ?>assets/js/sidebar-menu.js"></script>
    <script src="<?php echo BASE_PATH; ?>assets/js/date-range-picker.min.js"></script>
    <script src="<?php echo BASE_PATH; ?>assets/js/date-range-picker.js"></script>
    <script src="<?php echo BASE_PATH; ?>assets/js/project/glowing-chart-config.js"></script>

    <?php
    include(BASE_PATH . "assets/html/body-end.php");
    include(BASE_PATH . "assets/html/html-end.php");
    ?>