<?php
require_once '../../base-path/config-path.php';
require_once BASE_PATH_1 . 'config_db/config.php';
require_once BASE_PATH_1 . 'session/session-manager.php';

// Check session
SessionManager::checkSession();

$sessionVars = SessionManager::SessionVariables();
$user_id = $sessionVars['user_id'];
$role = $sessionVars['role'];

// Process POST requests
if ($_SERVER["REQUEST_METHOD"] === "POST") {
	$records = filter_input(INPUT_POST, 'RECORDS', FILTER_SANITIZE_STRING);
	$response = array();
	$data = "";

	// Connect to database
	$conn = mysqli_connect(HOST, USERNAME, PASSWORD, DB_ALL);
	if (!$conn) {
		$response[] = '<tr><td colspan="3" class="text-center text-danger">Connection failed: ' . mysqli_connect_error() . '</td></tr>';
		echo json_encode($response);
		exit;
	}

	$stmt = null;

	// Handle different record types
	if ($records === "LATEST") {
		$sql = "SELECT id, frame as frame_data, date_time FROM `crc_fail_frame_data_table` ORDER BY date_time DESC LIMIT 20";
		$stmt = mysqli_prepare($conn, $sql);
	} elseif ($records === "ADD" && isset($_POST['DATE_TIME'])) {
		$raw_date = filter_input(INPUT_POST, 'DATE_TIME', FILTER_SANITIZE_STRING);
		// Format date time
		$formatted_date = date('Y-m-d H:i:s', strtotime($raw_date));

		$sql = "SELECT id, frame as frame_data, date_time FROM `crc_fail_frame_data_table` WHERE date_time < ? ORDER BY date_time DESC LIMIT 20";
		$stmt = mysqli_prepare($conn, $sql);
		mysqli_stmt_bind_param($stmt, 's', $formatted_date);
	} elseif ($records === "DATE" && isset($_POST['DATE'])) {
		$date = trim(filter_input(INPUT_POST, 'DATE', FILTER_SANITIZE_STRING));
		$date_formatted = date('Y-m-d', strtotime($date));

		$sql = "SELECT id, frame as frame_data, date_time FROM `crc_fail_frame_data_table` WHERE DATE(date_time) = ? ORDER BY date_time DESC LIMIT 100";
		$stmt = mysqli_prepare($conn, $sql);
		mysqli_stmt_bind_param($stmt, 's', $date_formatted);
	} else {
		$response[] = '<tr><td colspan="3" class="text-center text-danger">Missing or invalid parameters</td></tr>';
		echo json_encode($response);
		mysqli_close($conn);
		exit;
	}

	// Execute query
	if ($stmt && mysqli_stmt_execute($stmt)) {
		$result = mysqli_stmt_get_result($stmt);

		if (mysqli_num_rows($result) > 0) {
			$sno = 1;
			$table_data = "";

			// Loop through results
			while ($row = mysqli_fetch_assoc($result)) {
				$id = $row['id'];
				$frame_data = htmlspecialchars($row['frame_data']);
				$date_time = $row['date_time'];

				// Split the frame data by semicolon
				$parts = explode(';', $frame_data);

				// How many parts to keep (for example, 5 parts)
				$max_parts = 30;

				if (count($parts) > $max_parts) {
					// Join only the first $max_parts and add '...'
					$truncated_frame = implode(';', array_slice($parts, 0, $max_parts)) . ';...';
				} else {
					// If less than or equal, no need to truncate
					$truncated_frame = $frame_data;
				}

				// Create table row
				$table_data .= '<tr>
					<td class="text-center" style="width: 60px;">' . $sno . '</td>
					<td>
						<div class="frame-data">' . $truncated_frame . '</div>
						<div id="full-frame-' . $id . '" class="frame-data-full">' . $frame_data . '</div>
						<span class="show-more-btn" id="toggle-btn-' . $id . '" data-id="' . $id . '">
							<i class="fas fa-chevron-down me-1"></i>Show More
						</span>
					</td>
					<td style="width: 180px;">' . $date_time . '</td>
				</tr>';

				$sno++;
			}


			$response[] = $table_data;
		} else {
			$response[] = '<tr><td colspan="3" class="text-center no-data-message">No records found for the specified criteria</td></tr>';
		}

		mysqli_stmt_close($stmt);
	} else {
		$response[] = '<tr><td colspan="3" class="text-center text-danger">Failed to execute query: ' . mysqli_error($conn) . '</td></tr>';
	}

	mysqli_close($conn);
	echo json_encode($response);
} else {
	// If not a POST request
	echo json_encode(array('<tr><td colspan="3" class="text-center text-danger">Invalid request method</td></tr>'));
}
