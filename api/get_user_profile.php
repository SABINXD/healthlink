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

$user_id = isset($_GET['user_id']) ? (int)$_GET['user_id'] : 0;
if ($user_id <= 0) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid user ID.']);
    exit;
}

try {
    // Get database connection using the function from db.php
    $conn = getDbConnection();
    
    // Query to get user profile
    $query = "SELECT id, first_name, last_name, username, bio, profile_pic, email 
              FROM users 
              WHERE id = ?";
              
    $stmt = $conn->prepare($query);
    if (!$stmt) {
        throw new Exception("Database query preparation failed: " . $conn->error);
    }
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($row = $result->fetch_assoc()) {
        $stmt->close();
        
        // Get post count
        $postCountQuery = "SELECT COUNT(*) as post_count FROM posts WHERE user_id = ?";
        $postCountStmt = $conn->prepare($postCountQuery);
        $postCountStmt->bind_param("i", $user_id);
        $postCountStmt->execute();
        $postCountResult = $postCountStmt->get_result();
        $postCountRow = $postCountResult->fetch_assoc();
        $postCount = $postCountRow['post_count'];
        $postCountStmt->close();
        
        // Get followers count
        $followersQuery = "SELECT COUNT(*) as followers FROM follow_list WHERE user_id = ?";
        $followersStmt = $conn->prepare($followersQuery);
        $followersStmt->bind_param("i", $user_id);
        $followersStmt->execute();
        $followersResult = $followersStmt->get_result();
        $followersRow = $followersResult->fetch_assoc();
        $followers = $followersRow['followers'];
        $followersStmt->close();
        
        // Get following count
        $followingQuery = "SELECT COUNT(*) as following FROM follow_list WHERE follower_id = ?";
        $followingStmt = $conn->prepare($followingQuery);
        $followingStmt->bind_param("i", $user_id);
        $followingStmt->execute();
        $followingResult = $followingStmt->get_result();
        $followingRow = $followingResult->fetch_assoc();
        $following = $followingRow['following'];
        $followingStmt->close();
        
        $user = [
            'id' => (int)$row['id'],
            'first_name' => $row['first_name'] ?? '',
            'last_name' => $row['last_name'] ?? '',
            'username' => $row['username'] ?? '',
            'bio' => $row['bio'] ?? 'No bio available',
            'profile_pic' => $row['profile_pic'] ?? '',
            'email' => $row['email'] ?? '',
            'post_count' => (int)$postCount,
            'followers' => (int)$followers,
            'following' => (int)$following
        ];

        echo json_encode(['status' => 'success', 'user' => $user]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'User not found']);
    }
    
    $conn->close();
} catch (Exception $e) {
    error_log("User profile error: " . $e->getMessage());
    echo json_encode(['status' => 'error', 'message' => 'Database error occurred: ' . $e->getMessage()]);
}
?>