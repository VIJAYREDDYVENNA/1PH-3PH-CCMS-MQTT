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
$device_ids = "";
$parameter_value = "";
$parameter = "";


/*$device_ids = "CCMS_1";
$parameter = "RESET_DEVICE";
$parameter_value = "RESET";*/


$conn = mysqli_connect(HOST, USERNAME, PASSWORD, DB_USER);
if (!$conn) {
    $response["message"] = "Connection to User database failed :" . mysqli_connect_error();
    echo json_encode($response);
    exit();
}
$sql = "SELECT iot_settings FROM user_permissions WHERE login_id = ?";
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
        if(isset($_POST['D_ID'])&&isset($_POST['PARAMETER_VALUE'])&&isset($_POST['UPDATED_STATUS']))
        {
            $device_ids = sanitize_input($_POST['D_ID'], $conn);
            $parameter_value = sanitize_input($_POST['PARAMETER_VALUE'], $conn);
            $parameter = sanitize_input($_POST['UPDATED_STATUS'], $conn);
        }
        else if(isset($_POST['D_ID'])&&isset($_POST['UPDATED_STATUS']) && $_POST['UPDATED_STATUS']=="ANGLE_CHANGE")
        {
            $device_ids = sanitize_input($_POST['D_ID'], $conn);
            $parameter = sanitize_input($_POST['UPDATED_STATUS'], $conn);
            
        }

        mysqli_close($conn);

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
            $conn_db = mysqli_connect(HOST, USERNAME, PASSWORD, $device_id);
            if (!$conn_db) {
                $response["message"] = "Connection to device database failed";
                echo json_encode($response);
                exit();
            }
            $user_activity = "";
            $update_parameter="";

            switch (trim($parameter)) {

                case 'CHANGE_DEVICE_ID':

                $user_activity = "New Device-Id updated";
                $device_id=strtoupper($device_id);

               // Updated SQL query for `iot_device_id_change` table
                $sql_mode = "INSERT INTO `iot_device_id_change` (`device_id`, `new_device_id`, `date_time`, `user_mobile`, `email`, `name`, `role`) 
                VALUES ( ?, ?, current_timestamp(), ?, ?, ?, ?)";

                $stmt = mysqli_prepare($conn_db, $sql_mode);
                if ($stmt) {
                    // Bind parameters: s (string) for each parameter
                    $parameter_value=strtoupper(trim($parameter_value));
                    mysqli_stmt_bind_param($stmt, 'ssssss', $device_id, $parameter_value, $mobile_no, $user_email, $user_name, $role);

                    // Execute the prepared statement
                    mysqli_stmt_execute($stmt);
                    mysqli_stmt_close($stmt);                   
                } else {
                    $response["message"] = "Error preparing device id change query";
                    mysqli_close($conn_db);
                    echo json_encode($response);
                    exit();
                }

                $setting_sql = "INSERT INTO device_settings (`setting_type`, `setting_flag`) VALUES ('ID_CHANGE', '1') 
                ON DUPLICATE KEY UPDATE setting_flag='1'";
                if (!mysqli_query($conn_db, $setting_sql)) {
                    $response["message"] = "Error initiating IoT settings";
                    mysqli_close($conn_db);
                    echo json_encode($response);
                    exit();
                }

                $response["status"] = "success";
                $response["message"] = $user_activity." successfully";
                break;

                case 'CHANGE_SERIAL_ID':

                $user_activity = "New Serial-Id updated";
                $device_id=strtoupper($device_id);

               // Updated SQL query for `iot_device_id_change` table
                $sql_mode = "INSERT INTO `iot_serial_id_change` (`device_id`, `serial_id`, `date_time`, `user_mobile`, `email`, `name`, `role`) 
                VALUES ( ?, ?, current_timestamp(), ?, ?, ?, ?)";

                $stmt = mysqli_prepare($conn_db, $sql_mode);
                if ($stmt) {
                    // Bind parameters: s (string) for each parameter
                    $parameter_value=strtoupper(trim($parameter_value));
                    mysqli_stmt_bind_param($stmt, 'ssssss', $device_id, $parameter_value, $mobile_no, $user_email, $user_name, $role);

                    // Execute the prepared statement
                    mysqli_stmt_execute($stmt);
                    mysqli_stmt_close($stmt);                   
                } else {
                    $response["message"] = "Error preparing device id change query";
                    mysqli_close($conn_db);
                    echo json_encode($response);
                    exit();
                }

                $setting_sql = "INSERT INTO device_settings (`setting_type`, `setting_flag`) VALUES ('SERIAL_ID', '1') 
                ON DUPLICATE KEY UPDATE setting_flag='1'";
                if (!mysqli_query($conn_db, $setting_sql)) {
                    $response["message"] = "Error initiating IoT settings";
                    mysqli_close($conn_db);
                    echo json_encode($response);
                    exit();
                }

                $response["status"] = "success";
                $response["message"] = $user_activity." successfully";
                break;

                case 'HYSTERESIS':

                $user_activity = "Hysteresis setting updated";
                $device_id=strtoupper($device_id);

               // Updated SQL query for `iot_device_id_change` table
                $sql_mode = "INSERT INTO `iot_hysteresis` (`device_id`, `value`, `date_time`, `user_mobile`, `email`, `name`, `role`) 
                VALUES ( ?, ?, current_timestamp(), ?, ?, ?, ?)";

                $stmt = mysqli_prepare($conn_db, $sql_mode);
                if ($stmt) {
                    // Bind parameters: s (string) for each parameter
                    $parameter_value=strtoupper(trim($parameter_value));
                    mysqli_stmt_bind_param($stmt, 'ssssss', $device_id, $parameter_value, $mobile_no, $user_email, $user_name, $role);

                    // Execute the prepared statement
                    mysqli_stmt_execute($stmt);
                    mysqli_stmt_close($stmt);                   
                } else {
                    $response["message"] = "Error preparing query";
                    mysqli_close($conn_db);
                    echo json_encode($response);
                    exit();
                }

                $setting_sql = "INSERT INTO device_settings (`setting_type`, `setting_flag`) VALUES ('HYSTERESIS', '1') 
                ON DUPLICATE KEY UPDATE setting_flag='1'";
                if (!mysqli_query($conn_db, $setting_sql)) {
                    $response["message"] = "Error initiating hysteresis";
                    mysqli_close($conn_db);
                    echo json_encode($response);
                    exit();
                }

                $response["status"] = "success";
                $response["message"] = $user_activity." successfully";
                break;

                case 'ON_OFF_INTERVAL':

                $user_activity = "On-Off interval updated";
                $device_id=strtoupper($device_id);

               // Updated SQL query for `iot_device_id_change` table
                $sql_mode = "INSERT INTO `iot_on_off_interval` (`device_id`, `value`, `date_time`, `user_mobile`, `email`, `name`, `role`) 
                VALUES ( ?, ?, current_timestamp(), ?, ?, ?, ?)";

                $stmt = mysqli_prepare($conn_db, $sql_mode);
                if ($stmt) {
                    // Bind parameters: s (string) for each parameter
                    $parameter_value=strtoupper(trim($parameter_value));
                    mysqli_stmt_bind_param($stmt, 'ssssss', $device_id, $parameter_value, $mobile_no, $user_email, $user_name, $role);

                    // Execute the prepared statement
                    mysqli_stmt_execute($stmt);
                    mysqli_stmt_close($stmt);                   
                } else {
                    $response["message"] = "Error preparing query";
                    mysqli_close($conn_db);
                    echo json_encode($response);
                    exit();
                }

                $setting_sql = "INSERT INTO device_settings (`setting_type`, `setting_flag`) VALUES ('LOOP_ON_OFF', '1') 
                ON DUPLICATE KEY UPDATE setting_flag='1'";
                if (!mysqli_query($conn_db, $setting_sql)) {
                    $response["message"] = "Error initiating on-off interval";
                    mysqli_close($conn_db);
                    echo json_encode($response);
                    exit();
                }

                $response["status"] = "success";
                $response["message"] = $user_activity." successfully";
                break;

                case 'RESET_ENERGY':

                $user_activity = "energy reset values updated";
                $device_id=strtoupper($device_id);

               // Updated SQL query for `iot_device_id_change` table
                $sql_mode = "INSERT INTO `iot_reset_energy` (`device_id`, `kwh`, `kvah`, `date_time`, `user_mobile`, `email`, `name`, `role`) 
                VALUES (?, ?, ?, current_timestamp(), ?, ?, ?, ?)";

                $stmt = mysqli_prepare($conn_db, $sql_mode);
                if ($stmt) {
                    // Bind parameters: d (double) for float values, s (string) for other parameters
                    $parameter_value = strtoupper(trim($parameter_value));
                    $parameter_values = explode(',', $parameter_value);

                    mysqli_stmt_bind_param($stmt, 'sddssss', $device_id, $parameter_values[0], $parameter_values[1], $mobile_no, $user_email, $user_name, $role);

                    // Execute the prepared statement
                    mysqli_stmt_execute($stmt);
                    mysqli_stmt_close($stmt);                   
                } else {
                    $response["message"] = "Error preparing query";
                    mysqli_close($conn_db);
                    echo json_encode($response);
                    exit();
                }
                $setting_sql = "INSERT INTO device_settings (`setting_type`, `setting_flag`) VALUES ('ENERGY_RESET', '1') 
                ON DUPLICATE KEY UPDATE setting_flag='1'";
                if (!mysqli_query($conn_db, $setting_sql)) {
                    $response["message"] = "Error initiating reset energy values";
                    mysqli_close($conn_db);
                    echo json_encode($response);
                    exit();
                }

                $response["status"] = "success";
                $response["message"] = $user_activity." successfully";
                break;

                case 'WIFI_CREDENTIALS':

                $user_activity = "WiFi credentials updated";
                $device_id=strtoupper($device_id);

               // Updated SQL query for `iot_device_id_change` table
                $sql_mode = "INSERT INTO `iot_wifi_credentials` (`device_id`, `ssid`, `password`, `date_time`, `user_mobile`, `email`, `name`, `role`) 
                VALUES (?, ?, ?, current_timestamp(), ?, ?, ?, ?)";

                $stmt = mysqli_prepare($conn_db, $sql_mode);
                if ($stmt) {
                    // Bind parameters: d (double) for float values, s (string) for other parameters
                    /*$parameter_value = strtoupper(trim($parameter_value));*/
                    $parameter_values = explode(',', $parameter_value);

                    mysqli_stmt_bind_param($stmt, 'sssssss', $device_id, $parameter_values[0], $parameter_values[1], $mobile_no, $user_email, $user_name, $role);

                    // Execute the prepared statement
                    mysqli_stmt_execute($stmt);
                    mysqli_stmt_close($stmt);                   
                } else {
                    $response["message"] = "Error preparing query";
                    mysqli_close($conn_db);
                    echo json_encode($response);
                    exit();
                }
                $setting_sql = "INSERT INTO device_settings (`setting_type`, `setting_flag`) VALUES ('WIFI_CREDENTIALS', '1') 
                ON DUPLICATE KEY UPDATE setting_flag='1'";
                if (!mysqli_query($conn_db, $setting_sql)) {
                    $response["message"] = "Error initiating wifi credentials";
                    mysqli_close($conn_db);
                    echo json_encode($response);
                    exit();
                }

                $response["status"] = "success";
                $response["message"] = $user_activity." successfully";
                break;

                case 'READ_SETTINGS':

                $user_activity = "Read saved settings from IoT initiated";

                $setting_sql = "INSERT INTO device_settings (`setting_type`, `setting_flag`) VALUES ('READ_SETTINGS', '1') 
                ON DUPLICATE KEY UPDATE setting_flag='1'";
                if (!mysqli_query($conn_db, $setting_sql)) {
                    $response["message"] = "Error initiating read saved settings";
                    mysqli_close($conn_db);
                    echo json_encode($response);
                    exit();
                }

                $response["status"] = "success";
                $response["message"] = $user_activity." successfully";
                break;


                case 'RESET_DEVICE':

                $user_activity = "IoT Device reset initiated";
                $device_id=strtoupper($device_id);

               // Updated SQL query for `iot_device_id_change` table
                $sql_mode = "INSERT INTO `iot_device_reset` (`device_id`, `reset`, `date_time`, `user_mobile`, `email`, `name`, `role`) 
                VALUES ( ?, ?, current_timestamp(), ?, ?, ?, ?)";

                $stmt = mysqli_prepare($conn_db, $sql_mode);
                if ($stmt) {
                    // Bind parameters: s (string) for each parameter
                    $parameter_value=strtoupper(trim($parameter_value));
                    mysqli_stmt_bind_param($stmt, 'ssssss', $device_id, $parameter_value, $mobile_no, $user_email, $user_name, $role);

                    $setting_sql = "INSERT INTO device_settings (`setting_type`, `setting_flag`) VALUES ('RESET', '1') 
                    ON DUPLICATE KEY UPDATE setting_flag='1'";
                    mysqli_query($conn_db, $setting_sql);
                    mysqli_stmt_execute($stmt);
                    mysqli_stmt_close($stmt);                   
                } else {
                    $response["message"] = "Error preparing query";
                    mysqli_close($conn_db);
                    echo json_encode($response);
                    exit();
                }


                $response["status"] = "success";
                $response["message"] = $user_activity." successfully";
                break;

                case 'ADDRESS':                
                $street= sanitize_input($_POST['STREET'], $conn_db);
                $town=sanitize_input($_POST['AREA'], $conn_db);
                $city= sanitize_input($_POST['CITY'], $conn_db);
                $district= sanitize_input($_POST['DISTRICT'], $conn_db);
                $state=sanitize_input($_POST['STATE'], $conn_db);
                $pincode=sanitize_input($_POST['PINCODE'], $conn_db);
                $landmark=sanitize_input($_POST['LANDMARK'], $conn_db);
                $country = sanitize_input($_POST['PARAMETER'], $conn_db);


                $user_activity = "Address updated";
                $device_id=strtoupper($device_id);
                $sql_mode = "INSERT INTO `device_address` (`device_id`, `street`, `town`, `city`, `district`, `state`, `pincode`, `country`, `landmark`, `date_time`, `user_mobile`, `email`, `name`, `role`) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, current_timestamp(), ?, ?, ?, ?)";

                $stmt = mysqli_prepare($conn_db, $sql_mode);
                if ($stmt) {
                    mysqli_stmt_bind_param($stmt, 'sssssssssssss', $device_id, $street, $town, $city, $district, $state, $pincode, $country, $landmark, $mobile_no, $user_email, $user_name, $role);

                    if ( mysqli_stmt_execute($stmt)) {
                        $response["status"] = "success";
                        $response["message"] = "Address updated successfully";
                    } else {
                        $response["message"] = "Error updating record" . mysqli_error($conn_db);
                    }
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

        // Prepare and execute the user activity log insertion query

            $log_sql = "INSERT INTO user_activity_log (`updated_field`, `date_time`, `user_mobile`, `email`, `name`, `role`) 
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
