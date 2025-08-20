<?php
require_once 'config-path.php';
require_once '../session/session-manager.php';
SessionManager::checkSession();
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
                <div class="col-lg-12 p-0">
                    <p class="m-0 p-0"><span class="text-body-tertiary">Pages / </span><span>IOT Settings</span></p>
                </div>
            </div>
            <?php
            include(BASE_PATH . "dropdown-selection/group-device-list.php");
            ?>
            <div class="row ">
                <div class="container-fluid">
                    <div class="row">
                        <div class="d-flex justify-content-end mt-3">
                            <button type="button" class="btn btn-primary btn-sm" id="add_devices_to_dp_selection" data-bs-toggle="modal" data-bs-target="#group_device_multiselection">Multiple Device Selection</button>
                        </div>
                        <div class="col-lg-3 col-md-6 p-0 mt-3 pe-md-2">
                            <div class="card">
                                <div class="card-header bg-primary bg-opacity-25 fw-bold d-flex justify-content-between align-items-center">
                                    <div>
                                        <span class="me-2">Device ID change</span>
                                        <a tabindex="0" role="button" data-bs-toggle="popover" data-bs-trigger="focus" data-bs-title="Info" data-bs-content="Update the new  Device id"> <i class="bi bi-info-circle"></i>
                                        </a>
                                    </div>
                                    <a role="button" data-bs-toggle="modal" data-bs-target="#statusModal" onclick="update_data_table('ID_CHANGE')" style="color: green;">
                                        <i class="bi bi-speedometer2" style="font-size: 0.9rem;"></i>
                                    </a>
                                </div>
                                <div class="card-body row">
                                    <div class="mb-2 ms-2">
                                        <label for="new_deviceid_change" class="form-label">New Device ID:</label>
                                        <input type="text" class="form-control" id="new_deviceid_change"
                                            placeholder="Enter New Device ID">
                                    </div>
                                </div>
                                <div class="card-footer d-flex justify-content-between align-items-center flex-wrap">
                                    <div class="w-100 text-center">
                                        <button type="submit" class="btn btn-primary mb-2" onclick="device_id_change()">Update</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6 p-0 mt-3 pe-lg-2 ps-md-2">
                            <div class="card">
                                <div class="card-header bg-primary bg-opacity-25 fw-bold d-flex justify-content-between align-items-center">
                                    <div>
                                        <span class="me-2">Device Serial ID</span>
                                        <a tabindex="0" role="button" data-bs-toggle="popover" data-bs-trigger="focus" data-bs-title="Info" data-bs-content="Update the new  serial id."> <i class="bi bi-info-circle"></i>
                                        </a>
                                    </div>
                                    <a role="button" data-bs-toggle="modal" data-bs-target="#statusModal" onclick="update_data_table('SERIAL_ID')" style="color: green;">
                                        <i class="bi bi-speedometer2" style="font-size: 0.9rem;"></i>
                                    </a>
                                </div>
                                <div class="card-body row">
                                    <div class="mb-2 ms-2">
                                        <label for="new_device_serial_id" class="form-label">New Serial ID:</label>
                                        <input type="text" class="form-control" id="new_device_serial_id"
                                            placeholder="Enter New Serial ID">
                                    </div>
                                </div>
                                <div class="card-footer d-flex justify-content-between align-items-center flex-wrap">
                                    <div class="w-100 text-center">
                                        <button type="submit" class="btn btn-primary mb-2" onclick="serial_id_change()">Update</button>
                                    </div>

                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6 p-0 mt-3 px-lg-2 pe-md-2">
                            <div class="card  mb-3">
                                <div class="card-header bg-primary bg-opacity-25 fw-bold d-flex justify-content-between align-items-center">
                                    <div>
                                        <span class="me-2">Hysteresis Setting</span>
                                        <a tabindex="0" role="button" data-bs-toggle="popover" data-bs-trigger="focus" data-bs-title="Info" data-bs-content="Update the hysteresis value."> <i class="bi bi-info-circle"></i>
                                        </a>
                                    </div>
                                    <a role="button" data-bs-toggle="modal" data-bs-target="#statusModal" onclick="update_data_table('HYSTERESIS')" style="color: green;">
                                        <i class="bi bi-speedometer2" style="font-size: 0.9rem;"></i>
                                    </a>
                                </div>
                                <div class="card-body row">
                                    <div class="mb-2 ms-2">
                                        <label for="hysteresis_value" class="form-label">Value:</label>
                                        <select class="form-select" id="hysteresis_value">
                                            <option value="5">5</option>
                                            <option value="10">10</option>
                                            <option value="15">15</option>
                                            <option value="20">20</option>
                                            <option value="25">25</option>
                                            <option value="30">30</option>
                                            <option value="35">35</option>
                                            <option value="40">40</option>
                                            <option value="45">45</option>
                                            <option value="50">50</option>
                                            <option value="60">60</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="card-footer d-flex justify-content-between align-items-center flex-wrap">
                                    <div class="w-100 text-center">
                                        <button type="submit" class="btn btn-primary mb-2" onclick="update_hysteresis()">Update</button>
                                    </div>
                                </div>
                            </div>
                        </div>


                        <div class="col-lg-3 col-md-6 p-0 mt-3 ps-md-2 ">
                            <div class="card flex-grow-1">
                                <div class="card-header bg-primary bg-opacity-25 fw-bold d-flex justify-content-between align-items-center">
                                    <div>
                                        <span class="me-2">On/Off Interval Time</span>
                                        <a tabindex="0" role="button" data-bs-toggle="popover" data-bs-trigger="focus" data-bs-title="Info" data-bs-content="Update the interval time for on/off.">
                                            <i class="bi bi-info-circle"></i>
                                        </a>
                                    </div>
                                    <a role="button" data-bs-toggle="modal" data-bs-target="#statusModal" onclick="update_data_table('LOOP_ON_OFF')" style="color: green;">
                                        <i class="bi bi-speedometer2" style="font-size: 0.9rem;"></i>
                                    </a>
                                </div>

                                <div class="card-body row">

                                    <div class="mb-2 ms-2">
                                        <label for="on_off_interval_time_value" class="form-label">Value:</label>
                                        <select class="form-select" id="on_off_interval_time_value">
                                            <option value="0">OFF</option>
                                            <option value="1">1</option>
                                            <option value="2">2</option>
                                            <option value="3">3</option>
                                            <option value="5">5</option>
                                            <option value="10">10</option>
                                            <option value="15">15</option>
                                            <option value="20">20</option>
                                            <option value="25">25</option>
                                            <option value="30">30</option>
                                            <option value="35">35</option>
                                            <option value="40">40</option>
                                            <option value="45">45</option>
                                            <option value="50">50</option>
                                            <option value="60">60</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="card-footer d-flex justify-content-between align-items-center flex-wrap">
                                    <div class="w-100 text-center">
                                        <button type="submit" class="btn btn-primary mb-2" onclick="on_off_inverval_update()">Update</button>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-4 p-0 mt-3 pe-sm-3">
                            <div class="card h-100">
                                <div class="card-header bg-primary bg-opacity-25 fw-bold d-flex justify-content-between align-items-center">
                                    <div>
                                        <span class="me-2">Energy</span>
                                        <a tabindex="0" role="button" data-bs-toggle="popover" data-bs-trigger="focus" data-bs-title="Info" data-bs-content="Update the Device's Energy.">
                                            <i class="bi bi-info-circle"></i>
                                        </a>
                                    </div>
                                    <a role="button" data-bs-toggle="modal" data-bs-target="#statusModal" onclick="update_data_table('ENERGY_RESET')" style="color: green;">
                                        <i class="bi bi-speedometer2" style="font-size: 0.9rem;"></i>
                                    </a>
                                </div>

                                <div class="card-body row">
                                    <div class="mb-2 ms-2">
                                        <label for="pf_settings" class="form-label">KWH:</label>
                                        <input type="text" class="form-control" id="energy_kwh" placeholder="0">
                                        <label for="pf_settings" class="form-label">KVAH:</label>
                                        <input type="text" class="form-control" id="energy_kvah" placeholder="0">
                                    </div>
                                </div>
                                <div class="card-footer d-flex justify-content-between align-items-center flex-wrap">
                                    <div class="d-flex flex-column flex-sm-row w-100 justify-content-center">

                                        <button type="button" class="btn btn-primary mb-2" onclick="reset_energy_values()">Update</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-4 p-0 mt-3 pe-sm-2">
                            <div class="card h-100">
                                <div class="card-header bg-primary bg-opacity-25 fw-bold d-flex justify-content-between align-items-center">
                                    <div>
                                        <span class="me-2">WIFI</span>
                                        <a tabindex="0" role="button" data-bs-toggle="popover" data-bs-trigger="focus" data-bs-title="Info" data-bs-content="Update the details of wifi.">
                                            <i class="bi bi-info-circle"></i>
                                        </a>
                                    </div>
                                    <a role="button" data-bs-toggle="modal" data-bs-target="#statusModal" onclick="update_data_table('WIFI_CREDENTIALS')" style="color: green;">
                                        <i class="bi bi-speedometer2" style="font-size: 0.9rem;"></i>
                                    </a>
                                </div>


                                <div class="card-body row">
                                    <div class="mb-2 ms-2">
                                        <label for="ssid" class="form-label">Access Point:</label>
                                        <input type="text" class="form-control" id="ssid"
                                            placeholder="Enter Access Point">
                                        <label for="Password" class="form-label">Password:</label>
                                        <input type="password" class="form-control" id="password"
                                            placeholder="Enter Password">
                                    </div>
                                </div>
                                <div class="card-footer d-flex justify-content-between align-items-center flex-wrap">
                                    <div class="w-100 text-center">
                                        <button type="submit" class="btn btn-primary mb-2" onclick="update_wifi_credentials()">Update</button>
                                    </div>
                                </div>
                            </div>
                        </div>


                        <div class="col-sm-4 p-0 mt-3 ps-sm-2 ">

                            <div class="h-100 ">
                                <div class="card ">
                                    <div class="card-header bg-primary bg-opacity-25 fw-bold d-flex justify-content-between align-items-center">
                                        <div>
                                            <span class="me-2">Read IOT Settings</span>
                                            <a tabindex="0" role="button" data-bs-toggle="popover" data-bs-trigger="focus" data-bs-title="Info" data-bs-content="Read more about device settings">
                                                <i class="bi bi-info-circle"></i>
                                            </a>
                                        </div>
                                        <a role="button" data-bs-toggle="modal" data-bs-target="#statusModal" onclick="update_data_table('READ_SETTINGS')" style="color: green;">
                                            <i class="bi bi-speedometer2" style="font-size: 0.9rem;"></i>
                                        </a>
                                    </div>

                                    <div class="card-footer d-flex justify-content-between align-items-center flex-wrap">
                                        <div class="w-100 text-center">
                                            <button type="submit" class="btn btn-primary mb-2" onclick="read_iot_settings()">Update</button>
                                        </div>

                                    </div>
                                </div>


                                <div class="card mt-3">
                                    <div class="card-header bg-primary bg-opacity-25 fw-bold d-flex justify-content-between align-items-center">
                                        <div>
                                            <span class="me-2">Reset</span>
                                            <a tabindex="0" role="button" data-bs-toggle="popover" data-bs-trigger="focus" data-bs-title="Info" data-bs-content="Reset the Device">
                                                <i class="bi bi-info-circle"></i>
                                            </a>
                                        </div>
                                        <a role="button" data-bs-toggle="modal" data-bs-target="#statusModal" onclick="update_data_table('RESET')" style="color: green;">
                                            <i class="bi bi-speedometer2" style="font-size: 0.9rem;"></i>
                                        </a>
                                    </div>

                                    <div class="card-footer d-flex justify-content-between align-items-center flex-wrap">
                                        <div class="w-100 text-center">
                                            <button type="submit" class="btn btn-primary mb-2" onclick="reset_iot_device()">Reset</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- <div class="row">
                        <div class="col-md-6 p-0 mt-3 pe-md-2 d-flex flex-column">
                            <div class="card flex-grow-1">
                                <div class="card-header bg-primary bg-opacity-25 fw-bold">
                                    <span class="me-2">Angle</span>
                                    <a tabindex="0" role="button" data-bs-toggle="popover" data-bs-trigger="focus" data-bs-title="Info" data-bs-content="To change angle."> <i class="bi bi-info-circle"></i>
                                    </a>
                                </div>
                                <div class="card-body row">
                                    <form class="col-sm-6 border-end" id="maxangle">
                                        <div class="fw-bold m-0">Angle (below)</div>
                                        <div class="mb-2 ms-2">
                                            <label for="r_phaseangle" class="form-label text-danger">R Phase:</label>
                                            <input type="text" class="form-control" id="r_phaseangle"
                                            placeholder="Enter angle (0-9)">
                                            <label for="y_phaseangle" class="form-label text-warning">Y Phase:</label>
                                            <input type="text" class="form-control" id="y_phaseangle"
                                            placeholder="Enter angle (0-9)">
                                            <label for="b_phaseangle" class="form-label text-primary">B Phase:</label>
                                            <input type="text" class="form-control" id="b_phaseangle"
                                            placeholder="Enter angle (0-9)">
                                        </div>
                                    </form>
                                    <form class="col-sm-6" id="minangle">
                                        <div class="fw-bold m-0">Angle (above)</div>
                                        <div class="mb-2 ms-2">
                                            <label for="r_phaseangle" class="form-label text-danger">R Phase:</label>
                                            <input type="text" class="form-control" id="r_phaseangle"
                                            placeholder="Enter angle (0-9)">
                                            <label for="y_phaseangle" class="form-label text-warning">Y Phase:</label>
                                            <input type="text" class="form-control" id="y_phaseangle"
                                            placeholder="Enter angle (0-9)">
                                            <label for="b_phaseangle" class="form-label text-primary">B Phase:</label>
                                            <input type="text" class="form-control" id="b_phaseangle"
                                            placeholder="Enter angle (0-9)">
                                        </div>
                                    </form>
                                </div>
                                <div class="card-footer d-flex justify-content-between align-items-center flex-wrap">
                                    <div class="w-100 text-center">
                                        <button type="submit" class="btn btn-primary mb-2">Update</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div> -->

                </div>

            </div>
        </div>
    </div>
    </div>
    </main>

    <?php
    include(BASE_PATH . "settings/html/update-status-modal.php");

    include(BASE_PATH . "dropdown-selection/multiple-group-device_selection.php");
    ?>

    <script src="<?php echo BASE_PATH; ?>js_modal_scripts/popover.js"></script>
    <script src="<?php echo BASE_PATH; ?>assets/js/sidebar-menu.js"></script>
    <script src="<?php echo BASE_PATH; ?>assets/js/project/settings-iot.js"></script>
    <?php
    include(BASE_PATH . "assets/html/body-end.php");
    include(BASE_PATH . "assets/html/html-end.php");
    ?>