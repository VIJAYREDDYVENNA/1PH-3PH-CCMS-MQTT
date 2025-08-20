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



if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $conn = mysqli_connect(HOST, USERNAME, PASSWORD, DB_ALL);
    
    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }
    
    
    $sql_mode = "SELECT * FROM system_mode ORDER BY id DESC LIMIT 1";
    $result_mode = mysqli_query($conn, $sql_mode);
    
    if ($result_mode && mysqli_num_rows($result_mode) > 0) {
        $opModeData = mysqli_fetch_assoc($result_mode);
    } else {
        $opModeData = []; 
    }

 
    $motorsData = [];
    $motorsQuery = mysqli_query($conn, "SELECT motor_id, on_off_status, flow_rate, running_time FROM motor_live_status ORDER BY motor_id");
    
    if ($motorsQuery && mysqli_num_rows($motorsQuery) > 0) {
        while ($motor = mysqli_fetch_assoc($motorsQuery)) {
            $motorsData[] = [
                'status' => $motor['on_off_status'],
                'flowRate' => $motor['flow_rate'],
                'runningTime' => $motor['running_time']
            ];
        }
    }

  
    $platformsData = [];
    $platformsQuery = mysqli_query($conn, "SELECT platform_id, valve_status, open_time FROM valve_status ORDER BY platform_id");
    
    if ($platformsQuery && mysqli_num_rows($platformsQuery) > 0) {
        while ($platform = mysqli_fetch_assoc($platformsQuery)) {
            $platformsData[] = [
                'status' => $platform['valve_status'],
                'openTime' => $platform['open_time']
            ];
        }
    }


    $response = [
        'operationMode' => $opModeData['operation_mode'] ?? 'N/A', 
        'operationModeSubtitle' => $opModeData['operation_mode'] ?? 'N/A',
        'inletPressureStatus' => $opModeData['inlet_pressure'] ?? 'N/A',
        'outletPressure1' => $opModeData['outlet_pressure1'] ?? 'N/A',
        'outletPressure2' => $opModeData['outlet_pressure2'] ?? 'N/A',
        'motors' => $motorsData,
        'platforms' => $platformsData
    ];

    mysqli_close($conn);

    header('Content-Type: application/json');
    echo json_encode($response);
}
