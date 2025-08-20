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

$response = ["status" => "error", "message" => ""];

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['D_ID']) && isset($_POST['ON_TIME']) && isset($_POST['OFF_TIME'])) {
	$conn = mysqli_connect(HOST, USERNAME, PASSWORD, DB_USER);
	if (!$conn) {
		$response["message"] = "Connection to User database failed: " . mysqli_connect_error();
		echo json_encode($response);
		exit();
	}

	$device_ids = htmlspecialchars(mysqli_real_escape_string($conn, $_POST['D_ID']));
	$on_time = htmlspecialchars(mysqli_real_escape_string($conn, $_POST['ON_TIME']));
	$off_time = htmlspecialchars(mysqli_real_escape_string($conn, $_POST['OFF_TIME']));

	$device_ids_array = explode(",", $device_ids);

	$sql = "SELECT on_off_mode FROM user_permissions WHERE login_id = ?";
	$stmt = mysqli_prepare($conn, $sql);
	if ($stmt) {
		mysqli_stmt_bind_param($stmt, "s", $user_login_id);
		mysqli_stmt_execute($stmt);
		mysqli_stmt_bind_result($stmt, $permission_check);
		mysqli_stmt_fetch($stmt);
		mysqli_stmt_close($stmt);

		if ($permission_check != 1) {
			$response["message"] = "No Permission to change the Operation Mode of the device(s)";
			mysqli_close($conn);
			echo json_encode($response);
			exit();
		}
	} else {
		$response["message"] = "Error preparing query for user permissions: " . mysqli_error($conn);
		mysqli_close($conn);
		echo json_encode($response);
		exit();
	}

	mysqli_close($conn);

	if ($permission_check == 1) {
		foreach ($device_ids_array as $device_id) {
			$conn_db = mysqli_connect(HOST, USERNAME, PASSWORD, trim(strtolower($device_id)));
			if (!$conn_db) {
				$response["message"] = "Connection to device database failed";
				echo json_encode($response);
				exit();
			}

			$insert_sql = "INSERT INTO on_off_schedule_time (`on_time`, `off_time`, `status`, `date_time`, `user_mobile`, `email`, `name`, `role`) VALUES (?, ?, 'Initiated', current_timestamp(), ?, ?, ?, ?)";
			$insert_stmt = mysqli_prepare($conn_db, $insert_sql);
			if ($insert_stmt) {
				mysqli_stmt_bind_param($insert_stmt, "ssssss", $on_time, $off_time, $mobile_no, $user_email, $user_name, $role);
				mysqli_stmt_execute($insert_stmt);
				mysqli_stmt_close($insert_stmt);

				$sql_mode = "INSERT INTO `on_off_modes` (`on_off_mode`, `status`, `date_time`, `user_mobile`, `email`, `name`, `role`) VALUES ('SCHEDULE_TIME', 'Initiated',  current_timestamp(), '$mobile_no', '$user_email', '$user_name', '$role');";
				mysqli_query($conn_db, $sql_mode);

				$cancel_sql = "INSERT INTO device_settings (`setting_type`, `setting_flag`) VALUES ('ON_OFF_MODE', '0') ON DUPLICATE KEY UPDATE setting_flag='0'";
				mysqli_query($conn_db, $cancel_sql);

				$setting_sql = "INSERT INTO device_settings (`setting_type`, `setting_flag`) VALUES ('SCHEDULE_TIME', '1') ON DUPLICATE KEY UPDATE setting_flag='1'";
				mysqli_query($conn_db, $setting_sql);
				$read_sql = "INSERT INTO device_settings (`setting_type`, `setting_flag`) VALUES ('READ_SETTINGS', '1') ON DUPLICATE KEY UPDATE setting_flag='1'";
				mysqli_query($conn_db, $read_sql);

				$on_off_activity = "Initiated Schedule mode ";

				$log_sql = "INSERT INTO user_activity_log (`updated_field`, `date_time`, `user_mobile`, `email`, `name`, `role`) VALUES (?, current_timestamp(), ?, ?, ?, ?)";
				$log_stmt = mysqli_prepare($conn_db, $log_sql);
				if ($log_stmt) {
					mysqli_stmt_bind_param($log_stmt, "sssss", $on_off_activity, $mobile_no, $user_email, $user_name, $role);
					mysqli_stmt_execute($log_stmt);
					mysqli_stmt_close($log_stmt);
				} else {
					$response["message"] = "Error preparing user activity log query: " . mysqli_error($conn_db);
					mysqli_close($conn_db);
					echo json_encode($response);
					exit();
				}
			} else {
				$response["message"] = "Error preparing insert query: " . mysqli_error($conn_db);
				mysqli_close($conn_db);
				echo json_encode($response);
				exit();
			}

			$response["status"] = "success";
			$response["message"] = "SCHEDULE TIME mode Initiated";
			mysqli_close($conn_db);
		}

		/*$conn_db_all = mysqli_connect(HOST, USERNAME, PASSWORD, DB_ALL);
		if (!$conn_db_all) {
			echo json_encode(["status" => "error", "message" => "Connection to second database failed: " . mysqli_connect_error()]);
			exit();
		} else {
			$values = [];
			foreach ($device_ids_array as $device_id) {
				$values[] = "('" . mysqli_real_escape_string($conn_db_all, $device_id) . "', 'Schedule Time-Initiated')";
			}
			$values_str = implode(", ", $values);

			$sql2 = "INSERT INTO live_data_updates (device_id, operation_mode) VALUES $values_str ON DUPLICATE KEY UPDATE operation_mode = VALUES(operation_mode)";
			if (mysqli_query($conn_db_all, $sql2)) {
				$response["status"] = "success";
				$response["message"] = "Details added and updated successfully.";
			} else {
				$response["status"] = "error";
				$response["message"] = "Error updating live_data_updates: " . mysqli_error($conn_db_all);
			}

			mysqli_close($conn_db_all);
		}*/


		echo json_encode($response);
	}
} else {
	$response["message"] = "Invalid request method or missing required POST parameters";
	echo json_encode($response);
}
?>
