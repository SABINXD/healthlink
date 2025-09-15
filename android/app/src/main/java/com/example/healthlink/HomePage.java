package com.example.healthlink;
import android.content.Intent;
import android.os.Bundle;
import android.util.Log;
import android.view.View;
import android.widget.*;
import androidx.appcompat.app.AppCompatActivity;
import androidx.recyclerview.widget.LinearLayoutManager;
import androidx.recyclerview.widget.RecyclerView;
import androidx.swiperefreshlayout.widget.SwipeRefreshLayout;
import com.android.volley.Request;
import com.android.volley.toolbox.JsonObjectRequest;
import com.android.volley.toolbox.StringRequest;
import com.android.volley.toolbox.Volley;
import org.json.JSONArray;
import org.json.JSONObject;
import java.util.ArrayList;
import java.util.HashMap;
import java.util.List;
import java.util.Map;

public class HomePage extends AppCompatActivity {
    private static final String TAG = "HomePage";
    ImageView searchButton, postCreateButton, chatButton, appointmentsButton, notificationsButton;
    LinearLayout navPostContainer, navProfileContainer, navAppointmentsContainer, navNotificationsContainer;
    Button emergencyButton;
    RecyclerView recyclerFeed;
    SwipeRefreshLayout swipeRefreshLayout;
    PostAdapter adapter;
    List<Post> postList = new ArrayList<>();
    String FEED_URL;
    SessionManager sessionManager;
    private boolean isLoadingFeed = false;
    private TextView tvNotificationBadge;
    private int unreadNotificationCount = 0;
    private String URL_NOTIFICATIONS;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.homepage);
        initializeComponents();
        setupRecyclerView();
        setupSwipeRefresh();
        setupClickListeners();
        loadFeed();
        fetchNotifications();
    }

    private void initializeComponents() {
        sessionManager = new SessionManager(this);
        String serverIp = getString(R.string.server_ip);
        FEED_URL = "http://" + serverIp + "/healthlink/api/get_posts.php?user_id=" + sessionManager.getUserId();
        URL_NOTIFICATIONS = "http://" + serverIp + "/healthlink/api/get_notifications.php";
        Log.d(TAG, "Feed URL: " + FEED_URL);
        Log.d(TAG, "Current user ID: " + sessionManager.getUserId());
        recyclerFeed = findViewById(R.id.recyclerFeed);
        swipeRefreshLayout = findViewById(R.id.swipeRefreshLayout);
        searchButton = findViewById(R.id.nav_search);
        postCreateButton = findViewById(R.id.nav_post);
        chatButton = findViewById(R.id.send);
        appointmentsButton = findViewById(R.id.nav_appointments);
        notificationsButton = findViewById(R.id.nav_notifications);
        emergencyButton = findViewById(R.id.emergency_button);
        navPostContainer = findViewById(R.id.nav_post_container);
        navProfileContainer = findViewById(R.id.nav_profile_container);
        navAppointmentsContainer = findViewById(R.id.nav_appointments_container);
        navNotificationsContainer = findViewById(R.id.nav_notifications_container);
    }

    private void setupRecyclerView() {
        recyclerFeed.setLayoutManager(new LinearLayoutManager(this));
        // FIXED: Use the existing constructor with SessionManager
        adapter = new PostAdapter(this, postList, sessionManager);
        recyclerFeed.setAdapter(adapter);
    }

    private void setupSwipeRefresh() {
        swipeRefreshLayout.setColorSchemeResources(
                android.R.color.holo_blue_bright,
                android.R.color.holo_green_light,
                android.R.color.holo_orange_light,
                android.R.color.holo_red_light
        );
        swipeRefreshLayout.setOnRefreshListener(() -> {
            Log.d(TAG, "Swipe refresh triggered");
            loadFeed();
            fetchNotifications();
        });
    }

    private void setupClickListeners() {
        searchButton.setOnClickListener(v ->
                startActivity(new Intent(this, SearchActivity.class)));
        postCreateButton.setOnClickListener(v -> {
            Intent intent = new Intent(this, PostActivity.class);
            startActivityForResult(intent, 100);
        });
        chatButton.setOnClickListener(v -> {
            Log.d(TAG, "Chat button clicked, opening ChatListActivity");
            Intent intent = new Intent(this, ChatListActivity.class);
            startActivity(intent);
        });
        appointmentsButton.setOnClickListener(v ->
                startActivity(new Intent(this, AppointmentsActivity.class)));
        notificationsButton.setOnClickListener(v -> {
            Log.d(TAG, "Notifications button clicked, opening NotificationsActivity");
            Intent intent = new Intent(this, NotificationsActivity.class);
            startActivity(intent);
        });
        emergencyButton.setOnClickListener(v -> {
            Log.d(TAG, "Emergency button clicked, opening EmergencyActivity");
            Intent intent = new Intent(this, EmergencyActivity.class);
            startActivity(intent);
        });
        navPostContainer.setOnClickListener(v -> {
            Intent intent = new Intent(this, PostActivity.class);
            startActivityForResult(intent, 100);
        });
        navProfileContainer.setOnClickListener(v ->
                startActivity(new Intent(this, ProfileActivity.class)));
        navAppointmentsContainer.setOnClickListener(v ->
                startActivity(new Intent(this, AppointmentsActivity.class)));
        navNotificationsContainer.setOnClickListener(v -> {
            Log.d(TAG, "Notifications container clicked, opening NotificationsActivity");
            Intent intent = new Intent(this, NotificationsActivity.class);
            startActivity(intent);
        });
    }

    private void fetchNotifications() {
        String userId = sessionManager.getUserIdAsString();
        if (userId == null) return;
        StringRequest request = new StringRequest(Request.Method.POST, URL_NOTIFICATIONS,
                response -> {
                    try {
                        JSONObject obj = new JSONObject(response);
                        if ("success".equals(obj.optString("status"))) {
                            JSONArray notificationsArray = obj.getJSONArray("notifications");
                            unreadNotificationCount = 0;
                            for (int i = 0; i < notificationsArray.length(); i++) {
                                JSONObject notifObj = notificationsArray.getJSONObject(i);
                                boolean isRead = notifObj.getBoolean("is_read");
                                if (!isRead) {
                                    unreadNotificationCount++;
                                }
                            }
                            updateNotificationBadge();
                        }
                    } catch (Exception e) {
                        Log.e(TAG, "Error parsing notifications", e);
                    }
                },
                error -> Log.e(TAG, "Network error in notifications", error)
        ) {
            @Override
            protected Map<String, String> getParams() {
                Map<String, String> params = new HashMap<>();
                params.put("user_id", userId);
                return params;
            }
        };
        Volley.newRequestQueue(this).add(request);
    }

    private void updateNotificationBadge() {
        if (tvNotificationBadge != null) {
            if (unreadNotificationCount > 0) {
                tvNotificationBadge.setText(String.valueOf(unreadNotificationCount));
                tvNotificationBadge.setVisibility(View.VISIBLE);
            } else {
                tvNotificationBadge.setVisibility(View.GONE);
            }
        }
    }

    private void loadFeed() {
        if (isLoadingFeed) {
            Log.d(TAG, "Feed loading already in progress");
            return;
        }
        isLoadingFeed = true;
        Log.d(TAG, "Loading feed from: " + FEED_URL);
        if (!swipeRefreshLayout.isRefreshing()) {
            swipeRefreshLayout.setRefreshing(true);
        }
        JsonObjectRequest request = new JsonObjectRequest(
                Request.Method.GET,
                FEED_URL,
                null,
                response -> {
                    isLoadingFeed = false;
                    swipeRefreshLayout.setRefreshing(false);
                    Log.d(TAG, "Feed response received: " + response.toString());
                    parseFeedResponse(response);
                },
                error -> {
                    isLoadingFeed = false;
                    swipeRefreshLayout.setRefreshing(false);
                    Log.e(TAG, "Feed loading error: " + error.toString());
                    if (error.networkResponse != null) {
                        Log.e(TAG, "Error status code: " + error.networkResponse.statusCode);
                        String errorBody = new String(error.networkResponse.data);
                        Log.e(TAG, "Error response: " + errorBody);
                    }
                    Toast.makeText(this, "Failed to load posts", Toast.LENGTH_SHORT).show();
                }
        );
        Volley.newRequestQueue(this).add(request);
    }

    private void parseFeedResponse(JSONObject response) {
        try {
            Log.d(TAG, "Parsing feed response...");
            if (!"success".equals(response.getString("status"))) {
                String errorMsg = response.optString("message", "Unknown error");
                Log.e(TAG, "Feed status not success: " + errorMsg);
                Toast.makeText(this, "Error: " + errorMsg, Toast.LENGTH_SHORT).show();
                return;
            }
            JSONArray postsArray = response.getJSONArray("posts");
            postList.clear();
            Log.d(TAG, "Processing " + postsArray.length() + " posts");
            for (int i = 0; i < postsArray.length(); i++) {
                JSONObject obj = postsArray.getJSONObject(i);
                Post post = createPostFromJson(obj);
                postList.add(post);
                Log.d(TAG, "Post " + i + ": " + post.getUserName() + " - " + post.getPostDescription() +
                        " (AI Summary: " + (post.hasAiSummary() ? "Yes" : "No") +
                        ", Spoiler: " + post.isSpoiler() + ")");
            }
            adapter.notifyDataSetChanged();
            Log.d(TAG, "✅ Feed loaded successfully with " + postList.size() + " posts");
        } catch (Exception e) {
            Log.e(TAG, "Error parsing feed", e);
            Toast.makeText(this, "Error parsing feed data", Toast.LENGTH_SHORT).show();
        }
    }

    private Post createPostFromJson(JSONObject obj) throws Exception {
        Post post = new Post();
        post.setId(obj.getInt("id"));
        post.setUserId(obj.getInt("user_id"));
        post.setUserName(obj.getString("user_name"));
        post.setProfilePic(obj.optString("profile_pic", null));
        post.setPostDescription(obj.getString("post_description"));
        post.setPostImage(obj.optString("post_image", ""));
        post.setLikeCount(obj.optInt("like_count", 0));
        post.setCommentCount(obj.optInt("comment_count", 0));
        post.setLikedByCurrentUser(obj.optBoolean("is_liked", false));
        post.setCreatedAt(obj.optString("created_at", ""));
        post.setCodeContent(obj.optString("code_content", ""));
        post.setCodeLanguage(obj.optString("code_language", ""));
        post.setCodeStatus(obj.optInt("code_status", 0));
        // FIXED: Parse spoiler field from JSON
        boolean spoiler = obj.optInt("spoiler", 0) == 1;
        post.setSpoiler(spoiler);
        post.setUnblurred(false); // Initially blurred
        if (obj.has("tags") && obj.optJSONArray("tags") != null) {
            JSONArray tagsArray = obj.optJSONArray("tags");
            String[] tags = new String[tagsArray.length()];
            for (int i = 0; i < tagsArray.length(); i++) {
                tags[i] = tagsArray.optString(i, "");
            }
            post.setTags(tags);
        }
        Log.d(TAG, "📄 Created post: ID=" + post.getId() + ", User=" + post.getUserName() +
                ", HasAiSummary=" + post.hasAiSummary() + ", Spoiler=" + spoiler);
        return post;
    }

    @Override
    protected void onActivityResult(int requestCode, int resultCode, Intent data) {
        super.onActivityResult(requestCode, resultCode, data);
        if (requestCode == 100 && resultCode == RESULT_OK) {
            Log.d(TAG, "Returned from PostActivity with success - refreshing feed");
            loadFeed();
        }
    }

    @Override
    protected void onResume() {
        super.onResume();
        Log.d(TAG, "Activity resumed - refreshing feed");
        loadFeed();
        fetchNotifications();
    }
}