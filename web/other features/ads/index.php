<?php
session_start();
require_once('assets/php/config.php');
$user_id=170;
$conn=new mysqli(DB_HOST,DB_USER,DB_PASS,DB_NAME);
$sql=$conn->prepare("SELECT * FROM credits WHERE user_id=?");
$sql->bind_param("i",$user_id);
if($sql->execute()){
$res=$sql->get_result();
$row=$res->fetch_assoc();
$credits=$row ? $row['credits'] : 0;
$sql->close();
}else{
    $credits=0;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HealthConnect - Earn Points</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="assets/css/ads.css">
</head>
<body class="bg-gray-50">
    <header class="bg-white shadow-sm">
        <div class="container mx-auto px-4 py-4">
            <div class="flex justify-between items-center">
                <a href="../index.php" class="flex items-center space-x-2">
                    <i class="fas fa-heartbeat text-green-600 text-2xl"></i>
                    <span class="text-xl font-bold text-gray-800">HealthConnect</span>
                </a>
                <nav>
                    <ul class="flex space-x-6">
                        <li><a href="../index.php" class="text-gray-600 hover:text-green-600">Home</a></li>
                        <li><a href="../assets/php/forum.php" class="text-gray-600 hover:text-green-600">Forum</a></li>
                        <li><a href="../assets/php/menstruation.php" class="text-gray-600 hover:text-green-600">Health Tracker</a></li>
                        <li><a href="index.php" class="text-green-600 font-medium">Earn Points</a></li>
                    </ul>
                </nav>
            </div>
        </div>
    </header>
    <main class="container mx-auto px-4 py-8">
        <div class="max-w-4xl mx-auto">
            <div class="text-center mb-12">
                <h1 class="text-3xl font-bold text-gray-800 mb-4">Earn Points with HealthConnect</h1>
                <p class="text-gray-600 max-w-2xl mx-auto">Watch short ads and earn points that you can use to unlock premium features and content on our platform.</p>
            </div>
            <div class="bg-white rounded-xl shadow-md p-6 mb-8">
                <div class="flex justify-between items-center">
                    <div>
                        <h2 class="text-xl font-semibold text-gray-800">Your Points Balance</h2>
                        <p class="text-gray-600">Earn more points by watching ads</p>
                    </div>
                    <div class="bg-green-100 text-green-800 px-4 py-2 rounded-full flex items-center">
                        <i class="fas fa-coins mr-2"></i>
                        <span id="userCredits" class="font-bold text-lg"><?=$credits?></span> Points
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-xl shadow-md p-6 mb-8">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-xl font-semibold text-gray-800">Available Ads</h2>
                    <a href="assets/php/ads_history.php" class="inline-flex items-center px-3 py-1 border border-transparent text-sm font-medium rounded-md text-green-700 bg-green-100 hover:bg-green-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                        <i class="fas fa-history mr-1"></i> View History
                    </a>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition">
                        <div class="flex justify-between items-start mb-3">
                            <div>
                                <h3 class="font-medium text-gray-800">Health & Wellness Tips</h3>
                                <p class="text-sm text-gray-600">30 seconds • 10 points</p>
                            </div>
                            <span class="bg-green-100 text-green-800 text-xs px-2 py-1 rounded">New</span>
                        </div>
                        <div class="bg-gray-100 rounded-lg h-32 flex items-center justify-center mb-4">
                            <i class="fas fa-heartbeat text-green-400 text-3xl"></i>
                        </div>
                        <button class="watch-ad-btn w-full py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
                            <i class="fas fa-play mr-2"></i> Watch Ad
                        </button>
                    </div>
                    
                    <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition">
                        <div class="flex justify-between items-start mb-3">
                            <div>
                                <h3 class="font-medium text-gray-800">Nutrition Advice</h3>
                                <p class="text-sm text-gray-600">30 seconds • 10 points</p>
                            </div>
                            <span class="bg-green-100 text-green-800 text-xs px-2 py-1 rounded">Popular</span>
                        </div>
                        <div class="bg-gray-100 rounded-lg h-32 flex items-center justify-center mb-4">
                            <i class="fas fa-apple-alt text-green-400 text-3xl"></i>
                        </div>
                        <button class="watch-ad-btn w-full py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
                            <i class="fas fa-play mr-2"></i> Watch Ad
                        </button>
                    </div>
                    
                    <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition">
                        <div class="flex justify-between items-start mb-3">
                            <div>
                                <h3 class="font-medium text-gray-800">Mental Wellness</h3>
                                <p class="text-sm text-gray-600">30 seconds • 10 points</p>
                            </div>
                            <span class="bg-green-100 text-green-800 text-xs px-2 py-1 rounded">Featured</span>
                        </div>
                        <div class="bg-gray-100 rounded-lg h-32 flex items-center justify-center mb-4">
                            <i class="fas fa-brain text-green-400 text-3xl"></i>
                        </div>
                        <button class="watch-ad-btn w-full py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
                            <i class="fas fa-play mr-2"></i> Watch Ad
                        </button>
                    </div>
                    
                    <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition">
                        <div class="flex justify-between items-start mb-3">
                            <div>
                                <h3 class="font-medium text-gray-800">Fitness Tips</h3>
                                <p class="text-sm text-gray-600">30 seconds • 10 points</p>
                            </div>
                            <span class="bg-green-100 text-green-800 text-xs px-2 py-1 rounded">Bonus</span>
                        </div>
                        <div class="bg-gray-100 rounded-lg h-32 flex items-center justify-center mb-4">
                            <i class="fas fa-dumbbell text-green-400 text-3xl"></i>
                        </div>
                        <button class="watch-ad-btn w-full py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
                            <i class="fas fa-play mr-2"></i> Watch Ad
                        </button>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-xl shadow-md p-6">
                <h2 class="text-xl font-semibold text-gray-800 mb-4">Doctor Appointment</h2>
                <div class="coupon-container">
                <?php
                $query = "SELECT * FROM cupons WHERE cupon_expire >= CURDATE()";
                $result = $conn->query($query);
                
                if ($result && $result->num_rows > 0) {
                    while($row = $result->fetch_assoc()) {
                        $cupon_name = htmlspecialchars($row['cupon_name']);
                        $price = htmlspecialchars($row['price']);
                        $cupon_expire = htmlspecialchars($row['cupon_expire']);
                        
                        echo '<div class="coupon-card">
                            <div class="coupon-image">
                                '.$cupon_name.'
                            </div>
                            <div class="coupon-details">
                                <div class="coupon-detail">
                                    <div class="coupon-label">Price</div>
                                    <div class="coupon-value">₹'.$price.'</div>
                                </div>
                                <div class="coupon-detail">
                                    <div class="coupon-label">Expiry</div>
                                    <div class="coupon-value">'.$cupon_expire.'</div>
                                </div>
                                <div class="coupon-detail">
                                    <div class="coupon-label">Status</div>
                                    <div class="coupon-status">
                                        <a href="assets/php/use_credits.php?name='.urlencode($cupon_name).'&price='.$price.'" class="text-green-600 font-semibold hover:underline">Use Coupon</a>
                                    </div>
                                </div>
                            </div>
                            <div class="coupon-ribbon">SAVE</div>
                        </div>';
                    }
                } else {
                    echo '<div class="text-center py-8 text-gray-500">
                        <i class="fas fa-ticket-alt text-4xl mb-3 text-gray-300"></i>
                        <p>No active cupons available at the moment.</p>
                        <p class="text-sm mt-2">Check back later for new offers!</p>
                    </div>';
                }
                ?>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-8">
                    <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition">
                        <div class="text-green-500 text-2xl mb-2">
                            <i class="fas fa-user-md"></i>
                        </div>
                        <h3 class="font-medium text-gray-800 mb-1">Priority Consultation</h3>
                        <p class="text-sm text-gray-600">Book appointments faster</p>
                    </div>
                    <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition">
                        <div class="text-green-500 text-2xl mb-2">
                            <i class="fas fa-gift"></i>
                        </div>
                        <h3 class="font-medium text-gray-800 mb-1">Exclusive Rewards</h3>
                        <p class="text-sm text-gray-600">Special offers and discounts</p>
                    </div>
                    <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition">
                        <div class="text-green-500 text-2xl mb-2">
                            <i class="fas fa-star"></i>
                        </div>
                        <h3 class="font-medium text-gray-800 mb-1">Premium Features</h3>
                        <p class="text-sm text-gray-600">Unlock advanced tools</p>
                    </div>
                </div>
            </div>
        </div>
    </main>
    <div id="adModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
        <div class="bg-white rounded-xl shadow-xl p-6 max-w-md w-full mx-4">
            <div class="text-center">
                <h3 class="text-xl font-semibold mb-4">Watching Ad</h3>
                <div class="mb-6">
                    <div class="bg-gray-200 rounded-lg h-48 flex items-center justify-center">
                        <i class="fas fa-ad text-gray-400 text-5xl"></i>
                    </div>
                </div>
                <div class="mb-4">
                    <div class="w-full bg-gray-200 rounded-full h-2.5">
                        <div id="adProgress" class="bg-green-500 h-2.5 rounded-full" style="width: 0%"></div>
                    </div>
                    <p class="text-sm text-gray-600 mt-2">Please wait <span id="adTimer">30</span> seconds</p>
                </div>
                <button id="skipAdBtn" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg cursor-not-allowed" disabled>
                    Skip Ad
                </button>
            </div>
        </div>
    </div>
    <div id="successMessage" class="fixed bottom-4 right-4 bg-green-500 text-white px-6 py-4 rounded-lg shadow-lg transform transition-transform duration-300 translate-y-20 opacity-0">
        <div class="flex items-center">
            <i class="fas fa-check-circle mr-3 text-xl"></i>
            <div>
                <p class="font-medium">Congratulations!</p>
                <p class="text-sm">You've earned 10 points for watching the ad.</p>
            </div>
        </div>
    </div>
    <script src="assets/js/ads.js"></script>
</body>
</html>