package com.example.healthlink;

import android.os.Bundle;
import android.util.Log;
import android.widget.Toast;
import androidx.appcompat.app.AppCompatActivity;
import androidx.appcompat.widget.SearchView;
import androidx.recyclerview.widget.LinearLayoutManager;
import androidx.recyclerview.widget.RecyclerView;
import com.android.volley.Request;
import com.android.volley.toolbox.StringRequest;
import com.android.volley.toolbox.Volley;
import org.json.JSONArray;
import org.json.JSONObject;
import java.io.UnsupportedEncodingException;
import java.net.URLEncoder;
import java.util.ArrayList;
import java.util.HashMap;
import java.util.List;
import java.util.Map;

public class SearchActivity extends AppCompatActivity {
    private static final String TAG = "SearchActivity";

    SearchView searchView;
    RecyclerView searchResultsRecyclerView;
    UserSearchAdapter adapter;
    List<User> userList = new ArrayList<>();
    String serverIp;
    String SEARCH_URL;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.search);

        initializeComponents();
        setupRecyclerView();
        setupSearchView();
    }

    private void initializeComponents() {
        serverIp = getString(R.string.server_ip);
        SEARCH_URL = "http://" + serverIp + "/healthlink1/api/search_users.php";

        Log.d(TAG, "✅ Search URL: " + SEARCH_URL);

        searchView = findViewById(R.id.searchView);
        searchResultsRecyclerView = findViewById(R.id.searchResultsRecyclerView);
    }

    private void setupRecyclerView() {
        searchResultsRecyclerView.setLayoutManager(new LinearLayoutManager(this));
        adapter = new UserSearchAdapter(this, userList);
        searchResultsRecyclerView.setAdapter(adapter);
        Log.d(TAG, "✅ RecyclerView setup complete");
    }

    private void setupSearchView() {
        searchView.setOnQueryTextListener(new SearchView.OnQueryTextListener() {
            @Override
            public boolean onQueryTextSubmit(String query) {
                if (query.trim().length() >= 2) {
                    searchUsers(query.trim());
                } else {
                    Toast.makeText(SearchActivity.this, "Enter at least 2 characters", Toast.LENGTH_SHORT).show();
                }
                return true;
            }

            @Override
            public boolean onQueryTextChange(String newText) {
                if (newText.trim().length() >= 2) {
                    searchUsers(newText.trim());
                } else if (newText.trim().isEmpty()) {
                    userList.clear();
                    adapter.notifyDataSetChanged();
                }
                return true;
            }
        });

        searchView.requestFocus();
        Log.d(TAG, "✅ SearchView setup complete");
    }

    private void searchUsers(String query) {
        Log.d(TAG, "🔍 Searching for: '" + query + "'");

        String encodedQuery = "";
        try {
            encodedQuery = URLEncoder.encode(query, "UTF-8");
        } catch (UnsupportedEncodingException e) {
            encodedQuery = query;
        }

        String fullUrl = SEARCH_URL + "?query=" + encodedQuery;
        Log.d(TAG, "📡 Full URL: " + fullUrl);

        StringRequest request = new StringRequest(
                Request.Method.GET,
                fullUrl,
                response -> {
                    Log.d(TAG, "✅ Raw response: " + response);
                    handleSearchResponse(response, query);
                },
                error -> {
                    Log.e(TAG, "❌ Network error: " + error.toString());
                    searchUsersWithPost(query);
                }
        );

        Volley.newRequestQueue(this).add(request);
    }

    private void searchUsersWithPost(String query) {
        Log.d(TAG, "🔄 Trying POST method for: '" + query + "'");

        StringRequest request = new StringRequest(
                Request.Method.POST,
                SEARCH_URL,
                response -> {
                    Log.d(TAG, "✅ POST response: " + response);
                    handleSearchResponse(response, query);
                },
                error -> {
                    Log.e(TAG, "❌ POST error: " + error.toString());
                    Toast.makeText(SearchActivity.this, "Search failed: " + error.getMessage(), Toast.LENGTH_LONG).show();
                }
        ) {
            @Override
            protected Map<String, String> getParams() {
                Map<String, String> params = new HashMap<>();
                params.put("query", query);
                Log.d(TAG, "📤 POST params: " + params.toString());
                return params;
            }
        };

        Volley.newRequestQueue(this).add(request);
    }

    private void handleSearchResponse(String response, String query) {
        try {
            JSONObject obj = new JSONObject(response);
            Log.d(TAG, "📋 Parsed JSON: " + obj.toString());

            if ("success".equals(obj.getString("status"))) {
                JSONArray usersArray = obj.getJSONArray("users");
                userList.clear();

                for (int i = 0; i < usersArray.length(); i++) {
                    JSONObject userObj = usersArray.getJSONObject(i);
                    User user = createUserFromJson(userObj);
                    userList.add(user);
                    Log.d(TAG, "✅ Added user: " + user.getDisplayName() + " (@" + user.getUsername() + ")");
                }

                adapter.notifyDataSetChanged();
                Log.d(TAG, "✅ Found " + userList.size() + " users for query: '" + query + "'");

                if (userList.size() == 0) {
                    Toast.makeText(this, "No users found for '" + query + "'", Toast.LENGTH_SHORT).show();
                }

            } else {
                String message = obj.optString("message", "Search failed");
                Toast.makeText(this, "❌ " + message, Toast.LENGTH_SHORT).show();
                Log.e(TAG, "Search failed: " + message);
            }

        } catch (Exception e) {
            Log.e(TAG, "❌ Error parsing response", e);
            Toast.makeText(this, "Error parsing search results", Toast.LENGTH_SHORT).show();
        }
    }

    private User createUserFromJson(JSONObject userObj) throws Exception {
        User user = new User();
        user.setId(userObj.getInt("id"));

        String firstName = userObj.optString("first_name", "");
        String lastName = userObj.optString("last_name", "");
        String username = userObj.getString("username");

        user.setFirstName(firstName);
        user.setLastName(lastName);
        user.setUsername(username);
        user.setBio(userObj.optString("bio", ""));
        user.setProfilePic(userObj.optString("profile_pic", "default_profile.jpg"));

        // Create proper display name
        String displayName = (firstName + " " + lastName).trim();
        if (displayName.isEmpty()) {
            displayName = username;
        }
        user.setDisplayName(displayName);

        Log.d(TAG, "✅ Created user: " + displayName + " (@" + username + ") with profile pic: " + user.getProfilePic());
        return user;
    }
}