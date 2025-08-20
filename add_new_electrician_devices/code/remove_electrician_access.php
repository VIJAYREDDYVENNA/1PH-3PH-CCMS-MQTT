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

$conn = mysqli_connect(HOST, USERNAME, PASSWORD, DB_USER);
if (!$conn) {
    die(json_encode(["status" => "error", "message" => "Database connection failed."]));
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
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
    if (isset($_POST["electrician_id"])) {
        $electrician_id = mysqli_real_escape_string($conn, $_POST["electrician_id"]);
        $sql = "DELETE FROM electrician_devices WHERE id = ?";
        $stmt = mysqli_prepare($conn, $sql);

        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "i", $electrician_id);
            if (mysqli_stmt_execute($stmt)) {
                echo json_encode(["status" => "success", "message" => "Electrician access removed successfully."]);
            } else {
                echo json_encode(["status" => "error", "message" => "Failed to remove electrician."]);
            }
            mysqli_stmt_close($stmt);
        } else {
            echo json_encode(["status" => "error", "message" => "Failed to prepare statement."]);
        }

    } elseif (isset($_POST["electrician_ids"])) {
        $electrician_ids = json_decode($_POST["electrician_ids"], true);

        if (!is_array($electrician_ids) || count($electrician_ids) === 0) {
            echo json_encode(["status" => "error", "message" => "No valid electrician IDs provided."]);
            exit;
        }

        $placeholders = implode(',', array_fill(0, count($electrician_ids), '?'));
        $sql = "DELETE FROM electrician_devices WHERE id IN ($placeholders)";
        $stmt = mysqli_prepare($conn, $sql);

        if ($stmt) {
            $types = str_repeat('i', count($electrician_ids));
            $bind_names[] = $stmt;
            $bind_names[] = $types;

            foreach ($electrician_ids as $key => $value) {
                $bind_name = 'bind' . $key;
                $$bind_name = $value;
                $bind_names[] = &$$bind_name;
            }

            call_user_func_array('mysqli_stmt_bind_param', $bind_names);

            if (mysqli_stmt_execute($stmt)) {
                echo json_encode(["status" => "success", "message" => "Selected electricians removed successfully."]);
            } else {
                echo json_encode(["status" => "error", "message" => "Failed to remove selected electricians."]);
            }

            mysqli_stmt_close($stmt);
        } else {
            echo json_encode(["status" => "error", "message" => "Failed to prepare statement for multiple deletions."]);
        }

    } else {
        echo json_encode(["status" => "error", "message" => "Invalid request."]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "Invalid request method."]);
}

mysqli_close($conn);
?>
