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

$response = ["status" => "error", "message" => "", "group_list" => ""];


$conn = mysqli_connect(HOST, USERNAME, PASSWORD, DB_USER);
if (!$conn) {
    $response["message"] = "Connection to User database failed: " . mysqli_connect_error();
    echo json_encode($response);
    exit();
}
$sql = "SELECT create_group FROM user_permissions WHERE login_id = ?";
$stmt = mysqli_prepare($conn, $sql);
if ($stmt) {
    mysqli_stmt_bind_param($stmt, "s", $user_login_id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $permission_check);
    mysqli_stmt_fetch($stmt);
    mysqli_stmt_close($stmt);

    if ($permission_check != 1) {
        $response["message"] = "No Permission to add/change the device(s) Group";
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
        $group = sanitize_input($_POST['GROUP'], $conn);
        $new_group = sanitize_input($_POST['NEW_GROUP'], $conn);
        $city_or_town=sanitize_input($_POST['TOWN'], $conn);
        $district= sanitize_input($_POST['DISTRICT'], $conn);
        $state=sanitize_input($_POST['STATE'], $conn);

        $device_ids_array = explode(",", $device_ids);
        foreach ($device_ids_array as $device_id) 
        {
            $device_id = trim(strtolower($device_id));
            if (!preg_match('/^[a-z0-9_]+$/', $device_id)) {
                $response["message"] = $device_id." Invalid device(s) ID";
                echo json_encode($response);
                exit();
            }
        }

        $user_activity = "";
        $update_parameter="";

        switch ($group) {

            case 'EXISTING':
            $state = "";
            $district = "";
            $city_or_town = "";
            $group_name = "";

            $user_activity = "Added the device(s) to the Group";
            $device_id = strtoupper($device_id);


            $sql_group_list = "SELECT `state`, `district`, `city_or_town`, `device_group_or_area` FROM device_list_by_group WHERE login_id = ? AND s_id = ? LIMIT 1";


            $stmt = mysqli_prepare($conn, $sql_group_list);

            if ($stmt) {
                mysqli_stmt_bind_param($stmt, "ss", $user_login_id, $new_group); // Bind parameters
                mysqli_stmt_execute($stmt);
                $result = mysqli_stmt_get_result($stmt);

                if (mysqli_num_rows($result) > 0) {
                    $row = mysqli_fetch_assoc($result);
                    $state = $row['state'];
                    $district = $row['district'];
                    $city_or_town = $row['city_or_town'];
                    $group_name = $row['device_group_or_area'];
                    
                } else {
                    $response["message"] = "Group details are not available.";
                    echo json_encode($response);
                    mysqli_close($conn);
                    exit();
                }
            } else {
                $response["message"] = "Database query failed.";
                echo json_encode($response);
                mysqli_close($conn);
                exit();
            }


            $device_id_array = explode(',', $device_ids);

            $sql_mode = "INSERT INTO `devices_group` (`device_id`, `state`, `district`, `city_or_town`, `device_group_or_area`)
            VALUES (?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE
            `state` = VALUES(`state`),
            `district` = VALUES(`district`),
            `city_or_town` = VALUES(`city_or_town`),
            `device_group_or_area` = VALUES(`device_group_or_area`)";

            $stmt = mysqli_prepare($conn, $sql_mode);

            if ($stmt) {
                foreach ($device_id_array as $device_id) {
                    mysqli_stmt_bind_param($stmt, 'sssss', $device_id, $state, $district, $city_or_town, $group_name);
                    mysqli_stmt_execute($stmt);
                }
                mysqli_stmt_close($stmt);
            } else {
                $response["message"] = "Error preparing the query.";
                mysqli_close($conn);
                echo json_encode($response);
                exit();
            }
            $response["status"] = "success";
            $response["message"] = $user_activity." successfully";
            break;

            case 'CREATE_NEW':  
            $user_activity = "Created New Group/area and added the device(s) to the Group";
            $device_id=strtoupper($device_id);

            $sql_group_list = "SELECT `state`, `district`, `city_or_town`, `device_group_or_area` FROM device_list_by_group WHERE device_group_or_area = ? LIMIT 1";
            $stmt = mysqli_prepare($conn, $sql_group_list);

            if ($stmt) {
                mysqli_stmt_bind_param($stmt, "s", $new_group); 
                mysqli_stmt_execute($stmt);
                $result = mysqli_stmt_get_result($stmt);

                if (mysqli_num_rows($result) > 0) {
                    $response["message"] = "The group/area already exists. Please enter another name.";
                    mysqli_close($conn);
                    echo json_encode($response);
                    exit();
                }
                mysqli_stmt_close($stmt);
            } else {
                $response["message"] = "Database query failed.";
                mysqli_close($conn);
                echo json_encode($response);
                exit();
            }


            $device_id_array = explode(',', $device_ids);

            $sql_mode = "INSERT INTO `devices_group` (`device_id`, `state`, `district`, `city_or_town`, `device_group_or_area`)
            VALUES (?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE
            `state` = VALUES(`state`),
            `district` = VALUES(`district`),
            `city_or_town` = VALUES(`city_or_town`),
            `device_group_or_area` = VALUES(`device_group_or_area`)";

            $stmt = mysqli_prepare($conn, $sql_mode);

            if ($stmt) {
                $state=  strtoupper($state);
                $district= strtoupper($district);
                $city_or_town=strtoupper($city_or_town);
                $new_group=strtoupper($new_group);
                foreach ($device_id_array as $device_id) {
                    mysqli_stmt_bind_param($stmt, 'sssss', $device_id, $state, $district, $city_or_town, $new_group);
                    mysqli_stmt_execute($stmt);
                }
                mysqli_stmt_close($stmt);
            } else {
                $response["message"] = "Error preparing the query.";
                mysqli_close($conn);
                echo json_encode($response);
                exit();
            }
            $response["status"] = "success";
            $response["message"] = $user_activity." successfully";
            break;

            default:
            mysqli_close($conn);
            $response["message"] = "Something went wrong..";
            echo json_encode($response);
            exit();
            break;
        }

        
        $group_by_column="device_group_or_area";
        $query = "SELECT `group_by` FROM device_selection_group WHERE login_id = ?";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "i", $user_login_id);  
        mysqli_stmt_execute($stmt);
        mysqli_stmt_bind_result($stmt, $group_by_column);
        mysqli_stmt_fetch($stmt);
        mysqli_stmt_close($stmt);

        $sql_group_list = "SELECT `$group_by_column` AS `group_list`  FROM device_list_by_group  WHERE login_id = ?  GROUP BY `$group_by_column` ORDER BY `$group_by_column`";


        $txt =$sql_group_list."\n";
        $myfile = file_put_contents('logs.txt', $txt.PHP_EOL , FILE_APPEND | LOCK_EX);


        $stmt = mysqli_prepare($conn, $sql_group_list);
        mysqli_stmt_bind_param($stmt, "i", $user_login_id);

        if (mysqli_stmt_execute($stmt)) {
            $results = mysqli_stmt_get_result($stmt);
            if (mysqli_num_rows($results) > 0) {
                while ($r = mysqli_fetch_assoc($results)) {
                    $group_list[] = array("GROUP" => strtoupper($r['group_list']));
                }
            }
        }

        $_SESSION["GROUP_LIST"] = json_encode($group_list);
        $response["group_list"] = $group_list;

        $log_sql = "INSERT INTO user_activity_log (`updated_field`, `date_time`, `user_mobile`, `email`, `name`, `role`) 
        VALUES (?, current_timestamp(), ?, ?, ?, ?)";
        $log_stmt = mysqli_prepare($conn, $log_sql);
        if ($log_stmt) {
            mysqli_stmt_bind_param($log_stmt, "sssss", $user_activity, $mobile_no, $user_email, $user_name, $role);
            mysqli_stmt_execute($log_stmt);
            mysqli_stmt_close($log_stmt);
        } else {
            $response["message"] = "Error preparing user activity log query";
            mysqli_close($conn);
            echo json_encode($response);
            exit();
        }


        mysqli_close($conn);

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
