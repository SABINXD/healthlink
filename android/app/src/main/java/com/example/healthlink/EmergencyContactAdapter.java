package com.example.healthlink;

import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.Button;
import android.widget.TextView;
import androidx.recyclerview.widget.RecyclerView;
import java.util.List;

public class EmergencyContactAdapter extends RecyclerView.Adapter<EmergencyContactAdapter.ViewHolder> {
    private List<EmergencyContact> contacts;
    private OnCallClickListener callListener;

    public interface OnCallClickListener {
        void onCallClick(String phoneNumber);
    }

    public EmergencyContactAdapter(List<EmergencyContact> contacts, OnCallClickListener callListener) {
        this.contacts = contacts;
        this.callListener = callListener;
    }

    @Override
    public ViewHolder onCreateViewHolder(ViewGroup parent, int viewType) {
        View view = LayoutInflater.from(parent.getContext())
                .inflate(R.layout.item_emergency_contact, parent, false);
        return new ViewHolder(view);
    }

    @Override
    public void onBindViewHolder(ViewHolder holder, int position) {
        EmergencyContact contact = contacts.get(position);
        holder.nameText.setText(contact.getName());
        holder.numberText.setText(contact.getNumber());
        holder.typeText.setText(contact.getType());
        holder.addressText.setText(contact.getAddress());

        holder.callButton.setOnClickListener(v ->
                callListener.onCallClick(contact.getNumber()));
    }

    @Override
    public int getItemCount() {
        return contacts.size();
    }

    static class ViewHolder extends RecyclerView.ViewHolder {
        TextView nameText, numberText, typeText, addressText;
        Button callButton;

        ViewHolder(View view) {
            super(view);
            nameText = view.findViewById(R.id.contact_name);
            numberText = view.findViewById(R.id.contact_number);
            typeText = view.findViewById(R.id.contact_type);
            addressText = view.findViewById(R.id.contact_address);
            callButton = view.findViewById(R.id.call_button);
        }
    }
}