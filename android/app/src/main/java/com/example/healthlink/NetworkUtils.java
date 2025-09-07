package com.example.healthlink;

import android.content.Context;
import android.os.AsyncTask;

import java.io.BufferedReader;
import java.io.BufferedWriter;
import java.io.InputStream;
import java.io.InputStreamReader;
import java.io.OutputStream;
import java.io.OutputStreamWriter;
import java.net.HttpURLConnection;
import java.net.URL;
import java.net.URLEncoder;
import java.nio.charset.StandardCharsets;

/**
 * Handles network requests to your PHP backend.
 * All methods now accept a Context so any Activity can call them.
 */
public class NetworkUtils {
    private static Context context;

    public static void init(Context ctx) {
        NetworkUtils.context = ctx.getApplicationContext();
    }

    // =============== API RESPONSE LISTENER =====================
    public interface ApiResponseListener {
        void onSuccess(String response);
        void onError(String error);
    }

    // =============== SIGN UP =====================
    public static void signUp(Context context, String firstName, String lastName, String email,
                              String password, String username, String accountType,
                              ApiResponseListener listener) {
        new PostTask(listener, "signup.php")
                .execute("first_name=" + firstName,
                        "last_name=" + lastName,
                        "email=" + email,
                        "password=" + password,
                        "username=" + username,
                        "account_type=" + accountType);
    }

    // =============== SEND RESET CODE =====================
    public static void sendResetCode(Context context, String userEmail, ApiResponseListener listener) {
        new PostTask(listener, "send_reset_code.php")
                .execute("email=" + userEmail);
    }

    // =============== VERIFY RESET CODE =====================
    public static void verifyResetCode(Context context, String userEmail, String verificationCode,
                                       ApiResponseListener listener) {
        new PostTask(listener, "verify_reset_code.php")
                .execute("email=" + userEmail,
                        "code=" + verificationCode);
    }

    // =============== RESET PASSWORD =====================
    public static void resetPassword(Context context, String userEmail, String password,
                                     ApiResponseListener listener) {
        new PostTask(listener, "reset_password.php")
                .execute("email=" + userEmail,
                        "password=" + password);
    }

    // =============== LOGIN =====================
    public static void login(Context context, String email, String password,
                             ApiResponseListener listener) {
        new PostTask(listener, "login.php")
                .execute("email=" + email,
                        "password=" + password);
    }

    // ======================================================
    // Generic AsyncTask for sending POST requests
    // ======================================================
    private static class PostTask extends AsyncTask<String, Void, String> {
        private final ApiResponseListener listener;
        private final String endpoint;

        PostTask(ApiResponseListener listener, String endpoint) {
            this.listener = listener;
            this.endpoint = endpoint;
        }

        @Override
        protected String doInBackground(String... params) {
            try {
                String serverIp = context.getString(R.string.server_ip);
                String urlString = "http://" + serverIp + "/healthlink/api/" + endpoint;
                URL url = new URL(urlString);

                HttpURLConnection connection = (HttpURLConnection) url.openConnection();
                connection.setRequestMethod("POST");
                connection.setDoOutput(true);
                connection.setDoInput(true);

                // Build POST data
                StringBuilder postData = new StringBuilder();
                for (int i = 0; i < params.length; i++) {
                    if (i > 0) postData.append("&");
                    String[] parts = params[i].split("=", 2);
                    postData.append(URLEncoder.encode(parts[0], "UTF-8"))
                            .append("=")
                            .append(URLEncoder.encode(parts[1], "UTF-8"));
                }

                // Send request
                OutputStream os = connection.getOutputStream();
                BufferedWriter writer = new BufferedWriter(new OutputStreamWriter(os, StandardCharsets.UTF_8));
                writer.write(postData.toString());
                writer.flush();
                writer.close();
                os.close();

                // Read response
                int responseCode = connection.getResponseCode();
                InputStream is = (responseCode == HttpURLConnection.HTTP_OK)
                        ? connection.getInputStream()
                        : connection.getErrorStream();

                BufferedReader reader = new BufferedReader(new InputStreamReader(is, StandardCharsets.UTF_8));
                StringBuilder response = new StringBuilder();
                String line;
                while ((line = reader.readLine()) != null) {
                    response.append(line);
                }
                reader.close();
                is.close();
                connection.disconnect();

                return response.toString();

            } catch (Exception e) {
                e.printStackTrace();
                return "Error: " + e.getMessage();
            }
        }

        @Override
        protected void onPostExecute(String result) {
            if (result.startsWith("Error:")) {
                listener.onError(result);
            } else {
                listener.onSuccess(result);
            }
        }
    }
}
