<?php
// Disable any error output that might interfere with JSON
ini_set('display_errors', 0);
error_reporting(0);

// Set content type header
header('Content-Type: application/json');

// Include database connection
require_once(__DIR__ . "/config/db.php");

// Check if appointment_id is provided
if (!isset($_GET['appointment_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Appointment ID is required']);
    exit;
}

$appointment_id = (int)$_GET['appointment_id'];

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

// Get appointment details
$query = "SELECT a.*, u.first_name, u.last_name, u.doctor_type, u.doctor_address 
          FROM appointment a
          JOIN users u ON a.doctor_id = u.id
          WHERE a.id = $appointment_id";
$result = mysqli_query($conn, $query);

if (!$result) {
    echo json_encode(['status' => 'error', 'message' => 'Database error']);
    exit;
}

if (mysqli_num_rows($result) == 0) {
    echo json_encode(['status' => 'error', 'message' => 'Appointment not found']);
    exit;
}

$appointment = mysqli_fetch_assoc($result);
$appointment['doctor_name'] = $appointment['first_name'] . ' ' . $appointment['last_name'];
unset($appointment['first_name']);
unset($appointment['last_name']);

// Split datetime into date and time
$datetime = new DateTime($appointment['datetime']);
$appointment['date'] = $datetime->format('Y-m-d');
$appointment['time'] = $datetime->format('H:i:s');

// Format status
$status = $appointment['a_satus']; // Note: it's a_satus not a_status
$appointment['status'] = getStatusString($status);
$appointment['status_code'] = $status;

// Return appointment details
echo json_encode([
    'status' => 'success',
    'appointment' => $appointment
]);

// Close database connection
mysqli_close($conn);

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