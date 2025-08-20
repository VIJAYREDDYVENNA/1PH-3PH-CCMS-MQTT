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

$return_response = "";
$add_confirm = false;
$code = "";
$phase = "3PH";


if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Check if any field is empty
    $conn = mysqli_connect(HOST, USERNAME, PASSWORD);
    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    } else {
        $device_id = htmlspecialchars(mysqli_real_escape_string($conn, $_POST['deviceId']));
        $device_name = htmlspecialchars(mysqli_real_escape_string($conn, $_POST['deviceName']));
        if (empty($device_name)) {
            echo json_encode("Please enter Device Name");
            mysqli_close($conn);
            exit();
        }
        // First, check if the device name already exists
        if ($role === "SUPERADMIN") {
            // Query for SUPERADMIN, only bind one parameter
            $sql = "SELECT count(s_device_name) as name_count FROM `$users_db`.user_device_list WHERE s_device_name = ?";
            $stmt_1 = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt_1, "s", $device_name);  // Only one placeholder, so bind one parameter
        } else {
            // Query for non-SUPERADMIN roles, bind two parameters
            $sql = "SELECT count(s_device_name) as name_count FROM `$users_db`.user_device_list WHERE s_device_name = ? OR c_device_name = ?";
            $stmt_1 = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt_1, "ss", $device_name, $device_name);  // Bind both s_device_name and c_device_name
        }

        mysqli_stmt_execute($stmt_1);
        mysqli_stmt_bind_result($stmt_1, $name_count);
        mysqli_stmt_fetch($stmt_1);

        // Close the statement
        mysqli_stmt_close($stmt_1);


        if ($name_count == 0) {
            if ($role === "SUPERADMIN") {
                // For SUPERADMIN, update only the s_device_name
                $sql = "UPDATE `$users_db`.user_device_list SET s_device_name = ? WHERE device_id = ?";
                $stmt = mysqli_prepare($conn, $sql);
                mysqli_stmt_bind_param($stmt, "ss", $device_name, $device_id);
            } else {
                // For non-SUPERADMIN roles, update both c_device_name and s_device_name
                $sql = "UPDATE `$users_db`.user_device_list SET c_device_name = ?, s_device_name = ? WHERE device_id = ?";
                $stmt = mysqli_prepare($conn, $sql);
                mysqli_stmt_bind_param($stmt, "sss", $device_name, $device_name, $device_id);
            }

            // Execute the update query
            if (mysqli_stmt_execute($stmt)) {
                // Now insert into the device_name_update_log
                $device_db = strtolower(trim($device_id));
                $current_date_time = date('Y-m-d H:i:s'); // Get current date-time

                $sql = "INSERT INTO `$device_db`.device_name_update_log (device_id, user_alternative_name, date_time) VALUES (?, ?, ?)";
                $stmt_log = mysqli_prepare($conn, $sql);
                mysqli_stmt_bind_param($stmt_log, "sss", $device_id, $device_name, $current_date_time);

                if (mysqli_stmt_execute($stmt_log)) {
                    // Successfully updated and logged
                    echo json_encode(['status' => 'success', 'message' => 'Device name updated and log added successfully.']);
                } else {
                    // Logging failed
                    echo json_encode(['status' => 'error', 'message' => 'Device name updated, but failed to log the change.']);
                }

                // Close the logging statement
                mysqli_stmt_close($stmt_log);
            } else {
                // If the update failed
                echo json_encode(['status' => 'error', 'message' => 'Failed to update the device name.']);
            }

            // Close the update statement
            mysqli_stmt_close($stmt);
        } else {
            // If the device name already exists
            echo json_encode(['status' => 'warning', 'message' => 'Device name already exists!']);
        }
    }
} else {
    $return_response = "Data not available";
    echo json_encode(['status' => 'error', 'message' => $return_response]);
}
