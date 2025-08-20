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
$user_devices = "";
header('Content-Type: application/json');

function sanitize_input($data, $conn)
{
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return mysqli_real_escape_string($conn, $data);
}

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["group_id"])) {
    $group_id = $_POST['group_id'];
    $searchTerm = isset($_POST['searchTerm']) ? trim($_POST['searchTerm']) : '';
    
    $conn = mysqli_connect(HOST, USERNAME, PASSWORD, DB_USER);
    
    if (!$conn) {
        die(json_encode(["status" => "error", "message" => "Connection failed: " . mysqli_connect_error()]));
    }

    $group_id = sanitize_input($group_id, $conn);
    $searchTerm = sanitize_input($searchTerm, $conn);

    $electricians = [];
    $group_areas = [];
    $group_by = null;

    if ($group_id === "ALL") {
        // Fetch all electricians with proper LIKE search
        if (!empty($searchTerm)) {
            $sql_electricians = "SELECT id, electrician_name, phone_number, device_id 
                               FROM electrician_devices 
                               WHERE device_id LIKE ? OR electrician_name LIKE ?";
            $stmt = mysqli_prepare($conn, $sql_electricians);
            
            if ($stmt) {
                $search_param = '%' . $searchTerm . '%';
                mysqli_stmt_bind_param($stmt, "ss", $search_param, $search_param);
                mysqli_stmt_execute($stmt);
                $result = mysqli_stmt_get_result($stmt);

                while ($row = mysqli_fetch_assoc($result)) {
                    $electricians[] = [
                        "id" => $row["id"],
                        "name" => $row["electrician_name"],
                        "phone" => $row["phone_number"],
                        "device_id" => $row["device_id"]
                    ];
                }
                mysqli_stmt_close($stmt);
            }
        } else {
            // If no search term, fetch all electricians
            $sql_electricians = "SELECT id, electrician_name, phone_number, device_id FROM electrician_devices";
            $stmt = mysqli_prepare($conn, $sql_electricians);
            
            if ($stmt) {
                mysqli_stmt_execute($stmt);
                $result = mysqli_stmt_get_result($stmt);

                while ($row = mysqli_fetch_assoc($result)) {
                    $electricians[] = [
                        "id" => $row["id"],
                        "name" => $row["electrician_name"],
                        "phone" => $row["phone_number"],
                        "device_id" => $row["device_id"]
                    ];
                }
                mysqli_stmt_close($stmt);
            }
        }
    } else {
        // Get the group_by value first
        $sql_group = "SELECT group_by FROM device_selection_group WHERE login_id = ?";
        $stmt_group = mysqli_prepare($conn, $sql_group);

        if ($stmt_group) {
            mysqli_stmt_bind_param($stmt_group, "i", $user_login_id);
            mysqli_stmt_execute($stmt_group);
            $result = mysqli_stmt_get_result($stmt_group);
            if ($row = mysqli_fetch_assoc($result)) {
                $group_by = $row['group_by'];
            }
            mysqli_stmt_close($stmt_group);
        }

        if ($group_by !== "device_group_or_area") {
            $sql_group_area = "";

            switch ($group_by) {
                case "state":
                    $sql_group_area = "SELECT DISTINCT device_group_or_area FROM user_device_group_view WHERE state = ?";
                    break;
                case "district":
                    $sql_group_area = "SELECT DISTINCT device_group_or_area FROM user_device_group_view WHERE district = ?";
                    break;
                case "city_or_town":
                    $sql_group_area = "SELECT DISTINCT device_group_or_area FROM user_device_group_view WHERE city_or_town = ?";
                    break;
            }

            if (!empty($sql_group_area)) {
                $stmt_area = mysqli_prepare($conn, $sql_group_area);
                if ($stmt_area) {
                    mysqli_stmt_bind_param($stmt_area, "s", $group_id);
                    mysqli_stmt_execute($stmt_area);
                    $result = mysqli_stmt_get_result($stmt_area);

                    while ($row = mysqli_fetch_assoc($result)) {
                        $group_areas[] = $row['device_group_or_area'];
                    }
                    mysqli_stmt_close($stmt_area);
                }
            }
        }

        // Build the SQL query based on group areas or direct group_id
        if (!empty($group_areas)) {
            // Create placeholders for prepared statement
            $placeholders = str_repeat('?,', count($group_areas) - 1) . '?';
            
            if (!empty($searchTerm)) {
                $sql_electricians = "SELECT id, electrician_name, phone_number, device_id 
                                   FROM electrician_devices 
                                   WHERE group_area IN ($placeholders) 
                                   AND (device_id LIKE ? OR electrician_name LIKE ?)";
                
                $stmt = mysqli_prepare($conn, $sql_electricians);
                if ($stmt) {
                    // Create type string and values array
                    $types = str_repeat('s', count($group_areas)) . 'ss';
                    $search_param = '%' . $searchTerm . '%';
                    $values = array_merge($group_areas, [$search_param, $search_param]);
                    
                    mysqli_stmt_bind_param($stmt, $types, ...$values);
                    mysqli_stmt_execute($stmt);
                    $result = mysqli_stmt_get_result($stmt);

                    while ($row = mysqli_fetch_assoc($result)) {
                        $electricians[] = [
                            "id" => $row["id"],
                            "name" => $row["electrician_name"],
                            "phone" => $row["phone_number"],
                            "device_id" => $row["device_id"]
                        ];
                    }
                    mysqli_stmt_close($stmt);
                }
            } else {
                // No search term, just filter by group areas
                $sql_electricians = "SELECT id, electrician_name, phone_number, device_id 
                                   FROM electrician_devices 
                                   WHERE group_area IN ($placeholders)";
                
                $stmt = mysqli_prepare($conn, $sql_electricians);
                if ($stmt) {
                    $types = str_repeat('s', count($group_areas));
                    mysqli_stmt_bind_param($stmt, $types, ...$group_areas);
                    mysqli_stmt_execute($stmt);
                    $result = mysqli_stmt_get_result($stmt);

                    while ($row = mysqli_fetch_assoc($result)) {
                        $electricians[] = [
                            "id" => $row["id"],
                            "name" => $row["electrician_name"],
                            "phone" => $row["phone_number"],
                            "device_id" => $row["device_id"]
                        ];
                    }
                    mysqli_stmt_close($stmt);
                }
            }
        } else {
            // Use direct group_id
            if (!empty($searchTerm)) {
                $sql_electricians = "SELECT id, electrician_name, phone_number, device_id 
                                   FROM electrician_devices 
                                   WHERE group_area = ? 
                                   AND (device_id LIKE ? OR electrician_name LIKE ?)";
                
                $stmt = mysqli_prepare($conn, $sql_electricians);
                if ($stmt) {
                    $search_param = '%' . $searchTerm . '%';
                    mysqli_stmt_bind_param($stmt, "sss", $group_id, $search_param, $search_param);
                    mysqli_stmt_execute($stmt);
                    $result = mysqli_stmt_get_result($stmt);

                    while ($row = mysqli_fetch_assoc($result)) {
                        $electricians[] = [
                            "id" => $row["id"],
                            "name" => $row["electrician_name"],
                            "phone" => $row["phone_number"],
                            "device_id" => $row["device_id"]
                        ];
                    }
                    mysqli_stmt_close($stmt);
                }
            } else {
                // No search term, just filter by group_id
                $sql_electricians = "SELECT id, electrician_name, phone_number, device_id 
                                   FROM electrician_devices 
                                   WHERE group_area = ?";
                
                $stmt = mysqli_prepare($conn, $sql_electricians);
                if ($stmt) {
                    mysqli_stmt_bind_param($stmt, "s", $group_id);
                    mysqli_stmt_execute($stmt);
                    $result = mysqli_stmt_get_result($stmt);

                    while ($row = mysqli_fetch_assoc($result)) {
                        $electricians[] = [
                            "id" => $row["id"],
                            "name" => $row["electrician_name"],
                            "phone" => $row["phone_number"],
                            "device_id" => $row["device_id"]
                        ];
                    }
                    mysqli_stmt_close($stmt);
                }
            }
        }
    }

    mysqli_close($conn);

    echo json_encode([
        "status" => "success",
        "electricians" => $electricians,
        "total_results" => count($electricians)
    ]);
} else {
    echo json_encode([
        "status" => "error",
        "message" => "Invalid request parameters",
        "electricians" => []
    ]);
}