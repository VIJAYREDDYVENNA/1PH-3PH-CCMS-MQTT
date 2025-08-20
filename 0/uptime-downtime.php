<?php
require_once 'config-path.php';
require_once '../session/session-manager.php';
SessionManager::checkSession();
?>
<!DOCTYPE html>
<html lang="en" data-bs-theme="auto">

<head>
    <title>Up/Down-Time</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-zoom@1.2.0/dist/chartjs-plugin-zoom.min.js"></script>
    <style>
        /* Add your custom styles here */
        #deviceHoursChart {
            height: 500px;
        }

        #downtimePieChart {
            height: 500px;
        }
    </style>
    <?php
    include(BASE_PATH . "assets/html/start-page.php");
    ?>
    <div class="d-flex flex-column flex-shrink-0 p-3 main-content ">
        <div class="container-fluid">
            <div class="row d-flex align-items-center">
                <div class="col-12 p-0">
                    <p class="m-0 p-0"><span class="text-body-tertiary">Pages / </span><span>Device Up Time vs Down Time</span></p>
                </div>
            </div>
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
                                    <select class="form-select mx-2" id="timeRangeSelect1">
                                        <option value="latest">Latest Data</option>
                                        <option value="currentWeek">Current Week</option>
                                        <option value="lastWeek">Last week</option>
                                        <option value="thisMonth">Current month</option>
                                        <option value="lastMonth">Last month</option>
                                        <option value="customRange">Custom range</option>
                                    </select>
                                </div>
                                <div class="mb-2 ps-2 custom-size">
                                    <div id="customRangeContainer1" style="display: none;">
                                        <div class="input-daterange input-group " id="datepicker1">
                                            <input type="date" id="date-range" class="form-select" name="date-range" placeholder="Select Date Range">
                                            <button id="customRangeButton1" class="btn btn-primary ">Submit</button>
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
                                    <div id="chartContainer" style="position: relative; height: 400px;">
                                        <canvas id="deviceHoursChart" style="height: 400px;"></canvas>
                                        <div id="errorMessage"
                                            class="alert alert-danger"
                                            style="display: none; position: absolute; top: 0; left: 0; width: 100%; height: 100%; 
                            display: flex; justify-content: center; align-items: center;">
                                            <div style="text-align: center; font-size: 18px; font-weight: bold;">
                                                No data available for the selected date range.
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- <div class="col-xl-3">
                    <div class="card mt-2">
                        <div class="card-body">
                            <canvas id="downtimePieChart"></canvas>
                        </div>
                    </div>
                </div> -->
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
    <script src="<?php echo BASE_PATH; ?>assets/js/project/device-reports.js"></script>


    <?php
    include(BASE_PATH . "assets/html/body-end.php");
    include(BASE_PATH . "assets/html/html-end.php");
    ?>