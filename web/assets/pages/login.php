 <!-- Auth Container -->
 <div class="flex-grow flex items-center justify-center py-10">
     <div class="bg-white rounded-lg shadow-lg w-full max-w-md overflow-hidden">
         <div class="bg-gradient-to-r from-primary to-primary-light text-white p-8 text-center">
             <h1 class="text-2xl font-bold mb-2">Welcome Back</h1>
             <p>Log in to continue your health journey</p>
         </div>
         <div class="p-8">
             <div id="login-alert"></div>
             <form method="post" action="assets/php/actions.php?login" id="login">
                 <div class="mb-5">
                     <label for="username_email" class="block mb-2 font-medium text-dark">Email Address</label>
                     <input
                         type="text"
                         id="username_email"
                         name="username_email"
                         value="<?= showFormData('username_email') ?>"
                         class="w-full px-4 py-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-light focus:border-transparent"
                         placeholder="Enter your email"
                         required>
                     <?= showError('username_email') ?>
                 </div>
                 <div class="mb-5">
                     <label for="password" class="block mb-2 font-medium text-dark">Password</label>
                     <div class="relative">
                         <input
                             type="password"
                             id="password"
                             name="password"
                             class="w-full px-4 py-3 pr-12 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-light focus:border-transparent"
                             placeholder="Enter your password"
                             required>
                         <i class="fas fa-eye absolute right-4 top-1/2 transform -translate-y-1/2 text-secondary cursor-pointer" onclick="togglePassword('password')"></i>
                     </div>
                     <?= showError('password') ?>
                 </div>
                 <?= showError('checkuser') ?>
                 <div class="flex items-center justify-between mb-6">
                     <div class="flex items-center">
                         <input type="checkbox" id="remember" name="remember" class="mr-2">
                         <label for="remember" class="text-secondary">Remember me</label>
                     </div>
                     <div class="text-sm">
                         <a href="?forgotpassword&newfp" class="text-primary font-medium">Forgot your password?</a>
                     </div>
                 </div>
                 <div class="mt-8">
                     <button type="submit" class="w-full py-3 bg-primary text-white rounded-md font-medium hover:bg-primary-dark transition-colors">Log In</button>
                 </div>
             </form>

             <div class="text-center text-secondary text-sm mt-6">
                 Don't have an account? <a href="?signup" class="text-primary font-medium">Sign Up</a>
             </div>
         </div>
     </div>
 </div>

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
         const alertDiv = document.getElementById('login-alert');
         alertDiv.innerHTML = `<div class="p-3 rounded-md mb-4 text-sm ${type === 'danger' ? 'bg-red-100 text-red-700 border border-red-200' : 'bg-green-100 text-green-700 border border-green-200'}">${message}</div>`;

         // Auto hide after 5 seconds
         setTimeout(() => {
             alertDiv.innerHTML = '';
         }, 5000);
     }
 </script>