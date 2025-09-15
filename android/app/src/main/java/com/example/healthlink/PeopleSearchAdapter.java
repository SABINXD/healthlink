package com.example.healthlink;

import android.content.Context;
import android.content.Intent;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.ImageView;
import android.widget.TextView;
import androidx.recyclerview.widget.RecyclerView;
import com.bumptech.glide.Glide;
import com.google.android.material.button.MaterialButton;
import com.google.android.material.card.MaterialCardView;
import java.util.List;

public class PeopleSearchAdapter extends RecyclerView.Adapter<PeopleSearchAdapter.UserViewHolder> {
    private Context context;
    private List<User> userList;
    private OnUserClickListener listener;

    public interface OnUserClickListener {
        void onUserClick(User user);
        void onFollowClick(User user);
    }

    public PeopleSearchAdapter(Context context, List<User> userList, OnUserClickListener listener) {
        this.context = context;
        this.userList = userList;
        this.listener = listener;
    }

    @Override
    public UserViewHolder onCreateViewHolder(ViewGroup parent, int viewType) {
        View view = LayoutInflater.from(context).inflate(R.layout.item_user_search, parent, false);
        return new UserViewHolder(view);
    }

    @Override
    public void onBindViewHolder(UserViewHolder holder, int position) {
        User user = userList.get(position);

        holder.userName.setText(user.getDisplayName());
        holder.userUsername.setText("@" + user.getUsername());
        holder.userBio.setText(user.getBio().isEmpty() ? "No bio available" : user.getBio());

        String imageUrl = "http://" + context.getString(R.string.server_ip) +
                "/healthlink1/uploads/profiles/" + user.getProfilePic();
        Glide.with(context)
                .load(imageUrl)
                .placeholder(R.drawable.default_profile)
                .error(R.drawable.default_profile)
                .circleCrop()
                .into(holder.userImage);

        holder.cardView.setOnClickListener(v -> {
            if (listener != null) {
                listener.onUserClick(user);
            }
        });

        holder.followButton.setOnClickListener(v -> {
            if (listener != null) {
                listener.onFollowClick(user);
            }
        });
    }

    @Override
    public int getItemCount() {
        return userList.size();
    }

    public void updateUsers(List<User> newUserList) {
        this.userList = newUserList;
        notifyDataSetChanged();
    }

    public static class UserViewHolder extends RecyclerView.ViewHolder {
        MaterialCardView cardView;
        ImageView userImage;
        TextView userName, userUsername, userBio;
        MaterialButton followButton;

        public UserViewHolder(View itemView) {
            super(itemView);
            cardView = itemView.findViewById(R.id.userCardView);
            userImage = itemView.findViewById(R.id.userImage);
            userName = itemView.findViewById(R.id.userName);
            userUsername = itemView.findViewById(R.id.userUsername);
            userBio = itemView.findViewById(R.id.userBio);
            followButton = itemView.findViewById(R.id.followButton);
        }
    }
}