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
$group_by_column="";
$group_list=array();

$device_list = array ();

/*if ($_SERVER["REQUEST_METHOD"] == "POST"&&isset($_POST["PHASE"])) 
{
	$phaseWise = $_POST['PHASE']; 
	if($phaseWise=="3PH" || $phaseWise=="1PH" || $phaseWise== "ALL")
	{*/
		$phaseWise="ALL";
		$group_id="ALL";

		$_SESSION["SELECTED_PHASE"] = strtoupper($phaseWise);

		include_once("selecting_group_device.php");
		$_SESSION["DEVICES_LIST"] =json_encode($device_list);



		$conn_user = mysqli_connect(HOST, USERNAME, PASSWORD, DB_USER);

		
		echo $sql_group = "SELECT group_by FROM device_selection_group WHERE login_id = ? ";
		echo "<br>";
		/*if($selected_phase!="ALL")
		{
			$sql_group = "SELECT group_by FROM device_selection_group WHERE login_id = ? AND phase='$selected_phase' ";
		}*/


		$stmt_group = mysqli_prepare($conn_user, $sql_group);
		mysqli_stmt_bind_param($stmt_group, "i", $user_login_id);

		if (mysqli_stmt_execute($stmt_group)) {
			mysqli_stmt_store_result($stmt_group);
			mysqli_stmt_bind_result($stmt_group, $group_by_column);
			mysqli_stmt_fetch($stmt_group);
		}

		if($group_by_column==null||$group_by_column=="")
		{
			$group_by_column="device_group_or_area";
		}
		mysqli_stmt_close($stmt_group);
		echo $sql_group_list = "SELECT `$group_by_column` AS `group_list`  FROM device_list_by_group  WHERE login_id = ? GROUP BY `$group_by_column` ORDER BY `$group_by_column`";
		if($selected_phase!="ALL")
		{
			$sql_group_list = "SELECT `$group_by_column` AS `group_list`  FROM device_list_by_group  WHERE login_id = ? AND phase='$selected_phase' GROUP BY `$group_by_column` ORDER BY `$group_by_column`";
		}

		$stmt = mysqli_prepare($conn_user, $sql_group_list);
		mysqli_stmt_bind_param($stmt, "i", $user_login_id);

		if (mysqli_stmt_execute($stmt)) {
			$results = mysqli_stmt_get_result($stmt);
			if (mysqli_num_rows($results) > 0) {
				while ($r = mysqli_fetch_assoc($results)) {
					
					$group_list[] = array("GROUP" => strtoupper($r['group_list']));
				}
			}
		}

		$_SESSION["GROUP_LIST"] = json_encode($group_list);


		
		echo json_encode(array($device_list,$group_list));
		//echo json_encode($group_list);
	/*}
	else
	{
		echo json_encode("FAIL");
	}
}
else
{
	echo json_encode("FAIL");
}*/


?>




