<?php
session_start();
require_once('config.php');

// Redirect if user is not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../../index.php?error=notloggedin");
    exit();
}
$user_id = $_SESSION['user_id'];
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
$sql = "SELECT * FROM credits WHERE user_id = ? ORDER BY updated_at DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

// Calculate total points
$total_points = 0;
$rows = [];
while ($row = $result->fetch_assoc()) {
    $total_points += (int)$row['credits'];
    $rows[] = $row; // Store all rows for later rendering
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ads History - HealthConnect</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="assets/css/ads.css">
</head>
<body class="bg-gray-50">
    <header class="bg-white shadow-sm">
        <div class="container mx-auto px-4 py-4">
            <div class="flex justify-between items-center">
                <a href="index.php" class="flex items-center space-x-2">
                    <i class="fas fa-heartbeat text-green-600 text-2xl"></i>
                    <span class="text-xl font-bold text-gray-800">HealthConnect</span>
                </a>
                <nav>
                    <ul class="flex space-x-6">
                        <li><a href="../../index.php" class="text-gray-600 hover:text-green-600">Home</a></li>
                        <li><a href="../assets/php/forum.php" class="text-gray-600 hover:text-green-600">Forum</a></li>
                        <li><a href="../assets/php/menstruation.php" class="text-gray-600 hover:text-green-600">Health Tracker</a></li>
                        <li><a href="../../index.php" class="text-green-600 font-medium">Earn Points</a></li>
                    </ul>
                </nav>
            </div>
        </div>
    </header>

    <main class="container mx-auto px-4 py-8">
        <div class="max-w-4xl mx-auto">
            <div class="flex justify-between items-center mb-8">
                <h1 class="text-3xl font-bold text-gray-800">Your Ads History</h1>
                <a href="../../index.php" class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
                    <i class="fas fa-arrow-left mr-2"></i> Back to Earn Points
                </a>
            </div>

            <div class="bg-white rounded-xl shadow-md overflow-hidden">
                <?php if (count($rows) > 0): ?>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Credits Earned</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <?php foreach ($rows as $row): ?>
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">
                                                <?php echo htmlspecialchars(date('F j, Y, g:i a', strtotime($row['updated_at']))); ?>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900 font-medium">
                                                <?php echo htmlspecialchars($row['credits']); ?> points
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                Completed
                                            </span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="bg-gray-50 px-6 py-4 text-right border-t">
                        <p class="text-gray-700 font-semibold">
                            Total Points Earned: <?php echo htmlspecialchars($total_points); ?> points
                        </p>
                    </div>
                <?php else: ?>
                    <div class="text-center py-12">
                        <i class="fas fa-history text-gray-300 text-5xl mb-4"></i>
                        <h3 class="text-lg font-medium text-gray-900 mb-1">No ads history yet</h3>
                        <p class="text-gray-500">Watch ads to start earning points and building your history.</p>
                        <a href="../../index.php" class="mt-6 inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
                            <i class="fas fa-play mr-2"></i> Watch Ads Now
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </main>
</body>
</html>
