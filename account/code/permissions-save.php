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

$permission_check = 0;

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['user_id'], $_POST['permissions'])) {
   $userId = filter_input(INPUT_POST, 'user_id', FILTER_SANITIZE_NUMBER_INT);   // Sanitize the user ID
    $permissions = $_POST['permissions'];  // Array of permissions

    // Database connection
    $conn = mysqli_connect(HOST, USERNAME, PASSWORD, DB_USER);

    if (!$conn) {
        $response = ['status' => 'error', 'message' => "Connection failed: " . mysqli_connect_error()];
        sendResponse($response);
    }
    $sql = "SELECT `user_permissions` FROM user_permissions WHERE login_id = ?";
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
        $userId = sanitize_input($userId, $conn);
    // Sanitize the permissions array
        $sanitizedPermissions = sanitize_permissions($permissions, $conn);

    // Check if user permissions exist for this user
        $checkQuery = "SELECT COUNT(*) FROM user_permissions WHERE login_id = ?";
        $stmt = mysqli_prepare($conn, $checkQuery);
        mysqli_stmt_bind_param($stmt, 'i', $userId);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_bind_result($stmt, $exists);
        mysqli_stmt_fetch($stmt);
        mysqli_stmt_close($stmt);

    // If permissions exist, update them, otherwise insert new ones
        if ($exists) {
        // Build the dynamic UPDATE query
            $updateParts = [];
            $updateValues = [];

            foreach ($sanitizedPermissions as $field => $value) {
                $updateParts[] = "$field = ?";
                $updateValues[] = $value;
            }

            $updateQuery = "UPDATE user_permissions SET " . implode(', ', $updateParts) . " WHERE login_id = ?";
            $updateStmt = mysqli_prepare($conn, $updateQuery);

        // Bind parameters dynamically
        $bindTypes = str_repeat('i', count($sanitizedPermissions)) . 'i';  // All values are integers, plus the user ID
        $updateValues[] = $userId;
        mysqli_stmt_bind_param($updateStmt, $bindTypes, ...$updateValues);

        if (mysqli_stmt_execute($updateStmt)) {
            $response = ['status' => 'success', 'message' => 'Permissions updated successfully.'];
        } else {
            $response = ['status' => 'error', 'message' => 'Failed to update permissions.'];
        }

        mysqli_stmt_close($updateStmt);
    } else {
        // Build the dynamic INSERT query
        $fields = implode(', ', array_keys($sanitizedPermissions));
        $placeholders = implode(', ', array_fill(0, count($sanitizedPermissions), '?'));
        $insertQuery = "INSERT INTO user_permissions (login_id, $fields) VALUES (?, $placeholders)";
        $insertStmt = mysqli_prepare($conn, $insertQuery);

        // Bind parameters dynamically
        $bindTypes = 'i' . str_repeat('i', count($sanitizedPermissions));  // First parameter is the user ID, the rest are integers
        $insertValues = array_merge([$userId], array_values($sanitizedPermissions));
        mysqli_stmt_bind_param($insertStmt, $bindTypes, ...$insertValues);

        if (mysqli_stmt_execute($insertStmt)) {
            $response = ['status' => 'success', 'message' => 'Permissions saved successfully.'];
        } else {
            $response = ['status' => 'error', 'message' => 'Failed to insert permissions.'];
        }

        mysqli_stmt_close($insertStmt);
    }

    mysqli_close($conn);
    sendResponse($response);
}

}

function sendResponse($response) {
    header('Content-Type: application/json');
    echo json_encode($response);
    exit();
}

// Sanitize user input to prevent SQL injection

function sanitize_input($data, $conn) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return mysqli_real_escape_string($conn, $data);
}

// Sanitize the permissions array
function sanitize_permissions($permissions, $conn) {
    $sanitizedPermissions = [];

    // Whitelist of valid permission fields (optional, to avoid SQL injection through field names)
    $validFields = ['on_off_control', 'on_off_mode', 'device_info_update', 'threshold_settings', 'iot_settings', 'lights_info_update', 'device_add_remove', 'user_details_updates', 'create_group','add_remove_electrician', 'notification_update', 'installation_status_update', 'download_data', 'user_permissions'];

    foreach ($permissions as $field => $value) {
        // Sanitize the field name and value
        $field = mysqli_real_escape_string($conn, $field);
        $value = mysqli_real_escape_string($conn, $value);

        // Optionally, check if the field is in the whitelist
        if (in_array($field, $validFields)) {
            $sanitizedPermissions[$field] = $value;
        }
    }
    return $sanitizedPermissions;
}
?>
