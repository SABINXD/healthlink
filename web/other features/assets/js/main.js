document.addEventListener("DOMContentLoaded", () => {
  const form = document.getElementById("commentForm");
  const responseBox = document.getElementById("responseBox");
  const commentImage = document.getElementById("commentImage");
  const imagePreview = document.getElementById("imagePreview");
  const removeImageBtn = document.getElementById("removeImage");
  const previewImg = imagePreview.querySelector("img");
  
  // Image preview functionality
  commentImage.addEventListener("change", function() {
    const file = this.files[0];
    if (file) {
      const reader = new FileReader();
      reader.onload = function(e) {
        previewImg.src = e.target.result;
        imagePreview.classList.remove("hidden");
      }
      reader.readAsDataURL(file);
    }
  });
  
  removeImageBtn.addEventListener("click", function() {
    commentImage.value = "";
    imagePreview.classList.add("hidden");
  });
  
  form.addEventListener("submit", async e => {
    e.preventDefault();
    
    // Show loading state
    responseBox.className = "mt-4 p-4 rounded-lg bg-blue-50 text-blue-700 border border-blue-200";
    responseBox.textContent = "Processing your comment...";
    responseBox.classList.remove("hidden");
    
    const commentText = document.getElementById("commentText").value;
    const imageFile = commentImage.files[0];
    
    const formData = new FormData();
    formData.append('user_id', 170);
    formData.append('post_id', 161);
    formData.append('comment_text', commentText);
    if (imageFile) {
        formData.append('image', imageFile);
    }
    
    try {
      const res = await fetch("assets/php/moderate_comment.php", {
        method: "POST",
        body: formData
      });
      
      if (!res.ok) {
        const errorData = await res.json();
        throw new Error(errorData.error || "Server error");
      }
      
      const data = await res.json();
      
      if (data.error) {
        throw new Error(data.error);
      }
      
      // Success response
      let responseContent = `
        <div class="flex items-start">
          <i class="fas fa-check-circle mt-1 mr-3"></i>
          <div>
            <p class="font-medium">${data.message}</p>
            <p class="text-sm mt-1">AI Classification: <span class="font-semibold">${data.ai_label}</span></p>
          </div>
        </div>
      `;
      
      // If there's a blurred image, display it
      if (data.blurred_image_path) {
        responseContent += `
          <div class="mt-4">
            <p class="font-medium">Your image has been moderated:</p>
            <img src="uploads/${data.blurred_image_path}" alt="Moderated image" class="mt-2 max-w-xs rounded-lg border">
          </div>
        `;
      }
      
      responseBox.innerHTML = responseContent;
      responseBox.className = "mt-4 p-4 rounded-lg bg-green-50 text-green-700 border border-green-200";
      
      // Reset form
      form.reset();
      imagePreview.classList.add("hidden");
    } catch (err) {
      responseBox.innerHTML = `
        <div class="flex items-start">
          <i class="fas fa-exclamation-circle mt-1 mr-3"></i>
          <div>
            <p class="font-medium">Error</p>
            <p>${err.message}</p>
          </div>
        </div>
      `;
      responseBox.className = "mt-4 p-4 rounded-lg bg-red-50 text-red-700 border border-red-200";
      console.error("Submission error:", err);
    }
  });
});