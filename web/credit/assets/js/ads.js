document.addEventListener("DOMContentLoaded", () => {
    const watchAdBtns = document.querySelectorAll(".watch-ad-btn");
    const adModal = document.getElementById("adModal");
    const skipAdBtn = document.getElementById("skipAdBtn");
    const adTimer = document.getElementById("adTimer");
    const adProgress = document.getElementById("adProgress");
    const successMessage = document.getElementById("successMessage");
    const userCredits = document.getElementById("userCredits");
    
    let countdown = 30;
    let timerInterval;
    let progressInterval;
    
    // Fetch current user credits
    fetchUserCredits();
    
    // Add event listeners to all watch ad buttons
    watchAdBtns.forEach(btn => {
        btn.addEventListener("click", () => {
            adModal.classList.remove("hidden");
            startAdTimer();
        });
    });
    
    skipAdBtn.addEventListener("click", () => {
        if (countdown <= 0) {
            completeAd();
        }
    });
    
    function startAdTimer() {
        countdown = 30;
        adTimer.textContent = countdown;
        skipAdBtn.disabled = true;
        skipAdBtn.classList.add("cursor-not-allowed", "bg-gray-300", "text-gray-700");
        skipAdBtn.classList.remove("bg-green-500", "text-white", "hover:bg-green-600");
        
        // Start countdown timer
        timerInterval = setInterval(() => {
            countdown--;
            adTimer.textContent = countdown;
            
            if (countdown <= 0) {
                clearInterval(timerInterval);
                enableSkipButton();
            }
        }, 1000);
        
        // Start progress bar
        let progress = 0;
        progressInterval = setInterval(() => {
            progress += 100/30; // 100% over 30 seconds
            adProgress.style.width = `${Math.min(progress, 100)}%`;
            
            if (progress >= 100) {
                clearInterval(progressInterval);
            }
        }, 1000);
    }
    
    function enableSkipButton() {
        skipAdBtn.disabled = false;
        skipAdBtn.classList.remove("cursor-not-allowed", "bg-gray-300", "text-gray-700");
        skipAdBtn.classList.add("bg-green-500", "text-white", "hover:bg-green-600");
    }
    
    function completeAd() {
        clearInterval(timerInterval);
        clearInterval(progressInterval);
        adModal.classList.add("hidden");
        
        // Send request to update credits
        fetch("assets/php/update_credits.php", {
            method: "POST",
            headers: {
                "Content-Type": "application/json"
            },
            body: JSON.stringify({
                user_id: 170 // Replace with actual user ID
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                userCredits.textContent = data.credits;
                showSuccessMessage();
            } else {
                console.error("Error updating credits:", data.message);
            }
        })
        .catch(error => {
            console.error("Error updating credits:", error);
        });
    }
    
    function fetchUserCredits() {
        fetch("assets/php/get_credits.php", {
            method: "POST",
            headers: {
                "Content-Type": "application/json"
            },
            body: JSON.stringify({
                user_id: 170 // Replace with actual user ID
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                userCredits.textContent = data.credits;
            }
        })
        .catch(error => {
            console.error("Error fetching credits:", error);
        });
    }
    
    function showSuccessMessage() {
        successMessage.classList.remove("translate-y-20", "opacity-0");
        successMessage.classList.add("translate-y-0", "opacity-100");
        
        // Hide success message after 5 seconds
        setTimeout(() => {
            successMessage.classList.remove("translate-y-0", "opacity-100");
            successMessage.classList.add("translate-y-20", "opacity-0");
        }, 5000);
    }
});