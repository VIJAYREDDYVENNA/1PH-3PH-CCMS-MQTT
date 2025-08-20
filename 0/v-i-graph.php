<?php
require_once 'config-path.php';
require_once '../session/session-manager.php';
SessionManager::checkSession();
?>
<!DOCTYPE html>
<html lang="en" data-bs-theme="auto">
<head>
  <title>Graphs</title>  
  <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.4.0/Chart.min.js"></script>
  <script src="https://www.amcharts.com/lib/3/amcharts.js"></script>
  <script src="https://www.amcharts.com/lib/3/serial.js"></script>
  <script src="https://www.amcharts.com/lib/3/themes/light.js"></script>
  <?php
  include(BASE_PATH."assets/html/start-page.php");
  ?>
  <div class="d-flex flex-column flex-shrink-0 p-3 main-content ">
    <div class="container-fluid">
      <div class="row d-flex align-items-center">
        <div class="col-12 p-0">
          <p class="m-0 p-0"><span class="text-body-tertiary">Pages / </span><span>Graphs</span></p>
        </div>
      </div>
      <?php
      include(BASE_PATH."dropdown-selection/group-device-list.php");
      ?>
      <div class="row">
        <div class="col-12 p-0">
          <div class="container-fluid mt-3 p-0">
            <div class="rounded border bg-body">
              <!--   <h6 class="mb-0 p-1 px-2">Phase Voltages</h6> -->

              <div class="row justify-content-center px-3">
                <div class="col-sm-12 d-flex justify-content-end mt-2">
                  <div class="btn-group mx-2" role="group" aria-label="First group">
                    <input type="date" class="form-control" id="graph_date" value="<?php echo date("Y-m-d"); ?>" name="">
                  </div>
                  <div class="btn-group mx-2" role="group" aria-label="Second group">
                    <select class="form-select" id="graph-paramater">
                      <option value="VOLTAGE">Voltage</option>
                      <option value="CURRENT">Current</option>                     
                    </select>
                  </div>
                  <div class="btn-group mx-2" role="group" aria-label="Third group">
                    <select class="form-select" id="graph-selection">
                      <option value="LATEST">Latest</option>
                      <option value="DAY">Selected Day</option>
                      <option value="DAYS">Days of Month</option>
                      <option value="MONTHS">Months of Year</option>
                      <option value="YEARS">All Years</option>
                    </select>
                  </div>
                  <div class="btn-group me-2" role="group" aria-label="fourth group">
                    <button type="button" class="btn btn-primary" onclick="update_graph()">Update</button>
                  </div>
                </div>

                <div class="col-md-12">
                  <span><b><label id="day_month"></label><label id="day_month_value"></label></b></span>
                </div>


                <div class="col-12 mb-2">
                  <div id="chartdiv" style="width: 100%; height: 500px;" class="bg-graph"></div>
                </div>
              </div>
              <small>* The graph displays the real time color waveforms of each phase voltages along with data-time.</small>
            </div>
          </div>

        </div>
      </div>
    </div>

    <!-- Modals links -->
  </main>
  <script src="<?php echo BASE_PATH;?>assets/js/sidebar-menu.js"></script>
  <script src="<?php echo BASE_PATH;?>assets/js/project/graph.js"></script>
  <?php


  include(BASE_PATH."assets/html/body-end.php"); 
  include(BASE_PATH."assets/html/html-end.php"); 
  ?>

