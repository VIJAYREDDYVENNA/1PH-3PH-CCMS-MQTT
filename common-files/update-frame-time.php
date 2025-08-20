<?php
require_once '../base-path/config-path.php';
require_once BASE_PATH . 'config_db/config.php';
require_once BASE_PATH . 'session/session-manager.php';

// Check session and retrieve session variables
SessionManager::checkSession();
$return_response = "";
$frame_date_time = "--";
$ping_date_time = "--";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

	$device_id = filter_input(INPUT_POST, 'DEVICE_ID', FILTER_SANITIZE_STRING); 
	$conn_db_all = mysqli_connect(HOST, USERNAME, PASSWORD, DB_ALL);
	if (!$conn_db_all) {
		die("Connection failed: " . mysqli_connect_error());
	} else {
        $device_id = sanitize_input($device_id, $conn_db_all);
        $sql = "SELECT  date_time, ping_time FROM live_data_updates WHERE device_id = ?";
        $stmt = mysqli_prepare($conn_db_all, $sql);
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "s", $device_id); 
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            if (mysqli_num_rows($result) > 0) {
            	$r = mysqli_fetch_assoc($result);
            	$frame_date_time = date("H:i:s d-m-Y", strtotime($r['date_time']));
            	$ping_date_time = date("H:i:s d-m-Y", strtotime($r['ping_time']));
            	$device_id = $r['device_id'];
            } 
            mysqli_stmt_close($stmt);
        } 
        $return_response = array(
          "DATE_TIME" => $ping_date_time
      );
        mysqli_close($conn_db_all);
    }
    echo json_encode($return_response);
} 
function sanitize_input($data, $conn) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return mysqli_real_escape_string($conn, $data);
}
?>
