package com.example.healthlink;

import android.content.Context;
import android.content.Intent;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.Button;
import android.widget.ImageView;
import android.widget.TextView;
import androidx.annotation.NonNull;
import androidx.recyclerview.widget.RecyclerView;
import java.util.List;

public class DoctorsAdapter extends RecyclerView.Adapter<DoctorsAdapter.DoctorViewHolder> {

    private List<Doctor> doctors;
    private Context context;

    public DoctorsAdapter(List<Doctor> doctors) {
        this.doctors = doctors;
    }

    @NonNull
    @Override
    public DoctorViewHolder onCreateViewHolder(@NonNull ViewGroup parent, int viewType) {
        context = parent.getContext();
        View view = LayoutInflater.from(context).inflate(R.layout.item_doctor, parent, false);
        return new DoctorViewHolder(view);
    }

    @Override
    public void onBindViewHolder(@NonNull DoctorViewHolder holder, int position) {
        Doctor doctor = doctors.get(position);
        holder.bind(doctor);
    }

    @Override
    public int getItemCount() {
        return doctors.size();
    }

    public void updateDoctors(List<Doctor> newDoctors) {
        this.doctors = newDoctors;
        notifyDataSetChanged();
    }

    class DoctorViewHolder extends RecyclerView.ViewHolder {
        private ImageView doctorImage, verifiedIcon;
        private TextView doctorName, specialty, hospital, rating, reviews, experience, fees;
        private Button bookButton, videoCallButton;

        public DoctorViewHolder(@NonNull View itemView) {
            super(itemView);
            doctorImage = itemView.findViewById(R.id.doctor_image);
            verifiedIcon = itemView.findViewById(R.id.verified_icon);
            doctorName = itemView.findViewById(R.id.doctor_name);
            specialty = itemView.findViewById(R.id.doctor_specialty);
            hospital = itemView.findViewById(R.id.doctor_hospital);
            rating = itemView.findViewById(R.id.doctor_rating);
            reviews = itemView.findViewById(R.id.doctor_reviews);
            experience = itemView.findViewById(R.id.doctor_experience);
            fees = itemView.findViewById(R.id.doctor_fees);
            bookButton = itemView.findViewById(R.id.book_button);
            videoCallButton = itemView.findViewById(R.id.video_call_button);
        }

        public void bind(Doctor doctor) {
            doctorName.setText(doctor.getName());
            specialty.setText(doctor.getSpecialty());
            hospital.setText(doctor.getHospital());
            rating.setText(doctor.getRating());
            reviews.setText(doctor.getReviews());
            experience.setText(doctor.getExperience());
            fees.setText(doctor.getFees());

            bookButton.setOnClickListener(v -> {
                Intent intent = new Intent(context, AppointmentBookingActivity.class);
                intent.putExtra("doctor_name", doctor.getName());
                intent.putExtra("doctor_specialty", doctor.getSpecialty());
                context.startActivity(intent);
            });

            videoCallButton.setOnClickListener(v -> {
                // Handle video call
            });

            itemView.setOnClickListener(v -> {
                Intent intent = new Intent(context, DoctorDetailActivity.class);
                context.startActivity(intent);
            });
        }
    }
}