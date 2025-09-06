// Switch between signup and login forms
    function switchTab(tab) {
        const signupTab = document.querySelector('.auth-tab:first-child');
        const loginTab = document.querySelector('.auth-tab:last-child');
        const signupForm = document.getElementById('signup-form');
        const loginForm = document.getElementById('login-form');

        if (tab === 'signup') {
            signupTab.classList.add('active');
            loginTab.classList.remove('active');
            signupForm.classList.add('active');
            loginForm.classList.remove('active');
        } else {
            loginTab.classList.add('active');
            signupTab.classList.remove('active');
            loginForm.classList.add('active');
            signupForm.classList.remove('active');
        }
    }

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
    function showAlert(formId, message, type) {
        const alertDiv = document.getElementById(`${formId}-alert`);
        alertDiv.innerHTML = `<div class="alert alert-${type}">${message}</div>`;

        // Auto hide after 5 seconds
        setTimeout(() => {
            alertDiv.innerHTML = '';
        }, 5000);
    }

    // Handle signup form submission
    document.getElementById('signup').addEventListener('submit', function(e) {
        e.preventDefault();

        const firstname = document.getElementById('signup-firstname').value;
        const lastname = document.getElementById('signup-lastname').value;
        const gender = document.getElementById('signup-gender').value;
        const username = document.getElementById('signup-username').value;
        const email = document.getElementById('signup-email').value;
        const password = document.getElementById('signup-password').value;
        const confirmPassword = document.getElementById('signup-confirm').value;
        const terms = document.getElementById('signup-terms').checked;

        // Basic validation
        if (!firstname || !lastname || !gender || !username || !email || !password || !confirmPassword) {
            showAlert('signup', 'Please fill in all required fields', 'danger');
            return;
        }

        if (password !== confirmPassword) {
            showAlert('signup', 'Passwords do not match', 'danger');
            return;
        }

        if (password.length < 8) {
            showAlert('signup', 'Password must be at least 8 characters long', 'danger');
            return;
        }

        if (!terms) {
            showAlert('signup', 'You must agree to the terms and conditions', 'danger');
            return;
        }

        // In a real application, you would send this data to a server
        // For this demo, we'll just show a success message
        showAlert('signup', 'Account created successfully! Redirecting to forum...', 'success');

        // Reset form
        this.reset();

        // Redirect to forum after 2 seconds
        setTimeout(() => {
            window.location.href = 'index.html';
        }, 2000);
    });

    // Handle login form submission
    document.getElementById('login').addEventListener('submit', function(e) {
        e.preventDefault();

        const email = document.getElementById('login-email').value;
        const password = document.getElementById('login-password').value;

        // Basic validation
        if (!email || !password) {
            showAlert('login', 'Please enter your email and password', 'danger');
            return;
        }

        // In a real application, you would send this data to a server for authentication
        // For this demo, we'll just show a success message
        showAlert('login', 'Login successful! Redirecting to forum...', 'success');

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