package com.example.healthlink;

import android.Manifest;
import android.content.ContentResolver;
import android.content.DialogInterface;
import android.content.Intent;
import android.content.pm.PackageManager;
import android.database.Cursor;
import android.net.Uri;
import android.os.Bundle;
import android.provider.ContactsContract;
import android.util.Log;
import android.view.LayoutInflater;
import android.view.View;
import android.widget.Button;
import android.widget.EditText;
import android.widget.LinearLayout;
import android.widget.TextView;
import android.widget.Toast;
import androidx.appcompat.app.AlertDialog;
import androidx.appcompat.app.AppCompatActivity;
import androidx.core.app.ActivityCompat;
import androidx.core.content.ContextCompat;
import java.util.ArrayList;

public class EmergencyActivity extends AppCompatActivity {
    private static final String TAG = "EmergencyActivity";
    private static final int REQUEST_READ_CONTACTS = 1;
    private static final int REQUEST_CALL_PHONE = 2;

    private LinearLayout policeContainer, fireContainer, ambulanceContainer, helplineContainer, touristContainer;
    private LinearLayout contactsContainer;
    private TextView noContactsText;
    private Button addContactButton, sosButton;
    private SessionManager sessionManager;

    private ArrayList<EmergencyContact> emergencyContacts = new ArrayList<>();

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_emergency);
        initializeViews();
        setupClickListeners();
        loadEmergencyContacts();
    }

    private void initializeViews() {
        sessionManager = new SessionManager(this);
        policeContainer = findViewById(R.id.police_container);
        fireContainer = findViewById(R.id.fire_container);
        ambulanceContainer = findViewById(R.id.ambulance_container);
        helplineContainer = findViewById(R.id.helpline_container);
        touristContainer = findViewById(R.id.tourist_container);
        sosButton = findViewById(R.id.sos_button);

        // New views for emergency contacts
        contactsContainer = findViewById(R.id.contacts_container);
        noContactsText = findViewById(R.id.no_contacts_text);
        addContactButton = findViewById(R.id.add_contact_button);
    }

    private void setupClickListeners() {
        // Police - 100
        policeContainer.setOnClickListener(v -> {
            Log.d(TAG, "Police button clicked");
            openDialer("100");
        });

        // Fire Department - 101
        fireContainer.setOnClickListener(v -> {
            Log.d(TAG, "Fire button clicked");
            openDialer("101");
        });

        // Ambulance - 102
        ambulanceContainer.setOnClickListener(v -> {
            Log.d(TAG, "Ambulance button clicked");
            openDialer("102");
        });

        // Emergency Helpline - 103
        helplineContainer.setOnClickListener(v -> {
            Log.d(TAG, "Helpline button clicked");
            openDialer("103");
        });

        // Tourist Helpline - 1144
        touristContainer.setOnClickListener(v -> {
            Log.d(TAG, "Tourist helpline button clicked");
            openDialer("1144");
        });

        // SOS Button
        sosButton.setOnClickListener(v -> {
            Log.d(TAG, "SOS button clicked");
            showSOSOptions();
        });

        // Add Contact Button
        addContactButton.setOnClickListener(v -> {
            showAddContactDialog();
        });
    }

    private void openDialer(String phoneNumber) {
        if (ContextCompat.checkSelfPermission(this, Manifest.permission.CALL_PHONE)
                != PackageManager.PERMISSION_GRANTED) {
            ActivityCompat.requestPermissions(this,
                    new String[]{Manifest.permission.CALL_PHONE},
                    REQUEST_CALL_PHONE);
        } else {
            try {
                Intent dialIntent = new Intent(Intent.ACTION_DIAL);
                dialIntent.setData(Uri.parse("tel:" + phoneNumber));
                startActivity(dialIntent);
                Log.d(TAG, "Opening dialer for: " + phoneNumber);
            } catch (Exception e) {
                Log.e(TAG, "Error opening dialer for " + phoneNumber, e);
                Toast.makeText(this, "Error opening dialer", Toast.LENGTH_SHORT).show();
            }
        }
    }

    private void showSOSOptions() {
        // Create a simple dialog to choose which emergency service to call
        AlertDialog.Builder builder = new AlertDialog.Builder(this);
        builder.setTitle("🆘 Emergency SOS");
        builder.setMessage("Choose emergency service to call:");
        builder.setPositiveButton("🚔 Police (100)", (dialog, which) -> {
            openDialer("100");
            Toast.makeText(this, "Calling Police Emergency", Toast.LENGTH_SHORT).show();
        });
        builder.setNeutralButton("🚑 Ambulance (102)", (dialog, which) -> {
            openDialer("102");
            Toast.makeText(this, "Calling Medical Emergency", Toast.LENGTH_SHORT).show();
        });
        builder.setNegativeButton("🚒 Fire (101)", (dialog, which) -> {
            openDialer("101");
            Toast.makeText(this, "Calling Fire Emergency", Toast.LENGTH_SHORT).show();
        });
        builder.show();
    }

    // Emergency Contacts Methods

    private void loadEmergencyContacts() {
        // Clear existing views
        contactsContainer.removeAllViews();

        if (emergencyContacts.isEmpty()) {
            noContactsText.setVisibility(View.VISIBLE);
        } else {
            noContactsText.setVisibility(View.GONE);

            for (EmergencyContact contact : emergencyContacts) {
                addContactToLayout(contact);
            }
        }
    }

    private void addContactToLayout(EmergencyContact contact) {
        View contactView = LayoutInflater.from(this).inflate(R.layout.item_emergency_contacts, contactsContainer, false);

        TextView nameText = contactView.findViewById(R.id.contact_name);
        TextView phoneText = contactView.findViewById(R.id.contact_phone);
        Button callButton = contactView.findViewById(R.id.call_contact_button);
        Button deleteButton = contactView.findViewById(R.id.delete_contact_button);

        nameText.setText(contact.getName());
        phoneText.setText(contact.getPhone());

        callButton.setOnClickListener(v -> openDialer(contact.getPhone()));
        deleteButton.setOnClickListener(v -> {
            emergencyContacts.remove(contact);
            loadEmergencyContacts();
            Toast.makeText(this, "Contact removed", Toast.LENGTH_SHORT).show();
        });

        contactsContainer.addView(contactView);
    }

    private void showAddContactDialog() {
        AlertDialog.Builder builder = new AlertDialog.Builder(this);
        builder.setTitle("Add Emergency Contact");

        View view = LayoutInflater.from(this).inflate(R.layout.dialog_add_contact, null);
        EditText nameInput = view.findViewById(R.id.contact_name_input);
        EditText phoneInput = view.findViewById(R.id.contact_phone_input);
        Button importButton = view.findViewById(R.id.import_contact_button);

        builder.setView(view);

        importButton.setOnClickListener(v -> {
            if (ContextCompat.checkSelfPermission(this, Manifest.permission.READ_CONTACTS)
                    != PackageManager.PERMISSION_GRANTED) {
                ActivityCompat.requestPermissions(this,
                        new String[]{Manifest.permission.READ_CONTACTS},
                        REQUEST_READ_CONTACTS);
            } else {
                pickContact();
            }
        });

        builder.setPositiveButton("Add", (dialog, which) -> {
            String name = nameInput.getText().toString().trim();
            String phone = phoneInput.getText().toString().trim();

            if (name.isEmpty() || phone.isEmpty()) {
                Toast.makeText(this, "Please enter both name and phone number", Toast.LENGTH_SHORT).show();
                return;
            }

            EmergencyContact newContact = new EmergencyContact(name, phone);
            emergencyContacts.add(newContact);
            loadEmergencyContacts();
            Toast.makeText(this, "Contact added successfully", Toast.LENGTH_SHORT).show();
        });

        builder.setNegativeButton("Cancel", null);

        AlertDialog dialog = builder.create();
        dialog.show();
    }

    private void pickContact() {
        Intent intent = new Intent(Intent.ACTION_PICK, ContactsContract.CommonDataKinds.Phone.CONTENT_URI);
        startActivityForResult(intent, REQUEST_READ_CONTACTS);
    }

    @Override
    public void onRequestPermissionsResult(int requestCode, String[] permissions, int[] grantResults) {
        super.onRequestPermissionsResult(requestCode, permissions, grantResults);

        if (requestCode == REQUEST_READ_CONTACTS) {
            if (grantResults.length > 0 && grantResults[0] == PackageManager.PERMISSION_GRANTED) {
                pickContact();
            } else {
                Toast.makeText(this, "Permission denied to read contacts", Toast.LENGTH_SHORT).show();
            }
        } else if (requestCode == REQUEST_CALL_PHONE) {
            if (grantResults.length > 0 && grantResults[0] == PackageManager.PERMISSION_GRANTED) {
                // Permission granted, you can make calls now
            } else {
                Toast.makeText(this, "Permission denied to make calls", Toast.LENGTH_SHORT).show();
            }
        }
    }

    @Override
    protected void onActivityResult(int requestCode, int resultCode, Intent data) {
        super.onActivityResult(requestCode, resultCode, data);

        if (requestCode == REQUEST_READ_CONTACTS && resultCode == RESULT_OK) {
            Uri contactUri = data.getData();
            String[] projection = {ContactsContract.CommonDataKinds.Phone.NUMBER,
                    ContactsContract.CommonDataKinds.Phone.DISPLAY_NAME};

            Cursor cursor = getContentResolver().query(
                    ContactsContract.CommonDataKinds.Phone.CONTENT_URI,
                    projection,
                    ContactsContract.CommonDataKinds.Phone.CONTACT_ID + " = ?",
                    new String[]{contactUri.getLastPathSegment()},
                    null);

            if (cursor != null && cursor.moveToFirst()) {
                String name = cursor.getString(cursor.getColumnIndex(ContactsContract.CommonDataKinds.Phone.DISPLAY_NAME));
                String phone = cursor.getString(cursor.getColumnIndex(ContactsContract.CommonDataKinds.Phone.NUMBER));

                EmergencyContact newContact = new EmergencyContact(name, phone);
                emergencyContacts.add(newContact);
                loadEmergencyContacts();
                Toast.makeText(this, "Contact added successfully", Toast.LENGTH_SHORT).show();
            }

            if (cursor != null) {
                cursor.close();
            }
        }
    }

    @Override
    public void onBackPressed() {
        super.onBackPressed();
        Log.d(TAG, "Back button pressed, returning to previous activity");
    }

    // Simple model class for emergency contacts
    private static class EmergencyContact {
        private String name;
        private String phone;

        public EmergencyContact(String name, String phone) {
            this.name = name;
            this.phone = phone;
        }

        public String getName() {
            return name;
        }

        public String getPhone() {
            return phone;
        }
    }
}