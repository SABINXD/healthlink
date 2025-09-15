<?php
    // Emergency Help System - Health Theme with Green and White
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Emergency Help</title>
        <script src="https://cdn.tailwindcss.com"></script>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
        <style>
            /* SOS pulse animation */
            @keyframes pulse-red {
                0%,
                100% {
                    transform: scale(1);
                    box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.7);
                }
                50% {
                    transform: scale(1.05);
                    box-shadow: 0 0 0 20px rgba(239, 68, 68, 0);
                }
            }
            .pulse-animation {
                animation: pulse-red 2s infinite;
            }
            /* Loading spinner */
            .spinner {
                border: 3px solid rgba(0, 0, 0, 0.1);
                border-radius: 50%;
                border-top: 3px solid #10b981;
                width: 20px;
                height: 20px;
                animation: spin 1s linear infinite;
            }
            @keyframes spin {
                0% {
                    transform: rotate(0deg);
                }
                100% {
                    transform: rotate(360deg);
                }
            }
            /* Floating animation */
            @keyframes float {
                0%,
                100% {
                    transform: translateY(0px);
                }
                50% {
                    transform: translateY(-10px);
                }
            }
            .float-animation {
                animation: float 3s ease-in-out infinite;
            }
            /* Notification slide in */
            @keyframes slideInRight {
                from {
                    transform: translateX(100%);
                    opacity: 0;
                }
                to {
                    transform: translateX(0);
                    opacity: 1;
                }
            }
            .notification {
                animation: slideInRight 0.3s ease-out;
            }
            /* Custom scrollbar */
            .scrollable-container::-webkit-scrollbar {
                width: 6px;
            }
            .scrollable-container::-webkit-scrollbar-track {
                background: #f0fdf4;
                border-radius: 10px;
            }
            .scrollable-container::-webkit-scrollbar-thumb {
                background: #bbf7d0;
                border-radius: 10px;
            }
            .scrollable-container::-webkit-scrollbar-thumb:hover {
                background: #86efac;
            }
            /* Floating contact styles */
            .floating-contact {
                position: absolute;
                width: 70px;
                height: 70px;
                border-radius: 50%;
                display: flex;
                flex-direction: column;
                align-items: center;
                justify-content: center;
                cursor: pointer;
                transition: all 0.3s ease;
                z-index: 30;
                box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
            }
            .floating-contact:hover {
                transform: scale(1.1);
                box-shadow: 0 6px 20px rgba(0, 0, 0, 0.3);
            }
            .floating-contact .initial {
                font-size: 20px;
                font-weight: bold;
                margin-bottom: 2px;
            }
            .floating-contact .name {
                font-size: 9px;
                text-align: center;
                line-height: 1;
                max-width: 100%;
                overflow: hidden;
                text-overflow: ellipsis;
                white-space: nowrap;
            }
            /* Glow effect */
            @keyframes glow {
                0%,
                100% {
                    box-shadow: 0 0 5px rgba(16, 185, 129, 0.5);
                }
                50% {
                    box-shadow: 0 0 20px rgba(16, 185, 129, 0.8),
                        0 0 30px rgba(16, 185, 129, 0.6);
                }
            }
            .glow-effect {
                animation: glow 2s infinite;
            }
        </style>
    </head>
    <body class="bg-gradient-to-br from-green-50 to-emerald-100 min-h-screen flex items-center justify-center p-4">
        <!-- Fixed Floating Contacts Sidebar -->
        <div id="floatingContacts" class="fixed left-0 top-0 h-full w-24 pointer-events-none z-10">
            <!-- Floating contacts will be inserted here -->
        </div>
        <!-- Main Container -->
        <div class="w-full max-w-md mt-8">
            <!-- Main Card -->
            <div class="bg-white/90 backdrop-blur-sm rounded-3xl shadow-xl p-8 space-y-8 border border-green-200">
                <!-- Header -->
                <div class="flex justify-between items-center">
                    <div>
                        <h1 class="text-3xl font-bold text-green-600 flex items-center gap-3">
                            <i class="fas fa-heartbeat text-4xl"></i>
                            Emergency Help
                        </h1>
                        <p class="text-gray-600 text-sm mt-1">24/7 Health Emergency Assistance</p>
                    </div>
                    <button onclick="toggleSidebar()" class="bg-green-50 p-4 rounded-2xl hover:bg-green-100 transition-all duration-300 flex items-center gap-2 group">
                        <i class="fas fa-address-book text-xl group-hover:scale-110 transition-transform text-green-700"></i>
                        <span class="font-medium text-green-700">Contacts</span>
                    </button>
                </div>
                <!-- Location Card -->
                <div class="bg-green-50 rounded-2xl p-6 border border-green-200">
                    <h2 class="text-xl font-semibold text-gray-800 flex items-center gap-2 mb-3">
                        <i class="fas fa-map-marker-alt text-green-600 text-xl"></i>
                        Your Location
                    </h2>
                    <div id="location" class="text-gray-600 flex items-center gap-3">
                        <div class="spinner"></div>
                        <span>Fetching location...</span>
                    </div>
                </div>
                <!-- Quick Emergency Contacts -->
                <div>
                    <h2 class="text-xl font-semibold text-gray-800 mb-4 flex items-center gap-2">
                        <i class="fas fa-phone-alt text-green-600 text-xl"></i>
                        Quick Emergency Contacts
                    </h2>
                    <div class="grid grid-cols-3 gap-4">
                        <!-- Police Contact -->
                        <div onclick="callNumber('100')" class="bg-green-50 rounded-2xl p-6 flex flex-col items-center justify-center cursor-pointer hover:bg-green-100 transition-all duration-300 group">
                            <div class="bg-blue-100 w-16 h-16 rounded-full flex items-center justify-center mb-3 group-hover:scale-110 transition-transform">
                                <i class="fas fa-shield-alt text-blue-600 text-2xl"></i>
                            </div>
                            <span class="font-bold text-lg text-gray-800">Police</span>
                            <span class="text-blue-500 text-sm">100</span>
                        </div>
                        <!-- Fire Contact -->
                        <div onclick="callNumber('101')" class="bg-green-50 rounded-2xl p-6 flex flex-col items-center justify-center cursor-pointer hover:bg-green-100 transition-all duration-300 group">
                            <div class="bg-orange-100 w-16 h-16 rounded-full flex items-center justify-center mb-3 group-hover:scale-110 transition-transform">
                                <i class="fas fa-fire-extinguisher text-orange-600 text-2xl"></i>
                            </div>
                            <span class="font-bold text-lg text-gray-800">Fire</span>
                            <span class="text-orange-500 text-sm">101</span>
                        </div>
                        <!-- Ambulance Contact -->
                        <div onclick="callNumber('102')" class="bg-green-50 rounded-2xl p-6 flex flex-col items-center justify-center cursor-pointer hover:bg-green-100 transition-all duration-300 group">
                            <div class="bg-red-100 w-16 h-16 rounded-full flex items-center justify-center mb-3 group-hover:scale-110 transition-transform">
                                <i class="fas fa-ambulance text-red-600 text-2xl"></i>
                            </div>
                            <span class="font-bold text-lg text-gray-800">Ambulance</span>
                            <span class="text-red-500 text-sm">102</span>
                        </div>
                    </div>
                </div>
                <!-- SOS Button -->
                <div class="flex justify-center">
                    <button id="sosBtn" class="bg-red-500 text-white text-2xl font-bold py-8 px-16 rounded-full shadow-lg pulse-animation hover:bg-red-600 transition-all duration-300 flex items-center gap-4 group">
                        <i class="fas fa-exclamation text-3xl group-hover:scale-110 transition-transform"></i>
                        <span>SOS EMERGENCY</span>
                    </button>
                </div>
            </div>
        </div>
        <!-- Sidebar for Contacts -->
        <div id="sidebar" class="fixed top-0 right-0 w-96 h-full bg-white/90 backdrop-blur-sm shadow-xl p-8 hidden z-40 border-l border-green-200">
            <h2 class="text-2xl font-bold text-green-600 mb-6 flex items-center gap-3">
                <i class="fas fa-address-book text-3xl"></i>
                Emergency Contacts
            </h2>
            <!-- Add Contact Form -->
            <form id="contactForm" class="space-y-4 mb-8">
                <div class="relative">
                    <input type="text" id="contactName" placeholder="Contact Name (e.g. Mother)" class="w-full p-4 bg-green-50 rounded-xl text-gray-800 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-green-500 text-lg" required />
                    <i class="fas fa-user absolute right-4 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                </div>
                <div class="relative">
                    <input type="tel" id="contactNumber" placeholder="Phone Number" class="w-full p-4 bg-green-50 rounded-xl text-gray-800 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-green-500 text-lg" required />
                    <i class="fas fa-phone absolute right-4 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                </div>
                <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white py-4 rounded-xl transition-all duration-300 flex items-center justify-center gap-2 text-lg font-semibold">
                    <i class="fas fa-plus text-xl"></i>
                    Add Contact
                </button>
            </form>
            <!-- Contacts List -->
            <h3 class="text-xl font-semibold text-gray-800 mb-4">All Contacts</h3>
            <ul id="contactsList" class="space-y-3 max-h-96 overflow-y-auto pr-2 scrollable-container">
                <!-- Contacts will be inserted here -->
            </ul>
            <!-- Close Button -->
            <button onclick="toggleSidebar()" class="absolute top-6 right-6 text-gray-400 hover:text-gray-600 text-2xl">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <!-- SOS Ambulance Modal -->
        <div id="sosModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center opacity-0 pointer-events-none z-50 transition-opacity duration-300">
            <div class="bg-white/90 backdrop-blur-sm rounded-3xl w-full max-w-lg p-8 relative border border-green-200">
                <h2 class="text-3xl font-bold text-green-600 text-center mb-6 flex items-center justify-center gap-3">
                    <i class="fas fa-ambulance text-4xl"></i>
                    Nearest Ambulance Services
                </h2>
                <div class="bg-green-50 rounded-2xl p-6 mb-8 text-center">
                    <p class="text-gray-700 text-lg">
                        District: <span id="districtName" class="font-bold text-green-600 text-xl">Loading...</span>
                    </p>
                </div>
                <div id="ambulanceList" class="space-y-4 mb-8 max-h-64 overflow-y-auto pr-2 scrollable-container">
                </div>
                <div class="flex gap-4">
                    <button id="callAmbulanceBtn" class="flex-1 bg-green-600 hover:bg-green-700 text-white py-4 rounded-2xl font-bold transition-all duration-300 flex items-center justify-center gap-2 text-lg">
                        <i class="fas fa-phone-alt text-xl"></i>
                        Call Ambulance
                    </button>
                    <button onclick="closeSosModal()" class="flex-1 bg-gray-100 hover:bg-gray-200 text-gray-800 py-4 rounded-2xl font-bold transition-all duration-300 text-lg">
                        Close
                    </button>
                </div>
            </div>
        </div>
        <!-- Success Notification -->
        <div id="successNotification" class="fixed bottom-6 right-6 bg-white/90 backdrop-blur-sm text-gray-800 p-4 rounded-2xl shadow-lg flex items-center gap-3 transform translate-x-full opacity-0 transition-all duration-300 z-50 border border-green-200">
            <div class="bg-green-100 w-10 h-10 rounded-full flex items-center justify-center">
                <i class="fas fa-check text-green-600"></i>
            </div>
            <div>
                <p class="font-bold">Contact Added</p>
                <p class="text-sm text-gray-600">Emergency contact saved successfully</p>
            </div>
        </div>
        <script>
            // Global variables
            let userDistrict = "Unknown";
            let userCoordinates = {
                lat: null,
                lng: null
            };
            // Mapping of districts to ambulance numbers
            const ambulanceNumbers = {
                Kathmandu: [{
                        name: "Red Cross Ambulance (Kathmandu)",
                        number: "9842627333"
                    },
                    {
                        name: "Kritipur Ambulance",
                        number: "01-4330200"
                    },
                ],
                Pokhara: [{
                    name: "Ambulance Service Bindabasini",
                    number: "061-441100"
                }, ],
                Lalitpur: [{
                    name: "Alka Hospital Ambulance",
                    number: "01-5970795"
                }, ],
                Bhaktapur: [{
                        name: "Thimi Ambulance",
                        number: "01-6631000"
                    },
                    {
                        name: "Nepal Red Cross Society (Bhaktapur)",
                        number: "9849857332"
                    },
                ],
                Chitwan: [{
                        name: "Bharatpur Hospital Ambulance",
                        number: "056-597003"
                    }, // also hotline & emergency counter
                    {
                        name: "Red Cross Ambulance (Chitwan)",
                        number: "056-520133"
                    },
                    {
                        name: "Chitwan Ambulance Service",
                        number: "9845731448"
                    },
                    {
                        name: "Suman Gole (Bharatpur)",
                        number: "9845155163"
                    },
                    {
                        name: "Saptagandaki Hospital",
                        number: "9845110741"
                    },
                ],
                Nawalpur: [{
                        name: "National Emergency (Nepal-wide)",
                        number: "102"
                    },
                    {
                        name: "Chitwan Ambulance Service (Facebook listing)",
                        number: "9845731448"
                    },
                    {
                        name: "Suman Gole (Bharatpur)",
                        number: "9845155163"
                    },
                    {
                        name: "Saptagandaki Hospital",
                        number: "9845110741"
                    },
                ],
                Default: [{
                        name: "National Emergency",
                        number: "102"
                    },
                    {
                        name: "Red Cross Nepal",
                        number: "110"
                    },
                ],
            };

            // Generate random position for floating contacts
            function getRandomPosition() {
                const container = document.getElementById('floatingContacts');
                const containerRect = container.getBoundingClientRect();
                const margin = 30; // Minimum distance from edges
                const centerX = window.innerWidth / 2;
                const centerY = window.innerHeight / 2;
                const centerRadius = 250; // Minimum distance from center
                let x, y;
                let attempts = 0;
                do {
                    // Position within the left sidebar area
                    x = margin + Math.random() * (containerRect.width - 2 * margin);
                    y = margin + Math.random() * (window.innerHeight - 2 * margin);
                    attempts++;
                } while (
                    Math.sqrt(Math.pow(x + containerRect.left - centerX, 2) + Math.pow(y - centerY, 2)) < centerRadius &&
                    attempts < 100
                );
                return {
                    x,
                    y
                };
            }
            // Create floating contact button
            function createFloatingContact(contact, index) {
                const button = document.createElement("div");
                button.className = "floating-contact bg-white/90 backdrop-blur-sm border border-green-200 glow-effect float-animation";
                button.style.animationDelay = `${index * 0.2}s`;
                // Get random position
                const position = getRandomPosition();
                button.style.left = `${position.x}px`;
                button.style.top = `${position.y}px`;
                // Get initial
                const initial = contact.name.charAt(0).toUpperCase();
                button.innerHTML = `
                    <div class="initial text-gray-800">${initial}</div>
                    <div class="name text-gray-600">${contact.name}</div>
                `;
                button.onclick = () => callNumber(contact.number);
                return button;
            }
            // Update floating contacts
            function updateFloatingContacts() {
                const container = document.getElementById("floatingContacts");
                container.innerHTML = "";
                contacts.forEach((contact, index) => {
                    const button = createFloatingContact(contact, index);
                    container.appendChild(button);
                });
            }
            // Toggle Sidebar
            function toggleSidebar() {
                const sidebar = document.getElementById("sidebar");
                sidebar.classList.toggle("hidden");
            }
            // Fetch GPS + District
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(
                    async (position) => {
                            const lat = position.coords.latitude.toFixed(4);
                            const lng = position.coords.longitude.toFixed(4);
                            userCoordinates = {
                                lat,
                                lng
                            };
                            try {
                                const res = await fetch(`https://nominatim.openstreetmap.org/reverse?lat=${lat}&lon=${lng}&format=json`);
                                const data = await res.json();
                                const city = data.address.city || data.address.town || data.address.village || "Unknown City";
                                const district = data.address.county || data.address.state_district || "Unknown District";
                                userDistrict = normalizeDistrictName(district);
                                document.getElementById("location").innerHTML = `
                                <div class="flex flex-col gap-2">
                                    <div class="flex items-center gap-3">
                                        <i class="fas fa-map-marker-alt text-green-600 text-xl"></i>
                                        <span class="text-gray-700 text-lg">${city}, ${district}</span>
                                    </div>
                                    <div class="text-sm text-gray-500">Lat: ${lat}, Lng: ${lng}</div>
                                </div>
                            `;
                            } catch (error) {
                                document.getElementById("location").innerHTML = `
                                <div class="flex items-center gap-3">
                                    <i class="fas fa-map-marker-alt text-green-600 text-xl"></i>
                                    <span class="text-gray-700 text-lg">Lat: ${lat}, Lng: ${lng}</span>
                                </div>
                                <div class="text-sm text-gray-500">District info unavailable.</div>
                            `;
                            }
                        },
                        () => {
                            document.getElementById("location").innerHTML = `
                            <div class="flex items-center gap-3">
                                <i class="fas fa-exclamation-triangle text-yellow-500 text-xl"></i>
                                <span class="text-gray-700 text-lg">Unable to fetch location.</span>
                            </div>
                        `;
                        }
                );
            } else {
                document.getElementById("location").innerHTML = `
                    <div class="flex items-center gap-3">
                        <i class="fas fa-exclamation-triangle text-yellow-500 text-xl"></i>
                        <span class="text-gray-700 text-lg">Geolocation not supported.</span>
                    </div>
                `;
            }
            // Call function
            function callNumber(number) {
                window.location.href = `tel:${number}`;
            }
            // SOS Button
            document.getElementById("sosBtn").addEventListener("click", () => {
                showSosModal();
            });
            // Show SOS Modal
            function showSosModal() {
                const modal = document.getElementById("sosModal");
                const districtName = document.getElementById("districtName");
                const ambulanceList = document.getElementById("ambulanceList");
                // Set district name
                districtName.textContent = userDistrict;
                // Clear previous list
                ambulanceList.innerHTML = "";
                // Get ambulance numbers for this district
                const ambulances = ambulanceNumbers[userDistrict] || ambulanceNumbers["Default"];
                // Add ambulance numbers to the list
                ambulances.forEach((ambulance) => {
                    const ambulanceItem = document.createElement("div");
                    ambulanceItem.className = "bg-green-50 rounded-2xl p-6 flex items-center justify-between cursor-pointer hover:bg-green-100 transition-all duration-300";
                    ambulanceItem.innerHTML = `
                        <div>
                            <span class="text-lg font-bold text-gray-800">${ambulance.name}</span>
                            <p class="text-sm text-gray-600">Emergency Service</p>
                        </div>
                        <span class="text-green-600 font-bold text-xl">${ambulance.number}</span>
                    `;
                    ambulanceItem.addEventListener("click", () => {
                        callNumber(ambulance.number);
                    });
                    ambulanceList.appendChild(ambulanceItem);
                });
                // Set the call button to call the first ambulance number
                const callAmbulanceBtn = document.getElementById("callAmbulanceBtn");
                callAmbulanceBtn.onclick = () => {
                    callNumber(ambulances[0].number);
                };
                // Show modal
                modal.classList.remove("opacity-0", "pointer-events-none");
                modal.classList.add("opacity-100", "pointer-events-auto");
            }
            // Close SOS Modal
            function closeSosModal() {
                const modal = document.getElementById("sosModal");
                modal.classList.remove("opacity-100", "pointer-events-auto");
                modal.classList.add("opacity-0", "pointer-events-none");
            }
            // Contact Storage
            const contactForm = document.getElementById("contactForm");
            const contactsList = document.getElementById("contactsList");
            const successNotification = document.getElementById("successNotification");
            // Load saved contacts
            let contacts = JSON.parse(localStorage.getItem("emergencyContacts")) || [];
            renderContacts();
            updateFloatingContacts();
            // Update positions on window resize
            window.addEventListener("resize", () => {
                updateFloatingContacts();
            });
            contactForm.addEventListener("submit", (e) => {
                e.preventDefault();
                const name = document.getElementById("contactName").value.trim();
                const number = document.getElementById("contactNumber").value.trim();
                if (name && number) {
                    contacts.push({
                        name,
                        number
                    });
                    localStorage.setItem("emergencyContacts", JSON.stringify(contacts));
                    renderContacts();
                    updateFloatingContacts();
                    contactForm.reset();
                    showSuccessNotification();
                    setTimeout(() => {
                        toggleSidebar();
                    }, 1500);
                }
            });
            // normalaize user location 
            function normalizeDistrictName(district) {
                const mapping = {
                    "Nawalpur District": "Nawalpur",
                    "Nawalparasi East": "Nawalpur",
                    "Nawalparasi (Bardaghat Susta East)": "Nawalpur",
                    "Nawalparasi": "Nawalpur",
                    "Chitwan District": "Chitwan",
                    "Kathmandu District": "Kathmandu",
                    "Lalitpur District": "Lalitpur",
                    "Bhaktapur District": "Bhaktapur",
                    "Pokhara": "Pokhara",
                };
                return mapping[district] || district;
            }

            function renderContacts() {
                contactsList.innerHTML = "";
                if (contacts.length === 0) {
                    contactsList.innerHTML = `
                        <li class="text-gray-500 text-center py-6">
                            <i class="fas fa-user-slash text-3xl mb-3"></i>
                            <p>No emergency contacts saved</p>
                        </li>
                    `;
                    return;
                }
                contacts.forEach((c, i) => {
                    const li = document.createElement("li");
                    li.className = "bg-green-50 rounded-2xl p-4 flex items-center justify-between hover:bg-green-100 transition-all duration-300";
                    li.innerHTML = `
                        <div class="flex items-center gap-4">
                            <div class="bg-green-100 w-12 h-12 rounded-full flex items-center justify-center">
                                <i class="fas fa-user text-green-600 text-xl"></i>
                            </div>
                            <div>
                                <span class="text-gray-800 font-semibold text-lg">${c.name}</span>
                                <p class="text-sm text-gray-600">${c.number}</p>
                            </div>
                        </div>
                        <div class="flex gap-3">
                            <button onclick="callNumber('${c.number}')" class="bg-green-600 hover:bg-green-700 text-white p-3 rounded-xl transition-all duration-300">
                                <i class="fas fa-phone"></i>
                            </button>
                            <button onclick="deleteContact(${i})" class="bg-red-500 hover:bg-red-600 text-white p-3 rounded-xl transition-all duration-300">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    `;
                    contactsList.appendChild(li);
                });
            }
            function deleteContact(index) {
                contacts.splice(index, 1);
                localStorage.setItem("emergencyContacts", JSON.stringify(contacts));
                renderContacts();
                updateFloatingContacts();
            }
            function showSuccessNotification() {
                successNotification.classList.remove("translate-x-full", "opacity-0");
                successNotification.classList.add("translate-x-0", "opacity-100", "notification");
                setTimeout(() => {
                    successNotification.classList.remove("translate-x-0", "opacity-100", "notification");
                    successNotification.classList.add("translate-x-full", "opacity-0");
                }, 3000);
            }
        </script>
    </body>
    </html>