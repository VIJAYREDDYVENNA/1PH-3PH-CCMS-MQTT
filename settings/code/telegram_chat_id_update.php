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

if (isset($_POST['CHAT_ID']) && isset($_POST['SAVE'])) {    
	$chat_id = filter_input(INPUT_POST, 'CHAT_ID', FILTER_SANITIZE_STRING);
	$group_name = filter_input(INPUT_POST, 'GROUP_NAME', FILTER_SANITIZE_STRING);

	$conn = mysqli_connect(HOST, USERNAME, PASSWORD, DB_USER);
	if (!$conn) {
		$response['message'] = "Connection failed: " . mysqli_connect_error();
		sendResponse($response);
	} else {
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
				$response['message'] = "You do not have permission to update.";
				mysqli_close($conn);
				sendResponse($response);
			}
		} else {
			$response['message'] = "Error checking permissions ";
			mysqli_close($conn);
			sendResponse($response);
		}

		if ($permission_check == 1) {
			date_default_timezone_set('Asia/Kolkata');
			$date = date("Y-m-d H:i:s");

            // Check if the chat_id already exists
			$check_sql = "SELECT `group_name` FROM `telegram_groups_new` WHERE `chat_id` = '$chat_id'";
			$result = mysqli_query($conn, $check_sql);

			if (mysqli_num_rows($result) > 0) {
				$row = mysqli_fetch_assoc($result);
				$response['message'] = "A group with the name '{$row['group_name']}' already exists.";
			} else {
                // Insert the new record
				$insert_sql = "INSERT INTO `telegram_groups_new` (`group_name`, `chat_id`, `token`, `date_time`, `user_id`) 
				VALUES ('$group_name', '$chat_id', 'bot5216794704:AAGkjWy3JDm-5wBaYWGwVwwnuvWrkd5QzgE', '$date', '$user_login_id')";

				if (mysqli_query($conn, $insert_sql)) {
					$response['status'] = 'success';
					$response['message'] = "Group successfully saved.";
				} else {
					$response['message'] = "Error saving group ";
				}
			}
		}
	}
	mysqli_close($conn);
	sendResponse($response);

} elseif (isset($_POST['CHAT_ID']) && isset($_POST['CHECK'])) {
	$chat_id = $_POST['CHAT_ID'];
	try {
		$TG_ALERT_URL = 'https://api.telegram.org/bot5216794704:AAGkjWy3JDm-5wBaYWGwVwwnuvWrkd5QzgE/sendMessage?chat_id=' . $chat_id . '&text=Hi, this is a confirmation message, please update the same without changing the chat ID';
		$get_msg = @file_get_contents($TG_ALERT_URL);

		if ($get_msg === FALSE) {
			$response['message'] = "Invalid Chat-ID, please check and try again.";
			sendResponse($response);
		}

		$response['status'] = 'success';
		$response['message'] = "Check your Telegram group for the confirmation message.";

	} catch (Exception $e) {
		$response['message'] = "Error occurred, please check the Chat-ID and try again.";
	}

	sendResponse($response);

} else {
	$response['message'] = "Invalid request. Please try again.";
	sendResponse($response);
}

// Send JSON response
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
