<?php

$group_id=strtoupper(trim($group_id));
$selected_phase=strtoupper($_SESSION["SELECTED_PHASE"]);
$conn_user = mysqli_connect(HOST, USERNAME, PASSWORD, DB_USER);
$group_by="device_group_or_area";
if (!$conn_user) {
	die("Connection failed: " . mysqli_connect_error());
} else {

	$sql = "";
	
	$stmt = "";
	$total_switch_point=0;
	$group_id = htmlspecialchars(mysqli_real_escape_string($conn_user, $group_id));
	require_once("client-super-admin-device-names.php");
	
	if($group_id=="ALL")
	{

		$sql = "SELECT $list FROM user_device_list WHERE login_id = ?  ORDER BY REGEXP_REPLACE(device_id, '[0-9]', ''),  CAST(REGEXP_REPLACE(device_id, '[^0-9]', '') AS UNSIGNED); ";
		if($selected_phase!="ALL")
		{
			$sql = "SELECT $list FROM user_device_list WHERE login_id = ? AND phase='$selected_phase'  ORDER BY REGEXP_REPLACE(device_id, '[0-9]', ''),  CAST(REGEXP_REPLACE(device_id, '[^0-9]', '') AS UNSIGNED); ";
		}

		//$sql = "SELECT $list FROM user_device_list WHERE login_id = ?  ORDER BY REGEXP_REPLACE(device_id, '[0-9]', ''),  CAST(REGEXP_REPLACE(device_id, '[^0-9]', '') AS UNSIGNED); ";
		$stmt = mysqli_prepare($conn_user, $sql);
		mysqli_stmt_bind_param($stmt, "i", $user_login_id);
	}
	else
	{
		
		$sql_group = "SELECT group_by FROM device_selection_group WHERE login_id = ? ";
		/*if($selected_phase!="ALL")
		{
			$sql_group = "SELECT group_by FROM device_selection_group WHERE login_id = ? AND phase='$selected_phase' ";
		}*/


		$stmt_group = mysqli_prepare($conn_user, $sql_group);
		mysqli_stmt_bind_param($stmt_group, "i", $user_login_id);

		if (mysqli_stmt_execute($stmt_group)) {
			mysqli_stmt_store_result($stmt_group);
			mysqli_stmt_bind_result($stmt_group, $group_by);
			mysqli_stmt_fetch($stmt_group);
		}
		mysqli_stmt_close($stmt_group);

		$sql = "SELECT $list FROM user_device_group_view WHERE login_id = ? AND $group_by = ?  ORDER BY REGEXP_REPLACE(device_id, '[0-9]', ''),  CAST(REGEXP_REPLACE(device_id, '[^0-9]', '') AS UNSIGNED); ";
		if($selected_phase!="ALL")
		{
			$sql = "SELECT $list FROM user_device_group_view WHERE login_id = ? AND $group_by = ? AND phase='$selected_phase'  ORDER BY REGEXP_REPLACE(device_id, '[0-9]', ''),  CAST(REGEXP_REPLACE(device_id, '[^0-9]', '') AS UNSIGNED); ";

		}
		$stmt = mysqli_prepare($conn_user, $sql);
		mysqli_stmt_bind_param($stmt, "is", $user_login_id, $group_id);

	}
	if (mysqli_stmt_execute($stmt)) {
		$results = mysqli_stmt_get_result($stmt);
		if (mysqli_num_rows($results) > 0) {
			while ($r = mysqli_fetch_assoc($results)) {
				$device_list[] = array("D_ID" => $r['device_id'], "D_NAME" => $r['device_name']);
				$user_devices=$user_devices."'".$r['device_id']."',";
				$total_switch_point+=1;
			}
		}
	}
	mysqli_close($conn_user);

}


?>