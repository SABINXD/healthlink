package com.example.healthlink;

import android.content.Intent;
import android.os.Bundle;
import android.text.TextUtils;
import android.view.View;
import android.widget.Button;
import android.widget.EditText;
import android.widget.ImageView;
import android.widget.Toast;
import androidx.appcompat.app.AppCompatActivity;

public class ResetPasswordActivity extends AppCompatActivity {
    private EditText passwordEditText, confirmPasswordEditText;
    private Button resetButton;
    private ImageView backButton;
    private String userEmail;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_reset_password);

        NetworkUtils.init(this);

        userEmail = getIntent().getStringExtra("email");

        backButton = findViewById(R.id.back_button);
        passwordEditText = findViewById(R.id.password_edit_text);
        confirmPasswordEditText = findViewById(R.id.confirm_password_edit_text);
        resetButton = findViewById(R.id.reset_button);

        backButton.setOnClickListener(v -> finish());
        resetButton.setOnClickListener(v -> resetPassword());
    }

    private void resetPassword() {
        String password = passwordEditText.getText().toString().trim();
        String confirmPassword = confirmPasswordEditText.getText().toString().trim();

        // Reset errors
        passwordEditText.setError(null);
        confirmPasswordEditText.setError(null);

        // Validate inputs
        if (TextUtils.isEmpty(password)) {
            passwordEditText.setError("Password is required");
            return;
        }
        if (password.length() < 6) {
            passwordEditText.setError("Password must be at least 6 characters");
            return;
        }
        if (!password.equals(confirmPassword)) {
            confirmPasswordEditText.setError("Passwords don't match");
            return;
        }

        // Show progress
        resetButton.setEnabled(false);
        resetButton.setText("Resetting...");

        // Make API call
        NetworkUtils.resetPassword(this, userEmail, password,
                new NetworkUtils.ApiResponseListener() {
                    @Override
                    public void onSuccess(String response) {
                        runOnUiThread(() -> {
                            resetButton.setEnabled(true);
                            resetButton.setText("Reset Password");
                            Toast.makeText(ResetPasswordActivity.this,
                                    "Password reset successful!", Toast.LENGTH_SHORT).show();

                            // Navigate to login
                            Intent intent = new Intent(ResetPasswordActivity.this, SignInActivity.class);
                            intent.setFlags(Intent.FLAG_ACTIVITY_NEW_TASK | Intent.FLAG_ACTIVITY_CLEAR_TASK);
                            startActivity(intent);
                            finish();
                        });
                    }

                    @Override
                    public void onError(String error) {
                        runOnUiThread(() -> {
                            resetButton.setEnabled(true);
                            resetButton.setText("Reset Password");
                            Toast.makeText(ResetPasswordActivity.this,
                                    "Error: " + error, Toast.LENGTH_SHORT).show();
                        });
                    }
                });
    }
}