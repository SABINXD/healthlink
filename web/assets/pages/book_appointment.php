<?php
include_once 'assets/php/function.php';
session_start();

// Get doctor by ID
$doctor_id = isset($_GET['bookappointment']) ? (int)$_GET['bookappointment'] : 0;
$doctor = isDoctorAvailable($doctor_id);
$booked_appointments = getBookedAppointments($doctor_id); // should return array like ['2025-08-28'=>['09:00','09:30'],...]

if (!$doctor) {
?>
    <div class="flex flex-col items-center justify-center h-screen bg-gray-50">
        <h1 class="text-6xl font-bold text-red-600 mb-4">404</h1>
        <p class="text-2xl text-gray-700 mb-6">Sorry, No Doctor Found!</p>
        <a href="?" class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
            Back to Profile
        </a>
    </div>
<?php
    exit;
}
?>
<div class="max-w-6xl mx-auto p-6 flex flex-col md:flex-row gap-6">
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>

    <!-- Left Sidebar: Date Selection -->
    <div class="md:w-1/4 bg-white p-6 rounded-xl shadow h-fit">
        <h3 class="font-bold text-lg mb-4 flex items-center"><i data-lucide="calendar" class="w-5 h-5 mr-2 text-blue-600"></i>Select Date</h3>
        <div class="space-y-3" id="dateBlocks"></div>
    </div>

    <!-- Main Area: Doctor Info + Time Slots -->
    <div class="md:flex-1 bg-white p-6 rounded-xl shadow">
        <!-- Doctor Info -->
        <div class="flex items-center gap-4 mb-6">
            <img src="assets/img/profile/<?= $doctor['profile_pic'] ?>" class="w-20 h-20 rounded-full border-2 border-blue-200">
            <div>
                <h2 class="text-2xl font-bold">Dr <?= $doctor['first_name'] . ' ' . $doctor['last_name'] ?></h2>
                <p class="text-blue-600 font-medium"><?= $doctor['doctor_type'] ?></p>
                <p class="text-gray-500 flex items-center gap-1"><i data-lucide="map-pin" class="w-4 h-4"></i><?= $doctor['doctor_address'] ?></p>
            </div>
        </div>

        <!-- Appointment Form -->
        <form id="appointmentForm" method="post" class="space-y-4">
            <input type="hidden" id="selectedDate" name="date">
            <input type="hidden" id="selectedTime" name="time">
            <input type="hidden" name="doctor_id" value="<?= $doctor_id ?>">

            <!-- Time Slots -->
            <div>
                <h3 class="font-bold text-lg mb-4 flex items-center"><i data-lucide="clock" class="w-5 h-5 mr-2 text-blue-600"></i>Select Time</h3>
                <div id="timeSlots" class="grid grid-cols-3 gap-3"></div>
            </div>

            <!-- Reason -->
            <select id="visitReason" name="reason" class="w-full p-3 border border-gray-300 rounded" required>
                <option value="">Select a reason</option>
                <option>General Consultation</option>
                <option>Follow-up Visit</option>
                <option>Routine Check-up</option>
                <option>Vaccination</option>
                <option>Other</option>
            </select>

            <!-- Notes -->
            <textarea name="desc" placeholder="Additional notes..." class="w-full p-3 border border-gray-300 rounded"></textarea>

            <!-- Book Button -->
            <button type="submit" id="bookBtn" class="w-full bg-blue-600 hover:bg-blue-700 text-white p-3 rounded font-bold disabled:bg-gray-400" disabled>
                Book Appointment
            </button>
        </form>
    </div>
</div>

<!-- Success Toast -->
<div id="successToast" class="fixed top-20 right-5 bg-green-500 text-white p-4 rounded shadow-lg hidden flex items-center">
    <i data-lucide="check-circle" class="inline w-5 h-5 mr-2"></i>
    <span>Appointment booked successfully!</span>
</div>

<script>
    lucide.createIcons();

    // Time slots
    const timeSlots = [
        '09:00', '09:30', '10:00', '10:30', '11:00', '11:30',
        '14:00', '14:30', '15:00', '15:30', '16:00', '16:30',
        '18:00', '18:30', '19:00', '19:30'
    ];
    const bookedSlots = <?= json_encode($booked_appointments); ?>;

    // Generate Date Blocks: Today, Tomorrow, Day after
    const dateBlocks = [];
    const today = new Date();
    for (let i = 0; i < 3; i++) {
        const d = new Date();
        d.setDate(today.getDate() + i);
        dateBlocks.push({
            label: i === 0 ? 'Today' : i === 1 ? 'Tomorrow' : d.toLocaleDateString('en-US', {
                weekday: 'long'
            }),
            date: d.toISOString().split('T')[0]
        });
    }

    // Render Date Blocks
    const dateContainer = document.getElementById('dateBlocks');
    dateBlocks.forEach((block, idx) => {
        const btn = document.createElement('button');
        btn.type = 'button';
        btn.textContent = block.label;
        btn.dataset.date = block.date;
        btn.className = `w-full p-3 text-left rounded-lg border ${idx===0?'bg-blue-600 text-white':'border-gray-300 hover:bg-blue-50'}`;
        btn.addEventListener('click', () => selectDate(block.date, btn));
        dateContainer.appendChild(btn);
    });

    // Selected Date/Time
    let selectedDate = dateBlocks[0].date;
    let selectedTime = null;

    // Select Date
    function selectDate(date, btn) {
        selectedDate = date;
        selectedTime = null;
        document.getElementById('selectedDate').value = date;
        document.getElementById('selectedTime').value = '';
        document.querySelectorAll('#dateBlocks button').forEach(b => b.classList.remove('bg-blue-600', 'text-white'));
        btn.classList.add('bg-blue-600', 'text-white');
        populateTimeSlots(date);
        updateBookButton();
    }

    // Populate Time Slots
    function populateTimeSlots(date) {
        const container = document.getElementById('timeSlots');
        container.innerHTML = '';
        const now = new Date();
        const booked = bookedSlots[date] || [];

        timeSlots.forEach(time => {
            const [h, m] = time.split(':');
            const slotTime = new Date(date);
            slotTime.setHours(parseInt(h), parseInt(m), 0, 0);

            const isPast = slotTime <= now;
            const isBooked = booked.includes(time);

            const btn = document.createElement('button');
            btn.type = 'button';
            btn.textContent = formatAMPM(time);
            btn.disabled = isPast || isBooked;
            btn.className = `p-2 rounded border ${isPast||isBooked?'bg-red-100 text-red-600 cursor-not-allowed':'hover:bg-blue-100'}`;
            if (!isPast && !isBooked) {
                btn.addEventListener('click', () => selectTime(time, btn));
            }
            container.appendChild(btn);
        });
    }

    // Format time
    function formatAMPM(time) {
        let [h, m] = time.split(':');
        h = parseInt(h);
        const ampm = h >= 12 ? 'PM' : 'AM';
        h = h % 12 || 12;
        return `${h}:${m} ${ampm}`;
    }

    // Select time
    function selectTime(time, btn) {
        selectedTime = time;
        document.getElementById('selectedTime').value = time;
        document.querySelectorAll('#timeSlots button').forEach(b => b.classList.remove('bg-blue-600', 'text-white'));
        btn.classList.add('bg-blue-600', 'text-white');
        updateBookButton();
    }

    // Update book button
    function updateBookButton() {
        const date = document.getElementById('selectedDate').value;
        const time = document.getElementById('selectedTime').value;
        const reason = document.getElementById('visitReason').value;
        document.getElementById('bookBtn').disabled = !(date && time && reason);
    }

    // Init
    selectDate(dateBlocks[0].date, dateContainer.children[0]);
    document.getElementById('visitReason').addEventListener('change', updateBookButton);

    /// Form submit
    document.getElementById('appointmentForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        const btn = document.getElementById('bookBtn');
        const origText = btn.textContent;
        btn.disabled = true;
        btn.textContent = 'Processing...';

        fetch('assets/php/actions.php?book_appointment', {
                method: 'POST',
                body: formData
            })
            .then(res => {
                if (!res.ok) {
                    throw new Error('Network response was not ok');
                }
                return res.json();
            })
            .then(data => {
                if (data.success) {
                    const toast = document.getElementById('successToast');
                    toast.classList.remove('hidden');
                    setTimeout(() => toast.classList.add('hidden'), 3000);
                    setTimeout(() => window.location.href = '?bookedappointment', 2000);
                } else {
                    alert(data.message || 'Error booking appointment');
                }
            })
            .catch(err => {
                console.error('Fetch Error:', err);
                alert('Error booking appointment. Please try again.');
            })
            .finally(() => {
                btn.disabled = false;
                btn.textContent = origText;
            });
    });
</script>