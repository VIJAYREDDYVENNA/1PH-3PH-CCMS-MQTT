<?php
require_once '../../base-path/config-path.php';
require_once BASE_PATH_1 . 'config_db/config.php';
require_once BASE_PATH_1 . 'session/session-manager.php';

SessionManager::checkSession();
$sessionVars = SessionManager::SessionVariables();

$permissionVariables = $_SESSION['permission_variables'];  // Example: "on_off_control, on_off_mode, device_info_update"

// Dynamically build the query
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['user_id'])) {
	$userId = filter_input(INPUT_POST, 'user_id', FILTER_SANITIZE_NUMBER_INT); 
	$conn = mysqli_connect(HOST, USERNAME, PASSWORD, DB_USER);

	if (!$conn) {
		$response = ['status' => 'error', 'message' => "Connection failed: " . mysqli_connect_error()];
		sendResponse($response);
	}

    // Prepare dynamic select query
	$userId = sanitize_input($userId, $conn);
	$query = "SELECT $permissionVariables FROM user_permissions WHERE login_id = ?";

	$stmt = mysqli_prepare($conn, $query);
	if ($stmt) {
		mysqli_stmt_bind_param($stmt, 'i', $userId);
		mysqli_stmt_execute($stmt);

        // Dynamically bind result variables
		$resultVars = [];
        $permissionFields = explode(',', $permissionVariables);  // Split the permission variables into an array
        $bindResultParams = [];
        
        foreach ($permissionFields as $key => $field) {
            $field = trim($field);  // Trim any extra whitespace
            $bindResultParams[$field] = null;  // Initialize with null to bind the result later
            $resultVars[] = &$bindResultParams[$field];  // Add references to the result variables
        }

        call_user_func_array(array($stmt, 'bind_result'), $resultVars);  // Dynamically bind result variables

        mysqli_stmt_fetch($stmt);
        mysqli_stmt_close($stmt);

        // Prepare response array with dynamic field names and their values
        $permissions = [];
        foreach ($permissionFields as $field) {
            $field = trim($field);  // Ensure no extra whitespace in field names
            $permissions[$field] = $bindResultParams[$field];
        }

        $response = ['status' => 'success', 'permissions' => $permissions];
        sendResponse($response);
    } else {
    	$response = ['status' => 'error', 'message' => 'Error preparing the statement.'];
    	sendResponse($response);
    }

    mysqli_close($conn);
}

function sendResponse($response) {
	header('Content-Type: application/json');
	echo json_encode($response);
	exit();
}

function sanitize_input($data, $conn) {
	$data = trim($data);
	$data = stripslashes($data);
	$data = htmlspecialchars($data);
	return mysqli_real_escape_string($conn, $data);
}
?>
