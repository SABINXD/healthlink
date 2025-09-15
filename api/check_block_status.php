<?php
header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/php_errors.log');

include __DIR__ . '/config/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
    exit;
}

$blocker_id = isset($_GET['blocker_id']) ? (int)$_GET['blocker_id'] : 0;
$blocked_id = isset($_GET['blocked_id']) ? (int)$_GET['blocked_id'] : 0;

if ($blocker_id <= 0 || $blocked_id <= 0) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid user IDs.']);
    exit;
}

try {
    $conn = getDbConnection();
    
    // Check if blocker has blocked the target user
    $query = "SELECT COUNT(*) as is_blocked FROM block_list 
              WHERE user_id = ? AND blocked_user_id = ?";
              
    $stmt = $conn->prepare($query);
    if (!$stmt) {
        throw new Exception("Database query preparation failed: " . $conn->error);
    }
    $stmt->bind_param("ii", $blocker_id, $blocked_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    
    $isBlocked = $row['is_blocked'] > 0;

    echo json_encode([
        'status' => 'success',
        'is_blocked' => $isBlocked
    ]);
    
    $stmt->close();
    $conn->close();
} catch (Exception $e) {
    error_log("Block status error: " . $e->getMessage());
    echo json_encode(['status' => 'error', 'message' => 'Database error occurred: ' . $e->getMessage()]);
}
?>