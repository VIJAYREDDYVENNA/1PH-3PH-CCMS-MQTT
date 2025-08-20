<div class="modal fade" id="edit" tabindex="-1" aria-labelledby="edit" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5">#<span id="userid_"></span>-User Details Update</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
               <div class="mb-3">
                <label class="form-label" for="edituserName">Name</label>
                <input type="text" class="form-control" id="edituserName" placeholder="Enter Name...">
            </div>
            <div class="mb-3">
                <label class="form-label" for="edituserid">User_ID</label>
                <input type="text" class="form-control" id="edituserid" placeholder="Enter User_ID...">
            </div>
            <div class="mb-3">
                <label class="form-label" for="editUserRole">User_Role</label>
                <select class="form-select" id="editUserRole">
                    <?php
                    if($role=="SUPERADMIN")
                    {
                        ?>
                        <option value="SUPERADMIN">Super-Admin ISTL User Only</option>
                        <?php
                    }
                    ?>
                    <option value="ADMIN">Admin</option>
                    <option value="DISTRICT">District Level</option>
                    <option value="CITY">City Level</option>
                    <option value="ZONE">Zone Level</option>
                    <option value="AREA">Area/Group Level</option>
                    <option value="TECHNICIAN">Technician</option>
                    
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label" for="edituserEmail">Email</label>
                <input type="text" class="form-control" id="edituserEmail" placeholder="Enter Email...">
            </div>
            <div class="mb-3">
                <label class="form-label" for="edituserMobile">Mobile</label>
                <input type="text" class="form-control" id="edituserMobile" placeholder="Enter Mobile..." maxlength="10">
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-primary" id="submitButton">Save</button>
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        </div>
    </div>
</div>
</div>