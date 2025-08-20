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

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $conn = mysqli_connect(HOST, USERNAME, PASSWORD, DB_ALL);

    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }

    // Get the latest row from live_frame_data
    $sql = "SELECT * FROM live_frame_data ORDER BY id DESC LIMIT 1";
    $result = mysqli_query($conn, $sql);

    $response = [];

    if ($result && mysqli_num_rows($result) > 0) {
        $response = mysqli_fetch_assoc($result);
    } else {
        $response = ['error' => 'No data found'];
    }

    mysqli_close($conn);

    header('Content-Type: application/json');
    echo json_encode($response);
}
