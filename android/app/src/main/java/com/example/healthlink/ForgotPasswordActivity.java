package com.example.healthlink;

import android.content.Intent;
import android.os.Bundle;
import android.text.TextUtils;
import android.view.View;
import android.widget.Button;
import android.widget.EditText;
import android.widget.ImageView;
import android.widget.TextView;
import android.widget.Toast;
import androidx.appcompat.app.AppCompatActivity;

public class ForgotPasswordActivity extends AppCompatActivity {
    private EditText emailEditText;
    private Button sendResetButton;
    private TextView backToSignInText;
    private ImageView backButton;
    private String userEmail;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_forgot_password);

        // Initialize NetworkUtils
        NetworkUtils.init(this);

        backButton = findViewById(R.id.back_button);
        emailEditText = findViewById(R.id.email_edit_text);
        sendResetButton = findViewById(R.id.send_reset_button);
        backToSignInText = findViewById(R.id.back_to_sign_in_text);

        backButton.setOnClickListener(v -> finish());
        sendResetButton.setOnClickListener(v -> sendResetLink());
        backToSignInText.setOnClickListener(v -> {
            startActivity(new Intent(this, SignInActivity.class));
            finish();
        });
    }

    private void sendResetLink() {
        userEmail = emailEditText.getText().toString().trim();

        // Reset errors
        emailEditText.setError(null);

        // Validate email
        if (TextUtils.isEmpty(userEmail)) {
            emailEditText.setError("Email is required");
            return;
        }
        if (!userEmail.contains("@")) {
            emailEditText.setError("Please enter a valid email address");
            return;
        }

        // Show progress
        sendResetButton.setEnabled(false);
        sendResetButton.setText("Sending...");

        NetworkUtils.sendResetCode(this, userEmail,
                new NetworkUtils.ApiResponseListener() {
                    @Override
                    public void onSuccess(String response) {
                        runOnUiThread(() -> {
                            sendResetButton.setEnabled(true);
                            sendResetButton.setText("Send Reset Code");
                            Toast.makeText(ForgotPasswordActivity.this,
                                    "Reset code sent to your email!", Toast.LENGTH_LONG).show();

                            // Navigate to verification activity
                            Intent intent = new Intent(ForgotPasswordActivity.this,
                                    TwoFactorVerificationActivity.class);
                            intent.putExtra("email", userEmail);
                            intent.putExtra("isPasswordReset", true);
                            startActivity(intent);
                            finish();
                        });
                    }

                    @Override
                    public void onError(String error) {
                        runOnUiThread(() -> {
                            sendResetButton.setEnabled(true);
                            sendResetButton.setText("Send Reset Code");
                            Toast.makeText(ForgotPasswordActivity.this,
                                    "Error: " + error, Toast.LENGTH_LONG).show();
                        });
                    }
                });
    }
}