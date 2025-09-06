package com.example.healthlink;

public class Doctor {
    private String name;
    private String specialty;
    private String hospital;
    private String rating;
    private String reviews;
    private String experience;
    private String fees;
    private String description;
    private String imageUrl;

    public Doctor(String name, String specialty, String hospital, String rating,
                  String reviews, String experience, String fees, String description) {
        this.name = name;
        this.specialty = specialty;
        this.hospital = hospital;
        this.rating = rating;
        this.reviews = reviews;
        this.experience = experience;
        this.fees = fees;
        this.description = description;
    }

    // Getters and setters
    public String getName() { return name; }
    public void setName(String name) { this.name = name; }

    public String getSpecialty() { return specialty; }
    public void setSpecialty(String specialty) { this.specialty = specialty; }

    public String getHospital() { return hospital; }
    public void setHospital(String hospital) { this.hospital = hospital; }

    public String getRating() { return rating; }
    public void setRating(String rating) { this.rating = rating; }

    public String getReviews() { return reviews; }
    public void setReviews(String reviews) { this.reviews = reviews; }

    public String getExperience() { return experience; }
    public void setExperience(String experience) { this.experience = experience; }

    public String getFees() { return fees; }
    public void setFees(String fees) { this.fees = fees; }

    public String getDescription() { return description; }
    public void setDescription(String description) { this.description = description; }

    public String getImageUrl() { return imageUrl; }
    public void setImageUrl(String imageUrl) { this.imageUrl = imageUrl; }
}