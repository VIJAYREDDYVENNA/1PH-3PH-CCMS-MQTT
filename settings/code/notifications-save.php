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
$sanitizedparamters;
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['D_ID'])&&isset($_POST['parameters'])) {
    $D_ID = filter_input(INPUT_POST, 'D_ID', FILTER_SANITIZE_STRING);  
    $parameters = $_POST['parameters'];  

    $conn = mysqli_connect(HOST, USERNAME, PASSWORD, DB_USER);
    if (!$conn) {
        $response = ['status' => 'error', 'message' => "Connection failed: " . mysqli_connect_error()];
        sendResponse($response);
    }
    $sql = "SELECT `notification_update` FROM user_permissions WHERE login_id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "s", $user_login_id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_bind_result($stmt, $permission_check);
        mysqli_stmt_fetch($stmt);
        mysqli_stmt_close($stmt);

        if ($permission_check != 1) {
            $response['status'] = 'error';
            $response["message"] = "This account doesn't have permission to update.";
            mysqli_close($conn);
            sendResponse($response);
        }
        else{
            $D_ID = sanitize_input($D_ID, $conn);
            $sanitizedparamters = sanitize_perameters($parameters, $conn);
            mysqli_close($conn);

        }
    } else {
        $response['status'] = 'error';
        $response["message"] = "Error preparing query for user permissions: " . mysqli_error($conn);
        mysqli_close($conn);
        sendResponse($response);
    }


    if ($permission_check == 1) {

        $fields = implode(', ', array_keys($sanitizedparamters));
        $placeholders = implode(', ', array_fill(0, count($sanitizedparamters), '?'));



        $conn_db = mysqli_connect(HOST, USERNAME, PASSWORD);
        if (!$conn_db) {
            $response = ['status' => 'error', 'message' =>"Connection to device database failed"];
            sendResponse($response);
        }
        $device_ids_array = explode(",", $D_ID);
        foreach ($device_ids_array as $device_id) 
        {
            $db=trim(strtolower($device_id));
            $device_id=strtoupper($device_id);
            try {

                $insertQuery = "INSERT INTO `$db`.`notification_updates` (device_id, $fields) VALUES (?, $placeholders)";
                $insertStmt = mysqli_prepare($conn_db, $insertQuery);
                $bindTypes = 's' . str_repeat('i', count($sanitizedparamters));  
                $insertValues = array_merge([$device_id], array_values($sanitizedparamters));
                mysqli_stmt_bind_param($insertStmt, $bindTypes, ...$insertValues);

                if (!mysqli_stmt_execute($insertStmt)) {
                    $response = ['status' => 'error', 'message' => 'Failed to update for device -'. $device_id. ' and continue for remaining device(s)'];
                    mysqli_close($conn_db);
                    sendResponse($response);
                }

                mysqli_stmt_close($insertStmt);
            } catch (Exception $e) {
                $response = ['status' => 'error', 'message' => 'Somthing went wrong while updating device -'. $device_id. ' and continue for remaining device(s)'];
                mysqli_close($conn_db);
                sendResponse($response);
            }
        }
    }
    mysqli_close($conn_db);
    $response = ['status' => 'success', 'message' => 'Notification settings updated successfully.'];
    sendResponse($response);
}

function sendResponse($response) {
    header('Content-Type: application/json');
    echo json_encode($response);
    exit();
}

// Sanitize user input to prevent SQL injection

function sanitize_input($data, $conn) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return mysqli_real_escape_string($conn, $data);
}

function sanitize_perameters($parameters, $conn) {

    $sanitizedParameters = [];
    $validFields = ['voltage', 'overload', 'power_fail', 'on_off', 'mcb_contactor_trip', 'door_alert'];    

    foreach ($parameters as $field => $value) {
        $field = stripslashes($field);
        $field = htmlspecialchars($field);
        $field = mysqli_real_escape_string($conn, $field);

        $value = stripslashes( $value);
        $value = htmlspecialchars( $value);
        $value = mysqli_real_escape_string($conn, $value);
        if (in_array($field, $validFields)) {
            $sanitizedParameters[$field] = $value;
        }
    }
    return $sanitizedParameters;
}
?>
