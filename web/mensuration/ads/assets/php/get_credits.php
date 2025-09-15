<?php
session_start();
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

// Database configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'healthlink');

$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if ($conn->connect_error) {
    echo json_encode(["success" => false, "message" => "Database connection failed"]);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);

if (!$data || !isset($data['user_id'])) {
    echo json_encode(["success" => false, "message" => "Invalid request"]);
    exit;
}

$user_id = intval($data['user_id']);

// Get user credits
$getCredits = $conn->prepare("SELECT credits FROM credits WHERE user_id = ?");
$getCredits->bind_param("i", $user_id);
$getCredits->execute();
$creditsResult = $getCredits->get_result();

if ($creditsResult->num_rows > 0) {
    $creditsData = $creditsResult->fetch_assoc();
    echo json_encode(["success" => true, "credits" => $creditsData['credits']]);
} else {
    echo json_encode(["success" => true, "credits" => 0]);
}

$conn->close();
?>