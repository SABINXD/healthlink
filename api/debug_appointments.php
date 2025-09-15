<?php
// Disable any error output that might interfere with JSON
ini_set('display_errors', 0);
error_reporting(0);
// Set content type header
header('Content-Type: application/json');
// Include database connection
require_once(__DIR__ . "/config/db.php");

// Get database connection
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

// Get all appointments with detailed information
$query = "SELECT a.*, 
                u1.first_name as patient_first_name, u1.last_name as patient_last_name, u1.is_doctor as patient_is_doctor,
                u2.first_name as doctor_first_name, u2.last_name as doctor_last_name, u2.is_doctor as doctor_is_doctor
         FROM appointment a
         LEFT JOIN users u1 ON a.patient_id = u1.id
         LEFT JOIN users u2 ON a.doctor_id = u2.id
         ORDER BY a.created_at DESC";

$result = $conn->query($query);

if (!$result) {
    echo json_encode([
        'status' => 'error', 
        'message' => 'Database query failed',
        'error_details' => $conn->error
    ]);
    exit;
}

$appointments = [];
while ($row = $result->fetch_assoc()) {
    $appointments[] = [
        'id' => $row['id'],
        'doctor_id' => $row['doctor_id'],
        'doctor_name' => $row['doctor_first_name'] . ' ' . $row['doctor_last_name'],
        'doctor_is_doctor' => (bool)$row['doctor_is_doctor'],
        'patient_id' => $row['patient_id'],
        'patient_name' => $row['patient_first_name'] . ' ' . $row['patient_last_name'],
        'patient_is_doctor' => (bool)$row['patient_is_doctor'],
        'appointment_date' => $row['appointment_date'],
        'appointment_time' => $row['appointment_time'],
        'reason' => $row['reason'],
        'patient_desc' => $row['patient_desc'],
        'status_code' => $row['a_status'],
        'status' => getStatusString($row['a_status']),
        'created_at' => $row['created_at'],
        'is_future' => isFutureAppointment($row['appointment_date'], $row['appointment_time'])
    ];
}

echo json_encode([
    'status' => 'success',
    'appointments' => $appointments,
    'count' => count($appointments),
    'debug_info' => [
        'current_datetime' => date('Y-m-d H:i:s'),
        'current_date' => date('Y-m-d'),
        'current_time' => date('H:i:s')
    ]
]);

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
        return false;
    }
}
?>