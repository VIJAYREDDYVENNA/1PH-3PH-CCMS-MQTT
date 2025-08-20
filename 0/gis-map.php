<?php
require_once 'config-path.php';
require_once '../session/session-manager.php';
SessionManager::checkSession();
?>
<!DOCTYPE html>
<html lang="en" data-bs-theme="auto">

<head>
  <title>Gis Map</title>
  <?php
  include(BASE_PATH . "assets/html/start-page.php");
  ?>
  <div class="d-flex flex-column flex-shrink-0 p-3 main-content ">
    <div class="container-fluid">

      <div class="row d-flex align-items-center">
        <div class="col-12 p-0">
          <p class="m-0 p-0"><span class="text-body-tertiary">Pages / </span><span>Gis Map</span></p>
        </div>
      </div>
      <?php
      include(BASE_PATH . "dropdown-selection/gis-dropdown.php");
      ?>
      <div class="row">
        <div class="col-12">
          <div class="row p-0 justify-content-end align-items-center mt-3 p-0">
            <div class="col-auto p-0 mb-2 ms-2">
              <button type="button" class="btn btn-primary" onclick="refreshMap()">
                Refresh
              </button>
            </div>
          </div>
          <div class="row pe-0 h-100">
            <div class="col-12 rounded mt-2 p-0 ">
              <div id="map" style="height: 650px; width: 100%;"></div>
            </div>
            <div class="col-12 mt-2">

              <small>* <i class="bi bi-geo-alt-fill text-danger h5" aria-hidden="true"></i> Lights are Turned OFF </small>
              <small>* <i class="bi bi-geo-alt-fill text-success h5" aria-hidden="true"></i> Lights are turned ON </small>
              <small>* <i class="bi bi-geo-alt-fill text-warning h5" aria-hidden="true"></i> Poor Network Units </small>
              <small>* <i class="bi bi-geo-alt-fill text-purple h5" aria-hidden="true"></i> Communication Loss Units </small>
              <small>* <i class="bi bi-geo-alt-fill text-primary h5" aria-hidden="true"></i> Power Fail Units </small>

            </div>

          </div>
        </div>
      </div>

    </div>
  </div>
  </main>
  <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCvlom5_AlCYoIgXu94yl_VyRRRBc0xSFQ&callback=initMap" async defer></script>
  <script src="<?php echo BASE_PATH; ?>assets/js/project/map.js"></script>
  <script src="<?php echo BASE_PATH; ?>assets/js/sidebar-menu.js"></script>
  <?php
  include(BASE_PATH . "assets/html/body-end.php");
  include(BASE_PATH . "assets/html/html-end.php");
  ?>