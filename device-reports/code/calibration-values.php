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


/*$device_ids="CCMS_1";
$row_id="0";
$row_search="LATEST";*/


if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['D_ID']) && isset($_POST['ROW'])&& isset($_POST['ROW_VIEW'])) {

	$device_ids = filter_input(INPUT_POST, 'D_ID', FILTER_SANITIZE_STRING);
	$row_id = filter_input(INPUT_POST, 'ROW', FILTER_SANITIZE_STRING);
	$row_search = filter_input(INPUT_POST, 'ROW_VIEW', FILTER_SANITIZE_STRING);

	$db = strtolower($device_ids);
	$send = array();
	$send = "";
	$d_name = "";

	$start_date="";
	$end_date ="";

    $conn = mysqli_connect(HOST, USERNAME, PASSWORD); // Ensure DATABASE constant is defined and holds the name of the database
    if (!$conn) {
    	die("Connection failed: " . mysqli_connect_error());
    } 
    else
    {
    	$sql = "";
    	if($row_id==0&&$row_search=="LATEST")
    	{		
    		$sql = "SELECT *from `$db`.`saved_settings_on_device`  ORDER BY id DESC LIMIT 1";
    	}
    	else if($row_search=="PREV")
    	{
    		$row_id=$row_id-1;
    		$sql = "SELECT *from `$db`.`saved_settings_on_device` WHERE id<= $row_id ORDER BY id DESC LIMIT 1";

    	}
    	else if($row_search=="NEXT")
    	{
    		$row_id=$row_id+1;
    		$sql = "SELECT *from `$db`.`saved_settings_on_device` WHERE id>= $row_id  ORDER BY id ASC LIMIT 1";
    	}


    	if(mysqli_query($conn, $sql))
    	{
    		$result = mysqli_query($conn, $sql);
    		if(mysqli_num_rows($result)>0)
    		{
    			while($r = mysqli_fetch_assoc( $result ) ) 
    			{					
    				$f1 = $r['frame'].";".$r['id'];
    				$send=explode(';', $f1 );
    			}	
    		}
    		else
    		{

    		}
    	}
    	

    	mysqli_close($conn);
    }

    echo json_encode($send);
}

function sanitize_input($data, $conn) {
	$data = trim($data);
	$data = stripslashes($data);
	$data = htmlspecialchars($data);
	return mysqli_real_escape_string($conn, $data);
}
?>