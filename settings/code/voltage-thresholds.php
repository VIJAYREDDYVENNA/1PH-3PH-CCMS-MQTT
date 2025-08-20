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

$response = ["status" => "error", "message" => ""];
$conn = mysqli_connect(HOST, USERNAME, PASSWORD, DB_USER);
if (!$conn) {
    $response["message"] = "Connection to User database failed: " . mysqli_connect_error();
    echo json_encode($response);
    exit();
}
$sql = "SELECT threshold_settings FROM user_permissions WHERE login_id = ?";
$stmt = mysqli_prepare($conn, $sql);
if ($stmt) {
    mysqli_stmt_bind_param($stmt, "s", $user_login_id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $permission_check);
    mysqli_stmt_fetch($stmt);
    mysqli_stmt_close($stmt);

    if ($permission_check != 1) {
        $response["message"] = "No Permission to change the voltage threshold settings of the device(s)";
        mysqli_close($conn);
        echo json_encode($response);
        exit();
    }
} else {
    $response["message"] = "Error preparing query for user permissions:";
    mysqli_close($conn);
    echo json_encode($response);
    exit();
}
if ($permission_check == 1) 
{
    if ($_SERVER["REQUEST_METHOD"] == "POST") 
    {
        $device_ids = sanitize_input($_POST['D_ID'], $conn);
        $r_lower_volt = sanitize_input($_POST['LR'], $conn);
        $y_lower_volt = sanitize_input($_POST['LY'], $conn);
        $b_lower_volt = sanitize_input($_POST['LB'], $conn);
        $r_upper_volt = sanitize_input($_POST['UR'], $conn);
        $y_upper_volt = sanitize_input($_POST['UY'], $conn);
        $b_upper_volt = sanitize_input($_POST['UB'], $conn);

        mysqli_close($conn);

        if (filter_var($r_lower_volt, FILTER_VALIDATE_INT, array("options" => array("min_range"=>1, "max_range"=>750))) === false ||
            filter_var($y_lower_volt, FILTER_VALIDATE_INT, array("options" => array("min_range"=>1, "max_range"=>750))) === false ||
            filter_var($b_lower_volt, FILTER_VALIDATE_INT, array("options" => array("min_range"=>1, "max_range"=>750))) === false ||
            filter_var($r_upper_volt, FILTER_VALIDATE_INT, array("options" => array("min_range"=>1, "max_range"=>750))) === false ||
            filter_var($y_upper_volt, FILTER_VALIDATE_INT, array("options" => array("min_range"=>1, "max_range"=>750))) === false ||
            filter_var($b_upper_volt, FILTER_VALIDATE_INT, array("options" => array("min_range"=>1, "max_range"=>750))) === false) 
        {
            $response["message"] = "Invalid voltage values ";
            exit();
        }

        $device_ids_array = explode(",", $device_ids);
        foreach ($device_ids_array as $device_id) 
        {
        // Validate and sanitize device ID
            $device_id = trim(strtolower($device_id));
            if (!preg_match('/^[a-z0-9_]+$/', $device_id)) {
                $response["message"] = "Invalid device ID";
                echo json_encode($response);
                exit();
            }

        // Create a new database connection
            $conn_db = mysqli_connect(HOST, USERNAME, PASSWORD);
            if (!$conn_db) {
                $response["message"] = "Connection to device database failed";
                echo json_encode($response);
                exit();
            }
             $device_id_update=strtoupper($device_id);
        // Prepare and execute the voltage limits insertion query
            $sql_limits = "INSERT INTO `$device_id`.`limits_voltage` (`device_id`, `l_r`, `l_y`, `l_b`, `u_r`, `u_y`, `u_b`, `date_time`, `user_mobile`, `email`, `name`, `role`) 
            VALUES (?, ?, ?, ?, ?, ?, ?, current_timestamp(), ?, ?, ?, ?)";
            $stmt = mysqli_prepare($conn_db, $sql_limits);
            if ($stmt) {
                mysqli_stmt_bind_param($stmt, 'sssssssssss', $device_id_update, $r_lower_volt, $y_lower_volt, $b_lower_volt, $r_upper_volt, $y_upper_volt, $b_upper_volt, $mobile_no, $user_email, $user_name, $role);
                mysqli_stmt_execute($stmt);
                mysqli_stmt_close($stmt);

                $sql="INSERT INTO `$central_db`.`thresholds` (device_id, l_r, l_y, l_b, u_r, u_y, u_b) VALUES ('$device_id_update', '$r_lower_volt', '$y_lower_volt', '$b_lower_volt', '$r_upper_volt', '$y_upper_volt', '$b_upper_volt') ON DUPLICATE KEY UPDATE l_r = VALUES(l_r), l_y = VALUES(l_y), l_b = VALUES(l_b), u_r = VALUES(u_r), u_y = VALUES(u_y), u_b = VALUES(u_b)";
                mysqli_query($conn_db, $sql);




            } else {
                $response["message"] = "Error preparing voltage limits query";
                mysqli_close($conn_db);
                echo json_encode($response);
                exit();
            }

        // Prepare and execute the device settings insertion query
            $setting_sql = "INSERT INTO `$device_id`.device_settings (`setting_type`, `setting_flag`) VALUES ('VOLTAGE', '1') 
            ON DUPLICATE KEY UPDATE setting_flag='1'";
            if (!mysqli_query($conn_db, $setting_sql)) {
                $response["message"] = "Error updating voltage settings:";
                mysqli_close($conn_db);
                echo json_encode($response);
                exit();
            }

            $read_sql = "INSERT INTO `$device_id`.device_settings (`setting_type`, `setting_flag`) VALUES ('READ_SETTINGS', '1') 
            ON DUPLICATE KEY UPDATE setting_flag='1'";
            if (!mysqli_query($conn_db, $read_sql)) {
                $response["message"] = "Error updating read settings";
                mysqli_close($conn_db);
                echo json_encode($response);
                exit();
            }

        // Prepare and execute the user activity log insertion query
            $user_activity = "Voltage thresholds updated";
            $log_sql = "INSERT INTO `$device_id`.user_activity_log (`updated_field`, `date_time`, `user_mobile`, `email`, `name`, `role`) 
            VALUES (?, current_timestamp(), ?, ?, ?, ?)";
            $log_stmt = mysqli_prepare($conn_db, $log_sql);
            if ($log_stmt) {
                mysqli_stmt_bind_param($log_stmt, "sssss", $user_activity, $mobile_no, $user_email, $user_name, $role);
                mysqli_stmt_execute($log_stmt);
                mysqli_stmt_close($log_stmt);
            } else {
                $response["message"] = "Error preparing user activity log query";
                mysqli_close($conn_db);
                echo json_encode($response);
                exit();
            }

            $response["status"] = "success";
            $response["message"] = "Voltage threshold settings updated successfully";
            mysqli_close($conn_db);
        }
        echo json_encode( $response);

    }
    else
    { 
        $response["message"] = "Invalid request method.";

    }
}

// Function to sanitize input data
function sanitize_input($data, $conn) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return mysqli_real_escape_string($conn, $data);
}

?>
