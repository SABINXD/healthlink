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
<style>
    :root {
        --primary: #2c7a7b;
        --primary-light: #4fd1c5;
        --secondary: #4a5568;
        --light: #f7fafc;
        --dark: #2d3748;
        --danger: #e53e3e;
        --success: #38a169;
        --border: #e2e8f0;
        --shadow: rgba(0, 0, 0, 0.1);
        --profile-bg: #f0fff4;
        --profile-border: #bbf7d0;
    }
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }
    body {
        background-color: #f8fafc;
        color: var(--dark);
        line-height: 1.6;
    }
    .container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 20px;
    }
    /* Header Styles */
    header {
        background-color: white;
        box-shadow: 0 2px 10px var(--shadow);
        position: sticky;
        top: 0;
        z-index: 100;
    }
    .header-content {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 15px 0;
    }
    .logo {
        display: flex;
        align-items: center;
        gap: 10px;
        font-size: 24px;
        font-weight: 700;
        color: var(--primary);
        text-decoration: none;
    }
    .logo i {
        font-size: 28px;
        color: var(--primary-light);
    }
    nav ul {
        display: flex;
        list-style: none;
        gap: 25px;
    }
    nav a {
        text-decoration: none;
        color: var(--secondary);
        font-weight: 500;
        transition: color 0.3s;
    }
    nav a:hover {
        color: var(--primary);
    }
    .user-menu {
        display: flex;
        align-items: center;
        gap: 15px;
    }
    .user-avatar {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        overflow: hidden;
        border: 2px solid var(--primary-light);
    }
    .user-avatar img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    /* JavaScript-based Dropdown */
    .js-dropdown {
        position: relative;
        display: inline-block;
    }
    .js-dropdown .dropdown-toggle {
        cursor: pointer;
        margin-bottom: 0;
    }
    .js-dropdown .dropdown-menu {
        position: absolute;
        top: 100%;
        right: 0;
        background-color: white;
        min-width: 180px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        border-radius: 8px;
        z-index: 1001;
        padding: 8px 0;
        margin-top: 5px;
        border: 1px solid #e2e8f0;
        display: none;
    }
    .js-dropdown.show .dropdown-menu {
        display: block;
    }
    .js-dropdown .dropdown-menu .dropdown-item {
        display: block;
        padding: 10px 16px;
        color: #333;
        text-decoration: none;
        transition: background-color 0.2s;
        white-space: nowrap;
    }
    .js-dropdown .dropdown-menu .dropdown-item:hover {
        background-color: #f8f9fa;
        color: var(--primary);
    }
    .js-dropdown .dropdown-menu .dropdown-item i {
        margin-right: 8px;
        width: 16px;
        text-align: center;
    }
    /* For better mobile experience */
    @media (max-width: 768px) {
        .js-dropdown .dropdown-menu {
            right: -80px;
        }
    }
    @media (max-width: 480px) {
        .js-dropdown .dropdown-menu {
            right: 0;
            left: 0;
            min-width: auto;
        }
    }
    .btn {
        padding: 8px 16px;
        border-radius: 6px;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.3s;
        border: none;
        text-decoration: none;
        display: inline-block;
    }
    .btn-primary {
        background-color: var(--primary);
        color: white;
    }
    .btn-primary:hover {
        background-color: #2a6b6c;
    }
    .btn-outline {
        background-color: transparent;
        border: 1px solid var(--primary);
        color: var(--primary);
    }
    .btn-outline:hover {
        background-color: var(--primary);
        color: white;
    }
    .btn-sm {
        padding: 5px 10px;
        font-size: 14px;
    }
    /* Profile Header */
    .profile-header {
        background: linear-gradient(135deg, var(--profile-bg) 0%, #e6fffa 100%);
        padding: 40px 0;
        border-bottom: 1px solid var(--border);
    }
    .profile-info {
        display: flex;
        align-items: center;
        gap: 30px;
    }
    .profile-avatar {
        width: 150px;
        height: 150px;
        border-radius: 50%;
        overflow: hidden;
        border: 5px solid white;
        box-shadow: 0 5px 15px var(--shadow);
    }
    .profile-avatar img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    .profile-details h1 {
        font-size: 32px;
        margin-bottom: 10px;
        color: var(--dark);
    }
    .profile-meta {
        display: flex;
        flex-wrap: wrap;
        gap: 20px;
        margin-bottom: 15px;
    }
    .meta-item {
        display: flex;
        align-items: center;
        gap: 8px;
        color: var(--secondary);
    }
    .meta-item i {
        color: var(--primary-light);
    }
    .profile-actions {
        display: flex;
        gap: 15px;
        margin-top: 20px;
    }
    /* Profile Content */
    .profile-content {
        padding: 40px 0;
    }
    .profile-tabs {
        display: flex;
        border-bottom: 1px solid var(--border);
        margin-bottom: 30px;
    }
    .profile-tab {
        padding: 12px 20px;
        font-weight: 500;
        color: var(--secondary);
        cursor: pointer;
        position: relative;
        transition: color 0.3s;
    }
    .profile-tab.active {
        color: var(--primary);
    }
    .profile-tab.active::after {
        content: '';
        position: absolute;
        bottom: -1px;
        left: 0;
        width: 100%;
        height: 3px;
        background-color: var(--primary);
    }
    .tab-content {
        display: none;
    }
    .tab-content.active {
        display: block;
    }
    /* About Section */
    .about-section {
        background-color: white;
        border-radius: 10px;
        padding: 30px;
        box-shadow: 0 2px 10px var(--shadow);
        margin-bottom: 30px;
    }
    .about-section h2 {
        font-size: 24px;
        margin-bottom: 20px;
        color: var(--dark);
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .about-section h2 i {
        color: var(--primary-light);
    }
    .about-content {
        color: var(--secondary);
        line-height: 1.8;
    }
    /* Health Interests */
    .interests-section {
        background-color: white;
        border-radius: 10px;
        padding: 30px;
        box-shadow: 0 2px 10px var(--shadow);
        margin-bottom: 30px;
    }
    .interests-grid {
        display: flex;
        flex-wrap: wrap;
        gap: 15px;
        margin-top: 20px;
    }
    .interest-tag {
        background-color: var(--primary-light);
        color: white;
        padding: 8px 15px;
        border-radius: 30px;
        font-size: 14px;
        font-weight: 500;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    /* Posts Section */
    .posts-section {
        background-color: white;
        border-radius: 10px;
        padding: 30px;
        box-shadow: 0 2px 10px var(--shadow);
        margin-bottom: 30px;
    }
    .posts-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
        gap: 25px;
        margin-top: 20px;
    }
    .post-card {
        border: 1px solid var(--border);
        border-radius: 10px;
        overflow: hidden;
        transition: transform 0.3s, box-shadow 0.3s;
        cursor: pointer;
    }
    .post-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 5px 15px var(--shadow);
    }
    .post-image {
        height: 150px;
        overflow: hidden;
    }
    .post-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.3s;
    }
    .post-card:hover .post-image img {
        transform: scale(1.05);
    }
    .post-content {
        padding: 20px;
    }
    .post-category {
        display: inline-block;
        background-color: var(--primary-light);
        color: white;
        padding: 3px 10px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 500;
        margin-bottom: 10px;
    }
    .post-title {
        font-size: 18px;
        margin-bottom: 10px;
        color: var(--dark);
    }
    .post-excerpt {
        color: var(--secondary);
        font-size: 14px;
        margin-bottom: 15px;
    }
    .post-meta {
        display: flex;
        justify-content: space-between;
        color: var(--secondary);
        font-size: 12px;
    }
    .post-meta span {
        display: flex;
        align-items: center;
        gap: 5px;
    }
    /* Saved Professionals */
    .professionals-section {
        background-color: white;
        border-radius: 10px;
        padding: 30px;
        box-shadow: 0 2px 10px var(--shadow);
        margin-bottom: 30px;
    }
    .professionals-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
        gap: 25px;
        margin-top: 20px;
    }
    .professional-card {
        border: 1px solid var(--border);
        border-radius: 10px;
        padding: 20px;
        text-align: center;
        transition: transform 0.3s;
    }
    .professional-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 5px 15px var(--shadow);
    }
    .professional-avatar {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        overflow: hidden;
        margin: 0 auto 15px;
        border: 3px solid var(--primary-light);
    }
    .professional-avatar img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    .professional-card h3 {
        font-size: 16px;
        margin-bottom: 5px;
        color: var(--dark);
    }
    .professional-specialty {
        color: var(--primary);
        font-weight: 500;
        margin-bottom: 10px;
        font-size: 14px;
    }
    .professional-rating {
        display: flex;
        justify-content: center;
        gap: 3px;
        margin-bottom: 15px;
    }
    .professional-rating i {
        color: #f59e0b;
        font-size: 12px;
    }
    /* Footer */
    footer {
        background-color: var(--dark);
        color: white;
        padding: 40px 0 20px;
        margin-top: 50px;
    }
    .footer-content {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 30px;
        margin-bottom: 30px;
    }
    .footer-column h4 {
        font-size: 18px;
        margin-bottom: 20px;
        position: relative;
        padding-bottom: 10px;
    }
    .footer-column h4::after {
        content: '';
        position: absolute;
        left: 0;
        bottom: 0;
        width: 50px;
        height: 2px;
        background-color: var(--primary-light);
    }
    .footer-column ul {
        list-style: none;
    }
    .footer-column ul li {
        margin-bottom: 10px;
    }
    .footer-column a {
        color: #cbd5e0;
        text-decoration: none;
        transition: color 0.3s;
    }
    .footer-column a:hover {
        color: var(--primary-light);
    }
    .copyright {
        text-align: center;
        padding-top: 20px;
        border-top: 1px solid #4a5568;
        color: #a0aec0;
        font-size: 14px;
    }
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
    /* Modal styles */
    .modal-overlay {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: rgba(0, 0, 0, 0.5);
        z-index: 1000;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .modal-overlay.hidden {
        display: none;
    }
    /* Responsive */
    @media (max-width: 768px) {
        .header-content {
            flex-direction: column;
            gap: 15px;
        }
        nav ul {
            flex-wrap: wrap;
            justify-content: center;
        }
        .profile-info {
            flex-direction: column;
            text-align: center;
        }
        .profile-meta {
            justify-content: center;
        }
        .profile-actions {
            justify-content: center;
        }
    }
    @media (max-width: 576px) {
        .posts-grid,
        .professionals-grid {
            grid-template-columns: 1fr;
        }
        .form-actions {
            flex-direction: column;
        }
    }
</style>
<!-- Profile Header -->
<section class="profile-header">
    <div class="container">
        <div class="profile-info">
            <div class="profile-avatar">
                <img src="assets/img/profile/<?= htmlspecialchars($profile['profile_pic']) ?>" alt="User Profile">
            </div>
            <div class="profile-details">
                <h1>
                    <?php if ($profile['is_doctor'] == 1): ?>
                        <span class="text-green-700">Dr. </span>
                    <?php endif; ?>
                    <?= htmlspecialchars($profile['first_name']) ?> <?= htmlspecialchars($profile['last_name']) ?>
                    <?php if ($profile['is_doctor'] == 1 && !empty($profile['is_verified']) && $profile['is_verified']): ?>
                        <svg class="w-5 h-5 inline-block ml-1 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    <?php endif; ?>
                </h1>
                <div class="profile-meta">
                    <div class="meta-item">
                        <i class="fas fa-user"></i>
                        <span>@<?= htmlspecialchars($profile['username']) ?></span>
                    </div>
                    <div class="meta-item">
                        <i class="fas fa-envelope"></i>
                        <span><?= htmlspecialchars($profile['email']) ?></span>
                    </div>
                    <div class="meta-item">
                        <i class="fas fa-calendar-alt"></i>
                        <span><?= date('F Y', strtotime($profile['created_at'])) ?></span>
                    </div>
                    <div class="meta-item">
                        <i class="fas fa-map-marker-alt"></i>
                        <span><?= htmlspecialchars($profile['address']) ?></span>
                    </div>
                    <div class="meta-item">
                        <i class="fas fa-comments"></i>
                        <span><?= count($filtered_posts) ?> posts</span>
                    </div>
                    <div class="meta-item cursor-pointer" data-bs-toggle="modal" data-bs-target="#follower_list">
                        <i class="fas fa-users"></i>
                        <span><?= count($profile['followers']) ?> followers</span>
                    </div>
                    <div class="meta-item cursor-pointer" data-bs-toggle="modal" data-bs-target="#following_list">
                        <i class="fas fa-user-plus"></i>
                        <span><?= count($profile['following']) ?> following</span>
                    </div>
                </div>
                <div class="profile-actions">
                    <?php if ($user['id'] == $profile['id']): ?>
                        <button class="btn btn-primary" onclick="switchTab('about')">
                            <i class="fas fa-edit"></i> Edit Profile
                        </button>
                        <?php if ($profile['is_doctor'] == 0): ?>
                            <a href="?verifydoctor" class="btn btn-outline">
                                <i class="fas fa-check-circle"></i> Verify as Doctor
                            </a>
                        <?php endif; ?>
                    <?php else: ?>
                        <?php if (checkBlockStatus($user['id'], $profile['id'])): ?>
                            <button class="btn btn-outline unblockbtn" data-user-id="<?= htmlspecialchars($profile['id']) ?>">
                                <i class="fas fa-unlock"></i> Unblock
                            </button>
                        <?php elseif (checkBS($profile['id'])): ?>
                            <div class="btn btn-outline" style="opacity: 0.7; cursor: not-allowed;">
                                <i class="fas fa-lock"></i> Private Account
                            </div>
                        <?php else: ?>
                            <?php if (checkFollowed($profile['id'])): ?>
                                <button class="btn btn-outline unfollowbtn" data-user-id="<?= htmlspecialchars($profile['id']) ?>">
                                    <i class="fas fa-user-check"></i> Following
                                </button>
                            <?php else: ?>
                                <button class="btn btn-primary followbtn" data-user-id="<?= htmlspecialchars($profile['id']) ?>">
                                    <i class="fas fa-user-plus"></i> Follow
                                </button>
                            <?php endif; ?>
                            <?php if ($profile['is_doctor'] == 1 && $user['is_doctor'] != 1): ?>
                                <a href="?bookappointment=<?= htmlspecialchars($profile['id']) ?>" class="btn btn-outline">
                                    <i class="fas fa-calendar-check"></i> Book Appointment
                                </a>
                            <?php endif; ?>
                            <!-- JavaScript-based Dropdown -->
                            <div class="js-dropdown">
                                <button class="btn btn-outline dropdown-toggle" onclick="toggleDropdown(event, this)">
                                    <i class="fas fa-ellipsis-h"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li>
                                        <a href="#" data-bs-toggle="modal" data-bs-target="#chatbox" onclick="popchat(<?= htmlspecialchars($profile['id']) ?>); return false;" class="dropdown-item">
                                            <i class="fas fa-comment me-2"></i> Message
                                        </a>
                                    </li>
                                    <?php if ($profile['is_doctor'] == 1 && $user['is_doctor'] != 1): ?>
                                        <li>
                                            <a href="?bookappointment=<?= htmlspecialchars($profile['id']) ?>" class="dropdown-item">
                                                <i class="fas fa-calendar-check me-2"></i> Book Appointment
                                            </a>
                                        </li>
                                    <?php endif; ?>
                                    <li>
                                        <a href="assets/php/actions.php?block=<?= htmlspecialchars($profile['id']) ?>&username=<?= htmlspecialchars($profile['username']) ?>" class="dropdown-item">
                                            <i class="fas fa-ban me-2"></i> Block
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- Profile Content -->
<section class="profile-content">
    <div class="container">
        <div class="profile-tabs">
            <div class="profile-tab active" onclick="switchTab('about')">About</div>
            <div class="profile-tab" onclick="switchTab('interests')">Health Interests</div>
            <div class="profile-tab" onclick="switchTab('posts')">My Posts</div>
            <div class="profile-tab" onclick="switchTab('professionals')">Saved Professionals</div>
        </div>
        <!-- About Tab -->
        <div id="about-tab" class="tab-content active">
            <div class="about-section">
                <h2><i class="fas fa-user"></i> About Me</h2>
                <div class="about-content">
                    <?php if (!empty($profile['bio'])): ?>
                        <p><?= htmlspecialchars($profile['bio']) ?></p>
                    <?php else: ?>
                        <p>No bio information available.</p>
                    <?php endif; ?>
                </div>
            </div>
            <div class="interests-section">
                <h2><i class="fas fa-heart"></i> Health Interests</h2>
                <div class="interests-grid">
                    <div class="interest-tag">
                        <i class="fas fa-brain"></i>
                        <span>Mental Health</span>
                    </div>
                    <div class="interest-tag">
                        <i class="fas fa-apple-alt"></i>
                        <span>Nutrition</span>
                    </div>
                    <div class="interest-tag">
                        <i class="fas fa-dumbbell"></i>
                        <span>Fitness</span>
                    </div>
                    <div class="interest-tag">
                        <i class="fas fa-allergies"></i>
                        <span>Skin Conditions</span>
                    </div>
                    <div class="interest-tag">
                        <i class="fas fa-leaf"></i>
                        <span>Natural Remedies</span>
                    </div>
                    <div class="interest-tag">
                        <i class="fas fa-spa"></i>
                        <span>Wellness</span>
                    </div>
                </div>
                <?php if ($user['id'] == $profile['id']): ?>
                    <div style="margin-top: 30px;">
                        <button class="btn btn-primary">
                            <i class="fas fa-edit"></i> Edit Interests
                        </button>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        <!-- Health Interests Tab -->
        <div id="interests-tab" class="tab-content">
            <div class="interests-section">
                <h2><i class="fas fa-heart"></i> My Health Interests</h2>
                <p style="margin-bottom: 20px; color: var(--secondary);">These topics help us personalize your experience and connect you with relevant discussions.</p>
                <div class="interests-grid">
                    <div class="interest-tag">
                        <i class="fas fa-brain"></i>
                        <span>Mental Health</span>
                    </div>
                    <div class="interest-tag">
                        <i class="fas fa-apple-alt"></i>
                        <span>Nutrition</span>
                    </div>
                    <div class="interest-tag">
                        <i class="fas fa-dumbbell"></i>
                        <span>Fitness</span>
                    </div>
                    <div class="interest-tag">
                        <i class="fas fa-allergies"></i>
                        <span>Skin Conditions</span>
                    </div>
                    <div class="interest-tag">
                        <i class="fas fa-leaf"></i>
                        <span>Natural Remedies</span>
                    </div>
                    <div class="interest-tag">
                        <i class="fas fa-spa"></i>
                        <span>Wellness</span>
                    </div>
                </div>
                <?php if ($user['id'] == $profile['id']): ?>
                    <div style="margin-top: 30px;">
                        <button class="btn btn-primary">
                            <i class="fas fa-edit"></i> Edit Interests
                        </button>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        <!-- My Posts Tab -->
        <div id="posts-tab" class="tab-content">
            <div class="posts-section">
                <h2><i class="fas fa-comments"></i> My Posts</h2>
                <?php
                // Check if account is private and user is not following
                if (checkBS($profile['id'])) {
                    $filtered_posts = [];
                ?>
                    <div class="bg-white rounded-xl shadow-sm p-8 text-center mt-6">
                        <div class="flex justify-center mb-4">
                            <i class="fas fa-lock fa-3x text-gray-400"></i>
                        </div>
                        <h3 class="text-lg font-medium text-gray-900 mb-1">This account is private</h3>
                        <p class="text-gray-500">Follow this account to see their posts.</p>
                    </div>
                <?php
                } else if (count($filtered_posts) < 1 && $user['id'] == $profile['id']) {
                ?>
                    <div class="bg-white rounded-xl shadow-sm p-8 text-center mt-6">
                        <div class="flex justify-center mb-4">
                            <i class="fas fa-images fa-3x text-gray-400"></i>
                        </div>
                        <h3 class="text-lg font-medium text-gray-900 mb-1">No Posts Yet</h3>
                        <p class="text-gray-500 mb-4">Share your first post to get started.</p>
                        <a href="?addpost" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Create Post
                        </a>
                    </div>
                <?php
                } else if (count($filtered_posts) < 1) {
                ?>
                    <div class="bg-white rounded-xl shadow-sm p-8 text-center mt-6">
                        <div class="flex justify-center mb-4">
                            <i class="fas fa-images fa-3x text-gray-400"></i>
                        </div>
                        <h3 class="text-lg font-medium text-gray-900 mb-1">No Posts Yet</h3>
                        <p class="text-gray-500">When they post, you'll see their content here.</p>
                    </div>
                <?php
                } else {
                ?>
                    <div class="posts-grid">
                        <?php
                        foreach ($filtered_posts as $post) {
                            $likes = getLikesCount($post['id']);
                            $comments = getComments($post['id']);
                            // Check if post is anonymous and current user is not the owner
                            $is_anonymous = ($post['post_privacy'] == 1 && $user['id'] != $profile['id']);
                            // Prepare image source
                            $img_src = '';
                            if (!empty($post['post_img'])) {
                                if (strpos($post['post_img'], 'http') === 0) {
                                    $img_src = $post['post_img'];
                                } else if (strpos($post['post_img'], 'web/assets/img/posts/') === 0) {
                                    $img_src = substr($post['post_img'], strpos($post['post_img'], 'assets/img/posts/'));
                                } else {
                                    $img_src = 'assets/img/posts/' . $post['post_img'];
                                }
                            }
                        ?>
                            <div class="post-card" onclick="openPostModal(<?= (int)$post['id'] ?>)">
                                <?php if (!empty($img_src)): ?>
                                    <div class="post-image">
                                        <img src="<?= htmlspecialchars($img_src) ?>" alt="Post Image">
                                    </div>
                                <?php endif; ?>
                                <div class="post-content">
                                    <div class="post-category">
                                        <?php if ($is_anonymous): ?>
                                            Anonymous
                                        <?php else: ?>
                                            <?= htmlspecialchars($profile['first_name']) ?>
                                        <?php endif; ?>
                                    </div>
                                    <h3 class="post-title"><?= htmlspecialchars(substr($post['post_title'], 0, 50)) ?>...</h3>
                                    <p class="post-excerpt"><?= htmlspecialchars(substr($post['post_desc'], 0, 100)) ?>...</p>
                                    <div class="post-meta">
                                        <span><i class="far fa-heart"></i> <?= count($likes) ?></span>
                                        <span><i class="far fa-comment"></i> <?= count($comments) ?></span>
                                        <span><i class="far fa-clock"></i> <?= show_time($post['created_at']) ?></span>
                                    </div>
                                </div>
                            </div>
                            <!-- Post Modal -->
                            <div id="post-modal-<?= (int)$post['id'] ?>" class="modal-overlay hidden">
                                <div class="flex items-center justify-center min-h-screen p-4">
                                    <div class="bg-white rounded-xl shadow-xl max-w-4xl w-full max-h-[90vh] overflow-hidden">
                                        <div class="flex justify-between items-center p-6 border-b border-gray-200">
                                            <h2 class="text-2xl font-bold text-gray-900">Post Details</h2>
                                            <button onclick="closePostModal(<?= (int)$post['id'] ?>)" class="text-gray-500 hover:text-gray-900">
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
                                                                    if ($profile['is_doctor'] == 1) {
                                                                        echo 'Dr ' . htmlspecialchars($profile['first_name'] . ' ' . $profile['last_name']);
                                                                    } else {
                                                                        echo htmlspecialchars($profile['first_name'] . ' ' . $profile['last_name']);
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
                        <?php
                        }
                        ?>
                    </div>
                <?php
                }
                ?>
            </div>
        </div>
        <!-- Saved Professionals Tab -->
        <div id="professionals-tab" class="tab-content">
            <div class="professionals-section">
                <h2><i class="fas fa-user-md"></i> Saved Professionals</h2>
                <p style="margin-bottom: 20px; color: var(--secondary);">Healthcare professionals you've saved for future reference or consultation.</p>
                <div class="professionals-grid">
                    <div class="professional-card">
                        <div class="professional-avatar">
                            <img src="https://picsum.photos/seed/prof1/200/200.jpg" alt="Dr. Sarah Johnson">
                        </div>
                        <h3>Dr. Sarah Johnson</h3>
                        <div class="professional-specialty">Dermatology</div>
                        <div class="professional-rating">
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                        </div>
                        <a href="#" class="btn btn-primary btn-sm">View Profile</a>
                        <a href="#" class="btn btn-outline btn-sm">Remove</a>
                    </div>
                    <div class="professional-card">
                        <div class="professional-avatar">
                            <img src="https://picsum.photos/seed/prof2/200/200.jpg" alt="Dr. Michael Chen">
                        </div>
                        <h3>Dr. Michael Chen</h3>
                        <div class="professional-specialty">Cardiology</div>
                        <div class="professional-rating">
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                        </div>
                        <a href="#" class="btn btn-primary btn-sm">View Profile</a>
                        <a href="#" class="btn btn-outline btn-sm">Remove</a>
                    </div>
                    <div class="professional-card">
                        <div class="professional-avatar">
                            <img src="https://picsum.photos/seed/prof3/200/200.jpg" alt="Dr. Emily Rodriguez">
                        </div>
                        <h3>Dr. Emily Rodriguez</h3>
                        <div class="professional-specialty">Mental Health</div>
                        <div class="professional-rating">
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="far fa-star"></i>
                        </div>
                        <a href="#" class="btn btn-primary btn-sm">View Profile</a>
                        <a href="#" class="btn btn-outline btn-sm">Remove</a>
                    </div>
                    <div class="professional-card">
                        <div class="professional-avatar">
                            <img src="https://picsum.photos/seed/prof4/200/200.jpg" alt="Dr. James Wilson">
                        </div>
                        <h3>Dr. James Wilson</h3>
                        <div class="professional-specialty">Neurology</div>
                        <div class="professional-rating">
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                        </div>
                        <a href="#" class="btn btn-primary btn-sm">View Profile</a>
                        <a href="#" class="btn btn-outline btn-sm">Remove</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- Follower List Modal -->
<div class="modal fade" id="follower_list" tabindex="-1" aria-labelledby="followerListLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content rounded-xl overflow-hidden shadow-2xl">
            <div class="modal-header flex justify-between items-center p-4 border-b border-green-200">
                <h5 class="text-xl font-bold text-gray-900">Followers</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body max-h-96 overflow-y-auto p-0">
                <?php if (count($profile['followers']) < 1): ?>
                    <div class="p-8 text-center">
                        <i class="fas fa-users fa-3x text-gray-400 mb-4"></i>
                        <h3 class="mt-4 text-lg font-medium text-gray-900">No followers yet</h3>
                        <p class="mt-1 text-gray-500">When someone follows this account, they'll appear here.</p>
                    </div>
                <?php else: ?>
                    <ul class="divide-y divide-green-200">
                        <?php foreach ($profile['followers'] as $f):
                            $fuser = getUser($f['follower_id']);
                            $fbtn = "";
                            if (checkFollowed($f['follower_id'])) {
                                $fbtn = '<button class="btn btn-sm btn-outline unfollowbtn" data-user-id="' . htmlspecialchars($fuser['id']) . '">Following</button>';
                            } else if ($user['id'] == $f['follower_id']) {
                                $fbtn = "";
                            } else {
                                $fbtn = '<button class="btn btn-sm btn-primary followbtn" data-user-id="' . htmlspecialchars($fuser['id']) . '">Follow</button>';
                            }
                        ?>
                            <li class="p-4 hover:bg-green-50 transition-colors duration-200">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-3">
                                        <img src="assets/img/profile/<?= htmlspecialchars($fuser['profile_pic']) ?>" alt="Follower Pic" class="w-12 h-12 rounded-full object-cover">
                                        <div>
                                            <a href="?u=<?= htmlspecialchars($fuser['username']) ?>" class="font-semibold text-gray-900 hover:underline">
                                                <?php
                                                if ($fuser['is_doctor'] == 1) {
                                                    echo '<span class="text-green-700 font-bold">Dr </span>';
                                                }
                                                echo htmlspecialchars($fuser['first_name'] . ' ' . $fuser['last_name']);
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
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body max-h-96 overflow-y-auto p-0">
                <?php if (count($profile['following']) < 1): ?>
                    <div class="p-8 text-center">
                        <i class="fas fa-user-plus fa-3x text-gray-400 mb-4"></i>
                        <h3 class="mt-4 text-lg font-medium text-gray-900">Not following anyone</h3>
                        <p class="mt-1 text-gray-500">When they follow someone, they'll appear here.</p>
                    </div>
                <?php else: ?>
                    <ul class="divide-y divide-green-200">
                        <?php foreach ($profile['following'] as $f):
                            $fuser = getUser($f['user_id']);
                            $fbtn = "";
                            if (checkFollowed($f['user_id'])) {
                                $fbtn = '<button class="btn btn-sm btn-outline unfollowbtn" data-user-id="' . htmlspecialchars($fuser['id']) . '">Following</button>';
                            } else if ($user['id'] == $f['user_id']) {
                                $fbtn = "";
                            } else {
                                $fbtn = '<button class="btn btn-sm btn-primary followbtn" data-user-id="' . htmlspecialchars($fuser['id']) . '">Follow</button>';
                            }
                        ?>
                            <li class="p-4 hover:bg-green-50 transition-colors duration-200">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-3">
                                        <img src="assets/img/profile/<?= htmlspecialchars($fuser['profile_pic']) ?>" alt="Following Pic" class="w-12 h-12 rounded-full object-cover">
                                        <div>
                                            <a href="?u=<?= htmlspecialchars($fuser['username']) ?>" class="font-semibold text-gray-900 hover:underline">
                                                <?php
                                                if ($fuser['is_doctor'] == 1) {
                                                    echo '<span class="text-green-700 font-bold">Dr </span>';
                                                }
                                                echo htmlspecialchars($fuser['first_name'] . ' ' . $fuser['last_name']);
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
<!-- Chatbox Modal -->
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
<script>
    // Switch between profile tabs
    function switchTab(tabName) {
        // Remove active class from all tabs and content
        document.querySelectorAll('.profile-tab').forEach(tab => {
            tab.classList.remove('active');
        });
        document.querySelectorAll('.tab-content').forEach(content => {
            content.classList.remove('active');
        });
        // Add active class to selected tab and content
        event.target.classList.add('active');
        document.getElementById(`${tabName}-tab`).classList.add('active');
    }
    
    // Handle remove professional button
    document.querySelectorAll('.professional-card .btn-outline').forEach(button => {
        button.addEventListener('click', function(e) {
            e.stopPropagation();
            if (confirm('Are you sure you want to remove this professional from your saved list?')) {
                // Remove the professional card
                this.closest('.professional-card').remove();
                // Check if there are any professionals left
                const remainingProfessionals = document.querySelectorAll('.professional-card');
                if (remainingProfessionals.length === 0) {
                    document.querySelector('.professionals-section p').textContent = "You haven't saved any healthcare professionals yet.";
                }
                showNotification('Professional removed from your list', 'success');
            }
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
                            this.classList.remove('btn-primary', 'followbtn');
                            this.classList.add('btn-outline', 'unfollowbtn');
                            this.innerHTML = '<i class="fas fa-user-check"></i> Following';
                        } else {
                            // Change to Follow button
                            this.classList.remove('btn-outline', 'unfollowbtn');
                            this.classList.add('btn-primary', 'followbtn');
                            this.innerHTML = '<i class="fas fa-user-plus"></i> Follow';
                        }
                        // Update follower count
                        const followerCountElements = document.querySelectorAll('.meta-item:nth-child(6) span');
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
                        this.classList.remove('btn-outline', 'unblockbtn');
                        this.classList.add('btn-primary', 'followbtn');
                        this.innerHTML = '<i class="fas fa-user-plus"></i> Follow';
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
                        newComment.className = 'flex items-center gap-3 mb-3';
                        newComment.style.opacity = '0';
                        newComment.innerHTML = `
                            <img src="assets/img/profile/<?= htmlspecialchars($user['profile_pic']) ?>" alt="Your Pic" class="w-10 h-10 rounded-full border border-green-300 object-cover">
                            <div>
                                <p class="text-sm font-semibold mb-0">
                                    <a href="?u=<?= htmlspecialchars($user['username']) ?>" class="text-gray-700 hover:underline">
                                        <?php
                                        if ($user['is_doctor'] == 1) {
                                            echo '<span class="text-green-700 font-bold">Dr </span>';
                                        }
                                        echo htmlspecialchars($user['first_name']) . ' ' . htmlspecialchars($user['last_name']);
                                        ?>
                                    </a> - ${commentText.replace(/</g, '&lt;').replace(/>/g, '&gt;')}
                                </p>
                                <p class="text-xs text-gray-400 mb-0">Just now</p>
                            </div>
                        `;
                        // If there's a "No comments" message, remove it
                        const noCommentsMsg = commentSection.querySelector('.text-center.text-gray-400');
                        if (noCommentsMsg) {
                            noCommentsMsg.remove();
                        }
                        // Add the new comment
                        commentSection.appendChild(newComment);
                        // Animate in
                        setTimeout(() => {
                            newComment.style.transition = 'opacity 0.3s ease';
                            newComment.style.opacity = '1';
                        }, 10);
                        // Clear input
                        input.value = '';
                        // Update comment count in the grid
                        const commentCounts = document.querySelectorAll(`[data-post-id="${postId}"] .post-meta span:nth-child(2)`);
                        commentCounts.forEach(count => {
                            const currentCount = parseInt(count.textContent.trim());
                            count.innerHTML = `<i class="far fa-comment"></i> ${currentCount + 1}`;
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
    
    // Handle message form submission
    document.getElementById('messageForm')?.addEventListener('submit', function(e) {
        e.preventDefault();
        const message = document.getElementById('message').value.trim();
        if (message === '') {
            showNotification('Please enter a message', 'error');
            return;
        }
        // In a real application, you would send this data to a server
        // For this demo, we'll just show a success message
        showNotification('Message sent successfully!', 'success');
        document.getElementById('message').value = '';
        // Close the modal
        const modal = bootstrap.Modal.getInstance(document.getElementById('chatbox'));
        modal.hide();
    });
    
    // Function to open chatbox with a specific user
    function popchat(userId) {
        // In a real application, you would set the recipient ID here
        // For this demo, we'll just open the modal
        const modal = new bootstrap.Modal(document.getElementById('chatbox'));
        modal.show();
    }
    
    // Function to open post modal
    function openPostModal(id) {
        const modal = document.getElementById(`post-modal-${id}`);
        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }
    
    // Function to close post modal
    function closePostModal(id) {
        const modal = document.getElementById(`post-modal-${id}`);
        modal.classList.add('hidden');
        document.body.style.overflow = 'auto';
    }
    
    // Notification function
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
    
    // Toggle dropdown function
    function toggleDropdown(event, button) {
        event.stopPropagation();
        const dropdown = button.closest('.js-dropdown');
        dropdown.classList.toggle('show');
    }
    
    // Close dropdowns when clicking outside
    document.addEventListener('click', function(event) {
        const dropdowns = document.querySelectorAll('.js-dropdown');
        dropdowns.forEach(dropdown => {
            if (!dropdown.contains(event.target)) {
                dropdown.classList.remove('show');
            }
        });
    });
    
    // Fix for Bootstrap modal conflicts
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize Bootstrap modals
        const modals = document.querySelectorAll('.modal');
        modals.forEach(modal => {
            new bootstrap.Modal(modal);
        });
        
        // Fix dropdown toggle when modal is opened
        document.querySelectorAll('[data-bs-toggle="modal"]').forEach(trigger => {
            trigger.addEventListener('click', function() {
                const dropdown = this.closest('.js-dropdown');
                if (dropdown) {
                    dropdown.classList.remove('show');
                }
            });
        });
    });
</script>