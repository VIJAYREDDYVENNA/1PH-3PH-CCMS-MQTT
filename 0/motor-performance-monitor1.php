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
        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
        }



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
                                            <h2 class="mb-0">1,245,320 L</h2>
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
                                            <h2 class="mb-0">8,742 kWh</h2>
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
                                    <div class="btn-group" role="group">
                                        <button type="button" class="btn btn-sm btn-outline-primary active" data-period="daily">Daily</button>
                                        <button type="button" class="btn btn-sm btn-outline-primary" data-period="weekly">Weekly</button>
                                        <button type="button" class="btn btn-sm btn-outline-primary" data-period="monthly">Monthly</button>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <!-- <div class="comparison-info">
                                            <h6><i class="fas fa-info-circle me-2"></i>What's being compared?</h6>
                                            <ul>
                                                <li><strong>Auto Mode (Blue):</strong> Water flow rates when the system operates in automatic mode</li>
                                                <li><strong>OEM Mode (Yellow):</strong> Water flow rates when the system operates in manufacturer's mode</li>
                                            </ul>
                                        </div> -->
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
                        <div class="col-lg-12">
                            <div class="card border-0 shadow-sm">
                                <div class="card-header">
                                    <h5 class="mb-0">MOTOR Runtime Comparison</h5>
                                </div>
                                <div class="card-body">
                                    <div class="comparison-info">
                                        <h6><i class="fas fa-info-circle me-2"></i>What's being compared?</h6>
                                        <ul>
                                            <li><strong>Runtime Hours:</strong> Total operational hours of each motor</li>
                                        </ul>
                                    </div>
                                    <div class="chart-container">
                                        <canvas id="motorRuntimeChart"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6 mt-2 mt-md-0">
                            <div class="card border-0 shadow-sm">
                                <div class="card-header">
                                    <h5 class="mb-0">System Reliability Trends</h5>
                                </div>
                                <div class="card-body">
                                    <div class="comparison-info">
                                        <h6><i class="fas fa-info-circle me-2"></i>What's being shown?</h6>
                                        <ul>
                                            <li><strong>Reliability (%):</strong> System uptime and consistent performance over time</li>
                                        </ul>
                                    </div>
                                    <div class="chart-container">
                                        <canvas id="reliabilityChart"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Mode Comparison -->
                    <div class="row mb-4">
                            <div class="col-12">
                                <div class="card border-0 shadow-sm">
                                    <div class="card-header">
                                        <h5 class="mb-0">Auto Mode vs OEM Mode Comparison</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="comparison-info">
                                            <h6><i class="fas fa-info-circle me-2"></i>What's being compared?</h6>
                                            <ul>
                                                <li><strong>Water Flow Rate:</strong> Volume of water delivered per minute</li>
                                                <li><strong>Energy Efficiency:</strong> Power consumption optimization</li>
                                                <li><strong>Operational Cost:</strong> Running expenses including power and maintenance</li>
                                                <li><strong>Maintenance Needs:</strong> Frequency and complexity of required maintenance</li>
                                                <li><strong>Response Time:</strong> How quickly the system reacts to demand changes</li>
                                                <li><strong>Reliability:</strong> System uptime and consistent performance</li>
                                            </ul>
                                        </div>
                                        <div class="row">
                                            <div class="col-lg-8">
                                                <div class="chart-container">
                                                    <canvas id="modeComparisonChart"></canvas>
                                                </div>
                                            </div>
                                            <div class="col-lg-4">
                                                <div class="mode-stats">
                                                    <div class="mode-stat-item">
                                                        <div class="d-flex justify-content-between mb-2">
                                                            <span class="fw-bold">Water Flow Efficiency</span>
                                                            <span class="badge bg-primary">Auto Mode</span>
                                                        </div>
                                                        <div class="progress mb-3" style="height: 10px;">
                                                            <div class="progress-bar bg-primary" role="progressbar" style="width: 85%"></div>
                                                        </div>
                                                        <div class="d-flex justify-content-between mb-2">
                                                            <span class="fw-bold">Water Flow Efficiency</span>
                                                            <span class="badge bg-warning text-dark">OEM Mode</span>
                                                        </div>
                                                        <div class="progress mb-4" style="height: 10px;">
                                                            <div class="progress-bar bg-warning" role="progressbar" style="width: 92%"></div>
                                                        </div>
                                                    </div>

                                                    <div class="mode-stat-item">
                                                        <div class="d-flex justify-content-between mb-2">
                                                            <span class="fw-bold">Energy Efficiency</span>
                                                            <span class="badge bg-primary">Auto Mode</span>
                                                        </div>
                                                        <div class="progress mb-3" style="height: 10px;">
                                                            <div class="progress-bar bg-primary" role="progressbar" style="width: 78%"></div>
                                                        </div>
                                                        <div class="d-flex justify-content-between mb-2">
                                                            <span class="fw-bold">Energy Efficiency</span>
                                                            <span class="badge bg-warning text-dark">OEM Mode</span>
                                                        </div>
                                                        <div class="progress mb-4" style="height: 10px;">
                                                            <div class="progress-bar bg-warning" role="progressbar" style="width: 65%"></div>
                                                        </div>
                                                    </div>

                                                    <div class="mode-recommendation p-3  rounded">
                                                        <h6 class="mb-2"><i class="fas fa-lightbulb text-warning me-2"></i>Recommendation</h6>
                                                        <p class="mb-0 small">Based on historical data, Auto Mode is 15% more energy efficient, while OEM Mode delivers 8% higher water flow rates. For optimal efficiency, use Auto Mode during off-peak hours and OEM Mode during peak demand periods.</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    <!-- Maintenance Schedule -->
                    <div class="row mb-4">
                            <div class="col-12">
                                <div class="card border-0 shadow-sm">
                                    <div class="card-header">
                                        <h5 class="mb-0">Upcoming Maintenance Schedule</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="table table-hover">
                                                <thead>
                                                    <tr>
                                                        <th>Motor ID</th>
                                                        <th>Maintenance Type</th>
                                                        <th>Scheduled Date</th>
                                                        <th>Estimated Duration</th>
                                                        <th>Priority</th>
                                                        <th>Status</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td><span class="fw-medium">MOTOR 5</span></td>
                                                        <td>Filter Replacement</td>
                                                        <td>May 15, 2025</td>
                                                        <td>2 hours</td>
                                                        <td><span class="badge bg-danger">High</span></td>
                                                        <td><span class="badge bg-warning text-dark">Pending</span></td>
                                                    </tr>
                                                    <tr>
                                                        <td><span class="fw-medium">MOTOR 3</span></td>
                                                        <td>Bearing Inspection</td>
                                                        <td>May 22, 2025</td>
                                                        <td>3 hours</td>
                                                        <td><span class="badge bg-warning text-dark">Medium</span></td>
                                                        <td><span class="badge bg-info">Scheduled</span></td>
                                                    </tr>
                                                    <tr>
                                                        <td><span class="fw-medium">MOTOR 6</span></td>
                                                        <td>Motor Efficiency Check</td>
                                                        <td>May 28, 2025</td>
                                                        <td>4 hours</td>
                                                        <td><span class="badge bg-warning text-dark">Medium</span></td>
                                                        <td><span class="badge bg-info">Scheduled</span></td>
                                                    </tr>
                                                    <tr>
                                                        <td><span class="fw-medium">MOTOR 1</span></td>
                                                        <td>Routine Inspection</td>
                                                        <td>June 10, 2025</td>
                                                        <td>2 hours</td>
                                                        <td><span class="badge bg-success">Low</span></td>
                                                        <td><span class="badge bg-secondary">Planned</span></td>
                                                    </tr>
                                                </tbody>
                                            </table>
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
                                    <div class="dropdown">
                                            <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" id="pumpMetricsDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                                All Motors
                                            </button>
                                            <ul class="dropdown-menu" aria-labelledby="pumpMetricsDropdown">
                                                <li><a class="dropdown-item active" href="#">All Motors</a></li>
                                                <li><a class="dropdown-item" href="#">MOTOR 1</a></li>
                                                <li><a class="dropdown-item" href="#">MOTOR 2</a></li>
                                                <li><a class="dropdown-item" href="#">MOTOR 3</a></li>
                                                <li><a class="dropdown-item" href="#">MOTOR 4</a></li>
                                                <li><a class="dropdown-item" href="#">MOTOR 5</a></li>
                                                <li><a class="dropdown-item" href="#">MOTOR 6</a></li>
                                            </ul>
                                        </div>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-hover">
                                            <thead>
                                                <tr>
                                                    <th class="text-center">Motor ID</th>
                                                    <th class="text-center">Total Runtime (hrs)</th>
                                                    <th class="text-center">Water Delivered (L)</th>
                                                    <th class="text-center">Energy Used (kWh)</th>
                                                    <th class="text-center">Average Speed</th>
                                                    <!-- <th>Maintenance Status</th>
                                                        <th>Reliability Score</th> -->
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td><span class="fw-medium">MOTOR 1</span></td>
                                                    <td>342.5</td>
                                                    <td>324,560</td>
                                                    <td>1,845</td>
                                                    <td>175.9</td>
                                                    <td><span class="badge bg-success">Optimal</span></td>
                                                        <td>
                                                            <div class="d-flex align-items-center">
                                                                <div class="progress flex-grow-1 me-2" style="height: 6px;">
                                                                    <div class="progress-bar bg-success" role="progressbar" style="width: 95%"></div>
                                                                </div>
                                                                <span>95%</span>
                                                            </div>
                                                        </td>
                                                </tr>
                                                <tr>
                                                    <td><span class="fw-medium">MOTOR 2</span></td>
                                                    <td>298.2</td>
                                                    <td>287,420</td>
                                                    <td>1,654</td>
                                                    <td>173.8</td>
                                                    <td><span class="badge bg-success">Optimal</span></td>
                                                        <td>
                                                            <div class="d-flex align-items-center">
                                                                <div class="progress flex-grow-1 me-2" style="height: 6px;">
                                                                    <div class="progress-bar bg-success" role="progressbar" style="width: 92%"></div>
                                                                </div>
                                                                <span>92%</span>
                                                            </div>
                                                        </td>
                                                </tr>
                                                <tr>
                                                    <td><span class="fw-medium">MOTOR 3</span></td>
                                                    <td>312.7</td>
                                                    <td>302,180</td>
                                                    <td>1,725</td>
                                                    <td>175.2</td>
                                                    <td><span class="badge bg-warning text-dark">Maintenance Due</span></td>
                                                        <td>
                                                            <div class="d-flex align-items-center">
                                                                <div class="progress flex-grow-1 me-2" style="height: 6px;">
                                                                    <div class="progress-bar bg-warning" role="progressbar" style="width: 78%"></div>
                                                                </div>
                                                                <span>78%</span>
                                                            </div>
                                                        </td>
                                                </tr>
                                                <tr>
                                                    <td><span class="fw-medium">MOTOR 4</span></td>
                                                    <td>156.3</td>
                                                    <td>124,780</td>
                                                    <td>985</td>
                                                    <td>126.7</td>
                                                    <td><span class="badge bg-success">Optimal</span></td>
                                                    <td>
                                                            <div class="d-flex align-items-center">
                                                                <div class="progress flex-grow-1 me-2" style="height: 6px;">
                                                                    <div class="progress-bar bg-success" role="progressbar" style="width: 88%"></div>
                                                                </div>
                                                                <span>88%</span>
                                                            </div>
                                                        </td>
                                                </tr>
                                                <tr>
                                                    <td><span class="fw-medium">MOTOR 5</span></td>
                                                    <td>142.8</td>
                                                    <td>118,540</td>
                                                    <td>876</td>
                                                    <td>135.3</td>
                                                    <td><span class="badge bg-danger">Maintenance Required</span></td>
                                                        <td>
                                                            <div class="d-flex align-items-center">
                                                                <div class="progress flex-grow-1 me-2" style="height: 6px;">
                                                                    <div class="progress-bar bg-danger" role="progressbar" style="width: 65%"></div>
                                                                </div>
                                                                <span>65%</span>
                                                            </div>
                                                        </td>
                                                </tr>
                                                <tr>
                                                    <td><span class="fw-medium">MOTOR 6</span></td>
                                                    <td>187.5</td>
                                                    <td>87,840</td>
                                                    <td>1,657</td>
                                                    <td>53.0</td>
                                                    <td><span class="badge bg-warning text-dark">Maintenance Due</span></td>
                                                        <td>
                                                            <div class="d-flex align-items-center">
                                                                <div class="progress flex-grow-1 me-2" style="height: 6px;">
                                                                    <div class="progress-bar bg-warning" role="progressbar" style="width: 72%"></div>
                                                                </div>
                                                                <span>72%</span>
                                                            </div>
                                                        </td>
                                                </tr>
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
            <!-- Moment.js -->
            <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
            <!-- DateRangePicker -->
            <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
            <!-- Chart.js -->
            <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
            <!-- Bootstrap 5 JS Bundle with Popper -->
            <!-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script> -->
            <!-- Custom JS -->
            <script>
                // Global chart instances
                let waterFlowTrendsChart, platformDistributionChart, motorRuntimeChart,
                    modeComparisonChart, reliabilityChart;

                // Chart data cache
                const chartData = {
                    waterFlowTrends: {},
                    platformDistribution: null,
                    motorRuntime: null,
                    modeComparison: null,
                    reliability: null
                };

                // Flag to prevent continuous updates
                let isUpdating = false;

                document.addEventListener('DOMContentLoaded', function() {
                    // Initialize date range picker
                    initializeDateRangePicker();

                    // Fetch initial data
                    fetchChartData();

                    // Setup event listeners
                    setupEventListeners();
                });

                // Initialize date range picker
                function initializeDateRangePicker() {
                    // Ensure $ and moment are available
                    if (typeof $ === 'undefined' || typeof moment === 'undefined') {
                        console.error('jQuery and/or Moment.js are not loaded. Date range picker will not function.');
                        return;
                    }

                    $('#dateRangePicker').daterangepicker({
                        startDate: moment().subtract(29, 'days'),
                        endDate: moment(),
                        ranges: {
                            'Today': [moment(), moment()],
                            'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                            'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                            'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                            'This Month': [moment().startOf('month'), moment().endOf('month')],
                            'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
                        }
                    });
                }

                // Setup event listeners
                function setupEventListeners() {
                    // Update report button
                    document.getElementById('updateReportBtn').addEventListener('click', function() {
                        if (isUpdating) return;

                        // Simulate loading data
                        showLoadingState();
                        isUpdating = true;

                        setTimeout(() => {
                            // Fetch and update charts with new data
                            fetchChartData();
                            hideLoadingState();
                            isUpdating = false;
                        }, 1000);
                    });

                    // Export report button
                    document.getElementById('exportReportBtn').addEventListener('click', function() {
                        alert('Report export functionality would be implemented here.');
                    });

                    // Period buttons for water flow trends
                    document.querySelectorAll('[data-period]').forEach(button => {
                        button.addEventListener('click', function() {
                            if (isUpdating) return;

                            // Remove active class from all buttons
                            document.querySelectorAll('[data-period]').forEach(btn => {
                                btn.classList.remove('active');
                            });

                            // Add active class to clicked button
                            this.classList.add('active');

                            // Update chart based on selected period
                            updateWaterFlowTrendsChart(this.dataset.period);
                        });
                    });
                }

                // Fetch all chart data
                function fetchChartData() {
                    // In a real application, this would make API calls to fetch data
                    // For this demo, we'll generate random data

                    // Generate data for all periods at once
                    chartData.waterFlowTrends = {
                        daily: {
                            labels: Array.from({
                                length: 30
                            }, (_, i) => moment().subtract(29 - i, 'days').format('MMM D')),
                            autoMode: generateRandomData(30, 40, 60),
                            oemMode: generateRandomData(30, 50, 70)
                        },
                        weekly: {
                            labels: Array.from({
                                length: 12
                            }, (_, i) => `Week ${i + 1}`),
                            autoMode: generateRandomData(12, 45, 65),
                            oemMode: generateRandomData(12, 55, 75)
                        },
                        monthly: {
                            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                            autoMode: generateRandomData(12, 50, 70),
                            oemMode: generateRandomData(12, 60, 80)
                        }
                    };

                    chartData.platformDistribution = {
                        labels: ['MOTOR 1', 'MOTOR 2', 'MOTOR 3', 'MOTOR 4', 'MOTOR 5', 'MOTOR 6'],
                        data: [25, 20, 18, 12, 10, 15]
                    };

                    chartData.motorRuntime = {
                        labels: ['MOTOR 1', 'MOTOR 2', 'MOTOR 3', 'MOTOR 4', 'MOTOR 5', 'MOTOR 6'],
                        runtime: [342.5, 298.2, 312.7, 156.3, 142.8, 187.5]
                    };

                    chartData.modeComparison = {
                        labels: [
                            'Water Flow Rate',
                            'Energy Efficiency',
                            'Operational Cost',
                            'Maintenance Needs',
                            'Response Time',
                            'Reliability'
                        ],
                        autoMode: [70, 85, 75, 90, 65, 80],
                        oemMode: [90, 65, 60, 70, 95, 75]
                    };

                    chartData.reliability = {
                        labels: Array.from({
                            length: 12
                        }, (_, i) => moment().subtract(11 - i, 'months').format('MMM')),
                        reliability: generateRandomData(12, 85, 98)
                    };

                    // Initialize or update charts
                    initializeCharts();
                }

                // Initialize all charts
                function initializeCharts() {
                    initializeWaterFlowTrendsChart();
                    initializePlatformDistributionChart();
                    initializeMotorRuntimeChart();
                    // initializeModeComparisonChart();
                    initializeReliabilityChart();
                }

                // Show loading state
                function showLoadingState() {
                    // Add loading overlay or spinner
                    const loadingOverlay = document.createElement('div');
                    loadingOverlay.id = 'loadingOverlay';
                    loadingOverlay.className = 'position-fixed top-0 start-0 w-100 h-100 d-flex justify-content-center align-items-center bg-white bg-opacity-75';
                    loadingOverlay.style.zIndex = '9999';
                    loadingOverlay.innerHTML = `
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                        `;
                    document.body.appendChild(loadingOverlay);
                }

                // Hide loading state
                function hideLoadingState() {
                    const loadingOverlay = document.getElementById('loadingOverlay');
                    if (loadingOverlay) {
                        loadingOverlay.remove();
                    }
                }

                // Initialize Water Flow Trends Chart
                function initializeWaterFlowTrendsChart() {
                    const ctx = document.getElementById('waterFlowTrendsChart').getContext('2d');
                    const data = chartData.waterFlowTrends.daily;

                    // Destroy existing chart if it exists
                    if (waterFlowTrendsChart) {
                        waterFlowTrendsChart.destroy();
                    }

                    waterFlowTrendsChart = new Chart(ctx, {
                        type: 'line',
                        data: {
                            labels: data.labels,
                            datasets: [{
                                    label: 'Average Water Flow',
                                    data: data.autoMode,
                                    borderColor: '#0d6efd',
                                    backgroundColor: 'rgba(13, 110, 253, 0.1)',
                                    borderWidth: 2,
                                    fill: true,
                                    tension: 0.4
                                },
                                {
                                    label: 'OEM Mode',
                                    data: data.oemMode,
                                    borderColor: '#ffc107',
                                    backgroundColor: 'rgba(255, 193, 7, 0.1)',
                                    borderWidth: 2,
                                    fill: true,
                                    tension: 0.4
                                }
                            ]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    title: {
                                        display: true,
                                        text: 'Flow Rate (LPM)'
                                    }
                                }
                            },
                            plugins: {
                                tooltip: {
                                    mode: 'index',
                                    intersect: false
                                },
                                legend: {
                                    position: 'top'
                                }
                            },
                            animation: {
                                duration: 1000 // Controlled animation duration
                            }
                        }
                    });
                }

                // Update Water Flow Trends Chart based on period
                function updateWaterFlowTrendsChart(period) {
                    if (!waterFlowTrendsChart || !chartData.waterFlowTrends[period]) return;

                    const data = chartData.waterFlowTrends[period];

                    waterFlowTrendsChart.data.labels = data.labels;
                    waterFlowTrendsChart.data.datasets[0].data = data.autoMode;
                    // waterFlowTrendsChart.data.datasets[1].data = data.oemMode;
                    waterFlowTrendsChart.update();
                }

                // Initialize Platform Distribution Chart
                function initializePlatformDistributionChart() {
                    const ctx = document.getElementById('platformDistributionChart').getContext('2d');
                    const data = chartData.platformDistribution;

                    // Destroy existing chart if it exists
                    if (platformDistributionChart) {
                        platformDistributionChart.destroy();
                    }

                    platformDistributionChart = new Chart(ctx, {
                        type: 'doughnut',
                        data: {
                            labels: data.labels,
                            datasets: [{
                                data: data.data,
                                backgroundColor: [
                                    '#0d6efd',
                                    '#6610f2',
                                    '#6f42c1',
                                    '#d63384',
                                    '#fd7e14',
                                    '#20c997'
                                ],
                                borderWidth: 1
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    position: 'right'
                                },
                                tooltip: {
                                    callbacks: {
                                        label: function(context) {
                                            const label = context.label || '';
                                            const value = context.raw || 0;
                                            return `${label}: ${value}% of total water distributed`;
                                        }
                                    }
                                }
                            },
                            animation: {
                                duration: 1000 // Controlled animation duration
                            }
                        }
                    });
                }

                // Initialize Motor Runtime Chart - Only showing runtime hours
                function initializeMotorRuntimeChart() {
                    const ctx = document.getElementById('motorRuntimeChart').getContext('2d');
                    const data = chartData.motorRuntime;

                    // Destroy existing chart if it exists
                    if (motorRuntimeChart) {
                        motorRuntimeChart.destroy();
                    }

                    motorRuntimeChart = new Chart(ctx, {
                        type: 'bar',
                        data: {
                            labels: data.labels,
                            datasets: [{
                                label: 'Runtime Hours',
                                data: data.runtime,
                                backgroundColor: '#0d6efd',
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    title: {
                                        display: true,
                                        text: 'Runtime Hours'
                                    }
                                }
                            },
                            plugins: {
                                tooltip: {
                                    callbacks: {
                                        label: function(context) {
                                            return `Runtime: ${context.raw} hours`;
                                        }
                                    }
                                },
                                legend: {
                                    display: true,
                                    position: 'top'
                                }
                            },
                            animation: {
                                duration: 1000 // Controlled animation duration
                            }
                        }
                    });
                }

                // Initialize Mode Comparison Chart
                function initializeModeComparisonChart() {
                    const ctx = document.getElementById('modeComparisonChart').getContext('2d');
                    const data = chartData.modeComparison;

                    // Destroy existing chart if it exists
                    if (modeComparisonChart) {
                        modeComparisonChart.destroy();
                    }

                    modeComparisonChart = new Chart(ctx, {
                        type: 'radar',
                        data: {
                            labels: data.labels,
                            datasets: [{
                                    label: 'Auto Mode',
                                    data: data.autoMode,
                                    backgroundColor: 'rgba(13, 110, 253, 0.2)',
                                    borderColor: '#0d6efd',
                                    pointBackgroundColor: '#0d6efd',
                                    pointBorderColor: '#fff',
                                    pointHoverBackgroundColor: '#fff',
                                    pointHoverBorderColor: '#0d6efd'
                                },
                                {
                                    label: 'OEM Mode',
                                    data: data.oemMode,
                                    backgroundColor: 'rgba(255, 193, 7, 0.2)',
                                    borderColor: '#ffc107',
                                    pointBackgroundColor: '#ffc107',
                                    pointBorderColor: '#fff',
                                    pointHoverBackgroundColor: '#fff',
                                    pointHoverBorderColor: '#ffc107'
                                }
                            ]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            scales: {
                                r: {
                                    angleLines: {
                                        display: true
                                    },
                                    suggestedMin: 0,
                                    suggestedMax: 100
                                }
                            },
                            animation: {
                                duration: 1000 // Controlled animation duration
                            }
                        }
                    });
                }

                // Initialize Reliability Chart - Only showing reliability, no maintenance
                function initializeReliabilityChart() {
                    const ctx = document.getElementById('reliabilityChart').getContext('2d');
                    const data = chartData.reliability;

                    // Destroy existing chart if it exists
                    if (reliabilityChart) {
                        reliabilityChart.destroy();
                    }

                    reliabilityChart = new Chart(ctx, {
                        type: 'line',
                        data: {
                            labels: data.labels,
                            datasets: [{
                                label: 'System Reliability (%)',
                                data: data.reliability,
                                borderColor: '#0d6efd',
                                backgroundColor: 'rgba(13, 110, 253, 0.1)',
                                borderWidth: 2,
                                fill: true,
                                tension: 0.4
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            scales: {
                                y: {
                                    beginAtZero: false,
                                    min: 80,
                                    max: 100,
                                    title: {
                                        display: true,
                                        text: 'Reliability (%)'
                                    }
                                }
                            },
                            plugins: {
                                tooltip: {
                                    callbacks: {
                                        label: function(context) {
                                            return `Reliability: ${context.raw}%`;
                                        }
                                    }
                                }
                            },
                            animation: {
                                duration: 1000 // Controlled animation duration
                            }
                        }
                    });
                }

                // Helper function to generate random data
                function generateRandomData(length, min, max) {
                    return Array.from({
                        length
                    }, () => Math.floor(Math.random() * (max - min + 1)) + min);
                }
            </script>
        </div>
    </div>
    </div>

    <!-- Modals links -->
    </main>
    <script src="<?php echo BASE_PATH; ?>assets/js/sidebar-menu.js"></script>
    <?php
    include(BASE_PATH . "assets/html/body-end.php");
    include(BASE_PATH . "assets/html/html-end.php");
    ?>