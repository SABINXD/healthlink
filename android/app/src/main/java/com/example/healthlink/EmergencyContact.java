package com.example.healthlink;

public class EmergencyContact {
    private String name;
    private String number;
    private String type;
    private String address;

    public EmergencyContact() {}

    public String getName() { return name; }
    public void setName(String name) { this.name = name; }

    public String getNumber() { return number; }
    public void setNumber(String number) { this.number = number; }

    public String getType() { return type; }
    public void setType(String type) { this.type = type; }

    public String getAddress() { return address; }
    public void setAddress(String address) { this.address = address; }
}