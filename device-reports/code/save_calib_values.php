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


/*$device_ids="CCMS_1";
$data="5;65532;18;34044;34044;33974;1000;1000;1000;0;0;0;0;0;160;130;119;152;94;87;97;65491;65496;65500;";*/


if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['D_ID']) && isset($_POST['DATA'])) {

	$device_ids = filter_input(INPUT_POST, 'D_ID', FILTER_SANITIZE_STRING);
	$data = filter_input(INPUT_POST, 'DATA', FILTER_SANITIZE_STRING);


	$db = strtolower($device_ids);



	if ($role == "SUPERADMIN") {
		$id = $device_ids;
		$frame_data = $data;

		date_default_timezone_set('Asia/Kolkata');
		$date = date("Y-m-d H:i:s");
		$frame_arr = explode(";", $frame_data);

		$frame_data = $frame_arr[0] . ";" . $frame_arr[3] . ";" . $frame_arr[6] . ";" . $frame_arr[9] . ";" . $frame_arr[12] . ";" . $frame_arr[15] . ";" . $frame_arr[18] . ";" . $frame_arr[21] . ";";
		$frame_data .= $frame_arr[1] . ";" . $frame_arr[4] . ";" . $frame_arr[7] . ";" . $frame_arr[10] . ";" . $frame_arr[13] . ";" . $frame_arr[16] . ";" . $frame_arr[19] . ";" . $frame_arr[22] . ";";
		$frame_data .= $frame_arr[2] . ";" . $frame_arr[5] . ";" . $frame_arr[8] . ";" . $frame_arr[11] . ";" . $frame_arr[14] . ";" . $frame_arr[17] . ";" . $frame_arr[20] . ";" . $frame_arr[23] . ";";

		$calib_frame = $frame_data;
		$frame_data = $id . ";SET_VALUES;0;0;" . $frame_data;
		$id = strtolower($id);

    // Database connection
		$conn = mysqli_connect(HOST, USERNAME, PASSWORD, $db);
		if (!$conn) {
			die(json_encode(["message" => "Connection failed: " . mysqli_connect_error()]));
		}

    // Insert into `saved_settings_on_device`
		$stmt = mysqli_prepare($conn, "INSERT INTO `saved_settings_on_device` (`frame`) VALUES (?)");
		mysqli_stmt_bind_param($stmt, 's', $frame_data);
		if (!mysqli_stmt_execute($stmt)) {
			$response["message"] = "Error inserting loaded settings";
			mysqli_stmt_close($stmt);
			mysqli_close($conn);
			echo json_encode($response);
			exit();
		}
		mysqli_stmt_close($stmt);

		try {
        // Insert into `iot_calibration_values`
			$stmt = mysqli_prepare($conn, "INSERT INTO `iot_calibration_values` (`device_id`, `frame`, `date_time`, `user_mobile`, `email`, `name`, `role`) VALUES (?, ?, ?, ?, ?, ?, ?)");
			mysqli_stmt_bind_param($stmt, 'sssssss', $device_ids, $calib_frame, $date, $mobile_no, $user_email, $user_name, $role);
			if (!mysqli_stmt_execute($stmt)) {
				$response["message"] = "Error inserting calibration values";
				mysqli_stmt_close($stmt);
				mysqli_close($conn);
				echo json_encode($response);
				exit();
			}
			mysqli_stmt_close($stmt);

        // Update `device_settings` for calibration values
			$stmt = mysqli_prepare($conn, "INSERT INTO device_settings (`setting_type`, `setting_flag`) VALUES ('CALIB_VALUES', '1') ON DUPLICATE KEY UPDATE setting_flag='1'");
			if (!mysqli_stmt_execute($stmt)) {
				$response["message"] = "Error updating calibration settings";
				mysqli_stmt_close($stmt);
				mysqli_close($conn);
				echo json_encode($response);
				exit();
			}
			mysqli_stmt_close($stmt);

        // Update `device_settings` for read settings
			$stmt = mysqli_prepare($conn, "INSERT INTO device_settings (`setting_type`, `setting_flag`) VALUES ('READ_SETTINGS', '1') ON DUPLICATE KEY UPDATE setting_flag='1'");
			if (!mysqli_stmt_execute($stmt)) {
				$response["message"] = "Error updating read settings";
				mysqli_stmt_close($stmt);
				mysqli_close($conn);
				echo json_encode($response);
				exit();
			}
			mysqli_stmt_close($stmt);

		} catch (Exception $e) {
			$response["message"] = "Please try again later!";
			mysqli_close($conn);
			echo json_encode($response);
			exit();
		}

		mysqli_close($conn);
		echo json_encode(["message" => "Updated Successfully"]);
	} else {
		echo json_encode(["message" => "This account is not allowed to make changes or modifications.."]);
	}

}
?>