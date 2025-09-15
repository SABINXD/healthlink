<?php
include_once 'assets/php/function.php';

if (isset($_GET['payment_success'])) {
    $credits = $_GET['credits'] ?? 0;
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Payment Successful - HealthLink</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
        <style>
            body {
                background-color: #f8fafc;
                font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            }
            .payment-container {
                max-width: 600px;
                margin: 100px auto;
                text-align: center;
                background-color: white;
                border-radius: 12px;
                box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
                padding: 40px;
            }
            .success-icon {
                font-size: 64px;
                color: #38a169;
                margin-bottom: 20px;
            }
        </style>
    </head>
    <body>
        <div class="payment-container">
            <div class="success-icon">
                <i class="fas fa-check-circle"></i>
            </div>
            <h2>Payment Successful!</h2>
            <p class="mb-4">You have successfully added <?= $credits ?> credits to your account.</p>
            <a href="?" class="btn btn-primary">Go to Dashboard</a>
        </div>
    </body>
    </html>
    <?php
} elseif (isset($_GET['payment_failed'])) {
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Payment Failed - HealthLink</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
        <style>
            body {
                background-color: #f8fafc;
                font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            }
            .payment-container {
                max-width: 600px;
                margin: 100px auto;
                text-align: center;
                background-color: white;
                border-radius: 12px;
                box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
                padding: 40px;
            }
            .error-icon {
                font-size: 64px;
                color: #e53e3e;
                margin-bottom: 20px;
            }
        </style>
    </head>
    <body>
        <div class="payment-container">
            <div class="error-icon">
                <i class="fas fa-times-circle"></i>
            </div>
            <h2>Payment Failed!</h2>
            <p class="mb-4">We couldn't process your payment. Please try again.</p>
            <a href="?" class="btn btn-primary">Go to Dashboard</a>
        </div>
    </body>
    </html>
    <?php
}
?>