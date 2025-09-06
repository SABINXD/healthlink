package com.example.healthlink;

import android.os.Bundle;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.SearchView;
import androidx.fragment.app.Fragment;
import androidx.recyclerview.widget.LinearLayoutManager;
import androidx.recyclerview.widget.RecyclerView;
import java.util.ArrayList;
import java.util.List;

public class DoctorsFragment extends Fragment {

    private RecyclerView doctorsRecyclerView;
    private DoctorsAdapter doctorsAdapter;
    private SearchView searchView;
    private List<Doctor> doctorsList;

    @Override
    public View onCreateView(LayoutInflater inflater, ViewGroup container, Bundle savedInstanceState) {
        View view = inflater.inflate(R.layout.fragment_doctors, container, false);

        initViews(view);
        setupRecyclerView();
        loadDoctors();

        return view;
    }

    private void initViews(View view) {
        doctorsRecyclerView = view.findViewById(R.id.doctors_recycler_view);
        searchView = view.findViewById(R.id.search_view);

        searchView.setOnQueryTextListener(new SearchView.OnQueryTextListener() {
            @Override
            public boolean onQueryTextSubmit(String query) {
                filterDoctors(query);
                return true;
            }

            @Override
            public boolean onQueryTextChange(String newText) {
                filterDoctors(newText);
                return true;
            }
        });
    }

    private void setupRecyclerView() {
        doctorsRecyclerView.setLayoutManager(new LinearLayoutManager(getContext()));
        doctorsAdapter = new DoctorsAdapter(new ArrayList<>());
        doctorsRecyclerView.setAdapter(doctorsAdapter);
    }

    private void loadDoctors() {
        doctorsList = new ArrayList<>();
        doctorsList.add(new Doctor("Dr. Sarah Johnson", "Dermatologist", "SF Medical Center",
                "4.8", "127 reviews", "12 years experience", "$150 - $300",
                "Board-certified dermatologist specializing in medical and cosmetic dermatology."));
        doctorsList.add(new Doctor("Dr. Michael Chen", "Cardiologist", "Heart Care Institute",
                "4.9", "89 reviews", "15 years experience", "$200 - $400",
                "Leading cardiologist with expertise in interventional cardiology."));
        doctorsList.add(new Doctor("Dr. Emily Rodriguez", "Psychiatrist", "Mental Health Center",
                "4.7", "156 reviews", "10 years experience", "$120 - $250",
                "Psychiatrist specializing in anxiety and depression treatment."));

        doctorsAdapter.updateDoctors(doctorsList);
    }

    private void filterDoctors(String query) {
        List<Doctor> filteredList = new ArrayList<>();
        for (Doctor doctor : doctorsList) {
            if (doctor.getName().toLowerCase().contains(query.toLowerCase()) ||
                    doctor.getSpecialty().toLowerCase().contains(query.toLowerCase())) {
                filteredList.add(doctor);
            }
        }
        doctorsAdapter.updateDoctors(filteredList);
    }
}