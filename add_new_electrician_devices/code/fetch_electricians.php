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

function sanitize_input($data, $conn) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return mysqli_real_escape_string($conn, $data);
}

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["group_id"])) {
    $group_id = $_POST['group_id'];
    include_once(BASE_PATH_1 . "common-files/selecting_group_device.php");

    if ($user_devices != "") {
        $user_devices = rtrim($user_devices, ",");
    }

    $conn = mysqli_connect(HOST, USERNAME, PASSWORD, DB_USER);
    if (!$conn) {
        die(json_encode(["status" => "error", "message" => "Connection failed: " . mysqli_connect_error()]));
    }

    $electricians = [];
    $temp_data = [];

    if (!empty($user_devices)) {
        // 1. Fetch electricians from electrician_devices using device_ids
        $sql = "SELECT DISTINCT electrician_name, phone_number FROM electrician_devices WHERE device_id IN ($user_devices)";
        $result = mysqli_query($conn, $sql);

        if ($result && mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
                $temp_data[] = [
                    'name' => $row['electrician_name'],
                    'phone' => $row['phone_number']
                ];
            }
        }
    }

    $fetched_phone_numbers = []; // to track already fetched

    // 2. If temp_data is still empty, fallback to electricians_list using login_id
    if (empty($temp_data)) {
        $fallback_query = "SELECT id, name, phone_number FROM electricians_list WHERE user_login_id = ?";
        $stmt = mysqli_prepare($conn, $fallback_query);
        mysqli_stmt_bind_param($stmt, "i", $user_login_id);

        if (mysqli_stmt_execute($stmt)) {
            $result = mysqli_stmt_get_result($stmt);
            if (mysqli_num_rows($result) > 0) {
                while ($row = mysqli_fetch_assoc($result)) {
                    $electricians[] = [
                        "id" => $row["id"],
                        "name" => $row["name"],
                        "phone" => $row["phone_number"]
                    ];
                    $fetched_phone_numbers[] = $row["phone_number"];
                }
            }
        }

        mysqli_stmt_close($stmt);
    } else {
        // 3. Get electrician IDs from electricians_list based on name & phone
        foreach ($temp_data as $data) {
            $name = sanitize_input($data['name'], $conn);
            $phone = sanitize_input($data['phone'], $conn);

            $query = "SELECT id, name, phone_number FROM electricians_list WHERE name = '$name' AND phone_number = '$phone'";
            $res = mysqli_query($conn, $query);
            if ($res && mysqli_num_rows($res) > 0) {
                $row = mysqli_fetch_assoc($res);
                $electricians[] = [
                    "id" => $row["id"],
                    "name" => $row["name"],
                    "phone" => $row["phone_number"]
                ];
                $fetched_phone_numbers[] = $row["phone_number"];
            }
        }

        // 4. Now fetch remaining electricians from list not already fetched
        if (!empty($fetched_phone_numbers)) {
            $escaped_numbers = implode("','", array_map(function ($num) use ($conn) {
                return mysqli_real_escape_string($conn, $num);
            }, $fetched_phone_numbers));

            $query_remaining = "SELECT id, name, phone_number FROM electricians_list 
                                WHERE user_login_id = '$user_login_id' 
                                AND phone_number NOT IN ('$escaped_numbers')";
        } else {
            $query_remaining = "SELECT id, name, phone_number FROM electricians_list 
                                WHERE user_login_id = '$user_login_id'";
        }

        $res_remaining = mysqli_query($conn, $query_remaining);
        if ($res_remaining && mysqli_num_rows($res_remaining) > 0) {
            while ($row = mysqli_fetch_assoc($res_remaining)) {
                $electricians[] = [
                    "id" => $row["id"],
                    "name" => $row["name"],
                    "phone" => $row["phone_number"]
                ];
            }
        }
    }

    mysqli_close($conn);
    echo json_encode($electricians);
} else {
    echo json_encode([]);
}
