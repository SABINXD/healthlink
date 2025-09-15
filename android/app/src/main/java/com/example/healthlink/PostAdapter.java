package com.example.healthlink;

import android.app.AlertDialog;
import android.content.Context;
import android.content.Intent;
import android.graphics.Color;
import android.graphics.ColorMatrix;
import android.graphics.ColorMatrixColorFilter;
import android.util.Log;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.*;
import androidx.annotation.NonNull;
import androidx.cardview.widget.CardView;
import androidx.recyclerview.widget.LinearLayoutManager;
import androidx.recyclerview.widget.RecyclerView;
import com.android.volley.Request;
import com.android.volley.toolbox.StringRequest;
import com.android.volley.toolbox.Volley;
import com.squareup.picasso.Callback;
import com.squareup.picasso.Picasso;
import org.json.JSONException;
import org.json.JSONObject;
import java.text.SimpleDateFormat;
import java.util.ArrayList;
import java.util.Arrays;
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
    private SessionManager sessionManager;
    private String serverIp;
    private int currentUserId;

    public PostAdapter(Context context, List<Post> posts, SessionManager sessionManager) {
        this.context = context;
        this.posts = posts;
        this.sessionManager = sessionManager;
        this.serverIp = context.getString(R.string.server_ip);
        this.currentUserId = sessionManager.getUserId();
        Log.d(TAG, "PostAdapter created with " + posts.size() + " posts");
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
        Log.d(TAG, "Binding post: " + post.getId() + " by " + post.getUserName() + ", Spoiler: " + post.isSpoiler());

        try {
            // Set user name
            if (holder.userName != null) {
                holder.userName.setText(post.getUserName() != null ? post.getUserName() : "Unknown User");
            }

            // Set post title (use first line of description as title)
            if (holder.postTitle != null) {
                String description = post.getPostDescription();
                if (description != null && !description.isEmpty()) {
                    String[] lines = description.split("\n");
                    String title = lines[0];
                    if (title.length() > 80) {
                        title = title.substring(0, 80) + "...";
                    }
                    holder.postTitle.setText(title);
                }
            }

            // Set post description
            if (holder.postDescription != null) {
                String description = post.getPostDescription();
                if (description != null && !description.isEmpty() && !description.equals("null")) {
                    holder.postDescription.setText(description);
                    holder.postDescription.setVisibility(View.VISIBLE);
                } else {
                    holder.postDescription.setVisibility(View.GONE);
                }
            }

            // Set like and comment counts
            if (holder.likeCount != null) {
                holder.likeCount.setText(String.valueOf(post.getLikeCount()));
            }

            if (holder.commentCount != null) {
                int commentCount = post.getCommentCount();
                String commentText = commentCount == 1 ? "1 reply" : commentCount + " replies";
                holder.commentCount.setText(commentText);
            }

            // Set post time
            if (holder.postTime != null) {
                holder.postTime.setText(getTimeAgo(post.getCreatedAt()));
            }

            // Setup tags
            setupTags(holder, post);

            loadProfilePicture(holder, post);
            loadPostImage(holder, post);
            // <CHANGE> Removed AI summary display from feed - only show in post details
            hideAiSummary(holder);
            updateLikeButtonImmediate(holder, post.isLikedByCurrentUser());
            setupClickListeners(holder, post, position);

            if (holder.postOptions != null) {
                boolean isOwnPost = (post.getUserId() == currentUserId);
                holder.postOptions.setVisibility(isOwnPost ? View.VISIBLE : View.GONE);
            }
        } catch (Exception e) {
            Log.e(TAG, "Error binding post at position " + position, e);
        }
    }

    private void setupTags(PostViewHolder holder, Post post) {
        if (holder.tagsRecyclerView != null) {
            List<String> tags = new ArrayList<>();

            // Add category as first tag
            if (post.getCategory() != null && !post.getCategory().isEmpty()) {
                tags.add(post.getCategory().toLowerCase());
            }

            // Add other tags if available
            if (post.getTags() != null) {
                tags.addAll(Arrays.asList(post.getTags()));
            }

            if (!tags.isEmpty()) {
                TagAdapter tagAdapter = new TagAdapter(context, tags, false);
                holder.tagsRecyclerView.setLayoutManager(new LinearLayoutManager(context, LinearLayoutManager.HORIZONTAL, false));
                holder.tagsRecyclerView.setAdapter(tagAdapter);
                holder.tagsRecyclerView.setVisibility(View.VISIBLE);
            } else {
                holder.tagsRecyclerView.setVisibility(View.GONE);
            }
        }
    }

    private void loadProfilePicture(PostViewHolder holder, Post post) {
        try {
            String profilePicUrl = post.getProfilePic();
            if (profilePicUrl != null && !profilePicUrl.isEmpty() && !profilePicUrl.equals("null")) {
                if (!profilePicUrl.startsWith("http")) {
                    profilePicUrl = "http://" + serverIp + "/healthlink/web/assets/img/profile/" + profilePicUrl;
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

    private void loadPostImage(PostViewHolder holder, Post post) {
        try {
            String postImageUrl = post.getPostImage();
            Log.d(TAG, "Loading image for post " + post.getId() + ": " + postImageUrl + ", Spoiler: " + post.isSpoiler());

            if (postImageUrl != null && !postImageUrl.isEmpty() && !postImageUrl.equals("null")) {
                String fullImageUrl;
                if (postImageUrl.startsWith("http")) {
                    fullImageUrl = postImageUrl;
                } else {
                    fullImageUrl = "http://" + serverIp + "/healthlink/web/assets/img/posts/" + postImageUrl;
                }

                Log.d(TAG, "Full image URL: " + fullImageUrl);

                if (holder.postImage != null) {
                    holder.postImage.setVisibility(View.VISIBLE);

                    // Load the image first
                    Picasso.get()
                            .load(fullImageUrl)
                            .placeholder(R.drawable.media_placeholder)
                            .error(R.drawable.media_placeholder)
                            .into(holder.postImage, new Callback() {
                                @Override
                                public void onSuccess() {
                                    Log.d(TAG, "Image loaded successfully for post: " + post.getId());
                                    // <CHANGE> Apply spoiler effect immediately after image loads
                                    applySpoilerEffect(holder, post);
                                }
                                @Override
                                public void onError(Exception e) {
                                    Log.e(TAG, "Error loading image for post: " + post.getId(), e);
                                    holder.postImage.setVisibility(View.GONE);
                                    if (holder.spoilerContainer != null) {
                                        holder.spoilerContainer.setVisibility(View.GONE);
                                    }
                                }
                            });
                }
            } else {
                Log.d(TAG, "No image for post: " + post.getId());
                if (holder.postImage != null) {
                    holder.postImage.setVisibility(View.GONE);
                }
                if (holder.spoilerContainer != null) {
                    holder.spoilerContainer.setVisibility(View.GONE);
                }
            }
        } catch (Exception e) {
            Log.e(TAG, "Error loading post image", e);
            if (holder.postImage != null) {
                holder.postImage.setVisibility(View.GONE);
            }
            if (holder.spoilerContainer != null) {
                holder.spoilerContainer.setVisibility(View.GONE);
            }
        }
    }

    // <CHANGE> Fixed spoiler effect with proper blur and overlay
    private void applySpoilerEffect(PostViewHolder holder, Post post) {
        if (post.isSpoiler() && !post.isUnblurred()) {
            Log.d(TAG, "Applying spoiler effect to post: " + post.getId());

            // Apply heavy blur effect to the image
            ColorMatrix matrix = new ColorMatrix();
            matrix.setSaturation(0.1f); // Heavy desaturation
            ColorMatrixColorFilter filter = new ColorMatrixColorFilter(matrix);
            holder.postImage.setColorFilter(filter);
            holder.postImage.setAlpha(0.3f); // Very transparent

            // Show spoiler overlay with unblur button
            if (holder.spoilerContainer != null) {
                holder.spoilerContainer.setVisibility(View.VISIBLE);
                Log.d(TAG, "Spoiler container made visible for post: " + post.getId());

                if (holder.unblurButton != null) {
                    holder.unblurButton.setOnClickListener(v -> {
                        Log.d(TAG, "Unblur button clicked for post: " + post.getId());
                        post.setUnblurred(true);

                        // Remove blur effect
                        holder.postImage.setColorFilter(null);
                        holder.postImage.setAlpha(1.0f);
                        holder.spoilerContainer.setVisibility(View.GONE);

                        Toast.makeText(context, "Content revealed", Toast.LENGTH_SHORT).show();
                    });
                }
            }
        } else {
            Log.d(TAG, "No spoiler effect needed for post: " + post.getId());
            // Ensure no blur effect
            holder.postImage.setColorFilter(null);
            holder.postImage.setAlpha(1.0f);
            if (holder.spoilerContainer != null) {
                holder.spoilerContainer.setVisibility(View.GONE);
            }
        }
    }

    // <CHANGE> Hide AI summary in feed
    private void hideAiSummary(PostViewHolder holder) {
        if (holder.aiSummaryCard != null) {
            holder.aiSummaryCard.setVisibility(View.GONE);
        }
    }

    private void updateLikeButtonImmediate(PostViewHolder holder, boolean isLiked) {
        if (holder.likeIcon != null) {
            if (isLiked) {
                holder.likeIcon.setImageResource(R.drawable.like_icon_filled);
                holder.likeIcon.setColorFilter(Color.parseColor("#EF4444"));
            } else {
                holder.likeIcon.setImageResource(R.drawable.like_icon);
                holder.likeIcon.setColorFilter(Color.parseColor("#6B7280"));
            }
        }
    }

    private void setupClickListeners(PostViewHolder holder, Post post, int position) {
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

        // Click on image or title to open post details
        View.OnClickListener openDetailsListener = v -> {
            Intent intent = new Intent(context, PostDetailsActivity.class);
            intent.putExtra("post_id", post.getId());
            intent.putExtra("post_img", post.getPostImage());
            intent.putExtra("post_text", post.getPostDescription());
            intent.putExtra("user_name", post.getUserName());
            intent.putExtra("created_at", post.getCreatedAt());
            intent.putExtra("user_profile_pic", post.getProfilePic());
            intent.putExtra("category", post.getCategory());
            intent.putExtra("spoiler", post.isSpoiler());
            if (post.hasAiSummary()) {
                intent.putExtra("ai_summary", post.getAiSummary());
            }
            context.startActivity(intent);
        };

        if (holder.postImage != null) {
            holder.postImage.setOnClickListener(openDetailsListener);
        }
        if (holder.postTitle != null) {
            holder.postTitle.setOnClickListener(openDetailsListener);
        }
    }

    private void toggleLikeOnServer(Post post, PostViewHolder holder, int position) {
        String url = "http://" + serverIp + "/healthlink/api/toggle_like.php";
        StringRequest request = new StringRequest(Request.Method.POST, url,
                response -> {
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
                        }
                    } catch (JSONException e) {
                        Log.e(TAG, "Error parsing like response", e);
                    }
                },
                error -> Log.e(TAG, "Like network error: " + error.toString())
        ) {
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
        TextView userName, postTime, postTitle, postDescription, likeCount, commentCount;
        ImageView postImage, postImage2, likeIcon, commentIcon, postOptions;
        LinearLayout likeLayout, commentLayout, spoilerContainer;
        CardView aiSummaryCard;
        TextView aiSummaryText;
        ProgressBar aiSummaryProgress;
        Button unblurButton;
        RecyclerView tagsRecyclerView;

        public PostViewHolder(@NonNull View itemView) {
            super(itemView);
            profilePic = itemView.findViewById(R.id.profilePic);
            userName = itemView.findViewById(R.id.userName);
            postTime = itemView.findViewById(R.id.postTime);
            postTitle = itemView.findViewById(R.id.postTitle);
            postDescription = itemView.findViewById(R.id.postDescription);
            likeCount = itemView.findViewById(R.id.likeCount);
            commentCount = itemView.findViewById(R.id.commentCount);
            postImage = itemView.findViewById(R.id.postImage);
            postImage2 = itemView.findViewById(R.id.postImage2);
            likeIcon = itemView.findViewById(R.id.likeIcon);
            commentIcon = itemView.findViewById(R.id.commentIcon);
            postOptions = itemView.findViewById(R.id.postOptions);
            likeLayout = itemView.findViewById(R.id.likeLayout);
            commentLayout = itemView.findViewById(R.id.commentLayout);
            spoilerContainer = itemView.findViewById(R.id.spoilerContainer);
            aiSummaryCard = itemView.findViewById(R.id.ai_summary_card);
            aiSummaryText = itemView.findViewById(R.id.ai_summary_text);
            aiSummaryProgress = itemView.findViewById(R.id.ai_summary_progress);
            unblurButton = itemView.findViewById(R.id.unblurButton);
            tagsRecyclerView = itemView.findViewById(R.id.tagsRecyclerView);
        }
    }
}