<?php
// Turn off all error reporting to prevent any output
error_reporting(0);
ini_set('display_errors', 0);

// Set headers first to avoid any output issues
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Include database connection
include __DIR__ . '/config/db.php';

try {
    // Get database connection
    $conn = getDbConnection();
    
    // Handle both POST data and JSON input
    $input = json_decode(file_get_contents('php://input'), true);
    
    // Get and validate parameters from POST or JSON
    $post_id = 0;
    $user_id = 0;
    
    if (isset($_POST['post_id'])) {
        $post_id = (int)$_POST['post_id'];
        $user_id = (int)$_POST['user_id'];
    } elseif (isset($input['post_id'])) {
        $post_id = (int)$input['post_id'];
        $user_id = (int)$input['user_id'];
    }
    
    // Validate parameters
    if ($post_id <= 0) {
        echo json_encode(['status' => false, 'error' => 'Invalid post ID']);
        exit;
    }
    
    if ($user_id <= 0) {
        echo json_encode(['status' => false, 'error' => 'Invalid user ID']);
        exit;
    }
    
    // First, check if the post exists and belongs to the user
    $checkQuery = "SELECT post_img FROM posts WHERE id = ? AND user_id = ?";
    $checkStmt = mysqli_prepare($conn, $checkQuery);
    
    if (!$checkStmt) {
        echo json_encode(['status' => false, 'error' => 'Database error']);
        exit;
    }
    
    mysqli_stmt_bind_param($checkStmt, "ii", $post_id, $user_id);
    
    if (!mysqli_stmt_execute($checkStmt)) {
        echo json_encode(['status' => false, 'error' => 'Database error']);
        mysqli_stmt_close($checkStmt);
        exit;
    }
    
    mysqli_stmt_store_result($checkStmt);
    
    if (mysqli_stmt_num_rows($checkStmt) === 0) {
        echo json_encode(['status' => false, 'error' => 'Post not found or you do not have permission to delete it']);
        mysqli_stmt_close($checkStmt);
        exit;
    }
    
    // Get the post image path
    $image_path = '';
    mysqli_stmt_bind_result($checkStmt, $image_path);
    mysqli_stmt_fetch($checkStmt);
    mysqli_stmt_close($checkStmt);
    
    // Start transaction for data consistency
    mysqli_begin_transaction($conn);
    
    try {
        // Delete related data first (foreign key constraints)
        
        // Delete likes
        $deleteLikesQuery = "DELETE FROM likes WHERE post_id = ?";
        $deleteLikesStmt = mysqli_prepare($conn, $deleteLikesQuery);
        mysqli_stmt_bind_param($deleteLikesStmt, "i", $post_id);
        mysqli_stmt_execute($deleteLikesStmt);
        mysqli_stmt_close($deleteLikesStmt);
        
        // Delete comments
        $deleteCommentsQuery = "DELETE FROM comments WHERE post_id = ?";
        $deleteCommentsStmt = mysqli_prepare($conn, $deleteCommentsQuery);
        mysqli_stmt_bind_param($deleteCommentsStmt, "i", $post_id);
        mysqli_stmt_execute($deleteCommentsStmt);
        mysqli_stmt_close($deleteCommentsStmt);
        
        // Delete notifications related to this post
        $deleteNotificationsQuery = "DELETE FROM notifications WHERE post_id = ?";
        $deleteNotificationsStmt = mysqli_prepare($conn, $deleteNotificationsQuery);
        mysqli_stmt_bind_param($deleteNotificationsStmt, "s", (string)$post_id);
        mysqli_stmt_execute($deleteNotificationsStmt);
        mysqli_stmt_close($deleteNotificationsStmt);
        
        // Now delete the post
        $deleteQuery = "DELETE FROM posts WHERE id = ? AND user_id = ?";
        $deleteStmt = mysqli_prepare($conn, $deleteQuery);
        
        if (!$deleteStmt) {
            throw new Exception("Database error");
        }
        
        mysqli_stmt_bind_param($deleteStmt, "ii", $post_id, $user_id);
        
        if (!mysqli_stmt_execute($deleteStmt)) {
            throw new Exception("Database error");
        }
        
        $affected_rows = mysqli_stmt_affected_rows($deleteStmt);
        mysqli_stmt_close($deleteStmt);
        
        if ($affected_rows > 0) {
            // Commit transaction
            mysqli_commit($conn);
            
            // Delete the post image if it exists
            if (!empty($image_path)) {
                $fullImagePath = __DIR__ . "/../web/assets/img/posts/$image_path";
                if (file_exists($fullImagePath)) {
                    unlink($fullImagePath);
                }
            }
            
            echo json_encode(['status' => true, 'message' => 'Post deleted successfully']);
        } else {
            throw new Exception("No rows affected");
        }
        
    } catch (Exception $e) {
        // Rollback transaction on error
        mysqli_rollback($conn);
        throw $e;
    }
    
    mysqli_close($conn);
    
} catch (Exception $e) {
    echo json_encode(['status' => false, 'error' => 'Server error']);
}
?>