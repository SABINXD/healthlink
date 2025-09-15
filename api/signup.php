<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require 'PHPMailer/PHPMailer.php';
require 'PHPMailer/SMTP.php';
require 'PHPMailer/Exception.php';

// Set proper headers
header('Content-Type: application/json');

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database connection
$conn = new mysqli('localhost', 'root', '', 'healthlink');

// Check connection
if ($conn->connect_error) {
    echo json_encode(['status' => 'fail', 'error' => 'Database connection failed: ' . $conn->connect_error]);
    exit;
}

// Get POST values
$email     = $_POST['email'] ?? '';
$password  = $_POST['password'] ?? '';
$firstName = $_POST['firstName'] ?? '';
$lastName  = $_POST['lastName'] ?? '';
$username  = $_POST['username'] ?? '';
$gender    = $_POST['gender'] ?? '';

// --- 🟡 Phase 1: Quick email check ---
if (!isset($_POST['firstName'])) {
    if (!$email) {
        echo json_encode(['status' => 'fail', 'error' => 'Missing email']);
        exit;
    }
    
    $stmt = $conn->prepare("SELECT id FROM users WHERE email=?");
    if (!$stmt) {
        echo json_encode(['status' => 'fail', 'error' => 'Database error: ' . $conn->error]);
        exit;
    }
    
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();
    $result = $stmt->num_rows > 0 ? "exists" : "available";
    $stmt->close();
    echo $result;
    exit;
}

// --- 🔄 Phase 2: Full signup with verification ---
if (!$email || !$password || !$firstName || !$lastName || !$username || !$gender) {
    echo json_encode(['status' => 'fail', 'error' => 'Missing one or more required fields']);
    exit;
}

// Check username availability
$stmt = $conn->prepare("SELECT id FROM users WHERE username=?");
if (!$stmt) {
    echo json_encode(['status' => 'fail', 'error' => 'Database error: ' . $conn->error]);
    exit;
}

$stmt->bind_param("s", $username);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    echo json_encode(['status' => 'fail', 'error' => 'Username already exists']);
    $stmt->close();
    $conn->close();
    exit;
}
$stmt->close();

// Check if email already registered
$stmt = $conn->prepare("SELECT id FROM users WHERE email=?");
if (!$stmt) {
    echo json_encode(['status' => 'fail', 'error' => 'Database error: ' . $conn->error]);
    exit;
}

$stmt->bind_param("s", $email);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    echo json_encode(['status' => 'fail', 'error' => 'Email already registered']);
    $stmt->close();
    $conn->close();
    exit;
}
$stmt->close();

// Insert unverified user
$hashedPassword = md5($password); // Consider upgrading to password_hash()
$stmt = $conn->prepare("INSERT INTO users (email, password, first_name, last_name, username, gender, ac_status) VALUES (?, ?, ?, ?, ?, ?, 0)");
if (!$stmt) {
    echo json_encode(['status' => 'fail', 'error' => 'Database error: ' . $conn->error]);
    exit;
}

$stmt->bind_param("ssssss", $email, $hashedPassword, $firstName, $lastName, $username, $gender);

if (!$stmt->execute()) {
    echo json_encode(['status' => 'fail', 'error' => 'Signup failed: ' . $stmt->error]);
    $stmt->close();
    $conn->close();
    exit;
}

// Get the inserted user ID
$insertedId = $stmt->insert_id;
$stmt->close();

// Generate verification code
$code = rand(100000, 999999);
$stmt = $conn->prepare("REPLACE INTO verification_codes (email, code, purpose, created_at) VALUES (?, ?, 'verify', NOW())");
if (!$stmt) {
    echo json_encode(['status' => 'fail', 'error' => 'Database error: ' . $conn->error]);
    exit;
}

$stmt->bind_param("si", $email, $code);
$stmt->execute();
$stmt->close();

// Send verification email
$mail = new PHPMailer(true);
try {
    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = 't32337817@gmail.com';
    $mail->Password   = 'pbmbbsbykwcokuja';
    $mail->SMTPSecure = 'tls';
    $mail->Port       = 587;
    $mail->setFrom('t32337817@gmail.com', 'HealthLink');
    $mail->addAddress($email);
    $mail->Subject = 'Verify your HealthLink account';
    $mail->Body    = "Welcome to HealthLink!\n\nYour verification code is: $code";
    $mail->send();
    
    echo json_encode([
        'status' => 'verify_sent',
        'message' => 'Verification email sent successfully'
    ]);
} catch (Exception $e) {
    echo json_encode([
        'status' => 'fail',
        'error' => 'Failed to send verification email: ' . $mail->ErrorInfo
    ]);
}

$conn->close();
?>