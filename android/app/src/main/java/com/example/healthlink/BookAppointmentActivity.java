package com.example.healthlink;

import android.app.DatePickerDialog;
import android.content.Intent;
import android.os.Bundle;
import android.util.Log;
import android.view.View;
import android.widget.AdapterView;
import android.widget.ArrayAdapter;
import android.widget.Button;
import android.widget.EditText;
import android.widget.ImageView;
import android.widget.Spinner;
import android.widget.TextView;
import android.widget.Toast;
import androidx.appcompat.app.ActionBar;
import androidx.appcompat.app.AppCompatActivity;
import androidx.recyclerview.widget.GridLayoutManager;
import androidx.recyclerview.widget.RecyclerView;
import com.android.volley.Request;
import com.android.volley.toolbox.StringRequest;
import com.android.volley.toolbox.Volley;
import com.squareup.picasso.Picasso;
import org.json.JSONArray;
import org.json.JSONException;
import org.json.JSONObject;
import java.text.SimpleDateFormat;
import java.util.ArrayList;
import java.util.Calendar;
import java.util.Date;
import java.util.HashMap;
import java.util.List;
import java.util.Locale;
import java.util.Map;

public class BookAppointmentActivity extends AppCompatActivity {
    private static final String TAG = "BookAppointmentActivity";
    private static final int TIME_SLOT_COLUMNS = 3;

    private ImageView doctorProfileImage;
    private TextView doctorName, doctorSpecialty, doctorAddress;
    private TextView selectDateButton, selectedDateText, selectedTimeText;
    private RecyclerView timeSlotsRecyclerView;
    private Spinner reasonSpinner;
    private EditText descriptionEditText;
    private Button bookAppointmentButton;

    private int doctorId;
    private String selectedDate;
    private String selectedTime;
    private String preselectedDate;
    private String preselectedTime;
    private Map<String, List<String>> bookedAppointments = new HashMap<>();

    // Time slots available for booking
    private final String[] timeSlots = {
            "09:00", "09:30", "10:00", "10:30", "11:00", "11:30",
            "14:00", "14:30", "15:00", "15:30", "16:00", "16:30",
            "18:00", "18:30", "19:00", "19:30"
    };

    // Reasons for appointment
    private final String[] reasons = {
            "Select a reason",
            "General Consultation",
            "Follow-up Visit",
            "Routine Check-up",
            "Vaccination",
            "Other"
    };

    // Calendar instance for date picker
    private Calendar calendar;
    private SimpleDateFormat dateFormat;
    private SessionManager sessionManager;
    private boolean isReschedule;
    private int appointmentId;
    private String doctorNameStr, doctorSpecialtyStr, doctorAddressStr;
    private String doctorProfilePicUrl;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);

        // Initialize session manager FIRST
        sessionManager = new SessionManager(this);
        sessionManager.refreshUserData(this);

        // Check if user is a doctor - doctors shouldn't be able to book appointments
        if (sessionManager.isDoctor()) {
            Toast.makeText(this, "Doctors cannot book appointments", Toast.LENGTH_SHORT).show();
            finish(); // Close the activity immediately
            return;   // Prevent any further execution
        }

        // Only proceed with UI setup if user is not a doctor
        setContentView(R.layout.activity_book_appointment);

        // Get doctor ID from intent
        doctorId = getIntent().getIntExtra("doctor_id", 0);
        if (doctorId == 0) {
            Toast.makeText(this, "Invalid doctor ID", Toast.LENGTH_SHORT).show();
            finish();
            return;
        }

        // Get doctor details from intent if available
        doctorNameStr = getIntent().getStringExtra("doctor_name");
        doctorSpecialtyStr = getIntent().getStringExtra("doctor_type");
        doctorAddressStr = getIntent().getStringExtra("doctor_address");

        // Check if this is a reschedule
        isReschedule = getIntent().getBooleanExtra("reschedule", false);
        appointmentId = getIntent().getIntExtra("appointment_id", 0);

        // Get pre-selected date and time if this is a reschedule
        preselectedDate = getIntent().getStringExtra("preselected_date");
        preselectedTime = getIntent().getStringExtra("preselected_time");

        // Initialize calendar and date format
        calendar = Calendar.getInstance();
        dateFormat = new SimpleDateFormat("yyyy-MM-dd", Locale.getDefault());

        // Log session info for debugging
        sessionManager.debugSession();

        initializeViews();
        setupReasonSpinner();

        // If we have doctor details from intent, use them directly
        if (doctorNameStr != null && doctorSpecialtyStr != null && doctorAddressStr != null) {
            doctorName.setText("Dr. " + doctorNameStr);
            doctorSpecialty.setText(doctorSpecialtyStr);
            doctorAddress.setText(doctorAddressStr);
            doctorProfileImage.setImageResource(R.drawable.doctor_placeholder);
        } else {
            // Otherwise, load doctor details from server
            loadDoctorDetails();
        }

        loadBookedAppointments();

        // Set up UI based on whether this is a reschedule
        if (isReschedule) {
            bookAppointmentButton.setText("Reschedule Appointment");
            // Fix: Add null check for ActionBar
            ActionBar actionBar = getSupportActionBar();
            if (actionBar != null) {
                actionBar.setTitle("Reschedule Appointment");
            }

            // Set pre-selected date if available
            if (preselectedDate != null) {
                // Fix: Handle date format that might include time
                try {
                    // If the date string includes time, extract just the date part
                    if (preselectedDate.contains(" ")) {
                        preselectedDate = preselectedDate.split(" ")[0];
                    }
                    selectedDate = preselectedDate;
                    updateSelectedDateUI();
                } catch (Exception e) {
                    Log.e(TAG, "Error processing pre-selected date: " + e.getMessage());
                }
            }
        }

        setupTimeSlots();
    }

    private void initializeViews() {
        doctorProfileImage = findViewById(R.id.doctorProfileImage);
        doctorName = findViewById(R.id.doctorName);
        doctorSpecialty = findViewById(R.id.doctorSpecialty);
        doctorAddress = findViewById(R.id.doctorAddress);
        selectDateButton = findViewById(R.id.selectDateButton);
        selectedDateText = findViewById(R.id.selectedDateText);
        selectedTimeText = findViewById(R.id.selectedTimeText);
        timeSlotsRecyclerView = findViewById(R.id.timeSlotsRecyclerView);
        reasonSpinner = findViewById(R.id.reasonSpinner);
        descriptionEditText = findViewById(R.id.descriptionEditText);
        bookAppointmentButton = findViewById(R.id.bookAppointmentButton);

        // Set up date picker
        selectDateButton.setOnClickListener(v -> showDatePicker());

        // Set up book appointment button with detailed logging
        bookAppointmentButton.setOnClickListener(v -> {
            Log.d(TAG, "Book appointment button clicked");
            bookAppointment();
        });

        // Initially disable the button
        bookAppointmentButton.setEnabled(false);
    }

    private void showDatePicker() {
        // Get current date
        final Calendar c = Calendar.getInstance();
        int year = c.get(Calendar.YEAR);
        int month = c.get(Calendar.MONTH);
        int day = c.get(Calendar.DAY_OF_MONTH);

        // Create date picker dialog and set minimum date to today
        DatePickerDialog datePickerDialog = new DatePickerDialog(
                this,
                (view, selectedYear, selectedMonth, selectedDay) -> {
                    // Update calendar with selected date
                    calendar.set(Calendar.YEAR, selectedYear);
                    calendar.set(Calendar.MONTH, selectedMonth);
                    calendar.set(Calendar.DAY_OF_MONTH, selectedDay);

                    // Format and store the selected date
                    selectedDate = dateFormat.format(calendar.getTime());

                    // Update UI
                    updateSelectedDateUI();

                    // Load time slots for the selected date
                    setupTimeSlots();
                },
                year,
                month,
                day
        );

        // Set minimum date to today
        datePickerDialog.getDatePicker().setMinDate(System.currentTimeMillis() - 1000);

        // If this is a reschedule and we have a pre-selected date, set it
        if (preselectedDate != null) {
            try {
                // Fix: Handle date format that might include time
                String dateToParse = preselectedDate;
                if (dateToParse.contains(" ")) {
                    dateToParse = dateToParse.split(" ")[0];
                }
                Date preselectedDateObj = dateFormat.parse(dateToParse);
                Calendar preselectedCalendar = Calendar.getInstance();
                preselectedCalendar.setTime(preselectedDateObj);
                datePickerDialog.updateDate(
                        preselectedCalendar.get(Calendar.YEAR),
                        preselectedCalendar.get(Calendar.MONTH),
                        preselectedCalendar.get(Calendar.DAY_OF_MONTH)
                );
            } catch (Exception e) {
                Log.e(TAG, "Error parsing pre-selected date: " + e.getMessage());
            }
        }

        // Show the dialog
        datePickerDialog.show();
    }

    private void updateSelectedDateUI() {
        try {
            SimpleDateFormat displayFormat = new SimpleDateFormat("EEEE, MMMM d, yyyy", Locale.getDefault());
            Date dateObj = dateFormat.parse(selectedDate);
            String formattedDate = displayFormat.format(dateObj);
            selectDateButton.setText(formattedDate);
            selectDateButton.setTextColor(getResources().getColor(R.color.colorPrimary));
            selectedDateText.setText("Selected: " + formattedDate);
            selectedDateText.setVisibility(View.VISIBLE);
        } catch (Exception e) {
            selectDateButton.setText(selectedDate);
            selectDateButton.setTextColor(getResources().getColor(R.color.colorPrimary));
            selectedDateText.setText("Selected: " + selectedDate);
            selectedDateText.setVisibility(View.VISIBLE);
        }
    }

    private void setupReasonSpinner() {
        ArrayAdapter<String> adapter = new ArrayAdapter<>(
                this, android.R.layout.simple_spinner_item, reasons);
        adapter.setDropDownViewResource(android.R.layout.simple_spinner_dropdown_item);
        reasonSpinner.setAdapter(adapter);
        reasonSpinner.setOnItemSelectedListener(new AdapterView.OnItemSelectedListener() {
            @Override
            public void onItemSelected(AdapterView<?> parent, View view, int position, long id) {
                updateBookButtonState();
            }

            @Override
            public void onNothingSelected(AdapterView<?> parent) {
                updateBookButtonState();
            }
        });
    }

    private void loadDoctorDetails() {
        String serverIp = getString(R.string.server_ip);
        String url = "http://" + serverIp + "/healthlink1/api/get_doctor_details.php?doctor_id=" + doctorId;
        Log.d(TAG, "Loading doctor details from: " + url);

        StringRequest request = new StringRequest(
                Request.Method.GET,
                url,
                response -> {
                    try {
                        JSONObject jsonResponse = new JSONObject(response);
                        if (jsonResponse.getString("status").equals("success")) {
                            JSONObject doctor = jsonResponse.getJSONObject("doctor");
                            doctorName.setText("Dr. " + doctor.getString("first_name") + " " + doctor.getString("last_name"));
                            doctorSpecialty.setText(doctor.getString("doctor_type"));
                            doctorAddress.setText(doctor.getString("doctor_address"));

                            // Load profile image if available
                            String profilePic = doctor.optString("profile_pic", "");
                            if (!profilePic.isEmpty() && !profilePic.equals("null")) {
                                String imageUrl = "http://" + serverIp + "/healthlink1/web/assets/img/profile/" + profilePic;
                                Log.d(TAG, "Loading doctor profile image from: " + imageUrl);
                                Picasso.get()
                                        .load(imageUrl)
                                        .placeholder(R.drawable.doctor_placeholder)
                                        .error(R.drawable.doctor_placeholder)
                                        .into(doctorProfileImage);
                            } else {
                                // Set default image
                                doctorProfileImage.setImageResource(R.drawable.doctor_placeholder);
                            }
                        } else {
                            Toast.makeText(this, "Failed to load doctor details", Toast.LENGTH_SHORT).show();
                        }
                    } catch (JSONException e) {
                        Log.e(TAG, "Error parsing doctor details", e);
                        Toast.makeText(this, "Error loading doctor details", Toast.LENGTH_SHORT).show();
                    }
                },
                error -> {
                    Log.e(TAG, "Error loading doctor details: " + error.toString());
                    Toast.makeText(this, "Error loading doctor details", Toast.LENGTH_SHORT).show();
                }
        );

        Volley.newRequestQueue(this).add(request);
    }

    private void loadBookedAppointments() {
        String serverIp = getString(R.string.server_ip);
        String url = "http://" + serverIp + "/healthlink1/api/get_booked_appointments.php?doctor_id=" + doctorId;
        Log.d(TAG, "Loading booked appointments from: " + url);

        StringRequest request = new StringRequest(
                Request.Method.GET,
                url,
                response -> {
                    try {
                        JSONObject jsonResponse = new JSONObject(response);
                        if (jsonResponse.getString("status").equals("success")) {
                            JSONArray appointments = jsonResponse.getJSONArray("appointments");
                            bookedAppointments.clear();

                            for (int i = 0; i < appointments.length(); i++) {
                                JSONObject appointment = appointments.getJSONObject(i);
                                String date = appointment.getString("date");
                                String time = appointment.getString("time");

                                if (!bookedAppointments.containsKey(date)) {
                                    bookedAppointments.put(date, new ArrayList<>());
                                }

                                // If this is a reschedule, exclude the current appointment from booked slots
                                if (isReschedule && preselectedDate != null && preselectedDate.equals(date)
                                        && preselectedTime != null && preselectedTime.equals(time)) {
                                    // Don't add this time slot to booked slots
                                } else {
                                    bookedAppointments.get(date).add(time);
                                }
                            }

                            // Update time slots if date is already selected
                            if (selectedDate != null) {
                                setupTimeSlots();
                            }
                        } else {
                            Log.e(TAG, "Failed to load booked appointments");
                        }
                    } catch (JSONException e) {
                        Log.e(TAG, "Error parsing booked appointments", e);
                    }
                },
                error -> {
                    Log.e(TAG, "Error loading booked appointments: " + error.toString());
                }
        );

        Volley.newRequestQueue(this).add(request);
    }

    private void setupTimeSlots() {
        TimeSlotAdapter adapter = new TimeSlotAdapter(timeSlots, selectedDate, bookedAppointments);
        adapter.setOnItemClickListener(time -> {
            selectedTime = time;
            Log.d(TAG, "Selected time: " + time);

            // Update selected time text
            try {
                SimpleDateFormat inputFormat = new SimpleDateFormat("HH:mm", Locale.getDefault());
                SimpleDateFormat outputFormat = new SimpleDateFormat("h:mm a", Locale.getDefault());
                Date timeObj = inputFormat.parse(time);
                selectedTimeText.setText(outputFormat.format(timeObj));
                selectedTimeText.setTextColor(getResources().getColor(R.color.colorPrimary));
            } catch (Exception e) {
                selectedTimeText.setText(time);
                selectedTimeText.setTextColor(getResources().getColor(R.color.colorPrimary));
            }

            updateBookButtonState();
        });

        timeSlotsRecyclerView.setLayoutManager(new GridLayoutManager(this, TIME_SLOT_COLUMNS));
        timeSlotsRecyclerView.setAdapter(adapter);

        // If this is a reschedule and we have a pre-selected time, select it
        if (preselectedTime != null && selectedDate != null && selectedDate.equals(preselectedDate)) {
            // Find the position of the pre-selected time
            for (int i = 0; i < timeSlots.length; i++) {
                if (timeSlots[i].equals(preselectedTime)) {
                    RecyclerView.ViewHolder holder = timeSlotsRecyclerView.findViewHolderForAdapterPosition(i);
                    if (holder != null) {
                        holder.itemView.performClick();
                    }
                    break;
                }
            }
        }
    }

    private void updateBookButtonState() {
        boolean dateSelected = selectedDate != null;
        boolean timeSelected = selectedTime != null;
        boolean reasonSelected = reasonSpinner.getSelectedItemPosition() > 0;

        Log.d(TAG, "Button state - Date: " + dateSelected + ", Time: " + timeSelected + ", Reason: " + reasonSelected);

        bookAppointmentButton.setEnabled(dateSelected && timeSelected && reasonSelected);

        if (bookAppointmentButton.isEnabled()) {
            bookAppointmentButton.setBackgroundColor(getResources().getColor(R.color.colorPrimary));
        } else {
            bookAppointmentButton.setBackgroundColor(getResources().getColor(R.color.gray));
        }
    }

    private void bookAppointment() {
        Log.d(TAG, "bookAppointment() called");

        if (selectedDate == null || selectedTime == null) {
            Log.e(TAG, "Date or time not selected");
            Toast.makeText(this, "Please select date and time", Toast.LENGTH_SHORT).show();
            return;
        }

        if (reasonSpinner.getSelectedItemPosition() <= 0) {
            Log.e(TAG, "Reason not selected");
            Toast.makeText(this, "Please select a reason", Toast.LENGTH_SHORT).show();
            return;
        }

        String reason = reasons[reasonSpinner.getSelectedItemPosition()];
        String description = descriptionEditText.getText().toString().trim();

        // Get current user ID from session
        int patientId = sessionManager.getUserId();
        Log.d(TAG, "Patient ID: " + patientId);

        // Check if user is a doctor
        if (sessionManager.isDoctor()) {
            Log.e(TAG, "Doctors cannot book appointments");
            Toast.makeText(this, "Doctors cannot book appointments", Toast.LENGTH_SHORT).show();
            finish(); // Close the activity
            return;
        }

        if (patientId <= 0) {
            Log.e(TAG, "Invalid patient ID: " + patientId);
            Toast.makeText(this, "Please login to book an appointment", Toast.LENGTH_SHORT).show();
            return;
        }

        // Show loading state
        bookAppointmentButton.setEnabled(false);
        bookAppointmentButton.setText(isReschedule ? "Updating..." : "Booking...");

        // Create request body
        Map<String, String> params = new HashMap<>();
        params.put("doctor_id", String.valueOf(doctorId));
        params.put("patient_id", String.valueOf(patientId));
        params.put("date", selectedDate);
        params.put("time", selectedTime);
        params.put("reason", reason);
        params.put("desc", description);

        // Add appointment ID if this is a reschedule
        if (isReschedule && appointmentId > 0) {
            params.put("appointment_id", String.valueOf(appointmentId));
        }

        // Log the parameters we're sending
        Log.d(TAG, "Booking appointment with parameters:");
        for (Map.Entry<String, String> entry : params.entrySet()) {
            Log.d(TAG, entry.getKey() + ": " + entry.getValue());
        }

        String serverIp = getString(R.string.server_ip);
        String url = "http://" + serverIp + "/healthlink1/api/" +
                (isReschedule ? "update_appointment.php" : "book_appointment.php");
        Log.d(TAG, "URL: " + url);

        // Use StringRequest to send form data
        StringRequest request = new StringRequest(
                Request.Method.POST,
                url,
                response -> {
                    bookAppointmentButton.setEnabled(true);
                    bookAppointmentButton.setText(isReschedule ? "Reschedule" : "Book Appointment");

                    Log.d(TAG, "Response: " + response);

                    try {
                        JSONObject jsonResponse = new JSONObject(response);
                        if (jsonResponse.getBoolean("success")) {
                            String successMessage = isReschedule ?
                                    "Appointment rescheduled successfully!" :
                                    "Appointment booked successfully!";
                            Toast.makeText(this, successMessage, Toast.LENGTH_SHORT).show();

                            // Navigate to appointments list or confirmation screen
                            Intent intent = new Intent(this, AppointmentsActivity.class);
                            intent.addFlags(Intent.FLAG_ACTIVITY_CLEAR_TOP);
                            startActivity(intent);
                            finish();
                        } else {
                            String message = jsonResponse.optString("message",
                                    isReschedule ? "Failed to reschedule appointment" : "Failed to book appointment");
                            Log.e(TAG, "Operation failed: " + message);
                            Toast.makeText(this, message, Toast.LENGTH_SHORT).show();
                        }
                    } catch (JSONException e) {
                        Log.e(TAG, "Error parsing response", e);
                        Toast.makeText(this, "Error parsing response", Toast.LENGTH_SHORT).show();
                    }
                },
                error -> {
                    bookAppointmentButton.setEnabled(true);
                    bookAppointmentButton.setText(isReschedule ? "Reschedule" : "Book Appointment");

                    Log.e(TAG, "Error: " + error.toString());

                    // Try to get more error details
                    if (error.networkResponse != null) {
                        try {
                            String responseBody = new String(error.networkResponse.data, "UTF-8");
                            Log.e(TAG, "Error response: " + responseBody);
                            Toast.makeText(this, "Error: " + responseBody, Toast.LENGTH_LONG).show();
                        } catch (Exception e) {
                            Log.e(TAG, "Error reading response: " + e.getMessage());
                            Toast.makeText(this, "Network error occurred", Toast.LENGTH_SHORT).show();
                        }
                    } else {
                        Toast.makeText(this, "Network error occurred", Toast.LENGTH_SHORT).show();
                    }
                }
        ) {
            @Override
            protected Map<String, String> getParams() {
                return params;
            }
        };

        Volley.newRequestQueue(this).add(request);
    }
}