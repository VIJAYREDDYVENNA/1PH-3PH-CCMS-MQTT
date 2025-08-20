<?php
require_once '../../base-path/config-path.php';
require_once BASE_PATH_1 . 'config_db/config.php';
require_once BASE_PATH_1 . 'session/session-manager.php';

SessionManager::checkSession();
$sessionVars = SessionManager::SessionVariables();

$send = [];  // Initialize response array

// Establish database connection
$conn = mysqli_connect(HOST, USERNAME, PASSWORD, DB_USER);

// Check if the connection is established successfully
if (!$conn) {
    $send["status"] = "error";
    $send["message"] = "Database connection failed: " . mysqli_connect_error();
} else {
    // Check if client-id and client-name are set
    if (isset($_POST['client-id']) && isset($_POST['client-name'])) {
        $client_id = sanitize_input($_POST['client-id'], $conn);
        $client_name = sanitize_input($_POST['client-name'], $conn);

        // Check if client_id and client_name are empty
        if (empty($client_id) || empty($client_name)) {
            $send["status"] = "error";
            $send["message"] = "Client ID and Name cannot be empty.";
        } else {
            // Check if client ID already exists
            $checkSql = "SELECT * FROM client_dashboard WHERE client_dashboard = ? or client_identity_name = ?";
            $stmt = $conn->prepare($checkSql);
            $stmt->bind_param("ss", $client_id,$client_name);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                // If client already exists
                $send["status"] = "error";
                $send["message"] = "Client with this ID already exists.";
            } else {
                // Insert the new client data
                $sql = "INSERT INTO client_dashboard (client_dashboard, client_identity_name, date_time) VALUES (?, ?, current_timestamp())";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("ss", $client_id, $client_name);

                if ($stmt->execute()) {
                    // If insertion is successful
                    $send["status"] = "success";
                    $send["message"] = "Client added successfully!";
                } else {
                    // If insertion fails
                    $send["status"] = "error";
                    $send["message"] = "Failed to add client.";
                }
            }

            // Close the statement
            $stmt->close();
        }
    } else {
        $send["status"] = "error";
        $send["message"] = "Client ID and Name are required.";
    }

    // Close the database connection
    $conn->close();
}

// Send response as JSON
header('Content-Type: application/json');
echo json_encode($send);

// Function to sanitize input data
function sanitize_input($data, $conn) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return mysqli_real_escape_string($conn, $data);
}
