<?php
require_once '../../base-path/config-path.php';
require_once BASE_PATH_1.'config_db/config.php';
require_once BASE_PATH_1.'session/session-manager.php';
SessionManager::checkSession();
$sessionVars = SessionManager::SessionVariables();

$mobile_no = $sessionVars['mobile_no'];
$user_id = $sessionVars['user_id'];
$role = $sessionVars['role'];
$user_login_id = $sessionVars['user_login_id'];

$return_response = "";
$add_confirm = false;
$code ="";

//$device_id = "CCMS_2";

// Uncomment this section if using POST method

if ($_SERVER["REQUEST_METHOD"] == "POST") {

	// Check if any field is empty
	$conn = mysqli_connect(HOST, USERNAME, PASSWORD, DB_USER);
	if (!$conn) {
		die("Connection failed: " . mysqli_connect_error());
	} else {

		$device_id = htmlspecialchars(mysqli_real_escape_string($conn, $_POST['D_ID']));
    	// Check user permissions
		$sql = "SELECT device_add_remove FROM user_permissions WHERE login_id = ?";
		$stmt = mysqli_prepare($conn, $sql);
		mysqli_stmt_bind_param($stmt, "s", $user_login_id);
		mysqli_stmt_execute($stmt);
		mysqli_stmt_bind_result($stmt, $device_add_remove);
		mysqli_stmt_fetch($stmt);
		mysqli_stmt_close($stmt);

		if ($device_add_remove != 1) {
			echo json_encode("No permission to Delete the device");
			mysqli_close($conn);
			exit();
		}

		
		$sql = "DELETE FROM user_device_list WHERE device_id = ? AND login_id = ?";
		$stmt = mysqli_prepare($conn, $sql);
		mysqli_stmt_bind_param($stmt, "si", $device_id, $user_login_id);

		if (mysqli_stmt_execute($stmt)) {
			$return_response = "Device deleted successfully";
		} else {
			$return_response = "Error: " . mysqli_stmt_error($stmt);
		}
		mysqli_stmt_close($stmt);

		mysqli_close($conn);
	}
}
else
{
	$return_response="Data not Available";
}

echo json_encode($return_response);
?>
