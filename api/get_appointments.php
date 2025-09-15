<?php
// Disable any error output that might interfere with JSON
ini_set('display_errors', 0);
error_reporting(0);
// Set content type header
header('Content-Type: application/json');
// Include database connection
require_once(__DIR__ . "/config/db.php");

// Check if user_id is provided
if (!isset($_GET['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'User ID is required']);
    exit;
}

$user_id = (int)$_GET['user_id'];

// Get database connection using the same method as other pages
try {
    $conn = getDbConnection();
} catch (Exception $e) {
    echo json_encode([
        'status' => 'error', 
        'message' => 'Database connection failed',
        'error_details' => $e->getMessage()
    ]);
    exit;
}

// Log the request for debugging
error_log("Fetching appointments for user_id: $user_id");

// FIXED: Use separate date and time columns instead of datetime
// FIXED: Corrected column name from a_satus to a_status
$query = "SELECT a.id, a.doctor_id, a.patient_id, 
                 a.appointment_date, a.appointment_time, 
                 a.reason, a.patient_desc, a.a_status,
                 u.first_name, u.last_name, u.doctor_type, u.doctor_address 
          FROM appointment a
          JOIN users u ON a.doctor_id = u.id
          WHERE a.patient_id = ?
          ORDER BY a.appointment_date DESC, a.appointment_time DESC";

// Prepare statement to prevent SQL injection
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if (!$result) {
    // Log the actual MySQL error
    $error = $conn->error;
    error_log("MySQL Error: " . $error);
    echo json_encode([
        'status' => 'error', 
        'message' => 'Database query failed',
        'error_details' => $error
    ]);
    exit;
}

$appointments = [];
while ($row = $result->fetch_assoc()) {
    // Format doctor name
    $row['doctor_name'] = $row['first_name'] . ' ' . $row['last_name'];
    unset($row['first_name']);
    unset($row['last_name']);
    
    // FIXED: We already have separate date and time columns
    // No need to split datetime
    
    // Format status - FIXED: using a_status instead of a_satus
    $status = $row['a_status'];
    $row['status'] = getStatusString($status);
    $row['status_code'] = $status;
    
    $appointments[] = $row;
}

// Return appointments
echo json_encode([
    'status' => 'success',
    'appointments' => $appointments,
    'count' => count($appointments)
]);

// Close database connection
$stmt->close();
$conn->close();

// Helper function to convert status code to string
function getStatusString($statusCode) {
    switch ($statusCode) {
        case 0: return "Pending";
        case 1: return "Confirmed";
        case 2: return "Cancelled";
        case 3: return "Completed";
        default: return "Unknown";
    }
}
?>