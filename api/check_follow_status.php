<?php
header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/php_errors.log');

// Include database configuration
$db_config_path = __DIR__ . "/config/db.php";
if (!file_exists($db_config_path)) {
    echo json_encode(['status' => 'error', 'message' => 'Database configuration file not found']);
    exit;
}

include($db_config_path);

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
    exit;
}

$current_user_id = isset($_GET['current_user_id']) ? (int)$_GET['current_user_id'] : 0;
$target_user_id = isset($_GET['target_user_id']) ? (int)$_GET['target_user_id'] : 0;

if ($current_user_id <= 0 || $target_user_id <= 0) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid user IDs.']);
    exit;
}

try {
    // Get database connection using the function from db.php
    $conn = getDbConnection();
    
    // Check if current user is following target user
    $query = "SELECT COUNT(*) as is_following FROM follow_list 
              WHERE follower_id = ? AND user_id = ?";
              
    $stmt = $conn->prepare($query);
    if (!$stmt) {
        throw new Exception("Database query preparation failed: " . $conn->error);
    }
    $stmt->bind_param("ii", $current_user_id, $target_user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    
    $isFollowing = $row['is_following'] > 0;

    echo json_encode([
        'status' => 'success',
        'is_following' => $isFollowing,
        'is_pending' => false // Add logic for pending requests if needed
    ]);
    
    $stmt->close();
    $conn->close();
} catch (Exception $e) {
    error_log("Follow status error: " . $e->getMessage());
    echo json_encode(['status' => 'error', 'message' => 'Database error occurred: ' . $e->getMessage()]);
}
?>