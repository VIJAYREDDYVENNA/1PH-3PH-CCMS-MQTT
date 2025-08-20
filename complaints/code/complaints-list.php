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
$count = 0;
$device_list = json_decode($_SESSION["DEVICES_LIST"]);


$send = array();
$send = "";
$user_devices = "";

// Check and sanitize POST variables
$selected_device_id = isset($_POST['ID']) ? filter_input(INPUT_POST, 'ID', FILTER_SANITIZE_STRING) : '';
$selection = isset($_POST['SELECTION']) ? (int)$_POST['SELECTION'] : 0;
$group = isset($_POST['GROUP']) ? filter_input(INPUT_POST, 'GROUP', FILTER_SANITIZE_STRING) : '';
$complaint_status = isset($_POST['COMPLAINT_STATUS']) ? (int)$_POST['COMPLAINT_STATUS'] : 0;

$limit=100;
$offset=0;

if(isset($_POST['FETCH_MORE'])&&$_POST['FETCH_MORE']==="MORE")
{
	if($_SESSION['FETCH_COUNT']==0)
	{
		exit();
	}
	$page= $_SESSION['FETCH_COUNT'];
	
	$page = $page ? intval($page) : 1;
	$limit = $limit ? intval($limit) : 1;
	$offset = ($page - 1) * $limit;

	$_SESSION['FETCH_COUNT']=$_SESSION['FETCH_COUNT']+1;
}
else
{
	$_SESSION['FETCH_COUNT']=2;

}



// Handle complaint status
switch ($complaint_status) {
	case 1:
	$complaint_status = "";
	break;
	case 2:
	$complaint_status = "status='OPEN' AND";
	break;
	case 3:
	$complaint_status = "status='PROGRESS' AND";
	break;
	case 4:
	$complaint_status = "status='CLOSED' AND";
	break;
	default:
	?>
	<tr><td class="text-danger" colspan="6">No records found</td></tr>
	<?php
	exit();
}

// Build user devices list from session
foreach ($device_list as $value) {
	$id = $value->D_ID;
	$user_devices .= "'" . $id . "',";
}
if ($user_devices !== "") {
    $user_devices = substr($user_devices, 0, -1); // Remove the trailing comma
}

$conn = mysqli_connect(HOST, USERNAME, PASSWORD, DB_ALL);

if (!$conn) {
	die("Connection failed: " . mysqli_connect_error());
} else {

    // Define the SQL query based on the selection
	if ($selection == 1) {
        // By group devices complaints
		$sql = "SELECT * FROM complaints WHERE $complaint_status device_id IN ($user_devices) ORDER BY LENGTH(device_id), device_id, registered_on DESC LIMIT $limit OFFSET $offset";
	} else if ($selection == 2) {
        // By device complaints
		$sql = "SELECT * FROM complaints WHERE $complaint_status device_id = ? ORDER BY id DESC LIMIT $limit OFFSET $offset";
	}

    // Prepared statement to prevent SQL injection for device-specific selection
	if ($selection == 2) {
		$stmt = mysqli_prepare($conn, $sql);
		mysqli_stmt_bind_param($stmt, "s", $selected_device_id);
	}

	if (($selection == 1 && $result = mysqli_query($conn, $sql)) || ($selection == 2 && mysqli_stmt_execute($stmt))) {

		if ($selection == 2) {
			$result = mysqli_stmt_get_result($stmt);
		}

		if (mysqli_num_rows($result) > 0) {
			while ($r = mysqli_fetch_assoc($result)) {
				$d_name = "";
				if ($selection == 1) {
					$device_ids = array_column($device_list, 'D_ID');
					$index = array_search($r['device_id'], $device_ids);
					$d_name = $r['device_id'];
					if ($index !== false) {
						$d_name = "(" . htmlspecialchars($device_list[$index]->D_NAME) . ")";
					}
				}
				$r['registered_on'] = date("H:i:s d-m-Y", strtotime($r['registered_on']));

				$status = htmlspecialchars($r['status']);
				$sts_1 = htmlspecialchars($r['complaint_no']);
				$sts_2 = htmlspecialchars($r['registered_on']);
				$sts_3 = htmlspecialchars($r['device_id']) . $d_name;
				$sts_4 = htmlspecialchars($r['complaint']);

                // Status badge
				if ($status == "OPEN") {
					$sts_5 = "<label class='badge text-bg-danger'>Pending</label>";
				} else if ($status == "PROGRESS") {
					$sts_5 = "<label class='badge text-bg-warning'>In Progress</label>";
				} else if ($status == "CLOSED") {
					$sts_5 = "<label class='badge text-bg-success'>Resolved</label>";
				}

                // Check track button
				$check_status = '<button class="btn btn-sm btn-primary p-0 px-2" onclick=check_track("' . $sts_3 . '","' . $sts_1 . '")>Check</button>';
				?>
				<tr>
					<td class="body-cell col2"><?php echo $sts_1; ?></td>
					<td class="body-cell col2"><?php echo $sts_2; ?></td>
					<td class="body-cell col2"><?php echo $sts_3; ?></td>
					<td class="body-cell col2 text-left" colspan="2"><?php echo $sts_4; ?></td>
					<td class="body-cell col1"><?php echo $sts_5; ?></td>
					<td class="body-cell col1"><?php echo $check_status; ?></td>
				</tr>
				<?php
			}
		} else {
			?>
			<tr><td class="text-danger" colspan="7">No records found</td></tr>
			<?php
			$_SESSION['FETCH_COUNT']=0;
		}
	} else {
		?>
		<tr><td class="text-danger" colspan="7">Something went wrong</td></tr>
		<?php
	}

	if ($selection == 2) {
		mysqli_stmt_close($stmt);
	}
	mysqli_close($conn);
}

//}
?>
