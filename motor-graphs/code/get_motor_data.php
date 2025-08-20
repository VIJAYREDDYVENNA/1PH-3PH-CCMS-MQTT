<?php
require_once '../../base-path/config-path.php';
require_once BASE_PATH_1 . 'config_db/config.php';
require_once BASE_PATH_1 . 'session/session-manager.php';
SessionManager::checkSession();
$sessionVars = SessionManager::SessionVariables();

header('Content-Type: application/json');

// Get parameters from request
$parameter = isset($_POST['parameter']) ? $_POST['parameter'] : '';
$timeRange = isset($_POST['timeRange']) ? $_POST['timeRange'] : '';
$voltageType = isset($_POST['voltageType']) ? $_POST['voltageType'] : 'ALL';
$selectedMotors = isset($_POST['selectedMotors']) ? json_decode($_POST['selectedMotors'], true) : [];
$comparisonMode = isset($_POST['comparisonMode']) ? $_POST['comparisonMode'] : 'INDIVIDUAL';

// If selectedMotors is still a string after json_decode, it might be a comma-separated list
if (!is_array($selectedMotors)) {
    // Try to convert from comma-separated string to array
    $selectedMotors = explode(',', $selectedMotors);
    // Remove any empty elements
    $selectedMotors = array_filter($selectedMotors, 'strlen');
}

// Validate input
if (empty($parameter) || empty($timeRange) || empty($selectedMotors)) {
    echo json_encode([
        'success' => false,
        'message' => 'Missing required parameters'
    ]);
    exit;
}

try {
    $conn = mysqli_connect(HOST, USERNAME, PASSWORD, DB_ALL);

    if (!$conn) {
        throw new Exception("Database connection failed: " . mysqli_connect_error());
    }

    // Array to store results
    $result = [
        'success' => true,
        'data' => [],
        'stats' => []
    ];

    // Get current date
    $currentDate = date('Y-m-d');
    $currentTime = date('H:i:s');

    // Define which datetime column to use
    $datetimeColumn = 'date_time';

    // Build query based on time range
    $timeCondition = '';
    $groupBy = '';
    $dateFormat = '';
    $orderBy = '';

    switch ($timeRange) {
        case 'LATESTHOUR':
            // Latest hour data with minute-by-minute resolution - NO MAX NEEDED
            $timeCondition = "$datetimeColumn >= DATE_SUB(NOW(), INTERVAL 1 HOUR)";
            $groupBy = "MINUTE($datetimeColumn), HOUR($datetimeColumn), DATE($datetimeColumn)";
            $dateFormat = "DATE_FORMAT($datetimeColumn, '%H:%i')";
            $orderBy = "DATE($datetimeColumn) ASC, HOUR($datetimeColumn) ASC, MINUTE($datetimeColumn) ASC";
            break;
        case 'LATEST':
            // Today's data with hourly grouping
            $timeCondition = "DATE($datetimeColumn) = '$currentDate'";
            $groupBy = "HOUR($datetimeColumn)";
            $dateFormat = "CONCAT(HOUR($datetimeColumn), ':00')";
            $orderBy = "HOUR($datetimeColumn) ASC";
            break;
        case 'DAY':
            // Last 24 hours data with hourly grouping
            $timeCondition = "$datetimeColumn >= DATE_SUB(NOW(), INTERVAL 1 DAY)";
            $groupBy = "HOUR($datetimeColumn), DATE($datetimeColumn)";
            $dateFormat = "CONCAT(HOUR($datetimeColumn), ':00 ', DATE_FORMAT($datetimeColumn, '%m-%d'))";
            $orderBy = "DATE($datetimeColumn) ASC, HOUR($datetimeColumn) ASC";
            break;
        case 'WEEK':
            // Last 7 days data with daily grouping
            $timeCondition = "$datetimeColumn >= DATE_SUB(NOW(), INTERVAL 7 DAY)";
            $groupBy = "DATE($datetimeColumn)";
            $dateFormat = "DATE_FORMAT($datetimeColumn, '%b %d')";
            $orderBy = "DATE($datetimeColumn) ASC";
            break;
        case 'MONTH':
            // Last 30 days data with daily grouping
            $timeCondition = "$datetimeColumn >= DATE_SUB(NOW(), INTERVAL 30 DAY)";
            $groupBy = "DATE($datetimeColumn)";
            $dateFormat = "DATE_FORMAT($datetimeColumn, '%b %d')";
            $orderBy = "DATE($datetimeColumn) ASC";
            break;
        case 'YEAR':
            // This year's data with monthly grouping
            $timeCondition = "YEAR($datetimeColumn) = YEAR(CURDATE())";
            $groupBy = "MONTH($datetimeColumn)";
            $dateFormat = "DATE_FORMAT($datetimeColumn, '%b')";
            $orderBy = "MONTH($datetimeColumn) ASC";
            break;
        default:
            throw new Exception("Invalid time range");
    }

    // Process each selected motor
    foreach ($selectedMotors as $motorId) {
        // Sanitize motor ID to prevent SQL injection
        $motorId = mysqli_real_escape_string($conn, $motorId);

        if ($parameter === 'LINE_VOLTAGE' && $voltageType === 'ALL') {
            // For ALL voltage types, we need to fetch each voltage type separately
            $motorData = [];
            $voltageTypes = ['r_y_voltage' => 'R_Y', 'y_b_voltage' => 'Y_B', 'b_r_voltage' => 'B_R'];
            $voltageStats = [
                'R_Y' => ['min' => PHP_FLOAT_MAX, 'max' => PHP_FLOAT_MIN, 'sum' => 0, 'count' => 0, 'last' => 0],
                'Y_B' => ['min' => PHP_FLOAT_MAX, 'max' => PHP_FLOAT_MIN, 'sum' => 0, 'count' => 0, 'last' => 0],
                'B_R' => ['min' => PHP_FLOAT_MAX, 'max' => PHP_FLOAT_MIN, 'sum' => 0, 'count' => 0, 'last' => 0]
            ];

            // Time-based data points map to merge values later
            $timePointsMap = [];

            // Process each voltage type separately
            foreach ($voltageTypes as $dbColumn => $displayKey) {
                if ($timeRange === 'LATESTHOUR') {
                    // For LATESTHOUR, we want all records, not just the max
                    $sql = "SELECT 
                                $dateFormat as formatted_time,
                                $datetimeColumn as date_time,
                                $dbColumn as value
                            FROM 
                                motor_data
                            WHERE 
                                motor_id = '$motorId' AND $timeCondition
                            ORDER BY 
                                $datetimeColumn ASC";
                } else {
                    // For other time ranges, we need to use a subquery to get the exact timestamp of the max value
                    $sql = "SELECT 
                                outer_query.formatted_time,
                                inner_query.date_time,
                                inner_query.value
                            FROM (
                                SELECT 
                                    $dateFormat as formatted_time,
                                    $groupBy as group_key,
                                    MAX($dbColumn) as max_value
                                FROM 
                                    motor_data
                                WHERE 
                                    motor_id = '$motorId' AND $timeCondition
                                GROUP BY 
                                    $groupBy
                            ) as outer_query
                            JOIN (
                                SELECT 
                                    $datetimeColumn as date_time,
                                    $dbColumn as value,
                                    $dateFormat as formatted_time,
                                    $groupBy as group_key
                                FROM 
                                    motor_data
                                WHERE 
                                    motor_id = '$motorId' AND $timeCondition
                            ) as inner_query
                            ON outer_query.group_key = inner_query.group_key 
                            AND outer_query.max_value = inner_query.value
                            ORDER BY 
                                $orderBy";
                }

                $query = mysqli_query($conn, $sql);

                if (!$query) {
                    throw new Exception("Query failed for $displayKey: " . mysqli_error($conn) . "\nSQL: $sql");
                }

                while ($row = mysqli_fetch_assoc($query)) {
                    $timeKey = $row['formatted_time'];
                    $value = floatval($row['value']);

                    // Create or update time point entry
                    if (!isset($timePointsMap[$timeKey])) {
                        $timePointsMap[$timeKey] = [
                            'formatted_time' => $timeKey,
                            'date_time' => $row['date_time'],
                            'R_Y' => null,
                            'Y_B' => null,
                            'B_R' => null
                        ];
                    }

                    // Set the specific voltage value
                    $timePointsMap[$timeKey][$displayKey] = $value;

                    // Update stats for this voltage type
                    $voltageStats[$displayKey]['min'] = min($voltageStats[$displayKey]['min'], $value);
                    $voltageStats[$displayKey]['max'] = max($voltageStats[$displayKey]['max'], $value);
                    $voltageStats[$displayKey]['sum'] += $value;
                    $voltageStats[$displayKey]['count']++;
                    $voltageStats[$displayKey]['last'] = $value;
                }
            }

            // Convert timePointsMap to array and ensure all points have values
            foreach ($timePointsMap as $timePoint) {
                // Ensure we have values for all voltage types (use 0 if missing)
                $dataPoint = [
                    'formatted_time' => $timePoint['formatted_time'],
                    'date_time' => $timePoint['date_time'],
                    'R_Y' => $timePoint['R_Y'] !== null ? $timePoint['R_Y'] : 0,
                    'Y_B' => $timePoint['Y_B'] !== null ? $timePoint['Y_B'] : 0,
                    'B_R' => $timePoint['B_R'] !== null ? $timePoint['B_R'] : 0
                ];

                $motorData[] = $dataPoint;
            }

            // Sort by formatted_time to ensure chronological order
            usort($motorData, function ($a, $b) use ($timeRange) {
                // For year, we need special sorting by month name
                if ($timeRange === 'YEAR') {
                    $monthOrder = [
                        'Jan' => 1, 'Feb' => 2, 'Mar' => 3, 'Apr' => 4,
                        'May' => 5, 'Jun' => 6, 'Jul' => 7, 'Aug' => 8,
                        'Sep' => 9, 'Oct' => 10, 'Nov' => 11, 'Dec' => 12
                    ];
                    return $monthOrder[substr($a['formatted_time'], 0, 3)] - $monthOrder[substr($b['formatted_time'], 0, 3)];
                }
                // For other time ranges
                return strtotime($a['date_time']) - strtotime($b['date_time']);
            });

            // Add motor data to result
            $result['data'][$motorId] = $motorData;

            // Calculate and add stats
            $motorVoltageStats = [];
            foreach (['R_Y', 'Y_B', 'B_R'] as $vType) {
                if ($voltageStats[$vType]['count'] > 0) {
                    $motorVoltageStats[$vType] = [
                        'min' => $voltageStats[$vType]['min'],
                        'max' => $voltageStats[$vType]['max'],
                        'avg' => $voltageStats[$vType]['sum'] / $voltageStats[$vType]['count'],
                        'last' => $voltageStats[$vType]['last']
                    ];
                }
            }
            $result['stats'][$motorId] = $motorVoltageStats;
        } else {
            // Standard single-parameter query
            $valueColumn = '';

            // Define value column based on parameter
            switch ($parameter) {
                case 'LINE_VOLTAGE':
                    $voltageCol = strtolower($voltageType) . "_voltage";
                    $valueColumn = "$voltageCol";
                    break;
                case 'MOTOR_VOLTAGE':
                    $valueColumn = "motor_voltage";
                    break;
                case 'MOTOR_CURRENT':
                    $valueColumn = "motor_current";
                    break;
                case 'ENERGY':
                    $valueColumn = "energy_kwh";
                    break;
                case 'REF_FREQUENCY':
                    $valueColumn = "reference_frequency";
                    break;
                case 'FREQUENCY':
                    $valueColumn = "frequency";
                    break;
                case 'SPEED':
                    $valueColumn = "speed";
                    break;
                case 'RUNNING_HOURS':
                    $valueColumn = "total_running_hours";
                    break;
                default:
                    throw new Exception("Invalid parameter selection");
            }

            if ($timeRange === 'LATESTHOUR') {
                // For LATESTHOUR, we want all records, not just the max
                $sql = "SELECT 
                            $dateFormat as formatted_time,
                            $datetimeColumn as date_time,
                            $valueColumn as value
                        FROM 
                            motor_data
                        WHERE 
                            motor_id = '$motorId' AND $timeCondition
                        ORDER BY 
                            $datetimeColumn ASC";
            } else {
                // For other time ranges, we need to use a subquery to get the exact timestamp of the max value
                $sql = "SELECT 
                            outer_query.formatted_time,
                            inner_query.date_time,
                            inner_query.value
                        FROM (
                            SELECT 
                                $dateFormat as formatted_time,
                                $groupBy as group_key,
                                MAX($valueColumn) as max_value
                            FROM 
                                motor_data
                            WHERE 
                                motor_id = '$motorId' AND $timeCondition
                            GROUP BY 
                                $groupBy
                        ) as outer_query
                        JOIN (
                            SELECT 
                                $datetimeColumn as date_time,
                                $valueColumn as value,
                                $dateFormat as formatted_time,
                                $groupBy as group_key
                            FROM 
                                motor_data
                            WHERE 
                                motor_id = '$motorId' AND $timeCondition
                        ) as inner_query
                        ON outer_query.group_key = inner_query.group_key 
                        AND outer_query.max_value = inner_query.value
                        ORDER BY 
                            $orderBy";
            }

            $query = mysqli_query($conn, $sql);

            if (!$query) {
                throw new Exception("Query failed: " . mysqli_error($conn) . "\nSQL: $sql");
            }

            $motorData = [];
            $motorStats = [
                'min' => PHP_FLOAT_MAX,
                'max' => PHP_FLOAT_MIN,
                'sum' => 0,
                'count' => 0,
                'last' => 0
            ];

            while ($row = mysqli_fetch_assoc($query)) {
                $dataPoint = [
                    'formatted_time' => $row['formatted_time'],
                    'date_time' => $row['date_time'],
                    'value' => floatval($row['value'])
                ];

                // Update stats
                $motorStats['min'] = min($motorStats['min'], $dataPoint['value']);
                $motorStats['max'] = max($motorStats['max'], $dataPoint['value']);
                $motorStats['sum'] += $dataPoint['value'];
                $motorStats['count']++;
                $motorStats['last'] = $dataPoint['value'];

                $motorData[] = $dataPoint;
            }

            // Add motor data to result
            $result['data'][$motorId] = $motorData;

            // Calculate and add stats
            if ($motorStats['count'] > 0) {
                $result['stats'][$motorId] = [
                    'min' => $motorStats['min'],
                    'max' => $motorStats['max'],
                    'avg' => $motorStats['sum'] / $motorStats['count'],
                    'last' => $motorStats['last']
                ];
            }
        }
    }

    // Close database connection
    mysqli_close($conn);

    // Return JSON response
    echo json_encode($result);
} catch (Exception $e) {
    // Return error
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);

    // Close database connection if open
    if (isset($conn) && $conn) {
        mysqli_close($conn);
    }
}