<?php
require_once 'config-path.php';
require_once '../session/session-manager.php';

SessionManager::checkSession();
$sessionVars = SessionManager::SessionVariables();

$mobile_no = $sessionVars['mobile_no'];
$user_id = $sessionVars['user_id'];
$role = $sessionVars['role'];
$user_login_id = $sessionVars['user_login_id'];
$user_name = $sessionVars['user_name'];
$user_email = $sessionVars['user_email'];
$client_login = $sessionVars['client_login'];
?>
<!DOCTYPE html>
<html lang="en" data-bs-theme="auto">
<head>
  <title>Profile</title>  
  <?php
  include(BASE_PATH."assets/html/start-page.php");
  ?>
  <div class="d-flex flex-column flex-shrink-0 p-3 main-content ">
    <div class="container-fluid">
      <div class="row d-flex align-items-center">
        <div class="col-12 p-0">
          <p class="m-0 p-0"><span class="text-body-tertiary">Pages / </span><span>Profile</span></p>
        </div>
      </div>
      <div class="row mt-2 ">
        <div class="col-md-3 ps-0 ">
          <div class="card h-100 shadow">
            <div class="m-2 d-flex justify-content-center ">
              <div>
                <img src="../assets/photos/profile/profile.png" style="height: 15rem; width:15rem;" class="rounded float-star card-img-top" alt="Profile Image">
              </div>
            </div>
            <div class="card-body border-top ">
              <h5 class="card-title" id="name"><?php echo $user_name; ?></h5>
              <h6 class="card-text" id="role">#<?php echo $role; ?> </h6>
              <h6 class="card-text "id="zone">@ <?php echo $client_login; ?></h6>

            </div>
          </div>
        </div>
        <div class=" col-md-9 pe-0 ">
          <div class="card h-100 shadow">
           <nav>
            <div class="nav nav-tabs" id="nav-tab" role="tablist">
              <button class="nav-link active" id="nav-profile-tab" data-bs-toggle="tab" data-bs-target="#nav-profile" type="button" role="tab" aria-controls="nav-home" aria-selected="true">Profile</button>
              <button class="nav-link" id="nav-credentials-tab" data-bs-toggle="tab" data-bs-target="#nav-credentials" type="button" role="tab" aria-controls="nav-profile" aria-selected="false">Credentials </button>

            </div>
          </nav>
          <div class="tab-content" id="nav-tabContent">
            <div class="tab-pane fade show active" id="nav-profile" role="tabpanel" aria-labelledby="nav-profile-tab" tabindex="0">
             <div class=" mt-2">
              <div class="card-header border-top d-flex justify-content-between align-items-center">
                <span>Personal Info</span>
                <button type="button" class="btn btn-primary btn-sm ms-auto" onclick="openEditModal()">
                  Edit
                </button>
              </div>
              <div class="card-body">
                <ul class="list-group">
                  <li class="list-group-item d-flex justify-content-between align-items-center">
                    <div class="ms-2 me-auto">
                      <div class="fw-bold"> Name</div>
                      <span id="empname"><?php echo $user_name;?></span>
                    </div>
                  </li>

                </ul>
              </div>

              <div class="card-body pt-0">
                <ul class="list-group">
                  <li class="list-group-item d-flex justify-content-between align-items-center">
                    <div class="ms-2 me-auto">
                      <div class="fw-bold">Mobile</div>
                      <span id="mobile"><?php echo $mobile_no;?></span>
                    </div>
                  </li>
                </ul>
              </div>
              <div class="card-body pt-0">
                <ul class="list-group">
                  <li class="list-group-item d-flex justify-content-between align-items-center">
                    <div class="ms-2 me-auto">
                      <div class="fw-bold">Email</div>
                      <span id="email"><?php echo $user_email;?></span>

                    </div>
                  </li>
                </ul>
              </div>
            </div>

          </div>
          <div class="tab-pane fade" id="nav-credentials" role="tabpanel" aria-labelledby="nav-credentials-tab" tabindex="0">
            <div class=" mt-2">
              <div class="card-header border-top d-flex justify-content-between align-items-center">
                <span>My Credentials</span>
              </div>
              <div class="card-body">
                <ul class="list-group">
                  <li class="list-group-item d-flex justify-content-between align-items-center">
                    <div class="ms-2 me-auto">
                      <div class="fw-bold">User Name</div>
                      <span id="username"><?php echo $user_id;?></span>
                    </div>
                    <button type="button" class="btn btn-primary btn-sm ms-auto" onclick="openUsernameModal()">
                      Change
                    </button>
                  </li>
                </ul>
              </div>
              <div class="card-body mb-3">
                <ul class="list-group">
                  <li class="list-group-item d-flex justify-content-between align-items-center">
                    <div class="ms-2 me-auto">
                      <div class="fw-bold">Password</div>
                      <!-- <span id="password"><?php echo $mobile_no;?></span> -->
                    </div>
                    <button type="button" class="btn btn-primary btn-sm ms-auto" onclick="openPasswordModal()">
                      Change
                    </button>
                  </li>
                </ul>
              </div>
            </div>
          </div>
        </div>

      </div>
    </div>
  </div>
</div> 
</div>  
</div>
</main>
<?php
include("../profile/html/edit-details.php");
include("../profile/html/password-change.php");
include("../profile/html/username-update.php");
?>


<script src="<?php echo BASE_PATH;?>assets/js/sidebar-menu.js"></script>
<script src="<?php echo BASE_PATH;?>assets/js/project/profile-update.js"></script>

<?php
include(BASE_PATH."assets/html/body-end.php"); 
include(BASE_PATH."assets/html/html-end.php"); 
?>
