<?php
require_once 'config-path.php';
require_once '../session/session-manager.php';
SessionManager::checkSession();
?>
<!DOCTYPE html>
<html lang="en" data-bs-theme="auto">
<head>
  <title>Alerts</title> 

  <?php 
  include(BASE_PATH."assets/html/start-page.php"); 
  ?>
  <div class="d-flex flex-column flex-shrink-0 p-3 main-content">
    <div class="container-fluid">
        <div class="row d-flex align-items-center">
            <div class="col-12 p-0">
                <p class="m-0 p-0"><span class="text-body-tertiary">Pages / </span><span>Alerts</span></p>
            </div>
        </div>
        <?php include(BASE_PATH."dropdown-selection/group-device-list.php"); ?>
        <div class="row">
            <div class="col-12 p-0">
                <div class="container-fluid mt-3 p-0">
                    <div class="row justify-content-end align-items-center">
                        <div class="col-auto d-flex align-items-center flex-wrap">
                            <div class="form-group d-flex align-items-center mb-2  ps-3 ">
                                <label for="actionDate" class="mr-2 mb-0">Filter:</label>
                                <input type="date" id="date-range" class="form-select mx-2" name="date-range" placeholder="Select Date Range">
                                <select class="form-select mx-2" id="selected_phase_alert">
                                    <option value="POWER-ON/OFF">Power-On/Off</option>
                                    <option value="DOOR">Door Open/Close</option>                                
                                    <option value="BATTERY_STATUS">Battery Status</option>                                
                                </select>
                            </div>
                            <div class="mb-2 ps-2 custom-size">
                                <button type="button" class="btn btn-primary" onclick="get_data()">Submit</button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- alerts -->
                <div  class="table-container">
                    <div class="table-responsive rounded mt-2 border">
                        <table id="alerts_table" class="table table-striped table-bordered table-hover table-type-1 table-sticky-header w-100">

                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</main>
<script src="<?php echo BASE_PATH;?>assets/js/sidebar-menu.js"></script>
<script src="<?php echo BASE_PATH;?>assets/js/date-range-picker.min.js"></script>
<script src="<?php echo BASE_PATH;?>assets/js/date-range-picker.js"></script>
<script src="<?php echo BASE_PATH;?>assets/js/project/alerts.js"></script>
<script>
        // Number of days for date range constraint

</script>
<?php include(BASE_PATH."assets/html/body-end.php"); ?>
<?php include(BASE_PATH."assets/html/html-end.php"); ?>



