package com.example.healthlink;

import android.content.Intent;
import android.os.Bundle;
import android.util.Log;
import android.util.Patterns;
import android.widget.Button;
import android.widget.EditText;
import android.widget.TextView;
import android.widget.Toast;
import androidx.appcompat.app.AppCompatActivity;
import org.json.JSONObject;
import java.io.*;
import java.net.HttpURLConnection;
import java.net.URL;
import java.net.URLEncoder;

public class LoginActivity extends AppCompatActivity {
    EditText emailInput, passwordInput;
    TextView goToSignupBtn, forgotPasswordText;
    Button loginContinueBtn;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        SessionManager sessionManager = new SessionManager(this);
        if (sessionManager.isLoggedIn()) {
            Log.d("SESSION", "User already logged in with UID = " + sessionManager.getUserId());
            startActivity(new Intent(LoginActivity.this, HomePage.class));
            finish();
            return;
        }
        setContentView(R.layout.login);
        goToSignupBtn = findViewById(R.id.sign_up_text);
        forgotPasswordText = findViewById(R.id.forgot_password_text);
        emailInput = findViewById(R.id.email_edit_text);
        passwordInput = findViewById(R.id.password_edit_text);
        loginContinueBtn = findViewById(R.id.sign_in_button);

        goToSignupBtn.setOnClickListener(v -> {
            startActivity(new Intent(this, SignUpActivity.class));
            overridePendingTransition(0, 0);
        });

        forgotPasswordText.setOnClickListener(v -> {
            startActivity(new Intent(this, ForgetPasswordActivity.class));
            overridePendingTransition(0, 0);
        });

        loginContinueBtn.setOnClickListener(v -> {
            String email = emailInput.getText().toString().trim();
            String password = passwordInput.getText().toString().trim();
            if (!isValidEmail(email)) {
                emailInput.setError("Enter a valid email");
                return;
            }
            if (password.length() < 6) {
                passwordInput.setError("Password must be at least 6 characters");
                return;
            }
            performLogin(email, password);
        });
    }

    private void performLogin(String email, String password) {
        // Create final copies for lambda
        final String finalEmail = email;

        new Thread(() -> {
            HttpURLConnection conn = null;
            InputStream is = null;
            try {
                String serverIp = getString(R.string.server_ip);
                URL url = new URL("http://" + serverIp + "/healthlink/api/login.php");
                conn = (HttpURLConnection) url.openConnection();
                conn.setRequestMethod("POST");
                conn.setDoOutput(true);
                conn.setDoInput(true);
                conn.setConnectTimeout(10000); // 10 seconds timeout
                conn.setReadTimeout(15000); // 15 seconds timeout

                String postData = "email=" + URLEncoder.encode(email, "UTF-8") +
                        "&password=" + URLEncoder.encode(password, "UTF-8");

                OutputStream os = conn.getOutputStream();
                BufferedWriter writer = new BufferedWriter(new OutputStreamWriter(os, "UTF-8"));
                writer.write(postData);
                writer.flush();
                writer.close();
                os.close();

                int responseCode = conn.getResponseCode();
                Log.d("LOGIN_RESPONSE", "Response Code: " + responseCode);

                if (responseCode == HttpURLConnection.HTTP_OK) {
                    is = conn.getInputStream();
                    BufferedReader reader = new BufferedReader(new InputStreamReader(is, "UTF-8"));
                    StringBuilder result = new StringBuilder();
                    String line;
                    while ((line = reader.readLine()) != null) {
                        result.append(line);
                    }
                    reader.close();

                    String resp = result.toString().trim();
                    Log.d("LOGIN_RESPONSE", "Raw JSON: " + resp);

                    if (resp.isEmpty()) {
                        runOnUiThread(() -> Toast.makeText(LoginActivity.this, "Empty response from server", Toast.LENGTH_LONG).show());
                        return;
                    }

                    final String finalResponse = resp;
                    runOnUiThread(() -> {
                        try {
                            JSONObject json = new JSONObject(finalResponse);
                            String status = json.getString("status");
                            if (status.equalsIgnoreCase("success")) {
                                JSONObject user = json.getJSONObject("user");
                                Log.d("LOGIN_JSON", "User object: " + user.toString());
                                int userId = user.getInt("user_id");
                                String username = user.getString("username");
                                String firstName = user.getString("firstName");
                                String gender = user.optString("gender", "N/A");
                                String profilePicUrl = user.optString("profile_pic", "");
                                Log.d("LOGIN_JSON", "Parsed user_id = " + userId);

                                SessionManager sessionManager = new SessionManager(LoginActivity.this);
                                sessionManager.createSession(userId, finalEmail, username, profilePicUrl);
                                Log.d("SESSION", "Session saved with UID = " + sessionManager.getUserId() + ", Profile Pic: " + sessionManager.getProfilePic());

                                Toast.makeText(LoginActivity.this, "Welcome back, " + firstName, Toast.LENGTH_SHORT).show();
                                Intent intent = new Intent(LoginActivity.this, HomePage.class);
                                intent.putExtra("username", username);
                                intent.putExtra("firstName", firstName);
                                intent.putExtra("gender", gender);
                                startActivity(intent);
                                finish();
                            } else {
                                String errorMsg = json.optString("message", "Unknown error");
                                Toast.makeText(LoginActivity.this, "Login failed: " + errorMsg, Toast.LENGTH_LONG).show();
                                Log.w("LOGIN_FAIL", errorMsg);
                            }
                        } catch (Exception e) {
                            Toast.makeText(LoginActivity.this, "JSON error: " + e.getMessage(), Toast.LENGTH_LONG).show();
                            Log.e("LOGIN_JSON", "Parse error", e);
                        }
                    });
                } else {
                    // Handle non-OK response
                    String errorResponse = "";
                    if (conn.getErrorStream() != null) {
                        try (BufferedReader errorReader = new BufferedReader(new InputStreamReader(conn.getErrorStream(), "UTF-8"))) {
                            StringBuilder errorResult = new StringBuilder();
                            String errorLine;
                            while ((errorLine = errorReader.readLine()) != null) {
                                errorResult.append(errorLine);
                            }
                            errorResponse = errorResult.toString();
                        }
                    }
                    final String errorMessage = "Server returned HTTP " + responseCode + ": " + errorResponse;
                    runOnUiThread(() -> {
                        Toast.makeText(LoginActivity.this, errorMessage, Toast.LENGTH_LONG).show();
                        Log.e("LOGIN_NETWORK", errorMessage);
                    });
                }
            } catch (Exception e) {
                final String errorMessage = "Network error: " + e.getMessage();
                runOnUiThread(() -> {
                    Toast.makeText(LoginActivity.this, errorMessage, Toast.LENGTH_LONG).show();
                    Log.e("LOGIN_NETWORK", "Error", e);
                });
            } finally {
                // Clean up resources
                if (is != null) {
                    try {
                        is.close();
                    } catch (IOException e) {
                        Log.e("LOGIN_NETWORK", "Error closing input stream", e);
                    }
                }
                if (conn != null) {
                    conn.disconnect();
                }
            }
        }).start();
    }

    private boolean isValidEmail(String email) {
        return Patterns.EMAIL_ADDRESS.matcher(email).matches();
    }
}