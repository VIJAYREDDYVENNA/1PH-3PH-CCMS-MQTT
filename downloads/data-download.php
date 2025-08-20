<?php

require_once '../base-path/config-path.php';
require_once BASE_PATH . 'config_db/config.php';
require_once BASE_PATH . 'session/session-manager.php';

SessionManager::checkSession();
$sessionVars = SessionManager::SessionVariables();

$mobile_no = $sessionVars['mobile_no'];
$user_id = $sessionVars['user_id'];
$role = $sessionVars['role'];
$user_login_id = $sessionVars['user_login_id'];
$user_name = $sessionVars['user_name'];
$user_email = $sessionVars['user_email'];
$permission_check = 0;
ini_set('max_execution_time', 0);

if (isset($_POST["date-range"]) && isset($_POST['device_id'])) {
	$Deviceid = trim($_POST['device_id']);
	$dateRange = $_POST['date-range'];


	// Check if the date range is empty
	if (empty($dateRange)) {
		echo "<script>alert('Please select a valid date range'); window.history.back();</script>";
		exit;
	}
	$id = $Deviceid;
	include_once("../common-files/fetch-device-phase.php");
	$phase = $device_phase;

	// Parse the date range
	list($from, $to) = explode(' to ', $dateRange);
	$from = date("Y-m-d 00:00:01", strtotime($from));
	$to = date("Y-m-d 23:59:59", strtotime($to));

	// Ensure date range does not exceed 30 days
	$dateDiff = (strtotime($to) - strtotime($from)) / (60 * 60 * 24);
	if ($dateDiff > 30) {
		die("Date range cannot exceed 30 days.");
	}

	// Get the device name from session
	$device_name = $Deviceid;
	$device_list = $device_list = json_decode($_SESSION["DEVICES_LIST"]);
	foreach ($device_list as $key => $value) {
		if ($value->D_ID == strtoupper($Deviceid)) {
			$device_name = $value->D_NAME;
		}
	}

	$filename = $device_name;
	$db = strtolower($Deviceid);



	try {
		$conn = mysqli_connect(HOST, USERNAME, PASSWORD, $db);
		if (!$conn) {
			die("Connection failed: " . mysqli_connect_error());
		} else {
			if ($phase == "1PH") {
				$params = "`id`, `device_id`, `date_time`, `voltage_ph1`, `current_ph1`,   `kw_total`, `kva_total`,  `energy_kwh_total`,`energy_kvah_total`, `frequency_ph1`,`powerfactor_ph1`,  `on_off_status`, `contactor_status`, `location`, `signal_level`";
			} else {
				$params = "`id`, `device_id`, `date_time`, `voltage_ph1`, `voltage_ph2`, `voltage_ph3`, `current_ph1`, `current_ph2`, `current_ph3`, `kw_1`, `kw_2`, `kw_3`, `kw_total`, `kva_1`, `kva_2`, `kva_3`, `kva_total`, `energy_kwh_ph1`, `energy_kwh_ph2`, `energy_kwh_ph3`, `energy_kwh_total`, `energy_kvah_ph1`, `energy_kvah_ph2`, `energy_kvah_ph3`, `energy_kvah_total`, `lag_kvarh_ph1`, `lag_kvarh_ph2`, `lag_kvarh_ph3`, `lag_kvarh_total`, `lead_kvarh_ph1`, `lead_kvarh_ph2`, `lead_kvarh_ph3`, `lead_kvarh_total`, `frequency_ph1`, `frequency_ph2`, `frequency_ph3`, `powerfactor_ph1`, `powerfactor_ph2`, `powerfactor_ph3`, `on_off_status`, `contactor_status`, `location`, `signal_level`";
			}
			$sql = "SELECT $params FROM live_data WHERE date_time BETWEEN '$from' AND '$to' ORDER BY id ASC";

			if ($result = mysqli_query($conn, $sql)) {
				$file_ending = "xls";

				header("Content-Type: application/octet-stream");
				header("Content-Disposition: attachment; filename=$filename.xls");
				header("Pragma: no-cache");
				header("Expires: 0");

				$sep = "\t";
				if ($phase == "1PH") {
					$col_headers = "S_NO\tDEVICE_ID\tDATE_TIME\tVOLTAGE\tCURRENT\tKW_TOTAL\tKVA_TOTAL\tENERGY_KWH_TOTAL\tENERGY_KVAH_TOTAL\tFREQUENCY_PH1\tPOWERFACTOR_PH1\tON_OFF_STATUS\tLOAD_STATUS\tLOCATION\tBATTERY VOLTAGE/SIGNAL_LEVEL";
				} else {
					$col_headers = "S_NO\tDEVICE_ID\tDATE_TIME\tVOLTAGE_PH1\tVOLTAGE_PH2\tVOLTAGE_PH3\tCURRENT_PH1\tCURRENT_PH2\tCURRENT_PH3\tKW_1\tKW_2\tKW_3\tKW_TOTAL\tKVA_1\tKVA_2\tKVA_3\tKVA_TOTAL\tENERGY_KWH_PH1\tENERGY_KWH_PH2\tENERGY_KWH_PH3\tENERGY_KWH_TOTAL\tENERGY_KVAH_PH1\tENERGY_KVAH_PH2\tENERGY_KVAH_PH3\tENERGY_KVAH_TOTAL\tLAG_KVARH_PH1\tLAG_KVARH_PH2\tLAG_KVARH_PH3\tLAG_KVARH_TOTAL\tLEAD_KVARH_PH1\tLEAD_KVARH_PH2\tLEAD_KVARH_PH3\tLEAD_KVARH_TOTAL\tFREQUENCY_PH1\tFREQUENCY_PH2\tFREQUENCY_PH3\tPOWERFACTOR_PH1\tPOWERFACTOR_PH2\tPOWERFACTOR_PH3\tON_OFF_STATUS\tLOAD_STATUS\tLOCATION\tBATTERY VOLTAGE/SIGNAL_LEVEL";
				}
				echo $col_headers . "\n";


				while ($row = mysqli_fetch_assoc($result)) {
					$schema_insert = $sep;

					foreach ($row as $value) {
						$schema_insert .= isset($value) ? "$value$sep" : "NULL$sep";
					}
					$schema_insert = trim($schema_insert);
					echo preg_replace("/\r\n|\n\r|\n|\r/", " ", $schema_insert) . "\n";
				}
			} else {
				echo "No records found. Please try again.";
			}

			mysqli_close($conn);
		}
	} catch (Exception $e) {
		echo "An error occurred. Please try again.";
	}
} else {
	die("Invalid input. Please ensure all fields are filled out.");
}
