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
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["group_id"])) {
    $group_id = $_POST['group_id'];
    include_once(BASE_PATH_1 . "common-files/selecting_group_device.php");
    if ($user_devices != "") {
        $user_devices = substr($user_devices, 0, -1);
    }

    $conn = mysqli_connect(HOST, USERNAME, PASSWORD, DB_USER);
    if (!$conn) {
        die(json_encode(["status" => "error", "message" => "Database connection failed."]));
    }

    $group_id = mysqli_real_escape_string($conn, $group_id);

    $electricians = [];
    $unassigned_devices = [];
    $group_areas = [];
    $group_areas_sql = "";
    $group_by = null;
    
    if ($group_id === "ALL") {
        // Fetch all electricians
        $sql_electricians = "SELECT id, electrician_name, phone_number, device_id FROM electrician_devices WHERE device_id IN ($user_devices)";
        $stmt = mysqli_prepare($conn, $sql_electricians);
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

        // Fetch unassigned devices
        $sql_devices = "SELECT DISTINCT device_id, c_device_name 
                        FROM user_device_list 
                        WHERE device_id NOT IN (
                            SELECT device_id FROM electrician_devices WHERE user_login_id = ?
                        )
                        GROUP BY device_id, c_device_name";
        $stmt = mysqli_prepare($conn, $sql_devices);
        mysqli_stmt_bind_param($stmt, "i", $user_login_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        while ($row = mysqli_fetch_assoc($result)) {
            $unassigned_devices[] = [
                "device_id" => $row["device_id"],
                "device_name" => $row["c_device_name"]
            ];
        }
        mysqli_stmt_close($stmt);
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
                mysqli_stmt_bind_param($stmt_area, "s", $group_id);
                mysqli_stmt_execute($stmt_area);
                $result = mysqli_stmt_get_result($stmt_area);
                
                // Build comma-separated quoted string for SQL IN clause
                $group_areas_sql = "";
                while ($row = mysqli_fetch_assoc($result)) {
                    $group_areas[] = $row['device_group_or_area'];
                    $group_areas_sql .= "'" . mysqli_real_escape_string($conn, $row['device_group_or_area']) . "',";
                }
                
                // Remove trailing comma
                if (!empty($group_areas_sql)) {
                    $group_areas_sql = rtrim($group_areas_sql, ',');
                }
                
                mysqli_stmt_close($stmt_area);
            }
        }
        
        // Only proceed if there are devices to query
        if (!empty($user_devices)) {
            // Use the group_areas_sql in the IN clause if available, otherwise use the original group_id
            if (!empty($group_areas_sql)) {
                $sql_electricians = "SELECT id, electrician_name, phone_number, device_id 
                                    FROM electrician_devices 
                                    WHERE group_area IN ($group_areas_sql) AND device_id IN ($user_devices)";
                $stmt = mysqli_prepare($conn, $sql_electricians);
                mysqli_stmt_execute($stmt);
            } else {
                $sql_electricians = "SELECT id, electrician_name, phone_number, device_id 
                                    FROM electrician_devices 
                                    WHERE group_area = ? AND device_id IN ($user_devices)";
                $stmt = mysqli_prepare($conn, $sql_electricians);
                mysqli_stmt_bind_param($stmt, "s", $group_id);
                mysqli_stmt_execute($stmt);
            }
            
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

        // Fetch unassigned devices for that group
        if (!empty($group_areas_sql)) {
            $sql_devices = "SELECT DISTINCT device_id, c_device_name 
                           FROM user_device_group_view 
                           WHERE device_group_or_area IN ($group_areas_sql) AND device_id NOT IN (
                               SELECT device_id 
                               FROM electrician_devices 
                               WHERE group_area IN ($group_areas_sql) AND user_login_id = ?
                           )";
            $stmt = mysqli_prepare($conn, $sql_devices);
            mysqli_stmt_bind_param($stmt, "i", $user_login_id);
        } else {
            $sql_devices = "SELECT DISTINCT device_id, c_device_name 
                           FROM user_device_group_view 
                           WHERE device_group_or_area = ? AND device_id NOT IN (
                               SELECT device_id 
                               FROM electrician_devices 
                               WHERE group_area = ? AND user_login_id = ?
                           )";
            $stmt = mysqli_prepare($conn, $sql_devices);
            mysqli_stmt_bind_param($stmt, "ssi", $group_id, $group_id, $user_login_id);
        }
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        while ($row = mysqli_fetch_assoc($result)) {
            $unassigned_devices[] = [
                "device_id" => $row["device_id"],
                "device_name" => $row["c_device_name"]
            ];
        }
        mysqli_stmt_close($stmt);
    }

    mysqli_close($conn);

    echo json_encode([
        "group_by" => $group_by,
        "group_areas" => $group_areas,
        "electricians" => $electricians,
        "unassigned_devices" => $unassigned_devices
    ]);
} else {
    echo json_encode([]);
}