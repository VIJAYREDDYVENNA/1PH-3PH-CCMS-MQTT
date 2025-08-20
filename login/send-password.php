<?php
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['USER_ID'])) {
    // Validate and sanitize input
	$user_details = filter_input(INPUT_POST, 'USER_ID', FILTER_SANITIZE_STRING);
	
	require("../config_db/config.php");

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
	$response="";

	$conn = mysqli_connect(HOST, USERNAME, PASSWORD, DB_USER);

	if (!$conn) {
		die("Connection failed: " . mysqli_connect_error());
	}
	$user_details = sanitize_input($user_details, $conn);
// Prepare SQL statement
	$sql = "SELECT * FROM login_details WHERE (mobile_no = ? OR email_id = ? OR user_id = ?)";
	$stmt = mysqli_prepare($conn, $sql);
	mysqli_stmt_bind_param($stmt, "sss", $user_details, $user_details, $user_details);

	if (mysqli_stmt_execute($stmt)) {
		$result = mysqli_stmt_get_result($stmt);
		$count = mysqli_num_rows($result);
		mysqli_stmt_close($stmt);
		if ($count == 1) {
			$r = mysqli_fetch_assoc($result);
			$mobile_no = trim(strtolower($r['mobile_no']));
			$e_mail = trim(strtolower($r['email_id']));
			$login_id = $r['id'];
			$password=generateTemporaryPassword(12);

			
			$hashedPassword = password_hash($password, PASSWORD_DEFAULT);
			$query = "UPDATE login_details SET password = ? WHERE id = ?";
			if ($stmt = mysqli_prepare($conn, $query)) {

				mysqli_stmt_bind_param($stmt, 'si',  $hashedPassword, $login_id);

                    // Execute the statement
				if (mysqli_stmt_execute($stmt)) {
					
					$msg ="Hi, we've sent you CCMS Login password:$password. Ignore if not requested. ISCIENTIFIC TECHSOLUTIONS LABS PRIVATE LIMITED";

					$url = "https://smslogin.co/v3/api.php?username=ISTLPL&apikey=4079df135be056d9e976&senderid=ISTLPL&mobile=$mobile_no&message=".urlencode($msg)."&templateid=1707174177356563520";    

					$ret = file($url); 

					$response="We have sent a new password to your registered mobile number."; 
				}
			}  



		} else {
			$response= $count > 1 ? "This account has multiple credentials. Please contact support." : "Invalid Credentials";
		}
	} else {
		$response = "Something went wrong. Please try again.";
	}

	echo json_encode($response);
}
function sanitize_input($data, $conn) {
	$data = trim($data);
	$data = stripslashes($data);
	$data = htmlspecialchars($data);
	return mysqli_real_escape_string($conn, $data);
}


function generateTemporaryPassword($length = 12) {
	$uppercase = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
	$lowercase = 'abcdefghijklmnopqrstuvwxyz';
	$numbers   = '0123456789';
	$special   = '!@#$^&*?';

    // Ensure at least one character from each required set
	$password  = $uppercase[random_int(0, strlen($uppercase) - 1)];
	$password .= $lowercase[random_int(0, strlen($lowercase) - 1)];
	$password .= $numbers[random_int(0, strlen($numbers) - 1)];
	$password .= $special[random_int(0, strlen($special) - 1)];

    // Fill the rest of the password with random characters
	$allChars = $uppercase . $lowercase . $numbers . $special;
	for ($i = 4; $i < $length; $i++) {
		$password .= $allChars[random_int(0, strlen($allChars) - 1)];
	}

	return $password ;

    // Shuffle to randomize the order
    //return str_shuffle($password);
}
?>