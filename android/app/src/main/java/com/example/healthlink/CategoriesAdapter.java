package com.example.healthlink;

import android.annotation.SuppressLint;
import android.content.Context;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import androidx.annotation.NonNull;
import androidx.recyclerview.widget.RecyclerView;
import com.google.android.material.chip.Chip;
import java.util.List;

public class CategoriesAdapter extends RecyclerView.Adapter<CategoriesAdapter.CategoryViewHolder> {
    private List<String> categories;
    private Context context;
    private ForumActivity forumActivity;
    private int selectedPosition = 0; // Default to "All Topics"

    public CategoriesAdapter(List<String> categories, ForumActivity forumActivity) {
        this.categories = categories;
        this.forumActivity = forumActivity;
        this.context = forumActivity; // Use the activity as context
    }

    public CategoriesAdapter(List<String> categoriesList, ForumFragment forumFragment) {
    }

    @NonNull
    @Override
    public CategoryViewHolder onCreateViewHolder(@NonNull ViewGroup parent, int viewType) {
        // Use parent's context to avoid null pointer exception
        View view = LayoutInflater.from(parent.getContext()).inflate(R.layout.item_category, parent, false);
        return new CategoryViewHolder(view);
    }

    @Override
    public void onBindViewHolder(@NonNull CategoryViewHolder holder, @SuppressLint("RecyclerView") int position) {
        String category = categories.get(position);
        holder.categoryChip.setText(category);
        holder.categoryChip.setChecked(position == selectedPosition);

        holder.categoryChip.setOnClickListener(v -> {
            int oldPosition = selectedPosition;
            selectedPosition = position;
            notifyItemChanged(oldPosition);
            notifyItemChanged(selectedPosition);
            forumActivity.onCategorySelected(category);
        });
    }

    @Override
    public int getItemCount() {
        return categories.size();
    }

    static class CategoryViewHolder extends RecyclerView.ViewHolder {
        Chip categoryChip;

        public CategoryViewHolder(@NonNull View itemView) {
            super(itemView);
            categoryChip = itemView.findViewById(R.id.categoryChip);
        }
    }
}