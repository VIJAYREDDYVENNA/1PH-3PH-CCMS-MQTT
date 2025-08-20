<?php
require_once '../../base-path/config-path.php';
require_once BASE_PATH_1 . 'config_db/config.php';
require_once BASE_PATH_1 . 'session/session-manager.php';

// Check session and retrieve session variables
SessionManager::checkSession();
$sessionVars = SessionManager::SessionVariables();
$mobile_no = $sessionVars['mobile_no'];
$user_id = $sessionVars['user_id'];
$role = $sessionVars['role'];
$user_login_id = $sessionVars['user_login_id'];


$permission_check=0;

// Handle POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['DEVICES'])) {
    // Retrieve the devices JSON string
	$devices_json = filter_input(INPUT_POST, 'DEVICES', FILTER_SANITIZE_STRING);
	$installed_date = filter_input(INPUT_POST, 'ACTION_DATE', FILTER_SANITIZE_STRING);
	$installed_status = filter_input(INPUT_POST, 'STATUS', FILTER_SANITIZE_STRING);
	$devices_json = htmlspecialchars_decode($devices_json);

    // Decode the JSON string to a PHP array
	$devices = json_decode($devices_json, true);

    // Check if decoding was successful
	if (json_last_error() !== JSON_ERROR_NONE) {
		sendResponse(['success' => false, 'message' => 'Invalid JSON format for devices.']);
	}


	$conn = mysqli_connect(HOST, USERNAME, PASSWORD, DB_USER);

	if (!$conn) {
		sendResponse(['success' => false, 'message' => "Connection failed"]);
	} else {
        // Check user permissions
		$sql = "SELECT installation_status_update FROM user_permissions WHERE login_id = ?";
		$stmt = mysqli_prepare($conn, $sql);
		if ($stmt) {
			mysqli_stmt_bind_param($stmt, "s", $user_login_id);
			mysqli_stmt_execute($stmt);
			mysqli_stmt_bind_result($stmt, $permission_check);
			mysqli_stmt_fetch($stmt);
			mysqli_stmt_close($stmt);

			if ($permission_check != 1) {
				/*$response['status'] = false;
				$response["message"] = "This account doesn't have permission to update.";*/				
				mysqli_close($conn);
				sendResponse(['success' => false, 'message' => "This account doesn't have permission to update."]);
			}
		} else {
			mysqli_close($conn);
			sendResponse(['success' => false, 'message' => "Error preparing query for user permissions."]);
			
		}
		mysqli_close($conn);
	}

        // If the user has permission, proceed with the uniqueness check and update
	if ($permission_check == 1) {




    // Database connection
		$conn_db_all = mysqli_connect(HOST, USERNAME, PASSWORD, DB_ALL);

		if (!$conn_db_all) {
			sendResponse(['success' => false, 'message' => 'Database connection error.']);
		}

    // Sanitize the inputs
		$installed_date = sanitize_input($installed_date, $conn_db_all);
		$installed_status = sanitize_input($installed_status, $conn_db_all);
		$devices = sanitize_devices($devices, $conn_db_all);
		$update_status=0;

		if($installed_status==="install")
		{
			$update_status=1;
		}
		elseif($installed_status==="uninstall")
		{
			$update_status=0;
		}






    // Prepare the SQL query
		$query = "INSERT INTO live_data_updates (device_id, installed_status, installed_date) 
		VALUES (?, $update_status, ?) 
		ON DUPLICATE KEY UPDATE installed_status = $update_status, installed_date = VALUES(installed_date)";

    // Prepare the statement
		if ($stmt = mysqli_prepare($conn_db_all, $query)) {
        // Iterate over the devices and execute the query
			foreach ($devices as $device_id) {
            // Bind parameters
            mysqli_stmt_bind_param($stmt, "ss", $device_id, $installed_date);  // 'ss' = string, string

            // Execute the query
            if (!mysqli_stmt_execute($stmt)) {
            	sendResponse(['success' => false, 'message' => 'Error updating device ID: ' . $device_id]);
            }
        }

        // Close the prepared statement
        mysqli_stmt_close($stmt);

        // Return a success message
        sendResponse(['success' => true, 'message' => 'Devices updated successfully.']);
    } else {
    	sendResponse(['success' => false, 'message' => 'Error preparing query.']);
    }

    // Close the database connection
    mysqli_close($conn_db_all);
}
} else {
	sendResponse(['success' => false, 'message' => 'Invalid request or missing data.']);
}

// Function to sanitize inputs
function sanitize_input($data, $conn) {
	$data = trim($data);
	$data = stripslashes($data);
	$data = htmlspecialchars($data);
	return mysqli_real_escape_string($conn, $data);
}

// Function to sanitize devices
function sanitize_devices($device_list, $conn) {
	$sanitizedDevices = [];
	foreach ($device_list as $device_id) {
        // Sanitize the device ID
		$sanitizedDevices[] = mysqli_real_escape_string($conn, $device_id);
	}
	return $sanitizedDevices;
}

// Function to send response as JSON
function sendResponse($response) {
	header('Content-Type: application/json');
	echo json_encode($response);
	exit();
}
?>
