package com.example.healthlink;

import android.content.Context;
import android.graphics.Color;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.Button;
import androidx.annotation.NonNull;
import androidx.core.content.ContextCompat;
import androidx.recyclerview.widget.RecyclerView;
import java.text.SimpleDateFormat;
import java.util.Date;
import java.util.List;
import java.util.Locale;
import java.util.Map;

public class TimeSlotAdapter extends RecyclerView.Adapter<TimeSlotAdapter.TimeSlotViewHolder> {
    private final String[] timeSlots;
    private final String selectedDate;
    private final Map<String, List<String>> bookedAppointments;
    private OnItemClickListener listener;
    private Context context;

    public interface OnItemClickListener {
        void onItemClick(String time);
    }

    public void setOnItemClickListener(OnItemClickListener listener) {
        this.listener = listener;
    }

    public TimeSlotAdapter(String[] timeSlots, String selectedDate, Map<String, List<String>> bookedAppointments) {
        this.timeSlots = timeSlots;
        this.selectedDate = selectedDate;
        this.bookedAppointments = bookedAppointments;
    }

    @NonNull
    @Override
    public TimeSlotViewHolder onCreateViewHolder(@NonNull ViewGroup parent, int viewType) {
        context = parent.getContext();
        View view = LayoutInflater.from(context)
                .inflate(R.layout.item_time_slot, parent, false);
        return new TimeSlotViewHolder(view);
    }

    @Override
    public void onBindViewHolder(@NonNull TimeSlotViewHolder holder, int position) {
        String time = timeSlots[position];
        holder.bind(time);
    }

    @Override
    public int getItemCount() {
        return timeSlots.length;
    }

    class TimeSlotViewHolder extends RecyclerView.ViewHolder {
        private final Button timeSlotButton;

        public TimeSlotViewHolder(@NonNull View itemView) {
            super(itemView);
            timeSlotButton = itemView.findViewById(R.id.timeSlotButton);
        }

        public void bind(String time) {
            // Format time to AM/PM
            String formattedTime = formatTime(time);
            timeSlotButton.setText(formattedTime);

            // Check if the time slot is booked
            List<String> bookedTimes = bookedAppointments.get(selectedDate);
            boolean isBooked = bookedTimes != null && bookedTimes.contains(time);

            // Check if the time slot is in the past
            boolean isPast = isTimeInPast(selectedDate, time);

            // Disable if booked or in the past
            timeSlotButton.setEnabled(!isBooked && !isPast);

            // Set appearance based on state
            if (isBooked) {
                // Gray background with red text for booked slots
                timeSlotButton.setBackgroundColor(ContextCompat.getColor(context, R.color.time_slot_booked_bg));
                timeSlotButton.setTextColor(ContextCompat.getColor(context, R.color.time_slot_booked_text));
            } else if (isPast) {
                // Light gray background with dark gray text for past slots
                timeSlotButton.setBackgroundColor(ContextCompat.getColor(context, R.color.time_slot_past_bg));
                timeSlotButton.setTextColor(ContextCompat.getColor(context, R.color.time_slot_past_text));
            } else {
                // White background with black text for available slots
                timeSlotButton.setBackgroundColor(ContextCompat.getColor(context, R.color.time_slot_available_bg));
                timeSlotButton.setTextColor(ContextCompat.getColor(context, R.color.time_slot_available_text));
            }

            timeSlotButton.setOnClickListener(v -> {
                if (listener != null && !isBooked && !isPast) {
                    listener.onItemClick(time);
                }
            });
        }

        private String formatTime(String time) {
            try {
                SimpleDateFormat sdf = new SimpleDateFormat("HH:mm", Locale.getDefault());
                Date date = sdf.parse(time);
                SimpleDateFormat outputFormat = new SimpleDateFormat("hh:mm a", Locale.getDefault());
                return outputFormat.format(date);
            } catch (Exception e) {
                return time;
            }
        }

        private boolean isTimeInPast(String date, String time) {
            try {
                SimpleDateFormat sdf = new SimpleDateFormat("yyyy-MM-dd HH:mm", Locale.getDefault());
                String dateTimeStr = date + " " + time;
                Date slotDateTime = sdf.parse(dateTimeStr);
                Date currentDateTime = new Date();
                return slotDateTime.before(currentDateTime);
            } catch (Exception e) {
                return false;
            }
        }
    }
}