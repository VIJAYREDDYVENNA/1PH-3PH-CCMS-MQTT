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
                    <p class="m-0 p-0"><span class="text-body-tertiary">Pages / </span><span>Threshold Settings</span>
                    </p>
                </div>
            </div>
            <?php
            include(BASE_PATH . "dropdown-selection/group-device-list.php");
            ?>
            <div class="row">
                <div class="col-12">
                    <div class="d-flex justify-content-end mt-3">
                        <button type="button" class="btn btn-primary btn-sm" id="add_devices_to_dp_selection" data-bs-toggle="modal" data-bs-target="#group_device_multiselection">Multiple Device Selection</button>
                    </div>
                </div>

                <div class="col-md-6 p-0 pe-md-2 mt-2">
                    <div class="card">
                        <div class="card-header bg-primary bg-opacity-25 fw-bold d-flex justify-content-between align-items-center">
                            <div>
                                <span class="me-2">Voltage</span>
                                <!-- Info icon after the title -->
                                <a tabindex="0" role="button" data-bs-toggle="popover" data-bs-trigger="focus" data-bs-title="Info" data-bs-content="Update the max-min voltage thresholds for each phase">
                                    <i class="bi bi-info-circle"></i>
                                </a>
                            </div>
                            <!-- speedometer2 icon at the end -->
                            <a role="button" data-bs-toggle="modal" data-bs-target="#statusModal" onclick="update_data_table('voltage')" style="color: green;">
                                <i class="bi bi-speedometer2" style="font-size: 0.9rem;"></i>
                            </a>
                        </div>

                        <div class="card-body row">
                            <div class="col-md-6 border-end" id="maxvoltage">
                                <div class="fw-bold m-0"> Lower threshold Voltages (V) </div>
                                <div class="mb-2 mt-1 ms-3">
                                    <label for="r_lower_volt" class="form-label text-danger">R Phase:</label>
                                    <input type="number" required="true" min="1" max="750" class="form-control" id="r_lower_volt" placeholder="Enter voltage 0 to 750">
                                    <div class="singlePhaseDisable">
                                        <label for="y_lower_volt" class="form-label text-warning">Y Phase:</label>
                                        <input type="number" required="true" min="1" max="750" class="form-control" id="y_lower_volt" placeholder="Enter voltage 0 to 750">
                                        <label for="b_lower_volt" class="form-label text-primary">B Phase:</label>
                                        <input type="number" required="true" min="1" max="750" class="form-control" id="b_lower_volt" placeholder="Enter voltage 0 to 750">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6" id="minvoltage">
                                <div class="fw-bold m-0"> Upper threshold Voltages (V) </div>
                                <div class="mb-2 mt-1 ms-3">
                                    <label for="r_upper_volt" class="form-label text-danger">R Phase:</label>
                                    <input type="number" required="true" min="1" max="750" class="form-control" id="r_upper_volt" placeholder="Enter voltage 0 to 750">
                                    <div class="singlePhaseDisable">
                                        <label for="y_upper_volt" class="form-label text-warning">Y Phase:</label>
                                        <input type="number" required="true" min="1" max="750" class="form-control" id="y_upper_volt" placeholder="Enter voltage 0 to 750">
                                        <label for="b_upper_volt" class="form-label text-primary">B Phase:</label>
                                        <input type="number" required="true" min="1" max="750" class="form-control" id="b_upper_volt" placeholder="Enter voltage 0 to 750">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer d-flex justify-content-between align-items-center">
                            <div class="w-100 text-center">
                                <button type="submit" class="btn btn-primary mb-2" id="voltage_values_btn">Update</button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 p-0 ps-md-2  mt-md-2 mt-3">
                    <div class="card h-100">
                        <div class="card-header bg-primary bg-opacity-25 fw-bold d-flex justify-content-between align-items-center">
                            <div>
                                <span class="me-2">Current</span>
                                <!-- Info icon after title -->
                                <a tabindex="0" role="button" data-bs-toggle="popover" data-bs-trigger="focus" data-bs-title="Info" data-bs-content="Update the max current thresholds for each phase">
                                    <i class="bi bi-info-circle"></i>
                                </a>
                            </div>
                            <!-- speedometer2 icon at the end -->
                            <a role="button" data-bs-toggle="modal" data-bs-target="#statusModal" onclick="update_data_table('current')" style="color: green;">
                                <i class="bi bi-speedometer2" style="font-size: 0.9rem;"></i>
                            </a>
                        </div>

                        <div class="card-body row">
                            <div class="col-md-12" id="maxcurrent">
                                <div class="fw-bold m-0"></div>
                                <div class="fw-bold m-0"> Max Current(A) </div>
                                <div class="mb-1  ms-2">
                                    <label for="r_current" class="form-label  text-danger">R Phase:</label>
                                    <input type="number" required="true" min="1" max="5000" class="form-control" id="r_current"
                                        placeholder="Enter Current">
                                </div>
                                <div class="singlePhaseDisable">
                                    <div class="mb-1 ms-2">
                                        <label for="y_current" class="form-label text-warning">Y Phase:</label>
                                        <input type="number" required="true" min="1" max="5000" class="form-control " id="y_current"
                                            placeholder="Enter Current">
                                    </div>
                                    <div class="mb-1 ms-2">
                                        <label for="b_current" class="form-label text-primary">B Phase:</label>
                                        <input type="number" required="true" min="1" max="5000" class="form-control" id="b_current"
                                            placeholder="Enter Current">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer d-flex justify-content-between align-items-center">
                            <div class="w-100 text-center">
                                <button type="submit" class="btn btn-primary mb-2" id="current_values_btn">Update</button>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
            <div class="row mt-3">
                <div class="col-md-6 col-lg-3  p-0  pe-md-2">
                    <div class="card">
                        <div class="card-header bg-primary bg-opacity-25 fw-bold d-flex justify-content-between align-items-center">
                            <div>
                                <span class="me-2">Unit Capacity:</span>
                                <!-- Info icon after title -->
                                <a tabindex="0" role="button" data-bs-toggle="popover" data-bs-trigger="focus" data-bs-title="Info" data-bs-content="Update the capacity of the installed unit.">
                                    <i class="bi bi-info-circle"></i>
                                </a>
                            </div>
                            <!-- speedometer2 icon at the end -->
                            <!-- <a role="button" data-bs-toggle="modal" data-bs-target="#statusModal" onclick="update_data_table('unit_capacity')" style="color: green;">
                                <i class="bi bi-speedometer2" style="font-size: 0.9rem;"></i>
                            </a> -->
                        </div>


                        <div class="card-body row">
                            <div class="col-md-12 ">
                                <div class="mb-2 ms-2">
                                    <label for="unit_capacity" class="form-label">Capacity(KW):</label>
                                    <input type="number" class="form-control" id="unit_capacity" onkeyup="check_validation('unit_capacity', 1, 5000)" required="true" min="1" max="5000" placeholder="Enter unit capacity(KW):">

                                </div>
                            </div>
                        </div>
                        <div class="card-footer d-flex justify-content-between align-items-center">

                            <div class="w-100 text-center">
                                <button type="submit" class="btn btn-primary mb-2" onclick="update_capcity()">Update</button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3  p-0  px-lg-2 ps-md-2 mt-md-0 mt-3">
                    <div class="card">
                        <div class="card-header bg-primary bg-opacity-25 fw-bold d-flex justify-content-between align-items-center">
                            <div>
                                <span class="me-2">Frame Update Time:</span>
                                <!-- Info icon after title -->
                                <a tabindex="0" role="button" data-bs-toggle="popover" data-bs-trigger="focus" data-bs-title="Info" data-bs-content="To Update the frame Time">
                                    <i class="bi bi-info-circle"></i>
                                </a>
                            </div>
                            <!-- speedometer2 icon at the end -->
                            <a role="button" data-bs-toggle="modal" data-bs-target="#statusModal" onclick="update_data_table('frame_time')" style="color: green;">
                                <i class="bi bi-speedometer2" style="font-size: 0.9rem;"></i>
                            </a>
                        </div>

                        <div class="card-body row">
                            <div class="col-md-12 " id="frame_update_time">
                                <div class="mb-2 ms-2">
                                    <label for="frame_update_time" class="form-label">Update Time:</label>
                                    <select class="form-select" id="frame_time">
                                        <option value="20">20 Sec </option>
                                        <option value="30">30 Sec </option>
                                        <option value="40">40 Sec </option>
                                        <option value="60">1 Min </option>
                                        <option value="120">2 Mins </option>
                                        <option value="180">3 Mins </option>
                                        <option value="240">4 Mins </option>
                                        <option value="300">5 Mins </option>
                                        <option value="600">10 Mins </option>
                                        <option value="900">15 Mins </option>
                                        <option value="1200">20 Mins </option>
                                        <option value="2400">40 Mins </option>
                                        <option value="3600">1 Hr </option>
                                        <option value="7200">2 Hrs </option>
                                        <option value="14400">4 Hrs </option>
                                        <option value="21600">6 Hrs </option>
                                        <option value="43200">12 Hrs </option>
                                        <option value="84600">24 Hrs </option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer d-flex justify-content-between align-items-center">

                            <div class="w-100 text-center">
                                <button type="submit" class="btn btn-primary mb-2" onclick="frame_interval_update()">Update</button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 col-lg-3 p-0 px-lg-2 pe-md-2 mt-lg-0 mt-3">

                    <div class="card">
                        <div class="card-header bg-primary bg-opacity-25 fw-bold d-flex justify-content-between align-items-center">
                            <div>
                                <span class="me-2">PF Settings:</span>
                                <!-- Info icon after title -->
                                <a tabindex="0" role="button" data-bs-toggle="popover" data-bs-trigger="focus" data-bs-title="Info" data-bs-content="To receive PF alerts, update PF value.">
                                    <i class="bi bi-info-circle"></i>
                                </a>
                            </div>
                            <!-- speedometer2 icon at the end -->
                            <!-- <a role="button" data-bs-toggle="modal" data-bs-target="#statusModal" onclick="update_data_table('pf_setting')" style="color: green;">
                                <i class="bi bi-speedometer2" style="font-size: 0.9rem;"></i>
                            </a> -->
                        </div>

                        <div class="card-body row">
                            <div class="col-md-12 ">
                                <div class="mb-2 ms-2">
                                    <label for="pf_settings" class="form-label">PF:</label>
                                    <input type="number" class="form-control" id="pf_settings" min="0.1" max="1" onkeyup="check_validation('pf_settings',0.1, 1)" placeholder="Enter Pf 0.1 to 0.99">

                                </div>
                            </div>
                        </div>
                        <div class="card-footer d-flex justify-content-between align-items-center">

                            <div class="w-100 text-center">
                                <button type="submit" class="btn btn-primary mb-2" onclick="update_pf()">Update</button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 col-lg-3 p-0 ps-md-2 mt-lg-0 mt-3">
                    <div class="card">
                        <div class="card-header bg-primary bg-opacity-25 fw-bold d-flex justify-content-between align-items-center">
                            <div>
                                <span class="me-2">CT Ratio:</span>
                                <!-- Info icon after title -->
                                <a tabindex="0" role="button" data-bs-toggle="popover" data-bs-trigger="focus" data-bs-title="Info" data-bs-content="To change the CT ratio value in the device.">
                                    <i class="bi bi-info-circle"></i>
                                </a>
                            </div>
                            <!-- speedometer2 icon at the end -->
                            <!-- <a role="button" data-bs-toggle="modal" data-bs-target="#statusModal" onclick="update_data_table('at_ratio')" style="color: green;">
                                <i class="bi bi-speedometer2" style="font-size: 0.9rem;"></i>
                            </a> -->
                        </div>

                        <div class="card-body row">
                            <div class="col-md-12 ">
                                <div class="mb-2 ms-2">
                                    <label for="ctratio" class="form-label">CT Ratio:</label>
                                    <input type="text" class="form-control" id="ctratio" onkeyup="check_validation('ctratio', 1, 2000)" placeholder="Enter CT ratio">

                                </div>
                            </div>
                        </div>
                        <div class="card-footer d-flex justify-content-between align-items-center">

                            <div class="w-100 text-center">
                                <button type="submit" class="btn btn-primary mb-2" onclick="update_ct_ratio()">Update</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row mt-3">
                <!-- <div class="col-md-4 p-0 pe-md-2">

                    <div class="card">
                        <div class="card-header bg-primary bg-opacity-25 fw-bold">
                            <span class="me-2">Temperature</span>
                            <a tabindex="0" role="button" data-bs-toggle="popover" data-bs-trigger="focus" data-bs-title="Info" data-bs-content="To establish the temperature threshold for all sensors"> <i class="bi bi-info-circle"></i>  </a>
                        </div>
                        <div class="card-body row">
                            <div class="col-md-12 " id="temperature">
                                <div class="mb-2 ms-2">
                                    <label for="temperature" class="form-label">Temp(*C):</label>
                                    <input type="text" class="form-control" id="temperature"
                                    placeholder="Enter Temperature">

                                </div>
                            </div>
                        </div>
                        <div class="card-footer d-flex justify-content-between align-items-center">
                            <div class="w-100 text-center">
                                <button type="submit" class="btn btn-primary mb-2">Update</button>
                            </div>
                        </div>
                    </div>

                </div> -->
                <!--  <div class="col-md-6 p-0 pe-md-2">

                    <div class="card">
                        <div class="card-header bg-primary bg-opacity-25 fw-bold">
                            <span class="me-2">PF Settings:</span>
                            <a tabindex="0" role="button" data-bs-toggle="popover" data-bs-trigger="focus" data-bs-title="Info" data-bs-content="To receive PF alerts, update PF value.">  <i class="bi bi-info-circle"></i>  </a>
                        </div>
                        <div class="card-body row">
                            <div class="col-md-12 " id="pf_settings">
                                <div class="mb-2 ms-2">
                                    <label for="pf_settings" class="form-label">PF:</label>
                                    <input type="text" class="form-control" id="pf_settings"
                                    placeholder="Enter Pf 0.1 to 0.99">

                                </div>
                            </div>
                        </div>
                        <div class="card-footer d-flex justify-content-between align-items-center">

                            <div class="w-100 text-center">
                                <button type="submit" class="btn btn-primary mb-2">Update</button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 p-0 ps-md-2 mt-md-0 mt-3">
                    <div class="card">
                        <div class="card-header bg-primary bg-opacity-25 fw-bold">
                            <span class="me-2">CT Ratio:</span>
                            <a tabindex="0" role="button" data-bs-toggle="popover" data-bs-trigger="focus" data-bs-title="Info" data-bs-content="To change the CT ratio value in the device.">  <i class="bi bi-info-circle"></i> </a>
                        </div>
                        <div class="card-body row">
                            <div class="col-md-12 " id="ct_ratio">
                                <div class="mb-2 ms-2">
                                    <label for="ctratio" class="form-label">CT Ratio:</label>
                                    <input type="text" class="form-control" id="ctratio" placeholder="Enter CT ratio">

                                </div>
                            </div>
                        </div>
                        <div class="card-footer d-flex justify-content-between align-items-center">

                            <div class="w-100 text-center">
                                <button type="submit" class="btn btn-primary mb-2">Update</button>
                            </div>
                        </div>
                    </div>
                </div> -->
            </div>
        </div>
    </div>
    </div>
    <!-- Modal for status and datetime -->


    </main>
    <?php
    include(BASE_PATH . "settings/html/update-status-modal.php");
    include(BASE_PATH . "dropdown-selection/multiple-group-device_selection.php");
    ?>
    <script src="<?php echo BASE_PATH; ?>js_modal_scripts/popover.js"></script>
    <script src="<?php echo BASE_PATH; ?>assets/js/sidebar-menu.js"></script>
    <script src="<?php echo BASE_PATH; ?>assets/js/project/settings.js"></script>
    <?php
    include(BASE_PATH . "assets/html/body-end.php");
    include(BASE_PATH . "assets/html/html-end.php");
    ?>