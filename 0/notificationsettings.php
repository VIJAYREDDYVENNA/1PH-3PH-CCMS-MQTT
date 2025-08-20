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
                    <div class="col-12 rounded mt-2 p-0">
                        <div class="card">
                            <div class="card-header bg-primary bg-opacity-25 fw-bold">
                                Alerts Configuration
                                <a tabindex="0" role="button" data-bs-toggle="popover" data-bs-trigger="focus" data-bs-title="Info"
                                    data-bs-content="Enable the alerts you want to receive and set their severity level for Telegram notifications">
                                    <i class="bi bi-info-circle"></i>
                                </a>
                            </div>
                            <div class="card-body">
                                <div class="alert alert-info alert-dismissible fade show mb-3" role="alert">
                                    <strong>Severity Levels:</strong> Select "Severe" for critical alerts that require immediate attention in Telegram, or "Normal" for standard notifications.
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>
                                <form id="notifications-form">
                                    <div class="table-responsive">
                                        <table class="table table-hover">
                                            <thead>
                                                <tr>
                                                    <th>Alert Type</th>
                                                    <th class="text-center">Enable</th>
                                                    <th class="text-center">Severity</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td>
                                                        <label for="voltage" class="form-label mb-0">Voltage</label>
                                                    </td>
                                                    <td class="text-center">
                                                        <div class="form-check form-switch d-flex justify-content-center">
                                                            <input class="form-check-input pointer" type="checkbox" name="notifications" id="voltage" data-permission="voltage" value="voltage">
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="d-flex justify-content-center gap-3 severity-options" id="voltage-severity">
                                                            <div class="form-check form-check-inline">
                                                                <input class="form-check-input" type="radio" name="voltage-severity" id="voltage-normal" value="normal" checked disabled>
                                                                <label class="form-check-label" for="voltage-normal">Normal</label>
                                                            </div>
                                                            <div class="form-check form-check-inline">
                                                                <input class="form-check-input" type="radio" name="voltage-severity" id="voltage-severe" value="severe" disabled>
                                                                <label class="form-check-label" for="voltage-severe">Severe</label>
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <label for="overload" class="form-label mb-0">Overload/Current</label>
                                                    </td>
                                                    <td class="text-center">
                                                        <div class="form-check form-switch d-flex justify-content-center">
                                                            <input class="form-check-input pointer" type="checkbox" name="notifications" id="overload" data-permission="overload" value="overload">
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="d-flex justify-content-center gap-3 severity-options" id="overload-severity">
                                                            <div class="form-check form-check-inline">
                                                                <input class="form-check-input" type="radio" name="overload-severity" id="overload-normal" value="normal" checked disabled>
                                                                <label class="form-check-label" for="overload-normal">Normal</label>
                                                            </div>
                                                            <div class="form-check form-check-inline">
                                                                <input class="form-check-input" type="radio" name="overload-severity" id="overload-severe" value="severe" disabled>
                                                                <label class="form-check-label" for="overload-severe">Severe</label>
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <label for="power_fail" class="form-label mb-0">Input Power Fail</label>
                                                    </td>
                                                    <td class="text-center">
                                                        <div class="form-check form-switch d-flex justify-content-center">
                                                            <input class="form-check-input pointer" type="checkbox" name="notifications" id="power_fail" data-permission="power_fail" value="power_fail">
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="d-flex justify-content-center gap-3 severity-options" id="power_fail-severity">
                                                            <div class="form-check form-check-inline">
                                                                <input class="form-check-input" type="radio" name="power_fail-severity" id="power_fail-normal" value="normal" checked disabled>
                                                                <label class="form-check-label" for="power_fail-normal">Normal</label>
                                                            </div>
                                                            <div class="form-check form-check-inline">
                                                                <input class="form-check-input" type="radio" name="power_fail-severity" id="power_fail-severe" value="severe" disabled>
                                                                <label class="form-check-label" for="power_fail-severe">Severe</label>
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <label for="on_off" class="form-label mb-0">On/Off</label>
                                                    </td>
                                                    <td class="text-center">
                                                        <div class="form-check form-switch d-flex justify-content-center">
                                                            <input class="form-check-input pointer" type="checkbox" name="notifications" id="on_off" data-permission="on_off" value="on_off">
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="d-flex justify-content-center gap-3 severity-options" id="on_off-severity">
                                                            <div class="form-check form-check-inline">
                                                                <input class="form-check-input" type="radio" name="on_off-severity" id="on_off-normal" value="normal" checked disabled>
                                                                <label class="form-check-label" for="on_off-normal">Normal</label>
                                                            </div>
                                                            <div class="form-check form-check-inline">
                                                                <input class="form-check-input" type="radio" name="on_off-severity" id="on_off-severe" value="severe" disabled>
                                                                <label class="form-check-label" for="on_off-severe">Severe</label>
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <label for="mcb_contactor_trip" class="form-label mb-0">MCB/Contactor</label>
                                                    </td>
                                                    <td class="text-center">
                                                        <div class="form-check form-switch d-flex justify-content-center">
                                                            <input class="form-check-input pointer" type="checkbox" name="notifications" id="mcb_contactor_trip" data-permission="mcb_contactor_trip" value="mcb_contactor_trip">
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="d-flex justify-content-center gap-3 severity-options" id="mcb_contactor_trip-severity">
                                                            <div class="form-check form-check-inline">
                                                                <input class="form-check-input" type="radio" name="mcb_contactor_trip-severity" id="mcb_contactor_trip-normal" value="normal" checked disabled>
                                                                <label class="form-check-label" for="mcb_contactor_trip-normal">Normal</label>
                                                            </div>
                                                            <div class="form-check form-check-inline">
                                                                <input class="form-check-input" type="radio" name="mcb_contactor_trip-severity" id="mcb_contactor_trip-severe" value="severe" disabled>
                                                                <label class="form-check-label" for="mcb_contactor_trip-severe">Severe</label>
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <label for="door_alert" class="form-label mb-0">Panel Door</label>
                                                    </td>
                                                    <td class="text-center">
                                                        <div class="form-check form-switch d-flex justify-content-center">
                                                            <input class="form-check-input pointer" type="checkbox" name="notifications" id="door_alert" data-permission="door_alert" value="door_alert">
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="d-flex justify-content-center gap-3 severity-options" id="door_alert-severity">
                                                            <div class="form-check form-check-inline">
                                                                <input class="form-check-input" type="radio" name="door_alert-severity" id="door_alert-normal" value="normal" checked disabled>
                                                                <label class="form-check-label" for="door_alert-normal">Normal</label>
                                                            </div>
                                                            <div class="form-check form-check-inline">
                                                                <input class="form-check-input" type="radio" name="door_alert-severity" id="door_alert-severe" value="severe" disabled>
                                                                <label class="form-check-label" for="door_alert-severe">Severe</label>
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="d-flex justify-content-center align-items-center mt-3">
                                        <button type="button" class="btn btn-primary" onclick="updateSelectedAlerts()">Save Configuration</button>
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
    </div>
    </div>
    </div>
    </div>