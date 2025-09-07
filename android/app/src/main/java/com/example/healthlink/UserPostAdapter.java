package com.example.healthlink;
import android.content.Context;
import android.content.Intent;
import android.util.Log;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.ImageView;
import androidx.annotation.NonNull;
import androidx.recyclerview.widget.RecyclerView;
import com.bumptech.glide.Glide;
import com.bumptech.glide.load.engine.DiskCacheStrategy;
import java.util.List;

public class UserPostAdapter extends RecyclerView.Adapter<UserPostAdapter.UserPostViewHolder> {
    private static final String TAG = "UserPostAdapter";
    private Context context;
    private List<Post> posts;
    private String serverIp;

    public UserPostAdapter(Context context, List<Post> posts, String serverIp) {
        this.context = context;
        this.posts = posts;
        this.serverIp = serverIp;
        Log.d(TAG, "Adapter created with " + posts.size() + " posts");
    }

    @NonNull
    @Override
    public UserPostViewHolder onCreateViewHolder(@NonNull ViewGroup parent, int viewType) {
        View view = LayoutInflater.from(context).inflate(R.layout.item_profile_post, parent, false);
        Log.d(TAG, "Creating view holder");
        return new UserPostViewHolder(view);
    }

    @Override
    public void onBindViewHolder(@NonNull UserPostViewHolder holder, int position) {
        Post post = posts.get(position);
        Log.d(TAG, "Binding post at position " + position + ": " + post.getPostImage());
        try {
            if (post.getPostImage() != null && !post.getPostImage().isEmpty() && !post.getPostImage().equals("null")) {
                String imageUrl = "http://" + serverIp + "/healthlink/web/assets/img/posts/" + post.getPostImage();
                Log.d(TAG, "Loading image from: " + imageUrl);
                Glide.with(context)
                        .load(imageUrl)
                        .placeholder(R.drawable.post_placeholder)
                        .error(R.drawable.post_placeholder)
                        .diskCacheStrategy(DiskCacheStrategy.ALL)
                        .into(holder.postImage);
            } else {
                // If there's no image, show a post placeholder
                Log.d(TAG, "Showing post placeholder");
                holder.postImage.setImageResource(R.drawable.post_placeholder);
            }

            // Set click listener to open post details
            holder.itemView.setOnClickListener(v -> {
                Intent intent = new Intent(context, PostDetailsActivity.class);
                intent.putExtra("post_id", post.getId());
                intent.putExtra("post_img", post.getPostImage());
                intent.putExtra("post_text", post.getPostDescription());
                intent.putExtra("user_name", post.getUserName());
                intent.putExtra("created_at", post.getCreatedAt());
                intent.putExtra("ai_summary", post.getAiSummary()); // Pass AI summary instead of code content
                context.startActivity(intent);
            });
        } catch (Exception e) {
            Log.e(TAG, "Error loading post image: " + e.getMessage());
            holder.postImage.setImageResource(R.drawable.post_placeholder);
        }
    }

    @Override
    public int getItemCount() {
        Log.d(TAG, "Getting item count: " + posts.size());
        return posts.size();
    }

    static class UserPostViewHolder extends RecyclerView.ViewHolder {
        ImageView postImage;

        public UserPostViewHolder(@NonNull View itemView) {
            super(itemView);
            postImage = itemView.findViewById(R.id.post_image);
            Log.d(TAG, "ViewHolder created");
        }
    }
}