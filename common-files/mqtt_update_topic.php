<?php
require_once '../base-path/config-path.php';
require_once BASE_PATH.'config_db/config.php';
require_once BASE_PATH.'session/session-manager.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $action = $_POST['action'] ?? '';

    if ($action === 'getEncrypted') {
        $activationCode = $_POST['activationCode'] ?? '';
        $userName = $_POST['userName'] ?? '';
        $userMobile = $_POST['userMobile'] ?? '';

        // Create topic dynamically
        $mainTopicPrefix = 'APP/ACTIVATE/CONFIRM/';
        $fullTopic = $mainTopicPrefix . $activationCode;

        // MQTT options
        $options = [
            'username' => 'istlMqttHyd',
            'password' => 'Istl_1234@Hyd',
            'reconnectPeriod' => 1000,
            'connectTimeout' => 4000,
            'clean' => true,
            'brokerUrl' => 'wss://mqtt-broker.istlabsonline.com/mqtt',
            'mainTopic' => $fullTopic,
            'userName' => $userName,
            'userMobile' => $userMobile,
            'activationCode' => $activationCode
        ];

        $json_data = json_encode($options);
        $secret_key = 'mobile_app_activation';

        function xor_encrypt($data, $key) {
            $output = '';
            $key_length = strlen($key);
            for ($i = 0; $i < strlen($data); $i++) {
                $output .= $data[$i] ^ $key[$i % $key_length];
            }
            return base64_encode($output);
        }

        $encrypted = xor_encrypt($json_data, $secret_key);
        echo json_encode(['data' => $encrypted]);
        exit;

    } elseif ($action === 'insertData') {
        $activationCode = $_POST['activationCode'] ?? '';
        $userName = $_POST['userName'] ?? '';
        $userMobile = $_POST['userMobile'] ?? '';

        // Procedural DB insert
        $conn = mysqli_connect(HOST,USERNAME, PASSWORD, DB_USER);

        if (!$conn) {
            echo json_encode(['status' => 'error', 'message' => 'Database connection failed']);
            exit;
        }

        $stmt = mysqli_prepare($conn, "INSERT INTO mobile_app_activation_details (activation_code, user_name, mobile, date_time) VALUES (?, ?, ?, NOW())");


        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "sss", $activationCode, $userName, $userMobile);
            $success = mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
            mysqli_close($conn);

            if ($success) {
                echo json_encode(['status' => 'success']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Insert failed']);
            }
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Prepare statement failed']);
        }
        exit;

    } else {
        echo json_encode(['status' => 'error', 'message' => 'Invalid action']);
        exit;
    }
} else {
    http_response_code(405);
    echo json_encode(['status' => 'error', 'message' => 'Only POST method allowed']);
}
