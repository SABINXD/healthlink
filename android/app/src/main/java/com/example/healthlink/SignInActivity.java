package com.example.healthlink;

import android.content.Intent;
import android.os.Bundle;
import android.text.TextUtils;
import android.view.View;
import android.widget.Button;
import android.widget.CheckBox;
import android.widget.EditText;
import android.widget.TextView;
import android.widget.Toast;
import androidx.appcompat.app.AppCompatActivity;
import org.json.JSONException;
import org.json.JSONObject;
import java.io.BufferedReader;
import java.io.BufferedWriter;
import java.io.InputStream;
import java.io.InputStreamReader;
import java.io.OutputStream;
import java.io.OutputStreamWriter;
import java.net.HttpURLConnection;
import java.net.URL;
import java.net.URLEncoder;

public class SignInActivity extends AppCompatActivity {
    private EditText emailInput, passwordInput;
    private Button signInBtn;
    private TextView signUpLink, forgotPasswordLink;
    private CheckBox rememberMeCheckbox;
    private SessionManager sessionManager;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_sign_in);

        // Initialize SessionManager
        sessionManager = new SessionManager(this);

        // Check if user is already logged in
        if (sessionManager.isLoggedIn()) {
            goToMainActivity();
            return;
        }

        // Connect UI elements
        emailInput = findViewById(R.id.email_edit_text);
        passwordInput = findViewById(R.id.password_edit_text);
        signInBtn = findViewById(R.id.sign_in_button);
        signUpLink = findViewById(R.id.sign_up_text);
        forgotPasswordLink = findViewById(R.id.forgot_password_text);
        rememberMeCheckbox = findViewById(R.id.remember_me_checkbox);

        // Set up button clicks
        signInBtn.setOnClickListener(v -> attemptLogin());
        signUpLink.setOnClickListener(v -> goToSignUpActivity());
        forgotPasswordLink.setOnClickListener(v -> goToForgotPasswordActivity());
    }

    private void attemptLogin() {
        // Get input values
        String email = emailInput.getText().toString().trim();
        String password = passwordInput.getText().toString().trim();

        // Reset errors
        emailInput.setError(null);
        passwordInput.setError(null);

        // Validate inputs
        if (TextUtils.isEmpty(email)) {
            emailInput.setError("Email required");
            return;
        }
        if (TextUtils.isEmpty(password)) {
            passwordInput.setError("Password required");
            return;
        }

        // Show progress
        signInBtn.setEnabled(false);
        signInBtn.setText("Signing In...");

        // Send login request to backend
        new Thread(() -> {
            try {
                URL url = new URL("http://192.168.1.88/healthlink/api/Login.php");
                HttpURLConnection conn = (HttpURLConnection) url.openConnection();
                conn.setRequestMethod("POST");
                conn.setDoOutput(true);
                conn.setDoInput(true);

                // Prepare POST data
                String postData = "email=" + URLEncoder.encode(email, "UTF-8") +
                        "&password=" + URLEncoder.encode(password, "UTF-8");

                OutputStream os = conn.getOutputStream();
                BufferedWriter writer = new BufferedWriter(new OutputStreamWriter(os, "UTF-8"));
                writer.write(postData);
                writer.flush();
                writer.close();
                os.close();

                InputStream is = conn.getInputStream();
                BufferedReader reader = new BufferedReader(new InputStreamReader(is, "UTF-8"));
                StringBuilder result = new StringBuilder();
                String line;
                while ((line = reader.readLine()) != null) {
                    result.append(line);
                }
                reader.close();
                is.close();
                conn.disconnect();

                runOnUiThread(() -> {
                    try {
                        JSONObject response = new JSONObject(result.toString().trim());
                        String status = response.optString("status", "");

                        if (status.equalsIgnoreCase("success")) {
                            // Parse user data
                            JSONObject user = response.getJSONObject("user");
                            int userId = user.getInt("user_id");
                            String userEmail = user.getString("email");
                            String username = user.getString("username");
                            String firstName = user.getString("firstName");
                            String lastName = user.getString("lastName");
                            String gender = user.getString("gender");
                            String profilePic = user.getString("profile_pic");

                            // Create session
                            sessionManager.createLoginSession(userId, userEmail, firstName + " " + lastName, false);

                            // Success! Show message and go to main
                            signInBtn.setEnabled(true);
                            signInBtn.setText("Sign In");
                            Toast.makeText(SignInActivity.this, "Welcome back, " + firstName + "!", Toast.LENGTH_SHORT).show();
                            goToMainActivity();
                        } else {
                            // Handle error
                            String errorMsg = response.optString("message", "Login failed");
                            signInBtn.setEnabled(true);
                            signInBtn.setText("Sign In");
                            Toast.makeText(SignInActivity.this, errorMsg, Toast.LENGTH_LONG).show();
                        }
                    } catch (JSONException e) {
                        // JSON parsing error
                        signInBtn.setEnabled(true);
                        signInBtn.setText("Sign In");
                        Toast.makeText(SignInActivity.this, "Response parsing error", Toast.LENGTH_SHORT).show();
                        e.printStackTrace();
                    }
                });
            } catch (Exception e) {
                runOnUiThread(() -> {
                    signInBtn.setEnabled(true);
                    signInBtn.setText("Sign In");
                    Toast.makeText(SignInActivity.this, "Error: " + e.getMessage(), Toast.LENGTH_LONG).show();
                });
            }
        }).start();
    }

    private void goToSignUpActivity() {
        startActivity(new Intent(this, SignUpActivity.class));
        finish(); // Close sign in so user can't go back
    }

    private void goToForgotPasswordActivity() {
        startActivity(new Intent(this, ForgotPasswordActivity.class));
    }

    private void goToMainActivity() {
        startActivity(new Intent(this, MainActivity.class));
        finish(); // Close sign in so user can't go back
    }
}