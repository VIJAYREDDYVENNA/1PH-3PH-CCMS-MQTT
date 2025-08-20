<?php
$servername = "localhost";
$username = "root";
$password = "123456";
$dbname = "ccms_1";

// Create connection
$conn = mysqli_connect($servername, $username, $password, $dbname);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Get the page number and the number of records per page
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$limit = isset($_GET['limit']) ? intval($_GET['limit']) : 20;
$offset = ($page - 1) * $limit;

// Prepare SQL query for total number of records
$totalQuery = "SELECT COUNT(*) AS total FROM live_data";
$totalResult = mysqli_query($conn, $totalQuery);

if (!$totalResult) {
    die("Query failed: " . mysqli_error($conn));
}

$totalRow = mysqli_fetch_assoc($totalResult);
$totalRecords = $totalRow['total'];
$totalPages = ceil($totalRecords / $limit);

// Prepare SQL query to fetch data for the current page
$sql = "SELECT id, device_id, date_time FROM live_data LIMIT ? OFFSET ?";
$stmt = mysqli_prepare($conn, $sql);

if (!$stmt) {
    die("Prepare failed: " . mysqli_error($conn));
}

mysqli_stmt_bind_param($stmt, "ii", $limit, $offset);
mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);

$data = [];
while ($row = mysqli_fetch_assoc($result)) {
    $data[] = $row;
}

$response = [
    'data' => $data,
    'totalPages' => $totalPages
];

header('Content-Type: application/json');
echo json_encode($response);

mysqli_stmt_close($stmt);
mysqli_close($conn);
?>


