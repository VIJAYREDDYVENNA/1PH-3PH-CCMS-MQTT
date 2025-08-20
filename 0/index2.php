<?php
require_once '../base-path/config-path.php';
require_once BASE_PATH . 'config_db/config.php';
require_once BASE_PATH . 'session/session-manager.php';

$conn = mysqli_connect(HOST, USERNAME, PASSWORD, DB_ALL);
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

$frame = "auto;1;20;31;0;0;0;6;230;45;121;120;1500;123;1;110.5;6;235;234;38;130;128;1480;980;1;95.0;4;228;234;42;115;112;1520;1025;0;0;3;234;240;40;122;120;1490;990;1;105.8;1;233;234;36;119;118;1510;1001;1;98.7;5;237;39;234;117;116;1475;970;1;20;0;0;0;0;1;15;1;12;1;26;2025-04-24 16:44:20";

// Step 1: Parse frame
$array = array_map('trim', explode(";", $frame));
echo count($array);
if (count($array) !== 77) {
    die("Invalid data length: expected 77, got " . count($array));
}

// Step 2: Extract current server time and server frame time
date_default_timezone_set('Asia/Kolkata');
$date_time = date("Y-m-d H:i:s");          // Current server time
$server_time = $array[76];                // From the frame (last item)

// Get actual column information from the database
$columns = [];
$result = mysqli_query($conn, "SHOW COLUMNS FROM live_frame_data");
while ($row = mysqli_fetch_assoc($result)) {
    if ($row['Field'] != 'id') {  // Skip the id column
        $columns[] = $row['Field'];
    }
}
$column_count = count($columns);
echo "Table has " . $column_count . " columns (excluding id)<br>";

// Build the SQL statement dynamically based on actual columns
$sql = "INSERT INTO live_frame_data (";
$sql .= implode(", ", $columns);
$sql .= ") VALUES (";
$sql .= implode(", ", array_fill(0, $column_count, "?"));
$sql .= ")";

$placeholder_count = substr_count($sql, "?");
echo "SQL has " . $placeholder_count . " placeholders<br>";

// Create parameter array
$params = [];

// Fill in values for the first 73 elements we have from the frame
for ($i = 0; $i < min(73, $column_count - 2); $i++) {
    $params[] = $array[$i];
}

// If we have more columns than data in our frame, pad with NULL values
while (count($params) < $column_count - 2) {
    $params[] = NULL;
}

// Add the two date fields
$params[] = $date_time;      // date_time
$params[] = $server_time;    // server_date_time

echo "Parameter count: " . count($params) . "<br>";

if (count($params) != $placeholder_count) {
    die("Parameter count (" . count($params) . ") doesn't match placeholder count (" . $placeholder_count . ")");
}

// Prepare and execute statement
$stmt = mysqli_prepare($conn, $sql);
if (!$stmt) {
    die("Prepare failed: " . mysqli_error($conn));
}

// Create type string for bind_param
$types = '';
foreach ($params as $param) {
    if (is_null($param)) {
        $types .= 's';  // Use string type for NULL values too
    } elseif (is_int($param)) {
        $types .= 'i';
    } elseif (is_float($param)) {
        $types .= 'd';
    } else {
        $types .= 's';
    }
}

// Bind parameters
mysqli_stmt_bind_param($stmt, $types, ...$params);

if (mysqli_stmt_execute($stmt)) {
    echo "Frame data inserted successfully.<br>";
} else {
    echo "Error inserting frame data: " . mysqli_stmt_error($stmt) . "<br>";
}
mysqli_stmt_close($stmt);

// Step 4: Insert into motor_data table
$motor_query = "INSERT INTO motor_data (
    motor_id, voltage, current, energy_kwh, frequency,
    on_off_status, speed, flow_rate, total_running_hours,
    date_time, server_date_time
) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

$stmt_motor = $conn->prepare($motor_query);
if (!$stmt_motor) {
    die("Prepare failed for motor data: " . mysqli_error($conn));
}

for ($i = 0; $i < 6; $i++) {
    // Each motor block starts at index 4 and has 10 fields
    $motor_start_index = 4 + ($i * 10);

    $motor_id = "motor_" . ($i + 1);
    
    // Check if we have enough data for this motor
    if ($motor_start_index + 9 >= count($array)) {
        echo "Warning: Not enough data for $motor_id<br>";
        continue;
    }
    
    $on_off_status = $array[$motor_start_index];
    $flow_rate = $array[$motor_start_index + 1];
    $voltage = $array[$motor_start_index + 4];
    $current = $array[$motor_start_index + 5];
    $energy_kwh = $array[$motor_start_index + 6];
    $frequency = $array[$motor_start_index + 7];
    $speed = $array[$motor_start_index + 8];
    $total_running_hours = $array[$motor_start_index + 9];

    $stmt_motor->bind_param(
        "sddddddddss",
        $motor_id,
        $voltage,
        $current,
        $energy_kwh,
        $frequency,
        $on_off_status,
        $speed,
        $flow_rate,
        $total_running_hours,
        $date_time,
        $server_time
    );

    if (!$stmt_motor->execute()) {
        echo "Error inserting $motor_id: " . $stmt_motor->error . "<br>";
    } else {
        echo "$motor_id inserted.<br>";
    }
}

$stmt_motor->close();
$conn->close();