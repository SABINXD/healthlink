<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
error_reporting(E_ALL);
ini_set('display_errors', 1);
// Create a log file
$logFile = 'debug_get_posts.txt';
$logMessage = "=== GET POSTS REQUEST ===\n";
$logMessage .= "Timestamp: " . date('Y-m-d H:i:s') . "\n";
$logMessage .= "GET Data: " . json_encode($_GET) . "\n";
$logMessage .= "POST Data: " . json_encode($_POST) . "\n\n";
require_once(__DIR__ . '/config/db.php');
try {
    $conn = getDbConnection();
    
    $current_user_id = 0;
    if (isset($_GET['user_id'])) {
        $current_user_id = (int)$_GET['user_id'];
    } elseif (isset($_POST['user_id'])) {
        $current_user_id = (int)$_POST['user_id'];
    }
    
    $logMessage .= "Current user ID: " . $current_user_id . "\n";
    
    // Updated SQL query with proper ID handling and spoiler field
    $sql = "SELECT 
                p.id, 
                p.user_id, 
                p.post_text, 
                p.post_img, 
                p.code_content, 
                p.code_language, 
                p.tags, 
                p.code_status,
                p.spoiler,  -- Added spoiler field
                p.created_at,
                u.username,
                COALESCE(u.first_name, '') as first_name,
                COALESCE(u.last_name, '') as last_name,
                COALESCE(u.profile_pic, 'default_profile.jpg') as profile_pic,
                COUNT(DISTINCT l.id) as like_count,
                COUNT(DISTINCT c.id) as comment_count,
                MAX(CASE WHEN l.user_id = ? THEN 1 ELSE 0 END) as is_liked
            FROM posts p 
            LEFT JOIN users u ON p.user_id = u.id 
            LEFT JOIN likes l ON p.id = l.post_id 
            LEFT JOIN comments c ON p.id = c.post_id 
            WHERE p.id > 0
            GROUP BY p.id, p.user_id, p.post_text, p.post_img, p.code_content, p.code_language, p.tags, p.code_status, p.spoiler, p.created_at, u.username, u.first_name, u.last_name, u.profile_pic
            ORDER BY p.created_at DESC 
            LIMIT 50";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $current_user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $posts = array();
    
    while ($row = $result->fetch_assoc()) {
        $post_id = (int)$row['id'];
        if ($post_id <= 0) {
            $logMessage .= "⚠️ Skipping post with invalid ID: " . $post_id . "\n";
            continue;
        }
        
        $display_name = trim($row['first_name'] . ' ' . $row['last_name']);
        if (empty($display_name)) {
            $display_name = $row['username'] ?: 'Unknown User';
        }
        
        $profile_pic_url = null;
        if (!empty($row['profile_pic']) && $row['profile_pic'] !== 'null' && $row['profile_pic'] !== 'default_profile.jpg') {
            $profile_pic_url = "http://" . IP_ADDRESS . "/healthlink/web/assets/img/profile/" . $row['profile_pic'];
        }
        
        $post_image_url = "";
        if (!empty($row['post_img']) && $row['post_img'] !== 'null') {
            $image_path = __DIR__ . "/../web/assets/img/posts/" . $row['post_img'];
            if (file_exists($image_path)) {
                $post_image_url = "http://" . IP_ADDRESS . "/healthlink/web/assets/img/posts/" . $row['post_img'];
            }
        }
        
        $tags_array = array();
        if (!empty($row['tags'])) {
            $tags_array = explode(',', $row['tags']);
            $tags_array = array_map('trim', $tags_array);
            $tags_array = array_filter($tags_array); // Remove empty tags
        }
        
        $code_content = $row['code_content'];
        if (!empty($code_content) && strpos($code_content, 'Check out this') === 0) {
            $code_content = preg_replace('/^Check out this [^:]+:\s*\n\n/', '', $code_content);
        }
        
        $post_data = array(
            'id' => $post_id,
            'user_id' => (int)$row['user_id'],
            'user_name' => $display_name,
            'username' => $row['username'],
            'post_description' => $row['post_text'],
            'post_image' => $post_image_url, // Full URL
            'profile_pic' => $profile_pic_url,
            'like_count' => (int)$row['like_count'],
            'comment_count' => (int)$row['comment_count'],
            'is_liked' => (bool)$row['is_liked'],
            'created_at' => $row['created_at'],
            'code_content' => $code_content,
            'code_language' => $row['code_language'],
            'code_status' => (int)$row['code_status'],
            'tags' => $tags_array,
            'spoiler' => (bool)$row['spoiler']  // Added spoiler field
        );
        
        $posts[] = $post_data;
        
        $logMessage .= "✅ Post added: ID=" . $post_id . ", User=" . $display_name . ", HasCode=" . (!empty($code_content) ? 'Yes' : 'No') . ", CodeStatus=" . $row['code_status'] . ", Spoiler=" . $row['spoiler'] . "\n";
    }
    
    $logMessage .= "📊 Total posts returned: " . count($posts) . "\n";
    file_put_contents($logFile, $logMessage, FILE_APPEND);
    
    echo json_encode([
        'status' => 'success',
        'posts' => $posts,
        'total_posts' => count($posts)
    ], JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
    
    $stmt->close();
    $conn->close();
    
} catch (Exception $e) {
    $logMessage .= "❌ Error in get_posts.php: " . $e->getMessage() . "\n";
    file_put_contents($logFile, $logMessage, FILE_APPEND);
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ], JSON_PRETTY_PRINT);
}
?>