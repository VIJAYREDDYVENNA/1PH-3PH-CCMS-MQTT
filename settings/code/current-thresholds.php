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
        $response["message"] = "No Permission to change the Current settings of the device(s)";
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
        $r_current = sanitize_input($_POST['IR'], $conn);
        $y_current = sanitize_input($_POST['IY'], $conn);
        $b_current = sanitize_input($_POST['IB'], $conn);


        mysqli_close($conn);

        if (filter_var($r_current, FILTER_VALIDATE_INT, array("options" => array("min_range"=>1, "max_range"=>5000))) === false ||
            filter_var($y_current, FILTER_VALIDATE_INT, array("options" => array("min_range"=>1, "max_range"=>5000))) === false ||
            filter_var($b_current, FILTER_VALIDATE_INT, array("options" => array("min_range"=>1, "max_range"=>5000))) === false )
        {
            $response["message"] = "Invalid Current Limits ";
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

        // Prepare and execute the Current limits insertion query
            $sql_mode = "INSERT INTO `$device_id`.`limits_current` (`device_id`, `i_r`, `i_y`, `i_b`, `date_time`, `user_mobile`, `email`, `name`, `role`) 
            VALUES (?, ?, ?, ?, current_timestamp(), ?, ?, ?, ?)";            

            $stmt = mysqli_prepare($conn_db, $sql_mode);
            if ($stmt) {
                mysqli_stmt_bind_param($stmt, 'ssssssss', $device_id, $r_current, $y_current, $b_current, $mobile_no, $user_email, $user_name, $role);
                mysqli_stmt_execute($stmt);
                mysqli_stmt_close($stmt);
                mysqli_query($conn_db, "INSERT INTO `$central_db`.`thresholds` (device_id, i_r, i_y, i_b) VALUES ('$device_id_update', '$r_current', '$y_current', '$b_current') ON DUPLICATE KEY UPDATE i_r = VALUES(i_r), i_y = VALUES(i_y), i_b = VALUES(i_b);");
                
            } else {
                $response["message"] = "Error preparing current limits query";
                mysqli_close($conn_db);
                echo json_encode($response);
                exit();
            }

        // Prepare and execute the device settings insertion query
            $setting_sql = "INSERT INTO `$device_id`.device_settings (`setting_type`, `setting_flag`) VALUES ('CURRENT', '1') 
            ON DUPLICATE KEY UPDATE setting_flag='1'";
            if (!mysqli_query($conn_db, $setting_sql)) {
                $response["message"] = "Error updating current settings:";
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
            $user_activity = "Current thresholds updated";
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
            $response["message"] = "Current Limits updated successfully";
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
