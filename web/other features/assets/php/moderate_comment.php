<?php
session_start();
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");
require_once("config.php");
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if ($conn->connect_error) {
    echo json_encode(["error" => "Database connection failed"]);
    exit;
}

// Local classification function
function localClassifyComment($comment) {
    // Load profanity words
    $profanityFile = __DIR__ . '/profanity.txt';
    $profanityWords = [];
    
    if (file_exists($profanityFile)) {
        $profanityWords = file($profanityFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    } else {
        // Create default profanity list if file doesn't exist
        $defaultProfanity = ["damn", "shit", "fuck", "ass", "crap", "bitch"];
        file_put_contents($profanityFile, implode("\n", $defaultProfanity));
        $profanityWords = $defaultProfanity;
    }
    
    // Check for profanity
    foreach ($profanityWords as $word) {
        if (stripos($comment, $word) !== false) {
            return "profanity";
        }
    }
    
    // Check for serious health claims
    $healthClaimPatterns = [
        '/you definitely have (.*)/i',
        '/it\'s 100% (.*)/i',
        '/you\'re clearly (.*)/i',
        '/you\'re definitely infected with (.*)/i',
        '/(.*) is for life/i'
    ];
    
    foreach ($healthClaimPatterns as $pattern) {
        if (preg_match($pattern, $comment)) {
            return "serious_health_claim";
        }
    }
    
    return "not_serious";
}

$contentType = isset($_SERVER["CONTENT_TYPE"]) ? trim($_SERVER["CONTENT_TYPE"]) : '';
if (strpos($contentType, 'application/json') !== false) {
    $json = file_get_contents('php://input');
    $data = json_decode($json, true);
} else {
    $data = $_POST;
}

if (!$data || !isset($data['comment_text'], $data['user_id'], $data['post_id'])) {
    echo json_encode(["error" => "Invalid request"]);
    exit;
}

$user_id = intval($data['user_id']);
$post_id = intval($data['post_id']);
$comment = $conn->real_escape_string($data['comment_text']);
$image_path = "";

if (!empty($_FILES['image']['tmp_name'])) {
    $uploadDir = __DIR__ . "/../../uploads/";
    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }
    $fileName = time() . "_" . basename($_FILES['image']['name']);
    $dest = $uploadDir . $fileName;
    if (move_uploaded_file($_FILES['image']['tmp_name'], $dest)) {
        $image_path = realpath($dest);
    }
}

// Try AI service first, fallback to local classifier
$label = "not_serious";
$blurredImagePath = "";

try {
    $payload = ["comment_text" => $comment];
    if ($image_path) {
        $payload["image_path"] = $image_path;
    }
    
    $ch = curl_init("http://127.0.0.1:5000/moderate");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ["Content-Type: application/json"]);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
    curl_setopt($ch, CURLOPT_TIMEOUT, 5); // Reduced timeout
    
    $response = curl_exec($ch);
    
    if (!curl_errno($ch)) {
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        if ($httpCode == 200 && $response) {
            $ai = json_decode($response, true);
            $label = $ai['label'] ?? "not_serious";
            if (!empty($ai['blurred_image'])) {
                $blurredImagePath = $ai['blurred_image'];
            }
        }
    }
    curl_close($ch);
} catch (Exception $e) {
    // Fall through to local classifier
}

// Use local classifier if AI service failed
if ($label === "not_serious") {
    $label = localClassifyComment($comment);
}

$userQ = $conn->query("SELECT ac_status, is_doctor FROM users WHERE id = $user_id");
if ($userQ->num_rows === 0) {
    echo json_encode(["error" => "User not found"]);
    exit;
}

$user = $userQ->fetch_assoc();
$isDoctor = intval($user['is_doctor']);
$ac_status = intval($user['ac_status']);
$action = "allow";
$message = "Your comment has been posted.";

if (!$isDoctor && ($label === "profanity" || $label === "serious_health_claim")) {
    $new = $ac_status < 2 ? max(3, $ac_status + 1) : $ac_status;
    if ($new > 5) {
        $new = 2;
        $action = "blocked";
        $message = "Your account has been blocked after multiple harmful claims.";
    } else {
        $action = "warn";
        $message = "Warning ($new/5): Unsupported medical claim.";
    }
    $conn->query("UPDATE users SET ac_status = $new WHERE id = $user_id");
    echo json_encode(["status" => $action, "message" => $message, "ai_label" => $label]);
    exit;
}

if ($action === "allow") {
    $stmt = $conn->prepare("INSERT INTO comments (post_id, user_id, comment) VALUES (?, ?, ?)");
    $stmt->bind_param("iis", $post_id, $user_id, $comment);
    $stmt->execute();
    $stmt->close();
}

$result = ["status" => $action, "message" => $message, "ai_label" => $label];
if ($blurredImagePath) {
    $result["blurred_image_path"] = basename($blurredImagePath);
}

echo json_encode($result);
$conn->close();
?>