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

// Log the request for debugging
error_log("Fetching appointments for doctor_id: $doctor_id");

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

// Verify that the user is a doctor
$checkDoctorQuery = "SELECT id, first_name, last_name, is_doctor FROM users WHERE id = ?";
$stmt = $conn->prepare($checkDoctorQuery);
$stmt->bind_param("i", $doctor_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    error_log("Doctor not found with ID: $doctor_id");
    http_response_code(404);
    echo json_encode(['status' => 'error', 'message' => 'Doctor not found']);
    exit;
}

$doctor = $result->fetch_assoc();
if ($doctor['is_doctor'] != 1) {
    error_log("User with ID: $doctor_id is not a doctor. Name: " . $doctor['first_name'] . " " . $doctor['last_name']);
    http_response_code(403);
    echo json_encode(['status' => 'error', 'message' => 'Access denied: not a doctor']);
    exit;
}

error_log("Doctor verified: " . $doctor['first_name'] . " " . $doctor['last_name'] . " (ID: $doctor_id)");

// Get appointments for the doctor - using the exact same query structure as the debug endpoint
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
    
    // Add debug information
    $appointment['_debug'] = [
        'doctor_id' => $doctor_id,
        'raw_status' => $row['a_status'],
        'current_date' => date('Y-m-d'),
        'current_time' => date('H:i:s'),
        'is_future' => isFutureAppointment($row['appointment_date'], $row['appointment_time'])
    ];
    
    $appointments[] = $appointment;
}

// Log the results
error_log("Found " . count($appointments) . " appointments for doctor_id: $doctor_id");

echo json_encode([
    'status' => 'success',
    'appointments' => $appointments,
    'count' => count($appointments),
    'doctor_info' => [
        'id' => $doctor['id'],
        'name' => $doctor['first_name'] . ' ' . $doctor['last_name'],
        'is_doctor' => (bool)$doctor['is_doctor']
    ],
    'debug_info' => [
        'current_datetime' => date('Y-m-d H:i:s'),
        'query' => $query
    ]
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

function isFutureAppointment($date, $time) {
    try {
        $appointmentDateTime = new DateTime($date . ' ' . $time);
        $currentDateTime = new DateTime();
        return $appointmentDateTime > $currentDateTime;
    } catch (Exception $e) {
        error_log("Error checking if appointment is future: " . $e->getMessage());
        return false;
    }
}
?>