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

$conn = mysqli_connect(HOST, USERNAME, PASSWORD, DB_ALL);
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Function to fetch data for bar and pie charts
function fetchData($conn, $startDate, $endDate, $device_id) {
    // Fetch data for the bar chart
    $queryBar = "SELECT date, uptime_hours FROM devicehours_bar WHERE (date BETWEEN ? AND ?) AND device_id = ?";
    $stmtBar = mysqli_prepare($conn, $queryBar);
    mysqli_stmt_bind_param($stmtBar, "sss", $startDate, $endDate, $device_id);
    mysqli_stmt_execute($stmtBar);
    $resultBar = mysqli_stmt_get_result($stmtBar);

    $dates = [];
    $uptimeHours = [];

    while ($row = mysqli_fetch_assoc($resultBar)) {
        $dates[] = $row['date'];
        //$uptimeHours[] = $row['uptime_hours'];
        $uptimeHours[] =convertMinutesToHours($row['uptime_hours']);
    }
    mysqli_stmt_close($stmtBar);

    // Fetch data for the pie chart
    $queryPie = "SELECT date, power_failure, device_failure FROM devicehours_pie WHERE (date BETWEEN ? AND ?) AND device_id = ?";
    $stmtPie = mysqli_prepare($conn, $queryPie);
    mysqli_stmt_bind_param($stmtPie, "sss", $startDate, $endDate, $device_id);
    mysqli_stmt_execute($stmtPie);
    $resultPie = mysqli_stmt_get_result($stmtPie);

    $pieData = [];

    while ($row = mysqli_fetch_assoc($resultPie)) {
        $pieData[$row['date']] = [
            'power_failure' => convertMinutesToHours($row['power_failure']),
            'device_failure' => convertMinutesToHours($row['device_failure'])
        ];
    }
    mysqli_stmt_close($stmtPie);

    return [
        'dates' => $dates,
        'uptimeHours' => $uptimeHours,
        'pieData' => $pieData
    ];
}

// Handle AJAX request for specific date range or single date
if (isset($_GET['startDate']) && isset($_GET['endDate']) && isset($_GET['D_ID'])) {
    $startDate = filter_input(INPUT_GET, 'startDate', FILTER_SANITIZE_STRING);
    $endDate = filter_input(INPUT_GET, 'endDate', FILTER_SANITIZE_STRING);
    $device_id = filter_input(INPUT_GET, 'D_ID', FILTER_SANITIZE_STRING);

    $startDate = sanitize_input($startDate, $conn);
    $endDate = sanitize_input($endDate, $conn);
    $device_id = sanitize_input($device_id, $conn);

    $data = fetchData($conn, $startDate, $endDate, $device_id);

    mysqli_close($conn);
    echo json_encode($data);
    exit;
}

if (isset($_GET['date']) && isset($_GET['D_ID'])) {
    $date = filter_input(INPUT_GET, 'date', FILTER_SANITIZE_STRING);
    $device_id = filter_input(INPUT_GET, 'D_ID', FILTER_SANITIZE_STRING);

    $date = sanitize_input($date, $conn);
    $device_id = sanitize_input($device_id, $conn);

    $data = fetchData($conn, $date, $date, $device_id);

    mysqli_close($conn);
    echo json_encode($data);
    exit;
}

function sanitize_input($data, $conn) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return mysqli_real_escape_string($conn, $data);
}

function convertMinutesToHours($totalMinutes) {
    $hours = floor($totalMinutes / 60); // Get the total hours
    $minutes = $totalMinutes % 60; // Get the remaining minutes
    return sprintf("%02d.%02d", $hours, $minutes); // Format as HH:MM
}



?>
