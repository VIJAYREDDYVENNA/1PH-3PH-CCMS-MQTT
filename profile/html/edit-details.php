<div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Edit Details</h5>
      </div>
      <div class="modal-body">

        <div class="row">
          <div class="col">
            <label for="FirstName" class="form-label">First Name</label>
            <input type="text" class="form-control" placeholder="Enter Name" id="edituserName" value="<?php echo $user_name;?>" name="Firstname">
          </div>
        </div>

        <div class="row">
          <div class="col">
            <label for="Mobile" class="form-label">Mobile</label>
            <input type="text" class="form-control" placeholder="Enter Mobile Number" id="edituserMobile" value="<?php echo $mobile_no;?>" name="Mobile" maxlength="10">
          </div>
          <div class="col">
            <label for="email" class="form-label">Email</label>
            <input type="email" class="form-control" placeholder="Enter Email" id="edituserEmail" value="<?php echo $user_email;?>" name="Email">
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button type="submit" class="btn btn-primary" id="submitButton">Submit</button>
      </div>
    </div>
  </div>
</div>