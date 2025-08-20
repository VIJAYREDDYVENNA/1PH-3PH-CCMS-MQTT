<div id="popup" class="popup-session-out  m-0 p-0">
  <div class="container m-0 p-0">
    <div class="row justify-content-center p-2 m-0">
      <div class="col-md-6 col-sm-10  col-lg-4 p-2 bg-body-tertiary border-1 rounded shadow-lg ">
        <h5 class="mt-0 text-danger text-center">Session Timeout Warning</h5>
        <hr>
        <div class="modal-body">
          <p> You will be logged out in <span id="timeout_count" class="fw-bold">2:00</span> minute(s) due to inactivity. </p>
          <p class="text-primary"> Do you want to extend your session ?</p>
        </div>
        <hr class="">
        <div class="d-flex justify-content-end p-0 mt-2">
          <button type="button" class="btn btn-danger me-2" onclick="logout()">Logout</button>
          <button type="button" class="btn btn-primary ms-2" onclick="extendSession()">Keep me Logged in</button>
        </div>
      </div>
    </div>
  </div>
</div>
