<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up - HealthConnect Forum</title>
    <script src="https://cdn.tailwindcss.com"></script>
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
</head>
<body class="bg-gray-50 text-gray-800 flex flex-col min-h-screen">
    <!-- Header -->
    <header class="bg-white shadow-md sticky top-0 z-100">
        <div class="container mx-auto px-4">
            <div class="flex flex-col md:flex-row justify-between items-center py-4">
                <a href="index.html" class="flex items-center gap-2 text-2xl font-bold text-primary mb-4 md:mb-0">
                    <i class="fas fa-heartbeat text-primary-light text-2xl"></i>
                    <span>HealthConnect</span>
                </a>
                <nav>
                    <ul class="flex flex-wrap justify-center gap-6">
                        <li><a href="index.html" class="text-secondary font-medium hover:text-primary transition-colors">Home</a></li>
                        <li><a href="index.html" class="text-secondary font-medium hover:text-primary transition-colors">Forum</a></li>
                        <li><a href="index.html" class="text-secondary font-medium hover:text-primary transition-colors">Resources</a></li>
                        <li><a href="index.html" class="text-secondary font-medium hover:text-primary transition-colors">About</a></li>
                        <li><a href="index.html" class="text-secondary font-medium hover:text-primary transition-colors">Contact</a></li>
                    </ul>
                </nav>
                <div class="flex items-center gap-3 mt-4 md:mt-0">
                    <a href="login.html" class="px-4 py-2 border border-primary text-primary rounded-md font-medium hover:bg-primary hover:text-white transition-colors">Sign In</a>
                    <a href="signup.html" class="px-4 py-2 bg-primary text-white rounded-md font-medium hover:bg-primary-dark transition-colors">Register</a>
                </div>
            </div>
        </div>
    </header>

    <!-- Auth Container -->
    <div class="flex-grow flex items-center justify-center py-10">
        <div class="bg-white rounded-lg shadow-lg w-full max-w-md overflow-hidden">
            <div class="bg-gradient-to-r from-primary to-primary-light text-white p-8 text-center">
                <h1 class="text-2xl font-bold mb-2">Join HealthConnect</h1>
                <p>Create your account to start your health journey</p>
            </div>
            
            <div class="p-8">
                <div id="signup-alert"></div>
                <form id="signup">
                    <div class="mb-5">
                        <label for="signup-name" class="block mb-2 font-medium text-dark">Full Name</label>
                        <input type="text" id="signup-name" class="w-full px-4 py-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-light focus:border-transparent" placeholder="Enter your full name" required>
                    </div>
                    
                    <div class="mb-5">
                        <label for="signup-email" class="block mb-2 font-medium text-dark">Email Address</label>
                        <input type="email" id="signup-email" class="w-full px-4 py-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-light focus:border-transparent" placeholder="Enter your email" required>
                    </div>
                    
                    <div class="mb-5">
                        <label for="signup-password" class="block mb-2 font-medium text-dark">Password</label>
                        <div class="relative">
                            <input type="password" id="signup-password" class="w-full px-4 py-3 pr-12 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-light focus:border-transparent" placeholder="Create a password" required>
                            <i class="fas fa-eye absolute right-4 top-1/2 transform -translate-y-1/2 text-secondary cursor-pointer" onclick="togglePassword('signup-password')"></i>
                        </div>
                        <p class="text-sm text-secondary mt-2">Use 8 or more characters with a mix of letters, numbers & symbols</p>
                    </div>
                    
                    <div class="mb-5">
                        <label for="signup-confirm" class="block mb-2 font-medium text-dark">Confirm Password</label>
                        <div class="relative">
                            <input type="password" id="signup-confirm" class="w-full px-4 py-3 pr-12 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-light focus:border-transparent" placeholder="Confirm your password" required>
                            <i class="fas fa-eye absolute right-4 top-1/2 transform -translate-y-1/2 text-secondary cursor-pointer" onclick="togglePassword('signup-confirm')"></i>
                        </div>
                    </div>
                    
                    <div class="mb-5">
                        <label class="block mb-2 font-medium text-dark">Health Interests (Optional)</label>
                        <div class="grid grid-cols-2 gap-3 mt-2">
                            <div class="flex items-center">
                                <input type="checkbox" id="interest-mental" value="mental" class="mr-2">
                                <label for="interest-mental">Mental Health</label>
                            </div>
                            <div class="flex items-center">
                                <input type="checkbox" id="interest-nutrition" value="nutrition" class="mr-2">
                                <label for="interest-nutrition">Nutrition</label>
                            </div>
                            <div class="flex items-center">
                                <input type="checkbox" id="interest-fitness" value="fitness" class="mr-2">
                                <label for="interest-fitness">Fitness</label>
                            </div>
                            <div class="flex items-center">
                                <input type="checkbox" id="interest-chronic" value="chronic" class="mr-2">
                                <label for="interest-chronic">Chronic Conditions</label>
                            </div>
                            <div class="flex items-center">
                                <input type="checkbox" id="interest-parenting" value="parenting" class="mr-2">
                                <label for="interest-parenting">Parenting</label>
                            </div>
                            <div class="flex items-center">
                                <input type="checkbox" id="interest-aging" value="aging" class="mr-2">
                                <label for="interest-aging">Aging</label>
                            </div>
                        </div>
                    </div>
                    
                    <div class="flex items-center mb-6">
                        <input type="checkbox" id="signup-terms" class="mr-2" required>
                        <label for="signup-terms" class="text-secondary">I agree to the <a href="#" class="text-primary">Terms of Service</a> and <a href="#" class="text-primary">Privacy Policy</a></label>
                    </div>
                    
                    <div class="mt-8">
                        <button type="submit" class="w-full py-3 bg-primary text-white rounded-md font-medium hover:bg-primary-dark transition-colors">Create Account</button>
                    </div>
                </form>
                
                <div class="flex items-center my-6 text-secondary">
                    <div class="flex-grow h-px bg-gray-300"></div>
                    <span class="px-4">or sign up with</span>
                    <div class="flex-grow h-px bg-gray-300"></div>
                </div>
                
                <div class="flex gap-4 mb-6">
                    <a href="#" class="flex-1 flex items-center justify-center gap-2 py-2 border border-gray-300 rounded-md bg-white text-gray-800 font-medium hover:bg-gray-50 transition-colors">
                        <i class="fab fa-google"></i>
                        <span>Google</span>
                    </a>
                    <a href="#" class="flex-1 flex items-center justify-center gap-2 py-2 border border-gray-300 rounded-md bg-white text-gray-800 font-medium hover:bg-gray-50 transition-colors">
                        <i class="fab fa-facebook-f"></i>
                        <span>Facebook</span>
                    </a>
                </div>
                
                <div class="text-center text-secondary text-sm">
                    Already have an account? <a href="login.html" class="text-primary font-medium">Log In</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-dark text-white py-10 mt-auto">
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8 mb-8">
                <div>
                    <h4 class="text-lg font-bold mb-4 relative pb-2">About HealthConnect</h4>
                    <p class="text-gray-300">We're a community-driven platform dedicated to empowering individuals with knowledge and support for their health journeys.</p>
                </div>
                <div>
                    <h4 class="text-lg font-bold mb-4 relative pb-2">Quick Links</h4>
                    <ul class="space-y-2">
                        <li><a href="index.html" class="text-gray-300 hover:text-primary-light transition-colors">Home</a></li>
                        <li><a href="index.html" class="text-gray-300 hover:text-primary-light transition-colors">Forum</a></li>
                        <li><a href="index.html" class="text-gray-300 hover:text-primary-light transition-colors">Resources</a></li>
                        <li><a href="#" class="text-gray-300 hover:text-primary-light transition-colors">Health Professionals</a></li>
                        <li><a href="#" class="text-gray-300 hover:text-primary-light transition-colors">Contact Us</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="text-lg font-bold mb-4 relative pb-2">Health Topics</h4>
                    <ul class="space-y-2">
                        <li><a href="#" class="text-gray-300 hover:text-primary-light transition-colors">Mental Health</a></li>
                        <li><a href="#" class="text-gray-300 hover:text-primary-light transition-colors">Nutrition & Diet</a></li>
                        <li><a href="#" class="text-gray-300 hover:text-primary-light transition-colors">Fitness & Exercise</a></li>
                        <li><a href="#" class="text-gray-300 hover:text-primary-light transition-colors">Chronic Conditions</a></li>
                        <li><a href="#" class="text-gray-300 hover:text-primary-light transition-colors">Preventive Care</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="text-lg font-bold mb-4 relative pb-2">Legal</h4>
                    <ul class="space-y-2">
                        <li><a href="#" class="text-gray-300 hover:text-primary-light transition-colors">Privacy Policy</a></li>
                        <li><a href="#" class="text-gray-300 hover:text-primary-light transition-colors">Terms of Service</a></li>
                        <li><a href="#" class="text-gray-300 hover:text-primary-light transition-colors">Medical Disclaimer</a></li>
                        <li><a href="#" class="text-gray-300 hover:text-primary-light transition-colors">Cookie Policy</a></li>
                        <li><a href="#" class="text-gray-300 hover:text-primary-light transition-colors">Accessibility</a></li>
                    </ul>
                </div>
            </div>
            <div class="pt-6 border-t border-gray-700 text-center text-gray-400 text-sm">
                <p>&copy; 2023 HealthConnect Forum. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script>
        // Toggle password visibility
        function togglePassword(fieldId) {
            const field = document.getElementById(fieldId);
            const icon = field.nextElementSibling;
            
            if (field.type === 'password') {
                field.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                field.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }

        // Show alert message
        function showAlert(message, type) {
            const alertDiv = document.getElementById('signup-alert');
            alertDiv.innerHTML = `<div class="p-3 rounded-md mb-4 text-sm ${type === 'danger' ? 'bg-red-100 text-red-700 border border-red-200' : 'bg-green-100 text-green-700 border border-green-200'}">${message}</div>`;
            
            // Auto hide after 5 seconds
            setTimeout(() => {
                alertDiv.innerHTML = '';
            }, 5000);
        }

        // Handle signup form submission
        document.getElementById('signup').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const name = document.getElementById('signup-name').value;
            const email = document.getElementById('signup-email').value;
            const password = document.getElementById('signup-password').value;
            const confirmPassword = document.getElementById('signup-confirm').value;
            const terms = document.getElementById('signup-terms').checked;
            
            // Basic validation
            if (!name || !email || !password || !confirmPassword) {
                showAlert('Please fill in all required fields', 'danger');
                return;
            }
            
            if (password !== confirmPassword) {
                showAlert('Passwords do not match', 'danger');
                return;
            }
            
            if (password.length < 8) {
                showAlert('Password must be at least 8 characters long', 'danger');
                return;
            }
            
            if (!terms) {
                showAlert('You must agree to the terms and conditions', 'danger');
                return;
            }
            
            // In a real application, you would send this data to a server
            // For this demo, we'll just show a success message
            showAlert('Account created successfully! Redirecting to forum...', 'success');
            
            // Reset form
            this.reset();
            
            // Redirect to forum after 2 seconds
            setTimeout(() => {
                window.location.href = 'index.html';
            }, 2000);
        });

        // Get health interests
        function getHealthInterests() {
            const interests = [];
            const checkboxes = document.querySelectorAll('.interests-grid input[type="checkbox"]:checked');
            
            checkboxes.forEach(checkbox => {
                interests.push(checkbox.value);
            });
            
            return interests;
        }
    </script>
</body>
</html>