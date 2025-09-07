<?php
declare(strict_types=1);

require_once 'config.php';
session_start();

// Temporary: hardcoded user ID for testing (remove in production)
const TEST_USER_ID = 170;
if (!isset($_SESSION['user_id'])) {
    $_SESSION['user_id'] = TEST_USER_ID;
}

$userId = $_SESSION['user_id'] ?? null;
if ($userId === null) {
    header('Location: ../../index.php?error=notloggedin');
    exit;
}

// Validate query parameters
$couponName = filter_input(INPUT_GET, 'name', FILTER_SANITIZE_STRING);
$priceInput = filter_input(INPUT_GET, 'price', FILTER_VALIDATE_INT);

if ($couponName === null || $priceInput === false || $priceInput <= 0) {
    header('Location: ../../index.php?error=invalidparams');
    exit;
}

$price = $priceInput;

// Establish database connection
$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if ($mysqli->connect_errno) {
    header('Location: ../../index.php?error=dbconnection');
    exit;
}

// Enable exceptions for easier error handling
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    // Ensure 'discount' column exists
    $result = $mysqli->query("SHOW COLUMNS FROM `user` LIKE 'discount'");
    if ($result->num_rows === 0) {
        $mysqli->query("ALTER TABLE `user` ADD COLUMN `discount` INT NOT NULL DEFAULT 0");
    }

    // Verify user existence
    $stmt = $mysqli->prepare('SELECT `id` FROM `user` WHERE `id` = ?');
    $stmt->bind_param('i', $userId);
    $stmt->execute();
    $userResult = $stmt->get_result();
    $stmt->close();

    if ($userResult->num_rows === 0) {
        header('Location: ../../index.php?error=usernotfound');
        exit;
    }

    // Retrieve user's credits
    $stmt = $mysqli->prepare('SELECT `credits` FROM `credits` WHERE `user_id` = ?');
    $stmt->bind_param('i', $userId);
    $stmt->execute();
    $creditsResult = $stmt->get_result();
    $stmt->close();

    if ($creditsResult->num_rows === 0) {
        header('Location: ../../index.php?error=creditsnotfound');
        exit;
    }

    $creditsRow = $creditsResult->fetch_assoc();
    $availableCredits = (int)$creditsRow['credits'];

    if ($availableCredits < $price) {
        header('Location: ../../index.php?error=insufficientcredits');
        exit;
    }

    // Perform updates inside transaction for consistency
    $mysqli->begin_transaction();

    $updateDiscountStmt = $mysqli->prepare('UPDATE `user` SET `discount` = `discount` + ? WHERE `id` = ?');
    $updateDiscountStmt->bind_param('ii', $price, $userId);
    $updateDiscountStmt->execute();
    $updateDiscountStmt->close();

    $newCredits = $availableCredits - $price;
    $updateCreditsStmt = $mysqli->prepare('UPDATE `credits` SET `credits` = ? WHERE `user_id` = ?');
    $updateCreditsStmt->bind_param('ii', $newCredits, $userId);
    $updateCreditsStmt->execute();
    $updateCreditsStmt->close();

    $mysqli->commit();

    $mysqli->close();

    // Redirect on success
    header('Location: ../../index.php?success=pointredeemed');
    exit;

} catch (mysqli_sql_exception $ex) {
    if ($mysqli && $mysqli->ping()) {
        $mysqli->rollback();
        $mysqli->close();
    }
    error_log('Credit redemption error: ' . $ex->getMessage());

    // Send generic error for security, detailed errors should be logged only
    header('Location: ../../index.php?error=transactionfailed');
    exit;
}
