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

/*$device_ids = "CCMS_1";
$parameter = "CT_RATIO";
$updated_value = 100;*/


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
        $parameter = sanitize_input($_POST['PARAMETER'], $conn);
        $updated_value = sanitize_input($_POST['UPDATED_VALUE'], $conn);



        mysqli_close($conn);
        if($parameter=="PF")
        {
            if (filter_var($updated_value, FILTER_VALIDATE_FLOAT, array("options" => array("min_range"=>0, "max_range"=>1))) === false)
            {
                $response["message"] = "Invalid Updated Value ";           
                echo json_encode($response);
                exit();
            }
        }
        else
        {
            if (filter_var($updated_value, FILTER_VALIDATE_INT, array("options" => array("min_range"=>1, "max_range"=>84600))) === false)
            {
                $response["message"] = "Invalid Updated Value ";           
                echo json_encode($response);
                exit();
            }
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
            $user_activity = "";
            $update_parameter="";

            switch (trim($parameter)) {

                case 'PF':

                $user_activity = "PF Limit updated";
                $device_id_update=strtoupper($device_id);

                $sql_mode = "INSERT INTO `$device_id`.`limits_pf` (`device_id`, `pf`, `date_time`, `user_mobile`, `email`, `name`, `role`) 
                VALUES (?, ?, current_timestamp(), ?, ?, ?, ?)";

                $stmt = mysqli_prepare($conn_db, $sql_mode);
                if ($stmt) {
                    mysqli_stmt_bind_param($stmt, 'ssssss',  $device_id_update, $updated_value, $mobile_no, $user_email, $user_name, $role);
                    mysqli_stmt_execute($stmt);
                    mysqli_stmt_close($stmt);

                    
                    mysqli_query($conn_db, "INSERT INTO `$central_db`.`thresholds` (device_id, pf) VALUES ('$device_id_update', '$updated_value') ON DUPLICATE KEY UPDATE  pf = VALUES(pf)");


                } else {
                    $response["message"] = "Error preparing pf limits query";
                    mysqli_close($conn_db);
                    echo json_encode($response);
                    exit();
                }
                break;

                case 'CAPACITY':
                $user_activity = "Unit Capaicty updated";

                $device_id_update=strtoupper($device_id);

                $sql_mode = "INSERT INTO `$device_id`.`unit_capacity` (`device_id`, `capacity`, `date_time`, `user_mobile`, `email`, `name`, `role`) 
                VALUES (?, ?, current_timestamp(), ?, ?, ?, ?)";

                $stmt = mysqli_prepare($conn_db, $sql_mode);
                if ($stmt) {
                    mysqli_stmt_bind_param($stmt, 'ssssss',  $device_id_update, $updated_value, $mobile_no, $user_email, $user_name, $role);
                    mysqli_stmt_execute($stmt);
                    mysqli_stmt_close($stmt);
                } else {
                    $response["message"] = "Error preparing unit capcity query";
                    mysqli_close($conn_db);
                    echo json_encode($response);
                    exit();
                }
                break;

                case 'FRAME_TIME':
                $user_activity = "Frame-Time interval updated";
                $update_parameter="FRAME_TIME";
                $device_id_update=strtoupper($device_id);
                $sql_mode = "INSERT INTO `$device_id`.`frame_time` (`device_id`, `frame_time`, `date_time`, `user_mobile`, `email`, `name`, `role`) 
                VALUES (?, ?, current_timestamp(), ?, ?, ?, ?)";

                $stmt = mysqli_prepare($conn_db, $sql_mode);
                if ($stmt) {
                    mysqli_stmt_bind_param($stmt, 'ssssss',  $device_id_update, $updated_value, $mobile_no, $user_email, $user_name, $role);
                    mysqli_stmt_execute($stmt);
                    mysqli_stmt_close($stmt);
                } else {
                    $response["message"] = "Error preparing Frame-Time interval query";
                    mysqli_close($conn_db);
                    echo json_encode($response);
                    exit();
                }

                // Prepare and execute the device settings insertion query
                $setting_sql = "INSERT INTO `$device_id`.device_settings (`setting_type`, `setting_flag`) VALUES ('FRAME_TIME', '1') 
                ON DUPLICATE KEY UPDATE setting_flag='1'";
                if (!mysqli_query($conn_db, $setting_sql)) {
                    $response["message"] = "Error updating ". strtolower($update_parameter);
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

                break;

                case 'CT_RATIO':
                $user_activity = "CT-Ratio updated";
                $device_id_update=strtoupper($device_id);
                $sql_mode = "INSERT INTO `$device_id`.`limits_ct_ratio` (`device_id`, `ct_ratio`, `date_time`, `user_mobile`, `email`, `name`, `role`) 
                VALUES (?, ?, current_timestamp(), ?, ?, ?, ?)";

                $stmt = mysqli_prepare($conn_db, $sql_mode);
                if ($stmt) {
                    mysqli_stmt_bind_param($stmt, 'ssssss', $device_id_update, $updated_value, $mobile_no, $user_email, $user_name, $role);
                    mysqli_stmt_execute($stmt);
                    mysqli_stmt_close($stmt);
                } else {
                    $response["message"] = "Error preparing ct-ratio query";
                    mysqli_close($conn_db);
                    echo json_encode($response);
                    exit();
                }
                break;

                default:
                mysqli_close($conn_db);
                $response["message"] = "Something went wrong..";
                echo json_encode($response);
                exit();
                break;
            }

            if($update_parameter=="FRAME_TIME")
            {

                // Prepare and execute the Current limits insertion query

            }

        // Prepare and execute the user activity log insertion query

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
            $response["message"] = $user_activity." successfully";
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
