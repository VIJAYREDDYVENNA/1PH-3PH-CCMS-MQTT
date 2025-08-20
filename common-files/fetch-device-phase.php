<?php
$device_phase="";

try {

	$conn_user = mysqli_connect(HOST, USERNAME, PASSWORD, DB_USER);
	if (!$conn_user) {
		die("Connection failed: " . mysqli_connect_error());
	}
	else
	{
		$device_phase="3PH";
		$sql = "SELECT  phase FROM activation_codes WHERE device_id = ?";
		$stmt = mysqli_prepare($conn_user, $sql);
		mysqli_stmt_bind_param($stmt, "s", $id);
		if (mysqli_stmt_execute($stmt)) 
		{
			$result = mysqli_stmt_get_result($stmt);
			if (mysqli_num_rows($result) > 0) {
				$r = mysqli_fetch_assoc($result);
				$phase = array("PHASE"=> $r['phase']); 
				$device_phase=$r['phase'];


			}
		}
		mysqli_stmt_close($stmt);
		mysqli_close($conn_user );
	}
} 
catch (Exception $e) {
	
}

?>