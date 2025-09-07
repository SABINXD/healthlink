package com.example.healthlink;
import android.content.Context;
import android.content.Intent;
import android.graphics.Color;
import android.util.Log;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.*;
import androidx.annotation.NonNull;
import androidx.cardview.widget.CardView;
import androidx.recyclerview.widget.RecyclerView;
import com.bumptech.glide.Glide;
import com.bumptech.glide.load.engine.DiskCacheStrategy;
import java.util.List;
import de.hdodenhof.circleimageview.CircleImageView;

public class ProfilePostAdapter extends RecyclerView.Adapter<ProfilePostAdapter.ProfilePostViewHolder> {
    private static final String TAG = "ProfilePostAdapter";
    private Context context;
    private List<Post> posts;
    private String serverIp;
    private int currentUserId;

    public ProfilePostAdapter(Context context, List<Post> posts, String serverIp, int currentUserId) {
        this.context = context;
        this.posts = posts;
        this.serverIp = serverIp;
        this.currentUserId = currentUserId;
        Log.d(TAG, "Adapter created with " + posts.size() + " posts");
    }

    @NonNull
    @Override
    public ProfilePostViewHolder onCreateViewHolder(@NonNull ViewGroup parent, int viewType) {
        View view = LayoutInflater.from(context).inflate(R.layout.item_profile_post, parent, false);
        Log.d(TAG, "Creating view holder");
        return new ProfilePostViewHolder(view);
    }

    @Override
    public void onBindViewHolder(@NonNull ProfilePostViewHolder holder, int position) {
        try {
            bindPost(holder, position);
        } catch (Exception e) {
            Log.e(TAG, "Error binding view at position " + position, e);
        }
    }

    private void bindPost(@NonNull ProfilePostViewHolder holder, int position) {
        Post post = posts.get(position);
        Log.d(TAG, "Binding post at position " + position);
        try {
            // Set user info - no anonymous handling needed for profile posts
            Log.d(TAG, "Post " + post.getId() + " - showing user info");
            holder.userName.setVisibility(View.VISIBLE);
            holder.profilePic.setVisibility(View.VISIBLE);
            holder.userName.setText(post.getUserName() != null ? post.getUserName() : "Unknown User");

            // Load profile picture
            if (post.getProfilePic() != null && !post.getProfilePic().isEmpty() && !post.getProfilePic().equals("null")) {
                String profilePicUrl;
                if (post.getProfilePic().startsWith("http")) {
                    profilePicUrl = post.getProfilePic();
                } else {
                    profilePicUrl = "http://" + serverIp + "/healthlink/web/assets/img/profile/" + post.getProfilePic();
                }
                Glide.with(context)
                        .load(profilePicUrl)
                        .circleCrop()
                        .placeholder(R.drawable.profile_placeholder)
                        .error(R.drawable.profile_placeholder)
                        .diskCacheStrategy(DiskCacheStrategy.ALL)
                        .into(holder.profilePic);
            } else {
                holder.profilePic.setImageResource(R.drawable.profile_placeholder);
            }

            // Set click listeners for profile navigation
            holder.profilePic.setOnClickListener(v -> {
                Intent intent = new Intent(context, ProfileActivity.class);
                intent.putExtra("user_id", post.getUserId());
                context.startActivity(intent);
            });

            holder.userName.setOnClickListener(v -> {
                Intent intent = new Intent(context, ProfileActivity.class);
                intent.putExtra("user_id", post.getUserId());
                context.startActivity(intent);
            });

            // Set post time with null check
            if (holder.postTime != null) {
                holder.postTime.setText(post.getCreatedAt() != null ? post.getCreatedAt() : "Just now");
            }

            // Set post description with null check
            if (holder.postDescription != null) {
                if (post.getPostDescription() != null && !post.getPostDescription().isEmpty() && !post.getPostDescription().equals("null")) {
                    holder.postDescription.setText(post.getPostDescription());
                    holder.postDescription.setVisibility(View.VISIBLE);
                } else {
                    holder.postDescription.setVisibility(View.GONE);
                }
            }

            // Handle post image with null check
            if (holder.postImage != null && holder.imageCard != null) {
                if (post.getPostImage() != null && !post.getPostImage().isEmpty() && !post.getPostImage().equals("null")) {
                    String imageUrl;
                    if (post.getPostImage().startsWith("http")) {
                        imageUrl = post.getPostImage();
                    } else {
                        imageUrl = "http://" + serverIp + "/healthlink/web/assets/img/posts/" + post.getPostImage();
                    }
                    Log.d(TAG, "Loading image from: " + imageUrl);
                    holder.imageCard.setVisibility(View.VISIBLE);
                    Glide.with(context)
                            .load(imageUrl)
                            .placeholder(R.drawable.post_placeholder)
                            .error(R.drawable.post_placeholder)
                            .diskCacheStrategy(DiskCacheStrategy.ALL)
                            .into(holder.postImage);
                } else {
                    holder.imageCard.setVisibility(View.GONE);
                }
            }

            // Handle AI summary
            if (holder.aiSummaryCard != null && holder.aiSummaryText != null) {
                if (post.getPostImage() != null && !post.getPostImage().isEmpty() && !post.getPostImage().equals("null")) {
                    holder.aiSummaryCard.setVisibility(View.VISIBLE);
                    if (post.hasAiSummary()) {
                        String aiSummary = post.getAiSummary();
                        holder.aiSummaryText.setText(aiSummary);

                        // Set color based on content type
                        if (aiSummary.contains("Medical Summary")) {
                            holder.aiSummaryText.setTextColor(Color.parseColor("#2E7D32")); // Green for medical
                        } else if (aiSummary.contains("does not appear to be a medical report")) {
                            holder.aiSummaryText.setTextColor(Color.parseColor("#F57C00")); // Orange for non-medical
                        } else {
                            holder.aiSummaryText.setTextColor(Color.parseColor("#6B7280")); // Gray for system messages
                        }

                        if (holder.aiSummaryProgress != null) {
                            holder.aiSummaryProgress.setVisibility(View.GONE);
                        }
                        Log.d(TAG, "Showing AI summary for post " + post.getId());
                    } else {
                        holder.aiSummaryText.setText("AI analysis in progress...");
                        holder.aiSummaryText.setTextColor(Color.parseColor("#9CA3AF"));
                        if (holder.aiSummaryProgress != null) {
                            holder.aiSummaryProgress.setVisibility(View.VISIBLE);
                        }
                        Log.d(TAG, "Showing AI analysis in progress for post " + post.getId());
                    }
                } else {
                    holder.aiSummaryCard.setVisibility(View.GONE);
                }
            }

            // Set like count with null check
            if (holder.likeCount != null) {
                holder.likeCount.setText(String.valueOf(post.getLikeCount()));
            }

            // Set comment count with null check
            if (holder.commentCount != null) {
                holder.commentCount.setText(String.valueOf(post.getCommentCount()));
            }

            // Set like button state
            if (holder.likeIcon != null) {
                if (post.isLikedByCurrentUser()) {
                    holder.likeIcon.setImageResource(R.drawable.like_icon_filled);
                    holder.likeIcon.setColorFilter(Color.parseColor("#FF6B6B"));
                } else {
                    holder.likeIcon.setImageResource(R.drawable.like_icon);
                    holder.likeIcon.setColorFilter(Color.parseColor("#666666"));
                }
            }

            // Set click listeners with null checks
            if (holder.likeLayout != null) {
                holder.likeLayout.setOnClickListener(v -> {
                    Log.d(TAG, "Like clicked for post " + post.getId());
                    boolean newLikeState = !post.isLikedByCurrentUser();
                    int newLikeCount = post.getLikeCount() + (newLikeState ? 1 : -1);
                    post.setLikedByCurrentUser(newLikeState);
                    post.setLikeCount(Math.max(0, newLikeCount));
                    if (holder.likeIcon != null) {
                        if (newLikeState) {
                            holder.likeIcon.setImageResource(R.drawable.like_icon_filled);
                            holder.likeIcon.setColorFilter(Color.parseColor("#FF6B6B"));
                        } else {
                            holder.likeIcon.setImageResource(R.drawable.like_icon);
                            holder.likeIcon.setColorFilter(Color.parseColor("#666666"));
                        }
                    }
                    if (holder.likeCount != null) {
                        holder.likeCount.setText(String.valueOf(Math.max(0, newLikeCount)));
                    }
                    toggleLikeOnServer(post);
                });
            }

            if (holder.commentLayout != null) {
                holder.commentLayout.setOnClickListener(v -> {
                    Log.d(TAG, "Comment clicked for post " + post.getId());
                    Intent intent = new Intent(context, CommentActivity.class);
                    intent.putExtra("post_id", post.getId());
                    intent.putExtra("post_img", post.getPostImage());
                    intent.putExtra("post_text", post.getPostDescription());
                    intent.putExtra("user_name", post.getUserName());
                    intent.putExtra("user_profile_pic", post.getProfilePic());
                    intent.putExtra("created_at", post.getCreatedAt());
                    context.startActivity(intent);
                });
            }

            // Only show post options for own posts (no anonymous posts to worry about)
            if (holder.postOptions != null) {
                boolean isOwnPost = (post.getUserId() == currentUserId);
                holder.postOptions.setVisibility(isOwnPost ? View.VISIBLE : View.GONE);
                if (isOwnPost) {
                    holder.postOptions.setOnClickListener(v -> {
                        Log.d(TAG, "Options clicked for post " + post.getId());
                        // Add your options functionality here
                    });
                }
            }

            holder.itemView.setOnClickListener(v -> {
                Intent intent = new Intent(context, PostDetailsActivity.class);
                intent.putExtra("post_id", post.getId());
                intent.putExtra("post_img", post.getPostImage());
                intent.putExtra("post_text", post.getPostDescription());
                intent.putExtra("ai_summary", post.getAiSummary());
                intent.putExtra("user_name", post.getUserName());
                intent.putExtra("created_at", post.getCreatedAt());
                context.startActivity(intent);
            });

        } catch (Exception e) {
            Log.e(TAG, "Error in bindPost at position " + position, e);
        }
    }

    private void toggleLikeOnServer(Post post) {
        String url = "http://" + serverIp + "/healthlink/api/toggle_like.php";
        Log.d(TAG, "Sending like to server for post " + post.getId());
        new Thread(() -> {
            try {
                java.net.URL urlObj = new java.net.URL(url);
                java.net.HttpURLConnection connection = (java.net.HttpURLConnection) urlObj.openConnection();
                connection.setRequestMethod("POST");
                connection.setDoOutput(true);
                String postData = "post_id=" + post.getId() + "&user_id=" + currentUserId;
                try (java.io.OutputStream os = connection.getOutputStream()) {
                    byte[] input = postData.getBytes("utf-8");
                    os.write(input, 0, input.length);
                }
                int responseCode = connection.getResponseCode();
                if (responseCode == 200) {
                    try (java.io.BufferedReader br = new java.io.BufferedReader(
                            new java.io.InputStreamReader(connection.getInputStream(), "utf-8"))) {
                        StringBuilder response = new StringBuilder();
                        String responseLine;
                        while ((responseLine = br.readLine()) != null) {
                            response.append(responseLine.trim());
                        }
                        org.json.JSONObject json = new org.json.JSONObject(response.toString());
                        if ("success".equals(json.getString("status"))) {
                            int serverLikeCount = json.getInt("like_count");
                            boolean serverIsLiked = json.getBoolean("is_liked");
                            post.setLikeCount(serverLikeCount);
                            post.setLikedByCurrentUser(serverIsLiked);
                            ((android.app.Activity) context).runOnUiThread(() -> {
                                notifyDataSetChanged();
                            });
                        }
                    }
                }
            } catch (Exception e) {
                Log.e(TAG, "Error toggling like on server", e);
            }
        }).start();
    }

    @Override
    public int getItemCount() {
        return posts != null ? posts.size() : 0;
    }

    public static class ProfilePostViewHolder extends RecyclerView.ViewHolder {
        CircleImageView profilePic;
        TextView userName, postTime, postDescription, likeCount, commentCount;
        ImageView postImage, likeIcon, commentIcon, postOptions;
        LinearLayout likeLayout, commentLayout;
        View imageCard;
        CardView aiSummaryCard;
        TextView aiSummaryText;
        ProgressBar aiSummaryProgress;

        public ProfilePostViewHolder(@NonNull View itemView) {
            super(itemView);
            try {
                profilePic = itemView.findViewById(R.id.profilePic);
                userName = itemView.findViewById(R.id.userName);
                postTime = itemView.findViewById(R.id.postTime);
                postDescription = itemView.findViewById(R.id.postDescription);
                likeCount = itemView.findViewById(R.id.likeCount);
                commentCount = itemView.findViewById(R.id.commentCount);
                postImage = itemView.findViewById(R.id.postImage);
                likeIcon = itemView.findViewById(R.id.likeIcon);
                commentIcon = itemView.findViewById(R.id.commentIcon);
                postOptions = itemView.findViewById(R.id.postOptions);
                likeLayout = itemView.findViewById(R.id.likeLayout);
                commentLayout = itemView.findViewById(R.id.commentLayout);
                imageCard = itemView.findViewById(R.id.imageCard);
                aiSummaryCard = itemView.findViewById(R.id.ai_summary_card);
                aiSummaryText = itemView.findViewById(R.id.ai_summary_text);
                aiSummaryProgress = itemView.findViewById(R.id.ai_summary_progress);
                View codeLanguage = itemView.findViewById(R.id.code_language);
                View codeLanguageIcon = itemView.findViewById(R.id.code_language_icon);
                if (codeLanguage != null) codeLanguage.setVisibility(View.GONE);
                if (codeLanguageIcon != null) codeLanguageIcon.setVisibility(View.GONE);
                View tagsRecyclerView = itemView.findViewById(R.id.rv_tags);
                if (tagsRecyclerView != null) tagsRecyclerView.setVisibility(View.GONE);
            } catch (Exception e) {
                Log.e(TAG, "Error initializing views in ViewHolder", e);
            }
        }
    }
}