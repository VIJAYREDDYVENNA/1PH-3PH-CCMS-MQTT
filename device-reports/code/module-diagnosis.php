
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



$normal = 'class="text-secondary-emphasis"';
$red = 'class="text-danger-emphasis fw-bold"';
$orange = 'class="text-warning-emphasis fw-bold"';
$green = 'class="text-success-emphasis fw-bold"';
$primary = 'class="text-info-emphasis fw-bold"';
$class_1 = $normal;
$class_2 = $normal;
$class_3 = $normal;
$class_4 = $normal;
$class_5 = $normal;
$class_6 = $normal;
$class_7 = $normal;
$class_8 = $normal;
$class_9 = $normal;
$class_10 = $normal;


$d_name = "";
$data = "";
$device_ids = "CCMS_1";
$records = "LATEST";
$module = "MODULES";


if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['D_ID']) && isset($_POST['RECORDS'])) {
	$device_ids = filter_input(INPUT_POST, 'D_ID', FILTER_SANITIZE_STRING);
	$records = filter_input(INPUT_POST, 'RECORDS', FILTER_SANITIZE_STRING);
	$module = filter_input(INPUT_POST, 'MODULE', FILTER_SANITIZE_STRING);




	$db = strtolower($device_ids);
	$send = array();
	$send = "";
	$d_name = "";

	$start_date = "";
	$end_date = "";

	$conn = mysqli_connect(HOST, USERNAME, PASSWORD); // Ensure DATABASE constant is defined and holds the name of the database
	if (!$conn) {
		die("Connection failed: " . mysqli_connect_error());
	} else {


		$device_ids = sanitize_input($device_ids, $conn);
		$records = sanitize_input($records, $conn);
		$module = sanitize_input($module, $conn);

		$sql = "";
		$stmt = "";


		if ($records === "DATE-RANGE") {
			if (isset($_POST['START_DATE']) && isset($_POST['END_DATE'])) {
				$start_date = trim(filter_input(INPUT_POST, 'START_DATE', FILTER_SANITIZE_STRING));
				$end_date = trim(filter_input(INPUT_POST, 'END_DATE', FILTER_SANITIZE_STRING));

				$start_date = sanitize_input($start_date, $conn);
				$end_date = sanitize_input($end_date, $conn);
			}
		}

		if ($module == "MODULES") {
			if ($records === "LATEST") {
				$sql = "SELECT * FROM `$db`.`system_status` ORDER BY id DESC LIMIT 50";
				$stmt = mysqli_prepare($conn, $sql);
			} else {
				$sql = "SELECT * FROM `$db`.`system_status` WHERE DATE(date_time) BETWEEN ? AND ? ORDER BY id DESC";

				$stmt = mysqli_prepare($conn, $sql);
				mysqli_stmt_bind_param($stmt, 'ss', $start_date, $end_date);
			}
			$data .= "<thead class='sticky-header text-center'>
    		<tr class='header-row-1'>                                    
    		<th class='table-header-row-1'>Updated On</th>                                
    		<th class='table-header-row-1'>RTC</th>                                
    		<th class='table-header-row-1'>FLASH</th>
    		<th class='table-header-row-1'>WIFI</th>
    		<th class='table-header-row-1'>ADE</th>
    		<th class='table-header-row-1'>DC Supply</th>
    		<th class='table-header-row-1'>GPS</th>
    		<th class='table-header-row-1'>ON/OFF Control</th>
    		<th class='table-header-row-1'>R-Contactor</th>
    		<th class='table-header-row-1'>Y-Contactor</th>
    		<th class='table-header-row-1'>B-Contactor</th>
    		</tr></thead><tbody>";

			if ($sql !== "") {
				if (isset($stmt) && mysqli_stmt_execute($stmt)) {
					$result = mysqli_stmt_get_result($stmt);
					if (mysqli_num_rows($result) > 0) {
						while ($r = mysqli_fetch_assoc($result)) {



							$r['date_time'] = date("H:i:s d-m-Y", strtotime($r['date_time']));
							$r['prev_date_time'] = date("H:i:s d-m-Y", strtotime($r['prev_date_time']));

							$status = $r['status'];
							$period = $r['prev_date_time'] . " <span class='text-primary'> To </span>" . $r['date_time'];


							$statuslist = explode(",", $status);
							$class_1 = $normal;
							$class_2 = $normal;
							$class_3 = $normal;
							$class_4 = $normal;
							$class_5 = $normal;
							$class_6 = $normal;

							if ($statuslist[0] == "1") {
								$sts_1 = "Normal";
							} else {
								$sts_1 = "FAIL";
								$class_1 = $red;
							}

							if ($statuslist[1] == "1") {
								$sts_2 = "Normal";
							} else {
								$sts_2 = "FAIL";
								$class_2 = $red;
							}

							if ($statuslist[2] == "1") {
								$sts_3 = "Normal";
							} else {
								$sts_3 = "FAIL";
								$class_3 = $red;
							}

							if ($statuslist[3] == "1") {
								$sts_4 = "Normal";
							} else {
								$sts_4 = "FAIL";
								$class_4 = $red;
							}

							if ((strstr($statuslist[4], ":"))) {
								if ($statuslist[4] == "0:0") {
									$sts_5 = "SMPS-1 & SMPS-2 FAIL";
									$class_5 = $red;
								}

								if ($statuslist[4] == "1:0") {
									$sts_5 = "SMPS-1 FAIL";
									$class_5 = $red;
								}
								if ($statuslist[4] == "0:1") {
									$sts_5 = "SMPS-2 FAIL";
									$class_5 = $red;
								}

								if ($statuslist[4] == "1:1") {
									$sts_5 = "Normal";
								}
							} else {

								if ($statuslist[4] == "1") {
									$sts_5 = "Normal";
								} else {
									$sts_5 = "FAIL";
									$class_5 = $red;
								}
							}

							if ($statuslist[5] == "1") {
								$sts_6 = "Normal";
							} else {
								$sts_6 = "FAIL";
								$class_6 = $red;
							}

							if ($statuslist[7] == "1") {
								$sts_8 = "ON";
								$class_8 = $green;
							} else {
								$sts_8 = "OFF";
								$class_8 = $red;
							}

							if ($statuslist[8] == "1") {
								$sts_9 = "ON";
								$class_9 = $green;
							} else {
								$sts_9 = "OFF";
								$class_9 = $red;
							}
							if ($statuslist[9] == "1") {
								$sts_10 = "ON";
								$class_10 = $green;
							} else {
								$sts_10 = "OFF";
								$class_10 = $red;
							}

							$on_off = $statuslist[6];
							switch ($on_off) {
								case "0":
									$sts_7 = "Auto OFF";
									$class_7 = $red;
									break;
								case "1":
									$sts_7 = "Auto ON";
									$class_7 = $green;
									break;
								case "2":
									$sts_7 = "Power Fail";
									$class_7 = $red;
									break;

								case "3":
									$sts_7 = "Server ON";
									$class_7 = $green;
									break;
								case "4":
									$sts_7 = "APP ON";
									$class_7 = $green;
									break;
								case "5":
									$sts_7 = "Manual ON";
									$class_7 = $green;
									break;
								case "6":
									$sts_7 = "Server OFF";
									$class_7 = $red;
									break;
								case "7":
									$sts_7 = "APP OFF";
									$class_7 = $red;
									break;
								default:
									break;
							}


							$data .= "<tr >

    						<td $normal> " . $period . "</td>
    						<td $class_1> " . $sts_1 . "</td>    						
    						<td $class_2> " . $sts_2 . "</td>    						
    						<td $class_3> " . $sts_3 . "</td>    						
    						<td $class_4> " . $sts_4 . "</td>    						
    						<td $class_5> " . $sts_5 . "</td>    						
    						<td $class_6> " . $sts_6 . "</td>    						
    						<td $class_7> " . $sts_7 . "</td>    						
    						<td $class_8> " . $sts_8 . "</td>    						
    						<td $class_9> " . $sts_9 . "</td>    						
    						<td $class_10> " . $sts_10 . "</td></tr>";
						}
					} else {
						$data .= '<tr><td class="text-danger" colspan="12">Records are not Found</td></tr>';
					}
					mysqli_stmt_close($stmt);
				} else {
					$data .= '<tr><td class="text-danger" colspan="12">Records are not Found</td></tr>';
				}
			} else {
				$data .= '<tr><td class="text-danger" colspan="12">Records are not Found</td></tr>';
			}

			$data .= "</tbody>";
		}

		if ($module == "SIM-DIAGNOSIS") {
			if ($records === "LATEST") {
				$sql = "SELECT * FROM `$db`.`simcom_status` ORDER BY id DESC LIMIT 50";
				$stmt = mysqli_prepare($conn, $sql);
			} else {
				$sql = "SELECT * FROM `$db`.`simcom_status` WHERE DATE(date_time) BETWEEN ? AND ? ORDER BY id DESC";

				$stmt = mysqli_prepare($conn, $sql);
				mysqli_stmt_bind_param($stmt, 'ss', $start_date, $end_date);
			}
			$data .= "<thead class='sticky-header text-center'>
    		<tr class='header-row-1'>                                    
    		<th class='table-header-row-1'>Updated On</th>                                
    		<th class='table-header-row-1'>SIM Detection</th>                                
    		<th class='table-header-row-1'>Network Status</th>
    		<th class='table-header-row-1'>GPRS Status</th>
    		<th class='table-header-row-1'>Posting Status</th>
    		<th class='table-header-row-1'>Power Dip</th>
    		<th class='table-header-row-1'>Status Code</th>
    		</tr></thead><tbody>";

			if ($sql !== "") {
				if (isset($stmt) && mysqli_stmt_execute($stmt)) {
					$result = mysqli_stmt_get_result($stmt);
					if (mysqli_num_rows($result) > 0) {
						while ($r = mysqli_fetch_assoc($result)) {
							$r['date_time'] = date("H:i:s d-m-Y", strtotime($r['date_time']));

							$status = $r['status'];
							$statuslist = explode(",", $status);

							$sts_1 = $statuslist[0];
							$sts_2 = $statuslist[1];
							$sts_3 = $statuslist[2];
							$sts_4 = $statuslist[3];
							$sts_5 = $statuslist[4];
							$sts_6 = $r['status_code'];
							$class_6 = $normal;
							if ($sts_6 == "202" || $sts_6 == "201") {
								$class_6 = $primary;
							}

							if ($statuslist[0] == "1") {
								$sts_1 = "Detected";
								$class_1 = $green;
							} else {
								$sts_1 = "Failed";
								$class_1 = $red;
							}

							if ($statuslist[1] == "1") {
								$sts_2 = "Connected";
								$class_2 = $green;
							} else {
								$sts_2 = "Failed";
								$class_2 = $red;
							}

							if ($statuslist[2] == "1") {
								$sts_3 = "Connected";
								$class_3 = $green;
							} else {
								$sts_3 = "Failed";
								$class_3 = $red;
							}
							if ($statuslist[3] == "1") {
								$sts_4 = "Connected";
								$class_4 = $green;
							} else {
								$sts_4 = "Failed";
								$class_4 = $red;
							}
							if ($statuslist[4] == "1") {
								$sts_5 = "DIP-Detected";
								$class_5 = $red;
							} else {
								$sts_5 = "No Issue";
							}



							$data .= "<tr >
    						<td > " . $r['date_time'] . "</td>    						
    						<td $class_1> " . $sts_1 . "</td>    						
    						<td $class_2> " . $sts_2 . "</td>    						
    						<td $class_3> " . $sts_3 . "</td>    						
    						<td $class_4> " . $sts_4 . "</td>    						
    						<td $class_5> " . $sts_5 . "</td>    						
    						<td $class_6> " . $sts_6 . "</td></tr>";
						}
					} else {
						$data .= '<tr><td class="text-danger" colspan="7">Records are not Found</td></tr>';
					}
					mysqli_stmt_close($stmt);
				} else {
					$data .= '<tr><td class="text-danger" colspan="7">Records are not Found</td></tr>';
				}
			} else {
				$data .= '<tr><td class="text-danger" colspan="7">Records are not Found</td></tr>';
			}

			$data .= "</tbody>";
		}

		if ($module == "SIM-MODULE-FAIL") {
			if ($records === "LATEST") {
				$sql = "SELECT * FROM `$db`.`sim_module_communication` ORDER BY id DESC LIMIT 50";
				$stmt = mysqli_prepare($conn, $sql);
			} else {
				$sql = "SELECT * FROM `$db`.`sim_module_communication` WHERE DATE(date_time) BETWEEN ? AND ? ORDER BY id DESC";

				$stmt = mysqli_prepare($conn, $sql);
				mysqli_stmt_bind_param($stmt, 'ss', $start_date, $end_date);
			}

			$data .= "<thead class='sticky-header text-center'>
    		<tr class='header-row-1'>                                    
    		<th class='table-header-row-1'>SIMCOM Fail Time</th>
    		<th class='table-header-row-1'>Server Time</th>
    		</tr></thead><tbody>";

			if ($sql !== "") {
				if (isset($stmt) && mysqli_stmt_execute($stmt)) {
					$result = mysqli_stmt_get_result($stmt);
					if (mysqli_num_rows($result) > 0) {
						while ($r = mysqli_fetch_assoc($result)) {

							$data .= "<tr >

    						<td > " . $r['date_time'] . "</td>
    						<td > " . $r['server_time'] . "</td></tr>";
						}
					} else {
						$data .= '<tr><td class="text-danger" colspan="3">Records are not Found</td></tr>';
					}
					mysqli_stmt_close($stmt);
				} else {
					$data .= '<tr><td class="text-danger" colspan="3">Records are not Found</td></tr>';
				}
			} else {
				$data .= '<tr><td class="text-danger" colspan="3">Records are not Found</td></tr>';
			}

			$data .= "</tbody>";
		}

		if ($module == "SIM-MODULE-REMOVAL") {
			if ($records === "LATEST") {
				$sql = "SELECT * FROM `$db`.`sim_module_removal` ORDER BY id DESC LIMIT 50";
				$stmt = mysqli_prepare($conn, $sql);
			} else {
				$sql = "SELECT * FROM `$db`.`sim_module_removal` WHERE DATE(date_time) BETWEEN ? AND ? ORDER BY id DESC";

				$stmt = mysqli_prepare($conn, $sql);
				mysqli_stmt_bind_param($stmt, 'ss', $start_date, $end_date);
			}

			$data .= "<thead class='sticky-header text-center'>
    		<tr class='header-row-1'> 
    		<th class='table-header-row-1'>Device_ID</th>                                   
    		<th class='table-header-row-1'>SIM MODULE ACTIVITY</th>
    		<th class='table-header-row-1'>DATE Time</th>
    		</tr></thead><tbody>";

			if ($sql !== "") {
				if (isset($stmt) && mysqli_stmt_execute($stmt)) {
					$result = mysqli_stmt_get_result($stmt);
					if (mysqli_num_rows($result) > 0) {
						while ($r = mysqli_fetch_assoc($result)) {
							$class_act = $normal;
							if ($r['activity'] != "") {
								$status = $r['activity'];
								$class_act = $normal;
								if ($status == "Removed") {
									$class_act = $red;
								} else {
									$class_act = $green;
								}
							}
							$data .= "<tr >
    						<td > " . $r['device_id'] . "</td>
    						<td $class_act> " . $r['activity'] . "</td>
    						<td > " . $r['date_time'] . "</td>
    						</tr>";
						}
					} else {
						$data .= '<tr><td class="text-danger" colspan="3">Records are not Found</td></tr>';
					}
					mysqli_stmt_close($stmt);
				} else {
					$data .= '<tr><td class="text-danger" colspan="3">Records are not Found</td></tr>';
				}
			} else {
				$data .= '<tr><td class="text-danger" colspan="3">Records are not Found</td></tr>';
			}

			$data .= "</tbody>";
		}

		if ($module == "BOX-TOP-COVER-OPEN-CLOSE") {
			if ($records === "LATEST") {
				$sql = "SELECT * FROM `$db`.`box_top_cover_activity` ORDER BY id DESC LIMIT 50";
				$stmt = mysqli_prepare($conn, $sql);
			} else {
				$sql = "SELECT * FROM `$db`.`box_top_cover_activity` WHERE DATE(date_time) BETWEEN ? AND ? ORDER BY id DESC";

				$stmt = mysqli_prepare($conn, $sql);
				mysqli_stmt_bind_param($stmt, 'ss', $start_date, $end_date);
			}

			$data .= "<thead class='sticky-header text-center'>
    		<tr class='header-row-1'> 
    		<th class='table-header-row-1'>Device_ID</th>                                   
    		<th class='table-header-row-1'>Box Top Cover Activity</th>
    		<th class='table-header-row-1'>DATE Time</th>
    		</tr></thead><tbody>";

			if ($sql !== "") {
				if (isset($stmt) && mysqli_stmt_execute($stmt)) {
					$result = mysqli_stmt_get_result($stmt);
					if (mysqli_num_rows($result) > 0) {
						while ($r = mysqli_fetch_assoc($result)) {
							$class_act = $normal;
							if ($r['activity'] != "") {
								$status = $r['activity'];
								$class_act = $normal;
								if ($status == "Opened") {
									$class_act = $red;
								} else {
									$class_act = $green;
								}
							}
							$data .= "<tr >
    						<td > " . $r['device_id'] . "</td>
    						<td $class_act> " . $r['activity'] . "</td>
    						<td > " . $r['date_time'] . "</td>
    						</tr>";
						}
					} else {
						$data .= '<tr><td class="text-danger" colspan="3">Records are not Found</td></tr>';
					}
					mysqli_stmt_close($stmt);
				} else {
					$data .= '<tr><td class="text-danger" colspan="3">Records are not Found</td></tr>';
				}
			} else {
				$data .= '<tr><td class="text-danger" colspan="3">Records are not Found</td></tr>';
			}

			$data .= "</tbody>";
		}

		mysqli_close($conn);
	}

	echo json_encode($data);
}

function sanitize_input($data, $conn)
{
	$data = trim($data);
	$data = stripslashes($data);
	$data = htmlspecialchars($data);
	return mysqli_real_escape_string($conn, $data);
}
?>
