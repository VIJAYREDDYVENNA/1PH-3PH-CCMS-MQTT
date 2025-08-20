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
    <title>Alerts</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Custom CSS -->
    <style>
        /* Custom styles for the motor report page */
        :root {
            --header-bg: #4a6da7;
            --power-connected: #198754;
            --power-disconnected: #dc3545;
            --battery-normal: #0d6efd;
            --battery-low: #ffc107;
        }

        /* Table Styles */
        .table-header-row {
            background-color: var(--header-bg) !important;
            color: white;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-size: 0.85rem;
        }

        .table> :not(caption)>*>* {
            padding: 1rem 1.5rem;
        }

        /* Power Status Indicator */
        /* .power-status {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;

        } */

        .status-indicator {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            display: inline-block;
        }

        .status-connected {
            background-color: var(--power-connected);
        }

        .status-disconnected {
            background-color: var(--power-disconnected);
        }

        /* Battery Level Indicator */
        .battery-level {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .battery-icon {
            width: 24px;
            height: 12px;
            border: 2px solid currentColor;
            border-radius: 2px;
            position: relative;
            display: inline-block;
        }

        .battery-icon::after {
            content: '';
            position: absolute;
            left: 0;
            top: 1px;
            height: 6px;
            background-color: currentColor;
            transition: width 0.3s ease;
        }

        /* Card Enhancements */
        .card {
            border: none;
            border-radius: 0.5rem;
        }

        .card-body {
            padding: 1.5rem;
        }

        /* Form Controls */
        .form-control,
        .form-select {
            border-radius: 0.375rem;
            border: 1px solid #dee2e6;
            padding: 0.5rem 1rem;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: var(--header-bg);
            box-shadow: 0 0 0 0.25rem rgba(74, 109, 167, 0.25);
        }

        /* Responsive Adjustments */
        @media (max-width: 768px) {
            .table> :not(caption)>*>* {
                padding: 0.75rem 1rem;
            }

            .card-body {
                padding: 1rem;
            }
        }
    </style>
</head>

<body>
    <?php include(BASE_PATH . "assets/html/start-page.php"); ?>

    <div class="d-flex flex-column flex-shrink-0 p-3 main-content">
        <div class="container-fluid">
            <!-- Breadcrumb -->
            <div class="row d-flex align-items-center mb-1">
                <div class="col-12">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item">
                                <i class="fas fa-home me-1"></i>
                                <a href="#" class="text-decoration-none">Pages</a>
                            </li>
                            <li class="breadcrumb-item active fw-medium">Alerts</li>
                        </ol>
                    </nav>
                </div>
            </div>

            <!-- Motor Selection Card -->
            <div class="row mb-2 justify-content-end">
                <div class="col-md-6">
                    <div class="card " style="background-color: transparent; border: none; box-shadow: none;">
                        <div class="card-body">

                            <label class="form-label text-muted mb-2">Select Motor</label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="fas fa-cog text-primary"></i>
                                </span>
                                <select class="form-select" id="motorSelect">
                                    <option value="motor_1">Motor 1</option>
                                    <option value="motor_2">Motor 2</option>
                                    <option value="motor_3">Motor 3</option>
                                    <option value="motor_4">Motor 4</option>
                                    <option value="motor_5">Motor 5</option>
                                    <option value="motor_6">Motor 6</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filters -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <div class="row align-items-end">
                                <div class="col-md-4">
                                    <label class="form-label text-muted">Date Filter</label>
                                    <input type="date" id="dateFilter" class="form-control" value="<?php echo date('Y-m-d'); ?>">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label text-muted">Alert Type</label>
                                    <select class="form-select" id="alertType">
                                        <option value="ALL">Power On/Off</option>

                                        <!-- <option value="POWER-ON/OFF">Power On/Off</option> -->
                                        <!-- <option value="BATTERY">Battery Level</option> -->
                                        <!-- <option value="ALL">All Alerts</option> -->
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <button type="button" class="btn btn-primary w-100" onclick="refreshData()">
                                        <i class="fas fa-sync-alt me-2"></i>Refresh Data
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Data Table -->
            <div class="row">
                <div class="col-12">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover table-bordered mb-0">
                                    <thead>
                                        <tr class="table-header-row">
                                            <th class="text-center">Power Status</th>
                                            <!-- <th>Battery (mV)</th> -->
                                            <th class="text-center">Date & Time</th>
                                        </tr>
                                    </thead>
                                    <tbody id="motorDataTable">
                                        <tr>
                                            <td colspan="3" class="text-center py-4">
                                                <div class="spinner-border text-primary" role="status">
                                                    <span class="visually-hidden">Loading...</span>
                                                </div>
                                                <p class="mt-2 text-muted">Loading motor data...</p>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="mt-3 text-muted small" id="currentMotorInfo">
                                <!-- Current motor info will be shown here -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include(BASE_PATH . "assets/html/body-end.php"); ?>

    <!-- Scripts -->
    <!-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script> -->
    <script>
        // Utility function to generate random data
        function generateRandomData(motorId, alertType) {
            const data = [];
            const now = new Date();

            // Generate 10 random entries
            for (let i = 0; i < 10; i++) {
                const timestamp = new Date(now.getTime() - i * 3600000); // 1 hour intervals
                const entry = {
                    powerStatus: Math.random() > 0.5 ? 'Power Restore' : 'Power Failure',
                    battery: Math.floor(Math.random() * (4500 - 3500) + 3500), // Random battery level between 3500-4500
                    timestamp: timestamp.toLocaleString('en-US', {
                        year: 'numeric',
                        month: '2-digit',
                        day: '2-digit',
                        hour: '2-digit',
                        minute: '2-digit',
                        second: '2-digit',
                    })
                };

                // Filter based on alert type
                if (alertType === 'ALL') {
                    data.push(entry);
                }
            }

            return data;
        }

        // Function to update the table with new data
        function updateTable(data) {
            const tableBody = document.getElementById('motorDataTable');
            const currentMotorInfo = document.getElementById('currentMotorInfo');
            const selectedMotor = document.getElementById('motorSelect').value;

            // Clear existing rows
            tableBody.innerHTML = '';

            if (data.length === 0) {
                tableBody.innerHTML = `
            <tr>
                <td colspan="3" class="text-center py-4">
                    <i class="fas fa-info-circle text-muted me-2"></i>
                    No data available for the selected filters
                </td>
            </tr>
        `;
                return;
            }

            // Add new rows
            data.forEach(entry => {
                const row = document.createElement('tr');

                // Power Status column
                const powerStatusCell = document.createElement('td');
                powerStatusCell.innerHTML = `
            <div class="power-status text-center">
                <span class="status-indicator ${entry.powerStatus === 'Power Restore' ? 'status-connected' : 'status-disconnected'}"></span>
                ${entry.powerStatus}
            </div>
        `;

                // Battery column
        //         const batteryCell = document.createElement('td');
        //         const batteryPercentage = ((entry.battery - 3500) / (4500 - 3500)) * 100;
        //         batteryCell.innerHTML = `
        //     <div class="battery-level">
        //         <div class="battery-icon" style="color: ${entry.battery < 3800 ? 'var(--battery-low)' : 'var(--battery-normal)'}">
        //             <div style="width: ${batteryPercentage}%"></div>
        //         </div>
        //         ${entry.battery} mV
        //     </div>
        // `;

                // Timestamp column
                const timestampCell = document.createElement('td');
                timestampCell.textContent = entry.timestamp;
                timestampCell.classList.add('text-center');
                // Append cells to row
                row.appendChild(powerStatusCell);
                // row.appendChild(batteryCell);
                row.appendChild(timestampCell);

                // Append row to table body
                console.log(row);
                tableBody.appendChild(row);
            });

            // Update current motor info
            currentMotorInfo.textContent = `Showing data for: ${selectedMotor.replace('_', ' ').toUpperCase()}`;
        }

        // Function to refresh data
        function refreshData() {
            const selectedMotor = document.getElementById('motorSelect').value;
            const selectedAlertType = document.getElementById('alertType').value;
            const dateFilter = document.getElementById('dateFilter').value;

            // Show loading state
            const tableBody = document.getElementById('motorDataTable');
            tableBody.innerHTML = `
        <tr>
            <td colspan="3" class="text-center py-4">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <p class="mt-2 text-muted">Loading motor data...</p>
            </td>
        </tr>
    `;

            // Simulate API call delay
            setTimeout(() => {
                const data = generateRandomData(selectedMotor, selectedAlertType);
                updateTable(data);
            }, 500);
        }

        // Initialize data on page load
        document.addEventListener('DOMContentLoaded', () => {
            refreshData();

            // Add event listeners for controls
            document.getElementById('motorSelect').addEventListener('change', refreshData);
            document.getElementById('dateFilter').addEventListener('change', refreshData);
            document.getElementById('alertType').addEventListener('change', refreshData);
        });
    </script>

    <script src="<?php echo BASE_PATH; ?>assets/js/sidebar-menu.js"></script>
    <?php
    include(BASE_PATH . "assets/html/body-end.php");
    include(BASE_PATH . "assets/html/html-end.php");
    ?>
</body>

</html>