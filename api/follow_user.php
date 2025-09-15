<?php
header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/php_errors.log');

include __DIR__ . '/config/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
    exit;
}

$follower_id = isset($_POST['follower_id']) ? (int)$_POST['follower_id'] : 0;
$user_id = isset($_POST['user_id']) ? (int)$_POST['user_id'] : 0;

if ($follower_id <= 0 || $user_id <= 0) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid user IDs.']);
    exit;
}

if ($follower_id == $user_id) {
    echo json_encode(['status' => 'error', 'message' => 'Cannot follow yourself']);
    exit;
}

try {
    $conn = getDbConnection();
    
    // Check if already following
    $check_sql = "SELECT * FROM follow_list WHERE follower_id = ? AND user_id = ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("ii", $follower_id, $user_id);
    $check_stmt->execute();
    $result = $check_stmt->get_result();
    
    if ($result->num_rows > 0) {
        echo json_encode(['status' => 'error', 'message' => 'Already following this user']);
        exit;
    }
    
    // Check if blocked
    $block_check_sql = "SELECT * FROM block_list WHERE user_id = ? AND blocked_user_id = ?";
    $block_stmt = $conn->prepare($block_check_sql);
    $block_stmt->bind_param("ii", $user_id, $follower_id);
    $block_stmt->execute();
    $block_result = $block_stmt->get_result();
    
    if ($block_result->num_rows > 0) {
        echo json_encode(['status' => 'error', 'message' => 'Cannot follow this user']);
        exit;
    }
    
    // Add follow relationship
    $insert_sql = "INSERT INTO follow_list (follower_id, user_id) VALUES (?, ?)";
    $insert_stmt = $conn->prepare($insert_sql);
    $insert_stmt->bind_param("ii", $follower_id, $user_id);
    
    if ($insert_stmt->execute()) {
        echo json_encode([
            'status' => 'success',
            'message' => 'Successfully followed user'
        ]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to follow user']);
    }
    
    $check_stmt->close();
    $block_stmt->close();
    $insert_stmt->close();
    $conn->close();
    
} catch (Exception $e) {
    error_log("Follow user error: " . $e->getMessage());
    echo json_encode(['status' => 'error', 'message' => 'Database error occurred: ' . $e->getMessage()]);
}
?>