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

// Initialize response array
$response = array();
$alerts = array();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    try {
        $conn = mysqli_connect(HOST, USERNAME, PASSWORD, DB_ALL); // Ensure DATABASE constant is defined and holds the name of the database

        // Check connection
        if ($conn->connect_error) {
            throw new Exception("Connection failed: " . $conn->connect_error);
        }

        // Get device ID and date from POST
        $device_id = isset($_POST['D_ID']) ? $_POST['D_ID'] : 'ALL';
        $search_date = isset($_POST['DATE']) ? $_POST['DATE'] : '';

        // Build query based on parameters
        if ($device_id !== 'ALL' && !empty($search_date)) {
            $query = "SELECT * FROM alerts_and_updates where device_id = ? AND DATE(date_time) = ? ORDER BY date_time DESC LIMIT 50";
        } elseif ($device_id !== 'ALL') {
            $query = "SELECT  * FROM alerts_and_updates where device_id = ? ORDER BY date_time DESC LIMIT 50";
        }
        elseif (!empty($search_date))
        {
            $query = "SELECT * FROM alerts_and_updates where DATE(date_time) = ? ORDER BY date_time DESC LIMIT 50";
        }
        else{
            $query = "SELECT * FROM alerts_and_updates  ORDER BY date_time DESC LIMIT 50";
        }

    

        // Prepare statement
        $stmt = $conn->prepare($query);

        // Bind parameters
        if ($device_id !== 'ALL' && !empty($search_date)) {
            $stmt->bind_param("ss", $device_id, $search_date);
        } elseif ($device_id !== 'ALL') {
            $stmt->bind_param("s", $device_id);
        } elseif (!empty($search_date)) {
            $stmt->bind_param("s", $search_date);
        }

        // Execute query
        $stmt->execute();
        $result = $stmt->get_result();

        // Process results
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                // Map alert names to alert types from JS
                $alert_type = 'drive-run'; // Default
                $alert_name = strtolower($row['alert_update_name']);

                if (strpos($alert_name, 'power') !== false && strpos($alert_name, 'restored') !== false) {
                    $alert_type = 'power-restored';
                } elseif (
                    strpos($alert_name, 'power') !== false &&
                    (strpos($alert_name, 'disconnect') !== false || strpos($alert_name, 'off') !== false)
                ) {
                    $alert_type = 'power-disconnected';
                } elseif (strpos($alert_name, 'overload') !== false) {
                    $alert_type = 'overload';
                }

                // Format date time
                $timestamp = date('M d, Y - h:i A', strtotime($row['date_time']));

                // Get motor number from device_id (assuming format is like MOTOR_X)
                $motorId = $row['device_id'];

                $alerts[] = array(
                    'id' => $row['id'],
                    'motorId' => $motorId,
                    'motorName' => $row['device_id_name'],
                    'type' => $alert_type,
                    'message' => $row['update'],
                    'timestamp' => $timestamp
                );
            }
        }

        $response['status'] = 'success';
        $response['alerts'] = $alerts;

        $stmt->close();
        $conn->close();
    } catch (Exception $e) {
        $response['status'] = 'error';
        $response['message'] = $e->getMessage();
    }

    // Return JSON response
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}
