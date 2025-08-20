
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
//==================================//
$return_response = "";
$user_devices = "";
$device_list = array();
$total_switch_point = 0;
//==================================//

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["GROUP_ID"])) {
    $group_id = $_POST['GROUP_ID'];

    include_once(BASE_PATH_1 . "common-files/selecting_group_device.php");
    $_SESSION["DEVICES_LIST"] = json_encode($device_list);

    if ($user_devices != "") {
        $user_devices = substr($user_devices, 0, -1);
    }

    $device_ids = explode(",", $user_devices);
    $param_type = str_repeat("s", count($device_ids));
    $params = array();
    foreach ($device_ids as $device_id) {
        $params[] = $device_id;
    }

    $conn = mysqli_connect(HOST, USERNAME, PASSWORD);
    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    } else {
        $sql_lights = "SELECT * FROM $central_db.`main_alerts_and_updates` WHERE device_id IN ($user_devices) ORDER BY id DESC limit 100";

        if ($result = mysqli_query($conn, $sql_lights)) {
            if (mysqli_num_rows($result) > 0) {
                while ($rl = mysqli_fetch_assoc($result)) {
                    $device_id = $rl['device_id'];
                    $device_id_name = $rl['device_id_name'];
                    $update = $rl['update'];
                    $date_time = $rl['date_time'];
                    $condition = $rl['condition'];

                    $sql_electrician = "SELECT electrician_name, phone_number FROM $users_db.`electrician_devices` WHERE device_id = '$device_id' LIMIT 1";
                    $electrician_result = mysqli_query($conn, $sql_electrician);
                    $electrician_name = '';
                    $phone_number = '';
                    if ($electrician_row = mysqli_fetch_assoc($electrician_result)) {
                        $electrician_name = $electrician_row['electrician_name'];
                        $phone_number = $electrician_row['phone_number'];
                    }
                    mysqli_free_result($electrician_result);

                    $return_response .= '<div class="alert-item">
                        <div class="device-header">
                            <span class="device-name">
                                <i class="bi bi-cpu"></i>
                                ' . $device_id_name . '
                            </span>
                           
                        </div>
                        <div class="mb-1 font-small text-info-emphasis">' . $update . '</div>
                        <div class="d-flex justify-content-end">  <span class="timestamp">
                                <i class="bi bi-clock"></i>
                                ' . $date_time . '
                            </span>
                            </div>
                        <div class="contact-info">
                            <span class="electrician-info">
                                <i class="bi bi-person"></i>
                                ' . $electrician_name . '
                            </span>
                            <a href="tel:' . $phone_number . '" class="phone-number">
                                <i class="bi bi-telephone"></i>
                                ' . $phone_number . '
                            </a>
                        </div>
                    </div>';
                }
            }
            mysqli_free_result($result);
        }
        mysqli_close($conn);
    }
    echo json_encode($return_response);
}
?>