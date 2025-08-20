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
$permission_check =0;


/*$device_id ="CCMS_1";
$brandName ="Bajaj";
$wattage = 60;
$lights = 125;
$db=strtolower($device_id);*/

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
    $brandName = mysqli_real_escape_string($conn1, $_POST['BRAND']);
    $wattage = mysqli_real_escape_string($conn1, $_POST['WATT']);
    $lights = mysqli_real_escape_string($conn1, $_POST['LIGHTS']);
    $db=strtolower($device_id);


    $totalWatts = $lights * $wattage;

    // Check user permissions
    $sql = "SELECT lights_info_update FROM user_permissions WHERE login_id = ?";
    $stmt = mysqli_prepare($conn1, $sql);
    if ($stmt) 
    {
        mysqli_stmt_bind_param($stmt, "s", $user_login_id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_bind_result($stmt, $permission_check);
        mysqli_stmt_fetch($stmt);
        mysqli_stmt_close($stmt);
        mysqli_close($conn1);
        if ($permission_check != 1) 
        {
            echo json_encode(["status" => "error", "message" => "No permission to add the device"]);
            mysqli_close($conn1);
            exit();
        }
    } 
    else 
    {
        echo json_encode(["status" => "error", "message" => "Error preparing query for user permissions: " . mysqli_error($conn1)]);
        mysqli_close($conn1);
        exit();
    }
    if($permission_check==1)
    {
        $conn = mysqli_connect(HOST, USERNAME, PASSWORD, $db);
        if (!$conn) {
            echo json_encode(["status" => "error", "message" => "Connection to second database failed: " . mysqli_connect_error()]);
            mysqli_close($conn);
            exit();
        }

        $sql_sum = "SELECT SUM(total_lights) AS total_lights_sum, SUM(total_wattage) AS total_wattage_sum FROM installed_lights_info WHERE add_or_removed = 1";
        $result_sum = mysqli_query($conn, $sql_sum);
        $row_sum = mysqli_fetch_assoc($result_sum);
        $total_lights_sum = (int) $row_sum['total_lights_sum'];
        $total_wattage_sum = (int) $row_sum['total_wattage_sum'];


    // Prepare an insert statement for the first database with additional fields
        $sql1 = "INSERT INTO installed_lights_info ( device_id, brand_name, wattage, total_lights, total_wattage, add_or_removed, user_id, user_mobile, user_name, role) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt1 = mysqli_prepare($conn, $sql1);

        if ($stmt1) {
            $add_or_removed = 1;
            mysqli_stmt_bind_param($stmt1, "ssiiiiisss", $device_id, $brandName, $wattage, $lights, $totalWatts, $add_or_removed, $user_id, $mobile_no, $user_name, $role);

            if (mysqli_stmt_execute($stmt1)) {
                // Create connection to the second database
                $conn2 = mysqli_connect(HOST, USERNAME, PASSWORD, DB_ALL);
                if (!$conn2) {
                    echo json_encode(["status" => "error", "message" => "Connection to second database failed: " . mysqli_connect_error()]);
                    mysqli_close($conn2);
                    exit();
                }

                $lights=$lights+$total_lights_sum;
                $totalWatts=$totalWatts+$total_wattage_sum;

                // Prepare an insert/update statement for the second database
                $sql2 = "INSERT INTO live_data_updates (device_id, lights_wattage, total_lights)  VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE  lights_wattage = VALUES(lights_wattage),  total_lights = VALUES(total_lights)";
                $stmt2 = mysqli_prepare($conn2, $sql2);

                if ($stmt2) {
                    mysqli_stmt_bind_param($stmt2, "sii", $device_id, $totalWatts, $lights);

                    if (mysqli_stmt_execute($stmt2)) {
                        echo json_encode(["status" => "success", "message" => "Details added and updated successfully."]);
                    } else {
                        echo json_encode(["status" => "error", "message" => "Error updating live_data_updates: " . mysqli_stmt_error($stmt2)]);
                    }

                    mysqli_stmt_close($stmt2);
                } else {
                    echo json_encode(["status" => "error", "message" => "Error preparing query for live_data_updates: " . mysqli_error($conn2)]);
                }

                mysqli_close($conn2);
            } else {
                echo json_encode(["status" => "error", "message" => "Error inserting into installed_lights_info: " . mysqli_stmt_error($stmt1)]);
            }

            mysqli_stmt_close($stmt1);
        } else {
            echo json_encode(["status" => "error", "message" => "Error preparing query for installed_lights_info: " . mysqli_error($conn)]);
        }

        mysqli_close($conn);
    }
} else {
    echo json_encode(["status" => "error", "message" => "Invalid request method."]);
}
?>
