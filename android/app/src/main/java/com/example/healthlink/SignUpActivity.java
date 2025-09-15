package com.example.healthlink;

import android.content.Intent;
import android.os.Bundle;
import android.util.Patterns;
import android.view.View;
import android.widget.CheckBox;
import android.widget.TextView;
import android.widget.Toast;
import androidx.appcompat.app.AppCompatActivity;
import com.google.android.material.button.MaterialButton;
import com.google.android.material.textfield.TextInputEditText;
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

public class SignUpActivity extends AppCompatActivity {
    TextInputEditText firstNameEditText, lastNameEditText, emailEditText, passwordEditText;
    CheckBox mentalHealthBox, nutritionBox, fitnessBox, chronicBox, parentingBox, agingBox, termsBox;
    MaterialButton signUpButton;
    TextView signInText;
    View backButton;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.signup);

        firstNameEditText = findViewById(R.id.first_name_edit_text);
        lastNameEditText = findViewById(R.id.last_name_edit_text);
        emailEditText = findViewById(R.id.email_edit_text);
        passwordEditText = findViewById(R.id.password_edit_text);
        mentalHealthBox = findViewById(R.id.mental_health_checkbox);
        nutritionBox = findViewById(R.id.nutrition_checkbox);
        fitnessBox = findViewById(R.id.fitness_checkbox);
        chronicBox = findViewById(R.id.chronic_checkbox);
        parentingBox = findViewById(R.id.parenting_checkbox);
        agingBox = findViewById(R.id.aging_checkbox);
        termsBox = findViewById(R.id.terms_checkbox);
        signUpButton = findViewById(R.id.sign_up_button);
        signInText = findViewById(R.id.sign_in_text);
        backButton = findViewById(R.id.back_button);

        backButton.setOnClickListener(v -> onBackPressed());
        signInText.setOnClickListener(v -> {
            startActivity(new Intent(SignUpActivity.this, LoginActivity.class));
            overridePendingTransition(0, 0);
        });
        signUpButton.setOnClickListener(v -> attemptSignup());
    }

    private void attemptSignup() {
        String firstName = firstNameEditText.getText().toString().trim();
        String lastName = lastNameEditText.getText().toString().trim();
        String email = emailEditText.getText().toString().trim();
        String password = passwordEditText.getText().toString().trim();

        if (firstName.isEmpty()) {
            firstNameEditText.setError("Enter your first name");
            return;
        }
        if (lastName.isEmpty()) {
            lastNameEditText.setError("Enter your last name");
            return;
        }
        if (!Patterns.EMAIL_ADDRESS.matcher(email).matches()) {
            emailEditText.setError("Enter a valid email");
            return;
        }
        if (!isStrongPassword(password)) {
            passwordEditText.setError("Password must be 8+ chars with letters, numbers & special char");
            return;
        }
        if (!termsBox.isChecked()) {
            Toast.makeText(this, "You must agree to Terms & Privacy Policy", Toast.LENGTH_SHORT).show();
            return;
        }

        String username = email.split("@")[0];
        String gender = "not_specified";

        signUpButton.setEnabled(false);
        signUpButton.setText("Creating Account...");

        new Thread(() -> {
            try {
                URL url = new URL("http://192.168.1.10/healthlink/api/signup.php");
                HttpURLConnection conn = (HttpURLConnection) url.openConnection();
                conn.setRequestMethod("POST");
                conn.setDoOutput(true);
                conn.setDoInput(true);

                String postData = "firstName=" + URLEncoder.encode(firstName, "UTF-8") +
                        "&lastName=" + URLEncoder.encode(lastName, "UTF-8") +
                        "&email=" + URLEncoder.encode(email, "UTF-8") +
                        "&password=" + URLEncoder.encode(password, "UTF-8") +
                        "&username=" + URLEncoder.encode(username, "UTF-8") +
                        "&gender=" + URLEncoder.encode(gender, "UTF-8");

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
                        JSONObject obj = new JSONObject(result.toString());
                        String status = obj.optString("status", "");

                        if (status.equalsIgnoreCase("verify_sent")) {
                            Toast.makeText(SignUpActivity.this, "Verification email sent!", Toast.LENGTH_SHORT).show();
                            Intent intent = new Intent(SignUpActivity.this, EnterVerificationCodeActivity.class);
                            intent.putExtra("email", email);
                            startActivity(intent);
                            finish();
                        } else {
                            String errorMsg = obj.optString("error", "Unexpected server response");
                            Toast.makeText(SignUpActivity.this, errorMsg, Toast.LENGTH_LONG).show();
                        }
                    } catch (Exception e) {
                        Toast.makeText(SignUpActivity.this, "Unexpected server response", Toast.LENGTH_SHORT).show();
                    }

                    signUpButton.setEnabled(true);
                    signUpButton.setText("Sign Up");
                });
            } catch (Exception e) {
                runOnUiThread(() -> {
                    Toast.makeText(SignUpActivity.this, "Error: " + e.getMessage(), Toast.LENGTH_LONG).show();
                    signUpButton.setEnabled(true);
                    signUpButton.setText("Sign Up");
                });
            }
        }).start();
    }

    private boolean isStrongPassword(String password) {
        return password.length() >= 8 &&
                password.matches(".*[a-zA-Z].*") &&
                password.matches(".*[0-9].*") &&
                password.matches(".*[!@#$%^&*(),.?\":{}|<>].*");
    }
}