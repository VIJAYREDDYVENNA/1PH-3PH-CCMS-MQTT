<div class="modal fade" id="group_device_multiselection" tabindex="-1" aria-labelledby="group_device_multiselection" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h1 class="modal-title fs-5">Devices List</h1>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="row justify-content-center " >
          <?php include("multiple-devices.php"); ?>
        </div>
      </div>
      <div class="modal-footer">  
        <?php
        include("multiple-devices-buttons-next.php");
        include("multiple-devices-buttons-cancel.php");
        ?>
      </div>
    </div>
  </div>
</div>

