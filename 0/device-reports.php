<?php
require_once 'config-path.php';
require_once '../session/session-manager.php';
SessionManager::checkSession();
?>
<!DOCTYPE html>
<html lang="en" data-bs-theme="auto">
<head>
    <title>Device Reports</title>
    <?php
    include (BASE_PATH . "assets/html/start-page.php");
    ?>
    <div class="d-flex flex-column flex-shrink-0 p-3 main-content ">
        <div class="container-fluid">

            <div class="row d-flex align-items-center">
                <div class="col-12 p-0">
                    <p class="m-0 p-0"><span class="text-body-tertiary">Pages / </span><span>Data Reports</span></p>
                </div>
            </div>
            <?php
            include (BASE_PATH . "dropdown-selection/group-device-list.php");
            ?>
            <div class="row">
              
                <div class="col-sm-12 p-0 pe-sm-2 ">
                    <div class="card mt-3">
                        <div class="card-header bg-primary bg-opacity-25 fw-bold">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <span class="me-2">Reports</span>
                                    <a tabindex="0" role="button" data-bs-toggle="popover" data-bs-trigger="focus" data-bs-title="Info"
                                    data-bs-content="The table shows the health parameters like rtc, gps, gprs,....etc of the selected devices">
                                    <i class="bi bi-info-circle"></i>
                                </a>
                            </div>
                            <div class="input-group w-auto">
                                <input type="text" class="form-control" placeholder="Search..." id="searchInput">
                                <button class="btn btn-primary" type="button" onclick="performSearch()"> <i class="bi bi-search"></i> Search </button>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive rounded border">
                            <table class="table table-striped table-bordered table-hover table-type-1 table-sticky-header w-100 text-center"
                            id="dataTable">
                            <thead>
                                <tr>
                                    <th class="bg-logo-color text-white" scope="col">#</th>
                                    <th class="bg-logo-color text-white" scope="col">Parameter</th>
                                    <th class="bg-logo-color text-white" scope="col">Date & Time</th>
                                    <th class="bg-logo-color text-white" scope="col">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>1</td>
                                    <td>SERIAL_NO</td>
                                    <td>2024-07-07 19:15:49</td>
                                    <td>Off</td>
                                </tr>
                                <tr>
                                    <td>2</td>
                                    <td>SIM_SS</td>
                                    <td>2024-07-07 19:15:49</td>
                                    <td>Off</td>
                                </tr>
                                <tr>
                                    <td>3</td>
                                    <td>Power_status</td>
                                    <td>2024-07-07 19:15:49</td>
                                    <td>On</td>
                                </tr>
                                <tr>
                                    <td>4</td>
                                    <td>MAINFRAME COUNT</td>
                                    <td>2024-07-07 19:15:49</td>
                                    <td>On</td>
                                </tr>
                                <tr>
                                    <td>5</td>
                                    <td>SIMCOM_OFF</td>
                                    <td>2024-07-07 19:15:49</td>
                                    <td>On</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>

</div>
</div>

<script src="<?php echo BASE_PATH; ?>js_modal_scripts/office-use-js/device-reports.js"></script>
<script src="<?php echo BASE_PATH; ?>js_modal_scripts/popover.js"></script>
<script src="<?php echo BASE_PATH; ?>assets/js/sidebar-menu.js"></script>
<?php
include (BASE_PATH . "assets/html/body-end.php");
include (BASE_PATH . "assets/html/html-end.php");
?>