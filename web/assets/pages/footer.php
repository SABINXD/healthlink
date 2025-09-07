<?php if (isset($_SESSION['Auth'])) { ?>
  <!-- Modal -->
  <div class="modal fade" id="codeOptionModal" tabindex="-1" aria-labelledby="addPostLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content rounded-2xl shadow-xl overflow-hidden">
        <!-- Header -->
        <div class="modal-header bg-gradient-to-r from-green-600 via-teal-600 to-emerald-700 px-6 py-4">
          <h5 class="modal-title font-bold text-white" id="addPostLabel">Share Health Update</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <!-- Body -->
        <div class="modal-body px-6 py-6 bg-white">
          <!-- Image Preview -->
          <img src="" id="post_img_nocode" style="display: none;"
            class="w-full h-64 object-cover rounded-xl border-2 border-green-200 mb-6 shadow-md">
          <!-- Form -->
          <form method="post" action="assets/php/actions.php?addnocodepost" enctype="multipart/form-data" class="space-y-6" id="postFormInModal">
            <!-- Title -->
            <div class="mb-4">
              <label class="block text-sm font-medium text-gray-700 mb-2">Question Title</label>
              <input type="text" name="post_title" class="w-full py-3 px-4 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent" placeholder="What's your health question or concern?" required>
            </div>
            <!-- Category -->
            <div class="mb-4">
              <label class="block text-sm font-medium text-gray-700 mb-2">Category</label>
              <select name="post_category" class="w-full py-3 px-4 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent" required>
                <option value="">Select a category</option>
                <option value="mental">Mental</option>
                <option value="nutrition">Sexual</option>
                <option value="fitness">Fitness</option>
                <option value="chronic">skin dieases</option>
                <option value="parenting">Parenting</option>
                <option value="aging">Aging</option>
                <option value="women">Women's Health</option>
                <option value="men">Men's Health</option>
              </select>
            </div>
            <!-- Details -->
            <div class="mb-4">
              <label for="post_desc" class="block text-sm font-medium text-gray-700 mb-2">Details</label>
              <textarea name="post_desc" id="post_desc" rows="3"
                class="w-full rounded-xl border border-gray-300 shadow-sm focus:border-green-500 focus:ring focus:ring-green-200 resize-none text-sm p-4 transition-all"
                placeholder="Provide more details about your question or concern..."></textarea>
            </div>
            <!-- File Input -->
            <div class="space-y-2">
              <label class="block text-sm font-medium text-gray-700">Upload Image (Optional)</label>
              <div class="flex items-center justify-center w-full">
                <label for="select_post_img_nocode"
                  class="flex flex-col items-center justify-center w-full h-32 border-2 border-dashed border-green-400 rounded-lg cursor-pointer bg-green-50 hover:bg-green-100 transition-colors">
                  <div class="flex flex-col items-center justify-center pt-5 pb-6">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 mb-2 text-green-500" fill="none"
                      viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    <p class="text-sm text-green-600"><span class="font-semibold">Click to upload</span> or drag and drop</p>
                    <p class="text-xs text-green-500">PNG, JPG, GIF up to 2MB</p>
                  </div>
                  <input name="post_img" type="file" id="select_post_img_nocode" class="hidden" />
                </label>
              </div>
            </div>
            <!-- Toggles Section -->
            <div class="p-4 rounded-xl bg-gradient-to-r from-green-500 to-emerald-600 text-white space-y-4">
              <!-- Spoiler Toggle -->
              <div class="flex items-center justify-between">
                <div>
                  <p class="font-semibold">Sensitive Content</p>
                  <p class="text-xs text-white/80">Blur and warn viewers if your post has medical images or sensitive content.</p>
                </div>
                <label class="relative inline-flex items-center cursor-pointer">
                  <input type="checkbox" id="spoiler_toggle" class="sr-only peer">
                  <div class="w-12 h-6 bg-gray-300 rounded-full peer peer-checked:bg-red-500 transition"></div>
                  <div class="absolute left-1 top-1 w-4 h-4 bg-white rounded-full peer-checked:translate-x-6 transition"></div>
                </label>
              </div>
              <!-- Anonymous Toggle -->
              <div class="flex items-center justify-between">
                <div>
                  <p class="font-semibold">Post Anonymously</p>
                  <p class="text-xs text-white/80">Hide your identity and share without your profile name.</p>
                </div>
                <label class="relative inline-flex items-center cursor-pointer">
                  <input type="checkbox" id="anonymous_toggle" class="sr-only peer">
                  <div class="w-12 h-6 bg-gray-300 rounded-full peer peer-checked:bg-green-600 transition"></div>
                  <div class="absolute left-1 top-1 w-4 h-4 bg-white rounded-full peer-checked:translate-x-6 transition"></div>
                </label>
              </div>
            </div>
            <!-- Hidden inputs for backend -->
            <input type="hidden" name="spoiler" id="spoiler_input" value="0">
            <input type="hidden" name="post_privacy" id="privacy_input" value="0">
            <!-- Submit Button -->
            <div class="flex justify-end">
              <button type="submit"
                class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-green-600 via-teal-600 to-emerald-700 text-white text-sm font-medium rounded-xl shadow-md hover:opacity-90 focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-all transform hover:scale-105">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                  <path
                    d="M10.894 2.553a1 1 0 00-1.788 0l-7 14a1 1 0 001.169 1.409l5-1.429A1 1 0 009 15.571V11a1 1 0 112 0v4.571a1 1 0 00.725.962l5 1.428a1 1 0 001.17-1.408l-7-14z" />
                </svg>
                Share
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>

  <!-- Notifications Sidebar -->
  <div class="offcanvas offcanvas-start" tabindex="-1" id="notification_sidebar" aria-labelledby="notificationSidebarLabel">
    <div class="offcanvas-header bg-gradient-to-r from-green-500 to-teal-600 text-white">
      <h5 class="offcanvas-title font-bold" id="notificationSidebarLabel">Health Notifications</h5>
      <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body bg-green-50 p-0">
      <div class="p-4 border-b border-green-200 bg-white">
        <h6 class="font-semibold text-gray-700">Recent Notifications</h6>
      </div>
      <div class="divide-y divide-green-200">
        <?php
        $notifications = getNotifications();
        foreach ($notifications as $not) {
          $time = $not['created_at'];
          $fuser = getUser($not['from_user_id']);
          $post = '';
          if ($not['post_id']) {
            $post = 'data-bs-toggle="modal" data-bs-target="#postview' . $not['post_id'] . '"';
          }
          $fbtn = '';
        ?>
          <div class="p-4 hover:bg-green-100 transition-colors cursor-pointer">
            <div class="flex items-start">
              <img src="assets/img/profile/<?= $fuser['profile_pic'] ?>" alt="" class="w-12 h-12 rounded-full border-2 border-white shadow-sm">
              <div class="ml-3 flex-1">
                <div class="flex items-center justify-between">
                  <a href='?u=<?= $fuser['username'] ?>' class="text-decoration-none text-dark">
                    <h6 class="font-semibold text-gray-900"><?= $fuser['first_name'] ?> <?= $fuser['last_name'] ?></h6>
                  </a>
                  <span class="text-xs text-gray-500"><?= show_time($time) ?></span>
                </div>
                <p class="text-sm text-gray-600 mt-1">@<?= $fuser['username'] ?> <?= $not['message'] ?></p>
              </div>
              <div class="ml-2 flex items-center">
                <?php
                if ($not['read_status'] == 0) {
                ?>
                  <div class="w-3 h-3 bg-green-500 rounded-full"></div>
                <?php
                } else if ($not['read_status'] == 2) {
                ?>
                  <span class="badge bg-danger">Post Deleted</span>
                <?php
                }
                ?>
              </div>
            </div>
          </div>
        <?php
        }
        ?>
      </div>
    </div>
  </div>
  <!-- Messages Sidebar -->
  <div class="offcanvas offcanvas-start" tabindex="-1" id="messages_sidebar" aria-labelledby="messagesSidebarLabel">
    <div class="offcanvas-header bg-gradient-to-r from-green-500 to-teal-600 text-white">
      <h5 class="offcanvas-title font-bold" id="messagesSidebarLabel">Health Messages</h5>
      <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body bg-green-50 p-0" id="chatlist">
      <!-- Chat list will be populated here -->
    </div>
  </div>
  <!-- Chat Box Modal -->
  <div class="modal fade" id="chatbox" tabindex="-1" aria-labelledby="chatboxLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
      <div class="modal-content rounded-2xl shadow-xl overflow-hidden">
        <div class="modal-header bg-gradient-to-r from-green-500 to-teal-600 text-white p-4">
          <a href="" id="cplink" class="text-decoration-none text-white flex items-center">
            <img src="assets/img/profile/default_profile.jpg" id="chatter_pic" class="w-10 h-10 rounded-full border-2 border-white mr-3">
            <div>
              <h5 class="modal-title font-bold" id="chatboxLabel">
                <span id="chatter_name"></span>
                <span class="text-sm font-normal opacity-75">(@<span id="chatter_username">loading..</span>)</span>
              </h5>
            </div>
          </a>
          <button type="button" class="btn-close btn-close-white ms-auto" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body p-4 bg-green-50" id="user_chat">
          <!-- Chat messages will be loaded here -->
        </div>
        <div class="p-3 bg-danger text-white text-center" id="blerror" style="display:none">
          <i class="bi bi-x-octagon-fill me-2"></i> You are not allowed to send messages to this user anymore
        </div>
        <div class="modal-footer bg-white p-3">
          <div class="input-group">
            <input type="text" class="form-control rounded-pill border-0 bg-green-100 focus:bg-white focus:shadow-sm" id="msginput" placeholder="Type your message..." aria-label="Message">
            <button class="btn btn-primary rounded-pill ms-2 bg-green-600 hover:bg-green-700" id="sendmsg" data-user-id="0" type="button">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                <path d="M10.894 2.553a1 1 0 00-1.788 0l-7 14a1 1 0 001.169 1.409l5-1.429A1 1 0 009 15.571V11a1 1 0 112 0v4.571a1 1 0 00.725.962l5 1.428a1 1 0 001.17-1.408l-7-14z" />
              </svg>
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
<?php } ?>
<!-- modal for refresh number  -->
<div class="modal fade" id="refreshNumber" tabindex="-1" aria-labelledby="refreshNumberLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content rounded-2xl shadow-xl overflow-hidden">
      <!-- Modal Header -->
      <div class="modal-header bg-gradient-to-r from-green-600 to-emerald-700 px-6 py-4">
        <h5 class="modal-title font-bold text-white" id="refreshNumberLabel">Refresh Hospital Numbers</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <!-- Modal Body -->
      <div class="modal-body px-6 py-6 bg-white">
        <form id="districtForm" method="post" action="assets/php/actions.php?refreshNumber" class="space-y-6">
          <!-- Input Field -->
          <div class="mb-4">
            <label for="district" class="block text-sm font-medium text-gray-700 mb-2">Enter Your Current District</label>
            <input type="text" id="district" name="district" placeholder="e.g., Chitwan"
              class="w-full px-4 py-2 border border-green-300 rounded-xl shadow-sm focus:ring-2 focus:ring-green-500 focus:border-green-500 outline-none">
          </div>
          <!-- Submit Button -->
          <div class="flex justify-end">
            <button type="submit"
              class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-green-600 to-emerald-700 text-white text-sm font-medium rounded-xl shadow-md hover:from-green-700 hover:to-emerald-800 focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-all transform hover:scale-105">
              Refresh Numbers
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
<script>
  document.addEventListener("DOMContentLoaded", function() {
    const codeInput = document.getElementById("code_input");
    const codeDisplay = document.getElementById("code_display");
    const langSelect = document.getElementById("language");
    const hiddenCode = document.getElementById("code_text");

    function syncCodeInput() {
      hiddenCode.value = codeInput.value;
    }

    function updateHighlight() {
      const selectedLang = langSelect.value || "javascript";
      codeDisplay.className = `language-${selectedLang} line-numbers`;
      codeDisplay.textContent = codeInput.value;
      if (typeof Prism !== 'undefined') {
        Prism.highlightElement(codeDisplay);
      }
    }
    if (codeInput && codeDisplay && langSelect) {
      codeInput.addEventListener("input", updateHighlight);
      langSelect.addEventListener("change", updateHighlight);
      // Initial highlight
      updateHighlight();
    }
    // Tag functionality
    const tagInput = document.getElementById("tag-input");
    const tagContainer = document.getElementById("tag-container");
    const hiddenTags = document.getElementById("post_tags");
    let tags = [];

    function renderTags() {
      tagContainer.querySelectorAll(".tag-item").forEach(el => el.remove());
      tags.forEach((tag, index) => {
        const tagEl = document.createElement("span");
        tagEl.className = "tag-item bg-green-100 text-green-800 text-xs font-medium px-3 py-1 rounded-full flex items-center";
        tagEl.innerHTML = `${tag}<button type="button" class="ml-2 text-green-500 hover:text-green-800" onclick="removeTag(${index})">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
          </svg>
        </button>`;
        tagContainer.insertBefore(tagEl, tagInput);
      });
      hiddenTags.value = tags.join(",");
    }

    function removeTag(index) {
      tags.splice(index, 1);
      renderTags();
    }
    if (tagInput) {
      tagInput.addEventListener("keydown", function(e) {
        if (e.key === "Enter" && this.value.trim() !== "") {
          e.preventDefault();
          const value = this.value.trim();
          if (!tags.includes(value)) {
            tags.push(value);
            renderTags();
          }
          this.value = "";
        }
      });
    }
    // Post image preview 
    ["code", "nocode"].forEach(type => {
      const input = document.getElementById(`select_post_img_${type}`);
      const preview = document.getElementById(`post_img_${type}`);
      if (input && preview) {
        input.addEventListener("change", function() {
          const file = this.files[0];
          if (file) {
            const reader = new FileReader();
            reader.readAsDataURL(file);
            reader.onload = function() {
              preview.src = reader.result;
              preview.style.display = "block";
            };
          }
        });
      }
    });
    // Expose to global scope if needed
    window.syncCodeInput = syncCodeInput;
    window.removeTag = removeTag;
  });
</script>
<!-- Bootstrap JS Bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
  integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
  crossorigin="anonymous"></script>
<script src="./assets/js/jquery.js"></script>
<script src="./assets/js/timeago_jquery.js"></script>
<!-- Prism.js for code highlighting -->
<link href="https://cdn.jsdelivr.net/npm/prismjs@1.29.0/themes/prism-tomorrow.min.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/prismjs@1.29.0/plugins/line-numbers/prism-line-numbers.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/prismjs@1.29.0/components/prism-core.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/prismjs@1.29.0/plugins/line-numbers/prism-line-numbers.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/prismjs@1.29.0/plugins/autoloader/prism-autoloader.min.js"></script>
<script>
  Prism.plugins.autoloader.languages_path = 'https://cdn.jsdelivr.net/npm/prismjs@1.29.0/components/';
</script>
<script src="./assets/js/index.js?v=<?= time() ?>"></script>
</body>
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


</html>