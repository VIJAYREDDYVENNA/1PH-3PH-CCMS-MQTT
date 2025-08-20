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
$user_name = $sessionVars['user_name'];
$user_email = $sessionVars['user_email'];
$permission_check = 0;

// Function to sanitize input
function sanitize_input($data, $conn) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    return mysqli_real_escape_string($conn, $data);
}

// Handle API request
if (isset($_POST['energyconsumption'])) {
    header('Content-Type: application/json');

    $conn = mysqli_connect(HOST, USERNAME, PASSWORD);
    if (!$conn) {
        echo json_encode([
            "status" => "error",
            "message" => "Database connection failed: " . mysqli_connect_error()
        ]);
        exit;
    }

    try {
        if (!isset($_POST['D_id'])) {
            throw new Exception("Missing device ID.");
        }

        if (!isset($_POST['fromdate'], $_POST['fromtime'], $_POST['todate'], $_POST['totime'])) {
            throw new Exception("Missing date/time parameters.");
        }

        // Sanitize inputs
        $device_id = sanitize_input($_POST['D_id'], $conn);
        $fromdate = sanitize_input($_POST['fromdate'], $conn);
        $fromtime = sanitize_input($_POST['fromtime'], $conn);
        $todate   = sanitize_input($_POST['todate'], $conn);
        $totime   = sanitize_input($_POST['totime'], $conn);

        $db = strtolower($device_id);

        // Validate date formats
        if (!strtotime($fromdate . ' ' . $fromtime) || !strtotime($todate . ' ' . $totime)) {
            throw new Exception("Invalid date/time format.");
        }

        // FROM query
        $query_from = "SELECT device_id, date_time, energy_kwh_total, energy_kvah_total 
                       FROM `$db`.`live_data` 
                       WHERE DATE(date_time) = '$fromdate' AND TIME(date_time) >= '$fromtime' 
                       ORDER BY date_time ASC 
                       LIMIT 1";

        $result_from = mysqli_query($conn, $query_from);

        // If no result found on the same day, try next day
        if (mysqli_num_rows($result_from) === 0) {
            $next_date = date('Y-m-d', strtotime($fromdate . ' +1 day'));
            $query_from = "SELECT device_id, date_time, energy_kwh_total, energy_kvah_total 
                           FROM `$db`.`live_data` 
                           WHERE DATE(date_time) >= '$fromdate' 
                           ORDER BY date_time ASC 
                           LIMIT 1";
            $result_from = mysqli_query($conn, $query_from);
        }

        if (mysqli_num_rows($result_from) === 0) {
            throw new Exception("No data found for the 'From' date time range.");
        }

        $from_data = mysqli_fetch_assoc($result_from);

        // TO query
        $query_to = "SELECT device_id, date_time, energy_kwh_total, energy_kvah_total 
                     FROM `$db`.`live_data` 
                     WHERE DATE(date_time) = '$todate' AND TIME(date_time) <= '$totime' 
                     ORDER BY date_time DESC 
                     LIMIT 1";

        $result_to = mysqli_query($conn, $query_to);

        // If no result found on the same day, try previous day
        if (mysqli_num_rows($result_to) === 0) {
            $prev_date = date('Y-m-d', strtotime($todate . ' -1 day'));
            $query_to = "SELECT device_id, date_time, energy_kwh_total, energy_kvah_total 
                         FROM `$db`.`live_data` 
                         WHERE DATE(date_time) <= '$todate' 
                         ORDER BY date_time DESC 
                         LIMIT 1";
            $result_to = mysqli_query($conn, $query_to);
        }

        if (mysqli_num_rows($result_to) === 0) {
            throw new Exception("No data found for the 'To' date time range.");
        }

        $to_data = mysqli_fetch_assoc($result_to);

        // Calculate consumption
        $diff_kwh = $to_data['energy_kwh_total'] - $from_data['energy_kwh_total'];
        $diff_kvah = $to_data['energy_kvah_total'] - $from_data['energy_kvah_total'];

        $actual_from_time = date('Y-m-d H:i:s', strtotime($from_data['date_time']));
        $actual_to_time = date('Y-m-d H:i:s', strtotime($to_data['date_time']));

        echo json_encode([
            "status" => "success",
            "data" => [
                "diff_kwh" => $diff_kwh,
                "diff_kvah" => $diff_kvah,
                "actual_from_time" => $actual_from_time,
                "actual_to_time" => $actual_to_time
            ]
        ]);

        mysqli_close($conn);
        exit;

    } catch (Exception $e) {
        echo json_encode([
            "status" => "error",
            "message" => $e->getMessage()
        ]);
        mysqli_close($conn);
        exit;
    }
}
?>
