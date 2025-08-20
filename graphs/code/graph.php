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

$year = "";
$month = "";
$day = "";
$selected_date = "";
$phase = array();

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['D_ID'])) {
	$device_ids = filter_input(INPUT_POST, 'D_ID', FILTER_SANITIZE_STRING);
	$type = filter_input(INPUT_POST, 'TYPE', FILTER_SANITIZE_STRING);
	$paramter = filter_input(INPUT_POST, 'PARAMTER', FILTER_SANITIZE_STRING);

	$id = $device_ids;
	$send = array();
	$db = strtolower($id);
	include_once("../../common-files/fetch-device-phase.php");

	$conn = mysqli_connect(HOST, USERNAME, PASSWORD, $db);
	if (!$conn) {
		die("Connection failed: " . mysqli_connect_error());
	} else {
		$device_ids = sanitize_input($device_ids, $conn);
		$type = sanitize_input($type, $conn);
		$paramter = sanitize_input($paramter, $conn);

		if (isset($_POST['DATE'])) {
			$selected_date = filter_input(INPUT_POST, 'DATE', FILTER_SANITIZE_STRING);
			$selected_date = sanitize_input($selected_date, $conn);
			$dateParts = explode("-", $selected_date);
			$year = intval($dateParts[0]);
			$month = intval($dateParts[1]);
			$day = intval($dateParts[2]);

			$month = str_pad($month, 2, "0", STR_PAD_LEFT);
			$day = str_pad($day, 2, "0", STR_PAD_LEFT);
		}

		if ($type === "YEAR") {
			if ($paramter === "VOLTAGE") {
				$sql = "SELECT max(voltage_ph1) as v_1, max(voltage_ph2) as v_2, max(voltage_ph3) as v_3, year as date_time FROM (SELECT max(voltage_ph1) AS voltage_ph1, max(voltage_ph2) AS voltage_ph2, max(voltage_ph3) AS voltage_ph3, `month` AS `month`, `year` AS `year` FROM voltage_current_graph GROUP BY `year`, `month`) as TB1 WHERE year > '2018' GROUP BY year ORDER BY year ASC";
			} elseif ($paramter === "CURRENT") {
				$sql = "SELECT max(current_ph1) as v_1, max(current_ph2) as v_2, max(current_ph3) as v_3, year as date_time FROM (SELECT max(current_ph1) AS current_ph1, max(current_ph2) AS current_ph2, max(current_ph3) AS current_ph3, `month` AS `month`, `year` AS `year` FROM voltage_current_graph GROUP BY `year`, `month`) as TB1 WHERE year > '2018' GROUP BY year ORDER BY year ASC";
			}
		} elseif ($type === "MONTHS") {
			if ($paramter === "VOLTAGE") {
				$sql = "SELECT voltage_ph1 as v_1, voltage_ph2 as v_2, voltage_ph3 as v_3, month as date_time FROM (SELECT max(voltage_ph1) AS `voltage_ph1`, max(voltage_ph2) AS `voltage_ph2`, max(voltage_ph3) AS `voltage_ph3`, month, `year` AS `year` FROM voltage_current_graph GROUP BY `year`, `month`) as TB1 WHERE year = ? ORDER BY month ASC";
			} elseif ($paramter === "CURRENT") {
				$sql = "SELECT current_ph1 as v_1, current_ph2 as v_2, current_ph3 as v_3, month as date_time FROM (SELECT max(current_ph1) AS `current_ph1`, max(current_ph2) AS `current_ph2`, max(current_ph3) AS `current_ph3`, `month`, `year` AS `year` FROM voltage_current_graph GROUP BY `year`, `month`) as TB1 WHERE year = ? ORDER BY month ASC";
			}
		} elseif ($type === "DAYS") {
			if ($paramter === "VOLTAGE") {
				$sql = "SELECT voltage_ph1 as v_1, voltage_ph2 as v_2, voltage_ph3 as v_3, day as date_time FROM (SELECT max(voltage_ph1) AS `voltage_ph1`, max(voltage_ph2) AS `voltage_ph2`, max(voltage_ph3) AS `voltage_ph3`, `day` AS `day`, `month` AS `month`, `year` AS `year` FROM `voltage_current_graph` GROUP BY `day`, `month`, `year`) as TB1 WHERE year = ? AND month = ? ORDER BY day ASC";
			} elseif ($paramter === "CURRENT") {
				$sql = "SELECT current_ph1 as v_1, current_ph2 as v_2, current_ph3 as v_3, day as date_time FROM (SELECT max(current_ph1) AS `current_ph1`, max(current_ph2) AS `current_ph2`, max(current_ph3) AS `current_ph3`, `day` AS `day`, `month` AS `month`, `year` AS `year` FROM `voltage_current_graph` GROUP BY `day`, `month`, `year`) as TB1 WHERE year = ? AND month = ? ORDER BY day ASC";
			}
		} elseif ($type === "DAY") {
			if ($paramter === "VOLTAGE") {
				$sql = "SELECT voltage_ph1 as v_1, voltage_ph2 as v_2, voltage_ph3 as v_3, `date_time` as date_time FROM `voltage_current_graph` WHERE year = ? AND month = ? AND day = ?";
			} elseif ($paramter === "CURRENT") {
				$sql = "SELECT current_ph1 as v_1, current_ph2 as v_2, current_ph3 as v_3, `date_time` as date_time FROM `voltage_current_graph` WHERE year = ? AND month = ? AND day = ?";
			}
		} elseif ($type === "LATEST") {
			if ($paramter === "VOLTAGE") {
				$sql = "SELECT * FROM (SELECT date_time as date_time, voltage_ph1 as v_1, voltage_ph2 as v_2, voltage_ph3 as v_3 FROM voltage_current_graph ORDER BY id DESC LIMIT 60) as tbl ORDER BY date_time ASC";
			} elseif ($paramter === "CURRENT") {
				$sql = "SELECT * FROM (SELECT date_time as date_time, current_ph1 as v_1, current_ph2 as v_2, current_ph3 as v_3 FROM voltage_current_graph ORDER BY id DESC LIMIT 60) as tbl ORDER BY date_time ASC";
			}
		}

        // Prepare the statement
		if ($stmt = mysqli_prepare($conn, $sql)) {
            // Bind parameters if necessary
			if ($type === "MONTHS" )
			{
				mysqli_stmt_bind_param($stmt, 'i', $year);
			}
			elseif( $type === "DAYS") {
				mysqli_stmt_bind_param($stmt, 'ii', $year, $month);
			} elseif ($type === "DAY") {
				mysqli_stmt_bind_param($stmt, 'iii', $year, $month, $day);
			}

            // Execute the statement
			mysqli_stmt_execute($stmt);

            // Get the result
			$result = mysqli_stmt_get_result($stmt);
			if (mysqli_num_rows($result) > 0) {
				while ($r = mysqli_fetch_assoc($result)) {
					$date_time = $r['date_time'];
					$val_1 = $r['v_1'];
					$val_2 = $r['v_2'];
					$val_3 = $r['v_3'];
					if ($type === "LATEST" || $type === "DAY") {
						$fromMYSQL = $date_time;
						$date_time = date("H:i M d", strtotime($fromMYSQL));
					}
					$send[] = array("date" => $date_time, "v_1" => $val_1, "v_2" => $val_2, "v_3" => $val_3);
				}
			}

            // Close the statement
			mysqli_stmt_close($stmt);
		}

		mysqli_close($conn);
	}

	echo json_encode(array($send,$phase));
}

function sanitize_input($data, $conn) {
	$data = trim($data);
	$data = stripslashes($data);
	$data = htmlspecialchars($data);
	return mysqli_real_escape_string($conn, $data);
}
?>
