<?php

// Check if user is logged in
$isLoggedIn = isset($_SESSION['Auth']) && $_SESSION['Auth'] === true;
// Get user data if logged in
$user = $isLoggedIn ? $_SESSION['user'] : null;
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="../healthlink/assets/img/circle-icon.png" type="image/x-icon">
    <title><?= isset($data["page_title"]) ? $data["page_title"] : "HealthConnect Forum" ?></title>
    <!-- CSS -->
    <link rel="stylesheet" href="./assets/css/responsive_code.css">
    <link rel="stylesheet" href="./assets/css/style.css">
    <link rel="stylesheet" href="./assets/css/verify_user.css">
    <link rel="stylesheet" href="./assets/css/blocked_user.css">
    <link rel="stylesheet" href="./assets/css/login_page.css">
    <link rel="stylesheet" href="./assets/css/signup_page.css">
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/ScrollTrigger.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root {
            --primary: #2c7a7b;
            --primary-light: #4fd1c5;
            --secondary: #4a5568;
            --light: #f7fafc;
            --dark: #2d3748;
            --danger: #e53e3e;
            --success: #38a169;
            --border: #e2e8f0;
            --shadow: rgba(0, 0, 0, 0.1);
        }

        body {
            background-color: #f8fafc;
            color: var(--dark);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .navbar {
            background-color: white;
            box-shadow: 0 2px 10px var(--shadow);
        }

        .navbar-brand {
            font-weight: 700;
            color: var(--primary) !important;
        }

        .nav-link {
            color: var(--secondary) !important;
            font-weight: 500;
        }

        .nav-link:hover {
            color: var(--primary) !important;
        }

        .notification-badge {
            position: absolute;
            top: -8px;
            right: -8px;
            background-color: var(--danger);
            color: white;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.75rem;
            font-weight: bold;
        }

        .user-avatar {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            object-fit: cover;
        }
    </style>
</head>

<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light sticky-top">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <i class="bi bi-heart-pulse-fill me-2"></i>HealthConnect
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="index.php">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Categories</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Experts</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Resources</a>
                    </li>
                </ul>
                <div class="d-flex align-items-center">
                    <?php if ($isLoggedIn): ?>
                        <!-- Notifications -->
                        <div class="position-relative me-3">
                            <a href="#" class="btn btn-outline-primary position-relative" data-bs-toggle="offcanvas" data-bs-target="#notification_sidebar">
                                <i class="bi bi-bell-fill me-1"></i> Notifications
                                <span class="notification-badge">3</span>
                            </a>
                        </div>
                        <!-- Messages -->
                        <div class="position-relative me-3">
                            <a href="#" class="btn btn-outline-primary position-relative" data-bs-toggle="offcanvas" data-bs-target="#messages_sidebar">
                                <i class="bi bi-envelope-fill me-1"></i> Messages
                                <span class="notification-badge">3</span>
                            </a>
                        </div>
                        <!-- User Account -->
                        <div class="dropdown">
                            <button class="btn btn-outline-secondary dropdown-toggle d-flex align-items-center" type="button" id="userDropdown" data-bs-toggle="dropdown">
                                <img src="./assets/img/profile/<?= htmlspecialchars($user['profile_pic']) ?>" alt="User Avatar" class="user-avatar me-2">
                                <?= htmlspecialchars($user['first_name']) ?>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><a class="dropdown-item" href="?u=<?= htmlspecialchars($user['username']) ?>">My Profile</a></li>
                                <li><a class="dropdown-item" href="#">My Posts</a></li>
                                <li><a class="dropdown-item" href="#">Settings</a></li>
                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                                <li><a class="dropdown-item" href="assets/php/actions.php?logout">Logout</a></li>
                            </ul>
                        </div>
                    <?php else: ?>
                        <!-- Login/Register Buttons -->
                        <a href="login.php" class="btn btn-outline-primary me-2">Sign In</a>
                        <a href="signup.php" class="btn btn-primary">Register</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>