<?php
require_once '../base-path/config-path.php';
require_once BASE_PATH . 'config_db/config.php';
require_once BASE_PATH . 'session/session-manager.php';
SessionManager::checkSession();

$sessionVars = SessionManager::SessionVariables();
$mobile_no = $sessionVars['mobile_no'];
$user_id = $sessionVars['user_id'];
$role = $sessionVars['role'];
$user_login_id = $sessionVars['user_login_id'];

// Establish DB connection
$conn = mysqli_connect(HOST, USERNAME, PASSWORD, DB_USER);
if (!$conn) {
    die(json_encode(["status" => "error", "message" => "Database connection failed."]));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'];

    switch ($action) {
        case 'fetchAssignedDevices':
            fetchAssignedDevices($_POST['electrician_id']);
            break;

        case 'fetchUnassignedDevices':
            fetchUnassignedDevices($_POST['electrician_id']);
            break;

        case 'removeAccess':
            removeElectricianAccess($_POST['device_id'], $_POST['electrician_name']);
            break;

        case 'assignElectrician':
            assignElectrician($_POST['device_id'], $_POST['electrician_id']);
            break;

        default:
            echo json_encode(["status" => "error", "message" => "Invalid action."]);
    }
}

// Fetch devices assigned to electricians
function fetchAssignedDevices($electricianId) {
    global $conn;
    $query = "SELECT d.device_id, d.device_name, e.electrician_name 
              FROM electrician_devices e 
              INNER JOIN user_device_list d ON e.device_id = d.device_id 
              WHERE e.electrician_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('i', $electricianId);
    $stmt->execute();
    $result = $stmt->get_result();

    $devices = [];
    while ($row = $result->fetch_assoc()) {
        $devices[] = $row;
    }

    echo json_encode(["status" => "success", "data" => $devices]);
}

// Fetch devices not assigned to the electrician
function fetchUnassignedDevices($electricianId) {
    global $conn;
    $query = "SELECT d.device_id, d.device_name 
              FROM user_device_list d 
              WHERE d.device_id NOT IN (
                  SELECT device_id FROM electrician_devices WHERE electrician_id = ?
              )";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('i', $electricianId);
    $stmt->execute();
    $result = $stmt->get_result();

    $unassignedDevices = [];
    while ($row = $result->fetch_assoc()) {
        $unassignedDevices[] = $row;
    }

    echo json_encode(["status" => "success", "data" => $unassignedDevices]);
}

// Remove electrician's access from a device
function removeElectricianAccess($deviceId, $electricianName) {
    global $conn;
    $query = "DELETE FROM electrician_devices WHERE device_id = ? AND electrician_name = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('is', $deviceId, $electricianName);

    if ($stmt->execute()) {
        echo json_encode(["status" => "success", "message" => "Electrician access removed successfully."]);
    } else {
        echo json_encode(["status" => "error", "message" => "Error removing electrician access."]);
    }
}

// Assign an electrician to a device
function assignElectrician($deviceId, $electricianId) {
    global $conn;
    $query = "INSERT INTO electrician_devices (device_id, electrician_id) VALUES (?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('ii', $deviceId, $electricianId);

    if ($stmt->execute()) {
        echo json_encode(["status" => "success", "message" => "Electrician assigned successfully."]);
    } else {
        echo json_encode(["status" => "error", "message" => "Error assigning electrician."]);
    }
}
?>
