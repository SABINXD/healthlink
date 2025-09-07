package com.example.healthlink;
import android.app.AlertDialog;
import android.content.Intent;
import android.graphics.Color;
import android.os.Bundle;
import android.text.SpannableString;
import android.text.style.ForegroundColorSpan;
import android.util.Log;
import android.view.Menu;
import android.view.MenuItem;
import android.view.View;
import android.widget.Button;
import android.widget.LinearLayout;
import android.widget.TextView;
import android.widget.Toast;
import androidx.appcompat.app.AppCompatActivity;
import androidx.appcompat.widget.Toolbar;
import androidx.recyclerview.widget.LinearLayoutManager;
import androidx.recyclerview.widget.RecyclerView;
import com.android.volley.Request;
import com.android.volley.toolbox.StringRequest;
import com.android.volley.toolbox.Volley;
import com.bumptech.glide.Glide;
import com.bumptech.glide.load.engine.DiskCacheStrategy;
import org.json.JSONArray;
import org.json.JSONObject;
import java.util.ArrayList;
import java.util.HashMap;
import java.util.List;
import java.util.Map;
import de.hdodenhof.circleimageview.CircleImageView;

public class UserProfileActivity extends AppCompatActivity {
    private static final String TAG = "UserProfileActivity";
    // UI Components
    private Toolbar toolbar;
    private CircleImageView profileImage;
    private TextView profileName, profileUsername, profileBio, tvPostCount, tvFollowers, tvFollowing;
    private Button btnFollow, btnMessage;
    private RecyclerView recyclerPosts;
    private LinearLayout emptyPostsLayout;
    private LinearLayout postsStatsLayout, followersStatsLayout, followingStatsLayout;
    // User data
    private int targetUserId;
    private String username;
    private String recipientProfileImageUrl; // Added to store profile image URL
    private boolean isOwnProfile = false;
    private boolean isFollowing = false;
    private boolean isBlocked = false;
    private boolean isFollowRequestPending = false;
    // Session and server
    private SessionManager sessionManager;
    private String serverIp;
    // Posts data
    private List<Post> userPosts = new ArrayList<>();
    private PostAdapter adapter;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_user_profile);
        try {
            initializeComponents();
            setupToolbar();
            setupRecyclerView();
            setupClickListeners();
            if (targetUserId != -1) {
                loadUserProfile();
                if (!isOwnProfile) {
                    checkFollowStatus();
                    checkBlockStatus();
                }
                loadUserPosts();
            } else {
                Toast.makeText(this, "Invalid user ID", Toast.LENGTH_SHORT).show();
                finish();
            }
        } catch (Exception e) {
            Log.e(TAG, "Error in onCreate", e);
            Toast.makeText(this, "Error loading profile", Toast.LENGTH_SHORT).show();
            finish();
        }
    }

    private void initializeComponents() {
        sessionManager = new SessionManager(this);
        if (!sessionManager.isSessionValid()) {
            Toast.makeText(this, "Session invalid", Toast.LENGTH_SHORT).show();
            finish();
            return;
        }
        serverIp = getString(R.string.server_ip);
        // Get user data from intent
        targetUserId = getIntent().getIntExtra("user_id", -1);
        username = getIntent().getStringExtra("username");
        // Proper own profile detection
        int currentUserId = sessionManager.getUserId();
        isOwnProfile = (targetUserId == currentUserId);
        Log.d(TAG, "🎯 Target user ID: " + targetUserId);
        Log.d(TAG, "🎯 Current user ID: " + currentUserId);
        Log.d(TAG, "👤 Is own profile: " + isOwnProfile);
        // Find views
        toolbar = findViewById(R.id.toolbar);
        profileImage = findViewById(R.id.profile_image);
        profileName = findViewById(R.id.profile_name);
        profileUsername = findViewById(R.id.profile_username);
        profileBio = findViewById(R.id.profile_bio);
        tvPostCount = findViewById(R.id.tv_post_count);
        tvFollowers = findViewById(R.id.tv_followers);
        tvFollowing = findViewById(R.id.tv_following);
        btnFollow = findViewById(R.id.btn_follow);
        btnMessage = findViewById(R.id.btn_message);
        recyclerPosts = findViewById(R.id.recycler_posts);
        emptyPostsLayout = findViewById(R.id.empty_posts_layout);
        // Stats layouts for click listeners
        postsStatsLayout = findViewById(R.id.posts_stats_layout);
        followersStatsLayout = findViewById(R.id.followers_stats_layout);
        followingStatsLayout = findViewById(R.id.following_stats_layout);
        // Configure UI based on profile type
        configureUIForProfileType();
    }

    private void configureUIForProfileType() {
        if (isOwnProfile) {
            // Own profile - show edit button, hide message button
            if (btnFollow != null) {
                btnFollow.setText("Edit Profile");
                btnFollow.setVisibility(View.VISIBLE);
                btnFollow.setBackgroundTintList(getResources().getColorStateList(R.color.colorPrimary));
            }
            if (btnMessage != null) {
                btnMessage.setVisibility(View.GONE);
            }
            Log.d(TAG, "✅ Configured UI for own profile");
        } else {
            // Other user's profile - show follow/message buttons
            if (btnFollow != null) {
                btnFollow.setText("Follow");
                btnFollow.setVisibility(View.VISIBLE);
                btnFollow.setBackgroundTintList(getResources().getColorStateList(R.color.colorPrimary));
            }
            if (btnMessage != null) {
                btnMessage.setVisibility(View.VISIBLE);
                btnMessage.setText("Message");
                btnMessage.setBackgroundTintList(getResources().getColorStateList(android.R.color.darker_gray));
            }
            Log.d(TAG, "✅ Configured UI for other user's profile");
        }
    }

    private void setupRecyclerView() {
        if (recyclerPosts != null) {
            LinearLayoutManager layoutManager = new LinearLayoutManager(this);
            recyclerPosts.setLayoutManager(layoutManager);
            adapter = new PostAdapter(this, userPosts, serverIp, sessionManager.getUserId());
            recyclerPosts.setAdapter(adapter);
            Log.d(TAG, "✅ RecyclerView setup complete");
        }
    }

    private void setupToolbar() {
        if (toolbar != null) {
            setSupportActionBar(toolbar);
            if (getSupportActionBar() != null) {
                getSupportActionBar().setDisplayHomeAsUpEnabled(true);
                if (isOwnProfile) {
                    getSupportActionBar().setTitle("My Profile");
                } else {
                    getSupportActionBar().setTitle(username != null ? "@" + username : "Profile");
                }
            }
            toolbar.setNavigationOnClickListener(v -> finish());
        }
    }

    private void setupClickListeners() {
        if (btnFollow != null) {
            btnFollow.setOnClickListener(v -> {
                if (isOwnProfile) {
                    // Edit profile
                    try {
                        Intent intent = new Intent(this, EditProfileInfoActivity.class);
                        startActivity(intent);
                    } catch (Exception e) {
                        Log.e(TAG, "EditProfileInfoActivity not found", e);
                        Toast.makeText(this, "Edit profile not available", Toast.LENGTH_SHORT).show();
                    }
                } else {
                    // Follow/Unfollow logic
                    if (isBlocked) {
                        Toast.makeText(this, "Cannot follow blocked user", Toast.LENGTH_SHORT).show();
                        return;
                    }
                    // Check current follow status and toggle
                    if (btnFollow.getText().toString().equals("Following")) {
                        unfollowUser();
                    } else {
                        followUser();
                    }
                }
            });
        }
        if (btnMessage != null) {
            btnMessage.setOnClickListener(v -> {
                if (!isOwnProfile) {
                    if (isBlocked) {
                        Toast.makeText(this, "❌ Cannot message blocked user", Toast.LENGTH_SHORT).show();
                    } else {
                        openMessageDialog();
                    }
                }
            });
        }
        // Stats click listeners
        if (followersStatsLayout != null) {
            followersStatsLayout.setOnClickListener(v -> {
                if (!isBlocked) {
                    Toast.makeText(this, "Followers: " + tvFollowers.getText(), Toast.LENGTH_SHORT).show();
                }
            });
        }
        if (followingStatsLayout != null) {
            followingStatsLayout.setOnClickListener(v -> {
                if (!isBlocked) {
                    Toast.makeText(this, "Following: " + tvFollowing.getText(), Toast.LENGTH_SHORT).show();
                }
            });
        }
    }

    @Override
    public boolean onCreateOptionsMenu(Menu menu) {
        if (!isOwnProfile) {
            // Use different menu files based on block status
            if (isBlocked) {
                getMenuInflater().inflate(R.menu.menu_user_profile_blocked, menu);
            } else {
                getMenuInflater().inflate(R.menu.menu_user_profile_normal, menu);
            }
            // Force menu text to be visible
            try {
                for (int i = 0; i < menu.size(); i++) {
                    MenuItem item = menu.getItem(i);
                    if (item != null && item.getTitle() != null) {
                        SpannableString spanString = new SpannableString(item.getTitle().toString());
                        spanString.setSpan(new ForegroundColorSpan(Color.BLACK), 0, spanString.length(), 0);
                        item.setTitle(spanString);
                        item.setShowAsAction(MenuItem.SHOW_AS_ACTION_NEVER);
                    }
                }
            } catch (Exception e) {
                Log.e(TAG, "Error setting menu text color", e);
            }
            Log.d(TAG, "✅ Options menu created for blocked status: " + isBlocked);
        }
        return true;
    }

    @Override
    public boolean onPrepareOptionsMenu(Menu menu) {
        // Force menu items to show text
        for (int i = 0; i < menu.size(); i++) {
            MenuItem item = menu.getItem(i);
            if (item != null) {
                item.setShowAsAction(MenuItem.SHOW_AS_ACTION_NEVER);
            }
        }
        return super.onPrepareOptionsMenu(menu);
    }

    @Override
    public boolean onOptionsItemSelected(MenuItem item) {
        int id = item.getItemId();
        if (id == R.id.menu_block) {
            blockUser();
            return true;
        } else if (id == R.id.menu_unblock) {
            unblockUser();
            return true;
        } else if (id == R.id.menu_message) {
            if (isBlocked) {
                Toast.makeText(this, "❌ Cannot message blocked user", Toast.LENGTH_SHORT).show();
            } else {
                openMessageDialog();
            }
            return true;
        }
        return super.onOptionsItemSelected(item);
    }

    private void openMessageDialog() {
        if (isBlocked) {
            Toast.makeText(this, "❌ Cannot message blocked user", Toast.LENGTH_SHORT).show();
            return;
        }
        // Create intent to open ChatActivity with the recipient's details
        Intent intent = new Intent(this, ChatActivity.class);
        intent.putExtra("RECIPIENT_ID", targetUserId);
        intent.putExtra("RECIPIENT_USERNAME", username);
        intent.putExtra("RECIPIENT_PROFILE_PIC", recipientProfileImageUrl);
        // Start the chat activity
        startActivity(intent);
        // Optional: Add a toast to confirm opening the chat
        Toast.makeText(this, "Opening chat with @" + username, Toast.LENGTH_SHORT).show();
    }

    private void loadUserProfile() {
        String url = "http://" + serverIp + "/healthlink1/api/get_user_profile.php?user_id=" + targetUserId;
        Log.d(TAG, "📡 Loading profile from: " + url);
        StringRequest request = new StringRequest(Request.Method.GET, url,
                response -> {
                    try {
                        Log.d(TAG, "✅ Profile response: " + response);
                        JSONObject jsonResponse = new JSONObject(response);
                        parseUserProfileResponse(jsonResponse);
                    } catch (Exception e) {
                        Log.e(TAG, "❌ Error processing profile response", e);
                        Toast.makeText(this, "Error processing profile data", Toast.LENGTH_SHORT).show();
                    }
                },
                error -> {
                    Log.e(TAG, "❌ Error loading user profile", error);
                    Toast.makeText(this, "Failed to load profile. Please check your connection.", Toast.LENGTH_LONG).show();
                });
        Volley.newRequestQueue(this).add(request);
    }

    private void checkFollowStatus() {
        String url = "http://" + serverIp + "/healthlink1/api/check_follow_status.php?current_user_id=" +
                sessionManager.getUserId() + "&target_user_id=" + targetUserId;
        Log.d(TAG, "📡 Checking follow status: " + url);
        StringRequest request = new StringRequest(Request.Method.GET, url,
                response -> {
                    try {
                        Log.d(TAG, "✅ Follow status response: " + response);
                        JSONObject obj = new JSONObject(response);
                        if ("success".equals(obj.getString("status"))) {
                            isFollowing = obj.getBoolean("is_following");
                            isFollowRequestPending = obj.optBoolean("is_pending", false);
                            updateFollowButton();
                            updateMessageButton();
                            Log.d(TAG, "✅ Follow status updated - Following: " + isFollowing);
                        }
                    } catch (Exception e) {
                        Log.e(TAG, "❌ Error parsing follow status", e);
                    }
                },
                error -> Log.e(TAG, "❌ Error checking follow status", error));
        Volley.newRequestQueue(this).add(request);
    }

    private void checkBlockStatus() {
        String url = "http://" + serverIp + "/healthlink1/api/check_block_status.php?blocker_id=" +
                sessionManager.getUserId() + "&blocked_id=" + targetUserId;
        Log.d(TAG, "📡 Checking block status: " + url);
        StringRequest request = new StringRequest(Request.Method.GET, url,
                response -> {
                    try {
                        Log.d(TAG, "✅ Block status response: " + response);
                        JSONObject obj = new JSONObject(response);
                        if ("success".equals(obj.getString("status"))) {
                            isBlocked = obj.getBoolean("is_blocked");
                            updateUIForBlockStatus();
                            invalidateOptionsMenu();
                            Log.d(TAG, "✅ Block status updated - Blocked: " + isBlocked);
                        }
                    } catch (Exception e) {
                        Log.e(TAG, "❌ Error parsing block status", e);
                    }
                },
                error -> Log.e(TAG, "❌ Error checking block status", error));
        Volley.newRequestQueue(this).add(request);
    }

    private void loadUserPosts() {
        if (isBlocked) {
            Log.d(TAG, "⚠️ Not loading posts for blocked user");
            updatePostsUI();
            return;
        }
        String url = "http://" + serverIp + "/healthlink1/api/get_user_posts.php";
        Log.d(TAG, "📡 Loading posts from: " + url);
        StringRequest request = new StringRequest(Request.Method.POST, url,
                response -> {
                    try {
                        Log.d(TAG, "✅ Posts response: " + response);
                        JSONObject jsonResponse = new JSONObject(response);
                        if ("success".equals(jsonResponse.getString("status"))) {
                            JSONArray postsArray = jsonResponse.getJSONArray("posts");
                            userPosts.clear();
                            for (int i = 0; i < postsArray.length(); i++) {
                                JSONObject postObj = postsArray.getJSONObject(i);
                                Post post = createPostFromJson(postObj);
                                userPosts.add(post);
                                // Debug log for like status and AI summary
                                Log.d(TAG, "🔍 Post " + post.getId() +
                                        " - Likes: " + post.getLikeCount() +
                                        ", Is Liked: " + post.isLikedByCurrentUser() +
                                        ", Has AI Summary: " + post.hasAiSummary());
                            }
                            // Update post count in UI
                            if (tvPostCount != null) {
                                tvPostCount.setText(String.valueOf(userPosts.size()));
                            }
                            updatePostsUI();
                            if (adapter != null) {
                                adapter.notifyDataSetChanged();
                            }
                            Log.d(TAG, "✅ Loaded " + userPosts.size() + " posts for user " + targetUserId);
                        } else {
                            String message = jsonResponse.optString("message", "Unknown error");
                            Log.e(TAG, "❌ Posts loading failed: " + message);
                        }
                    } catch (Exception e) {
                        Log.e(TAG, "❌ Error parsing posts", e);
                    }
                },
                error -> {
                    Log.e(TAG, "❌ Error loading posts", error);
                    if (error.networkResponse != null) {
                        Log.e(TAG, "Status code: " + error.networkResponse.statusCode);
                        String errorBody = new String(error.networkResponse.data);
                        Log.e(TAG, "Error body: " + errorBody);
                    }
                }) {
            @Override
            protected Map<String, String> getParams() {
                Map<String, String> params = new HashMap<>();
                params.put("user_id", String.valueOf(targetUserId));
                params.put("current_user_id", String.valueOf(sessionManager.getUserId()));
                Log.d(TAG, "📤 Posts params: " + params);
                return params;
            }
        };
        Volley.newRequestQueue(this).add(request);
    }

    private Post createPostFromJson(JSONObject obj) throws Exception {
        Post post = new Post();
        post.setId(obj.getInt("id"));
        post.setUserId(obj.getInt("user_id"));
        post.setUserName(obj.optString("user_name", ""));
        post.setProfilePic(obj.optString("profile_pic", ""));

        // Handle post description
        String postDescription = obj.optString("post_description", "");
        if (postDescription != null && !postDescription.equals("null") && !postDescription.trim().isEmpty()) {
            post.setPostDescription(postDescription);
        }

        // Handle post image
        String postImage = obj.optString("post_image", "");
        if (postImage != null && !postImage.equals("null") && !postImage.trim().isEmpty()) {
            post.setPostImage(postImage);
        }

        post.setLikeCount(obj.optInt("like_count", 0));
        post.setCommentCount(obj.optInt("comment_count", 0));

        // Proper like status handling
        boolean isLiked = obj.optBoolean("is_liked", false);
        post.setLikedByCurrentUser(isLiked);
        post.setCreatedAt(obj.optString("created_at", ""));

       

        return post;
    }

    private void updatePostsUI() {
        if (isBlocked) {
            if (recyclerPosts != null) recyclerPosts.setVisibility(View.GONE);
            if (emptyPostsLayout != null) emptyPostsLayout.setVisibility(View.VISIBLE);
            Log.d(TAG, "✅ Showing blocked user empty state");
        } else if (userPosts.size() > 0) {
            if (recyclerPosts != null) recyclerPosts.setVisibility(View.VISIBLE);
            if (emptyPostsLayout != null) emptyPostsLayout.setVisibility(View.GONE);
            Log.d(TAG, "✅ Showing " + userPosts.size() + " posts");
        } else {
            if (recyclerPosts != null) recyclerPosts.setVisibility(View.GONE);
            if (emptyPostsLayout != null) emptyPostsLayout.setVisibility(View.VISIBLE);
            Log.d(TAG, "✅ Showing empty posts state");
        }
    }

    private void parseUserProfileResponse(JSONObject response) {
        try {
            if ("success".equals(response.getString("status"))) {
                JSONObject user = response.getJSONObject("user");
                String firstName = user.optString("first_name", "");
                String lastName = user.optString("last_name", "");
                String displayName = (firstName + " " + lastName).trim();
                if (displayName.isEmpty()) {
                    displayName = user.optString("username", "Unknown User");
                }
                String username = user.optString("username", "");
                String bio = user.optString("bio", "No bio available");
                String profilePic = user.optString("profile_pic", "");
                int postCount = user.optInt("post_count", 0);
                int followers = user.optInt("followers", 0);
                int following = user.optInt("following", 0);

                // Store the profile image URL for use in messaging
                recipientProfileImageUrl = profilePic;

                // Update UI - ALWAYS update for own profile, only if not blocked for others
                if (isOwnProfile || !isBlocked) {
                    if (profileName != null) profileName.setText(displayName);
                    if (profileUsername != null) profileUsername.setText("@" + username);
                    if (profileBio != null) profileBio.setText(bio);
                    if (tvPostCount != null) tvPostCount.setText(String.valueOf(postCount));
                    if (tvFollowers != null) tvFollowers.setText(String.valueOf(followers));
                    if (tvFollowing != null) tvFollowing.setText(String.valueOf(following));
                    loadProfileImage(profilePic);
                }
                Log.d(TAG, "✅ Profile UI updated for user: " + displayName);
            } else {
                String message = response.optString("message", "Unknown error");
                Log.e(TAG, "❌ Profile loading failed: " + message);
                Toast.makeText(this, "Failed to load profile: " + message, Toast.LENGTH_LONG).show();
            }
        } catch (Exception e) {
            Log.e(TAG, "❌ Error parsing profile response", e);
            Toast.makeText(this, "Error parsing profile data", Toast.LENGTH_SHORT).show();
        }
    }

    private void loadProfileImage(String profilePic) {
        if (profileImage != null && (isOwnProfile || !isBlocked)) {
            if (profilePic != null && !profilePic.isEmpty() && !profilePic.equals("null")) {
                String imageUrl = "http://" + serverIp + "/healthlink1/web/assets/img/profile/" + profilePic;
                Log.d(TAG, "🖼️ Loading profile image: " + imageUrl);
                Glide.with(this)
                        .load(imageUrl)
                        .circleCrop()
                        .placeholder(R.drawable.profile_placeholder)
                        .error(R.drawable.profile_placeholder)
                        .diskCacheStrategy(DiskCacheStrategy.ALL)
                        .into(profileImage);
            } else {
                profileImage.setImageResource(R.drawable.profile_placeholder);
            }
        }
    }

    private void followUser() {
        String url = "http://" + serverIp + "/healthlink1/api/follow_user.php";
        Log.d(TAG, "📡 Following user: " + url);
        btnFollow.setEnabled(false);
        btnFollow.setText("Following...");
        StringRequest request = new StringRequest(Request.Method.POST, url,
                response -> {
                    try {
                        Log.d(TAG, "✅ Follow response: " + response);
                        JSONObject obj = new JSONObject(response);
                        if ("success".equals(obj.getString("status"))) {
                            btnFollow.setText("Following");
                            btnFollow.setEnabled(true);
                            btnFollow.setBackgroundTintList(getResources().getColorStateList(android.R.color.darker_gray));
                            Toast.makeText(this, "✅ Following " + username, Toast.LENGTH_SHORT).show();
                            // Update follower count
                            if (tvFollowers != null) {
                                try {
                                    String currentFollowers = tvFollowers.getText().toString();
                                    int count = Integer.parseInt(currentFollowers) + 1;
                                    tvFollowers.setText(String.valueOf(count));
                                } catch (NumberFormatException e) {
                                    Log.e(TAG, "Error parsing follower count", e);
                                }
                            }
                        } else {
                            String message = obj.optString("message", "Failed to follow");
                            Toast.makeText(this, "❌ " + message, Toast.LENGTH_LONG).show();
                            btnFollow.setEnabled(true);
                            btnFollow.setText("Follow");
                        }
                    } catch (Exception e) {
                        Log.e(TAG, "❌ Error parsing follow response", e);
                        Toast.makeText(this, "❌ Error following user", Toast.LENGTH_SHORT).show();
                        btnFollow.setEnabled(true);
                        btnFollow.setText("Follow");
                    }
                },
                error -> {
                    Log.e(TAG, "❌ Error following user", error);
                    Toast.makeText(this, "❌ Network error. Please try again.", Toast.LENGTH_LONG).show();
                    btnFollow.setEnabled(true);
                    btnFollow.setText("Follow");
                }) {
            @Override
            protected Map<String, String> getParams() {
                Map<String, String> params = new HashMap<>();
                params.put("follower_id", String.valueOf(sessionManager.getUserId()));
                params.put("user_id", String.valueOf(targetUserId));
                Log.d(TAG, "📤 Follow params: " + params);
                return params;
            }
        };
        Volley.newRequestQueue(this).add(request);
    }

    private void unfollowUser() {
        String url = "http://" + serverIp + "/healthlink1/api/unfollow_user.php";
        Log.d(TAG, "📡 Unfollowing user: " + url);
        btnFollow.setEnabled(false);
        btnFollow.setText("Unfollowing...");
        StringRequest request = new StringRequest(Request.Method.POST, url,
                response -> {
                    try {
                        Log.d(TAG, "✅ Unfollow response: " + response);
                        JSONObject obj = new JSONObject(response);
                        if ("success".equals(obj.getString("status"))) {
                            btnFollow.setText("Follow");
                            btnFollow.setEnabled(true);
                            btnFollow.setBackgroundTintList(getResources().getColorStateList(R.color.colorPrimary));
                            Toast.makeText(this, "✅ Unfollowed " + username, Toast.LENGTH_SHORT).show();
                            // Update follower count
                            if (tvFollowers != null) {
                                try {
                                    String currentFollowers = tvFollowers.getText().toString();
                                    int count = Math.max(0, Integer.parseInt(currentFollowers) - 1);
                                    tvFollowers.setText(String.valueOf(count));
                                } catch (NumberFormatException e) {
                                    Log.e(TAG, "Error parsing follower count", e);
                                }
                            }
                        } else {
                            String message = obj.optString("message", "Failed to unfollow");
                            Toast.makeText(this, "❌ " + message, Toast.LENGTH_LONG).show();
                            btnFollow.setEnabled(true);
                            btnFollow.setText("Following");
                        }
                    } catch (Exception e) {
                        Log.e(TAG, "❌ Error parsing unfollow response", e);
                        Toast.makeText(this, "❌ Error unfollowing user", Toast.LENGTH_SHORT).show();
                        btnFollow.setEnabled(true);
                        btnFollow.setText("Following");
                    }
                },
                error -> {
                    Log.e(TAG, "❌ Error unfollowing user", error);
                    Toast.makeText(this, "❌ Network error. Please try again.", Toast.LENGTH_LONG).show();
                    btnFollow.setEnabled(true);
                    btnFollow.setText("Following");
                }) {
            @Override
            protected Map<String, String> getParams() {
                Map<String, String> params = new HashMap<>();
                params.put("follower_id", String.valueOf(sessionManager.getUserId()));
                params.put("user_id", String.valueOf(targetUserId));
                Log.d(TAG, "📤 Unfollow params: " + params);
                return params;
            }
        };
        Volley.newRequestQueue(this).add(request);
    }

    private void blockUser() {
        new AlertDialog.Builder(this)
                .setTitle("Block User")
                .setMessage("Are you sure you want to block @" + username + "?\n\n🚫 You won't see their posts\n🚫 They won't be able to follow you\n🚫 You won't be able to message them")
                .setPositiveButton("Block", (dialog, which) -> performBlockUser())
                .setNegativeButton("Cancel", null)
                .show();
    }

    private void performBlockUser() {
        String url = "http://" + serverIp + "/healthlink1/api/block_user.php";
        Log.d(TAG, "📡 Blocking user: " + url);
        StringRequest request = new StringRequest(Request.Method.POST, url,
                response -> {
                    try {
                        Log.d(TAG, "✅ Block response: " + response);
                        JSONObject obj = new JSONObject(response);
                        if ("success".equals(obj.getString("status"))) {
                            isBlocked = true;
                            isFollowing = false;
                            updateUIForBlockStatus();
                            invalidateOptionsMenu();
                            Toast.makeText(this, "🚫 Blocked " + username, Toast.LENGTH_SHORT).show();
                        } else {
                            String message = obj.optString("message", "Failed to block");
                            Toast.makeText(this, "❌ " + message, Toast.LENGTH_SHORT).show();
                        }
                    } catch (Exception e) {
                        Log.e(TAG, "❌ Error parsing block response", e);
                        Toast.makeText(this, "❌ Error blocking user", Toast.LENGTH_SHORT).show();
                    }
                },
                error -> {
                    Log.e(TAG, "❌ Error blocking user", error);
                    Toast.makeText(this, "❌ Network error", Toast.LENGTH_SHORT).show();
                }) {
            @Override
            protected Map<String, String> getParams() {
                Map<String, String> params = new HashMap<>();
                params.put("user_id", String.valueOf(sessionManager.getUserId()));
                params.put("blocked_user_id", String.valueOf(targetUserId));
                return params;
            }
        };
        Volley.newRequestQueue(this).add(request);
    }

    private void unblockUser() {
        new AlertDialog.Builder(this)
                .setTitle("Unblock User")
                .setMessage("Are you sure you want to unblock @" + username + "?\n\n✅ You'll see their posts again\n✅ They can follow you\n✅ You can message them")
                .setPositiveButton("Unblock", (dialog, which) -> performUnblockUser())
                .setNegativeButton("Cancel", null)
                .show();
    }

    private void performUnblockUser() {
        String url = "http://" + serverIp + "/healthlink1/api/unblock_user.php";
        Log.d(TAG, "📡 Unblocking user: " + url);
        StringRequest request = new StringRequest(Request.Method.POST, url,
                response -> {
                    try {
                        Log.d(TAG, "✅ Unblock response: " + response);
                        JSONObject obj = new JSONObject(response);
                        if ("success".equals(obj.getString("status"))) {
                            isBlocked = false;
                            updateUIForBlockStatus();
                            invalidateOptionsMenu();
                            Toast.makeText(this, "✅ Unblocked " + username, Toast.LENGTH_SHORT).show();
                            // Reload profile and posts
                            loadUserProfile();
                            checkFollowStatus();
                            loadUserPosts();
                        } else {
                            String message = obj.optString("message", "Failed to unblock");
                            Toast.makeText(this, "❌ " + message, Toast.LENGTH_SHORT).show();
                        }
                    } catch (Exception e) {
                        Log.e(TAG, "❌ Error parsing unblock response", e);
                        Toast.makeText(this, "❌ Error unblocking user", Toast.LENGTH_SHORT).show();
                    }
                },
                error -> {
                    Log.e(TAG, "❌ Error unblocking user", error);
                    Toast.makeText(this, "❌ Network error", Toast.LENGTH_SHORT).show();
                }) {
            @Override
            protected Map<String, String> getParams() {
                Map<String, String> params = new HashMap<>();
                params.put("user_id", String.valueOf(sessionManager.getUserId()));
                params.put("blocked_user_id", String.valueOf(targetUserId));
                return params;
            }
        };
        Volley.newRequestQueue(this).add(request);
    }

    private void updateFollowButton() {
        if (btnFollow != null && !isOwnProfile) {
            if (isBlocked) {
                btnFollow.setVisibility(View.GONE);
            } else if (isFollowRequestPending) {
                btnFollow.setText("Requested");
                btnFollow.setEnabled(false);
                btnFollow.setVisibility(View.VISIBLE);
                btnFollow.setBackgroundTintList(getResources().getColorStateList(android.R.color.darker_gray));
            } else if (isFollowing) {
                btnFollow.setText("Following");
                btnFollow.setEnabled(true);
                btnFollow.setVisibility(View.VISIBLE);
                btnFollow.setBackgroundTintList(getResources().getColorStateList(android.R.color.darker_gray));
            } else {
                btnFollow.setText("Follow");
                btnFollow.setEnabled(true);
                btnFollow.setVisibility(View.VISIBLE);
                btnFollow.setBackgroundTintList(getResources().getColorStateList(R.color.colorPrimary));
            }
        }
    }

    private void updateMessageButton() {
        if (btnMessage != null && !isOwnProfile) {
            if (isBlocked) {
                btnMessage.setVisibility(View.GONE);
            } else {
                btnMessage.setEnabled(true);
                btnMessage.setVisibility(View.VISIBLE);
            }
        }
    }

    private void updateUIForBlockStatus() {
        if (isBlocked) {
            // Lock entire profile for blocked users
            if (profileName != null) profileName.setText("🚫 Blocked User");
            if (profileBio != null) profileBio.setText("This user has been blocked");
            if (profileUsername != null) profileUsername.setText("@blocked");
            if (profileImage != null) profileImage.setImageResource(R.drawable.profile_placeholder);
            // Hide stats
            if (tvPostCount != null) tvPostCount.setText("0");
            if (tvFollowers != null) tvFollowers.setText("0");
            if (tvFollowing != null) tvFollowing.setText("0");
            // Hide buttons
            if (btnFollow != null) btnFollow.setVisibility(View.GONE);
            if (btnMessage != null) btnMessage.setVisibility(View.GONE);
            // Hide posts
            if (recyclerPosts != null) recyclerPosts.setVisibility(View.GONE);
            if (emptyPostsLayout != null) emptyPostsLayout.setVisibility(View.VISIBLE);
            Log.d(TAG, "🚫 Profile locked for blocked user");
        } else {
            // Restore normal UI for unblocked users
            if (!isOwnProfile) {
                updateFollowButton();
                updateMessageButton();
            }
            updatePostsUI();
            Log.d(TAG, "✅ Profile unlocked for unblocked user");
        }
    }

    @Override
    protected void onResume() {
        super.onResume();
        // Refresh data when returning to this activity
        if (targetUserId != -1) {
            if (!isOwnProfile) {
                checkFollowStatus();
                checkBlockStatus();
            }
            loadUserPosts();
        }
    }
}