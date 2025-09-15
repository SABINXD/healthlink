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

$user_id = isset($_POST['user_id']) ? (int)$_POST['user_id'] : 0;
$blocked_user_id = isset($_POST['blocked_user_id']) ? (int)$_POST['blocked_user_id'] : 0;

if ($user_id <= 0 || $blocked_user_id <= 0) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid user IDs']);
    exit;
}

if ($user_id == $blocked_user_id) {
    echo json_encode(['status' => 'error', 'message' => 'Cannot block yourself']);
    exit;
}

try {
    $conn = getDbConnection();
    
    // Check if already blocked
    $check_sql = "SELECT * FROM block_list WHERE user_id = ? AND blocked_user_id = ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("ii", $user_id, $blocked_user_id);
    $check_stmt->execute();
    $result = $check_stmt->get_result();
    
    if ($result->num_rows > 0) {
        echo json_encode(['status' => 'error', 'message' => 'User already blocked']);
        exit;
    }
    
    // Remove any existing follow relationships
    $unfollow_sql1 = "DELETE FROM follow_list WHERE follower_id = ? AND user_id = ?";
    $unfollow_stmt1 = $conn->prepare($unfollow_sql1);
    $unfollow_stmt1->bind_param("ii", $user_id, $blocked_user_id);
    $unfollow_stmt1->execute();
    
    $unfollow_sql2 = "DELETE FROM follow_list WHERE follower_id = ? AND user_id = ?";
    $unfollow_stmt2 = $conn->prepare($unfollow_sql2);
    $unfollow_stmt2->bind_param("ii", $blocked_user_id, $user_id);
    $unfollow_stmt2->execute();
    
    // Add block relationship
    $insert_sql = "INSERT INTO block_list (user_id, blocked_user_id) VALUES (?, ?)";
    $insert_stmt = $conn->prepare($insert_sql);
    $insert_stmt->bind_param("ii", $user_id, $blocked_user_id);
    
    if ($insert_stmt->execute()) {
        echo json_encode([
            'status' => 'success',
            'message' => 'User blocked successfully'
        ]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to block user']);
    }
    
    $conn->close();
    
} catch (Exception $e) {
    error_log("Block user error: " . $e->getMessage());
    echo json_encode(['status' => 'error', 'message' => 'Database error occurred: ' . $e->getMessage()]);
}
?>