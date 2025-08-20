
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

$d_name = "";
$data = "";
/*$device_ids="CCMS_1";
$records="LATEST";
$alert="POWER-ON/OFF";*/


if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['D_ID']) && isset($_POST['RECORDS'])) {
	$device_ids = filter_input(INPUT_POST, 'D_ID', FILTER_SANITIZE_STRING);
	$records = filter_input(INPUT_POST, 'RECORDS', FILTER_SANITIZE_STRING);
	$alert = filter_input(INPUT_POST, 'ALERT', FILTER_SANITIZE_STRING);
	$id =$device_ids;
	include_once("../../common-files/fetch-device-phase.php");
	$phase= $device_phase;
	

	$db = strtolower($device_ids);
	$send = array();
	$send = "";
	$d_name = "";

	$start_date="";
	$end_date ="";

    $conn = mysqli_connect(HOST, USERNAME, PASSWORD); // Ensure DATABASE constant is defined and holds the name of the database
    if (!$conn) {
    	die("Connection failed: " . mysqli_connect_error());
    } else {


    	$device_ids = sanitize_input($device_ids, $conn);
    	$records = sanitize_input($records, $conn);
    	$alert = sanitize_input($alert, $conn);
    	
    	$sql = "";
    	$stmt ="";


    	if ($records === "DATE-RANGE")
    	{
    		if (isset($_POST['START_DATE'])&&isset($_POST['END_DATE'])) {
    			$start_date = trim(filter_input(INPUT_POST, 'START_DATE', FILTER_SANITIZE_STRING));
    			$end_date = trim(filter_input(INPUT_POST, 'END_DATE', FILTER_SANITIZE_STRING));

    			$start_date = sanitize_input($start_date, $conn);
    			$end_date = sanitize_input($end_date, $conn);

    			
    		}
    	}

    	if ($alert == "POWER-ON/OFF") 
    	{
    		if ($records === "LATEST") {
    			$sql = "SELECT * FROM `$db`.`alert_power_failure` ORDER BY id DESC LIMIT 50";
    			$stmt = mysqli_prepare($conn, $sql);    			
    		} else {
    			$sql = "SELECT * FROM `$db`.`alert_power_failure` WHERE DATE(date_time) BETWEEN ? AND ? ORDER BY id DESC";
    			
    			$stmt = mysqli_prepare($conn, $sql);
    			mysqli_stmt_bind_param($stmt, 'ss', $start_date, $end_date);
    		}
    		if($phase=="3PH")
    		{
    			$data.="<thead class='sticky-header text-center'>
    			<tr class='header-row-1'>                                    
    			<th class='table-header-row-1'>Power status</th>                                
    			<th class='table-header-row-1'>Battery(mV)</th>    
    			<th class='table-header-row-1'>System Voltage(mV)</th> 
    			<th class='table-header-row-1'>Ph-R(V)</th> 
    			<th class='table-header-row-1'>Ph-Y(V)</th> 
    			<th class='table-header-row-1'>Ph-B(V)</th>                          
    			<th class='table-header-row-1'>Data & Time</th>
    			</tr></thead><tbody>";
    		}
    		else{
    			$data.="<thead class='sticky-header text-center'>
    			<tr class='header-row-1'>                                    
    			<th class='table-header-row-1'>Power status</th>                                
    			<th class='table-header-row-1'>Battery(mV)</th>    
    			<th class='table-header-row-1'>System Voltage(mV)</th> 
    			<th class='table-header-row-1'>Phase (V)</th> 

    			<th class='table-header-row-1'>Data & Time</th>
    			</tr></thead><tbody>";
    		}
    		if ($sql !== "") {
    			if (isset($stmt) && mysqli_stmt_execute($stmt)) 
    			{
    				$result = mysqli_stmt_get_result($stmt);
    				if (mysqli_num_rows($result) > 0) {
    					while ($r = mysqli_fetch_assoc($result)) { 

    						if( strpos($r['status'], "OFF") == true || strpos($r['status'], "Disconnected") == true){
    							$class_r=$red;
    						}
    						elseif( strpos($r['status'], "ON") == true || strpos($r['status'], "Restored") == true){
    							$class_r=$green;
    						}
    						if($phase=="3PH")
    						{
    							$data.= "<tr >

    							<td $class_r > ".$r['status']."</td>
    							<td $class_r > ".$r['battery_voltage']."</td>   
    							<td $class_r > ".$r['system_voltage']."</td> 
    							<td $class_r > ".$r['ph_r']."</td> 
    							<td $class_r > ".$r['ph_y']."</td> 
    							<td $class_r > ".$r['ph_b']."</td>  						
    							<td $class_r > ".$r['date_time']."</td></tr>";
    						}
    						else
    						{
    							$data.= "<tr >

    							<td $class_r > ".$r['status']."</td>
    							<td $class_r > ".$r['battery_voltage']."</td>   
    							<td $class_r > ".$r['system_voltage']."</td> 
    							<td $class_r > ".$r['ph_r']."</td> 

    							<td $class_r > ".$r['date_time']."</td></tr>";
    						}
    					}
    				} else {
    					$data.= '<tr><td class="text-danger" colspan="5">Records are not Found</td></tr>';
    				}
    				mysqli_stmt_close($stmt);
    			} else {
    				$data.= '<tr><td class="text-danger" colspan="5">Records are not Found</td></tr>';
    			}
    		} else {
    			$data.= '<tr><td class="text-danger" colspan="5">Records are not Found</td></tr>';
    		}

    		$data.="</tbody>";

    	}

    	if ($alert == "BATTERY_STATUS") 
    	{
    		if ($records === "LATEST") {
    			$sql = "SELECT * FROM `$db`.`alert_power_supply_check` ORDER BY id DESC LIMIT 50";
    			$stmt = mysqli_prepare($conn, $sql);    			
    		} else {
    			$sql = "SELECT * FROM `$db`.`alert_power_supply_check` WHERE DATE(date_time) BETWEEN ? AND ? ORDER BY id DESC";
    			
    			$stmt = mysqli_prepare($conn, $sql);
    			mysqli_stmt_bind_param($stmt, 'ss', $start_date, $end_date);
    		}

    		$data.="<thead class='sticky-header text-center'>
    		<tr class='header-row-1'>                                    
    		<th class='table-header-row-1'>Battery Voltage(mV)</th>
    		<th class='table-header-row-1'>Data & Time</th>
    		</tr></thead><tbody>";

    		if ($sql !== "") 
    		{
    			if (isset($stmt) && mysqli_stmt_execute($stmt)) 
    			{
    				$result = mysqli_stmt_get_result($stmt);
    				if (mysqli_num_rows($result) > 0) {
    					while ($r = mysqli_fetch_assoc($result)) { 
    						$data.= "<tr >
    						<td > ".$r['battery_voltage']."</td>
    						<td> ".$r['date_time']."</td></tr>";
    					}
    				} else {
    					$data.= '<tr><td class="text-danger" colspan="3">Records are not Found</td></tr>';
    				}
    				mysqli_stmt_close($stmt);
    			} else {
    				$data.= '<tr><td class="text-danger" colspan="3">Records are not Found</td></tr>';
    			}
    		} else {
    			$data.= '<tr><td class="text-danger" colspan="3">Records are not Found</td></tr>';
    		}

    		$data.="</tbody>";
    	}

    	if ($alert == "DOOR") 
    	{
    		if ($records === "LATEST") {
    			$sql = "SELECT * FROM `$db`.`alert_door` ORDER BY id DESC LIMIT 50";
    			$stmt = mysqli_prepare($conn, $sql);    			
    		} else {
    			$sql = "SELECT * FROM `$db`.`alert_door` WHERE DATE(date_time) BETWEEN ? AND ? ORDER BY id DESC";
    			
    			$stmt = mysqli_prepare($conn, $sql);
    			mysqli_stmt_bind_param($stmt, 'ss', $start_date, $end_date);
    		}

    		$data.="<thead class='sticky-header text-center'>
    		<tr class='header-row-1'>                                    
    		<th class='table-header-row-1'>Door Alert</th>
    		<th class='table-header-row-1'>Data & Time</th>
    		</tr></thead><tbody>";

    		if ($sql !== "") 
    		{
    			if (isset($stmt) && mysqli_stmt_execute($stmt)) 
    			{
    				$result = mysqli_stmt_get_result($stmt);
    				if (mysqli_num_rows($result) > 0) {
    					while ($r = mysqli_fetch_assoc($result)) { 

    						if( strpos(strtoupper($r['alert']), "OPEN") == true){
    							$class_r=$red;
    						}
    						elseif( strpos(strtoupper($r['alert']), "CLOSE") == true){
    							$class_r=$green;
    						}
    						$data.= "<tr >
    						
    						<td $class_r > ".$r['alert']."</td>
    						<td  $class_r> ".$r['date_time']."</td></tr>";
    					}
    				} else {
    					$data.= '<tr><td class="text-danger" colspan="3">Records are not Found</td></tr>';
    				}
    				mysqli_stmt_close($stmt);
    			} else {
    				$data.= '<tr><td class="text-danger" colspan="3">Records are not Found</td></tr>';
    			}
    		} else {
    			$data.= '<tr><td class="text-danger" colspan="3">Records are not Found</td></tr>';
    		}

    		$data.="</tbody>";
    	}

    	mysqli_close($conn);
    }

    echo json_encode($data);
}

function sanitize_input($data, $conn) {
	$data = trim($data);
	$data = stripslashes($data);
	$data = htmlspecialchars($data);
	return mysqli_real_escape_string($conn, $data);
}
?>
