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

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["device_id"])) {
    // Sanitize only if it's used in a raw query or output — not needed here since we use prepared statement
    $device_id = $_POST["device_id"];

    $conn = mysqli_connect(HOST, USERNAME, PASSWORD, DB_USER);
    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }

    $sql = "DELETE FROM electrician_devices WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);

    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "i", $device_id);
        if (mysqli_stmt_execute($stmt)) {
            echo "Device removed successfully!";
        } else {
            echo "Error removing device.";
        }
        mysqli_stmt_close($stmt);
    } else {
        echo "Failed to prepare the delete query.";
    }

    mysqli_close($conn);
} else {
    echo "Invalid request!";
}
?>
