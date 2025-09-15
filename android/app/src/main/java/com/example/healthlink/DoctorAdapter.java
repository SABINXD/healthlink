package com.example.healthlink;
import android.content.Context;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.Button;
import android.widget.TextView;
import androidx.annotation.NonNull;
import androidx.recyclerview.widget.RecyclerView;
import com.bumptech.glide.Glide;
import com.google.android.material.chip.Chip;
import java.util.List;
import de.hdodenhof.circleimageview.CircleImageView;

public class DoctorAdapter extends RecyclerView.Adapter<DoctorAdapter.DoctorViewHolder> {
    private Context context;
    private List<Doctor> doctors;
    private OnDoctorClickListener listener;

    public interface OnDoctorClickListener {
        void onConsultClick(Doctor doctor);
        void onViewProfileClick(Doctor doctor);
    }

    public DoctorAdapter(Context context, List<Doctor> doctors, OnDoctorClickListener listener) {
        this.context = context;
        this.doctors = doctors;
        this.listener = listener;
    }

    @NonNull
    @Override
    public DoctorViewHolder onCreateViewHolder(@NonNull ViewGroup parent, int viewType) {
        View view = LayoutInflater.from(context).inflate(R.layout.item_doctor, parent, false);
        return new DoctorViewHolder(view);
    }

    @Override
    public void onBindViewHolder(@NonNull DoctorViewHolder holder, int position) {
        Doctor doctor = doctors.get(position);
        // FIXED: Use the correct getter methods for the Doctor class
        holder.doctorName.setText(doctor.getFirstName() + " " + doctor.getLastName());
        holder.doctorSpecialty.setText(doctor.getDoctorType());
        holder.doctorAddress.setText(doctor.getDoctorAddress());

        // Load doctor image
        String imageUrl = doctor.getProfilePic();
        if (imageUrl != null && !imageUrl.isEmpty()) {
            if (!imageUrl.startsWith("http")) {
                String serverIp = context.getString(R.string.server_ip);
                imageUrl = "http://" + serverIp + "/healthlink/web/assets/img/profile/" + imageUrl;
            }
            Glide.with(context)
                    .load(imageUrl)
                    .placeholder(R.drawable.doctor_placeholder)
                    .error(R.drawable.doctor_placeholder)
                    .into(holder.doctorImage);
        } else {
            holder.doctorImage.setImageResource(R.drawable.doctor_placeholder);
        }

        holder.consultButton.setOnClickListener(v -> {
            if (listener != null) {
                listener.onConsultClick(doctor);
            }
        });

        holder.viewProfileButton.setOnClickListener(v -> {
            if (listener != null) {
                listener.onViewProfileClick(doctor);
            }
        });
    }

    @Override
    public int getItemCount() {
        return doctors.size();
    }

    public static class DoctorViewHolder extends RecyclerView.ViewHolder {
        CircleImageView doctorImage;
        TextView doctorName, doctorAddress;
        Chip doctorSpecialty;
        Button consultButton, viewProfileButton;

        public DoctorViewHolder(@NonNull View itemView) {
            super(itemView);
            doctorImage = itemView.findViewById(R.id.doctorImage);
            doctorName = itemView.findViewById(R.id.doctorName);
            doctorAddress = itemView.findViewById(R.id.doctorAddress);
            doctorSpecialty = itemView.findViewById(R.id.doctorSpecialty);
            consultButton = itemView.findViewById(R.id.consultButton);
            viewProfileButton = itemView.findViewById(R.id.viewProfileButton);
        }
    }
}