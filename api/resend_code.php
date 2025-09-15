<?php
// Include database connection
include(__DIR__ . "/config/db.php");
// Set header for JSON response
header('Content-Type: application/json');

// Get email
$email = $_POST['email'] ?? '';

// Simple validation
if (empty($email)) {
    echo json_encode(['status' => 'fail', 'error' => 'Email is required']);
    exit;
}

try {
    $conn = getDbConnection();
    
    // Check if user exists and is not verified
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ? AND ac_status = 0");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        echo json_encode(['status' => 'fail', 'error' => 'User not found or already verified']);
        exit;
    }
    
    // Delete any existing verification codes
    $stmt = $conn->prepare("DELETE FROM verification_codes WHERE email = ? AND purpose = 'verify'");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    
    // Generate new verification code
    $verificationCode = rand(100000, 999999);
    
    // Insert new verification code
    $stmt = $conn->prepare("INSERT INTO verification_codes (email, code, purpose) VALUES (?, ?, 'verify')");
    if (!$stmt) {
        error_log("Prepare statement failed: " . $conn->error);
        echo json_encode(["status" => "error", "error" => "Internal server error"]);
        exit;
    }
    
    $stmt->bind_param("si", $email, $verificationCode);
    if (!$stmt->execute()) {
        echo json_encode(["status" => "error", "error" => "Failed to generate verification code"]);
        exit;
    }
    
    // Send email with PHPMailer
    require_once __DIR__ . '/phpmailer/PHPMailerAutoload.php';
    $mail = new PHPMailer;
    
    $mail->isSMTP();
    $mail->Host = 'smtp.example.com'; // Your SMTP server
    $mail->SMTPAuth = true;
    $mail->Username = 'your_email@example.com'; // Your SMTP username
    $mail->Password = 'your_password'; // Your SMTP password
    $mail->SMTPSecure = 'tls';
    $mail->Port = 587;
    
    $mail->setFrom('your_email@example.com', 'HealthLink');
    $mail->addAddress($email);
    $mail->Subject = 'Your Verification Code';
    $mail->Body    = "Your verification code is: $verificationCode";
    
    if(!$mail->send()) {
        error_log("Mailer Error: " . $mail->ErrorInfo);
        echo json_encode(['status' => 'fail', 'error' => 'Failed to send email']);
    } else {
        echo json_encode(['status' => 'success', 'message' => 'Verification code resent']);
    }
    
    $stmt->close();
    $conn->close();
    
} catch (Exception $e) {
    echo json_encode(['status' => 'fail', 'error' => 'Database error: ' . $e->getMessage()]);
}
?>