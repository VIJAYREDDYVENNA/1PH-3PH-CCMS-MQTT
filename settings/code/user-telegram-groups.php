<?php
require_once BASE_PATH . 'config_db/config.php';
require_once BASE_PATH . 'session/session-manager.php';

SessionManager::checkSession();
$sessionVars = SessionManager::SessionVariables();
$mobile_no = $sessionVars['mobile_no'];
$user_id = $sessionVars['user_id'];
$role = $sessionVars['role'];
$user_login_id = $sessionVars['user_login_id'];

$conn = mysqli_connect(HOST, USERNAME, PASSWORD, DB_USER);
if (!$conn) {
	die("Connection failed: " . mysqli_connect_error());
}
else
{
	$device_list=array();
	$sql ="";
	if($role=="SUPERADMIN")
	{
		$sql = "SELECT *from telegram_groups_new";
	}
	else
	{
		$sql = "SELECT *from telegram_groups_new where user_id = '$user_login_id' ";
	}

	echo '<option value="">Select Telegram Group</option>';	
	if(mysqli_query( $conn, $sql))
	{
		$result = mysqli_query($conn, $sql);
		if(mysqli_num_rows($result)>0)
		{
			while ($r=  mysqli_fetch_assoc( $result)) 
			{	
				echo '<option value="'. $r['chat_id'] .'">'. $r['group_name'].'</option>';	
			}

		}
	}
	mysqli_close($conn);
	
}
?>