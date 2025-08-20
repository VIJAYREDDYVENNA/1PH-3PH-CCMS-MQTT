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
if ($_SERVER['REQUEST_METHOD'] == 'POST' && $_POST['UPDATE']==="EDIT") {
    // Sanitize and validate input fields
    $userName = trim(filter_input(INPUT_POST, 'USERNAME', FILTER_SANITIZE_STRING));
    $userId = trim(filter_input(INPUT_POST, 'USERID', FILTER_SANITIZE_STRING));
    $userRole = trim(filter_input(INPUT_POST, 'USERROLE', FILTER_SANITIZE_STRING));
    $userEmail = trim(filter_input(INPUT_POST, 'USEREMAIL', FILTER_SANITIZE_EMAIL));
    $userMobile = trim(filter_input(INPUT_POST, 'USERMOBILE', FILTER_SANITIZE_NUMBER_INT));
    $id = trim(filter_input(INPUT_POST, 'ID', FILTER_SANITIZE_NUMBER_INT));

    // Validation checks
    if (empty($userName)) {
        $response['status'] = 'error';
        $response['message'] = "Name field is required.";
        sendResponse($response);
    }

    if (empty($userId)) {
        $response['status'] = 'error';
        $response['message'] = "User_ID field is required.";
        sendResponse($response);
    }

    if (empty($userRole)) {
        $response['status'] = 'error';
        $response['message'] = "User_Role field is required.";
        sendResponse($response);
    }

    if (empty($userEmail)) {
        $response['status'] = 'error';
        $response['message'] = "Email field is required.";
        sendResponse($response);
    } elseif (!filter_var($userEmail, FILTER_VALIDATE_EMAIL)) {
        $response['status'] = 'error';
        $response['message'] = "Invalid email format.";
        sendResponse($response);
    }

    if (empty($userMobile)) {
        $response['status'] = 'error';
        $response['message'] = "Mobile field is required.";
        sendResponse($response);
    } elseif (strlen($userMobile) != 10 || !ctype_digit($userMobile)) {
        $response['status'] = 'error';
        $response['message'] = "Please enter a valid 10-digit mobile number.";
        sendResponse($response);
    }

    // Proceed with database update if no errors
    $conn = mysqli_connect(HOST, USERNAME, PASSWORD, DB_USER);

    if (!$conn) {
        $response['status'] = 'error';
        $response['message'] = "Connection failed: " . mysqli_connect_error();
        sendResponse($response);
    } else {
        // Check user permissions
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

        // If the user has permission, proceed with the uniqueness check and update
        if ($permission_check == 1) {
            // Sanitize inputs
            $userName = sanitize_input($userName, $conn);
            $userId = sanitize_input($userId, $conn);
            $userRole = sanitize_input($userRole, $conn);
            $userEmail = sanitize_input($userEmail, $conn);
            $userMobile = sanitize_input($userMobile, $conn);
            $id = sanitize_input($id, $conn);

            // Check for unique email, mobile, and user_id
            $checkQuery = "SELECT COUNT(*) AS count FROM login_details WHERE (email_id = ? OR mobile_no = ? OR user_id = ?) AND id != ?";
            $checkStmt = mysqli_prepare($conn, $checkQuery);
            if ($checkStmt) {
                mysqli_stmt_bind_param($checkStmt, 'sssi', $userEmail, $userMobile, $userId, $id);
                mysqli_stmt_execute($checkStmt);
                mysqli_stmt_bind_result($checkStmt, $count);
                mysqli_stmt_fetch($checkStmt);
                mysqli_stmt_close($checkStmt);

                if ($count > 0) {
                    $response['status'] = 'error';
                    $response['message'] = "Email, mobile number, or user ID already exists.";
                    mysqli_close($conn);
                    sendResponse($response);
                }



//////////////////////////////////////////////////////////////////////

                $prev_role = "";
                $prev_status = "";
                $checkQueryRole = "SELECT role, status FROM login_details WHERE id = ?"; 
                $checkStmtRole = mysqli_prepare($conn, $checkQueryRole);
                if ($checkStmtRole) {
                    mysqli_stmt_bind_param($checkStmtRole, 'i', $id);
                    mysqli_stmt_execute($checkStmtRole);
                    mysqli_stmt_bind_result($checkStmtRole, $prev_role, $prev_status);
                    mysqli_stmt_fetch($checkStmtRole);
                    mysqli_stmt_close($checkStmtRole);
                } else {
                    $response['status'] = 'error';
                    $response['message'] = "Error preparing statement for comparing the previous role and status.";
                    mysqli_close($conn);
                    sendResponse($response);  
                }

                $account_confirmation="ACTIVE";
                $msg="User details updated successfully.";
                if( $prev_status=="HOLD")
                {
                    $account_confirmation="HOLD";
                    if( $userRole==="SUPERADMIN" && $prev_role!=="SUPERADMIN"){

                        $msg="User details updated successfully, but the account is currently on hold. Please contact ISTL for activation.";
                    }
                }
                else
                {
                    if( $userRole==="SUPERADMIN" && $prev_role!=="SUPERADMIN")
                    {
                        $account_confirmation="HOLD";
                        $msg="The user details have been successfully updated; however, the account is now on hold because a role cannot be upgraded to SUPERADMIN. Please contact ISTL for reactivation.";
                    }
                }
 ///////////////////////////////////////////////////////////////////




                // Prepare SQL statement for update
                $query = "UPDATE login_details SET name = ?, user_id = ?, role = ?, status=?, email_id = ?, mobile_no = ? WHERE id = ?";
                if ($stmt = mysqli_prepare($conn, $query)) {
                    // Bind parameters to the prepared statement
                    mysqli_stmt_bind_param($stmt, 'ssssssi', $userName, $userId, $userRole, $account_confirmation, $userEmail, $userMobile, $id);

                    // Execute the statement
                    if (mysqli_stmt_execute($stmt)) {
                        $response['status'] = 'success';
                        $response['message'] = $msg;
                    } else {
                        $response['status'] = 'error';
                        $response['message'] = "Error updating record. ";
                    }

                    // Close the statement
                    mysqli_stmt_close($stmt);
                } else {
                    $response['status'] = 'error';
                    $response['message'] = "Error preparing statement. ";
                }
            } else {
                $response['status'] = 'error';
                $response['message'] = "Error preparing uniqueness check.";
            }
        } 

        // Close the database connection
        mysqli_close($conn);
    }

    // Output the JSON response
    sendResponse($response);
}
else if ($_SERVER['REQUEST_METHOD'] == 'POST' && $_POST['UPDATE']==="DELETE") 
{
    $userId = trim(filter_input(INPUT_POST, 'USERID', FILTER_SANITIZE_STRING));
    $userMobile = trim(filter_input(INPUT_POST, 'USERMOBILE', FILTER_SANITIZE_NUMBER_INT));

    $conn = mysqli_connect(HOST, USERNAME, PASSWORD, DB_USER);
    if (!$conn) {
        $response['status'] = 'error';
        $response['message'] = "Connection failed: " . mysqli_connect_error();
        sendResponse($response);

    } 

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

        $userId = sanitize_input($userId, $conn);
        $userMobile = sanitize_input($userMobile, $conn);
        $query = "UPDATE login_details SET account_delete = ? WHERE id = ? AND mobile_no = ?";
        $stmt = mysqli_prepare($conn, $query);
        if ($stmt) {

            $accountDelete = "0";
            mysqli_stmt_bind_param($stmt, 'sss', $accountDelete, $userId, $userMobile);


            if (mysqli_stmt_execute($stmt)) {
                $response['status'] = 'success';
                $response['message'] = "Account deleted successfully.";
            } else {
                $response['status'] = 'error';
                $response['message'] = "Error updating record: " . mysqli_stmt_error($stmt);
            }


            mysqli_stmt_close($stmt);
        } else {
            $response['status'] = 'error';
            $response['message'] = "Error preparing statement: " . mysqli_error($conn);
        }
    }


    mysqli_close($conn);
    sendResponse($response);


}
else if ($_SERVER['REQUEST_METHOD'] == 'POST' && $_POST['UPDATE'] === "NEW_USER") {
    // Sanitize and validate input fields
    $userName = trim(filter_input(INPUT_POST, 'USERNAME', FILTER_SANITIZE_STRING));
    $userId = trim(filter_input(INPUT_POST, 'USERID', FILTER_SANITIZE_STRING));
    $userRole = trim(filter_input(INPUT_POST, 'USERROLE', FILTER_SANITIZE_STRING));
    $userEmail = trim(filter_input(INPUT_POST, 'USEREMAIL', FILTER_SANITIZE_EMAIL));
    $userMobile = trim(filter_input(INPUT_POST, 'USERMOBILE', FILTER_SANITIZE_NUMBER_INT));
    $login_page = trim(filter_input(INPUT_POST, 'LOGIN_PAGE', FILTER_SANITIZE_STRING));

    $newPassword = trim(filter_input(INPUT_POST, 'PASSWORD', FILTER_SANITIZE_STRING));
    $reenterPassword = trim(filter_input(INPUT_POST, 'REENTERPASSWORD', FILTER_SANITIZE_STRING));


    //////////////////////////////////////////////////////////////////////////////////////
    /*$userName ="SWAMY" ;
    $userId = "SWAMY_123344";
    $userRole = "SUPERADMIN";
    $userEmail = "sw1@gmail.com";
    $userMobile ="8801111151";
    $login_page = "ISTL";

    $newPassword = "Swamy@123456";
    $reenterPassword ="Swamy@123456" ;*/
    //////////////////////////////////////////////////////////////////////////////////////

    // Validation checks
    if (empty($userName)) {
        $response['status'] = 'error';
        $response['message'] = "Name field is required.";
        sendResponse($response);
    }

    if (empty($userId)) {
        $response['status'] = 'error';
        $response['message'] = "User_ID field is required.";
        sendResponse($response);
    }

    if (empty($userRole)) {
        $response['status'] = 'error';
        $response['message'] = "User_Role field is required.";
        sendResponse($response);
    }

    if (empty($userEmail)) {
        $response['status'] = 'error';
        $response['message'] = "Email field is required.";
        sendResponse($response);
    } elseif (!filter_var($userEmail, FILTER_VALIDATE_EMAIL)) {
        $response['status'] = 'error';
        $response['message'] = "Invalid email format.";
        sendResponse($response);
    }

    if (empty($userMobile)) {
        $response['status'] = 'error';
        $response['message'] = "Mobile field is required.";
        sendResponse($response);
    } elseif (strlen($userMobile) != 10 || !ctype_digit($userMobile)) {
        $response['status'] = 'error';
        $response['message'] = "Please enter a valid 10-digit mobile number.";
        sendResponse($response);
    }

    if (empty($newPassword) || empty($reenterPassword)) {
        $response['status'] = 'error';
        $response['message'] = "Both password fields are required.";
        sendResponse($response);
    }

    $error_message = "";

    if (strlen($newPassword) < 8) {
        $error_message .= "at least 8 characters, ";
    }

    // if (!preg_match('/[A-Z]/', $newPassword)) {
    //     $error_message .= "an uppercase letter, ";
    // }

    // if (!preg_match('/[a-z]/', $newPassword)) {
    //     $error_message .= "a lowercase letter, ";
    // }

    // if (!preg_match('/[0-9]/', $newPassword)) {
    //     $error_message .= "a number, ";
    // }

    // if (!preg_match('/[\W_]/', $newPassword)) {
    //     $error_message .= "a special character, ";
    // }

    if (!empty($error_message)) {
        $error_message = rtrim($error_message, ", ");
        $response['status'] = 'error';
        $response['message'] = "Password must include: " . $error_message . ".";
        sendResponse($response);
    } elseif ($newPassword !== $reenterPassword) {
        $response['status'] = 'error';
        $response['message'] = "Passwords do not match.";
        sendResponse($response);
    }

    // Proceed with database insert if no errors
    $conn = mysqli_connect(HOST, USERNAME, PASSWORD, DB_USER);

    if (!$conn) {
        $response['status'] = 'error';
        $response['message'] = "Connection failed: " . mysqli_connect_error();
        sendResponse($response);
    } else {
        // Check user permissions
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

        // If the user has permission, proceed with the uniqueness check and insert
        if ($permission_check == 1) {
            // Sanitize inputs
            $userName = sanitize_input($userName, $conn);
            $userId = sanitize_input($userId, $conn);
            $userRole = sanitize_input($userRole, $conn);
            $userEmail = sanitize_input($userEmail, $conn);
            $userMobile = sanitize_input($userMobile, $conn);
            $login_page = sanitize_input($login_page, $conn);
            $newPassword = sanitize_input($newPassword, $conn);

            if ($login_page === "USER") {
                $login_page = $client_dashboard_login;
            }
            $login_page = strtolower($login_page);
            $count=0;
            // Check for unique email, mobile, and user_id
            $checkQuery = "SELECT COUNT(*) AS count FROM login_details WHERE (email_id = ? OR mobile_no = ? OR user_id = ?)";
            $checkStmt = mysqli_prepare($conn, $checkQuery);
            if ($checkStmt) {
                mysqli_stmt_bind_param($checkStmt, 'sss', $userEmail, $userMobile, $userId);
                mysqli_stmt_execute($checkStmt);
                mysqli_stmt_bind_result($checkStmt, $count);
                mysqli_stmt_fetch($checkStmt);
                mysqli_stmt_close($checkStmt);

                if ($count > 0) {
                    $response['status'] = 'error';
                    $response['message'] = "Email, mobile number, or user ID already exists.";
                    mysqli_close($conn);
                    sendResponse($response);
                }

                // Prepare SQL statement for insert
                $account_confirmation="ACTIVE";
                if($userRole==="SUPERADMIN")
                {
                    $account_confirmation="HOLD";

                }
                $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
                $query = "INSERT INTO `login_details` (`user_id`, `mobile_no`, `email_id`, `password`, `name`, `role`, `status`, `client`, `client_login`, `created_by`) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

                if ($stmt = mysqli_prepare($conn, $query)) {
                    mysqli_stmt_bind_param($stmt, 'ssssssssss', $userId, $userMobile, $userEmail, $hashedPassword, $userName, $userRole, $account_confirmation, $login_page, $dashboard_version, $user_login_id);

                    if (mysqli_stmt_execute($stmt)) {
                        
                        mysqli_query($conn, "INSERT INTO `menu_permissions_list` (`login_id`) VALUES ((SELECT id FROM login_details ORDER BY id DESC LIMIT 1))");
                        $response['status'] = 'success';
                        $response['message'] = "Account created successfully.";
                    } else {
                        $response['status'] = 'error';
                        $response['message'] = "Error inserting record: " . mysqli_stmt_error($stmt);
                    }

                    mysqli_stmt_close($stmt);
                } else {
                    $response['status'] = 'error';
                    $response['message'] = "Error preparing statement: " . mysqli_error($conn);
                }
            } 
            else {
                $response['status'] = 'error';
                $response['message'] = "Error preparing uniqueness check.";
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
?>
