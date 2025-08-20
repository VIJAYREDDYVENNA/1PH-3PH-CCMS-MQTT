<nav class="navbar bg-body-tertiary fixed-top shadow">
  <div class="container-fluid">
    <a href="#" class="navbar-brand d-flex align-items-center me-md-auto link-body-emphasis text-decoration-none" style="margin-top:-10px">
      <img id="istl-logo" src="../assets/logos/istl_light.png" class="img-fluid" alt="iSTL Logo" alt="iScientific"> 
      <div class="name-and-tagline ms-2" >
        <div class="name fs-5 fw-bold" style="letter-spacing: -1px;">iScientific</div>
        <div class="text-primary" style="margin-top: -10px; font-size: 14px; letter-spacing: -1px;">TechSolutions Labs Pvt Ltd</div>
      </div>
    </a>


    <div class="d-flex pe-1">

     <!--  <div class=" dropdown me-1">
       <a href="#" class="d-flex align-items-center content-justify-center link-body-emphasis text-decoration-none mt-2 fw-bold" data-bs-toggle="dropdown" data-bs-offset="10,20"  aria-expanded="false">        
         <svg class="bi pe-none me-2" width="20" height="20"><use xlink:href="#bell"/></svg>
       </a>
      
        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown" style="width:300px">
          <li><a class="dropdown-item" href="#">Notification 1</a></li>
          <li><a class="dropdown-item" href="#">Notification 2</a></li>
          <li><hr class="dropdown-divider"></li>
          <li><a class="dropdown-item" href="#">View All Notifications</a></li>
        </ul>
      </div> -->

      <div class="dropdown">

        <a href="#" class="d-flex align-items-center link-body-emphasis text-decoration-none dropdown-toggle " data-bs-toggle="dropdown" aria-expanded="false">
          <img src="../assets/photos/profile/profile.png" alt="" width="32" height="32" class="rounded-circle me-1">
          
        </a>
        <ul class="dropdown-menu text-small shadow dropdown-menu-end pt-0" aria-labelledby="navbarDropdown">
          <li class="bg-dark-subtle py-2"><h6 class=" my-0 ms-2"> Welcome <strong><?php $sessionVars = SessionManager::SessionVariables();
          $user_name = $sessionVars['user_name']; echo  $user_name;?></strong></h6></li>
          <!-- <li><a class="dropdown-item bs-primary" href="#">Settings</a></li> -->
          <li><a class="dropdown-item" href="profile.php">Profile</a></li>
          <?php if (hasPermission('users_list', $menu_list)): ?>
            <li><a class="dropdown-item" href="users-list.php">Users</a></li>
          <?php endif; ?>
          <li><hr class="dropdown-divider"></li>
          <li><a class="dropdown-item" href="logout.php">Sign Out</a></li>
        </ul>

      </div>

    </div>
    <button class="navbar-toggler" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasNavbar" aria-controls="offcanvasNavbar" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
  </div>
</nav>