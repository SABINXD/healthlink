package com.example.healthlink;

import android.content.Context;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.Button;
import android.widget.TextView;
import androidx.annotation.NonNull;
import androidx.recyclerview.widget.RecyclerView;
import java.util.List;

public class AppointmentsAdapter extends RecyclerView.Adapter<AppointmentsAdapter.AppointmentViewHolder> {

    private List<Appointment> appointmentList;
    private Context context;
    private boolean isDoctor;
    private OnAppointmentActionListener listener;

    public interface OnAppointmentActionListener {
        void onReschedule(Appointment appointment);
        void onCancel(Appointment appointment);
        void onAccept(Appointment appointment);
        void onDelete(Appointment appointment);
        void onViewDetails(Appointment appointment);
    }

    public AppointmentsAdapter(Context context, List<Appointment> appointmentList, boolean isDoctor, OnAppointmentActionListener listener) {
        this.context = context;
        this.appointmentList = appointmentList;
        this.isDoctor = isDoctor;
        this.listener = listener;
    }

    @NonNull
    @Override
    public AppointmentViewHolder onCreateViewHolder(@NonNull ViewGroup parent, int viewType) {
        View view = LayoutInflater.from(context).inflate(R.layout.item_appointment, parent, false);
        return new AppointmentViewHolder(view);
    }

    @Override
    public void onBindViewHolder(@NonNull AppointmentViewHolder holder, int position) {
        Appointment appointment = appointmentList.get(position);

        // Set appointment details
        if (isDoctor) {
            holder.tvAppointmentWith.setText("Patient: " + appointment.getPatientName());
        } else {
            holder.tvAppointmentWith.setText("Dr. " + appointment.getDoctorName());
        }

        holder.tvHospitalName.setText(appointment.getHospitalName());
        holder.tvDate.setText(appointment.getDate());
        holder.tvTime.setText(appointment.getTime());
        holder.tvReason.setText(appointment.getReason());

        // Set status with appropriate color
        setStatus(holder.tvStatus, appointment.getStatus());

        // Show appropriate buttons based on user type
        if (isDoctor) {
            holder.btnReschedule.setVisibility(View.GONE);
            holder.btnCancel.setVisibility(View.GONE);
            holder.btnAccept.setVisibility(View.VISIBLE);
            holder.btnDelete.setVisibility(View.VISIBLE);
        } else {
            holder.btnReschedule.setVisibility(View.VISIBLE);
            holder.btnCancel.setVisibility(View.VISIBLE);
            holder.btnAccept.setVisibility(View.GONE);
            holder.btnDelete.setVisibility(View.GONE);
        }

        // Set button listeners
        holder.btnReschedule.setOnClickListener(v -> {
            if (listener != null) listener.onReschedule(appointment);
        });

        holder.btnCancel.setOnClickListener(v -> {
            if (listener != null) listener.onCancel(appointment);
        });

        holder.btnAccept.setOnClickListener(v -> {
            if (listener != null) listener.onAccept(appointment);
        });

        holder.btnDelete.setOnClickListener(v -> {
            if (listener != null) listener.onDelete(appointment);
        });

        // Set click listener for the whole card to view details
        holder.itemView.setOnClickListener(v -> {
            if (listener != null) listener.onViewDetails(appointment);
        });
    }

    private void setStatus(TextView tvStatus, String status) {
        tvStatus.setText(status);

        switch (status.toLowerCase()) {
            case "confirmed":
                tvStatus.setBackgroundResource(R.drawable.status_confirmed);
                break;
            case "pending":
                tvStatus.setBackgroundResource(R.drawable.status_pending);
                break;
            case "canceled":
                tvStatus.setBackgroundResource(R.drawable.status_canceled);
                break;
            case "submitted":
                tvStatus.setBackgroundResource(R.drawable.status_submitted);
                break;
            default:
                tvStatus.setBackgroundResource(R.drawable.status_default);
        }
    }

    @Override
    public int getItemCount() {
        return appointmentList.size();
    }

    static class AppointmentViewHolder extends RecyclerView.ViewHolder {
        TextView tvAppointmentWith, tvHospitalName, tvDate, tvTime, tvReason, tvStatus;
        Button btnReschedule, btnCancel, btnAccept, btnDelete;

        AppointmentViewHolder(@NonNull View itemView) {
            super(itemView);
            tvAppointmentWith = itemView.findViewById(R.id.tvAppointmentWith);
            tvDate = itemView.findViewById(R.id.tvDate);
            tvTime = itemView.findViewById(R.id.tvTime);
            tvReason = itemView.findViewById(R.id.tvReason);
            tvStatus = itemView.findViewById(R.id.tvStatus);
            btnReschedule = itemView.findViewById(R.id.btnReschedule);
            btnCancel = itemView.findViewById(R.id.btnCancel);
            btnAccept = itemView.findViewById(R.id.btnAccept);
            btnDelete = itemView.findViewById(R.id.btnDelete);
        }
    }
}