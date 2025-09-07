<?php
session_start();
$old = $_SESSION['old'] ?? [];
$error = $_SESSION['error'] ?? [];
$alreadySubmitted = false;
if (isset($error['field']) && $error['field'] == 'alreadySubmitted') {
  $alreadySubmitted = true;
}
?>
<div class="min-h-screen bg-gradient-to-br from-green-50 to-emerald-100 pt-16">
  <div class="container mx-auto px-4 py-8 w-full max-w-4xl">
    <!-- Header -->
    <header class="mb-10 animate-fade-in">
      <div class="flex items-center justify-between">
        <div>
          <h1 class="text-3xl md:text-4xl font-bold text-gray-800">Doctor Verification</h1>
          <p class="text-gray-600 mt-2">Complete your professional profile for verification</p>
        </div>
        <div class="bg-gradient-to-r from-green-500 to-teal-600 p-4 rounded-full shadow-lg transform transition-all duration-300 hover:scale-110">
          <i class="fas fa-user-md text-white text-2xl"></i>
        </div>
      </div>
    </header>
    
    <!-- Show global error if already submitted -->
    <?php if ($alreadySubmitted): ?>
      <div class="mb-6 p-4 bg-red-100 text-red-700 rounded-lg shadow-md animate-fade-in-up">
        <div class="flex items-center">
          <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
          </svg>
          You have already submitted a doctor verification request. Please wait for approval.
        </div>
      </div>
    <?php endif; ?>
    
    <!-- Main Form -->
    <form
      id="doctorVerificationForm"
      enctype="multipart/form-data"
      action="assets/php/actions.php?verifydoctor"
      class="bg-white rounded-xl shadow-lg p-6 md:p-8 mb-8 w-full animate-fade-in-up animation-delay-200 <?= $alreadySubmitted ? 'opacity-50 pointer-events-none' : '' ?>"
      method="post">
      
      <h2 class="text-xl font-semibold text-gray-800 mb-6 pb-2 border-b border-green-200 flex items-center">
        <svg class="w-5 h-5 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
        </svg>
        Professional Information
      </h2>
      
      <!-- Medical Specialty and Years of Experience -->
      <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
        <div class="animate-fade-in-up animation-delay-300">
          <label for="specialty" class="block text-gray-700 font-medium mb-2">Medical Specialty</label>
          <div class="relative">
            <select id="specialty" name="specialty" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all duration-300 appearance-none bg-white">
              <option value="">Select your specialty</option>
              <?php
              $specialties = ['cardiology', 'dermatology', 'endocrinology', 'gastroenterology', 'neurology', 'oncology', 'pediatrics', 'psychiatry', 'radiology', 'surgery', 'other'];
              foreach ($specialties as $s):
                $selected = (isset($old['specialty']) && $old['specialty'] == $s) ? 'selected' : '';
              ?>
                <option value="<?= $s ?>" <?= $selected ?>><?= ucfirst($s) ?></option>
              <?php endforeach; ?>
            </select>
            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700">
              <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                <path d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z"/>
              </svg>
            </div>
          </div>
          <?php if (isset($error['field']) && $error['field'] == 'specialty'): ?>
            <p class="text-red-500 text-sm mt-1 flex items-center animate-fade-in">
              <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
              </svg>
              <?= $error['msg'] ?>
            </p>
          <?php endif; ?>
        </div>
        
        <div class="animate-fade-in-up animation-delay-400">
          <label for="experience" class="block text-gray-700 font-medium mb-2">Years of Experience</label>
          <input type="text" id="experience" name="experience" value="<?= htmlspecialchars($old['experience'] ?? '') ?>" min="0" max="50"
            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all duration-300" placeholder="Enter years of experience" />
          <?php if (isset($error['field']) && $error['field'] == 'experience'): ?>
            <p class="text-red-500 text-sm mt-1 flex items-center animate-fade-in">
              <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
              </svg>
              <?= $error['msg'] ?>
            </p>
          <?php endif; ?>
        </div>
      </div>
      
      <!-- Medical License Number -->
      <div class="mb-6 animate-fade-in-up animation-delay-500">
        <label for="license" class="block text-gray-700 font-medium mb-2">Medical License Number</label>
        <input type="text" id="license" name="license" value="<?= htmlspecialchars($old['license'] ?? '') ?>"
          class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all duration-300"
          placeholder="Enter your medical license number" />
        <?php if (isset($error['field']) && $error['field'] == 'license'): ?>
          <p class="text-red-500 text-sm mt-1 flex items-center animate-fade-in">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <?= $error['msg'] ?>
            </p>
          <?php endif; ?>
      </div>
      
      <!-- Contact Information -->
      <h3 class="text-lg font-semibold text-gray-800 mb-4 mt-8 flex items-center animate-fade-in-up animation-delay-600">
        <svg class="w-5 h-5 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
        </svg>
        Contact Information
      </h3>
      
      <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
        <div class="animate-fade-in-up animation-delay-700">
          <label for="phone" class="block text-gray-700 font-medium mb-2">Phone Number</label>
          <input type="text" id="phone" name="phone" value="<?= htmlspecialchars($old['phone'] ?? '') ?>"
            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all duration-300"
            placeholder="Enter your phone number" />
          <?php if (isset($error['field']) && $error['field'] == 'phone'): ?>
            <p class="text-red-500 text-sm mt-1 flex items-center animate-fade-in">
              <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
              </svg>
              <?= $error['msg'] ?>
            </p>
          <?php endif; ?>
        </div>
        
        <div class="animate-fade-in-up animation-delay-800">
          <label for="address" class="block text-gray-700 font-medium mb-2">Address</label>
          <textarea id="address" name="address" rows="2"
            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all duration-300"
            placeholder="Enter your address"><?= htmlspecialchars($old['address'] ?? '') ?></textarea>
          <?php if (isset($error['field']) && $error['field'] == 'address'): ?>
            <p class="text-red-500 text-sm mt-1 flex items-center animate-fade-in">
              <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
              </svg>
              <?= $error['msg'] ?>
            </p>
          <?php endif; ?>
        </div>
      </div>
      
      <!-- City and Country -->
      <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
        <div class="animate-fade-in-up animation-delay-900">
          <label for="city" class="block text-gray-700 font-medium mb-2">City</label>
          <input type="text" id="city" name="city" value="<?= htmlspecialchars($old['city'] ?? '') ?>"
            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all duration-300"
            placeholder="Enter your city" />
          <?php if (isset($error['field']) && $error['field'] == 'city'): ?>
            <p class="text-red-500 text-sm mt-1 flex items-center animate-fade-in">
              <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
              </svg>
              <?= $error['msg'] ?>
            </p>
          <?php endif; ?>
        </div>
        
        <div class="animate-fade-in-up animation-delay-1000">
          <label for="country" class="block text-gray-700 font-medium mb-2">Country</label>
          <input type="text" id="country" name="country" value="<?= htmlspecialchars($old['country'] ?? '') ?>"
            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all duration-300"
            placeholder="Enter your country" />
          <?php if (isset($error['field']) && $error['field'] == 'country'): ?>
            <p class="text-red-500 text-sm mt-1 flex items-center animate-fade-in">
              <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
              </svg>
              <?= $error['msg'] ?>
            </p>
          <?php endif; ?>
        </div>
      </div>
      
      <!-- Document Upload Section -->
      <h3 class="text-lg font-semibold text-gray-800 mb-4 mt-8 flex items-center animate-fade-in-up animation-delay-1100">
        <svg class="w-5 h-5 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
        </svg>
        Document Verification
      </h3>
      
      <p class="text-gray-600 mb-6 animate-fade-in-up animation-delay-1200">Please upload the following documents. All documents must be clear and legible.</p>
      
      <?php
      $documents = [
        'citizenshipFront' => 'Citizenship Document (Front)',
        'citizenshipBack' => 'Citizenship Document (Back)',
        'medicalCertificate' => 'Medical Certificate'
      ];
      
      foreach ($documents as $id => $label):
        $delay = 1200 + (array_search($id, array_keys($documents)) * 100);
      ?>
        <div class="mb-6 animate-fade-in-up animation-delay-<?= $delay ?>">
          <label class="block text-gray-700 font-medium mb-2"><?= $label ?></label>
          <div class="flex items-center justify-center w-full">
            <label for="<?= $id ?>" class="flex flex-col items-center justify-center w-full h-64 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50 hover:bg-gray-100 transition-all duration-300 hover:border-green-400 group">
              <div id="<?= $id ?>Preview" class="preview-container flex flex-col items-center justify-center pt-5 pb-6 w-full h-full <?= isset($old[$id]) ? '' : 'hidden' ?>">
                <img id="<?= $id ?>Img" class="max-h-52 max-w-full object-contain rounded-lg shadow-md" src="<?= $old[$id] ?? '' ?>" alt="<?= $label ?> Preview" />
                <p class="text-sm text-gray-500 mt-2">Click to change</p>
              </div>
              <div id="<?= $id ?>Placeholder" class="flex flex-col items-center justify-center pt-5 pb-6 <?= isset($old[$id]) ? 'hidden' : '' ?>">
                <div class="bg-green-100 p-3 rounded-full mb-3 group-hover:bg-green-200 transition-colors duration-300">
                  <i class="fas fa-cloud-upload-alt text-green-600 text-2xl"></i>
                </div>
                <p class="mb-2 text-sm text-gray-500"><span class="font-semibold">Click to upload</span> or drag and drop</p>
                <p class="text-xs text-gray-500">PNG, JPG or PDF (MAX. 5MB)</p>
              </div>
              <input type="file" id="<?= $id ?>" name="<?= $id ?>" class="hidden" accept="image/*,.pdf" />
            </label>
            <?php if (isset($error['field']) && $error['field'] == $id): ?>
              <p class="text-red-500 text-sm mt-1 flex items-center animate-fade-in">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <?= $error['msg'] ?>
              </p>
            <?php endif; ?>
          </div>
        </div>
      <?php endforeach; ?>
      
      <!-- Terms -->
      <div class="mb-8 animate-fade-in-up animation-delay-1500">
        <div class="flex items-start">
          <div class="flex items-center h-5 mt-1">
            <input id="terms" name="terms" type="checkbox" class="w-4 h-4 border border-gray-300 rounded bg-gray-50 focus:ring-3 focus:ring-green-300 text-green-600" required />
          </div>
          <label for="terms" class="ml-2 text-sm font-medium text-gray-700">
            I agree to the <a href="#" class="text-green-600 hover:text-green-800 transition-colors duration-200">Terms</a> and <a href="#" class="text-green-600 hover:text-green-800 transition-colors duration-200">Privacy Policy</a>.
          </label>
        </div>
        <?php if (isset($error['field']) && $error['field'] == 'terms'): ?>
          <p class="text-red-500 text-sm mt-1 flex items-center animate-fade-in">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <?= $error['msg'] ?>
          </p>
        <?php endif; ?>
      </div>
      
      <!-- Submit -->
      <div class="flex justify-end animate-fade-in-up animation-delay-1600">
        <button type="submit" class="px-8 py-3 bg-gradient-to-r from-green-600 to-teal-600 text-white font-medium rounded-lg hover:from-green-700 hover:to-teal-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition-all duration-300 transform hover:-translate-y-0.5 shadow-lg hover:shadow-xl flex items-center">
          <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
          </svg>
          Submit Verification
        </button>
      </div>
    </form>
  </div>
</div>
<style>
  /* Custom animations */
  @keyframes fade-in {
    from { opacity: 0; }
    to { opacity: 1; }
  }
  
  @keyframes fade-in-up {
    from { 
      opacity: 0;
      transform: translateY(20px);
    }
    to { 
      opacity: 1;
      transform: translateY(0);
    }
  }
  
  .animate-fade-in {
    animation: fade-in 0.6s ease-out forwards;
  }
  
  .animate-fade-in-up {
    animation: fade-in-up 0.6s ease-out forwards;
  }
  
  .animation-delay-200 {
    animation-delay: 0.2s;
  }
  
  .animation-delay-300 {
    animation-delay: 0.3s;
  }
  
  .animation-delay-400 {
    animation-delay: 0.4s;
  }
  
  .animation-delay-500 {
    animation-delay: 0.5s;
  }
  
  .animation-delay-600 {
    animation-delay: 0.6s;
  }
  
  .animation-delay-700 {
    animation-delay: 0.7s;
  }
  
  .animation-delay-800 {
    animation-delay: 0.8s;
  }
  
  .animation-delay-900 {
    animation-delay: 0.9s;
  }
  
  .animation-delay-1000 {
    animation-delay: 1.0s;
  }
  
  .animation-delay-1100 {
    animation-delay: 1.1s;
  }
  
  .animation-delay-1200 {
    animation-delay: 1.2s;
  }
  
  .animation-delay-1300 {
    animation-delay: 1.3s;
  }
  
  .animation-delay-1400 {
    animation-delay: 1.4s;
  }
  
  .animation-delay-1500 {
    animation-delay: 1.5s;
  }
  
  .animation-delay-1600 {
    animation-delay: 1.6s;
  }
</style>
<script>
  document.addEventListener('DOMContentLoaded', () => {
    // Image preview functionality
    const files = ["citizenshipFront", "citizenshipBack", "medicalCertificate"];
    files.forEach(file => {
      document.getElementById(file).addEventListener("change", function(e) {
        const preview = document.getElementById(file + "Preview");
        const placeholder = document.getElementById(file + "Placeholder");
        const img = document.getElementById(file + "Img");
        
        if (e.target.files && e.target.files[0]) {
          const reader = new FileReader();
          reader.onload = function(ev) {
            img.src = ev.target.result;
            preview.classList.remove("hidden");
            placeholder.classList.add("hidden");
            
            // Animate the preview
            gsap.fromTo(preview, {
              opacity: 0,
              scale: 0.9
            }, {
              opacity: 1,
              scale: 1,
              duration: 0.3,
              ease: "back.out(1.7)"
            });
          }
          reader.readAsDataURL(e.target.files[0]);
        }
      });
    });
    
    // Basic form validation
    document.getElementById("doctorVerificationForm").addEventListener("submit", function(e) {
      let valid = true;
      const requiredFields = ["specialty", "experience", "license", "phone", "address", "city", "country"];
      
      requiredFields.forEach(f => {
        const el = document.getElementById(f);
        if (!el.value.trim()) {
          el.classList.add("border-red-500");
          
          // Shake animation for invalid fields
          gsap.fromTo(el, {
            x: -5
          }, {
            x: 5,
            duration: 0.1,
            repeat: 5,
            yoyo: true,
            ease: "power2.inOut",
            onComplete: () => {
              gsap.set(el, { x: 0 });
            }
          });
          
          valid = false;
        } else {
          el.classList.remove("border-red-500");
        }
      });
      
      if (!valid) {
        e.preventDefault();
        
        // Show error notification
        const notification = document.createElement('div');
        notification.className = 'fixed bottom-4 right-4 px-6 py-3 bg-red-500 text-white rounded-lg shadow-lg z-50';
        notification.textContent = 'Please fill in all required fields';
        document.body.appendChild(notification);
        
        // Animate in
        gsap.fromTo(notification, {
          opacity: 0,
          y: 20
        }, {
          opacity: 1,
          y: 0,
          duration: 0.3,
          ease: "power2.out"
        });
        
        // Remove after 3 seconds
        setTimeout(() => {
          gsap.to(notification, {
            opacity: 0,
            y: 20,
            duration: 0.3,
            ease: "power2.in",
            onComplete: () => {
              notification.remove();
            }
          });
        }, 3000);
      }
    });
    
    // Add hover effect to form inputs
    const inputs = document.querySelectorAll('input, textarea, select');
    inputs.forEach(input => {
      input.addEventListener('focus', function() {
        gsap.to(this, {
          scale: 1.02,
          duration: 0.2,
          ease: "power2.out"
        });
      });
      
      input.addEventListener('blur', function() {
        gsap.to(this, {
          scale: 1,
          duration: 0.2,
          ease: "power2.out"
        });
      });
    });
  });
</script>
<?php
unset($_SESSION['old']);
unset($_SESSION['error']);
?>