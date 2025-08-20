<!DOCTYPE html>
<html lang="en">

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Nandhyala Monitoring System</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">

    <style>
        body {
            margin: 0;
            padding: 0;
            background: none;
            font-family: 'Poppins', sans-serif;
        }

        .main-container {
            background-image: url('../assets/photos/nandyala.png');
            background-size: cover;
            background-position: center top;
            /* Changed to bottom to show the data visualization */
            background-repeat: no-repeat;
            background-attachment: fixed;
            /* Keeps background fixed while scrolling */
            min-height: 100vh;
            width: 100%;
            position: relative;
            overflow: auto;
            /* Ensures content scrolls properly */
        }


        .overlay {
            background: rgba(0, 0, 0, 0.4);
            min-height: 100vh;
            width: 100%;
            position: absolute;
            top: 0;
            left: 0;
        }

        .content {
            position: relative;
            z-index: 1;
            padding-top: 2rem;
        }

        .header-container {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 2rem;
            margin-bottom: 2rem;
            padding: 0 1rem;
        }

        .system-logo {
            width: 100px;
            height: 100px;
            object-fit: contain;
            transition: all 0.3s ease;
        }

        .system-title {
            color: white;
            font-size: 2.2rem;
            margin: 0;
            font-weight: 600;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
            line-height: 1.3;
            letter-spacing: 0.5px;
            transition: all 0.3s ease;
        }

        .login-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
        }

        .circle-logo {
            background: white;
            padding: 1rem;
        }

        @media (max-width: 1200px) {
            .system-title {
                font-size: 1.8rem;
            }

            .system-logo {
                width: 70px;
                height: 70px;
            }
        }

        @media (max-width: 768px) {
            .header-container {
                flex-direction: column;
                text-align: center;
                gap: 1rem;
            }

            .system-title {
                font-size: 1.5rem;
            }

            .system-logo {
                width: 60px;
                height: 60px;
            }
        }

        @media (max-width: 480px) {
            .system-title {
                font-size: 1.2rem;
            }

            .system-logo {
                width: 50px;
                height: 50px;
            }
        }

        @media (max-width: 768px) {
            .main-container {
                background-position: 65% bottom;
            }
        }

        /* For very small screens */
        @media (max-width: 480px) {
            .main-container {
                background-position: 70% bottom;
            }
        }
    </style>
</head>

<body>
    <div class="main-container">
        <div class="overlay"></div>
        <div class="content">
            <div class="container">
                <div class="header-container">
                    <img src="../assets/photos/ap.png" alt="Emblem" class="system-logo">
                    <h1 class="system-title">Nandyala Street Lights Centralized Control and Monitoring System</h1>
                </div>
                <div class="row d-flex justify-content-center mt-3 p-0 m-0 p-3 ">
                    <div class="col-xl-4 col-md-8 col-sm-10 shadow rounded border bg-body-tertiary">
                        <div class="d-flex justify-content-center w-100 mt-3">
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
                            <span class="text-danger"><?php echo $login_error;   ?></span>
                        </div>
                        <div class="d-flex justify-content-center mt-1">
                            <a href="#" id="open_fp_model" class="nav-link text-primary ">Forgot your password?</a>
                        </div>
                        <div class="d-flex justify-content-center mt-2">
                            Don't have an account? <a href="#" class="ms-2 nav-link text-primary" id="liveToastBtn">Sign Up</a>
                        </div>
                        <div class="d-flex justify-content-center my-3">
                            <a href="#" title="iScientific" class="brand-tag d-flex justify-content-center align-item-center">
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
    </div>