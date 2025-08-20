<?php
require_once 'config-path.php';
require_once '../session/session-manager.php';
SessionManager::checkSession();
$sessionVars = SessionManager::SessionVariables();
$role = $sessionVars['role'];
?>
<!DOCTYPE html>
<html lang="en" data-bs-theme="auto">

<head>
    <title>Device Dashboard</title>
    <?php
    include(BASE_PATH . "assets/html/start-page.php");
    ?>
    <div class="d-flex flex-column flex-shrink-0 p-3 main-content">
        <div class="container-fluid">
            <div class="row d-flex align-items-center">
                <div class="col-12 p-0">
                    <p class="m-0 p-0"><span class="text-body-tertiary">Pages / </span><span>Device Dashboard</span></p>
                </div>
            </div>
            <?php
            include(BASE_PATH . "dropdown-selection/device-list.php");
            ?>
            <div class="row">
                <div class="col-lg-8">
                    <div class="row pe-0 pe-lg-2 h-100">
                        <div class="col-12 rounded mt-2 p-0 ">
                            <div class="row">
                                <div class="col-md-4 col-12 pointer">
                                    <div class="card text-start shadow" data-bs-toggle="modal" data-bs-target="#TotalModal" id="total_device">
                                        <div class="card-body m-0 p-0">
                                        <div class="fs-6">Total Lights <span id="transformer_kwh_total_load" class="fw-bold fs-6">10</span></div>
                                        <div class="fs-6">Total on% <span id="transformer_kvah_total_load" class="fw-bold fs-6"></span></div>
                                        <div class="text-muted fs-6">Total off% <span id="transformer_kvah_total_load" class="fw-bold fs-6"></span></div>

                                        </div>
                                    </div>
                                    <div>
                                        <h5 class="card-title">lights</h5>
                                        <div class="chart-container">
                                            <canvas id="floorDevicesChart"></canvas>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-6 col-md-4 mt-3 mt-md-0 pointer ">
                                    <div class="card text-center shadow" data-bs-toggle="modal" data-bs-target="#installedModal" id="installed_devices_list">
                                        <div class="card-body m-0 p-0">
                                            <p class="card-text fw-semibold text-info-emphasis m-0 py-1"><i class="bi bi-check-circle-fill h4"></i> Installed</p>
                                            <h3 class="card-title py-2 text-primary-emphasis" id="installed_devices">0</h3>
                                        </div>
                                    </div>

                                    <div>
                                        <h5 class="card-title">lights</h5>
                                        <div class="chart-container">
                                            <canvas id="floorDevicesChart"></canvas>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-6 col-md-4 mt-3 mt-md-0 pointer">
                                    <div class="card text-center shadow" data-bs-toggle="modal" data-bs-target="#notinstalledModal" id="not_installed_devices_list">
                                        <div class="card-body m-0 p-0">
                                            <p class="card-text fw-semibold text-info-emphasis m-0 py-1"><i class="bi bi-x-circle-fill h4"></i> Not-installed</p>
                                            <h3 class="card-title py-2 text-primary-emphasis" id="not_installed_devices">0</h3>
                                        </div>
                                    </div>
                                    <div>
                                        <h5 class="card-title">lights</h5>
                                        <div class="chart-container">
                                            <canvas id="floorDevicesChart"></canvas>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

                <div class="col-lg-4 ">
                    <div class="row ps-0 ps-lg-2 h-100">
                        <div class="col-12 rounded mt-4 mt-lg-2 p-0">
                            <div class="card bg-light-subtle shadow">
                                <div class="card-header fw-bold">
                                    <i class="bi bi-chat-dots-fill"></i> Updates
                                </div>
                                <div class="card-body">
                                    <div id="alerts_list" class="list-group overflow-y-auto" style=" height:<?php echo $alerts_card_height; ?>;">

                                    </div>
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
    include(BASE_PATH . "dashboard/dashboard_modals.php");
    ?>

    </main>
    <script src="<?php echo BASE_PATH; ?>assets/js/sidebar-menu.js"></script>
    <script src="<?php echo BASE_PATH; ?>assets/js/project/dashboard.js"></script>


    <?php
    include(BASE_PATH . "assets/html/body-end.php");
    include(BASE_PATH . "assets/html/html-end.php");
    ?>