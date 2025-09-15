<?php
// Disable any error output that might interfere with JSON
ini_set('display_errors', 0);
error_reporting(0);

// Set content type header
header('Content-Type: application/json');

// Include database connection
include(__DIR__ . "/config/db.php");

// Check if it's a POST request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

// Get POST data
$appointment_id = isset($_POST['appointment_id']) ? (int)$_POST['appointment_id'] : 0;
$user_id = isset($_POST['user_id']) ? (int)$_POST['user_id'] : 0;

// Validate required fields
if (empty($appointment_id) || empty($user_id)) {
    echo json_encode(['success' => false, 'message' => 'Appointment ID and User ID are required']);
    exit;
}

// Verify that the appointment belongs to the user
$checkQuery = "SELECT id FROM appointment 
               WHERE id = $appointment_id AND patient_id = $user_id";
$checkResult = mysqli_query($db, $checkQuery);

if (!$checkResult || mysqli_num_rows($checkResult) == 0) {
    echo json_encode(['success' => false, 'message' => 'Appointment not found or access denied']);
    exit;
}

// Update appointment status to cancelled
$query = "UPDATE appointment SET a_satus = 2 WHERE id = $appointment_id"; // Note: it's a_satus not a_status
$result = mysqli_query($db, $query);

if ($result) {
    echo json_encode([
        'success' => true, 
        'message' => 'Appointment cancelled successfully'
    ]);
} else {
    echo json_encode([
        'success' => false, 
        'message' => 'Failed to cancel appointment'
    ]);
}

// Close database connection
mysqli_close($db);
?>