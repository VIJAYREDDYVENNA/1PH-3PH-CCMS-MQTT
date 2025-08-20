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

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["device_id"], $_POST["new_electrician_id"], $_POST["group_id"])) {
    $conn = mysqli_connect(HOST, USERNAME, PASSWORD, DB_USER);
    
    if (!$conn) {
        die(json_encode(["status" => "error", "message" => "Database connection failed."]));
    }
    $permission_query = "SELECT add_remove_electrician FROM user_permissions WHERE login_id = ?";
    $permission_stmt = mysqli_prepare($conn, $permission_query);
    mysqli_stmt_bind_param($permission_stmt, "s", $user_login_id);
    mysqli_stmt_execute($permission_stmt);
    mysqli_stmt_bind_result($permission_stmt, $add_remove_electrician);
    mysqli_stmt_fetch($permission_stmt);
    mysqli_stmt_close($permission_stmt);

    if ($add_remove_electrician != 1) {
        echo json_encode(["status" => "error", "message" => "You do not have permission to Add or Remove electricians and Devices."]);
        mysqli_close($conn);
        exit();
    }
    function sanitize_input($data, $conn) {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
        return mysqli_real_escape_string($conn, $data);
    }

    $device_id = sanitize_input($_POST["device_id"], $conn);
    $new_electrician_id = sanitize_input($_POST["new_electrician_id"], $conn);
    $group_id = sanitize_input($_POST["group_id"], $conn);

    // Fetch new electrician details
    $fetch_sql = "SELECT name, phone_number FROM electricians_list WHERE id = '$new_electrician_id' ";
    $result = mysqli_query($conn, $fetch_sql);

    if ($row = mysqli_fetch_assoc($result)) {
        $electrician_name = $row['name'];
        $electrician_phone = $row['phone_number'];

        // Fetch group_area
        $fetch_group_sql = "SELECT device_group_or_area FROM user_device_group_view WHERE device_id = '$device_id' LIMIT 1";
        $result_group = mysqli_query($conn, $fetch_group_sql);

        $group_area = '';
        if ($row_group = mysqli_fetch_assoc($result_group)) {
            $group_area = $row_group['device_group_or_area'];
        }

        // Check if the device already has an associated electrician
        $check_sql = "SELECT id FROM electrician_devices WHERE device_id = '$device_id'";
        $check_result = mysqli_query($conn, $check_sql);

        if (mysqli_num_rows($check_result) > 0) {
            // Update the existing record
            $update_sql = "UPDATE electrician_devices 
                           SET electrician_name = '$electrician_name', phone_number = '$electrician_phone', group_area = '$group_area' 
                           WHERE device_id = '$device_id'";
            if (mysqli_query($conn, $update_sql)) {
                echo json_encode(["status" => "success", "message" => "Electrician updated successfully."]);
            } else {
                echo json_encode(["status" => "error", "message" => "Failed to update electrician."]);
            }
        } else {
            // Insert new record
            $insert_sql = "INSERT INTO electrician_devices (device_id, electrician_name, phone_number, group_area, user_login_id) 
                           VALUES ('$device_id', '$electrician_name', '$electrician_phone', '$group_area', '$user_login_id')";
            if (mysqli_query($conn, $insert_sql)) {
                echo json_encode(["status" => "success", "message" => "Electrician added successfully."]);
            } else {
                echo json_encode(["status" => "error", "message" => "Failed to add electrician."]);
            }
        }
    } else {
        echo json_encode(["status" => "error", "message" => "Electrician details not found."]);
    }

    mysqli_close($conn);
}
?>
