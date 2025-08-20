<?php
require_once 'config-path.php';
require_once '../session/session-manager.php';
SessionManager::checkSession();
?>
<!DOCTYPE html>
<html lang="en" data-bs-theme="auto">

<head>
  <title>On-Off Control</title>
  <?php
  include(BASE_PATH . "assets/html/start-page.php");
  ?>
  <div class="d-flex flex-column flex-shrink-0 p-3 main-content ">
    <div class="container-fluid">

      <div class="row d-flex align-items-center">
        <div class="col-12 p-0">
          <p class="m-0 p-0"><span class="text-body-tertiary">Pages / </span><span>On-Off Control</span></p>
        </div>
      </div>
      <?php
      include(BASE_PATH . "dropdown-selection/group-device-list.php");
      ?>
      <div class="row">
        <div class="col-lg-6 p-0 m-0 pe-lg-2">
          <div class=" mt-2 h-100">
            <div class="card shadow ">
              <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                  <h3 class="card-title text-center flex-grow-1">ON/OFF</h3>
                  <a role="button" data-bs-toggle="modal" data-bs-target="#statusModal" onclick="update_data_table('ONOFF')" style="color: green;">
                    <i class="bi bi-speedometer2" style="font-size: 1.5rem;"></i>
                  </a>
                </div>
                <!--  <p class="text-left">Door status will be updated</p> -->
                <div class="d-flex justify-content-center my-3">
                  <div class="input-group">
                    <input type="number" class="form-control" id="on_off_hours" placeholder="Hrs" aria-label="Hours" min="0" max="23">
                    <span class="input-group-text">:</span>
                    <input type="number" class="form-control" id="on_off_mins" placeholder="Mins" aria-label="Minutes" min="0" max="59">
                  </div>
                </div>
                <div class="d-flex justify-content-around my-4">
                  <div class="col-5 d-flex justify-content-center">
                    <button type="button" class="btn btn-success btn-lg w-100 py-3 fs-2 fw-bold" onclick="lights_on()">ON</button>
                  </div>
                  <div class="col-5 d-flex justify-content-center">
                    <button type="button" class="btn btn-danger btn-lg w-100 py-3 fs-2 fw-bold" onclick="lights_off()">OFF</button>
                  </div>
                </div>
                <h5 class="text-center my-3">ON-OFF Operational Modes</h5>
                <div class="d-flex justify-content-center ">
                  <div class="btn-group overflow-x-auto" role="group ">
                    <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#schedulemodal">Schedule Time</button>
                    <button type="button" class="btn btn-outline-primary" onclick="operational_mode('ASTRO')">Astronomical Time</button>
                    <button type="button" class="btn btn-outline-primary" onclick="operational_mode('AMBIENT')">Ambient/LDR</button>
                    <button type="button" class="btn btn-outline-primary" onclick="operational_mode('AMBIENT_ASTRO')">Ambient & Astronomical</button>
                  </div>
                </div>
                <p class="text-left text-muted mt-2">
                  <small class="text-primary fw-bold" id="mode_status_update"> </small>
                </p>

                <p class="text-left text-muted mt-3">
                  <small class="text-danger"> <b>Note:</b> The device operates in one of the above modes to turn the lights on or off. All modes do not operate simultaneously.</small>
                </p>
                <div class="d-flex justify-content-end mt-3">
                  <button type="button" class="btn btn-primary btn-sm" id="add_devices_to_dp_selection" data-bs-toggle="modal" data-bs-target="#group_device_multiselection">Multiple Device Selection</button>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="col-lg-6 p-0 m-0 ps-lg-2 ">
          <div class=" mt-2 h-100">
            <div class="card shadow ">
              <div class="card-header">
                <i class="bi bi-toggles2"></i> Activity
              </div>
              <div class="card-body pt-0 pe-0">
                <div class="list-group overflow-y-auto" style=" height:380px; ">
                  <div class="w-100 p-0 table-responsive">
                    <table class="table table-hover text-center">
                      <thead class="thead-dark">
                        <tr>
                          <th scope="col">Device ID</th>
                          <th scope="col">Name</th>
                          <th scope="col">Operation Mode</th>
                          <th scope="col">Current Status</th>
                          <th scope="col">Date-Time</th>
                        </tr>
                      </thead>
                      <tbody id="operational_mode_table">
                      </tbody>
                    </table>
                  </div>
                  <div class="d-flex justify-content-end mt-2">
                    <button class="btn btn-secondary" onclick="fetch_more_records()">+ Show More</button>
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
  </div>
  <!-- Modal -->
  <?php

  include(BASE_PATH . "dashboard/html/schedule_time_modal.php");
  include(BASE_PATH . "dropdown-selection/multiple-group-device_selection.php");
  include(BASE_PATH . "settings/html/update-status-modal.php");
  ?>
  <script src="<?php echo BASE_PATH; ?>assets/js/sidebar-menu.js"></script>
  <script src="<?php echo BASE_PATH; ?>assets/js/project/on-off-control.js"></script>



  <?php
  include(BASE_PATH . "assets/html/body-end.php");
  include(BASE_PATH . "assets/html/html-end.php");
  ?>