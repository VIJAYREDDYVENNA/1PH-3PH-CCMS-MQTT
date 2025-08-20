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

// Initialize response array and other variables
$return_response = "";
$total_switch_point = 0;
$user_devices = "";
$send = []; // Initialize as an empty array

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $group_id = $_POST['GROUP_ID'];
    include_once(BASE_PATH_1 . "common-files/selecting_group_device.php");

    if ($user_devices != "") {
        $user_devices = substr($user_devices, 0, -1); // Remove the trailing comma
    }

    $conn_db_all = mysqli_connect(HOST, USERNAME, PASSWORD, DB_ALL);
    if (!$conn_db_all) {
        die("Connection failed: " . mysqli_connect_error());
    } else {
        // Get pagination parameters from POST
        $items_per_page = isset($_POST['items_per_page']) ? (int)$_POST['items_per_page'] : 100;
        $page = isset($_POST['page']) ? (int)$_POST['page'] : 1;
        $offset = ($page - 1) * $items_per_page;

        // SQL query with LIMIT and OFFSET for pagination
        $sql = "SELECT device_id, sim_ccid, imei_no, fw_no, pcb_no, date_time 
                FROM sim_card_details 
                WHERE device_id IN ($user_devices) 
                ORDER BY sim_ccid ASC
                LIMIT $items_per_page OFFSET $offset";

        $result = mysqli_query($conn_db_all, $sql);

        // Get total count of records for pagination
        $total_query = "SELECT COUNT(*) as total FROM sim_card_details WHERE device_id IN ($user_devices)";
        $total_result = mysqli_query($conn_db_all, $total_query);
        $total_row = mysqli_fetch_assoc($total_result);
        $total_records = $total_row['total'];

        // If query executes successfully
        if ($result && mysqli_num_rows($result) > 0) {
            while ($r = mysqli_fetch_assoc($result)) {
                $device_id = $r['device_id'];
                $sim_ccid = $r['sim_ccid'];
                $imei_no = $r['imei_no'];
                $fw_no = $r['fw_no'];
                $pcb_no = $r['pcb_no'];
                $date = date("H:i:s d-m-Y", strtotime($r['date_time']));

                // Append each row to $send array
                $send[] = array(
                    "D_ID" => $device_id,
                    "CCID" => $sim_ccid,
                    "IMEI" => $imei_no,
                    "FW" => $fw_no,
                    "PCB" => $pcb_no,
                    "DATE_TIME" => $date
                );
            }
        } 

        // Send the response with both data and total_records
        echo json_encode(['data' => $send, 'total_records' => $total_records]);
    }
} else {
    echo json_encode(['error' => 'Data not Available']);
}

?>
