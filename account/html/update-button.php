<div class="modal fade" id="updatebutton" tabindex="-1" aria-labelledby="updatebutton" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="updatebutton">Update Details</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="updateform">
                    <div class="mb-3">
                        <label class="form-label">Device Name</label>
                        <input type="text" class="form-control" id="devicename" placeholder="Enter Name...">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Device ID</label>
                        <input type="text" class="form-control" id="deviceid" placeholder="Enter Device ID...">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Group/Area</label>
                        <input type="text" class="form-control" id="devicegroup" placeholder="Enter Group/Area...">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success" onclick="saveUpdate()">Save</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>