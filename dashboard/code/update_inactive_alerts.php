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

$return_response = "";
$user_devices = "";
$device_list = array();
$total_switch_point = 0;

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["GROUP_ID"])) {
    $group_id = $_POST['GROUP_ID'];

    include_once(BASE_PATH_1 . "common-files/selecting_group_device.php");
    if ($user_devices != "") {
        $user_devices = substr($user_devices, 0, -1);
    }

    $user_devices_array = explode(',', $user_devices);
    $user_devices_array = array_map(function ($item) {
        return trim(trim($item, "'"));
    }, $user_devices_array);

    // Ensure $user_devices_array is non-empty and contains only valid device IDs
    $user_devices_array = array_filter($user_devices_array);

    if (empty($user_devices_array)) {
        echo json_encode(['error' => 'No devices found for this group']);
        exit;
    }

    // Create placeholders for device IDs in SQL query
    $placeholders = implode(',', array_fill(0, count($user_devices_array), '?'));

    $conn_db_all = mysqli_connect(HOST, USERNAME, PASSWORD, DB_ALL);
    $conn_db_user = mysqli_connect(HOST, USERNAME, PASSWORD, DB_USER);

    if (!$conn_db_all || !$conn_db_user) {
        die("Connection failed: " . mysqli_connect_error());
    }

    try {
        // Updated SQL query to join both tables and get electrician details
        $sql = "SELECT 
                    ld.device_id, 
                    ld.date_time,
                    CASE
                        WHEN ld.poor_network = '1' THEN 'poor_network'
                        WHEN ld.power_failure = '1' THEN 'power_failure'
                        WHEN ld.faulty = '1' THEN 'faulty'
                    END AS category,
                    ed.electrician_name,
                    ed.phone_number
                FROM {$conn_db_all->real_escape_string(DB_ALL)}.live_data_updates ld
                LEFT JOIN {$conn_db_user->real_escape_string(DB_USER)}.electrician_devices ed 
                    ON ld.device_id = ed.device_id
                WHERE (ld.poor_network = '1' OR ld.power_failure = '1' OR ld.faulty = '1') 
                AND ld.installed_status = '1' 
                AND ld.device_id IN ($placeholders)
                ORDER BY date_time DESC";

        $stmt = mysqli_prepare($conn_db_all, $sql);

        if ($stmt) {
            // Bind parameters dynamically
            $types = str_repeat('s', count($user_devices_array));
            mysqli_stmt_bind_param($stmt, $types, ...$user_devices_array);

            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);

            if (mysqli_num_rows($result) > 0) {
                while ($row = mysqli_fetch_assoc($result)) {
                    $device_id = htmlspecialchars($row['device_id']);
                    $category = htmlspecialchars($row['category']);
                    $date_time = htmlspecialchars($row['date_time']);
                    $electrician_name = htmlspecialchars($row['electrician_name'] ?? 'Not Assigned');
                    $phone_number = htmlspecialchars($row['phone_number'] ?? '');

                    // Determine status icon and color based on category
                    $status_icon = '';
                    $status_class = '';
                    $status_text = '';

                    switch ($category) {
                        case 'power_failure':
                            $status_icon = 'bi-power';
                            $status_class = 'text-danger';
                            $status_text = 'Power Failure';
                            break;
                        case 'faulty':
                            $status_icon = 'bi-exclamation-triangle';
                            $status_class = 'text-warning';
                            $status_text = 'Faulty';
                            break;
                        case 'poor_network':
                            $status_icon = 'bi-wifi-off';
                            $status_class = 'text-secondary';
                            $status_text = 'Poor Network';
                            break;
                    }

                    $return_response .= '<div class="alert-item mb-1 p-1 border rounded" style="font-size: 0.8rem;">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="fw-bold" style="font-size: 0.75rem;">
                                <i class="bi bi-cpu text-primary" style="font-size: 0.7rem;"></i>
                                ' . $device_id . '
                            </span>
                            <span class="' . $status_class . '" style="font-size: 0.7rem;">
                                <i class="' . $status_icon . '" style="font-size: 0.65rem;"></i>
                                ' . $status_text . '
                            </span>                        
                        </div>
                        
                        <div class="d-flex justify-content-between align-items-center">
                           <div class="d-flex align-items-center" style="font-size: 0.7rem;">
                             <span class="me-2">
                                <i class="bi bi-person" style="font-size: 0.65rem;"></i>
                                ' . $electrician_name . '
                            </span>
                            <a href="tel:' . $phone_number . '" class="text-decoration-none" style="font-size: 0.7rem;">
                                <i class="bi bi-telephone" style="font-size: 0.65rem;"></i>
                                ' . $phone_number . '
                            </a>
                        </div>
                            <small class="text-muted" style="font-size: 0.65rem;">
                                <i class="bi bi-clock" style="font-size: 0.6rem;"></i>
                                ' . $date_time . '
                            </small>
                        </div>';

                    $return_response .= '</div>';
                }
            } else {
                $return_response = '<div class="alert alert-success p-2" style="font-size: 0.8rem;">
                    <i class="bi bi-check-circle" style="font-size: 0.75rem;"></i>
                    All devices in this group are active and functioning normally.
                </div>';
            }

            mysqli_stmt_close($stmt);
        } else {
            throw new Exception("Failed to prepare SQL statement: " . mysqli_error($conn_db_all));
        }
    } catch (Exception $e) {
        error_log("Error in device status check: " . $e->getMessage());
        $return_response = '<div class="alert alert-danger p-2" style="font-size: 0.8rem;">
            <i class="bi bi-exclamation-triangle" style="font-size: 0.75rem;"></i>
            Error loading device status. Please try again.
        </div>';
    } finally {
        mysqli_close($conn_db_all);
        mysqli_close($conn_db_user);
    }
    echo json_encode($return_response);
} else {
    echo json_encode(['error' => 'Invalid request method or missing GROUP_ID']);
}
?>