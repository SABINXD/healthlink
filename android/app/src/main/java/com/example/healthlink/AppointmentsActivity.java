package com.example.healthlink;

import android.content.DialogInterface;
import android.content.Intent;
import android.os.Bundle;
import android.util.Log;
import android.view.Menu;
import android.view.MenuItem;
import android.view.View;
import android.widget.TextView;
import android.widget.Toast;
import androidx.annotation.NonNull;
import androidx.appcompat.app.AlertDialog;
import androidx.appcompat.app.AppCompatActivity;
import androidx.appcompat.widget.Toolbar;
import androidx.recyclerview.widget.LinearLayoutManager;
import androidx.recyclerview.widget.RecyclerView;
import androidx.swiperefreshlayout.widget.SwipeRefreshLayout;
import com.android.volley.Request;
import com.android.volley.Response;
import com.android.volley.VolleyError;
import com.android.volley.toolbox.StringRequest;
import com.android.volley.toolbox.Volley;
import com.google.android.material.floatingactionbutton.FloatingActionButton;
import org.json.JSONArray;
import org.json.JSONException;
import org.json.JSONObject;
import java.util.ArrayList;
import java.util.HashMap;
import java.util.List;
import java.util.Map;

public class AppointmentsActivity extends AppCompatActivity implements AppointmentsAdapter.OnAppointmentActionListener {

    private static final String TAG = "AppointmentsActivity";
    private RecyclerView recyclerView;
    private SwipeRefreshLayout swipeRefreshLayout;
    private TextView emptyView;
    private FloatingActionButton fabBookAppointment;
    private SessionManager sessionManager;
    private AppointmentsAdapter adapter;
    private List<Appointment> appointmentList = new ArrayList<>();
    private boolean isDoctor;
    private int userId;
    private String APPOINTMENTS_URL;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_appointments);

        // Initialize session manager
        sessionManager = new SessionManager(this);
        userId = sessionManager.getUserId();
        isDoctor = sessionManager.isDoctor();

        // Set up URLs based on user type
        String serverIp = getString(R.string.server_ip);
        if (isDoctor) {
            APPOINTMENTS_URL = "http://" + serverIp + "/healthlink1/api/get_doctor_appointments.php?doctor_id=" + userId;
        } else {
            APPOINTMENTS_URL = "http://" + serverIp + "/healthlink1/api/get_patient_appointments.php?patient_id=" + userId;
        }

        // Initialize views
        Toolbar toolbar = findViewById(R.id.toolbar);
        setSupportActionBar(toolbar);
        if (getSupportActionBar() != null) {
            getSupportActionBar().setTitle(isDoctor ? "Appointment Requests" : "My Appointments");
            getSupportActionBar().setDisplayHomeAsUpEnabled(true);
        }

        recyclerView = findViewById(R.id.recyclerAppointments);
        swipeRefreshLayout = findViewById(R.id.swipeRefreshLayout);
        emptyView = findViewById(R.id.emptyView);
        fabBookAppointment = findViewById(R.id.fabBookAppointment);

        // Setup RecyclerView
        recyclerView.setLayoutManager(new LinearLayoutManager(this));
        adapter = new AppointmentsAdapter(this, appointmentList, isDoctor, this);
        recyclerView.setAdapter(adapter);

        // Setup swipe refresh
        swipeRefreshLayout.setOnRefreshListener(this::loadAppointments);

        // Setup FAB (only show for patients)
        if (!isDoctor) {
            fabBookAppointment.setVisibility(View.VISIBLE);
            fabBookAppointment.setOnClickListener(v -> {
                Intent intent = new Intent(AppointmentsActivity.this, DoctorsListActivity.class);
                startActivity(intent);
            });
        } else {
            fabBookAppointment.setVisibility(View.GONE);
        }

        // Load appointments
        loadAppointments();
    }
    

    @Override
    public boolean onOptionsItemSelected(MenuItem item) {
        if (item.getItemId() == android.R.id.home) {
            onBackPressed();
            return true;
        } else if (item.getItemId() == R.id.action_refresh) {
            loadAppointments();
            return true;
        }
        return super.onOptionsItemSelected(item);
    }

    private void loadAppointments() {
        swipeRefreshLayout.setRefreshing(true);

        StringRequest request = new StringRequest(
                Request.Method.GET,
                APPOINTMENTS_URL,
                response -> {
                    swipeRefreshLayout.setRefreshing(false);
                    try {
                        JSONObject jsonResponse = new JSONObject(response);
                        if (jsonResponse.getString("status").equals("success")) {
                            JSONArray appointments = jsonResponse.getJSONArray("appointments");
                            appointmentList.clear();

                            for (int i = 0; i < appointments.length(); i++) {
                                JSONObject obj = appointments.getJSONObject(i);
                                Appointment appointment = new Appointment();

                                appointment.setId(obj.getInt("id"));
                                appointment.setPatientId(obj.getInt("patient_id"));
                                appointment.setDoctorId(obj.getInt("doctor_id"));
                                appointment.setPatientName(obj.getString("patient_name"));
                                appointment.setDoctorName(obj.getString("doctor_name"));
                                appointment.setHospitalName(obj.getString("hospital_name"));
                                appointment.setDate(obj.getString("appointment_date"));
                                appointment.setTime(obj.getString("appointment_time"));
                                appointment.setReason(obj.getString("reason"));
                                appointment.setDescription(obj.getString("patient_desc"));
                                appointment.setStatus(getStatusString(obj.getInt("status_code")));
                                appointment.setStatusCode(obj.getInt("status_code"));

                                appointmentList.add(appointment);
                            }

                            adapter.notifyDataSetChanged();

                            // Show/hide empty view
                            if (appointmentList.isEmpty()) {
                                recyclerView.setVisibility(View.GONE);
                                emptyView.setVisibility(View.VISIBLE);
                                emptyView.setText(isDoctor ?
                                        "You don't have any appointment requests yet." :
                                        "You don't have any appointments yet.\nTap the + button to book one.");
                            } else {
                                recyclerView.setVisibility(View.VISIBLE);
                                emptyView.setVisibility(View.GONE);
                            }
                        } else {
                            String message = jsonResponse.optString("message", "Failed to load appointments");
                            Toast.makeText(this, message, Toast.LENGTH_SHORT).show();
                        }
                    } catch (JSONException e) {
                        Log.e(TAG, "Error parsing appointments", e);
                        Toast.makeText(this, "Error loading appointments", Toast.LENGTH_SHORT).show();
                    }
                },
                error -> {
                    swipeRefreshLayout.setRefreshing(false);
                    Log.e(TAG, "Error loading appointments: " + error.toString());
                    Toast.makeText(this, "Error loading appointments", Toast.LENGTH_SHORT).show();
                }
        );

        Volley.newRequestQueue(this).add(request);
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

    @Override
    public void onReschedule(Appointment appointment) {
        Intent intent = new Intent(this, BookAppointmentActivity.class);
        intent.putExtra("doctor_id", appointment.getDoctorId());
        intent.putExtra("doctor_name", appointment.getDoctorName());
        intent.putExtra("reschedule", true);
        intent.putExtra("appointment_id", appointment.getId());
        intent.putExtra("preselected_date", appointment.getDate());
        intent.putExtra("preselected_time", appointment.getTime());
        startActivity(intent);
    }

    @Override
    public void onCancel(Appointment appointment) {
        new AlertDialog.Builder(this)
                .setTitle("Cancel Appointment")
                .setMessage("Are you sure you want to cancel this appointment?")
                .setPositiveButton("Yes", (dialog, which) -> {
                    cancelAppointment(appointment.getId());
                })
                .setNegativeButton("No", null)
                .show();
    }

    @Override
    public void onAccept(Appointment appointment) {
        updateAppointmentStatus(appointment.getId(), 1); // 1 = Confirmed
    }

    @Override
    public void onDelete(Appointment appointment) {
        new AlertDialog.Builder(this)
                .setTitle("Delete Appointment")
                .setMessage("Are you sure you want to delete this appointment request?")
                .setPositiveButton("Yes", (dialog, which) -> {
                    deleteAppointment(appointment.getId());
                })
                .setNegativeButton("No", null)
                .show();
    }

    @Override
    public void onViewDetails(Appointment appointment) {
        Intent intent = new Intent(this, AppointmentDetailActivity.class);
        intent.putExtra("appointment_id", appointment.getId());
        startActivity(intent);
    }

    private void cancelAppointment(int appointmentId) {
        String serverIp = getString(R.string.server_ip);
        String url = "http://" + serverIp + "/healthlink1/api/cancel_appointment.php";

        StringRequest request = new StringRequest(
                Request.Method.POST,
                url,
                response -> {
                    try {
                        JSONObject jsonResponse = new JSONObject(response);
                        if (jsonResponse.getBoolean("success")) {
                            Toast.makeText(this, "Appointment cancelled successfully", Toast.LENGTH_SHORT).show();
                            loadAppointments(); // Refresh the list
                        } else {
                            String message = jsonResponse.optString("message", "Failed to cancel appointment");
                            Toast.makeText(this, message, Toast.LENGTH_SHORT).show();
                        }
                    } catch (JSONException e) {
                        Log.e(TAG, "Error parsing response", e);
                        Toast.makeText(this, "Error cancelling appointment", Toast.LENGTH_SHORT).show();
                    }
                },
                error -> {
                    Log.e(TAG, "Error cancelling appointment", error);
                    Toast.makeText(this, "Error cancelling appointment", Toast.LENGTH_SHORT).show();
                }
        ) {
            @Override
            protected Map<String, String> getParams() {
                Map<String, String> params = new HashMap<>();
                params.put("appointment_id", String.valueOf(appointmentId));
                params.put("patient_id", String.valueOf(userId));
                return params;
            }
        };

        Volley.newRequestQueue(this).add(request);
    }

    private void deleteAppointment(int appointmentId) {
        String serverIp = getString(R.string.server_ip);
        String url = "http://" + serverIp + "/healthlink1/api/delete_appointment.php";

        StringRequest request = new StringRequest(
                Request.Method.POST,
                url,
                response -> {
                    try {
                        JSONObject jsonResponse = new JSONObject(response);
                        if (jsonResponse.getBoolean("success")) {
                            Toast.makeText(this, "Appointment deleted successfully", Toast.LENGTH_SHORT).show();
                            loadAppointments(); // Refresh the list
                        } else {
                            String message = jsonResponse.optString("message", "Failed to delete appointment");
                            Toast.makeText(this, message, Toast.LENGTH_SHORT).show();
                        }
                    } catch (JSONException e) {
                        Log.e(TAG, "Error parsing response", e);
                        Toast.makeText(this, "Error deleting appointment", Toast.LENGTH_SHORT).show();
                    }
                },
                error -> {
                    Log.e(TAG, "Error deleting appointment", error);
                    Toast.makeText(this, "Error deleting appointment", Toast.LENGTH_SHORT).show();
                }
        ) {
            @Override
            protected Map<String, String> getParams() {
                Map<String, String> params = new HashMap<>();
                params.put("appointment_id", String.valueOf(appointmentId));
                params.put("doctor_id", String.valueOf(userId));
                return params;
            }
        };

        Volley.newRequestQueue(this).add(request);
    }

    private void updateAppointmentStatus(int appointmentId, int status) {
        String serverIp = getString(R.string.server_ip);
        String url = "http://" + serverIp + "/healthlink1/api/update_appointment_status.php";

        StringRequest request = new StringRequest(
                Request.Method.POST,
                url,
                response -> {
                    try {
                        JSONObject jsonResponse = new JSONObject(response);
                        if (jsonResponse.getBoolean("success")) {
                            String statusText = (status == 1) ? "confirmed" : "cancelled";
                            Toast.makeText(this, "Appointment " + statusText + " successfully", Toast.LENGTH_SHORT).show();
                            loadAppointments(); // Refresh the list
                        } else {
                            String message = jsonResponse.optString("message", "Failed to update appointment");
                            Toast.makeText(this, message, Toast.LENGTH_SHORT).show();
                        }
                    } catch (JSONException e) {
                        Log.e(TAG, "Error parsing response", e);
                        Toast.makeText(this, "Error updating appointment", Toast.LENGTH_SHORT).show();
                    }
                },
                error -> {
                    Log.e(TAG, "Error updating appointment", error);
                    Toast.makeText(this, "Error updating appointment", Toast.LENGTH_SHORT).show();
                }
        ) {
            @Override
            protected Map<String, String> getParams() {
                Map<String, String> params = new HashMap<>();
                params.put("appointment_id", String.valueOf(appointmentId));
                params.put("doctor_id", String.valueOf(userId));
                params.put("status", String.valueOf(status));
                return params;
            }
        };

        Volley.newRequestQueue(this).add(request);
    }
}