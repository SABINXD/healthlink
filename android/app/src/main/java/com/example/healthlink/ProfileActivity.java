package com.example.healthlink;

import android.app.AlertDialog;
import android.content.Intent;
import android.graphics.Bitmap;
import android.net.Uri;
import android.os.Bundle;
import android.provider.MediaStore;
import android.util.Log;
import android.view.Menu;
import android.view.MenuItem;
import android.view.View;
import android.widget.*;
import androidx.activity.result.ActivityResult;
import androidx.activity.result.ActivityResultLauncher;
import androidx.activity.result.contract.ActivityResultContracts;
import androidx.appcompat.app.ActionBarDrawerToggle;
import androidx.appcompat.app.AppCompatActivity;
import androidx.appcompat.widget.Toolbar;
import androidx.core.content.ContextCompat;
import androidx.drawerlayout.widget.DrawerLayout;
import androidx.recyclerview.widget.LinearLayoutManager;
import androidx.recyclerview.widget.RecyclerView;
import com.android.volley.Request;
import com.android.volley.toolbox.StringRequest;
import com.android.volley.toolbox.Volley;
import com.bumptech.glide.Glide;
import com.bumptech.glide.load.engine.DiskCacheStrategy;
import com.google.android.material.navigation.NavigationView;
import com.canhub.cropper.CropImageView;
import org.json.JSONArray;
import org.json.JSONObject;
import java.io.ByteArrayOutputStream;
import java.util.ArrayList;
import java.util.HashMap;
import java.util.List;
import java.util.Map;
import de.hdodenhof.circleimageview.CircleImageView;

public class ProfileActivity extends AppCompatActivity {
    private static final String TAG = "ProfileActivity";

    private DrawerLayout drawerLayout;
    private NavigationView navigationView;
    private Toolbar toolbar;
    private TextView profileName, tvFollowers, tvFollowing, profileBio, profileUsername, postsLabel, tvPostCount;
    private Button btnEditProfile, btnSaveCrop, btnCancelCrop;
    private Button btnVerifyDoctor;
    private CircleImageView profileImage;
    private RecyclerView profilePostsRecyclerView;
    private LinearLayout cropButtonsLayout, emptyPostsLayout;
    private CropImageView cropImageView;

    private SessionManager sessionManager;
    private String serverIp;
    private String URL_PROFILE;
    private String URL_UPLOAD;
    private String URL_FOLLOW_STATS;
    private String URL_USER_POSTS;

    private PostAdapter adapter;
    private List<Post> userPosts = new ArrayList<>();

    private ActivityResultLauncher<Intent> imagePickerLauncher;
    private ActionBarDrawerToggle toggle;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_profile);
        initializeComponents();
        setupToolbarAndDrawer();
        setupClickListeners();
        setupProfilePostsRecyclerView();

        if (sessionManager.isSessionValid()) {
            fetchProfileDetails();
            fetchFollowStats();
            fetchUserPosts();
        } else {
            Log.e(TAG, "Invalid session - redirecting to login");
            redirectToLogin();
        }
    }

    @Override
    public boolean onCreateOptionsMenu(Menu menu) {
        getMenuInflater().inflate(R.menu.profile_nav_menu, menu);
        return true;
    }

    @Override
    public boolean onOptionsItemSelected(MenuItem item) {
        if (toggle.onOptionsItemSelected(item)) {
            return true;
        }
        return super.onOptionsItemSelected(item);
    }

    private void initializeComponents() {
        sessionManager = new SessionManager(this);
        sessionManager.debugSession();
        serverIp = getString(R.string.server_ip);
        URL_PROFILE = "http://" + serverIp + "/healthlink1/api/get_profile_info.php";
        URL_UPLOAD = "http://" + serverIp + "/healthlink1/api/upload_profile_pic.php";
        URL_FOLLOW_STATS = "http://" + serverIp + "/healthlink1/api/get_follow_stats.php";
        URL_USER_POSTS = "http://" + serverIp + "/healthlink1/api/get_user_posts.php";

        toolbar = findViewById(R.id.profile_toolbar);
        drawerLayout = findViewById(R.id.drawer_layout);
        navigationView = findViewById(R.id.profile_nav_view);
        profileName = findViewById(R.id.profile_name);
        profileUsername = findViewById(R.id.profile_username);
        tvFollowers = findViewById(R.id.tv_followers);
        tvFollowing = findViewById(R.id.tv_following);
        tvPostCount = findViewById(R.id.tv_post_count);
        profileBio = findViewById(R.id.profile_bio);
        btnEditProfile = findViewById(R.id.btn_follow_or_edit);
        btnVerifyDoctor = findViewById(R.id.btn_verify_doctor);
        profileImage = findViewById(R.id.profile_image);
        profilePostsRecyclerView = findViewById(R.id.profile_posts_recycler_view);
        postsLabel = findViewById(R.id.posts_label);
        emptyPostsLayout = findViewById(R.id.empty_posts_layout);
        cropImageView = findViewById(R.id.cropImageView);
        cropButtonsLayout = findViewById(R.id.cropButtonsLayout);
        btnSaveCrop = findViewById(R.id.btn_save_crop);
        btnCancelCrop = findViewById(R.id.btn_cancel_crop);

        if (postsLabel != null) postsLabel.setVisibility(View.GONE);
        if (profilePostsRecyclerView != null) profilePostsRecyclerView.setVisibility(View.GONE);
        if (emptyPostsLayout != null) emptyPostsLayout.setVisibility(View.GONE);
    }

    private void setupToolbarAndDrawer() {
        setSupportActionBar(toolbar);
        getSupportActionBar().setTitle("Profile");
        getSupportActionBar().setDisplayHomeAsUpEnabled(true);
        getSupportActionBar().setHomeAsUpIndicator(R.drawable.ic_menu);

        toggle = new ActionBarDrawerToggle(
                this, drawerLayout, toolbar, R.string.navigation_drawer_open, R.string.navigation_drawer_close);
        drawerLayout.addDrawerListener(toggle);
        toggle.syncState();

        navigationView.setNavigationItemSelectedListener(item -> {
            drawerLayout.closeDrawers();
            int id = item.getItemId();
            if (id == R.id.nav_account_center) {
                startActivity(new Intent(this, AccountCenterActivity.class));
            } else if (id == R.id.nav_logout) {
                showLogoutDialog();
            }
            return true;
        });
    }

    private void setupClickListeners() {
        btnEditProfile.setOnClickListener(v ->
                startActivity(new Intent(this, EditProfileInfoActivity.class))
        );

        btnVerifyDoctor.setOnClickListener(v -> {
            Intent intent = new Intent(this, DoctorVerificationActivity.class);
            startActivity(intent);
        });

        if (btnSaveCrop != null) {
            btnSaveCrop.setOnClickListener(v -> {
                Bitmap croppedBitmap = cropImageView.getCroppedImage();
                if (croppedBitmap != null) {
                    uploadProfileImage(croppedBitmap);
                    hideCropView();
                } else {
                    Toast.makeText(this, "Failed to crop image", Toast.LENGTH_SHORT).show();
                    Log.e(TAG, "Cropped bitmap is null");
                }
            });
        }

        if (btnCancelCrop != null) {
            btnCancelCrop.setOnClickListener(v -> hideCropView());
        }

        imagePickerLauncher = registerForActivityResult(
                new ActivityResultContracts.StartActivityForResult(),
                this::handlePickedImage
        );

        profileImage.setOnClickListener(v -> {
            Intent intent = new Intent(Intent.ACTION_PICK, MediaStore.Images.Media.EXTERNAL_CONTENT_URI);
            imagePickerLauncher.launch(intent);
        });
    }

    private void setupProfilePostsRecyclerView() {
        if (profilePostsRecyclerView != null) {
            profilePostsRecyclerView.setLayoutManager(new LinearLayoutManager(this));
            adapter = new PostAdapter(this, userPosts, serverIp, sessionManager.getUserId());
            profilePostsRecyclerView.setAdapter(adapter);
            profilePostsRecyclerView.setBackgroundColor(ContextCompat.getColor(this, android.R.color.white));
        } else {
            Log.e(TAG, "RecyclerView is null! Check layout file.");
        }
    }

    private void handlePickedImage(ActivityResult result) {
        if (result.getResultCode() == RESULT_OK && result.getData() != null) {
            Uri pickedImageUri = result.getData().getData();
            showCropView(pickedImageUri);
        }
    }

    private void showCropView(Uri imageUri) {
        cropImageView.setImageUriAsync(imageUri);
        cropImageView.setVisibility(View.VISIBLE);
        if (cropButtonsLayout != null) cropButtonsLayout.setVisibility(View.VISIBLE);
        profileImage.setVisibility(View.GONE);
    }

    private void hideCropView() {
        cropImageView.clearImage();
        cropImageView.setVisibility(View.GONE);
        if (cropButtonsLayout != null) cropButtonsLayout.setVisibility(View.GONE);
        profileImage.setVisibility(View.VISIBLE);
    }

    private void fetchProfileDetails() {
        String userId = sessionManager.getUserIdAsString();
        if (userId == null) {
            Toast.makeText(this, "Session invalid, please login again", Toast.LENGTH_LONG).show();
            redirectToLogin();
            return;
        }

        StringRequest request = new StringRequest(Request.Method.POST, URL_PROFILE,
                response -> {
                    try {
                        JSONObject obj = new JSONObject(response);
                        if ("success".equals(obj.optString("status"))) {
                            JSONObject user = obj.optJSONObject("user");
                            if (user != null) updateProfileUI(user);
                        } else {
                            String msg = obj.optString("message");
                            if (msg.contains("invalid") || msg.contains("not found")) {
                                Toast.makeText(this, "Session expired, login again", Toast.LENGTH_LONG).show();
                                redirectToLogin();
                            } else {
                                Toast.makeText(this, msg, Toast.LENGTH_SHORT).show();
                            }
                        }
                    } catch (Exception e) {
                        Log.e(TAG, "Parse error", e);
                        Toast.makeText(this, "Error parsing profile data", Toast.LENGTH_SHORT).show();
                    }
                },
                error -> {
                    Log.e(TAG, "Network error", error);
                    Toast.makeText(this, "Network error loading profile", Toast.LENGTH_SHORT).show();
                }
        ) {
            @Override
            protected Map<String, String> getParams() {
                HashMap<String, String> params = new HashMap<>();
                params.put("uid", userId);
                return params;
            }
        };
        Volley.newRequestQueue(this).add(request);
    }

    private void fetchFollowStats() {
        String userId = sessionManager.getUserIdAsString();
        if (userId == null) return;

        StringRequest request = new StringRequest(Request.Method.POST, URL_FOLLOW_STATS,
                response -> {
                    try {
                        JSONObject obj = new JSONObject(response);
                        if ("success".equals(obj.optString("status"))) {
                            int followers = obj.optInt("followers", 0);
                            int following = obj.optInt("following", 0);
                            if (tvFollowers != null) tvFollowers.setText(String.valueOf(followers));
                            if (tvFollowing != null) tvFollowing.setText(String.valueOf(following));
                        }
                    } catch (Exception e) {
                        Log.e(TAG, "Error parsing follow stats", e);
                    }
                },
                error -> Log.e(TAG, "Network error in follow stats", error)
        ) {
            @Override
            protected Map<String, String> getParams() {
                HashMap<String, String> params = new HashMap<>();
                params.put("uid", userId);
                return params;
            }
        };
        Volley.newRequestQueue(this).add(request);
    }

    private void fetchUserPosts() {
        String userId = sessionManager.getUserIdAsString();
        if (userId == null) return;

        Log.d(TAG, "📡 Loading MY posts with proper like status...");
        StringRequest request = new StringRequest(Request.Method.POST, URL_USER_POSTS,
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
                                Log.d(TAG, "🔍 Post " + post.getId() +
                                        " - Likes: " + post.getLikeCount() +
                                        ", Is Liked: " + post.isLikedByCurrentUser() +
                                        ", Has AI Summary: " + post.hasAiSummary()) ;
                                     
                            }
                            if (tvPostCount != null) {
                                tvPostCount.setText(String.valueOf(userPosts.size()));
                            }
                            updatePostsUI();
                            if (adapter != null) adapter.notifyDataSetChanged();
                            Log.d(TAG, "✅ Loaded " + userPosts.size() + " of MY posts with correct like status");
                        } else {
                            String message = jsonResponse.optString("message", "Unknown error");
                            Log.e(TAG, "❌ Posts loading failed: " + message);
                        }
                    } catch (Exception e) {
                        Log.e(TAG, "❌ Error parsing my posts", e);
                    }
                },
                error -> {
                    Log.e(TAG, "❌ Network error loading my posts", error);
                    if (error.networkResponse != null) {
                        Log.e(TAG, "Status code: " + error.networkResponse.statusCode);
                        String errorBody = new String(error.networkResponse.data);
                        Log.e(TAG, "Error body: " + errorBody);
                    }
                }) {
            @Override
            protected Map<String, String> getParams() {
                Map<String, String> params = new HashMap<>();
                params.put("user_id", userId);
                params.put("current_user_id", userId);
                Log.d(TAG, "📤 Posts params: " + params);
                return params;
            }
        };
        Volley.newRequestQueue(this).add(request);
    }

    private void updatePostsUI() {
        if (userPosts.size() > 0) {
            if (postsLabel != null) postsLabel.setVisibility(View.VISIBLE);
            if (profilePostsRecyclerView != null) profilePostsRecyclerView.setVisibility(View.VISIBLE);
            if (emptyPostsLayout != null) emptyPostsLayout.setVisibility(View.GONE);
            Log.d(TAG, "✅ Showing " + userPosts.size() + " posts");
        } else {
            if (postsLabel != null) postsLabel.setVisibility(View.VISIBLE);
            if (profilePostsRecyclerView != null) profilePostsRecyclerView.setVisibility(View.GONE);
            if (emptyPostsLayout != null) emptyPostsLayout.setVisibility(View.VISIBLE);
            Log.d(TAG, "✅ Showing empty posts state");
        }
    }

    private Post createPostFromJson(JSONObject obj) throws Exception {
        Post post = new Post();
        post.setId(obj.optInt("id", 0));
        post.setUserId(obj.optInt("user_id", 0));
        post.setUserName(obj.optString("user_name", ""));
        post.setProfilePic(obj.optString("profile_pic", ""));

        String postDescription = obj.optString("post_description", "");
        if (postDescription != null && !postDescription.equals("null") && !postDescription.trim().isEmpty()) {
            post.setPostDescription(postDescription);
        }

        String postImage = obj.optString("post_image", "");
        if (postImage != null && !postImage.equals("null") && !postImage.trim().isEmpty()) {
            post.setPostImage(postImage);
        }

        post.setCreatedAt(obj.optString("created_at", ""));
        post.setLikeCount(obj.optInt("like_count", 0));
        post.setCommentCount(obj.optInt("comment_count", 0));

        boolean isLiked = obj.optBoolean("is_liked", false);
        post.setLikedByCurrentUser(isLiked);

       

        if (obj.has("tags") && !obj.isNull("tags")) {
            try {
                JSONArray tagsArray = obj.getJSONArray("tags");
                List<String> tagsList = new ArrayList<>();
                for (int i = 0; i < tagsArray.length(); i++) {
                    String tag = tagsArray.getString(i);
                    if (!tag.trim().isEmpty()) {
                        tagsList.add(tag.trim());
                    }
                }
              
            } catch (Exception e) {
                String tagsString = obj.optString("tags", "");
                if (!tagsString.trim().isEmpty()) {
                    String[] tagsArray = tagsString.split(",");
                    List<String> tagsList = new ArrayList<>();
                    for (String tag : tagsArray) {
                        if (!tag.trim().isEmpty()) {
                            tagsList.add(tag.trim());
                        }
                    }
                    
                }
            }
        }

        post.setCodeContent(obj.optString("code_content", ""));
        post.setCodeLanguage(obj.optString("code_language", ""));
        post.setCodeStatus(obj.optInt("code_status", 0));

        return post;
    }

    private void updateProfileUI(JSONObject user) {
        try {
            String displayName = user.optString("display_name", "Unknown User");
            String username = user.optString("username", "");
            String bio = user.optString("bio", "No bio available");

            if (profileName != null) profileName.setText(displayName);
            if (profileUsername != null) profileUsername.setText("@" + username);
            if (profileBio != null) profileBio.setText(bio);

            String profilePic = user.optString("profile_pic", "default_profile.jpg");
            String imageUrl = "http://" + serverIp + "/healthlink1/web/assets/img/profile/" + profilePic;

            if (profileImage != null) {
                Glide.with(this)
                        .load(imageUrl)
                        .circleCrop()
                        .diskCacheStrategy(DiskCacheStrategy.ALL)
                        .placeholder(R.drawable.profile_placeholder)
                        .error(R.drawable.profile_placeholder)
                        .into(profileImage);
            }

            String currentProfilePic = sessionManager.getProfilePic();
            if (!profilePic.equals(currentProfilePic)) {
                sessionManager.updateProfilePic(profilePic);
            }
        } catch (Exception e) {
            Log.e(TAG, "Error updating profile UI", e);
        }
    }

    private void uploadProfileImage(Bitmap bitmap) {
        String userId = sessionManager.getUserIdAsString();
        if (userId == null) {
            Toast.makeText(this, "Session expired. Login again.", Toast.LENGTH_LONG).show();
            redirectToLogin();
            return;
        }

        ByteArrayOutputStream baos = new ByteArrayOutputStream();
        bitmap.compress(Bitmap.CompressFormat.JPEG, 80, baos);
        final byte[] imageBytes = baos.toByteArray();

        MultipartRequest multipartRequest = new MultipartRequest(URL_UPLOAD,
                response -> {
                    try {
                        JSONObject obj = new JSONObject(response);
                        if ("success".equals(obj.optString("status"))) {
                            Toast.makeText(this, "Profile photo updated!", Toast.LENGTH_SHORT).show();
                            fetchProfileDetails();
                        } else {
                            String msg = obj.optString("message", "Upload failed");
                            Toast.makeText(this, msg, Toast.LENGTH_SHORT).show();
                        }
                    } catch (Exception e) {
                        Log.e(TAG, "Upload response parse error", e);
                        Toast.makeText(this, "Upload response error", Toast.LENGTH_SHORT).show();
                    }
                },
                error -> {
                    Log.e(TAG, "Upload network error", error);
                    Toast.makeText(this, "Network error during upload", Toast.LENGTH_LONG).show();
                }) {
            @Override
            protected Map<String, String> getParams() {
                Map<String, String> params = new HashMap<>();
                params.put("uid", userId);
                return params;
            }

            @Override
            protected Map<String, DataPart> getByteData() {
                Map<String, DataPart> params = new HashMap<>();
                String fileName = "profile_pic_" + System.currentTimeMillis() + ".jpg";
                params.put("profile_pic", new DataPart(fileName, imageBytes, "image/jpeg"));
                return params;
            }
        };
        Volley.newRequestQueue(this).add(multipartRequest);
    }

    private void redirectToLogin() {
        sessionManager.logout();
        Intent intent = new Intent(this, LoginActivity.class);
        intent.setFlags(Intent.FLAG_ACTIVITY_NEW_TASK | Intent.FLAG_ACTIVITY_CLEAR_TASK);
        startActivity(intent);
        finish();
    }

    private void showLogoutDialog() {
        new AlertDialog.Builder(this)
                .setTitle("Logging Out")
                .setMessage("Disconnecting from HealthLink. See you next time!")
                .setPositiveButton("Logout", (dialog, which) -> {
                    sessionManager.logout();
                    Intent intent = new Intent(this, LoginActivity.class);
                    intent.setFlags(Intent.FLAG_ACTIVITY_NEW_TASK | Intent.FLAG_ACTIVITY_CLEAR_TASK);
                    startActivity(intent);
                    finish();
                })
                .setNegativeButton("Cancel", null)
                .show();
    }

    @Override
    protected void onResume() {
        super.onResume();
        if (sessionManager.isSessionValid()) {
            fetchProfileDetails();
            fetchFollowStats();
            fetchUserPosts();
        } else {
            redirectToLogin();
        }
    }
}