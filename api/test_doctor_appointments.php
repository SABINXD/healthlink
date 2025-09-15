<?php
// Disable any error output that might interfere with JSON
ini_set('display_errors', 0);
error_reporting(0);
// Set content type header
header('Content-Type: application/json');
// Include database connection
require_once(__DIR__ . "/config/db.php");

// Test with doctor ID 174 (from your debug output)
$doctor_id = 174;

// Log the request for debugging
error_log("Testing appointments for doctor_id: $doctor_id");

// Get database connection
try {
    $conn = getDbConnection();
} catch (Exception $e) {
    error_log("Database connection error: " . $e->getMessage());
    echo json_encode([
        'status' => 'error', 
        'message' => 'Database connection failed',
        'error_details' => $e->getMessage()
    ]);
    exit;
}

// Get appointments for the doctor
$query = "SELECT a.id, a.doctor_id, a.patient_id, a.appointment_date, a.appointment_time, a.reason, a.patient_desc, a.a_status, a.created_at, u.first_name, u.last_name, u.phone, u.email FROM appointment a JOIN users u ON a.patient_id = u.id WHERE a.doctor_id = ? ORDER BY a.appointment_date ASC, a.appointment_time ASC";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $doctor_id);
$stmt->execute();
$result = $stmt->get_result();

$appointments = [];
while ($row = $result->fetch_assoc()) {
    $appointment = [
        'id' => $row['id'],
        'patient_id' => $row['patient_id'],
        'patient_name' => $row['first_name'] . ' ' . $row['last_name'],
        'patient_phone' => $row['phone'],
        'patient_email' => $row['email'],
        'appointment_date' => $row['appointment_date'],
        'appointment_time' => $row['appointment_time'],
        'reason' => $row['reason'],
        'patient_desc' => $row['patient_desc'],
        'status' => getStatusString($row['a_status']),
        'status_code' => $row['a_status']
    ];
    
    $appointments[] = $appointment;
}

// Log the results
error_log("Found " . count($appointments) . " appointments for doctor_id: $doctor_id");

echo json_encode([
    'status' => 'success',
    'appointments' => $appointments,
    'count' => count($appointments),
    'doctor_id' => $doctor_id
]);

$stmt->close();
$conn->close();

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