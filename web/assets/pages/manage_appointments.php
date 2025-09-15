<?php
ob_start(); // Start output buffering to catch any accidental output

include_once './assets/php/function.php';

// Check if user is logged in and is a doctor
if (!isset($_SESSION['userdata']['id']) || $_SESSION['userdata']['is_doctor'] != 1) {
    header("Location: ?login");
    exit();
}

// Handle status update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $appointment_id = $_POST['appointment_id'];
    $status = $_POST['status'];
    
    $result = updateAppointmentStatus($appointment_id, $status);
    
    // Redirect to prevent form resubmission
    header("Location: ?manageappointment");
    exit();
}

$doctor_id = $_SESSION['userdata']['id'];
$appointments = getDoctorAppointments($doctor_id);
ob_end_flush(); // End output buffering and send output
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Appointments</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>
    <style>
        .tab-content { display: none; }
        .tab-content.active { display: block; }
    </style>
</head>
<body class="bg-gray-50">
    <div class="max-w-6xl mx-auto p-6">
        <h1 class="text-3xl font-bold mb-6">Manage Appointments</h1>
        
        <!-- Tabs -->
        <div class="flex border-b mb-6">
            <button class="tab-btn px-4 py-2 font-medium text-blue-600 border-b-2 border-blue-600" data-tab="pending">Pending</button>
            <button class="tab-btn px-4 py-2 font-medium text-gray-500 hover:text-gray-700" data-tab="approved">Approved</button>
            <button class="tab-btn px-4 py-2 font-medium text-gray-500 hover:text-gray-700" data-tab="declined">Declined</button>
        </div>
        
        <!-- Pending Appointments Tab -->
        <div id="pending" class="tab-content active">
            <?php if (empty($appointments['pending'])): ?>
                <div class="bg-white p-6 rounded-xl shadow text-center">
                    <p class="text-gray-500">No pending appointments</p>
                </div>
            <?php else: ?>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <?php foreach ($appointments['pending'] as $appointment): ?>
                        <div class="bg-white p-6 rounded-xl shadow">
                            <div class="flex justify-between items-start mb-4">
                                <div>
                                    <h3 class="text-xl font-bold"><?= $appointment['first_name'] . ' ' . $appointment['last_name'] ?></h3>
                                    <p class="text-gray-600"><?= $appointment['email'] ?></p>
                                </div>
                                <span class="px-3 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                    Pending
                                </span>
                            </div>
                            
                            <div class="space-y-2 mb-4">
                                <p class="flex items-center text-gray-600">
                                    <i data-lucide="calendar" class="w-4 h-4 mr-2"></i>
                                    <?= date('F j, Y', strtotime($appointment['datetime'])) ?>
                                </p>
                                <p class="flex items-center text-gray-600">
                                    <i data-lucide="clock" class="w-4 h-4 mr-2"></i>
                                    <?= date('g:i A', strtotime($appointment['datetime'])) ?>
                                </p>
                                <p class="flex items-center text-gray-600">
                                    <i data-lucide="file-text" class="w-4 h-4 mr-2"></i>
                                    <?= $appointment['reason'] ?>
                                </p>
                                <?php if (!empty($appointment['patient_desc'])): ?>
                                    <p class="flex items-start text-gray-600">
                                        <i data-lucide="message-square" class="w-4 h-4 mr-2 mt-1"></i>
                                        <span><?= $appointment['patient_desc'] ?></span>
                                    </p>
                                <?php endif; ?>
                            </div>
                            
                            <form method="post" class="flex gap-2">
                                <input type="hidden" name="appointment_id" value="<?= $appointment['id'] ?>">
                                <input type="hidden" name="status" value="1">
                                <button type="submit" name="update_status" class="flex-1 px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700 transition-colors">
                                    Approve
                                </button>
                                <button type="button" class="flex-1 px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700 transition-colors decline-btn" 
                                        data-id="<?= $appointment['id'] ?>">
                                    Decline
                                </button>
                            </form>
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
                                    <h3 class="text-xl font-bold"><?= $appointment['first_name'] . ' ' . $appointment['last_name'] ?></h3>
                                    <p class="text-gray-600"><?= $appointment['email'] ?></p>
                                </div>
                                <span class="px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    Approved
                                </span>
                            </div>
                            
                            <div class="space-y-2 mb-4">
                                <p class="flex items-center text-gray-600">
                                    <i data-lucide="calendar" class="w-4 h-4 mr-2"></i>
                                    <?= date('F j, Y', strtotime($appointment['datetime'])) ?>
                                </p>
                                <p class="flex items-center text-gray-600">
                                    <i data-lucide="clock" class="w-4 h-4 mr-2"></i>
                                    <?= date('g:i A', strtotime($appointment['datetime'])) ?>
                                </p>
                                <p class="flex items-center text-gray-600">
                                    <i data-lucide="file-text" class="w-4 h-4 mr-2"></i>
                                    <?= $appointment['reason'] ?>
                                </p>
                                <?php if (!empty($appointment['patient_desc'])): ?>
                                    <p class="flex items-start text-gray-600">
                                        <i data-lucide="message-square" class="w-4 h-4 mr-2 mt-1"></i>
                                        <span><?= $appointment['patient_desc'] ?></span>
                                    </p>
                                <?php endif; ?>
                            </div>
                            
                            <form method="post">
                                <input type="hidden" name="appointment_id" value="<?= $appointment['id'] ?>">
                                <input type="hidden" name="status" value="2">
                                <button type="submit" name="update_status" class="w-full px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700 transition-colors">
                                    Decline Appointment
                                </button>
                            </form>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
        
        <!-- Declined Appointments Tab -->
        <div id="declined" class="tab-content">
            <?php if (empty($appointments['declined'])): ?>
                <div class="bg-white p-6 rounded-xl shadow text-center">
                    <p class="text-gray-500">No declined appointments</p>
                </div>
            <?php else: ?>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <?php foreach ($appointments['declined'] as $appointment): ?>
                        <div class="bg-white p-6 rounded-xl shadow">
                            <div class="flex justify-between items-start mb-4">
                                <div>
                                    <h3 class="text-xl font-bold"><?= $appointment['first_name'] . ' ' . $appointment['last_name'] ?></h3>
                                    <p class="text-gray-600"><?= $appointment['email'] ?></p>
                                </div>
                                <span class="px-3 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                    Declined
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
                                    <i data-lucide="file-text" class="w-4 h-4 mr-2"></i>
                                    <?= $appointment['reason'] ?>
                                </p>
                                <?php if (!empty($appointment['patient_desc'])): ?>
                                    <p class="flex items-start text-gray-600">
                                        <i data-lucide="message-square" class="w-4 h-4 mr-2 mt-1"></i>
                                        <span><?= $appointment['patient_desc'] ?></span>
                                    </p>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- Confirmation Modal for Decline -->
    <div id="confirmModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
        <div class="bg-white rounded-xl shadow-lg max-w-md w-full p-6">
            <h3 class="text-xl font-bold mb-4">Confirm Decline</h3>
            <p class="text-gray-600 mb-6">Are you sure you want to decline this appointment?</p>
            
            <form method="post" id="declineForm">
                <input type="hidden" name="appointment_id" id="declineAppointmentId">
                <input type="hidden" name="status" value="2">
                <input type="hidden" name="update_status" value="1">
                
                <div class="flex justify-end gap-2">
                    <button type="button" id="cancelDecline" class="px-4 py-2 border border-gray-300 rounded hover:bg-gray-50 transition-colors">
                        Cancel
                    </button>
                    <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700 transition-colors">
                        Decline
                    </button>
                </div>
            </form>
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
        
        // Decline confirmation modal
        const confirmModal = document.getElementById('confirmModal');
        const cancelDecline = document.getElementById('cancelDecline');
        const declineForm = document.getElementById('declineForm');
        const declineAppointmentId = document.getElementById('declineAppointmentId');
        
        document.querySelectorAll('.decline-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                const appointmentId = btn.getAttribute('data-id');
                declineAppointmentId.value = appointmentId;
                confirmModal.classList.remove('hidden');
            });
        });
        
        cancelDecline.addEventListener('click', () => {
            confirmModal.classList.add('hidden');
        });
        
        // Close modal when clicking outside
        confirmModal.addEventListener('click', (e) => {
            if (e.target === confirmModal) {
                confirmModal.classList.add('hidden');
            }
        });
    </script>
</body>
</html>