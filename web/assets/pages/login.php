<link rel="stylesheet" href="assets/css/login.css">
<!-- Login Container -->
<div class="auth-container">
    <div class="auth-card">
        <div class="auth-header">
            <h1>Welcome Back</h1>
            <p>Log in to your HealthConnect account</p>
        </div>
        <div class="auth-body">
            <div id="login-alert"></div>
            <form id="login">
                <div class="form-group">
                    <label for="login-email">Email Address</label>
                    <input type="email" id="login-email" class="form-control" placeholder="Enter your email" required>
                </div>

                <div class="form-group">
                    <label for="login-password">Password</label>
                    <div class="input-group">
                        <input type="password" id="login-password" class="form-control" placeholder="Enter your password" required>
                        <i class="fas fa-eye input-group-icon" onclick="togglePassword('login-password')"></i>
                    </div>
                </div>

                <div class="form-check">
                    <input type="checkbox" id="login-remember">
                    <label for="login-remember">Remember me</label>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary btn-block">Log In</button>
                </div>
            </form>

            <div class="auth-footer">
                <a href="#">Forgot your password?</a>
            </div>

            <div class="auth-divider">
                <span>or log in with</span>
            </div>

            <div class="social-login">
                <a href="#" class="social-btn">
                    <i class="fab fa-google"></i>
                    <span>Google</span>
                </a>
                <a href="#" class="social-btn">
                    <i class="fab fa-facebook-f"></i>
                    <span>Facebook</span>
                </a>
            </div>

            <div class="auth-footer">
                Don't have an account? <a href="signup.html">Sign Up</a>
            </div>
        </div>
    </div>
</div>