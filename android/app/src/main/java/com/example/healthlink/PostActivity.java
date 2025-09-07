package com.example.healthlink;

import android.Manifest;
import android.content.Intent;
import android.content.pm.PackageManager;
import android.graphics.Bitmap;
import android.graphics.BitmapFactory;
import android.net.Uri;
import android.os.Build;
import android.os.Bundle;
import android.util.Log;
import android.view.View;
import android.widget.*;
import androidx.annotation.NonNull;
import androidx.appcompat.app.AppCompatActivity;
import androidx.core.app.ActivityCompat;
import androidx.core.content.ContextCompat;
import com.android.volley.NetworkResponse;
import com.android.volley.Request;
import com.android.volley.Response;
import com.android.volley.VolleyError;
import com.android.volley.toolbox.Volley;
import org.json.JSONException;
import org.json.JSONObject;
import java.io.ByteArrayOutputStream;
import java.io.InputStream;
import java.util.HashMap;
import java.util.Map;

public class PostActivity extends AppCompatActivity {
    private static final String TAG = "PostActivity";
    private static final int PICK_IMAGE_REQUEST = 1;
    private static final int PERMISSION_REQUEST_CODE = 101;

    private EditText etContent;
    private Button btnPost;
    private ImageButton btnImage;
    private ImageView mediaPreview;
    private CheckBox checkSpoiler;
    private Uri selectedImageUri = null;
    private byte[] imageBytes = null;
    private boolean isPosting = false;
    private SessionManager sessionManager;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_create_post);

        sessionManager = new SessionManager(this);
        if (!sessionManager.isLoggedIn()) {
            Toast.makeText(this, "Please log in first", Toast.LENGTH_LONG).show();
            finish();
            return;
        }

        initializeViews();
        setupClickListeners();
        Log.d(TAG, "Current user ID: " + sessionManager.getUserId());
    }

    private void initializeViews() {
        etContent = findViewById(R.id.et_content);
        btnPost = findViewById(R.id.btn_post);
        btnImage = findViewById(R.id.btn_image);
        mediaPreview = findViewById(R.id.media_preview);
        checkSpoiler = findViewById(R.id.checkSpoiler);
        mediaPreview.setVisibility(View.GONE);
    }

    private void setupClickListeners() {
        btnImage.setOnClickListener(v -> {
            if (hasImagePermission()) {
                pickImage();
            } else {
                requestImagePermission();
            }
        });

        btnPost.setOnClickListener(v -> createPost());
    }

    private boolean hasImagePermission() {
        if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.TIRAMISU) {
            return ContextCompat.checkSelfPermission(this, Manifest.permission.READ_MEDIA_IMAGES) == PackageManager.PERMISSION_GRANTED;
        } else {
            return ContextCompat.checkSelfPermission(this, Manifest.permission.READ_EXTERNAL_STORAGE) == PackageManager.PERMISSION_GRANTED;
        }
    }

    private void createPost() {
        if (isPosting) {
            return;
        }

        String content = etContent.getText().toString().trim();
        if (content.isEmpty()) {
            Toast.makeText(this, "Please describe your condition", Toast.LENGTH_SHORT).show();
            etContent.requestFocus();
            return;
        }

        if (imageBytes == null) {
            Toast.makeText(this, "Please select an image", Toast.LENGTH_SHORT).show();
            return;
        }

        if (!sessionManager.isLoggedIn()) {
            Toast.makeText(this, "Session expired. Please log in again.", Toast.LENGTH_LONG).show();
            finish();
            return;
        }

        int userId = sessionManager.getUserId();
        if (userId == -1) {
            Toast.makeText(this, "Invalid user session. Please log in again.", Toast.LENGTH_LONG).show();
            finish();
            return;
        }

        isPosting = true;
        String serverIp = getString(R.string.server_ip);
        String uploadUrl = "http://" + serverIp + "/healthlink1/api/create_post.php";

        btnPost.setEnabled(false);
        btnPost.setText("Posting...");

        Log.d(TAG, "Creating post with:");
        Log.d(TAG, "User ID: " + userId);
        Log.d(TAG, "Content: " + content);
        Log.d(TAG, "Image size: " + imageBytes.length + " bytes");
        Log.d(TAG, "Spoiler: " + checkSpoiler.isChecked());

        Map<String, String> params = new HashMap<>();
        params.put("caption", content);
        params.put("user_id", String.valueOf(userId));
        params.put("spoiler", checkSpoiler.isChecked() ? "1" : "0");

        Map<String, VolleyMultipartRequest.DataPart> byteData = new HashMap<>();
        byteData.put("post_img", new VolleyMultipartRequest.DataPart("post.jpg", imageBytes, "image/jpeg"));

        VolleyMultipartRequest multipartRequest = new VolleyMultipartRequest(
                Request.Method.POST,
                uploadUrl,
                new Response.Listener<NetworkResponse>() {
                    @Override
                    public void onResponse(NetworkResponse response) {
                        isPosting = false;
                        btnPost.setEnabled(true);
                        btnPost.setText("✈️ Post");

                        String responseString = new String(response.data);
                        Log.d(TAG, "Server response code: " + response.statusCode);
                        Log.d(TAG, "Server response: " + responseString);

                        try {
                            JSONObject jsonResponse = new JSONObject(responseString);
                            String status = jsonResponse.optString("status", "unknown");

                            if ("success".equals(status)) {
                                JSONObject postJson = jsonResponse.optJSONObject("post");
                                if (postJson != null) {
                                    Post newPost = parsePostFromJson(postJson);
                                    Log.d(TAG, "Post created with ID: " + newPost.getId());
                                    Log.d(TAG, "AI Summary: " + newPost.getAiSummary());
                                    Log.d(TAG, "Spoiler: " + newPost.isSpoiler());
                                }

                                Intent resultIntent = new Intent();
                                setResult(RESULT_OK, resultIntent);
                                finish();
                            } else {
                                String errorMessage = jsonResponse.optString("message", "Something went wrong");
                                Log.e(TAG, "Post creation failed: " + errorMessage);
                                Toast.makeText(PostActivity.this, "Error: " + errorMessage, Toast.LENGTH_LONG).show();
                            }
                        } catch (JSONException e) {
                            Log.e(TAG, "Failed to parse JSON response", e);
                            Log.e(TAG, "Raw response: " + responseString);

                            if (response.statusCode >= 200 && response.statusCode < 300) {
                                Intent resultIntent = new Intent();
                                setResult(RESULT_OK, resultIntent);
                                finish();
                            } else {
                                Toast.makeText(PostActivity.this, "Server response error", Toast.LENGTH_LONG).show();
                            }
                        }
                    }
                },
                new Response.ErrorListener() {
                    @Override
                    public void onErrorResponse(VolleyError error) {
                        isPosting = false;
                        btnPost.setEnabled(true);
                        btnPost.setText("✈️ Post");

                        String errorMsg = "Failed to create post";
                        Log.e(TAG, "Volley error: " + error.toString());

                        if (error.networkResponse != null) {
                            int statusCode = error.networkResponse.statusCode;
                            String errorBody = new String(error.networkResponse.data);
                            Log.e(TAG, "HTTP Error " + statusCode + ": " + errorBody);

                            try {
                                JSONObject errorJson = new JSONObject(errorBody);
                                String serverMessage = errorJson.optString("message", "Unknown error");
                                errorMsg = serverMessage;
                            } catch (JSONException e) {
                                Log.e(TAG, "Failed to parse error response", e);
                            }

                            switch (statusCode) {
                                case 400:
                                    errorMsg = "Invalid request data";
                                    break;
                                case 401:
                                    errorMsg = "Authentication failed. Please log in again.";
                                    break;
                                case 404:
                                    errorMsg = "Server endpoint not found";
                                    break;
                                case 500:
                                    errorMsg = "Server error. Please try again later";
                                    break;
                                case 413:
                                    errorMsg = "Image too large. Try a smaller image";
                                    break;
                                case 408:
                                    errorMsg = "Request timeout. Check your connection";
                                    break;
                                default:
                                    errorMsg = "Network error (HTTP " + statusCode + ")";
                            }
                        } else {
                            Log.e(TAG, "Network error with no response: " + error.toString());
                            errorMsg = "Connection failed. Check your internet connection";
                        }

                        Toast.makeText(PostActivity.this, errorMsg, Toast.LENGTH_LONG).show();
                    }
                },
                new HashMap<>(),
                params,
                byteData
        );

        multipartRequest.setRetryPolicy(new com.android.volley.DefaultRetryPolicy(
                30000,
                2,
                1.0f
        ));

        Volley.newRequestQueue(this).add(multipartRequest);
    }

    private Post parsePostFromJson(JSONObject postJson) throws JSONException {
        Post post = new Post();
        post.setId(postJson.getInt("id"));
        post.setUserId(postJson.getInt("user_id"));
        post.setUserName(postJson.optString("first_name") + " " + postJson.optString("last_name"));
        post.setUsername(postJson.optString("username"));
        post.setPostDescription(postJson.optString("post_text"));
        post.setPostImage(postJson.optString("post_img"));
        post.setProfilePic(postJson.optString("profile_pic"));
        post.setLikeCount(postJson.optInt("like_count", 0));
        post.setCommentCount(postJson.optInt("comment_count", 0));
        post.setCreatedAt(postJson.optString("created_at"));
        post.setCodeContent(postJson.optString("code_content"));
        post.setCodeLanguage(postJson.optString("code_language"));
        post.setCodeStatus(postJson.optInt("code_status", 0));
        // Parse spoiler value
        post.setSpoiler(postJson.optInt("spoiler", 0) == 1);
        return post;
    }

    private void requestImagePermission() {
        String[] permissions;
        if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.TIRAMISU) {
            permissions = new String[]{Manifest.permission.READ_MEDIA_IMAGES};
        } else {
            permissions = new String[]{Manifest.permission.READ_EXTERNAL_STORAGE};
        }

        ActivityCompat.requestPermissions(this, permissions, PERMISSION_REQUEST_CODE);
    }

    private void pickImage() {
        Intent intent = new Intent(Intent.ACTION_PICK);
        intent.setType("image/*");
        Intent chooser = Intent.createChooser(intent, "Select Image");
        startActivityForResult(chooser, PICK_IMAGE_REQUEST);
    }

    @Override
    protected void onActivityResult(int requestCode, int resultCode, Intent data) {
        super.onActivityResult(requestCode, resultCode, data);

        if (requestCode == PICK_IMAGE_REQUEST && resultCode == RESULT_OK && data != null && data.getData() != null) {
            selectedImageUri = data.getData();
            try {
                processSelectedImage();
            } catch (Exception e) {
                Log.e(TAG, "Error processing selected image", e);
                Toast.makeText(this, "Error loading image: " + e.getMessage(), Toast.LENGTH_SHORT).show();
                resetImageSelection();
            }
        }
    }

    private void processSelectedImage() throws Exception {
        InputStream inputStream = getContentResolver().openInputStream(selectedImageUri);
        BitmapFactory.Options options = new BitmapFactory.Options();
        options.inJustDecodeBounds = true;
        BitmapFactory.decodeStream(inputStream, null, options);
        inputStream.close();

        int sampleSize = 1;
        int maxDimension = Math.max(options.outWidth, options.outHeight);
        if (maxDimension > 1200) {
            sampleSize = maxDimension / 1200;
        }

        Log.d(TAG, "Original image size: " + options.outWidth + "x" + options.outHeight);
        Log.d(TAG, "Using sample size: " + sampleSize);

        options.inSampleSize = sampleSize;
        options.inJustDecodeBounds = false;

        inputStream = getContentResolver().openInputStream(selectedImageUri);
        Bitmap bitmap = BitmapFactory.decodeStream(inputStream, null, options);
        inputStream.close();

        if (bitmap == null) {
            throw new Exception("Failed to decode image");
        }

        ByteArrayOutputStream baos = new ByteArrayOutputStream();
        bitmap.compress(Bitmap.CompressFormat.JPEG, 85, baos);
        imageBytes = baos.toByteArray();

        Log.d(TAG, "Processed image size: " + imageBytes.length + " bytes");

        mediaPreview.setImageBitmap(bitmap);
        mediaPreview.setVisibility(View.VISIBLE);
    }

    private void resetImageSelection() {
        mediaPreview.setVisibility(View.GONE);
        imageBytes = null;
        selectedImageUri = null;
    }

    @Override
    public void onRequestPermissionsResult(int requestCode, @NonNull String[] permissions, @NonNull int[] grantResults) {
        super.onRequestPermissionsResult(requestCode, permissions, grantResults);
        if (requestCode == PERMISSION_REQUEST_CODE) {
            if (grantResults.length > 0 && grantResults[0] == PackageManager.PERMISSION_GRANTED) {
                pickImage();
            } else {
                Toast.makeText(this, "Permission needed to select images", Toast.LENGTH_SHORT).show();
            }
        }
    }
}