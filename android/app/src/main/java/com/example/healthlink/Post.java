package com.example.healthlink;

import java.util.List;

public class Post {
    private String title;
    private String content;
    private String author;
    private String timeAgo;
    private int repliesCount;
    private int likesCount;
    private boolean isLiked;
    private String category;
    private List<String> tags;
    private List<String> imageUrls;

    // Constructors
    public Post() {}

    public Post(String title, String content, String author, String timeAgo) {
        this.title = title;
        this.content = content;
        this.author = author;
        this.timeAgo = timeAgo;
        this.repliesCount = 0;
        this.likesCount = 0;
        this.isLiked = false;
    }

    // Getters and Setters
    public String getTitle() { return title; }
    public void setTitle(String title) { this.title = title; }

    public String getContent() { return content; }
    public void setContent(String content) { this.content = content; }

    public String getAuthor() { return author; }
    public void setAuthor(String author) { this.author = author; }

    public String getTimeAgo() { return timeAgo; }
    public void setTimeAgo(String timeAgo) { this.timeAgo = timeAgo; }

    public int getRepliesCount() { return repliesCount; }
    public void setRepliesCount(int repliesCount) { this.repliesCount = repliesCount; }

    public int getLikesCount() { return likesCount; }
    public void setLikesCount(int likesCount) { this.likesCount = likesCount; }

    public boolean isLiked() { return isLiked; }
    public void setLiked(boolean liked) { isLiked = liked; }

    public String getCategory() { return category; }
    public void setCategory(String category) { this.category = category; }

    public List<String> getTags() { return tags; }
    public void setTags(List<String> tags) { this.tags = tags; }

    public List<String> getImageUrls() { return imageUrls; }
    public void setImageUrls(List<String> imageUrls) { this.imageUrls = imageUrls; }

    public void toggleLike() {
        if (isLiked) {
            likesCount--;
            isLiked = false;
        } else {
            likesCount++;
            isLiked = true;
        }
    }
}
