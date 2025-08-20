<?php
require_once 'config-path.php';
require_once '../session/session-manager.php';
SessionManager::checkSession();
$sessionVars = SessionManager::SessionVariables();

$mobile_no = $sessionVars['mobile_no'];
$user_id = $sessionVars['user_id'];
$role = $sessionVars['role'];
$user_login_id = $sessionVars['user_login_id'];
$user_name = $sessionVars['user_name'];
$user_email = $sessionVars['user_email'];
?>
<?php
require_once 'config-path.php';
require_once '../session/session-manager.php';
SessionManager::checkSession();
$sessionVars = SessionManager::SessionVariables();

$mobile_no = $sessionVars['mobile_no'];
$user_id = $sessionVars['user_id'];
$role = $sessionVars['role'];
$user_login_id = $sessionVars['user_login_id'];
$user_name = $sessionVars['user_name'];
$user_email = $sessionVars['user_email'];
?>
<!DOCTYPE html>
<html lang="en">

<head>

    <title>Motor to Platform Water Pumping System</title>
    <style>
       
        .layout {
            display: flex;
            flex-direction: column;
            margin-top: 50px;
            position: relative;
        }

        .motor-row {
            display: flex;
            justify-content: space-around;
            width: 30%;
            gap: 10px;
        }

        .motor {
            width: 60px;
            height: 80px;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 10px;
            position: relative;
        }

        .motor-start {
            background: green;
        }

        .motor-stop {
            background: red;
        }

        .motor-connector {
            width: 10px;
            height: 50px;
            background: blue;
            margin: 0 auto;
            position: relative;
        }

        .main-pipe {
            height: 20px;
            background: blue;
            position: relative;
            margin-left: 10px;
            margin-right: 10px;
        }

        .branch {
            position: absolute;
            width: 10px;
            height: 110px;
            background: blue;
            top: 20px;
            transform: translateX(-50%);
            transition: background 0.3s;
        }

        .platform-row {
            display: flex;
            justify-content: space-around;
            width: 100%;
            gap: 10px;
            margin-top: 100px;
        }

        .platform-wrapper {
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .platform {
            width: 60px;
            height: 150px;
            border: 2px solid #333;
            background: white;
            position: relative;
            overflow: hidden;
        }

        .platform-fill {
            position: absolute;
            bottom: 0;
            width: 100%;
            height: 0%;
            transition: height 4s;
        }

        .platform-label {
            margin-top: 5px;
            font-size: 14px;
        }

        .motor-label {
            margin-bottom: 5px;
            font-size: 14px;
        }

        .pipe-flow::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image: linear-gradient(45deg,
                    rgba(255, 255, 255, 0.3) 25%,
                    transparent 25%,
                    transparent 50%,
                    rgba(255, 255, 255, 0.3) 50%,
                    rgba(255, 255, 255, 0.3) 75%,
                    transparent 75%,
                    transparent);
            background-size: 20px 20px;
            animation: flow 1s linear infinite;
        }

        @keyframes fillAnimation {
            from {
                height: 0%;
            }

            to {
                height: 95%;
            }
        }

        @keyframes flow {
            from {
                background-position: 0 0;
            }

            to {
                background-position: 40px 0;
            }
        }

        .splash {
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%) translateY(-40px);
            width: 20px;
            height: 20px;
            background: rgba(0, 134, 179, 0.91);
            border-radius: 50%;
            opacity: 0;
            animation: splashAnim 0.1s ease-out infinite;
            pointer-events: none;
        }

        @keyframes splashAnim {
            0% {
                transform: translateX(-50%) translateY(-120px);
                width: 40px;
                height: 20px;
                opacity: 0.9;
            }

            100% {
                transform: translateX(-50%) translateY(0);
                width: 80px;
                height: 20px;
                opacity: 0;
            }
        }

        .controls {
            margin-bottom: 20px;
        }

        button {
            padding: 8px 15px;
            margin: 5px;
            cursor: pointer;
            background: #4CAF50;
            color: white;
            border: none;
            border-radius: 4px;
        }

        button:hover {
            background: #45a049;
        }

      

        /* .platform-fill {
            background: rgba(0, 134, 179, 0.7);
        } */
    </style>
    <?php
    include(BASE_PATH . "assets/html/start-page.php");
    ?>
    <div class="d-flex flex-column flex-shrink-0 p-3 main-content ">
        <div class="container-fluid">
            <div class="row mb-1 mt-1">
                <div class="col-12 ">
                    <p class="breadcrumb-text text-muted m-0">
                        <i class="bi bi-house-door-fill "></i> Pages / <span class="fw-medium ">Dashboard</span>
                    </p>
                </div>
            </div>
            <h2>Water Pumping System with Motors and Platforms</h2>

            <div id="mqtt-status" class="status-indicator disconnected">
                MQTT Status: Disconnected
            </div>

            <div class="controls" style="display:none">
                <button id="startAll">Start All Motors</button>
                <button id="stopAll">Stop All Motors</button>
                <div id="motorButtons"></div>
            </div>

            <div class="layout">
                <div class="motor-row" id="motorRow"></div>
                <div class="main-pipe" id="mainPipe"></div>
                <div class="platform-row" id="platformRow"></div>
            </div>

            <script src="https://cdnjs.cloudflare.com/ajax/libs/mqtt/5.5.1/mqtt.min.js"></script>
            <script src="https://cdnjs.cloudflare.com/ajax/libs/crypto-js/4.1.1/crypto-js.min.js"></script>

            <script>
                // Initialize components
                const motorRow = document.getElementById('motorRow');
                const mainPipe = document.getElementById('mainPipe');
                const platformRow = document.getElementById('platformRow');
                const motorButtons = document.getElementById('motorButtons');
                const mqttStatus = document.getElementById('mqtt-status');

                // Create motors
                for (let i = 1; i <= 6; i++) {
                    // Motor wrapper
                    const motorWrapper = document.createElement('div');
                    motorWrapper.style.display = 'flex';
                    motorWrapper.style.flexDirection = 'column';
                    motorWrapper.style.alignItems = 'center';

                    // Motor label
                    const motorLabel = document.createElement('div');
                    motorLabel.className = 'motor-label';
                    motorLabel.textContent = `Motor ${i}`;

                    // Motor
                    const motor = document.createElement('div');
                    motor.className = 'motor motor-stop';
                    motor.dataset.motor = i;
                    motor.textContent = `M${i}`;

                    // Motor connector pipe
                    const connector = document.createElement('div');
                    connector.className = 'motor-connector';
                    connector.dataset.connector = i;

                    // Add components to wrapper
                    motorWrapper.appendChild(motorLabel);
                    motorWrapper.appendChild(motor);
                    motorWrapper.appendChild(connector);

                    // Add wrapper to motor row
                    motorRow.appendChild(motorWrapper);

                    // Create motor control button
                    const button = document.createElement('button');
                    button.textContent = `Toggle Motor ${i}`;
                    button.dataset.motor = i;
                    button.addEventListener('click', () => toggleMotor(i));
                    motorButtons.appendChild(button);
                }

                // Create platforms
                for (let i = 1; i <= 6; i++) {
                    // Platform wrapper
                    const platformWrapper = document.createElement('div');
                    platformWrapper.className = 'platform-wrapper';

                    // Platform
                    const platform = document.createElement('div');
                    platform.style.position = 'absolute';
                    platform.style.left = `${(i * 100) / 7}%`;
                    platform.style.transform = 'translateX(-50%)';
                    platform.className = 'platform';
                    platform.dataset.platform = i;

                    // Platform fill
                    const fill = document.createElement('div');
                    fill.className = 'platform-fill';
                    fill.dataset.fill = i;
                    platform.appendChild(fill);

                    // Platform branch pipe
                    const branch = document.createElement('div');
                    branch.className = 'branch';
                    branch.style.left = `${(i * 100) / 7}%`;
                    branch.dataset.branch = i;
                    mainPipe.appendChild(branch);

                    // Platform label
                    const platformLabel = document.createElement('div');
                    platformLabel.className = 'platform-label';
                    platformLabel.style.position = 'absolute';
                    platformLabel.style.left = `${(i * 100) / 7}%`;
                    platformLabel.style.transform = 'translateX(-50%)';
                    
                    var pf_name;
                    if (i == 1) {
                        pf_name = "PF 1 & 2";
                    }
                    if (i == 2) {
                        pf_name = "PF 3 & 4";
                    }
                    if (i == 3) {
                        pf_name = "PF 5 & 6";
                    }
                    if (i == 4) {
                        pf_name = "PF 7";
                    }
                    if (i == 5) {
                        pf_name = "PF 8";
                    }
                    if (i == 6) {
                        pf_name = "PF 9 & 10";
                    }

                    platformLabel.textContent = pf_name;

                    // Add components to wrapper
                    platformWrapper.appendChild(platform);
                    platformWrapper.appendChild(platformLabel);

                    // Add wrapper to platform row
                    platformRow.appendChild(platformWrapper);
                }

                // Track active motors and platforms
                let activeMotors = [];
                let activePlatforms = [];

                // Motor toggle function
                function toggleMotor(motorIndex) {
                    motorIndex = parseInt(motorIndex);
                    const index = activeMotors.indexOf(motorIndex);

                    if (index === -1) {
                        // Activate motor
                        activeMotors.push(motorIndex);
                    } else {
                        // Deactivate motor
                        activeMotors.splice(index, 1);
                    }

                    updateAnimation();
                }

                // Platform toggle function
                function togglePlatform(platformIndex) {
                    platformIndex = parseInt(platformIndex);
                    const index = activePlatforms.indexOf(platformIndex);

                    if (index === -1) {
                        // Activate platform
                        activePlatforms.push(platformIndex);
                    } else {
                        // Deactivate platform
                        activePlatforms.splice(index, 1);
                    }

                    updateAnimation();
                }

                // Update animation states
                function updateAnimation() {
                    // Update motor animations
                    document.querySelectorAll('.motor').forEach((motor, i) => {
                        const motorIndex = i + 1;
                        if (activeMotors.includes(motorIndex)) {
                            motor.classList.add('pipe-flow');
                            motor.classList.remove('motor-stop');
                            motor.classList.add('motor-start');
                        } else {
                            motor.classList.remove('pipe-flow');
                            motor.classList.remove('motor-start');
                            motor.classList.add('motor-stop');
                        }
                    });

                    // Update motor connector animations
                    document.querySelectorAll('.motor-connector').forEach((connector, i) => {
                        const connectorIndex = i + 1;
                        if (activeMotors.includes(connectorIndex)) {
                            connector.classList.add('pipe-flow');
                        } else {
                            connector.classList.remove('pipe-flow');
                        }
                    });

                    // Main pipe should be active if any motor is active
                    if (activeMotors.length > 0) {
                        mainPipe.classList.add('pipe-flow');
                    } else {
                        mainPipe.classList.remove('pipe-flow');
                    }

                    // Update platform branch animations
                    document.querySelectorAll('.branch').forEach((branch, i) => {
                        const branchIndex = i + 1;
                        if (activePlatforms.includes(branchIndex) && activeMotors.length > 0) {
                            branch.classList.add('pipe-flow');
                        } else {
                            branch.classList.remove('pipe-flow');
                        }
                    });

                    // Update platform fill animations
                    document.querySelectorAll('.platform-fill').forEach((fill, i) => {
                        const platformIndex = i + 1;
                        const platform = fill.parentElement;

                        if (activePlatforms.includes(platformIndex) && activeMotors.length > 0) {
                            fill.style.animation = 'fillAnimation 4s forwards';

                            // Add splash effect
                            const existingSplash = platform.querySelector('.splash');
                            if (existingSplash) existingSplash.remove();

                            setTimeout(() => {
                                const splash = document.createElement('div');
                                splash.className = 'splash';
                                platform.appendChild(splash);
                            }, 1000);
                        } else {
                            fill.style.animation = 'none';
                            fill.style.height = '0%';

                            const oldSplash = platform.querySelector('.splash');
                            if (oldSplash) oldSplash.remove();
                        }
                    });
                }

                // Make platforms clickable to toggle
                document.querySelectorAll('.platform').forEach((platform, i) => {
                    const platformIndex = i + 1;
                    platform.style.cursor = 'pointer';
                    platform.addEventListener('click', () => togglePlatform(platformIndex));
                });

                // Start/Stop all motors buttons
                document.getElementById('startAll').addEventListener('click', () => {
                    activeMotors = [1, 2, 3, 4, 5, 6];
                    updateAnimation();
                });

                document.getElementById('stopAll').addEventListener('click', () => {
                    activeMotors = [];
                    updateAnimation();
                });

                // MQTT Integration
                let mqttClient = null;
                let mqttConnected = false;

                function connectMqtt() {
                    $.ajax({
                        type: 'POST',
                        url: '../common-files/get_mqtt_credentials.php',
                        dataType: 'json',
                        success: function(response) {
                            const consoledata = 'consoledata';
                            const decryptedData = xor_decrypt(response.data, consoledata);

                            if (!decryptedData) {
                                console.error('Failed to decrypt MQTT credentials');
                                updateMqttStatus(false);
                                return;
                            }

                            const options = {
                                username: decryptedData.username,
                                password: decryptedData.password,
                                reconnectPeriod: decryptedData.reconnectPeriod,
                                connectTimeout: decryptedData.connectTimeout,
                                clean: decryptedData.clean,
                            };

                            // Get broker URL and topics from PHP response
                            const brokerUrl = decryptedData.brokerUrl;
                            const topic = decryptedData.mainTopic;
                            const requestTopic = decryptedData.requestTopic;

                            mqttClient = mqtt.connect(brokerUrl, options);

                            mqttClient.on('connect', function() {
                                console.log('MQTT Connected');
                                mqttConnected = true;
                                updateMqttStatus(true);

                                // Subscribe to main data topic
                                mqttClient.subscribe(topic, function(err) {
                                    if (err) {
                                        console.error('Subscribe error:', err);
                                    } else {
                                        console.log('Subscribed to', topic);
                                    }
                                });

                                // Subscribe to request response topic
                                mqttClient.subscribe(requestTopic, function(err) {
                                    if (err) {
                                        console.error('Subscribe error for request topic:', err);
                                    } else {
                                        console.log('Subscribed to', requestTopic);
                                    }
                                });
                            });

                            mqttClient.on('message', function(topic, message) {
                                if (topic === decryptedData.mainTopic) {
                                    // Process data message
                                    let data = message.toString();
                                    let fields = data.split(';').map(item => item.trim()); // Split and trim
                                    
                                    // Extract motor and platform statuses
                                    const mqttData = {
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

                                    // Update active motors and platforms based on MQTT data
                                    updateSystemFromMqtt(mqttData);
                                    console.log("MQTT Data received:", mqttData);
                                }
                            });

                            mqttClient.on('close', function() {
                                console.log('MQTT Connection lost');
                                mqttConnected = false;
                                updateMqttStatus(false);
                            });

                            mqttClient.on('error', function(error) {
                                console.error('MQTT Error:', error);
                                mqttConnected = false;
                                updateMqttStatus(false);
                            });
                        },
                        error: function(xhr, status, error) {
                            console.error(`Error fetching MQTT credentials:`, error);
                            mqttConnected = false;
                            updateMqttStatus(false);
                        }
                    });
                }

                // Update system based on MQTT data
                function updateSystemFromMqtt(mqttData) {
                    // Update active motors array based on MQTT data
                    activeMotors = [];
                    mqttData.motors.forEach((motor, index) => {
                        if (motor.status === "1") {
                            activeMotors.push(index + 1);
                        }
                    });

                    // Update active platforms array based on MQTT data
                    activePlatforms = [];
                    mqttData.platforms.forEach((platform, index) => {
                        if (platform.status === "1") {
                            activePlatforms.push(index + 1);
                        }
                    });

                    // Update visualization
                    updateAnimation();
                }

                // Update MQTT status indicator
                function updateMqttStatus(connected) {
                    if (connected) {
                        mqttStatus.textContent = "MQTT Status: Connected";
                        mqttStatus.className = "status-indicator connected";
                    } else {
                        mqttStatus.textContent = "MQTT Status: Disconnected";
                        mqttStatus.className = "status-indicator disconnected";
                    }
                }

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

                // Initial connection
                connectMqtt();
            </script>
            <script src="<?php echo BASE_PATH; ?>assets/js/sidebar-menu.js"></script>

            <?php
            include(BASE_PATH . "assets/html/body-end.php");
            include(BASE_PATH . "assets/html/html-end.php");
            ?>