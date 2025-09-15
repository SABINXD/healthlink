<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

// Get post ID from URL
$postId = isset($_GET['post_id']) ? (int)$_GET['post_id'] : 0;

if ($postId <= 0) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid post ID']);
    exit;
}

// Connect to database
require_once(__DIR__ . '/config/db.php');
$conn = getDbConnection();

// Get post details
$stmt = $conn->prepare("SELECT * FROM posts WHERE id = ?");
$stmt->bind_param("i", $postId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(['status' => 'error', 'message' => 'Post not found']);
    exit;
}

$post = $result->fetch_assoc();

// Return spoiler status
echo json_encode([
    'status' => 'success',
    'post_id' => $postId,
    'spoiler' => (bool)$post['spoiler'],
    'post_image' => $post['post_img']
]);

$stmt->close();
$conn->close();
?>