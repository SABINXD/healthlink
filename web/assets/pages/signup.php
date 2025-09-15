 <!-- Auth Container -->
 <div class="flex-grow flex items-center justify-center py-10">
     <div class="bg-white rounded-lg shadow-lg w-full max-w-md overflow-hidden">
         <div class="bg-gradient-to-r from-primary to-primary-light text-white p-8 text-center">
             <h1 class="text-2xl font-bold mb-2">Join HealthLink</h1>
             <p>Create your account to start your health journey</p>
         </div>

         <div class="p-8">
             <div id="signup-alert"></div>
             <form method="post" action="./assets/php/actions.php?signup" id="signup">
                 <!-- First & Last Name -->
                 <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-5">
                     <div>
                         <label for="first_name" class="block mb-2 font-medium text-dark">First Name</label>
                         <input type="text" id="first_name" name="first_name" value="<?= showFormData('first_name') ?>"
                             class="w-full px-4 py-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-light focus:border-transparent"
                             placeholder="First name" required>
                         <?= showError('first_name') ?>
                     </div>
                     <div>
                         <label for="last_name" class="block mb-2 font-medium text-dark">Last Name</label>
                         <input type="text" id="last_name" name="last_name" value="<?= showFormData('last_name') ?>"
                             class="w-full px-4 py-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-light focus:border-transparent"
                             placeholder="Last name" required>
                         <?= showError('last_name') ?>
                     </div>
                 </div>

                 <!-- Gender -->
                 <div class="mb-5">
                     <label class="block mb-2 font-medium text-dark">Gender</label>
                     <div class="flex space-x-4 text-sm text-secondary">
                         <label class="flex items-center gap-2">
                             <input type="radio" name="gender" value="1"
                                 class="text-primary focus:ring-primary-light"
                                 <?= isset($_SESSION['formdata']) ? '' : 'checked' ?>
                                 <?= showFormData('gender') == 1 ? 'checked' : '' ?>>
                             <span>Male</span>
                         </label>
                         <label class="flex items-center gap-2">
                             <input type="radio" name="gender" value="2"
                                 class="text-primary focus:ring-primary-light"
                                 <?= showFormData('gender') == 2 ? 'checked' : '' ?>>
                             <span>Female</span>
                         </label>
                         <label class="flex items-center gap-2">
                             <input type="radio" name="gender" value="3"
                                 class="text-primary focus:ring-primary-light"
                                 <?= showFormData('gender') == 3 ? 'checked' : '' ?>>
                             <span>Other</span>
                         </label>
                     </div>
                 </div>

                 <!-- Email -->
                 <div class="mb-5">
                     <label for="email" class="block mb-2 font-medium text-dark">Email Address</label>
                     <input type="email" id="email" name="email" value="<?= showFormData('email') ?>"
                         class="w-full px-4 py-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-light focus:border-transparent"
                         placeholder="Enter your email" required>
                     <?= showError('email') ?>
                 </div>

                 <!-- Username -->
                 <div class="mb-5">
                     <label for="username" class="block mb-2 font-medium text-dark">Username</label>
                     <input type="text" id="username" name="username" value="<?= showFormData('username') ?>"
                         class="w-full px-4 py-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-light focus:border-transparent"
                         placeholder="Choose a username" required>
                     <?= showError('username') ?>
                 </div>

                 <!-- Password -->
                 <div class="mb-5">
                     <label for="password" class="block mb-2 font-medium text-dark">Password</label>
                     <div class="relative">
                         <input type="password" id="password" name="password" value="<?= showFormData('password') ?>"
                             class="w-full px-4 py-3 pr-12 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-light focus:border-transparent"
                             placeholder="Create a password" required>
                         <i class="fas fa-eye absolute right-4 top-1/2 transform -translate-y-1/2 text-secondary cursor-pointer"
                             onclick="togglePassword('password')"></i>
                     </div>
                     <p class="text-sm text-secondary mt-2">Use 8 or more characters with a mix of letters, numbers & symbols</p>
                     <?= showError('password') ?>
                 </div>

                 <!-- Health Interests (Optional) -->
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

                 <!-- Terms -->
                 <div class="flex items-center mb-6">
                     <input type="checkbox" id="signup-terms" class="mr-2" required>
                     <label for="signup-terms" class="text-secondary">I agree to the <a href="#" class="text-primary">Terms of Service</a> and <a href="#" class="text-primary">Privacy Policy</a></label>
                 </div>

                 <div class="mt-8">
                     <button type="submit" class="w-full py-3 bg-primary text-white rounded-md font-medium hover:bg-primary-dark transition-colors">Create Account</button>
                 </div>
             </form>



             <div class="text-center text-secondary text-sm">
                 Already have an account? <a href="?login" class="text-primary font-medium">Log In</a>
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
         const alertDiv = document.getElementById('signup-alert');
         alertDiv.innerHTML = `<div class="p-3 rounded-md mb-4 text-sm ${type === 'danger' ? 'bg-red-100 text-red-700 border border-red-200' : 'bg-green-100 text-green-700 border border-green-200'}">${message}</div>`;

         // Auto hide after 5 seconds
         setTimeout(() => {
             alertDiv.innerHTML = '';
         }, 5000);
     }

     // Handle signup form submission
     document.getElementById('signup').addEventListener('submit', function(e) {
         // Let the form submit normally to the PHP script
         // We're not preventing default behavior here
     });
 </script>