<?php
require_once 'config-path.php';
require_once '../session/session-manager.php';
SessionManager::checkSession();
$sessionVars = SessionManager::SessionVariables();

$mobile_no = $sessionVars['mobile_no'];
$user_id = $sessionVars['user_id'];
$role = $sessionVars['role'];

?>
<!DOCTYPE html>
<html lang="en" data-bs-theme="auto">

<head>
    <title>Device List</title>
    <?php
    include(BASE_PATH . "assets/html/start-page.php");
    ?>
    <div class="d-flex flex-column flex-shrink-0 p-3 main-content ">
        <div class="container-fluid">
            <div class="row d-flex align-items-center">
                <div class="col-12 p-0">
                    <p class="m-0 p-0"><span class="text-body-tertiary">Pages / </span><span>Device List</span></p>
                </div>
            </div>
            <?php
            include(BASE_PATH . "dropdown-selection/device-list.php");
            ?>

            <div class="row justify-content-end align-items-center mt-3 p-0">
                <div class="col-auto p-0 mb-2">
                    <div class="input-group">
                        <input type="text" class="form-control" placeholder="Search..." id="deviceListInput">
                        <button class="btn btn-primary" type="button" onclick="deviceListSearch()">
                            <i class="bi bi-search"></i> Search
                        </button>
                    </div>
                </div>
                <div class="col-auto p-0 mb-2 ms-2">
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addDeviceModal">
                        Add Device
                    </button>
                </div>
            </div>
            <div class="row">
                <div class="col-12 p-0">
                    <div class="table-responsive rounded border">
                        <table class="table table-striped table-type-1 w-100 text-center deviceListSearch">
                            <thead>
                                <tr>
                                    <th class="table-header-row-1">Device-ID</th>
                                    <th class="table-header-row-1">Device-Name</th>
                                    <th class="table-header-row-1">Installation Status</th>
                                    <th class="table-header-row-1">Installed Date</th>
                                    <th class="table-header-row-1">Capacity(kW)</th>
                                    <th class="table-header-row-1 col-size-1">Last Update</th>
                                    <th class="table-header-row-1">On/Off Status</th>
                                    <th class="table-header-row-1">Operation Mode</th>
                                    <th class="table-header-row-1">Active Status</th>
                                    <th class="table-header-row-1">Location</th>
                                    <th class="table-header-row-1">Installed Lights</th>
                                    <th class="table-header-row-1">Actions</th>
                                </tr>
                            </thead>
                            <tbody id="device_list_table">
                                <tr>
                                    <td class="text-danger" colspan="12">Device List not found</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>
    <!-- Edit Modal -->


    </div>
    <?php
    include(BASE_PATH . "device-list/html/add_device.php");
    include(BASE_PATH . "device-list/html/installedlights.php");
    include(BASE_PATH . "device-list/html/addlight.php");
    include(BASE_PATH . "device-list/html/edit_name_modal.php");

    ?>
    </main>
    <script src="<?php echo BASE_PATH; ?>assets/js/sidebar-menu.js"></script>
    <script src="<?php echo BASE_PATH; ?>assets/js/project/device-list.js"></script>
    <script src="<?php echo BASE_PATH; ?>js_modal_scripts/searchbar.js"></script>
    <?php
    include(BASE_PATH . "assets/html/body-end.php");
    include(BASE_PATH . "assets/html/html-end.php");
    ?>