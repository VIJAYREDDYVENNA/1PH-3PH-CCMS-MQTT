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
$device_list = array ();
$user_devices="";
$total_switch_point=0;
//==================================//

//$group_id="ALL";

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["GROUP_ID"])) {
	$group_id = $_POST['GROUP_ID'];

	include_once(BASE_PATH_1 . "common-files/selecting_group_device.php");
	$_SESSION["DEVICES_LIST"] = json_encode($device_list);

	if ($user_devices != "") {
		$user_devices = substr($user_devices, 0, -1);
	}

	$device_ids = explode(",", $user_devices);

// Count the number of device IDs
	$num_devices = count($device_ids);

// Prepare placeholders for mysqli_stmt_bind_param
$param_type = str_repeat("s", $num_devices); // Assuming all are strings

// Initialize parameters array
$params = array();
foreach ($device_ids as $device_id) {
	$params[] = $device_id;
}

$installed_lights = 0;
$installed_load = 0;
$kw = 0;
$kva = 0;
$kwh = 0;
$faulty_lights = 0;
$switch_points = 0;
$active_switch_points = 0;
$inactive_switch_points = 0;
$poor_network = 0;
$uninstalled_devices = 0;
$power_failure_count = 0;
$power_failure_count_in_poor_nw = 0;
$auto_system_on = 0;
$manual_on = 0;
$off = 0;
$installed_switch_points = 0;

// Database connection
$conn = mysqli_connect(HOST, USERNAME, PASSWORD, DB_ALL);
if (!$conn) {
	die("Connection failed: " . mysqli_connect_error());
} else {
	$sql_lights = "SELECT COALESCE(SUM(lights_wattage) , 0) AS total_wattage, COALESCE(SUM(total_lights) , 0) AS lights, COALESCE(SUM(poor_network) , 0) AS poor_network, COALESCE(SUM(power_failure) , 0) AS power_failure, COALESCE(SUM(faulty) , 0) AS faulty, COALESCE(SUM(energy_kwh_total) , 0) AS energy_kwh_total, COALESCE(SUM(installed_status) , 0) AS installed_status FROM live_data_updates WHERE device_id IN ($user_devices)";

	if ($result = mysqli_query($conn, $sql_lights)) {
		if (mysqli_num_rows($result) > 0) {
			while ($rl = mysqli_fetch_assoc($result)) {
				$installed_lights = $rl['lights'];
				$installed_load = $rl['total_wattage'];
				$installed_switch_points = $rl['installed_status'];
				$kwh = $rl['energy_kwh_total'];
			}
		}
		mysqli_free_result($result);
	}

    // Fetch data for installed status
	$sql_installed_status = "SELECT COALESCE(SUM(active_device), 0) AS active_device, COALESCE(SUM(poor_network), 0) AS poor_network, COALESCE(SUM(power_failure), 0) AS power_failure, COALESCE(SUM(faulty), 0) AS faulty FROM live_data_updates WHERE device_id IN ($user_devices) AND installed_status = 1";

	if ($result = mysqli_query($conn, $sql_installed_status)) {
		if (mysqli_num_rows($result) > 0) {
			while ($r_installed_status = mysqli_fetch_assoc($result)) {
				$active_switch_points = $r_installed_status['active_device'];
				$poor_network = $r_installed_status['poor_network'];
				$power_failure_count = $r_installed_status['power_failure'];
				$inactive_switch_points = $r_installed_status['faulty'];
			}
		}
		mysqli_free_result($result);
	}

    // Fetch data for active status
	$sql_active_status = "SELECT SUM(kw_total) AS kw_total, SUM(kva_total) AS kva_total  FROM live_data_updates  WHERE device_id IN ($user_devices) AND active_device=1";

	if ($result = mysqli_query($conn, $sql_active_status)) {
		if (mysqli_num_rows($result) > 0) {
			while ($rl = mysqli_fetch_assoc($result)) {
				$kw = $rl['kw_total'];
                $kva = $rl['kw_total']; // Check if this should be kva_total
            }
        }
        mysqli_free_result($result);
    }

    // Fetch data for on/off status
    $sql = "SELECT on_off_status, COUNT(*) as count FROM (SELECT * FROM live_data_updates WHERE device_id IN ($user_devices) AND active_device=1) AS tb1    GROUP BY on_off_status";

    if ($result = mysqli_query($conn, $sql)) {
    	if (mysqli_num_rows($result) > 0) {
    		while ($r = mysqli_fetch_assoc($result)) {
    			$on_off_status = $r['on_off_status'];

    			switch ($on_off_status) {
    				case "1":
    				$auto_system_on += $r['count'];
    				break;
    				case "3":
    				$auto_system_on += $r['count'];
    				break;
    				case "4":
    				$auto_system_on += $r['count'];
    				break;
    				case "5":
    				$manual_on = $r['count'];
    				break;
    				default:
    				$off += $r['count'];
    				break;
    			}
    		}
    	}
    	mysqli_free_result($result);
    }

    mysqli_close($conn);
}

// Calculate uninstalled devices
$uninstalled_devices = $total_switch_point - $installed_switch_points;

// Perform calculations
$kw = round($kw * 1000, 3);
$total_load = 0;
$off_percentage = 0;

if ($installed_lights <= 0 || $installed_lights == null) {
	$off_percentage = "--";
	$total_load = "--";
	$installed_lights = "--";
}

if ($installed_load > 0 && $installed_load != null && $installed_load != "") {
	if ($kw != 0) {
		$total_load = round(($kw / $installed_load) * 100, 2);
	}
} else {
	$installed_load = 0;
}

if ($total_load > 0 && $total_load <= 100) {
	$off_percentage = round((100 - $total_load), 2);
} else if ($total_load > 100) {
	$total_load = 100;
	$off_percentage = 0;
} else {
	$off_percentage = 100;
	if ($installed_lights <= 0 || $installed_lights == null) {
		$off_percentage = "--";
		$total_load = "--";
		$installed_lights = "--";
	}
}

// Calculations for power consumption savings
$TotalPowerConsumed = $kwh;
$TotalPowerConsumedHPSV = $TotalPowerConsumed * 10 / 7;
$unitsSaved = $TotalPowerConsumedHPSV - $TotalPowerConsumed;
$amountSaved = $unitsSaved * 6.25;
$amountCo2 = $unitsSaved * 0.82;

// Prepare response array
$return_response = array(
	"TOTAL_UNITS" => $total_switch_point,
	"SWITCH_POINTS" => $installed_switch_points,
	"UNISTALLED_UNITS" => $uninstalled_devices,
	"ACTIVE_SWITCH" => $active_switch_points,
	"POOR_NW" => $poor_network,
	"POWER_FAILURE" => $power_failure_count,
	"FAULTY_SWITCH" => $inactive_switch_points,
	"TOTAL_LIGHTS" => $installed_lights,
	"ON_LIGHTS" => $total_load,
	"OFF_LIGHT" => $off_percentage,
	"FAULTY_LIGHT" => "0",
	"INSTALLED_LOAD" => $installed_load,
	"ACTIVE_LOAD" => $kw,
	"KWH" => round($kwh, 2),
	"KVAH" => "0.12",
	"SAVED_UNITS" => round($unitsSaved, 2),
	"SAVED_AMOUNT" => round($amountSaved, 2),
	"SAVED_CO2" => round($amountCo2, 2),
	"ON_UNITS" => $auto_system_on,
	"MANUAL_ON" => $manual_on,
	"OFF" => $off
);
} else {
    // Handle if POST data is not set
	$return_response = array(
		"SWITCH_POINTS" => "--",
		"ACTIVE_SWITCH" => "--",
		"FAULTY_SWITCH" => "--",
		"TOTAL_LIGHTS" => "--",
		"ON_LIGHTS" => "--",
		"OFF_LIGHT" => "--",
		"FAULTY_LIGHT" => "--",
		"INSTALLED_LOAD" => "--",
		"ACTIVE_LOAD" => "--",
		"KWH" => "--",
		"KVAH" => "--",
		"SAVED_UNITS" => "--",
		"SAVED_AMOUNT" => "--",
		"SAVED_CO2" => "--",
		"POOR_NW" => $poor_network,
		"TOTAL_UNITS" => $total_switch_point,
		"UNISTALLED_UNITS" => $uninstalled_devices,
		"POWER_FAILURE" => $power_failure_count,
		"ON_UNITS" => $auto_system_on,
		"MANUAL_ON" => $manual_on,
		"OFF" => $off
	);
}

// Return JSON response
echo json_encode($return_response);
?>
