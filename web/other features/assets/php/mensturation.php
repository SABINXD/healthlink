<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menstrual Health Tracker - HealthConnect</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="assets/js/main.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="../css/menstruation.css">
</head>
<body>
    <header>
        <div class="container">
            <div class="header-content">
                <a href="index.html" class="logo">
                    <i class="fas fa-heartbeat"></i>
                    <span>HealthConnect</span>
                </a>
                <nav>
                    <ul>
                        <li><a href="../../index.php">Home</a></li>
                        <li><a href="forum.php">Forum</a></li>
                        <li><a href="professionals.php">Professionals</a></li>
                        <li><a href="tracker.php" class="active">Health Tracker</a></li>
                        <li><a href="resources.php">Resources</a></li>
                    </ul>
                </nav>
                <div class="user-menu">
                    <a href="profile.html" class="btn btn-outline">My Profile</a>
                    <a href="logout.html" class="btn btn-primary">Logout</a>
                </div>
            </div>
        </div>
    </header>
    <div class="mt-12 grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-white rounded-xl shadow-md p-6">
                <div class="text-teal-500 text-3xl mb-4">
                    <i class="fas fa-book-medical"></i>
                </div>
                <h3 class="text-xl font-semibold mb-2">Understanding Your Cycle</h3>
                <p class="text-gray-600">Learn about the different phases of your menstrual cycle and what's normal for your body.</p>
                <a href="#" class="text-teal-600 font-medium mt-3 inline-block">Learn More →</a>
            </div>
            
            <div class="bg-white rounded-xl shadow-md p-6">
                <div class="text-teal-500 text-3xl mb-4">
                    <i class="fas fa-comments"></i>
                </div>
                <h3 class="text-xl font-semibold mb-2">Community Support</h3>
                <p class="text-gray-600">Join discussions with others about menstrual health, symptoms, and wellness tips.</p>
                <a href="forum.php" class="text-teal-600 font-medium mt-3 inline-block">Join Forum →</a>
            </div>
            
            <div class="bg-white rounded-xl shadow-md p-6">
                <div class="text-teal-500 text-3xl mb-4">
                    <i class="fas fa-user-md"></i>
                </div>
                <h3 class="text-xl font-semibold mb-2">Professional Advice</h3>
                <p class="text-gray-600">Connect with healthcare professionals who specialize in reproductive health.</p>
                <a href="professional.php" class="text-teal-600 font-medium mt-3 inline-block">Find Experts →</a>
            </div>
        </div>
    <div class="dashboard-header">
        <div class="container">
            <h1>Menstrual Health Tracker</h1>
            <p>Track your menstrual cycle, predict ovulation, and monitor your reproductive health with our easy-to-use tracker.</p>
        </div>
    </div>
    <div class="container">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <div class="cycle-card">
                <h2><i class="fas fa-chart-pie"></i> Cycle Overview</h2>
                
                <?php if ($avgCycle && $expectedNext): ?>
                    <div class="cycle-info">
                        <div class="info-item">
                            <div class="value"><?= $avgCycle ?></div>
                            <div class="label">Avg. Cycle Length</div>
                        </div>
                        <div class="info-item">
                            <div class="value"><?= round($daysPassed) ?></div>
                            <div class="label">Days Since Last Period</div>
                        </div>
                        <div class="info-item">
                            <div class="value"><?= round($remaining) ?></div>
                            <div class="label">Days Until Next Period</div>
                        </div>
                    </div>
                    
                    <div class="chart-container">
                        <canvas id="cycleChart"></canvas>
                    </div>
                    
                    <div class="expected-date">
                        Next Period: <strong><?= htmlspecialchars($expectedNext); ?></strong><br/>
                        Estimated Ovulation Day: <strong><?= htmlspecialchars($ovulationDay); ?></strong>
                    </div>
                    
                    <div class="upcoming">
                        <h3>Upcoming fertile days:</h3>
                        <ul>
                            <?php foreach ($upcomingDays as $day): ?>
                                <li><?= $day ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php else: ?>
                    <div class="no-data">
                        <i class="fas fa-info-circle"></i>
                        <p>Enter at least 2 period start dates to get predictions and cycle insights.</p>
                    </div>
                <?php endif; ?>
            </div>
            
            <div class="form-box">
                <h3><i class="fas fa-calendar-alt"></i> Enter Period Start Dates</h3>
                <p class="mb-4 text-gray-600">Add your period start dates to track your cycle and get predictions.</p>
                
                <form method="POST">
                    <label class="block text-gray-700 mb-2">Most Recent Period:</label>
                    <input type="date" name="cycle_dates[]" required class="w-full mb-4" />
                    
                    <label class="block text-gray-700 mb-2">Previous Period:</label>
                    <input type="date" name="cycle_dates[]" class="w-full mb-4" />
                    
                    <label class="block text-gray-700 mb-2">Earlier Period:</label>
                    <input type="date" name="cycle_dates[]" class="w-full mb-6" />
                    
                    <button type="submit" class="w-full">Save Period Dates</button>
                </form>
                <div class="mt-8 p-4 bg-blue-50 rounded-lg">
                    <h4 class="font-semibold text-blue-800 mb-2">Health Tips</h4>
                    <ul class="text-sm text-blue-700 space-y-1">
                        <li>• Track your cycle regularly for accurate predictions</li>
                        <li>• Note any symptoms or changes in your cycle</li>
                        <li>• Consult a healthcare provider if you notice irregularities</li>
                    </ul>
                </div>
            </div>
        </div>
    <footer>
        <div class="container">
            <div class="footer-content">
                <div class="footer-column">
                    <h4>About HealthConnect</h4>
                    <p>We're a community-driven platform dedicated to empowering individuals with knowledge and support for their health journeys.</p>
                    <div class="social-links">
                        <a href="#"><i class="fab fa-facebook-f"></i></a>
                        <a href="#"><i class="fab fa-twitter"></i></a>
                        <a href="#"><i class="fab fa-instagram"></i></a>
                        <a href="#"><i class="fab fa-linkedin-in"></i></a>
                    </div>
                </div>
                <div class="footer-column">
                    <h4>Quick Links</h4>
                    <ul>
                        <li><a href="index.php">Home</a></li>
                        <li><a href="forum.php">Forum</a></li>
                        <li><a href="tracker.php">Health Tracker</a></li>
                        <li><a href="professionals.html">Health Professionals</a></li>
                        <li><a href="resources.html">Resources</a></li>
                    </ul>
                </div>
                <div class="footer-column">
                    <h4>Health Topics</h4>
                    <ul>
                        <li><a href="mensturation.php">Menstrual Health</a></li>
                        <li><a href="mental.php">Mental Wellness</a></li>
                        <li><a href="nutrition.php">Nutrition & Diet</a></li>
                        <li><a href="#">Fitness & Exercise</a></li>
                        <li><a href="#">Chronic Conditions</a></li>
                    </ul>
                </div>
                <div class="footer-column">
                    <h4>Contact Us</h4>
                    <ul>
                        <li><i class="fas fa-envelope"></i> info@healthconnect.com</li>
                        <li><i class="fas fa-phone"></i> +1 (555) 123-4567</li>
                        <li><i class="fas fa-map-marker-alt"></i> 123 Health St, Wellness City</li>
                    </ul>
                </div>
            </div>
            <div class="copyright">
                <p>&copy; 2023 HealthConnect Forum. All rights reserved.</p>
            </div>
        </div>
    </footer>
    <script>
        const labels = <?= json_encode($labels) ?>;
        const values = <?= json_encode($values) ?>;
    </script>
    <script src="../js/menstruation.js"></script>
</body>
</html>