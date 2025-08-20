<!-- Edit Device Modal -->
<div class="modal fade" id="editDeviceModal" tabindex="-1" aria-labelledby="editDeviceModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editDeviceModalLabel">Edit Device Name</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="editDeviceForm">
                    <div class="mb-3">
                        <label for="deviceId" class="form-label">Device ID</label>
                        <input type="text" class="form-control" id="deviceId" readonly>
                    </div>
                    <div class="mb-3" style="display:none;">
                        <label for="olddeviceName" class="form-label">Old Device Name</label>
                        <input type="text" class="form-control" id="olddeviceName" readonly>
                    </div>
                    <div class="mb-3">
                        <label for="newdeviceName" class="form-label">New Device Name</label>
                        <input type="text" class="form-control" id="newdeviceName" required>
                    </div>

                    <!-- Message Box for Warnings/Success -->
                    <div id="messageBox" class="alert" style="display: none;"></div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" onclick="updateDeviceName()">Save changes</button>
            </div>
        </div>
    </div>
</div>
