<!-- <div class="modal fade" id="removeElectricianlistModal" tabindex="-1" aria-labelledby="removeElectricianlistModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="removeElectricianlistModalLabel">Edit Electrician</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="table-responsive w-100" style="height: 400px; overflow-y: auto;">
                    <table class="table table-bordered w-100" id="devicesTable">
                        <thead>
                            <tr>
                              
                                <th class="table-header1-row-1">Device-ID</th>
                                <th class="table-header1-row-1">Electrician_name</th>

                                <th class="table-header1-row-1">Group / Area</th>
                                
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
                <button class="btn btn-success mt-3" id="removeElectricianlist" onclick="removeTheElectrician()">Remove Electrician</button>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div> -->

<!-- Remove Electrician Confirmation Modal -->
<div class="modal fade" id="removeElectricianModal" tabindex="-1" aria-labelledby="removeElectricianModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-warning">
      <div class="modal-header bg-warning text-dark">
        <h5 class="modal-title" id="removeElectricianModalLabel">Warning!</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <strong>Warning!</strong> Removing these electricians will revoke their access to all devices shown below. Please reassign these devices to other electricians before proceeding with deletion.
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="button" id="confirmRemoveElectrician" class="btn btn-danger">Remove</button>
      </div>
    </div>
  </div>
</div>
