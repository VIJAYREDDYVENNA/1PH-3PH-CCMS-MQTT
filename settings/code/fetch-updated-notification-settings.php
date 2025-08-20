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

$normal='class=""';
$red='class="text-danger-emphasis fw-bold"'; 
$orange='class="text-warning-emphasis fw-bold"'; 
$green='class="text-success-emphasis fw-bold"';  
$primary='class="text-info-emphasis fw-bold"'; 
$class=$normal;
$data = "";

$response = ["status" => "error", "message" => ""];

//$device_ids="CCMS_1";



if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['D_ID'])) {
	$device_ids = filter_input(INPUT_POST, 'D_ID', FILTER_SANITIZE_STRING);

	$db = strtolower($device_ids);
	$conn = mysqli_connect(HOST, USERNAME, PASSWORD );
	if (!$conn) {
		die("Connection failed: " . mysqli_connect_error());
	} else {

		$device_ids = sanitize_input($device_ids, $conn);
		$sql = "";
		$stmt ="";    	

		$sql = "SELECT `voltage`, `overload`, `power_fail`, `on_off`, `mcb_contactor_trip`, `door_alert` FROM `$db`.`notification_updates` ORDER BY id DESC LIMIT 1;";
		
		
		$stmt = mysqli_prepare($conn, $sql); 

		if ($sql !== "") {
			if (isset($stmt) && mysqli_stmt_execute($stmt)) 
			{
				$result = mysqli_stmt_get_result($stmt);
				if (mysqli_num_rows($result) > 0) {
					$r = mysqli_fetch_assoc($result);
					$response = ["status" => "success", "data" => [
						"voltage" => $r['voltage'],
						"overload" => $r['overload'],
						"power_fail" => $r['power_fail'],
						"on_off" => $r['on_off'],
						"mcb_contactor_trip" => $r['mcb_contactor_trip'],
						"door_alert" => $r['door_alert']
					]];
					
				} else {
					$response = ["status" => "error", "message" =>"Records not found.."];
					
				}
				mysqli_stmt_close($stmt);
			} else {
				$response = ["status" => "error", "message" =>"Something went wrong..!"];
			}
		} else {
			$response = ["status" => "error", "message" =>"Error in query..!"];
			
		}

		mysqli_close($conn);
	}


	echo json_encode($response);
}

function sanitize_input($data, $conn) {
	$data = trim($data);
	$data = stripslashes($data);
	$data = htmlspecialchars($data);
	return mysqli_real_escape_string($conn, $data);
}
?>