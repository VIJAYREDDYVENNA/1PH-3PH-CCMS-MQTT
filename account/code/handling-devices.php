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
$user_email = $sessionVars['user_email'];
$client_dashboard_login  = $sessionVars['client'];
$dashboard_version = $sessionVars['client_login'];

$permission_check = 0;

// Initialize the response array
//$userId ="2";
/*$devices="CCMS_49,CCMS_50";
$response = ["status" => "", "message" => ""];*/

// Check if the form was submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST' && $_POST['STATUS'] === "DELETE") {
    // Sanitize and validate input fields
    $devices = trim(filter_input(INPUT_POST, 'DEVICES', FILTER_SANITIZE_STRING));
    $userId = trim(filter_input(INPUT_POST, 'USERID', FILTER_SANITIZE_STRING));

    // Validation checks
    if (empty($devices)) {
        $response['status'] = 'error';
        $response['message'] = "Devices are required.";
        sendResponse($response);
    }

    if (empty($userId)) {
        $response['status'] = 'error';
        $response['message'] = "User_ID is required.";
        sendResponse($response);
    }

    // Proceed with database update if no errors
    $conn = mysqli_connect(HOST, USERNAME, PASSWORD, DB_USER);

    if (!$conn) {
        $response['status'] = 'error';
        $response['message'] = "Connection failed: " . mysqli_connect_error();
        sendResponse($response);
    } else {
        // Check user permissions...
        $sql = "SELECT user_details_updates FROM user_permissions WHERE login_id = ?";
        $stmt = mysqli_prepare($conn, $sql);
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "s", $user_login_id);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_bind_result($stmt, $permission_check);
            mysqli_stmt_fetch($stmt);
            mysqli_stmt_close($stmt);

            if ($permission_check != 1) {
                $response['status'] = 'error';
                $response["message"] = "This account doesn't have permission to update.";
                mysqli_close($conn);
                sendResponse($response);
            }
        } else {
            $response['status'] = 'error';
            $response["message"] = "Error preparing query for user permissions: " . mysqli_error($conn);
            mysqli_close($conn);
            sendResponse($response);
        }

        if ($permission_check == 1) {
            // Sanitize inputs
            $devices = sanitize_input($devices, $conn);
            $userId = sanitize_input($userId, $conn);

            // Convert devices to an array and prepare them for the SQL query
            $deviceArray = explode(',', $devices);
            $deviceArray = array_map('trim', $deviceArray); // Remove any extra spaces
            $devicePlaceholders = implode(',', array_fill(0, count($deviceArray), '?')); // Creates a string like "?, ?, ?"
            
            // Prepare SQL statement for delete
            $query = "DELETE FROM `user_device_list` WHERE login_id = ? AND device_id IN ($devicePlaceholders)";
            if ($stmt = mysqli_prepare($conn, $query)) {
                // Bind parameters to the prepared statement
                mysqli_stmt_bind_param($stmt, 's' . str_repeat('s', count($deviceArray)), $userId, ...$deviceArray);

                // Execute the statement
                if (mysqli_stmt_execute($stmt)) {
                    $response['status'] = 'success';
                    $response['message'] = "Devices deleted successfully.";
                } else {
                    $response['status'] = 'error';
                    $response['message'] = "Error deleting devices.";
                }

                // Close the statement
                mysqli_stmt_close($stmt);
            } else {
                $response['status'] = 'error';
                $response['message'] = "Error preparing statement.";
            }
        }

        // Close the database connection
        mysqli_close($conn);
    }

    // Output the JSON response
    sendResponse($response);
}
if ($_SERVER['REQUEST_METHOD'] == 'POST' && $_POST['STATUS'] === "ADD") {
    // Sanitize and validate input fields
    $devices = trim(filter_input(INPUT_POST, 'DEVICES', FILTER_SANITIZE_STRING));
    $userId = trim(filter_input(INPUT_POST, 'USERID', FILTER_SANITIZE_STRING));

    // Validation checks
    if (empty($devices)) {
        $response['status'] = 'error';
        $response['message'] = "Devices are required.";
        sendResponse($response);
    }

    if (empty($userId)) {
        $response['status'] = 'error';
        $response['message'] = "User_ID is required.";
        sendResponse($response);
    }

    // Proceed with database update if no errors
    $conn = mysqli_connect(HOST, USERNAME, PASSWORD, DB_USER);

    if (!$conn) {
        $response['status'] = 'error';
        $response['message'] = "Connection failed: " . mysqli_connect_error();
        sendResponse($response);
    } else {
        // Check user permissions...
        $sql = "SELECT user_details_updates FROM user_permissions WHERE login_id = ?";
        $stmt = mysqli_prepare($conn, $sql);
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "s", $user_login_id);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_bind_result($stmt, $permission_check);
            mysqli_stmt_fetch($stmt);
            mysqli_stmt_close($stmt);

            if ($permission_check != 1) {
                $response['status'] = 'error';
                $response["message"] = "This account doesn't have permission to update.";
                mysqli_close($conn);
                sendResponse($response);
            }
        } else {
            $response['status'] = 'error';
            $response["message"] = "Error preparing query for user permissions: " . mysqli_error($conn);
            mysqli_close($conn);
            sendResponse($response);
        }

        if ($permission_check == 1) {
            // Sanitize inputs
            $devices = sanitize_input($devices, $conn);
            $userId = sanitize_input($userId, $conn);

            // Convert devices to an array and prepare them for the SQL query
            $deviceArray = explode(',', $devices);
            $deviceArray = array_map('trim', $deviceArray); // Remove any extra spaces

            // Fetch the role from login_details table
            $roleQuery = "SELECT role FROM login_details WHERE id = ?";
            $stmt = mysqli_prepare($conn, $roleQuery);
            if ($stmt) {
                mysqli_stmt_bind_param($stmt, "s", $userId);
                mysqli_stmt_execute($stmt);
                mysqli_stmt_bind_result($stmt, $userRole);
                mysqli_stmt_fetch($stmt);
                mysqli_stmt_close($stmt);
            } else {
                $response['status'] = 'error';
                $response["message"] = "Error fetching user role: " . mysqli_error($conn);
                mysqli_close($conn);
                sendResponse($response);
            }

            // Check if devices exist for the current user ($user_login_id)
            $devicePlaceholders = implode(',', array_fill(0, count($deviceArray), '?'));
            $checkQuery = "SELECT `device_id`, `c_device_name`, `s_device_name`, `phase` FROM `user_device_list` WHERE login_id = ? AND device_id IN ($devicePlaceholders)";
            if ($stmt = mysqli_prepare($conn, $checkQuery)) {
                mysqli_stmt_bind_param($stmt, 's' . str_repeat('s', count($deviceArray)), $user_login_id, ...$deviceArray);
                mysqli_stmt_execute($stmt);
                $result = mysqli_stmt_get_result($stmt);

                $existingDevices = [];
                while ($row = mysqli_fetch_assoc($result)) {
                    $existingDevices[] = $row;
                }
                mysqli_stmt_close($stmt);

                if (!empty($existingDevices)) {
                    // Devices to be added to the new user ($userId)
                    $insertQuery = "INSERT INTO `user_device_list` (`device_id`, `c_device_name`, `s_device_name`, `role`, `login_id`, `phase`) VALUES ";
                    $values = [];
                    $types = '';
                    $params = [];

                    foreach ($existingDevices as $device) {
                        $values[] = "(?, ?, ?, ?, ?, ?)";
                        $types .= 'ssssss';
                        $params[] = $device['device_id'];
                        $params[] = $device['c_device_name'];
                        $params[] = $device['s_device_name'];
                        $params[] = $userRole; // Add the fetched role
                        $params[] = $userId; // Add the new user id
                        $params[] = $device['phase'];
                    }

                    $insertQuery .= implode(',', $values);

                    if ($stmt = mysqli_prepare($conn, $insertQuery)) {
                        mysqli_stmt_bind_param($stmt, $types, ...$params);

                        if (mysqli_stmt_execute($stmt)) {
                            $response['status'] = 'success';
                            $response['message'] = "Devices added successfully.";
                        } else {
                            $response['status'] = 'error';
                            $response['message'] = "Error adding devices.";
                        }

                        mysqli_stmt_close($stmt);
                    } else {
                        $response['status'] = 'error';
                        $response['message'] = "Error preparing statement for adding devices.";
                    }
                } else {
                    $response['status'] = 'error';
                    $response['message'] = "No matching devices found for the user.";
                }
            } else {
                $response['status'] = 'error';
                $response['message'] = "Error preparing statement for checking devices.";
            }
        }

        // Close the database connection
        mysqli_close($conn);
    }

    // Output the JSON response
    sendResponse($response);
}


if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['STATUS']) && $_POST['STATUS'] === "SYNC") {

    // Sanitize and validate input fields
    $devices = filter_input(INPUT_POST, 'DEVICES', FILTER_SANITIZE_STRING);
    $userId = filter_input(INPUT_POST, 'USERID', FILTER_SANITIZE_STRING);

    if (empty($devices) || empty($userId)) {
        $response['message'] = "Devices and User ID are required.";
        sendResponse($response);
    }

    $conn = mysqli_connect(HOST, USERNAME, PASSWORD, DB_USER);
    if (!$conn) {
        $response['message'] = "Database connection failed: " . mysqli_connect_error();
        sendResponse($response);
    }

    $devices = sanitize_input($devices, $conn);
    $userId = sanitize_input($userId, $conn);

    $deviceArray = array_map('trim', explode(',', $devices));

    if (empty($deviceArray)) {
        $response['message'] = "Invalid device list.";
        sendResponse($response);
    }

    // Prepare dynamic placeholders
    $placeholders = implode(',', array_fill(0, count($deviceArray), '?'));

    //$checkQuery = "SELECT `device_id`, `c_device_name`, `s_device_name`, `phase` FROM `user_device_list` WHERE `login_id` != ? AND `device_id` IN ($placeholders)";

      $checkQuery = "SELECT `device_id`, `c_device_name`, `s_device_name`, `phase` FROM `user_device_list` WHERE login_id = ? AND device_id IN ($placeholders)";

    if ($stmt = mysqli_prepare($conn, $checkQuery)) {
        mysqli_stmt_bind_param($stmt, 's' . str_repeat('s', count($deviceArray)), $userId, ...$deviceArray);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        $existingDevices = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $existingDevices[] = $row;
        }
        mysqli_stmt_close($stmt);

        if (!empty($existingDevices)) {
            $insertQuery = "INSERT IGNORE INTO `user_device_list`  (`device_id`, `c_device_name`, `s_device_name`, `role`, `login_id`, `phase`) VALUES ";
            
            $values = [];
            $types = '';
            $params = [];

            foreach ($existingDevices as $device) {
                $values[] = "(?, ?, ?, ?, ?, ?)";
                $types .= 'ssssss';
                array_push($params, $device['device_id'], $device['c_device_name'], $device['s_device_name'], $role, $user_login_id, $device['phase']);
            }

            $insertQuery .= implode(',', $values);

            if ($stmt = mysqli_prepare($conn, $insertQuery)) {
                mysqli_stmt_bind_param($stmt, $types, ...$params);

                if (mysqli_stmt_execute($stmt)) {


                    $group_by_column="device_group_or_area";
                    $query = "SELECT `group_by` FROM device_selection_group WHERE login_id = ?";
                    $stmt = mysqli_prepare($conn, $query);
                    mysqli_stmt_bind_param($stmt, "i", $user_login_id);  
                    mysqli_stmt_execute($stmt);
                    mysqli_stmt_bind_result($stmt, $group_by_column);
                    mysqli_stmt_fetch($stmt);
                    mysqli_stmt_close($stmt);

                    $sql_group_list = "SELECT `$group_by_column` AS `group_list`  FROM device_list_by_group  WHERE login_id = ?  GROUP BY `$group_by_column` ORDER BY `$group_by_column`";


                    $stmt = mysqli_prepare($conn, $sql_group_list);
                    mysqli_stmt_bind_param($stmt, "i", $user_login_id);

                    if (mysqli_stmt_execute($stmt)) {
                        $results = mysqli_stmt_get_result($stmt);
                        if (mysqli_num_rows($results) > 0) {
                            while ($r = mysqli_fetch_assoc($results)) {
                                $group_list[] = array("GROUP" => strtoupper($r['group_list']));
                            }
                        }
                    }

                    $_SESSION["GROUP_LIST"] = json_encode($group_list);
                    $response['status'] = 'success';
                    $response = ['status' => 'success', 'message' => "Devices added successfully."];
                } else {
                   $response['status'] = 'error';
                   $response['message'] = "Error adding devices.";
               }

               mysqli_stmt_close($stmt);
           } else {
             $response['status'] = 'error';
             $response['message'] = "Error preparing statement for adding devices.";
         }
     } else {
         $response['status'] = 'error';
         $response['message'] = "No matching devices found to add.";
     }
 } else {
     $response['status'] = 'error';
     $response['message'] = "Error preparing statement for checking devices.";
 }

 mysqli_close($conn);
 sendResponse($response);
}

function sendResponse($response) {
    header('Content-Type: application/json');
    echo json_encode($response);
    exit();
}

function sanitize_input($data, $conn) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return mysqli_real_escape_string($conn, $data);
}



?>