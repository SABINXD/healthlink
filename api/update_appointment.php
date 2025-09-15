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
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

// Get database connection using the same method as other pages
try {
    $conn = getDbConnection();
} catch (Exception $e) {
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
$patient_id = isset($_POST['patient_id']) ? (int)$_POST['patient_id'] : 0;
$date = isset($_POST['date']) ? trim($_POST['date']) : '';
$time = isset($_POST['time']) ? trim($_POST['time']) : '';
$reason = isset($_POST['reason']) ? trim($_POST['reason']) : '';
$desc = isset($_POST['desc']) ? trim($_POST['desc']) : '';

// Validate required fields
if (empty($appointment_id) || empty($doctor_id) || empty($patient_id) || empty($date) || empty($time) || empty($reason)) {
    echo json_encode([
        'success' => false, 
        'message' => 'All required fields must be filled'
    ]);
    exit;
}

// Check if the appointment exists and belongs to the patient
$checkQuery = "SELECT * FROM appointment WHERE id = $appointment_id AND patient_id = $patient_id";
$checkResult = mysqli_query($conn, $checkQuery);

if (!$checkResult || mysqli_num_rows($checkResult) == 0) {
    echo json_encode([
        'success' => false, 
        'message' => 'Appointment not found or access denied'
    ]);
    exit;
}

// FIXED: Use separate date and time columns instead of datetime
// Check if the time slot is already booked (excluding the current appointment)
$checkSlotQuery = "SELECT id FROM appointment 
                   WHERE doctor_id = $doctor_id 
                   AND appointment_date = '$date'
                   AND appointment_time = '$time'
                   AND id != $appointment_id";
$checkSlotResult = mysqli_query($conn, $checkSlotQuery);

if (!$checkSlotResult) {
    $error = mysqli_error($conn);
    echo json_encode([
        'success' => false, 
        'message' => 'Database error during slot check'
    ]);
    exit;
}

if (mysqli_num_rows($checkSlotResult) > 0) {
    echo json_encode([
        'success' => false, 
        'message' => 'This time slot is already booked'
    ]);
    exit;
}

// FIXED: Use separate date and time columns instead of datetime
// Update the appointment
$updateQuery = "UPDATE appointment 
               SET appointment_date = '$date', 
                   appointment_time = '$time', 
                   patient_desc = '$desc', 
                   reason = '$reason' 
               WHERE id = $appointment_id AND patient_id = $patient_id";

$result = mysqli_query($conn, $updateQuery);

if ($result) {
    echo json_encode([
        'success' => true, 
        'message' => 'Appointment updated successfully!'
    ]);
} else {
    $error = mysqli_error($conn);
    echo json_encode([
        'success' => false, 
        'message' => 'Failed to update appointment',
        'error_details' => $error
    ]);
}

// Close database connection
mysqli_close($conn);
?>