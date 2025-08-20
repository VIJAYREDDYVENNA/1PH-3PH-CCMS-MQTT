<?php
require_once '../../base-path/config-path.php';
require_once BASE_PATH_1 . 'config_db/config.php';
require_once BASE_PATH_1 . 'session/session-manager.php';

SessionManager::checkSession();
$sessionVars = SessionManager::SessionVariables();
$mobile_no = $sessionVars['mobile_no'];
$user_id = $sessionVars['user_id'];
$role = $sessionVars['role'];
$user_login_id = $sessionVars['user_login_id'];

$conn = mysqli_connect(HOST, USERNAME, PASSWORD, DB_USER);
if (!$conn) {
    die(json_encode(["status" => "error", "message" => "Database connection failed."]));
}

// Get the electrician ID from the POST request
$electrician_name = $_POST['electrician_name'] ?? 0;
$electrician_phone = $_POST['electrician_phone'] ?? 0;

$response = ['success' => false, 'devices' => []];

if ($electrician_name > 0) {
    // Query to get all devices assigned to this electrician
    $query = "SELECT device_id,electrician_name, group_area as group_area 
              FROM electrician_devices
            
              WHERE electrician_name= ? and phone_number = ?";
    
    $stmt = $conn->prepare($query);
    $stmt->bind_param("is", $electrician_name,$electrician_phone);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $devices = [];
    while ($row = $result->fetch_assoc()) {
        $devices[] = $row;
    }
    
    $response = ['success' => true, 'devices' => $devices];
}

// Return JSON response
header('Content-Type: application/json');
echo json_encode($response);
?>