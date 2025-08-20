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

$device_list = [];

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["GROUP_ID"])) {
    $group_id = strtoupper(trim($_POST['GROUP_ID']));
    $selected_phase = strtoupper($_SESSION["SELECTED_PHASE"]);

    $conn_user = mysqli_connect(HOST, USERNAME, PASSWORD, DB_USER);
    if (!$conn_user) {
        die(json_encode(["status" => "error", "message" => "Database connection failed."]));
    }

    require_once(BASE_PATH_1 . "common-files/client-super-admin-device-names.php"); // defines $list
    $group_by = "device_group_or_area"; // default

    if ($group_id === "ALL") {
        $sql = "SELECT $list FROM user_device_list WHERE login_id = ? ";
        if ($selected_phase !== "ALL") {
            $sql .= "AND phase = ? ";
        }
        $sql .= "AND device_id NOT IN (SELECT device_id FROM electrician_devices) ";
        $sql .= "ORDER BY REGEXP_REPLACE(device_id, '[0-9]', ''), CAST(REGEXP_REPLACE(device_id, '[^0-9]', '') AS UNSIGNED)";

        $stmt = mysqli_prepare($conn_user, $sql);
        if ($selected_phase !== "ALL") {
            mysqli_stmt_bind_param($stmt, "is", $user_login_id, $selected_phase);
        } else {
            mysqli_stmt_bind_param($stmt, "i", $user_login_id);
        }

    } else {
        // Get group_by column for current login_id
        $sql_group = "SELECT group_by FROM device_selection_group WHERE login_id = ?";
        $stmt_group = mysqli_prepare($conn_user, $sql_group);
        mysqli_stmt_bind_param($stmt_group, "i", $user_login_id);
        mysqli_stmt_execute($stmt_group);
        mysqli_stmt_bind_result($stmt_group, $group_by);
        mysqli_stmt_fetch($stmt_group);
        mysqli_stmt_close($stmt_group);

        // Fetch device list based on group_id and phase
        $sql = "SELECT $list FROM user_device_group_view WHERE login_id = ? AND $group_by = ? ";
        if ($selected_phase !== "ALL") {
            $sql .= "AND phase = ? ";
        }
        $sql .= "AND device_id NOT IN (SELECT device_id FROM electrician_devices) ";
        $sql .= "ORDER BY REGEXP_REPLACE(device_id, '[0-9]', ''), CAST(REGEXP_REPLACE(device_id, '[^0-9]', '') AS UNSIGNED)";

        $stmt = mysqli_prepare($conn_user, $sql);
        if ($selected_phase !== "ALL") {
            mysqli_stmt_bind_param($stmt, "iss", $user_login_id, $group_id, $selected_phase);
        } else {
            mysqli_stmt_bind_param($stmt, "is", $user_login_id, $group_id);
        }
    }

    if (mysqli_stmt_execute($stmt)) {
        $result = mysqli_stmt_get_result($stmt);
        if ($result) {
            while ($r = mysqli_fetch_assoc($result)) {
                $device_list[] = ["D_ID" => $r['device_id'], "D_NAME" => $r['device_name']];
            }
        }
    }

    mysqli_stmt_close($stmt);
    mysqli_close($conn_user);

    echo json_encode($device_list);
} else {
    echo json_encode(["status" => "error", "message" => "Invalid request."]);
}
?>
