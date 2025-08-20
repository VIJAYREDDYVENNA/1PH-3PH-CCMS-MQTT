<?php
require_once '../../base-path/config-path.php';
require_once BASE_PATH_1 . 'config_db/config.php';
require_once BASE_PATH_1 . 'session/session-manager.php';

// Check session and retrieve session variables
SessionManager::checkSession();
$sessionVars = SessionManager::SessionVariables();
$mobile_no = $sessionVars['mobile_no'];
$user_id = $sessionVars['user_id'];
$role = $sessionVars['role'];
$user_login_id = $sessionVars['user_login_id'];

$permission_check = 0;

// Handle POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $group_id = $_POST['GROUP_ID'];

    $conn = mysqli_connect(HOST, USERNAME, PASSWORD, DB_ALL);

    if (!$conn) {
        sendResponse(['success' => false, 'message' => "Connection failed"]);
    } else {
        // Fetch device details where overload_flag = 1
        $sql = "SELECT device_id, energy_kwh_total, lights_wattage FROM live_data_updates WHERE overload_flag = ?";
        $stmt = mysqli_prepare($conn, $sql);
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "i", $overload_flag);
            $overload_flag = 1;
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);

            $devices = [];
            while ($row = mysqli_fetch_assoc($result)) {
                $difference = $row['energy_kwh_total'] - $row['lights_wattage'];
                $devices[] = [
                    'device_id' => $row['device_id'],
                    'total_load_received' => $row['energy_kwh_total'],
                    'total_wattage_installed' => $row['lights_wattage'],
                    'difference' => $difference
                ];
            }

            mysqli_stmt_close($stmt);
            mysqli_close($conn);

            // Send back JSON response
            echo json_encode([
                'success' => true,
                'devices' => $devices
            ]);
            exit;

        } else {
            mysqli_close($conn);
            sendResponse(['success' => false, 'message' => "Error preparing query."]);
        }
    }
}

// Helper function to send JSON response
function sendResponse($data) {
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}
?>
