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

// Establish database connection
$conn = mysqli_connect(HOST, USERNAME, PASSWORD, DB_USER);
if (!$conn) {
    die(json_encode(["status" => "error", "message" => "Database connection failed."]));
}

// Prepare SQL query
if ($role == "SUPERADMIN") {
    $sql = "SELECT id, name, phone_number FROM electricians_list  ORDER BY name ASC";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_execute($stmt);
    if ($stmt) {
        // Bind parameters and execute
        mysqli_stmt_execute($stmt);

        // Get result
        $result = mysqli_stmt_get_result($stmt);
        $electricians = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $electricians[] = [
                "id" => $row["id"],
                "name" => $row["name"],
                "phone" => $row["phone_number"]
            ];
        }

        echo json_encode(["status" => "success", "data" => $electricians]);
        mysqli_stmt_close($stmt);
    } else {
        echo json_encode(["status" => "error", "message" => "Failed to prepare SQL statement."]);
    }
} else {
    $sql = "SELECT id, name, phone_number FROM electricians_list WHERE user_login_id = ? ORDER BY name ASC";
    $stmt = mysqli_prepare($conn, $sql);

    if ($stmt) {
        // Bind parameters and execute
        mysqli_stmt_bind_param($stmt, "i", $user_login_id);
        mysqli_stmt_execute($stmt);

        // Get result
        $result = mysqli_stmt_get_result($stmt);
        $electricians = [];

        while ($row = mysqli_fetch_assoc($result)) {
            $electricians[] = [
                "id" => $row["id"],
                "name" => $row["name"],
                "phone" => $row["phone_number"]
            ];
        }

        echo json_encode(["status" => "success", "data" => $electricians]);
        mysqli_stmt_close($stmt);
    } else {
        echo json_encode(["status" => "error", "message" => "Failed to prepare SQL statement."]);
    }
}
// Close database connection
mysqli_close($conn);
