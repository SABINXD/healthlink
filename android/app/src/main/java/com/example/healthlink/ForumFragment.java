package com.example.healthlink;

import android.content.Intent;
import android.os.Bundle;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.ArrayAdapter;
import android.widget.AutoCompleteTextView;
import android.widget.Toast;
import androidx.annotation.NonNull;
import androidx.fragment.app.Fragment;
import androidx.recyclerview.widget.LinearLayoutManager;
import androidx.recyclerview.widget.RecyclerView;
import com.google.android.material.button.MaterialButton;
import com.google.android.material.floatingactionbutton.FloatingActionButton;
import com.google.android.material.textfield.TextInputEditText;
import java.util.ArrayList;
import java.util.Arrays;
import java.util.List;

public class ForumFragment extends Fragment {
    private SessionManager sessionManager;
    private RecyclerView postsRecyclerView;
    private RecyclerView categoriesRecyclerView;
    private PostsAdapter postsAdapter;
    private CategoriesAdapter categoriesAdapter;
    private TextInputEditText titleEditText, detailsEditText;
    private AutoCompleteTextView categorySpinner;
    private MaterialButton postButton, addImageButton;
    private FloatingActionButton fab;
    private List<Post> postsList;
    private List<String> categoriesList;
    private String[] categoryOptions = {
            "Mental Health", "Nutrition", "Fitness", "Chronic Conditions",
            "Parenting", "Aging", "Women's Health", "Men's Health"
    };

    @Override
    public void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        sessionManager = new SessionManager(requireContext());
    }

    @Override
    public View onCreateView(LayoutInflater inflater, ViewGroup container,
                             Bundle savedInstanceState) {
        // Inflate the layout for this fragment
        View view = inflater.inflate(R.layout.activity_forum, container, false);

        initViews(view);
        setupRecyclerViews();
        setupCategorySpinner();
        setupClickListeners();
        loadSampleData();

        return view;
    }

    private void initViews(View view) {
        postsRecyclerView = view.findViewById(R.id.postsRecyclerView);
        categoriesRecyclerView = view.findViewById(R.id.categoriesRecyclerView);
        titleEditText = view.findViewById(R.id.titleEditText);
        detailsEditText = view.findViewById(R.id.detailsEditText);
        categorySpinner = view.findViewById(R.id.categorySpinner);
        postButton = view.findViewById(R.id.postButton);
        addImageButton = view.findViewById(R.id.addImageButton);
        fab = view.findViewById(R.id.fab);
    }

    private void setupRecyclerViews() {
        postsList = new ArrayList<>();
        postsAdapter = new PostsAdapter(postsList, this); 
        postsRecyclerView.setLayoutManager(new LinearLayoutManager(getContext()));
        postsRecyclerView.setAdapter(postsAdapter);

        // Setup categories RecyclerView
        categoriesList = new ArrayList<>();
        categoriesList.add("All Topics");
        categoriesList.addAll(Arrays.asList(categoryOptions));
        categoriesAdapter = new CategoriesAdapter(categoriesList, this); 
        LinearLayoutManager horizontalLayoutManager = new LinearLayoutManager(getContext(), LinearLayoutManager.HORIZONTAL, false);
        categoriesRecyclerView.setLayoutManager(horizontalLayoutManager);
        categoriesRecyclerView.setAdapter(categoriesAdapter);
    }

    private void setupCategorySpinner() {
        ArrayAdapter<String> adapter = new ArrayAdapter<>(getContext(),
                android.R.layout.simple_dropdown_item_1line, categoryOptions);
        categorySpinner.setAdapter(adapter);
    }

    private void setupClickListeners() {
        postButton.setOnClickListener(v -> handlePostSubmission());
        addImageButton.setOnClickListener(v -> {
            // Handle image selection
            Toast.makeText(getContext(), "Image selection feature coming soon", Toast.LENGTH_SHORT).show();
        });
        fab.setOnClickListener(v -> {
            // Scroll to top or show post form
            postsRecyclerView.smoothScrollToPosition(0);
        });
    }

    private void handlePostSubmission() {
        String title = titleEditText.getText().toString().trim();
        String details = detailsEditText.getText().toString().trim();
        String category = categorySpinner.getText().toString().trim();

        if (title.isEmpty() || details.isEmpty()) {
            Toast.makeText(getContext(), "Please fill in all required fields", Toast.LENGTH_SHORT).show();
            return;
        }

        // Create new post
        Post newPost = new Post();
        newPost.setTitle(title);
        newPost.setContent(details);
        newPost.setAuthor(sessionManager.getUserName()); // Use actual user name
        newPost.setTimeAgo("Just now");
        newPost.setRepliesCount(0);
        newPost.setLikesCount(0);
        newPost.setCategory(category);

        // Add tags based on category
        List<String> tags = new ArrayList<>();
        tags.add(category.toLowerCase());
        newPost.setTags(tags);

        // Add to list and notify adapter
        postsList.add(0, newPost);
        postsAdapter.notifyItemInserted(0);
        postsRecyclerView.smoothScrollToPosition(0);

        // Clear form
        titleEditText.setText("");
        detailsEditText.setText("");
        categorySpinner.setText("");

        Toast.makeText(getContext(), "Your question has been posted successfully!", Toast.LENGTH_SHORT).show();
    }

    private void loadSampleData() {
        // Sample post 1
        Post post1 = new Post();
        post1.setTitle("Skin rash that won't go away - any ideas?");
        post1.setAuthor("Jennifer Lee");
        post1.setTimeAgo("3 hours ago");
        post1.setContent("I've had this rash on my forearm for about 3 weeks now. It started as small red bumps and has spread slightly. It's itchy but not painful. I've tried over-the-counter hydrocortisone cream but it hasn't helped much. Any ideas what this could be?");
        post1.setRepliesCount(8);
        post1.setLikesCount(15);
        post1.setTags(Arrays.asList("skin condition", "rash", "dermatology"));
        postsList.add(post1);

        // Sample post 2
        Post post2 = new Post();
        post2.setTitle("Persistent cough and chest tightness");
        post2.setAuthor("David Wilson");
        post2.setTimeAgo("1 day ago");
        post2.setContent("For the past month, I've had a dry cough that won't go away. I also feel tightness in my chest, especially when I try to take deep breaths. I don't have a fever or other cold symptoms. I'm a non-smoker and in my early 30s. Should I be concerned?");
        post2.setRepliesCount(14);
        post2.setLikesCount(27);
        post2.setTags(Arrays.asList("respiratory", "cough", "chest pain"));
        postsList.add(post2);

        // Sample post 3
        Post post3 = new Post();
        post3.setTitle("Frequent headaches and blurred vision");
        post3.setAuthor("Maria Garcia");
        post3.setTimeAgo("2 days ago");
        post3.setContent("Lately I've been getting headaches almost every day, usually in the afternoon. They're not severe but persistent. I've also noticed that my vision gets blurry sometimes, especially when I'm looking at screens for a long time. I'm 42 and have never had vision problems before. Could these be related?");
        post3.setRepliesCount(21);
        post3.setLikesCount(32);
        post3.setTags(Arrays.asList("headache", "vision", "neurology"));
        postsList.add(post3);

        postsAdapter.notifyDataSetChanged();
    }

    public void onCategorySelected(String category) {
        // Filter posts by category
        if ("All Topics".equals(category)) {
            // Show all posts
            Toast.makeText(getContext(), "Showing all topics", Toast.LENGTH_SHORT).show();
        } else {
            // Filter by specific category
            Toast.makeText(getContext(), "Filtering by: " + category, Toast.LENGTH_SHORT).show();
        }
    }

    public void onPostClicked(Post post) {
        // Open post detail activity
        Intent intent = new Intent(getActivity(), PostDetailActivity.class);
        intent.putExtra("post_title", post.getTitle());
        intent.putExtra("post_content", post.getContent());
        intent.putExtra("post_author", post.getAuthor());
        intent.putExtra("post_time", post.getTimeAgo());
        startActivity(intent);
    }
}