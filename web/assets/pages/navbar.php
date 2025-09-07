<?php global $user; ?>
<nav class="fixed top-0 left-0 right-0 z-50 w-full backdrop-blur-lg bg-white/80 border-b border-green-100 shadow-sm transition-all duration-300">
    <!-- Animated Background Elements for Navbar -->
    <div class="absolute inset-0 overflow-hidden pointer-events-none">
        <div class="absolute -top-10 -right-10 w-32 h-32 bg-green-200 rounded-full mix-blend-multiply filter blur-xl opacity-20 animate-blob"></div>
        <div class="absolute -bottom-10 -left-10 w-24 h-24 bg-teal-200 rounded-full mix-blend-multiply filter blur-xl opacity-20 animate-blob animation-delay-2000"></div>
    </div>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        <div class="flex items-center justify-between h-16">
            <!-- Logo with animation -->
            <div class="flex-shrink-0 transform transition-transform duration-500 hover:scale-110">
                <a href="?" class="flex items-center space-x-2">
                    <div class="relative">
                        <img src="assets/img/logo.png" alt="healthlink" class="h-9 w-auto">
                        <div class="absolute inset-0 bg-gradient-to-r from-green-500 to-teal-600 rounded-full opacity-0 hover:opacity-20 transition-opacity duration-300"></div>
                    </div>
                </a>
            </div>
            <!-- Enhanced Search Bar -->
            <div class="flex-1 max-w-lg mx-4">
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <input id="search" type="search" placeholder="Search for health professionals..."
                        class="block w-full pl-10 pr-3 py-2 border border-green-200 rounded-full bg-white/70 backdrop-blur-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all duration-300">
                    <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                        <button id="voice-search" class="text-gray-400 hover:text-green-600 focus:outline-none transition-colors duration-200">
                            <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M7 4a3 3 0 016 0v4a3 3 0 11-6 0V4zm4 10.93A7.001 7.001 0 0017 8a1 1 0 10-2 0A5 5 0 015 8a1 1 0 00-2 0 7.001 7.001 0 006 6.93V17H6a1 1 0 100 2h8a1 1 0 100-2h-3v-2.07z" clip-rule="evenodd" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
            <!-- Emergency Button -->
            <div class="bg-gradient-to-r from-red-500 to-red-600 rounded-2xl shadow-lg p-1 emergency-card transform transition-all duration-300 hover:shadow-xl hover:-translate-y-0.5">
                <a href="?emergencyhelp" class="w-full flex items-center justify-center text-white font-bold py-2 px-3 rounded-xl hover:from-red-600 hover:to-red-700 transition-all duration-300">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-alert-triangle mr-2">
                        <path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z" />
                        <line x1="12" x2="12" y1="9" y2="13" />
                        <line x1="12" x2="12.01" y1="17" y2="17" />
                    </svg>
                    Emergency
                </a>
            </div>
            <!-- Navigation Items -->
            <div class="flex items-center space-x-1 md:space-x-2 ml-2">
                <!-- Home Button -->
                <a href="?" class="p-2 rounded-full text-gray-600 hover:bg-green-50 hover:text-green-600 transition-all duration-200 group">
                    <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                    <span class="sr-only">Home</span>
                </a>
                <!-- Create Button -->
                <button data-bs-toggle="modal" data-bs-target="#codeOptionModal" class="p-2 rounded-full text-gray-600 hover:bg-green-50 hover:text-green-600 transition-all duration-200 group relative">
                    <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                </button>
                <!-- Notifications -->
                <div class="relative">
                    <button id="show_not" data-bs-toggle="offcanvas" href="#notification_sidebar" role="button" aria-controls="offcanvasExample"
                        class="p-2 rounded-full text-gray-600 hover:bg-green-50 hover:text-green-600 transition-all duration-200 group">
                        <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                        </svg>
                        <span class="sr-only">Notifications</span>
                        <?php if (getUnreadNotificationsCount() > 0) { ?>
                            <span class="absolute top-0 right-0 flex items-center justify-center w-5 h-5 text-xs font-bold text-white bg-gradient-to-r from-green-500 to-teal-600 rounded-full animate-pulse">
                                <?= getUnreadNotificationsCount() ?>
                            </span>
                        <?php } ?>
                    </button>
                </div>
                <!-- Messages -->
                <div class="relative">
                    <button data-bs-toggle="offcanvas" href="#messages_sidebar"
                        class="p-2 rounded-full text-gray-600 hover:bg-green-50 hover:text-green-600 transition-all duration-200 group">
                        <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                        </svg>
                        <span class="sr-only">Messages</span>
                        <span id="msgcounter" class="absolute top-0 right-0 flex items-center justify-center w-5 h-5 text-xs font-bold text-white bg-gradient-to-r from-green-500 to-teal-600 rounded-full animate-pulse hidden">
                            <!-- This will be updated by JS -->
                        </span>
                    </button>
                </div>
                <!-- User Profile Dropdown -->
                <div class="relative ml-2">
                    <button id="user-menu-button" type="button" class="flex items-center justify-center w-10 h-10 rounded-full focus:outline-none focus:ring-2 focus:ring-green-500 transition-all duration-200"
                        aria-expanded="false" aria-haspopup="true">
                        <span class="sr-only">Open user menu</span>
                        <img class="h-9 w-9 rounded-full object-cover border-2 border-white shadow-md ring-2 ring-green-500 ring-opacity-30"
                            src="assets/img/profile/<?= !empty($user['profile_pic']) ? $user['profile_pic'] : 'default-avatar.png' ?>"
                            alt="<?= !empty($user['name']) ? $user['name'] : 'User' ?>"
                            onerror="this.src='assets/img/profile/default-avatar.png'">
                    </button>
                    <!-- Dropdown Menu -->
                    <div id="user-menu" class="origin-top-right absolute right-0 mt-2 w-56 rounded-xl shadow-lg bg-white/80 backdrop-blur-sm ring-1 ring-black ring-opacity-5 focus:outline-none transition-all duration-300"
                        role="menu" aria-orientation="vertical" aria-labelledby="user-menu-button">
                        <div class="py-1">
                            <div class="px-4 py-3 border-b border-green-100">
                                <p class="text-sm font-medium text-gray-900"><?= !empty($user['name']) ? $user['name'] : 'User' ?></p>
                                <p class="text-sm text-gray-500"><?= !empty($user['email']) ? $user['email'] : 'user@example.com' ?></p>
                            </div>
                            <a href="?u=<?= !empty($user['username']) ? $user['username'] : 'profile' ?>" class="flex items-center px-4 py-3 text-sm text-gray-700 hover:bg-green-50 hover:text-green-600 transition-colors duration-200">
                                <svg class="mr-3 h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                                Your Profile
                            </a>
                            <a href="?editprofile" class="flex items-center px-4 py-3 text-sm text-gray-700 hover:bg-green-50 hover:text-green-600 transition-colors duration-200">
                                <svg class="mr-3 h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c-.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                Settings
                            </a>
                            <?php
                            if ($user['is_doctor'] == 0 && $user['ac_status'] == 1) {
                            ?>
                                <a href="?myappointment" class="flex items-center px-4 py-3 text-sm text-gray-700 hover:bg-green-50 hover:text-green-600 transition-colors duration-200">
                                    <svg class="mr-3 h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                    </svg>
                                    my Appointment
                                </a>
                            <?php
                            } elseif ($user['is_doctor'] == 1 && $user['ac_status'] == 1) {
                            ?>
                                <a href="?manageappointment" class="flex items-center px-4 py-3 text-sm text-gray-700 hover:bg-green-50 hover:text-green-600 transition-colors duration-200">
                                    <svg class="mr-3 h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                    </svg>
                                    Manage Appoitment
                                </a>
                            <?php
                            }

                            ?>

                            <div class="border-t border-green-100"></div>
                            <a href="./assets/php/actions.php?logout" class="flex items-center px-4 py-3 text-sm text-gray-700 hover:bg-green-50 hover:text-green-600 transition-colors duration-200">
                                <svg class="mr-3 h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                </svg>
                                Sign out
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Search Results Overlay -->
    <div id="search_result" class="absolute top-16 left-1/2 transform -translate-x-1/2 w-full max-w-2xl bg-white/80 backdrop-blur-sm rounded-xl shadow-xl border border-green-100 overflow-hidden z-50 hidden">
        <div class="p-4 border-b border-green-100 flex justify-between items-center">
            <h3 class="text-lg font-medium text-gray-900">Search Results</h3>
            <button id="close_search" class="text-gray-400 hover:text-gray-500">
                <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
        <div id="sra" class="p-4 max-h-96 overflow-y-auto">
            <p class="text-center text-gray-500 py-8">Enter name or username</p>
        </div>
    </div>
</nav>
<style>
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

    .animate-blob {
        animation: blob 7s infinite;
    }

    .animation-delay-2000 {
        animation-delay: 2s;
    }

    #user-menu {
        z-index: 60;
        /* Higher than the navbar */
    }
</style>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        // Search functionality
        const searchInput = document.getElementById('search');
        const searchResult = document.getElementById('search_result');
        const closeSearch = document.getElementById('close_search');
        const sra = document.getElementById('sra');
        // Show search results when the search input is focused
        searchInput.addEventListener('focus', () => {
            searchResult.classList.remove('hidden');
            // Add entrance animation
            gsap.fromTo(searchResult, {
                opacity: 0,
                y: -10
            }, {
                opacity: 1,
                y: 0,
                duration: 0.3,
                ease: "power2.out"
            });
        });
        // Update search results when typing
        searchInput.addEventListener('input', (e) => {
            const query = e.target.value.trim();
            if (query.length > 0) {
                // Show search results immediately
                sra.innerHTML = `
                <div class="space-y-4">
                    <div class="flex items-center p-3 rounded-lg hover:bg-green-50 cursor-pointer transition-all duration-300 group">
                        <img src="https://picsum.photos/seed/user1/40/40.jpg" alt="User" class="h-10 w-10 rounded-full mr-3 ring-2 ring-green-500 ring-opacity-30">
                        <div>
                            <p class="font-medium group-hover:text-green-600 transition-colors">Dr. Sarah Johnson</p>
                            <p class="text-sm text-gray-500">@sarahj • Cardiologist</p>
                        </div>
                        <div class="ml-auto opacity-0 group-hover:opacity-100 transition-opacity">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-chevron-right text-gray-400">
                                <path d="m9 18 6-6-6-6" />
                            </svg>
                        </div>
                    </div>
                    <div class="flex items-center p-3 rounded-lg hover:bg-green-50 cursor-pointer transition-all duration-300 group">
                        <img src="https://picsum.photos/seed/user2/40/40.jpg" alt="User" class="h-10 w-10 rounded-full mr-3 ring-2 ring-green-500 ring-opacity-30">
                        <div>
                            <p class="font-medium group-hover:text-green-600 transition-colors">Dr. Michael Chen</p>
                            <p class="text-sm text-gray-500">@mchen • Neurologist</p>
                        </div>
                        <div class="ml-auto opacity-0 group-hover:opacity-100 transition-opacity">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-chevron-right text-gray-400">
                                <path d="m9 18 6-6-6-6" />
                            </svg>
                        </div>
                    </div>
                    <div class="p-3 rounded-lg hover:bg-green-50 cursor-pointer transition-all duration-300 group">
                        <p class="font-medium group-hover:text-green-600 transition-colors">Health Tip: "Best practices for heart health"</p>
                        <p class="text-sm text-gray-500">By HealthLink Team • 2 days ago</p>
                    </div>
                </div>
            `;
                // Animate search results
                gsap.fromTo(sra.children[0].children, {
                    opacity: 0,
                    y: 10
                }, {
                    opacity: 1,
                    y: 0,
                    duration: 0.3,
                    stagger: 0.1,
                    ease: "power2.out"
                });
            } else {
                // Show initial message when input is empty
                sra.innerHTML = `<p class="text-center text-gray-500 py-8">Enter name or username</p>`;
            }
        });
        // Close the search result when the close button is clicked
        closeSearch.addEventListener('click', () => {
            gsap.to(searchResult, {
                opacity: 0,
                y: -10,
                duration: 0.2,
                ease: "power2.in",
                onComplete: () => {
                    searchResult.classList.add('hidden');
                }
            });
        });
        // Hide search results if clicking outside the search box or results
        document.addEventListener('click', (event) => {
            if (!searchInput.contains(event.target) && !searchResult.contains(event.target)) {
                gsap.to(searchResult, {
                    opacity: 0,
                    y: -10,
                    duration: 0.2,
                    ease: "power2.in",
                    onComplete: () => {
                        searchResult.classList.add('hidden');
                    }
                });
            }
        });
        // User dropdown menu
        const userMenuButton = document.getElementById('user-menu-button');
        const userMenu = document.getElementById('user-menu');
        // Initialize the dropdown with GSAP
        gsap.set(userMenu, {
            opacity: 0,
            scale: 0.95,
            y: -10,
            display: 'none' // Start with display none
        });
        userMenuButton.addEventListener('click', () => {
            if (userMenu.style.display === 'none') {
                // Show the menu
                gsap.set(userMenu, {
                    display: 'block'
                });
                // Animate in
                gsap.to(userMenu, {
                    opacity: 1,
                    scale: 1,
                    y: 0,
                    duration: 0.3,
                    ease: "power3.out"
                });
            } else {
                // Animate out
                gsap.to(userMenu, {
                    opacity: 0,
                    scale: 0.95,
                    y: -10,
                    duration: 0.2,
                    ease: "power2.in",
                    onComplete: () => {
                        gsap.set(userMenu, {
                            display: 'none'
                        });
                    }
                });
            }
        });
        // Close dropdown when clicking outside
        document.addEventListener('click', (event) => {
            if (!userMenuButton.contains(event.target) && !userMenu.contains(event.target)) {
                if (userMenu.style.display !== 'none') {
                    // Animate out
                    gsap.to(userMenu, {
                        opacity: 0,
                        scale: 0.95,
                        y: -10,
                        duration: 0.2,
                        ease: "power2.in",
                        onComplete: () => {
                            gsap.set(userMenu, {
                                display: 'none'
                            });
                        }
                    });
                }
            }
        });
        // Voice search functionality
        const voiceSearch = document.getElementById('voice-search');
        voiceSearch.addEventListener('click', () => {
            // Placeholder for voice search implementation
            // Create a subtle notification instead of alert
            const notification = document.createElement('div');
            notification.className = 'fixed bottom-4 right-4 bg-green-600 text-white px-4 py-2 rounded-lg shadow-lg z-50';
            notification.textContent = 'Voice search would be activated here';
            document.body.appendChild(notification);
            // Animate in
            gsap.fromTo(notification, {
                opacity: 0,
                y: 20
            }, {
                opacity: 1,
                y: 0,
                duration: 0.3,
                ease: "power2.out"
            });
            // Remove after 3 seconds
            setTimeout(() => {
                gsap.to(notification, {
                    opacity: 0,
                    y: 20,
                    duration: 0.3,
                    ease: "power2.in",
                    onComplete: () => {
                        notification.remove();
                    }
                });
            }, 3000);
        });
        // Simulate message counter updates
        const msgCounter = document.getElementById('msgcounter');
        // Simulate receiving a new message after 5 seconds
        setTimeout(() => {
            msgCounter.textContent = '3';
            msgCounter.classList.remove('hidden');
            // Animate the counter
            gsap.fromTo(msgCounter, {
                scale: 0
            }, {
                scale: 1.2,
                duration: 0.3,
                ease: "back.out(1.7)",
                onComplete: () => {
                    gsap.to(msgCounter, {
                        scale: 1,
                        duration: 0.2
                    });
                }
            });
        }, 5000);
    });
</script>