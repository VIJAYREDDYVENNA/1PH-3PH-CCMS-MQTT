<?php
require_once '../../base-path/config-path.php';
require_once BASE_PATH_1 . 'config_db/config.php';
require_once BASE_PATH_1 . 'session/session-manager.php';

SessionManager::checkSession();
//$db='ccms_2';
$return_response = [
    'success' => false,
    'message' => '',
    'data' => []
];

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['D_ID'])) {
    $db = strtolower(trim($_POST['D_ID']));
    $conn = mysqli_connect(HOST, USERNAME, PASSWORD, $db);

    if (!$conn) {
        $return_response['message'] = "Connection failed: " . mysqli_connect_error();
    } else {
        $sql = "SELECT `id`, `device_id`, `brand_name`, `wattage`, `total_lights`, `total_wattage` FROM `installed_lights_info` WHERE `add_or_removed` = 1";

        $result = mysqli_query($conn, $sql);

        if ($result) {
            $data = [];
            $total_lights_sum = 0;
            $total_wattage_sum = 0;

            while ($row = mysqli_fetch_assoc($result)) {
                $data[] = $row;
                $total_lights_sum += $row['total_lights'];
                $total_wattage_sum += $row['total_wattage'];
            }

            // Add the last row with the sums
            $data[] = [
                'device_id' => 'Total',
                'brand_name' => '',
                'wattage' => '',
                'total_lights' => $total_lights_sum,
                'total_wattage' => $total_wattage_sum
            ];

            $return_response['success'] = true;
            $return_response['data'] = $data;
        } else {
            $return_response['message'] = "Error: " . mysqli_error($conn);
        }

        mysqli_free_result($result);
        mysqli_close($conn);
    }
} else {
    $return_response['message'] = "Data not available";
}

// Output JSON response
header('Content-Type: application/json');
echo json_encode($return_response);
?>
