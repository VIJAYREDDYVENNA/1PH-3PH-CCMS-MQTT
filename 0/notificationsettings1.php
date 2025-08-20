<?php
require_once 'config-path.php';
require_once '../session/session-manager.php';
require_once BASE_PATH . 'config_db/config.php';
require_once BASE_PATH . 'session/session-manager.php';
SessionManager::checkSession();
$sessionVars = SessionManager::SessionVariables();
$role = $sessionVars['role'];
$user_login_id = $sessionVars['user_login_id'];
?>
<!DOCTYPE html>
<html lang="en" data-bs-theme="auto">

<head>
    <title>Dashboard</title>
    <style>
        .alert-card {
            transition: all 0.3s ease;
            border: 1px solid #dee2e6;
        }

        .alert-card:hover {
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
            transform: translateY(-2px);
        }

        .alert-card.active {
            border-color: #0d6efd;
            background-color: rgba(13, 110, 253, 0.05);
        }

        .border-left {
            border-left: 4px solid #0d6efd !important;
        }

        .form-check-input:checked {
            background-color: #0d6efd;
            border-color: #0d6efd;
        }

        .badge {
            font-weight: 500;
        }
    </style>
    <?php
    include(BASE_PATH . "assets/html/start-page.php");
    ?>
    <div class="d-flex flex-column flex-shrink-0 p-3 main-content ">
        <div class="container-fluid">
            <div class="row d-flex align-items-center">
                <div class="col-12 p-0">
                    <p class="m-0 p-0"><span class="text-body-tertiary">Pages / </span><span>Alert Settings</span></p>
                </div>
            </div>
            <?php
            include(BASE_PATH . "dropdown-selection/group-device-list.php");
            ?>

            <div class="row d-flex justify-content-center">
                <div class="col-lg-8 col-xl-6 p-0 m-0">
                    <!-- Top buttons card -->
                    <div class="col-12 rounded mt-2 p-0">
                        <div class="card">
                            <div class="d-flex justify-content-between align-items-center p-1">
                                <button type="button" class="btn btn-primary btn-sm" id="add_devices_to_dp_selection" data-bs-toggle="modal" data-bs-target="#group_device_multiselection">Multiple Device Selection</button>

                                <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#exampleModal">
                                    Telegram Group Update
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Main settings card -->
                    <div class="col-12 rounded mt-2 p-0">
                        <div class="card">
                            <div class="card-header bg-primary bg-opacity-25 d-flex justify-content-between align-items-center">
                                <h5 class="fw-bold m-0">Alert Configuration</h5>
                                <a tabindex="0" role="button" data-bs-toggle="popover" data-bs-trigger="focus" data-bs-title="Info"
                                    data-bs-content="Configure which alerts you receive and set their priority level for Telegram notifications">
                                    <i class="bi bi-info-circle"></i>
                                </a>
                            </div>
                            <div class="card-body">
                                <div class="alert alert-primary border-left p-3 mb-4">
                                    <div class="d-flex">
                                        <div class="me-3">
                                            <i class="bi bi-info-circle-fill fs-4"></i>
                                        </div>
                                        <div>
                                            <h5 class="alert-heading">Telegram Alert Priorities</h5>
                                            <p class="mb-0">Enable alerts you want to receive, then set their priority level:<br>
                                                <span class="badge bg-danger me-1">Severe</span> - Critical alerts requiring immediate attention<br>
                                                <span class="badge bg-info me-1">Normal</span> - Standard notification alerts
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <form id="notifications-form">
                                    <div class="row row-cols-1 row-cols-md-2 g-3">
                                        <!-- Voltage Card -->
                                        <div class="col">
                                            <div class="card h-100 alert-card" id="voltage-card">
                                                <div class="card-body">
                                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                                        <h5 class="card-title mb-0">
                                                            <i class="bi bi-lightning-charge me-2"></i>Voltage
                                                        </h5>
                                                        <div class="form-check form-switch">
                                                            <input class="form-check-input pointer" type="checkbox" name="notifications" id="voltage" data-permission="voltage" value="voltage">
                                                        </div>
                                                    </div>
                                                    <div class="alert-severity" id="voltage-severity-wrapper">
                                                        <label class="form-label text-muted small">Priority Level</label>
                                                        <div class="d-flex gap-3">
                                                            <div class="form-check">
                                                                <input class="form-check-input" type="radio" name="voltage-severity" id="voltage-normal" value="normal" checked disabled>
                                                                <label class="form-check-label" for="voltage-normal">
                                                                    <span class="badge bg-info">Normal</span>
                                                                </label>
                                                            </div>
                                                            <div class="form-check">
                                                                <input class="form-check-input" type="radio" name="voltage-severity" id="voltage-severe" value="severe" disabled>
                                                                <label class="form-check-label" for="voltage-severe">
                                                                    <span class="badge bg-danger">Severe</span>
                                                                </label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Overload Card -->
                                        <div class="col">
                                            <div class="card h-100 alert-card" id="overload-card">
                                                <div class="card-body">
                                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                                        <h5 class="card-title mb-0">
                                                            <i class="bi bi-exclamation-triangle me-2"></i>Overload/Current
                                                        </h5>
                                                        <div class="form-check form-switch">
                                                            <input class="form-check-input pointer" type="checkbox" name="notifications" id="overload" data-permission="overload" value="overload">
                                                        </div>
                                                    </div>
                                                    <div class="alert-severity" id="overload-severity-wrapper">
                                                        <label class="form-label text-muted small">Priority Level</label>
                                                        <div class="d-flex gap-3">
                                                            <div class="form-check">
                                                                <input class="form-check-input" type="radio" name="overload-severity" id="overload-normal" value="normal" checked disabled>
                                                                <label class="form-check-label" for="overload-normal">
                                                                    <span class="badge bg-info">Normal</span>
                                                                </label>
                                                            </div>
                                                            <div class="form-check">
                                                                <input class="form-check-input" type="radio" name="overload-severity" id="overload-severe" value="severe" disabled>
                                                                <label class="form-check-label" for="overload-severe">
                                                                    <span class="badge bg-danger">Severe</span>
                                                                </label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Power Fail Card -->
                                        <div class="col">
                                            <div class="card h-100 alert-card" id="power_fail-card">
                                                <div class="card-body">
                                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                                        <h5 class="card-title mb-0">
                                                            <i class="bi bi-plug me-2"></i>Input Power Fail
                                                        </h5>
                                                        <div class="form-check form-switch">
                                                            <input class="form-check-input pointer" type="checkbox" name="notifications" id="power_fail" data-permission="power_fail" value="power_fail">
                                                        </div>
                                                    </div>
                                                    <div class="alert-severity" id="power_fail-severity-wrapper">
                                                        <label class="form-label text-muted small">Priority Level</label>
                                                        <div class="d-flex gap-3">
                                                            <div class="form-check">
                                                                <input class="form-check-input" type="radio" name="power_fail-severity" id="power_fail-normal" value="normal" checked disabled>
                                                                <label class="form-check-label" for="power_fail-normal">
                                                                    <span class="badge bg-info">Normal</span>
                                                                </label>
                                                            </div>
                                                            <div class="form-check">
                                                                <input class="form-check-input" type="radio" name="power_fail-severity" id="power_fail-severe" value="severe" disabled>
                                                                <label class="form-check-label" for="power_fail-severe">
                                                                    <span class="badge bg-danger">Severe</span>
                                                                </label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- On/Off Card -->
                                        <div class="col">
                                            <div class="card h-100 alert-card" id="on_off-card">
                                                <div class="card-body">
                                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                                        <h5 class="card-title mb-0">
                                                            <i class="bi bi-power me-2"></i>On/Off
                                                        </h5>
                                                        <div class="form-check form-switch">
                                                            <input class="form-check-input pointer" type="checkbox" name="notifications" id="on_off" data-permission="on_off" value="on_off">
                                                        </div>
                                                    </div>
                                                    <div class="alert-severity" id="on_off-severity-wrapper">
                                                        <label class="form-label text-muted small">Priority Level</label>
                                                        <div class="d-flex gap-3">
                                                            <div class="form-check">
                                                                <input class="form-check-input" type="radio" name="on_off-severity" id="on_off-normal" value="normal" checked disabled>
                                                                <label class="form-check-label" for="on_off-normal">
                                                                    <span class="badge bg-info">Normal</span>
                                                                </label>
                                                            </div>
                                                            <div class="form-check">
                                                                <input class="form-check-input" type="radio" name="on_off-severity" id="on_off-severe" value="severe" disabled>
                                                                <label class="form-check-label" for="on_off-severe">
                                                                    <span class="badge bg-danger">Severe</span>
                                                                </label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- MCB/Contactor Card -->
                                        <div class="col">
                                            <div class="card h-100 alert-card" id="mcb_contactor_trip-card">
                                                <div class="card-body">
                                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                                        <h5 class="card-title mb-0">
                                                            <i class="bi bi-stars me-2"></i>MCB/Contactor
                                                        </h5>
                                                        <div class="form-check form-switch">
                                                            <input class="form-check-input pointer" type="checkbox" name="notifications" id="mcb_contactor_trip" data-permission="mcb_contactor_trip" value="mcb_contactor_trip">
                                                        </div>
                                                    </div>
                                                    <div class="alert-severity" id="mcb_contactor_trip-severity-wrapper">
                                                        <label class="form-label text-muted small">Priority Level</label>
                                                        <div class="d-flex gap-3">
                                                            <div class="form-check">
                                                                <input class="form-check-input" type="radio" name="mcb_contactor_trip-severity" id="mcb_contactor_trip-normal" value="normal" checked disabled>
                                                                <label class="form-check-label" for="mcb_contactor_trip-normal">
                                                                    <span class="badge bg-info">Normal</span>
                                                                </label>
                                                            </div>
                                                            <div class="form-check">
                                                                <input class="form-check-input" type="radio" name="mcb_contactor_trip-severity" id="mcb_contactor_trip-severe" value="severe" disabled>
                                                                <label class="form-check-label" for="mcb_contactor_trip-severe">
                                                                    <span class="badge bg-danger">Severe</span>
                                                                </label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Door Alert Card -->
                                        <div class="col">
                                            <div class="card h-100 alert-card" id="door_alert-card">
                                                <div class="card-body">
                                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                                        <h5 class="card-title mb-0">
                                                            <i class="bi bi-door-open me-2"></i>Panel Door
                                                        </h5>
                                                        <div class="form-check form-switch">
                                                            <input class="form-check-input pointer" type="checkbox" name="notifications" id="door_alert" data-permission="door_alert" value="door_alert">
                                                        </div>
                                                    </div>
                                                    <div class="alert-severity" id="door_alert-severity-wrapper">
                                                        <label class="form-label text-muted small">Priority Level</label>
                                                        <div class="d-flex gap-3">
                                                            <div class="form-check">
                                                                <input class="form-check-input" type="radio" name="door_alert-severity" id="door_alert-normal" value="normal" checked disabled>
                                                                <label class="form-check-label" for="door_alert-normal">
                                                                    <span class="badge bg-info">Normal</span>
                                                                </label>
                                                            </div>
                                                            <div class="form-check">
                                                                <input class="form-check-input" type="radio" name="door_alert-severity" id="door_alert-severe" value="severe" disabled>
                                                                <label class="form-check-label" for="door_alert-severe">
                                                                    <span class="badge bg-danger">Severe</span>
                                                                </label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="d-flex justify-content-center align-items-center mt-4">
                                        <button type="button" class="btn btn-primary px-4 py-2" onclick="updateSelectedAlerts()">
                                            <i class="bi bi-save me-2"></i>Save Configuration
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Get all alert toggles
            const alertToggles = document.querySelectorAll('input[name="notifications"]');

            // For each toggle, add an event listener
            alertToggles.forEach(toggle => {
                toggle.addEventListener('change', function() {
                    const alertType = this.id;
                    const severityRadios = document.querySelectorAll(`input[name="${alertType}-severity"]`);
                    const alertCard = document.getElementById(`${alertType}-card`);

                    // Enable/disable severity options based on toggle state
                    severityRadios.forEach(radio => {
                        radio.disabled = !this.checked;
                    });

                    // Add/remove active class to card
                    if (this.checked) {
                        alertCard.classList.add('active');
                    } else {
                        alertCard.classList.remove('active');
                    }
                });
            });

            // Load user's saved preferences
            loadUserAlertPreferences();
        });

        function loadUserAlertPreferences() {
            // Simulate loading user preferences (this would be an API call in production)
            setTimeout(() => {
                // Mock data - in production, this would come from your API
                const mockPreferences = {
                    alerts: [{
                            type: 'voltage',
                            severity: 'normal'
                        },
                        {
                            type: 'overload',
                            severity: 'severe'
                        },
                        {
                            type: 'power_fail',
                            severity: 'severe'
                        }
                    ]
                };

                // Set toggles and severity based on preferences
                mockPreferences.alerts.forEach(alert => {
                    const toggle = document.getElementById(alert.type);
                    if (toggle) {
                        toggle.checked = true;

                        // Enable severity radios and set correct option
                        const severityRadios = document.querySelectorAll(`input[name="${alert.type}-severity"]`);
                        severityRadios.forEach(radio => {
                            radio.disabled = false;
                            if (radio.value === alert.severity) {
                                radio.checked = true;
                            }
                        });

                        // Add active class to card
                        const alertCard = document.getElementById(`${alert.type}-card`);
                        if (alertCard) {
                            alertCard.classList.add('active');
                        }
                    }
                });
            }, 500);
        }

        function updateSelectedAlerts() {
            // Get all selected alerts and their severity levels
            const alertToggles = document.querySelectorAll('input[name="notifications"]:checked');
            const selectedAlerts = [];

            alertToggles.forEach(toggle => {
                const alertType = toggle.id;
                const severityRadio = document.querySelector(`input[name="${alertType}-severity"]:checked`);

                selectedAlerts.push({
                    type: alertType,
                    severity: severityRadio ? severityRadio.value : 'normal'
                });
            });

            console.log('Saving alert preferences:', selectedAlerts);

            // Show success toast notification
            showToast('Settings saved successfully', 'success');

            // In a real implementation, you would send this data to your server
            // via fetch or another AJAX method
        }

        function showToast(message, type = 'success') {
            // Create toast container if it doesn't exist
            let toastContainer = document.querySelector('.toast-container');
            if (!toastContainer) {
                toastContainer = document.createElement('div');
                toastContainer.className = 'toast-container position-fixed top-0 end-0 p-3';
                toastContainer.style.zIndex = '1050';
                document.body.appendChild(toastContainer);
            }

            // Create toast element
            const toastEl = document.createElement('div');
            toastEl.className = `toast align-items-center text-white bg-${type === 'success' ? 'success' : 'danger'} border-0`;
            toastEl.setAttribute('role', 'alert');
            toastEl.setAttribute('aria-live', 'assertive');
            toastEl.setAttribute('aria-atomic', 'true');

            // Toast content
            toastEl.innerHTML = `
        <div class="d-flex">
            <div class="toast-body">
                <i class="bi bi-${type === 'success' ? 'check-circle' : 'exclamation-circle'} me-2"></i>
                ${message}
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
    `;

            // Add to container
            toastContainer.appendChild(toastEl);

            // Initialize Bootstrap toast
            const toast = new bootstrap.Toast(toastEl, {
                delay: 3000
            });

            // Show the toast
            toast.show();

            // Remove after it's hidden
            toastEl.addEventListener('hidden.bs.toast', function() {
                toastEl.remove();
            });
        }
    </script>
    <?php
    include(BASE_PATH . "settings/html/telegram-integration.php");
    include(BASE_PATH . "dropdown-selection/multiple-group-device_selection.php");
    ?>
    <script src="<?php echo BASE_PATH; ?>assets/js/sidebar-menu.js"></script>
    <script src="<?php echo BASE_PATH; ?>assets/js/project/alert-settings.js"></script>
    <script src="<?php echo BASE_PATH; ?>js_modal_scripts/popover.js"></script>

    <?php
    include(BASE_PATH . "assets/html/body-end.php");
    include(BASE_PATH . "assets/html/html-end.php");
    ?>