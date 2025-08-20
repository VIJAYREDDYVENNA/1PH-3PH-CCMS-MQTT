<div class="row d-flex align-items-center">
  <div class="col-12 p-0 ">
    <div class="row d-flex justify-content-end align-items-center">
      <div class="col-xl-3 col-lg-12 d-flex justify-content-end align-items-center">
        <p class="m-0" id="update_time"><span class="text-body-tertiary">Updated On : </span><span id="auto_update_date_time"></span></p>
      </div>
      <div class="col-xl-3 col-md-4 col-6 d-flex align-items-center">
        <?php
        include("phase-selection.php");
        ?>
      </div>
      <div class="col-xl-3 col-md-4 col-6 d-flex align-items-center">
        <?php
        include("group_selection.php");
        ?>
      </div>
      <div class="col-xl-3 col-md-4 col-12 mt-sx-2 mt-md-0 mt-xl-0 mt-2 d-flex align-items-center device_id_section" id="device_id_section">
        <?php
        include("device_selection.php");
        ?>
      </div>
    </div>
  </div>
</div>
