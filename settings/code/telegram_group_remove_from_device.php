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

$permission_check = 0;
$response = ["status" => "error", "message" => ""];

if (isset($_POST['ID']) && isset($_POST['GROUP_ID'])) {
	$id = filter_input(INPUT_POST, 'ID', FILTER_SANITIZE_STRING);
	$GROUP_ID = filter_input(INPUT_POST, 'GROUP_ID', FILTER_SANITIZE_STRING);

	$send = "";
	$conn = mysqli_connect(HOST, USERNAME, PASSWORD, DB_USER);
	if (!$conn) {
		die("Connection failed: " . mysqli_connect_error());
	} else {
		$id = sanitize_input($id, $conn);
		$GROUP_ID = sanitize_input($GROUP_ID, $conn);

        // Check user permissions
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
				$response['message'] = "This account doesn't have permission to update.";
				mysqli_close($conn);
				sendResponse($response);
			}
		} else {
			$response['status'] = 'error';
			$response['message'] = "Error preparing query for user permissions: " . mysqli_error($conn);
			mysqli_close($conn);
			sendResponse($response);
		}

		if ($permission_check == 1) {
			date_default_timezone_set('Asia/Kolkata');
			$date = date("Y-m-d H:i:s");
			$group_id = 0;

            // Get group id from telegram_groups_new
			$sql = "SELECT `id` FROM telegram_groups_new WHERE chat_id = '$GROUP_ID' LIMIT 1";
			$result = mysqli_query($conn, $sql);
			if ($result && mysqli_num_rows($result) > 0) {
				$r = mysqli_fetch_assoc($result);
				$group_id = $r["id"];
			}

            // Prepare multiple device query
			$device_list = explode(',', $id);
			$multi_query = "";
			foreach ($device_list as $device) {
				$multi_query .= "('" . strtoupper(trim($device)) . "', '" . $group_id . "'),";
			}
			$multi_query = rtrim($multi_query, ',');

			$qry = "DELETE FROM `telegram_groups_devices` WHERE (`device_id`, `group_id`) IN ($multi_query)";

			if (mysqli_query($conn, $qry)) {
				$response['status'] = 'success';
				$response['message'] = "Successfully Removed";
			} else {
				$response['status'] = 'error';
				$response['message'] = "Please try again.";
			}
		}

		mysqli_close($conn);
		sendResponse($response);
	}
} else {
	$response['status'] = 'error';
	$response['message'] = "Please try again.";
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
?>
