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

//==================================//

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $motorId = filter_input(INPUT_POST, 'motor', FILTER_SANITIZE_STRING);
    // Establish DB connection
    $conn = mysqli_connect(HOST, USERNAME, PASSWORD, DB_ALL);

    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }
    // Get electrical details for the specific motor
    $sql = "SELECT r_y_voltage,y_b_voltage,b_r_voltage, motor_current, energy_kwh, frequency, speed, total_running_hours,motor_voltage, reference_frequency,drive_status,date_time FROM motor_data WHERE motor_id = ? 
    ORDER BY id DESC LIMIT 1";

    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "s", $motorId);
    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);
    $data = mysqli_fetch_assoc($result);

    // Format response  drive_status
    $response = [
        'r_y_voltage'   => $data['r_y_voltage'],
        'y_b_voltage'   => $data['y_b_voltage'],
        'b_r_voltage'   => $data['b_r_voltage'],
        'motorCurrent'  => $data['motor_current'],
        'energyKwh'     => $data['energy_kwh'],
        'frequency'     => $data['frequency'],
        'speed'         => $data['speed'],
        'runningHours'  => $data['total_running_hours'],
        'motorVoltage'  => $data['motor_voltage'],
        'referencefrequency'  => $data['reference_frequency'],
        'date_time' => $data['date_time']

    ];
    if ($role === 'SUPERADMIN') {

        $response['adminStatus'] = $data['drive_status'];
    }
    // Close connections
    mysqli_stmt_close($stmt);
    mysqli_close($conn);

    // Return JSON response
    header('Content-Type: application/json');
    echo json_encode($response);
}
