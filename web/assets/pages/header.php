<?php global $user; ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="../healthlink/assets/img/circle-icon.png" type="image/x-icon">
    <title><?= $data["page_title"] ?></title>
    <!-- linking css -->
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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#2c7a7b',
                        'primary-light': '#4fd1c5',
                        secondary: '#4a5568',
                        light: '#f7fafc',
                        dark: '#2d3748',
                        danger: '#e53e3e',
                        success: '#38a169',
                        border: '#e2e8f0',
                    }
                }
            }
        }
    </script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.1/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <style>
        :root {
            --primary: #2c7a7b;
            --primary-light: '#4fd1c5';
            --primary-dark: '#2a6b6c';
            --secondary: '#4a5568';
            --light: '#f7fafc';
            --dark: '#2d3748';
            --danger: '#e53e3e';
            --success: '#38a169';
            --border: '#e2e8f0';
            --shadow: rgba(0, 0, 0, 0.1);
            --shadow-lg: rgba(0, 0, 0, 0.15);
            --gradient: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background-color: #f8fafc;
            color: var(--dark);
            line-height: 1.6;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }

        /* Header Styles */
        header {
            background-color: white;
            box-shadow: 0 4px 20px var(--shadow);
            position: sticky;
            top: 0;
            z-index: 1000;
            backdrop-filter: blur(10px);
            background-color: rgba(255, 255, 255, 0.95);
            transition: all 0.3s ease;
        }

        .header-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 0;
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 26px;
            font-weight: 700;
            color: var(--primary);
            text-decoration: none;
            transition: transform 0.3s ease;
        }

        .logo:hover {
            transform: scale(1.05);
        }

        .logo img {
            height: 40px;
            transition: transform 0.3s ease;
        }

        .logo:hover img {
            transform: rotate(5deg);
        }

        nav ul {
            display: flex;
            list-style: none;
            gap: 30px;
        }

        nav a {
            text-decoration: none;
            color: var(--secondary);
            font-weight: 500;
            font-size: 16px;
            padding: 8px 0;
            position: relative;
            transition: color 0.3s ease;
        }

        nav a::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 0;
            height: 2px;
            background-color: var(--primary);
            transition: width 0.3s ease;
        }

        nav a:hover {
            color: var(--primary);
        }

        nav a:hover::after {
            width: 100%;
        }

        .user-menu {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .btn {
            padding: 10px 20px;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            border: none;
            text-decoration: none;
            display: inline-block;
            font-size: 15px;
            box-shadow: 0 2px 5px var(--shadow);
        }

        .btn-primary {
            background: var(--gradient);
            color: white;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px var(--shadow-lg);
        }

        .btn-outline {
            background-color: transparent;
            border: 2px solid var(--primary);
            color: var(--primary);
        }

        .btn-outline:hover {
            background-color: var(--primary);
            color: white;
            transform: translateY(-2px);
        }

        .btn-large {
            padding: 14px 28px;
            font-size: 17px;
        }

        .notification-badge {
            position: absolute;
            top: -8px;
            right: -8px;
            background-color: var(--danger);
            color: white;
            border-radius: 50%;
            width: 22px;
            height: 22px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 11px;
            font-weight: bold;
            box-shadow: 0 2px 5px rgba(229, 62, 62, 0.3);
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% {
                box-shadow: 0 0 0 0 rgba(229, 62, 62, 0.4);
            }

            70% {
                box-shadow: 0 0 0 10px rgba(229, 62, 62, 0);
            }

            100% {
                box-shadow: 0 0 0 0 rgba(229, 62, 62, 0);
            }
        }

        /* Dropdown Styles */
        .dropdown {
            position: relative;
        }

        .dropdown-menu {
            position: absolute;
            top: 100%;
            right: 0;
            /* Align to right */
            z-index: 1050;
            display: none;
            min-width: 10rem;
            padding: 0.5rem 0;
            margin: 0.125rem 0 0;
            font-size: 1rem;
            color: #212529;
            text-align: left;
            list-style: none;
            background-color: #fff;
            background-clip: padding-box;
            border: 1px solid rgba(0, 0, 0, .15);
            border-radius: 0.25rem;
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, .175);
        }

        .dropdown-menu.show {
            display: block;
        }

        .dropdown-item {
            display: flex;
            align-items: center;
            padding: 10px 20px;
            color: var(--dark);
            text-decoration: none;
            transition: all 0.2s ease;
        }

        .dropdown-item:hover {
            background-color: var(--light);
            color: var(--primary);
        }

        .dropdown-item i {
            margin-right: 10px;
            width: 20px;
            text-align: center;
        }

        .dropdown-divider {
            height: 1px;
            background-color: var(--border);
            margin: 8px 0;
        }

        .user-avatar {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid var(--primary-light);
        }

        /* Responsive */
        @media (max-width: 992px) {
            nav ul {
                flex-wrap: wrap;
                justify-content: center;
                gap: 15px;
            }

            .header-content {
                flex-direction: column;
                gap: 15px;
            }
        }

        @media (max-width: 768px) {
            .user-menu {
                flex-direction: column;
                width: 100%;
            }
        }
    </style>
</head>

<body>
    <!-- Header -->
    <header>
        <div class="container">
            <div class="header-content">
                <a href="?" class="logo">
                    <img src="assets/img/logo.png" class="h-10" alt="">
                </a>
                <nav>
                    <ul>
                        <li><a href="#features">Features</a></li>
                        <li><a href="#testimonials">Testimonials</a></li>
                        <li><a href="#forum">Forum</a></li>
                        <li><a href="#professionals">Professionals</a></li>
                        <li><a href="#contact">Contact</a></li>
                    </ul>
                </nav>
                <?php
                if (!isset($_SESSION['Auth'])) {
                ?>
                    <div class="flex items-center space-x-4">
                        <a href="?login" class="px-4 py-2 rounded-md font-medium border border-[#2c7a7b] text-[#2c7a7b] hover:bg-[#2c7a7b] hover:text-white transition-all">Sign In</a>
                        <a href="?signup" class="px-4 py-2 rounded-md font-medium bg-[#2c7a7b] text-white hover:bg-[#2a6b6c] transition-all">Register</a>

                    </div>
                <?php

                } else {
                ?>

                    <div class="user-menu">
                        <!-- Messages Button -->
                        <div class="position-relative me-3">
                            <button data-bs-toggle="offcanvas" href="#messages_sidebar"
                                class="btn btn-outline-primary position-relative">
                                <i class="bi bi-envelope-fill me-1"></i> Messages
                                <span id="msgcounter" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                    <!-- This will be updated by JS -->
                                </span>
                            </button>
                        </div>

                        <!-- User Dropdown -->
                        <div class="dropdown">
                            <button class="btn btn-outline-secondary dropdown-toggle d-flex align-items-center"
                                type="button"
                                id="userDropdown"
                                aria-expanded="false">
                                <img src="assets/img/profile/<?= !empty($user['profile_pic']) ? $user['profile_pic'] : 'default-avatar.png' ?>"
                                    alt="<?= !empty($user['name']) ? $user['name'] : 'User' ?>"
                                    class="user-avatar me-2">
                                <?= !empty($user['name']) ? $user['name'] : 'My Account' ?>
                            </button>
                            <ul class="dropdown-menu" id="userDropdownMenu">
                                <li>
                                    <div class="px-4 py-3 border-bottom border-green-100">
                                        <p class="mb-0 text-sm fw-medium text-gray-900"><?= !empty($user['name']) ? $user['name'] : 'User' ?></p>
                                        <p class="mb-0 text-sm text-gray-500"><?= !empty($user['email']) ? $user['email'] : 'user@example.com' ?></p>
                                    </div>
                                </li>
                                <li><a class="dropdown-item" href="?u=<?= !empty($user['username']) ? $user['username'] : 'profile' ?>"><i class="bi bi-person"></i> My Profile</a></li>
                                <li><a class="dropdown-item" href="?editprofile"><i class="bi bi-gear"></i> Settings</a></li>
                                <?php
                                if ($user['is_doctor'] == 0 && $user['ac_status'] == 1) {
                                ?>
                                    <li><a class="dropdown-item" href="?myappointment"><i class="bi bi-calendar-check"></i> My Appointments</a></li>
                                <?php
                                } elseif ($user['is_doctor'] == 1 && $user['ac_status'] == 1) {
                                ?>
                                    <li><a class="dropdown-item" href="?manageappointment"><i class="bi bi-calendar-event"></i> Manage Appointments</a></li>
                                <?php
                                }
                                ?>
                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                                <li><a class="dropdown-item text-danger" href="./assets/php/actions.php?logout"><i class="bi bi-box-arrow-right"></i> Logout</a></li>
                            </ul>
                        </div>
                    </div>
                <?php

                }
                ?>

            </div>
        </div>
    </header>

    <!-- Load Bootstrap JS at the end of the body -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Simple dropdown implementation
            const dropdownButton = document.getElementById('userDropdown');
            const dropdownMenu = document.getElementById('userDropdownMenu');

            if (dropdownButton && dropdownMenu) {
                // Toggle dropdown when button is clicked
                dropdownButton.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();

                    const isVisible = dropdownMenu.classList.contains('show');

                    // Close all dropdowns
                    document.querySelectorAll('.dropdown-menu.show').forEach(menu => {
                        menu.classList.remove('show');
                    });

                    // Toggle current dropdown
                    if (!isVisible) {
                        dropdownMenu.classList.add('show');
                    }
                });

                // Close dropdown when clicking outside
                document.addEventListener('click', function(e) {
                    if (!dropdownButton.contains(e.target) && !dropdownMenu.contains(e.target)) {
                        dropdownMenu.classList.remove('show');
                    }
                });
            }

            // Simulate message counter updates
            const msgCounter = document.getElementById('msgcounter');
            if (msgCounter) {
                setTimeout(() => {
                    msgCounter.textContent = '3';
                    msgCounter.classList.remove('hidden');
                    // Animate the counter
                    gsap.fromTo(msgCounter, {
                        scale: 0
                    }, {
                        scale: 1.2,
                        duration: 0.3,
                        ease: "back.out(1.7)",
                        onComplete: () => {
                            gsap.to(msgCounter, {
                                scale: 1,
                                duration: 0.2
                            });
                        }
                    });
                }, 5000);
            }
        });
    </script>