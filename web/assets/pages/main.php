<?php
global $user;
global $posts;
global $follow_sugesstions;
// Get user statistics
$userPosts = getPostById($user['id']);
$postCount = count($userPosts);
$userFollowers = getFollowersCount($user['id']);
$followerCount = count($userFollowers);
$userFollowing = getFollowingCount($user['id']);
$followingCount = count($userFollowing);
?>
<style>
    /* Custom animations and styles */
    :root {
        --primary: #10b981;
        --primary-dark: #059669;
        --primary-light: #34d399;
        --secondary: #0d9488;
        --success: #10b981;
        --danger: #ef4444;
        --warning: #f59e0b;
        --dark: #1f2937;
        --light: #f3f4f6;
        --health-light: #ecfdf5;
        --health-accent: #14b8a6;
        --border: #e5e7eb;
        --ai-bg: #f0f9ff;
        --ai-border: #bae6fd;
        --doctor-bg: #f0fdf4;
        --doctor-border: #bbf7d0;
    }

    /* Smooth scrollbar */
    ::-webkit-scrollbar {
        width: 8px;
        height: 8px;
    }

    ::-webkit-scrollbar-track {
        background: #f1f1f1;
    }

    ::-webkit-scrollbar-thumb {
        background: var(--primary);
        border-radius: 4px;
    }

    ::-webkit-scrollbar-thumb:hover {
        background: var(--primary-dark);
    }

    /* Glassmorphism effect */
    .glass {
        background: rgba(255, 255, 255, 0.85);
        backdrop-filter: blur(10px);
        -webkit-backdrop-filter: blur(10px);
        border: 1px solid rgba(16, 185, 129, 0.2);
    }

    /* Loading animation */
    .loader {
        border-top-color: var(--primary);
        -webkit-animation: spinner 1.5s linear infinite;
        animation: spinner 1.5s linear infinite;
    }

    @-webkit-keyframes spinner {
        0% {
            -webkit-transform: rotate(0deg);
        }

        100% {
            -webkit-transform: rotate(360deg);
        }
    }

    @keyframes spinner {
        0% {
            transform: rotate(0deg);
        }

        100% {
            transform: rotate(360deg);
        }
    }

    /* Pulse animation for notifications */
    @keyframes pulse {
        0% {
            box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7);
        }

        70% {
            box-shadow: 0 0 0 10px rgba(16, 185, 129, 0);
        }

        100% {
            box-shadow: 0 0 0 0 rgba(16, 185, 129, 0);
        }
    }

    .pulse {
        animation: pulse 2s infinite;
    }

    /* Floating animation */
    @keyframes float {
        0% {
            transform: translateY(0px);
        }

        50% {
            transform: translateY(-10px);
        }

        100% {
            transform: translateY(0px);
        }
    }

    .float {
        animation: float 3s ease-in-out infinite;
    }

    /* Custom button styles */
    .btn-primary {
        background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
        transition: all 0.3s ease;
    }

    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 20px rgba(16, 185, 129, 0.2);
    }

    /* Post card hover effect */
    .post-card {
        transition: all 0.3s ease;
        border-left: 4px solid transparent;
    }

    .post-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 20px 25px -5px rgba(16, 185, 129, 0.15), 0 10px 10px -5px rgba(16, 185, 129, 0.1);
        border-left-color: var(--primary);
    }

    /* Health Summary styles */
    .health-summary {
        background: linear-gradient(135deg, #ecfdf5 0%, #d1fae5 100%);
        position: relative;
        overflow: hidden;
        border-left: 4px solid var(--primary);
    }

    #search-results {
        border-radius: 0.5rem;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
    }

    #search-results div:last-child {
        border-bottom: none;
    }

    #search-results div:hover {
        background-color: rgba(16, 185, 129, 0.05);
    }

    .health-summary::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: linear-gradient(45deg, rgba(16, 185, 129, 0.1), rgba(20, 184, 166, 0.1));
        z-index: 0;
    }

    .health-summary-content {
        position: relative;
        z-index: 1;
    }

    /* Skeleton loading */
    .skeleton {
        background: linear-gradient(90deg, #f0fdf4 25%, #dcfce7 50%, #f0fdf4 75%);
        background-size: 200% 100%;
        animation: loading 1.5s infinite;
    }

    @keyframes loading {
        0% {
            background-position: 200% 0;
        }

        100% {
            background-position: -200% 0;
        }
    }

    /* Health theme specific styles */
    .health-icon {
        color: var(--primary);
    }

    .health-bg-light {
        background-color: var(--health-light);
    }

    .health-text-primary {
        color: var(--primary);
    }

    .health-border {
        border-color: var(--primary);
    }

    /* Forum-specific styles */
    .forum-header {
        background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
        color: white;
        padding: 2rem;
        border-radius: 1rem;
        margin-bottom: 2rem;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
    }

    .discussion-item {
        transition: all 0.3s ease;
        border-radius: 0.75rem;
        padding: 1rem;
        margin-bottom: 1rem;
        border-left: 4px solid transparent;
    }

    .discussion-item:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        border-left-color: var(--primary);
    }

    .category-tag {
        display: inline-block;
        padding: 0.25rem 0.75rem;
        border-radius: 9999px;
        font-size: 0.75rem;
        font-weight: 600;
        margin-right: 0.5rem;
    }

    .category-general {
        background-color: #dbeafe;
        color: #1d4ed8;
    }

    .category-question {
        background-color: #fef3c7;
        color: #b45309;
    }

    .category-discussion {
        background-color: #dcfce7;
        color: #16a34a;
    }

    .category-announcement {
        background-color: #fce7f3;
        color: #be185d;
    }

    /* New styles for the updated design */
    .category {
        transition: all 0.2s ease;
    }

    .category.active {
        background-color: var(--primary);
        color: white;
        border-color: var(--primary);
    }

    .action-btn {
        transition: all 0.2s ease;
    }

    .action-btn:hover {
        color: var(--primary);
    }

    .post-image-container {
        display: flex;
        gap: 0.5rem;
        flex-wrap: wrap;
    }

    .post-image-container img {
        width: 6rem;
        height: 6rem;
        object-fit: cover;
        border-radius: 0.5rem;
        border: 1px solid var(--border);
    }
</style>

<section class="bg-gradient-to-r from-teal-50 to-green-50 py-16 text-center border-b border-border">
    <div class="container mx-auto px-4">
        <h1 class="text-4xl font-bold mb-4 text-dark">Health Community Forum</h1>
        <p class="text-lg text-secondary max-w-2xl mx-auto mb-8">Ask questions, share experiences, and connect with others on your health journey</p>
        <div class="max-w-xl mx-auto relative">
            <input type="text" id="search-input" placeholder="Search for health topics, conditions, or advice..." class="w-full py-4 px-6 rounded-full border border-border shadow-md text-base">
            <button class="absolute right-2 top-1/2 transform -translate-y-1/2 bg-[#10b981] text-white w-10 h-10 rounded-full flex items-center justify-center">
                <i class="fas fa-search"></i>
            </button>
            <!-- Search Results Container -->
            <div id="search-results" class="absolute top-full left-0 w-full bg-white rounded-lg shadow-lg mt-1 max-h-96 overflow-y-auto z-10 hidden">
                <!-- Results will be inserted here -->
            </div>
        </div>
    </div>
</section>
<!-- Main Content -->
<div class="container mx-auto px-4 py-8">
    <div class="flex flex-col lg:flex-row gap-8">
        <!-- Content Area -->
        <div class="flex-1">
            <h2 class="text-2xl font-bold mb-6 flex items-center gap-2">
                <i class="fas fa-comments text-primary-light"></i>
                Health Discussions
            </h2>
            <!-- Categories -->
            <div class="flex flex-wrap gap-2 mb-8">
                <?php
                $categories = ['All Topics', 'Mental', 'Sexual', 'Fitness', 'skin dieases', 'Parenting', 'Aging', 'Women\'s Health', 'Men\'s Health'];
                $currentCategory = isset($_GET['category']) ? $_GET['category'] : 'All Topics';
                foreach ($categories as $category):
                    $isActive = ($currentCategory === $category) ? 'active' : '';
                    $categoryValue = ($category === 'All Topics') ? '' : $category;
                ?>
                    <a href="?category=<?= urlencode($categoryValue) ?>"
                        class="px-4 py-2 bg-white border border-border rounded-full text-sm cursor-pointer transition-all hover:bg-primary hover:text-white hover:border-primary category <?= $isActive ?>">
                        <?= htmlspecialchars($category) ?>
                    </a>
                <?php endforeach; ?>
            </div>
            <!-- Create Post Card -->
            <div class="bg-white rounded-xl shadow-md p-6 mb-8">
                <h3 class="text-xl font-bold mb-4">Post Your Health Query</h3>
                <p class="text-gray-600 mb-4">Share your health journey and connect with others in the community.</p>
                <?php if (isset($_SESSION['Auth'])) { ?>
                    <button type="button" data-bs-toggle="modal" data-bs-target="#codeOptionModal" class="py-3 px-6  bg-[#10b981] text-white rounded-lg font-medium hover:bg-[#2a6b6c] transition-all">
                        Create New Post
                    </button>
                <?php } else { ?>
                    <a href="?login" class="py-3 px-6 bg-[#10b981] text-white rounded-lg font-medium hover:bg-[#2a6b6c] transition-all inline-block">
                        Login to Create Post
                    </a>
                <?php } ?>
            </div>
            <!-- Posts Container -->
            <div class="space-y-6 posts-container">
                <?php
                showError('post_img');
                // Get the current category from URL or default to null
                $currentCategory = isset($_GET['category']) && !empty($_GET['category']) ? $_GET['category'] : null;
                $filteredPosts = getPost($currentCategory);
                foreach ($filteredPosts as $post):
                    $likes = getLikesCount($post['id']);
                    $comments = getComments($post['id']);
                    // Prepare image source
                    $image = $post['post_img'];
                    $p_user = getUser($post['user_id']);
                    $img_src = '';
                    if (!empty($image)) {
                        if (strpos($image, 'http') === 0) {
                            $img_src = $image;
                        } else if (strpos($image, 'web/assets/img/posts/') === 0) {
                            $img_src = substr($image, strpos($image, 'assets/img/posts/'));
                        } else {
                            $img_src = 'assets/img/posts/' . $image;
                        }
                    }
                    // Get AI summary for this post
                    $aiSummary = $post['code_content']; // directly from DB
                ?>
                    <article class="bg-white rounded-xl shadow-md p-6 transition-all hover:shadow-lg hover:-translate-y-1 post-card" data-post-id="<?= (int)$post['id'] ?>">
                        <div class="flex justify-between mb-4">
                            <div>
                                <h3 class="text-xl font-bold text-dark mb-1"><?= htmlspecialchars($post['post_title'] ?: 'Health Discussion') ?></h3>
                                <div class="flex gap-4 text-secondary text-sm">
                                    <span><i class="far fa-user"></i>
                                        <?php
                                        if ($post['post_privacy'] == 0) {
                                            $username = htmlspecialchars($p_user['username']);
                                            $displayName = htmlspecialchars($post['first_name'] . ' ' . $post['last_name']);
                                            if ($p_user['is_doctor'] == 1) {
                                                $displayName = 'Dr ' . $displayName;
                                            }
                                            echo '<a href="?u=' . $username . '" class="text-decoration-none text-dark hover:text-primary">' . $displayName . '</a>';
                                        } else {
                                            echo 'Anonymous user';
                                        }
                                        ?>
                                    </span>
                                    <span><i class="far fa-clock"></i> <?= show_time($post['created_at']) ?></span>
                                    <span><i class="far fa-comment"></i> <?= count($comments) ?> replies</span>
                                </div>
                            </div>
                        </div>
                        <!-- Category -->
                        <?php if (!empty($post['post_category'])): ?>
                            <div class="mb-2">
                                <a href="?category=<?= urlencode($post['post_category']) ?>" class="px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-xs font-medium hover:bg-blue-200 transition-colors">
                                    <?= htmlspecialchars($post['post_category']) ?>
                                </a>
                            </div>
                        <?php endif; ?>
                        <div class="mb-4 text-dark">
                            <?php if (!empty($post['post_desc'])): ?>
                                <p class="text-gray-800 leading-relaxed"><?= htmlspecialchars($post['post_desc']) ?></p>
                            <?php endif; ?>
                        </div>
                        <!-- Post Image -->
                        <?php if (!empty($img_src)): ?>
                            <div class="mb-4 post-image-container">
                                <img src="<?= htmlspecialchars($img_src) ?>" alt="Post image" class="w-24 h-24 rounded-lg object-cover border border-border">
                            </div>
                        <?php endif; ?>
                        <div class="flex flex-wrap gap-2 mb-4">
                            <span class="px-3 py-1 bg-slate-100 text-secondary rounded-full text-xs">health discussion</span>
                            <span class="px-3 py-1 bg-slate-100 text-secondary rounded-full text-xs">community</span>
                            <span class="px-3 py-1 bg-slate-100 text-secondary rounded-full text-xs">wellness</span>
                        </div>
                        <div class="flex gap-4">
                            <!-- Like Section -->
                            <div class="flex items-center space-x-2 like-container">
                                <?php
                                if (checkLiked($post['id'])) {
                                    $like_btn_display = 'none';
                                    $unlike_btn_display = 'inline-block';
                                } else {
                                    $like_btn_display = 'inline-block';
                                    $unlike_btn_display = 'none';
                                }
                                ?>
                                <!-- Like Buttons -->
                                <div data-post-id="<?= (int)$post['id'] ?>"
                                    style="display:<?= $unlike_btn_display ?>;"
                                    class="unlike_btn flex items-center gap-1 text-green-600 cursor-pointer hover:text-green-700 transition-colors">
                                    <i class="fas fa-thumbs-up text-lg"></i>
                                    <span class="text-sm font-medium">Liked</span>
                                </div>
                                <div data-post-id="<?= (int)$post['id'] ?>"
                                    style="display:<?= $like_btn_display ?>;"
                                    class="like-toggle-btn like_btn flex items-center gap-1 text-gray-600 cursor-pointer hover:text-green-600 transition-colors">
                                    <i class="far fa-thumbs-up text-lg"></i>
                                    <span class="text-sm font-medium">Like</span>
                                </div>
                                <!-- Like Count -->
                                <p class="text-sm font-medium text-gray-700 hover:text-green-600 cursor-pointer transition likes-count-<?= (int)$post['id'] ?>"
                                    onclick="openLikesModal('<?= (int)$post['id'] ?>')">
                                    <?= count($likes) ?>
                                </p>
                            </div>
                            <!-- Comment Section -->
                            <div class="flex items-center space-x-2 cursor-pointer hover:text-green-600 transition-colors"
                                onclick="openPostModal('<?= (int)$post['id'] ?>')">
                                <i class="far fa-comment text-gray-500 hover:text-green-600 text-xl transition-colors"></i>
                                <p class="text-sm font-medium text-gray-700 hover:text-green-600 transition">
                                    <?= count($comments) ?> Comments
                                </p>
                            </div>
                            <!-- Share Section -->
                            <div class="flex items-center space-x-2 cursor-pointer hover:text-blue-600 transition-colors">
                                <i class="far fa-share text-gray-500 hover:text-blue-600 text-xl transition-colors"></i>
                                <p class="text-sm font-medium text-gray-700 hover:text-blue-600 transition">
                                    Share
                                </p>
                            </div>
                        </div>
                    </article>
                    <!-- Post Modal -->
                    <div id="post-modal-<?= (int)$post['id'] ?>" class="modal-overlay fixed inset-0 bg-black bg-opacity-50 z-50 hidden overflow-y-auto">
                        <div class="flex items-center justify-center min-h-screen p-4">
                            <div class="bg-white rounded-xl shadow-xl max-w-4xl w-full max-h-[90vh] overflow-hidden">
                                <div class="flex justify-between items-center p-6 border-b border-gray-200">
                                    <h2 class="text-2xl font-bold text-gray-900">Post Details</h2>
                                    <button onclick="closePostModal('<?= (int)$post['id'] ?>')" class="text-gray-500 hover:text-gray-900">
                                        <i class="fas fa-times text-xl"></i>
                                    </button>
                                </div>
                                <div class="p-6 overflow-y-auto max-h-[calc(90vh-80px)]">
                                    <!-- Post Content Section -->
                                    <div class="mb-8">
                                        <div class="flex justify-between items-start mb-4">
                                            <div>
                                                <h2 class="text-2xl font-bold text-gray-900 mb-2"><?= htmlspecialchars($post['post_title'] ?: 'Health Discussion') ?></h2>
                                                <div class="flex gap-4 text-gray-600 text-sm">
                                                    <span><i class="far fa-user"></i>
                                                        <?php
                                                        if ($post['post_privacy'] == 0) {
                                                            $username = htmlspecialchars($p_user['username']);
                                                            $displayName = htmlspecialchars($post['first_name'] . ' ' . $post['last_name']);
                                                            if ($p_user['is_doctor'] == 1) {
                                                                $displayName = 'Dr ' . $displayName;
                                                            }
                                                            echo '<a href="?u=' . $username . '" class="text-decoration-none text-dark hover:text-primary">' . $displayName . '</a>';
                                                        } else {
                                                            echo 'Anonymous user';
                                                        }
                                                        ?>
                                                    </span>
                                                    <span><i class="far fa-clock"></i> <?= show_time($post['created_at']) ?></span>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- Category -->
                                        <?php if (!empty($post['post_category'])): ?>
                                            <div class="mb-4">
                                                <a href="?category=<?= urlencode($post['post_category']) ?>" class="px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-sm font-medium hover:bg-blue-200 transition-colors">
                                                    <?= htmlspecialchars($post['post_category']) ?>
                                                </a>
                                            </div>
                                        <?php endif; ?>
                                        <!-- Post Image -->
                                        <?php if (!empty($img_src)): ?>
                                            <div class="mb-6">
                                                <img src="<?= htmlspecialchars($img_src) ?>" alt="Post image" class="w-full rounded-lg object-cover max-h-96">
                                            </div>
                                        <?php endif; ?>
                                        <!-- Post Content -->
                                        <?php if (!empty($post['post_desc'])): ?>
                                            <div class="text-gray-800 mb-6 text-base leading-relaxed"><?= htmlspecialchars($post['post_desc']) ?></div>
                                        <?php endif; ?>
                                        <!-- Tags -->
                                        <div class="flex flex-wrap gap-2 mb-4">
                                            <span class="px-3 py-1 bg-slate-100 text-gray-700 rounded-full text-xs">health discussion</span>
                                            <span class="px-3 py-1 bg-slate-100 text-gray-700 rounded-full text-xs">community</span>
                                            <span class="px-3 py-1 bg-slate-100 text-gray-700 rounded-full text-xs">wellness</span>
                                        </div>
                                    </div>
                                    <!-- AI Summary Section -->
                                    <div class="bg-blue-50 border border-blue-200 rounded-xl p-5 mb-8">
                                        <div class="flex items-center gap-2 mb-3">
                                            <h3 class="text-xl font-bold text-gray-900">AI Summary</h3>
                                            <i class="fas fa-robot text-blue-500 text-xl"></i>
                                        </div>
                                        <div class="text-gray-800">
                                            <?php
                                            $aiData = json_decode($post['code_content'], true);
                                            $summary = '';
                                            // Check if we have valid JSON data
                                            if (json_last_error() === JSON_ERROR_NONE && is_array($aiData)) {
                                                // Extract summary from JSON
                                                $summary = $aiData['summary'] ?? 'No summary available';
                                                // Convert newlines to HTML paragraphs
                                                $summary = nl2br(htmlspecialchars($summary));
                                                // Split into paragraphs if it contains multiple lines
                                                $paragraphs = explode('<br />', $summary);
                                                echo '<div class="space-y-3">';
                                                foreach ($paragraphs as $paragraph) {
                                                    if (trim($paragraph)) {
                                                        echo '<p class="text-gray-800 leading-relaxed">' . $paragraph . '</p>';
                                                    }
                                                }
                                                echo '</div>';
                                            } else {
                                                // Fallback for plain text or invalid JSON
                                                $fallbackSummary = is_string($aiData) ? $aiData : $post['code_content'];
                                                echo '<p class="text-gray-800 leading-relaxed">' . nl2br(htmlspecialchars($fallbackSummary)) . '</p>';
                                            }
                                            ?>
                                        </div>
                                    </div>
                                    <!-- Possible Conditions Section -->
                                    <div class="bg-amber-50 border border-amber-200 rounded-xl p-5 mb-8">
                                        <div class="flex items-center gap-2 mb-3">
                                            <h3 class="text-xl font-bold text-gray-900">Possible Conditions (AI Analysis)</h3>
                                            <i class="fas fa-brain text-amber-500 text-xl"></i>
                                        </div>
                                        <div class="space-y-3">
                                            <?php
                                            $conditions = [];
                                            if (!empty($post['possible_conditions'])) {
                                                $conditions = json_decode($post['possible_conditions'], true);
                                            }
                                            // If no conditions from DB, try to get from code_content
                                            if (empty($conditions)) {
                                                $aiData = json_decode($post['code_content'], true);
                                                if (is_array($aiData) && isset($aiData['conditions'])) {
                                                    $conditions = $aiData['conditions'];
                                                }
                                            }
                                            // If still no conditions, use defaults
                                            if (empty($conditions)) {
                                                $conditions = [
                                                    ['condition' => 'General Health', 'likelihood' => 75],
                                                    ['condition' => 'Preventive Care', 'likelihood' => 60],
                                                    ['condition' => 'Lifestyle Factors', 'likelihood' => 45]
                                                ];
                                            }
                                            foreach ($conditions as $condition):
                                            ?>
                                                <div class="flex justify-between items-center p-3 bg-white rounded-lg border border-amber-200">
                                                    <span class="font-medium text-gray-900"><?= htmlspecialchars($condition['condition']) ?></span>
                                                    <span class="bg-amber-200 text-amber-800 px-3 py-1 rounded-full text-sm font-semibold"><?= htmlspecialchars($condition['likelihood']) ?>%</span>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                        <p class="text-amber-800 text-sm mt-4">
                                            <i class="fas fa-info-circle"></i> This is not a diagnosis. Percentages indicate likelihood based on symptoms described.
                                        </p>
                                    </div>
                                    <!-- Comments Section -->
                                    <div class="bg-white border border-gray-200 rounded-xl p-5 mb-8">
                                        <div class="flex items-center gap-2 mb-4">
                                            <h3 class="text-xl font-bold text-gray-900">Community Discussion</h3>
                                            <i class="fas fa-comments text-green-500 text-xl"></i>
                                        </div>
                                        <!-- Comments List -->
                                        <div class="space-y-4 mb-6" id="comment-section<?= (int)$post['id'] ?>">
                                            <?php if (count($comments) < 1): ?>
                                                <p class="text-center text-gray-600 py-4">No comments yet</p>
                                            <?php endif; ?>
                                            <?php foreach ($comments as $comment):
                                                $cuser = getUser($comment['user_id']);
                                            ?>
                                                <div class="flex items-start space-x-4 mb-4">
                                                    <img src="assets/img/profile/<?= htmlspecialchars($cuser['profile_pic']) ?>" class="w-12 h-12 rounded-full object-cover border-2 border-green-200 shadow-sm" />
                                                    <div class="flex-1 flex flex-col">
                                                        <div class="bg-green-50 rounded-2xl px-4 py-3">
                                                            <div class="flex items-baseline space-x-1">
                                                                <?php
                                                                if ($cuser['is_doctor'] == 1) {
                                                                ?>
                                                                    <a href="?u=<?= htmlspecialchars($cuser['username']) ?>" class="font-bold text-gray-900 hover:opacity-80">
                                                                        @ Dr <?= htmlspecialchars($cuser['username']) ?> ✅
                                                                    </a>
                                                                <?php
                                                                } else {
                                                                ?>
                                                                    <a href="?u=<?= htmlspecialchars($cuser['username']) ?>" class="font-bold text-gray-900 hover:opacity-80">
                                                                        @<?= htmlspecialchars($cuser['username']) ?>
                                                                    </a>
                                                                <?php
                                                                }
                                                                ?>
                                                                <span class="text-sm text-gray-600 ml-2">(<?= show_time($comment['created_at']) ?>)</span>
                                                            </div>
                                                            <p class="text-gray-800 text-base mt-1"><?= htmlspecialchars($comment['comment']) ?></p>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                        <!-- Add Comment -->
                                        <div class="flex items-center space-x-3">
                                            <img src="./assets/img/profile/<?= htmlspecialchars($user['profile_pic']) ?>"
                                                class="w-10 h-10 rounded-full object-cover ring-2 ring-green-500 ring-opacity-30">
                                            <div class="flex-1 flex items-center space-x-2">
                                                <input type="text"
                                                    class="flex-1 bg-green-50 rounded-full px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-600 comment-input"
                                                    placeholder="Add a comment...">
                                                <button class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-full text-sm font-medium transition-all duration-300 transform hover:scale-105 add-comment"
                                                    data-cs="comment-section<?= (int)$post['id'] ?>"
                                                    data-post-id="<?= (int)$post['id'] ?>">
                                                    Post
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- Consult a Doctor Section -->
                                    <div class="bg-green-50 border border-green-200 rounded-xl p-5">
                                        <div class="flex items-center gap-2 mb-4">
                                            <h3 class="text-xl font-bold text-gray-900">Consult a Doctor</h3>
                                            <i class="fas fa-user-md text-green-600 text-xl"></i>
                                        </div>
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                            <div class="bg-white rounded-lg p-4 shadow-sm border border-green-100 transition-transform hover:-translate-y-1">
                                                <div class="flex items-center gap-3 mb-3">
                                                    <div class="w-14 h-14 rounded-full overflow-hidden border-2 border-green-300">
                                                        <img src="https://picsum.photos/seed/doctor1/100/100.jpg" alt="Dr. Sarah Johnson" class="w-full h-full object-cover">
                                                    </div>
                                                    <div>
                                                        <h4 class="font-semibold text-gray-900">Dr. Sarah Johnson</h4>
                                                        <p class="text-sm text-gray-600">12 years experience • Rating: 4.8/5</p>
                                                    </div>
                                                </div>
                                                <div class="inline-block bg-green-100 text-green-800 px-2 py-1 rounded text-xs font-medium mb-3">General Practitioner</div>
                                                <div class="flex gap-2">
                                                    <button class="px-3 py-1.5 bg-green-600 text-white rounded text-sm font-medium hover:bg-green-700 transition-colors">Consult</button>
                                                    <button class="px-3 py-1.5 border border-green-600 text-green-600 rounded text-sm font-medium hover:bg-green-50 transition-colors">View Profile</button>
                                                </div>
                                            </div>
                                            <div class="bg-white rounded-lg p-4 shadow-sm border border-green-100 transition-transform hover:-translate-y-1">
                                                <div class="flex items-center gap-3 mb-3">
                                                    <div class="w-14 h-14 rounded-full overflow-hidden border-2 border-green-300">
                                                        <img src="https://picsum.photos/seed/doctor2/100/100.jpg" alt="Dr. Michael Chen" class="w-full h-full object-cover">
                                                    </div>
                                                    <div>
                                                        <h4 class="font-semibold text-gray-900">Dr. Michael Chen</h4>
                                                        <p class="text-sm text-gray-600">15 years experience • Rating: 4.9/5</p>
                                                    </div>
                                                </div>
                                                <div class="inline-block bg-green-100 text-green-800 px-2 py-1 rounded text-xs font-medium mb-3">Internal Medicine</div>
                                                <div class="flex gap-2">
                                                    <button class="px-3 py-1.5 bg-green-600 text-white rounded text-sm font-medium hover:bg-green-700 transition-colors">Consult</button>
                                                    <button class="px-3 py-1.5 border border-green-600 text-green-600 rounded text-sm font-medium hover:bg-green-50 transition-colors">View Profile</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Likes Modal -->
                    <div id="likes-modal-<?= (int)$post['id'] ?>" class="modal-overlay fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center p-4">
                        <div class="modal-content bg-white rounded-2xl max-w-md w-full max-h-96 flex flex-col overflow-hidden shadow-2xl">
                            <div class="flex items-center justify-between p-4 border-b border-green-100">
                                <h5 class="text-lg font-bold text-gray-900">Likes</h5>
                                <button onclick="closeLikesModal('<?= (int)$post['id'] ?>')" class="p-2 hover:bg-green-100 rounded-full">
                                    <i class="fas fa-times text-gray-500"></i>
                                </button>
                            </div>
                            <div class="overflow-y-auto p-4">
                                <?php if (count($likes) < 1): ?>
                                    <p class="text-center text-gray-600 py-8">No likes yet</p>
                                <?php endif; ?>
                                <?php foreach ($likes as $like):
                                    $fuser = getUser($like['user_id']);
                                    $isFollowed = checkFollowed($like['user_id']);
                                ?>
                                    <div class="flex items-center justify-between py-3">
                                        <div class="flex items-center space-x-3">
                                            <div class="relative">
                                                <img src="assets/img/profile/<?= htmlspecialchars($fuser['profile_pic']) ?>" class="w-10 h-10 rounded-full object-cover" />
                                                <?php if ($fuser['is_doctor'] == 1): ?>
                                                    <div class="absolute bottom-0 right-0 bg-green-500 rounded-full p-1 border-2 border-white">
                                                        <i class="fas fa-check text-white text-xs"></i>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                            <div>
                                                <a href="?u=<?= htmlspecialchars($fuser['username']) ?>" class="block hover:opacity-80">
                                                    <p class="font-semibold text-gray-900">
                                                        <?php
                                                        if ($fuser['is_doctor'] == 1) {
                                                            echo '<span class="text-green-700 font-bold">Dr </span>';
                                                            echo htmlspecialchars($fuser['first_name'] . ' ' . $fuser['last_name']);
                                                        } else {
                                                            echo htmlspecialchars($fuser['first_name'] . ' ' . $fuser['last_name']);
                                                        }
                                                        ?>
                                                    </p>
                                                    <p class="text-sm text-gray-600">@<?= htmlspecialchars($fuser['username']) ?></p>
                                                </a>
                                            </div>
                                        </div>
                                        <?php if ($user['id'] != $like['user_id']): ?>
                                            <?php if ($isFollowed): ?>
                                                <button class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-full text-sm font-medium transition-all duration-300 transform hover:scale-105 unfollowbtn"
                                                    data-user-id="<?= (int)$fuser['id'] ?>">
                                                    Unfollow
                                                </button>
                                            <?php else: ?>
                                                <button class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-full text-sm font-medium transition-all duration-300 transform hover:scale-105 followbtn"
                                                    data-user-id="<?= (int)$fuser['id'] ?>">
                                                    Follow
                                                </button>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            <!-- Disclaimer -->
            <div class="bg-amber-50 border-l-4 border-amber-500 p-4 rounded-lg mt-8">
                <h3 class="text-amber-700 font-bold mb-2 flex items-center gap-2">
                    <i class="fas fa-exclamation-triangle"></i> Important Disclaimer
                </h3>
                <p class="text-amber-800 text-sm">The information shared on this forum is for educational purposes only and is not a substitute for professional medical advice, diagnosis, or treatment. Always seek the advice of your physician or other qualified health provider with any questions you may have regarding a medical condition.</p>
            </div>
        </div>
        <!-- Sidebar -->
        <div class="lg:w-80 space-y-6">
            <div class="bg-white rounded-xl shadow-md p-5">
                <h3 class="text-lg font-bold mb-4">Trending Topics</h3>
                <ul class="space-y-3">
                    <li class="flex items-center gap-2 pb-3 border-b border-border">
                        <span class="bg-primary-light text-white w-6 h-6 rounded-full flex items-center justify-center text-xs font-bold">1</span>
                        <a href="#" class="text-dark hover:text-primary">Managing stress during pandemic recovery</a>
                    </li>
                    <li class="flex items-center gap-2 pb-3 border-b border-border">
                        <span class="bg-primary-light text-white w-6 h-6 rounded-full flex items-center justify-center text-xs font-bold">2</span>
                        <a href="#" class="text-dark hover:text-primary">Best diets for heart health</a>
                    </li>
                    <li class="flex items-center gap-2 pb-3 border-b border-border">
                        <span class="bg-primary-light text-white w-6 h-6 rounded-full flex items-center justify-center text-xs font-bold">3</span>
                        <a href="#" class="text-dark hover:text-primary">Sleep improvement techniques</a>
                    </li>
                    <li class="flex items-center gap-2 pb-3 border-b border-border">
                        <span class="bg-primary-light text-white w-6 h-6 rounded-full flex items-center justify-center text-xs font-bold">4</span>
                        <a href="#" class="text-dark hover:text-primary">Exercises for arthritis pain relief</a>
                    </li>
                    <li class="flex items-center gap-2">
                        <span class="bg-primary-light text-white w-6 h-6 rounded-full flex items-center justify-center text-xs font-bold">5</span>
                        <a href="#" class="text-dark hover:text-primary">Boosting immune system naturally</a>
                    </li>
                </ul>
            </div>
            <div class="bg-white rounded-xl shadow-md p-5">
                <h3 class="text-lg font-bold mb-4">Health Resources</h3>
                <ul class="space-y-2">
                    <li><a href="#" class="text-dark hover:text-primary">Understanding Blood Test Results</a></li>
                    <li><a href="#" class="text-dark hover:text-primary">Mental Health Support Groups</a></li>
                    <li><a href="#" class="text-dark hover:text-primary">Nutrition Guidelines by Age</a></li>
                    <li><a href="#" class="text-dark hover:text-primary">Exercise Programs for Beginners</a></li>
                    <li><a href="#" class="text-dark hover:text-primary">Preventive Health Checkups</a></li>
                </ul>
            </div>
            <div class="bg-white rounded-xl shadow-md p-5">
                <h3 class="text-lg font-bold mb-4">Community Guidelines</h3>
                <ul class="space-y-2">
                    <li><a href="#" class="text-dark hover:text-primary">Be respectful and supportive</a></li>
                    <li><a href="#" class="text-dark hover:text-primary">Share personal experiences, not medical advice</a></li>
                    <li><a href="#" class="text-dark hover:text-primary">Protect your privacy and others'</a></li>
                    <li><a href="#" class="text-dark hover:text-primary">Cite credible sources when sharing information</a></li>
                    <li><a href="#" class="text-dark hover:text-primary">Report inappropriate content</a></li>
                </ul>
            </div>
        </div>
    </div>
</div>
<!-- Modals and Sidebars -->
<?php if (isset($_SESSION['Auth'])) { ?>
    <!-- Modal -->
    <div class="modal fade" id="codeOptionModal" tabindex="-1" aria-labelledby="addPostLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content rounded-2xl shadow-xl overflow-hidden">
                <!-- Header -->
                <div class="modal-header bg-gradient-to-r from-green-600 via-teal-600 to-emerald-700 px-6 py-4">
                    <h5 class="modal-title font-bold text-white" id="addPostLabel">Share Health Update</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <!-- Body -->
                <div class="modal-body px-6 py-6 bg-white">
                    <!-- Image Preview -->
                    <img src="" id="post_img_nocode" style="display: none;"
                        class="w-full h-64 object-cover rounded-xl border-2 border-green-200 mb-6 shadow-md">
                    <!-- Form -->
                    <form method="post" action="assets/php/actions.php?addnocodepost" enctype="multipart/form-data" class="space-y-6" id="postFormInModal">
                        <!-- Title -->
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Question Title</label>
                            <input type="text" name="post_title" class="w-full py-3 px-4 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent" placeholder="What's your health question or concern?" required>
                        </div>
                        <!-- Category -->
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Category</label>
                            <select name="post_category" class="w-full py-3 px-4 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent" required>
                                <option value="">Select a category</option>
                                <option value="mental">Mental</option>
                                <option value="nutrition">Sexual</option>
                                <option value="fitness">Fitness</option>
                                <option value="chronic">skin dieases</option>
                                <option value="parenting">Parenting</option>
                                <option value="aging">Aging</option>
                                <option value="women">Women's Health</option>
                                <option value="men">Men's Health</option>
                            </select>
                        </div>
                        <!-- Details -->
                        <div class="mb-4">
                            <label for="post_desc" class="block text-sm font-medium text-gray-700 mb-2">Details</label>
                            <textarea name="post_desc" id="post_desc" rows="3"
                                class="w-full rounded-xl border border-gray-300 shadow-sm focus:border-green-500 focus:ring focus:ring-green-200 resize-none text-sm p-4 transition-all"
                                placeholder="Provide more details about your question or concern..."></textarea>
                        </div>
                        <!-- File Input -->
                        <div class="space-y-2">
                            <label class="block text-sm font-medium text-gray-700">Upload Image (Optional)</label>
                            <div class="flex items-center justify-center w-full">
                                <label for="select_post_img_nocode"
                                    class="flex flex-col items-center justify-center w-full h-32 border-2 border-dashed border-green-400 rounded-lg cursor-pointer bg-green-50 hover:bg-green-100 transition-colors">
                                    <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 mb-2 text-green-500" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                        <p class="text-sm text-green-600"><span class="font-semibold">Click to upload</span> or drag and drop</p>
                                        <p class="text-xs text-green-500">PNG, JPG, GIF up to 2MB</p>
                                    </div>
                                    <input name="post_img" type="file" id="select_post_img_nocode" class="hidden" />
                                </label>
                            </div>
                        </div>
                        <!-- Toggles Section -->
                        <div class="p-4 rounded-xl bg-gradient-to-r from-green-500 to-emerald-600 text-white space-y-4">
                            <!-- Spoiler Toggle -->
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="font-semibold">Sensitive Content</p>
                                    <p class="text-xs text-white/80">Blur and warn viewers if your post has medical images or sensitive content.</p>
                                </div>
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" id="spoiler_toggle" class="sr-only peer">
                                    <div class="w-12 h-6 bg-gray-300 rounded-full peer peer-checked:bg-red-500 transition"></div>
                                    <div class="absolute left-1 top-1 w-4 h-4 bg-white rounded-full peer-checked:translate-x-6 transition"></div>
                                </label>
                            </div>
                            <!-- Anonymous Toggle -->
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="font-semibold">Post Anonymously</p>
                                    <p class="text-xs text-white/80">Hide your identity and share without your profile name.</p>
                                </div>
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" id="anonymous_toggle" class="sr-only peer">
                                    <div class="w-12 h-6 bg-gray-300 rounded-full peer peer-checked:bg-green-600 transition"></div>
                                    <div class="absolute left-1 top-1 w-4 h-4 bg-white rounded-full peer-checked:translate-x-6 transition"></div>
                                </label>
                            </div>
                        </div>
                        <!-- Hidden inputs for backend -->
                        <input type="hidden" name="spoiler" id="spoiler_input" value="0">
                        <input type="hidden" name="post_privacy" id="privacy_input" value="0">
                        <!-- Submit Button -->
                        <div class="flex justify-end">
                            <button type="submit"
                                class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-green-600 via-teal-600 to-emerald-700 text-white text-sm font-medium rounded-xl shadow-md hover:opacity-90 focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-all transform hover:scale-105">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                                    <path
                                        d="M10.894 2.553a1 1 0 00-1.788 0l-7 14a1 1 0 001.169 1.409l5-1.429A1 1 0 009 15.571V11a1 1 0 112 0v4.571a1 1 0 00.725.962l5 1.428a1 1 0 001.17-1.408l-7-14z" />
                                </svg>
                                Share
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- Notifications Sidebar -->
    <div class="offcanvas offcanvas-start" tabindex="-1" id="notification_sidebar" aria-labelledby="notificationSidebarLabel">
        <div class="offcanvas-header bg-gradient-to-r from-green-500 to-teal-600 text-white">
            <h5 class="offcanvas-title font-bold" id="notificationSidebarLabel">Health Notifications</h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body bg-green-50 p-0">
            <div class="p-4 border-b border-green-200 bg-white">
                <h6 class="font-semibold text-gray-700">Recent Notifications</h6>
            </div>
            <div class="divide-y divide-green-200">
                <?php
                $notifications = getNotifications();
                foreach ($notifications as $not) {
                    $time = $not['created_at'];
                    $fuser = getUser($not['from_user_id']);
                    $post = '';
                    if ($not['post_id']) {
                        $post = 'data-bs-toggle="modal" data-bs-target="#postview' . $not['post_id'] . '"';
                    }
                    $fbtn = '';
                ?>
                    <div class="p-4 hover:bg-green-100 transition-colors cursor-pointer">
                        <div class="flex items-start">
                            <img src="assets/img/profile/<?= $fuser['profile_pic'] ?>" alt="" class="w-12 h-12 rounded-full border-2 border-white shadow-sm">
                            <div class="ml-3 flex-1">
                                <div class="flex items-center justify-between">
                                    <a href='?u=<?= $fuser['username'] ?>' class="text-decoration-none text-dark">
                                        <h6 class="font-semibold text-gray-900"><?= $fuser['first_name'] ?> <?= $fuser['last_name'] ?></h6>
                                    </a>
                                    <span class="text-xs text-gray-500"><?= show_time($time) ?></span>
                                </div>
                                <p class="text-sm text-gray-600 mt-1">@<?= $fuser['username'] ?> <?= $not['message'] ?></p>
                            </div>
                            <div class="ml-2 flex items-center">
                                <?php
                                if ($not['read_status'] == 0) {
                                ?>
                                    <div class="w-3 h-3 bg-green-500 rounded-full"></div>
                                <?php
                                } else if ($not['read_status'] == 2) {
                                ?>
                                    <span class="badge bg-danger">Post Deleted</span>
                                <?php
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                <?php
                }
                ?>
            </div>
        </div>
    </div>
    <!-- Messages Sidebar -->
    <div class="offcanvas offcanvas-start" tabindex="-1" id="messages_sidebar" aria-labelledby="messagesSidebarLabel">
        <div class="offcanvas-header bg-gradient-to-r from-green-500 to-teal-600 text-white">
            <h5 class="offcanvas-title font-bold" id="messagesSidebarLabel">Health Messages</h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body bg-green-50 p-0" id="chatlist">
            <!-- Chat list will be populated here -->
        </div>
    </div>
    <!-- Chat Box Modal -->
    <div class="modal fade" id="chatbox" tabindex="-1" aria-labelledby="chatboxLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content rounded-2xl shadow-xl overflow-hidden">
                <div class="modal-header bg-gradient-to-r from-green-500 to-teal-600 text-white p-4">
                    <a href="" id="cplink" class="text-decoration-none text-white flex items-center">
                        <img src="assets/img/profile/default_profile.jpg" id="chatter_pic" class="w-10 h-10 rounded-full border-2 border-white mr-3">
                        <div>
                            <h5 class="modal-title font-bold" id="chatboxLabel">
                                <span id="chatter_name"></span>
                                <span class="text-sm font-normal opacity-75">(@<span id="chatter_username">loading..</span>)</span>
                            </h5>
                        </div>
                    </a>
                    <button type="button" class="btn-close btn-close-white ms-auto" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4 bg-green-50" id="user_chat">
                    <!-- Chat messages will be loaded here -->
                </div>
                <div class="p-3 bg-danger text-white text-center" id="blerror" style="display:none">
                    <i class="bi bi-x-octagon-fill me-2"></i> You are not allowed to send messages to this user anymore
                </div>
                <div class="modal-footer bg-white p-3">
                    <div class="input-group">
                        <input type="text" class="form-control rounded-pill border-0 bg-green-100 focus:bg-white focus:shadow-sm" id="msginput" placeholder="Type your message..." aria-label="Message">
                        <button class="btn btn-primary rounded-pill ms-2 bg-green-600 hover:bg-green-700" id="sendmsg" data-user-id="0" type="button">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                <path d="M10.894 2.553a1 1 0 00-1.788 0l-7 14a1 1 0 001.169 1.409l5-1.429A1 1 0 009 15.571V11a1 1 0 112 0v4.571a1 1 0 00.725.962l5 1.428a1 1 0 001.17-1.408l-7-14z" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php } ?>
<!-- modal for refresh number  -->
<div class="modal fade" id="refreshNumber" tabindex="-1" aria-labelledby="refreshNumberLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-2xl shadow-xl overflow-hidden">
            <!-- Modal Header -->
            <div class="modal-header bg-gradient-to-r from-green-600 to-emerald-700 px-6 py-4">
                <h5 class="modal-title font-bold text-white" id="refreshNumberLabel">Refresh Hospital Numbers</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <!-- Modal Body -->
            <div class="modal-body px-6 py-6 bg-white">
                <form id="districtForm" method="post" action="assets/php/actions.php?refreshNumber" class="space-y-6">
                    <!-- Input Field -->
                    <div class="mb-4">
                        <label for="district" class="block text-sm font-medium text-gray-700 mb-2">Enter Your Current District</label>
                        <input type="text" id="district" name="district" placeholder="e.g., Chitwan"
                            class="w-full px-4 py-2 border border-green-300 rounded-xl shadow-sm focus:ring-2 focus:ring-green-500 focus:border-green-500 outline-none">
                    </div>
                    <!-- Submit Button -->
                    <div class="flex justify-end">
                        <button type="submit"
                            class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-green-600 to-emerald-700 text-white text-sm font-medium rounded-xl shadow-md hover:from-green-700 hover:to-emerald-800 focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-all transform hover:scale-105">
                            Refresh Numbers
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- Footer -->
<footer class="bg-dark text-white py-10 mt-auto">
    <div class="container mx-auto px-4">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-8 mb-8">
            <div>
                <h4 class="text-lg font-bold mb-4 relative pb-2">About healthlink</h4>
                <p class="text-gray-300">We're a community-driven platform dedicated to empowering individuals with knowledge and support for their health journeys.</p>
            </div>
            <div>
                <h4 class="text-lg font-bold mb-4 relative pb-2">Quick Links</h4>
                <ul class="space-y-2">
                    <li><a href="index.html" class="text-gray-300 hover:text-primary-light transition-colors">Home</a></li>
                    <li><a href="index.html" class="text-gray-300 hover:text-primary-light transition-colors">Forum</a></li>
                    <li><a href="index.html" class="text-gray-300 hover:text-primary-light transition-colors">Resources</a></li>
                    <li><a href="#" class="text-gray-300 hover:text-primary-light transition-colors">Health Professionals</a></li>
                    <li><a href="#" class="text-gray-300 hover:text-primary-light transition-colors">Contact Us</a></li>
                </ul>
            </div>
            <div>
                <h4 class="text-lg font-bold mb-4 relative pb-2">Health Topics</h4>
                <ul class="space-y-2">
                    <li><a href="#" class="text-gray-300 hover:text-primary-light transition-colors">Mental Health</a></li>
                    <li><a href="#" class="text-gray-300 hover:text-primary-light transition-colors">Nutrition & Diet</a></li>
                    <li><a href="#" class="text-gray-300 hover:text-primary-light transition-colors">Fitness & Exercise</a></li>
                    <li><a href="#" class="text-gray-300 hover:text-primary-light transition-colors">Chronic Conditions</a></li>
                    <li><a href="#" class="text-gray-300 hover:text-primary-light transition-colors">Preventive Care</a></li>
                </ul>
            </div>
            <div>
                <h4 class="text-lg font-bold mb-4 relative pb-2">Legal</h4>
                <ul class="space-y-2">
                    <li><a href="#" class="text-gray-300 hover:text-primary-light transition-colors">Privacy Policy</a></li>
                    <li><a href="#" class="text-gray-300 hover:text-primary-light transition-colors">Terms of Service</a></li>
                    <li><a href="#" class="text-gray-300 hover:text-primary-light transition-colors">Medical Disclaimer</a></li>
                    <li><a href="#" class="text-gray-300 hover:text-primary-light transition-colors">Cookie Policy</a></li>
                    <li><a href="#" class="text-gray-300 hover:text-primary-light transition-colors">Accessibility</a></li>
                </ul>
            </div>
        </div>
        <div class="pt-6 border-t border-gray-700 text-center text-gray-400 text-sm">
            <p>&copy; 2023 healthlink Forum. All rights reserved.</p>
        </div>
    </div>
</footer>
<!-- Scripts -->
<script>
    // Toggle Sync Script
    document.addEventListener("DOMContentLoaded", function() {
        // Check if the user is logged in before setting up event listeners
        if (document.getElementById('spoiler_toggle')) {
            document.getElementById("spoiler_toggle").addEventListener("change", function() {
                document.getElementById("spoiler_input").value = this.checked ? 1 : 0;
            });
            document.getElementById("anonymous_toggle").addEventListener("change", function() {
                document.getElementById("privacy_input").value = this.checked ? 1 : 0;
            });
            // Image preview functionality
            document.getElementById('select_post_img_nocode').addEventListener('change', function(e) {
                const file = e.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        document.getElementById('post_img_nocode').src = e.target.result;
                        document.getElementById('post_img_nocode').style.display = 'block';
                    }
                    reader.readAsDataURL(file);
                }
            });
        }
    });
    // Function to combine form fields
    function combineFields(event) {
        event.preventDefault();
        const title = document.getElementById('title').value;
        const category = document.getElementById('category').value;
        const details = document.getElementById('details').value;
        const combined = `Title: ${title}\nCategory: ${category}\nDetails: ${details}`;
        document.getElementById('combined_post_text').value = combined;
        event.target.submit();
    }
    // Register GSAP ScrollTrigger plugin
    gsap.registerPlugin(ScrollTrigger);
    // GSAP Animations with ScrollTrigger
    document.addEventListener('DOMContentLoaded', () => {
        // Forum header animation
        gsap.fromTo('section.bg-gradient-to-r', {
            opacity: 0,
            y: -30
        }, {
            opacity: 1,
            y: 0,
            duration: 0.8,
            ease: "power2.out"
        });
        // Create post card animation
        gsap.fromTo('.bg-white.rounded-xl.shadow-md.p-6.mb-8', {
            opacity: 0,
            y: 30
        }, {
            opacity: 1,
            y: 0,
            duration: 0.8,
            delay: 0.2,
            ease: "power2.out"
        });
        // Post cards animation
        gsap.fromTo('.post-card', {
            opacity: 0,
            y: 50
        }, {
            opacity: 1,
            y: 0,
            duration: 0.8,
            stagger: 0.2,
            scrollTrigger: {
                trigger: '.posts-container',
                start: 'top 80%',
                toggleActions: 'play none none none'
            }
        });
        // Sidebar items animation
        gsap.fromTo('.lg\\:w-80 > div', {
            opacity: 0,
            x: 20
        }, {
            opacity: 1,
            x: 0,
            duration: 0.5,
            stagger: 0.1,
            delay: 0.4,
            scrollTrigger: {
                trigger: '.lg\\:w-80',
                start: 'top 80%',
                toggleActions: 'play none none none'
            }
        });
        // Hover animations for interactive elements
        const hoverElements = document.querySelectorAll('.followbtn, .unfollowbtn, .add-comment, .like_btn, .unlike_btn, .action-btn, .category');
        hoverElements.forEach(element => {
            element.addEventListener('mouseenter', () => {
                gsap.to(element, {
                    scale: 1.05,
                    duration: 0.2
                });
            });
            element.addEventListener('mouseleave', () => {
                gsap.to(element, {
                    scale: 1,
                    duration: 0.2
                });
            });
        });
    });
    // Category filtering
    const categories = document.querySelectorAll('.category');
    categories.forEach(category => {
        category.addEventListener('click', function() {
            // Remove active class from all categories
            categories.forEach(c => c.classList.remove('active'));
            // Add active class to clicked category
            this.classList.add('active');
            // The page will reload with the new category, no alert needed
        });
    });

    function toggleDropdown(id) {
        const dropdown = document.getElementById(id);
        if (dropdown.style.display === 'block') {
            dropdown.style.display = 'none';
        } else {
            dropdown.style.display = 'block';
        }
    }

    function openPostModal(id) {
        const modal = document.getElementById(`post-modal-${id}`);
        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
        // Add entrance animation
        gsap.fromTo(modal.querySelector('.bg-white'), {
            scale: 0.9,
            opacity: 0
        }, {
            scale: 1,
            opacity: 1,
            duration: 0.3
        });
    }

    function closePostModal(id) {
        const modal = document.getElementById(`post-modal-${id}`);
        const modalContent = modal.querySelector('.bg-white');
        // Add exit animation
        gsap.to(modalContent, {
            scale: 0.9,
            opacity: 0,
            duration: 0.2,
            onComplete: () => {
                modal.classList.add('hidden');
                document.body.style.overflow = 'auto';
            }
        });
    }

    function openLikesModal(id) {
        const modal = document.getElementById(`likes-modal-${id}`);
        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
        // Add entrance animation
        gsap.fromTo(modal.querySelector('.modal-content'), {
            scale: 0.9,
            opacity: 0
        }, {
            scale: 1,
            opacity: 1,
            duration: 0.3
        });
    }

    function closeLikesModal(id) {
        const modal = document.getElementById(`likes-modal-${id}`);
        const modalContent = modal.querySelector('.modal-content');
        // Add exit animation
        gsap.to(modalContent, {
            scale: 0.9,
            opacity: 0,
            duration: 0.2,
            onComplete: () => {
                modal.classList.add('hidden');
                document.body.style.overflow = 'auto';
            }
        });
    }
    // Like button animation and logic
    document.querySelectorAll('.like-toggle-btn').forEach(button => {
        button.addEventListener('click', function(e) {
            e.stopPropagation(); // Prevent post modal from opening
            const postId = this.dataset.postId;
            const icon = this.querySelector('i');
            const text = this.querySelector('span');
            const likesCountSpan = document.querySelector(`.likes-count-${postId}`);
            const likeContainer = this.closest('.like-container');
            let action;
            // Determine action based on current icon class
            if (icon.classList.contains('far')) { // Currently unliked
                action = 'like';
            } else { // Currently liked
                action = 'unlike';
            }
            // Button animation
            gsap.fromTo(this, {
                scale: 1
            }, {
                scale: 1.2,
                duration: 0.15,
                yoyo: true,
                repeat: 1,
                ease: "power2.out"
            });
            // Send AJAX request
            fetch(`assets/php/actions.php?toggle_like=1&post_id=${postId}&action=${action}`)
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        // Update button based on the action performed
                        if (action === 'like') {
                            // Hide like button, show unlike button
                            this.style.display = 'none';
                            const unlikeBtn = likeContainer.querySelector('.unlike_btn');
                            unlikeBtn.style.display = 'flex';
                        } else {
                            // Hide unlike button, show like button
                            this.style.display = 'none';
                            const likeBtn = likeContainer.querySelector('.like_btn');
                            likeBtn.style.display = 'flex';
                        }
                        // Update like count
                        if (likesCountSpan) {
                            likesCountSpan.textContent = data.likes_count;
                        }
                    } else {
                        console.error('Error toggling like:', data.message);
                    }
                })
                .catch(error => {
                    console.error('Fetch error:', error);
                });
        });
    });
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('search-input');
        const searchResults = document.getElementById('search-results');
        searchInput.addEventListener('input', function() {
            const keyword = this.value.trim();
            if (keyword.length < 2) {
                searchResults.classList.add('hidden');
                return;
            }
            // Show loading indicator
            searchResults.innerHTML = '<div class="p-4 text-center"><div class="loader"></div></div>';
            searchResults.classList.remove('hidden');
            // Send AJAX request
            fetch('assets/php/actions.php?live_search=1', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: 'keyword=' + encodeURIComponent(keyword)
                })
                .then(response => response.json())
                .then(data => {
                    searchResults.innerHTML = '';
                    if (data.posts && data.posts.length > 0) {
                        data.posts.forEach(post => {
                            const postElement = document.createElement('div');
                            postElement.className = 'p-4 hover:bg-gray-100 cursor-pointer border-b border-gray-200';
                            postElement.innerHTML = `
                        <h3 class="font-semibold">${post.post_title}</h3>
                        <p class="text-sm text-gray-600">${post.post_desc.substring(0, 100)}...</p>
                        <div class="flex justify-between items-center mt-2">
                            <span class="text-xs bg-blue-100 text-blue-800 px-2 py-1 rounded">${post.post_category}</span>
                            <span class="text-xs text-gray-500">${new Date(post.created_at).toLocaleDateString()}</span>
                        </div>
                    `;
                            // Make the entire result clickable to view the post
                            postElement.addEventListener('click', function() {
                                openPostModal(post.id);
                                searchResults.classList.add('hidden');
                            });
                            searchResults.appendChild(postElement);
                        });
                    } else {
                        searchResults.innerHTML = '<div class="p-4 text-center text-gray-500">No posts found</div>';
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    searchResults.innerHTML = '<div class="p-4 text-center text-red-500">Error loading results</div>';
                });
        });
        // Hide search results when clicking outside
        document.addEventListener('click', function(e) {
            if (!searchInput.contains(e.target) && !searchResults.contains(e.target)) {
                searchResults.classList.add('hidden');
            }
        });
    });
    // Unlike button logic
    document.querySelectorAll('.unlike_btn').forEach(button => {
        button.addEventListener('click', function(e) {
            e.stopPropagation(); // Prevent post modal from opening
            const postId = this.dataset.postId;
            const likesCountSpan = document.querySelector(`.likes-count-${postId}`);
            const likeContainer = this.closest('.like-container');
            // Button animation
            gsap.fromTo(this, {
                scale: 1
            }, {
                scale: 1.2,
                duration: 0.15,
                yoyo: true,
                repeat: 1,
                ease: "power2.out"
            });
            // Send AJAX request
            fetch(`assets/php/actions.php?toggle_like=1&post_id=${postId}&action=unlike`)
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        // Hide unlike button, show like button
                        this.style.display = 'none';
                        const likeBtn = likeContainer.querySelector('.like_btn');
                        likeBtn.style.display = 'flex';
                        // Update like count
                        if (likesCountSpan) {
                            likesCountSpan.textContent = data.likes_count;
                        }
                    } else {
                        console.error('Error toggling like:', data.message);
                    }
                })
                .catch(error => {
                    console.error('Fetch error:', error);
                });
        });
    });
    // Comment button logic
    document.querySelectorAll('.add-comment').forEach(button => {
        button.addEventListener('click', function(e) {
            e.stopPropagation(); // Prevent post modal from opening
            const postId = this.dataset.postId;
            const commentSectionId = this.dataset.cs;
            const commentInput = this.parentElement.querySelector('.comment-input');
            const commentText = commentInput.value.trim();
            if (commentText === '') {
                // Show a subtle error message instead of alert
                gsap.to(commentInput, {
                    x: 10,
                    duration: 0.1,
                    repeat: 5,
                    yoyo: true,
                    ease: "power2.inOut",
                    onComplete: () => {
                        commentInput.focus();
                    }
                });
                return;
            }
            // Send AJAX request to add comment
            fetch('assets/php/actions.php?add_comment=1', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `post_id=${postId}&comment_text=${encodeURIComponent(commentText)}`
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        commentInput.value = '';
                        // Dynamically add comment to the comment section
                        const commentSection = document.getElementById(commentSectionId);
                        if (commentSection) {
                            // If "No comments yet" message exists, remove it
                            const noCommentsMsg = commentSection.querySelector('p.text-center');
                            if (noCommentsMsg) {
                                noCommentsMsg.remove();
                            }
                            const newCommentHtml = `
                                    <div class="flex items-start space-x-4 mb-4 new-comment">
                                        <img src="assets/img/profile/${data.user_profile_pic}"
                                            class="w-16 h-16 rounded-full object-cover border-2 border-green-200 shadow-sm">
                                        <div class="flex-1 flex flex-col">
                                            <div class="bg-green-50 rounded-2xl px-4 py-2">
                                                <div class="flex items-baseline space-x-1">
                                                    <a href="?u=${data.username}" class="font-bold text-gray-900 text-lg hover:opacity-80">
                                                        @${data.username}
                                                    </a>
                                                    <span class="text-sm text-gray-600 ml-2">(${data.time_ago})</span>
                                                </div>
                                                <p class="text-gray-800 text-base mt-1">${data.comment_text}</p>
                                            </div>
                                        </div>
                                    </div>
                                `;
                            commentSection.insertAdjacentHTML('beforeend', newCommentHtml);
                            // Animate the new comment
                            const newComment = commentSection.querySelector('.new-comment');
                            gsap.fromTo(newComment, {
                                opacity: 0,
                                y: 20
                            }, {
                                opacity: 1,
                                y: 0,
                                duration: 0.4
                            });
                            // Scroll to the bottom of the comment section
                            commentSection.scrollTop = commentSection.scrollHeight;
                        }
                    } else {
                        console.error('Error adding comment:', data.message);
                    }
                })
                .catch(error => {
                    console.error('Fetch error:', error);
                });
        });
    });
    // Follow/Unfollow button logic
    document.querySelectorAll('.followbtn, .unfollowbtn').forEach(button => {
        button.addEventListener('click', function(e) {
            e.stopPropagation(); // Prevent post modal from opening
            const userId = this.dataset.userId;
            const isFollowBtn = this.classList.contains('followbtn');
            const action = isFollowBtn ? 'follow' : 'unfollow';
            // Send AJAX request
            fetch(`assets/php/actions.php?toggle_follow=1&user_id=${userId}&action=${action}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        // Toggle button classes and text
                        if (isFollowBtn) {
                            this.classList.remove('bg-green-600', 'hover:bg-green-700', 'followbtn');
                            this.classList.add('bg-red-500', 'hover:bg-red-600', 'unfollowbtn');
                            this.innerHTML = '<i class="fas fa-user-times mr-1"></i> Unfollow';
                        } else {
                            this.classList.remove('bg-red-500', 'hover:bg-red-600', 'unfollowbtn');
                            this.classList.add('bg-green-600', 'hover:bg-green-700', 'followbtn');
                            this.innerHTML = '<i class="fas fa-user-plus mr-1"></i> Follow';
                        }
                        // Add a subtle animation
                        gsap.fromTo(this, {
                            scale: 1
                        }, {
                            scale: 1.1,
                            duration: 0.2,
                            yoyo: true,
                            repeat: 1
                        });
                    } else {
                        console.error('Error toggling follow:', data.message);
                    }
                })
                .catch(error => {
                    console.error('Fetch error:', error);
                });
        });
    });
    // Make the post card clickable to open modal, but exclude interactive elements
    document.querySelectorAll('.post-card').forEach(card => {
        card.addEventListener('click', function(e) {
            // Don't open modal if clicking on interactive elements
            if (!e.target.closest('.like-container') &&
                !e.target.closest('.add-comment') &&
                !e.target.closest('.unlike_btn') &&
                !e.target.closest('.like_btn') &&
                !e.target.closest('.followbtn') &&
                !e.target.closest('.unfollowbtn')) {
                const postId = this.dataset.postId;
                openPostModal(postId);
            }
        });
    });
    // Smooth scroll for better UX
    document.documentElement.style.scrollBehavior = 'smooth';
    // Close dropdowns when clicking outside
    document.addEventListener('click', function(event) {
        if (!event.target.closest('.relative')) {
            document.querySelectorAll('[id^="dropdown-"]').forEach(dropdown => {
                dropdown.style.display = 'none';
            });
        }
    });
    // Additional code for handling code input and highlighting
    document.addEventListener("DOMContentLoaded", function() {
        const codeInput = document.getElementById("code_input");
        const codeDisplay = document.getElementById("code_display");
        const langSelect = document.getElementById("language");
        const hiddenCode = document.getElementById("code_text");

        function syncCodeInput() {
            hiddenCode.value = codeInput.value;
        }

        function updateHighlight() {
            const selectedLang = langSelect.value || "javascript";
            codeDisplay.className = `language-${selectedLang} line-numbers`;
            codeDisplay.textContent = codeInput.value;
            if (typeof Prism !== 'undefined') {
                Prism.highlightElement(codeDisplay);
            }
        }
        if (codeInput && codeDisplay && langSelect) {
            codeInput.addEventListener("input", updateHighlight);
            langSelect.addEventListener("change", updateHighlight);
            // Initial highlight
            updateHighlight();
        }
        // Tag functionality
        const tagInput = document.getElementById("tag-input");
        const tagContainer = document.getElementById("tag-container");
        const hiddenTags = document.getElementById("post_tags");
        let tags = [];

        function renderTags() {
            tagContainer.querySelectorAll(".tag-item").forEach(el => el.remove());
            tags.forEach((tag, index) => {
                const tagEl = document.createElement("span");
                tagEl.className = "tag-item bg-green-100 text-green-800 text-xs font-medium px-3 py-1 rounded-full flex items-center";
                tagEl.innerHTML = `${tag}<button type="button" class="ml-2 text-green-500 hover:text-green-800" onclick="removeTag(${index})">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
          </svg>
        </button>`;
                tagContainer.insertBefore(tagEl, tagInput);
            });
            hiddenTags.value = tags.join(",");
        }

        function removeTag(index) {
            tags.splice(index, 1);
            renderTags();
        }
        if (tagInput) {
            tagInput.addEventListener("keydown", function(e) {
                if (e.key === "Enter" && this.value.trim() !== "") {
                    e.preventDefault();
                    const value = this.value.trim();
                    if (!tags.includes(value)) {
                        tags.push(value);
                        renderTags();
                    }
                    this.value = "";
                }
            });
        }
        // Post image preview 
        ["code", "nocode"].forEach(type => {
            const input = document.getElementById(`select_post_img_${type}`);
            const preview = document.getElementById(`post_img_${type}`);
            if (input && preview) {
                input.addEventListener("change", function() {
                    const file = this.files[0];
                    if (file) {
                        const reader = new FileReader();
                        reader.readAsDataURL(file);
                        reader.onload = function() {
                            preview.src = reader.result;
                            preview.style.display = "block";
                        };
                    }
                });
            }
        });
        // Expose to global scope if needed
        window.syncCodeInput = syncCodeInput;
        window.removeTag = removeTag;
    });
</script>
<!-- Bootstrap JS Bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
    crossorigin="anonymous"></script>
<script src="./assets/js/jquery.js"></script>
<script src="./assets/js/timeago_jquery.js"></script>
<!-- Prism.js for code highlighting -->
<link href="https://cdn.jsdelivr.net/npm/prismjs@1.29.0/themes/prism-tomorrow.min.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/prismjs@1.29.0/plugins/line-numbers/prism-line-numbers.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/prismjs@1.29.0/components/prism-core.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/prismjs@1.29.0/plugins/line-numbers/prism-line-numbers.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/prismjs@1.29.0/plugins/autoloader/prism-autoloader.min.js"></script>
<script>
    Prism.plugins.autoloader.languages_path = 'https://cdn.jsdelivr.net/npm/prismjs@1.29.0/components/';
</script>
<script src="./assets/js/index.js?v=<?= time() ?>"></script>