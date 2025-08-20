<!-- Username Modal -->
<div class="modal fade" id="usernameModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Edit Details</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">

        <div class="mb-3">
          <label for="oldusername" class="form-label">Old Username</label>
          <input type="text" class="form-control" id="oldusername" placeholder="Enter Old Username">
        </div>
        <div class="mb-3">
          <label for="newusername" class="form-label">New Username</label>
          <input type="text" class="form-control" id="newusername" placeholder="Enter New Username">
        </div>

      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary" id="update_user_id">Submit</button>
      </div>
    </div>
  </div>
</div>