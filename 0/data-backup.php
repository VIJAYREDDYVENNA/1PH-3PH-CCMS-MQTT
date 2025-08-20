<?php
require_once 'config-path.php';
require_once '../session/session-manager.php';
SessionManager::checkSession();
?>
<!DOCTYPE html>
<html lang="en" data-bs-theme="auto">

<head>
    <title>Data Backup/Reset</title>
    <?php
    include(BASE_PATH . "assets/html/start-page.php");
    ?>
    <div class="d-flex flex-column flex-shrink-0 p-3 main-content">
        <div class="container-fluid">

            <div class="row d-flex align-items-center">
                <div class="col-12 p-0">
                    <p class="m-0 p-0"><span class="text-body-tertiary">Pages / </span><span>Data Backup / Reset</span></p>
                </div>
            </div>
            <?php
            include(BASE_PATH . "dropdown-selection/group-device-list.php");
            ?>

            <div class="row my-4">
                <div class="col-12 col-md-6">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Backup Data</h5>
                            <p>Select the format for backing up your data:</p>
                            <div class="d-grid gap-2 d-md-flex justify-content-md-start">
                                <button class="btn btn-primary btn-block mb-2" id="backup-excel" onclick="data_backup('backup-excel')">Backup in Excel</button>
                                <button class="btn btn-secondary btn-block mb-2" id="backup-sql" onclick="data_backup('backup-sql')">Backup in SQL</button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Reset Section -->
                <div class="col-12 col-md-6">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Reset Device</h5>
                            <p>Click the button below to reset all data:</p>
                            <button class="btn btn-danger btn-block mb-2" id="reset-data" onclick="reset()">Reset Device</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="<?php echo BASE_PATH; ?>assets/js/project/data-backup.js"></script>
    <script src="<?php echo BASE_PATH; ?>assets/js/sidebar-menu.js"></script>
    <?php
    include(BASE_PATH . "assets/html/body-end.php");
    include(BASE_PATH . "assets/html/html-end.php");
    ?>
