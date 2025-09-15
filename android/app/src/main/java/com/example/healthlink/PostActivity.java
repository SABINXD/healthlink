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
    private Button btnPost, btnRemoveImage;
    private ImageButton btnImage;
    private ImageView mediaPreview;
    private CheckBox checkSpoiler;
    private Spinner categorySpinner;
    private LinearLayout imagePreviewContainer, spoilerPreviewOverlay;
    private Uri selectedImageUri = null;
    private byte[] imageBytes = null;
    private boolean isPosting = false;
    private SessionManager sessionManager;
    private String selectedCategory = "";

    // Health categories
    private String[] categories = {
            "Select a category",
            "Mental Health",
            "Nutrition",
            "Fitness",
            "Chronic Conditions",
            "Parenting",
            "Aging",
            "Women's Health",
            "Men's Health",
            "Dermatology",
            "Cardiology",
            "Neurology",
            "Orthopedics",
            "General Health"
    };

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
        setupCategorySpinner();
        setupClickListeners();
        Log.d(TAG, "Current user ID: " + sessionManager.getUserId());
    }

    private void initializeViews() {
        etContent = findViewById(R.id.et_content);
        btnPost = findViewById(R.id.btn_post);
        btnImage = findViewById(R.id.btn_image);
        btnRemoveImage = findViewById(R.id.btn_remove_image);
        mediaPreview = findViewById(R.id.media_preview);
        checkSpoiler = findViewById(R.id.checkSpoiler);
        categorySpinner = findViewById(R.id.categorySpinner);
        imagePreviewContainer = findViewById(R.id.image_preview_container);
        spoilerPreviewOverlay = findViewById(R.id.spoiler_preview_overlay);

        imagePreviewContainer.setVisibility(View.GONE);
    }

    private void setupCategorySpinner() {
        ArrayAdapter<String> adapter = new ArrayAdapter<>(this, android.R.layout.simple_spinner_item, categories);
        adapter.setDropDownViewResource(android.R.layout.simple_spinner_dropdown_item);
        categorySpinner.setAdapter(adapter);

        categorySpinner.setOnItemSelectedListener(new AdapterView.OnItemSelectedListener() {
            @Override
            public void onItemSelected(AdapterView<?> parent, View view, int position, long id) {
                if (position > 0) {
                    selectedCategory = categories[position];
                } else {
                    selectedCategory = "";
                }
                Log.d(TAG, "Selected category: " + selectedCategory);
            }

            @Override
            public void onNothingSelected(AdapterView<?> parent) {
                selectedCategory = "";
            }
        });
    }

    private void setupClickListeners() {
        btnImage.setOnClickListener(v -> {
            if (hasImagePermission()) {
                pickImage();
            } else {
                requestImagePermission();
            }
        });

        btnRemoveImage.setOnClickListener(v -> {
            resetImageSelection();
            Toast.makeText(this, "Image removed", Toast.LENGTH_SHORT).show();
        });

        checkSpoiler.setOnCheckedChangeListener((buttonView, isChecked) -> {
            updateSpoilerPreview();
        });

        btnPost.setOnClickListener(v -> createPost());
    }

    private void updateSpoilerPreview() {
        if (imagePreviewContainer.getVisibility() == View.VISIBLE) {
            spoilerPreviewOverlay.setVisibility(checkSpoiler.isChecked() ? View.VISIBLE : View.GONE);
        }
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
            Toast.makeText(this, "Please write something to share", Toast.LENGTH_SHORT).show();
            etContent.requestFocus();
            return;
        }

        if (selectedCategory.isEmpty()) {
            Toast.makeText(this, "Please select a category", Toast.LENGTH_SHORT).show();
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
        String uploadUrl = "http://" + serverIp + "/healthlink/api/create_post.php";

        btnPost.setEnabled(false);
        btnPost.setText("Posting...");

        Log.d(TAG, "Creating post with:");
        Log.d(TAG, "User ID: " + userId);
        Log.d(TAG, "Content: " + content);
        Log.d(TAG, "Category: " + selectedCategory);
        Log.d(TAG, "Has image: " + (imageBytes != null));
        Log.d(TAG, "Spoiler: " + checkSpoiler.isChecked());

        Map<String, String> params = new HashMap<>();
        params.put("caption", content);
        params.put("user_id", String.valueOf(userId));
        params.put("category", selectedCategory);
        params.put("spoiler", checkSpoiler.isChecked() ? "1" : "0");
        params.put("has_image", imageBytes != null ? "1" : "0");

        Map<String, VolleyMultipartRequest.DataPart> byteData = new HashMap<>();
        if (imageBytes != null) {
            byteData.put("post_img", new VolleyMultipartRequest.DataPart("post.jpg", imageBytes, "image/jpeg"));
        }

        VolleyMultipartRequest multipartRequest = new VolleyMultipartRequest(
                Request.Method.POST,
                uploadUrl,
                new Response.Listener<NetworkResponse>() {
                    @Override
                    public void onResponse(NetworkResponse response) {
                        isPosting = false;
                        btnPost.setEnabled(true);
                        btnPost.setText("Share Experience");

                        String responseString = new String(response.data);
                        Log.d(TAG, "Server response: " + responseString);

                        try {
                            JSONObject jsonResponse = new JSONObject(responseString);
                            String status = jsonResponse.optString("status", "unknown");

                            if ("success".equals(status)) {
                                if (imageBytes != null) {
                                    Toast.makeText(PostActivity.this, "Post created! AI analysis will be generated automatically.", Toast.LENGTH_SHORT).show();
                                } else {
                                    Toast.makeText(PostActivity.this, "Post created successfully!", Toast.LENGTH_SHORT).show();
                                }

                                Intent resultIntent = new Intent();
                                setResult(RESULT_OK, resultIntent);
                                finish();
                            } else {
                                String errorMessage = jsonResponse.optString("message", "Something went wrong");
                                Toast.makeText(PostActivity.this, "Error: " + errorMessage, Toast.LENGTH_LONG).show();
                            }
                        } catch (JSONException e) {
                            Log.e(TAG, "Failed to parse JSON response", e);
                            Toast.makeText(PostActivity.this, "Post created successfully!", Toast.LENGTH_SHORT).show();
                            Intent resultIntent = new Intent();
                            setResult(RESULT_OK, resultIntent);
                            finish();
                        }
                    }
                },
                new Response.ErrorListener() {
                    @Override
                    public void onErrorResponse(VolleyError error) {
                        isPosting = false;
                        btnPost.setEnabled(true);
                        btnPost.setText("Share Experience");
                        Toast.makeText(PostActivity.this, "Failed to create post. Please try again.", Toast.LENGTH_LONG).show();
                    }
                },
                new HashMap<>(),
                params,
                byteData
        );

        Volley.newRequestQueue(this).add(multipartRequest);
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

        mediaPreview.setImageBitmap(bitmap);
        imagePreviewContainer.setVisibility(View.VISIBLE);
        updateSpoilerPreview();
    }

    private void resetImageSelection() {
        imagePreviewContainer.setVisibility(View.GONE);
        spoilerPreviewOverlay.setVisibility(View.GONE);
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