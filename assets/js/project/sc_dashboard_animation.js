   const baseAnimationSpeed = 100; // Base speed value
const animationDuration = 2; // seconds
const flowAnimationClass = 'water-flow';

// Color constants
const colorMotorActive = "#38A169";   // Active motor - blue
const colorMotorInactive = "#E53E3E"; // Inactive motor - gray
const colorPipeActive = "#3182ce";    // Active pipe - blue
const colorPipeInactive = "#cbd5e0";  // Inactive pipe - light gray
const colorValveActive = "#38A169";   // Active valve - teal
const colorValveInactive = "#E53E3E"; // Inactive valve - light red 
const colorPlatformActive = "#4299e1"; // Active platform - blue
const colorPlatformInactive = "#e2e8f0"; // Inactive platform - very light gray

// MQTT variables
let mqttClients;

// State management
const state = {
    motors: [
        { id: 1, name: "Motor 1", isOn: false },
        { id: 2, name: "Motor 2", isOn: false },
        { id: 3, name: "Motor 3", isOn: false },
        { id: 4, name: "Motor 4", isOn: false },
        { id: 5, name: "Motor 5", isOn: false },
        { id: 6, name: "Motor 6", isOn: false }
    ],
    valves: [
        { id: 1, name: "Valve 1", isOpen: false, platformId: 1 },
        { id: 2, name: "Valve 2", isOpen: false, platformId: 2 },
        { id: 3, name: "Valve 3", isOpen: false, platformId: 3 },
        { id: 4, name: "Valve 4", isOpen: false, platformId: 4 },
        { id: 5, name: "Valve 5", isOpen: false, platformId: 5 },
        { id: 6, name: "Valve 6", isOpen: false, platformId: 6 }
    ]
};

// XOR Decryption function
function xor_decrypt(encoded, key) {
    try {
        const decoded = atob(encoded);
        let result = '';

        for (let i = 0; i < decoded.length; i++) {
            result += String.fromCharCode(decoded.charCodeAt(i) ^ key.charCodeAt(i % key.length));
        }

        return JSON.parse(result);
    } catch (error) {
        console.error('Decryption failed:', error);
        return null;
    }
}
var inletPressure = 0.0;
// MQTT Connection function
function mqttConnection() {
    $.ajax({
        type: 'POST',
        url: '../common-files/get_mqtt_credentials.php',
        dataType: 'json',
        success: function (response) {
            const consoledata = 'consoledata';
            const decryptedData = xor_decrypt(response.data, consoledata);

            if (!decryptedData) {
                // console.error('Failed to decrypt MQTT credentials');
                return;
            }

            const options = {
                username: decryptedData.username,
                password: decryptedData.password,
                reconnectPeriod: decryptedData.reconnectPeriod,
                connectTimeout: decryptedData.connectTimeout,
                clean: decryptedData.clean,
            };

            // Get broker URL and topics from PHP response instead of hardcoding
            const brokerUrl = decryptedData.brokerUrl;
            const topic = decryptedData.mainTopic;
            const requestTopic = decryptedData.requestTopic;

            mqttClients = mqtt.connect(brokerUrl, options);

            mqttClients.on('connect', function () {
                // console.log('MQTT Connected');

                // Subscribe to main data topic
                mqttClients.subscribe(topic, function (err) {
                    if (err) {
                        // console.error('Subscribe error:', err);
                    } else {
                        // console.log('Subscribed to', topic);
                    }
                });

                // Subscribe to request response topic
                mqttClients.subscribe(requestTopic, function (err) {
                    if (err) {
                        // console.error('Subscribe error for request topic:', err);
                    } else {
                        // console.log('Subscribed to', requestTopic);
                    }
                });
            });

            mqttClients.on('message', function (topic, message) {
                // console.log("topic " + topic + " Message Data" + message);
                // console.log("Decrypted Topic:" + decryptedData.mainTopic);
                if (topic === decryptedData.mainTopic) {
                    // Process data message
                    let data = message.toString();
                    let fields = data.split(';').map(item => item.trim()); // Split and trim
                     inletPressure = fields[1];
                    const mqttDataVisual = {
                        motors: [
                            { status: fields[4] },
                            { status: fields[7] },
                            { status: fields[10] },
                            { status: fields[13] },
                            { status: fields[16] },
                            { status: fields[19] }
                        ],
                        platforms: [
                            { status: fields[22] },
                            { status: fields[24] },
                            { status: fields[26] },
                            { status: fields[28] },
                            { status: fields[30] },
                            { status: fields[32] }
                        ]
                    };

                    // Update motor states based on MQTT data
                    mqttDataVisual.motors.forEach((motorData, index) => {
                        if (state.motors[index]) {
                            state.motors[index].isOn = motorData.status === "1";
                        }
                    });

                    // Update valve states based on platform data
                    mqttDataVisual.platforms.forEach((platformData, index) => {
                        if (state.valves[index]) {
                            state.valves[index].isOpen = platformData.status === "1";
                        }
                    });

                    // Update the visualization to reflect the new state
                    updateVisualization();
                }
                else if (topic === decryptedData.requestTopic) {
                    // Handle response to our request if needed
                    // console.log('Request acknowledged by publisher');
                }
            });

            mqttClients.on('close', function () {
                // console.log('MQTT Connection lost');
            });

            mqttClients.on('error', function (error) {
                // console.error('MQTT Error:', error);
            });
        },
        error: function (xhr, status, error) {
            // console.error(`Error fetching MQTT credentials:`, error);
        }
    });
}

// Helper functions
function isAnyMotorOn() {
    return state.motors.some(motor => motor.isOn);
}

function getActiveMotorCount() {
    return state.motors.filter(motor => motor.isOn).length;
}

function calculateAnimationSpeed() {
    const activeMotors = getActiveMotorCount();
    return baseAnimationSpeed + (activeMotors * 20);
}

function getAnimationDuration() {
    const speed = calculateAnimationSpeed();
    // Convert speed to duration - higher speed means shorter duration
    return (baseAnimationSpeed / speed) * animationDuration;
}

function updateAnimationSpeed() {
    const duration = getAnimationDuration();

    // Update CSS custom properties for animation duration
    const style = document.createElement('style');
    style.textContent = `
        .water-flow {
            animation-duration: ${duration}s;
        }
        .water-flow-main {
            animation-duration: ${duration}s;
        }
        .water-flow-short {
            animation-duration: ${duration}s;
        }
    `;

    // Remove previous dynamic style if it exists
    const existingStyle = document.getElementById('dynamic-animation-style');
    if (existingStyle) {
        existingStyle.remove();
    }

    style.id = 'dynamic-animation-style';
    document.head.appendChild(style);
}

function getFlowDashArray(type) {
    switch (type) {
        case 'main':
            return '20 20'; // Long dashes for main pipe
        case 'short':
            return '10 10'; // Medium dashes for short pipes
        case 'long':
            return '15 15'; // Medium-long dashes for long pipes
        default:
            return '10 10';
    }
}

function setElementVisibility(id, visible) {
    const element = document.getElementById(id);
    if (element) {
        element.style.display = visible ? '' : 'none';
    }
}

function toggleMotor(motorId) {
    const motor = state.motors.find(m => m.id === motorId);
    if (motor) {
        motor.isOn = !motor.isOn;
        updateVisualization();
    }
}

function toggleValve(valveId) {
    const valve = state.valves.find(v => v.id === valveId);
    if (valve) {
        valve.isOpen = !valve.isOpen;
        updateVisualization();
    }
}

// Create SVG elements
function createSVG() {
    const svg = document.getElementById('water-system-svg');

    
    svg.innerHTML = ''; // Clear existing content

    // Draw motors (reduced size)
    state.motors.forEach((motor, index) => {
        const x = 100 + index * 100;
        const y = 80;

        // Create motor group
        const motorGroup = document.createElementNS('http://www.w3.org/2000/svg', 'g');
        motorGroup.setAttribute('id', `motor-${motor.id}`);

        // Motor body (reduced from 60x50 to 45x38)
        const motorRect = document.createElementNS('http://www.w3.org/2000/svg', 'rect');
        motorRect.setAttribute('x', x - 22.5);
        motorRect.setAttribute('y', y - 19);
        motorRect.setAttribute('width', '45');
        motorRect.setAttribute('height', '38');
        motorRect.setAttribute('rx', '4');
        motorRect.setAttribute('fill', motor.isOn ? colorMotorActive : colorMotorInactive);
        motorRect.setAttribute('stroke', '#2d3748');
        motorRect.setAttribute('stroke-width', '2');
        motorRect.setAttribute('class', 'motor-rect');
        motorRect.setAttribute('id', `motor-rect-${motor.id}`);

        // Motor label (adjusted font size)
        const motorText = document.createElementNS('http://www.w3.org/2000/svg', 'text');
        motorText.setAttribute('x', x);
        motorText.setAttribute('y', y + 3);
        motorText.setAttribute('text-anchor', 'middle');
        motorText.setAttribute('fill', 'white');
        motorText.setAttribute('font-weight', 'bold');
        motorText.setAttribute('font-size', '9');
        motorText.textContent = `Motor ${motor.id}`;

        // Indicator light (reduced size)
        const indicatorCircle = document.createElementNS('http://www.w3.org/2000/svg', 'circle');
        indicatorCircle.setAttribute('cx', x + 15);
        indicatorCircle.setAttribute('cy', y - 12);
        indicatorCircle.setAttribute('r', '4');
        indicatorCircle.setAttribute('fill', motor.isOn ? '#38a169' : '#e53e3e');
        indicatorCircle.setAttribute('id', `motor-indicator-${motor.id}`);

        // Pipe structure from motor to main pipe (always visible)
        const pipePath = document.createElementNS('http://www.w3.org/2000/svg', 'path');
        pipePath.setAttribute('d', `M${x} ${y + 19} L${x} ${y + 50}`);
        pipePath.setAttribute('stroke', colorPipeInactive);
        pipePath.setAttribute('stroke-width', '8');
        pipePath.setAttribute('fill', 'none');
        pipePath.setAttribute('stroke-linecap', 'round');

        // Water flow animation (conditionally visible)
        const waterFlowPath = document.createElementNS('http://www.w3.org/2000/svg', 'path');
        waterFlowPath.setAttribute('d', `M${x} ${y + 19} L${x} ${y + 50}`);
        waterFlowPath.setAttribute('stroke', colorPipeActive);
        waterFlowPath.setAttribute('stroke-width', '6');
        waterFlowPath.setAttribute('fill', 'none');
        waterFlowPath.setAttribute('stroke-linecap', 'round');
        waterFlowPath.setAttribute('stroke-dasharray', getFlowDashArray('short'));
        waterFlowPath.setAttribute('id', `motor-flow-${motor.id}`);
        waterFlowPath.setAttribute('class', 'water-flow-short');
        waterFlowPath.style.display = motor.isOn ? '' : 'none';

        // Add all elements to motor group
        motorGroup.appendChild(pipePath);
        motorGroup.appendChild(waterFlowPath);
        motorGroup.appendChild(motorRect);
        motorGroup.appendChild(motorText);
        motorGroup.appendChild(indicatorCircle);

        // Add motor group to SVG
        svg.appendChild(motorGroup);
    });

    // Main pipe (always visible)
    const mainPipe = document.createElementNS('http://www.w3.org/2000/svg', 'path');
    mainPipe.setAttribute('d', 'M100 130 L700 130');
    mainPipe.setAttribute('stroke', colorPipeInactive);
    mainPipe.setAttribute('stroke-width', '10');
    mainPipe.setAttribute('fill', 'none');
    mainPipe.setAttribute('stroke-linecap', 'round');
    svg.appendChild(mainPipe);

    // Main pipe water flow (conditionally visible)
    const mainPipeFlow = document.createElementNS('http://www.w3.org/2000/svg', 'path');
    mainPipeFlow.setAttribute('d', 'M100 130 L700 130');
    mainPipeFlow.setAttribute('stroke', colorPipeActive);
    mainPipeFlow.setAttribute('stroke-width', '8');
    mainPipeFlow.setAttribute('fill', 'none');
    mainPipeFlow.setAttribute('stroke-linecap', 'round');
    mainPipeFlow.setAttribute('stroke-dasharray', getFlowDashArray('main'));
    mainPipeFlow.setAttribute('id', 'main-pipe-flow');
    mainPipeFlow.setAttribute('class', 'water-flow-main');
    mainPipeFlow.style.display = 'block'; 
    svg.appendChild(mainPipeFlow);

    // Valves and platforms (both pipe heights set to 35px)
    state.valves.forEach((valve, index) => {
        const x = 130 + index * 110;
        const y = 137; 
        const platformY = 235; // Adjusted platform position to accommodate equal pipe heights

        // Create valve group
        const valveGroup = document.createElementNS('http://www.w3.org/2000/svg', 'g');
        valveGroup.setAttribute('id', `valve-group-${valve.id}`);

        // Pipe from main to valve (static) - height = 35px
        const pipeToValve = document.createElementNS('http://www.w3.org/2000/svg', 'path');
        pipeToValve.setAttribute('d', `M${x} ${y} L${x} ${y + 35}`);
        pipeToValve.setAttribute('stroke', colorPipeInactive);
        pipeToValve.setAttribute('stroke-width', '6');
        pipeToValve.setAttribute('fill', 'none');
        pipeToValve.setAttribute('stroke-linecap', 'round');

        // Water flow to valve (conditionally visible) - height = 35px
        const flowToValve = document.createElementNS('http://www.w3.org/2000/svg', 'path');
        flowToValve.setAttribute('d', `M${x} ${y} L${x} ${y + 35}`);
        flowToValve.setAttribute('stroke', colorPipeActive);
        flowToValve.setAttribute('stroke-width', '4');
        flowToValve.setAttribute('fill', 'none');
        flowToValve.setAttribute('stroke-linecap', 'round');
        flowToValve.setAttribute('stroke-dasharray', getFlowDashArray('short'));
        flowToValve.setAttribute('id', `valve-in-flow-${valve.id}`);
        flowToValve.setAttribute('class', 'water-flow-short');
        flowToValve.style.display = ''; // Always visible

        // Valve (adjusted position)
        const valveCircle = document.createElementNS('http://www.w3.org/2000/svg', 'circle');
        valveCircle.setAttribute('cx', x);
        valveCircle.setAttribute('cy', y + 50);
        valveCircle.setAttribute('r', '15');
        valveCircle.setAttribute('fill', valve.isOpen ? colorValveActive : colorValveInactive);
        valveCircle.setAttribute('stroke', '#2d3748');
        valveCircle.setAttribute('stroke-width', '2');
        valveCircle.setAttribute('class', 'valve-circle');
        valveCircle.setAttribute('id', `valve-circle-${valve.id}`);

        // Valve handle - FIXED POSITIONING
        const valveHandle = document.createElementNS('http://www.w3.org/2000/svg', 'rect');
        const valveCenterX = x;
        const valveCenterY = y + 50;
        
        // Center the handle within the circle
        valveHandle.setAttribute('x', valveCenterX - 2); 
        valveHandle.setAttribute('y', valveCenterY - 10);
        valveHandle.setAttribute('width', '4');
        valveHandle.setAttribute('height', '20');
        valveHandle.setAttribute('fill', '#2d3748');
        valveHandle.setAttribute('rx', '1'); // Slightly rounded corners
        
        // Set rotation around the center of the circle
        if (valve.isOpen) {
            // Vertical position (0 degrees) - handle is already vertical by default
            valveHandle.setAttribute('transform', `rotate(0 ${valveCenterX} ${valveCenterY})`);
        } else {
            // Horizontal position (90 degrees rotation)
            valveHandle.setAttribute('transform', `rotate(90 ${valveCenterX} ${valveCenterY})`);
        }
        
        valveHandle.setAttribute('id', `valve-handle-${valve.id}`);

        // Valve number (adjusted position)
        const valveText = document.createElementNS('http://www.w3.org/2000/svg', 'text');
        valveText.setAttribute('x', x);
        valveText.setAttribute('y', y + 46);
        valveText.setAttribute('text-anchor', 'middle');
        valveText.setAttribute('fill', 'white');
        valveText.setAttribute('font-weight', 'bold');
        valveText.setAttribute('font-size', '7');
        // valveText.textContent = valve.id;
        valveText.textContent = "Valve";

        // Pipe from valve to platform (static) - height = 35px
        const pipeToPlatform = document.createElementNS('http://www.w3.org/2000/svg', 'path');
        pipeToPlatform.setAttribute('d', `M${x} ${y + 65} L${x} ${platformY - 15}`);
        pipeToPlatform.setAttribute('stroke', colorPipeInactive);
        pipeToPlatform.setAttribute('stroke-width', '6');
        pipeToPlatform.setAttribute('fill', 'none');
        pipeToPlatform.setAttribute('stroke-linecap', 'round');

        // Water flow to platform (conditionally visible) - height = 35px
        const flowToPlatform = document.createElementNS('http://www.w3.org/2000/svg', 'path');
        flowToPlatform.setAttribute('d', `M${x} ${y + 65} L${x} ${platformY - 15}`);
        flowToPlatform.setAttribute('stroke', colorPipeActive);
        flowToPlatform.setAttribute('stroke-width', '4');
        flowToPlatform.setAttribute('fill', 'none');
        flowToPlatform.setAttribute('stroke-linecap', 'round');
        flowToPlatform.setAttribute('stroke-dasharray', getFlowDashArray('long'));
        flowToPlatform.setAttribute('id', `platform-flow-${valve.id}`);
        flowToPlatform.setAttribute('class', 'water-flow');
        flowToPlatform.style.display = 'none'; // Initially hidden

        // Platform (reduced size from 70x40 to 55x30)
        const platform = document.createElementNS('http://www.w3.org/2000/svg', 'rect');
        platform.setAttribute('x', x - 27.5);
        platform.setAttribute('y', platformY - 15);
        platform.setAttribute('width', '70');
        platform.setAttribute('height', '30');
        platform.setAttribute('rx', '4');
        platform.setAttribute('fill', colorPlatformInactive);
        platform.setAttribute('stroke', '#2d3748');
        platform.setAttribute('stroke-width', '2');
        platform.setAttribute('id', `platform-${valve.id}`);
   
      


        // Platform label (adjusted font size)
        const platformText = document.createElementNS('http://www.w3.org/2000/svg', 'text');
        platformText.setAttribute('x', x+8);
        platformText.setAttribute('y', platformY + 5);
        platformText.setAttribute('text-anchor', 'middle');
        platformText.setAttribute('fill', '#4a5568');
        platformText.setAttribute('font-weight', 'bold');
        platformText.setAttribute('font-size', '10');
        platformText.setAttribute('id', `platform-text-${valve.id}`);

        if (valve.id === 1) {
            platformText.textContent = "PF-1 & PF-2";
        }
        else if (valve.id === 2) {
            platformText.textContent = "PF-3 & PF-4";
        }
        else if (valve.id === 3) {
            platformText.textContent = "PF-5 & PF-6";
        }
        else if (valve.id === 4) {
            platformText.textContent = "PF-7";
        }
        else if (valve.id === 5) {
            platformText.textContent = "PF-8";
        }
        else if (valve.id === 6) {
            platformText.textContent = "PF-9 & PF-10";
        }

        // Add all elements to valve group
        valveGroup.appendChild(pipeToValve);
        valveGroup.appendChild(flowToValve);
        valveGroup.appendChild(pipeToPlatform);
        valveGroup.appendChild(flowToPlatform);
        valveGroup.appendChild(platform);
        valveGroup.appendChild(platformText);
        valveGroup.appendChild(valveCircle);
        valveGroup.appendChild(valveHandle);
        valveGroup.appendChild(valveText);

        // Add valve group to SVG
        svg.appendChild(valveGroup);
    });
}

// Update the visualization based on current state
function updateVisualization() {
    // console.log("inletPressure"+inletPressure);
    // Update animation speed based on active motors
    updateAnimationSpeed();
    // console.log(inletPressure);
    
    // Update motors
    state.motors.forEach(motor => {
        const motorRect = document.getElementById(`motor-rect-${motor.id}`);
        const motorIndicator = document.getElementById(`motor-indicator-${motor.id}`);
        const motorFlow = document.getElementById(`motor-flow-${motor.id}`);

        if (motorRect) motorRect.setAttribute('fill', motor.isOn ? colorMotorActive : colorMotorInactive);
        if (motorIndicator) motorIndicator.setAttribute('fill', motor.isOn ? '#38a169' : '#e53e3e');
        if (motorFlow) motorFlow.style.display = motor.isOn ? '' : 'none';
    });

    // Update main pipe flow
    const mainPipeFlow = document.getElementById('main-pipe-flow');
    if (parseFloat(inletPressure) >= 0.1) {
        mainPipeFlow.style.display = ''; 
    }
    else{
        mainPipeFlow.style.display = 'none'; 
    }
    
    // Update valves, platforms, and their flows
    state.valves.forEach(valve => {
        const valveCircle = document.getElementById(`valve-circle-${valve.id}`);
        const valveHandle = document.getElementById(`valve-handle-${valve.id}`);
        const valveInFlow = document.getElementById(`valve-in-flow-${valve.id}`);
        const platformFlow = document.getElementById(`platform-flow-${valve.id}`);
        const platform = document.getElementById(`platform-${valve.id}`);
        const platformText = document.getElementById(`platform-text-${valve.id}`);

        const anyMotorOn = isAnyMotorOn();
        const isPassingWater = anyMotorOn && valve.isOpen;

        if (valveCircle) valveCircle.setAttribute('fill', valve.isOpen ? colorValveActive : colorValveInactive);
        
        // FIXED HANDLE ROTATION - using proper center coordinates
        if (valveHandle) {
            const x = 130 + (valve.id - 1) * 110;
            const valveCenterY = 137 + 50; // y + 50 from createSVG function
            
            if (valve.isOpen) {
                // Vertical position (0 degrees)
                valveHandle.setAttribute('transform', `rotate(0 ${x} ${valveCenterY})`);
            } else {
                // Horizontal position (90 degrees)
                valveHandle.setAttribute('transform', `rotate(90 ${x} ${valveCenterY})`);
            }
        }

        // Valve flow visibility based on inlet pressure
        if (parseFloat(inletPressure) >= 0.1) {
            if (valveInFlow) valveInFlow.style.display = ''; 
        } else {
            if (valveInFlow) valveInFlow.style.display = 'none'; 
        }
        
        if (platformFlow) platformFlow.style.display = valve.isOpen ? '' : 'none'; // Only depends on valve state

        if (platform) platform.setAttribute('fill', valve.isOpen ? colorPlatformActive : colorPlatformInactive);
        if (platformText) platformText.setAttribute('fill', isPassingWater ? 'white' : '#4a5568');
    });
}

// Initialize the visualization
function initialize() {
    createSVG();
    updateVisualization();

    // Initialize MQTT connection
    mqttConnection();
}

// Setup event listener for window resize
window.addEventListener('resize', () => {
    // Only recreate if window size significantly changes
    clearTimeout(window.resizeTimer);
    window.resizeTimer = setTimeout(() => {
        createSVG();
        updateVisualization();
    }, 250);
});

// Initialize on page load
document.addEventListener('DOMContentLoaded', () => {
    initialize();
});