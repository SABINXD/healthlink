package com.example.healthlink;

import android.content.Context;
import android.content.Intent;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.ImageView;
import android.widget.LinearLayout;
import android.widget.TextView;
import androidx.annotation.NonNull;
import androidx.recyclerview.widget.RecyclerView;
import java.util.List;

public class PostsAdapter extends RecyclerView.Adapter<PostsAdapter.PostViewHolder> {
    private List<Post> postsList;
    private Context context;
    private ForumActivity forumActivity;

    public PostsAdapter(List<Post> postsList, ForumActivity forumActivity) {
        this.postsList = postsList;
        this.forumActivity = forumActivity;
        this.context = forumActivity; // Use the activity as context
    }

    public PostsAdapter(List<Post> postsList, ForumFragment forumFragment) {
    }

    @NonNull
    @Override
    public PostViewHolder onCreateViewHolder(@NonNull ViewGroup parent, int viewType) {
        // Use parent's context to avoid null pointer exception
        View view = LayoutInflater.from(parent.getContext()).inflate(R.layout.item_post, parent, false);
        return new PostViewHolder(view);
    }

    @Override
    public void onBindViewHolder(@NonNull PostViewHolder holder, int position) {
        Post post = postsList.get(position);

        // Set text for TextViews
        holder.postTitle.setText(post.getTitle());
        holder.postContent.setText(post.getContent());
        holder.postAuthor.setText(post.getAuthor());
        holder.postTime.setText(post.getTimeAgo());
        holder.postReplies.setText(post.getRepliesCount() + " replies");
        holder.likeCount.setText(String.valueOf(post.getLikesCount()));

        // Set click listeners for interaction buttons
        holder.likeButton.setOnClickListener(v -> {
            // Handle like action
            int currentLikes = post.getLikesCount();
            post.setLikesCount(currentLikes + 1);
            holder.likeCount.setText(String.valueOf(currentLikes + 1));

            // Change icon to filled
            holder.likeIcon.setImageResource(R.drawable.like_icon_filled);
        });

        holder.replyButton.setOnClickListener(v -> {
            // Open post detail activity
            forumActivity.onPostClicked(post);
        });

        // Set click listener for the entire card
        holder.itemView.setOnClickListener(v -> {
            forumActivity.onPostClicked(post);
        });
    }

    @Override
    public int getItemCount() {
        return postsList.size();
    }

    public static class PostViewHolder extends RecyclerView.ViewHolder {
        // Post content views
        TextView postTitle, postContent, postAuthor, postTime, postReplies;

        // Interaction views
        LinearLayout likeButton, replyButton;
        ImageView likeIcon;
        TextView likeCount;

        public PostViewHolder(@NonNull View itemView) {
            super(itemView);

            // Post content
            postTitle = itemView.findViewById(R.id.postTitle);
            postContent = itemView.findViewById(R.id.postContent);
            postAuthor = itemView.findViewById(R.id.postAuthor);
            postTime = itemView.findViewById(R.id.postTime);
            postReplies = itemView.findViewById(R.id.postReplies);

            // Interaction elements
            likeButton = itemView.findViewById(R.id.likeButton);
            replyButton = itemView.findViewById(R.id.replyButton);
            likeIcon = itemView.findViewById(R.id.likeIcon);
            likeCount = itemView.findViewById(R.id.likeCount);
        }
    }
}