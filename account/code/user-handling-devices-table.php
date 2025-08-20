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

$response = ["data" => "", "totalPages" => ""];
$device_ids = "";
$parameter_value = "";
$parameter = "";

/*$page =1;
$limit =20;
$search_item ="";
$user_login_id=2;*/

if ($_SERVER["REQUEST_METHOD"] == "GET") 
{
    $page = filter_input(INPUT_GET, 'page', FILTER_SANITIZE_NUMBER_INT);
    $limit = filter_input(INPUT_GET, 'limit', FILTER_SANITIZE_NUMBER_INT); 
    $search_item = filter_input(INPUT_GET, 'search_item', FILTER_SANITIZE_STRING); 
    $user_devices = filter_input(INPUT_GET, 'user_devices', FILTER_SANITIZE_STRING); 

    $conn = mysqli_connect(HOST, USERNAME, PASSWORD, DB_USER);

    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }
    $page = sanitize_input($page, $conn);
    $limit = sanitize_input($limit, $conn);
    $search_item = sanitize_input($search_item, $conn);
    $user_devices = sanitize_input($user_devices, $conn);

    $page = $page ? intval($page) : 1;
    $limit = $limit ? intval($limit) : 20;

    $offset = ($page - 1) * $limit;
    $totalQuery="";
    $sql="";



    if($search_item=="")
    {
        $totalQuery = "SELECT COUNT(*) AS total FROM user_device_group_view WHERE   login_id='$user_devices'";
        $sql = "SELECT `device_id`, `c_device_name` AS device_name, `device_group_or_area` FROM user_device_group_view WHERE  login_id='$user_devices' LIMIT ? OFFSET ?";
    }
    else
    {
        $totalQuery = "SELECT COUNT(*) AS total FROM user_device_group_view WHERE   login_id='$user_devices' and (`device_id` LIKE '%$search_item%' or `c_device_name` LIKE '%$search_item%' )";

        $sql = "SELECT `device_id`, `c_device_name` AS device_name, `device_group_or_area` FROM user_device_group_view WHERE  login_id='$user_devices'and (`device_id` LIKE '%$search_item%' or `c_device_name` LIKE '%$search_item%') LIMIT ? OFFSET ?";
    }


    $totalResult = mysqli_query($conn, $totalQuery);

    if (!$totalResult) {
        die("Query failed: " . mysqli_error($conn));
    }

    $totalRow = mysqli_fetch_assoc($totalResult);
    $totalRecords = $totalRow['total'];
    $totalPages = ceil($totalRecords / $limit);

    $stmt = mysqli_prepare($conn, $sql);

    if (!$stmt) {
        die("Prepare failed: " . mysqli_error($conn));
    }

    mysqli_stmt_bind_param($stmt, "ii",  $limit, $offset);
    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);

    $data = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $data[] = $row;
    }

    $response = ['data' => $data, 'totalPages' => $totalPages ];

    header('Content-Type: application/json');
    mysqli_stmt_close($stmt);
    echo json_encode($response);
    mysqli_close($conn);

}

function sanitize_input($data, $conn) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return mysqli_real_escape_string($conn, $data);
}
?>


