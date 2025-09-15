<?php
// Disable any error output that might interfere with JSON
ini_set('display_errors', 0);
error_reporting(0);
// Set content type header
header('Content-Type: application/json');
// Include database connection
require_once(__DIR__ . "/config/db.php");

// Check if it's a POST request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

// Get database connection
try {
    $conn = getDbConnection();
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false, 
        'message' => 'Database connection failed',
        'error_details' => $e->getMessage()
    ]);
    exit;
}

// Get POST data
$appointment_id = isset($_POST['appointment_id']) ? (int)$_POST['appointment_id'] : 0;
$doctor_id = isset($_POST['doctor_id']) ? (int)$_POST['doctor_id'] : 0;
$status = isset($_POST['status']) ? (int)$_POST['status'] : 0;

// Log the request
error_log("Updating appointment: appointment_id=$appointment_id, doctor_id=$doctor_id, status=$status");

// Validate required fields
if (empty($appointment_id) || empty($doctor_id) || !in_array($status, [1, 2])) {
    http_response_code(400);
    echo json_encode([
        'success' => false, 
        'message' => 'Invalid input data'
    ]);
    exit;
}

// Verify that the user is a doctor
$checkDoctorQuery = "SELECT is_doctor FROM users WHERE id = ?";
$stmt = $conn->prepare($checkDoctorQuery);
$stmt->bind_param("i", $doctor_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    http_response_code(404);
    echo json_encode(['success' => false, 'message' => 'Doctor not found']);
    exit;
}

$doctor = $result->fetch_assoc();
if ($doctor['is_doctor'] != 1) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Access denied: not a doctor']);
    exit;
}

// Verify that the appointment belongs to this doctor
$checkAppointmentQuery = "SELECT id FROM appointment WHERE id = ? AND doctor_id = ?";
$stmt = $conn->prepare($checkAppointmentQuery);
$stmt->bind_param("ii", $appointment_id, $doctor_id);
$stmt->execute();
$checkResult = $stmt->get_result();

if ($checkResult->num_rows == 0) {
    http_response_code(404);
    echo json_encode(['success' => false, 'message' => 'Appointment not found or access denied']);
    exit;
}

// Update the appointment status
$updateQuery = "UPDATE appointment SET a_status = ? WHERE id = ?";
$stmt = $conn->prepare($updateQuery);
$stmt->bind_param("ii", $status, $appointment_id);
$result = $stmt->execute();

if ($result) {
    error_log("Appointment status updated successfully: appointment_id=$appointment_id, new_status=$status");
    echo json_encode([
        'success' => true, 
        'message' => 'Appointment status updated successfully'
    ]);
} else {
    $error = $stmt->error;
    error_log("Failed to update appointment status: " . $error);
    http_response_code(500);
    echo json_encode([
        'success' => false, 
        'message' => 'Failed to update appointment status',
        'error_details' => $error
    ]);
}

$stmt->close();
$conn->close();
?>