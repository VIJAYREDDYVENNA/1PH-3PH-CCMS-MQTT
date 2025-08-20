<?php
require("../config_db/config.php");

// Initialize variables
$mobile_no = "";
$e_mail = "";
$user_id = "";
$user_name = "";
$role = "";
$user_type = "";
$client = "";
$status = "INACTIVE";
$client_login_verion = "";
$redirect = false;
$count = 0;
$login_id = 0;
$delete_status = 0;
$credentials_check=false;

// Create connection
$conn = mysqli_connect(HOST, USERNAME, PASSWORD, DB_USER);

if (!$conn) {
	die("Connection failed: " . mysqli_connect_error());
}

$user_login_id = trim(strtolower(mysqli_real_escape_string($conn, $user_login_id)));
$password = trim(mysqli_real_escape_string($conn, $password));

// Prepare SQL statement
$sql = "SELECT * FROM login_details WHERE (mobile_no = ? OR email_id = ? OR user_id = ?)";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "sss", $user_login_id, $user_login_id, $user_login_id);

if (mysqli_stmt_execute($stmt)) {
	$result = mysqli_stmt_get_result($stmt);
	$count = mysqli_num_rows($result);

	if ($count == 1) {
		$r = mysqli_fetch_assoc($result);

		if (password_verify($password, $r['password'])) {
			
			$mobile_no = trim(strtolower($r['mobile_no']));
			$e_mail = trim(strtolower($r['email_id']));
			$user_id = $r['user_id'];
			$user_name = $r['name'];
			$role = $r['role'];
			$user_type = $r['user_type'];
			$client = $r['client'];
			$status = $r['status'];
			$client_login_verion = $r['client_login'];
			$login_id = $r['id'];
			
			$delete_status = $r['account_delete'];
			$credentials_check=true;
		} else {
			$GLOBALS['login_error'] = "Invalid Credentials";

		}
	} else {
		$GLOBALS['login_error'] = $count > 1 ? "This account has multiple credentials. Please contact support." : "Invalid Credentials";
	}
} else {
	$GLOBALS['login_error'] = "Something went wrong. Please try again.";
}

mysqli_stmt_close($stmt);

if($credentials_check)
{
	if($delete_status===1)
	{
		if (strtoupper($status) === "ACTIVE") {

			$_SESSION['mobile_no'] = $mobile_no;
			$_SESSION['login_user_id'] = $user_id;
			$_SESSION['user_name'] = $user_name;
			$_SESSION['user_email'] = $e_mail;
			$_SESSION['role'] = $role;
			$_SESSION['user_type'] = $user_type;
			$_SESSION['client'] = $client;
			$_SESSION['status'] = $status;
			$_SESSION['client_login'] = $client_login_verion;
			$_SESSION['user_login_id'] = $login_id;

			echo "<script> localStorage.setItem('client_type', '$login_path'); </script>";

			activity_log($conn, $mobile_no, $e_mail, $login_id);

			$device_list = array();
			$group_list = array();


			require("../common-files/client-super-admin-device-names.php");
			$sql = "SELECT $list FROM user_device_list WHERE login_id = ? ORDER BY LEFT(device_id, LENGTH(device_id) - LENGTH(SUBSTRING_INDEX(device_id, '_', -1))), CAST(SUBSTRING_INDEX(device_id, '_', -1) AS UNSIGNED) ";
			$stmt = mysqli_prepare($conn, $sql);
			mysqli_stmt_bind_param($stmt, "i", $login_id);

			if (mysqli_stmt_execute($stmt)) {
				$result = mysqli_stmt_get_result($stmt);
				if (mysqli_num_rows($result) > 0) {
					$redirect = true;

					while ($r = mysqli_fetch_assoc($result)) {
						$device_list[] = array("D_ID" => $r['device_id'], "D_NAME" => $r['device_name']);
					}
				} else {
					if ($role == "SUPERADMIN" || $role == "ADMIN") {
						fetch_menu_permissions($login_id, $conn);
						header("location:device-list.php");
						exit();
					} else {
						$GLOBALS['login_error'] = "Please contact your admin.";
					}
				}
			} else {
				$GLOBALS['login_error'] = "Please try again later.";
			}

			mysqli_stmt_close($stmt);
			$group_by_column="device_group_or_area";
			$query = "SELECT `group_by` FROM device_selection_group WHERE login_id = ?";
			$stmt = mysqli_prepare($conn, $query);
			mysqli_stmt_bind_param($stmt, "i", $login_id);  
			mysqli_stmt_execute($stmt);
			mysqli_stmt_bind_result($stmt, $group_by_column);
			mysqli_stmt_fetch($stmt);
			mysqli_stmt_close($stmt);

			//$sql_group_list = "SELECT `device_group_or_area` AS `group_list` FROM device_list_by_group WHERE login_id = ? GROUP BY device_group_or_area ORDER BY device_group_or_area";
			$sql_group_list = "SELECT `$group_by_column` AS `group_list`  FROM device_list_by_group  WHERE login_id = ?  GROUP BY `$group_by_column` ORDER BY `$group_by_column`";


			$stmt = mysqli_prepare($conn, $sql_group_list);
			mysqli_stmt_bind_param($stmt, "i", $login_id);

			if (mysqli_stmt_execute($stmt)) {
				$results = mysqli_stmt_get_result($stmt);
				if (mysqli_num_rows($results) > 0) {
					while ($r = mysqli_fetch_assoc($results)) {
						$group_list[] = array("GROUP" => strtoupper($r['group_list']));
					}
				}
			}


			$_SESSION["SELECTED_PHASE"] = "ALL";
			$_SESSION["DEVICES_LIST"] = json_encode($device_list);
			$_SESSION["GROUP_LIST"] = json_encode($group_list);

			if ($redirect) {
				$_SESSION["login_time_stamp"] = time();

				
				fetch_menu_permissions($login_id, $conn);
				mysqli_close($conn);
				if($login_path=="0")
				{
					header("location:index.php");
				}
				else
				{
					header("location:../$client_login_verion/index.php");
				}
				exit();
			}
		} else {
			$GLOBALS['login_error'] = "Your account is Inactive.";
		}
	} else {
		$GLOBALS['login_error'] = "Your account has been deleted.";
	}
}

mysqli_close($conn);


function fetch_menu_permissions($login_id, $conn)
{
	$sql = "SELECT * FROM `menu_permissions_list` WHERE login_id='$login_id'";
	$result = mysqli_query($conn, $sql);
	$permissions = "";
	if ($result) {
		if (mysqli_num_rows($result) > 0) {
			$count = 0;
			$r = mysqli_fetch_assoc($result);
			$permission_fields = [
				'device_dashboard' => 'Device Overview',
				'dashboard' => 'Dashboard Access',
				'devices_list' => 'Devices List',
				'onoff_control' => 'ON/OFF Control',
				'gis_map' => 'GIS Map',
				'data_report' => 'Data Report',
				'energy_consumption' => 'Energy Consumption',
				'thresholdsettings' => 'Threshold Settings',
				'group_creation' => 'Group Creation',
				'location_update' => 'Location Update',
				'notification_settings' => 'Notification Settings',
				'iotsettings' => 'IoT Settings',
				'pending_actions' => 'Pending Actions',
				'add_new_electrician_devices' => 'Add New Electrician Devices',
				'phase_alerts' => 'Phase Alerts',
				'alerts' => 'Alerts',
				'notification_mesages' => 'Notification Messages',
				'graphs' => 'Graphs',
				'up_down_time' => 'Up/Down Time',
				'glowing_time' => 'Glowing Time',
				'user_activity' => 'User Activity',
				'download' => 'Download',
				'complaints' => 'Complaints',
				'office_use' => 'Office Use',
				'users_list' => 'Users List'
			];
			foreach ($permission_fields as $key => $label) {
				if ($r[$key] == 1) {
					$count++;
					$permissions .= $key . ', ';
				}
			}

			if ($count > 0) {
				$permissions = substr($permissions, 0, -2);
				$_SESSION['menu_permission_variables'] = $permissions;
			}
		}
	}
}

function activity_log($conn, $mobile_no, $mail, $user_id)
{
	include('../account/code/client-login-details.php');

	$ip_address = $_SERVER['REMOTE_ADDR'];
	if ($ip_address != "::1") {
		$device_info = $_SERVER['HTTP_USER_AGENT'];
		$stmt = mysqli_prepare($conn, "INSERT INTO `user_login_activity` (`user_id`, `mobile`, `email`, `activity`, `ip_address`, `country`, `subdivision`, `city`, `isp_name`, `device_details`, `date_time`) VALUES (?, ?, ?, 'LOGIN', ?, ?, ?, ?, ?, ?, ?)");
		mysqli_stmt_bind_param($stmt, "ssssssssss", $user_id, $mobile_no, $mail, $ip_address, $country, $subdivision, $city, $org, $device_info, $date);
		mysqli_stmt_execute($stmt);
		mysqli_stmt_close($stmt);
	}
}
?>
