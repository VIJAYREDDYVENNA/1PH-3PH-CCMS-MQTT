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

//$group = "district";

if ($_SERVER["REQUEST_METHOD"] == "POST") 
{
    $group = trim($_POST['GROUP']);

    $conn = mysqli_connect(HOST, USERNAME, PASSWORD, DB_USER);
    if (!$conn) {
        $response["message"] = "Connection to User database failed: " . mysqli_connect_error();
        echo json_encode($response);
        exit();
    }

    $group = sanitize_input($group, $conn);

    $user_activity = "";
    $update_parameter="";

    $setting_sql = "INSERT INTO device_selection_group (`login_id`, `group_by`) VALUES ('$user_login_id', '$group') 
    ON DUPLICATE KEY UPDATE group_by='$group'";
    
    if (!mysqli_query($conn, $setting_sql)) {
        $response["message"] = "Error: updating Group  ";
        mysqli_close($conn);
        echo json_encode($response);
        exit();
    }
    $group_by_column=$group;
    $sql_group_list = "SELECT `$group_by_column` AS `group_list`  FROM device_list_by_group  WHERE login_id = ?  GROUP BY `$group_by_column` ORDER BY `$group_by_column`";

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
    $response["status"] = "success";
    $response["message"] = "Successfully Updated.."; 

    $_SESSION["GROUP_LIST"] = json_encode($group_list);
    $response["group_list"] = $group_list;

    mysqli_close($conn);
    echo json_encode( $response);

}
else
{ 
    $response["message"] = "Invalid request method.";

}



// Function to sanitize input data
function sanitize_input($data, $conn) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return mysqli_real_escape_string($conn, $data);
}

?>
