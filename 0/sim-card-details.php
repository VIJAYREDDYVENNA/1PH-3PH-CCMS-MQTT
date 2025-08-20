<!DOCTYPE html>
<html lang="en" data-bs-theme="auto">

<head>
    <title>Sim Card Details</title>
    <?php
    require_once 'config-path.php';
    require_once '../session/session-manager.php';
    SessionManager::checkSession();
    $sessionVars = SessionManager::SessionVariables();

    $mobile_no = $sessionVars['mobile_no'];
    $user_id = $sessionVars['user_id'];
    $role = $sessionVars['role'];

    include(BASE_PATH . "assets/html/start-page.php");
    ?>
    <div class="d-flex flex-column flex-shrink-0 p-3 main-content ">
        <div class="container-fluid">
            <div class="row d-flex align-items-center">
                <div class="col-12 p-0">
                    <p class="m-0 p-0"><span class="text-body-tertiary">Pages / </span><span>Sim Card Details</span></p>
                </div>
            </div>

            <?php
            include(BASE_PATH . "dropdown-selection/device-list.php");
            ?>

            <div class="row justify-content-end align-items-center mt-3 p-0">
                <div class="col-auto p-0 mb-2">
                    <div class="input-group">
                        <input type="text" class="form-control" placeholder="Search..." id="deviceListInput">
                        <button class="btn btn-primary" type="button" onclick="SimListSearch()">
                            <i class="bi bi-search"></i> Search
                        </button>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-12 p-0">
                    <div class="table-responsive rounded border">
                        <table class="table text-center table-bordered  w-100 SimListSearch">
                            <thead>
                                <tr>
                                    <th class="table-header1-row-1">Device-ID</th>
                                    <th class="table-header1-row-1">SIM CCID</th>
                                    <th class="table-header1-row-1">IMEI Number</th>
                                    <th class="table-header1-row-1">Firmware Version</th>
                                    <th class="table-header1-row-1">PCB Version</th>
                                    <th class="table-header1-row-1">Date Time</th>
                                </tr>
                            </thead>
                            <tbody id="sim_list_table">
                                <tr>
                                    <td class="text-danger" colspan="6">Device List not found!</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="pagination-wrapper mt-2">
                <div class="row">
                    <div class="col">
                        <div class="row g-2 align-items-center d-flex">
                            <div class="col-auto">
                                <label for="items-per-page" class="form-label">Items per page:</label>
                            </div>
                            <div class="col-auto">
                                <select id="items-per-page" class="form-select">
                                    <option value="100"selected>100</option>
                                    <option value="200" >200</option>
                                    <option value="300">300</option>
                                    <option value="500">500</option>
                                    <option value="1000">1000</option>
                                </select>
                            </div>
                        </div>

                    </div>
                    <div class="col">
                        <div class="pagination-container">
                            <nav>
                                <ul class="pagination justify-content-end " id="pagination">
                                </ul>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
    </div>

    <script>

    </script>
    <script src="<?php echo BASE_PATH; ?>assets/js/sidebar-menu.js"></script>
    <script src="<?php echo BASE_PATH; ?>assets/js/project/sim-card-details.js"></script>
    <script src="<?php echo BASE_PATH; ?>sim-card-details/js/search-bar.js"></script>

    <?php
    include(BASE_PATH . "assets/html/body-end.php");
    include(BASE_PATH . "assets/html/html-end.php");
    ?>