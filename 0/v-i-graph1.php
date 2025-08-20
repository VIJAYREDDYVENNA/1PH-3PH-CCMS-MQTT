<?php
require_once 'config-path.php';
require_once '../session/session-manager.php';
SessionManager::checkSession();

$sessionVars = SessionManager::SessionVariables();
$devices = isset($_SESSION["DEVICES_LIST"]) ? json_decode($_SESSION["DEVICES_LIST"], true) : [];
?>
<!DOCTYPE html>
<html lang="en" data-bs-theme="auto">

<head>
  <title>Motor Parameter Comparison</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.4.0/Chart.min.js"></script>
  <script src="https://www.amcharts.com/lib/3/amcharts.js"></script>
  <script src="https://www.amcharts.com/lib/3/serial.js"></script>
  <script src="https://www.amcharts.com/lib/3/themes/light.js"></script>
  <?php
  include(BASE_PATH . "assets/html/start-page.php");
  ?>
  <div class="d-flex flex-column flex-shrink-0 p-3 main-content">
    <div class="container-fluid">
      <!-- Page Header -->
      <div class="row d-flex align-items-center mb-3">
        <div class="col-12 p-0">
          <nav aria-label="breadcrumb">
            <ol class="breadcrumb m-0">
              <li class="breadcrumb-item"><a href="#" class="text-decoration-none text-body-tertiary">Pages</a></li>
              <li class="breadcrumb-item active" aria-current="page">Motor Comparison</li>
            </ol>
          </nav>
        </div>
      </div>

      <!-- Motor Comparison Controls -->
      <div class="card shadow-sm mb-4">
        <div class="card-header bg-white">
          <h5 class="card-title mb-0">Parameter Comparison Configuration</h5>
        </div>
        <div class="card-body">
          <div class="row">
            <!-- Parameter Selection -->
            <div class="col-md-6 col-lg-3 mb-3">
              <label class="form-label text-muted">Select Parameter</label>
              <div class="input-group">
                <span class="input-group-text">
                  <i class="fas fa-chart-line text-primary"></i>
                </span>
                <select class="form-select" id="graph-parameter">
                  <option value="LINE_VOLTAGE">Line Voltage</option>
                  <option value="MOTOR_VOLTAGE">Motor Voltage</option>
                  <option value="MOTOR_CURRENT">Motor Current</option>
                  <option value="ENERGY">Energy (kWh)</option>
                  <option value="REF_FREQUENCY">Reference Frequency</option>
                  <option value="FREQUENCY">Frequency</option>
                  <option value="SPEED">Speed</option>
                  <option value="RUNNING_HOURS">Running Hours</option>
                </select>
              </div>
            </div>

            <!-- Line Voltage Sub-Parameter (conditionally shown) -->
            <div class="col-md-6 col-lg-3 mb-3" id="line-voltage-options" style="display:none;">
              <label class="form-label text-muted">Voltage Type</label>
              <div class="input-group">
                <span class="input-group-text">
                  <i class="fas fa-bolt text-primary"></i>
                </span>
                <select class="form-select" id="voltage-type">
                  <option value="ALL">All Voltages</option>
                  <option value="R_Y">R-Y Voltage</option>
                  <option value="Y_B">Y-B Voltage</option>
                  <option value="B_R">B-R Voltage</option>
                </select>
              </div>
            </div>

            <!-- Time Range Selection -->
            <div class="col-md-6 col-lg-3 mb-3">
              <label class="form-label text-muted">Time Range</label>
              <div class="input-group">
                <span class="input-group-text">
                  <i class="fas fa-calendar-alt text-primary"></i>
                </span>
                <select class="form-select" id="graph-selection">
                  <option value="LATEST">Latest Day (hourly)</option>
                  <option value="WEEK">Last 7 Days (daily)</option>
                  <option value="MONTH">Last 30 Days (daily)</option>
                  <option value="YEAR">This Year (monthly)</option>
                </select>
              </div>
            </div>

            <!-- Comparison Mode -->
            <div class="col-md-6 col-lg-3 mb-3">
              <label class="form-label text-muted">Comparison Mode</label>
              <div class="input-group">
                <span class="input-group-text">
                  <i class="fas fa-layer-group text-primary"></i>
                </span>
                <select class="form-select" id="comparison-mode">
                  <option value="INDIVIDUAL">Compare Individual Motors</option>
                  <option value="ALL">Compare All Motors</option>
                </select>
              </div>
            </div>
          </div>

          <!-- Custom Date Range (conditionally shown) -->
          <!-- Custom Date Range has been removed per client request -->

          <!-- Motor Selection Area -->
          <div class="row" id="motor-selection-area">
            <div class="col-12 mb-3">
              <div class="d-flex flex-wrap align-items-center">
                <label class="form-label text-muted me-3 mb-0">Select Motors:</label>
                <div class="form-check form-check-inline me-3">
                  <input class="form-check-input" type="checkbox" id="select-all-motors">
                  <label class="form-check-label" for="select-all-motors">Select All</label>
                </div>
                <?php
                if (!empty($devices)) {
                  foreach ($devices as $index => $device) {
                    $id = htmlspecialchars($device["D_ID"]);
                    $name = htmlspecialchars($device["D_NAME"]);
                    $colorClass = 'motor-color-' . ($index % 6 + 1); // Assign different color classes
                    echo "<div class='form-check form-check-inline me-3 mb-2'>";
                    echo "<input class='form-check-input motor-checkbox' type='checkbox' id='motor-$id' value='$id' data-name='$name'>";
                    echo "<label class='form-check-label' for='motor-$id'>";
                    echo "<span class='color-indicator $colorClass me-1'></span>$name</label>";
                    echo "</div>";
                  }
                } else {
                  echo '<div class="alert alert-info">No motors available for comparison</div>';
                }
                ?>
              </div>
            </div>
          </div>

          <!-- Action Buttons -->
          <div class="row">
            <div class="col-12 d-flex justify-content-end">
              <button type="button" class="btn btn-outline-secondary me-2" id="reset-filters">
                <i class="fas fa-redo-alt me-1"></i> Reset
              </button>
              <button type="button" class="btn btn-primary" id="update-graph">
                <i class="fas fa-sync-alt me-1"></i> Update Graph
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Graph Display Section -->
      <div class="card shadow-sm">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
          <h5 class="card-title mb-0" id="graph-title">Motor Parameter Comparison</h5>
          <!-- <div class="btn-group">
            <button type="button" class="btn btn-sm btn-outline-secondary" id="download-csv">
              <i class="fas fa-file-csv me-1"></i> Export CSV
            </button>
            <button type="button" class="btn btn-sm btn-outline-secondary" id="download-image">
              <i class="fas fa-image me-1"></i> Download Graph
            </button>
          </div> -->
        </div>
        <div class="card-body">
          <div class="row">
            <div class="col-12">
              <div id="parameter-info" class="alert alert-info mb-3">
                Select parameters and motors to generate comparison graph
              </div>
              <div id="chartdiv" style="width: 100%; height: 500px;" class="bg-graph rounded border"></div>
            </div>
          </div>
          <div class="row mt-3">
            <div class="col-12">
              <div class="table-responsive">
                <table class="table table-sm table-bordered" id="stats-table">
                  <thead>
                    <tr>
                      <th>Motor</th>
                      <th>Min</th>
                      <th>Max</th>
                      <th>Average</th>
                      <th>Last Reading</th>
                    </tr>
                  </thead>
                  <tbody>
                    <!-- Statistics will be populated here -->
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
        <div class="card-footer">
          <small class="text-body-secondary">
            <i class="fas fa-info-circle me-1"></i> The graph displays real-time data of the selected parameters for each motor. Use the controls above to customize your view.
          </small>
        </div>
      </div>
    </div>
  </div>

  <!-- Add custom style for motor indicators -->
  <style>
    .color-indicator {
      display: inline-block;
      width: 12px;
      height: 12px;
      border-radius: 50%;
    }
    .motor-color-1 { background-color: #4285F4; }
    .motor-color-2 { background-color: #EA4335; }
    .motor-color-3 { background-color: #FBBC05; }
    .motor-color-4 { background-color: #34A853; }
    .motor-color-5 { background-color: #9C27B0; }
    .motor-color-6 { background-color: #FF6D00; }
    
    .bg-graph {
      background-color: #FFFFFF;
      border-radius: 4px;
    }
    
    /* Improve form elements appearance */
    .input-group-text {
      border-right: 0;
    }
    
    .form-select, .form-control {
      border-left: 0;
    }
    
    .input-group:focus-within {
      box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
    }
  </style>

  <!-- Modals links -->
  </main>
  <script src="<?php echo BASE_PATH; ?>assets/js/sidebar-menu.js"></script>
  
  <!-- Add custom JavaScript for the interface -->
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      // Initialize variables
      let chart;
      const motorColors = [
        "#4285F4", "#EA4335", "#FBBC05", "#34A853", "#9C27B0", "#FF6D00"
      ];
      
      // Show/hide Line Voltage sub-options based on parameter selection
      document.getElementById('graph-parameter').addEventListener('change', function() {
        const lineVoltageOptions = document.getElementById('line-voltage-options');
        if (this.value === 'LINE_VOLTAGE') {
          lineVoltageOptions.style.display = 'block';
        } else {
          lineVoltageOptions.style.display = 'none';
        }
        updateGraphTitle();
      });
      
      // Reset filters button
      document.getElementById('reset-filters').addEventListener('click', function() {
        document.getElementById('graph-parameter').value = 'LINE_VOLTAGE';
        document.getElementById('voltage-type').value = 'ALL';
        document.getElementById('graph-selection').value = 'LATEST';
        document.getElementById('comparison-mode').value = 'INDIVIDUAL';
        document.getElementById('line-voltage-options').style.display = 'block';
        
        // Uncheck all motor checkboxes
        document.getElementById('select-all-motors').checked = false;
        document.querySelectorAll('.motor-checkbox').forEach(checkbox => {
          checkbox.checked = false;
        });
        
        // Clear the graph
        document.getElementById('parameter-info').textContent = 'Select parameters and motors to generate comparison graph';
        document.getElementById('parameter-info').style.display = 'block';
        document.getElementById('stats-table').querySelector('tbody').innerHTML = '';
        
        if (chart) {
          chart.clear();
        }
        
        updateGraphTitle();
      });
      
      // Update graph button
      document.getElementById('update-graph').addEventListener('click', function() {
        const selectedMotors = getSelectedMotors();
        if (selectedMotors.length === 0) {
          alert('Please select at least one motor for comparison');
          return;
        }
        
        document.getElementById('parameter-info').style.display = 'none';
        generateGraph(selectedMotors);
      });
      
      // Helper functions
      function getSelectedMotors() {
        const checkboxes = document.querySelectorAll('.motor-checkbox:checked');
        return Array.from(checkboxes).map(checkbox => ({
          id: checkbox.value,
          name: checkbox.dataset.name
        }));
      }
      
      function formatDate(date) {
        const year = date.getFullYear();
        const month = String(date.getMonth() + 1).padStart(2, '0');
        const day = String(date.getDate()).padStart(2, '0');
        return `${year}-${month}-${day}`;
      }
      
      function updateGraphTitle() {
        const parameterSelect = document.getElementById('graph-parameter');
        const parameterText = parameterSelect.options[parameterSelect.selectedIndex].text;
        
        let title = `Motor ${parameterText} Comparison`;
        
        if (parameterSelect.value === 'LINE_VOLTAGE') {
          const voltageTypeSelect = document.getElementById('voltage-type');
          const voltageTypeText = voltageTypeSelect.options[voltageTypeSelect.selectedIndex].text;
          if (voltageTypeSelect.value !== 'ALL') {
            title = `Motor ${voltageTypeText} Comparison`;
          }
        }
        
        document.getElementById('graph-title').textContent = title;
      }
      
      function generateGraph(selectedMotors) {
        // This is a placeholder for the actual graph generation logic
        // In a real implementation, you would:
        // 1. Get the selected parameters and date range
        // 2. Make an AJAX call to fetch the data
        // 3. Process the data and update the chart
        
        const parameter = document.getElementById('graph-parameter').value;
        const parameterText = document.getElementById('graph-parameter').options[document.getElementById('graph-parameter').selectedIndex].text;
        const timeRange = document.getElementById('graph-selection').value;
        const voltageType = document.getElementById('voltage-type').value;
        
        // Create demo data for visualization
        const chartData = [];
        
        // Define x-axis format based on time range
        let timeFormat;
        let dataPoints;
        const now = new Date();
        
        switch(timeRange) {
          case 'LATEST':
         
            // Hourly data for the day
            timeFormat = "HH:00";
            dataPoints = 24;
            break;
          case 'WEEK':
            // Daily data for 7 days
            timeFormat = "MMM DD";
            dataPoints = 7;
            break;
          case 'MONTH':
            // Daily data for 30 days
            timeFormat = "MMM DD";
            dataPoints = 30;
            break;
          case 'YEAR':
            // Monthly data for the year
            timeFormat = "MMM";
            dataPoints = 12;
            break;
        }
        
        // Generate appropriate sample data based on time range
        for (let i = 0; i < dataPoints; i++) {
          let dataPoint = {};
          let date;
          let formattedTime;
          
          switch(timeRange) {
            case 'LATEST':
            case 'DAY':
              date = new Date(now.getTime() - (24 - i) * 60 * 60 * 1000);
              formattedTime = `${i}:00`;
              break;
            case 'WEEK':
              date = new Date(now.getTime() - (7 - i) * 24 * 60 * 60 * 1000);
              formattedTime = `${date.toLocaleDateString('en-US', {month: 'short', day: 'numeric'})}`;
              break;
            case 'MONTH':
              date = new Date(now.getTime() - (30 - i) * 24 * 60 * 60 * 1000);
              formattedTime = `${date.toLocaleDateString('en-US', {month: 'short', day: 'numeric'})}`;
              break;
            case 'YEAR':
              date = new Date(now.getFullYear(), i, 1);
              formattedTime = `${date.toLocaleDateString('en-US', {month: 'short'})}`;
              break;
          }
          
          dataPoint = {
            date: date,
            formattedTime: formattedTime
          };
          
          // Handle different data generation based on parameter and voltage type
          selectedMotors.forEach((motor, index) => {
            if (parameter === 'LINE_VOLTAGE' && voltageType === 'ALL') {
              // Generate all three voltage types for each motor
              dataPoint[`motor_${motor.id}_R_Y`] = 380 + (Math.sin(i / 4) * 10) + (Math.random() * 5);
              dataPoint[`motor_${motor.id}_Y_B`] = 385 + (Math.sin(i / 4 + 1) * 10) + (Math.random() * 5);
              dataPoint[`motor_${motor.id}_B_R`] = 378 + (Math.sin(i / 4 + 2) * 10) + (Math.random() * 5);
            } else if (parameter === 'LINE_VOLTAGE') {
              // Generate specific voltage type for each motor
              const baseValue = 380 + Math.random() * 10;
              dataPoint[`motor_${motor.id}`] = baseValue + (Math.sin(i / 4) * 10);
            } else {
              // Generate single parameter value for each motor
              let baseValue;
              switch(parameter) {
                case 'MOTOR_VOLTAGE':
                  baseValue = 220 + Math.random() * 30;
                  break;
                case 'MOTOR_CURRENT':
                  baseValue = 5 + Math.random() * 3;
                  break;
                case 'ENERGY':
                  baseValue = 100 + i * 10 + Math.random() * 20;
                  break;
                case 'REF_FREQUENCY':
                case 'FREQUENCY':
                  baseValue = 50 + Math.random() * 2;
                  break;
                case 'SPEED':
                  baseValue = 1450 + Math.random() * 50;
                  break;
                case 'RUNNING_HOURS':
                  baseValue = 1000 + i * 5 + Math.random() * 10;
                  break;
                default:
                  baseValue = 100 + Math.random() * 50;
              }
              dataPoint[`motor_${motor.id}`] = baseValue + (Math.sin(i / 4) * (baseValue * 0.05));
            }
            dataPoint[`motor_${motor.id}_name`] = motor.name;
          });
          
          chartData.push(dataPoint);
        }
        
        // Prepare graph configurations
        const graphs = [];
        
        // Create appropriate graphs based on parameter and voltage type
        if (parameter === 'LINE_VOLTAGE' && voltageType === 'ALL') {
          // Create three graphs for each motor (R-Y, Y-B, B-R)
          selectedMotors.forEach((motor, motorIndex) => {
            const voltageTypes = ['R_Y', 'Y_B', 'B_R'];
            const voltageLabels = ['R-Y', 'Y-B', 'B-R'];
            
            voltageTypes.forEach((vType, vIndex) => {
              graphs.push({
                id: `g${motorIndex}_${vType}`,
                bullet: "round",
                bulletBorderAlpha: 1,
                bulletColor: "#FFFFFF",
                bulletSize: 5,
                hideBulletsCount: 50,
                lineThickness: 2,
                title: `${motor.name} (${voltageLabels[vIndex]})`,
                useLineColorForBulletBorder: true,
                valueField: `motor_${motor.id}_${vType}`,
                balloonText: `<div style='margin:5px; font-size:12px;'><b>${motor.name}</b><br>${voltageLabels[vIndex]} Voltage: [[value]]V</div>`,
                lineColor: motorColors[(motorIndex * 3 + vIndex) % motorColors.length],
                dashLength: vIndex > 0 ? vIndex * 3 : 0 // Make lines distinguishable
              });
            });
          });
        } else {
          // Create one graph per motor
          selectedMotors.forEach((motor, index) => {
            let valueFieldSuffix = '';
            let titleSuffix = '';
            
            if (parameter === 'LINE_VOLTAGE' && voltageType !== 'ALL') {
              valueFieldSuffix = '';
              titleSuffix = ` (${document.getElementById('voltage-type').options[document.getElementById('voltage-type').selectedIndex].text})`;
            }
            
            graphs.push({
              id: `g${index}`,
              bullet: "round",
              bulletBorderAlpha: 1,
              bulletColor: "#FFFFFF",
              bulletSize: 5,
              hideBulletsCount: 50,
              lineThickness: 2,
              title: `${motor.name}${titleSuffix}`,
              useLineColorForBulletBorder: true,
              valueField: `motor_${motor.id}${valueFieldSuffix}`,
              balloonText: `<div style='margin:5px; font-size:12px;'><b>${motor.name}</b><br>${parameterText}: [[value]]</div>`,
              lineColor: motorColors[index % motorColors.length]
            });
          });
        }
        
        // Initialize amCharts
        chart = AmCharts.makeChart("chartdiv", {
          type: "serial",
          theme: "light",
          marginRight: 40,
          marginLeft: 40,
          autoMarginOffset: 20,
          dataDateFormat: "YYYY-MM-DD JJ:NN",
          valueAxes: [{
            id: "v1",
            axisAlpha: 0,
            position: "left",
            title: parameterText
          }],
          balloon: {
            borderThickness: 1,
            shadowAlpha: 0
          },
          graphs: graphs,
          chartScrollbar: {
            graph: "g0",
            oppositeAxis: false,
            offset: 30,
            scrollbarHeight: 80,
            backgroundAlpha: 0,
            selectedBackgroundAlpha: 0.1,
            selectedBackgroundColor: "#888888",
            graphFillAlpha: 0,
            graphLineAlpha: 0.5,
            selectedGraphFillAlpha: 0,
            selectedGraphLineAlpha: 1,
            autoGridCount: true,
            color: "#AAAAAA"
          },
          chartCursor: {
            pan: true,
            valueLineEnabled: true,
            valueLineBalloonEnabled: true,
            cursorAlpha: 1,
            cursorColor: "#258cbb",
            limitToGraph: "g0",
            valueLineAlpha: 0.2,
            valueZoomable: true
          },
          categoryField: "formattedTime",
          categoryAxis: {
            parseDates: false,
            dashLength: 1,
            minorGridEnabled: true,
            title: timeFormat === "HH:00" ? "Time" : "Date"
          },
          export: {
            enabled: true
          },
          dataProvider: chartData,
          legend: {
            useGraphSettings: true,
            position: "top",
            align: "center",
            markerSize: 10
          }
        });
        
        // Update statistics table
        updateStatsTable(selectedMotors, chartData, parameter, voltageType);
      }
      
      function updateStatsTable(selectedMotors, data, parameter, voltageType) {
        const tableBody = document.getElementById('stats-table').querySelector('tbody');
        tableBody.innerHTML = '';
        
        // Create appropriate table structure based on parameter and voltage type
        if (parameter === 'LINE_VOLTAGE' && voltageType === 'ALL') {
          // Handle multiple voltage types for each motor
          selectedMotors.forEach((motor, motorIndex) => {
            const voltageTypes = ['R_Y', 'Y_B', 'B_R'];
            const voltageLabels = ['R-Y Voltage', 'Y-B Voltage', 'B-R Voltage'];
            
            voltageTypes.forEach((vType, vIndex) => {
              const motorValues = data.map(point => point[`motor_${motor.id}_${vType}`]);
              
              if (!motorValues.length) return;
              
              const min = Math.min(...motorValues).toFixed(2);
              const max = Math.max(...motorValues).toFixed(2);
              const avg = (motorValues.reduce((sum, val) => sum + val, 0) / motorValues.length).toFixed(2);
              const last = motorValues[motorValues.length - 1].toFixed(2);
              
              const row = document.createElement('tr');
              row.innerHTML = `
                <td>
                  <span class="color-indicator motor-color-${((motorIndex * 3 + vIndex) % 6) + 1} me-2"></span>
                  ${motor.name} (${voltageLabels[vIndex]})
                </td>
                <td>${min}</td>
                <td>${max}</td>
                <td>${avg}</td>
                <td>${last}</td>
              `;
              tableBody.appendChild(row);
            });
          });
        } else {
          // Standard single value per motor
          selectedMotors.forEach((motor, index) => {
            let valueField = `motor_${motor.id}`;
            const motorValues = data.map(point => point[valueField]);
            
            if (!motorValues.length) return;
            
            const min = Math.min(...motorValues).toFixed(2);
            const max = Math.max(...motorValues).toFixed(2);
            const avg = (motorValues.reduce((sum, val) => sum + val, 0) / motorValues.length).toFixed(2);
            const last = motorValues[motorValues.length - 1].toFixed(2);
            
            let titleSuffix = '';
            if (parameter === 'LINE_VOLTAGE' && voltageType !== 'ALL') {
              titleSuffix = ` (${document.getElementById('voltage-type').options[document.getElementById('voltage-type').selectedIndex].text})`;
            }
            
            const row = document.createElement('tr');
            row.innerHTML = `
              <td>
                <span class="color-indicator motor-color-${(index % 6) + 1} me-2"></span>
                ${motor.name}${titleSuffix}
              </td>
              <td>${min}</td>
              <td>${max}</td>
              <td>${avg}</td>
              <td>${last}</td>
            `;
            tableBody.appendChild(row);
          });
        }
      }
      
      // Initialize display
      document.getElementById('graph-parameter').dispatchEvent(new Event('change'));
      document.getElementById('graph-selection').dispatchEvent(new Event('change'));
    });
    // Toggle all motor checkboxes
      document.getElementById('select-all-motors').addEventListener('change', function() {
        const motorCheckboxes = document.querySelectorAll('.motor-checkbox');
        motorCheckboxes.forEach(checkbox => {
          checkbox.checked = this.checked;
        });
      });
  </script>
  
  <?php
  include(BASE_PATH . "assets/html/body-end.php");
  include(BASE_PATH . "assets/html/html-end.php");
  ?>