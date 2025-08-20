
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

//$device_ids = "SPMS_1";

$data = "<thead class='sticky-header text-center'>
<tr class='header-row-1'>                                    
<th class='table-header-row-1'>Status</th>                                
<th class='table-header-row-1'>Code</th>                                
<th class='table-header-row-1'>Date Time</th>
</tr>
</thead><tbody>";

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['D_ID'])) {
	$device_ids = filter_input(INPUT_POST, 'D_ID', FILTER_SANITIZE_STRING);
	$db = strtolower($device_ids);
	$conn = mysqli_connect(HOST, USERNAME, PASSWORD);
	if (!$conn) {
		die("Connection failed: " . mysqli_connect_error());
	} else {
		$device_ids = sanitize_input($device_ids, $conn);
		$sql = "SELECT * FROM `$db`.`software_update_status` ORDER BY id DESC LIMIT 50";
		$stmt = mysqli_prepare($conn, $sql);

		if ($stmt && mysqli_stmt_execute($stmt)) {
			$result = mysqli_stmt_get_result($stmt);
			if (mysqli_num_rows($result) > 0) {
				while ($r = mysqli_fetch_assoc($result)) {
					$data .= "<tr>
					<td>{$r['status']}</td>
					<td>{$r['status_code']}</td>
					<td>{$r['date_time']}</td>
					</tr>";
				}
			} else {
				$data .= '<tr><td class="text-danger" colspan="3">No records found</td></tr>';
			}
			mysqli_stmt_close($stmt);
		} else {
			$data .= '<tr><td class="text-danger" colspan="3">Query execution failed</td></tr>';
		}

		$data .= "</tbody>";
		mysqli_close($conn);
	}

	echo $data;
}

function sanitize_input($data, $conn) {
	$data = trim($data);
	$data = stripslashes($data);
	$data = htmlspecialchars($data);
	return mysqli_real_escape_string($conn, $data);
}
?>



