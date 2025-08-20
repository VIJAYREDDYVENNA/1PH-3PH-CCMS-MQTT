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
$user_login_id = $sessionVars['user_login_id'];

// Initialize variables
$return_response = "";
$user_devices = "";

//$group_id = "ALL";


// Handle POST request
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate and sanitize input
	$group_id = filter_input(INPUT_POST, 'GROUP_ID', FILTER_SANITIZE_STRING);
	include_once(BASE_PATH_1."common-files/selecting_group_device.php");

	if($user_devices!="")
	{
		$user_devices= substr($user_devices, 0, -1);
	}

	$user_devices_array = explode(',', $user_devices);
	$user_devices_array = array_map(function($item) {
		return trim(trim($item, "'"));
	}, $user_devices_array);

    // Ensure $user_devices_array is non-empty and contains only valid device IDs
	$user_devices_array = array_filter($user_devices_array);

    // Create placeholders for device IDs in SQL query
	$placeholders = implode(',', array_fill(0, count($user_devices_array), '?'));

    // Connect to the database using procedural mysqli
	$conn_db_all = mysqli_connect(HOST, USERNAME, PASSWORD, DB_ALL);
	if (!$conn_db_all) {
		die("Connection failed: " . mysqli_connect_error());
	} else {

		$limit=200;
		$offset=0;

		if(isset($_POST['FETCH_MORE'])&&$_POST['FETCH_MORE']==="MORE")
		{
			if($_SESSION['FETCH_DEVICES_LIST']==0)
			{
				echo json_encode("");
				exit();
			}
			$page= $_SESSION['FETCH_DEVICES_LIST'];

			$page = $page ? intval($page) : 1;
			$limit = $limit ? intval($limit) : 1;
			$offset = ($page - 1) * $limit;

			$_SESSION['FETCH_DEVICES_LIST']=$_SESSION['FETCH_DEVICES_LIST']+1;
		}
		else
		{
			$_SESSION['FETCH_DEVICES_LIST']=2;

		}


        // Prepare SQL statement based on the device status
		$sql = "SELECT device_id, date_time, on_off_status, operation_mode  FROM live_data_updates  WHERE device_id IN ($placeholders) ORDER BY LEFT(device_id, LENGTH(device_id) - LENGTH(SUBSTRING_INDEX(device_id, '_', -1))), CAST(SUBSTRING_INDEX(device_id, '_', -1) AS UNSIGNED) LIMIT $limit OFFSET $offset";
        // Prepare SQL statement based on the device status
		//$sql = "SELECT device_id, date_time, on_off_status, operation_mode  FROM live_data_updates  WHERE device_id IN ($placeholders) LIMIT $limit OFFSET $offset";
		

        // Use prepared statement to execute the query
		$stmt = mysqli_prepare($conn_db_all, $sql);
		if ($stmt) {
            $types = str_repeat('s', count($user_devices_array)); // 's' for string type
            mysqli_stmt_bind_param($stmt, $types, ...$user_devices_array);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);

            // Check if there are rows returned
            if (mysqli_num_rows($result) > 0) {
            	while ($r = mysqli_fetch_assoc($result)) {
                    // Process each row
            		$operation_mode = $r['operation_mode'];
            		$date = date("H:i:s d-m-Y", strtotime($r['date_time']));
            		$device_id = $r['device_id'];
            		

            		$on_off_status = $r['on_off_status'];
            		switch ($on_off_status) {
            			case "1":
            			$on_off_status = "<span class='text-success fw-bold'>Auto ON</span>";
            			break;
            			case "3":
            			$on_off_status = "<span class='text-success fw-bold'>Server ON</span>";
            			break;
            			case "4":
            			$on_off_status = "<span class='text-success fw-bold'>WiFi ON</span>";
            			break;
            			case "5":
            			$on_off_status = "<span class='text-info-emphasis fw-bold'>Manual ON</span>";
            			break;
            			case "6":
            			$on_off_status = "<span class='text-danger fw-bold'>SERVER OFF</span>";
            			break;
            			case "7":
            			$on_off_status = "<span class='text-danger fw-bold'>WiFi OFF</span>";
            			break;
            			case "0":
            			default:
            			$on_off_status = "<span class='text-danger fw-bold'>OFF</span>";
            			break;
            		}

                    // Placeholder for $device_list (assuming it's properly populated elsewhere)
            		$name = $device_id;
            		/*foreach ($device_list as $device) {

            			$c_id =  $device['D_ID'];
            			if(trim($device_id)===$c_id)
            			{						
            				$name= $device['D_NAME'];						
            			}
            		}
*/


            		$device_ids = array_column($device_list, 'D_ID');
            		$index = array_search($device_id, $device_ids);

            		if ($index !== false) {
            			$name = $device_list[$index]['D_NAME'];
            			//$name = $device_list[$index]['D_ID'];

            		}

                    // Output HTML table rows based on installation status and device_status
            		

            		$return_response.= '<tr>
            		<td>'.$device_id.'</td>
            		<td>'.$name.'</td>
            		<td >'.$operation_mode.'</td>
            		<td>'.$on_off_status.'</td> 
            		<td>'.$date.'</td> 
            		</tr>';
            		
            	}
            } else {
            	$_SESSION['FETCH_DEVICES_LIST']=0;
                // No devices found
            	$return_response .= '<tr><td colspan="6" class="text-danger">Devices Not Found</td></tr>';
            }

            // Close statement
            mysqli_stmt_close($stmt);
        } else {
            // SQL preparation failed
        	$return_response .= '<tr><td colspan="6" class="text-danger">Failed to prepare SQL statement</td></tr>';
        }

        // Close database connection
        mysqli_close($conn_db_all);
    }
} else {
    // Handle case where request method is not POST
	$return_response .= '<tr><td colspan="6" class="text-danger">Input Data Not Valid</td></tr>';
}

// Output JSON response
echo json_encode($return_response);
?>
