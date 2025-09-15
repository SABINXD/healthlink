package com.example.healthlink;

import android.content.Intent;
import android.graphics.Color;
import android.graphics.ColorMatrix;
import android.graphics.ColorMatrixColorFilter;
import android.os.Bundle;
import android.util.Log;
import android.view.View;
import android.widget.Button;
import android.widget.ImageView;
import android.widget.LinearLayout;
import android.widget.TextView;
import android.widget.Toast;
import androidx.appcompat.app.AppCompatActivity;
import androidx.appcompat.widget.Toolbar;
import androidx.cardview.widget.CardView;
import androidx.recyclerview.widget.LinearLayoutManager;
import androidx.recyclerview.widget.RecyclerView;
import com.android.volley.Request;
import com.android.volley.Response;
import com.android.volley.VolleyError;
import com.android.volley.toolbox.StringRequest;
import com.android.volley.toolbox.Volley;
import com.bumptech.glide.Glide;
import com.squareup.picasso.Callback;
import com.squareup.picasso.Picasso;
import org.json.JSONArray;
import org.json.JSONException;
import org.json.JSONObject;
import java.util.ArrayList;
import java.util.HashMap;
import java.util.List;
import java.util.Map;
import de.hdodenhof.circleimageview.CircleImageView;

public class PostDetailsActivity extends AppCompatActivity implements RealTimeManager.RealTimeListener {
    private static final String TAG = "PostDetailsActivity";
    private ImageView postImage, postImage2;
    private TextView postTitle, postAuthorName, postAuthorDate, postDescription;
    private CircleImageView userProfilePic;
    private RecyclerView commentsRecyclerView, tagsRecyclerView, doctorsRecyclerView;
    private TextView noCommentsText;
    private List<Comment> commentsList = new ArrayList<>();
    private CommentsAdapter commentsAdapter;
    private String serverIp;
    private int postId;
    private RealTimeManager realTimeManager;
    private TagAdapter tagAdapter;
    private List<String> tagsList = new ArrayList<>();
    private CardView aiSummaryCard;
    private TextView aiSummaryText;
    private String aiSummaryContent;
    private DoctorAdapter doctorAdapter;
    private List<Doctor> doctorsList = new ArrayList<>();
    private LinearLayout spoilerContainer;
    private Button unblurButton;
    private boolean isSpoiler = false;
    private boolean isUnblurred = false;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_post_details);
        Toolbar toolbar = findViewById(R.id.toolbar);
        setSupportActionBar(toolbar);
        if (getSupportActionBar() != null) {
            getSupportActionBar().setDisplayHomeAsUpEnabled(true);
            getSupportActionBar().setDisplayShowHomeEnabled(true);
        }
        realTimeManager = RealTimeManager.getInstance();
        initializeViews();
        setupData();
        setupComments();
        setupTags();
        setupDoctors();
        loadComments();
        Log.d(TAG, "PostDetailsActivity created with postId: " + postId);
    }

    @Override
    protected void onResume() {
        super.onResume();
        realTimeManager.addListener(this);
        loadComments();
    }

    @Override
    protected void onPause() {
        super.onPause();
        realTimeManager.removeListener(this);
    }

    private void initializeViews() {
        postImage = findViewById(R.id.post_image);
        postTitle = findViewById(R.id.post_title);
        postAuthorName = findViewById(R.id.post_author_name);
        postAuthorDate = findViewById(R.id.post_author_date);
        postDescription = findViewById(R.id.post_description);
        userProfilePic = findViewById(R.id.user_profile_pic);
        commentsRecyclerView = findViewById(R.id.comments_recycler_view);
        noCommentsText = findViewById(R.id.no_comments_text);
        tagsRecyclerView = findViewById(R.id.tags_recycler_view);
        doctorsRecyclerView = findViewById(R.id.doctors_recycler_view);
        aiSummaryCard = findViewById(R.id.ai_summary_card);
        aiSummaryText = findViewById(R.id.ai_summary_text);
        spoilerContainer = findViewById(R.id.spoilerContainer);
        unblurButton = findViewById(R.id.unblur_button);
        serverIp = getString(R.string.server_ip);
    }

    private void setupData() {
        String imageUrl = getIntent().getStringExtra("post_img");
        String title = getIntent().getStringExtra("post_text");
        String author = getIntent().getStringExtra("user_name");
        String createdAt = getIntent().getStringExtra("created_at");
        String profilePic = getIntent().getStringExtra("user_profile_pic");
        String category = getIntent().getStringExtra("category");
        postId = getIntent().getIntExtra("post_id", -1);
        aiSummaryContent = getIntent().getStringExtra("ai_summary");
        isSpoiler = getIntent().getBooleanExtra("spoiler", false);

        Log.d(TAG, "Setting up data:");
        Log.d(TAG, "Image URL: " + imageUrl);
        Log.d(TAG, "Profile Pic: " + profilePic);
        Log.d(TAG, "Author: " + author);
        Log.d(TAG, "Is Spoiler: " + isSpoiler);

        // Set post title and description
        if (title != null && !title.isEmpty()) {
            postTitle.setText(title);
            postDescription.setText(title);
        }

        // Set author info
        postAuthorName.setText(author != null ? author : "Unknown User");
        postAuthorDate.setText(getTimeAgo(createdAt));

        // Load profile picture
        if (profilePic != null && !profilePic.isEmpty() && !profilePic.equals("null")) {
            String profilePicUrl;
            if (profilePic.startsWith("http")) {
                profilePicUrl = profilePic;
            } else {
                profilePicUrl = "http://" + serverIp + "/healthlink/web/assets/img/profile/" + profilePic;
            }
            Log.d(TAG, "Loading profile pic from: " + profilePicUrl);
            Glide.with(this)
                    .load(profilePicUrl)
                    .placeholder(R.drawable.profile_placeholder)
                    .error(R.drawable.profile_placeholder)
                    .into(userProfilePic);
        } else {
            userProfilePic.setImageResource(R.drawable.profile_placeholder);
        }

        // Load post image with spoiler support
        if (imageUrl != null && !imageUrl.isEmpty() && !imageUrl.equals("null")) {
            String fullImageUrl;
            if (imageUrl.startsWith("http")) {
                fullImageUrl = imageUrl;
            } else {
                fullImageUrl = "http://" + serverIp + "/healthlink/web/assets/img/posts/" + imageUrl;
            }
            Log.d(TAG, "Loading post image from: " + fullImageUrl);

            // Show the image container
            View imageContainer = findViewById(R.id.post_images_container);
            if (imageContainer != null) {
                imageContainer.setVisibility(View.VISIBLE);
            }

            Picasso.get()
                    .load(fullImageUrl)
                    .placeholder(R.drawable.media_placeholder)
                    .error(R.drawable.media_placeholder)
                    .into(postImage, new Callback() {
                        @Override
                        public void onSuccess() {
                            Log.d(TAG, "Image loaded successfully, applying spoiler effect if needed");
                            applySpoilerEffect();
                        }

                        @Override
                        public void onError(Exception e) {
                            Log.e(TAG, "Error loading image", e);
                        }
                    });
        } else {
            // Hide image container if no image
            View imageContainer = findViewById(R.id.post_images_container);
            if (imageContainer != null) {
                imageContainer.setVisibility(View.GONE);
            }
        }

        // Setup tags
        if (category != null && !category.isEmpty()) {
            tagsList.add(category.toLowerCase());
        }

        // Add some sample tags based on content
        if (title != null) {
            if (title.toLowerCase().contains("rash")) tagsList.add("skin condition");
            if (title.toLowerCase().contains("headache")) tagsList.add("headache");
            if (title.toLowerCase().contains("pain")) tagsList.add("pain");
        }

        // Show AI summary if available with medical disclaimer
        if (aiSummaryContent != null && !aiSummaryContent.isEmpty()) {
            aiSummaryCard.setVisibility(View.VISIBLE);
            String fullSummary = aiSummaryContent + "\n\n⚠️ This is not an official medical diagnosis. Please consult with a healthcare professional if you feel unwell or have concerns about your health.";
            aiSummaryText.setText(fullSummary);
        } else {
            // Try to refresh AI summary if missing
            refreshAiSummary();
        }
    }

    // Add method to refresh AI summary
    private void refreshAiSummary() {
        String url = "http://" + serverIp + "/healthlink/api/refresh_ai_summary.php";

        StringRequest request = new StringRequest(Request.Method.POST, url,
                response -> {
                    try {
                        JSONObject json = new JSONObject(response);
                        if (json.optBoolean("success")) {
                            String aiSummary = json.optString("ai_summary", "");
                            if (!aiSummary.isEmpty()) {
                                aiSummaryCard.setVisibility(View.VISIBLE);
                                String fullSummary = aiSummary + "\n\n⚠️ This is not an official medical diagnosis. Please consult with a healthcare professional if you feel unwell or have concerns about your health.";
                                aiSummaryText.setText(fullSummary);
                                Log.d(TAG, "AI summary refreshed successfully");
                            }
                        }
                    } catch (Exception e) {
                        Log.e(TAG, "Error parsing AI summary response", e);
                    }
                },
                error -> Log.e(TAG, "Error refreshing AI summary", error)
        ) {
            @Override
            protected Map<String, String> getParams() {
                Map<String, String> params = new HashMap<>();
                params.put("post_id", String.valueOf(postId));
                return params;
            }
        };

        Volley.newRequestQueue(this).add(request);
    }

    // Apply spoiler effect to post details image
    private void applySpoilerEffect() {
        if (isSpoiler && !isUnblurred) {
            Log.d(TAG, "Applying spoiler effect to post details image");
            // Apply heavy blur effect
            ColorMatrix matrix = new ColorMatrix();
            matrix.setSaturation(0.1f); // Heavy desaturation
            ColorMatrixColorFilter filter = new ColorMatrixColorFilter(matrix);
            postImage.setColorFilter(filter);
            postImage.setAlpha(0.3f); // Very transparent

            // Show spoiler overlay
            if (spoilerContainer != null) {
                spoilerContainer.setVisibility(View.VISIBLE);
                if (unblurButton != null) {
                    unblurButton.setOnClickListener(v -> {
                        Log.d(TAG, "Unblur button clicked in post details");
                        isUnblurred = true;
                        // Remove blur effect
                        postImage.setColorFilter(null);
                        postImage.setAlpha(1.0f);
                        spoilerContainer.setVisibility(View.GONE);
                        Toast.makeText(this, "Content revealed", Toast.LENGTH_SHORT).show();
                    });
                }
            }
        } else {
            Log.d(TAG, "No spoiler effect needed");
            // Ensure no blur effect
            postImage.setColorFilter(null);
            postImage.setAlpha(1.0f);
            if (spoilerContainer != null) {
                spoilerContainer.setVisibility(View.GONE);
            }
        }
    }

    private void setupComments() {
        commentsRecyclerView.setLayoutManager(new LinearLayoutManager(this));
        commentsAdapter = new CommentsAdapter(this, commentsList);
        commentsRecyclerView.setAdapter(commentsAdapter);
    }

    private void setupTags() {
        if (tagsRecyclerView != null && !tagsList.isEmpty()) {
            tagAdapter = new TagAdapter(this, tagsList, false);
            tagsRecyclerView.setLayoutManager(new LinearLayoutManager(this, LinearLayoutManager.HORIZONTAL, false));
            tagsRecyclerView.setAdapter(tagAdapter);
        }
    }

    private void setupDoctors() {
        // Load doctors from database
        loadDoctors();

        doctorAdapter = new DoctorAdapter(this, doctorsList, new DoctorAdapter.OnDoctorClickListener() {
            @Override
            public void onConsultClick(Doctor doctor) {
                // Open chat with the doctor
                openChatWithDoctor(doctor);
            }

            @Override
            public void onViewProfileClick(Doctor doctor) {
                // Handle view profile button click
                Intent intent = new Intent(PostDetailsActivity.this, DoctorProfileActivity.class);
                intent.putExtra("doctor_id", doctor.getId());
                startActivity(intent);
            }
        });

        doctorsRecyclerView.setLayoutManager(new LinearLayoutManager(this));
        doctorsRecyclerView.setAdapter(doctorAdapter);
    }

    // Open chat with the selected doctor
    private void openChatWithDoctor(Doctor doctor) {
        // Get current user ID from session
        SessionManager sessionManager = new SessionManager(this);
        int currentUserId = sessionManager.getUserId();

        // Create intent for chat activity
        Intent intent = new Intent(this, ChatActivity.class);
        intent.putExtra("receiver_id", doctor.getId());
        intent.putExtra("receiver_name", doctor.getFirstName() + " " + doctor.getLastName());
        intent.putExtra("receiver_profile", doctor.getProfilePic());
        intent.putExtra("current_user_id", currentUserId);

        // Start chat activity
        startActivity(intent);
        Toast.makeText(this, "Starting chat with Dr. " + doctor.getLastName(), Toast.LENGTH_SHORT).show();
    }

    // Load doctors from database
    private void loadDoctors() {
        String url = "http://" + serverIp + "/healthlink/api/get_doctors.php";

        StringRequest request = new StringRequest(Request.Method.GET, url,
                new Response.Listener<String>() {
                    @Override
                    public void onResponse(String response) {
                        try {
                            JSONObject jsonObject = new JSONObject(response);
                            if (jsonObject.getString("status").equals("success")) {
                                JSONArray doctorsArray = jsonObject.getJSONArray("doctors");
                                doctorsList.clear();

                                for (int i = 0; i < doctorsArray.length(); i++) {
                                    JSONObject doctorObj = doctorsArray.getJSONObject(i);
                                    Doctor doctor = new Doctor();
                                    doctor.setId(doctorObj.getInt("id"));
                                    doctor.setFirstName(doctorObj.getString("first_name"));
                                    doctor.setLastName(doctorObj.getString("last_name"));
                                    doctor.setDoctorType(doctorObj.getString("doctor_type"));
                                    doctor.setDoctorAddress(doctorObj.getString("doctor_address"));
                                    doctor.setProfilePic(doctorObj.optString("profile_pic", ""));
                                    doctorsList.add(doctor);
                                }

                                doctorAdapter.notifyDataSetChanged();
                                Log.d(TAG, "Loaded " + doctorsList.size() + " doctors from database");
                            } else {
                                Log.e(TAG, "Failed to load doctors: " + jsonObject.getString("message"));
                                Toast.makeText(PostDetailsActivity.this, "Failed to load doctors", Toast.LENGTH_SHORT).show();
                            }
                        } catch (JSONException e) {
                            Log.e(TAG, "Error parsing doctors response", e);
                            Toast.makeText(PostDetailsActivity.this, "Error parsing doctors data", Toast.LENGTH_SHORT).show();
                        }
                    }
                },
                new Response.ErrorListener() {
                    @Override
                    public void onErrorResponse(VolleyError error) {
                        Log.e(TAG, "Error loading doctors: " + error.toString());
                        Toast.makeText(PostDetailsActivity.this, "Failed to load doctors", Toast.LENGTH_SHORT).show();
                    }
                });

        Volley.newRequestQueue(this).add(request);
    }

    private void loadComments() {
        if (postId == -1) {
            Log.e(TAG, "Invalid post ID");
            return;
        }

        String url = "http://" + serverIp + "/healthlink/api/get_comments.php";

        StringRequest request = new StringRequest(Request.Method.POST, url,
                response -> {
                    try {
                        JSONObject json = new JSONObject(response);
                        if (json.getBoolean("status")) {
                            JSONArray commentsArray = json.getJSONArray("comments");
                            commentsList.clear();
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
                            }
                            commentsAdapter.notifyDataSetChanged();
                            if (commentsList.isEmpty()) {
                                noCommentsText.setVisibility(View.VISIBLE);
                            } else {
                                noCommentsText.setVisibility(View.GONE);
                            }
                        }
                    } catch (Exception e) {
                        Log.e(TAG, "Error parsing comments", e);
                    }
                },
                error -> Log.e(TAG, "Network error loading comments", error)
        ) {
            @Override
            protected Map<String, String> getParams() {
                Map<String, String> params = new HashMap<>();
                params.put("post_id", String.valueOf(postId));
                return params;
            }
        };

        Volley.newRequestQueue(this).add(request);
    }

    @Override
    public void onEvent(String eventType, JSONObject data) {
        try {
            if ("new_comment".equals(eventType)) {
                int receivedPostId = data.getInt("post_id");
                if (receivedPostId == postId) {
                    JSONObject commentData = data.getJSONObject("comment");
                    Comment comment = new Comment();
                    comment.setId(commentData.getInt("id"));
                    comment.setUserId(commentData.getInt("user_id"));
                    comment.setUserName(commentData.getString("user_name"));
                    comment.setCommentText(commentData.getString("comment_text"));
                    comment.setCreatedAt(commentData.getString("created_at"));
                    comment.setProfilePic(commentData.optString("profile_pic", null));
                    commentsList.add(0, comment);
                    commentsAdapter.notifyItemInserted(0);
                    if (noCommentsText.getVisibility() == View.VISIBLE) {
                        noCommentsText.setVisibility(View.GONE);
                    }
                    commentsRecyclerView.scrollToPosition(0);
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
        try {
            java.text.SimpleDateFormat format = new java.text.SimpleDateFormat("yyyy-MM-dd HH:mm:ss", java.util.Locale.getDefault());
            java.util.Date postDate = format.parse(rawTimestamp);
            if (postDate == null) return "just now";

            long diff = System.currentTimeMillis() - postDate.getTime();
            if (diff < 0) return "just now";

            long seconds = java.util.concurrent.TimeUnit.MILLISECONDS.toSeconds(diff);
            long minutes = java.util.concurrent.TimeUnit.MILLISECONDS.toMinutes(diff);
            long hours = java.util.concurrent.TimeUnit.MILLISECONDS.toHours(diff);
            long days = java.util.concurrent.TimeUnit.MILLISECONDS.toDays(diff);

            if (seconds < 60) return seconds + "s ago";
            else if (minutes < 60) return minutes + "m ago";
            else if (hours < 24) return hours + "h ago";
            else if (days < 7) return days + "d ago";
            else return "just now";
        } catch (Exception e) {
            return "just now";
        }
    }

    @Override
    public boolean onSupportNavigateUp() {
        onBackPressed();
        return true;
    }
}