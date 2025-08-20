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

// Initialize the response array
$response = ["status" => "", "message" => "", "errors" => []];

// Check if the form was submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST' &&isset($_POST['USERNAME'])&&isset($_POST['USEREMAIL'])&&isset($_POST['USERMOBILE'])) {
    // Sanitize and validate input fields
    $userName = trim(filter_input(INPUT_POST, 'USERNAME', FILTER_SANITIZE_STRING));
    $userEmail = trim(filter_input(INPUT_POST, 'USEREMAIL', FILTER_SANITIZE_EMAIL));
    $userMobile = trim(filter_input(INPUT_POST, 'USERMOBILE', FILTER_SANITIZE_NUMBER_INT));

    // Validation checks
    if (empty($userName)) {
        $response['status'] = 'error';
        $response['message'] = "Name field is required.";
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

            // Sanitize inputs
        $userName = sanitize_input($userName, $conn);        
        $userEmail = sanitize_input($userEmail, $conn);
        $userMobile = sanitize_input($userMobile, $conn);


            // Check for unique email, mobile, and user_id
        $checkQuery = "SELECT COUNT(*) AS count FROM login_details WHERE (email_id = ? OR mobile_no = ? ) AND id != ?";
        $checkStmt = mysqli_prepare($conn, $checkQuery);
        if ($checkStmt) {
            mysqli_stmt_bind_param($checkStmt, 'ssi', $userEmail, $userMobile, $user_login_id);
            mysqli_stmt_execute($checkStmt);
            mysqli_stmt_bind_result($checkStmt, $count);
            mysqli_stmt_fetch($checkStmt);
            mysqli_stmt_close($checkStmt);

            if ($count > 0) {
                $response['status'] = 'error';
                $response['message'] = "Email or mobile number already exists.";
                mysqli_close($conn);
                sendResponse($response);
            }

                // Prepare SQL statement for update
            $query = "UPDATE login_details SET name = ?, email_id = ?, mobile_no = ? WHERE id = ?";
            if ($stmt = mysqli_prepare($conn, $query)) {
                    // Bind parameters to the prepared statement
                mysqli_stmt_bind_param($stmt, 'sssi', $userName, $userEmail, $userMobile, $user_login_id);

                    // Execute the statement
                if (mysqli_stmt_execute($stmt)) {

                    $_SESSION['mobile_no'] = $userMobile;
                    $_SESSION['user_name'] = $userName;
                    $_SESSION['user_email'] = $userEmail;

                    $response['status'] = 'success';
                    $response['message'] = "Profile details updated successfully.";
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


        // Close the database connection
        mysqli_close($conn);
    }

    // Output the JSON response
    sendResponse($response);
}
if ($_SERVER['REQUEST_METHOD'] == 'POST' &&isset($_POST['OLDUSERNAME'])&&isset($_POST['NEWUSERNAME'])) {
    // Sanitize and validate input fields
    $oldusername = trim(filter_input(INPUT_POST, 'OLDUSERNAME', FILTER_SANITIZE_STRING));
    $newusername = trim(filter_input(INPUT_POST, 'NEWUSERNAME', FILTER_SANITIZE_EMAIL));


    // Validation checks
    if (empty($oldusername)) {
        $response['status'] = 'error';
        $response['message'] = "Old user id/Name field is required..";
        sendResponse($response);
    }
    if (empty($newusername)) {
        $response['status'] = 'error';
        $response['message'] = "New user id/Name field is required.";
        sendResponse($response);
    }
    elseif (strlen($newusername) < 6 ) {
        $response['status'] = 'error';
        $response['message'] = "New user ID/Name must be at least 6 characters long";
        sendResponse($response);
    }



    // Proceed with database update if no errors
    $conn = mysqli_connect(HOST, USERNAME, PASSWORD, DB_USER);

    if (!$conn) {
        $response['status'] = 'error';
        $response['message'] = "Connection failed: " . mysqli_connect_error();
        sendResponse($response);
    } else {

            // Sanitize inputs
        $oldusername = sanitize_input($oldusername, $conn);        
        $newusername = sanitize_input($newusername, $conn);


        $checkQuery = "SELECT COUNT(*) AS count FROM login_details WHERE user_id = ? AND id = ?";
        $checkStmt = mysqli_prepare($conn, $checkQuery);
        if ($checkStmt) {
            mysqli_stmt_bind_param($checkStmt, 'si', $oldusername,  $user_login_id);
            mysqli_stmt_execute($checkStmt);
            mysqli_stmt_bind_result($checkStmt, $count);
            mysqli_stmt_fetch($checkStmt);
            mysqli_stmt_close($checkStmt);

            if ($count === 0) {
                $response['status'] = 'error';
                $response['message'] = "Old User id/Name Doen't exists.";
                mysqli_close($conn);
                sendResponse($response);
            }
            elseif ($count ===1)
            {

                $checkQuery = "SELECT COUNT(*) AS count FROM login_details WHERE user_id = ? ";
                $checkStmt = mysqli_prepare($conn, $checkQuery);
                if ($checkStmt) {
                    mysqli_stmt_bind_param($checkStmt, 's', $newusername);
                    mysqli_stmt_execute($checkStmt);
                    mysqli_stmt_bind_result($checkStmt, $count);
                    mysqli_stmt_fetch($checkStmt);
                    mysqli_stmt_close($checkStmt);

                    if ($count > 0) {
                        $response['status'] = 'error';
                        $response['message'] = "New User id/Name already exists.";
                        mysqli_close($conn);
                        sendResponse($response);
                    }

                // Prepare SQL statement for update
                    $query = "UPDATE login_details SET user_id = ? WHERE user_id = ?";
                    if ($stmt = mysqli_prepare($conn, $query)) {
                    // Bind parameters to the prepared statement
                        mysqli_stmt_bind_param($stmt, 'ss', $newusername, $oldusername,);

                    // Execute the statement
                        if (mysqli_stmt_execute($stmt)) {
                            $_SESSION['login_user_id'] = $newusername;

                            $response['status'] = 'success';
                            $response['message'] = "User id/Name updated successfully.";
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
        } else {
            $response['status'] = 'error';
            $response['message'] = "Error preparing uniqueness check.";
        }


        // Close the database connection
        mysqli_close($conn);
    }

    // Output the JSON response
    sendResponse($response);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' &&isset($_POST['PASSWORD'])&&isset($_POST['REENTERPASSWORD'])) {

    $newPassword = trim(filter_input(INPUT_POST, 'PASSWORD', FILTER_SANITIZE_STRING));
    $reenterPassword = trim(filter_input(INPUT_POST, 'REENTERPASSWORD', FILTER_SANITIZE_STRING));


    $error_message="";
    if (empty($newPassword) || empty($reenterPassword)) {

        $error_message= "Both password fields are required";

        $response['status'] = 'error';
        $response['message'] = $error_message;  
        sendResponse($response);
    }

    
    if (strlen($newPassword) < 8) {
        if($error_message!="")
        {
            $error_message.=", ";  
        }

        $error_message.= "at least 8 characters";
    }


    if (!preg_match('/[A-Z]/', $newPassword)) {
        if($error_message!="")
        {
            $error_message.=", ";  
        }
        $error_message.= "an uppercase letter";
    }


    if (!preg_match('/[a-z]/', $newPassword)) {
        if($error_message!="")
        {
            $error_message.=", ";  
        }
        $error_message.= "a lowercase letter";
    }


    if (!preg_match('/[0-9]/', $newPassword)) {
        if($error_message!="")
        {
            $error_message.=", ";  
        }
        $error_message.= "a number";


    }

    // Check for a special character
    if (!preg_match('/[\W_]/', $newPassword)) {
        if($error_message!="")
        {
            $error_message.=", ";  
        }

        $error_message.= "a special character";

    }


    if($error_message!="")
    {
        $response['status'] = 'error';
        $response['message'] ="Password must include: ". $error_message.".";  
        sendResponse($response);
    }
    else
    {
        if ($newPassword !== $reenterPassword) {
            $error_message.= "Passwords do not match.";
            $response['status'] = 'error';
            $response['message'] = $error_message;  
            sendResponse($response);
        }
    }


    $conn = mysqli_connect(HOST, USERNAME, PASSWORD, DB_USER);

    if (!$conn) {
        $response['status'] = 'error';
        $response['message'] = "Connection failed: " . mysqli_connect_error();
        sendResponse($response);
    } else {


        $password = sanitize_input($newPassword, $conn);
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);          


        $query = "UPDATE login_details SET password = ? WHERE id = ?";
        if ($stmt = mysqli_prepare($conn, $query)) {

            mysqli_stmt_bind_param($stmt, 'ss', $hashedPassword, $user_login_id,);

            if (mysqli_stmt_execute($stmt)) {

                $response['status'] = 'success';
                $response['message'] = "New Password updated successfully.";
            } else {
                $response['status'] = 'error';
                $response['message'] = "Error updating record. ";
            }
            mysqli_stmt_close($stmt);
        } else {
            $response['status'] = 'error';
            $response['message'] = "Error preparing statement. ";
        }
        mysqli_close($conn);
    }
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
