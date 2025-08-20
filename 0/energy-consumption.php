<?php
require_once 'config-path.php';
require_once '../session/session-manager.php';
SessionManager::checkSession();

$sessionVars = SessionManager::SessionVariables();

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Energy Consumption</title>
    <?php
    include(BASE_PATH . "assets/html/start-page.php");
    ?>
    <style>
       
        
        /* Form Styling */
        .form-control:focus, .form-select:focus {
            border-color: rgba(9, 88, 172, 0.5);
            box-shadow: 0 0 0 0.25rem rgba(9, 88, 172, 0.25);
        }
        
        .time-note {
            font-size: 0.875rem;
            /* color: #6c757d; */
        }
        
        /* Card Styling */
        .card {
            border-radius: 0.75rem;
            transition: all 0.2s;
            border: 1px solid rgba(0, 0, 0, 0.08);
        }
        
        .card-header {
            background-color: rgba(9, 88, 172, 0.03);
            border-bottom: 1px solid rgba(0, 0, 0, 0.08);
            font-weight: 600;
        }
        
        /* Button Styling */
        .btn-primary {
            background-color: rgba(9, 88, 172, 0.85);
            border-color: rgba(9, 88, 172, 0.85);
        }
        
        .btn-primary:hover, .btn-primary:focus {
            background-color: rgba(8, 62, 119, 0.9);
            border-color: rgba(8, 62, 119, 0.9);
        }
        
        /* Result Card */
        .result-card {
            border: none;
            border-radius: 0.75rem;
            overflow: hidden;
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.08);
        }
        
        .result-header {
            background: rgba(9, 88, 172, 0.9);
            color: white;
            padding: 1.25rem 1.5rem;
        }
        
        .consumption-value {
            font-size: 2rem;
            font-weight: 600;
            color: rgba(9, 88, 172, 0.85);
        }
        
        .consumption-card {
            border-radius: 0.75rem;
            transition: transform 0.2s;
        }
        
        .consumption-card:hover {
            transform: translateY(-3px);
        }
        
        .icon-container {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem;
        }
        
        .bg-light-primary {
            background-color: rgba(9, 88, 172, 0.1);
        }
        
        .bg-light-warning {
            background-color: rgba(255, 193, 7, 0.1);
        }
    </style>
</head>

<body>
    <div class="d-flex flex-column flex-shrink-0 p-3 main-content">
        <div class="container-fluid">
            <!-- Breadcrumb -->
            <div class="row mb-3">
                <div class="col-12">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="#" class="text-decoration-none text-muted">Pages</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Energy Consumption</li>
                        </ol>
                    </nav>
                </div>
            </div>

            <!-- Group/Device Selection -->
            <?php
            include (BASE_PATH . "dropdown-selection/group-device-list.php");
            ?>

            <!-- Time Range Form -->
            <div class="row mt-4">
                <div class="col-12">
                    <form id="timeRangeForm" onsubmit="handleSubmit(event)">
                        <div class="row g-4">
                            <!-- From Date/Time -->
                            <div class="col-md-6">
                                <div class="card h-100">
                                    <div class="card-header py-3">
                                        <h5 class="card-title mb-0">From</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <label for="fromDate" class="form-label">Date</label>
                                            <input type="date" class="form-control" id="fromDate" required>
                                        </div>
                                        <div>
                                            <label class="form-label">Time</label>
                                            <div class="row g-2">
                                                <div class="col-6">
                                                    <select id="fromHours" class="form-select">
                                                        <!-- Hours will be filled by JS -->
                                                    </select>
                                                </div>
                                                <div class="col-6">
                                                    <select id="fromMinutes" class="form-select">
                                                        <!-- Minutes will be filled by JS -->
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- To Date/Time -->
                            <div class="col-md-6">
                                <div class="card h-100">
                                    <div class="card-header py-3">
                                        <h5 class="card-title mb-0">To</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <label for="toDate" class="form-label">Date</label>
                                            <input type="date" class="form-control" id="toDate" required>
                                        </div>
                                        <div>
                                            <label class="form-label">Time</label>
                                            <div class="row g-2">
                                                <div class="col-6">
                                                    <select id="toHours" class="form-select">
                                                        <!-- Hours will be filled by JS -->
                                                    </select>
                                                </div>
                                                <div class="col-6">
                                                    <select id="toMinutes" class="form-select">
                                                        <!-- Minutes will be filled by JS -->
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="text-center mt-4">
                            <button type="submit" class="btn btn-primary px-4 py-2">
                                <i class="bi bi-calculator me-2"></i>Calculate Energy Consumption
                            </button>
                        </div>
                    </form>

                    <!-- Results Container -->
                    <div id="results-container" class="result-card mt-5" style="display: none;">
                        <div class="result-header d-flex justify-content-between align-items-center">
                            <h5 class="mb-0 d-flex align-items-center">
                                <i class="bi bi-lightning-charge me-2"></i>
                                Energy Consumption Results
                            </h5>
                            <span id="result-date-range" class="badge bg-light text-dark"></span>
                        </div>
                        <div class="card-body p-4">
                            <!-- Status Messages -->
                            <div id="status" class="mb-4"></div>
                            
                            <!-- Results Content -->
                            <div id="results-content" style="display: none;">
                                <!-- Time Range Information -->
                                <div class="card  border-0 mb-4">
                                    <div class="card-body p-4">
                                        <h6 class="card-title d-flex align-items-center mb-3 pb-2 border-bottom">
                                            <i class="bi bi-clock-history me-2"></i>
                                            Time Range Details
                                        </h6>
                                        <div class="row">
                                            <div class="col-md-6 mb-3 mb-md-0">
                                                <div class="d-flex align-items-center mb-2">
                                                    <i class="bi bi-calendar-range me-2 text-primary"></i>
                                                    <strong>Requested Period:</strong>
                                                </div>
                                                <div id="requested-time-range" class="time-note ms-4"></div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="d-flex align-items-center mb-2">
                                                    <i class="bi bi-calendar-check me-2 text-success"></i>
                                                    <strong>Actual Period Used:</strong>
                                                </div>
                                                <div id="actual-time-range" class="time-note ms-4"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Consumption Values -->
                                <div class="row g-4">
                                    <!-- kWh Card -->
                                    <div class="col-md-6">
                                        <div class="card h-100 border-0 shadow-sm consumption-card">
                                            <div class="card-body text-center p-4">
                                                <div class="icon-container bg-light-warning">
                                                    <i class="bi bi-lightning-charge-fill text-warning fs-3"></i>
                                                </div>
                                                <h6 class="text-muted mb-3">Energy Consumption(kWh)</h6>
                                                <div id="kwh-value" class="consumption-value mb-2">-</div>
                                                
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- kVAh Card -->
                                    <div class="col-md-6">
                                        <div class="card h-100 border-0 shadow-sm consumption-card">
                                            <div class="card-body text-center p-4">
                                                <div class="icon-container bg-light-primary">
                                                    <i class="bi bi-lightning text-primary fs-3"></i>
                                                </div>
                                                <h6 class="text-muted mb-3">Energy Consumption(kVAh)</h6>
                                                <div id="kvah-value" class="consumption-value mb-2">-</div>
                                                
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Optional Additional Metrics -->
                                <div id="additional-metrics" class="mt-4" style="display: none;">
                                    <!-- Space for future expansion -->
                                </div>
                            </div>
                        </div>
                        
                        
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="<?php echo BASE_PATH; ?>assets/js/sidebar-menu.js"></script>
    <script src="<?php echo BASE_PATH; ?>assets/js/project/energy-consumption.js"></script>

    <?php
    include(BASE_PATH . "assets/html/body-end.php");
    include(BASE_PATH . "assets/html/html-end.php");
    ?>
</body>
</html>