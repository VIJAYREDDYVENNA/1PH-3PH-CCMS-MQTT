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
$red='class="text-danger "'; 
$orange='class="text-warning "'; 
$green='class="text-success "'; 
$class_r=$green;
$class_y=$green;
$class_b=$green;
$class_primary='class="text-primary fw-bold "'; 
$class_danger='class="text-danger fw-bold "'; 
$class_info='class="text-info fw-bold "'; 
$class_warning='class="text-warning fw-bold "'; 

$d_name = "";
$data = "";
/*$device_ids="CCMS_1";
$records="LATEST";
$alert="OVERLOAD";*/
$phase = array();
$device_phase="3PH";

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['D_ID']) && isset($_POST['RECORDS'])) {
	$device_ids = filter_input(INPUT_POST, 'D_ID', FILTER_SANITIZE_STRING);
	$records = filter_input(INPUT_POST, 'RECORDS', FILTER_SANITIZE_STRING);
	$alert = filter_input(INPUT_POST, 'ALERT', FILTER_SANITIZE_STRING);

	$db = strtolower($device_ids);
	$send = array();
	$send = "";
	$d_name = "";
	$id=$device_ids;

	include_once("../../common-files/fetch-device-phase.php");

    $conn = mysqli_connect(HOST, USERNAME, PASSWORD); // Ensure DATABASE constant is defined and holds the name of the database
    if (!$conn) {
    	die("Connection failed: " . mysqli_connect_error());
    } else {




    	
    	$sql = "";
    	$stmt ="";
    	if ($records === "LATEST") 
    	{
    		if ($alert == "ALL") {
    			$sql = "SELECT * FROM `$db`.`alert_phases` ORDER BY id DESC LIMIT 50";
    			$stmt = mysqli_prepare($conn, $sql);
    		} else {
    			$sql = "SELECT * FROM `$db`.`alert_phases` WHERE alert_name = ? ORDER BY id DESC LIMIT 50";
    			$stmt = mysqli_prepare($conn, $sql);
    			mysqli_stmt_bind_param($stmt, 's', $alert);
    		}

    	}
    	elseif ($records === "DATE-RANGE") {
    		if (isset($_POST['START_DATE'])&&isset($_POST['END_DATE'])) {
    			$start_date = trim(filter_input(INPUT_POST, 'START_DATE', FILTER_SANITIZE_STRING));
    			$end_date = trim(filter_input(INPUT_POST, 'END_DATE', FILTER_SANITIZE_STRING));
    			

    			if ($alert == "ALL") {
    				$sql = "SELECT * FROM `$db`.`alert_phases` WHERE DATE(date_time) BETWEEN ? AND ? ORDER BY id DESC";
    				$types = 'ss'; 
    			} else {
    				$sql = "SELECT * FROM `$db`.`alert_phases` WHERE alert_name=? AND DATE(date_time) BETWEEN ? AND ? ORDER BY id DESC";
    				$types = 'sss'; 
    			}
    			$stmt = mysqli_prepare($conn, $sql);
    			if ($alert == "ALL") {
    				mysqli_stmt_bind_param($stmt, $types, $start_date, $end_date);
    			} else {
    				mysqli_stmt_bind_param($stmt, $types, $alert, $start_date, $end_date);
    			}

    		} else {
    			$data = '<tr><td class="text-danger" colspan="12">Records are not Found. Empty date parameter sent</td></tr>';
    			mysqli_close($conn);
    			echo json_encode($data);
    			exit();
    		}
    	}


    	if ($sql !== "") {
    		if (isset($stmt) && mysqli_stmt_execute($stmt)) 
    		{
    			$result = mysqli_stmt_get_result($stmt);
    			if (mysqli_num_rows($result) > 0) {
    				while ($r = mysqli_fetch_assoc($result)) { 
    					$class_r=$green;
    					$class_y=$green;
    					$class_b=$green;
    					if($r['ph_r']=="LOW"||$r['ph_r']=="HIGH"||$r['ph_r']=="OVERLOAD"||$r['ph_r']=="TRIPPED"){
    						$class_r=$red;
    					}
    					if($r['ph_y']=="LOW"||$r['ph_y']=="HIGH"||$r['ph_y']=="OVERLOAD"||$r['ph_y']=="TRIPPED"){
    						$class_y=$red;
    					}
    					if($r['ph_b']=="LOW"||$r['ph_b']=="HIGH"||$r['ph_b']=="OVERLOAD"||$r['ph_b']=="TRIPPED"){
    						$class_b=$red;
    					}
    					$class_parmeter=$class_primary;
    					if($r['alert_name']=="VOLTAGE")
    					{
    						$class_parmeter=$class_primary;
    					}
    					else if($r['alert_name']=="CURRENT"||$r['alert_name']=="OVERLOAD")
    					{
    						$class_parmeter=$class_info;
    					}
    					else if($r['alert_name']=="CONTACTOR/MCB")
    					{
    						$class_parmeter=$class_warning;
    					}
    					else
    					{
    						$class_parmeter=$class_danger;
    					}

    					if($device_phase=="3PH")
    					{
    						$data.= "<tr >    						
    						<td $class_parmeter > ".$r['alert_name']."</td>
    						<td $class_r > ".$r['ph_r']."</td>
    						<td $class_y > ".$r['ph_y']."</td>
    						<td $class_b > ".$r['ph_b']."</td>    					
    						<td> ".$r['v_r']."</td>
    						<td> ".$r['v_y']."</td>
    						<td> ".$r['v_b']."</td>
    						<td> ".$r['i_r']."</td>
    						<td> ".$r['i_y']."</td>
    						<td> ".$r['i_b']."</td>
    						<td> ".$r['date_time']."</td></tr>";
    					}
    					else if($device_phase=="1PH")
    					{
    						$data.= "<tr >
    						<td $class_parmeter > ".$r['alert_name']."</td>
    						<td $class_r > ".$r['ph_r']."</td>
    						<td> ".$r['v_r']."</td>
    						<td> ".$r['i_r']."</td>
    						<td> ".$r['date_time']."</td></tr>";
    					}


    				}
    			} else {
    				$data = '<tr><td class="text-danger" colspan="12">Records are not Found</td></tr>';
    			}
    			mysqli_stmt_close($stmt);
    		} else {
    			$data = '<tr><td class="text-danger" colspan="12">Records are not Found</td></tr>';
    		}
    	} else {
    		$data = '<tr><td class="text-danger" colspan="12">Records are not Found</td></tr>';
    	}
    	mysqli_close($conn);
    }

    echo json_encode(array($data, $phase));
}
?>
