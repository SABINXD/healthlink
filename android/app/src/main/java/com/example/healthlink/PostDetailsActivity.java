package com.example.healthlink;

import android.content.ClipData;
import android.content.ClipboardManager;
import android.content.Context;
import android.graphics.Color;
import android.os.Bundle;
import android.util.Log;
import android.view.View;
import android.widget.ImageButton;
import android.widget.ImageView;
import android.widget.ProgressBar;
import android.widget.TextView;
import android.widget.Toast;
import androidx.appcompat.app.AppCompatActivity;
import androidx.appcompat.widget.Toolbar;
import androidx.cardview.widget.CardView;
import androidx.recyclerview.widget.LinearLayoutManager;
import androidx.recyclerview.widget.RecyclerView;
import com.android.volley.Request;
import com.android.volley.toolbox.StringRequest;
import com.android.volley.toolbox.Volley;
import com.bumptech.glide.Glide;
import org.json.JSONArray;
import org.json.JSONObject;
import java.util.ArrayList;
import java.util.HashMap;
import java.util.List;
import java.util.Map;

public class PostDetailsActivity extends AppCompatActivity implements RealTimeManager.RealTimeListener {
    private static final String TAG = "PostDetailsActivity";
    private ImageView postImage;
    private TextView postTitle, postAuthorDate, postDescription;
    private RecyclerView commentsRecyclerView;
    private TextView noCommentsText;
    private List<Comment> commentsList = new ArrayList<>();
    private CommentsAdapter commentsAdapter;
    private String serverIp;
    private int postId;
    private RealTimeManager realTimeManager;
    // Code-related views
    private View codeCard;
    private TextView codeLanguage, codeContent;
    private RecyclerView tagsRecyclerView;
    private TagAdapter tagAdapter;
    private List<String> tagsList = new ArrayList<>();
    private ImageButton btnCopyCode;
    private String currentCodeContent;
    // AI Summary views
    private CardView aiSummaryCard;
    private TextView aiSummaryText;
    private ProgressBar aiSummaryProgress;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_post_details);

        // Setup toolbar
        Toolbar toolbar = findViewById(R.id.toolbar);
        setSupportActionBar(toolbar);
        if (getSupportActionBar() != null) {
            getSupportActionBar().setDisplayHomeAsUpEnabled(true);
            getSupportActionBar().setDisplayShowHomeEnabled(true);
        }

        // Initialize RealTimeManager
        realTimeManager = RealTimeManager.getInstance();

        initializeViews();
        setupData();
        setupComments();
        loadComments();

        // Log for debugging
        Log.d(TAG, "PostDetailsActivity created with postId: " + postId);
    }

    @Override
    protected void onResume() {
        super.onResume();
        // Register WebSocket listener
        realTimeManager.addListener(this);
        Log.d(TAG, "WebSocket listener registered");
        // Reload comments when returning to the activity
        loadComments();
    }

    @Override
    protected void onPause() {
        super.onPause();
        // Unregister WebSocket listener
        realTimeManager.removeListener(this);
        Log.d(TAG, "WebSocket listener unregistered");
    }

    private void initializeViews() {
        postImage = findViewById(R.id.post_image);
        postTitle = findViewById(R.id.post_title);
        postAuthorDate = findViewById(R.id.post_author_date);
        postDescription = findViewById(R.id.post_description);
        commentsRecyclerView = findViewById(R.id.comments_recycler_view);
        noCommentsText = findViewById(R.id.no_comments_text);

        // Initialize code-related views
        codeCard = findViewById(R.id.code_card);
        codeLanguage = findViewById(R.id.code_language);
        codeContent = findViewById(R.id.code_content);
        tagsRecyclerView = findViewById(R.id.tags_recycler_view);
        btnCopyCode = findViewById(R.id.btn_copy_code);

        // Initialize AI Summary views
        aiSummaryCard = findViewById(R.id.ai_summary_card);
        aiSummaryText = findViewById(R.id.ai_summary_text);
        aiSummaryProgress = findViewById(R.id.ai_summary_progress);

        serverIp = getString(R.string.server_ip);

        // Setup copy button click listener
        btnCopyCode.setOnClickListener(v -> copyCodeToClipboard());

        // Log for debugging
        Log.d(TAG, "Server IP: " + serverIp);
        Log.d(TAG, "Views initialized");
    }

    private void setupData() {
        String imageUrl = getIntent().getStringExtra("post_img");
        String title = getIntent().getStringExtra("post_text");
        String author = getIntent().getStringExtra("user_name");
        String createdAt = getIntent().getStringExtra("created_at");
        postId = getIntent().getIntExtra("post_id", -1);
        String aiSummary = getIntent().getStringExtra("ai_summary");

        // Log for debugging
        Log.d(TAG, "Setting up data - postId: " + postId + ", title: " + title);

        // Set post title and description
        postTitle.setText(title != null ? title : "Untitled Post");
        postAuthorDate.setText("By " + author + " • " + getTimeAgo(createdAt));

        // Set actual post description if available
        if (title != null && !title.isEmpty()) {
            postDescription.setText(title);
            postDescription.setVisibility(View.VISIBLE);
        } else {
            postDescription.setVisibility(View.GONE);
        }

        // Fix the image URL to use the server IP
        if (imageUrl != null && !imageUrl.startsWith("http") && !imageUrl.isEmpty()) {
            imageUrl = "http://" + serverIp + "/healthlink1/web/assets/img/posts/" + imageUrl;
        }

        // Load post image if available
        if (imageUrl != null && !imageUrl.isEmpty()) {
            Glide.with(this)
                    .load(imageUrl)
                    .placeholder(R.drawable.ic_post)
                    .error(R.drawable.ic_broken_image)
                    .into(postImage);
            postImage.setVisibility(View.VISIBLE);
        } else {
            postImage.setVisibility(View.GONE);
        }

        // Handle AI summary if available
        if (aiSummary != null && !aiSummary.isEmpty()) {
            setupAiSummaryView(aiSummary);
        } else if (imageUrl != null && !imageUrl.isEmpty()) {
            // Show AI analysis in progress if there's an image but no AI summary yet
            showAiAnalysisInProgress();
        } else {
            aiSummaryCard.setVisibility(View.GONE);
        }

        // Hide code card since we're using AI summary instead
        codeCard.setVisibility(View.GONE);
    }

    private void setupAiSummaryView(String aiSummary) {
        try {
            aiSummaryCard.setVisibility(View.VISIBLE);
            aiSummaryText.setText(aiSummary);
            aiSummaryText.setTextColor(Color.parseColor("#6B7280"));
            aiSummaryProgress.setVisibility(View.GONE);
            Log.d(TAG, "AI Summary displayed successfully");
        } catch (Exception e) {
            Log.e(TAG, "Error setting up AI summary view: " + e.getMessage());
            aiSummaryCard.setVisibility(View.GONE);
        }
    }

    private void showAiAnalysisInProgress() {
        try {
            aiSummaryCard.setVisibility(View.VISIBLE);
            aiSummaryText.setText("AI analysis in progress...");
            aiSummaryText.setTextColor(Color.parseColor("#9CA3AF"));
            aiSummaryProgress.setVisibility(View.VISIBLE);
            Log.d(TAG, "AI analysis in progress displayed");
        } catch (Exception e) {
            Log.e(TAG, "Error showing AI analysis in progress: " + e.getMessage());
            aiSummaryCard.setVisibility(View.GONE);
        }
    }

    private void setupCodeView(String code, String language) {
        try {
            codeCard.setVisibility(View.VISIBLE);
            currentCodeContent = code; // Store code for copying

            // Set language info
            if (language != null && !language.isEmpty()) {
                codeLanguage.setText(language + " Code");
            } else {
                codeLanguage.setText("Code");
            }

            // Set code content
            codeContent.setText(code);
            // Apply monospace font for better code display
            codeContent.setTypeface(android.graphics.Typeface.MONOSPACE);
        } catch (Exception e) {
            Log.e(TAG, "Error setting up code view: " + e.getMessage());
            codeCard.setVisibility(View.GONE);
        }
    }

    private void copyCodeToClipboard() {
        if (currentCodeContent != null && !currentCodeContent.isEmpty()) {
            ClipboardManager clipboard = (ClipboardManager) getSystemService(Context.CLIPBOARD_SERVICE);
            ClipData clip = ClipData.newPlainText("Code", currentCodeContent);
            clipboard.setPrimaryClip(clip);
            Toast.makeText(this, "Code copied to clipboard", Toast.LENGTH_SHORT).show();
        } else {
            Toast.makeText(this, "No code to copy", Toast.LENGTH_SHORT).show();
        }
    }

    private void setupComments() {
        commentsRecyclerView.setLayoutManager(new LinearLayoutManager(this));
        commentsAdapter = new CommentsAdapter(this, commentsList);
        commentsRecyclerView.setAdapter(commentsAdapter);

        // Setup tags RecyclerView if it exists
        if (tagsRecyclerView != null) {
            tagsRecyclerView.setLayoutManager(new LinearLayoutManager(this, LinearLayoutManager.HORIZONTAL, false));
            tagAdapter = new TagAdapter(this, tagsList, false);
            tagsRecyclerView.setAdapter(tagAdapter);
        }

        // Log for debugging
        Log.d(TAG, "Comments RecyclerView setup with adapter");
    }

    private void loadComments() {
        if (postId == -1) {
            Log.e(TAG, "Invalid post ID");
            return;
        }

        String url = "http://" + serverIp + "/healthlink1/api/get_comments.php";
        Log.d(TAG, "Loading comments from: " + url);

        StringRequest request = new StringRequest(Request.Method.POST, url,
                response -> {
                    try {
                        Log.d(TAG, "Comments response: " + response);
                        JSONObject json = new JSONObject(response);
                        if (json.getBoolean("status")) {
                            JSONArray commentsArray = json.getJSONArray("comments");
                            commentsList.clear();
                            Log.d(TAG, "Found " + commentsArray.length() + " comments");

                            for (int i = 0; i < commentsArray.length(); i++) {
                                JSONObject commentObj = commentsArray.getJSONObject(i);
                                Comment comment = new Comment();
                                comment.setId(commentObj.getInt("id"));
                                comment.setUserId(commentObj.getInt("user_id"));
                                comment.setUserName(commentObj.getString("user_name"));
                                comment.setCommentText(commentObj.getString("comment_text"));
                                comment.setCreatedAt(commentObj.getString("created_at"));
                                comment.setProfilePic(commentObj.optString("profile_pic", null));
                                commentsList.add(comment);
                                // Log each comment for debugging
                                Log.d(TAG, "Added comment: " + comment.getCommentText());
                            }

                            commentsAdapter.notifyDataSetChanged();

                            // Show/hide no comments message
                            if (commentsList.isEmpty()) {
                                noCommentsText.setVisibility(View.VISIBLE);
                                Log.d(TAG, "Showing no comments message");
                            } else {
                                noCommentsText.setVisibility(View.GONE);
                                Log.d(TAG, "Hiding no comments message");
                            }

                            Log.d(TAG, "Loaded " + commentsList.size() + " comments");
                        } else {
                            String error = json.optString("message", "Failed to load comments");
                            Log.e(TAG, "Comments load failed: " + error);
                            Toast.makeText(this, "Error: " + error, Toast.LENGTH_SHORT).show();
                        }
                    } catch (Exception e) {
                        Log.e(TAG, "Error parsing comments", e);
                        Toast.makeText(this, "Error parsing comments", Toast.LENGTH_SHORT).show();
                    }
                },
                error -> {
                    Log.e(TAG, "Network error loading comments", error);
                    Toast.makeText(this, "Network error loading comments", Toast.LENGTH_SHORT).show();
                }
        ) {
            @Override
            protected Map<String, String> getParams() {
                Map<String, String> params = new HashMap<>();
                params.put("post_id", String.valueOf(postId));
                Log.d(TAG, "Sending post_id: " + postId);
                return params;
            }
        };

        Volley.newRequestQueue(this).add(request);
        Log.d(TAG, "Comment request added to queue");
    }

    // RealTimeManager.RealTimeListener methods
    @Override
    public void onEvent(String eventType, JSONObject data) {
        try {
            Log.d(TAG, "Received WebSocket event: " + eventType);
            Log.d(TAG, "Event data: " + data.toString());

            if ("new_comment".equals(eventType)) {
                int receivedPostId = data.getInt("post_id");
                Log.d(TAG, "Received comment for post_id: " + receivedPostId + ", current post_id: " + postId);

                if (receivedPostId == postId) {
                    // This comment is for the current post
                    JSONObject commentData = data.getJSONObject("comment");
                    Comment comment = new Comment();
                    comment.setId(commentData.getInt("id"));
                    comment.setUserId(commentData.getInt("user_id"));
                    comment.setUserName(commentData.getString("user_name"));
                    comment.setCommentText(commentData.getString("comment_text"));
                    comment.setCreatedAt(commentData.getString("created_at"));
                    comment.setProfilePic(commentData.optString("profile_pic", null));

                    // Add comment to the top of the list
                    commentsList.add(0, comment);
                    commentsAdapter.notifyItemInserted(0);

                    // Hide no comments message if it was visible
                    if (noCommentsText.getVisibility() == View.VISIBLE) {
                        noCommentsText.setVisibility(View.GONE);
                    }

                    // Scroll to top to show the new comment
                    commentsRecyclerView.scrollToPosition(0);

                    Log.d(TAG, "New comment added via WebSocket: " + comment.getCommentText());
                }
            }
        } catch (Exception e) {
            Log.e(TAG, "Error processing WebSocket event", e);
        }
    }

    @Override
    public void onConnectionStateChanged(boolean connected) {
        Log.d(TAG, "WebSocket connection state changed: " + connected);
    }

    private String getTimeAgo(String rawTimestamp) {
        // Implement time formatting as in your other activities
        try {
            java.text.SimpleDateFormat format = new java.text.SimpleDateFormat("yyyy-MM-dd HH:mm:ss", java.util.Locale.getDefault());
            java.util.Date postDate = format.parse(rawTimestamp);
            if (postDate == null) {
                return "just now";
            }
            long postMillis = postDate.getTime();
            long nowMillis = System.currentTimeMillis();
            long diff = nowMillis - postMillis;
            if (diff < 0) {
                return "just now";
            }
            long seconds = java.util.concurrent.TimeUnit.MILLISECONDS.toSeconds(diff);
            long minutes = java.util.concurrent.TimeUnit.MILLISECONDS.toMinutes(diff);
            long hours   = java.util.concurrent.TimeUnit.MILLISECONDS.toHours(diff);
            long days    = java.util.concurrent.TimeUnit.MILLISECONDS.toDays(diff);

            if (seconds < 60) return seconds + "s ago";
            else if (minutes < 60) return minutes + "m ago";
            else if (hours < 24) return hours + "h ago";
            else if (days < 7) return days + "d ago";
            else if (days < 30) return (days / 7) + "w ago";
            else if (days < 365) return (days / 30) + "mo ago";
            else return (days / 365) + "y ago";
        } catch (Exception e) {
            Log.e(TAG, "TimeAgo parsing error for: " + rawTimestamp, e);
            return "just now";
        }
    }

    @Override
    public boolean onSupportNavigateUp() {
        onBackPressed();
        return true;
    }
}