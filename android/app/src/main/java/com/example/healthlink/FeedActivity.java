package com.example.healthlink;

import android.content.Intent;
import android.os.Bundle;
import android.util.Log;
import android.view.View;
import android.widget.Toast;
import androidx.appcompat.app.AppCompatActivity;
import androidx.recyclerview.widget.LinearLayoutManager;
import androidx.recyclerview.widget.RecyclerView;
import androidx.swiperefreshlayout.widget.SwipeRefreshLayout;
import com.android.volley.DefaultRetryPolicy;
import com.android.volley.Request;
import com.android.volley.toolbox.JsonObjectRequest;
import com.android.volley.toolbox.Volley;
import com.google.android.material.chip.Chip;
import com.google.android.material.chip.ChipGroup;
import com.google.android.material.floatingactionbutton.FloatingActionButton;
import org.json.JSONArray;
import org.json.JSONObject;
import java.util.ArrayList;
import java.util.Collections;
import java.util.Comparator;
import java.util.List;

public class FeedActivity extends AppCompatActivity {
    private static final String TAG = "FeedActivity";
    private static final int CREATE_POST_REQUEST = 1001;

    private RecyclerView recyclerView;
    private PostAdapter adapter;
    private List<Post> postList = new ArrayList<>();
    private String FEED_URL, FORUM_POSTS_URL;
    private SessionManager sessionManager;
    private SwipeRefreshLayout swipeRefreshLayout;
    private ChipGroup categoryChipGroup;
    private FloatingActionButton fabCreatePost;
    private boolean isLoadingFeed = false;
    private String selectedCategory = "all";

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.item_post);

        sessionManager = new SessionManager(this);
        String serverIp = getString(R.string.server_ip);
        int userId = sessionManager.getUserId();
        FEED_URL = "http://" + serverIp + "/healthlink/api/get_posts.php?user_id=" + userId;
        FORUM_POSTS_URL = "http://" + serverIp + "/healthlink/api/get_forum_posts.php";

        if (userId == -1) {
            Log.e(TAG, "User not logged in!");
            finish();
            return;
        }

        initializeViews();
        setupRecyclerView();
        setupSwipeRefresh();
        setupCategoryFilter();
        setupFab();
        testConnectivity();
        loadCombinedFeed();
    }

    private void initializeViews() {
        recyclerView = findViewById(R.id.recyclerFeed);
        swipeRefreshLayout = findViewById(R.id.swipeRefreshLayout);
        categoryChipGroup = findViewById(R.id.categoryChipGroup);
        fabCreatePost = findViewById(R.id.fabCreatePost);

        if (recyclerView == null) {
            Log.e(TAG, "RecyclerView not found!");
            return;
        }
    }

    private void setupRecyclerView() {
        recyclerView.setLayoutManager(new LinearLayoutManager(this));
        adapter = new PostAdapter(this, postList, sessionManager);
        recyclerView.setAdapter(adapter);
        Log.d(TAG, "RecyclerView setup complete");
    }

    private void setupSwipeRefresh() {
        if (swipeRefreshLayout != null) {
            swipeRefreshLayout.setColorSchemeResources(
                    R.color.primary,
                    R.color.primary_light,
                    R.color.secondary
            );

            swipeRefreshLayout.setOnRefreshListener(() -> {
                Log.d(TAG, "Swipe refresh triggered");
                loadCombinedFeed();
            });
        }
    }

    private void setupCategoryFilter() {
        if (categoryChipGroup != null) {
            categoryChipGroup.setOnCheckedStateChangeListener((group, checkedIds) -> {
                if (!checkedIds.isEmpty()) {
                    Chip selectedChip = findViewById(checkedIds.get(0));
                    selectedCategory = selectedChip.getTag().toString();
                    loadCombinedFeed();
                }
            });
        }
    }

    private void setupFab() {
        if (fabCreatePost != null) {
            fabCreatePost.setOnClickListener(v -> {
                Intent intent = new Intent(this, CreatePostActivity.class);
                startActivityForResult(intent, CREATE_POST_REQUEST);
            });
        }
    }

    private void testConnectivity() {
        Log.d(TAG, "Testing connectivity to: " + FEED_URL);
        Log.d(TAG, "Forum URL: " + FORUM_POSTS_URL);
    }

    private void loadCombinedFeed() {
        if (isLoadingFeed) {
            Log.d(TAG, "Feed loading already in progress");
            return;
        }

        isLoadingFeed = true;
        Log.d(TAG, "Loading combined feed (posts + forum)");

        if (swipeRefreshLayout != null && !swipeRefreshLayout.isRefreshing()) {
            swipeRefreshLayout.setRefreshing(true);
        }

        loadRegularPosts();
    }

    private void loadRegularPosts() {
        JsonObjectRequest request = new JsonObjectRequest(
                Request.Method.GET,
                FEED_URL,
                null,
                response -> {
                    Log.d(TAG, "Regular posts loaded");
                    parseRegularPosts(response);
                    loadForumPosts();
                },
                error -> {
                    Log.e(TAG, "Regular posts error: " + error.toString());
                    loadForumPosts();
                }
        );

        request.setRetryPolicy(new DefaultRetryPolicy(15000, 2, 1.0f));
        Volley.newRequestQueue(this).add(request);
    }

    private void loadForumPosts() {
        String forumUrl = FORUM_POSTS_URL + "?category=" + selectedCategory + "&user_id=" + sessionManager.getUserId();

        JsonObjectRequest request = new JsonObjectRequest(
                Request.Method.GET,
                forumUrl,
                null,
                response -> {
                    isLoadingFeed = false;
                    if (swipeRefreshLayout != null) {
                        swipeRefreshLayout.setRefreshing(false);
                    }
                    Log.d(TAG, "Forum posts loaded");
                    parseForumPosts(response);
                    sortPostsByTime();
                    adapter.notifyDataSetChanged();
                },
                error -> {
                    isLoadingFeed = false;
                    if (swipeRefreshLayout != null) {
                        swipeRefreshLayout.setRefreshing(false);
                    }
                    Log.e(TAG, "Forum posts error: " + error.toString());
                    adapter.notifyDataSetChanged();
                }
        );

        request.setRetryPolicy(new DefaultRetryPolicy(15000, 2, 1.0f));
        Volley.newRequestQueue(this).add(request);
    }

    private void parseRegularPosts(JSONObject response) {
        try {
            if ("success".equals(response.getString("status"))) {
                JSONArray postsArray = response.getJSONArray("posts");
                postList.clear();

                for (int i = 0; i < postsArray.length(); i++) {
                    JSONObject postObj = postsArray.getJSONObject(i);
                    Post post = createPostFromJson(postObj);
                    post.setPostType("regular");
                    postList.add(post);
                }
                Log.d(TAG, "Added " + postList.size() + " regular posts");
            }
        } catch (Exception e) {
            Log.e(TAG, "Error parsing regular posts", e);
        }
    }

    private void parseForumPosts(JSONObject response) {
        try {
            if ("success".equals(response.getString("status"))) {
                JSONArray forumArray = response.getJSONArray("posts");

                for (int i = 0; i < forumArray.length(); i++) {
                    JSONObject forumObj = forumArray.getJSONObject(i);
                    Post forumPost = createForumPostFromJson(forumObj);
                    forumPost.setPostType("forum");
                    postList.add(forumPost);
                }
                Log.d(TAG, "Added " + forumArray.length() + " forum posts");
                Log.d(TAG, "Total posts in feed: " + postList.size());
            }
        } catch (Exception e) {
            Log.e(TAG, "Error parsing forum posts", e);
        }
    }

    private Post createPostFromJson(JSONObject obj) throws Exception {
        Post post = new Post();

        post.setId(obj.getInt("id"));
        post.setUserId(obj.getInt("user_id"));
        post.setUserName(obj.getString("user_name"));
        post.setProfilePic(obj.optString("profile_pic", "default_profile.jpg"));
        post.setPostDescription(obj.optString("post_description", ""));
        post.setPostImage(obj.optString("post_image", ""));
        post.setLikeCount(obj.optInt("like_count", 0));
        post.setCommentCount(obj.optInt("comment_count", 0));
        post.setLikedByCurrentUser(obj.optInt("liked_by_user", 0) == 1);
        post.setCreatedAt(obj.optString("created_at", ""));
        post.setSpoiler(obj.optInt("spoiler", 0) == 1);
        post.setUnblurred(false);

        return post;
    }

    private Post createForumPostFromJson(JSONObject obj) throws Exception {
        Post post = new Post();

        post.setId(obj.getInt("id"));
        post.setUserId(obj.getInt("user_id"));
        post.setUserName(obj.getString("user_name"));
        post.setProfilePic(obj.optString("user_profile_pic", "default_profile.jpg"));

        String title = obj.optString("title", "");
        String content = obj.optString("content", "");
        post.setPostDescription(title.isEmpty() ? content : title + "\n\n" + content);
        post.setTitle(title);

        post.setPostImage(obj.optString("image_url", ""));
        post.setLikeCount(obj.optInt("like_count", 0));
        post.setCommentCount(obj.optInt("comment_count", 0));
        post.setLikedByCurrentUser(obj.optInt("liked_by_user", 0) == 1);
        post.setCreatedAt(obj.optString("created_at", ""));
        post.setCategory(obj.optString("category", "general"));
        post.setSpoiler(obj.optInt("spoiler", 0) == 1);
        post.setUnblurred(false);

        JSONArray tagsArray = obj.optJSONArray("tags");
        if (tagsArray != null) {
            String[] tags = new String[tagsArray.length()];
            for (int i = 0; i < tagsArray.length(); i++) {
                tags[i] = tagsArray.getString(i);
            }
            post.setTags(tags);
        }

        return post;
    }

    private void sortPostsByTime() {
        Collections.sort(postList, new Comparator<Post>() {
            @Override
            public int compare(Post p1, Post p2) {
                return p2.getCreatedAt().compareTo(p1.getCreatedAt());
            }
        });
    }

    @Override
    protected void onResume() {
        super.onResume();
        Log.d(TAG, "Activity resumed - refreshing feed");
        loadCombinedFeed();
    }

    @Override
    protected void onActivityResult(int requestCode, int resultCode, Intent data) {
        super.onActivityResult(requestCode, resultCode, data);
        if (requestCode == CREATE_POST_REQUEST && resultCode == RESULT_OK) {
            loadCombinedFeed();
        }
    }
}