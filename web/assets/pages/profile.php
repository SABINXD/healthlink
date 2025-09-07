<?php
global $profile;
global $profile_post;
global $user;
// Filter posts based on privacy and viewer
$filtered_posts = [];
if (!empty($profile_post)) {
    foreach ($profile_post as $post) {
        // If post is public OR current user is the profile owner, include it
        if ($post['post_privacy'] == 0 || $user['id'] == $profile['id']) {
            $filtered_posts[] = $post;
        }
    }
}
?>
<div class="min-h-screen bg-gradient-to-br from-green-50 to-emerald-50 pt-16">
    <!-- Cover Photo Section -->
    <div class="relative">
        <div class="h-48 md:h-64 bg-gradient-to-r from-green-500 to-teal-600 relative overflow-hidden">
            <div class="absolute inset-0 bg-black opacity-20"></div>
            <div class="absolute inset-0 flex items-center justify-center">
                <div class="text-white text-center">
                    <h1 class="text-3xl md:text-4xl font-bold animate-fade-in">
                        <?php if ($profile['is_doctor'] == 1): ?>
                            <span class="text-green-200">Dr. </span>
                        <?php endif; ?>
                        <?= htmlspecialchars($profile['first_name']) ?> <?= htmlspecialchars($profile['last_name']) ?>
                        <?php if ($profile['is_doctor'] == 1 && !empty($profile['is_verified']) && $profile['is_verified']): ?>
                            <!-- Green verification badge for doctors -->
                            <svg class="w-5 h-5 inline-block ml-1 text-green-300 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        <?php endif; ?>
                    </h1>
                    <p class="text-lg opacity-90 animate-fade-in animation-delay-200">@<?= htmlspecialchars($profile['username']) ?></p>
                </div>
            </div>
            <!-- Animated background elements -->
            <div class="absolute -top-20 -right-20 w-40 h-40 bg-green-400 rounded-full mix-blend-multiply filter blur-xl opacity-20 animate-blob"></div>
            <div class="absolute -bottom-20 -left-20 w-32 h-32 bg-teal-400 rounded-full mix-blend-multiply filter blur-xl opacity-20 animate-blob animation-delay-2000"></div>
        </div>
        <!-- Profile Picture -->
        <div class="absolute -bottom-16 left-6 md:left-12">
            <div class="relative group">
                <div class="absolute inset-0 bg-gradient-to-r from-green-500 to-teal-600 rounded-full opacity-0 group-hover:opacity-20 transition-opacity duration-300 scale-110 animate-pulse-slow"></div>
                <img src="assets/img/profile/<?= htmlspecialchars($profile['profile_pic']) ?>" alt="Profile Picture"
                    class="w-32 h-32 rounded-full object-cover border-4 border-white shadow-lg transform transition-all duration-500 group-hover:scale-105"
                    onerror="this.onerror=null; this.src='assets/img/profile/default-avatar.png'">
                <div class="absolute bottom-0 right-0 bg-green-500 w-8 h-8 rounded-full border-2 border-white flex items-center justify-center shadow-md animate-ping-slow">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>
    <!-- Profile Info Section -->
    <div class="max-w-6xl mx-auto mt-20 px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 pb-6 border-b border-green-200 animate-fade-in-up">
            <div class="mt-2">
                <h2 class="text-2xl font-bold text-gray-900">
                    <?php
                    if ($profile['is_doctor'] == 1) {
                        echo '<div class="inline-flex gap-[3px] items-center justify-center font-medium">';
                        // Doctor prefix
                        echo '<span class="text-green-700 font-bold">Dr </span>';
                        // Doctor name
                        echo '<span class="text-gray-900">' . htmlspecialchars($profile['first_name'] . ' ' . $profile['last_name']) . '</span>';
                        // Verified checkmark icon
                        echo ' ';
                        if (!empty($profile['is_verified']) && $profile['is_verified']) {
                            echo '<svg class="w-4 h-4 text-green-500 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">';
                            echo '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>';
                            echo '</svg>';
                        }
                        echo '</div>';
                    } else {
                        echo htmlspecialchars($profile['first_name'] . ' ' . $profile['last_name']);
                    }
                    ?>
                </h2>
                <div class="flex items-center gap-2 mt-1">
                    <span class="text-gray-600">@<?= htmlspecialchars($profile['username']) ?></span>
                    <!-- Only show green shield for non-doctors -->
                    <?php if (!empty($profile['is_verified']) && $profile['is_verified'] && $profile['is_doctor'] != 1): ?>
                        <svg class="w-5 h-5 text-green-500 animate-pulse" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" d="M2.166 4.999A11.954 11.954 0 0010 1.944 11.954 11.954 0 0017.834 5c.11.65.166 1.32.166 2.001 0 5.225-3.34 9.67-8 11.317C5.34 16.67 2 12.225 2 7c0-.682.057-1.35.166-2.001zm11.541 3.708a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                        </svg>
                    <?php endif; ?>
                </div>
                <?php if (!empty($profile['bio'])): ?>
                    <p class="mt-3 text-gray-700 animate-fade-in animation-delay-300"><?= htmlspecialchars($profile['bio']) ?></p>
                <?php endif; ?>
                <!-- Stats -->
                <div class="flex flex-wrap gap-4 mt-4 animate-fade-in animation-delay-400">
                    <div class="flex items-center gap-1 group cursor-pointer">
                        <div class="flex items-center gap-1 group cursor-pointer">
                            <span class="font-semibold text-gray-900 group-hover:text-green-600 transition-colors"><?= count($filtered_posts) ?></span>
                            <span class="text-gray-600 group-hover:text-green-600 transition-colors">posts</span>
                        </div>
                    </div>
                    <div class="flex items-center gap-1 cursor-pointer group" data-bs-toggle="modal" data-bs-target="#follower_list">
                        <span class="font-semibold text-gray-900 group-hover:text-green-600 transition-colors"><?= count($profile['followers']) ?></span>
                        <span class="text-gray-600 group-hover:text-green-600 transition-colors">followers</span>
                    </div>
                    <div class="flex items-center gap-1 cursor-pointer group" data-bs-toggle="modal" data-bs-target="#following_list">
                        <span class="font-semibold text-gray-900 group-hover:text-green-600 transition-colors"><?= count($profile['following']) ?></span>
                        <span class="text-gray-600 group-hover:text-green-600 transition-colors">following</span>
                    </div>
                </div>
            </div>
            <!-- Action Buttons -->
            <div class="flex flex-wrap gap-3 w-full md:w-auto animate-fade-in animation-delay-500">
                <?php if ($user['id'] == $profile['id']): ?>
                    <a href="?editprofile" class="flex-1 md:flex-none bg-white border border-green-300 text-green-700 font-medium py-2 px-4 rounded-lg hover:bg-green-50 transition-all duration-300 flex items-center justify-center gap-2 shadow-sm hover:shadow-md transform hover:-translate-y-0.5">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                        Edit Profile
                    </a>
                    <!-- Verify as Doctor Button (only for non-doctors viewing their own profile) -->
                    <?php if ($profile['is_doctor'] == 0): ?>
                        <a href="?verifydoctor" class="flex-1 md:flex-none bg-gradient-to-r from-yellow-500 to-yellow-600 text-white font-medium py-2 px-4 rounded-lg hover:from-yellow-600 hover:to-yellow-700 transition-all duration-300 flex items-center justify-center gap-2 shadow-md hover:shadow-lg transform hover:-translate-y-0.5">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Verify as Doctor
                        </a>
                    <?php endif; ?>
                <?php else: ?>
                    <?php if (checkBlockStatus($user['id'], $profile['id'])): ?>
                        <button class="flex-1 md:flex-none bg-gradient-to-r from-red-500 to-red-600 text-white font-medium py-2 px-4 rounded-lg hover:from-red-600 hover:to-red-700 transition-all duration-300 shadow-md hover:shadow-lg transform hover:-translate-y-0.5 unblockbtn" data-user-id="<?= htmlspecialchars($profile['id']) ?>">
                            Unblock
                        </button>
                    <?php elseif (checkBS($profile['id'])): ?>
                        <div class="flex-1 md:flex-none bg-gray-100 text-gray-500 font-medium py-2 px-4 rounded-lg cursor-not-allowed shadow-sm">
                            Blocked
                        </div>
                    <?php else: ?>
                        <?php if (checkFollowed($profile['id'])): ?>
                            <button class="flex-1 md:flex-none bg-white border border-green-300 text-green-700 font-medium py-2 px-4 rounded-lg hover:bg-green-50 transition-all duration-300 shadow-sm hover:shadow-md transform hover:-translate-y-0.5 unfollowbtn" data-user-id="<?= htmlspecialchars($profile['id']) ?>">
                                Following
                            </button>
                        <?php else: ?>
                            <button class="flex-1 md:flex-none bg-gradient-to-r from-green-600 to-teal-700 text-white font-medium py-2 px-4 rounded-lg hover:from-green-700 hover:to-teal-800 transition-all duration-300 shadow-md hover:shadow-lg transform hover:-translate-y-0.5 followbtn" data-user-id="<?= htmlspecialchars($profile['id']) ?>">
                                Follow
                            </button>
                        <?php endif; ?>
                        <!-- Book Appointment Button for Doctors (only for non-doctor users) -->
                        <?php if ($profile['is_doctor'] == 1 && $user['is_doctor'] != 1): ?>
                            <a href="?bookappointment=<?= htmlspecialchars($profile['id']) ?>" class="flex-1 md:flex-none bg-gradient-to-r from-blue-600 to-teal-700 text-white font-medium py-2 px-4 rounded-lg hover:from-blue-700 hover:to-teal-800 transition-all duration-300 flex items-center justify-center gap-2 shadow-md hover:shadow-lg transform hover:-translate-y-0.5">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                                Book Appointment
                            </a>
                        <?php endif; ?>
                        <div class="relative inline-block text-left">
                            <button type="button" class="p-2 text-gray-600 hover:text-gray-900 hover:bg-green-100 rounded-full transition-all duration-300 hover:shadow-md transform hover:-translate-y-0.5" id="menu-button" aria-expanded="false" aria-haspopup="true">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h.01M12 12h.01M19 12h.01M6 12a1 1 0 11-2 0 1 1 0 012 0zm7 0a1 1 0 11-2 0 1 1 0 012 0zm7 0a1 1 0 11-2 0 1 1 0 012 0z"></path>
                                </svg>
                            </button>
                            <!-- Dropdown menu -->
                            <div class="origin-top-right absolute right-0 mt-2 w-48 rounded-lg shadow-lg bg-white/90 backdrop-blur-sm ring-1 ring-black ring-opacity-5 focus:outline-none z-50 hidden transition-all duration-300 transform opacity-0 scale-95" role="menu" aria-orientation="vertical" aria-labelledby="menu-button" tabindex="-1" id="dropdown-menu">
                                <a href="#"
                                    data-bs-toggle="modal" data-bs-target="#chatbox"
                                    onclick="popchat(<?= htmlspecialchars($profile['id']) ?>)"
                                    class="block px-4 py-3 text-gray-700 hover:bg-green-50 hover:text-green-600 transition-colors duration-200 flex items-center gap-2" role="menuitem" tabindex="-1">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                                    </svg>
                                    Message
                                </a>
                                <!-- Book Appointment Option in Dropdown (only for non-doctor users) -->
                                <?php if ($profile['is_doctor'] == 1 && $user['is_doctor'] != 1): ?>
                                    <a href="?book_appointment=<?= htmlspecialchars($profile['id']) ?>" class="block px-4 py-3 text-gray-700 hover:bg-green-50 hover:text-green-600 transition-colors duration-200 flex items-center gap-2" role="menuitem" tabindex="-1">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                        </svg>
                                        Book Appointment
                                    </a>
                                <?php endif; ?>
                                <a href="assets/php/actions.php?block=<?= htmlspecialchars($profile['id']) ?>&username=<?= htmlspecialchars($profile['username']) ?>"
                                    class="block px-4 py-3 text-gray-700 hover:bg-green-50 hover:text-green-600 transition-colors duration-200 flex items-center gap-2" role="menuitem" tabindex="-1">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728a9 9 0 015.636 5.636m-12.728 12.728L5.636 5.636"></path>
                                    </svg>
                                    Block
                                </a>
                            </div>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
        <!-- Navigation Tabs -->
        <div class="border-b border-green-200 mt-2 animate-fade-in animation-delay-600">
            <nav class="-mb-px flex space-x-8" aria-label="Tabs">
                <a href="#" class="border-green-500 text-green-600 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm flex items-center transition-all duration-300 hover:text-green-700">
                    <svg class="w-5 h-5 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path>
                    </svg>
                    POSTS
                </a>
                <a href="#" class="border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm flex items-center transition-all duration-300">
                    <svg class="w-5 h-5 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"></path>
                    </svg>
                    SAVED
                </a>
                <a href="#" class="border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm flex items-center transition-all duration-300">
                    <svg class="w-5 h-5 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                    TAGGED
                </a>
            </nav>
        </div>
        <?php
        // Check if account is private and user is not following
        if (checkBS($profile['id'])) {
            $filtered_posts = [];
        ?>
            <div class="bg-white rounded-xl shadow-sm p-8 text-center mt-6 animate-fade-in animation-delay-700">
                <div class="flex justify-center mb-4">
                    <svg class="w-16 h-16 text-gray-400 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mb-1">This account is private</h3>
                <p class="text-gray-500">Follow this account to see their photos and videos.</p>
            </div>
        <?php
        } else if (count($filtered_posts) < 1 && $user['id'] == $profile['id']) {
        ?>
            <div class="bg-white rounded-xl shadow-sm p-8 text-center mt-6 animate-fade-in animation-delay-700">
                <div class="flex justify-center mb-4">
                    <svg class="w-16 h-16 text-gray-400 animate-bounce" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mb-1">Share Photos</h3>
                <p class="text-gray-500 mb-4">When you share photos, they will appear on your profile.</p>
                <label for="addpost" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-gradient-to-r from-green-600 to-teal-600 hover:from-green-700 hover:to-teal-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 cursor-pointer transition-all duration-300 transform hover:-translate-y-0.5">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    Share your first photo
                </label>
            </div>
        <?php
        } else if (count($filtered_posts) < 1) {
        ?>
            <div class="bg-white rounded-xl shadow-sm p-8 text-center mt-6 animate-fade-in animation-delay-700">
                <div class="flex justify-center mb-4">
                    <svg class="w-16 h-16 text-gray-400 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mb-1">No Posts Yet</h3>
                <p class="text-gray-500">When they post, you'll see their photos and videos here.</p>
            </div>
        <?php
        } else {
        ?>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-6" id="posts-grid">
                <?php
                foreach ($filtered_posts as $post) {
                    $likes = getLikesCount($post['id']);
                    $comments = getComments($post['id']);
                    // Check if post is anonymous and current user is not the owner
                    $is_anonymous = ($post['post_privacy'] == 1 && $user['id'] != $profile['id']);
                ?>
                    <div class="overflow-hidden rounded-xl aspect-[16/9] cursor-pointer group relative animate-fade-in-up" data-bs-toggle="modal" data-bs-target="#postview<?= htmlspecialchars($post['id']) ?>">
                        <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-all duration-300 z-10 flex items-end p-4">
                            <div class="text-white w-full">
                                <div class="flex justify-between items-center">
                                    <div class="flex items-center gap-2">
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                            <path fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" clip-rule="evenodd"></path>
                                        </svg>
                                        <span><?= count($likes) ?></span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                                        </svg>
                                        <span><?= count($comments) ?></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <img src="assets/img/posts/<?= htmlspecialchars($post['post_img']) ?>" alt="Post Image"
                            class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110">
                    </div>
                    <!-- Modal -->
                    <div class="modal fade" id="postview<?= htmlspecialchars($post['id']) ?>" tabindex="-1" aria-labelledby="postviewLabel<?= htmlspecialchars($post['id']) ?>" aria-hidden="true">
                        <div class="modal-dialog modal-xl modal-dialog-centered max-w-7xl">
                            <div class="modal-content rounded-xl overflow-hidden shadow-2xl">
                                <div class="modal-body flex flex-col md:flex-row p-0 h-[90vh]">
                                    <!-- Left: Post image -->
                                    <div class="md:w-2/3 w-full bg-black flex items-center justify-center">
                                        <img src="assets/img/posts/<?= htmlspecialchars($post['post_img']) ?>" alt="Post Image" class="max-h-[90vh] object-contain w-full">
                                    </div>
                                    <!-- Right: Comment and info panel -->
                                    <div class="md:w-1/3 w-full flex flex-col bg-white border-l border-green-200">
                                        <!-- Header -->
                                        <div class="flex items-center p-4 border-b border-green-300 flex-shrink-0">
                                            <?php if ($is_anonymous): ?>
                                                <!-- Anonymous post indicator -->
                                                <div class="w-12 h-12 rounded-full bg-gray-200 flex items-center justify-center">
                                                    <svg class="w-6 h-6 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                                    </svg>
                                                </div>
                                                <div class="ml-4 flex-1">
                                                    <h6 class="font-semibold text-lg mb-0">Anonymous User</h6>
                                                    <p class="text-gray-500 text-sm">@anonymous</p>
                                                </div>
                                            <?php else: ?>
                                                <img src="assets/img/profile/<?= htmlspecialchars($profile['profile_pic']) ?>" alt="Profile Pic" class="w-12 h-12 rounded-full border border-green-300 object-cover">
                                                <div class="ml-4 flex-1">
                                                    <h6 class="font-semibold text-lg mb-0">
                                                        <?php
                                                        if ($profile['is_doctor'] == 1) {
                                                            echo '<div class="inline-flex gap-[3px] items-center justify-center font-medium">';
                                                            // Doctor prefix
                                                            echo '<span class="text-green-700 font-bold">Dr </span>';
                                                            // Doctor name
                                                            echo '<span class="text-gray-900">' . htmlspecialchars($profile['first_name'] . ' ' . $profile['last_name']) . '</span>';
                                                            // Verified checkmark icon
                                                            echo ' ';
                                                            if (!empty($profile['is_verified']) && $profile['is_verified']) {
                                                                echo '<svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">';
                                                                echo '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>';
                                                                echo '</svg>';
                                                            }
                                                            echo '</div>';
                                                        } else {
                                                            echo htmlspecialchars($profile['first_name'] . ' ' . $profile['last_name']);
                                                        }
                                                        ?>
                                                    </h6>
                                                    <p class="text-gray-500 text-sm">@<?= htmlspecialchars($profile['username']) ?></p>
                                                </div>
                                            <?php endif; ?>
                                            <div class="relative text-right">
                                                <button class="font-medium text-gray-700 <?= count($likes) < 1 ? 'opacity-50 cursor-not-allowed' : '' ?>" id="likesDropdownBtn<?= htmlspecialchars($post['id']) ?>" data-bs-toggle="dropdown" aria-expanded="false">
                                                    <?= count($likes) ?> likes
                                                </button>
                                                <ul class="dropdown-menu dropdown-menu-end max-h-48 overflow-auto" aria-labelledby="likesDropdownBtn<?= htmlspecialchars($post['id']) ?>">
                                                    <?php
                                                    foreach ($likes as $like) {
                                                        $lu = getUser($like['user_id']);
                                                    ?>
                                                        <li>
                                                            <a href="?u=<?= htmlspecialchars($lu['username']) ?>" class="block px-4 py-2 hover:bg-gray-100">
                                                                <?php
                                                                if ($lu['is_doctor'] == 1) {
                                                                    echo '<div class="inline-flex gap-[3px] items-center justify-center font-medium">';
                                                                    // Doctor prefix
                                                                    echo '<span class="text-green-700 font-bold">Dr </span>';
                                                                    // Doctor name
                                                                    echo '<span class="text-gray-900">' . htmlspecialchars($lu['first_name'] . ' ' . $lu['last_name']) . '</span>';
                                                                    // Verified checkmark icon
                                                                    echo ' ';
                                                                    if (!empty($lu['is_verified']) && $lu['is_verified']) {
                                                                        echo '<svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">';
                                                                        echo '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>';
                                                                        echo '</svg>';
                                                                    }
                                                                    echo '</div>';
                                                                } else {
                                                                    echo htmlspecialchars($lu['first_name'] . ' ' . $lu['last_name']);
                                                                }
                                                                ?> (@<?= htmlspecialchars($lu['username']) ?>)
                                                            </a>
                                                        </li>
                                                    <?php } ?>
                                                </ul>
                                                <div class="text-xs text-gray-400 mt-1">Posted <?= show_time($post['created_at']) ?></div>
                                            </div>
                                        </div>
                                        <!-- Comments scrollable area -->
                                        <div id="comment-section<?= htmlspecialchars($post['id']) ?>" class="flex-1 overflow-y-auto px-4 py-2">
                                            <?php if (count($comments) < 1): ?>
                                                <p class="text-center text-gray-400 py-4">No comments</p>
                                            <?php endif; ?>
                                            <?php foreach ($comments as $comment):
                                                $cuser = getUser($comment['user_id']);
                                            ?>
                                                <div class="flex items-center gap-3 mb-3 animate-fade-in">
                                                    <img src="assets/img/profile/<?= htmlspecialchars($cuser['profile_pic']) ?>" alt="Commenter Pic" class="w-10 h-10 rounded-full border border-green-300 object-cover">
                                                    <div>
                                                        <p class="text-sm font-semibold mb-0">
                                                            <a href="?u=<?= htmlspecialchars($cuser['username']) ?>" class="text-gray-700 hover:underline">
                                                                <?php
                                                                if ($cuser['is_doctor'] == 1) {
                                                                    echo '<div class="inline-flex gap-[3px] items-center justify-center font-medium">';
                                                                    // Doctor prefix
                                                                    echo '<span class="text-green-700 font-bold">Dr </span>';
                                                                    // Doctor name
                                                                    echo '<span class="text-gray-900">' . htmlspecialchars($cuser['first_name'] . ' ' . $cuser['last_name']) . '</span>';
                                                                    // Verified checkmark icon
                                                                    echo ' ';
                                                                    if (!empty($cuser['is_verified']) && $cuser['is_verified']) {
                                                                        echo '<svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">';
                                                                        echo '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>';
                                                                        echo '</svg>';
                                                                    }
                                                                    echo '</div>';
                                                                } else {
                                                                    echo htmlspecialchars($cuser['first_name'] . ' ' . $cuser['last_name']);
                                                                }
                                                                ?>
                                                            </a> - <?= htmlspecialchars($comment['comment']) ?>
                                                        </p>
                                                        <p class="text-xs text-gray-400 mb-0">(<?= show_time($comment['created_at']) ?>)</p>
                                                    </div>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                        <!-- Comment input fixed at bottom -->
                                        <div class="border-t border-green-300 p-3 flex gap-2 flex-shrink-0">
                                            <input type="text" class="flex-1 border border-green-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500 comment-input" placeholder="Say something.."
                                                aria-label="Add comment" />
                                            <button class="bg-gradient-to-r from-green-600 to-teal-600 text-white px-4 py-2 rounded-lg hover:from-green-700 hover:to-teal-700 transition-all duration-300 add-comment" data-cs="comment-section<?= htmlspecialchars($post['id']) ?>" data-post-id="<?= htmlspecialchars($post['id']) ?>">Post</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php
                }
                ?>
            </div>
        <?php
        }
        ?>
    </div>
</div>
<div class="py-6">
    <!-- Back to top button -->
    <button id="backToTop" class="fixed bottom-8 right-8 bg-gradient-to-r from-green-600 to-teal-600 text-white p-3 rounded-full shadow-lg hover:from-green-700 hover:to-teal-700 transition-all duration-300 opacity-0 invisible transform hover:scale-110">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path>
        </svg>
    </button>
    <!-- Follower List Modal -->
    <div class="modal fade" id="follower_list" tabindex="-1" aria-labelledby="followerListLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content rounded-xl overflow-hidden shadow-2xl">
                <div class="modal-header flex justify-between items-center p-4 border-b border-green-200">
                    <h5 class="text-xl font-bold text-gray-900">Followers</h5>
                    <button type="button" class="text-gray-400 hover:text-gray-500 focus:outline-none transition-colors duration-200" data-bs-dismiss="modal" aria-label="Close">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                <div class="modal-body max-h-96 overflow-y-auto p-0">
                    <?php if (count($profile['followers']) < 1): ?>
                        <div class="p-8 text-center">
                            <svg class="w-16 h-16 mx-auto text-gray-400 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                            </svg>
                            <h3 class="mt-4 text-lg font-medium text-gray-900">No followers yet</h3>
                            <p class="mt-1 text-gray-500">When someone follows this account, they'll appear here.</p>
                        </div>
                    <?php else: ?>
                        <ul class="divide-y divide-green-200">
                            <?php foreach ($profile['followers'] as $f):
                                $fuser = getUser($f['follower_id']);
                                $fbtn = "";
                                if (checkFollowed($f['follower_id'])) {
                                    $fbtn = '<button class="bg-white border border-green-300 text-green-700 text-sm font-medium py-1 px-3 rounded-lg hover:bg-green-50 transition-colors duration-200 unfollowbtn" data-user-id="' . htmlspecialchars($fuser['id']) . '">Following</button>';
                                } else if ($user['id'] == $f['follower_id']) {
                                    $fbtn = "";
                                } else {
                                    $fbtn = '<button class="bg-gradient-to-r from-green-600 to-teal-600 text-white text-sm font-medium py-1 px-3 rounded-lg hover:from-green-700 hover:to-teal-700 transition-all duration-300 followbtn" data-user-id="' . htmlspecialchars($fuser['id']) . '">Follow</button>';
                                }
                            ?>
                                <li class="p-4 hover:bg-green-50 transition-colors duration-200">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center gap-3">
                                            <img src="assets/img/profile/<?= htmlspecialchars($fuser['profile_pic']) ?>" alt="Follower Pic" class="w-12 h-12 rounded-full object-cover"
                                                onerror="this.onerror=null; this.src='assets/img/profile/default-avatar.png'">
                                            <div>
                                                <a href="?u=<?= htmlspecialchars($fuser['username']) ?>" class="font-semibold text-gray-900 hover:underline">
                                                    <?php
                                                    if ($fuser['is_doctor'] == 1) {
                                                        echo '<div class="inline-flex gap-[3px] items-center justify-center font-medium">';
                                                        // Doctor prefix
                                                        echo '<span class="text-green-700 font-bold">Dr </span>';
                                                        // Doctor name
                                                        echo '<span class="text-gray-900">' . htmlspecialchars($fuser['first_name'] . ' ' . $fuser['last_name']) . '</span>';
                                                        // Verified checkmark icon
                                                        echo ' ';
                                                        if (!empty($fuser['is_verified']) && $fuser['is_verified']) {
                                                            echo '<svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">';
                                                            echo '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>';
                                                            echo '</svg>';
                                                        }
                                                        echo '</div>';
                                                    } else {
                                                        echo htmlspecialchars($fuser['first_name'] . ' ' . $fuser['last_name']);
                                                    }
                                                    ?>
                                                </a>
                                                <p class="text-sm text-gray-500">@<?= htmlspecialchars($fuser['username']) ?></p>
                                            </div>
                                        </div>
                                        <div><?= $fbtn ?></div>
                                    </div>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    <!-- Following List Modal -->
    <div class="modal fade" id="following_list" tabindex="-1" aria-labelledby="followingListLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content rounded-xl overflow-hidden shadow-2xl">
                <div class="modal-header flex justify-between items-center p-4 border-b border-green-200">
                    <h5 class="text-xl font-bold text-gray-900">Following</h5>
                    <button type="button" class="text-gray-400 hover:text-gray-500 focus:outline-none transition-colors duration-200" data-bs-dismiss="modal" aria-label="Close">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                <div class="modal-body max-h-96 overflow-y-auto p-0">
                    <?php if (count($profile['following']) < 1): ?>
                        <div class="p-8 text-center">
                            <svg class="w-16 h-16 mx-auto text-gray-400 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283-.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                            <h3 class="mt-4 text-lg font-medium text-gray-900">Not following anyone</h3>
                            <p class="mt-1 text-gray-500">When they follow someone, they'll appear here.</p>
                        </div>
                    <?php else: ?>
                        <ul class="divide-y divide-green-200">
                            <?php foreach ($profile['following'] as $f):
                                $fuser = getUser($f['user_id']);
                                $fbtn = "";
                                if (checkFollowed($f['user_id'])) {
                                    $fbtn = '<button class="bg-white border border-green-300 text-green-700 text-sm font-medium py-1 px-3 rounded-lg hover:bg-green-50 transition-colors duration-200 unfollowbtn" data-user-id="' . htmlspecialchars($fuser['id']) . '">Following</button>';
                                } else if ($user['id'] == $f['user_id']) {
                                    $fbtn = "";
                                } else {
                                    $fbtn = '<button class="bg-gradient-to-r from-green-600 to-teal-600 text-white text-sm font-medium py-1 px-3 rounded-lg hover:from-green-700 hover:to-teal-700 transition-all duration-300 followbtn" data-user-id="' . htmlspecialchars($fuser['id']) . '">Follow</button>';
                                }
                            ?>
                                <li class="p-4 hover:bg-green-50 transition-colors duration-200">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center gap-3">
                                            <img src="assets/img/profile/<?= htmlspecialchars($fuser['profile_pic']) ?>" alt="Following Pic" class="w-12 h-12 rounded-full object-cover"
                                                onerror="this.onerror=null; this.src='assets/img/profile/default-avatar.png'">
                                            <div>
                                                <a href="?u=<?= htmlspecialchars($fuser['username']) ?>" class="font-semibold text-gray-900 hover:underline">
                                                    <?php
                                                    if ($fuser['is_doctor'] == 1) {
                                                        echo '<div class="inline-flex gap-[3px] items-center justify-center font-medium">';
                                                        // Doctor prefix
                                                        echo '<span class="text-green-700 font-bold">Dr </span>';
                                                        // Doctor name
                                                        echo '<span class="text-gray-900">' . htmlspecialchars($fuser['first_name'] . ' ' . $fuser['last_name']) . '</span>';
                                                        // Verified checkmark icon
                                                        echo ' ';
                                                        if (!empty($fuser['is_verified']) && $fuser['is_verified']) {
                                                            echo '<svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">';
                                                            echo '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>';
                                                            echo '</svg>';
                                                        }
                                                        echo '</div>';
                                                    } else {
                                                        echo htmlspecialchars($fuser['first_name'] . ' ' . $fuser['last_name']);
                                                    }
                                                    ?>
                                                </a>
                                                <p class="text-sm text-gray-500">@<?= htmlspecialchars($fuser['username']) ?></p>
                                            </div>
                                        </div>
                                        <div><?= $fbtn ?></div>
                                    </div>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    <!-- Add your chatbox modal & other modals here if needed -->
    <style>
        /* Custom animations */
        @keyframes fade-in {
            from {
                opacity: 0;
            }
            to {
                opacity: 1;
            }
        }
        @keyframes fade-in-up {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        @keyframes blob {
            0% {
                transform: translate(0px, 0px) scale(1);
            }
            33% {
                transform: translate(10px, -15px) scale(1.05);
            }
            66% {
                transform: translate(-5px, 5px) scale(0.95);
            }
            100% {
                transform: translate(0px, 0px) scale(1);
            }
        }
        @keyframes pulse-slow {
            0%,
            100% {
                opacity: 1;
            }
            50% {
                opacity: 0.7;
            }
        }
        @keyframes ping-slow {
            0% {
                transform: scale(1);
                opacity: 1;
            }
            75%,
            100% {
                transform: scale(1.5);
                opacity: 0;
            }
        }
        .animate-fade-in {
            animation: fade-in 0.6s ease-out forwards;
        }
        .animate-fade-in-up {
            animation: fade-in-up 0.6s ease-out forwards;
        }
        .animate-blob {
            animation: blob 7s infinite;
        }
        .animate-pulse-slow {
            animation: pulse-slow 3s infinite;
        }
        .animate-ping-slow {
            animation: ping-slow 3s cubic-bezier(0, 0, 0.2, 1) infinite;
        }
        .animation-delay-200 {
            animation-delay: 0.2s;
        }
        .animation-delay-300 {
            animation-delay: 0.3s;
        }
        .animation-delay-400 {
            animation-delay: 0.4s;
        }
        .animation-delay-500 {
            animation-delay: 0.5s;
        }
        .animation-delay-600 {
            animation-delay: 0.6s;
        }
        .animation-delay-700 {
            animation-delay: 0.7s;
        }
        .animation-delay-2000 {
            animation-delay: 2s;
        }
        
        /* Notification styles */
        .notification {
            position: fixed;
            bottom: 20px;
            right: 20px;
            padding: 16px 24px;
            border-radius: 8px;
            color: white;
            font-weight: 500;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            z-index: 1000;
            opacity: 0;
            transform: translateY(20px);
            transition: opacity 0.3s ease, transform 0.3s ease;
        }
        
        .notification.show {
            opacity: 1;
            transform: translateY(0);
        }
        
        .notification.success {
            background-color: #10B981;
        }
        
        .notification.error {
            background-color: #EF4444;
        }
        
        .notification.info {
            background-color: #3B82F6;
        }
    </style>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // Dropdown menu toggle logic
            const menuButton = document.getElementById('menu-button');
            const dropdownMenu = document.getElementById('dropdown-menu');
            
            if (menuButton && dropdownMenu) {
                menuButton.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    
                    const isExpanded = this.getAttribute('aria-expanded') === 'true';
                    
                    // Toggle dropdown visibility
                    dropdownMenu.classList.toggle('hidden');
                    
                    // Update ARIA attribute
                    this.setAttribute('aria-expanded', !isExpanded);
                    
                    // If showing the dropdown, add animation classes
                    if (!isExpanded) {
                        dropdownMenu.classList.remove('opacity-0', 'scale-95');
                        dropdownMenu.classList.add('opacity-100', 'scale-100');
                    } else {
                        dropdownMenu.classList.remove('opacity-100', 'scale-100');
                        dropdownMenu.classList.add('opacity-0', 'scale-95');
                    }
                });
                
                // Close dropdown when clicking outside
                document.addEventListener('click', function(e) {
                    if (!menuButton.contains(e.target) && !dropdownMenu.contains(e.target)) {
                        dropdownMenu.classList.add('hidden');
                        dropdownMenu.classList.remove('opacity-100', 'scale-100');
                        dropdownMenu.classList.add('opacity-0', 'scale-95');
                        menuButton.setAttribute('aria-expanded', 'false');
                    }
                });
            }
            
            // Back to top button
            const backToTopButton = document.getElementById('backToTop');
            window.addEventListener('scroll', () => {
                if (window.scrollY > 300) {
                    backToTopButton.classList.remove('opacity-0', 'invisible');
                    backToTopButton.classList.add('opacity-100', 'visible');
                } else {
                    backToTopButton.classList.add('opacity-0', 'invisible');
                    backToTopButton.classList.remove('opacity-100', 'visible');
                }
            });
            
            backToTopButton.addEventListener('click', () => {
                window.scrollTo({
                    top: 0,
                    behavior: 'smooth'
                });
            });
            
            // Animate posts on scroll - using CSS instead of GSAP
            const postsGrid = document.getElementById('posts-grid');
            if (postsGrid) {
                const posts = postsGrid.children;
                Array.from(posts).forEach((post, index) => {
                    // Set initial state
                    post.style.opacity = '0';
                    post.style.transform = 'translateY(20px)';
                    
                    // Use Intersection Observer to trigger animation when post is in view
                    const observer = new IntersectionObserver((entries) => {
                        entries.forEach(entry => {
                            if (entry.isIntersecting) {
                                setTimeout(() => {
                                    // Use CSS transitions instead of GSAP
                                    post.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
                                    post.style.opacity = '1';
                                    post.style.transform = 'translateY(0)';
                                }, index * 100); // Stagger the animations
                                observer.unobserve(post);
                            }
                        });
                    }, {
                        threshold: 0.1
                    });
                    observer.observe(post);
                });
            }
            
            // Like functionality
            document.querySelectorAll('[class^="like-btn-"]').forEach(button => {
                button.addEventListener('click', function() {
                    const postId = this.getAttribute('data-post-id');
                    const userLiked = this.getAttribute('data-user-liked') === 'true';
                    
                    // Send AJAX request
                    fetch('assets/php/actions.php', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/x-www-form-urlencoded',
                            },
                            body: `like_post=${postId}&user_liked=${userLiked ? '1' : '0'}`
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                // Update like count
                                const likeCountElements = document.querySelectorAll(`.like-count-${postId}, .modal-like-count-${postId}`);
                                likeCountElements.forEach(element => {
                                    element.textContent = data.like_count;
                                });
                                
                                // Update button state
                                if (userLiked) {
                                    // Unlike
                                    this.setAttribute('data-user-liked', 'false');
                                    this.classList.remove('text-red-500');
                                    this.classList.add('text-gray-500');
                                    this.querySelector('svg').setAttribute('fill', 'none');
                                } else {
                                    // Like
                                    this.setAttribute('data-user-liked', 'true');
                                    this.classList.remove('text-gray-500');
                                    this.classList.add('text-red-500');
                                    this.querySelector('svg').setAttribute('fill', 'currentColor');
                                    
                                    // Add a little animation using CSS
                                    this.style.transform = 'scale(1.2)';
                                    setTimeout(() => {
                                        this.style.transform = 'scale(1)';
                                    }, 200);
                                }
                            } else {
                                // Show error message
                                showNotification('Error: ' + data.message, 'error');
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            showNotification('An error occurred while processing your request.', 'error');
                        });
                });
            });
            
            // Comment functionality
            document.querySelectorAll('.add-comment').forEach(button => {
                button.addEventListener('click', function() {
                    const postId = this.getAttribute('data-post-id');
                    const commentSection = document.getElementById(this.getAttribute('data-cs'));
                    const input = commentSection.parentElement.querySelector('.comment-input');
                    const commentText = input.value.trim();
                    
                    if (commentText === '') {
                        return;
                    }
                    
                    // Send AJAX request
                    fetch('assets/php/actions.php', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/x-www-form-urlencoded',
                            },
                            body: `add_comment=${postId}&comment=${encodeURIComponent(commentText)}`
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                // Add new comment to the section
                                const newComment = document.createElement('div');
                                newComment.className = 'flex items-start gap-3 mb-4';
                                newComment.style.opacity = '0';
                                newComment.style.transform = 'translateY(20px)';
                                
                                newComment.innerHTML = `
                                    <img src="assets/img/profile/<?= htmlspecialchars($user['profile_pic']) ?>" alt="Your Pic" class="w-8 h-8 rounded-full object-cover"
                                         onerror="this.onerror=null; this.src='assets/img/profile/default-avatar.png'">
                                    <div class="flex-1">
                                        <div class="bg-green-100 rounded-lg p-3">
                                            <a href="?u=<?= htmlspecialchars($user['username']) ?>" class="font-semibold text-sm text-gray-900 hover:underline">
                                                <?php
                                                if ($user['is_doctor'] == 1) {
                                                    echo '<div class="inline-flex gap-[3px] items-center justify-center font-medium">';
                                                    // Doctor prefix
                                                    echo '<span class="text-green-700 font-bold">Dr </span>';
                                                    // Doctor name
                                                    echo '<span class="text-gray-900">' . htmlspecialchars($user['first_name']) . ' ' . htmlspecialchars($user['last_name']) . '</span>';
                                                    // Verified checkmark icon
                                                    echo ' ';
                                                    if (!empty($user['is_verified']) && $user['is_verified']) {
                                                        echo '<svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">';
                                                        echo '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>';
                                                        echo '</svg>';
                                                    }
                                                    echo '</div>';
                                                } else {
                                                    echo htmlspecialchars($user['first_name']) . ' ' . htmlspecialchars($user['last_name']);
                                                }
                                                ?>
                                            </a>
                                            <p class="text-sm text-gray-700 mt-1">${commentText.replace(/</g, '&lt;').replace(/>/g, '&gt;')}</p>
                                        </div>
                                        <p class="text-xs text-gray-500 mt-1">Just now</p>
                                    </div>
                                `;
                                
                                // If there's a "No comments" message, remove it
                                const noCommentsMsg = commentSection.querySelector('.text-center.text-gray-400');
                                if (noCommentsMsg) {
                                    noCommentsMsg.remove();
                                }
                                
                                // Add the new comment
                                commentSection.appendChild(newComment);
                                
                                // Animate in using CSS transitions
                                setTimeout(() => {
                                    newComment.style.transition = 'opacity 0.3s ease, transform 0.3s ease';
                                    newComment.style.opacity = '1';
                                    newComment.style.transform = 'translateY(0)';
                                }, 10);
                                
                                // Clear input
                                input.value = '';
                                
                                // Update comment count in the grid
                                const commentCounts = document.querySelectorAll(`[data-bs-target="#postview${postId}"] .flex.items-center.text-white.font-medium:last-child span`);
                                commentCounts.forEach(count => {
                                    const currentCount = parseInt(count.textContent);
                                    count.textContent = currentCount + 1;
                                });
                                
                                // Show success notification
                                showNotification('Comment posted successfully!', 'success');
                            } else {
                                // Show error message
                                showNotification('Error: ' + data.message, 'error');
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            showNotification('An error occurred while posting your comment.', 'error');
                        });
                });
            });
            
            // Follow/Unfollow functionality
            document.querySelectorAll('.followbtn, .unfollowbtn').forEach(button => {
                button.addEventListener('click', function() {
                    const userId = this.getAttribute('data-user-id');
                    const isFollowBtn = this.classList.contains('followbtn');
                    
                    // Send AJAX request
                    fetch('assets/php/actions.php', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/x-www-form-urlencoded',
                            },
                            body: `${isFollowBtn ? 'follow' : 'unfollow'}=${userId}`
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                // Update button state
                                if (isFollowBtn) {
                                    // Change to Unfollow button
                                    this.classList.remove('bg-gradient-to-r', 'from-green-600', 'to-teal-600', 'text-white', 'followbtn');
                                    this.classList.add('bg-white', 'border', 'border-green-300', 'text-green-700', 'unfollowbtn');
                                    this.textContent = 'Following';
                                } else {
                                    // Change to Follow button
                                    this.classList.remove('bg-white', 'border', 'border-green-300', 'text-green-700', 'unfollowbtn');
                                    this.classList.add('bg-gradient-to-r', 'from-green-600', 'to-teal-600', 'text-white', 'followbtn');
                                    this.textContent = 'Follow';
                                }
                                
                                // Update follower count
                                const followerCountElements = document.querySelectorAll('.flex.items-center.gap-1.cursor-pointer.group:nth-child(2) span.font-semibold');
                                followerCountElements.forEach(element => {
                                    const currentCount = parseInt(element.textContent);
                                    element.textContent = isFollowBtn ? currentCount + 1 : currentCount - 1;
                                });
                                
                                // Show notification
                                showNotification(data.message, 'success');
                            } else {
                                // Show error message
                                showNotification('Error: ' + data.message, 'error');
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            showNotification('An error occurred while processing your request.', 'error');
                        });
                });
            });
            
            // Unblock functionality
            document.querySelectorAll('.unblockbtn').forEach(button => {
                button.addEventListener('click', function() {
                    const userId = this.getAttribute('data-user-id');
                    
                    // Send AJAX request
                    fetch('assets/php/actions.php', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/x-www-form-urlencoded',
                            },
                            body: `unblock=${userId}`
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                // Change to Follow button
                                this.classList.remove('bg-gradient-to-r', 'from-red-500', 'to-red-600', 'text-white', 'unblockbtn');
                                this.classList.add('bg-gradient-to-r', 'from-green-600', 'to-teal-600', 'text-white', 'followbtn');
                                this.textContent = 'Follow';
                                
                                // Show notification
                                showNotification(data.message, 'success');
                            } else {
                                // Show error message
                                showNotification('Error: ' + data.message, 'error');
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            showNotification('An error occurred while processing your request.', 'error');
                        });
                });
            });
            
            // Notification function - using CSS instead of GSAP
            function showNotification(message, type = 'info') {
                // Create notification element
                const notification = document.createElement('div');
                notification.className = `notification ${type}`;
                notification.textContent = message;
                
                // Add to DOM
                document.body.appendChild(notification);
                
                // Trigger animation
                setTimeout(() => {
                    notification.classList.add('show');
                }, 10);
                
                // Remove after 3 seconds
                setTimeout(() => {
                    notification.classList.remove('show');
                    
                    // Remove from DOM after animation completes
                    setTimeout(() => {
                        notification.remove();
                    }, 300);
                }, 3000);
            }
        });
    </script>
</div>