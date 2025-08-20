<?php
require_once '../../base-path/config-path.php';
require_once BASE_PATH_1 . 'config_db/config.php';
require_once BASE_PATH_1 . 'session/session-manager.php';

SessionManager::checkSession();

$return_response = [
    'success' => false,
    'message' => '',
    'data' => [
        'latitude' => 0, 
        'longitude' => 0,
        'update_status' => 0,
        'street' => "--",
        'town' => "--",
        'city' => "--",
        'district' => "--",
        'state' => "--",
        'pincode' => "--",
        'country' => "--",
        'landmark' => "--",
    ]
];

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['D_ID'])) {
    $db = strtolower(trim($_POST['D_ID']));
     // /$db="ccms_1";
    $conn = mysqli_connect(HOST, USERNAME, PASSWORD, $db);

    if (!$conn) {
        $return_response['message'] = "Connection failed: " . mysqli_connect_error();
    } else {
        // Query for coordinates_list
        $sql = "SELECT `latitude`, `longitude`, `update_status` FROM `coordinates_list` ORDER BY id DESC LIMIT 1;"; // Corrected 'lattitude' to 'latitude'
        $result = mysqli_query($conn, $sql);

        if ($result) {
            $row = mysqli_fetch_assoc($result);
            if ($row) {
                $return_response['data'] = array_merge($return_response['data'], array_intersect_key($row, $return_response['data']));
            } else {
                $return_response['message'] = "No records found.";
            }
            mysqli_free_result($result);
        } else {
            $return_response['message'] = "Error";
        }

        // Query for limits_current
        $sql = "SELECT `street`, `town`, `city`, `district`, `state`, `pincode`, `country`, `landmark` FROM `device_address` ORDER BY id DESC LIMIT 1;";
        $result = mysqli_query($conn, $sql);

        if ($result) {
            $row = mysqli_fetch_assoc($result);
            if ($row) {
                $return_response['data'] = array_merge($return_response['data'], array_intersect_key($row, $return_response['data']));
            } else {
                $return_response['message'] = "No records found.";
            }
            mysqli_free_result($result);
        } else {
            $return_response['message'] = "Error " ;
        }

        $return_response['success'] = true;
        mysqli_close($conn);
    }
} else {
    $return_response['message'] = "Data not available";
}

// Output JSON response
header('Content-Type: application/json');
echo json_encode($return_response);
?>
