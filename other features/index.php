<?php
session_start();
$conn = new mysqli("localhost", "root", "", "healthlink");
$dates = [];
$avgCycle = null;
$expectedNext = '';
$daysPassed = 0;
$remaining = 0;
$labels = [];
$values = [];
$upcomingDays = [];
$age = 0;
$defaultCycle = 28;
$userId = 170;

$ageQuery = $conn->prepare("SELECT age FROM users WHERE id = ? AND gender=1");
$ageQuery->bind_param("i", $userId);
$ageQuery->execute();
$ageResult = $ageQuery->get_result();
if ($row = $ageResult->fetch_assoc()) {
    $age = (int)$row['age'];
}

if ($age >= 12 && $age <= 17) {
    $defaultCycle = 33;
} elseif ($age >= 18 && $age <= 35) {
    $defaultCycle = 29;
} elseif ($age >= 36 && $age <= 45) {
    $defaultCycle = 28;
} elseif ($age >= 46) {
    $defaultCycle = 26;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cycle_dates'])) {
    $stmt = $conn->prepare("INSERT INTO menstrual_cycles (user_id, start_date) VALUES (?, ?)");
    foreach ($_POST['cycle_dates'] as $date) {
        if (!empty($date)) {
            $stmt->bind_param("is", $userId, $date);
            $stmt->execute();
        }
    }
    header("Location: index.php");
    exit;
}

$stmt = $conn->prepare("SELECT start_date FROM menstrual_cycles WHERE user_id = ? ORDER BY start_date DESC LIMIT 3");
$stmt->bind_param("i", $userId);
$stmt->execute();
$res = $stmt->get_result();
$rows = [];
while ($r = $res->fetch_assoc()) {
    $rows[] = $r['start_date'];
}
$dates = array_map(function ($d) {
    return date('Y-m-d', strtotime($d));
}, array_reverse($rows));

if (count($dates) >= 2) {
    $sum = 0;
    for ($i = 1; $i < count($dates); $i++) {
        $sum += (strtotime($dates[$i]) - strtotime($dates[$i - 1])) / (60 * 60 * 24);
    }
    $avgCycle = round($sum / (count($dates) - 1));
} else {
    $avgCycle = $defaultCycle;
}

if (!empty($dates)) {
    $lastStart = end($dates);
    $today = date('Y-m-d');
    $daysPassed = (strtotime($today) - strtotime($lastStart)) / (60 * 60 * 24);
    $daysPassed = max(0, min($daysPassed, $avgCycle));
    $remaining = $avgCycle - $daysPassed;
    $expectedNext = date('Y-m-d', strtotime($lastStart . " +{$avgCycle} days"));
    $ovulationDay = date('Y-m-d', strtotime($expectedNext . " -14 days"));
    $daysToOvulation = max(0, (strtotime($ovulationDay) - strtotime($today)) / (60 * 60 * 24));
    $daysToOvulationChart = max(0, $daysToOvulation);
    $postOvulationDays = $remaining - $daysToOvulationChart;
    if ($postOvulationDays < 0) $postOvulationDays = 0;
    $labels = ['Days Passed', 'Days to Ovulation', 'Days After Ovulation'];
    $values = [$daysPassed, $daysToOvulationChart, $postOvulationDays];
    for ($i = 1; $i <= 5; $i++) {
        $upcomingDays[] = date('Y-m-d', strtotime($expectedNext . " +{$i} days"));
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menstrual Health Tracker - HealthConnect</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="assets/css/menstruation.css">
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
                        <li><a href="index.html">Home</a></li>
                        <li><a href="forum.html">Forum</a></li>
                        <li><a href="professionals.html">Professionals</a></li>
                        <li><a href="tracker.php" class="active">Health Tracker</a></li>
                        <li><a href="resources.html">Resources</a></li>
                    </ul>
                </nav>
                <div class="user-menu">
                    <a href="profile.html" class="btn btn-outline">My Profile</a>
                    <a href="logout.html" class="btn btn-primary">Logout</a>
                </div>
            </div>
        </div>
    </header>

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
                <a href="#" class="text-teal-600 font-medium mt-3 inline-block">Join Forum →</a>
            </div>
            
            <div class="bg-white rounded-xl shadow-md p-6">
                <div class="text-teal-500 text-3xl mb-4">
                    <i class="fas fa-user-md"></i>
                </div>
                <h3 class="text-xl font-semibold mb-2">Professional Advice</h3>
                <p class="text-gray-600">Connect with healthcare professionals who specialize in reproductive health.</p>
                <a href="#" class="text-teal-600 font-medium mt-3 inline-block">Find Experts →</a>
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
                        <li><a href="index.html">Home</a></li>
                        <li><a href="forum.html">Forum</a></li>
                        <li><a href="tracker.php">Health Tracker</a></li>
                        <li><a href="professionals.html">Health Professionals</a></li>
                        <li><a href="resources.html">Resources</a></li>
                    </ul>
                </div>
                <div class="footer-column">
                    <h4>Health Topics</h4>
                    <ul>
                        <li><a href="#">Menstrual Health</a></li>
                        <li><a href="#">Mental Wellness</a></li>
                        <li><a href="#">Nutrition & Diet</a></li>
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
    <script src="assets/js/menstruation.js"></script>
</body>
</html>