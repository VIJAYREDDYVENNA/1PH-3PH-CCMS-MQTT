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

/*$group_id = "ALL";
$device_status = "FAULTY_DEVICES";*/

// Handle POST request
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate and sanitize input
	$group_id = filter_input(INPUT_POST, 'GROUP_ID', FILTER_SANITIZE_STRING);
	$device_status = filter_input(INPUT_POST, 'STATUS', FILTER_SANITIZE_STRING);

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
        // Prepare SQL statement based on the device status
		$sql = "";
		if ($device_status == "ACTIVE_DEVICES") {
			$sql = "SELECT active_device, device_id, date_time, ping_time, installed_date, installed_status, location FROM live_data_updates 
			WHERE active_device='1' AND installed_status = '1'AND device_id IN ($placeholders) ORDER BY LEFT(device_id, LENGTH(device_id) - LENGTH(SUBSTRING_INDEX(device_id, '_', -1))), CAST(SUBSTRING_INDEX(device_id, '_', -1) AS UNSIGNED)";
		} 
		elseif ($device_status == "POOR_NW_DEVICES") {
			$sql = "SELECT poor_network, device_id, date_time, ping_time, installed_date, installed_status, location FROM live_data_updates 
			WHERE poor_network='1' AND installed_status = '1'AND device_id IN ($placeholders) ORDER BY LEFT(device_id, LENGTH(device_id) - LENGTH(SUBSTRING_INDEX(device_id, '_', -1))), CAST(SUBSTRING_INDEX(device_id, '_', -1) AS UNSIGNED)";
		} 
		elseif ($device_status == "POWER_FAIL_DEVICES") {
			$sql = "SELECT power_failure, device_id, date_time, ping_time, installed_date, installed_status, location FROM live_data_updates 
			WHERE power_failure='1' AND installed_status = '1'AND device_id IN ($placeholders) ORDER BY LEFT(device_id, LENGTH(device_id) - LENGTH(SUBSTRING_INDEX(device_id, '_', -1))), CAST(SUBSTRING_INDEX(device_id, '_', -1) AS UNSIGNED)";
		} 
		elseif ($device_status == "FAULTY_DEVICES") {
			$sql = "SELECT faulty, device_id, date_time, ping_time, installed_date, installed_status, location FROM live_data_updates 
			WHERE faulty='1' AND installed_status = '1'AND device_id IN ($placeholders) ORDER BY LEFT(device_id, LENGTH(device_id) - LENGTH(SUBSTRING_INDEX(device_id, '_', -1))), CAST(SUBSTRING_INDEX(device_id, '_', -1) AS UNSIGNED)";
		} 

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
            		$installation_date = $r['installed_date'];
            		$frame_date_time = date("H:i:s d-m-Y", strtotime($r['date_time']));
            		$ping_date_time = date("H:i:s d-m-Y", strtotime($r['ping_time']));
            		$device_id = $r['device_id'];
            		

                    // Handle location and address
            		if ($r['location'] != '0,0' && strpos($r['location'], "0000000,000000") === false) {
            			$address = '<a href="#" class="pt-0 pb-0" onclick="show_location(\'' . $r['location'] . '\')">Map</a>';
            		} else {
            			$address = '<button class="address_update btn btn-primary pt-0 pb-0" onclick="address_update(\'' . $device_id . '\')">Update</button>';
            		}

                    // Placeholder for $device_list (assuming it's properly populated elsewhere)
            		$name = $device_id;
            		foreach ($device_list as $device) {

            			$c_id =  $device['D_ID'];
            			if(trim($device_id)===$c_id)
            			{						
            				$name= $device['D_NAME'];						
            			}
            		}

                    // Output HTML table rows based on installation status and device_status
            		if ($device_status == "ACTIVE_DEVICES") {
            			
            			$return_response.= '<tr>
            			<td>'.$device_id.'</td>
            			<td>'.$name.'</td>
            			<td class="col-size-1">'.$frame_date_time .'</td>
            			<td class="col-size-1"> '.$ping_date_time.'</td> 
            			<td><button class="btn fw-semibold  text-success bg-success-subtle btn-sm p-0 px-2 " onclick=openOpenviewModal("'.$device_id.'") >View</button></td>
            			<td>'.$address.'</td> 
            			</tr>';
            			
            		} 
            		else if ($device_status == "POOR_NW_DEVICES") {
            			
            			$return_response.= '<tr>
            			<td>'.$device_id.'</td>
            			<td>'.$name.'</td>
            			<td class="col-size-1">'.$frame_date_time .'</td>
            			<td class="col-size-1"> '.$ping_date_time.'</td> 
            			<td><button class="btn fw-semibold btn-warning btn-sm p-0 px-2 " onclick=openOpenviewModal("'.$device_id.'") >View</button></td>
            			<td>'.$address.'</td> 
            			</tr>';
            			
            		} 
            		else if ($device_status == "FAULTY_DEVICES") {
            			
            			$return_response.= '<tr>
            			<td>'.$device_id.'</td>
            			<td>'.$name.'</td>
            			<td class="col-size-1">'.$frame_date_time .'</td>
            			<td class="col-size-1"> '.$ping_date_time.'</td> 
            			<td><button class="btn fw-semibold text-danger bg-danger-subtle text-success bg-success-subtle btn-sm p-0 px-2 " onclick=openOpenviewModal("'.$device_id.'") >View</button></td>
            			<td>'.$address.'</td> 
            			</tr>';
            			
            		} 
            		else if ($device_status == "POWER_FAIL_DEVICES") {
            			
            			$return_response.= '<tr>
            			<td>'.$device_id.'</td>
            			<td>'.$name.'</td>
            			<td class="col-size-1">'.$frame_date_time .'</td>
            			<td class="col-size-1"> '.$ping_date_time.'</td> 
            			<td><button class="btn fw-semibold  bg-secondary-subtle btn-sm p-0 px-2 " onclick=openOpenviewModal("'.$device_id.'") >View</button></td>
            			<td>'.$address.'</td> 
            			</tr>';
            			
            		} 
            	}
            } else {
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
