<?php
// Disable any error output that might interfere with JSON
ini_set('display_errors', 0);
error_reporting(0);

// Set content type header
header('Content-Type: application/json');

// Include database connection
require_once(__DIR__ . "/config/db.php");

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

// Get patient ID
$patient_id = isset($_GET['patient_id']) ? (int)$_GET['patient_id'] : 0;
if ($patient_id <= 0) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Invalid patient ID'
    ]);
    exit;
}

// FIXED: Use correct column names (appointment_date and appointment_time instead of datetime)
// Get appointments for this patient
$query = "SELECT a.*, 
                 CONCAT(d.first_name, ' ', d.last_name) AS doctor_name,
                 d.doctor_address AS hospital_name
          FROM appointment a
          JOIN users d ON a.doctor_id = d.id
          WHERE a.patient_id = $patient_id
          ORDER BY a.appointment_date DESC, a.appointment_time DESC";

$result = mysqli_query($conn, $query);

if (!$result) {
    $error = mysqli_error($conn);
    error_log("MySQL Error: " . $error);
    echo json_encode([
        'status' => 'error', 
        'message' => 'Database query failed',
        'error_details' => $error
    ]);
    exit;
}

$appointments = [];
while ($row = mysqli_fetch_assoc($result)) {
    // FIXED: No need to format datetime since we already have separate date and time columns
    $appointments[] = $row;
}

echo json_encode([
    'status' => 'success',
    'appointments' => $appointments
]);

// Close database connection
mysqli_close($conn);
?>