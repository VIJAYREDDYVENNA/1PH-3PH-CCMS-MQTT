<?php
require_once 'config-path.php';
require_once '../session/session-manager.php';
SessionManager::checkSession();
?>
<!DOCTYPE html>
<html lang="en" data-bs-theme="auto">

<head>
    <title>Downloads</title>
    <?php
    include (BASE_PATH . "assets/html/start-page.php");
    ?>
    <div class="d-flex flex-column flex-shrink-0 p-3 main-content ">
        <div class="container-fluid">
            <div class="row d-flex align-items-center">
                <div class="col-12 p-0">
                    <p class="m-0 p-0"><span class="text-body-tertiary">Pages / </span><span>Download-Data</span></p>
                </div>
            </div>

            <div class="row">
                <div class="col-sm-3 p-0  ">
                </div>
                <div class="col-sm-6 p-0 ">
                    <form action="../downloads/data-download.php" method="post" >
                        <div class="card mt-3">
                            <div class="card-header bg-primary bg-opacity-25 fw-bold">
                                <span class="me-2">Download Data</span>
                                <a tabindex="0" role="button" data-bs-toggle="popover" data-bs-trigger="focus"
                                data-bs-title="Info" data-bs-content="To Download the device data."> <i
                                class="bi bi-info-circle"></i>
                            </a>
                        </div>
                        <div class="card-body row">
                            <form class="col-md-12" id="ccms-data">
                                <div class="pb-2">
                                    <label for="select-group" class="form-label">Group:</label>
                                    <?php
                                    include(BASE_PATH . "dropdown-selection/group_selection.php");
                                    ?>
                                    <label for="select-device" class="form-label mt-2">Device:</label>
                                    <?php
                                    include(BASE_PATH . "dropdown-selection/device_selection.php");
                                    ?>

                                    <label for="select-group" class="form-label mt-2">Select Dates:</label>
                                    <input type="date" id="date-range" class="form-select" name="date-range" placeholder="Select Date Range">

                                </div>
                                <div class="d-flex justify-content-between align-items-center mt-2">
                                    <div class="w-100 text-center">
                                        <button type="submit" class="btn btn-primary ">Download</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="card-footer d-flex justify-content-between align-items-center">
                            <div class="w-100 text-center">
                               
                                <div class="mt-1 text-start">
                                    <p class="text-danger">*30 days data can be downloaded only once.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </main>
    <script src="<?php echo BASE_PATH; ?>js_modal_scripts/popover.js"></script>
    <script src="<?php echo BASE_PATH; ?>assets/js/sidebar-menu.js"></script>
    <script src="<?php echo BASE_PATH;?>assets/js/date-range-picker.min.js"></script>
    <script src="<?php echo BASE_PATH;?>assets/js/date-range-picker.js"></script> 

    <script type="text/javascript">
        initializeDateRangePicker("#date-range", 29);
        function getSelectedDateRange() {
            const selectedDates = window.fp.selectedDates;
            if (selectedDates.length === 2) {
                const [startDate, endDate] = selectedDates;
                return {
                    startDate: startDate,
                    endDate: endDate
                };
            } else {
                return null; 
            }
        }
    </script>
    <?php
    include (BASE_PATH . "assets/html/body-end.php");
    include (BASE_PATH . "assets/html/html-end.php");
?>