<?php
session_start();
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

require_once 'config.php';

// Get POST data
$data = json_decode(file_get_contents('php://input'), true);

if (!$data || !isset($data['user_id'])) {
    echo json_encode(["success" => false, "message" => "Invalid request"]);
    exit;
}

$user_id = intval($data['user_id']);
$points_to_add = 10; // Each ad gives 10 points

$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if ($conn->connect_error) {
    echo json_encode(["success" => false, "message" => "Database connection failed"]);
    exit;
}

try {
    // Check if user exists in credits table
    $check_user = $conn->prepare("SELECT * FROM credits WHERE user_id = ?");
    $check_user->bind_param("i", $user_id);
    $check_user->execute();
    $result = $check_user->get_result();
    
    if ($result->num_rows > 0) {
        // Update existing user credits
        $update_credits = $conn->prepare("UPDATE credits SET credits = credits + ? WHERE user_id = ?");
        $update_credits->bind_param("ii", $points_to_add, $user_id);
        $update_credits->execute();
    } else {
        // Create new credits record for user
        $insert_credits = $conn->prepare("INSERT INTO credits (user_id, credits) VALUES (?, ?)");
        $insert_credits->bind_param("ii", $user_id, $points_to_add);
        $insert_credits->execute();
    }
    
    // Record ad view in history
    $record_ad = $conn->prepare("INSERT INTO ad_history (user_id, points_earned, view_date) VALUES (?, ?, NOW())");
    $record_ad->bind_param("ii", $user_id, $points_to_add);
    $record_ad->execute();
    
    // Get updated credits
    $get_credits = $conn->prepare("SELECT credits FROM credits WHERE user_id = ?");
    $get_credits->bind_param("i", $user_id);
    $get_credits->execute();
    $credits_result = $get_credits->get_result();
    $credits_data = $credits_result->fetch_assoc();
    $updated_credits = $credits_data['credits'];
    
    echo json_encode(["success" => true, "credits" => $updated_credits]);
    
} catch (Exception $e) {
    echo json_encode(["success" => false, "message" => $e->getMessage()]);
}

$conn->close();
?>