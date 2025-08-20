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

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["device_ids"])) {
    $conn = mysqli_connect(HOST, USERNAME, PASSWORD, DB_USER);
    if (!$conn) {
        echo json_encode(["status" => "error", "message" => "Database connection failed."]);
        exit;
    }
    $permission_query = "SELECT add_remove_electrician FROM `$users_db`.user_permissions WHERE login_id = ?";
    $permission_stmt = mysqli_prepare($conn, $permission_query);
    mysqli_stmt_bind_param($permission_stmt, "s", $user_login_id);
    mysqli_stmt_execute($permission_stmt);
    mysqli_stmt_bind_result($permission_stmt, $add_remove_electrician);
    mysqli_stmt_fetch($permission_stmt);
    mysqli_stmt_close($permission_stmt);

    if ($add_remove_electrician != 1) {
        echo json_encode(["status" => "error", "message" => "You do not have permission to Add / remove electricians and Devices."]);
        mysqli_close($conn);
        exit();
    }
    function sanitize_input($data, $conn) {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
        return mysqli_real_escape_string($conn, $data);
    }

    $electrician_name = sanitize_input($_POST['electrician_name'], $conn);
    $electrician_phone = sanitize_input($_POST['electrician_phone'], $conn);
    $group_id = sanitize_input($_POST['group_id'], $conn);
    $device_ids = array_map(function($id) use ($conn) {
        return sanitize_input($id, $conn);
    }, $_POST['device_ids']);

    if (empty($electrician_name) || empty($electrician_phone) || empty($device_ids)) {
        echo json_encode(["status" => "error", "message" => "All fields are required."]);
        exit;
    }

    // Check if electrician exists
    $check_query = "SELECT id FROM electricians_list WHERE phone_number = ? AND name = ?";
    $stmt = mysqli_prepare($conn, $check_query);
    mysqli_stmt_bind_param($stmt, "ss", $electrician_phone, $electrician_name);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $electrician_id);
    mysqli_stmt_fetch($stmt);
    mysqli_stmt_close($stmt);

    if ($electrician_id) {
        echo json_encode(["status" => "error", "message" => "Electrician already exists."]);
        exit;
    }
    $check_query1 = "SELECT phone_number FROM electricians_list WHERE phone_number = ? ";
    $stmt1 = mysqli_prepare($conn, $check_query1);
    mysqli_stmt_bind_param($stmt1, "s", $electrician_phone);
    mysqli_stmt_execute($stmt1);
    mysqli_stmt_bind_result($stmt1, $electrician_number);
    mysqli_stmt_fetch($stmt1);
    mysqli_stmt_close($stmt1);

    if ($electrician_number) {
        echo json_encode(["status" => "error", "message" => "Phone Number Already Exists."]);
        exit;
    }


    // Fetch group_area for the device
    $placeholders = implode(',', array_fill(0, count($device_ids), '?'));
    $fetch_group_sql = "SELECT device_group_or_area FROM user_device_group_view WHERE device_id IN ($placeholders) LIMIT 1";
    $stmt = mysqli_prepare($conn, $fetch_group_sql);
    $types = str_repeat('s', count($device_ids));
    mysqli_stmt_bind_param($stmt, $types, ...$device_ids);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $fetched_group_id);
    mysqli_stmt_fetch($stmt);
    mysqli_stmt_close($stmt);

    $group_area = $fetched_group_id;

    // Insert into electricians_list
    $insert_query = "INSERT INTO electricians_list (name, phone_number, group_area, user_login_id) VALUES (?, ?, ?, ?)";
    $stmt = mysqli_prepare($conn, $insert_query);
    mysqli_stmt_bind_param($stmt, "ssss", $electrician_name, $electrician_phone, $group_area, $user_login_id);

    if (mysqli_stmt_execute($stmt)) {
        $electrician_id = mysqli_insert_id($conn);
    } else {
        echo json_encode(["status" => "error", "message" => "Failed to add electrician."]);
        mysqli_stmt_close($stmt);
        mysqli_close($conn);
        exit;
    }
    mysqli_stmt_close($stmt);

    // Assign electrician to devices
    $query = "INSERT INTO electrician_devices (electrician_name, phone_number, device_id, group_area, user_login_id) VALUES (?, ?, ?, ?, ?)";
    $stmt = mysqli_prepare($conn, $query);

    foreach ($device_ids as $device_id) {
        mysqli_stmt_bind_param($stmt, "sssss", $electrician_name, $electrician_phone, $device_id, $group_area, $user_login_id);
        if (!mysqli_stmt_execute($stmt)) {
            echo json_encode(["status" => "error", "message" => "Failed to assign electrician to device."]);
            mysqli_stmt_close($stmt);
            mysqli_close($conn);
            exit;
        }
    }

    mysqli_stmt_close($stmt);
    mysqli_close($conn);

    echo json_encode(["status" => "success", "message" => "Electrician added successfully."]);
} else {
    echo json_encode(["status" => "error", "message" => "Invalid request."]);
}
?>
