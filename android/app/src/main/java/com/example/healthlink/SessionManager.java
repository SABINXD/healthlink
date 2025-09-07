package com.example.healthlink;

import android.content.Context;
import android.content.SharedPreferences;
import android.content.SharedPreferences.Editor;

public class SessionManager {
    // Shared Preferences file name
    private static final String PREF_NAME = "HealthLinkSession";
    // All Shared Preferences Keys
    private static final String KEY_IS_LOGGEDIN = "isLoggedIn";
    private static final String KEY_USER_ID = "userId";
    private static final String KEY_EMAIL = "email";
    private static final String KEY_NAME = "name";
    private static final String KEY_IS_DOCTOR = "isDoctor";
    private static final String KEY_AUTH_TOKEN = "authToken";

    // Shared Preferences
    SharedPreferences pref;
    // Editor for Shared preferences
    Editor editor;
    // Context
    Context _context;

    // Shared pref mode
    int PRIVATE_MODE = 0;

    // Constructor
    public SessionManager(Context context) {
        this._context = context;
        pref = _context.getSharedPreferences(PREF_NAME, PRIVATE_MODE);
        editor = pref.edit();
    }

    /**
     * Create login session
     * @param userId ID of the user
     * @param email Email of the user
     * @param name Full name of the user
     * @param isDoctor Whether user is a doctor
     */
    public void createLoginSession(int userId, String email, String name, boolean isDoctor) {
        // Storing login value as TRUE
        editor.putBoolean(KEY_IS_LOGGEDIN, true);

        // Storing user id
        editor.putInt(KEY_USER_ID, userId);

        // Storing email in pref
        editor.putString(KEY_EMAIL, email);

        // Storing name in pref
        editor.putString(KEY_NAME, name);

        // Storing user type
        editor.putBoolean(KEY_IS_DOCTOR, isDoctor);

        // Commit changes
        editor.commit();
    }

    /**
     * Create login session with auth token
     * @param userId ID of the user
     * @param email Email of the user
     * @param name Full name of the user
     * @param isDoctor Whether user is a doctor
     * @param authToken Authentication token for API calls
     */
    public void createLoginSession(int userId, String email, String name, boolean isDoctor, String authToken) {
        // Storing login value as TRUE
        editor.putBoolean(KEY_IS_LOGGEDIN, true);

        // Storing user id
        editor.putInt(KEY_USER_ID, userId);

        // Storing email in pref
        editor.putString(KEY_EMAIL, email);

        // Storing name in pref
        editor.putString(KEY_NAME, name);

        // Storing user type
        editor.putBoolean(KEY_IS_DOCTOR, isDoctor);

        // Storing auth token
        editor.putString(KEY_AUTH_TOKEN, authToken);

        // Commit changes
        editor.commit();
    }

    /**
     * Check login method will check user login status
     * If false it will redirect user to login page
     * Else won't do anything
     * */
    public boolean checkLogin() {
        // Check login status
        if (!this.isLoggedIn()) {
            // user is not logged in redirect him to Login Activity
            return false;
        }
        return true;
    }

    /**
     * Get stored session data
     * */
    public boolean isLoggedIn() {
        return pref.getBoolean(KEY_IS_LOGGEDIN, false);
    }

    /**
     * Get stored user id
     * */
    public int getUserId() {
        return pref.getInt(KEY_USER_ID, 0);
    }

    /**
     * Get stored email
     * */
    public String getUserEmail() {
        return pref.getString(KEY_EMAIL, null);
    }

    public String getUserName() {
        return pref.getString(KEY_NAME, null);
    }

  
    public boolean isDoctor() {
        return pref.getBoolean(KEY_IS_DOCTOR, false);
    }


    public String getAuthToken() {
        return pref.getString(KEY_AUTH_TOKEN, null);
    }

   
    public void logout() {
        editor.clear();
        editor.commit();
    }
}