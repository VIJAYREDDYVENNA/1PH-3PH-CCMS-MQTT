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

//////////////////////////////////////////////////////////

if ($_SERVER["REQUEST_METHOD"] == "POST" )
{
	$complaint_id = isset($_POST['ID']) ? $_POST['ID'] : $_SESSION['complaint_id'];      // Sanitize ID input
	$send = array();
	$send = "";

	$sts_1 = "";
	$sts_2 = "";
	$sts_3 = "";

	$conn = mysqli_connect(HOST, USERNAME, PASSWORD, DB_ALL);
	if (!$conn) {
		die("Connection failed: " . mysqli_connect_error());
	} else {

    	// Sanitize complaint_id for SQL
		$complaint_id = sanitize_input($complaint_id, $conn);

		$limit=100;
		$offset=0;

		if(isset($_POST['FETCH_MORE'])&&$_POST['FETCH_MORE']==="MORE")
		{
			if($_SESSION['FETCH_TRACK']==0||$_SESSION['complaint_id']=="")
			{
				exit();
			}
			$page= $_SESSION['FETCH_TRACK'];

			$page = $page ? intval($page) : 1;
			$limit = $limit ? intval($limit) : 1;
			$offset = ($page - 1) * $limit;

			$_SESSION['FETCH_TRACK']=$_SESSION['FETCH_TRACK']+1;
		}
		else
		{
			$_SESSION['FETCH_TRACK']=2;
			$_SESSION['complaint_id']=$complaint_id;

		}

		$sql = "SELECT * FROM `complaints_history` WHERE complaint_no = ? ORDER BY id DESC LIMIT $limit OFFSET $offset";
		$stmt = mysqli_prepare($conn, $sql);
		mysqli_stmt_bind_param($stmt, "s", $complaint_id);

		if (mysqli_stmt_execute($stmt)) {
			$result = mysqli_stmt_get_result($stmt);

			if (mysqli_num_rows($result) > 0) {
				while ($r = mysqli_fetch_assoc($result)) {
					$r['updated_time'] = date("H:i:s d-m-Y", strtotime($r['updated_time']));

					$sts_1 = htmlspecialchars($r['complaint_update']);  
					$sts_2 = htmlspecialchars($r['updated_time']);      
					$sts_3 = htmlspecialchars($r['updated_by']);        

					?>
					<tr>
						<td class="body-cell col2"><?php echo $sts_1; ?></td>
						<td class="body-cell col2"><?php echo $sts_3; ?></td>
						<td class="body-cell col2"><?php echo $sts_2; ?></td>
					</tr>
					<?php
				}
			} else {
				$_SESSION['FETCH_TRACK']=0;
				?>
				<tr>
					<td class="body-cell col1 text-left text-danger" colspan="5">Records are not Found</td>
				</tr>
				<?php
			}
		} else {
			$_SESSION['FETCH_TRACK']=0;
			?>
			<tr>
				<td class="body-cell col1 text-left text-danger" colspan="5">Records are not Found</td>
			</tr>
			<?php
		}

		mysqli_stmt_close($stmt);
		mysqli_close($conn);
	}
}

// Function to sanitize input data
function sanitize_input($data, $conn) {
	$data = trim($data);
	$data = stripslashes($data);
	$data = htmlspecialchars($data);
	return mysqli_real_escape_string($conn, $data);
}

?>
