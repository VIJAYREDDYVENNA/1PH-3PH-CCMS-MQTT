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
$user_email = $sessionVars['user_email'];
$user_login_id = $sessionVars['user_login_id'];

$permission_check = 0;
$response = ["status" => "error", "message" => ""];

if (isset($_POST['ID']) && isset($_POST['CHAT_ID'])) {
	$id = filter_input(INPUT_POST, 'ID', FILTER_SANITIZE_STRING);
	$group_name = filter_input(INPUT_POST, 'GROUP', FILTER_SANITIZE_STRING);
	$chat_id = filter_input(INPUT_POST, 'CHAT_ID', FILTER_SANITIZE_STRING);

	$conn = mysqli_connect(HOST, USERNAME, PASSWORD, DB_USER);
	if (!$conn) {
		die("Connection failed: " . mysqli_connect_error());
	} else {
		$id = sanitize_input($id, $conn);
		$group_name = sanitize_input($group_name, $conn);
		$chat_id = sanitize_input($chat_id, $conn);

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

            // Fetch group id
			$sql = "SELECT `id` FROM telegram_groups_new WHERE group_name = ? AND chat_id = ?";
			$stmt = mysqli_prepare($conn, $sql);
			if ($stmt) {
				mysqli_stmt_bind_param($stmt, "ss", $group_name, $chat_id);
				mysqli_stmt_execute($stmt);
				$result = mysqli_stmt_get_result($stmt);
				if (mysqli_num_rows($result) > 0) {
					$r = mysqli_fetch_assoc($result);
					$group_id = $r["id"];
				}
				mysqli_stmt_close($stmt);
			}

            // Prepare device-group pairs
			$device_group_pairs = [];
			foreach (explode(',', $id) as $deviceId) {
				$device_group_pairs[] = "('" . strtoupper(trim($deviceId)) . "', '" . $group_id . "')";
			}

            // UPSERT query
			$query = "INSERT INTO `telegram_groups_devices` (`device_id`, `group_id`) VALUES " . implode(",", $device_group_pairs) . " ON DUPLICATE KEY UPDATE `group_id` = VALUES(`group_id`)";
			if (mysqli_query($conn, $query)) {
				if (mysqli_affected_rows($conn) > 0) {
					$response['status'] = 'success';
					$response['message'] = "Successfully Saved";
				} else {
					$response['status'] = 'error';
					$response['message'] = "No changes made. Already Exist";
				}
			} else {
				$response['status'] = 'error';
				$response['message'] = "Error preparing UPSERT query: " . mysqli_error($conn);
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
