package com.example.healthlink;

public class Doctor {
    private int id;
    private String firstName;
    private String lastName;
    private String doctorType;
    private String doctorAddress;
    private String profilePic;

    // Additional fields for the new constructor
    private String name;
    private String specialty;
    private int experience;
    private float rating;
    private String address;
    private String imageUrl;

    // Default constructor
    public Doctor() {
    }

    // Original constructor
    public Doctor(int id, String firstName, String lastName, String doctorType, String doctorAddress, String profilePic) {
        this.id = id;
        this.firstName = firstName;
        this.lastName = lastName;
        this.doctorType = doctorType;
        this.doctorAddress = doctorAddress;
        this.profilePic = profilePic;
        // Set additional fields
        this.name = firstName + " " + lastName;
        this.specialty = doctorType;
        this.address = doctorAddress;
        this.imageUrl = profilePic;
    }

    // NEW constructor for PostDetailsActivity
    public Doctor(int id, String name, String specialty, int experience, float rating, String address, String imageUrl) {
        this.id = id;
        this.name = name;
        this.specialty = specialty;
        this.experience = experience;
        this.rating = rating;
        this.address = address;
        this.imageUrl = imageUrl;
        // Set original fields
        String[] nameParts = name.split(" ", 2);
        this.firstName = nameParts.length > 0 ? nameParts[0] : "";
        this.lastName = nameParts.length > 1 ? nameParts[1] : "";
        this.doctorType = specialty;
        this.doctorAddress = address;
        this.profilePic = imageUrl;
    }

    // Getters and setters for original fields
    public int getId() { return id; }
    public void setId(int id) { this.id = id; }

    public String getFirstName() { return firstName; }
    public void setFirstName(String firstName) { this.firstName = firstName; }

    public String getLastName() { return lastName; }
    public void setLastName(String lastName) { this.lastName = lastName; }

    public String getDoctorType() { return doctorType; }
    public void setDoctorType(String doctorType) { this.doctorType = doctorType; }

    public String getDoctorAddress() { return doctorAddress; }
    public void setDoctorAddress(String doctorAddress) { this.doctorAddress = doctorAddress; }

    public String getProfilePic() { return profilePic; }
    public void setProfilePic(String profilePic) { this.profilePic = profilePic; }

    // Getters and setters for additional fields
    public String getName() { return name; }
    public void setName(String name) { this.name = name; }

    public String getSpecialty() { return specialty; }
    public void setSpecialty(String specialty) { this.specialty = specialty; }

    public int getExperience() { return experience; }
    public void setExperience(int experience) { this.experience = experience; }

    public float getRating() { return rating; }
    public void setRating(float rating) { this.rating = rating; }

    public String getAddress() { return address; }
    public void setAddress(String address) { this.address = address; }

    public String getImageUrl() { return imageUrl; }
    public void setImageUrl(String imageUrl) { this.imageUrl = imageUrl; }
}