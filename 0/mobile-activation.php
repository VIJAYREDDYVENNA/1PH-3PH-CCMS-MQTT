<?php
require_once 'config-path.php';
require_once '../session/session-manager.php';
SessionManager::checkSession();
?>
<!DOCTYPE html>
<html lang="en" data-bs-theme="auto">

<head>
    <title>Mobile Activation</title>
    <?php
    include(BASE_PATH . "assets/html/start-page.php");
    ?>
    
    <!-- Bootstrap Icons for notifications -->
    <svg xmlns="http://www.w3.org/2000/svg" style="display: none;" id="bootstrap-icons">
        <symbol id="check-circle-fill" fill="currentColor" viewBox="0 0 16 16">
            <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.061L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z"/>
        </symbol>
        <symbol id="exclamation-triangle-fill" fill="currentColor" viewBox="0 0 16 16">
            <path d="M8.982 1.566a1.13 1.13 0 0 0-1.96 0L.165 13.233c-.457.778.091 1.767.98 1.767h13.713c.889 0 1.438-.99.98-1.767L8.982 1.566zM8 5c.535 0 .954.462.9.995l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 5.995A.905.905 0 0 1 8 5zm.002 6a1 1 0 1 1 0 2 1 1 0 0 1 0-2z"/>
        </symbol>
    </svg>
    
    <div class="d-flex flex-column flex-shrink-0 p-3 main-content">
        <div class="container-fluid">
            <div class="row d-flex align-items-center">
                <div class="col-12 p-0">
                    <p class="m-0 p-0"><span class="text-body-tertiary">Pages / </span><span>Mobile Activation</span></p>
                </div>
            </div>

            <div class="row">
                <div class="container mt-5 w-50">
                    <div class="card shadow-sm p-4">
                        <h4 class="mb-3 text-center">Mobile App Activation</h4>
                        <form id="ActivationForm">
                            <div class="d-flex justify-content-center align-items-center">
                                <div class="mb-3 w-75">
                                    <!-- Activation Code Field -->
                                    <label for="activationCode" class="form-label">Activation Code</label>
                                    <input type="text" class="form-control" id="activationCode" name="activationCode" 
                                           placeholder="Enter Activation Code Here..." required>
                                    
                                    <!-- Name Field -->
                                    <label for="userName" class="form-label mt-3">App User Name</label>
                                    <input type="text" class="form-control" id="userName" name="userName" 
                                           placeholder="Enter App User name" required>

                                    <!-- Mobile Number Field -->
                                    <label for="mobileNumber" class="form-label mt-3">App User Mobile Number</label>
                                    <input type="tel" class="form-control" id="mobileNumber" name="mobileNumber" 
                                           placeholder="Enter App User Mobile Number" required pattern="[0-9]{10}" maxlength="10">
                                </div>
                            </div>
                            <div class="text-center">
                                <button type="button" class="btn btn-primary" onclick="ActivateDevice()" id="publishBtn">
                                    <span id="btnText">Activate</span>
                                    <span id="btnSpinner" style="display: none;" class="spinner-border spinner-border-sm ms-2"></span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
<div id="toastContainer" class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 1100;"></div>

    </main>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/mqtt/5.5.1/mqtt.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/crypto-js/4.1.1/crypto-js.min.js"></script>
    <script src="<?php echo BASE_PATH; ?>assets/js/sidebar-menu.js"></script>
    <script src="<?php echo BASE_PATH; ?>assets/js/project/mobile-activation.js"></script>
    
    <?php
    include(BASE_PATH . "assets/html/body-end.php");
    include(BASE_PATH . "assets/html/html-end.php");
    ?>