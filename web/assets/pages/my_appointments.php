<?php
include_once './assets/php/function.php';



// Check if user is logged in
if (!isset($_SESSION['userdata']['id'])) {
    header("Location: ?login");
    exit();
}

$patient_id = $_SESSION['userdata']['id'];
$appointments = getPatientAppointments($patient_id);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Appointments</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>
    <style>
        .tab-content { display: none; }
        .tab-content.active { display: block; }
    </style>
</head>
<body class="bg-gray-50">
    <div class="max-w-6xl mx-auto p-6">
        <h1 class="text-3xl font-bold mb-6">My Appointments</h1>
        
        <!-- Tabs -->
        <div class="flex border-b mb-6">
            <button class="tab-btn px-4 py-2 font-medium text-blue-600 border-b-2 border-blue-600" data-tab="upcoming">Upcoming</button>
            <button class="tab-btn px-4 py-2 font-medium text-gray-500 hover:text-gray-700" data-tab="approved">Approved</button>
            <button class="tab-btn px-4 py-2 font-medium text-gray-500 hover:text-gray-700" data-tab="completed">Completed</button>
        </div>
        
        <!-- Upcoming Appointments Tab -->
        <div id="upcoming" class="tab-content active">
            <?php if (empty($appointments['upcoming'])): ?>
                <div class="bg-white p-6 rounded-xl shadow text-center">
                    <p class="text-gray-500">No upcoming appointments</p>
                </div>
            <?php else: ?>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <?php foreach ($appointments['upcoming'] as $appointment): ?>
                        <div class="bg-white p-6 rounded-xl shadow">
                            <div class="flex justify-between items-start mb-4">
                                <div>
                                    <h3 class="text-xl font-bold">Dr. <?= $appointment['first_name'] . ' ' . $appointment['last_name'] ?></h3>
                                    <p class="text-gray-600"><?= $appointment['doctor_type'] ?></p>
                                </div>
                                <span class="px-3 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                    Pending
                                </span>
                            </div>
                            
                            <div class="space-y-2">
                                <p class="flex items-center text-gray-600">
                                    <i data-lucide="calendar" class="w-4 h-4 mr-2"></i>
                                    <?= date('F j, Y', strtotime($appointment['datetime'])) ?>
                                </p>
                                <p class="flex items-center text-gray-600">
                                    <i data-lucide="clock" class="w-4 h-4 mr-2"></i>
                                    <?= date('g:i A', strtotime($appointment['datetime'])) ?>
                                </p>
                                <p class="flex items-center text-gray-600">
                                    <i data-lucide="map-pin" class="w-4 h-4 mr-2"></i>
                                    <?= $appointment['doctor_address'] ?>
                                </p>
                                <p class="flex items-center text-gray-600">
                                    <i data-lucide="file-text" class="w-4 h-4 mr-2"></i>
                                    <?= $appointment['reason'] ?>
                                </p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
        
        <!-- Approved Appointments Tab -->
        <div id="approved" class="tab-content">
            <?php if (empty($appointments['approved'])): ?>
                <div class="bg-white p-6 rounded-xl shadow text-center">
                    <p class="text-gray-500">No approved appointments</p>
                </div>
            <?php else: ?>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <?php foreach ($appointments['approved'] as $appointment): ?>
                        <div class="bg-white p-6 rounded-xl shadow">
                            <div class="flex justify-between items-start mb-4">
                                <div>
                                    <h3 class="text-xl font-bold">Dr. <?= $appointment['first_name'] . ' ' . $appointment['last_name'] ?></h3>
                                    <p class="text-gray-600"><?= $appointment['doctor_type'] ?></p>
                                </div>
                                <span class="px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    Approved
                                </span>
                            </div>
                            
                            <div class="space-y-2">
                                <p class="flex items-center text-gray-600">
                                    <i data-lucide="calendar" class="w-4 h-4 mr-2"></i>
                                    <?= date('F j, Y', strtotime($appointment['datetime'])) ?>
                                </p>
                                <p class="flex items-center text-gray-600">
                                    <i data-lucide="clock" class="w-4 h-4 mr-2"></i>
                                    <?= date('g:i A', strtotime($appointment['datetime'])) ?>
                                </p>
                                <p class="flex items-center text-gray-600">
                                    <i data-lucide="map-pin" class="w-4 h-4 mr-2"></i>
                                    <?= $appointment['doctor_address'] ?>
                                </p>
                                <p class="flex items-center text-gray-600">
                                    <i data-lucide="file-text" class="w-4 h-4 mr-2"></i>
                                    <?= $appointment['reason'] ?>
                                </p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
        
        <!-- Completed Appointments Tab -->
        <div id="completed" class="tab-content">
            <?php if (empty($appointments['completed'])): ?>
                <div class="bg-white p-6 rounded-xl shadow text-center">
                    <p class="text-gray-500">No completed appointments</p>
                </div>
            <?php else: ?>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <?php foreach ($appointments['completed'] as $appointment): ?>
                        <div class="bg-white p-6 rounded-xl shadow">
                            <div class="flex justify-between items-start mb-4">
                                <div>
                                    <h3 class="text-xl font-bold">Dr. <?= $appointment['first_name'] . ' ' . $appointment['last_name'] ?></h3>
                                    <p class="text-gray-600"><?= $appointment['doctor_type'] ?></p>
                                </div>
                                <span class="px-3 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                    Completed
                                </span>
                            </div>
                            
                            <div class="space-y-2">
                                <p class="flex items-center text-gray-600">
                                    <i data-lucide="calendar" class="w-4 h-4 mr-2"></i>
                                    <?= date('F j, Y', strtotime($appointment['datetime'])) ?>
                                </p>
                                <p class="flex items-center text-gray-600">
                                    <i data-lucide="clock" class="w-4 h-4 mr-2"></i>
                                    <?= date('g:i A', strtotime($appointment['datetime'])) ?>
                                </p>
                                <p class="flex items-center text-gray-600">
                                    <i data-lucide="map-pin" class="w-4 h-4 mr-2"></i>
                                    <?= $appointment['doctor_address'] ?>
                                </p>
                                <p class="flex items-center text-gray-600">
                                    <i data-lucide="file-text" class="w-4 h-4 mr-2"></i>
                                    <?= $appointment['reason'] ?>
                                </p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    <script>
        lucide.createIcons();
        
        // Tab functionality
        document.querySelectorAll('.tab-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                const tabId = btn.getAttribute('data-tab');
                
                // Update active tab button
                document.querySelectorAll('.tab-btn').forEach(b => {
                    b.classList.remove('text-blue-600', 'border-b-2', 'border-blue-600');
                    b.classList.add('text-gray-500');
                });
                btn.classList.remove('text-gray-500');
                btn.classList.add('text-blue-600', 'border-b-2', 'border-blue-600');
                
                // Show active tab content
                document.querySelectorAll('.tab-content').forEach(content => {
                    content.classList.remove('active');
                });
                document.getElementById(tabId).classList.add('active');
            });
        });
    </script>
</body>
</html>