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
$add_response="";
$group_id = "ALL";
$device_status = "ALL";

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

		/////////////////////////////////////////////////////////////////
		if ($device_status != "INSTALLED") {
			$not_available_list = str_replace("'", "", $user_devices);
			$not_available_list_array = explode(",", $not_available_list);

			$sql = "SELECT * FROM live_data_updates WHERE device_id IN ($user_devices)  ORDER BY LEFT(device_id, LENGTH(device_id) - LENGTH(SUBSTRING_INDEX(device_id, '_', -1))), CAST(SUBSTRING_INDEX(device_id, '_', -1) AS UNSIGNED)";
			$available_devices = [];
			if ($result = mysqli_query($conn_db_all, $sql)) {
				if (mysqli_num_rows($result) > 0) {
					while ($row = mysqli_fetch_assoc($result)) {
						$available_devices[] = $row['device_id'];
					}
				}
			}
			$not_available_devices = array_diff($not_available_list_array, $available_devices);

			$not_available_devices_list= explode(',', implode(',', $not_available_devices));



			for($i=0; $i<count($not_available_devices_list);$i++)
			{
				$device_id=$not_available_devices_list[$i];
				if($device_id!="")
				{

					$name = $device_id;

					foreach ($device_list as $device) {

						$c_id =  $device['D_ID'];
						if(trim($device_id)===$c_id)
						{						
							$name= $device['D_NAME'];						
						}
					}

					if ($device_status == "ALL") {

						$add_response.= '<tr>
						<td ><input type="checkbox" name="selectedDevice" value="'.$device_id.'"></td>
						<td>'.$device_id.'</td>
						<td>'.$name.'</td>
						<td class="text-danger fw-semibold">Not Installed.</td>
						<td>---</td> 
						<td>--</td> 
						</tr>';
					}
					else
					{
						$add_response.= '<tr>
						<td ><input type="checkbox" name="selectedDevice" value="'.$device_id.'"></td>
						<td>'.$device_id.'</td>
						<td>'.$name.'</td>
						</tr>';
					}
				}
			}


		}


       ////////////////////////////////////////////////////////////////// 
		$sql = "";
		if ($device_status == "ALL") {
			$sql = "SELECT active_device, device_id, date_time, installed_date, installed_status, location 
			FROM live_data_updates  WHERE device_id IN ($placeholders)  ORDER BY LEFT(device_id, LENGTH(device_id) - LENGTH(SUBSTRING_INDEX(device_id, '_', -1))), CAST(SUBSTRING_INDEX(device_id, '_', -1) AS UNSIGNED)";

		} elseif ($device_status == "INSTALLED") {
			$sql = "SELECT active_device, device_id, date_time, installed_date, installed_status, location 
			FROM live_data_updates  WHERE installed_status = '1' AND device_id IN ($placeholders)  ORDER BY LEFT(device_id, LENGTH(device_id) - LENGTH(SUBSTRING_INDEX(device_id, '_', -1))), CAST(SUBSTRING_INDEX(device_id, '_', -1) AS UNSIGNED)";
		} else {
			$sql = "SELECT active_device, device_id, date_time, installed_date, installed_status, location 
			FROM live_data_updates  WHERE installed_status = '0' AND device_id IN ($placeholders)  ORDER BY LEFT(device_id, LENGTH(device_id) - LENGTH(SUBSTRING_INDEX(device_id, '_', -1))), CAST(SUBSTRING_INDEX(device_id, '_', -1) AS UNSIGNED)";
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
            		$date = date("H:i:s d-m-Y", strtotime($r['date_time']));
            		$device_id = $r['device_id'];
            		$status = $r['active_device'];

                    // Handle location and address
            		if ($r['location'] != '0,0' && strpos($r['location'], "0000000,000000") === false) {
            			$address = '<a href="#" class="pt-0 pb-0" onclick="show_location(\'' . $r['location'] . '\')">Map</a>';
            		} else {

            			$address='<a href="location-details.php?id='.$device_id.'"  target="blank"><button class="btn btn-primary pt-0 pb-0" >Update</button></a>';
            			//$address = '<button class="address_update btn btn-primary pt-0 pb-0" onclick="address_update(\'' . $device_id . '\')">Update</button>';
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
            		if ($r['installed_status'] == 1) {
            			$installation_status="Installed";
            			if($device_status =="INSTALLED")
            			{
            				$return_response.= '<tr>
            				<td ><input type="checkbox" name="selectedDevice" value="'.$device_id.'"></td>
            				<td>'.$device_id.'</td>
            				<td>'.$name.'</td>
            				<td class="text-success fw-semibold">'.$installation_status.'</td>
            				<td>'.$installation_date.'</td> 
            				<td>'.$address.'</td> 
            				</tr>';
            			}
            			else{

            				$return_response.= '<tr>
            				<td ><input type="checkbox" name="selectedDevice" value="'.$device_id.'"></td>
            				<td>'.$device_id.'</td>
            				<td>'.$name.'</td>
            				<td class="text-success fw-semibold">'.$installation_status.'</td>
            				<td>'.$installation_date.'</td> 
            				<td>'.$address.'</td> 
            				</tr>';
            			}
            		} else {
            			if($device_status =="NOTINSTALLED")
            			{
            				$installation_status="Not Installed";
            				$return_response.= '<tr>
            				<td ><input type="checkbox" name="selectedDevice" value="'.$device_id.'"></td>
            				<td>'.$device_id.'</td>
            				<td>'.$name.'</td>							
            				</tr>';
            			}else{
            				$installation_status="Not Installed";
            				$return_response.= '<tr>
            				<td ><input type="checkbox" name="selectedDevice" value="'.$device_id.'"></td>
            				<td>'.$device_id.'</td>
            				<td>'.$name.'</td>
            				<td class="text-danger fw-semibold">'.$installation_status.'</td>
            				<td>'.$installation_date.'</td> 
            				<td>--</td> 
            				</tr>';
            			}
            		}
            	}
            	$return_response=$return_response.$add_response;
            }
            else {
            	if($add_response!="")
            	{
            		$return_response=$add_response;
            	}
            	else{


            		$return_response .= '<tr><td colspan="6" class="text-danger">Devices Not Found</td></tr>';
            	}
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
