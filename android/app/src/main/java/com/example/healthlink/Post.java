package com.example.healthlink;

public class Post {
    private int id;
    private int userId;
    private String userName;
    private String profilePic;
    private String postDescription;
    private String postImage;
    private String aiSummary;
    private String createdAt;
    private int likeCount;
    private int commentCount;
    private boolean isLikedByCurrentUser;
    private boolean spoiler;
    private boolean unblurred;
    private String title;
    private String category;
    private String[] tags;
    private String postType; // To distinguish between "regular" and "forum" posts

    // Additional fields for code content
    private String codeContent;
    private String codeLanguage;
    private int codeStatus;

    // Constructors
    public Post() {}

    public Post(int id, int userId, String userName, String profilePic, String postDescription,
                String postImage, String aiSummary, String createdAt, int likeCount, int commentCount, boolean isLikedByCurrentUser) {
        this.id = id;
        this.userId = userId;
        this.userName = userName;
        this.profilePic = profilePic;
        this.postDescription = postDescription;
        this.postImage = postImage;
        this.aiSummary = aiSummary;
        this.createdAt = createdAt;
        this.likeCount = likeCount;
        this.commentCount = commentCount;
        this.isLikedByCurrentUser = isLikedByCurrentUser;
    }

    // Getters and Setters
    public int getId() { return id; }
    public void setId(int id) { this.id = id; }

    public int getUserId() { return userId; }
    public void setUserId(int userId) { this.userId = userId; }

    public String getUserName() { return userName; }
    public void setUserName(String userName) { this.userName = userName; }

    public String getProfilePic() { return profilePic; }
    public void setProfilePic(String profilePic) { this.profilePic = profilePic; }

    public String getPostDescription() { return postDescription; }
    public void setPostDescription(String postDescription) { this.postDescription = postDescription; }

    public String getPostImage() { return postImage; }
    public void setPostImage(String postImage) { this.postImage = postImage; }

    public String getAiSummary() { return aiSummary; }
    public void setAiSummary(String aiSummary) { this.aiSummary = aiSummary; }

    public String getCreatedAt() { return createdAt; }
    public void setCreatedAt(String createdAt) { this.createdAt = createdAt; }

    public int getLikeCount() { return likeCount; }
    public void setLikeCount(int likeCount) { this.likeCount = likeCount; }

    public int getCommentCount() { return commentCount; }
    public void setCommentCount(int commentCount) { this.commentCount = commentCount; }

    public boolean isLikedByCurrentUser() { return isLikedByCurrentUser; }
    public void setLikedByCurrentUser(boolean likedByCurrentUser) { isLikedByCurrentUser = likedByCurrentUser; }

    public boolean isSpoiler() { return spoiler; }
    public void setSpoiler(boolean spoiler) { this.spoiler = spoiler; }

    public boolean isUnblurred() { return unblurred; }
    public void setUnblurred(boolean unblurred) { this.unblurred = unblurred; }

    public String getTitle() { return title; }
    public void setTitle(String title) { this.title = title; }

    public String getCategory() { return category; }
    public void setCategory(String category) { this.category = category; }

    public String[] getTags() { return tags; }
    public void setTags(String[] tags) { this.tags = tags; }

    // Methods for post type
    public String getPostType() { return postType; }
    public void setPostType(String postType) { this.postType = postType; }

    // Methods for code content
    public String getCodeContent() { return codeContent; }
    public void setCodeContent(String codeContent) { this.codeContent = codeContent; }

    public String getCodeLanguage() { return codeLanguage; }
    public void setCodeLanguage(String codeLanguage) { this.codeLanguage = codeLanguage; }

    public int getCodeStatus() { return codeStatus; }
    public void setCodeStatus(int codeStatus) { this.codeStatus = codeStatus; }

    // Helper method to check if AI summary is available
    public boolean hasAiSummary() {
        return aiSummary != null && !aiSummary.trim().isEmpty() && !aiSummary.equals("null");
    }

    // Helper method to check if post has code content
    public boolean hasCodeContent() {
        return codeContent != null && !codeContent.trim().isEmpty() && !codeContent.equals("null");
    }
}