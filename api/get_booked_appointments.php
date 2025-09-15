<?php
// Disable any error output that might interfere with JSON
ini_set('display_errors', 0);
error_reporting(0);
// Set content type header
header('Content-Type: application/json');
// Include database connection
require_once(__DIR__ . "/config/db.php");

// Check if doctor_id is provided
if (!isset($_GET['doctor_id'])) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Doctor ID is required']);
    exit;
}

$doctor_id = (int)$_GET['doctor_id'];

// Log the request
error_log("Fetching booked appointments for doctor_id: $doctor_id");

// Get database connection
try {
    $conn = getDbConnection();
} catch (Exception $e) {
    error_log("Database connection error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'status' => 'error', 
        'message' => 'Database connection failed',
        'error_details' => $e->getMessage()
    ]);
    exit;
}

// Get booked appointments
$query = "SELECT appointment_date, appointment_time 
          FROM appointment 
          WHERE doctor_id = ? AND a_status != 2
          ORDER BY appointment_date, appointment_time";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $doctor_id);
$stmt->execute();
$result = $stmt->get_result();

$bookedAppointments = [];
while ($row = $result->fetch_assoc()) {
    $date = $row['appointment_date'];
    $time = $row['appointment_time'];
    
    if (!isset($bookedAppointments[$date])) {
        $bookedAppointments[$date] = [];
    }
    
    $bookedAppointments[$date][] = $time;
}

// Convert to the format expected by the app
$formattedAppointments = [];
foreach ($bookedAppointments as $date => $times) {
    $formattedAppointments[] = [
        'date' => $date,
        'time' => $times
    ];
}

error_log("Found " . count($formattedAppointments) . " dates with booked appointments");

echo json_encode([
    'status' => 'success',
    'appointments' => $formattedAppointments,
    'count' => count($formattedAppointments)
]);

$stmt->close();
$conn->close();
?>