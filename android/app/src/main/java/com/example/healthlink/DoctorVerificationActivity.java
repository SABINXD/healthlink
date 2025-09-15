package com.example.healthlink;

import android.Manifest;
import android.annotation.SuppressLint;
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
import com.google.android.material.button.MaterialButton;
import org.json.JSONException;
import org.json.JSONObject;
import java.io.ByteArrayOutputStream;
import java.io.InputStream;
import java.util.HashMap;
import java.util.Map;

public class DoctorVerificationActivity extends AppCompatActivity {
    private static final String TAG = "DoctorVerification";
    private static final int PERMISSION_REQUEST_CODE = 101;
    // Image pick requests
    private static final int PICK_CITIZENSHIP_FRONT = 1;
    private static final int PICK_CITIZENSHIP_BACK = 2;
    private static final int PICK_MEDICAL_CERT = 3;
    // Form fields
    private AutoCompleteTextView spinnerSpecialty;
    private EditText etExperience, etLicense, etPhone, etAddress, etCity, etCountry;
    private MaterialButton btnSubmit;
    // Changed to ImageView for gallery icons
    private ImageView btnCitizenshipFront, btnCitizenshipBack, btnMedicalCert;
    private ImageView ivCitizenshipFront, ivCitizenshipBack, ivMedicalCert;
    private CheckBox cbTerms;
    // Image data
    private Uri citizenshipFrontUri = null;
    private Uri citizenshipBackUri = null;
    private Uri medicalCertUri = null;
    private byte[] citizenshipFrontBytes = null;
    private byte[] citizenshipBackBytes = null;
    private byte[] medicalCertBytes = null;
    private boolean isSubmitting = false;
    private SessionManager sessionManager;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_doctor_verification);
        sessionManager = new SessionManager(this);
        if (!sessionManager.isLoggedIn()) {
            Toast.makeText(this, "Please log in first", Toast.LENGTH_LONG).show();
            finish();
            return;
        }
        initializeViews();
        setupSpinner();
        setupClickListeners();
        Log.d(TAG, "Current user ID: " + sessionManager.getUserId());
    }

    @SuppressLint("WrongViewCast")
    private void initializeViews() {
        // Form fields
        spinnerSpecialty = findViewById(R.id.spinnerSpecialty);
        etExperience = findViewById(R.id.etExperience);
        etLicense = findViewById(R.id.etLicense);
        etPhone = findViewById(R.id.etPhone);
        etAddress = findViewById(R.id.etAddress);
        etCity = findViewById(R.id.etCity);
        etCountry = findViewById(R.id.etCountry);
        // Buttons - Now using MaterialButton for submit and ImageView for gallery icons
        btnSubmit = findViewById(R.id.btnSubmit);
        btnCitizenshipFront = findViewById(R.id.btnUploadCitizenshipFront);
        btnCitizenshipBack = findViewById(R.id.btnUploadCitizenshipBack);
        btnMedicalCert = findViewById(R.id.btnUploadMedicalCertificate);
        // Image views
        ivCitizenshipFront = findViewById(R.id.ivCitizenshipFront);
        ivCitizenshipBack = findViewById(R.id.ivCitizenshipBack);
        ivMedicalCert = findViewById(R.id.ivMedicalCertificate);
        // Checkbox
        cbTerms = findViewById(R.id.cbTerms);
        // Initially hide image previews
        ivCitizenshipFront.setVisibility(View.GONE);
        ivCitizenshipBack.setVisibility(View.GONE);
        ivMedicalCert.setVisibility(View.GONE);
    }

    private void setupSpinner() {
        String[] specialties = {
                "Select your specialty",
                "Cardiology",
                "Dermatology",
                "Endocrinology",
                "Gastroenterology",
                "Neurology",
                "Oncology",
                "Pediatrics",
                "Psychiatry",
                "Radiology",
                "Surgery",
                "Other"
        };
        ArrayAdapter<String> adapter = new ArrayAdapter<>(this,
                android.R.layout.simple_dropdown_item_1line, specialties);
        spinnerSpecialty.setAdapter(adapter);
    }

    private void setupClickListeners() {
        btnCitizenshipFront.setOnClickListener(v -> {
            if (hasStoragePermission()) {
                pickImage(PICK_CITIZENSHIP_FRONT);
            } else {
                requestStoragePermission();
            }
        });
        btnCitizenshipBack.setOnClickListener(v -> {
            if (hasStoragePermission()) {
                pickImage(PICK_CITIZENSHIP_BACK);
            } else {
                requestStoragePermission();
            }
        });
        btnMedicalCert.setOnClickListener(v -> {
            if (hasStoragePermission()) {
                pickImage(PICK_MEDICAL_CERT);
            } else {
                requestStoragePermission();
            }
        });
        btnSubmit.setOnClickListener(v -> submitVerification());
    }

    private boolean hasStoragePermission() {
        if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.TIRAMISU) {
            return ContextCompat.checkSelfPermission(this, Manifest.permission.READ_MEDIA_IMAGES) == PackageManager.PERMISSION_GRANTED;
        } else {
            return ContextCompat.checkSelfPermission(this, Manifest.permission.READ_EXTERNAL_STORAGE) == PackageManager.PERMISSION_GRANTED;
        }
    }

    private void requestStoragePermission() {
        String[] permissions;
        if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.TIRAMISU) {
            permissions = new String[]{Manifest.permission.READ_MEDIA_IMAGES};
        } else {
            permissions = new String[]{Manifest.permission.READ_EXTERNAL_STORAGE};
        }
        ActivityCompat.requestPermissions(this, permissions, PERMISSION_REQUEST_CODE);
    }

    private void pickImage(int requestCode) {
        Intent intent = new Intent(Intent.ACTION_PICK);
        intent.setType("image/*");
        startActivityForResult(intent, requestCode);
    }

    @Override
    protected void onActivityResult(int requestCode, int resultCode, Intent data) {
        super.onActivityResult(requestCode, resultCode, data);
        if (resultCode == RESULT_OK && data != null && data.getData() != null) {
            try {
                Uri imageUri = data.getData();
                processSelectedImage(imageUri, requestCode);
            } catch (Exception e) {
                Log.e(TAG, "Error processing selected image", e);
                Toast.makeText(this, "Error loading image: " + e.getMessage(), Toast.LENGTH_SHORT).show();
            }
        }
    }

    private void processSelectedImage(Uri imageUri, int requestCode) throws Exception {
        InputStream inputStream = getContentResolver().openInputStream(imageUri);
        BitmapFactory.Options options = new BitmapFactory.Options();
        options.inJustDecodeBounds = true;
        BitmapFactory.decodeStream(inputStream, null, options);
        inputStream.close();
        // Calculate sample size to reduce image size
        int sampleSize = 1;
        int maxDimension = Math.max(options.outWidth, options.outHeight);
        if (maxDimension > 1200) {
            sampleSize = maxDimension / 1200;
        }
        Log.d(TAG, "Original image size: " + options.outWidth + "x" + options.outHeight);
        Log.d(TAG, "Using sample size: " + sampleSize);
        options.inSampleSize = sampleSize;
        options.inJustDecodeBounds = false;
        inputStream = getContentResolver().openInputStream(imageUri);
        Bitmap bitmap = BitmapFactory.decodeStream(inputStream, null, options);
        inputStream.close();
        if (bitmap == null) {
            throw new Exception("Failed to decode image");
        }
        ByteArrayOutputStream baos = new ByteArrayOutputStream();
        bitmap.compress(Bitmap.CompressFormat.JPEG, 85, baos);
        byte[] imageBytes = baos.toByteArray();
        Log.d(TAG, "Processed image size: " + imageBytes.length + " bytes");
        // Set the image data based on the request code
        switch (requestCode) {
            case PICK_CITIZENSHIP_FRONT:
                citizenshipFrontUri = imageUri;
                citizenshipFrontBytes = imageBytes;
                ivCitizenshipFront.setImageBitmap(bitmap);
                ivCitizenshipFront.setVisibility(View.VISIBLE);
                // Change the gallery icon to indicate image is selected
                btnCitizenshipFront.setImageResource(android.R.drawable.ic_menu_save);
                Toast.makeText(this, "Citizenship front uploaded", Toast.LENGTH_SHORT).show();
                break;
            case PICK_CITIZENSHIP_BACK:
                citizenshipBackUri = imageUri;
                citizenshipBackBytes = imageBytes;
                ivCitizenshipBack.setImageBitmap(bitmap);
                ivCitizenshipBack.setVisibility(View.VISIBLE);
                // Change the gallery icon to indicate image is selected
                btnCitizenshipBack.setImageResource(android.R.drawable.ic_menu_save);
                Toast.makeText(this, "Citizenship back uploaded", Toast.LENGTH_SHORT).show();
                break;
            case PICK_MEDICAL_CERT:
                medicalCertUri = imageUri;
                medicalCertBytes = imageBytes;
                ivMedicalCert.setImageBitmap(bitmap);
                ivMedicalCert.setVisibility(View.VISIBLE);
                // Change the gallery icon to indicate image is selected
                btnMedicalCert.setImageResource(android.R.drawable.ic_menu_save);
                Toast.makeText(this, "Medical certificate uploaded", Toast.LENGTH_SHORT).show();
                break;
        }
    }

    private void submitVerification() {
        if (isSubmitting) {
            return;
        }
        // Validate form
        if (!validateForm()) {
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
        isSubmitting = true;
        String serverIp = getString(R.string.server_ip);
        String uploadUrl = "http://" + serverIp + "/healthlink/api/doctor_verification.php";
        btnSubmit.setEnabled(false);
        btnSubmit.setText("Submitting...");
        Log.d(TAG, "Submitting verification with:");
        Log.d(TAG, "User ID: " + userId);
        Log.d(TAG, "Specialty: " + spinnerSpecialty.getText().toString());
        // Prepare form data
        Map<String, String> params = new HashMap<>();
        params.put("user_id", String.valueOf(userId));
        params.put("specialty", spinnerSpecialty.getText().toString());
        params.put("experience", etExperience.getText().toString().trim());
        params.put("license", etLicense.getText().toString().trim());
        params.put("phone", etPhone.getText().toString().trim());
        params.put("address", etAddress.getText().toString().trim());
        params.put("city", etCity.getText().toString().trim());
        params.put("country", etCountry.getText().toString().trim());
        // Prepare image data
        Map<String, DoctorVerificationRequest.DataPart> byteData = new HashMap<>();
        if (citizenshipFrontBytes != null) {
            byteData.put("citizenshipFront", new DoctorVerificationRequest.DataPart("citizenship_front.jpg", citizenshipFrontBytes, "image/jpeg"));
        }
        if (citizenshipBackBytes != null) {
            byteData.put("citizenshipBack", new DoctorVerificationRequest.DataPart("citizenship_back.jpg", citizenshipBackBytes, "image/jpeg"));
        }
        if (medicalCertBytes != null) {
            byteData.put("medicalCertificate", new DoctorVerificationRequest.DataPart("medical_certificate.jpg", medicalCertBytes, "image/jpeg"));
        }
        // Create multipart request using the custom DoctorVerificationRequest
        DoctorVerificationRequest multipartRequest = new DoctorVerificationRequest(
                Request.Method.POST,
                uploadUrl,
                new Response.Listener<NetworkResponse>() {
                    @Override
                    public void onResponse(NetworkResponse response) {
                        isSubmitting = false;
                        btnSubmit.setEnabled(true);
                        btnSubmit.setText("Submit Verification");
                        String responseString = new String(response.data);
                        Log.d(TAG, "Server response code: " + response.statusCode);
                        Log.d(TAG, "Server response: " + responseString);
                        try {
                            JSONObject jsonResponse = new JSONObject(responseString);
                            boolean status = jsonResponse.getBoolean("status");
                            String message = jsonResponse.getString("msg");
                            if (status) {
                                // Update user session to mark as doctor
                                sessionManager.setDoctorStatus(true);
                                Toast.makeText(DoctorVerificationActivity.this, message, Toast.LENGTH_LONG).show();
                                finish(); // Close activity and return to profile
                            } else {
                                Toast.makeText(DoctorVerificationActivity.this, message, Toast.LENGTH_LONG).show();
                            }
                        } catch (JSONException e) {
                            Log.e(TAG, "Failed to parse JSON response", e);
                            Toast.makeText(DoctorVerificationActivity.this, "Server response error", Toast.LENGTH_LONG).show();
                        }
                    }
                },
                new Response.ErrorListener() {
                    @Override
                    public void onErrorResponse(VolleyError error) {
                        isSubmitting = false;
                        btnSubmit.setEnabled(true);
                        btnSubmit.setText("Submit Verification");
                        String errorMsg = "Failed to submit verification";
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
                                    errorMsg = "Image too large. Try smaller images";
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
                        Toast.makeText(DoctorVerificationActivity.this, errorMsg, Toast.LENGTH_LONG).show();
                    }
                },
                params,
                byteData
        );
        multipartRequest.setRetryPolicy(new com.android.volley.DefaultRetryPolicy(
                30000, // 30 seconds timeout
                2,     // max retries
                1.0f   // backoff multiplier
        ));
        Volley.newRequestQueue(this).add(multipartRequest);
    }

    private boolean validateForm() {
        // Validate specialty
        if (spinnerSpecialty.getText().toString().equals("Select your specialty")) {
            Toast.makeText(this, "Please select your medical specialty", Toast.LENGTH_SHORT).show();
            return false;
        }
        // Validate experience
        if (etExperience.getText().toString().trim().isEmpty()) {
            etExperience.setError("Years of experience is required");
            return false;
        }
        // Validate license
        if (etLicense.getText().toString().trim().isEmpty()) {
            etLicense.setError("Medical license number is required");
            return false;
        }
        // Validate phone
        if (etPhone.getText().toString().trim().isEmpty()) {
            etPhone.setError("Phone number is required");
            return false;
        }
        // Validate address
        if (etAddress.getText().toString().trim().isEmpty()) {
            etAddress.setError("Address is required");
            return false;
        }
        // Validate city
        if (etCity.getText().toString().trim().isEmpty()) {
            etCity.setError("City is required");
            return false;
        }
        // Validate country
        if (etCountry.getText().toString().trim().isEmpty()) {
            etCountry.setError("Country is required");
            return false;
        }
        // Validate images
        if (citizenshipFrontBytes == null) {
            Toast.makeText(this, "Please upload citizenship document (front)", Toast.LENGTH_SHORT).show();
            return false;
        }
        if (citizenshipBackBytes == null) {
            Toast.makeText(this, "Please upload citizenship document (back)", Toast.LENGTH_SHORT).show();
            return false;
        }
        if (medicalCertBytes == null) {
            Toast.makeText(this, "Please upload medical certificate", Toast.LENGTH_SHORT).show();
            return false;
        }
        // Validate terms
        if (!cbTerms.isChecked()) {
            Toast.makeText(this, "Please agree to the terms and privacy policy", Toast.LENGTH_SHORT).show();
            return false;
        }
        return true;
    }

    @Override
    public void onRequestPermissionsResult(int requestCode, @NonNull String[] permissions, @NonNull int[] grantResults) {
        super.onRequestPermissionsResult(requestCode, permissions, grantResults);
        if (requestCode == PERMISSION_REQUEST_CODE) {
            if (grantResults.length > 0 && grantResults[0] == PackageManager.PERMISSION_GRANTED) {
                Toast.makeText(this, "Permission granted. You can now upload documents.", Toast.LENGTH_SHORT).show();
            } else {
                Toast.makeText(this, "Storage permission is required to upload documents", Toast.LENGTH_LONG).show();
            }
        }
    }
}