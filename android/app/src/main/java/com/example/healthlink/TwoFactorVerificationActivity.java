package com.example.healthlink;

import android.content.Intent;
import android.os.Bundle;
import android.os.CountDownTimer;
import android.text.Editable;
import android.text.TextWatcher;
import android.view.KeyEvent;
import android.view.View;
import android.widget.Button;
import android.widget.EditText;
import android.widget.ImageView;
import android.widget.TextView;
import android.widget.Toast;
import androidx.appcompat.app.AppCompatActivity;
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

public class TwoFactorVerificationActivity extends AppCompatActivity {
    private EditText[] codeInputs = new EditText[6];
    private Button verifyButton, resendButton;
    private TextView timerText, backToSignInText;
    private ImageView backButton;
    private CountDownTimer countDownTimer;
    private String userEmail;
    private boolean isPasswordReset = false;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_two_factor_verification);

        userEmail = getIntent().getStringExtra("email");
        isPasswordReset = getIntent().getBooleanExtra("isPasswordReset", false);

        initViews();
        setupClickListeners();
        setupCodeInputs();
        startResendTimer();
    }

    private void initViews() {
        backButton = findViewById(R.id.back_button);
        codeInputs[0] = findViewById(R.id.code_input_1);
        codeInputs[1] = findViewById(R.id.code_input_2);
        codeInputs[2] = findViewById(R.id.code_input_3);
        codeInputs[3] = findViewById(R.id.code_input_4);
        codeInputs[4] = findViewById(R.id.code_input_5);
        codeInputs[5] = findViewById(R.id.code_input_6);
        verifyButton = findViewById(R.id.verify_button);
        resendButton = findViewById(R.id.resend_button);
        timerText = findViewById(R.id.timer_text);
        backToSignInText = findViewById(R.id.back_to_sign_in_text);

        // Set email in subtitle
        TextView emailText = findViewById(R.id.email_text);
        if (userEmail != null) {
            emailText.setText(userEmail);
        }
    }

    private void setupClickListeners() {
        backButton.setOnClickListener(v -> finish());
        verifyButton.setOnClickListener(v -> verifyCode());
        resendButton.setOnClickListener(v -> resendCode());
        backToSignInText.setOnClickListener(v -> {
            startActivity(new Intent(this, SignInActivity.class));
            finish();
        });
    }

    private void setupCodeInputs() {
        for (int i = 0; i < codeInputs.length; i++) {
            final int index = i;
            codeInputs[i].addTextChangedListener(new TextWatcher() {
                @Override
                public void beforeTextChanged(CharSequence s, int start, int count, int after) {}

                @Override
                public void onTextChanged(CharSequence s, int start, int before, int count) {
                    if (s.length() == 1 && index < codeInputs.length - 1) {
                        codeInputs[index + 1].requestFocus();
                    }
                    checkCodeComplete();
                }

                @Override
                public void afterTextChanged(Editable s) {}
            });

            codeInputs[i].setOnKeyListener((v, keyCode, event) -> {
                if (keyCode == KeyEvent.KEYCODE_DEL && event.getAction() == KeyEvent.ACTION_DOWN) {
                    if (codeInputs[index].getText().toString().isEmpty() && index > 0) {
                        codeInputs[index - 1].requestFocus();
                        codeInputs[index - 1].setText("");
                    }
                }
                return false;
            });
        }
    }

    private void checkCodeComplete() {
        StringBuilder code = new StringBuilder();
        for (EditText input : codeInputs) {
            code.append(input.getText().toString());
        }
        verifyButton.setEnabled(code.length() == 6);
    }

    private void verifyCode() {
        StringBuilder code = new StringBuilder();
        for (EditText input : codeInputs) {
            code.append(input.getText().toString());
        }
        String verificationCode = code.toString();

        // Show progress
        verifyButton.setEnabled(false);
        verifyButton.setText("Verifying...");

        if (isPasswordReset) {
            // Verify reset code
            verifyResetCode(verificationCode);
        } else {
            // Verify email verification code
            verifyEmailCode(verificationCode);
        }
    }

    private void verifyEmailCode(String verificationCode) {
        new Thread(() -> {
            try {
                URL url = new URL("http://192.168.1.88/healthlink/api/verify.php");
                HttpURLConnection conn = (HttpURLConnection) url.openConnection();
                conn.setRequestMethod("POST");
                conn.setDoOutput(true);
                conn.setDoInput(true);

                String postData = "email=" + URLEncoder.encode(userEmail, "UTF-8") +
                        "&code=" + URLEncoder.encode(verificationCode, "UTF-8");

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
                        JSONObject obj = new JSONObject(result.toString().trim());
                        String status = obj.optString("status", "");

                        if (status.equalsIgnoreCase("verified")) {
                            Toast.makeText(TwoFactorVerificationActivity.this,
                                    "Email verified successfully!", Toast.LENGTH_SHORT).show();
                            // Navigate to login
                            startActivity(new Intent(TwoFactorVerificationActivity.this, SignInActivity.class));
                            finish();
                        } else {
                            String errorMsg = obj.optString("error", "Verification failed");
                            Toast.makeText(TwoFactorVerificationActivity.this,
                                    errorMsg, Toast.LENGTH_LONG).show();
                            verifyButton.setEnabled(true);
                            verifyButton.setText("Verify");
                            clearCodeInputs();
                        }
                    } catch (Exception e) {
                        Toast.makeText(TwoFactorVerificationActivity.this,
                                "Unexpected server response", Toast.LENGTH_SHORT).show();
                        verifyButton.setEnabled(true);
                        verifyButton.setText("Verify");
                        e.printStackTrace();
                    }
                });
            } catch (Exception e) {
                runOnUiThread(() -> {
                    Toast.makeText(TwoFactorVerificationActivity.this,
                            "Error: " + e.getMessage(), Toast.LENGTH_LONG).show();
                    verifyButton.setEnabled(true);
                    verifyButton.setText("Verify");
                });
            }
        }).start();
    }

    private void verifyResetCode(String verificationCode) {
        
    }

    private void resendCode() {
        resendButton.setEnabled(false);
        resendButton.setText("Sending...");

        new Thread(() -> {
            try {
                URL url = new URL("http://192.168.1.88/healthlink/api/resend_code.php");
                HttpURLConnection conn = (HttpURLConnection) url.openConnection();
                conn.setRequestMethod("POST");
                conn.setDoOutput(true);
                conn.setDoInput(true);

                String postData = "email=" + URLEncoder.encode(userEmail, "UTF-8");

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
                        JSONObject obj = new JSONObject(result.toString().trim());
                        String status = obj.optString("status", "");

                        if (status.equalsIgnoreCase("success")) {
                            Toast.makeText(TwoFactorVerificationActivity.this,
                                    "Verification code resent!", Toast.LENGTH_SHORT).show();
                            startResendTimer();
                        } else {
                            String errorMsg = obj.optString("error", "Failed to resend code");
                            Toast.makeText(TwoFactorVerificationActivity.this,
                                    errorMsg, Toast.LENGTH_LONG).show();
                        }
                    } catch (Exception e) {
                        Toast.makeText(TwoFactorVerificationActivity.this,
                                "Unexpected server response", Toast.LENGTH_SHORT).show();
                    }
                    resendButton.setEnabled(true);
                    resendButton.setText("Resend Code");
                });
            } catch (Exception e) {
                runOnUiThread(() -> {
                    Toast.makeText(TwoFactorVerificationActivity.this,
                            "Error: " + e.getMessage(), Toast.LENGTH_LONG).show();
                    resendButton.setEnabled(true);
                    resendButton.setText("Resend Code");
                });
            }
        }).start();
    }

    private void startResendTimer() {
        resendButton.setEnabled(false);
        countDownTimer = new CountDownTimer(60000, 1000) {
            @Override
            public void onTick(long millisUntilFinished) {
                timerText.setText("Resend code in " + (millisUntilFinished / 1000) + "s");
            }

            @Override
            public void onFinish() {
                timerText.setText("Didn't receive the code?");
                resendButton.setEnabled(true);
            }
        }.start();
    }

    private void clearCodeInputs() {
        for (EditText input : codeInputs) {
            input.setText("");
        }
        codeInputs[0].requestFocus();
    }

    @Override
    protected void onDestroy() {
        super.onDestroy();
        if (countDownTimer != null) {
            countDownTimer.cancel();
        }
    }
}