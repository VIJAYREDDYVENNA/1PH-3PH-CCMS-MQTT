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
$user_name = $sessionVars['user_name']; 
$permission_check = 0;

// Check if the request is POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Create connection to the first database
    $conn1 = mysqli_connect(HOST, USERNAME, PASSWORD, DB_USER);
    if (!$conn1) {
        echo json_encode(["status" => "error", "message" => "Connection to the first database failed: " . mysqli_connect_error()]);
        mysqli_close($conn1);
        exit();
    }

    // Get POST parameters and sanitize
    $device_id = mysqli_real_escape_string($conn1, $_POST['D_ID']);
    $record_id = mysqli_real_escape_string($conn1, $_POST['RECORD']);    
    $db = strtolower($device_id);

    // Check user permissions
    $sql = "SELECT lights_info_update FROM user_permissions WHERE login_id = ?";
    $stmt = mysqli_prepare($conn1, $sql);
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "s", $user_login_id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_bind_result($stmt, $permission_check);
        mysqli_stmt_fetch($stmt);
        mysqli_stmt_close($stmt);

        if ($permission_check != 1) {
            echo json_encode(["status" => "error", "message" => "No permission to delete the device record"]);
            mysqli_close($conn1);
            exit();
        }
    } else {
        echo json_encode(["status" => "error", "message" => "Something went wrong, try again: " . mysqli_error($conn1)]);
        mysqli_close($conn1);
        exit();
    }

    if ($permission_check == 1) {
        // Connect to the target database
        $conn = mysqli_connect(HOST, USERNAME, PASSWORD, $db);
        if (!$conn) {
            echo json_encode(["status" => "error", "message" => "Connection to second database failed: " . mysqli_connect_error()]);
            mysqli_close($conn);
            exit();
        }

        // Delete the record
        $sql_delete = "DELETE FROM installed_lights_info WHERE id = ?";
        $stmt_delete = mysqli_prepare($conn, $sql_delete);
        if ($stmt_delete) {
            mysqli_stmt_bind_param($stmt_delete, "i", $record_id);

            if (mysqli_stmt_execute($stmt_delete)) {
                // Calculate the sum of total_lights and total_wattage
                $sql_sum = "SELECT SUM(total_lights) AS total_lights_sum, SUM(total_wattage) AS total_wattage_sum FROM installed_lights_info WHERE add_or_removed = 1";
                $result_sum = mysqli_query($conn, $sql_sum);
                $row_sum = mysqli_fetch_assoc($result_sum);
                $total_lights_sum = (int) $row_sum['total_lights_sum'];
                $total_wattage_sum = (int) $row_sum['total_wattage_sum'];

                // Update the live_data_updates table
                $conn2 = mysqli_connect(HOST, USERNAME, PASSWORD, DB_ALL);
                if (!$conn2) {
                    echo json_encode(["status" => "error", "message" => "Connection to third database failed: " . mysqli_connect_error()]);
                    mysqli_close($conn);
                    exit();
                }

                $sql_update = "INSERT INTO live_data_updates (device_id, lights_wattage, total_lights) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE lights_wattage = VALUES(lights_wattage), total_lights = VALUES(total_lights)";
                $stmt_update = mysqli_prepare($conn2, $sql_update);

                if ($stmt_update) {
                    mysqli_stmt_bind_param($stmt_update, "sii", $device_id, $total_wattage_sum, $total_lights_sum);

                    if (mysqli_stmt_execute($stmt_update)) {
                        echo json_encode(["status" => "success", "message" => "Lights details removed successfully."]);
                    } else {
                        echo json_encode(["status" => "error", "message" => "Error updating lights status"]);
                    }

                    mysqli_stmt_close($stmt_update);
                } else {
                    echo json_encode(["status" => "error", "message" => "Something went wrong"]);
                }

                mysqli_close($conn2);
            } else {
                echo json_encode(["status" => "error", "message" => "Error removing record."]);
            }

            mysqli_stmt_close($stmt_delete);
        } else {
            echo json_encode(["status" => "error", "message" => "Error preparing Remove the record"]);
        }

        mysqli_close($conn);
    }

    mysqli_close($conn1);
} else {
    echo json_encode(["status" => "error", "message" => "Invalid request method."]);
}
?>
