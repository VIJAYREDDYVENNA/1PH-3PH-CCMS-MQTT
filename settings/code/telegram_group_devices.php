<?php
require_once '../../base-path/config-path.php';
require_once BASE_PATH_1 . 'config_db/config.php';
require_once BASE_PATH_1 . 'session/session-manager.php';

// Check session and retrieve session variables
SessionManager::checkSession();
$sessionVars = SessionManager::SessionVariables();
$mobile_no = $sessionVars['mobile_no'];
$user_id = $sessionVars['user_id'];
$role = $sessionVars['role'];
$user_email = $sessionVars['user_email'];
$user_login_id = $sessionVars['user_login_id'];


if(isset($_POST['GROUP_ID']))
{	
	$id=$_POST['GROUP_ID'];
	/*$id="-1001637935906";*/
	$send = array();
	$conn = mysqli_connect(HOST, USERNAME, PASSWORD, DB_USER);
	if (!$conn) {
		die("Connection failed: " . mysqli_connect_error());
	}
	else
	{
		
		//$sql = "SELECT * FROM `telegram_groups_devices` WHERE group_id=(SELECT id from  `telegram_groups_new` where chat_id='$id' limit 1)";
		$sql = "";
		if($role=="SUPERADMIN")
		{
			$sql = "SELECT device_id, c_device_name AS device_name FROM `user_device_list` WHERE login_id='$user_login_id' AND device_id IN( SELECT device_id FROM `telegram_groups_devices` WHERE group_id=(SELECT id from `telegram_groups_new` where chat_id='$id' limit 1));";
		}
		else
		{
			$sql = "SELECT device_id, c_device_name AS device_name FROM `user_device_list` WHERE login_id='$user_login_id' AND device_id IN( SELECT device_id FROM `telegram_groups_devices` WHERE group_id=(SELECT id from `telegram_groups_new` where chat_id='$id' limit 1));";
		}
		
		if(mysqli_query($conn, $sql))
		{
			$result = mysqli_query($conn, $sql);
			if(mysqli_num_rows($result)>0)
			{
				while($r=mysqli_fetch_assoc( $result ))
				{
					$send[]=$r;
				}
			}
		}
		
		echo json_encode($send);
		// /echo count($frame_array);
		mysqli_close($conn);
	}
}
?>