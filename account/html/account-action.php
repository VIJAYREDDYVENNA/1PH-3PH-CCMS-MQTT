<div class="modal fade" id="user_action" tabindex="-1" aria-labelledby="user_action" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5">#<span id="userid_action"></span>-User Account Update</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label" for="editUserRole">Action</label>
                    <select class="form-select" id="select_action">
                        <option value="ACTIVATE">Activate</option>
                        <option value="HOLD">Hold</option>
                        <option value="DELETE">Delete</option>

                    </select>
                </div>
                
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" onclick="saveAction()">Save</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>