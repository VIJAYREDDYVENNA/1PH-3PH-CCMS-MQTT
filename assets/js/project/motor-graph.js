/**
 * Motor Parameter Comparison
 * This script handles the motor parameter comparison functionality
 */
console.log(document.getElementsByName)
document.addEventListener('DOMContentLoaded', function () {
  // Initialize variables
  let chart;
  const motorColors = [
    "#4285F4", "#EA4335", "#FBBC05", "#34A853", "#9C27B0", "#FF6D00"
  ];
  const MAX_MOTORS = 3; // Maximum motors allowed for non-latest hour selections

  // Event Listeners
  // Show/hide Line Voltage sub-options based on parameter selection
  document.getElementById('graph-parameter').addEventListener('change', function () {
    const lineVoltageOptions = document.getElementById('line-voltage-options');
    if (this.value === 'LINE_VOLTAGE') {
      lineVoltageOptions.style.display = 'block';
    } else {
      lineVoltageOptions.style.display = 'none';
    }
    updateGraphTitle();
  });


  // Handle time range changes - enforce motor selection limits
  document.getElementById('graph-selection').addEventListener('change', function () {
    const timeRange = this.value;
    const selectedMotors = getSelectedMotors();

    if (timeRange !== 'LATESTHOUR' && selectedMotors.length > MAX_MOTORS) {
      // If switching to a non-latest hour view with too many motors selected
      alert(`For ${this.options[this.selectedIndex].text} view, please select a maximum of ${MAX_MOTORS} motors for optimal performance.`);

      // Uncheck motors beyond the limit
      const checkboxes = document.querySelectorAll('.motor-checkbox:checked');
      for (let i = MAX_MOTORS; i < checkboxes.length; i++) {
        checkboxes[i].checked = false;
      }

      // Update select-all checkbox
      document.getElementById('select-all-motors').checked = false;
    }
  });

  // Toggle all motor checkboxes with limit enforcement
  document.getElementById('select-all-motors').addEventListener('change', function () {
    const timeRange = document.getElementById('graph-selection').value;
    const motorCheckboxes = document.querySelectorAll('.motor-checkbox');

    if (this.checked && timeRange !== 'LATESTHOUR' && motorCheckboxes.length > MAX_MOTORS) {
      // If trying to select all in a non-latest hour view
      alert(`For ${document.getElementById('graph-selection').options[document.getElementById('graph-selection').selectedIndex].text} view, please select a maximum of ${MAX_MOTORS} motors for optimal performance.`);
      this.checked = false;

      // Don't change any motor checkboxes
      return;
    }

    // Apply the selection to all checkboxes
    motorCheckboxes.forEach(checkbox => {
      checkbox.checked = this.checked;
    });
  });

  document.querySelectorAll('.motor-checkbox').forEach(checkbox => {
    checkbox.addEventListener('change', function () {
      const timeRange = document.getElementById('graph-selection').value;

      if (timeRange !== 'LATESTHOUR') {
        const selectedCount = document.querySelectorAll('.motor-checkbox:checked').length;

        if (selectedCount > MAX_MOTORS && this.checked) {
          alert(`For ${document.getElementById('graph-selection').options[document.getElementById('graph-selection').selectedIndex].text} view, please select a maximum of ${MAX_MOTORS} motors for optimal performance.`);
          this.checked = false;
        }
      }

      // Update select-all checkbox state
      const allCheckboxes = document.querySelectorAll('.motor-checkbox');
      const checkedCheckboxes = document.querySelectorAll('.motor-checkbox:checked');
      document.getElementById('select-all-motors').checked =
        allCheckboxes.length === checkedCheckboxes.length;
    });
  });
  // Reset filters button
  document.getElementById('reset-filters').addEventListener('click', function () {
    document.getElementById('graph-parameter').value = 'LINE_VOLTAGE';
    document.getElementById('voltage-type').value = 'R_Y';
    document.getElementById('graph-selection').value = 'LATESTHOUR';  // Changed from LATEST to LATESTHOUR
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

    if (document.getElementById('stats-table')) {
      document.getElementById('stats-table').querySelector('tbody').innerHTML = '';
    }

    if (chart) {
      chart.clear();
    }

    updateGraphTitle();
  });

  // Update graph button
  document.getElementById('update-graph').addEventListener('click', function () {
    const selectedMotors = getSelectedMotors();
    if (selectedMotors.length === 0) {
      alert('Please select at least one motor for comparison');
      return;
    }

    document.getElementById('parameter-info').style.display = 'none';
    fetchAndDisplayData(selectedMotors);
  });

  // Helper functions
  function getSelectedMotors() {
    const checkboxes = document.querySelectorAll('.motor-checkbox:checked');
    return Array.from(checkboxes).map(checkbox => ({
      id: checkbox.value,
      name: checkbox.dataset.name
    }));
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

  // Fetch data from the server and display graph
  function fetchAndDisplayData(selectedMotors) {
    // Show loading indicator
    document.getElementById('pre-loader').style.display = 'block';

    document.getElementById('parameter-info').textContent = 'Loading data...';
    document.getElementById('parameter-info').style.display = 'block';

    const parameter = document.getElementById('graph-parameter').value;
    const timeRange = document.getElementById('graph-selection').value;
    const voltageType = document.getElementById('voltage-type').value;
    const comparisonMode = document.getElementById('comparison-mode').value;

    // Prepare motor IDs for the request
    const motorIds = selectedMotors.map(motor => motor.id);

    // Create form data for POST request
    const formData = new FormData();
    formData.append('parameter', parameter);
    formData.append('timeRange', timeRange);
    formData.append('voltageType', voltageType);
    formData.append('comparisonMode', comparisonMode);
    formData.append('selectedMotors', JSON.stringify(motorIds));

    // Send AJAX request to server
    fetch('../motor-graphs/code/get_motor_data.php', {
      method: 'POST',
      body: formData
    })
      .then(response => {
        if (!response.ok) {
          throw new Error('Network response was not ok');
        }
        return response.json();
      })
      .then(data => {
        if (data.success) {
          generateGraph(selectedMotors, data);
        } else {
          throw new Error(data.message || 'Failed to fetch data');
        }
        $("#pre-loader").css('display', 'none');

      })
      .catch(error => {
        document.getElementById('parameter-info').textContent = 'Error: ' + error.message;
        document.getElementById('parameter-info').style.display = 'block';
        console.error('Error:', error);
      });
  }

  function generateGraph(selectedMotors, responseData) {
    // Hide info message
    document.getElementById('parameter-info').style.display = 'none';

    const parameter = document.getElementById('graph-parameter').value;
    const parameterText = document.getElementById('graph-parameter').options[document.getElementById('graph-parameter').selectedIndex].text;
    const timeRange = document.getElementById('graph-selection').value;
    const voltageType = document.getElementById('voltage-type').value;

    // Process data for chart
    const chartData = processDataForChart(selectedMotors, responseData.data, parameter, voltageType);

    // Create graphs configuration
    const graphs = createGraphConfigurations(selectedMotors, parameter, voltageType, parameterText);

    // Get appropriate Y-axis title based on parameter
    const valueAxisTitle = getValueAxisTitle(parameter, voltageType);

    // Initialize amCharts
    chart = AmCharts.makeChart("chartdiv", {
      type: "serial",
      theme: "light",
      marginRight: 40,
      marginLeft: 40,
      autoMarginOffset: 20,
      valueAxes: [{
        id: "v1",
        axisAlpha: 0,
        position: "left",
        title: valueAxisTitle
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
      categoryField: "formatted_time",
      categoryAxis: {
        parseDates: false,
        dashLength: 1,
        minorGridEnabled: true,
        title: getXAxisTitle(timeRange)
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
  }

  function processDataForChart(selectedMotors, rawData, parameter, voltageType) {
    // Create a map to consolidate data points with the same time
    const timeMap = new Map();

    // Process data for each motor
    selectedMotors.forEach(motor => {
      const motorData = rawData[motor.id] || [];

      motorData.forEach(dataPoint => {
        const time = dataPoint.formatted_time;

        // Get or create entry for this time point
        if (!timeMap.has(time)) {
          timeMap.set(time, { formatted_time: time });
        }

        const timeEntry = timeMap.get(time);

        // Add motor data to the time entry
        if (parameter === 'LINE_VOLTAGE' && voltageType === 'ALL') {
          // Handle multiple voltage types
          timeEntry[`motor_${motor.id}_R_Y`] = dataPoint.R_Y;
          timeEntry[`motor_${motor.id}_Y_B`] = dataPoint.Y_B;
          timeEntry[`motor_${motor.id}_B_R`] = dataPoint.B_R;
          timeEntry[`motor_${motor.id}_name`] = motor.name;
          timeEntry[`motor_${motor.id}_time`] = dataPoint.date_time;

        } else {
          // Handle single parameter
          timeEntry[`motor_${motor.id}`] = dataPoint.value;
          timeEntry[`motor_${motor.id}_name`] = motor.name;
          timeEntry[`motor_${motor.id}_time`] = dataPoint.date_time;
        }
      });
    });

    // Convert map values to array and sort by time
    let chartDataArray = Array.from(timeMap.values());

    // Sort the data by time
    chartDataArray = sortChartDataByTime(chartDataArray);

    return chartDataArray;
  }

  function sortChartDataByTime(chartData) {
    const timeRange = document.getElementById('graph-selection').value;

    return chartData.sort((a, b) => {
      // For "LATESTHOUR" time range with hour:minute format like "12:30", "12:45", etc.
      if (timeRange === 'LATESTHOUR') {
        // For HH:MM format, we can sort based on hour and minute
        const [hourA, minA] = a.formatted_time.split(':').map(num => parseInt(num, 10));
        const [hourB, minB] = b.formatted_time.split(':').map(num => parseInt(num, 10));

        if (hourA !== hourB) {
          return hourA - hourB;
        }
        return minA - minB;
      }
      // For "LATEST" time range with hour:minute format like "0:00", "1:00", etc.
      else if (timeRange === 'LATEST') {
        // Extract the hour from the formatted time
        const hourA = parseInt(a.formatted_time.split(':')[0], 10);
        const hourB = parseInt(b.formatted_time.split(':')[0], 10);
        return hourA - hourB;
      }
      // For "WEEK" or "MONTH" with date format like "May 10"
      else if (timeRange === 'WEEK' || timeRange === 'MONTH') {
        const dateA = new Date(a.formatted_time + ', ' + new Date().getFullYear());
        const dateB = new Date(b.formatted_time + ', ' + new Date().getFullYear());
        return dateA - dateB;
      }
      // For "YEAR" with month format like "Jan", "Feb", etc.
      else if (timeRange === 'YEAR') {
        const months = {
          'Jan': 0, 'Feb': 1, 'Mar': 2, 'Apr': 3,
          'May': 4, 'Jun': 5, 'Jul': 6, 'Aug': 7,
          'Sep': 8, 'Oct': 9, 'Nov': 10, 'Dec': 11
        };
        return months[a.formatted_time] - months[b.formatted_time];
      }
      // Default comparison (lexicographical)
      return a.formatted_time.localeCompare(b.formatted_time);
    });
  }

  function createGraphConfigurations(selectedMotors, parameter, voltageType, parameterText) {
    const graphs = [];

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
            balloonText: `<div style='margin:5px; font-size:12px;'><b>${motor.name}</b><br>${voltageLabels[vIndex]} Voltage: <b>[[value]]V</b><br>Time: <b>[[motor_${motor.id}_time]]</b></div>`,
            lineColor: motorColors[(motorIndex * 3 + vIndex) % motorColors.length],
            dashLength: vIndex > 0 ? vIndex * 3 : 0 // Make lines distinguishable
          });
        });
      });
    } else {
      // Create one graph per motor
      selectedMotors.forEach((motor, index) => {
        let titleSuffix = '';

        if (parameter === 'LINE_VOLTAGE' && voltageType !== 'ALL') {
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
          valueField: `motor_${motor.id}`,
          balloonText: `<div style='margin:5px; font-size:12px;'><b>${motor.name}</b><br>${parameterText}: <b>[[value]]</b><br>Time: <b>[[motor_${motor.id}_time]]</b></div>`,
          lineColor: motorColors[index % motorColors.length]
        });
      });
    }

    return graphs;
  }

  function getValueAxisTitle(parameter, voltageType) {
    switch (parameter) {
      case 'LINE_VOLTAGE':
        return voltageType === 'ALL' ? 'Voltage (V)' : 'Voltage (V)';
      case 'MOTOR_VOLTAGE':
        return 'Voltage (V)';
      case 'MOTOR_CURRENT':
        return 'Current (A)';
      case 'ENERGY':
        return 'Energy (kWh)';
      case 'REF_FREQUENCY':
      case 'FREQUENCY':
        return 'Frequency (Hz)';
      case 'SPEED':
        return 'Speed (RPM)';
      case 'RUNNING_HOURS':
        return 'Hours';
      default:
        return 'Value';
    }
  }

  function getXAxisTitle(timeRange) {
    switch (timeRange) {
      case 'LATESTHOUR':
        return 'Time (HH:MM)';
      case 'LATEST':
      case 'DAY':
        return 'Time';
      case 'WEEK':
      case 'MONTH':
        return 'Date';
      case 'YEAR':
        return 'Month';
      default:
        return 'Time';
    }
  }

  // Function to initialize default motor selection and load the graph
  function initDefaultView() {
    // Set default parameter values
    document.getElementById('graph-parameter').value = 'LINE_VOLTAGE';
    document.getElementById('voltage-type').value = 'ALL';
    document.getElementById('graph-selection').value = 'LATESTHOUR'; // Changed from LATEST to LATESTHOUR
    document.getElementById('comparison-mode').value = 'INDIVIDUAL';
    document.getElementById('line-voltage-options').style.display = 'block';

    // Update the graph title
    updateGraphTitle();

    // Find motor_1 and motor_2 checkboxes and check them
    const motorCheckboxes = document.querySelectorAll('.motor-checkbox');
    let motor1Found = false;
    let motor2Found = false;

    motorCheckboxes.forEach(checkbox => {
      // Check if ID is exactly "motor-1" or if it contains "motor-1" with suffix/prefix
      if (checkbox.id === "motor-1" || checkbox.value === "1") {
        checkbox.checked = true;
        motor1Found = true;
      }
      // Check if ID is exactly "motor-2" or if it contains "motor-2" with suffix/prefix  
      else if (checkbox.id === "motor-2" || checkbox.value === "2") {
        checkbox.checked = true;
        motor2Found = true;
      }
    });

    // If both motors were found, fetch data and display the graph
    if (motor1Found || motor2Found) {
      const selectedMotors = getSelectedMotors();
      if (selectedMotors.length > 0) {
        document.getElementById('parameter-info').style.display = 'none';
        fetchAndDisplayData(selectedMotors);
      }
    } else {
      // If motors weren't found, just select the first two motors
      if (motorCheckboxes.length >= 2) {
        motorCheckboxes[0].checked = true;
        motorCheckboxes[1].checked = true;

        const selectedMotors = getSelectedMotors();
        document.getElementById('parameter-info').style.display = 'none';
        fetchAndDisplayData(selectedMotors);
      }
    }
  }

  // Initialize the page by showing default view
  initDefaultView();
});







// Updated generateGraph function with modified chartCursor configuration
// function generateGraph(selectedMotors, responseData) {
//   // Hide info message
//   document.getElementById('parameter-info').style.display = 'none';

//   const parameter = document.getElementById('graph-parameter').value;
//   const parameterText = document.getElementById('graph-parameter').options[document.getElementById('graph-parameter').selectedIndex].text;
//   const timeRange = document.getElementById('graph-selection').value;
//   const voltageType = document.getElementById('voltage-type').value;

//   // Process data for chart
//   const chartData = processDataForChart(selectedMotors, responseData.data, parameter, voltageType);

//   // Create graphs configuration
//   const graphs = createGraphConfigurations(selectedMotors, parameter, voltageType, parameterText);

//   // Get appropriate Y-axis title based on parameter
//   const valueAxisTitle = getValueAxisTitle(parameter, voltageType);

//   // Initialize amCharts
//   chart = AmCharts.makeChart("chartdiv", {
//     type: "serial",
//     theme: "light",
//     marginRight: 40,
//     marginLeft: 40,
//     autoMarginOffset: 20,
//     valueAxes: [{
//       id: "v1",
//       axisAlpha: 0,
//       position: "left",
//       title: valueAxisTitle
//     }],
//     balloon: {
//       borderThickness: 1,
//       shadowAlpha: 0
//     },
//     graphs: graphs,
//     chartScrollbar: {
//       graph: "g0",
//       oppositeAxis: false,
//       offset: 30,
//       scrollbarHeight: 80,
//       backgroundAlpha: 0,
//       selectedBackgroundAlpha: 0.1,
//       selectedBackgroundColor: "#888888",
//       graphFillAlpha: 0,
//       graphLineAlpha: 0.5,
//       selectedGraphFillAlpha: 0,
//       selectedGraphLineAlpha: 1,
//       autoGridCount: true,
//       color: "#AAAAAA"
//     },
//     chartCursor: {
//       pan: true,
//       valueLineEnabled: true,
//       valueLineBalloonEnabled: true,
//       cursorAlpha: 1,
//       cursorColor: "#258cbb",
//       limitToGraph: "g0",
//       valueLineAlpha: 0.2,
//       valueZoomable: true,
//       oneBalloonOnly: true,      // Show only one balloon for the closest point
//       fullWidth: false,          // Don't extend cursor across full width
//       categoryBalloonEnabled: true,
//       selectWithoutZooming: true // Allow selecting point without zooming
//     },
//     categoryField: "formatted_time",
//     categoryAxis: {
//       parseDates: false,
//       dashLength: 1,
//       minorGridEnabled: true,
//       title: getXAxisTitle(timeRange)
//     },
//     export: {
//       enabled: true
//     },
//     dataProvider: chartData,
//     legend: {
//       useGraphSettings: true,
//       position: "top",
//       align: "center",
//       markerSize: 10
//     }
//   });
// }