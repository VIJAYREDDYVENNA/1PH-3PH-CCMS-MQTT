<?php
require_once '../../base-path/config-path.php';
require_once BASE_PATH_1 . 'config_db/config.php';
require_once BASE_PATH_1 . 'session/session-manager.php';
SessionManager::checkSession();

$sessionVars = SessionManager::SessionVariables();
$mobile_no     = $sessionVars['mobile_no'];
$user_id       = $sessionVars['user_id'];
$role          = $sessionVars['role'];
$user_login_id = $sessionVars['user_login_id'];
$user_name     = $sessionVars['user_name'];
$user_email    = $sessionVars['user_email'];

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['error' => 'Only POST method is allowed']);
    exit;
}
$conn = mysqli_connect(HOST, USERNAME, PASSWORD, DB_ALL);
	if (!$conn) {
		echo '<tr><td class="text-danger" colspan="75">Connection failed: ' . mysqli_connect_error() . '</td></tr>';
		exit;
	}
// Get request data
$postData = json_decode(file_get_contents('php://input'), true);
$action = isset($postData['action']) ? $postData['action'] : '';
$startDate = isset($postData['startDate']) ? $postData['startDate'] : null;
$endDate = isset($postData['endDate']) ? $postData['endDate'] : null;

// Validate dates
if ($startDate && !validateDate($startDate)) {
    echo json_encode(['error' => 'Invalid start date format']);
    exit;
}

if ($endDate && !validateDate($endDate)) {
    echo json_encode(['error' => 'Invalid end date format']);
    exit;
}

// Process based on action
switch ($action) {
    case 'getSummaryMetrics':
        getSummaryMetrics($startDate, $endDate);
        break;
    case 'getWaterFlowTrends':
        // getWaterFlowTrends($startDate, $endDate, $postData['period']);
        getWaterFlowTrends($startDate, $endDate);

        break;
    case 'getDistributionByMotors':
        getDistributionByMotors($startDate, $endDate);
        break;
    case 'getMotorRuntimeComparison':
        getMotorRuntimeComparison($startDate, $endDate);
        break;
    case 'getMotorEnergyComparison':
        getMotorEnergyComparison($startDate, $endDate);
        break;
    case 'getPerformanceMetrics':
        getPerformanceMetrics($startDate, $endDate);
        break;
    default:
        echo json_encode(['error' => 'Invalid action']);
        break;
}

// Function to validate date format (YYYY-MM-DD)
function validateDate($date, $format = 'Y-m-d')
{
    $d = DateTime::createFromFormat($format, $date);
    return $d && $d->format($format) === $date;
}

// Get summary metrics
function getSummaryMetrics($startDate, $endDate)
{
    global $conn;
    
    try {
        // Prepare the SQL query with date filtering
        $dateFilter = "";
        if ($startDate && $endDate) {
            $dateFilter = " WHERE date(date_time) BETWEEN '$startDate' AND '$endDate'";
        }
        // Total Water Delivered
        $waterQuery = "SELECT SUM(water_delivered) as total_water FROM motor_metrics" . $dateFilter;
        $waterResult = mysqli_query($conn, $waterQuery);
        
        if (!$waterResult) {
            throw new Exception("Error executing water query: " . mysqli_error($conn));
        }
        
        // Energy Consumption
        $energyQuery = "SELECT SUM(energy_consumption) as total_energy FROM motor_metrics" . $dateFilter;
        $energyResult = mysqli_query($conn, $energyQuery);
        
        if (!$energyResult) {
            throw new Exception("Error executing energy query: " . mysqli_error($conn));
        }
        
        // Fetch results
        $waterData = mysqli_fetch_assoc($waterResult);
        $energyData = mysqli_fetch_assoc($energyResult);
        
        // Prepare response
        $response = [
            'total_water' => (float)$waterData['total_water'] ?? 0,
            'total_energy' => (float)$energyData['total_energy'] ?? 0
        ];
        
        echo json_encode($response);
        
        // Free result sets
        mysqli_free_result($waterResult);
        mysqli_free_result($energyResult);
        
    } catch (Exception $e) {
        echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
    }
}

// Get water flow trends data
function getWaterFlowTrends($startDate, $endDate)
{
    global $conn;
    
    try {
        $dateFormat = '';
        $groupBy = '';
        
        // Set date format and group by clause based on period
        // switch ($period) {
        //     case 'daily':
        //         $dateFormat = '%Y-%m-%d';
        //         $groupBy = 'DATE(date_time)';
        //         break;
        //     case 'weekly':
        //         $dateFormat = '%Y-%u'; // Year and week number
        //         $groupBy = 'YEARWEEK(date_time)';
        //         break;
        //     case 'monthly':
        //         $dateFormat = '%Y-%m';
        //         $groupBy = 'YEAR(date_time), MONTH(date_time)';
        //         break;
        //     default:
        //         echo json_encode(['error' => 'Invalid period']);
        //         return;
        // }
        
        // Prepare the SQL query
        // $sql = "SELECT 
        //         DATE_FORMAT(date_time, '$dateFormat') as period_label,
        //         AVG(water_flow_rate) as avg_flow_rate
        //     FROM motor_metrics
        //     WHERE date_time BETWEEN '$startDate' AND '$endDate'
        //     GROUP BY " . $groupBy . "
        //     ORDER BY date_time";
         $sql = "SELECT 
                date(date_time) as period_label,
                AVG(water_flow_rate) as avg_flow_rate
            FROM motor_metrics
            WHERE date_time BETWEEN '$startDate' AND '$endDate'
            GROUP BY period_label
            ORDER BY date_time";
        
        $result = mysqli_query($conn, $sql);
        
        if (!$result) {
            throw new Exception("Error executing query: " . mysqli_error($conn));
        }
        
        // Format the response
        $labels = [];
        $flowData = [];
        
        while ($row = mysqli_fetch_assoc($result)) {
            $labels[] = $row['period_label'];
            $flowData[] = (float)$row['avg_flow_rate'];
        }
        
        $response = [
            'labels' => $labels,
            'flowData' => $flowData
        ];
        
        echo json_encode($response);
        
        // Free result set
        mysqli_free_result($result);
        
    } catch (Exception $e) {
        echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
    }
}

// Get water distribution by motors
function getDistributionByMotors($startDate, $endDate)
{
    global $conn;
    
    try {
        // Prepare the SQL query
        $sql = "SELECT 
                motor_id,
                SUM(water_delivered) as total_water
            FROM motor_metrics
            WHERE date(date_time) BETWEEN '$startDate' AND '$endDate'
            GROUP BY motor_id
            ORDER BY motor_id";
        
        $result = mysqli_query($conn, $sql);
        
        if (!$result) {
            throw new Exception("Error executing query: " . mysqli_error($conn));
        }
        
        $results = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $results[] = $row;
        }
        
        // Calculate total water for percentage calculation
        $totalWater = 0;
        foreach ($results as $row) {
            $totalWater += (float)$row['total_water'];
        }
        
        // Format the response
        $labels = [];
        $data = [];
        
        foreach ($results as $row) {
            $labels[] =  $row['motor_id'];
            // Calculate percentage if total water is not zero
            $percentage = ($totalWater > 0) ? ((float)$row['total_water'] / $totalWater) * 100 : 0;
            $data[] = round($percentage, 1);
        }
        
        $response = [
            'labels' => $labels,
            'data' => $data
        ];
        
        echo json_encode($response);
        
        // Free result set
        mysqli_free_result($result);
        
    } catch (Exception $e) {
        echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
    }
}

// Get motor runtime comparison
function getMotorRuntimeComparison($startDate, $endDate)
{
    global $conn;
    
    try {
        // Prepare the SQL query
        $sql = "SELECT 
                motor_id,
                SUM(runtime_hours) as total_runtime
            FROM motor_metrics
            WHERE date(date_time) BETWEEN '$startDate' AND '$endDate'
            GROUP BY motor_id
            ORDER BY motor_id";
        
        $result = mysqli_query($conn, $sql);
        
        if (!$result) {
            throw new Exception("Error executing query: " . mysqli_error($conn));
        }
        
        // Format the response
        $labels = [];
        $runtimeData = [];
        
        while ($row = mysqli_fetch_assoc($result)) {
            $labels[] =  $row['motor_id'];
            $runtimeData[] = round((float)$row['total_runtime'], 1);
        }
        
        $response = [
            'labels' => $labels,
            'runtime' => $runtimeData
        ];
        
        echo json_encode($response);
        
        // Free result set
        mysqli_free_result($result);
        
    } catch (Exception $e) {
        echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
    }
}

// Get motor energy comparison
function getMotorEnergyComparison($startDate, $endDate)
{
    global $conn;
    
    try {
        // Prepare the SQL query
        $sql = "SELECT 
                motor_id,
                SUM(energy_consumption) as total_energy
            FROM motor_metrics
            WHERE date(date_time) BETWEEN '$startDate' AND '$endDate'
            GROUP BY motor_id
            ORDER BY motor_id";
        
        $result = mysqli_query($conn, $sql);
        
        if (!$result) {
            throw new Exception("Error executing query: " . mysqli_error($conn));
        }
        
        // Format the response
        $labels = [];
        $energyData = [];
        
        while ($row = mysqli_fetch_assoc($result)) {
            $labels[] =  $row['motor_id'];
            $energyData[] = round((float)$row['total_energy'], 0);
        }
        
        $response = [
            'labels' => $labels,
            'energy' => $energyData
        ];
        
        echo json_encode($response);
        
        // Free result set
        mysqli_free_result($result);
        
    } catch (Exception $e) {
        echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
    }
}

// Get detailed performance metrics for all motors
function getPerformanceMetrics($startDate, $endDate)
{
    global $conn;
    
    try {
        // Prepare the SQL query
        $sql = "SELECT motor_id,SUM(runtime_hours) as total_runtime,SUM(water_delivered) as total_water,SUM(energy_consumption) as total_energy
            FROM motor_metrics WHERE date(date_time) BETWEEN '$startDate' AND '$endDate'GROUP BY motor_id ORDER BY motor_id";
        
        $result = mysqli_query($conn, $sql);
        
        if (!$result) {
            throw new Exception("Error executing query: " . mysqli_error($conn));
        }
        
        // Format the response
        $metrics = [];
        
        while ($row = mysqli_fetch_assoc($result)) {
            $metrics[] = [
                'motor_id' => $row['motor_id'],
                'total_runtime' => round((float)$row['total_runtime'], 1),
                'total_water' => round((float)$row['total_water'], 0),
                'total_energy' => round((float)$row['total_energy'], 0)
            ];
        }
        
        echo json_encode(['metrics' => $metrics]);
        
        // Free result set
        mysqli_free_result($result);
        
    } catch (Exception $e) {
        echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
    }
}
?>