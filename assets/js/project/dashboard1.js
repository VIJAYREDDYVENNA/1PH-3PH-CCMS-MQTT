setInterval(refresh_data, 60000)
function refresh_data() {
  const activeTab = document.querySelector(".tab-content.active")
  if (!activeTab) return // Guard clause if no active tab exists

  const activeMotorTab = activeTab.id
  const motorNumber = activeMotorTab.replace("motor_", "")
  fetchAndUpdateMotorDetails(activeMotorTab, motorNumber)
}

const motorConfig = {
  count: 6, // Total number of motors
  metrics: [
    // Left column metrics
    {
      id: "r_y_voltage",
      icon: "bi-lightning-charge", // Lightning with charge (no fill)
      title: "Line R_Y Voltage",
      valueId: "r_y_voltage",
      unit: "V",
    },
    {
      id: "y_b_voltage",
      icon: "bi-lightning", // Regular lightning
      title: "Line Y_B Voltage",
      valueId: "y_b_voltage",
      unit: "V",
    },
    {
      id: "b_r_voltage",
      icon: "bi-lightning-charge-fill", // Lightning with charge (filled)
      title: "Line B_R Voltage",
      valueId: "b_r_voltage",
      unit: "V",
    },
    {
      id: "current",
      icon: "bi-plug-fill",
      title: "Motor Current",
      valueId: "current",
      unit: "A",
    },
    {
      id: "refVoltage",
      icon: "bi-lightning-fill",
      title: "Motor Voltage",
      valueId: "motor_voltage",
      unit: "V",
    },
    // Right column metrics

    {
      id: "energy",
      icon: "bi-battery-half",
      title: "Energy (kWh)",
      valueId: "kwh",
      unit: "",
    },

    {
      id: "refFrequency",
      icon: "bi-broadcast-pin",
      title: "Reference Frequency",
      valueId: "ref_frequency",
      unit: "Hz",
    },
    {
      id: "frequency",
      icon: "bi-activity",
      title: "Frequency",
      valueId: "frequency",
      unit: "Hz",
    },
    {
      id: "speed",
      icon: "bi-speedometer2",
      title: "Speed",
      valueId: "speed",
      unit: "RPM",
    },
    {
      id: "hours",
      icon: "bi-clock-history",
      title: "Running Hours",
      valueId: "hours",
      unit: "hrs",
    },
  ],
  // Admin-only metric
  adminMetric: {
    id: "admin-only",
    icon: "bi-shield-lock-fill",
    title: " Drive Status",
    valueId: "admin-status",
    unit: "",
  },
}

// Global variable for MQTT client
let mqttClient = null
let mqttConnected = false

// Define activeMotors and activePlatforms at the top level to fix the initialization error
let activeMotors = []
let activePlatforms = []

// Generate HTML for motor tabs and content
function generateMotorTabsAndContent() {
  const tabsContainer = document.getElementById("motor-tabs")
  const tabContentsContainer = document.getElementById("motor-tab-contents")

  // Guard clause if elements don't exist
  if (!tabsContainer || !tabContentsContainer) {
    console.error("Required DOM elements not found: motor-tabs or motor-tab-contents")
    return
  }

  const userRole = document.getElementById("user-role")?.value || ""
  const isSuperAdmin = userRole === "SUPERADMIN"

  // Clear existing content
  tabsContainer.innerHTML = ""
  tabContentsContainer.innerHTML = ""

  // Generate tabs and content for each motor
  for (let i = 1; i <= motorConfig.count; i++) {
    // Create tab trigger
    const tabTrigger = document.createElement("div")
    tabTrigger.className = "tab-trigger" + (i === 1 ? " active" : "")
    tabTrigger.setAttribute("data-tab", `motor_${i}`)
    tabTrigger.textContent = `Motor ${i}`
    tabsContainer.appendChild(tabTrigger)

    // Create tab content
    const tabContent = document.createElement("div")
    tabContent.id = `motor_${i}`
    tabContent.className = "tab-content" + (i === 1 ? " active" : "")

    // Create card
    tabContent.innerHTML = `
<div class="card">
<div class="card-header amber-gradient">
  <div class="d-flex flex-column flex-md-row justify-content-md-between align-items-md-center w-100">
    <h3 class="mb-2 mb-md-0 d-flex align-items-center gap-2">
      <i class="bi bi-cpu-fill fs-4"></i> Motor ${i} Electrical Details
    </h3>
    <div>
      <h3 class="m-0 fs-6 fs-md-5" id="update_time">
        <span class="timestamp-value">Updated On: </span>
        <span id="motor-${i}motor_update_date_time"></span>
      </h3>
    </div>
  </div>
</div>
<div class="card-body">
  <div class="grid-2-cols">
    <!-- Left Side -->
    <div id="motor-${i}-left-metrics">
      ${generateMetrics(i, 0, 5, isSuperAdmin)}
      ${isSuperAdmin ? generateAdminMetric(i) : ""}
    </div>
    
    <!-- Right Side -->
    <div id="motor-${i}-right-metrics">
      ${generateMetrics(i, 5, 10, isSuperAdmin)}
    </div>
  </div>
</div>
</div>
`

    tabContentsContainer.appendChild(tabContent)
  }

  // Add event listeners for tab changes
  addTabEventListeners()
}

// Generate HTML for a set of metrics
function generateMetrics(motorId, startIndex, endIndex, isSuperAdmin) {
  let metricsHtml = ""

  for (let i = startIndex; i < endIndex && i < motorConfig.metrics.length; i++) {
    const metric = motorConfig.metrics[i]

    metricsHtml += `
<div class="metric-row ${metric.id}">
<div class="d-flex align-items-center">
  <div class="metric-icon icon-${metric.id}">
    <i class="bi ${metric.icon} fs-5"></i>
  </div>
  <h5 class="section-subtitle">${metric.title}</h5>
</div>
<span id="motor-${motorId}-${metric.valueId}" class="small-card-value">-- ${metric.unit}</span>
</div>
`
  }

  return metricsHtml
}

// Generate HTML for admin-only metric
function generateAdminMetric(motorId) {
  const metric = motorConfig.adminMetric

  return `
<div class="metric-row ${metric.id} admin-only-metric">
<div class="d-flex align-items-center">
<div class="metric-icon icon-${metric.id}">
  <i class="bi ${metric.icon} fs-5"></i>
</div>
<h5 class="section-subtitle">${metric.title}</h5>
</div>
<span id="motor-${motorId}-${metric.valueId}" class="small-card-value">--</span>
</div>
`
}

// Add event listeners to the tabs
function addTabEventListeners() {
  document.querySelectorAll(".tab-trigger").forEach((trigger) => {
    trigger.addEventListener("click", () => {
      const activeMotorTab = trigger.getAttribute("data-tab")
      const motorNumber = activeMotorTab.replace("motor_", "")

      // Remove 'active' from all tab triggers
      document.querySelectorAll(".tab-trigger").forEach((tab) => {
        tab.classList.remove("active")
      })

      // Add 'active' class to the clicked tab trigger
      trigger.classList.add("active")

      // Remove 'active' from all tab contents
      document.querySelectorAll(".tab-content").forEach((tab) => {
        tab.classList.remove("active")
      })

      // Add 'active' to the selected tab content
      const selectedTab = document.getElementById(activeMotorTab)
      if (selectedTab) {
        selectedTab.classList.add("active")
        const preLoader = document.getElementById("pre-loader")
        if (preLoader) preLoader.style.display = "block"

        // Update electrical details for the active motor tab
        fetchAndUpdateMotorDetails(activeMotorTab, motorNumber)
      }
    })
  })
}

// Helper function to update electrical details for a specific motor
function updateElectricalDetails(motorNumber, details) {
  motorConfig.metrics.forEach((metric) => {
    const elementId = `motor-${motorNumber}-${metric.valueId}`
    const element = document.getElementById(elementId)

    if (element) {
      const valueMap = {
        r_y_voltage: details.r_y_voltage,
        y_b_voltage: details.y_b_voltage,
        b_r_voltage: details.b_r_voltage,
        current: details.motorCurrent,
        kwh: details.energyKwh,
        frequency: details.frequency,
        speed: details.speed,
        hours: details.runningHours,
        motor_voltage: details.motorVoltage,
        ref_frequency: details.referencefrequency,
      }

      // Set update date time
      const updateTimeElement = document.getElementById(`motor-${motorNumber}motor_update_date_time`)
      if (updateTimeElement) {
        updateTimeElement.innerHTML = details.date_time || ""
      }

      // Ensure 0 is not treated as "no value"
      const rawValue = valueMap[metric.valueId]
      const value = rawValue !== null && rawValue !== undefined ? rawValue : "0"
      element.textContent = metric.unit ? `${value} ${metric.unit}` : value
    }
  })

  // Update admin metric if user is superadmin
  const userRole = document.getElementById("user-role")?.value || ""
  if (userRole === "SUPERADMIN") {
    const adminMetricElement = document.getElementById(`motor-${motorNumber}-${motorConfig.adminMetric.valueId}`)
    const adminStatus = details.adminStatus

    if (adminMetricElement && adminStatus != null && adminStatus !== undefined) {
      adminMetricElement.textContent = adminStatus
    }
  }
}

// Function to fetch motor details and update the electrical details
function fetchAndUpdateMotorDetails(motor_id, motor_number) {
  const userRole = document.getElementById("user-role")?.value || ""
  const preLoader = document.getElementById("pre-loader")

  // Fetch latest data for the selected motor from database
  $.ajax({
    type: "POST",
    url: "../dashboard/code/update_motortab_data.php",
    data: {
      motor: motor_id,
      role: userRole, // Pass the role to the server
    },
    dataType: "json",
    success: (data) => {
      if (preLoader) preLoader.style.display = "none"
      updateElectricalDetails(motor_number, data)
    },
    error: (xhr, status, error) => {
      console.error(`Error fetching motor ${motor_id} details:`, error)
      if (preLoader) preLoader.style.display = "none"
    },
  })
}

// Helper function to update inlet pressure display
function updateInletPressure(isActive) {
  const inletPressureEl = $("#inlet-pressure-status")
  if (inletPressureEl.length === 0) return

  // Update classes
  inletPressureEl
    .removeClass(isActive ? "badge-outline-danger" : "badge-outline-success")
    .addClass(isActive ? "badge-outline-success" : "badge-outline-danger")

  // Remove existing text nodes without affecting child elements
  inletPressureEl
    .contents()
    .filter(function () {
      return this.nodeType === 3 // Text nodes
    })
    .remove()

  // Insert the new text at the beginning
  inletPressureEl.prepend(isActive ? "Yes" : "No")

  // Add the pulse dot if it doesn't exist
  if (inletPressureEl.find(".pulse-dot").length === 0) {
    // inletPressureEl.append('<span class="pulse-dot"></span>');
  }

  // Show/hide the dot based on status
  inletPressureEl.find(".pulse-dot")[isActive ? "show" : "hide"]()
}

// Helper function to update platform statuses
function updatePlatformStatuses(data) {
  const platforms = [
    {
      id: "platform-1-2",
      statusKey: "p1_p2_on_off_status",
      timeKey: "p1_p2_last_open_time",
    },
    {
      id: "platform-3-4",
      statusKey: "p3_p4_on_off_status",
      timeKey: "p3_p4_last_open_time",
    },
    {
      id: "platform-5-6",
      statusKey: "p5_p6_on_off_status",
      timeKey: "p5_p6_last_open_time",
    },
    {
      id: "platform-7",
      statusKey: "p7_on_off_status",
      timeKey: "p7_last_open_time",
    },
    {
      id: "platform-8",
      statusKey: "p8_on_off_status",
      timeKey: "p8_last_open_time",
    },
    {
      id: "platform-9-10",
      statusKey: "p9_p10_on_off_status",
      timeKey: "p9_p10_last_open_time",
    },
  ]

  platforms.forEach(({ id, statusKey, timeKey }) => {
    const status = Number.parseInt(data[statusKey]) === 1 ? "Open" : "Closed"
    const time = data[timeKey] ?? 0

    const platformElement = $(`#${id}-status`)
    if (platformElement.length === 0) return

    platformElement.empty() // Clear previous contents

    // Apply new class based on status
    platformElement
      .attr("class", status === "Open" ? "badge-success" : "badge-danger")
      .append(document.createTextNode(status))

    // Append pulse-dot if open
    if (status === "Open" && platformElement.find(".pulse-dot").length === 0) {
      platformElement.append('<span class="pulse-dot"></span>')
    }

    // Update open time
    $(`#${id}-time`).text(`${time} min`)
  })
}

// MQTT connection and message handling
function requestMqttUpdate() {
  if (mqttClient && mqttConnected) {
    console.log("Requesting data update via MQTT")
    // Publish a request message to a request topic that the publisher is listening to
    mqttClient.publish("test/request", "data_request")
  }
}

// First, add the CryptoJS library to your HTML page
// <script src="https://cdnjs.cloudflare.com/ajax/libs/crypto-js/4.1.1/crypto-js.min.js"></script>
function xor_decrypt(encoded, key) {
  try {
    const decoded = atob(encoded)
    let result = ""

    for (let i = 0; i < decoded.length; i++) {
      result += String.fromCharCode(decoded.charCodeAt(i) ^ key.charCodeAt(i % key.length))
    }

    return JSON.parse(result)
  } catch (error) {
    console.error("Decryption failed:", error)
    return null
  }
}

let vfdMqttClient
let isVfdMqttConnected = false
let isVfdMqttIntervalSet = false

function mqttReconnect() {
  $.ajax({
    type: "POST",
    url: "../common-files/get_mqtt_credentials.php",
    dataType: "json",
    success: (response) => {
      const consoledata = "consoledata"
      const decryptedData = xor_decrypt(response.data, consoledata)

      if (!decryptedData) {
        console.error("Failed to decrypt MQTT credentials")
        return
      }

      const options = {
        username: decryptedData.username,
        password: decryptedData.password,
        reconnectPeriod: decryptedData.reconnectPeriod,
        connectTimeout: decryptedData.connectTimeout,
        clean: decryptedData.clean,
      }

      const brokerUrl = decryptedData.brokerUrl
      vfdMqttClient = mqtt.connect(brokerUrl, options)

      const publishTopic = "SUB/SCRSC/VFD_SETTING"
      const publishMessage = "APP;CONNECTED"

      vfdMqttClient.on("connect", () => {
        console.log("Connected to MQTT broker")
        isVfdMqttConnected = true

        const connectionStatus = document.getElementById("connection_status")
        if (connectionStatus) {
          connectionStatus.innerHTML = "Connected"
          connectionStatus.style.color = "green"
        }

        // Hide reconnect button if it exists
        const btn = document.getElementById("reconnectBtn")
        if (btn) btn.remove()

        vfdMqttClient.publish(publishTopic, publishMessage, (err) => {
          if (!err) {
            console.log("Published message after connecting.")
          } else {
            console.error("Publish error:", err)
          }
        })
      })

      vfdMqttClient.on("close", () => {
        console.log("MQTT Connection lost")
        isVfdMqttConnected = false
        showDisconnectedStatus()
      })

      vfdMqttClient.on("error", (error) => {
        console.error("MQTT Error:", error)
        isVfdMqttConnected = false
        showDisconnectedStatus()
      })

      if (!isVfdMqttIntervalSet) {
        setInterval(() => {
          const connectionStatus = document.getElementById("connection_status")
          if (!connectionStatus) return

          if (vfdMqttClient && vfdMqttClient.connected) {
            connectionStatus.innerHTML = "Connected"
            connectionStatus.style.color = "green"
          } else {
            showDisconnectedStatus()
            console.warn("MQTT disconnected. Waiting for manual reconnect...")
          }
        }, 30000)
        isVfdMqttIntervalSet = true
      }
    },
    error: (xhr, status, error) => {
      console.error(`Error fetching MQTT credentials:`, error)
      isVfdMqttConnected = false
      showDisconnectedStatus()
    },
  })
}

function showDisconnectedStatus() {
  const statusElement = document.getElementById("connection_status")
  if (!statusElement) return

  statusElement.innerHTML = `
        Disconnected 
        <button id="reconnectBtn" class="btn btn-sm btn-outline-danger ms-2">
            Reconnect
        </button>
    `
  statusElement.style.color = "red"

  // Bind the reconnect button event
  const btn = document.getElementById("reconnectBtn")
  if (btn) {
    btn.addEventListener("click", () => {
      mqttReconnect()
    })
  }
}

function connectMqtt() {
  $.ajax({
    type: "POST",
    url: "../common-files/get_mqtt_credentials.php",
    dataType: "json",
    success: (response) => {
      const consoledata = "consoledata"
      const decryptedData = xor_decrypt(response.data, consoledata)

      if (!decryptedData) {
        console.error("Failed to decrypt MQTT credentials")
        return
      }

      //  console.log('Decrypted data:', decryptedData);
      const options = {
        username: decryptedData.username,
        password: decryptedData.password,
        reconnectPeriod: decryptedData.reconnectPeriod,
        connectTimeout: decryptedData.connectTimeout,
        clean: decryptedData.clean,
      }

      // Get broker URL and topics from PHP response instead of hardcoding
      const brokerUrl = decryptedData.brokerUrl
      const topic = decryptedData.mainTopic
      const requestTopic = decryptedData.requestTopic

      mqttClient = mqtt.connect(brokerUrl, options)

      mqttClient.on("connect", () => {
        console.log("MQTT Connected")
        mqttConnected = true

        // Subscribe to main data topic
        mqttClient.subscribe(topic, (err) => {
          if (err) {
            console.error("Subscribe error:", err)
          } else {
            console.log("Subscribed to", topic)
          }
        })

        // Subscribe to request response topic
        mqttClient.subscribe(requestTopic, (err) => {
          if (err) {
            console.error("Subscribe error for request topic:", err)
          } else {
            console.log("Subscribed to", requestTopic)
          }
        })

        // Request data immediately after connection is established
        requestMqttUpdate()
      })

      mqttClient.on("message", (topic, message) => {
        if (topic === decryptedData.mainTopic) {
          // Process data message
          const data = message.toString()
          const fields = data.split(";").map((item) => item.trim()) // Split and trim
          const mqttData = {
            mode: fields[0],
            inPressure: fields[1],
            outPressure1: fields[2],
            outPressure2: fields[3],
            totalFlowrate:
              Number.parseFloat(fields[5] || 0) +
              Number.parseFloat(fields[8] || 0) +
              Number.parseFloat(fields[11] || 0) +
              Number.parseFloat(fields[14] || 0) +
              Number.parseFloat(fields[17] || 0) +
              Number.parseFloat(fields[20] || 0),
            motors: [
              { status: fields[4], flowRate: fields[5], time: fields[6] },
              { status: fields[7], flowRate: fields[8], time: fields[9] },
              { status: fields[10], flowRate: fields[11], time: fields[12] },
              { status: fields[13], flowRate: fields[14], time: fields[15] },
              { status: fields[16], flowRate: fields[17], time: fields[18] },
              { status: fields[19], flowRate: fields[20], time: fields[21] },
            ],
            platforms: [
              { status: fields[22], time: fields[23] },
              { status: fields[24], time: fields[25] },
              { status: fields[26], time: fields[27] },
              { status: fields[28], time: fields[29] },
              { status: fields[30], time: fields[31] },
              { status: fields[32], time: fields[33] },
            ],
            dateTime: fields[34],
            crc: fields[35],
          }
          const mqttDataVisual = {
            motors: [
              { status: fields[4] },
              { status: fields[7] },
              { status: fields[10] },
              { status: fields[13] },
              { status: fields[16] },
              { status: fields[19] },
            ],
            platforms: [
              { status: fields[22] },
              { status: fields[24] },
              { status: fields[26] },
              { status: fields[28] },
              { status: fields[30] },
              { status: fields[32] },
            ],
          }

          // Update active motors and platforms based on MQTT data
          updateSystemFromMqtt(mqttDataVisual)
          updateDashboardWithMqttData(mqttData)
        } else if (topic === decryptedData.requestTopic) {
          // Handle response to our request if needed
          console.log("Request acknowledged by publisher")
        }
      })

      mqttClient.on("close", () => {
        console.log("MQTT Connection lost")
        mqttConnected = false
      })

      mqttClient.on("error", (error) => {
        console.error("MQTT Error:", error)
        mqttConnected = false
      })
    },
    error: (xhr, status, error) => {
      console.error(`Error fetching MQTT credentials:`, error)
      mqttConnected = false
    },
  })
}
document.addEventListener("DOMContentLoaded", () => {


  // Position branch pipes and create valves
  positionBranchPipes();
  createValves();

  // Ensure visibility of all elements
  ensureValvesAndPipesVisibility();

  // Make motors clickable
  document.querySelectorAll(".motor").forEach((motor, i) => {
    const motorIndex = i + 1;
    motor.style.cursor = "pointer";
    motor.addEventListener("click", () => toggleMotor(motorIndex));
  });

  // Make platforms clickable
  document.querySelectorAll(".platform").forEach((platform, i) => {
    const platformIndex = i + 1;
    platform.style.cursor = "pointer";
    platform.addEventListener("click", () => togglePlatform(platformIndex));
  });

  // Make valves clickable
  document.querySelectorAll(".valve").forEach((valve) => {
    const valveIndex = parseInt(valve.getAttribute("data-valve"));
    valve.style.cursor = "pointer";
    valve.addEventListener("click", () => togglePlatform(valveIndex));
  });

  // Set initial connection status
  const connectionStatus = document.getElementById("connection_status");
  if (connectionStatus) {
    connectionStatus.innerHTML = "Connected";
    connectionStatus.style.color = "green";
  }

  // Set initial date/time
  // const dateTimeElement = document.getElementById("auto_update_date_time");
  // if (dateTimeElement) {
  //   const now = new Date();
  //   dateTimeElement.textContent = now.toLocaleString();
  // }

  // Simulate some active motors and platforms for demonstration
  toggleMotor(1);
  toggleMotor(3);
  togglePlatform(1);
  togglePlatform(4);

  // Update the animation
  updateAnimation();
});
// Modify DOMContentLoaded event handler to detect page refresh
document.addEventListener("DOMContentLoaded", () => {
  // Generate tabs and content
  generateMotorTabsAndContent()
  createValves()
  // Connect to MQTT
  connectMqtt()

  const activeTab = document.querySelector(".tab-content.active")
  if (activeTab) {
    const activeMotorTab = activeTab.id
    const motorNumber = activeMotorTab.replace("motor_", "")
    fetchAndUpdateMotorDetails(activeMotorTab, motorNumber)
  }

  // Setup reconnection mechanism
  setInterval(() => {
    if (!mqttConnected && mqttClient) {
      console.log("Attempting to reconnect MQTT...")
      mqttClient.end(true)
      connectMqtt()
    }
  }, 60000) // Try to reconnect every minute if disconnected
})

// Add event listener for page visibility changes to detect when user returns to the page
document.addEventListener("visibilitychange", () => {
  if (document.visibilityState === "visible") {
    console.log("Page is now visible, requesting data update")
    requestMqttUpdate()
  }
})

// Listen for page refresh events using beforeunload and performance navigation
window.addEventListener("beforeunload", () => {
  // Store timestamp to detect page refresh
  sessionStorage.setItem("lastUnload", Date.now())
})

// Check if this is a page refresh by comparing timestamps
if (
  (performance.navigation && performance.navigation.type === 1) ||
  (sessionStorage.getItem("lastUnload") && Date.now() - sessionStorage.getItem("lastUnload") < 3000)
) {
  console.log("Page was refreshed")
  mqttReconnect()
}

// Update dashboard with MQTT data
function updateDashboardWithMqttData(data) {
  // Update Operation Mode
  var mode = ""
  if (data.mode === "2") {
    mode = "AUTO"
  } else if (data.mode === "1") {
    mode = "OEM"
  } else if (data.mode === "0") {
    mode = "OFF"
  }

  const operationModeDisplay = $("#operation-mode-display")
  if (operationModeDisplay.length > 0) {
    operationModeDisplay.text(mode)

    // Add pulse dot if not already present
    if (!operationModeDisplay.find(".pulse-dots").length) {
      operationModeDisplay.append('<span class="pulse-dots"></span>')
    }
  }

  const modeTextMap = {
    2: "Automatic Operation",
    1: "OEM Operation",
    0: "NO Operation",
  }

  const operationModeSubtitle = $("#operation-mode-subtitle")
  if (operationModeSubtitle.length > 0) {
    operationModeSubtitle.text(modeTextMap[data.mode] || data.mode)
  }

  // Update Inlet Pressure/Level
  updateInletPressure(Number.parseFloat(data.inPressure) >= 0.1)

  // Update Outlet Pressure
  const outletPressure1 = $("#outlet-pressure-1")
  if (outletPressure1.length > 0) {
    outletPressure1.text(data.outPressure1 + " kg/cm²")
  }

  const outletPressure2 = $("#outlet-pressure-2")
  if (outletPressure2.length > 0) {
    outletPressure2.text(data.outPressure2 + " kg/cm²")
  }

  const totalFlowrate = $("#total-flowrate")
  if (totalFlowrate.length > 0) {
    totalFlowrate.text(data.totalFlowrate + " L/min")
  }

  // Update Motors
  for (let i = 0; i < motorConfig.count; i++) {
    const motorData = data.motors[i]
    const motorNumber = i + 1

    const motorStatus = motorData.status === "1" ? "ON" : "OFF"
    const flowRate = motorData.flowRate
    const runningTime = motorData.time

    const motorImageSrc =
      motorStatus === "ON" ? "../assets/photos/scr_images/on_motor.png" : "../assets/photos/scr_images/off_motor.png"

    const motorStatusElement = $(`#motor-${motorNumber}-status`)
    if (motorStatusElement.length > 0) {
      motorStatusElement.empty()
      motorStatusElement.attr("class", motorStatus === "ON" ? "badge-success" : "badge-danger").html(`
                    <div class="status-container">
                        ${motorStatus === "ON" ? '<span class="pulse-dot"></span>' : ""}
                        <div class="status-content">
                            <img src="${motorImageSrc}" style="height: 40px;width:50px;margin-right:5px"> ${motorStatus}
                        </div>
                    </div>
                `)
    }

    const motorFlow = $(`#motor-${motorNumber}-flow`)
    if (motorFlow.length > 0) {
      motorFlow
        .text(`Flow Rate: ${flowRate} L/min`)
        .attr("class", motorStatus === "ON" ? "flow-value" : "flow-value flow-inactive")
    }

    const motorRuntime = $(`#motor-${motorNumber}-runtime`)
    if (motorRuntime.length > 0) {
      motorRuntime.text(`${runningTime} min`)
    }
  }

  // Update Platforms
  const platformMappings = [
    { index: 0, id: "platform-1-2" },
    { index: 1, id: "platform-3-4" },
    { index: 2, id: "platform-5-6" },
    { index: 3, id: "platform-7" },
    { index: 4, id: "platform-8" },
    { index: 5, id: "platform-9-10" },
  ]

  platformMappings.forEach(({ index, id }) => {
    const platformData = data.platforms[index]
    const status = platformData.status === "1" ? "Open" : "Closed"
    const time = platformData.time

    const platformImageSrc =
      status === "Open" ? "../assets/photos/scr_images/green.png" : "../assets/photos/scr_images/valvecopy.png"

    const platformElement = $(`#${id}-status`)
    if (platformElement.length > 0) {
      platformElement.empty()
      platformElement.attr("class", status === "Open" ? "badge-success" : "badge-danger").html(`
                    <div class="status-container">
                        ${status === "Open" ? '<span class="pulse-dot"></span>' : ""}
                        <div class="status-content" style="display: flex; align-items: center; gap: 0;">
                            <img src="${platformImageSrc}" style="height: 60px; width: 60px;" />
                            <span style="font-size: 18px; font-weight: bold; margin-left: ${status === "Open" ? "-15px" : "0"}; margin-top: ${status === "Open" ? "15px" : "20px"};">
                                ${status}
                            </span>
                        </div>
                    </div>
                `)
    }

    const platformTime = $(`#${id}-time`)
    if (platformTime.length > 0) {
      platformTime.text(`${time} min`)
    }
  })

  // Update Date-Time
  const updateDateTime = $("#auto_update_date_time")
  if (updateDateTime.length > 0) {
    updateDateTime.text(data.dateTime)
  }
}

// Initialize the visualization elements

// Get DOM elements
const motorRow = document.getElementById("motorRow")
const mainPipe = document.getElementById("mainPipe")
const platformRow = document.getElementById("platformRow")
const motorButtons = document.getElementById("motorButtons")
const mqttStatus = document.getElementById("mqtt-status")

document.addEventListener("DOMContentLoaded", () => {
  // Initialize branch pipes positions - this is critical for visibility
  positionBranchPipes()

  // Create valves if they don't exist yet
  createValves()
  // Connect to MQTT



  // Make motors clickable
  document.querySelectorAll(".motor").forEach((motor, i) => {
    const motorIndex = i + 1
    motor.style.cursor = "pointer"
    motor.addEventListener("click", () => toggleMotor(motorIndex))
  })

  // Make platforms clickable
  document.querySelectorAll(".platform").forEach((platform, i) => {
    const platformIndex = i + 1
    platform.style.cursor = "pointer"
    platform.addEventListener("click", () => togglePlatform(platformIndex))
  })

  // Make valves clickable
  document.querySelectorAll(".valve").forEach((valve) => {
    const valveIndex = Number.parseInt(valve.getAttribute("data-valve"))
    valve.style.cursor = "pointer"
    valve.addEventListener("click", () => togglePlatform(valveIndex))
  })
})

// Make sure branch pipes are always visible by modifying the positionBranchPipes function
function positionBranchPipes() {
  document.querySelectorAll(".branch").forEach((branch, i) => {
    const branchIndex = i + 1
    // Calculate positions based on the container width
    const position = branchIndex * 16.6 - 8.3 // Distributed evenly
    branch.style.left = `${position}%`

    // Ensure branches are always visible
    branch.style.display = "block"
    branch.style.visibility = "visible"
    branch.style.opacity = "1"
    branch.style.zIndex = "20" // Set z-index to ensure visibility
  })
}

// Remove the existing addValveStyles function if it exists in your code

// Replace the createValves function with this simplified version
function createValves() {
  // Check if valves already exist
  const existingValves = document.querySelectorAll(".valve");
  if (existingValves.length === 6) {
    // Make sure existing valves are visible with proper z-index
    existingValves.forEach((valve) => {
      valve.style.display = "block";
      valve.style.zIndex = "30"; // Higher z-index to ensure visibility
    });
    return;
  }

  // Remove existing valves if they're not complete
  existingValves.forEach((valve) => valve.remove());

  // Create valves for each branch pipe
  document.querySelectorAll(".branch").forEach((branch, i) => {
    const branchIndex = i + 1;
    const systemLayout = document.querySelector(".system-layout");

    if (!systemLayout) return;

    // Create valve element
    const valve = document.createElement("div");
    valve.className = `valve valve-${branchIndex}`;
    valve.dataset.valve = branchIndex;

    // Position based on branch index
    const position = branchIndex * 16.6 - 8.3;
    valve.style.left = `${position}%`;
    valve.style.top = "75px";
    valve.style.display = "block";
    valve.style.zIndex = "30"; // Higher z-index to ensure visibility

    // Add valve to the system layout
    systemLayout.appendChild(valve);

    // Create or update flow indicator
    let flowIndicator = document.querySelector(`.flow-indicator-${branchIndex}`);
    if (!flowIndicator) {
      flowIndicator = document.createElement("div");
      flowIndicator.className = `flow-indicator flow-indicator-${branchIndex}`;
      flowIndicator.style.left = `${position}%`;
      flowIndicator.style.top = "40px";
      flowIndicator.style.zIndex = "40"; // Even higher z-index
      systemLayout.appendChild(flowIndicator);
    }

    flowIndicator.textContent = "0 L/min";
  });

  // Add event listeners to valves
  document.querySelectorAll(".valve").forEach((valve) => {
    const valveIndex = parseInt(valve.dataset.valve);
    valve.style.cursor = "pointer";
    valve.addEventListener("click", () => togglePlatform(valveIndex));
  });
}

// Replace the updateAnimation function with this simplified version


// Add this to your DOMContentLoaded event handler
document.addEventListener("DOMContentLoaded", () => {
  // Initialize branch pipes positions - this is critical for visibility
  positionBranchPipes()

  // Create valves if they don't exist yet
  createValves()

  // Add CSS to ensure valves and pipes are always visible
  const styleElement = document.createElement("style")
  styleElement.id = "visibility-fix-styles"
  styleElement.textContent = `
    /* Critical visibility fixes */
    .branch {
      display: block !important;
      visibility: visible !important;
      opacity: 1 !important;
      z-index: 20 !important;
    }
    
    .valve {
      display: block !important;
      visibility: visible !important;
      opacity: 1 !important;
      z-index: 30 !important;
    }
    
    .flow-indicator {
      z-index: 40 !important;
    }
    
    /* Fix for platform z-index to ensure it doesn't overlap valves */
    .platform {
      z-index: 10 !important;
    }
    
    .platform-wrapper {
      z-index: 10 !important;
    }
  `
  document.head.appendChild(styleElement)

  // Make motors clickable
  document.querySelectorAll(".motor").forEach((motor, i) => {
    const motorIndex = i + 1
    motor.style.cursor = "pointer"
    motor.addEventListener("click", () => toggleMotor(motorIndex))
  })

  // Make platforms clickable
  document.querySelectorAll(".platform").forEach((platform, i) => {
    const platformIndex = i + 1
    platform.style.cursor = "pointer"
    platform.addEventListener("click", () => togglePlatform(platformIndex))
  })

  // Make valves clickable
  document.querySelectorAll(".valve").forEach((valve) => {
    const valveIndex = Number.parseInt(valve.getAttribute("data-valve"))
    valve.style.cursor = "pointer"
    valve.addEventListener("click", () => togglePlatform(valveIndex))
  })
  ensureValvesAndPipesVisibility();
})
function toggleMotor(motorIndex) {
  const index = activeMotors.indexOf(motorIndex);

  if (index === -1) {
    // Activate motor
    activeMotors.push(motorIndex);
  } else {
    // Deactivate motor
    activeMotors.splice(index, 1);
  }

  updateAnimation();
  // Ensure valves and pipes remain visible after animation update
  ensureValvesAndPipesVisibility();
}

// Toggle platform state
function togglePlatform(platformIndex) {
  const index = activePlatforms.indexOf(platformIndex);

  if (index === -1) {
    // Activate platform
    activePlatforms.push(platformIndex);
  } else {
    // Deactivate platform
    activePlatforms.splice(index, 1);
  }

  updateAnimation();
  // Ensure valves and pipes remain visible after animation update
  ensureValvesAndPipesVisibility();
}

// Update animation based on active motors and platforms
function updateAnimation() {
  // Update motor animations
  document.querySelectorAll(".motor").forEach((motor, i) => {
    const motorIndex = i + 1;
    if (activeMotors.includes(motorIndex)) {
      motor.classList.add("active");
    } else {
      motor.classList.remove("active");
    }
  });

  // Update motor connector animations
  document.querySelectorAll(".motor-connector").forEach((connector, i) => {
    const connectorIndex = i + 1;
    if (activeMotors.includes(connectorIndex)) {
      connector.classList.add("active");
    } else {
      connector.classList.remove("active");
    }
  });

  // Main pipe should be active if any motor is active
  const mainPipe = document.getElementById("mainPipe");
  if (activeMotors.length > 0) {
    mainPipe.classList.add("active");
  } else {
    mainPipe.classList.remove("active");
  }

  // Update branch pipes and valves
  document.querySelectorAll(".branch").forEach((branch, i) => {
    const branchIndex = i + 1;

    // CRITICAL: Always ensure branch pipes are visible regardless of state
    branch.style.display = "block";
    branch.style.visibility = "visible";
    branch.style.opacity = "1";

    const valve = document.querySelector(`.valve-${branchIndex}`);
    const flowIndicator = document.querySelector(`.flow-indicator-${branchIndex}`);

    if (activePlatforms.includes(branchIndex) && activeMotors.length > 0) {
      branch.classList.add("active");

      // Update valve state - keep visible
      if (valve) {
        valve.classList.add("open");
        valve.style.display = "block";
      }

      // Update flow indicator
      if (flowIndicator) {
        // Get the flow rate from the corresponding motor or use a default value
        let flowRate = 0;
        const motorData = activeMotors.map((motorIndex) => {
          const flowElement = document.getElementById(`motor-${motorIndex}-flow`);
          if (flowElement) {
            const flowText = flowElement.textContent;
            const match = flowText.match(/(\d+(\.\d+)?)/);
            return match ? parseFloat(match[0]) : 0;
          }
          return 0;
        });

        // Calculate average flow rate from active motors
        if (motorData.length > 0) {
          flowRate = Math.round(motorData.reduce((sum, flow) => sum + flow, 0) / motorData.length);
        }

        flowIndicator.textContent = `${flowRate} L/min`;
        flowIndicator.style.opacity = "1";
      }
    } else {
      branch.classList.remove("active");

      // Update valve state - keep visible but closed
      if (valve) {
        valve.classList.remove("open");
        valve.style.display = "block"; // Keep valve visible
      }

      // Reset flow indicator but keep it visible
      if (flowIndicator) {
        flowIndicator.textContent = "0 L/min";
        flowIndicator.style.opacity = "0.5";
      }
    }
  });
  document.querySelectorAll(".platform-fill").forEach((fill, i) => {
    const platformIndex = i + 1;
    const platform = fill.parentElement;

    if (activePlatforms.includes(platformIndex) && activeMotors.length > 0) {
      fill.style.animation = "fillAnimation 4s forwards";

      // Add splash effect
      const existingSplash = platform.querySelector(".splash");
      if (existingSplash) existingSplash.remove();

      setTimeout(() => {
        const splash = document.createElement("div");
        splash.className = "splash";
        platform.appendChild(splash);
      }, 1000);
    } else {
      fill.style.animation = "none";
      fill.style.height = "0%";

      const oldSplash = platform.querySelector(".splash");
      if (oldSplash) oldSplash.remove();
    }
  });

  // Update motor status displays
  activeMotors.forEach(motorIndex => {
    const statusElement = document.getElementById(`motor-${motorIndex}-status`);
    if (statusElement) {
      statusElement.className = "badge-success";
      statusElement.innerHTML = `<img src="https://via.placeholder.com/50x40" style="height: 40px;width:50px;margin-right:5px"> ON`;

      // Update flow rate
      const flowElement = document.getElementById(`motor-${motorIndex}-flow`);
      if (flowElement) {
        flowElement.className = "flow-value";
        flowElement.textContent = `Flow Rate: ${Math.floor(Math.random() * 50) + 50} L/min`;
      }

      // Update runtime
      const runtimeElement = document.getElementById(`motor-${motorIndex}-runtime`);
      if (runtimeElement) {
        runtimeElement.textContent = `${Math.floor(Math.random() * 60) + 10} min`;
      }
    }
  });

  // Update platform status displays
  activePlatforms.forEach(platformIndex => {
    let statusElementId;
    switch (platformIndex) {
      case 1: statusElementId = "platform-1-2-status"; break;
      case 2: statusElementId = "platform-3-4-status"; break;
      case 3: statusElementId = "platform-5-6-status"; break;
      case 4: statusElementId = "platform-7-status"; break;
      case 5: statusElementId = "platform-8-status"; break;
      case 6: statusElementId = "platform-9-10-status"; break;
    }

    const statusElement = document.getElementById(statusElementId);
    if (statusElement) {
      statusElement.className = "badge-success";
      statusElement.innerHTML = `<img src="https://via.placeholder.com/50x60" style="height: 60px;width:50px;margin-right:5px"> Open`;

      // Update time
      const timeElementId = statusElementId.replace("-status", "-time");
      const timeElement = document.getElementById(timeElementId);
      if (timeElement) {
        timeElement.textContent = `${Math.floor(Math.random() * 60) + 10} min`;
      }
    }
  });
}
function ensureValvesAndPipesVisibility() {
  // Force branch pipes to be visible with high z-index
  document.querySelectorAll(".branch").forEach((branch) => {
    branch.style.display = "block";
    branch.style.visibility = "visible";
    branch.style.opacity = "1";
    branch.style.zIndex = "20";
  });

  // Force valves to be visible with even higher z-index
  document.querySelectorAll(".valve").forEach((valve) => {
    valve.style.display = "block";
    valve.style.visibility = "visible";
    valve.style.opacity = "1";
    valve.style.zIndex = "30";
  });

  // Force flow indicators to be visible with highest z-index
  document.querySelectorAll(".flow-indicator").forEach((indicator) => {
    indicator.style.zIndex = "40";
  });

  // Make sure platforms have lower z-index
  document.querySelectorAll(".platform, .platform-wrapper").forEach((platform) => {
    platform.style.zIndex = "10";
  });
}
// Update system visualization from MQTT data
function updateSystemFromMqtt(mqttData) {
  if (!mqttData) return

  // Update active motors array based on MQTT data
  activeMotors = []
  if (mqttData.motors) {
    mqttData.motors.forEach((motor, index) => {
      if (motor.status === "1") {
        activeMotors.push(index + 1)
      }
    })
  }

  // Update active platforms array based on MQTT data
  activePlatforms = []
  if (mqttData.platforms) {
    mqttData.platforms.forEach((platform, index) => {
      if (platform.status === "1") {
        activePlatforms.push(index + 1)
      }
    })
  }

  // Update visualization
  updateAnimation();
  ensureValvesAndPipesVisibility();
}

// Add jQuery and MQTT.js imports
const script1 = document.createElement("script")
script1.src = "https://code.jquery.com/jquery-3.6.0.min.js"
script1.onload = () => {
  const script2 = document.createElement("script")
  script2.src = "https://unpkg.com/mqtt@4.10.0/dist/mqtt.min.js"
  document.head.appendChild(script2)
}
document.head.appendChild(script1)
function ensureValvesAndPipesVisibility() {
  // Force branch pipes to be visible with high z-index
  document.querySelectorAll(".branch").forEach((branch) => {
    branch.style.display = "block"
    branch.style.visibility = "visible"
    branch.style.opacity = "1"
    branch.style.zIndex = "20"
  })

  // Force valves to be visible with even higher z-index
  document.querySelectorAll(".valve").forEach((valve) => {
    valve.style.display = "block"
    valve.style.visibility = "visible"
    valve.style.opacity = "1"
    valve.style.zIndex = "30"
  })

  // Force flow indicators to be visible with highest z-index
  document.querySelectorAll(".flow-indicator").forEach((indicator) => {
    indicator.style.zIndex = "40"
  })

  // Make sure platforms have lower z-index
  document.querySelectorAll(".platform, .platform-wrapper").forEach((platform) => {
    platform.style.zIndex = "10"
  })
}