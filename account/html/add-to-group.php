<div class="modal fade" id="device_group" tabindex="-1" aria-labelledby="device_group" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5">#<span id="userid_group_devices"></span>-View Devices By Group </h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">

                <div class="mb-3">
                    <label class="form-label" for="assing-group">Assign Group Level Devices</label>
                    <select class="form-select" id="assing-group">

                        <option value="device_group_or_area">GROUP/AREA Devices</option>
                        <option value="city_or_town">CITY/TOWN Devices</option>
                        <option value="district">DISTRICT Devices</option>
                        <option value="state">STATE Devices</option>


                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" onclick="assign_group()">Save</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>