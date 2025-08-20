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
$class_r=$normal;
$class_y=$green;
$class_b=$green;

$d_name = "";
$data = "";
/*$device_ids="CCMS_1";
$records="LATEST";
$alert="SAVED-SETTINGS";*/


if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['D_ID']) && isset($_POST['RECORDS'])) {
	$device_ids = filter_input(INPUT_POST, 'D_ID', FILTER_SANITIZE_STRING);
	$records = filter_input(INPUT_POST, 'RECORDS', FILTER_SANITIZE_STRING);
	$alert = filter_input(INPUT_POST, 'ALERT', FILTER_SANITIZE_STRING);

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

    			$start_date = date('Y-m-d', strtotime($start_date));
    			$end_date = date('Y-m-d', strtotime($end_date));
    		}
    	}

    	if ($alert == "PING-UPDATES") 
    	{

    		$sql = "SELECT * FROM `$db`.`device_check_report` ORDER BY id DESC LIMIT 100";
    		$stmt = mysqli_prepare($conn, $sql);  

    		$data.="<thead class='sticky-header text-center'>
    		<tr class='header-row-1'>                                    
    		<th class='table-header-row-1'>Parameter</th>                                
    		<th class='table-header-row-1'>Date&Time</th>                                
    		<th class='table-header-row-1'>Status</th>
    		
    		</tr></thead><tbody>";
    		

    		if ($sql !== "") {
    			if (isset($stmt) && mysqli_stmt_execute($stmt)) 
    			{
    				$result = mysqli_stmt_get_result($stmt);
    				if (mysqli_num_rows($result) > 0) {
    					while ($r = mysqli_fetch_assoc($result)) { 

    						$data.= "<tr >    						
    						<td > ".$r['parameter']."</td>
    						<td > ".$r['date_time']."</td>    						
    						<td > ".$r['status']."</td>";
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

    	if ($alert == "SAVED-SETTINGS") 
    	{
    		$row_count = 45;
    		$sql = ""; 

    		if ($records === "LATEST") {
    			$sql = "SELECT * FROM `$db`.`saved_settings_on_device` ORDER BY id DESC LIMIT 50";
    			$stmt = mysqli_prepare($conn, $sql);
    		} else {
    			$sql = "SELECT * FROM `$db`.`saved_settings_on_device` WHERE DATE(date_time) BETWEEN ? AND ? ORDER BY id DESC";
    			$stmt = mysqli_prepare($conn, $sql);
    			mysqli_stmt_bind_param($stmt, 'ss', $start_date, $end_date);
    		}

    		$data = "<thead class='sticky-header text-center'>
    		<tr class='header-row-1'>";

    		for ($i = 1; $i <= $row_count; $i++) {
    			$data .= "<th class='table-header-row-1'>Value " . $i . "</th>";
    		}

    		$data .= "</tr></thead><tbody>";

    		if ($sql !== "") {
    			if (isset($stmt) && mysqli_stmt_execute($stmt)) {
    				$result = mysqli_stmt_get_result($stmt);
    				if (mysqli_num_rows($result) > 0) {
    					while ($r = mysqli_fetch_assoc($result)) {
    						$frame = $r['frame'];
    						if (!empty($frame)) {
    							$frame_array = explode(";", $frame);
    							$data .= "<tr>";
    							foreach ($frame_array as $value) {
    								$data .= "<td>" . htmlspecialchars($value) . "</td>";
    							}
    							$data .= "</tr>";
    						}
    					}
    				} else {
    					$data .= '<tr><td class="text-danger" colspan="' . $row_count . '">Records are not Found</td></tr>';
    				}
    				mysqli_stmt_close($stmt);
    			} else {
    				$data .= '<tr><td class="text-danger" colspan="' . $row_count . '">Records are not Found</td></tr>';
    			}
    		} else {
    			$data .= '<tr><td class="text-danger" colspan="' . $row_count . '">Records are not Found</td></tr>';
    		}

    		$data .= "</tbody>";

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
