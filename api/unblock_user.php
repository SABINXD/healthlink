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
    echo json_encode(['status' => 'error', 'message' => 'Cannot unblock yourself']);
    exit;
}

try {
    $conn = getDbConnection();
    
    // Remove block relationship
    $delete_sql = "DELETE FROM block_list WHERE user_id = ? AND blocked_user_id = ?";
    $delete_stmt = $conn->prepare($delete_sql);
    $delete_stmt->bind_param("ii", $user_id, $blocked_user_id);
    
    if ($delete_stmt->execute() && $delete_stmt->affected_rows > 0) {
        echo json_encode([
            'status' => 'success',
            'message' => 'User unblocked successfully'
        ]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'User was not blocked or unblock failed']);
    }
    
    $conn->close();
    
} catch (Exception $e) {
    error_log("Unblock user error: " . $e->getMessage());
    echo json_encode(['status' => 'error', 'message' => 'Database error occurred: ' . $e->getMessage()]);
}
?>