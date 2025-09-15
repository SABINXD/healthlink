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

// Get all doctors
$query = "SELECT id, first_name, last_name, doctor_type, doctor_address, profile_pic 
          FROM users 
          WHERE is_doctor = 1 
          ORDER BY first_name, last_name";

$result = mysqli_query($conn, $query);

if (!$result) {
    // Log the actual MySQL error
    $error = mysqli_error($conn);
    error_log("MySQL Error: " . $error);
    echo json_encode([
        'status' => 'error', 
        'message' => 'Database query failed',
        'error_details' => $error
    ]);
    exit;
}

$doctors = [];
while ($row = mysqli_fetch_assoc($result)) {
    $doctors[] = $row;
}

// Return doctors
echo json_encode([
    'status' => 'success',
    'doctors' => $doctors,
    'count' => count($doctors)
]);

// Close database connection
mysqli_close($conn);
?>