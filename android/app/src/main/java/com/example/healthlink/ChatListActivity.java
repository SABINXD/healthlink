package com.example.healthlink;

import android.content.Intent;
import android.os.Bundle;
import android.util.Log;
import android.view.View;
import android.widget.EditText;
import android.widget.LinearLayout;
import android.widget.Toast;

import androidx.appcompat.app.AppCompatActivity;
import androidx.appcompat.widget.Toolbar;
import androidx.recyclerview.widget.LinearLayoutManager;
import androidx.recyclerview.widget.RecyclerView;
import androidx.swiperefreshlayout.widget.SwipeRefreshLayout;
import com.android.volley.Request;
import com.android.volley.toolbox.StringRequest;
import com.android.volley.toolbox.Volley;
import org.json.JSONArray;
import org.json.JSONException;
import org.json.JSONObject;
import java.util.ArrayList;
import java.util.HashMap;
import java.util.List;
import java.util.Map;

public class ChatListActivity extends AppCompatActivity {
    private static final String TAG = "ChatListActivity";
    private RecyclerView recyclerView;
    private SwipeRefreshLayout swipeRefreshLayout;
    private ChatListAdapter adapter;
    private SessionManager sessionManager;
    private String serverIp;
    private LinearLayout emptyStateLayout;
    private EditText searchEditText;
    private boolean isLoading = false;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_chat_list);

        // Initialize components
        sessionManager = new SessionManager(this);
        serverIp = getString(R.string.server_ip);

        // Check if user is logged in
        if (!sessionManager.isSessionValid()) {
            redirectToLogin();
            return;
        }

        // Initialize views
        Toolbar toolbar = findViewById(R.id.toolbar);
        setSupportActionBar(toolbar);
        if (getSupportActionBar() != null) {
            getSupportActionBar().setTitle("Messages");
            getSupportActionBar().setDisplayHomeAsUpEnabled(true);
        }

        recyclerView = findViewById(R.id.recyclerView);
        swipeRefreshLayout = findViewById(R.id.swipeRefreshLayout);
        emptyStateLayout = findViewById(R.id.empty_state_layout);
        searchEditText = findViewById(R.id.search_edit_text);

        // Setup RecyclerView
        adapter = new ChatListAdapter(this, new ArrayList<>());
        recyclerView.setAdapter(adapter);
        recyclerView.setLayoutManager(new LinearLayoutManager(this));

        // Setup SwipeRefreshLayout
        swipeRefreshLayout.setColorSchemeResources(
                android.R.color.holo_blue_bright,
                android.R.color.holo_green_light,
                android.R.color.holo_orange_light,
                android.R.color.holo_red_light
        );
        swipeRefreshLayout.setOnRefreshListener(this::loadChatList);

        // Set item click listener
        adapter.setOnItemClickListener((chatUser, position) -> {
            Intent intent = new Intent(ChatListActivity.this, ChatActivity.class);
            intent.putExtra("RECIPIENT_ID", chatUser.getUserId());
            intent.putExtra("RECIPIENT_USERNAME", chatUser.getUserName());
            intent.putExtra("RECIPIENT_PROFILE_PIC", chatUser.getProfilePic());
            startActivity(intent);
        });

        // Load chat list
        loadChatList();
        Log.d(TAG, "ChatListActivity created successfully");
    }

    private void redirectToLogin() {
        sessionManager.logout();
        Intent intent = new Intent(this, LoginActivity.class);
        intent.setFlags(Intent.FLAG_ACTIVITY_NEW_TASK | Intent.FLAG_ACTIVITY_CLEAR_TASK);
        startActivity(intent);
        finish();
    }

    private String cleanResponse(String response) {
        // If response is null or empty, return as is
        if (response == null || response.trim().isEmpty()) {
            return response;
        }

        // Remove any PHP warnings or notices that might be prepended to the JSON
        // Common patterns: "php", "PHP Notice:", "PHP Warning:", etc.
        String cleanedResponse = response;

        // If response starts with "php" (case-insensitive), remove it and any following whitespace
        if (response.toLowerCase().startsWith("php")) {
            // Find the first occurrence of '{' which should be the start of JSON
            int jsonStart = response.indexOf('{');
            if (jsonStart != -1) {
                cleanedResponse = response.substring(jsonStart);
            }
        }

        // If response starts with HTML or other non-JSON content
        if (cleanedResponse.trim().startsWith("<")) {
            // Find the first occurrence of '{' which should be the start of JSON
            int jsonStart = cleanedResponse.indexOf('{');
            if (jsonStart != -1) {
                cleanedResponse = cleanedResponse.substring(jsonStart);
            } else {
                // If no JSON found, return original response
                return response;
            }
        }

        return cleanedResponse;
    }

    private void loadChatList() {
        if (isLoading) {
            return;
        }
        isLoading = true;
        if (!swipeRefreshLayout.isRefreshing()) {
            swipeRefreshLayout.setRefreshing(true);
        }

        String url = "http://" + serverIp + "/healthlink/api/get_chat_list.php";
        Log.d(TAG, "Loading chat list from: " + url);

        StringRequest request = new StringRequest(Request.Method.POST, url,
                response -> {
                    isLoading = false;
                    swipeRefreshLayout.setRefreshing(false);
                    Log.d(TAG, "Raw chat list response: " + response);

                    try {
                        // Clean the response to remove any PHP warnings or HTML
                        String cleanedResponse = cleanResponse(response);
                        Log.d(TAG, "Cleaned response: " + cleanedResponse);

                        // First check if cleaned response is valid JSON
                        if (cleanedResponse == null || cleanedResponse.trim().isEmpty()) {
                            throw new JSONException("Empty response after cleaning");
                        }

                        JSONObject jsonResponse = new JSONObject(cleanedResponse);
                        String status = jsonResponse.optString("status", "");

                        if ("success".equals(status)) {
                            JSONArray chatListArray = jsonResponse.optJSONArray("chat_list");
                            List<ChatUser> chatUsers = new ArrayList<>();

                            if (chatListArray != null) {
                                // Parse JSON manually to avoid Gson issues
                                for (int i = 0; i < chatListArray.length(); i++) {
                                    try {
                                        JSONObject chatObj = chatListArray.getJSONObject(i);
                                        ChatUser chatUser = new ChatUser();
                                        chatUser.setUserId(chatObj.optInt("user_id", 0));
                                        chatUser.setUserName(chatObj.optString("user_name", ""));
                                        chatUser.setProfilePic(chatObj.optString("profile_pic", ""));
                                        chatUser.setLastMessage(chatObj.optString("last_message", ""));
                                        chatUser.setTimestamp(chatObj.optString("timestamp", ""));
                                        chatUser.setUnreadCount(chatObj.optInt("unread_count", 0));
                                        chatUsers.add(chatUser);
                                    } catch (JSONException e) {
                                        Log.e(TAG, "Error parsing chat user at index " + i, e);
                                    }
                                }
                            }

                            if (chatUsers.isEmpty()) {
                                recyclerView.setVisibility(View.GONE);
                                emptyStateLayout.setVisibility(View.VISIBLE);
                            } else {
                                recyclerView.setVisibility(View.VISIBLE);
                                emptyStateLayout.setVisibility(View.GONE);
                                adapter.updateChatList(chatUsers);
                                Log.d(TAG, "Loaded " + chatUsers.size() + " chats");
                            }
                        } else {
                            String errorMsg = jsonResponse.optString("message", "Unknown error");
                            Log.e(TAG, "Server error: " + errorMsg);
                            Toast.makeText(this, "Failed to load chat list: " + errorMsg, Toast.LENGTH_SHORT).show();
                        }
                    } catch (JSONException e) {
                        Log.e(TAG, "Error parsing chat list", e);
                        Toast.makeText(this, "Server returned invalid data. Please try again later.", Toast.LENGTH_SHORT).show();
                        // Show empty state on parse error
                        recyclerView.setVisibility(View.GONE);
                        emptyStateLayout.setVisibility(View.VISIBLE);
                    }
                },
                error -> {
                    isLoading = false;
                    swipeRefreshLayout.setRefreshing(false);
                    Log.e(TAG, "Error loading chat list", error);

                    if (error.networkResponse != null) {
                        Log.e(TAG, "Status code: " + error.networkResponse.statusCode);
                        try {
                            String responseBody = new String(error.networkResponse.data, "utf-8");
                            Log.e(TAG, "Response body: " + responseBody);
                            Toast.makeText(this, "Server error: " + responseBody, Toast.LENGTH_SHORT).show();
                        } catch (Exception e) {
                            Log.e(TAG, "Error parsing error response", e);
                        }
                    } else {
                        Toast.makeText(this, "Network error. Check your connection.", Toast.LENGTH_SHORT).show();
                    }

                    // Show empty state on network error
                    recyclerView.setVisibility(View.GONE);
                    emptyStateLayout.setVisibility(View.VISIBLE);
                }
        ) {
            @Override
            protected Map<String, String> getParams() {
                Map<String, String> params = new HashMap<>();
                int userId = sessionManager.getUserId();
                params.put("user_id", String.valueOf(userId));
                Log.d(TAG, "Sending params: " + params);
                return params;
            }
        };

        Volley.newRequestQueue(this).add(request);
    }

    @Override
    protected void onResume() {
        super.onResume();
        // Refresh chat list when returning to this activity
        loadChatList();
    }

    @Override
    public boolean onSupportNavigateUp() {
        finish();
        return true;
    }
}