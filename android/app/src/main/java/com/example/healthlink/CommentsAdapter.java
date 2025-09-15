package com.example.healthlink;

import android.content.Context;
import android.util.Log;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.TextView;
import androidx.annotation.NonNull;
import androidx.recyclerview.widget.RecyclerView;
import com.bumptech.glide.Glide;
import java.text.SimpleDateFormat;
import java.util.Date;
import java.util.List;
import java.util.Locale;
import java.util.concurrent.TimeUnit;

public class CommentsAdapter extends RecyclerView.Adapter<CommentsAdapter.CommentViewHolder> {
    private static final String TAG = "CommentsAdapter";
    private Context context;
    private List<Comment> commentsList;
    private String serverIp;

    public CommentsAdapter(Context context, List<Comment> commentsList) {
        this.context = context;
        this.commentsList = commentsList;
        this.serverIp = context.getString(R.string.server_ip);
        Log.d(TAG, "Adapter created with " + commentsList.size() + " comments");
    }

    @NonNull
    @Override
    public CommentViewHolder onCreateViewHolder(@NonNull ViewGroup parent, int viewType) {
        View view = LayoutInflater.from(context).inflate(R.layout.comment_item, parent, false);
        Log.d(TAG, "Creating view holder");
        return new CommentViewHolder(view);
    }

    @Override
    public void onBindViewHolder(@NonNull CommentViewHolder holder, int position) {
        Comment comment = commentsList.get(position);
        Log.d(TAG, "Binding comment at position " + position + ": " + comment.getCommentText());

        // Set user name
        if (holder.userName != null) {
            holder.userName.setText(comment.getUserName() != null ? comment.getUserName() : "Unknown User");
        }

        // Set comment text
        if (holder.commentText != null) {
            holder.commentText.setText(comment.getCommentText() != null ? comment.getCommentText() : "");
        }

        // Set comment time
        if (holder.commentTime != null) {
            holder.commentTime.setText(getTimeAgo(comment.getCreatedAt()));
        }

        // Load profile picture
        if (holder.profilePic != null) {
            String profilePicFilename = comment.getProfilePic();
            Log.d(TAG, "Profile pic filename: " + profilePicFilename);
            if (profilePicFilename != null && !profilePicFilename.isEmpty() &&
                    !profilePicFilename.equals("default_profile.jpg") && !profilePicFilename.equals("null")) {
                // Check if it's already a full URL
                String profilePicUrl;
                if (profilePicFilename.startsWith("http")) {
                    profilePicUrl = profilePicFilename;
                } else {
                    profilePicUrl = "http://" + serverIp + "/healthlink/web/assets/img/profile/" + profilePicFilename;
                }
                Log.d(TAG, "Loading profile pic from: " + profilePicUrl);
                Glide.with(context)
                        .load(profilePicUrl)
                        .circleCrop()
                        .placeholder(R.drawable.profile_placeholder)
                        .error(R.drawable.profile_placeholder)
                        .into(holder.profilePic);
            } else {
                Log.d(TAG, "Using placeholder for profile pic");
                holder.profilePic.setImageResource(R.drawable.profile_placeholder);
            }
        }
    }

    @Override
    public int getItemCount() {
        Log.d(TAG, "Getting item count: " + commentsList.size());
        return commentsList.size();
    }

    private String getTimeAgo(String rawTimestamp) {
        try {
            SimpleDateFormat format = new SimpleDateFormat("yyyy-MM-dd HH:mm:ss", Locale.getDefault());
            Date commentDate = format.parse(rawTimestamp);
            long commentMillis = commentDate.getTime();
            long nowMillis = System.currentTimeMillis();
            long diff = nowMillis - commentMillis;
            long seconds = TimeUnit.MILLISECONDS.toSeconds(diff);
            long minutes = TimeUnit.MILLISECONDS.toMinutes(diff);
            long hours = TimeUnit.MILLISECONDS.toHours(diff);
            long days = TimeUnit.MILLISECONDS.toDays(diff);
            if (seconds < 60) return seconds + "s ago";
            else if (minutes < 60) return minutes + "m ago";
            else if (hours < 24) return hours + "h ago";
            else if (days < 7) return days + "d ago";
            else return format.format(commentDate);
        } catch (Exception e) {
            Log.e("TimeAgoError", "Failed to parse timestamp: " + rawTimestamp);
            return "just now";
        }
    }

    public static class CommentViewHolder extends RecyclerView.ViewHolder {
        de.hdodenhof.circleimageview.CircleImageView profilePic;
        TextView userName, commentText, commentTime;

        public CommentViewHolder(@NonNull View itemView) {
            super(itemView);
            profilePic = itemView.findViewById(R.id.comment_profile_pic);
            userName = itemView.findViewById(R.id.comment_user_name);
            commentText = itemView.findViewById(R.id.comment_text);
            commentTime = itemView.findViewById(R.id.comment_time);
            Log.d(TAG, "View holder created");
        }
    }
}