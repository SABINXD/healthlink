<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menstrual Health Tracker - HealthConnect</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="../js/main.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="../css/menstruation.css">
    <link rel="stylesheet" href="../css/styles.css">
</head>
<body>
    <header>
        <div class="container">
            <div class="header-content">
                <a href="../" class="logo">
                    <i class="fas fa-heartbeat"></i>
                    <span>HealthConnect</span>
                </a>
                <nav>
                    <ul>
                        <li><a href="../../index.php">Home</a></li>
                        <li><a href="forum.php">Forum</a></li>
                        <li><a href="professionals.php">Professionals</a></li>
                        <li><a href="mensturation.php" class="active">Mensuration Tracker</a></li>
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
    <div class="container">        
            <div class="bg-white rounded-xl shadow-md p-6">
                <div class="text-teal-500 text-3xl mb-4">
                    <i class="fas fa-user-md"></i>
                </div>
                <h3 class="text-xl font-semibold mb-2">Professional Advice</h3>
                <p class="text-gray-600">Connect with healthcare professionals who specialize in reproductive health.</p>
                <a href="#" class="text-teal-600 font-medium mt-3 inline-block">Find Experts →</a>
            </div>
        </div>
        
        <!-- Comment Section -->
        <div class="mt-12">
            <div class="bg-white rounded-xl shadow-md p-6">
                <h3 class="text-xl font-semibold mb-4"><i class="fas fa-comment-dots"></i> Share Your Experience</h3>    
                <form id="commentForm" class="space-y-4">
                    <div>
                        <label for="commentText" class="block text-gray-700 font-medium mb-2">Your Comment</label>
                        <textarea id="commentText" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition" rows="4" placeholder="Share your thoughts or questions..." required></textarea>
                    </div>
                    
                    <div>
                        <label for="commentImage" class="block text-gray-700 font-medium mb-2">Upload Image (optional)</label>
                        <div class="flex items-center space-x-4">
                            <label for="commentImage" class="flex-1 cursor-pointer">
                                <div class="flex items-center justify-center px-6 py-4 border-2 border-dashed border-gray-300 rounded-lg bg-gray-50 hover:bg-gray-100 transition">
                                    <div class="text-center">
                                        <i class="fas fa-cloud-upload-alt text-gray-400 text-2xl mb-2"></i>
                                        <p class="text-sm text-gray-600">Click to upload or drag and drop</p>
                                        <p class="text-xs text-gray-500 mt-1">PNG, JPG, GIF up to 10MB</p>
                                    </div>
                                </div>
                                <input type="file" id="commentImage" name="image" accept="image/*" class="hidden" />
                            </label>
                            <div id="imagePreview" class="hidden">
                                <img src="" alt="Preview" class="h-24 w-24 object-cover rounded-lg border" />
                                <button type="button" id="removeImage" class="mt-2 text-sm text-red-600 hover:text-red-800">
                                    <i class="fas fa-trash-alt mr-1"></i> Remove
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <div class="flex justify-end">
                        <button type="submit" class="px-6 py-3 bg-teal-600 text-white font-medium rounded-lg hover:bg-teal-700 focus:outline-none focus:ring-2 focus:ring-teal-500 focus:ring-offset-2 transition">
                            Post Comment
                        </button>
                    </div>
                </form>
                
                <div id="responseBox" class="mt-6 p-4 rounded-lg hidden"></div>
            </div>
        </div>
    </div>
    
    <footer>
        <div class="container">
            <div class="footer-content">
                <div class="footer-column">
                    <h4>About HealthLink</h4>
                    <p>We connect health wiith connectivity</p>
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
                        <li><a href="../../index.php">Home</a></li>
                        <li><a href="forum.php">Forum</a></li>
                        <li><a href="mensturation.php">Health Tracker</a></li>
                        <li><a href="professionals.html">Health Professionals</a></li>
                        <li><a href="resources.html">Resources</a></li>
                    </ul>
                </div>
                <div class="footer-column">
                    <h4>Contact Us</h4>
                    <ul>
                        <li><i class="fas fa-envelope"></i> info@healthconnect.com</li>
                    </ul>
                </div>
            </div>
            <div class="copyright">
                <p>&copy; 2025 HealthLink . All rights reserved.</p>
            </div>
        </div>
    </footer>
    <script>
        const labels = <?= json_encode($labels) ?>;
        const values = <?= json_encode($values) ?>;
    </script>
</body>
</html>