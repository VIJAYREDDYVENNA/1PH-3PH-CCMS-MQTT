<div class="modal fade" id="addDeviceModal" tabindex="-1" aria-labelledby="addDeviceModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="addDeviceModalLabel">Add New Device</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="addDeviceForm">
                    <div class="mb-3">
                        <label for="deviceID" class="form-label">Device ID <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="deviceID" required>
                    </div>
                    <div class="mb-3">
                        <label for="deviceName" class="form-label">Device Name</label>
                        <input type="text" class="form-control" id="deviceName">
                    </div>
                    <!-- <div class="mb-3">
                        <label for="groupArea" class="form-label">Group/Area</label>
                        <input type="text" class="form-control" id="groupArea">
                    </div>
                    <div class="mb-3">
                        <label for="capacity" class="form-label">Capacity (kW)</label>
                        <input type="number" class="form-control" id="capacity" required>
                    </div> -->
                    <?php
                    $enable_diable='class="form-control"';
                    if($role!=="SUPERADMIN")
                    {
                        ?>
                        <div class="mb-3">
                            <label for="activationCode" class="form-label">Activation Code <span class="text-danger">*</span> </label>
                            <input type="text" class="form-control" id="activationCode">
                        </div>
                        <?php

                    }
                    else
                    {
                        ?>
                        <div class="mb-3">
                            <label for="activationCode" class="form-label">Activation Code</label>
                            <input type="text" class="form-control" id="activationCode" readonly disabled>
                        </div>
                        <?php
                    }

                    ?>


                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" onclick="addDevice()">Add New Device</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>