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
$selection="";
$phase="3PH";
/*$device_ids="SPMS_1";
$records="LATEST";
*/


if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['D_ID']) && isset($_POST['RECORDS'])) {
	$device_ids = filter_input(INPUT_POST, 'D_ID', FILTER_SANITIZE_STRING);
	$records = filter_input(INPUT_POST, 'RECORDS', FILTER_SANITIZE_STRING);

	$db = strtolower($device_ids);
	$send = array();
	$send = "";
	$d_name = "";
	$id =$device_ids;

	include_once("../../common-files/fetch-device-phase.php");
	$phase= $device_phase;

    $conn = mysqli_connect(HOST, USERNAME, PASSWORD); // Ensure DATABASE constant is defined and holds the name of the database
    if (!$conn) {
    	die("Connection failed: " . mysqli_connect_error());
    } else {

    	$db_check_query = "SHOW DATABASES LIKE '$db';";
    	$result = mysqli_query($conn, $db_check_query);

    	if (mysqli_num_rows($result) >= 1) {

    		$device_id=strtoupper($db);
    		include("set_parameters.php");

    		$sql = "";
    		$stmt ="";
    		if ($records === "LATEST") {
    			$sql = "SELECT * FROM `$db`.`live_data` ORDER BY id DESC LIMIT 20";
    			$stmt = mysqli_prepare($conn, $sql);
    		} elseif ($records === "ADD") {
    			if (isset($_POST['DATE_TIME'])) {
    				$date = trim(filter_input(INPUT_POST, 'DATE_TIME', FILTER_SANITIZE_STRING));
    				$cr_date_time = str_replace('/', '-', $date);
    				$date_new = date_create_from_format('H:i:s d-m-Y', $cr_date_time);
    				if ($date_new !== false) {
    					$date = date_format($date_new, "Y-m-d H:i:s");
    					$sql = "SELECT * FROM `$db`.`live_data` WHERE date_time < ? ORDER BY id DESC LIMIT 200";
    					$stmt = mysqli_prepare($conn, $sql);
    					mysqli_stmt_bind_param($stmt, 's', $date);
    				} else {
    					$data = '<tr><td class="text-danger" colspan="75">Records are not Found. Date-Time format error</td></tr>';
    					mysqli_close($conn);
    					echo json_encode($data);
    					exit();
    				}
    			} else {
    				$data = '<tr><td class="text-danger" colspan="75">Records are not Found. Empty parameter sent</td></tr>';
    				mysqli_close($conn);
    				echo json_encode($data);
    				exit();
    			}
    		} elseif ($records === "DATE") {
    			if (isset($_POST['DATE'])) {
    				$date = trim(filter_input(INPUT_POST, 'DATE', FILTER_SANITIZE_STRING));
    				$date_formatted = date('Y-m-d', strtotime($date));
    				$sql = "SELECT * FROM `$db`.`live_data` WHERE DATE(date_time) = ? ORDER BY id DESC LIMIT 200";
    				$stmt = mysqli_prepare($conn, $sql);
    				mysqli_stmt_bind_param($stmt, 's', $date_formatted);
    			} else {
    				$data = '<tr><td class="text-danger" colspan="75">Records are not Found. Empty date parameter sent</td></tr>';
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
    						include("table_cells.php");
    					}
    				} else {
    					$data = '<tr><td class="text-danger" colspan="75">Records are not Found</td></tr>';
    				}
    				mysqli_stmt_close($stmt);
    			} else {
    				$data = '<tr><td class="text-danger" colspan="75">Records are not Found</td></tr>';
    			}
    		} else {
    			$data = '<tr><td class="text-danger" colspan="75">Records are not Found</td></tr>';
    		}
    		
    	}
    	else {
    		$data = '<tr><td class="text-danger" colspan="75"> '.strtoupper($db).' records does not exist</td></tr>';
    		
    	}
    	mysqli_close($conn);
    }

    echo json_encode(array($data, $phase));
}
?>
