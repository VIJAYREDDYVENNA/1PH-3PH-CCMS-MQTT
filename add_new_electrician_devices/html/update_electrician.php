<div class="modal fade" id="editElectricianModal" tabindex="-1" aria-labelledby="editElectricianLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editElectricianLabel">Edit Electrician</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <label for="electricianDropdown">Select Electrician:</label>
                <select id="electricianDropdown" class="form-select">
                    <!-- Electricians will be loaded dynamically -->
                </select>
                <input type="hidden" id="editElectricianId">
                <p>Device ID: <span id="deviceIdDisplay"></span></p> <!-- Display the device_id here -->
                <button class="btn btn-success mt-3" id="updateElectrician">Update Electrician</button>
            </div>
            <div class="modal-footer">
                <!-- Custom close button at the bottom -->
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
