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
  
    $electrician_name = $_POST['electrician_name'];
    $electrician_phone = $_POST['electrician_phone'];
    $group_id = $_POST['group_id'];
    $device_ids = $_POST['device_ids'];

    if (empty($electrician_name) || empty($electrician_phone) || empty($device_ids)) {
        echo json_encode(["status" => "error", "message" => "All fields are required."]);
        exit;
    }

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
        echo json_encode(["status" => "error", "message" => "You do not have permission to Add or Remove electricians and Devices."]);
        mysqli_close($conn);
        exit();
    }
    // Fetch group_id for the given device_ids
    $device_ids_placeholder = implode(',', array_fill(0, count($device_ids), '?'));
    $fetch_group_sql = "SELECT device_group_or_area FROM user_device_group_view WHERE device_id IN ($device_ids_placeholder) LIMIT 1";
    $stmt = mysqli_prepare($conn, $fetch_group_sql);

    $types = str_repeat('s', count($device_ids));
    mysqli_stmt_bind_param($stmt, $types, ...$device_ids);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $fetched_group_id);
    mysqli_stmt_fetch($stmt);
    mysqli_stmt_close($stmt);

    if (empty($fetched_group_id)) {
        echo json_encode(["status" => "error", "message" => "No group area found for selected devices."]);
        exit;
    }

    $group_area = $fetched_group_id;

    // Check if the electrician already exists
    $check_query = "SELECT id FROM electricians_list WHERE phone_number = ?";
    $stmt = mysqli_prepare($conn, $check_query);
    mysqli_stmt_bind_param($stmt, "s", $electrician_phone);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $electrician_id);
    mysqli_stmt_fetch($stmt);
    mysqli_stmt_close($stmt);

    if (empty($electrician_id)) {
        // Insert new electrician
        $insert_query = "INSERT INTO electricians_list (name, phone_number, group_area, user_login_id) VALUES (?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $insert_query);
        mysqli_stmt_bind_param($stmt, "ssss", $electrician_name, $electrician_phone, $group_area, $user_login_id);

        if (mysqli_stmt_execute($stmt)) {
            $electrician_id = mysqli_insert_id($conn);
        } else {
            echo json_encode(["status" => "error", "message" => "Failed to add electrician."]);
            exit;
        }
        mysqli_stmt_close($stmt);
    }

    // Insert electrician_devices for each device
    $insert_device_query = "INSERT INTO electrician_devices (electrician_name, phone_number, device_id, group_area, user_login_id) VALUES (?, ?, ?, ?, ?)";

    foreach ($device_ids as $device_id) {
        $stmt = mysqli_prepare($conn, $insert_device_query);
        mysqli_stmt_bind_param($stmt, "sssss", $electrician_name, $electrician_phone, $device_id, $group_area, $user_login_id);

        if (!mysqli_stmt_execute($stmt)) {
            echo json_encode(["status" => "error", "message" => "Failed to assign electrician to device."]);
            mysqli_stmt_close($stmt);
            exit;
        }

        mysqli_stmt_close($stmt);
    }

    mysqli_close($conn);
    echo json_encode(["status" => "success", "message" => "Electrician added successfully."]);
} else {
    echo json_encode(["status" => "error", "message" => "Invalid request."]);
}
?>
