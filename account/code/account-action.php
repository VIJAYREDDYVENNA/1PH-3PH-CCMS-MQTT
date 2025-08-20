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


if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['user_id'], $_POST['action'])) {
    $userId = filter_input(INPUT_POST, 'user_id', FILTER_SANITIZE_NUMBER_INT);   
    $action = filter_input(INPUT_POST, 'action', FILTER_SANITIZE_STRING);
    
    $conn = mysqli_connect(HOST, USERNAME, PASSWORD, DB_USER);
    if (!$conn) 
    {
        $response = ['status' => 'error', 'message' => "Connection failed: " . mysqli_connect_error()];
        sendResponse($response);
    }
    $sql = "SELECT `user_details_updates` FROM user_permissions WHERE login_id = ?";
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
    } else {
        $response['status'] = 'error';
        $response["message"] = "Error preparing query for user permissions: " . mysqli_error($conn);
        mysqli_close($conn);
        sendResponse($response);
    }

    if ($permission_check == 1) {

        $userId = sanitize_input($userId, $conn);
        $action = sanitize_input($action, $conn);
        $updateQuery ="";
        // Determine the query based on the action
        if ($action == "ACTIVATE") {
            $updateQuery = "UPDATE login_details SET account_delete = '1', status = 'ACTIVE' WHERE id = ?";
        } elseif ($action == "HOLD") {
            $updateQuery = "UPDATE login_details SET account_delete = '1', status = 'HOLD' WHERE id = ?";
        } elseif ($action == "DELETE") {
            $updateQuery = "UPDATE login_details SET account_delete = '0' WHERE id = ?";
        }
        else{
            $response = ['status' => 'error', 'message' => 'Invalid Action.'];
            mysqli_close($conn);
            sendResponse($response);
        }

        // Prepare the statement
        $updateStmt = mysqli_prepare($conn, $updateQuery);

        if ($updateStmt === false) {
            $response = ['status' => 'error', 'message' => 'Failed to prepare the statement.'];
        } 
        else 
        {
            // Bind the login_id parameter (assuming it's an integer)
            mysqli_stmt_bind_param($updateStmt, 'i', $userId);

            // Execute the statement
            if (mysqli_stmt_execute($updateStmt)) {
                $response = ['status' => 'success', 'message' => 'Updated successfully.'];
            } else {
                $response = ['status' => 'error', 'message' => 'Failed to execute the update.'];
            }

            // Close the statement
            mysqli_stmt_close($updateStmt);
        }

        mysqli_close($conn);
        sendResponse($response);
    }

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
?>
