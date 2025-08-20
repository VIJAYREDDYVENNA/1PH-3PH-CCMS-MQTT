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

$d_name = "";
$data = "";
$phase = "3PH";
$count = 0;
$device_list = json_decode($_SESSION["DEVICES_LIST"]);
$selected_phase1 ="3PH";
$phase_row="ALL";
$send = array();
$send = "";
$user_devices = "";
foreach ($device_list as $key => $value) {
	$id = $value->D_ID;
	$user_devices = $user_devices . "'" . $id . "',";
}
if ($user_devices != "") {
	$user_devices = substr($user_devices, 0, -1);
}

$conn = mysqli_connect(HOST, USERNAME, PASSWORD);

if (!$conn) {
	die("Connection failed: " . mysqli_connect_error());
} else {
	$sql1 = "SELECT phase FROM `$central_db`.`live_data_updates` WHERE device_id IN ($user_devices) GROUP BY phase";

	$result = mysqli_query($conn, $sql1);

	if ($result) {
		$num_rows = mysqli_num_rows($result);

		if ($num_rows == 2) {
			$phase_row  ="ALL";
			
		} elseif ($num_rows == 1) {
			$row = mysqli_fetch_assoc($result);
			$phase_row =  ($row['phase'] == "3PH") ? "3PH" : "1PH";
			$selected_phase1 = ($row['phase'] == "3PH") ? "3PH" : "1PH";
		}
	} 
	else {
		$selected_phase = "ALL"; // Default fallback in case of query failure
	}
	$sql = "SELECT * FROM `$central_db`.`live_data_updates` WHERE device_id IN ($user_devices) ORDER BY phase DESC, LEFT(device_id, LENGTH(device_id) - LENGTH(SUBSTRING_INDEX(device_id, '_', -1))), CAST(SUBSTRING_INDEX(device_id, '_', -1) AS UNSIGNED)";

	if ($result = mysqli_query($conn, $sql)) {
		if (mysqli_num_rows($result) > 0) {
			$count = 1;

			while ($r = mysqli_fetch_assoc($result)) {

				$device_ids = array_column($device_list, 'D_ID');
				$index = array_search($r['device_id'], $device_ids);

				if ($index !== false) {

					if ($phase_row== "1PH") {
						$phase="1PH";
						$selection = "1PH";
						$selected_phase = "1PH";
					} else {
						$phase = $r['phase'];

						$selection = "ALL";
						$selected_phase = $_SESSION["SELECTED_PHASE"];
					}

					$device_id = strtoupper($device_list[$index]->D_ID);
					include("set_parameters.php");
					$d_name = "<small class='font-small'> (" . $device_list[$index]->D_NAME . ")</small>";
					include("table_cells.php");

					$db = strtolower($device_list[$index]->D_ID);
				}
			}
		} else {
			$data = '<tr><td class="text-danger" colspan="75">No records found</td></tr>';
		}
	} else {
		$data = '<tr><td class="text-danger" colspan="75">Error executing query: ' . mysqli_error($conn) . '</td></tr>';
	}
	$selected_phase = $selected_phase1;


	mysqli_close($conn);
}

echo json_encode(array($data, $selected_phase));
