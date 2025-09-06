document.addEventListener("DOMContentLoaded", () => {
  document.getElementById("commentForm").addEventListener("submit", async function(e) {
    e.preventDefault();

    const commentText = document.getElementById("commentText").value;
    const responseBox = document.getElementById("responseBox");

    responseBox.innerHTML = "⏳ Checking...";

    try {
      const res = await fetch("assets/php/moderate_comment.php", {
        method: "POST",
        headers: {"Content-Type": "application/json"},
        body: JSON.stringify({
          user_id: 170,  // Replace with logged-in user
          post_id: 161,  // Replace with current post
          comment_text: commentText
        })
      });

      const data = await res.json();
      responseBox.innerHTML = `${data.message} (AI: ${data.ai_label})`;
    } catch (err) {
      responseBox.innerHTML = "⚠️ Error: " + err.message;
    }
  });
});
