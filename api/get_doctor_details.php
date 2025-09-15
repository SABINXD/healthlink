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
error_log("Fetching doctor details for ID: $doctor_id");

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

// Get doctor details
$query = "SELECT id, first_name, last_name, email, phone, doctor_type, doctor_address, profile_pic, is_doctor 
          FROM users 
          WHERE id = ? AND is_doctor = 1";

$stmt = $conn->prepare($query);
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

// Prepare response
$doctorDetails = [
    'id' => $doctor['id'],
    'first_name' => $doctor['first_name'],
    'last_name' => $doctor['last_name'],
    'email' => $doctor['email'],
    'phone' => $doctor['phone'],
    'doctor_type' => $doctor['doctor_type'],
    'doctor_address' => $doctor['doctor_address'],
    'profile_pic' => $doctor['profile_pic'],
    'is_doctor' => (bool)$doctor['is_doctor']
];

error_log("Doctor details found: " . print_r($doctorDetails, true));

echo json_encode([
    'status' => 'success',
    'doctor' => $doctorDetails
]);

$stmt->close();
$conn->close();
?>