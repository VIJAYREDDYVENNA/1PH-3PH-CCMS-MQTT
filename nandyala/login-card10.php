<div class="all">
        <!-- Original Header section - visible only on larger screens -->
        <header class="header-container d-none d-md-block">
            <div class="container">
                <div class="row header-emblem-row">
                    <div class="col-md-2">
                        <img src="../assets/photos/client/nandyala/ap.png" alt="AP Government Logo" class="ap-logo">
                    </div>

                </div>
            </div>
        </header>

        <div class="container py-4">
            <!-- Small screen layout - visible only on small screens -->
            <div class="row d-md-none mb-3">
                <div class="col-4">
                    <div class="official-card">
                        <img src="../assets/photos/client/nandyala/ap_cm.jpg" alt="Chief Minister">
                        <h6>Sri. N. Chandra Babu Naidu</h6>
                        <p>Honourable Chief Minister<br>AP</p>
                    </div>
                </div>

                <div class="col-4 small-screen-logo">
                    <img src="../assets/photos/client/nandyala/ap.png" alt="AP Government Logo" class="ap-logo">
                    <div class="small-screen-title">
                        <h1 class="text-light"><span class="green-text">Nandyala</span> Municipality Street lights</h1>
                        <h2 class="text-light">CCMS</h2>
                    </div>
                </div>

                <div class="col-4">
                    <div class="official-card">
                        <img src="../assets/photos/client/nandyala/ap_municipal_minister1.jpg" alt="Minister">
                        <h6>Sri. Dr. Narayana</h6>
                        <p>Honourable Minister for MA & UD<br>AP</p>
                    </div>
                </div>
            </div>

            <!-- Original layout for larger screens -->
            <div class="row justify-content-center">
                <div class="col-lg-8 d-none d-md-block">
                    <div class="row mb-4">
                        <!-- CM Card -->
                        <div class="col-md-3">
                            <div class="official-card">
                                <img src="../assets/photos/client/nandyala/ap_cm.jpg" alt="Chief Minister">
                                <h6>Sri. N. Chandra Babu Naidu</h6>
                                <p>Honourable Chief Minister<br>Andhra Pradesh</p>
                            </div>
                        </div>

                        <!-- Title Section - Hidden on small screens -->
                        <div class="col-md-6">
                            <div class="title-section">
                                <h1 class="text-light"><span class="green-text">Nandyala</span> Municipality Street lights</h1>
                                <h2 class="text-light">CENTRALIZED CONTROL & MONITORING SYSTEM</h2>
                            </div>
                        </div>

                        <!-- Minister Card -->
                        <div class="col-md-3">
                            <div class="official-card">
                                <img src="../assets/photos/client/nandyala/ap_municipal_minister1.jpg" alt="Minister">
                                <h6>Sri. Dr. Narayana</h6>
                                <p>Honourable Minister for MA & UD<br>Andhra Pradesh</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Login Container - Centered on medium and large screens -->
                <div class="col-lg-4 d-flex justify-content-center">
                    <div class="login-container bg-body-tertiary">
                        <div class="d-flex justify-content-center w-100 ">
                            <div class="d-flex justify-content-center rounded-circle p-3 border bg-body circle-logo">
                                <img id="istl-logo-login-1" src="<?php echo BASE_PATH; ?>assets/logos/istl_light.png" alt="Logo" />
                            </div>
                        </div>
                        <form class="mt-3 d-flex justify-content-center" method="post">
                            <div class="w-75">
                                <div class="form-floating mb-3">
                                    <input type="text" class="form-control" name="userid" id="userid" placeholder="User-ID/Mobile-No/Email ID" required>
                                    <label for="userid">User-ID / Mobile-No</label>
                                </div>
                                <div class="form-floating mb-3 position-relative">
                                    <input type="password" class="form-control" name="password" id="password" placeholder="Password" required>
                                    <label for="password">Password</label>
                                    <button class="position-absolute top-50 end-0 translate-middle-y border-0 z-1 me-1" type="button" id="togglePassword"><i class="bi bi-eye"></i></button>
                                </div>
                                <div class="d-flex justify-content-center">
                                    <button class="btn btn-primary w-50" type="submit">Login</button>
                                </div>
                            </div>
                        </form>
                        <div class="d-flex justify-content-center col-12 mt-2 ">
                            <span class="text-danger font-small"><?php echo $login_error; ?></span>
                        </div>
                        <div class="d-flex justify-content-center mt-1">
                            <a href="#" id="open_fp_model" class="nav-link text-primary ">Forgot your password?</a>
                        </div>
                        <div class="d-flex justify-content-center mt-2">
                            Don't have an account? <a href="#" class="ms-2 nav-link text-primary" id="liveToastBtn">Sign Up</a>
                        </div>
                        <div class="d-flex justify-content-center my-3">
                            <a href="#" title="iScientific" class="brand-tag d-flex justify-content-center align-items-center">
                                <div class="brand-img">
                                    <img id="istl-logo-login" class="logo-img" src="<?php echo BASE_PATH; ?>assets/logos/istl_light.png" alt="iScientific">
                                </div>
                                <div>
                                    <span class="brand-title text-body-emphasis ml-2">© iScientific TechSolutions Labs Pvt Ltd</span>
                                    <span class="brand-tag ms-2 ms-sm-3"> Energizing Quality and Accountability</span>
                                </div>
                            </a>
                        </div>
                        <div class="d-flex justify-content-center links font-small">
                            <p>By Proceeding you agree to the <a href="http://istlabs.in/terms-and-conditions.html" target="_blank" class="text-decoration-none">Terms of Use</a> and <a href="http://istlabs.in/policy.html" target="_blank" class="text-decoration-none">Privacy Policy</a>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>



  