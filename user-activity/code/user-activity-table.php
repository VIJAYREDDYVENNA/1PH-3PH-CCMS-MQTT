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

$normal='class=""';
$red='class="text-danger-emphasis fw-bold"'; 
$orange='class="text-warning-emphasis fw-bold"'; 
$green='class="text-success-emphasis fw-bold"';  
$primary='class="text-info-emphasis fw-bold"'; 
$class_r=$normal;
$class_y=$green;
$class_b=$green;

$d_name = "";
$data = "";
/*$device_ids="CCMS_1";
$records="LATEST";
$alert="VOLTAGE";*/


if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['D_ID']) && isset($_POST['RECORDS'])) {
	$device_ids = filter_input(INPUT_POST, 'D_ID', FILTER_SANITIZE_STRING);
	$records = filter_input(INPUT_POST, 'RECORDS', FILTER_SANITIZE_STRING);
	$alert = filter_input(INPUT_POST, 'ALERT', FILTER_SANITIZE_STRING);

	$db = strtolower($device_ids);
	$send = array();
	$send = "";
	$d_name = "";

	$start_date="";
	$end_date ="";

    $conn = mysqli_connect(HOST, USERNAME, PASSWORD); // Ensure DATABASE constant is defined and holds the name of the database
    if (!$conn) {
    	die("Connection failed: " . mysqli_connect_error());
    } else {


    	$device_ids = sanitize_input($device_ids, $conn);
    	$records = sanitize_input($records, $conn);
    	$alert = sanitize_input($alert, $conn);
    	
    	$sql = "";
    	$stmt ="";


    	if ($records === "DATE-RANGE")
    	{
    		if (isset($_POST['START_DATE'])&&isset($_POST['END_DATE'])) {
    			$start_date = trim(filter_input(INPUT_POST, 'START_DATE', FILTER_SANITIZE_STRING));
    			$end_date = trim(filter_input(INPUT_POST, 'END_DATE', FILTER_SANITIZE_STRING));

    			$start_date = sanitize_input($start_date, $conn);
    			$end_date = sanitize_input($end_date, $conn);

    		}
    	}

    	if ($alert == "ALL") 
    	{
    		if ($records === "LATEST") {
    			$sql = "SELECT * FROM `$db`.`user_activity_log` ORDER BY id DESC LIMIT 50";
    			$stmt = mysqli_prepare($conn, $sql);    			
    		} else {
    			$sql = "SELECT * FROM `$db`.`user_activity_log` WHERE DATE(date_time) BETWEEN ? AND ? ORDER BY id DESC";
    			
    			$stmt = mysqli_prepare($conn, $sql);
    			mysqli_stmt_bind_param($stmt, 'ss', $start_date, $end_date);
    		}

    		$data.="<thead class='sticky-header text-center'>
    		<tr class='header-row-1'>                                    
    		<th class='table-header-row-1'>Acivity</th>                                
    		<th class='table-header-row-1'>Date&Time</th>                                
    		<th class='table-header-row-1'>User Name</th>
    		<th class='table-header-row-1'>User Role</th>
    		<th class='table-header-row-1'>User Mobile/e-mail</th>
    		</tr></thead><tbody>";
    		

    		if ($sql !== "") {
    			if (isset($stmt) && mysqli_stmt_execute($stmt)) 
    			{
    				$result = mysqli_stmt_get_result($stmt);
    				if (mysqli_num_rows($result) > 0) {
    					while ($r = mysqli_fetch_assoc($result)) { 

    						
    						$data.= "<tr >    						
    						<td > ".$r['updated_field']."</td>
    						<td > ".$r['date_time']."</td>    						
    						<td > ".$r['name']."</td>
    						<td > ".$r['role']."</td>
    						<td > ".$r['user_mobile']."/".$r['email']."</td></tr>";
    					}
    				} else {
    					$data.= '<tr><td class="text-danger" colspan="5">Records are not Found</td></tr>';
    				}
    				mysqli_stmt_close($stmt);
    			} else {
    				$data.= '<tr><td class="text-danger" colspan="5">Records are not Found</td></tr>';
    			}
    		} else {
    			$data.= '<tr><td class="text-danger" colspan="5">Records are not Found</td></tr>';
    		}

    		$data.="</tbody>";
    	}

    	if ($alert == "ON_OFF") 
    	{
    		if ($records === "LATEST") {
    			$sql = "SELECT * FROM `$db`.`on_off_activities` ORDER BY id DESC LIMIT 50";
    			$stmt = mysqli_prepare($conn, $sql);    			
    		} else {
    			$sql = "SELECT * FROM `$db`.`on_off_activities` WHERE DATE(date_time) BETWEEN ? AND ? ORDER BY id DESC";
    			
    			$stmt = mysqli_prepare($conn, $sql);
    			mysqli_stmt_bind_param($stmt, 'ss', $start_date, $end_date);
    		}

    		$data.="<thead class='sticky-header text-center'>
    		<tr class='header-row-1'>                                    
    		<th class='table-header-row-1'>Acivity</th>                                
    		<th class='table-header-row-1'>Time(Mins)</th>                                
    		<th class='table-header-row-1'>Status</th>                                
    		<th class='table-header-row-1'>Date&Time</th>                                
    		<th class='table-header-row-1'>User Name</th>
    		<th class='table-header-row-1'>User Role</th>
    		<th class='table-header-row-1'>User Mobile/e-mail</th>
    		</tr></thead><tbody>";
    		if ($sql !== "") {
    			if (isset($stmt) && mysqli_stmt_execute($stmt)) 
    			{
    				$result = mysqli_stmt_get_result($stmt);
    				if (mysqli_num_rows($result) > 0) {
    					while ($r = mysqli_fetch_assoc($result)) { 

    						if($r['on_off']==="ON")
    						{
    							$class_r=$green;
    							if($r['time']==0)
    							{
    								$r['time']="--";
    							}
    						}
    						else if($r['on_off']==="OFF")
    						{
    							$class_r=$red;
    							if($r['time']==0)
    							{
    								$r['time']="--";
    							}
    						}
    						$data.= "<tr >    						
    						<td $class_r> ".$r['on_off']."</td>
    						<td > ".$r['time']."</td>    						
    						<td > ".$r['status']."</td>    						
    						<td > ".$r['date_time']."</td>    						
    						<td > ".$r['name']."</td>
    						<td > ".$r['role']."</td>
    						<td > ".$r['user_mobile']."/".$r['email']."</td></tr>";
    					}
    				} else {
    					$data.= '<tr><td class="text-danger" colspan="7">Records are not Found</td></tr>';
    				}
    				mysqli_stmt_close($stmt);
    			} else {
    				$data.= '<tr><td class="text-danger" colspan="7">Records are not Found</td></tr>';
    			}
    		} else {
    			$data.= '<tr><td class="text-danger" colspan="7">Records are not Found</td></tr>';
    		}

    		$data.="</tbody>";
    	}

    	if ($alert == "ON_OFF_MODES") 
    	{
    		if ($records === "LATEST") {
    			$sql = "SELECT * FROM `$db`.`on_off_modes` ORDER BY id DESC LIMIT 50";
    			$stmt = mysqli_prepare($conn, $sql);    			
    		} else {
    			$sql = "SELECT * FROM `$db`.`on_off_modes` WHERE DATE(date_time) BETWEEN ? AND ? ORDER BY id DESC";
    			
    			$stmt = mysqli_prepare($conn, $sql);
    			mysqli_stmt_bind_param($stmt, 'ss', $start_date, $end_date);
    		}

    		$data.="<thead class='sticky-header text-center'>
    		<tr class='header-row-1'>                                    
    		<th class='table-header-row-1'>On-Off Mode</th>                                

    		<th class='table-header-row-1'>Status</th>                                
    		<th class='table-header-row-1'>Date&Time</th>                                
    		<th class='table-header-row-1'>User Name</th>
    		<th class='table-header-row-1'>User Role</th>
    		<th class='table-header-row-1'>User Mobile/e-mail</th>
    		</tr></thead><tbody>";
    		if ($sql !== "") {
    			if (isset($stmt) && mysqli_stmt_execute($stmt)) 
    			{
    				$result = mysqli_stmt_get_result($stmt);
    				if (mysqli_num_rows($result) > 0) {
    					while ($r = mysqli_fetch_assoc($result)) { 

    						if($r['status']==="In-Progress")
    						{
    							$class_r=$orange;
    							
    						}
    						else if($r['status']==="Pending")
    						{
    							$class_r=$red;
    							
    						}else if($r['status']==="Updated")
    						{
    							$class_r=$green;
    							
    						}
    						else if($r['status']==="Initiated")
    						{
    							$class_r=$primary;
    							
    						}



    						$data.= "<tr >    						
    						<td > ".$r['on_off_mode']."</td>

    						<td $class_r > ".$r['status']."</td>    						
    						<td > ".$r['date_time']."</td>    						
    						<td > ".$r['name']."</td>
    						<td > ".$r['role']."</td>
    						<td > ".$r['user_mobile']."/".$r['email']."</td></tr>";
    					}
    				} else {
    					$data.= '<tr><td class="text-danger" colspan="7">Records are not Found</td></tr>';
    				}
    				mysqli_stmt_close($stmt);
    			} else {
    				$data.= '<tr><td class="text-danger" colspan="7">Records are not Found</td></tr>';
    			}
    		} else {
    			$data.= '<tr><td class="text-danger" colspan="7">Records are not Found</td></tr>';
    		}

    		$data.="</tbody>";
    	}

    	if ($alert == "ON_OFF_SCHEDULE") 
    	{
    		if ($records === "LATEST") {
    			$sql = "SELECT * FROM `$db`.`on_off_schedule_time` ORDER BY id DESC LIMIT 50";
    			$stmt = mysqli_prepare($conn, $sql);    			
    		} else {
    			$sql = "SELECT * FROM `$db`.`on_off_schedule_time` WHERE DATE(date_time) BETWEEN ? AND ? ORDER BY id DESC";
    			
    			$stmt = mysqli_prepare($conn, $sql);
    			mysqli_stmt_bind_param($stmt, 'ss', $start_date, $end_date);
    		}

    		$data.="<thead class='sticky-header text-center'>
    		<tr class='header-row-1'>                                    
    		<th class='table-header-row-1'>On-Time</th>                                
    		<th class='table-header-row-1'>Off-Time</th>                                

    		<th class='table-header-row-1'>Status</th>                                
    		<th class='table-header-row-1'>Date&Time</th>                                
    		<th class='table-header-row-1'>User Name</th>
    		<th class='table-header-row-1'>User Role</th>
    		<th class='table-header-row-1'>User Mobile/e-mail</th>
    		</tr></thead><tbody>";
    		if ($sql !== "") {
    			if (isset($stmt) && mysqli_stmt_execute($stmt)) 
    			{
    				$result = mysqli_stmt_get_result($stmt);
    				if (mysqli_num_rows($result) > 0) {
    					while ($r = mysqli_fetch_assoc($result)) { 

    						if($r['status']==="In-Progress")
    						{
    							$class_r=$orange;
    							
    						}
    						else if($r['status']==="Pending" ||strtoupper($r['status'])==="DISABLED")
    						{
    							$class_r=$red;
    							
    						}else if($r['status']==="Updated"||strtoupper($r['status'])==="ENABLED")
    						{
    							$class_r=$green;
    							
    						}
    						else if($r['status']==="Initiated")
    						{
    							$class_r=$primary;
    							
    						}
    						$data.= "<tr >    						
    						<td > ".$r['on_time']."</td>
    						<td > ".$r['off_time']."</td>

    						<td $class_r > ".$r['status']."</td>    						
    						<td > ".$r['date_time']."</td>    						
    						<td > ".$r['name']."</td>
    						<td > ".$r['role']."</td>
    						<td > ".$r['user_mobile']."/".$r['email']."</td></tr>";
    					}
    				} else {
    					$data.= '<tr><td class="text-danger" colspan="7">Records are not Found</td></tr>';
    				}
    				mysqli_stmt_close($stmt);
    			} else {
    				$data.= '<tr><td class="text-danger" colspan="7">Records are not Found</td></tr>';
    			}
    		} else {
    			$data.= '<tr><td class="text-danger" colspan="7">Records are not Found</td></tr>';
    		}

    		$data.="</tbody>";
    	}

    	if ($alert == "VOLTAGE") 
    	{
    		if ($records === "LATEST") {
    			$sql = "SELECT * FROM `$db`.`limits_voltage` ORDER BY id DESC LIMIT 50";
    			$stmt = mysqli_prepare($conn, $sql);    			
    		} else {
    			$sql = "SELECT * FROM `$db`.`limits_voltage` WHERE DATE(date_time) BETWEEN ? AND ? ORDER BY id DESC";
    			
    			$stmt = mysqli_prepare($conn, $sql);
    			mysqli_stmt_bind_param($stmt, 'ss', $start_date, $end_date);
    		}

    		$slected_phase=get_phase(strtoupper($db));
    		if($slected_phase=="3PH")
    		{
    			$data.="<thead class='sticky-header text-center'>

    			<tr class='header-row-1'>                                    
    			<th class='table-header-row-1' colspan='3'>Lower Threshold Voltage(V)</th>                                
    			<th class='table-header-row-1' colspan='3'>Upper Threshold Voltage(V)</th>                                
    			<th class='table-header-row-1'>Data & Time</th>
    			<th class='table-header-row-1' colspan='3'>User Details</th>
    			</tr>
    			<tr class='table-header-row-2'>    
    			<th class='table-header-row-2'>R</th>
    			<th class='table-header-row-2'>Y</th>
    			<th class='table-header-row-2'>B</th>
    			<th class='table-header-row-2'>R</th>
    			<th class='table-header-row-2'>Y</th>
    			<th class='table-header-row-2'>B</th>
    			<th class='table-header-row-2'></th>
    			<th class='table-header-row-2'> Name</th>
    			<th class='table-header-row-2'> Role</th>            
    			<th class='table-header-row-2'> Mobile/e-mail</th>
    			</tr>
    			</thead><tbody>";
    		}
    		else if($slected_phase=="1PH")
    		{
    			$data.="<thead class='sticky-header text-center'>
    			<tr class='header-row-2'>                                    
    			<th class='table-header-row-1' >Lower Threshold</th>                                
    			<th class='table-header-row-1'>Upper Threshold</th>                                
    			<th class='table-header-row-1'>Data & Time</th>
    			<th class='table-header-row-1' colspan='3'>User Details</th>
    			</tr>
    			<tr class='table-header-row-2'>    
    			<th class='table-header-row-2'>Voltage(V)</th>

    			<th class='table-header-row-2'>Voltage(V)</th>

    			<th class='table-header-row-2'></th>
    			<th class='table-header-row-2'> Name</th>
    			<th class='table-header-row-2'> Role</th>            
    			<th class='table-header-row-2'> Mobile/e-mail</th>
    			</tr>
    			</thead><tbody>";
    		}



    		if ($sql !== "") {
    			if (isset($stmt) && mysqli_stmt_execute($stmt)) 
    			{
    				$result = mysqli_stmt_get_result($stmt);
    				if (mysqli_num_rows($result) > 0) {
    					while ($r = mysqli_fetch_assoc($result)) { 
    						if($slected_phase=="3PH")
    						{
    							$data.= "<tr >
    							<td> ".$r['l_r']."</td>
    							<td> ".$r['l_y']."</td>
    							<td> ".$r['l_b']."</td>
    							<td> ".$r['u_r']."</td>
    							<td> ".$r['u_y']."</td>
    							<td> ".$r['u_b']."</td>
    							<td> ".$r['date_time']."</td>
    							<td> ".$r['name']."</td>
    							<td> ".$r['role']."</td>
    							<td> ".$r['user_mobile']."/".$r['email']."</td></tr>";

    						}
    						else if($slected_phase=="1PH")
    						{

    							$data.= "<tr >
    							<td> ".$r['l_r']."</td>

    							<td> ".$r['u_r']."</td>

    							<td> ".$r['date_time']."</td>
    							<td> ".$r['name']."</td>
    							<td> ".$r['role']."</td>
    							<td> ".$r['user_mobile']."/".$r['email']."</td></tr>";
    						}

    					}

    				} else {
    					$data.= '<tr><td class="text-danger" colspan="15">Records are not Found</td></tr>';
    				}
    				mysqli_stmt_close($stmt);
    			} else {
    				$data.= '<tr><td class="text-danger" colspan="15">Records are not Found</td></tr>';
    			}
    		} else {
    			$data.= '<tr><td class="text-danger" colspan="15">Records are not Found</td></tr>';
    		}

    		$data.="</tbody>";

    	}

    	if ($alert == "CURRENT") 
    	{
    		if ($records === "LATEST") {
    			$sql = "SELECT * FROM `$db`.`limits_current` ORDER BY id DESC LIMIT 50";
    			$stmt = mysqli_prepare($conn, $sql);    			
    		} else {
    			$sql = "SELECT * FROM `$db`.`limits_current` WHERE DATE(date_time) BETWEEN ? AND ? ORDER BY id DESC";

    			$stmt = mysqli_prepare($conn, $sql);
    			mysqli_stmt_bind_param($stmt, 'ss', $start_date, $end_date);
    		}
    		$slected_phase=get_phase(strtoupper($db));
    		if($slected_phase=="3PH")
    		{
    			$data.="<thead class='sticky-header text-center'>
    			<tr class='header-row-1'>                                    
    			<th class='table-header-row-1' colspan='3'>Current Limits(Amps)</th>
    			<th class='table-header-row-1'>Data & Time</th>
    			<th class='table-header-row-1' colspan='3'>User Details</th>
    			</tr>
    			<tr class='header-row-2'>    
    			<th class='table-header-row-2'>R</th>
    			<th class='table-header-row-2'>Y</th>
    			<th class='table-header-row-2'>B</th>
    			<th class='table-header-row-2'></th>
    			<th class='table-header-row-2'> Name</th>
    			<th class='table-header-row-2'> Role</th>            
    			<th class='table-header-row-2'> Mobile/e-mail</th>
    			</tr>
    			</thead><tbody>";

    		}
    		else if($slected_phase=="1PH")
    		{
    			$data.="<thead class='sticky-header text-center'>
    			<tr class='header-row-1'>                                    
    			<th class='table-header-row-1'>Upper Threshold</th>
    			<th class='table-header-row-1'>Data & Time</th>
    			<th class='table-header-row-1' colspan='3'>User Details</th>
    			</tr>
    			<tr class='header-row-2'>    
    			<th class='table-header-row-2'>Current(Amps)</th>

    			<th class='table-header-row-2'></th>
    			<th class='table-header-row-2'> Name</th>
    			<th class='table-header-row-2'> Role</th>            
    			<th class='table-header-row-2'> Mobile/e-mail</th>
    			</tr>
    			</thead><tbody>";
    		}
    		if ($sql !== "") {
    			if (isset($stmt) && mysqli_stmt_execute($stmt)) 
    			{
    				$result = mysqli_stmt_get_result($stmt);
    				if (mysqli_num_rows($result) > 0) {
    					while ($r = mysqli_fetch_assoc($result)) { 
    						if($slected_phase=="3PH")
    						{
    							$data.= "<tr >
    							<td> ".$r['i_r']."</td>
    							<td> ".$r['i_y']."</td>
    							<td> ".$r['i_b']."</td>
    							<td> ".$r['date_time']."</td>
    							<td> ".$r['name']."</td>
    							<td> ".$r['role']."</td>
    							<td> ".$r['user_mobile']."/".$r['email']."</td></tr>";

    						}
    						else if($slected_phase=="1PH")
    						{
    							$data.= "<tr >
    							<td> ".$r['i_r']."</td>
    							
    							<td> ".$r['date_time']."</td>
    							<td> ".$r['name']."</td>
    							<td> ".$r['role']."</td>
    							<td> ".$r['user_mobile']."/".$r['email']."</td></tr>";
    						}
    					}
    				} else {
    					$data.= '<tr><td class="text-danger" colspan="7">Records are not Found</td></tr>';
    				}
    				mysqli_stmt_close($stmt);
    			} else {
    				$data.= '<tr><td class="text-danger" colspan="7">Records are not Found</td></tr>';
    			}
    		} else {
    			$data.= '<tr><td class="text-danger" colspan="7">Records are not Found</td></tr>';
    		}

    		$data.="</tbody>";

    	}
    	if ($alert == "UNIT-CAPACITY") 
    	{
    		if ($records === "LATEST") {
    			$sql = "SELECT * FROM `$db`.`unit_capacity` ORDER BY id DESC LIMIT 50";
    			$stmt = mysqli_prepare($conn, $sql);    			
    		} else {
    			$sql = "SELECT * FROM `$db`.`unit_capacity` WHERE DATE(date_time) BETWEEN ? AND ? ORDER BY id DESC";

    			$stmt = mysqli_prepare($conn, $sql);
    			mysqli_stmt_bind_param($stmt, 'ss', $start_date, $end_date);
    		}

    		$data.="<thead class='sticky-header text-center'>

    		<tr class='header-row-2'>    
    		<th class='table-header-row-1'>Unit Capacity</th>

    		<th class='table-header-row-1'>Date&Time</th>
    		<th class='table-header-row-1'> Name</th>
    		<th class='table-header-row-1'> Role</th>            
    		<th class='table-header-row-1'> Mobile/e-mail</th>
    		</tr>
    		</thead><tbody>";
    		if ($sql !== "") {
    			if (isset($stmt) && mysqli_stmt_execute($stmt)) 
    			{
    				$result = mysqli_stmt_get_result($stmt);
    				if (mysqli_num_rows($result) > 0) {
    					while ($r = mysqli_fetch_assoc($result)) { 

    						$data.= "<tr >
    						<td> ".$r['capacity']."</td>    						
    						<td> ".$r['date_time']."</td>
    						<td> ".$r['name']."</td>
    						<td> ".$r['role']."</td>
    						<td> ".$r['user_mobile']."/".$r['email']."</td></tr>";
    					}
    				} else {
    					$data.= '<tr><td class="text-danger" colspan="7">Records are not Found</td></tr>';
    				}
    				mysqli_stmt_close($stmt);
    			} else {
    				$data.= '<tr><td class="text-danger" colspan="7">Records are not Found</td></tr>';
    			}
    		} else {
    			$data.= '<tr><td class="text-danger" colspan="7">Records are not Found</td></tr>';
    		}

    		$data.="</tbody>";

    	}

    	if ($alert == "FRAME-TIME") 
    	{
    		if ($records === "LATEST") {
    			$sql = "SELECT * FROM `$db`.`frame_time` ORDER BY id DESC LIMIT 50";
    			$stmt = mysqli_prepare($conn, $sql);    			
    		} else {
    			$sql = "SELECT * FROM `$db`.`frame_time` WHERE DATE(date_time) BETWEEN ? AND ? ORDER BY id DESC";

    			$stmt = mysqli_prepare($conn, $sql);
    			mysqli_stmt_bind_param($stmt, 'ss', $start_date, $end_date);
    		}

    		$data.="<thead class='sticky-header text-center'>

    		<tr class='header-row-2'>    
    		<th class='table-header-row-1'>frame Update Time(Mins)</th>

    		<th class='table-header-row-1'>Status</th>
    		<th class='table-header-row-1'>Date&Time</th>
    		<th class='table-header-row-1'> Name</th>
    		<th class='table-header-row-1'> Role</th>            
    		<th class='table-header-row-1'> Mobile/e-mail</th>
    		</tr>
    		</thead><tbody>";
    		if ($sql !== "") {
    			if (isset($stmt) && mysqli_stmt_execute($stmt)) 
    			{
    				$result = mysqli_stmt_get_result($stmt);
    				if (mysqli_num_rows($result) > 0) {
    					while ($r = mysqli_fetch_assoc($result)) { 

    						if($r['status']==="In-Progress")
    						{
    							$class_r=$orange;

    						}
    						else if($r['status']==="Pending" )
    						{
    							$class_r=$red;

    						}else if($r['status']==="Updated")
    						{
    							$class_r=$green;

    						}
    						else if($r['status']==="Initiated")
    						{
    							$class_r=$primary;

    						}
    						$data.= "<tr >
    						<td> ".$r['frame_time']."</td>    						
    						<td $class_r> ".$r['status']."</td>    						
    						<td> ".$r['date_time']."</td>
    						<td> ".$r['name']."</td>
    						<td> ".$r['role']."</td>
    						<td> ".$r['user_mobile']."/".$r['email']."</td></tr>";
    					}
    				} else {
    					$data.= '<tr><td class="text-danger" colspan="7">Records are not Found</td></tr>';
    				}
    				mysqli_stmt_close($stmt);
    			} else {
    				$data.= '<tr><td class="text-danger" colspan="7">Records are not Found</td></tr>';
    			}
    		} else {
    			$data.= '<tr><td class="text-danger" colspan="7">Records are not Found</td></tr>';
    		}

    		$data.="</tbody>";

    	}

    	if ($alert == "LIGHTS-DETAILS") 
    	{
    		if ($records === "LATEST") {
    			$sql = "SELECT * FROM `$db`.`installed_lights_info` ORDER BY id DESC LIMIT 50";
    			$stmt = mysqli_prepare($conn, $sql);    			
    		} else {
    			$sql = "SELECT * FROM `$db`.`installed_lights_info` WHERE DATE(date_time) BETWEEN ? AND ? ORDER BY id DESC";

    			$stmt = mysqli_prepare($conn, $sql);
    			mysqli_stmt_bind_param($stmt, 'ss', $start_date, $end_date);
    		}

    		$data.="<thead class='sticky-header text-center'>

    		<tr class='header-row-2'>    
    		<th class='table-header-row-1'> Brand</th>
    		<th class='table-header-row-1'> Wattage</th>
    		<th class='table-header-row-1'> Quantity</th>
    		<th class='table-header-row-1'> Total Wattge</th>
    		<th class='table-header-row-1'> Status</th>            
    		<th class='table-header-row-1'> Date&Time</th>
    		<th class='table-header-row-1'> Name</th>
    		<th class='table-header-row-1'> Role</th>            
    		<th class='table-header-row-1'> Mobile/e-mail</th>
    		</tr>
    		</thead><tbody>";
    		if ($sql !== "") {
    			if (isset($stmt) && mysqli_stmt_execute($stmt)) 
    			{
    				$result = mysqli_stmt_get_result($stmt);
    				if (mysqli_num_rows($result) > 0) {
    					while ($r = mysqli_fetch_assoc($result)) { 

    						$updated_date=$r['updated_date_time'];
    						$update="Removed";
    						$class_r=$red;
    						if($r['add_or_removed']==1)
    						{
    							$class_r=$green;
    							$updated_date=$r['created_date_time'];
    							$update="Added";
    						}

    						$data.= "<tr >
    						<td> ".$r['brand_name']."</td>    						
    						<td> ".$r['wattage']."</td>    						
    						<td> ".$r['total_lights']."</td>    						
    						<td> ".$r['total_wattage']."</td>    						

    						<td $class_r> ".$update."</td>  
    						<td> ".$updated_date."</td>
    						<td> ".$r['user_name']."</td>
    						<td> ".$r['role']."</td>
    						<td> ".$r['user_mobile']."</td></tr>";
    					}
    				} else {
    					$data.= '<tr><td class="text-danger" colspan="7">Records are not Found</td></tr>';
    				}
    				mysqli_stmt_close($stmt);
    			} else {
    				$data.= '<tr><td class="text-danger" colspan="7">Records are not Found</td></tr>';
    			}
    		} else {
    			$data.= '<tr><td class="text-danger" colspan="7">Records are not Found</td></tr>';
    		}

    		$data.="</tbody>";

    	}
    	if ($alert == "LOCATION") 
    	{
    		if ($records === "LATEST") {
    			$sql = "SELECT * FROM `$db`.`coordinates_list` ORDER BY id DESC LIMIT 50";
    			$stmt = mysqli_prepare($conn, $sql);    			
    		} else {
    			$sql = "SELECT * FROM `$db`.`coordinates_list` WHERE DATE(date_time) BETWEEN ? AND ? ORDER BY id DESC";

    			$stmt = mysqli_prepare($conn, $sql);
    			mysqli_stmt_bind_param($stmt, 'ss', $start_date, $end_date);
    		}

    		$data.="<thead class='sticky-header text-center'>

    		<tr class='header-row-2'>    
    		<th class='table-header-row-1'> Latitude</th>
    		<th class='table-header-row-1'> Longitude</th>
    		<th class='table-header-row-1'> Location</th>    		         
    		<th class='table-header-row-1'> Date&Time</th>
    		<th class='table-header-row-1'> Name</th>
    		<th class='table-header-row-1'> Role</th>            
    		<th class='table-header-row-1'> Mobile/e-mail</th>
    		</tr>
    		</thead><tbody>";
    		if ($sql !== "") {
    			if (isset($stmt) && mysqli_stmt_execute($stmt)) 
    			{
    				$result = mysqli_stmt_get_result($stmt);
    				if (mysqli_num_rows($result) > 0) {
    					while ($r = mysqli_fetch_assoc($result)) { 
    						$loc = "https://www.google.com/maps?q=".$r['latitude'].",".$r['longitude'];
    						$location = '<a href="'.$loc.'" target="_blank" class="link-underline link-underline-opacity-0 text-primary">Location</a>';

    						$data .= "<tr>
    						<td>".$r['latitude']."</td>
    						<td>".$r['longitude']."</td>
    						<td>".$location."</td>
    						<td>".$r['date_time']."</td>
    						<td>".$r['name']."</td>
    						<td>".$r['role']."</td>
    						<td>".$r['user_mobile']."/".$r['email']."</td>
    						</tr>";
    					}

    				} else {
    					$data.= '<tr><td class="text-danger" colspan="7">Records are not Found</td></tr>';
    				}
    				mysqli_stmt_close($stmt);
    			} else {
    				$data.= '<tr><td class="text-danger" colspan="7">Records are not Found</td></tr>';
    			}
    		} else {
    			$data.= '<tr><td class="text-danger" colspan="7">Records are not Found</td></tr>';
    		}

    		$data.="</tbody>";

    	}

    	if ($alert == "ADDRESS") 
    	{
    		if ($records === "LATEST") {
    			$sql = "SELECT * FROM `$db`.`device_address` ORDER BY id DESC LIMIT 50";
    			$stmt = mysqli_prepare($conn, $sql);    			
    		} else {
    			$sql = "SELECT * FROM `$db`.`device_address` WHERE DATE(date_time) BETWEEN ? AND ? ORDER BY id DESC";

    			$stmt = mysqli_prepare($conn, $sql);
    			mysqli_stmt_bind_param($stmt, 'ss', $start_date, $end_date);
    		}

    		$data.="<thead class='sticky-header text-center'>

    		<tr class='header-row-2'>    
    		<th class='table-header-row-1'> Street</th>
    		<th class='table-header-row-1'> town</th>
    		<th class='table-header-row-1'> city</th>    		         
    		<th class='table-header-row-1'> district</th>    		         
    		<th class='table-header-row-1'> state</th>    		         
    		<th class='table-header-row-1'> pincode</th>    		         
    		<th class='table-header-row-1'> country</th>    		         
    		<th class='table-header-row-1'> Landmark</th>    		         
    		<th class='table-header-row-1'> Date&Time</th>
    		<th class='table-header-row-1'> Name</th>
    		<th class='table-header-row-1'> Role</th>            
    		<th class='table-header-row-1'> Mobile/e-mail</th>
    		</tr>
    		</thead><tbody>";
    		if ($sql !== "") {
    			if (isset($stmt) && mysqli_stmt_execute($stmt)) 
    			{
    				$result = mysqli_stmt_get_result($stmt);
    				if (mysqli_num_rows($result) > 0) {
    					while ($r = mysqli_fetch_assoc($result)) {     						
    						$data .= "<tr>
    						<td>".$r['street']."</td>
    						<td>".$r['town']."</td>
    						<td>".$r['city']."</td>
    						<td>".$r['district']."</td>
    						<td>".$r['state']."</td>
    						<td>".$r['pincode']."</td>
    						<td>".$r['pincode']."</td>
    						<td>".$r['Landmark']."</td>    						
    						<td>".$r['date_time']."</td>
    						<td>".$r['name']."</td>
    						<td>".$r['role']."</td>
    						<td>".$r['user_mobile']."/".$r['email']."</td>
    						</tr>";
    					}

    				} else {
    					$data.= '<tr><td class="text-danger" colspan="7">Records are not Found</td></tr>';
    				}
    				mysqli_stmt_close($stmt);
    			} else {
    				$data.= '<tr><td class="text-danger" colspan="7">Records are not Found</td></tr>';
    			}
    		} else {
    			$data.= '<tr><td class="text-danger" colspan="7">Records are not Found</td></tr>';
    		}

    		$data.="</tbody>";

    	}
    	if ($alert == "RESET-IOT") 
    	{
    		if ($records === "LATEST") {
    			$sql = "SELECT * FROM `$db`.`iot_device_reset` ORDER BY id DESC LIMIT 50";
    			$stmt = mysqli_prepare($conn, $sql);    			
    		} else {
    			$sql = "SELECT * FROM `$db`.`iot_device_reset` WHERE DATE(date_time) BETWEEN ? AND ? ORDER BY id DESC";

    			$stmt = mysqli_prepare($conn, $sql);
    			mysqli_stmt_bind_param($stmt, 'ss', $start_date, $end_date);
    		}

    		$data.="<thead class='sticky-header text-center'>

    		<tr class='header-row-2'>    
    		<th class='table-header-row-1'> Command</th>

    		<th class='table-header-row-1'> Date&Time</th>
    		<th class='table-header-row-1'> Name</th>
    		<th class='table-header-row-1'> Role</th>            
    		<th class='table-header-row-1'> Mobile/e-mail</th>
    		</tr>
    		</thead><tbody>";
    		if ($sql !== "") {
    			if (isset($stmt) && mysqli_stmt_execute($stmt)) 
    			{
    				$result = mysqli_stmt_get_result($stmt);
    				if (mysqli_num_rows($result) > 0) {
    					while ($r = mysqli_fetch_assoc($result)) {     						
    						$data .= "<tr>
    						<td>".$r['reset']."</td>    						    						
    						<td>".$r['date_time']."</td>
    						<td>".$r['name']."</td>
    						<td>".$r['role']."</td>
    						<td>".$r['user_mobile']."/".$r['email']."</td>
    						</tr>";
    					}

    				} else {
    					$data.= '<tr><td class="text-danger" colspan="7">Records are not Found</td></tr>';
    				}
    				mysqli_stmt_close($stmt);
    			} else {
    				$data.= '<tr><td class="text-danger" colspan="7">Records are not Found</td></tr>';
    			}
    		} else {
    			$data.= '<tr><td class="text-danger" colspan="7">Records are not Found</td></tr>';
    		}

    		$data.="</tbody>";

    	}
    	if ($alert == "RESET-ENERGY") 
    	{
    		if ($records === "LATEST") {
    			$sql = "SELECT * FROM `$db`.`iot_reset_energy` ORDER BY id DESC LIMIT 50";
    			$stmt = mysqli_prepare($conn, $sql);    			
    		} else {
    			$sql = "SELECT * FROM `$db`.`iot_reset_energy` WHERE DATE(date_time) BETWEEN ? AND ? ORDER BY id DESC";

    			$stmt = mysqli_prepare($conn, $sql);
    			mysqli_stmt_bind_param($stmt, 'ss', $start_date, $end_date);
    		}

    		$data.="<thead class='sticky-header text-center'>

    		<tr class='header-row-2'>    
    		<th class='table-header-row-1'> kWh</th>
    		<th class='table-header-row-1'> kVAh</th>

    		<th class='table-header-row-1'> Date&Time</th>
    		<th class='table-header-row-1'> Name</th>
    		<th class='table-header-row-1'> Role</th>            
    		<th class='table-header-row-1'> Mobile/e-mail</th>
    		</tr>
    		</thead><tbody>";
    		if ($sql !== "") {
    			if (isset($stmt) && mysqli_stmt_execute($stmt)) 
    			{
    				$result = mysqli_stmt_get_result($stmt);
    				if (mysqli_num_rows($result) > 0) {
    					while ($r = mysqli_fetch_assoc($result)) {     						
    						$data .= "<tr>
    						<td>".$r['kwh']."</td>    						    						
    						<td>".$r['kvah']."</td>    						    						
    						<td>".$r['date_time']."</td>
    						<td>".$r['name']."</td>
    						<td>".$r['role']."</td>
    						<td>".$r['user_mobile']."/".$r['email']."</td>
    						</tr>";
    					}

    				} else {
    					$data.= '<tr><td class="text-danger" colspan="7">Records are not Found</td></tr>';
    				}
    				mysqli_stmt_close($stmt);
    			} else {
    				$data.= '<tr><td class="text-danger" colspan="7">Records are not Found</td></tr>';
    			}
    		} else {
    			$data.= '<tr><td class="text-danger" colspan="7">Records are not Found</td></tr>';
    		}

    		$data.="</tbody>";

    	}

    	if ($alert == "HYSTERESIS") 
    	{
    		if ($records === "LATEST") {
    			$sql = "SELECT * FROM `$db`.`iot_hysteresis` ORDER BY id DESC LIMIT 50";
    			$stmt = mysqli_prepare($conn, $sql);    			
    		} else {
    			$sql = "SELECT * FROM `$db`.`iot_hysteresis` WHERE DATE(date_time) BETWEEN ? AND ? ORDER BY id DESC";

    			$stmt = mysqli_prepare($conn, $sql);
    			mysqli_stmt_bind_param($stmt, 'ss', $start_date, $end_date);
    		}

    		$data.="<thead class='sticky-header text-center'>

    		<tr class='header-row-2'>    
    		<th class='table-header-row-1'> Update Value</th>
    		<th class='table-header-row-1'> Date&Time</th>
    		<th class='table-header-row-1'> Name</th>
    		<th class='table-header-row-1'> Role</th>            
    		<th class='table-header-row-1'> Mobile/e-mail</th>
    		</tr>
    		</thead><tbody>";
    		if ($sql !== "") {
    			if (isset($stmt) && mysqli_stmt_execute($stmt)) 
    			{
    				$result = mysqli_stmt_get_result($stmt);
    				if (mysqli_num_rows($result) > 0) {
    					while ($r = mysqli_fetch_assoc($result)) {     						
    						$data .= "<tr>
    						<td>".$r['value']."</td>    						
    						<td>".$r['date_time']."</td>
    						<td>".$r['name']."</td>
    						<td>".$r['role']."</td>
    						<td>".$r['user_mobile']."/".$r['email']."</td>
    						</tr>";
    					}

    				} else {
    					$data.= '<tr><td class="text-danger" colspan="7">Records are not Found</td></tr>';
    				}
    				mysqli_stmt_close($stmt);
    			} else {
    				$data.= '<tr><td class="text-danger" colspan="7">Records are not Found</td></tr>';
    			}
    		} else {
    			$data.= '<tr><td class="text-danger" colspan="7">Records are not Found</td></tr>';
    		}

    		$data.="</tbody>";

    	}
    	if ($alert == "WIFI-DETAILS") 
    	{
    		if ($records === "LATEST") {
    			$sql = "SELECT * FROM `$db`.`iot_wifi_credentials` ORDER BY id DESC LIMIT 50";
    			$stmt = mysqli_prepare($conn, $sql);    			
    		} else {
    			$sql = "SELECT * FROM `$db`.`iot_wifi_credentials` WHERE DATE(date_time) BETWEEN ? AND ? ORDER BY id DESC";

    			$stmt = mysqli_prepare($conn, $sql);
    			mysqli_stmt_bind_param($stmt, 'ss', $start_date, $end_date);
    		}

    		$data.="<thead class='sticky-header text-center'>

    		<tr class='header-row-2'>    
    		<th class='table-header-row-1'> SSID</th>
    		<th class='table-header-row-1'> Password</th>
    		<th class='table-header-row-1'> Date&Time</th>
    		<th class='table-header-row-1'> Name</th>
    		<th class='table-header-row-1'> Role</th>            
    		<th class='table-header-row-1'> Mobile/e-mail</th>
    		</tr>
    		</thead><tbody>";
    		if ($sql !== "") {
    			if (isset($stmt) && mysqli_stmt_execute($stmt)) 
    			{
    				$result = mysqli_stmt_get_result($stmt);
    				if (mysqli_num_rows($result) > 0) {
    					while ($r = mysqli_fetch_assoc($result)) {     						
    						$data .= "<tr>
    						<td>".$r['ssid']."</td>    						
    						<td>".$r['password']."</td>    						
    						<td>".$r['date_time']."</td>
    						<td>".$r['name']."</td>
    						<td>".$r['role']."</td>
    						<td>".$r['user_mobile']."/".$r['email']."</td>
    						</tr>";
    					}

    				} else {
    					$data.= '<tr><td class="text-danger" colspan="7">Records are not Found</td></tr>';
    				}
    				mysqli_stmt_close($stmt);
    			} else {
    				$data.= '<tr><td class="text-danger" colspan="7">Records are not Found</td></tr>';
    			}
    		} else {
    			$data.= '<tr><td class="text-danger" colspan="7">Records are not Found</td></tr>';
    		}

    		$data.="</tbody>";

    	}

    	if ($alert == "ID-UPDATE") 
    	{
    		if ($records === "LATEST") {
    			$sql = "SELECT * FROM `$db`.`iot_device_id_change` ORDER BY id DESC LIMIT 50";
    			$stmt = mysqli_prepare($conn, $sql);    			
    		} else {
    			$sql = "SELECT * FROM `$db`.`iot_device_id_change` WHERE DATE(date_time) BETWEEN ? AND ? ORDER BY id DESC";

    			$stmt = mysqli_prepare($conn, $sql);
    			mysqli_stmt_bind_param($stmt, 'ss', $start_date, $end_date);
    		}

    		$data.="<thead class='sticky-header text-center'>

    		<tr class='header-row-2'>    
    		<th class='table-header-row-1'> Old Device ID</th>
    		<th class='table-header-row-1'>  Transferred To </th>
    		<th class='table-header-row-1'> Date&Time</th>
    		<th class='table-header-row-1'> Name</th>
    		<th class='table-header-row-1'> Role</th>            
    		<th class='table-header-row-1'> Mobile/e-mail</th>
    		</tr>
    		</thead><tbody>";
    		if ($sql !== "") {
    			if (isset($stmt) && mysqli_stmt_execute($stmt)) 
    			{
    				$result = mysqli_stmt_get_result($stmt);
    				if (mysqli_num_rows($result) > 0) {
    					while ($r = mysqli_fetch_assoc($result)) {     						
    						$data .= "<tr>
    						<td>".$r['device_id']."</td>    						
    						<td>".$r['new_device_id']."</td>    						
    						<td>".$r['date_time']."</td>
    						<td>".$r['name']."</td>
    						<td>".$r['role']."</td>
    						<td>".$r['user_mobile']."/".$r['email']."</td>
    						</tr>";
    					}

    				} else {
    					$data.= '<tr><td class="text-danger" colspan="7">Records are not Found</td></tr>';
    				}
    				mysqli_stmt_close($stmt);
    			} else {
    				$data.= '<tr><td class="text-danger" colspan="7">Records are not Found</td></tr>';
    			}
    		} else {
    			$data.= '<tr><td class="text-danger" colspan="7">Records are not Found</td></tr>';
    		}

    		$data.="</tbody>";

    	}

    	if ($alert == "SERIAL-ID-UPDATE") 
    	{
    		if ($records === "LATEST") {
    			$sql = "SELECT * FROM `$db`.`iot_serial_id_change` ORDER BY id DESC LIMIT 50";
    			$stmt = mysqli_prepare($conn, $sql);    			
    		} else {
    			$sql = "SELECT * FROM `$db`.`iot_serial_id_change` WHERE DATE(date_time) BETWEEN ? AND ? ORDER BY id DESC";

    			$stmt = mysqli_prepare($conn, $sql);
    			mysqli_stmt_bind_param($stmt, 'ss', $start_date, $end_date);
    		}

    		$data.="<thead class='sticky-header text-center'>

    		<tr class='header-row-2'>    
    		<th class='table-header-row-1'> Updated New Serial No/ID</th>

    		<th class='table-header-row-1'> Date&Time</th>
    		<th class='table-header-row-1'> Name</th>
    		<th class='table-header-row-1'> Role</th>            
    		<th class='table-header-row-1'> Mobile/e-mail</th>
    		</tr>
    		</thead><tbody>";
    		if ($sql !== "") {
    			if (isset($stmt) && mysqli_stmt_execute($stmt)) 
    			{
    				$result = mysqli_stmt_get_result($stmt);
    				if (mysqli_num_rows($result) > 0) {
    					while ($r = mysqli_fetch_assoc($result)) {     						
    						$data .= "<tr>

    						<td>".$r['serial_id']."</td>    						
    						<td>".$r['date_time']."</td>
    						<td>".$r['name']."</td>
    						<td>".$r['role']."</td>
    						<td>".$r['user_mobile']."/".$r['email']."</td>
    						</tr>";
    					}

    				} else {
    					$data.= '<tr><td class="text-danger" colspan="7">Records are not Found</td></tr>';
    				}
    				mysqli_stmt_close($stmt);
    			} else {
    				$data.= '<tr><td class="text-danger" colspan="7">Records are not Found</td></tr>';
    			}
    		} else {
    			$data.= '<tr><td class="text-danger" colspan="7">Records are not Found</td></tr>';
    		}

    		$data.="</tbody>";

    	}
    	if ($alert == "ON-OFF-INTERVAL") 
    	{
    		if ($records === "LATEST") {
    			$sql = "SELECT * FROM `$db`.`iot_on_off_interval` ORDER BY id DESC LIMIT 50";
    			$stmt = mysqli_prepare($conn, $sql);    			
    		} else {
    			$sql = "SELECT * FROM `$db`.`iot_on_off_interval` WHERE DATE(date_time) BETWEEN ? AND ? ORDER BY id DESC";

    			$stmt = mysqli_prepare($conn, $sql);
    			mysqli_stmt_bind_param($stmt, 'ss', $start_date, $end_date);
    		}

    		$data.="<thead class='sticky-header text-center'>

    		<tr class='header-row-2'>    
    		<th class='table-header-row-1'>Interval Time(min)</th>

    		<th class='table-header-row-1'> Date&Time</th>
    		<th class='table-header-row-1'> Name</th>
    		<th class='table-header-row-1'> Role</th>            
    		<th class='table-header-row-1'> Mobile/e-mail</th>
    		</tr>
    		</thead><tbody>";
    		if ($sql !== "") {
    			if (isset($stmt) && mysqli_stmt_execute($stmt)) 
    			{
    				$result = mysqli_stmt_get_result($stmt);
    				if (mysqli_num_rows($result) > 0) {
    					while ($r = mysqli_fetch_assoc($result)) {     						
    						$data .= "<tr>

    						<td>".$r['value']."</td>    						
    						<td>".$r['date_time']."</td>
    						<td>".$r['name']."</td>
    						<td>".$r['role']."</td>
    						<td>".$r['user_mobile']."/".$r['email']."</td>
    						</tr>";
    					}

    				} else {
    					$data.= '<tr><td class="text-danger" colspan="7">Records are not Found</td></tr>';
    				}
    				mysqli_stmt_close($stmt);
    			} else {
    				$data.= '<tr><td class="text-danger" colspan="7">Records are not Found</td></tr>';
    			}
    		} else {
    			$data.= '<tr><td class="text-danger" colspan="7">Records are not Found</td></tr>';
    		}

    		$data.="</tbody>";

    	}


    	mysqli_close($conn);
    }

    echo json_encode($data);
}

function sanitize_input($data, $conn) {
	$data = trim($data);
	$data = stripslashes($data);
	$data = htmlspecialchars($data);
	return mysqli_real_escape_string($conn, $data);
}


function get_phase($id)
{

	include_once("../../common-files/fetch-device-phase.php");
	return $device_phase;
	//return "3PH";

}
?>
