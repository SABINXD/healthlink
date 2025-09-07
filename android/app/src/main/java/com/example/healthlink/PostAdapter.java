package com.example.healthlink;

import android.app.AlertDialog;
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
import com.android.volley.Request;
import com.android.volley.toolbox.StringRequest;
import com.android.volley.toolbox.Volley;
import com.squareup.picasso.Callback;
import com.squareup.picasso.Picasso;
import org.json.JSONException;
import org.json.JSONObject;
import java.text.SimpleDateFormat;
import java.util.Date;
import java.util.HashMap;
import java.util.List;
import java.util.Locale;
import java.util.Map;
import java.util.concurrent.TimeUnit;
import de.hdodenhof.circleimageview.CircleImageView;

public class PostAdapter extends RecyclerView.Adapter<PostAdapter.PostViewHolder> {
    private static final String TAG = "PostAdapter";
    private Context context;
    private List<Post> posts;
    private String serverIp;
    private int currentUserId;

    public PostAdapter(Context context, List<Post> posts, String serverIp, int currentUserId) {
        this.context = context;
        this.posts = posts;
        this.serverIp = serverIp;
        this.currentUserId = currentUserId;
        Log.d(TAG, "PostAdapter created with " + posts.size() + " posts, serverIp: " + serverIp);
    }

    @NonNull
    @Override
    public PostViewHolder onCreateViewHolder(@NonNull ViewGroup parent, int viewType) {
        View view = LayoutInflater.from(context).inflate(R.layout.item_post, parent, false);
        return new PostViewHolder(view);
    }

    @Override
    public void onBindViewHolder(@NonNull PostViewHolder holder, int position) {
        Post post = posts.get(position);
        Log.d(TAG, "Binding post: " + post.getId() + " by " + post.getUserName());

        // Log spoiler info for debugging
        Log.d(TAG, "Post ID: " + post.getId() +
                ", spoiler: " + post.isSpoiler() +
                ", isUnblurred: " + post.isUnblurred());

        try {
            // Set user info
            if (holder.userName != null) {
                holder.userName.setText(post.getUserName() != null ? post.getUserName() : "Unknown User");
            }

            if (holder.postDescription != null) {
                String description = post.getPostDescription();
                if (description != null && !description.isEmpty() && !description.equals("null")) {
                    holder.postDescription.setText(description);
                    holder.postDescription.setVisibility(View.VISIBLE);
                } else {
                    holder.postDescription.setVisibility(View.GONE);
                }
            }

            if (holder.likeCount != null) {
                holder.likeCount.setText(String.valueOf(post.getLikeCount()));
            }

            if (holder.commentCount != null) {
                holder.commentCount.setText(String.valueOf(post.getCommentCount()));
            }

            if (holder.postTime != null) {
                holder.postTime.setText(getTimeAgo(post.getCreatedAt()));
            }

            // Load profile picture
            loadProfilePicture(holder, post);

            // Load post image with spoiler handling
            loadPostImage(holder, post);

            // Display AI summary
            displayAiSummary(holder, post, position);

            // Set like button state
            updateLikeButtonImmediate(holder, post.isLikedByCurrentUser());

            // Setup click listeners
            setupClickListeners(holder, post, position);

            // Only show post options for own posts
            if (holder.postOptions != null) {
                boolean isOwnPost = (post.getUserId() == currentUserId);
                holder.postOptions.setVisibility(isOwnPost ? View.VISIBLE : View.GONE);
            }
        } catch (Exception e) {
            Log.e(TAG, "Error binding post at position " + position, e);
        }
    }

    // Fixed method to handle spoiler functionality
    private void loadPostImage(PostViewHolder holder, Post post) {
        try {
            String postImageUrl = post.getPostImage();
            Log.d(TAG, "Post image URL: " + postImageUrl);

            if (postImageUrl != null && !postImageUrl.isEmpty() && !postImageUrl.equals("null")) {
                String fullImageUrl;
                if (postImageUrl.startsWith("http")) {
                    fullImageUrl = postImageUrl;
                } else {
                    fullImageUrl = "http://" + serverIp + "/healthlink1/web/assets/img/posts/" + postImageUrl;
                }

                Log.d(TAG, "Loading post image: " + fullImageUrl);

                if (holder.postImage != null && holder.imageCard != null) {
                    holder.imageCard.setVisibility(View.VISIBLE);

                    // Handle spoiler functionality
                    if (post.isSpoiler() && !post.isUnblurred()) {
                        Log.d(TAG, "=== SPOILER DETECTED ===");
                        Log.d(TAG, "Post ID: " + post.getId() + " is marked as spoiler");

                        // Hide original image, show spoiler container
                        holder.postImage.setVisibility(View.GONE);
                        if (holder.spoilerContainer != null) {
                            holder.spoilerContainer.setVisibility(View.VISIBLE);

                            // Set spoiler background color
                            holder.spoilerContainer.setBackgroundColor(Color.parseColor("#000000"));

                            // Set spoiler text
                            if (holder.spoilerText != null) {
                                holder.spoilerText.setText("This content has been marked as sensitive");
                                holder.spoilerText.setTextColor(Color.parseColor("#FFFFFF"));
                            }

                            // Load the image in the background (but keep it hidden)
                            Picasso.get()
                                    .load(fullImageUrl)
                                    .placeholder(R.drawable.media_placeholder)
                                    .error(R.drawable.media_placeholder)
                                    .fit()
                                    .centerCrop()
                                    .into(holder.postImage, new Callback() {
                                        @Override
                                        public void onSuccess() {
                                            Log.d(TAG, "Image loaded for spoiler post: " + post.getId());
                                            // Image is loaded but hidden behind spoiler overlay
                                        }

                                        @Override
                                        public void onError(Exception e) {
                                            Log.e(TAG, "Error loading image for spoiler post: " + post.getId(), e);
                                        }
                                    });

                            // Set unblur button click listener
                            if (holder.unblurButton != null) {
                                holder.unblurButton.setOnClickListener(v -> {
                                    Log.d(TAG, "Unblur button clicked for post: " + post.getId());
                                    post.setUnblurred(true);
                                    holder.postImage.setVisibility(View.VISIBLE);
                                    holder.spoilerContainer.setVisibility(View.GONE);
                                    Toast.makeText(context, "Content unblurred", Toast.LENGTH_SHORT).show();
                                });
                            }
                        }
                    } else {
                        Log.d(TAG, "=== NO SPOILER ===");
                        Log.d(TAG, "Post ID: " + post.getId() + " is not marked as spoiler or already unblurred");

                        // Show original image, hide spoiler container
                        holder.postImage.setVisibility(View.VISIBLE);
                        if (holder.spoilerContainer != null) {
                            holder.spoilerContainer.setVisibility(View.GONE);
                        }

                        // Load original image
                        Picasso.get()
                                .load(fullImageUrl)
                                .placeholder(R.drawable.media_placeholder)
                                .error(R.drawable.media_placeholder)
                                .fit()
                                .centerCrop()
                                .into(holder.postImage);
                    }
                }
            } else {
                Log.d(TAG, "No image for post: " + post.getId());
                if (holder.imageCard != null) {
                    holder.imageCard.setVisibility(View.GONE);
                }
            }
        } catch (Exception e) {
            Log.e(TAG, "Error loading post image", e);
            if (holder.imageCard != null) {
                holder.imageCard.setVisibility(View.GONE);
            }
        }
    }

    private void displayAiSummary(PostViewHolder holder, Post post, int position) {
        if (holder.aiSummaryCard != null) {
            // Show AI summary card if post has an image
            if (post.getPostImage() != null && !post.getPostImage().isEmpty() && !post.getPostImage().equals("null")) {
                holder.aiSummaryCard.setVisibility(View.VISIBLE);

                // Check if we have AI summary content
                if (post.hasAiSummary()) {
                    String aiSummary = post.getAiSummary();
                    holder.aiSummaryText.setText(aiSummary);
                    holder.aiSummaryText.setTextColor(Color.parseColor("#6B7280"));

                    if (holder.aiSummaryProgress != null) {
                        holder.aiSummaryProgress.setVisibility(View.GONE);
                    }

                    Log.d(TAG, "Displaying AI summary for post " + post.getId() + ": " +
                            aiSummary.substring(0, Math.min(50, aiSummary.length())) + "...");
                } else {
                    holder.aiSummaryText.setText("AI analysis in progress...");
                    holder.aiSummaryText.setTextColor(Color.parseColor("#9CA3AF"));

                    if (holder.aiSummaryProgress != null) {
                        holder.aiSummaryProgress.setVisibility(View.VISIBLE);
                    }

                    // Add refresh mechanism
                    holder.aiSummaryCard.setOnClickListener(v -> {
                        Log.d(TAG, "Manual refresh requested for AI summary of post " + post.getId());
                        if (!post.hasAiSummary()) {
                            refreshAiSummary(post, holder, position);
                        }
                    });
                }
            } else {
                holder.aiSummaryCard.setVisibility(View.GONE);
            }
        }
    }

    private void refreshAiSummary(Post post, PostViewHolder holder, int position) {
        String url = "http://" + serverIp + "/healthlink1/api/refresh_ai_summary.php";
        Log.d(TAG, "Refreshing AI summary for post: " + post.getId());

        StringRequest request = new StringRequest(Request.Method.POST, url,
                response -> {
                    try {
                        JSONObject json = new JSONObject(response);
                        if (json.optBoolean("success")) {
                            String aiSummary = json.optString("ai_summary", "");
                            post.setCodeContent(aiSummary);
                            post.setCodeStatus(0); // Non-code post with AI summary

                            // Update UI
                            holder.aiSummaryText.setText(aiSummary);
                            holder.aiSummaryText.setTextColor(Color.parseColor("#6B7280"));
                            holder.aiSummaryProgress.setVisibility(View.GONE);

                            // Remove click listener to prevent multiple refreshes
                            holder.aiSummaryCard.setOnClickListener(null);

                            Log.d(TAG, "AI summary refreshed for post: " + post.getId() + ": " +
                                    aiSummary.substring(0, Math.min(50, aiSummary.length())) + "...");
                        } else {
                            String errorMsg = json.optString("message", "Failed to refresh AI summary");
                            Log.e(TAG, "AI summary refresh error: " + errorMsg);
                            Toast.makeText(context, errorMsg, Toast.LENGTH_SHORT).show();
                        }
                    } catch (JSONException e) {
                        Log.e(TAG, "Error parsing AI summary response", e);
                    }
                },
                error -> {
                    Log.e(TAG, "Error refreshing AI summary", error);
                    Toast.makeText(context, "Network error while refreshing AI summary", Toast.LENGTH_SHORT).show();
                }
        ) {
            @Override
            protected Map<String, String> getParams() {
                Map<String, String> params = new HashMap<>();
                params.put("post_id", String.valueOf(post.getId()));
                return params;
            }
        };

        Volley.newRequestQueue(context).add(request);
    }

    private void loadProfilePicture(PostViewHolder holder, Post post) {
        try {
            String profilePicUrl = post.getProfilePic();
            if (profilePicUrl != null && !profilePicUrl.isEmpty() && !profilePicUrl.equals("null")) {
                if (!profilePicUrl.startsWith("http")) {
                    profilePicUrl = "http://" + serverIp + "/healthlink1/web/assets/img/profile/" + profilePicUrl;
                }

                if (holder.profilePic != null) {
                    Picasso.get()
                            .load(profilePicUrl)
                            .placeholder(R.drawable.profile_placeholder)
                            .error(R.drawable.profile_placeholder)
                            .into(holder.profilePic);
                }
            } else {
                if (holder.profilePic != null) {
                    holder.profilePic.setImageResource(R.drawable.profile_placeholder);
                }
            }
        } catch (Exception e) {
            Log.e(TAG, "Error loading profile picture", e);
        }
    }

    private void updateLikeButtonImmediate(PostViewHolder holder, boolean isLiked) {
        if (holder.likeIcon != null) {
            if (isLiked) {
                holder.likeIcon.setImageResource(R.drawable.like_icon_filled);
                holder.likeIcon.setColorFilter(Color.parseColor("#FF6B6B"));
            } else {
                holder.likeIcon.setImageResource(R.drawable.like_icon);
                holder.likeIcon.setColorFilter(Color.parseColor("#666666"));
            }
        }
    }

    private void setupClickListeners(PostViewHolder holder, Post post, int position) {
        // Like button click
        if (holder.likeLayout != null) {
            holder.likeLayout.setOnClickListener(v -> {
                boolean newLikeState = !post.isLikedByCurrentUser();
                int newLikeCount = post.getLikeCount() + (newLikeState ? 1 : -1);
                post.setLikedByCurrentUser(newLikeState);
                post.setLikeCount(Math.max(0, newLikeCount));

                updateLikeButtonImmediate(holder, newLikeState);
                if (holder.likeCount != null) {
                    holder.likeCount.setText(String.valueOf(Math.max(0, newLikeCount)));
                }

                toggleLikeOnServer(post, holder, position);
            });
        }

        // Comment button click
        if (holder.commentLayout != null) {
            holder.commentLayout.setOnClickListener(v -> {
                Intent intent = new Intent(context, CommentActivity.class);
                intent.putExtra("post_id", post.getId());
                intent.putExtra("post_img", post.getPostImage());
                intent.putExtra("post_text", post.getPostDescription());
                intent.putExtra("user_name", post.getUserName());
                intent.putExtra("created_at", post.getCreatedAt());
                intent.putExtra("user_profile_pic", post.getProfilePic());
                context.startActivity(intent);
            });
        }

        // Post options - Only show for own posts
        if (holder.postOptions != null && post.getUserId() == currentUserId) {
            holder.postOptions.setOnClickListener(v -> {
                showPostOptionsMenu(holder, post, position);
            });
        }

        // Post image click
        if (holder.postImage != null) {
            holder.postImage.setOnClickListener(v -> {
                Intent intent = new Intent(context, PostDetailsActivity.class);
                intent.putExtra("post_id", post.getId());
                intent.putExtra("post_img", post.getPostImage());
                intent.putExtra("post_text", post.getPostDescription());
                intent.putExtra("user_name", post.getUserName());
                intent.putExtra("created_at", post.getCreatedAt());
                context.startActivity(intent);
            });
        }
    }

    private void showPostOptionsMenu(PostViewHolder holder, Post post, int position) {
        PopupMenu popup = new PopupMenu(context, holder.postOptions);
        popup.getMenuInflater().inflate(R.menu.menu_post_own, popup.getMenu());

        popup.setOnMenuItemClickListener(item -> {
            int id = item.getItemId();
            if (id == R.id.menu_delete_post) {
                showDeleteConfirmation(post.getId(), position);
                return true;
            }
            return false;
        });

        popup.show();
    }

    private void showDeleteConfirmation(int postId, int position) {
        new AlertDialog.Builder(context)
                .setTitle("Delete Post")
                .setMessage("Are you sure you want to delete this post?")
                .setPositiveButton("Delete", (dialog, which) -> deletePost(postId, position))
                .setNegativeButton("Cancel", null)
                .show();
    }

    private void deletePost(int postId, int position) {
        String url = "http://" + serverIp + "/healthlink1/api/delete_post.php";
        Log.d(TAG, "Deleting post with ID: " + postId);

        StringRequest request = new StringRequest(Request.Method.POST, url,
                response -> {
                    Log.d(TAG, "Delete response: " + response);
                    try {
                        JSONObject json = new JSONObject(response);
                        String status = json.optString("status", "");

                        if ("success".equals(status)) {
                            if (position >= 0 && position < posts.size()) {
                                posts.remove(position);
                                notifyItemRemoved(position);
                                notifyItemRangeChanged(position, posts.size());
                                Toast.makeText(context, "Post deleted successfully", Toast.LENGTH_SHORT).show();
                            }
                        } else {
                            String errorMsg = json.optString("message", "Delete failed");
                            Toast.makeText(context, "Failed to delete: " + errorMsg, Toast.LENGTH_LONG).show();
                        }
                    } catch (JSONException e) {
                        Log.e(TAG, "Error parsing delete response", e);
                        Toast.makeText(context, "Invalid server response", Toast.LENGTH_SHORT).show();
                    }
                },
                error -> {
                    Log.e(TAG, "Delete error: " + error.toString());
                    Toast.makeText(context, "Delete failed: network error", Toast.LENGTH_SHORT).show();
                }) {
            @Override
            protected Map<String, String> getParams() {
                Map<String, String> params = new HashMap<>();
                params.put("post_id", String.valueOf(postId));
                params.put("user_id", String.valueOf(currentUserId));
                return params;
            }
        };

        Volley.newRequestQueue(context).add(request);
    }

    private void toggleLikeOnServer(Post post, PostViewHolder holder, int position) {
        String url = "http://" + serverIp + "/healthlink1/api/toggle_like.php";
        Log.d(TAG, "Sending like to server for post " + post.getId());

        StringRequest request = new StringRequest(Request.Method.POST, url,
                response -> {
                    Log.d(TAG, "Like server response: " + response);
                    try {
                        JSONObject json = new JSONObject(response);
                        if ("success".equals(json.getString("status"))) {
                            int serverLikeCount = json.getInt("like_count");
                            boolean serverIsLiked = json.getBoolean("is_liked");

                            post.setLikeCount(serverLikeCount);
                            post.setLikedByCurrentUser(serverIsLiked);

                            updateLikeButtonImmediate(holder, serverIsLiked);
                            if (holder.likeCount != null) {
                                holder.likeCount.setText(String.valueOf(serverLikeCount));
                            }
                        } else {
                            Log.e(TAG, "Server like error: " + json.optString("message"));
                        }
                    } catch (JSONException e) {
                        Log.e(TAG, "Error parsing like response", e);
                    }
                },
                error -> {
                    Log.e(TAG, "Like network error: " + error.toString());
                }) {
            @Override
            protected Map<String, String> getParams() {
                Map<String, String> params = new HashMap<>();
                params.put("post_id", String.valueOf(post.getId()));
                params.put("user_id", String.valueOf(currentUserId));
                return params;
            }
        };

        Volley.newRequestQueue(context).add(request);
    }

    private String getTimeAgo(String rawTimestamp) {
        try {
            SimpleDateFormat format = new SimpleDateFormat("yyyy-MM-dd HH:mm:ss", Locale.getDefault());
            Date postDate = format.parse(rawTimestamp);
            if (postDate == null) return "just now";

            long diff = System.currentTimeMillis() - postDate.getTime();
            if (diff < 0) return "just now";

            long seconds = TimeUnit.MILLISECONDS.toSeconds(diff);
            long minutes = TimeUnit.MILLISECONDS.toMinutes(diff);
            long hours = TimeUnit.MILLISECONDS.toHours(diff);
            long days = TimeUnit.MILLISECONDS.toDays(diff);

            if (seconds < 60) return seconds + "s ago";
            else if (minutes < 60) return minutes + "m ago";
            else if (hours < 24) return hours + "h ago";
            else if (days < 7) return days + "d ago";
            else if (days < 30) return (days / 7) + "w ago";
            else if (days < 365) return (days / 30) + "mo ago";
            else return (days / 365) + "y ago";
        } catch (Exception e) {
            return "just now";
        }
    }

    @Override
    public int getItemCount() {
        return posts.size();
    }

    public void updatePosts(List<Post> newPosts) {
        this.posts.clear();
        this.posts.addAll(newPosts);
        notifyDataSetChanged();
    }

    public static class PostViewHolder extends RecyclerView.ViewHolder {
        CircleImageView profilePic;
        TextView userName, postTime, postDescription, likeCount, commentCount;
        ImageView postImage, likeIcon, commentIcon, postOptions;
        LinearLayout likeLayout, commentLayout, spoilerContainer;
        View imageCard;
        CardView aiSummaryCard;
        TextView aiSummaryText, spoilerText;
        ProgressBar aiSummaryProgress;
        Button unblurButton;

        public PostViewHolder(@NonNull View itemView) {
            super(itemView);
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
            spoilerContainer = itemView.findViewById(R.id.spoilerContainer);
            spoilerText = itemView.findViewById(R.id.spoilerText);
            aiSummaryCard = itemView.findViewById(R.id.ai_summary_card);
            aiSummaryText = itemView.findViewById(R.id.ai_summary_text);
            aiSummaryProgress = itemView.findViewById(R.id.ai_summary_progress);
            unblurButton = itemView.findViewById(R.id.unblurButton);
        }
    }
}