package com.example.healthlink;

import java.util.ArrayList;
import java.util.Arrays;
import java.util.List;

public class Post {
    private int id;
    private int userId;
    private String userName;
    private String username;
    private String postDescription;
    private String postImage;
    private String profilePic;
    private int likeCount;
    private int commentCount;
    private boolean likedByCurrentUser;
    private String createdAt;
    private String codeContent;
    private String codeLanguage;
    private int codeStatus;
    private List<String> tags;

    // Spoiler functionality fields
    private boolean spoiler; // Flag to indicate if post contains sensitive content
    private boolean isUnblurred; // Track if user has chosen to view the blurred content

    // Constructors
    public Post() {
        this.isUnblurred = false; // Initially blurred
    }

    public Post(int id, int userId, String userName, String postDescription) {
        this.id = id;
        this.userId = userId;
        this.userName = userName;
        this.postDescription = postDescription;
        this.isUnblurred = false; // Initially blurred
    }

    // Full constructor
    public Post(int id, int userId, String userName, String username, String postDescription,
                String postImage, String profilePic, int likeCount, int commentCount,
                boolean likedByCurrentUser, String createdAt, String codeContent,
                String codeLanguage, int codeStatus, List<String> tags, boolean spoiler) {
        this.id = id;
        this.userId = userId;
        this.userName = userName;
        this.username = username;
        this.postDescription = postDescription;
        this.postImage = postImage;
        this.profilePic = profilePic;
        this.likeCount = likeCount;
        this.commentCount = commentCount;
        this.likedByCurrentUser = likedByCurrentUser;
        this.createdAt = createdAt;
        this.codeContent = codeContent;
        this.codeLanguage = codeLanguage;
        this.codeStatus = codeStatus;
        this.tags = tags;
        this.spoiler = spoiler;
        this.isUnblurred = false; // Initially blurred
    }

    // Helper methods for AI Summary
    public boolean hasAiSummary() {
        return codeContent != null && !codeContent.trim().isEmpty();
    }

    public String getAiSummary() {
        if (hasAiSummary()) {
            return codeContent;
        }
        return "";
    }

    public boolean isAiAnalysisComplete() {
        return codeStatus > 0 && codeContent != null && !codeContent.trim().isEmpty();
    }

    public boolean isAiAnalysisSuccess() {
        return codeStatus == 1 && codeContent != null && !codeContent.trim().isEmpty();
    }

    public boolean isLiked() {
        return likedByCurrentUser;
    }

    public void setLiked(boolean liked) {
        this.likedByCurrentUser = liked;
    }

    // Getters and Setters
    public int getId() { return id; }
    public void setId(int id) { this.id = id; }

    public int getUserId() { return userId; }
    public void setUserId(int userId) { this.userId = userId; }

    public String getUserName() { return userName; }
    public void setUserName(String userName) { this.userName = userName; }

    public String getUsername() { return username; }
    public void setUsername(String username) { this.username = username; }

    public String getPostDescription() { return postDescription; }
    public void setPostDescription(String postDescription) { this.postDescription = postDescription; }

    public String getPostImage() { return postImage; }
    public void setPostImage(String postImage) { this.postImage = postImage; }

    public String getProfilePic() { return profilePic; }
    public void setProfilePic(String profilePic) { this.profilePic = profilePic; }

    public int getLikeCount() { return likeCount; }
    public void setLikeCount(int likeCount) { this.likeCount = likeCount; }

    public int getCommentCount() { return commentCount; }
    public void setCommentCount(int commentCount) { this.commentCount = commentCount; }

    public boolean isLikedByCurrentUser() { return likedByCurrentUser; }
    public void setLikedByCurrentUser(boolean likedByCurrentUser) { this.likedByCurrentUser = likedByCurrentUser; }

    public String getCreatedAt() { return createdAt; }
    public void setCreatedAt(String createdAt) { this.createdAt = createdAt; }

    public String getCodeContent() { return codeContent; }
    public void setCodeContent(String codeContent) { this.codeContent = codeContent; }

    public String getCodeLanguage() { return codeLanguage; }
    public void setCodeLanguage(String codeLanguage) { this.codeLanguage = codeLanguage; }

    public int getCodeStatus() { return codeStatus; }
    public void setCodeStatus(int codeStatus) { this.codeStatus = codeStatus; }

    public void setTags(String[] tags) {
        if (tags != null) {
            this.tags = Arrays.asList(tags);
        } else {
            this.tags = new ArrayList<>();
        }
    }

    public List<String> getTags() {
        return tags != null ? tags : new ArrayList<>();
    }

    // Spoiler functionality getters and setters
    public boolean isSpoiler() {
        return spoiler;
    }

    public void setSpoiler(boolean spoiler) {
        this.spoiler = spoiler;
    }

    public boolean isUnblurred() {
        return isUnblurred;
    }

    public void setUnblurred(boolean unblurred) {
        isUnblurred = unblurred;
    }

    @Override
    public String toString() {
        return "Post{" +
                "id=" + id +
                ", userId=" + userId +
                ", userName='" + userName + '\'' +
                ", postDescription='" + postDescription + '\'' +
                ", hasAiSummary=" + hasAiSummary() +
                ", spoiler=" + spoiler +
                ", isUnblurred=" + isUnblurred +
                '}';
    }
}