package com.example.healthlink;

import android.content.Context;
import android.content.Intent;
import android.util.Log;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.ImageView;
import android.widget.TextView;
import androidx.annotation.NonNull;
import androidx.recyclerview.widget.RecyclerView;
import com.bumptech.glide.Glide;
import com.bumptech.glide.load.engine.DiskCacheStrategy;
import java.util.List;

public class UserSearchAdapter extends RecyclerView.Adapter<UserSearchAdapter.UserViewHolder> {
    private static final String TAG = "UserSearchAdapter";
    private Context context;
    private List<User> userList;
    private String serverIp;

    public UserSearchAdapter(Context context, List<User> userList) {
        this.context = context;
        this.userList = userList;
        this.serverIp = context.getString(R.string.server_ip);
        Log.d(TAG, "✅ UserSearchAdapter created with server IP: " + serverIp);
    }

    @NonNull
    @Override
    public UserViewHolder onCreateViewHolder(@NonNull ViewGroup parent, int viewType) {
        View view = LayoutInflater.from(context).inflate(R.layout.item_user_search, parent, false);
        return new UserViewHolder(view);
    }

    @Override
    public void onBindViewHolder(@NonNull UserViewHolder holder, int position) {
        User user = userList.get(position);
        try {
            // Set display name
            String displayName = user.getDisplayName();
            if (displayName == null || displayName.trim().isEmpty()) {
                displayName = (user.getFirstName() + " " + user.getLastName()).trim();
                if (displayName.isEmpty()) {
                    displayName = user.getUsername();
                }
            }
            holder.userName.setText(displayName);
            Log.d(TAG, "✅ Display name set: " + displayName);

            // Set username
            holder.userUsername.setText("@" + user.getUsername());

            // Set bio
            String bio = user.getBio();
            if (bio == null || bio.trim().isEmpty() || bio.equals("null")) {
                holder.userBio.setText("No bio available");
                holder.userBio.setAlpha(0.6f);
            } else {
                holder.userBio.setText(bio);
                holder.userBio.setAlpha(1.0f);
            }

            // Load profile image
            loadProfileImage(holder.userImage, user.getProfilePic());

            // Set click listener
            holder.itemView.setOnClickListener(v -> {
                Log.d(TAG, "🔗 User clicked: " + user.getUsername() + " (ID: " + user.getId() + ")");
                Intent intent = new Intent(context, UserProfileActivity.class);
                intent.putExtra("user_id", user.getId());
                intent.putExtra("username", user.getUsername());
                context.startActivity(intent);
            });

        } catch (Exception e) {
            Log.e(TAG, "❌ Error binding user at position " + position, e);
        }
    }

    private void loadProfileImage(ImageView imageView, String profilePic) {
        try {
            if (profilePic != null && !profilePic.isEmpty() && !profilePic.equals("null") && !profilePic.equals("default_profile.jpg")) {
                String imageUrl = "http://" + serverIp + "/healthlink/web/assets/img/profile/" + profilePic;
                Log.d(TAG, "🖼️ Loading profile image: " + imageUrl);
                Glide.with(context)
                        .load(imageUrl + "?t=" + System.currentTimeMillis())
                        .circleCrop()
                        .placeholder(R.drawable.default_profile)
                        .error(R.drawable.default_profile)
                        .diskCacheStrategy(DiskCacheStrategy.ALL)
                        .into(imageView);
            } else {
                Log.d(TAG, "🖼️ Using default profile image");
                imageView.setImageResource(R.drawable.default_profile);
            }
        } catch (Exception e) {
            Log.e(TAG, "❌ Error loading profile image", e);
            imageView.setImageResource(R.drawable.default_profile);
        }
    }

    @Override
    public int getItemCount() {
        return userList.size();
    }

    static class UserViewHolder extends RecyclerView.ViewHolder {
        ImageView userImage;
        TextView userName, userUsername, userBio;

        public UserViewHolder(@NonNull View itemView) {
            super(itemView);
            // FIXED: Using correct IDs from your item_user_search layout
            userImage = itemView.findViewById(R.id.userImage);
            userName = itemView.findViewById(R.id.userName);
            userUsername = itemView.findViewById(R.id.userUsername);
            userBio = itemView.findViewById(R.id.userBio);
            Log.d("UserViewHolder", "✅ ViewHolder created with correct IDs");
        }
    }
}