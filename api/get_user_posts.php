<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Include database configuration
$db_config_path = __DIR__ . "/config/db.php";
if (!file_exists($db_config_path)) {
    echo json_encode(['status' => 'error', 'message' => 'Database configuration file not found']);
    exit;
}
include($db_config_path);

try {
    // Get parameters
    $user_id = isset($_POST['user_id']) ? (int)$_POST['user_id'] : 0;
    $current_user_id = isset($_POST['current_user_id']) ? (int)$_POST['current_user_id'] : 0;
    
    if ($user_id <= 0) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Invalid user ID'
        ]);
        exit;
    }
    
    // Get database connection
    $conn = getDbConnection();
    
    // Check if ai_summary column exists in posts table
    $checkColumnQuery = "SHOW COLUMNS FROM posts LIKE 'ai_summary'";
    $result = $conn->query($checkColumnQuery);
    $hasAiSummaryColumn = $result->num_rows > 0;
    
    // Build query based on available columns
    if ($hasAiSummaryColumn) {
        // Use ai_summary if available
        $query = "SELECT 
                    p.id,
                    p.user_id,
                    p.post_text as post_description,
                    p.post_img as post_image,
                    p.ai_summary,
                    p.created_at,
                    u.username as user_name,
                    u.profile_pic,
                    COALESCE(like_counts.like_count, 0) as like_count,
                    COALESCE(comment_counts.comment_count, 0) as comment_count,
                    CASE WHEN user_likes.post_id IS NOT NULL THEN 1 ELSE 0 END as is_liked
                  FROM posts p
                  LEFT JOIN users u ON p.user_id = u.id 
                  LEFT JOIN (
                      SELECT post_id, COUNT(*) as like_count 
                      FROM likes 
                      GROUP BY post_id
                  ) like_counts ON p.id = like_counts.post_id
                  LEFT JOIN (
                      SELECT post_id, COUNT(*) as comment_count 
                      FROM comments 
                      GROUP BY post_id
                  ) comment_counts ON p.id = comment_counts.post_id
                  LEFT JOIN (
                      SELECT post_id 
                      FROM likes 
                      WHERE user_id = ?
                  ) user_likes ON p.id = user_likes.post_id
                  WHERE p.user_id = ? 
                  ORDER BY p.created_at DESC";
    } else {
        // Fallback to code_content and code_language
        $query = "SELECT 
                    p.id,
                    p.user_id,
                    p.post_text as post_description,
                    p.post_img as post_image,
                    p.code_content,
                    p.code_language,
                    p.created_at,
                    u.username as user_name,
                    u.profile_pic,
                    COALESCE(like_counts.like_count, 0) as like_count,
                    COALESCE(comment_counts.comment_count, 0) as comment_count,
                    CASE WHEN user_likes.post_id IS NOT NULL THEN 1 ELSE 0 END as is_liked
                  FROM posts p
                  LEFT JOIN users u ON p.user_id = u.id 
                  LEFT JOIN (
                      SELECT post_id, COUNT(*) as like_count 
                      FROM likes 
                      GROUP BY post_id
                  ) like_counts ON p.id = like_counts.post_id
                  LEFT JOIN (
                      SELECT post_id, COUNT(*) as comment_count 
                      FROM comments 
                      GROUP BY post_id
                  ) comment_counts ON p.id = comment_counts.post_id
                  LEFT JOIN (
                      SELECT post_id 
                      FROM likes 
                      WHERE user_id = ?
                  ) user_likes ON p.id = user_likes.post_id
                  WHERE p.user_id = ? 
                  ORDER BY p.created_at DESC";
    }
    
    $stmt = $conn->prepare($query);
    if (!$stmt) {
        throw new Exception("Database query preparation failed: " . $conn->error);
    }
    
    $stmt->bind_param("ii", $current_user_id, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $posts = [];
    
    while ($row = $result->fetch_assoc()) {
        $post = [
            'id' => (int)$row['id'],
            'user_id' => (int)$row['user_id'],
            'user_name' => $row['user_name'] ?: 'Unknown User',
            'profile_pic' => $row['profile_pic'] ?: '',
            'post_description' => $row['post_description'] ?: '',
            'post_image' => $row['post_image'] ?: '',
            'created_at' => $row['created_at'],
            'like_count' => (int)$row['like_count'],
            'comment_count' => (int)$row['comment_count'],
            'is_liked' => (bool)$row['is_liked']
        ];
        
        // Add AI summary based on available columns
        if ($hasAiSummaryColumn) {
            $post['ai_summary'] = $row['ai_summary'] ?: '';
        } else {
            // Use code_content as AI summary if code_language is "AI Analysis"
            if ($row['code_language'] === 'AI Analysis') {
                $post['ai_summary'] = $row['code_content'] ?: '';
            } else {
                $post['ai_summary'] = '';
            }
            $post['code_content'] = $row['code_content'] ?: '';
            $post['code_language'] = $row['code_language'] ?: '';
        }
        
        $posts[] = $post;
    }
    
    echo json_encode([
        'status' => 'success',
        'posts' => $posts,
        'total_posts' => count($posts)
    ]);
    
    $stmt->close();
    $conn->close();
    
} catch (Exception $e) {
    error_log("User posts error: " . $e->getMessage());
    echo json_encode([
        'status' => 'error',
        'message' => 'Database error: ' . $e->getMessage()
    ]);
}
?>