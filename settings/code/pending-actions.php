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

$normal = 'class=""';
$red = 'class="text-danger-emphasis fw-bold"';
$orange = 'class="text-warning-emphasis fw-bold"';
$green = 'class="text-success-emphasis fw-bold"';
$primary = 'class="text-info-emphasis fw-bold"';
$class = $normal;
$data = "";


/*$device_ids="CCMS_1";*/


if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['D_ID']) && isset($_POST['KEY'])) {
	$device_ids = filter_input(INPUT_POST, 'D_ID', FILTER_SANITIZE_STRING);
	$parameter = filter_input(INPUT_POST, 'KEY', FILTER_SANITIZE_STRING);
	update_data($device_ids, $parameter);
} else if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['D_ID'])) {
	$device_ids = filter_input(INPUT_POST, 'D_ID', FILTER_SANITIZE_STRING);
	update_data($device_ids, "");
}

function update_data($device_ids, $parameter)
{
	global $role;
	$normal = 'class=""';
	$red = 'class="text-danger-emphasis fw-bold"';
	$orange = 'class="text-warning-emphasis fw-bold"';
	$green = 'class="text-success-emphasis fw-bold"';
	$primary = 'class="text-info-emphasis fw-bold"';
	$class = $normal;
	$data = "";


	$db = strtolower($device_ids);
	$conn = mysqli_connect(HOST, USERNAME, PASSWORD); // Ensure DATABASE constant is defined and holds the name of the database
	if (!$conn) {
		die("Connection failed: " . mysqli_connect_error());
	} else {

		$device_ids = sanitize_input($device_ids, $conn);
		$sql = "";
		$stmt = "";

		if (isset($_POST['CANCEL_PARAMTER'])) {
			/*try {  */


			$configuration = filter_input(INPUT_POST, 'CANCEL_PARAMTER', FILTER_SANITIZE_STRING);
			$configuration = sanitize_input($configuration, $conn);
			$sql = "UPDATE `$db`.`device_settings` SET setting_flag = 3 WHERE setting_type = ?";
			$stmt = mysqli_prepare($conn, $sql);
			if ($stmt) {
				mysqli_stmt_bind_param($stmt, 's', $configuration);
				mysqli_stmt_execute($stmt);
				mysqli_stmt_close($stmt);
			}
			/*} catch (Exception $e) {
    			
    		}*/
		}

		$data .= "<thead class='sticky-header text-center'>
    	<tr class='header-row-1'>                                    
    	<th class='table-header-row-1'>Configuration</th>                                
    	<th class='table-header-row-1'>Status</th>                                
    	<th class='table-header-row-1'>Date&Time</th>                                
    	<th class='table-header-row-1'>Action</th>
    	</tr></thead><tbody>";

		if ($role == "SUPERADMIN") {
			if($parameter=="")
			{
				$sql = "SELECT * FROM `$db`.`device_settings` ORDER BY setting_type ASC LIMIT 100";
			}
			else
			{
				$sql = "SELECT * FROM `$db`.`device_settings` where setting_type='$parameter'";
			}
			
		} else {
			if($parameter=="")
			{
			
			$sql = "SELECT * FROM `$db`.`device_settings` WHERE setting_type IN ('ONOFF', 'VOLTAGE', 'CURRENT','SCHEDULE_TIME','ON_OFF_MODE')  ORDER BY setting_type ASC LIMIT 100";
			}
			else if($parameter=="ONOFF"||$parameter=="VOLTAGE"||$parameter=="CURRENT"||$parameter=="SCHEDULE_TIME"||$parameter=="ON_OFF_MODE"){
				$sql = "SELECT * FROM `$db`.`device_settings` where setting_type='$parameter'";
			}
			else{
				mysqli_close($conn);
				$data .= '<tr><td class="text-danger" colspan="5">Records are not Found</td></tr>';
				$data .= "</tbody>";
				echo json_encode($data);
				exit(); 
			}
			
		}
		$stmt = mysqli_prepare($conn, $sql);

		


		if ($sql !== "") {
			if (isset($stmt) && mysqli_stmt_execute($stmt)) {
				$result = mysqli_stmt_get_result($stmt);
				if (mysqli_num_rows($result) > 0) {
					while ($r = mysqli_fetch_assoc($result)) {

						$flag_status = "";
						$configuration = $r['setting_type'];
						$cancel_btn = '';
						if ($r['setting_flag'] == 0) {
							$class = $green;
							$flag_status = "Updated";
						} elseif ($r['setting_flag'] == 1) {
							$class = $red;
							$flag_status = "Pending";
							$cancel_btn = '<button class="btn btn-danger pt-0 pb-0" onclick=cancel_update("' . $configuration . '")>Cancel</button>';
						} elseif ($r['setting_flag'] == 2) {
							$class = $primary;
							$flag_status = "In-progress and waiting for Ack...";
							$cancel_btn = '<button class="btn btn-danger pt-0 pb-0" onclick=cancel_update("' . $configuration . '")>Cancel</button>';
						} elseif ($r['setting_flag'] == 3) {
							$class = $normal;
							$flag_status = "Cancelled";
						}


						$data .= "<tr >    						
    					<td > " . $configuration . "</td>
    					<td $class> " . $flag_status . "</td>    						
    					<td > " . $r['date_time'] . "</td>    						
    					<td > " . $cancel_btn . "</td></tr>";
					}
				} else {
					$data .= '<tr><td class="text-danger" colspan="5">Records are not Found</td></tr>';
				}
				mysqli_stmt_close($stmt);
			} else {
				$data .= '<tr><td class="text-danger" colspan="5">Records are not Found</td></tr>';
			}
		} else {
			$data .= '<tr><td class="text-danger" colspan="5">Records are not Found</td></tr>';
		}

		$data .= "</tbody>";

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
