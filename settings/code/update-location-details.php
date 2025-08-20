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
$parameter = "COORDINATES";
$parameter_value = "17.467080, 78.599158";*/


$conn = mysqli_connect(HOST, USERNAME, PASSWORD, DB_USER);
if (!$conn) {
    $response["message"] = "Connection to User database failed: " . mysqli_connect_error();
    echo json_encode($response);
    exit();
}
$sql = "SELECT device_info_update FROM user_permissions WHERE login_id = ?";
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
        $parameter_value = sanitize_input($_POST['PARAMETER'], $conn);
        $parameter = sanitize_input($_POST['UPDATED_STATUS'], $conn);

        mysqli_close($conn);
        if($parameter=="COORDINATES")
        {
            $latLongPattern = '/^-?([1-8]?\d(\.\d+)?|90(\.0+)?),\s*-?((1[0-7]\d(\.\d+)?|180(\.0+)?)|((\d|[1-9]\d)(\.\d+)?))$/';

            if (!preg_match($latLongPattern, $parameter_value)) 
            {
                $response["message"]= "Invalid coordinates. Please enter valid latitude and longitude values in the format 'latitude,longitude'.";
                exit();
            } 
        }
        else if($parameter=="COORDINATES_CHANGE"){
            if (filter_var($parameter_value, FILTER_VALIDATE_INT, array("options" => array("min_range"=>0, "max_range"=>2))) === false)
            {
                $response["message"] = "Invalid Updated Value ";           
                echo json_encode($response);
                exit();
            }

        }
        else
        {

            if($parameter!=="ADDRESS")
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
            $conn_db = mysqli_connect(HOST, USERNAME, PASSWORD, $device_id);
            if (!$conn_db) {
                $response["message"] = "Connection to device database failed";
                echo json_encode($response);
                exit();
            }
            $user_activity = "";
            $update_parameter="";

            switch (trim($parameter)) {

                case 'COORDINATES':

                $user_activity = "Coordinates updated";
                $device_id=strtoupper($device_id);



                $lat_long=explode(",", $parameter_value);
                $lat_long[0]=trim($lat_long[0]);
                $lat_long[1]=trim($lat_long[1]);

                // Just converting latitude and longitudes to ddm format

                $dec=(int)$lat_long[0];
                $deg=(int)(($lat_long[0]-$dec)*60);
                $min=($lat_long[0]-$dec-$deg/60)*60;
                $ddm_lat=round($dec*100+$deg+$min, 5);

                $dec=(int)$lat_long[1];
                $deg=(int)(($lat_long[1]-$dec)*60);
                $min=($lat_long[1]-$dec-$deg/60)*60;
                $ddm_long=round($dec*100+$deg+$min, 5); 

                $ddm_lat_long=$ddm_lat.','.$ddm_long;

                $sql_mode = "INSERT INTO `coordinates_list` (`device_id`, `latitude`, `longitude`, `lat_long_ddm_format`, `date_time`, `user_mobile`, `email`, `name`, `role`) 
                VALUES (?, ?, ?, ?, current_timestamp(), ?, ?, ?, ?)";

                $stmt = mysqli_prepare($conn_db, $sql_mode);
                if ($stmt) {
                    mysqli_stmt_bind_param($stmt, 'ssssssss', $device_id, $lat_long[0], $lat_long[1], $ddm_lat_long, $mobile_no, $user_email, $user_name, $role);
                    mysqli_stmt_execute($stmt);
                    mysqli_stmt_close($stmt);
                } else {
                    $response["message"] = "Error preparing coordinates query";
                    mysqli_close($conn_db);
                    echo json_encode($response);
                    exit();
                }

                $response["status"] = "success";
                $response["message"] = $user_activity." successfully";
                break;

                case 'COORDINATES_CHANGE':
                $user_activity = "Device GPS location update disabled updated";
                if($parameter_value)
                {
                    $user_activity = "Device GPS location update enabled updated";
                }

                $device_id=strtoupper($device_id);

                $sql_latest = "SELECT id FROM coordinates_list WHERE device_id = ? ORDER BY date_time DESC LIMIT 1";
                $stmt_latest = mysqli_prepare($conn_db, $sql_latest);
                mysqli_stmt_bind_param($stmt_latest, "s", $device_id);
                mysqli_stmt_execute($stmt_latest);
                mysqli_stmt_bind_result($stmt_latest, $id);
                mysqli_stmt_fetch($stmt_latest);
                mysqli_stmt_close($stmt_latest);

                if ($id) 
                {
                    $sql_update = "UPDATE coordinates_list SET update_status = $parameter_value WHERE id = ?";
                    $stmt_update = mysqli_prepare($conn_db, $sql_update);
                    mysqli_stmt_bind_param($stmt_update, "i", $id);
                    if (mysqli_stmt_execute($stmt_update)) {
                        $response["status"] = "success";
                        $response["message"] = "Updated successfully";
                    } else {
                        $response["message"] = "Error updating record: " . mysqli_error($conn_db);
                    }
                    mysqli_stmt_close($stmt_update);
                } else {
                    $response["message"] = "Error: Provide the coordinate to enable it.";
                }
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
                        $response["message"] = "Error updating record: " . mysqli_error($conn_db);
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
