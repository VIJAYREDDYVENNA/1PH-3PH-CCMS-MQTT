<?php
require_once '../base-path/config-path.php';
require_once BASE_PATH.'config_db/config.php';
require_once BASE_PATH.'session/session-manager.php';
SessionManager::checkSession();
$sessionVars = SessionManager::SessionVariables();
$mobile_no = $sessionVars['mobile_no'];
$user_id = $sessionVars['user_id'];
$role = $sessionVars['role'];
$user_login_id = $sessionVars['user_login_id'];

$return_response = "";
$add_confirm = false;
$code ="";
$user_devices="";


$device_list = array ();

if ($_SERVER["REQUEST_METHOD"] == "POST"&&isset($_POST["GROUP_ID"])) 
{
	$group_id = $_POST['GROUP_ID'];
	
	include_once("selecting_group_device.php");
	$_SESSION["DEVICES_LIST"] =json_encode($device_list);
	echo json_encode($device_list);
}
else
{
	echo json_encode("FAIL");
}

?>