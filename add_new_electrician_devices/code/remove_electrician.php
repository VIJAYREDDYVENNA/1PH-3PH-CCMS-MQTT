<?php
require_once '../../base-path/config-path.php';
require_once BASE_PATH_1 . 'config_db/config.php';
require_once BASE_PATH_1 . 'session/session-manager.php';

SessionManager::checkSession();
$sessionVars = SessionManager::SessionVariables();
$user_login_id = $sessionVars['user_login_id'];

$conn = mysqli_connect(HOST, USERNAME, PASSWORD, DB_USER);
if (!$conn) {
    die(json_encode(["status" => "error", "message" => "Database connection failed."]));
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Check permission
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
        // DELETE ONE ELECTRICIAN
        $id = mysqli_real_escape_string($conn, $_POST["electrician_id"]);
        $name = mysqli_real_escape_string($conn, $_POST["electricianName"]);
        $phone = mysqli_real_escape_string($conn, $_POST["electricianPhone"]);

        // Delete from electricians_list
        $query1 = "DELETE FROM electricians_list WHERE id = ?";
        $stmt1 = mysqli_prepare($conn, $query1);
        mysqli_stmt_bind_param($stmt1, "i", $id);
        mysqli_stmt_execute($stmt1);
        mysqli_stmt_close($stmt1);

        // Delete from electrician_devices
        $query2 = "DELETE FROM electrician_devices WHERE electrician_name = ? AND phone_number = ?";
        $stmt2 = mysqli_prepare($conn, $query2);
        mysqli_stmt_bind_param($stmt2, "ss", $name, $phone);
        mysqli_stmt_execute($stmt2);
        mysqli_stmt_close($stmt2);

        echo json_encode(["status" => "success", "message" => "Electrician access removed successfully."]);

    } elseif (isset($_POST["electrician_ids"])) {
        // DELETE MULTIPLE ELECTRICIANS
        $ids = json_decode($_POST["electrician_ids"], true);
        if (!is_array($ids) || empty($ids)) {
            echo json_encode(["status" => "error", "message" => "No valid IDs provided."]);
            exit;
        }

        // Fetch names and phones first
        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        $types = str_repeat('i', count($ids));

        $queryFetch = "SELECT name, phone_number FROM electricians_list WHERE id IN ($placeholders)";
        $stmtFetch = mysqli_prepare($conn, $queryFetch);
        mysqli_stmt_bind_param($stmtFetch, $types, ...$ids);
        mysqli_stmt_execute($stmtFetch);
        $result = mysqli_stmt_get_result($stmtFetch);

        $electricianData = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $electricianData[] = [
                'name' => $row['name'],
                'phone' => $row['phone_number']
            ];
        }
        mysqli_stmt_close($stmtFetch);

        // Delete from electricians_list
        $queryDeleteList = "DELETE FROM electricians_list WHERE id IN ($placeholders)";
        $stmtDeleteList = mysqli_prepare($conn, $queryDeleteList);
        mysqli_stmt_bind_param($stmtDeleteList, $types, ...$ids);
        mysqli_stmt_execute($stmtDeleteList);
        mysqli_stmt_close($stmtDeleteList);

        // Delete from electrician_devices
        $queryDeleteDevices = "DELETE FROM electrician_devices WHERE electrician_name = ? AND phone_number = ?";
        $stmtDeleteDevices = mysqli_prepare($conn, $queryDeleteDevices);
        foreach ($electricianData as $item) {
            mysqli_stmt_bind_param($stmtDeleteDevices, "ss", $item['name'], $item['phone']);
            mysqli_stmt_execute($stmtDeleteDevices);
        }
        mysqli_stmt_close($stmtDeleteDevices);

        echo json_encode(["status" => "success", "message" => "Selected electricians removed successfully."]);

    } else {
        echo json_encode(["status" => "error", "message" => "Invalid request."]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "Invalid request method."]);
}

mysqli_close($conn);
?>
