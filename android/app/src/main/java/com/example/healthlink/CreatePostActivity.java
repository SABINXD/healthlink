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
import com.android.volley.Request;
import com.android.volley.toolbox.Volley;
import org.json.JSONException;
import org.json.JSONObject;
import java.io.ByteArrayOutputStream;
import java.io.InputStream;
import java.util.HashMap;
import java.util.Map;
public class CreatePostActivity extends AppCompatActivity {
    private static final String TAG = "CreatePostActivity";
    private static final int PICK_IMAGE_REQUEST = 1;
    private static final int PERMISSION_REQUEST_CODE = 100;
    private EditText etContent;
    private Button btnPost;
    private ImageButton btnImage;
    private ImageView mediaPreview;
    private CheckBox checkAnonymous;
    private Uri selectedImageUri = null;
    private byte[] imageBytes = null;
    private SessionManager sessionManager;
    private String serverIp;
    private int currentUserId;
    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_create_post);
        sessionManager = new SessionManager(this);
        serverIp = getString(R.string.server_ip);
        currentUserId = sessionManager.getUserId();
        if (!sessionManager.isLoggedIn()) {
            Toast.makeText(this, "Please login to create a post", Toast.LENGTH_SHORT).show();
            finish();
            return;
        }
        initializeViews();
        setupClickListeners();
    }
    private void initializeViews() {
        etContent = findViewById(R.id.et_content);
        btnPost = findViewById(R.id.btn_post);
        btnImage = findViewById(R.id.btn_image);
        mediaPreview = findViewById(R.id.media_preview);
        checkAnonymous = findViewById(R.id.checkSpoiler); // Added this line
        mediaPreview.setVisibility(View.GONE);
    }
    private void setupClickListeners() {
        btnImage.setOnClickListener(v -> {
            if (checkImagePermission()) {
                pickImage();
            }
        });
        btnPost.setOnClickListener(v -> createPost());
    }
    private boolean checkImagePermission() {
        if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.TIRAMISU) {
            if (ContextCompat.checkSelfPermission(this, Manifest.permission.READ_MEDIA_IMAGES) != PackageManager.PERMISSION_GRANTED) {
                ActivityCompat.requestPermissions(this, new String[]{Manifest.permission.READ_MEDIA_IMAGES}, PERMISSION_REQUEST_CODE);
                return false;
            }
        } else {
            if (ContextCompat.checkSelfPermission(this, Manifest.permission.READ_EXTERNAL_STORAGE) != PackageManager.PERMISSION_GRANTED) {
                ActivityCompat.requestPermissions(this, new String[]{Manifest.permission.READ_EXTERNAL_STORAGE}, PERMISSION_REQUEST_CODE);
                return false;
            }
        }
        return true;
    }
    private void pickImage() {
        Intent intent = new Intent(Intent.ACTION_PICK);
        intent.setType("image/*");
        startActivityForResult(Intent.createChooser(intent, "Select Image"), PICK_IMAGE_REQUEST);
    }
    @Override
    protected void onActivityResult(int requestCode, int resultCode, Intent data) {
        super.onActivityResult(requestCode, resultCode, data);
        if (requestCode == PICK_IMAGE_REQUEST && resultCode == RESULT_OK && data != null) {
            selectedImageUri = data.getData();
            try {
                processSelectedImage();
            } catch (Exception e) {
                Toast.makeText(this, "Image error: " + e.getMessage(), Toast.LENGTH_SHORT).show();
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
        int maxDim = Math.max(options.outWidth, options.outHeight);
        if (maxDim > 1200) sampleSize = maxDim / 1200;
        options.inSampleSize = sampleSize;
        options.inJustDecodeBounds = false;
        inputStream = getContentResolver().openInputStream(selectedImageUri);
        Bitmap bitmap = BitmapFactory.decodeStream(inputStream, null, options);
        inputStream.close();
        if (bitmap == null) throw new Exception("Bitmap decode failed");
        ByteArrayOutputStream baos = new ByteArrayOutputStream();
        bitmap.compress(Bitmap.CompressFormat.JPEG, 90, baos);
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
                Toast.makeText(this, "Permission denied. Cannot pick images.", Toast.LENGTH_SHORT).show();
                showPermissionDeniedDialog();
            }
        }
    }
    private void showPermissionDeniedDialog() {
        new androidx.appcompat.app.AlertDialog.Builder(this)
                .setTitle("Permission Required")
                .setMessage("This app needs permission to access your images to analyze them. Please grant the permission in settings.")
                .setPositiveButton("Go to Settings", (dialog, which) -> {
                    Intent intent = new Intent(android.provider.Settings.ACTION_APPLICATION_DETAILS_SETTINGS);
                    intent.setData(Uri.parse("package:" + getPackageName()));
                    startActivity(intent);
                })
                .setNegativeButton("Cancel", null)
                .show();
    }
    private void createPost() {
        String content = etContent.getText().toString().trim();
        boolean isAnonymous = checkAnonymous.isChecked();
        if (content.isEmpty()) {
            Toast.makeText(this, "Please describe your condition", Toast.LENGTH_SHORT).show();
            etContent.requestFocus();
            return;
        }
        // Removed image requirement check
        if (currentUserId == -1) {
            Toast.makeText(this, "User session expired. Please login again.", Toast.LENGTH_LONG).show();
            finish();
            return;
        }
        Log.d(TAG, "Creating post with:");
        Log.d(TAG, "User ID: " + currentUserId);
        Log.d(TAG, "Content: " + content);
        Log.d(TAG, "Has image: " + (imageBytes != null));
        if (imageBytes != null) {
            Log.d(TAG, "Image size: " + imageBytes.length + " bytes");
        }
        Log.d(TAG, "Anonymous: " + isAnonymous);
        String uploadUrl = "http://" + serverIp + "/healthlink/api/create_post.php";
        Toast.makeText(this, "Creating post...", Toast.LENGTH_SHORT).show();
        btnPost.setEnabled(false);
        btnPost.setText("Posting...");
        Map<String, String> params = new HashMap<>();
        params.put("user_id", String.valueOf(currentUserId));
        params.put("caption", content);
        params.put("is_anonymous", isAnonymous ? "1" : "0");
        params.put("post_privacy", isAnonymous ? "1" : "0");
        Log.d(TAG, "Sending parameters: " + params.toString());
        Map<String, VolleyMultipartRequest.DataPart> byteData = new HashMap<>();
        if (imageBytes != null) {
            byteData.put("post_img", new VolleyMultipartRequest.DataPart("post.jpg", imageBytes, "image/jpeg"));
        }
        VolleyMultipartRequest multipartRequest = new VolleyMultipartRequest(
                Request.Method.POST,
                uploadUrl,
                response -> {
                    btnPost.setEnabled(true);
                    btnPost.setText("✈️ Post");
                    String responseString = new String(response.data);
                    Log.d(TAG, "Post creation response: " + responseString);
                    try {
                        JSONObject jsonResponse = new JSONObject(responseString);
                        String status = jsonResponse.optString("status", "unknown");
                        if ("success".equals(status)) {
                            int postId = jsonResponse.optInt("post_id", -1);
                            String imageUrl = jsonResponse.optString("image_url", "");
                            Log.d(TAG, "Post created with ID: " + postId);
                            // Check for credit warning
                            if (jsonResponse.has("credit_warning") && jsonResponse.optBoolean("credit_warning")) {
                                Toast.makeText(this, "Post created! AI analysis requires more credits. You can retry later.", Toast.LENGTH_LONG).show();
                            } else {
                                Toast.makeText(this, "Post created successfully! AI analysis will be completed shortly.", Toast.LENGTH_SHORT).show();
                            }
                            finish();
                        } else {
                            String errorMessage = jsonResponse.optString("message", "Something went wrong");
                            Log.e(TAG, "Post creation failed: " + errorMessage);
                            Toast.makeText(this, "Error: " + errorMessage, Toast.LENGTH_LONG).show();
                        }
                    } catch (JSONException e) {
                        Log.e(TAG, "Failed to parse JSON response", e);
                        Log.e(TAG, "Raw response: " + responseString);
                        Toast.makeText(this, "Post created but response format error", Toast.LENGTH_SHORT).show();
                    }
                },
                error -> {
                    btnPost.setEnabled(true);
                    btnPost.setText(" Post");
                    String errorMsg = "Failed to create post";
                    Log.e(TAG, "Create post error: " + error.toString());
                    if (error.networkResponse != null) {
                        try {
                            String errorBody = new String(error.networkResponse.data);
                            Log.e(TAG, "heat response: " + errorBody);
                            try {
                                JSONObject errorJson = new JSONObject(errorBody);
                                String serverMessage = errorJson.optString("message", "Unknown error");
                                errorMsg = serverMessage;
                            } catch (JSONException e) {
                                Log.e(TAG, "Failed to parse error response", e);
                            }
                        } catch (Exception e) {
                            Log.e(TAG, "Could not parse error response", e);
                        }
                    } else {
                        errorMsg += " - Check your internet connection";
                    }
                    Toast.makeText(this, errorMsg, Toast.LENGTH_LONG).show();
                },
                new HashMap<>(),
                params,
                byteData
        );
        multipartRequest.setRetryPolicy(new com.android.volley.DefaultRetryPolicy(
                30000, 
                1,    
                1.0f  
        ));
        Volley.newRequestQueue(this).add(multipartRequest);
    }
}