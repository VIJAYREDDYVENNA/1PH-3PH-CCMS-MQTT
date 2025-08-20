<div class="modal fade" id="TotalModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-primary" id="TotalModalLabel">Total Devices</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" >
                <div class="col-12 p-0">
                    <div class="table-responsive-1 rounded mt-2 border ">
                        <table class="table table-striped table-type-1 w-100 text-center"id="totalDeviceTable">
                            <thead>
                                <tr>
                                    <th class="bg-primary-subtle" scope="col">Select</th>
                                    <th class="bg-primary-subtle" scope="col">Device ID</th>
                                    <th class="bg-primary-subtle" scope="col">Device Name</th>
                                    <th class="bg-primary-subtle" scope="col">Installation Status</th>
                                    <th class="bg-primary-subtle" scope="col">Installed Date</th>
                                    <th class="bg-primary-subtle" scope="col">Location</th>
                                </tr>
                            </thead>
                            <tbody id="total_device_table">
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" onclick="openBatchConfirmModal('install', 'totalDeviceTable')"> Install Selected</button>
                <button type="button" class="btn btn-danger" onclick="openBatchConfirmModal('uninstall', 'totalDeviceTable')">Uninstall Selected</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
