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

if (isset($_POST['ID']) && isset($_POST['COMPLAINT'])) {

    // Sanitize inputs
	$id = filter_input(INPUT_POST, 'ID', FILTER_SANITIZE_STRING);
	$new_complaint = filter_input(INPUT_POST, 'COMPLAINT', FILTER_SANITIZE_STRING);

	$response = "";

	$update_user = $mobile_no . " / " . $user_email . "/" . $user_name;

	date_default_timezone_set('Asia/Kolkata');
	$date = date("Y-m-d H:i:s");
	$complaint_no = date("YmdHis");

	// Secure connection
	$conn = mysqli_connect(HOST, USERNAME, PASSWORD, DB_ALL);
	if (!$conn) {
		error_log("Connection failed: " . mysqli_connect_error(), 0);
		die("Connection failed, please try again later.");
	}

    // Sanitize inputs for SQL
	$id = sanitize_input($id, $conn);
	$new_complaint = sanitize_input($new_complaint, $conn);

    // Prepared statement to insert complaint data
	$sql_up = "INSERT INTO `complaints` (`device_id`, `complaint_no`, `complaint`, `status`, `registered_by`, `registered_on`, `closed_by`, `closed_on`, `estimated_date`) 
	VALUES (?, ?, ?, 'OPEN', ?, ?, '', ?, ?)";

	$stmt = mysqli_prepare($conn, $sql_up);
	mysqli_stmt_bind_param($stmt, "sssssss", $id, $complaint_no, $new_complaint, $update_user, $date, $date, $date);

	if (mysqli_stmt_execute($stmt)) {
		$stmt1 = mysqli_prepare($conn, "INSERT INTO `complaints_history` (`complaint_no`, `complaint_update`, `updated_by`, `updated_time`) VALUES (?, ?, ?, ?)");
		mysqli_stmt_bind_param($stmt1, "ssss", $complaint_no, $new_complaint, $update_user, $date);

		if (mysqli_stmt_execute($stmt1)) {
			$response = "Successfully registered..!!";
		} else {
			$response = "Failed to save complaint track.";
		}
		mysqli_stmt_close($stmt1);
	} else {
		$response = "Failed to register the complaint.";
	}

    // Close the statement and connection
	mysqli_stmt_close($stmt);
	mysqli_close($conn);

	echo $response;

} else {
	echo "Something went wrong. Please try again.";
}

// Function to sanitize input data
function sanitize_input($data, $conn) {
	$data = trim($data);
	$data = stripslashes($data);
	$data = htmlspecialchars($data);
	return mysqli_real_escape_string($conn, $data);
}

?>
