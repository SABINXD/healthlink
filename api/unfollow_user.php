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

try {
    $conn = getDbConnection();
    
    // Remove follow relationship
    $delete_sql = "DELETE FROM follow_list WHERE follower_id = ? AND user_id = ?";
    $delete_stmt = $conn->prepare($delete_sql);
    $delete_stmt->bind_param("ii", $follower_id, $user_id);
    
    if ($delete_stmt->execute()) {
        if ($delete_stmt->affected_rows > 0) {
            echo json_encode([
                'status' => 'success',
                'message' => 'Successfully unfollowed user'
            ]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'You are not following this user']);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to unfollow user']);
    }
    
    $delete_stmt->close();
    $conn->close();
    
} catch (Exception $e) {
    error_log("Unfollow user error: " . $e->getMessage());
    echo json_encode(['status' => 'error', 'message' => 'Database error occurred: ' . $e->getMessage()]);
}
?>