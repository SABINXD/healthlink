<?php
// Include database connection
include(__DIR__ . "/config/db.php");

// Set header for JSON response
header('Content-Type: application/json');

// Get verification data
$email = $_POST['email'] ?? '';
$code = $_POST['code'] ?? '';

// Simple validation
if (empty($email) || empty($code)) {
    echo json_encode(['status' => 'fail', 'error' => 'Email and code are required']);
    exit;
}

try {
    $conn = getDbConnection();
    
    // Get the verification code and expiration
    $stmt = $conn->prepare("SELECT code, expires_at FROM verification_codes WHERE email = ? AND purpose = 'verify'");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        echo json_encode(['status' => 'fail', 'error' => 'Invalid verification code']);
        exit;
    }
    
    $data = $result->fetch_assoc();
    $storedCode = $data['code'];
    $expiresAt = $data['expires_at'];
    
    // Check if code is expired
    if (strtotime($expiresAt) < time()) {
        echo json_encode(['status' => 'expired', 'error' => 'Verification code has expired']);
        exit;
    }
    
    // Check if code matches
    if ($code != $storedCode) {
        echo json_encode(['status' => 'fail', 'error' => 'Invalid verification code']);
        exit;
    }
    
    // Update user status to verified
    $stmt = $conn->prepare("UPDATE users SET ac_status = 1 WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    
    // Delete the verification code
    $stmt = $conn->prepare("DELETE FROM verification_codes WHERE email = ? AND purpose = 'verify'");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    
    echo json_encode(['status' => 'verified', 'message' => 'Account verified successfully']);
    
    $conn->close();
    
} catch (Exception $e) {
    echo json_encode(['status' => 'fail', 'error' => 'Database error: ' . $e->getMessage()]);
}
?>