<?php
require_once 'config-path.php';
require_once '../session/session-manager.php';
SessionManager::checkSession();
?>
<!DOCTYPE html>
<html lang="en" data-bs-theme="auto">
<head>
  <title>Status Updates</title>
  <?php 
  include(BASE_PATH."assets/html/start-page.php"); 
  ?>
  <div class="d-flex flex-column flex-shrink-0 p-3 main-content">
    <div class="container-fluid">
        <div class="row d-flex align-items-center">
            <div class="col-12 p-0">
                <p class="m-0 p-0"><span class="text-body-tertiary">Pages / </span><span>Device Status Updates</span></p>
            </div>
        </div>
        <?php include(BASE_PATH."dropdown-selection/group-device-list.php"); ?>
        <div class="row">
            <div class="col-12 p-0 ">
                <div class="container-fluid mt-3 p-0">
                    <div class="row justify-content-end align-items-center">
                        <div class="col-auto d-flex align-items-center flex-wrap">
                            <div class="form-group d-flex align-items-center mb-2 ps-3">
                                <label for="actionDate" class="mr-2 mb-0">Filter:</label>
                                <select class="form-select mx-2" id="activity-selected">
                                    <option value="PING-UPDATES">Ping Updates</option>
                                    <option value="SAVED-SETTINGS">Saved Settings</option>                                    
                                </select>
                            </div>
                            <div class="mb-2 ps-2 custom-size">
                                <button type="button" class="btn btn-primary" onclick="get_data()">Submit</button>
                            </div>
                            <!-- Container for the right-aligned button -->
                            <div class="ms-auto mb-2 ps-2 custom-size" style="display: none;" id="load_setting_btn">
                              <!--   <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#settings_modal" id="view_sttings">Load-Settings</button> -->
                              <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#calibration_model" id="view_sttings" onclick="readIotSetValues(0, 'LATEST');">Load-Settings</button>
                          </div>
                      </div>
                  </div>
              </div>

              <!-- alerts -->
              <div id="alerts_table" class="table-container ">
                <div class="table-responsive rounded mt-2 border">
                    <table id="device_update" class="table table-striped table-bordered table-hover table-type-1 table-sticky-header w-100">

                    </table>
                </div>
            </div>
        </div>

    </div>
</div>
</div>
</div>
</main>
<?php
include( BASE_PATH."device-reports/html/update-calibration-values.php")
?>
<script src="<?php echo BASE_PATH;?>assets/js/sidebar-menu.js"></script>
<script src="<?php echo BASE_PATH;?>assets/js/project/status-update.js"></script>



<?php include(BASE_PATH."assets/html/body-end.php"); ?>
<?php include(BASE_PATH."assets/html/html-end.php"); ?>

