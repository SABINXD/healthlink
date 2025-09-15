package com.example.healthlink;

import android.content.Intent;
import android.os.Bundle;
import android.util.Log;
import android.view.View;
import android.widget.Button;
import android.widget.TextView;
import android.widget.Toast;
import androidx.appcompat.app.AppCompatActivity;
import com.android.volley.Request;
import com.android.volley.Response;
import com.android.volley.VolleyError;
import com.android.volley.toolbox.StringRequest;
import com.android.volley.toolbox.Volley;
import org.json.JSONException;
import org.json.JSONObject;
import java.text.SimpleDateFormat;
import java.util.Date;
import java.util.Locale;

public class AppointmentDetailActivity extends AppCompatActivity {
    private static final String TAG = "AppointmentDetailActivity";
    private TextView doctorName, hospitalName, date, time, status, description;
    private Button cancelButton, rescheduleButton;
    private int appointmentId;
    private String APPOINTMENT_DETAIL_URL;
    private String CANCEL_APPOINTMENT_URL;
    private SessionManager sessionManager;
    private boolean isDoctor;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_appointment_detail);

        // Initialize session manager
        sessionManager = new SessionManager(this);
        isDoctor = sessionManager.isDoctor();

        // Set up toolbar
        if (getSupportActionBar() != null) {
            getSupportActionBar().setTitle("Appointment Details");
            getSupportActionBar().setDisplayHomeAsUpEnabled(true);
        }

        // Get appointment ID from intent
        appointmentId = getIntent().getIntExtra("appointment_id", 0);
        if (appointmentId == 0) {
            Toast.makeText(this, "Invalid appointment ID", Toast.LENGTH_SHORT).show();
            finish();
            return;
        }

        // Initialize views
        doctorName = findViewById(R.id.doctorName);
        hospitalName = findViewById(R.id.hospitalName);
        date = findViewById(R.id.date);
        time = findViewById(R.id.time);
        status = findViewById(R.id.status);
        description = findViewById(R.id.description);
        cancelButton = findViewById(R.id.cancelButton);
        rescheduleButton = findViewById(R.id.rescheduleButton);

        // Set up URLs
        String serverIp = getString(R.string.server_ip);
        APPOINTMENT_DETAIL_URL = "http://" + serverIp + "/healthlink/api/get_appointment_detail.php?appointment_id=" + appointmentId;
        CANCEL_APPOINTMENT_URL = "http://" + serverIp + "/healthlink/api/cancel_appointment.php";

        // Load appointment details
        loadAppointmentDetails();

        // Set up button click listeners
        cancelButton.setOnClickListener(v -> cancelAppointment());
        rescheduleButton.setOnClickListener(v -> rescheduleAppointment());
    }

    private void loadAppointmentDetails() {
        StringRequest request = new StringRequest(
                Request.Method.GET,
                APPOINTMENT_DETAIL_URL,
                response -> {
                    try {
                        JSONObject jsonResponse = new JSONObject(response);
                        if (jsonResponse.getString("status").equals("success")) {
                            JSONObject appointment = jsonResponse.getJSONObject("appointment");

                            // Set doctor/patient name based on user type
                            if (isDoctor) {
                                doctorName.setText("Patient: " + appointment.getString("patient_name"));
                            } else {
                                doctorName.setText("Dr. " + appointment.getString("doctor_name"));
                            }

                            // Fix: Check if hospital_name exists, if not use doctor_address or a default value
                            if (appointment.has("hospital_name")) {
                                hospitalName.setText(appointment.getString("hospital_name"));
                            } else if (appointment.has("doctor_address")) {
                                hospitalName.setText(appointment.getString("doctor_address"));
                            } else {
                                hospitalName.setText("Address not available");
                            }

                            // Fix: Check if date exists, handle different field names
                            if (appointment.has("appointment_date")) {
                                date.setText(appointment.getString("appointment_date"));
                            } else if (appointment.has("date")) {
                                date.setText(appointment.getString("date"));
                            } else {
                                date.setText("Date not available");
                            }

                            // Fix: Check if time exists, handle different field names
                            if (appointment.has("appointment_time")) {
                                time.setText(appointment.getString("appointment_time"));
                            } else if (appointment.has("time")) {
                                time.setText(appointment.getString("time"));
                            } else {
                                time.setText("Time not available");
                            }

                            // Fix: Check if status exists, handle different field names
                            if (appointment.has("a_satus")) {
                                int statusCode = appointment.getInt("a_satus");
                                status.setText(getStatusString(statusCode));
                            } else if (appointment.has("a_status")) {
                                int statusCode = appointment.getInt("a_status");
                                status.setText(getStatusString(statusCode));
                            } else if (appointment.has("status")) {
                                status.setText(appointment.getString("status"));
                            } else {
                                status.setText("Unknown");
                            }

                            // Fix: Check if description exists, handle different field names
                            if (appointment.has("patient_desc")) {
                                String desc = appointment.getString("patient_desc");
                                description.setText(desc.isEmpty() ? "No description available" : desc);
                            } else if (appointment.has("description")) {
                                String desc = appointment.getString("description");
                                description.setText(desc.isEmpty() ? "No description available" : desc);
                            } else if (appointment.has("desc")) {
                                String desc = appointment.getString("desc");
                                description.setText(desc.isEmpty() ? "No description available" : desc);
                            } else {
                                description.setText("No description available");
                            }

                            // Disable buttons if appointment is already cancelled or completed
                            String statusText = status.getText().toString();
                            if ("Cancelled".equals(statusText) || "Completed".equals(statusText)) {
                                cancelButton.setEnabled(false);
                                rescheduleButton.setEnabled(false);
                            }

                            // Hide reschedule button for doctors
                            if (isDoctor) {
                                rescheduleButton.setVisibility(View.GONE);
                            }
                        } else {
                            String errorMsg = jsonResponse.optString("message", "Failed to load appointment details");
                            Toast.makeText(this, errorMsg, Toast.LENGTH_SHORT).show();
                        }
                    } catch (JSONException e) {
                        Log.e(TAG, "Error parsing appointment details", e);
                        Toast.makeText(this, "Error parsing data", Toast.LENGTH_SHORT).show();
                    }
                },
                error -> {
                    Log.e(TAG, "Error loading appointment details: " + error.toString());
                    Toast.makeText(this, "Error loading appointment details", Toast.LENGTH_SHORT).show();
                }
        );
        Volley.newRequestQueue(this).add(request);
    }

    private void cancelAppointment() {
        // Create request body
        JSONObject params = new JSONObject();
        try {
            params.put("appointment_id", appointmentId);
            params.put("user_id", sessionManager.getUserId());
        } catch (JSONException e) {
            e.printStackTrace();
        }

        StringRequest request = new StringRequest(
                Request.Method.POST,
                CANCEL_APPOINTMENT_URL,
                response -> {
                    try {
                        JSONObject jsonResponse = new JSONObject(response);
                        if (jsonResponse.getBoolean("success")) {
                            Toast.makeText(this, "Appointment cancelled successfully", Toast.LENGTH_SHORT).show();
                            finish();
                        } else {
                            String message = jsonResponse.optString("message", "Failed to cancel appointment");
                            Toast.makeText(this, message, Toast.LENGTH_SHORT).show();
                        }
                    } catch (JSONException e) {
                        Log.e(TAG, "Error parsing cancellation response", e);
                        Toast.makeText(this, "Error cancelling appointment", Toast.LENGTH_SHORT).show();
                    }
                },
                error -> {
                    Log.e(TAG, "Error cancelling appointment: " + error.toString());
                    Toast.makeText(this, "Error cancelling appointment", Toast.LENGTH_SHORT).show();
                }
        ) {
            @Override
            public byte[] getBody() {
                return params.toString().getBytes();
            }

            @Override
            public String getBodyContentType() {
                return "application/json";
            }
        };
        Volley.newRequestQueue(this).add(request);
    }

    private void rescheduleAppointment() {
        // Get the current appointment details
        int appointmentId = getIntent().getIntExtra("appointment_id", 0);
        int doctorId = getIntent().getIntExtra("doctor_id", 0);

        // Create intent for booking/rescheduling
        Intent intent = new Intent(this, BookAppointmentActivity.class);
        intent.putExtra("doctor_id", doctorId);
        intent.putExtra("reschedule", true);
        intent.putExtra("appointment_id", appointmentId);

        // Get current date and time to pre-select in the booking activity
        String currentDate = date.getText().toString();
        String currentTime = time.getText().toString();

        try {
            // Parse the current date and time
            SimpleDateFormat inputFormat = new SimpleDateFormat("EEE, MMM d, yyyy hh:mm a", Locale.getDefault());
            SimpleDateFormat outputDateFormat = new SimpleDateFormat("yyyy-MM-dd", Locale.getDefault());
            SimpleDateFormat outputTimeFormat = new SimpleDateFormat("HH:mm", Locale.getDefault());
            Date dateTime = inputFormat.parse(currentDate + " " + currentTime);
            String formattedDate = outputDateFormat.format(dateTime);
            String formattedTime = outputTimeFormat.format(dateTime);
            intent.putExtra("preselected_date", formattedDate);
            intent.putExtra("preselected_time", formattedTime);
        } catch (Exception e) {
            Log.e(TAG, "Error parsing date/time: " + e.getMessage());
        }

        startActivity(intent);
    }

    private String getStatusString(int statusCode) {
        switch (statusCode) {
            case 0: return "Pending";
            case 1: return "Confirmed";
            case 2: return "Completed";
            case 3: return "Cancelled";
            default: return "Unknown";
        }
    }
}