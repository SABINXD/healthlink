package com.example.healthlink;
import android.content.Intent;
import android.os.Bundle;
import android.util.Log;
import android.view.View;
import android.widget.ProgressBar;
import android.widget.TextView;
import android.widget.Toast;
import androidx.appcompat.app.AppCompatActivity;
import androidx.recyclerview.widget.LinearLayoutManager;
import androidx.recyclerview.widget.RecyclerView;
import com.android.volley.Request;
import com.android.volley.toolbox.JsonObjectRequest;
import com.android.volley.toolbox.Volley;
import org.json.JSONArray;
import org.json.JSONException;
import org.json.JSONObject;
import java.util.ArrayList;
import java.util.List;

public class DoctorsListActivity extends AppCompatActivity implements DoctorAdapter.OnDoctorClickListener {
    private static final String TAG = "DoctorsListActivity";
    private RecyclerView recyclerView;
    private ProgressBar progressBar;
    private TextView emptyView;
    private DoctorAdapter adapter;
    private List<Doctor> doctorList = new ArrayList<>();
    private String DOCTORS_URL;
    private SessionManager sessionManager;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_doctors_list);
        // Initialize session manager and check if user is a doctor
        sessionManager = new SessionManager(this);
        sessionManager.refreshUserData(this);
        if (sessionManager.isDoctor()) {
            Toast.makeText(this, "Doctors cannot book appointments", Toast.LENGTH_SHORT).show();
            finish(); // Close the activity
            return;
        }
        // Set up toolbar
        if (getSupportActionBar() != null) {
            getSupportActionBar().setTitle("Available Doctors");
            getSupportActionBar().setDisplayHomeAsUpEnabled(true);
        }
        // Initialize views
        recyclerView = findViewById(R.id.recyclerViewDoctors);
        progressBar = findViewById(R.id.progressBar);
        emptyView = findViewById(R.id.emptyView);
        // Set up RecyclerView
        recyclerView.setLayoutManager(new LinearLayoutManager(this));
        // FIXED: Now passing the listener (this activity) as the third parameter
        adapter = new DoctorAdapter(this, doctorList, this);
        recyclerView.setAdapter(adapter);
        // Set up URL
        String serverIp = getString(R.string.server_ip);
        DOCTORS_URL = "http://" + serverIp + "/healthlink/api/get_doctors.php";
        // Load doctors
        loadDoctors();
    }

    private void loadDoctors() {
        progressBar.setVisibility(View.VISIBLE);
        recyclerView.setVisibility(View.GONE);
        emptyView.setVisibility(View.GONE);
        JsonObjectRequest request = new JsonObjectRequest(
                Request.Method.GET,
                DOCTORS_URL,
                null,
                response -> {
                    progressBar.setVisibility(View.GONE);
                    try {
                        if (response.getString("status").equals("success")) {
                            JSONArray doctorsArray = response.getJSONArray("doctors");
                            doctorList.clear();
                            for (int i = 0; i < doctorsArray.length(); i++) {
                                JSONObject obj = doctorsArray.getJSONObject(i);
                                Doctor doctor = new Doctor();
                                doctor.setId(obj.getInt("id"));
                                doctor.setFirstName(obj.getString("first_name"));
                                doctor.setLastName(obj.getString("last_name"));
                                doctor.setDoctorType(obj.getString("doctor_type"));
                                doctor.setDoctorAddress(obj.getString("doctor_address"));
                                doctor.setProfilePic(obj.optString("profile_pic", ""));
                                doctorList.add(doctor);
                            }
                            adapter.notifyDataSetChanged();
                            if (doctorList.isEmpty()) {
                                recyclerView.setVisibility(View.GONE);
                                emptyView.setVisibility(View.VISIBLE);
                            } else {
                                recyclerView.setVisibility(View.VISIBLE);
                                emptyView.setVisibility(View.GONE);
                            }
                        } else {
                            Toast.makeText(this, "Failed to load doctors", Toast.LENGTH_SHORT).show();
                        }
                    } catch (JSONException e) {
                        Log.e(TAG, "Error parsing doctors", e);
                        Toast.makeText(this, "Error parsing data", Toast.LENGTH_SHORT).show();
                    }
                },
                error -> {
                    progressBar.setVisibility(View.GONE);
                    Log.e(TAG, "Error loading doctors: " + error.toString());
                    Toast.makeText(this, "Failed to load doctors", Toast.LENGTH_SHORT).show();
                }
        );
        Volley.newRequestQueue(this).add(request);
    }

    @Override
    public boolean onSupportNavigateUp() {
        onBackPressed();
        return true;
    }

    // FIXED: Implement the OnDoctorClickListener interface methods
    @Override
    public void onConsultClick(Doctor doctor) {
        // Handle consult button click
        Intent intent = new Intent(this, BookAppointmentActivity.class);
        intent.putExtra("doctor_id", doctor.getId());
        intent.putExtra("doctor_name", doctor.getFirstName() + " " + doctor.getLastName());
        startActivity(intent);
    }

    @Override
    public void onViewProfileClick(Doctor doctor) {
        // Handle view profile button click
        Intent intent = new Intent(this, DoctorProfileActivity.class);
        intent.putExtra("doctor_id", doctor.getId());
        startActivity(intent);
    }
}