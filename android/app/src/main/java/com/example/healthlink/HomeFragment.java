package com.example.healthlink;

import android.content.Intent;
import android.os.Bundle;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.TextView;
import androidx.annotation.NonNull;
import androidx.fragment.app.Fragment;

public class HomeFragment extends Fragment {
    private SessionManager sessionManager;

    @Override
    public View onCreateView(LayoutInflater inflater, ViewGroup container,
                             Bundle savedInstanceState) {
        // Inflate the layout for this fragment
        View view = inflater.inflate(R.layout.fragment_home, container, false);

        // Get session manager
        sessionManager = new SessionManager(requireContext());

       
        TextView welcomeText = view.findViewById(R.id.welcome_text);
        welcomeText.setText("Welcome, " + sessionManager.getUserName() + "!");

        // Sign out button
        TextView signOutButton = view.findViewById(R.id.sign_out_button);
        signOutButton.setOnClickListener(v -> {
            sessionManager.logout();
            startActivity(new Intent(requireActivity(), SignUpActivity.class));
            requireActivity().finish();
        });

        return view;
    }
}