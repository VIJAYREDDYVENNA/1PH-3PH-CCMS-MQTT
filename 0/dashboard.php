<?php
require_once 'config-path.php';
require_once '../session/session-manager.php';
SessionManager::checkSession();
$sessionVars = SessionManager::SessionVariables();
$role = $sessionVars['role'];
?>
<!DOCTYPE html>
<html lang="en" data-bs-theme="auto">
<head>
  <title>Dashboard</title>    
  <?php
  include(BASE_PATH."assets/html/start-page.php");  
  ?>
  <div class="d-flex flex-column flex-shrink-0 p-3 main-content">
    <div class="container-fluid">
      <div class="row d-flex align-items-center">
        <div class="col-12 p-0">
          <p class="m-0 p-0"><span class="text-body-tertiary">Pages / </span><span>Dashboard</span></p>
        </div>
      </div>
      <?php
      include(BASE_PATH."dropdown-selection/device-list.php");
      ?>
      <div class="row">
        <div class="col-lg-8">
          <div class="row pe-0 pe-lg-2 h-100">
            <div class="col-12 rounded mt-2 p-0 ">
              <div class="row">
                <div class="col-md-4 col-12 pointer">
                  <div class="card text-center shadow" data-bs-toggle="modal" data-bs-target="#TotalModal" id="total_device">
                    <div class="card-body m-0 p-0">
                      <p class="card-text fw-semibold text-info-emphasis m-0 py-1"><i class="bi bi-bar-chart-fill h4"></i> Total</p>
                      <h3 class="card-title py-2 text-primary-emphasis" id="total_devices">0</h3>
                    </div>
                  </div>
                </div>

                <div class="col-6 col-md-4 mt-3 mt-md-0 pointer ">
                  <div class="card text-center shadow" data-bs-toggle="modal" data-bs-target="#installedModal" id="installed_devices_list">
                    <div class="card-body m-0 p-0">
                      <p class="card-text fw-semibold text-info-emphasis m-0 py-1"><i class="bi bi-check-circle-fill h4"></i> Installed</p>
                      <h3 class="card-title py-2 text-primary-emphasis" id="installed_devices">0</h3>
                    </div>
                  </div>
                </div>
                <div class="col-6 col-md-4 mt-3 mt-md-0 pointer">
                  <div class="card text-center shadow" data-bs-toggle="modal" data-bs-target="#notinstalledModal"  id="not_installed_devices_list">
                    <div class="card-body m-0 p-0">
                      <p class="card-text fw-semibold text-info-emphasis m-0 py-1"><i class="bi bi-x-circle-fill h4"></i> Not-installed</p>
                      <h3 class="card-title py-2 text-primary-emphasis" id="not_installed_devices">0</h3>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <div class="col-12 rounded mt-3 p-0">
              <div class="row">
                <div class="col-xl-3 col-6 pointer">
                  <div class="card text-center shadow" data-bs-toggle="modal" data-bs-target="#activeModal" id="active_device_list">
                    <div class="card-body m-0 p-0">
                      <p class="card-text fw-semibold m-0 py-1 text-success-emphasis "><i class="bi bi-lightbulb-fill h4"></i> Active</p>
                      <!-- <hr class="mt-0"> -->
                      <h3 class="card-title py-2 text-success-emphasis" id="active_devices">0</h3>
                    </div>
                  </div>
                </div>
                <div class="col-xl-3 col-6 pointer">
                  <div class="card text-center shadow" data-bs-toggle="modal" data-bs-target="#activePoorNetworkModal" id="poor_nw_device_list">
                    <div class="card-body m-0 p-0">
                      <p class="card-text fw-semibold m-0 py-1 text-warning-emphasis"><i class="bi bi-exclamation-triangle-fill h4"></i> Poor N/W</p>
                      <!-- <hr class="mt-0"> -->
                      <h3 class="card-title py-2 text-warning-emphasis text-opacity-25" id="poornetwork">0</h3>
                    </div>
                  </div>
                </div>
                <div class="col-xl-3 col-6 pointer mt-3 mt-xl-0" >
                  <div class="card text-center shadow"data-bs-toggle="modal" data-bs-target="#powerfailureModal" id="power_failure_device_list">
                    <div class="card-body m-0 p-0">
                      <p class="card-text fw-semibold m-0 py-1 text-secondary-emphasis"><i class="bi bi-power h4"></i> Input Power Fail</p>
                      <!-- <hr class="mt-0"> -->
                      <h3 class="card-title py-2 text-secondary-emphasis" id="input_power_fail">0</h3>
                    </div>
                  </div>
                </div>
                <div class="col-xl-3 col-6 pointer mt-3 mt-xl-0">
                  <div class="card text-center shadow" data-bs-toggle="modal" data-bs-target="#faultModal" id="faulty_device_list">
                    <div class="card-body m-0 p-0">
                      <p class="card-text fw-semibold m-0 py-1 text-danger-emphasis"><i class="bi bi-bug-fill h4"></i> Faulty</p>
                      <!-- <hr class="mt-0"> -->
                      <h3 class="card-title py-2 text-danger-emphasis" id="faulty">0</h3>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <div class="col-12 rounded mt-3 p-0">
              <div class="row">
                <div class="col-12 col-md-4 pointer">
                  <div class="card text-center shadow" data-bs-toggle="modal" data-bs-target="#AutoOnModal" id="auto_on_devices_list">
                    <div class="card-body m-0 p-0 text-success-emphasis">
                      <p class="card-text fw-semibold m-0 py-1"> <i class="bi bi-clock-fill h4"></i> Auto/System On</p>
                      <!-- <hr class="mt-0"> -->
                      <h3 class="card-title py-2" id="auto_on">0</h3>
                    </div>
                  </div>
                </div>
                <div class="col-6 col-md-4 mt-3 mt-md-0 pointer">
                  <div class="card text-center shadow" data-bs-toggle="modal" data-bs-target="#manualOnModal" id="manual_on_devices_list">
                    <div class="card-body m-0 p-0 text-info-emphasis">
                      <p class="card-text fw-semibold m-0 py-1">  <i class="bi bi-hand-index-fill h4"></i> Manual On</p>
                      <!-- <hr class="mt-0"> -->
                      <h3 class="card-title py-2" id="manual_on">0</h3>
                    </div>
                  </div>
                </div>
                <div class="col-6 col-md-4 mt-3 mt-md-0 pointer">
                  <div class="card text-center shadow" data-bs-toggle="modal" data-bs-target="#offModal" id="off_devices_list">
                    <div class="card-body m-0 p-0 text-danger-emphasis">
                      <p class="card-text fw-semibold m-0 py-1"> <i class="bi bi-toggle-off h4"></i> OFF</p>
                      <!-- <hr class="mt-0"> -->
                      <h3 class="card-title py-2" id="off">0</h3>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <div class="col-12 rounded mt-3 p-0 ">
              <div class="row">
                <div class="col-xl-6 ">
                  <div class="card h-100 shadow-sm text-left p-2">
                    <p class="d-flex align-items-center"> <i class="bi bi-lamp-fill h4"></i> Installed Lights: <span><span class="h3 ms-4 text-primary-emphasis" id="installed_lights"> 2562</span> </span>
                    </p>
                    <div class="progress">
                      <div class="progress-bar bg-success" role="progressbar" style="width: 50%" aria-valuenow="15" aria-valuemin="0" aria-valuemax="100" id="installed_lights_on">60%-ON</div>
                      <div class="progress-bar bg-danger" role="progressbar" style="width: 50%" aria-valuenow="30" aria-valuemin="0" aria-valuemax="100" id="installed_lights_off">40%-OFF</div>

                    </div>

                  </div>
                </div>
                <div class="col-xl-6 mt-lg-0 mt-3">
                  <div class="card h-100 shadow-sm text-left p-2">
                    <p class="d-flex align-items-center"><i class="bi bi-plug h4"></i> Cumulative Load (kW)</p>
                    <div class="progress" role="progressbar" aria-label="Primary example" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100">
                      <div class="progress-bar bg-primary" style="width: 100%" id="installed_load">Intalled load- 0</div>
                    </div>

                    <div class="progress mt-2" role="progressbar" aria-label="Animated striped example" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100">
                      <div class="progress-bar progress-bar-striped bg-info progress-bar-animated overflow-visible text-light-emphasis" style="width: 0%" id="active_load">Active - 0</div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <div class="col-12 rounded mt-3 p-0">
              <div class="row">


                <?php
                if($role!="SUPERADMIN")
                {
                  $alerts_card_height='500px';
                  ?>
                  <div class="col-12">
                    <div class="card text-center shadow">
                      <div class="card-body m-0 p-0">
                        <p class="card-text fw-semibold m-0 py-1"> <i class="bi bi-lightning-fill h4"></i> Total Consumption (Units)</p>

                        <h3 class="card-title py-2" id="total_consumption_units">0</h3>
                      </div>
                    </div>
                  </div>
                  <?php
                }
                else{
                  $alerts_card_height='600px';
                  ?>
                  <div class="col-12 col-md-6">
                    <div class="card text-center shadow">
                      <div class="card-body m-0 p-0">
                        <p class="card-text fw-semibold m-0 py-1"> <i class="bi bi-lightning-fill h4"></i> Total Consumption (Units)</p>

                        <h3 class="card-title py-2" id="total_consumption_units">0</h3>
                      </div>
                    </div>
                  </div>
                  <div class="col-12 col-md-6 mt-3 mt-md-0">
                    <div class="card text-center shadow">
                      <div class="card-body m-0 p-0">
                        <p class="card-text fw-semibold m-0 py-1"><i class="bi bi-graph-up-arrow h4"></i> Energy Saved (Units)</p>

                        <h3 class="card-title py-2" id="energy_saved_units">0</h3>
                      </div>
                    </div>
                  </div>
                  <div class="col-12 col-md-6 mt-3">
                    <div class="card text-center shadow">
                      <div class="card-body m-0 p-0">
                        <p class="card-text fw-semibold m-0 py-1"><i class="bi bi-currency-rupee h4"></i>Amount Saved(INR)</p>

                        <h3 class="card-title py-2" id="amount_saved">0</h3>
                      </div>
                    </div>
                  </div>

                  <div class="col-12 col-md-6 mt-3">
                    <div class="card text-center shadow">
                      <div class="card-body m-0 p-0">
                        <p class="card-text fw-semibold m-0 py-1"><i class="bi bi-tree-fill h4"></i> Co2 Saved (Kg)</p>

                        <h3 class="card-title py-2" id="co2_saved">0</h3>
                      </div>
                    </div>
                  </div>
                  <?php
                }
                ?>
              </div>
            </div>

          </div>
        </div>
        <div class="col-lg-4 ">
          <div class="row ps-0 ps-lg-2 h-100">
            <div class="col-12 rounded mt-4 mt-lg-2 p-0">
              <div class="card bg-light-subtle shadow">
                <div class="card-header fw-bold">
                  <i class="bi bi-chat-dots-fill"></i> Updates
                </div>
                <div class="card-body">
                  <div id="alerts_list" class="list-group overflow-y-auto" style=" height:<?php echo $alerts_card_height; ?>;">

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

<?php
include(BASE_PATH."dashboard/dashboard_modals.php");
?>

</main>
<script src="<?php echo BASE_PATH;?>assets/js/sidebar-menu.js"></script>
<script src="<?php echo BASE_PATH;?>assets/js/project/dashboard.js"></script>


<?php
include(BASE_PATH."assets/html/body-end.php"); 
include(BASE_PATH."assets/html/html-end.php"); 
?>


