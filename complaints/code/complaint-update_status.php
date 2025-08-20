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

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['ID']) && isset($_POST['COMPLAINT'])) {

    // Sanitize inputs
	$complaint_no = filter_input(INPUT_POST, 'ID', FILTER_SANITIZE_STRING);
	$complaint_update = filter_input(INPUT_POST, 'COMPLAINT', FILTER_SANITIZE_STRING);
	$close_status = filter_input(INPUT_POST, 'CLOSE', FILTER_SANITIZE_STRING);
	$response = "";

	$update_user = $mobile_no . " / " . $user_email . "/" . $user_name;

	date_default_timezone_set('Asia/Kolkata');
	$date = date("Y-m-d H:i:s");

    // Secure connection
	$conn = mysqli_connect(HOST, USERNAME, PASSWORD, DB_ALL);
	if (!$conn) {
		error_log("Connection failed: " . mysqli_connect_error(), 0);
		die("Connection failed, please try again later.");
	}

        // Insert into complaints_history and update the complaint status
	$stmt1 = mysqli_prepare($conn, "INSERT INTO `complaints_history` (`complaint_no`, `complaint_update`, `updated_by`, `updated_time`) VALUES (?, ?, ?, ?)");
	mysqli_stmt_bind_param($stmt1, "ssss", $complaint_no, $complaint_update, $update_user, $date);

	if (mysqli_stmt_execute($stmt1)) {
		
		if ($close_status === "CLOSE") {
        // Update the complaint to CLOSED
			$stmt = mysqli_prepare($conn, "UPDATE `complaints` SET `status` = 'CLOSED', `closed_by` = ?, `closed_on` = ? WHERE `complaint_no` = ?");
			mysqli_stmt_bind_param($stmt, "sss", $update_user, $date, $complaint_no);
			if (mysqli_stmt_execute($stmt)) {
				$response = "Complaint closed successfully.";
			} else {
				error_log("Error closing complaint: " . mysqli_stmt_error($stmt), 0);
				$response = "Failed to close the complaint.";
			}
			mysqli_stmt_close($stmt);
		}
		else
		{
			$stmt2 = mysqli_prepare($conn, "UPDATE `complaints` SET `status` = 'PROGRESS' WHERE `complaint_no` = ?");
			
			mysqli_stmt_bind_param($stmt2, "s", $complaint_no);
			if (mysqli_stmt_execute($stmt2)) {
				$response = "Complaint updated successfully.";
			} else {
				error_log("Error updating complaint status: " . mysqli_stmt_error($stmt2), 0);
				$response = "Failed to update complaint status.";
			}
			mysqli_stmt_close($stmt2);
		}


	} else {
		error_log("Error inserting complaint history: " . mysqli_stmt_error($stmt1), 0);
		$response = "Failed to save complaint update.";
	}
	mysqli_stmt_close($stmt1);


    // Close the connection
	mysqli_close($conn);

	echo $response;
} else {
	echo "Something went wrong. Please try again.";
}

// Sanitize input function
function sanitize_input($data) {
	$data = trim($data);
	$data = stripslashes($data);
	$data = htmlspecialchars($data);
	return $data;
}

?>
