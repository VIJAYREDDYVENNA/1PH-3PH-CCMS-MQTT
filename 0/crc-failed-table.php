<?php
require_once 'config-path.php';
require_once '../session/session-manager.php';
SessionManager::checkSession();
?>
<!DOCTYPE html>
<html lang="en" data-bs-theme="auto">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <title>CRC Failed Data</title>

    <style>
        /* .frame-data {
            max-width: 300px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        } */

        .frame-data-full {
            display: none;
            white-space: pre-wrap;
            word-break: break-all;
            border: 1px solid #e9ecef;
            border-radius: 4px;
            padding: 10px;
            margin-top: 5px;
            font-family: monospace;
            font-size: 0.9rem;
        }

        .show-more-btn {
            color: #4a6da7;
            cursor: pointer;
            font-size: 0.8rem;
            text-decoration: underline;
            margin-left: 5px;
        }

        .show-more-btn:hover {
            color: #3c5a8a;
        }

        .table-container {
            position: relative;
            min-height: 200px;
            max-height: 600px;
            overflow-y: auto;
        }

        .loading-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 10;
            background-color: rgba(var(--bs-body-bg-rgb), 0.7);
        }

        .spinner-border {
            width: 3rem;
            height: 3rem;
        }

        .form-select:focus {
            border-color: #4a6da7;
            box-shadow: 0 0 0 0.25rem rgba(74, 109, 167, 0.25);
        }

        .table-sticky-header th {
            position: sticky;
            top: 0;
            z-index: 1;
            background-color: var(--bs-table-bg);
        }

        .breadcrumb-item a {
            text-decoration: none;
        }

        .no-data-message {
            padding: 20px;
            text-align: center;
        }

        /* Make S.No column narrower */
        .table th:first-child,
        .table td:first-child {
            width: 60px;
            text-align: center;
        }

        /* Make date column fixed width */
        .table th:last-child,
        .table td:last-child {
            width: 180px;
        }

        /* Allow frame data column to expand */
        .table th:nth-child(2),
        .table td:nth-child(2) {
            min-width: 200px;
        }

        /* Toast notifications */
        .toast-container {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 1050;
        }
    </style>
</head>

<body>
    <?php
    include(BASE_PATH . "assets/html/start-page.php");
    ?>
    <div class="d-flex flex-column flex-shrink-0 p-3 main-content">
        <div class="container-fluid">


            <!-- Breadcrumb -->
            <div class="row d-flex align-items-center mb-1">
                <div class="col-12 p-0">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="#" class="text-body-tertiary">Pages</a></li>
                            <li class="breadcrumb-item active">CRC Failed Data</li>
                        </ol>
                    </nav>
                </div>
            </div>

            <!-- Date search -->
            <!-- Date search -->
            <div class="col-12 d-flex justify-content-end mb-2">
                <div style="max-width: 250px; width: 100%;">
                    <label class="form-label text-muted small mb-1">Select Date:</label>
                    <div class="input-group input-group-sm">
                        <span class="input-group-text">
                            <i class="fas fa-calendar-alt text-primary"></i>
                        </span>
                        <input type="date" class="form-control" id="search_date">
                        <button class="btn btn-primary" type="button" id="search-button" onclick="search_records()">
                            <i class="fas fa-search me-1"></i> Search
                        </button>
                    </div>
                </div>
            </div>


            <!-- Main card -->
            <div class="col-12 mt-2">

                <div class="card shadow-sm">
                    <div class="card-header">
                        <div class="row align-items-center">
                            <div class="col-md-6">
                                <h5 class="card-title mb-0">
                                    <i class="fas fa-exclamation-triangle text-warning me-2"></i>CRC Failed Data Records
                                </h5>
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-container">
                            <div class="table-responsive">
                                <table class="table table-striped table-bordered table-hover table-sticky-header mb-0">
                                    <thead>
                                        <tr>
                                            <th>S.No</th>
                                            <th>Frame Data</th>
                                            <th>Date & Time</th>
                                        </tr>
                                    </thead>
                                    <tbody id="crc_data_body">
                                        <!-- Data will be loaded here via AJAX -->
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="text-muted small">
                                Showing records with CRC validation failures
                            </div>
                            <div>
                                <button id="refresh-btn" class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-sync-alt me-1"></i> Refresh
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 d-flex justify-content-end">
                <button class="btn btn-secondary btn-sm mt-2" id="btn_add_more" onclick="add_more_records()">
                    <i class="fas fa-plus me-1"></i> More Records
                </button>
            </div>
        </div>

    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="<?php echo BASE_PATH; ?>assets/js/project/crc_table_update.js"></script>
    <script src="<?php echo BASE_PATH; ?>assets/js/sidebar-menu.js"></script>

    <?php include(BASE_PATH . "assets/html/body-end.php"); ?>
    <?php include(BASE_PATH . "assets/html/html-end.php"); ?>
</body>

</html>