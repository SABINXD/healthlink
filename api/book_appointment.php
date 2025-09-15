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

// Log the raw request for debugging
error_log("Raw POST data: " . file_get_contents('php://input'));
error_log("POST array: " . print_r($_POST, true));

// Get POST data
$doctor_id = isset($_POST['doctor_id']) ? (int)$_POST['doctor_id'] : 0;
$patient_id = isset($_POST['patient_id']) ? (int)$_POST['patient_id'] : 0;
$date = isset($_POST['date']) ? trim($_POST['date']) : '';
$time = isset($_POST['time']) ? trim($_POST['time']) : '';
$reason = isset($_POST['reason']) ? trim($_POST['reason']) : '';
$desc = isset($_POST['desc']) ? trim($_POST['desc']) : '';

// Log the extracted values
error_log("Extracted values: doctor_id=$doctor_id, patient_id=$patient_id, date='$date', time='$time', reason='$reason'");

// Validate required fields
if (empty($doctor_id) || empty($patient_id) || empty($date) || empty($time) || empty($reason)) {
    $missing_fields = [];
    if (empty($doctor_id)) $missing_fields[] = 'doctor_id';
    if (empty($patient_id)) $missing_fields[] = 'patient_id';
    if (empty($date)) $missing_fields[] = 'date';
    if (empty($time)) $missing_fields[] = 'time';
    if (empty($reason)) $missing_fields[] = 'reason';
    
    error_log("Missing fields: " . implode(', ', $missing_fields));
    
    echo json_encode([
        'success' => false, 
        'message' => 'All required fields must be filled',
        'missing_fields' => $missing_fields
    ]);
    exit;
}

// Validate patient_id is greater than 0
if ($patient_id <= 0) {
    echo json_encode([
        'success' => false, 
        'message' => 'Invalid patient ID'
    ]);
    exit;
}

// Check if the patient is a doctor (doctors cannot book appointments)
$userCheckQuery = "SELECT is_doctor FROM users WHERE id = ?";
$stmt = $conn->prepare($userCheckQuery);
$stmt->bind_param("i", $patient_id);
$stmt->execute();
$userCheckResult = $stmt->get_result();

if ($userCheckResult->num_rows == 0) {
    echo json_encode([
        'success' => false, 
        'message' => 'User not found'
    ]);
    exit;
}

$userRow = $userCheckResult->fetch_assoc();
if ($userRow['is_doctor'] == 1) {
    echo json_encode([
        'success' => false, 
        'message' => 'Doctors cannot book appointments'
    ]);
    exit;
}

// Check if the time slot is already booked
$checkSlotQuery = "SELECT id FROM appointment 
                   WHERE doctor_id = ? 
                   AND appointment_date = ?
                   AND appointment_time = ?";
$stmt = $conn->prepare($checkSlotQuery);
$stmt->bind_param("iss", $doctor_id, $date, $time);
$stmt->execute();
$checkSlotResult = $stmt->get_result();

if ($checkSlotResult->num_rows > 0) {
    echo json_encode([
        'success' => false, 
        'message' => 'This time slot is already booked'
    ]);
    exit;
}

// Get patient name
$patientQuery = "SELECT first_name, last_name FROM users WHERE id = ?";
$stmt = $conn->prepare($patientQuery);
$stmt->bind_param("i", $patient_id);
$stmt->execute();
$patientResult = $stmt->get_result();
$patientName = "Unknown";
if ($patientResult->num_rows > 0) {
    $patientRow = $patientResult->fetch_assoc();
    $patientName = $patientRow['first_name'] . ' ' . $patientRow['last_name'];
}

// Insert the new appointment
$insertQuery = "INSERT INTO appointment 
                (doctor_id, patient_id, appointment_date, appointment_time, patient_name, patient_desc, reason, a_status) 
                VALUES (?, ?, ?, ?, ?, ?, ?, 0)"; // 0 = Pending
$stmt = $conn->prepare($insertQuery);
$stmt->bind_param("iissssi", $doctor_id, $patient_id, $date, $time, $patientName, $desc, $reason);
$result = $stmt->execute();

if ($result) {
    $appointment_id = $conn->insert_id;
    error_log("Appointment booked successfully with ID: " . $appointment_id);
    
    echo json_encode([
        'success' => true, 
        'message' => 'Appointment booked successfully!',
        'appointment_id' => $appointment_id
    ]);
} else {
    $error = $stmt->error;
    error_log("MySQL Error in insert: " . $error);
    echo json_encode([
        'success' => false, 
        'message' => 'Failed to book appointment',
        'error_details' => $error
    ]);
}

$stmt->close();
$conn->close();
?>