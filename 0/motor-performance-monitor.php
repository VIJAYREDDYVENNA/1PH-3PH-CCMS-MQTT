<?php
require_once 'config-path.php';
require_once '../session/session-manager.php';
SessionManager::checkSession();
?>
<!DOCTYPE html>
<html lang="en" data-bs-theme="auto">

<head>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- DateRangePicker -->
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
    <title>Motor Performance Monitor</title>
    <style>
        /* General Styles */
        /* body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
        } */



        td {
            text-align: center;
        }

        .icon-bg {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 20px;
        }

        /* Date Range Picker */
        .date-range-container {
            max-width: 400px;
        }

        /* Card Styling */
        .card {
            /* transition: transform 0.2s, box-shadow 0.2s; */
        }

        .card:hover {
            /* transform: translateY(-3px); */
            /* box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.1) !important; */
        }

        /* Chart Container */
        .chart-container {
            position: relative;
            height: 300px;
            width: 100%;
        }

        /* Maintenance Timeline */
        .maintenance-timeline {
            position: relative;
            padding-left: 20px;
        }

        .maintenance-timeline::before {
            content: '';
            position: absolute;
            top: 0;
            bottom: 0;
            left: 9px;
            width: 2px;
            background-color: #e9ecef;
        }

        .timeline-item {
            position: relative;
            padding-bottom: 20px;
        }

        .timeline-marker {
            position: absolute;
            left: -20px;
            width: 18px;
            height: 18px;
            border-radius: 50%;
            border: 2px solid #fff;
            box-shadow: 0 0 0 2px rgba(0, 0, 0, 0.1);
        }

        .timeline-content {
            padding-left: 15px;
        }

        /* Conservation Stats */
        .conservation-stat {
            border: 1px solid #e9ecef;
            transition: all 0.3s ease;
        }

        .conservation-stat:hover {
            background-color: #f1f8ff !important;
            border-color: #cfe2ff;
        }

        .conservation-icon {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            background-color: rgba(13, 110, 253, 0.1);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
        }

        .conservation-icon .fa-tint-slash {
            color: #0d6efd;
        }

        .conservation-icon .fa-rupee-sign {
            color: #198754;
        }

        .conservation-icon .fa-leaf {
            color: #0dcaf0;
        }

        /* Mode Comparison */
        .mode-stat-item {
            margin-bottom: 20px;
        }

        .mode-recommendation {
            border-left: 4px solid #ffc107;
        }

        /* Comparison Info */
        .comparison-info {
            /* background-color: #f8f9fa; */
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 20px;
        }

        .comparison-info h6 {
            color: #0d6efd;
            margin-bottom: 10px;
        }

        .comparison-info ul {
            padding-left: 20px;
            margin-bottom: 0;
        }

        .comparison-info li {
            margin-bottom: 5px;
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .conservation-stat {
                margin-bottom: 15px;
            }
        }
    </style>
    <?php
    include(BASE_PATH . "assets/html/start-page.php");
    ?>
    <div class="d-flex flex-column flex-shrink-0 p-3 main-content ">
        <div class="container-fluid">
            <div class="row d-flex align-items-center">
                <div class="col-12">
                    <p class="breadcrumb-text m-0">
                        <i class="bi bi-cpu"></i> Pages / <span class="fw-medium text-muted">Motor Performance Monitor</span>
                    </p>
                </div>
            </div>

            <div class="row">
                <div class="container-fluid">
                    <!-- Header -->
                    <header class="bg-primary text-white py-3 mt-3 mb-4 rounded">
                        <div class="container-fluid">
                            <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mt-2 gap-2">
                                <div>
                                    <p class="mb-0">Performance Reports & Analytics</p>
                                </div>
                                <div class="d-flex flex-column flex-sm-row align-items-stretch align-items-sm-center gap-2">
                                    <a href="index.php" class="btn btn-outline-light">
                                        <i class="fas fa-tachometer-alt me-1"></i> Dashboard
                                    </a>
                                    <button class="btn btn-light" id="exportReportBtn">
                                        <i class="fas fa-file-export me-1"></i> Export Report
                                    </button>
                                </div>
                            </div>
                        </div>
                    </header>



                    <!-- Date Range Selector -->
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-md-6">
                                    <h5 class="mb-0">Historical Data Analysis</h5>
                                    <p class="text-muted mb-md-0">Select date range to analyze system performance</p>
                                </div>
                                <div class="col-md-6">
                                    <div class="d-flex justify-content-md-end">
                                        <div class="input-group date-range-container">
                                            <input type="text" id="dateRangePicker" class="form-control" value="Last 30 Days">
                                            <button class="btn btn-primary" type="button" id="updateReportBtn">
                                                <i class="fas fa-sync-alt me-1"></i> Update
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Key Performance Metrics -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="card border-0 shadow-sm h-100">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <h6 class="text-muted mb-1">Total Water Delivered</h6>
                                            <h2 class="mb-0" id="total_water">0 L</h2>
                                        </div>
                                        <div class="icon-bg bg-primary">
                                            <i class="fas fa-water"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mt-2 mt-md-0">
                            <div class="card border-0 shadow-sm h-100">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <h6 class="text-muted mb-1">Energy Consumption</h6>
                                            <h2 class="mb-0" id="total_energy">8,742 kWh</h2>
                                        </div>
                                        <div class="icon-bg bg-success">
                                            <i class="fas fa-bolt"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Charts Row 1 -->
                    <div class="row mb-4">
                        <div class="col-lg-8 h-100">
                            <div class="card border-0 shadow-sm">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <h5 class="mb-0">Water Flow Trends</h5>
                                    <!-- <div class="btn-group" role="group">
                                        <button type="button" class="btn btn-sm btn-outline-primary active" data-period="daily">Daily</button>
                                        <button type="button" class="btn btn-sm btn-outline-primary" data-period="weekly">Weekly</button>
                                        <button type="button" class="btn btn-sm btn-outline-primary" data-period="monthly">Monthly</button>
                                    </div> -->
                                </div>
                                <div class="card-body">

                                    <div class="chart-container">
                                        <canvas id="waterFlowTrendsChart"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 h-100 mt-2 mt-md-0">
                            <div class="card border-0 shadow-sm">
                                <div class="card-header">
                                    <h5 class="mb-0">Water Distribution by Motors</h5>
                                </div>
                                <div class="card-body">
                                    <div class="chart-container">
                                        <canvas id="platformDistributionChart"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Charts Row 2 -->
                    <div class="row mb-4">
                        <div class="col-12 col-lg-6">
                            <div class="card border-0 shadow-sm">
                                <div class="card-header">
                                    <h5 class="mb-0">MOTOR Runtime Comparison</h5>
                                </div>
                                <div class="card-body">
                                    <div class="comparison-info">
                                        <h6><i class="fas fa-info-circle me-2"></i>What's being compared?</h6>
                                        <ul>
                                            <li><strong>Runtime Hours:</strong> Total operational Hours of each Motor in the selected Range</li>
                                        </ul>
                                    </div>
                                    <div class="chart-container">
                                        <canvas id="motorRuntimeChart"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-lg-6">
                            <div class="card border-0 shadow-sm">
                                <div class="card-header">
                                    <h5 class="mb-0">MOTOR energy Comparison</h5>
                                </div>
                                <div class="card-body">
                                    <div class="comparison-info">
                                        <h6><i class="fas fa-info-circle me-2"></i>What's being compared?</h6>
                                        <ul>
                                            <li><strong>Energy Consumption:</strong> Total Energy consumption of each Motor in the selected Range</li>
                                        </ul>
                                    </div>
                                    <div class="chart-container">
                                        <canvas id="motorEnergyChart"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>


                    <!-- Detailed Metrics -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="card border-0 shadow-sm">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <h5 class="mb-0">MOTOR Performance Metrics</h5>

                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-hover  ">
                                            <thead>
                                                <tr>
                                                    <th class="text-center">Motor ID</th>
                                                    <th class="text-center">Total Runtime (hrs)</th>
                                                    <th class="text-center">Water Delivered (L)</th>
                                                    <th class="text-center">Energy Used (kWh)</th>
                                                    <!-- <th class="text-center">Status</th> -->

                                                    <!-- <th class="text-center">Average Speed</th> -->
                                                    <!-- <th>Maintenance Status</th>
                                                        <th>Reliability Score</th> -->
                                                </tr>
                                            </thead>
                                            <tbody id="performanceMetricsTable" class="tbody">

                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- jQuery -->
            <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
            <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
            <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
            <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>


        </div>
    </div>
    </div>

    <!-- Modals links -->
    </main>
    <script src="<?php echo BASE_PATH; ?>assets/js/project/motor_performance1.js"></script>

    <script src="<?php echo BASE_PATH; ?>assets/js/sidebar-menu.js"></script>
    <?php
    include(BASE_PATH . "assets/html/body-end.php");
    include(BASE_PATH . "assets/html/html-end.php");
    ?>