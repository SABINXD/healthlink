<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

// Just return what we received
echo json_encode([
    'status' => 'connected',
    'method' => $_SERVER['REQUEST_METHOD'],
    'post_data' => $_POST,
    'has_image' => isset($_POST['image']),
    'image_length' => isset($_POST['image']) ? strlen($_POST['image']) : 0
]);
?>