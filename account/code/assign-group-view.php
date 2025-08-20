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


$response = ["status" => "", "message" => ""];

// Check if the form was submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Sanitize and validate input fields
    $group = trim(filter_input(INPUT_POST, 'group', FILTER_SANITIZE_STRING));
    $userId = trim(filter_input(INPUT_POST, 'user_id', FILTER_SANITIZE_STRING));

    // Validation checks
    if (empty($group)) {
        $response['status'] = 'error';
        $response['message'] = "Group is empty.";
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
        if ($stmt) 
        {
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
            $group = sanitize_input($group, $conn);
            $userId = sanitize_input($userId, $conn);
            if($group==="device_group_or_area"||$group==="city_or_town"||$group==="district"||$group==="state")
            {
                $validate_by_user_group="device_group_or_area";
                $sql = "SELECT group_by FROM device_selection_group WHERE login_id = ?";
                $stmt = mysqli_prepare($conn, $sql);
                if ($stmt) 
                {
                    mysqli_stmt_bind_param($stmt, "s", $user_login_id);
                    mysqli_stmt_execute($stmt);
                    mysqli_stmt_bind_result($stmt, $validate_by_user_group);
                    mysqli_stmt_fetch($stmt);
                    mysqli_stmt_close($stmt);
                }

                if($role!="SUPERADMIN")
                {

                    if($validate_by_user_group==="device_group_or_area")
                    {
                        if($group==="city_or_town"||$group==="district"||$group==="state") 
                        {

                            $response['status'] = 'error';
                            $response["message"] = "Not allowed to assign this group";
                            mysqli_close($conn);
                            sendResponse($response);
                        }
                    }
                    if($validate_by_user_group==="city_or_town")
                    {
                        if($group==="district"||$group==="state") 
                        {

                            $response['status'] = 'error';
                            $response["message"] = "Not allowed to assign this group";
                            mysqli_close($conn);
                            sendResponse($response);
                        }
                    }
                    if($validate_by_user_group==="district")
                    {
                        if($group==="state") 
                        {

                            $response['status'] = 'error';
                            $response["message"] = "Not allowed to assign this group";
                            mysqli_close($conn);
                            sendResponse($response);
                        }
                    }
                }


            }
            else
            {
                $response['status'] = 'error';
                $response["message"] = "Selected group is Invalid";
                mysqli_close($conn);
                sendResponse($response);
            }

            $query = "INSERT INTO `device_selection_group` (`login_id`, `group_by`) 
            VALUES (?, ?) 
            ON DUPLICATE KEY UPDATE `group_by` = VALUES(`group_by`)";

            if ($stmt = mysqli_prepare($conn, $query)) {
            // Bind parameters to the prepared statement
                mysqli_stmt_bind_param($stmt, 'ss', $userId, $group);

                // Execute the statement
                if (mysqli_stmt_execute($stmt)) {
                    $response['status'] = 'success';
                    $response['message'] = "Device group saved successfully.";
                } else {
                    $response['status'] = 'error';
                    $response['message'] = "Error saving device group.";
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
