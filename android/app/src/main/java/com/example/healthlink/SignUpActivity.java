package com.example.healthlink;

import android.content.Intent;
import android.os.Bundle;
import android.text.TextUtils;
import android.widget.Button;
import android.widget.CheckBox;
import android.widget.ImageView;
import android.widget.LinearLayout;
import android.widget.TextView;
import android.widget.Toast;
import androidx.appcompat.app.AppCompatActivity;
import com.google.android.material.textfield.TextInputEditText;
import com.google.android.material.textfield.TextInputLayout;
import java.util.ArrayList;
import java.util.List;

public class SignUpActivity extends AppCompatActivity {
    private TextInputEditText nameEditText, emailEditText, passwordEditText, confirmPasswordEditText;
    private TextInputLayout nameLayout, emailLayout, passwordLayout, confirmPasswordLayout;
    private CheckBox termsCheckBox;
    private CheckBox mentalHealthCheckBox, nutritionCheckBox, fitnessCheckBox,
            chronicCheckBox, parentingCheckBox, agingCheckBox;
    private Button signUpButton;
    private TextView signInText;
    private ImageView backButton;
    private LinearLayout socialButtonsLayout;
    private boolean isPasswordVisible = false;
    private boolean isConfirmPasswordVisible = false;
    private TextView passwordStrengthText;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_sign_up); 
        initViews();
        setupClickListeners();
    }

    private void initViews() {
        backButton = findViewById(R.id.back_button);
        nameEditText = findViewById(R.id.name_edit_text);
        emailEditText = findViewById(R.id.email_edit_text);
        passwordEditText = findViewById(R.id.password_edit_text);
        confirmPasswordEditText = findViewById(R.id.confirm_password_edit_text);
        nameLayout = findViewById(R.id.name_layout);
        emailLayout = findViewById(R.id.email_layout);
        passwordLayout = findViewById(R.id.password_layout);
        confirmPasswordLayout = findViewById(R.id.confirm_password_layout);
        termsCheckBox = findViewById(R.id.terms_checkbox);
        signUpButton = findViewById(R.id.sign_up_button);
        signInText = findViewById(R.id.sign_in_text);
        passwordStrengthText = findViewById(R.id.password_strength);

        mentalHealthCheckBox = findViewById(R.id.mental_health_checkbox);
        nutritionCheckBox = findViewById(R.id.nutrition_checkbox);
        fitnessCheckBox = findViewById(R.id.fitness_checkbox);
        chronicCheckBox = findViewById(R.id.chronic_checkbox);
        parentingCheckBox = findViewById(R.id.parenting_checkbox);
        agingCheckBox = findViewById(R.id.aging_checkbox);
    }

    private void setupClickListeners() {
        if (backButton != null) {
            backButton.setOnClickListener(v -> finish());
        }

        signUpButton.setOnClickListener(v -> performSignUp());

        if (signInText != null) {
            signInText.setOnClickListener(v -> {
                startActivity(new Intent(this, SignInActivity.class));
                finish();
            });
        }

        if (passwordEditText != null) {
            passwordEditText.addTextChangedListener(new android.text.TextWatcher() {
                @Override
                public void beforeTextChanged(CharSequence s, int start, int count, int after) {}

                @Override
                public void onTextChanged(CharSequence s, int start, int before, int count) {
                    updatePasswordStrength(s.toString());
                }

                @Override
                public void afterTextChanged(android.text.Editable s) {}
            });
        }
    }

    private void updatePasswordStrength(String password) {
        if (passwordStrengthText == null) return;

        if (password.length() < 8) {
            passwordStrengthText.setText("Weak");
            passwordStrengthText.setTextColor(getResources().getColor(R.color.danger));
        } else if (password.matches(".*[A-Z].*") && password.matches(".*[a-z].*") &&
                password.matches(".*\\d.*") && password.length() >= 8) {
            passwordStrengthText.setText("Strong");
            passwordStrengthText.setTextColor(getResources().getColor(R.color.success));
        } else {
            passwordStrengthText.setText("Medium");
            passwordStrengthText.setTextColor(getResources().getColor(R.color.warning));
        }
    }

    private void performSignUp() {
        String name = nameEditText != null ? nameEditText.getText().toString().trim() : "";
        String email = emailEditText != null ? emailEditText.getText().toString().trim() : "";
        String password = passwordEditText != null ? passwordEditText.getText().toString().trim() : "";
        String confirmPassword = confirmPasswordEditText != null ? confirmPasswordEditText.getText().toString().trim() : "";

        if (nameLayout != null) nameLayout.setError(null);
        if (emailLayout != null) emailLayout.setError(null);
        if (passwordLayout != null) passwordLayout.setError(null);
        if (confirmPasswordLayout != null) confirmPasswordLayout.setError(null);

        if (TextUtils.isEmpty(name)) {
            if (nameLayout != null) nameLayout.setError("Full name is required");
            return;
        }

        if (TextUtils.isEmpty(email)) {
            if (emailLayout != null) emailLayout.setError("Email is required");
            return;
        }

        if (!android.util.Patterns.EMAIL_ADDRESS.matcher(email).matches()) {
            if (emailLayout != null) emailLayout.setError("Please enter a valid email");
            return;
        }

        if (TextUtils.isEmpty(password)) {
            if (passwordLayout != null) passwordLayout.setError("Password is required");
            return;
        }

        if (password.length() < 8) {
            if (passwordLayout != null) passwordLayout.setError("Password must be at least 8 characters");
            return;
        }

        if (TextUtils.isEmpty(confirmPassword)) {
            if (confirmPasswordLayout != null) confirmPasswordLayout.setError("Please confirm your password");
            return;
        }

        if (!password.equals(confirmPassword)) {
            if (confirmPasswordLayout != null) confirmPasswordLayout.setError("Passwords do not match");
            return;
        }

        if (termsCheckBox != null && !termsCheckBox.isChecked()) {
            Toast.makeText(this, "You must agree to the terms and conditions", Toast.LENGTH_SHORT).show();
            return;
        }

        List<String> healthInterests = getSelectedHealthInterests();
        Toast.makeText(this, "Account created successfully!", Toast.LENGTH_LONG).show();
        startActivity(new Intent(this, SignInActivity.class)); // Changed to go to SignInActivity first
        finish();
    }

    private List<String> getSelectedHealthInterests() {
        List<String> interests = new ArrayList<>();
        if (mentalHealthCheckBox != null && mentalHealthCheckBox.isChecked())
            interests.add("Mental Health");
        if (nutritionCheckBox != null && nutritionCheckBox.isChecked())
            interests.add("Nutrition");
        if (fitnessCheckBox != null && fitnessCheckBox.isChecked())
            interests.add("Fitness");
        if (chronicCheckBox != null && chronicCheckBox.isChecked())
            interests.add("Chronic Conditions");
        if (parentingCheckBox != null && parentingCheckBox.isChecked())
            interests.add("Parenting");
        if (agingCheckBox != null && agingCheckBox.isChecked())
            interests.add("Aging");
        return interests;
    }
}