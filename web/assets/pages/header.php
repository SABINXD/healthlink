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
    </style>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.1/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

    <!-- linking css finshed -->




    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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
            box-shadow: 0 2px 10px var(--shadow);
            position: sticky;
            top: 0;
            z-index: 100;
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
            gap: 10px;
            font-size: 24px;
            font-weight: 700;
            color: var(--primary);
            text-decoration: none;
        }

        .logo i {
            font-size: 28px;
            color: var(--primary-light);
        }

        nav ul {
            display: flex;
            list-style: none;
            gap: 25px;
        }

        nav a {
            text-decoration: none;
            color: var(--secondary);
            font-weight: 500;
            transition: color 0.3s;
        }

        nav a:hover {
            color: var(--primary);
        }

        .user-menu {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .btn {
            padding: 8px 16px;
            border-radius: 6px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s;
            border: none;
            text-decoration: none;
            display: inline-block;
        }

        .btn-primary {
            background-color: var(--primary);
            color: white;
        }

        .btn-primary:hover {
            background-color: #2a6b6c;
        }

        .btn-outline {
            background-color: transparent;
            border: 1px solid var(--primary);
            color: var(--primary);
        }

        .btn-outline:hover {
            background-color: var(--primary);
            color: white;
        }

        .btn-large {
            padding: 12px 24px;
            font-size: 16px;
        }

        /* Hero Section */
        .hero {
            background: linear-gradient(135deg, #e6fffa 0%, #f0fff4 100%);
            padding: 80px 0;
            text-align: center;
        }

        .hero h1 {
            font-size: 48px;
            margin-bottom: 20px;
            color: var(--dark);
            line-height: 1.2;
        }

        .hero p {
            font-size: 20px;
            color: var(--secondary);
            max-width: 700px;
            margin: 0 auto 30px;
        }

        .hero-actions {
            display: flex;
            justify-content: center;
            gap: 20px;
            margin-top: 30px;
        }

        /* Features Section */
        .features {
            padding: 80px 0;
            background-color: white;
        }

        .section-header {
            text-align: center;
            margin-bottom: 60px;
        }

        .section-header h2 {
            font-size: 36px;
            margin-bottom: 15px;
            color: var(--dark);
        }

        .section-header p {
            font-size: 18px;
            color: var(--secondary);
            max-width: 700px;
            margin: 0 auto;
        }

        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 40px;
        }

        .feature-card {
            text-align: center;
            padding: 30px;
            border-radius: 10px;
            transition: transform 0.3s, box-shadow 0.3s;
        }

        .feature-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 10px 30px var(--shadow);
        }

        .feature-icon {
            width: 80px;
            height: 80px;
            background-color: var(--primary-light);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
        }

        .feature-icon i {
            font-size: 36px;
            color: white;
        }

        .feature-card h3 {
            font-size: 22px;
            margin-bottom: 15px;
            color: var(--dark);
        }

        .feature-card p {
            color: var(--secondary);
        }

        /* Stats Section */
        .stats {
            padding: 80px 0;
            background-color: var(--primary);
            color: white;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 30px;
            text-align: center;
        }

        .stat-item h3 {
            font-size: 48px;
            margin-bottom: 10px;
        }

        .stat-item p {
            font-size: 18px;
            opacity: 0.9;
        }

        /* Testimonials Section */
        .testimonials {
            padding: 80px 0;
            background-color: white;
        }

        .testimonials-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 30px;
        }

        .testimonial-card {
            background-color: var(--light);
            border-radius: 10px;
            padding: 30px;
            position: relative;
        }

        .testimonial-card::before {
            content: '\f10d';
            font-family: 'Font Awesome 5 Free';
            font-weight: 900;
            position: absolute;
            top: 20px;
            left: 20px;
            font-size: 24px;
            color: var(--primary-light);
            opacity: 0.3;
        }

        .testimonial-content {
            margin-bottom: 20px;
            font-style: italic;
            color: var(--secondary);
        }

        .testimonial-author {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .author-avatar {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            overflow: hidden;
        }

        .author-avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .author-info h4 {
            font-size: 16px;
            margin-bottom: 3px;
            color: var(--dark);
        }

        .author-info p {
            font-size: 14px;
            color: var(--secondary);
        }

        /* Recent Posts Section */
        .recent-posts {
            padding: 80px 0;
            background-color: var(--light);
        }

        .posts-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 30px;
        }

        .post-card {
            background-color: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 5px 15px var(--shadow);
            transition: transform 0.3s, box-shadow 0.3s;
            cursor: pointer;
        }

        .post-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px var(--shadow);
        }

        .post-image {
            height: 200px;
            overflow: hidden;
        }

        .post-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.3s;
        }

        .post-card:hover .post-image img {
            transform: scale(1.05);
        }

        .post-content {
            padding: 25px;
        }

        .post-category {
            display: inline-block;
            background-color: var(--primary-light);
            color: white;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 500;
            margin-bottom: 15px;
        }

        .post-title {
            font-size: 20px;
            margin-bottom: 10px;
            color: var(--dark);
        }

        .post-excerpt {
            color: var(--secondary);
            margin-bottom: 15px;
        }

        .post-meta {
            display: flex;
            justify-content: space-between;
            color: var(--secondary);
            font-size: 14px;
        }

        .post-meta span {
            display: flex;
            align-items: center;
            gap: 5px;
        }

        /* Health Professionals Section */
        .health-professionals {
            padding: 80px 0;
            background-color: white;
        }

        .professionals-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 30px;
        }

        .professional-card {
            background-color: var(--light);
            border-radius: 10px;
            padding: 25px;
            text-align: center;
            transition: transform 0.3s;
        }

        .professional-card:hover {
            transform: translateY(-5px);
        }

        .professional-avatar {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            overflow: hidden;
            margin: 0 auto 20px;
            border: 4px solid var(--primary-light);
        }

        .professional-avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .professional-card h3 {
            font-size: 18px;
            margin-bottom: 5px;
            color: var(--dark);
        }

        .professional-specialty {
            color: var(--primary);
            font-weight: 500;
            margin-bottom: 15px;
        }

        .professional-rating {
            display: flex;
            justify-content: center;
            gap: 5px;
            margin-bottom: 15px;
        }

        .professional-rating i {
            color: #f59e0b;
        }

        .professional-card p {
            color: var(--secondary);
            margin-bottom: 20px;
            font-size: 14px;
        }

        /* Newsletter Section */
        .newsletter {
            padding: 60px 0;
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%);
            color: white;
            text-align: center;
        }

        .newsletter h2 {
            font-size: 32px;
            margin-bottom: 15px;
        }

        .newsletter p {
            font-size: 18px;
            margin-bottom: 30px;
            opacity: 0.9;
        }

        .newsletter-form {
            max-width: 500px;
            margin: 0 auto;
            display: flex;
            gap: 10px;
        }

        .newsletter-form input {
            flex: 1;
            padding: 12px 15px;
            border: none;
            border-radius: 6px;
            font-size: 16px;
        }

        .newsletter-form button {
            padding: 12px 24px;
            background-color: var(--dark);
            color: white;
            border: none;
            border-radius: 6px;
            font-weight: 500;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .newsletter-form button:hover {
            background-color: #1a202c;
        }

        /* Footer */
        footer {
            background-color: var(--dark);
            color: white;
            padding: 60px 0 20px;
        }

        .footer-content {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 40px;
            margin-bottom: 40px;
        }

        .footer-column h4 {
            font-size: 18px;
            margin-bottom: 20px;
            position: relative;
            padding-bottom: 10px;
        }

        .footer-column h4::after {
            content: '';
            position: absolute;
            left: 0;
            bottom: 0;
            width: 50px;
            height: 2px;
            background-color: var(--primary-light);
        }

        .footer-column p {
            color: #cbd5e0;
            margin-bottom: 20px;
        }

        .footer-column ul {
            list-style: none;
        }

        .footer-column ul li {
            margin-bottom: 10px;
        }

        .footer-column a {
            color: #cbd5e0;
            text-decoration: none;
            transition: color 0.3s;
        }

        .footer-column a:hover {
            color: var(--primary-light);
        }

        .social-links {
            display: flex;
            gap: 15px;
            margin-top: 20px;
        }

        .social-links a {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            background-color: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            color: white;
            transition: background-color 0.3s;
        }

        .social-links a:hover {
            background-color: var(--primary-light);
        }

        .copyright {
            text-align: center;
            padding-top: 20px;
            border-top: 1px solid #4a5568;
            color: #a0aec0;
            font-size: 14px;
        }

        /* Responsive */
        @media (max-width: 992px) {
            .hero h1 {
                font-size: 36px;
            }

            .hero p {
                font-size: 18px;
            }

            .hero-actions {
                flex-direction: column;
                align-items: center;
            }
        }

        @media (max-width: 768px) {
            .header-content {
                flex-direction: column;
                gap: 15px;
            }

            nav ul {
                flex-wrap: wrap;
                justify-content: center;
            }

            .section-header h2 {
                font-size: 28px;
            }

            .section-header p {
                font-size: 16px;
            }

            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
            }

            .newsletter-form {
                flex-direction: column;
            }
        }

        @media (max-width: 576px) {
            .hero {
                padding: 60px 0;
            }

            .features,
            .stats,
            .testimonials,
            .recent-posts,
            .health-professionals,
            .newsletter {
                padding: 60px 0;
            }

            .features-grid,
            .testimonials-grid,
            .posts-grid,
            .professionals-grid {
                grid-template-columns: 1fr;
            }

            .stats-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>

<body>
    <!-- Header -->
    <header>
        <div class="container">
            <div class="header-content">
                <a href="index.html" class="logo">
                    <i class="fas fa-heartbeat"></i>
                    <span>HealthConnect</span>
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
                <div class="user-menu">
                    <a href="login.html" class="btn btn-outline">Sign In</a>
                    <a href="signup.html" class="btn btn-primary">Register</a>
                </div>
            </div>
        </div>
    </header>

    <!-- Hero Section -->