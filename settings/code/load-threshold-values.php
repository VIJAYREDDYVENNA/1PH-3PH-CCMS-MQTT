<?php
require_once '../../base-path/config-path.php';
require_once BASE_PATH_1 . 'config_db/config.php';
require_once BASE_PATH_1 . 'session/session-manager.php';

SessionManager::checkSession();

$return_response = [
    'success' => false,
    'message' => '',
    'data' => [
        'l_r' => 0,
        'l_y' => 0,
        'l_b' => 0,
        'u_r' => 0,
        'u_y' => 0,
        'u_b' => 0,
        'i_r' => 0,
        'i_y' => 0,
        'i_b' => 0,
        'pf' => 0,
        'capacity' => 0,
        'frame_time' => 60,
        'ct_ratio' => 0
    ],
    'phase' =>''
];

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['D_ID'])) {
    $Deviceid = trim($_POST['D_ID']);
    $db = strtolower(trim($_POST['D_ID']));
    
    $id = $Deviceid;
	include_once("../../common-files/fetch-device-phase.php");
	$phase = $device_phase;
    $return_response['phase'] = $phase;

    $conn = mysqli_connect(HOST, USERNAME, PASSWORD, $db);
    if (!$conn) {
        $return_response['message'] = "Connection failed: " . mysqli_connect_error();
    } else {
        // Query for limits_voltage
        $sql = "SELECT `l_r`, `l_y`, `l_b`, `u_r`, `u_y`, `u_b` FROM `limits_voltage` ORDER BY id DESC LIMIT 1;";
        $result = mysqli_query($conn, $sql);

        if ($result) {
            $row = mysqli_fetch_assoc($result);
            if ($row) {
                $return_response['data'] = array_merge($return_response['data'], array_filter($row, 'is_numeric'));
            } else {
                $return_response['message'] = "No data in limits_voltage.";
            }
            mysqli_free_result($result);
        } else {
            $return_response['message'] = "Error: " . mysqli_error($conn);
        }

        // Query for limits_current
        $sql = "SELECT `i_r`, `i_y`, `i_b` FROM `limits_current` ORDER BY id DESC LIMIT 1;";
        $result = mysqli_query($conn, $sql);

        if ($result) {
            $row = mysqli_fetch_assoc($result);
            if ($row) {
                $return_response['data'] = array_merge($return_response['data'], array_filter($row, 'is_numeric'));
            } else {
                $return_response['message'] = "No data in limits_current.";
            }
            mysqli_free_result($result);
        } else {
            $return_response['message'] = "Error: " . mysqli_error($conn);
        }

        // Query for limits_pf
        $sql = "SELECT `pf` FROM `limits_pf` ORDER BY id DESC LIMIT 1;";
        $result = mysqli_query($conn, $sql);

        if ($result) {
            $row = mysqli_fetch_assoc($result);
            if ($row) {
                $return_response['data'] = array_merge($return_response['data'], array_filter($row, 'is_numeric'));
            } else {
                $return_response['message'] = "No data in limits_pf.";
            }
            mysqli_free_result($result);
        } else {
            $return_response['message'] = "Error: " . mysqli_error($conn);
        }

        // Query for unit_capacity
        $sql = "SELECT `capacity` FROM `unit_capacity` ORDER BY id DESC LIMIT 1;";
        $result = mysqli_query($conn, $sql);

        if ($result) {
            $row = mysqli_fetch_assoc($result);
            if ($row) {
                $return_response['data'] = array_merge($return_response['data'], array_filter($row, 'is_numeric'));
            } else {
                $return_response['message'] = "No data in unit_capacity.";
            }
            mysqli_free_result($result);
        } else {
            $return_response['message'] = "Error: " . mysqli_error($conn);
        }

        // Query for frame_time
        $sql = "SELECT `frame_time` FROM `frame_time` ORDER BY id DESC LIMIT 1;";
        $result = mysqli_query($conn, $sql);

        if ($result) {
            $row = mysqli_fetch_assoc($result);
            if ($row) {
                $return_response['data'] = array_merge($return_response['data'], array_filter($row, 'is_numeric'));
            } else {
                $return_response['message'] = "No data in frame_time.";
            }
            mysqli_free_result($result);
        } else {
            $return_response['message'] = "Error: " . mysqli_error($conn);
        }

        // Query for limits_ct_ratio
        $sql = "SELECT `ct_ratio` FROM `limits_ct_ratio` ORDER BY id DESC LIMIT 1;";
        $result = mysqli_query($conn, $sql);

        if ($result) {
            $row = mysqli_fetch_assoc($result);
            if ($row) {
                $return_response['data'] = array_merge($return_response['data'], array_filter($row, 'is_numeric'));
            } else {
                $return_response['message'] = "No data in limits_ct_ratio.";
            }
            mysqli_free_result($result);
        } else {
            $return_response['message'] = "Error: " . mysqli_error($conn);
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
