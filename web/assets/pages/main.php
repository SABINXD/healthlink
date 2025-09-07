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
<!-- Custom CSS -->
<style>
    /* Custom animations and styles */
    :root {
        --primary: #10b981;
        --primary-dark: #059669;
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
</head>
<!-- New Header Section -->
<section class="bg-gradient-to-r from-teal-50 to-green-50 py-16 text-center border-b border-border">
    <div class="container mx-auto px-4">
        <h1 class="text-4xl font-bold mb-4 text-dark">Health Community Forum</h1>
        <p class="text-lg text-secondary max-w-2xl mx-auto mb-8">Ask questions, share experiences, and connect with others on your health journey</p>
        <div class="max-w-xl mx-auto relative">
            <input type="text" placeholder="Search for health topics, conditions, or advice..." class="w-full py-4 px-6 rounded-full border border-border shadow-md text-base">
            <button class="absolute right-2 top-1/2 transform -translate-y-1/2 bg-primary text-white w-10 h-10 rounded-full flex items-center justify-center">
                <i class="fas fa-search"></i>
            </button>
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
                <div class="px-4 py-2 bg-white border border-border rounded-full text-sm cursor-pointer transition-all hover:bg-primary hover:text-white hover:border-primary category active">All Topics</div>
                <div class="px-4 py-2 bg-white border border-border rounded-full text-sm cursor-pointer transition-all hover:bg-primary hover:text-white hover:border-primary category">Mental Health</div>
                <div class="px-4 py-2 bg-white border border-border rounded-full text-sm cursor-pointer transition-all hover:bg-primary hover:text-white hover:border-primary category">Nutrition</div>
                <div class="px-4 py-2 bg-white border border-border rounded-full text-sm cursor-pointer transition-all hover:bg-primary hover:text-white hover:border-primary category">Fitness</div>
                <div class="px-4 py-2 bg-white border border-border rounded-full text-sm cursor-pointer transition-all hover:bg-primary hover:text-white hover:border-primary category">Chronic Conditions</div>
                <div class="px-4 py-2 bg-white border border-border rounded-full text-sm cursor-pointer transition-all hover:bg-primary hover:text-white hover:border-primary category">Parenting</div>
                <div class="px-4 py-2 bg-white border border-border rounded-full text-sm cursor-pointer transition-all hover:bg-primary hover:text-white hover:border-primary category">Aging</div>
                <div class="px-4 py-2 bg-white border border-border rounded-full text-sm cursor-pointer transition-all hover:bg-primary hover:text-white hover:border-primary category">Women's Health</div>
                <div class="px-4 py-2 bg-white border border-border rounded-full text-sm cursor-pointer transition-all hover:bg-primary hover:text-white hover:border-primary category">Men's Health</div>
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
                foreach ($posts as $post):
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
                                            if ($p_user['is_doctor'] == 1) {
                                                echo 'Dr ' . htmlspecialchars($post['first_name'] . ' ' . $post['last_name']);
                                            } else {
                                                echo htmlspecialchars($post['first_name'] . ' ' . $post['last_name']);
                                            }
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
                                <span class="px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-xs font-medium">
                                    <?= htmlspecialchars($post['post_category']) ?>
                                </span>
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
                                    <!-- Post Content Section -->
                                    <div class="mb-8">
                                        <div class="flex justify-between items-start mb-4">
                                            <div>
                                                <h2 class="text-2xl font-bold text-gray-900 mb-2"><?= htmlspecialchars($post['post_title'] ?: 'Health Discussion') ?></h2>
                                                <div class="flex gap-4 text-gray-600 text-sm">
                                                    <span><i class="far fa-user"></i>
                                                        <?php
                                                        if ($post['post_privacy'] == 0) {
                                                            if ($p_user['is_doctor'] == 1) {
                                                                echo 'Dr ' . htmlspecialchars($post['first_name'] . ' ' . $post['last_name']);
                                                            } else {
                                                                echo htmlspecialchars($post['first_name'] . ' ' . $post['last_name']);
                                                            }
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
                                                <span class="px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-sm font-medium">
                                                    <?= htmlspecialchars($post['post_category']) ?>
                                                </span>
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
                                            <?= nl2br(htmlspecialchars($post['code_content'])) ?>
                                        </div>
                                    </div>

                                    <!-- Possible Conditions Section -->
                                    <div class="bg-amber-50 border border-amber-200 rounded-xl p-5 mb-8">
                                        <div class="flex items-center gap-2 mb-3">
                                            <h3 class="text-xl font-bold text-gray-900">Possible Conditions (AI Analysis)</h3>
                                            <i class="fas fa-brain text-amber-500 text-xl"></i>
                                        </div>
                                        <div class="space-y-3">
                                            <div class="flex justify-between items-center p-3 bg-white rounded-lg border border-amber-200">
                                                <span class="font-medium text-gray-900">General Health</span>
                                                <span class="bg-amber-200 text-amber-800 px-3 py-1 rounded-full text-sm font-semibold">75%</span>
                                            </div>
                                            <div class="flex justify-between items-center p-3 bg-white rounded-lg border border-amber-200">
                                                <span class="font-medium text-gray-900">Preventive Care</span>
                                                <span class="bg-amber-200 text-amber-800 px-3 py-1 rounded-full text-sm font-semibold">60%</span>
                                            </div>
                                            <div class="flex justify-between items-center p-3 bg-white rounded-lg border border-amber-200">
                                                <span class="font-medium text-gray-900">Lifestyle Factors</span>
                                                <span class="bg-amber-200 text-amber-800 px-3 py-1 rounded-full text-sm font-semibold">45%</span>
                                            </div>
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



<!-- Toggle Sync Script -->
<script>
    // Toggle Sync Script
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
</script>

<!-- Include Prism.js for code highlighting -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/themes/prism-tomorrow.min.css" rel="stylesheet" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/prism.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/components/prism-javascript.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/components/prism-python.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/components/prism-php.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/components/prism-css.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/components/prism-java.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/components/prism-cpp.min.js"></script>
<script>
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

            // In a real application, you would filter posts based on the selected category
            // For this demo, we'll just show an alert
            if (this.textContent !== 'All Topics') {
                alert(`Showing posts in ${this.textContent} category`);
            }
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
</script>