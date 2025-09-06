<?php
header("Content-Type: application/json");
require_once("config.php");

$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if ($conn->connect_error) {
    echo json_encode(["error" => "Database connection failed"]);
    exit();
}

$data = json_decode(file_get_contents("php://input"), true);

if (!$data || !isset($data['comment_text'], $data['user_id'], $data['post_id'])) {
    echo json_encode(["error" => "Invalid request"]);
    exit();
}

$user_id = intval($data['user_id']);
$post_id = intval($data['post_id']);
$comment = $conn->real_escape_string($data['comment_text']);

// --- Call Python AI API ---
$ch = curl_init("http://127.0.0.1:5000/moderate");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, ["Content-Type: application/json"]);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(["comment_text" => $comment]));

$response = curl_exec($ch);
curl_close($ch);

if (!$response) {
    echo json_encode(["error" => "AI service not responding"]);
    exit();
}

$ai = json_decode($response, true);
$label = $ai['label'] ?? "safe";
$userQ = $conn->query("SELECT ac_status, is_doctor FROM users WHERE id = $user_id");
if ($userQ->num_rows === 0) {
    echo json_encode(["error" => "User not found"]);
    exit();
}
$user = $userQ->fetch_assoc();
$isDoctor = intval($user['is_doctor']);
$ac_status = intval($user['ac_status']);

// --- Default values ---
$action = "allow";
$message = "✅ Your comment has been posted.";

// --- Apply rules ---
if (!$isDoctor) {
    if ($label === "profanity" || $label === "serious_health_claim") {
        $new_status = ($ac_status < 2 ? max(3, $ac_status + 1) : $ac_status);

        // block if more than 5 strikes
        if ($new_status > 5) {
            $new_status = 2;
            $action = "blocked";
            $message = "🚫 Your account has been blocked after multiple harmful medical claims.";
        } else {
            $action = "warn";
            $message = "⚠️ Warning ($new_status/5): Unsupported medical claim.";
        }

        // update user status
        $conn->query("UPDATE users SET ac_status = $new_status WHERE id = $user_id");

        // 🚫 Do not insert the comment if warn or blocked
        echo json_encode([
            "status" => $action,
            "message" => $message,
            "ai_label" => $label
        ]);
        exit();
    }
}

// ✅ Only insert if allowed
if ($action === "allow") {
    $conn->query("INSERT INTO comments (post_id, user_id, comment) 
                  VALUES ($post_id, $user_id, '$comment')");
}

echo json_encode([
    "status" => $action,
    "message" => $message,
    "ai_label" => $label
]);

$conn->close();
?>
