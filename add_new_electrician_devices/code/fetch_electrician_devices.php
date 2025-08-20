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

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["electrician_name"])) {
    $electrician_name = $_POST['electrician_name'];

    $conn = mysqli_connect(HOST, USERNAME, PASSWORD, DB_USER);
    if (!$conn) {
        die(json_encode(["status" => "error", "message" => "Connection failed: " . mysqli_connect_error()]));
    }

    $electrician_name = mysqli_real_escape_string($conn, $electrician_name);

    $sql = "SELECT id, device_id FROM electrician_devices WHERE electrician_name = ?";
    $stmt = mysqli_prepare($conn, $sql);

    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "s", $electrician_name);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        $devices = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $devices[] = [
                "id" => $row["id"],
                "device_id" => $row["device_id"]
            ];
        }

        mysqli_stmt_close($stmt);
        mysqli_close($conn);

        echo json_encode($devices);
    } else {
        echo json_encode(["status" => "error", "message" => "Failed to prepare SQL statement."]);
    }
} else {
    echo json_encode([]);
}
?>
