<?php
require_once 'config-path.php';
require_once '../session/session-manager.php';
SessionManager::checkSession();
?>
<!DOCTYPE html>
<html lang="en" data-bs-theme="auto">
<head>
    <title>Down Time</title>
    <?php
    include (BASE_PATH . "assets/html/start-page.php");
    ?>
    <div class="d-flex flex-column flex-shrink-0 p-3 main-content ">
        <div class="container-fluid">

            <div class="row d-flex align-items-center">
                <div class="col-12 p-0">
                    <p class="m-0 p-0"><span class="text-body-tertiary">Pages / </span><span>Down Time</span></p>
                </div>
            </div>
            <?php
            include (BASE_PATH . "dropdown-selection/group-device-list.php");
            ?>
            <div class="row">
                <div class="col-sm-2 p-0 pe-sm-2 ">
                </div>
                <div class="col-sm-8 p-0 pe-sm-2 ">
                    <div class="card mt-3">
                        <div class="card-header bg-primary bg-opacity-25 fw-bold">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <span class="me-2">Downtime</span>
                                    <a tabindex="0" role="button" data-bs-toggle="popover" data-bs-trigger="focus"
                                    data-bs-title="Info"
                                    data-bs-content="The table shows the health parameters like rtc, gps, gprs,....etc of the selected devices">
                                    <i class="bi bi-info-circle"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="container">
                            <!-- Button Container -->
                            <div class="d-flex justify-content-end mb-2">
                                <button type="button" class="btn btn-primary btn-sm me-2" onclick="showTodayData()">Today</button>
                                <button type="button" class="btn btn-primary btn-sm me-2" onclick="showAllData()">Total</button>
                                <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#dateRangeModal">Search</button>
                            </div>

                            <!-- Table -->
                            <div class="table-responsive rounded border" style="overflow-y: auto;">
                                <table class="table table-striped table-bordered table-hover table-type-1 table-sticky-header w-100 text-center" id="dataTable">
                                    <thead>
                                        <tr>
                                            <th class="bg-logo-color text-white" scope="col">Device Id</th>
                                            <th class="bg-logo-color text-white" scope="col">Date</th>
                                            <th class="bg-logo-color text-white" scope="col">Down Time(min)</th>
                                            <th class="bg-logo-color text-white" scope="col"></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>CCMS_1</td>
                                            <td>2024-07-07</td>
                                            <td>710</td>
                                            <td>
                                                <button type="button" class="btn btn-primary btn-sm" onclick="downTimeView('CCMS_1')">View</button>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>CCMS_2</td>
                                            <td>2024-07-08</td>
                                            <td>520</td>
                                            <td>
                                                <button type="button" class="btn btn-primary btn-sm" onclick="downTimeView('CCMS_2')">View</button>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>CCMS_3</td>
                                            <td>2024-07-09</td>
                                            <td>330</td>
                                            <td>
                                                <button type="button" class="btn btn-primary btn-sm" onclick="downTimeView('CCMS_3')">View</button>
                                            </td>
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

</div>
</div>
</div>




</main>

<?php
include (BASE_PATH . "office-use/modals/downtime-view-modal.php");
include (BASE_PATH . "office-use/modals/downtime-date-search-modal.php");
?>
<script src="<?php echo BASE_PATH; ?>js_modal_scripts/office-use-js/down-time.js"></script>
<script src="<?php echo BASE_PATH; ?>js_modal_scripts/popover.js"></script>
<script src="<?php echo BASE_PATH; ?>assets/js/sidebar-menu.js"></script>
<?php
include (BASE_PATH . "assets/html/body-end.php");
include (BASE_PATH . "assets/html/html-end.php");
?>
