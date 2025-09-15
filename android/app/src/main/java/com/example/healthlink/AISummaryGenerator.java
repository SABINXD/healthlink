package com.example.healthlink;

import android.content.Context;
import android.graphics.Bitmap;
import android.graphics.BitmapFactory;
import android.util.Base64;
import android.util.Log;

import com.android.volley.Request;
import com.android.volley.Response;
import com.android.volley.VolleyError;
import com.android.volley.toolbox.JsonObjectRequest;
import com.android.volley.toolbox.Volley;

import org.json.JSONArray;
import org.json.JSONException;
import org.json.JSONObject;

import java.io.ByteArrayOutputStream;
import java.io.File;
import java.io.FileInputStream;
import java.io.IOException;
import java.util.HashMap;
import java.util.Map;

public class AISummaryGenerator {
    private static final String TAG = "AISummaryGenerator";
    private static final String API_KEY = "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";
    private static final String API_URL = "https://openrouter.ai/api/v1/chat/completions";

    public interface SummaryCallback {
        void onSuccess(String summary, boolean isMedicalReport);
        void onError(String errorMessage);
    }

    public static void generateSummary(Context context, String imagePath, String caption, SummaryCallback callback) {
        // First, check if the image exists
        File imageFile = new File(imagePath);
        if (!imageFile.exists()) {
            callback.onError("Image file not found");
            return;
        }

        // Load and compress the image
        Bitmap bitmap = decodeSampledBitmapFromFile(imageFile, 1024, 1024);
        if (bitmap == null) {
            callback.onError("Failed to load image");
            return;
        }

        // Convert bitmap to base64
        ByteArrayOutputStream byteArrayOutputStream = new ByteArrayOutputStream();
        bitmap.compress(Bitmap.CompressFormat.JPEG, 85, byteArrayOutputStream);
        byte[] byteArray = byteArrayOutputStream.toByteArray();
        String encodedImage = Base64.encodeToString(byteArray, Base64.DEFAULT);

        // Create the request payload
        try {
            JSONObject payload = new JSONObject();
            payload.put("model", "openai/gpt-4o");

            JSONArray messages = new JSONArray();
            JSONObject message = new JSONObject();
            message.put("role", "user");

            JSONArray content = new JSONArray();

            // Text part
            JSONObject textContent = new JSONObject();
            textContent.put("type", "text");
            textContent.put("text", "Please analyze this image and determine if it is a medical report or contains medical information. " +
                    "If it is NOT a medical report, respond with exactly: \"This does not appear to be a medical report.\" " +
                    "If it IS a medical report, provide a concise summary of the key medical findings in simple terms that a non-medical person can understand.");

            // Image part
            JSONObject imageContent = new JSONObject();
            imageContent.put("type", "image_url");
            JSONObject imageUrl = new JSONObject();
            imageUrl.put("url", "data:image/jpeg;base64," + encodedImage);
            imageContent.put("image_url", imageUrl);

            content.put(textContent);
            content.put(imageContent);
            message.put("content", content);
            messages.put(message);
            payload.put("messages", messages);
            payload.put("max_tokens", 300);

            // Create the request
            JsonObjectRequest request = new JsonObjectRequest(
                    Request.Method.POST,
                    API_URL,
                    payload,
                    new Response.Listener<JSONObject>() {
                        @Override
                        public void onResponse(JSONObject response) {
                            try {
                                JSONArray choices = response.getJSONArray("choices");
                                if (choices.length() > 0) {
                                    JSONObject firstChoice = choices.getJSONObject(0);
                                    JSONObject message = firstChoice.getJSONObject("message");
                                    String content = message.getString("content");

                                    // Check if it's a medical report
                                    boolean isMedicalReport = !content.equals("This does not appear to be a medical report.");

                                    // Format the summary
                                    String formattedSummary;
                                    if (isMedicalReport) {
                                        formattedSummary = "🔍 Medical Summary\n\n" + content;
                                    } else {
                                        formattedSummary = content;
                                    }

                                    callback.onSuccess(formattedSummary, isMedicalReport);
                                } else {
                                    callback.onError("No response from AI");
                                }
                            } catch (JSONException e) {
                                Log.e(TAG, "Error parsing AI response", e);
                                callback.onError("Error parsing AI response");
                            }
                        }
                    },
                    new Response.ErrorListener() {
                        @Override
                        public void onErrorResponse(VolleyError error) {
                            Log.e(TAG, "Error generating AI summary", error);

                            // Check for specific error codes
                            if (error.networkResponse != null) {
                                int statusCode = error.networkResponse.statusCode;
                                if (statusCode == 402) {
                                    // Payment required (insufficient credits)
                                    callback.onSuccess("🔍 Educational Note\n\n" +
                                            "For accurate medical analysis, please consult with a qualified healthcare professional.\n\n" +
                                            "⚠️ This app provides educational content only, not medical advice.", false);
                                    return;
                                }
                            }

                            callback.onError("Network error: " + error.getMessage());
                        }
                    }
            ) {
                @Override
                public Map<String, String> getHeaders() {
                    Map<String, String> headers = new HashMap<>();
                    headers.put("Content-Type", "application/json");
                    headers.put("Authorization", "Bearer " + API_KEY);
                    return headers;
                }
            };

            // Add the request to the queue
            Volley.newRequestQueue(context).add(request);

        } catch (JSONException e) {
            Log.e(TAG, "Error creating JSON payload", e);
            callback.onError("Error creating request");
        }
    }

    // Helper method to load a scaled down version of the image
    private static Bitmap decodeSampledBitmapFromFile(File file, int reqWidth, int reqHeight) {
        // First decode with inJustDecodeBounds=true to check dimensions
        final BitmapFactory.Options options = new BitmapFactory.Options();
        options.inJustDecodeBounds = true;

        try (FileInputStream fis = new FileInputStream(file)) {
            BitmapFactory.decodeStream(fis, null, options);
        } catch (IOException e) {
            Log.e(TAG, "Error reading image file", e);
            return null;
        }

        // Calculate inSampleSize
        options.inSampleSize = calculateInSampleSize(options, reqWidth, reqHeight);

        // Decode bitmap with inSampleSize set
        options.inJustDecodeBounds = false;

        try (FileInputStream fis = new FileInputStream(file)) {
            return BitmapFactory.decodeStream(fis, null, options);
        } catch (IOException e) {
            Log.e(TAG, "Error reading image file", e);
            return null;
        }
    }

    // Helper method to calculate the sample size
    private static int calculateInSampleSize(BitmapFactory.Options options, int reqWidth, int reqHeight) {
        // Raw height and width of image
        final int height = options.outHeight;
        final int width = options.outWidth;
        int inSampleSize = 1;

        if (height > reqHeight || width > reqWidth) {
            final int halfHeight = height / 2;
            final int halfWidth = width / 2;

            // Calculate the largest inSampleSize value that is a power of 2 and keeps both
            // height and width larger than the requested height and width.
            while ((halfHeight / inSampleSize) >= reqHeight
                    && (halfWidth / inSampleSize) >= reqWidth) {
                inSampleSize *= 2;
            }
        }

        return inSampleSize;
    }
}