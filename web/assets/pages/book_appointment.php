<?php
include_once 'assets/php/function.php';

// Get doctor by ID
$doctor_id = isset($_GET['bookappointment']) ? (int)$_GET['bookappointment'] : 0;
$doctor = isDoctorAvailable($doctor_id);
$booked_appointments = getBookedAppointments($doctor_id); // Returns array like ['2025-08-28'=>['09:00','09:30'],...]

if (!$doctor) {
?>
    <div class="flex flex-col items-center justify-center min-h-screen bg-gray-50">
        <div class="text-center p-8 bg-white rounded-xl shadow-lg max-w-md">
            <div class="text-6xl font-bold text-red-600 mb-4">404</div>
            <h1 class="text-2xl font-bold text-gray-800 mb-2">Doctor Not Found</h1>
            <p class="text-gray-600 mb-6">Sorry, we couldn't find the doctor you're looking for.</p>
            <a href="?" class="inline-block px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                Back to Search
            </a>
        </div>
    </div>
<?php
    exit;
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
        --rating: #f59e0b;
        --warning: #f59e0b;
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
        padding: 20px;
    }

    /* Header Styles */
    .header {
        background-color: white;
        box-shadow: 0 2px 10px var(--shadow);
        position: sticky;
        top: 0;
        z-index: 100;
        padding: 15px 0;
    }

    .header-content {
        display: flex;
        justify-content: space-between;
        align-items: center;
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 20px;
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

    /* Main Content */
    .main-content {
        padding: 30px 0;
    }

    /* Doctor Details Section */
    .doctor-details {
        background-color: white;
        border-radius: 12px;
        box-shadow: 0 5px 20px var(--shadow);
        margin-bottom: 30px;
        overflow: hidden;
    }

    .doctor-header {
        background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%);
        padding: 30px;
        color: white;
    }

    .doctor-info {
        display: flex;
        align-items: center;
        gap: 30px;
    }

    .doctor-avatar {
        width: 120px;
        height: 120px;
        border-radius: 50%;
        overflow: hidden;
        border: 5px solid white;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
    }

    .doctor-avatar img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .doctor-details-content h1 {
        font-size: 32px;
        margin-bottom: 10px;
    }

    .doctor-meta {
        display: flex;
        flex-wrap: wrap;
        gap: 20px;
        margin-bottom: 15px;
    }

    .meta-item {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 16px;
    }

    .doctor-rating {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .stars {
        display: flex;
        gap: 3px;
    }

    .stars i {
        color: var(--rating);
    }

    .verified-badge {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        background-color: #3182ce;
        color: white;
        padding: 5px 12px;
        border-radius: 20px;
        font-size: 14px;
        font-weight: 500;
        margin-left: 15px;
    }

    .doctor-body {
        padding: 30px;
    }

    .doctor-description {
        color: var(--secondary);
        line-height: 1.8;
        margin-bottom: 25px;
    }

    .doctor-highlights {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 20px;
        margin-bottom: 25px;
    }

    .highlight-item {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 15px;
        background-color: var(--light);
        border-radius: 8px;
    }

    .highlight-item i {
        font-size: 24px;
        color: var(--primary-light);
    }

    /* Booking Section */
    .booking-container {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 30px;
    }

    .booking-form-container,
    .booking-summary-container {
        background-color: white;
        border-radius: 12px;
        box-shadow: 0 5px 20px var(--shadow);
        padding: 30px;
    }

    .section-title {
        font-size: 24px;
        margin-bottom: 25px;
        color: var(--dark);
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .section-title i {
        color: var(--primary-light);
    }

    .form-group {
        margin-bottom: 20px;
    }

    .form-group label {
        display: block;
        margin-bottom: 8px;
        font-weight: 500;
        color: var(--dark);
    }

    .form-control {
        width: 100%;
        padding: 12px 15px;
        border: 1px solid var(--border);
        border-radius: 6px;
        font-size: 16px;
        transition: border 0.3s;
    }

    .form-control:focus {
        outline: none;
        border-color: var(--primary);
        box-shadow: 0 0 0 3px rgba(79, 209, 197, 0.2);
    }

    textarea.form-control {
        min-height: 120px;
        resize: vertical;
    }

    .date-selector {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 10px;
        margin-bottom: 20px;
    }

    .date-option {
        padding: 15px;
        border: 1px solid var(--border);
        border-radius: 8px;
        text-align: center;
        cursor: pointer;
        transition: all 0.3s;
        background-color: white;
    }

    .date-option:hover {
        border-color: var(--primary);
        background-color: rgba(79, 209, 197, 0.05);
    }

    .date-option.selected {
        background-color: var(--primary);
        color: white;
        border-color: var(--primary);
    }

    .date-option .day {
        font-weight: 600;
        font-size: 18px;
    }

    .date-option .date {
        font-size: 14px;
        opacity: 0.8;
    }

    .time-slots {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 10px;
        margin-top: 10px;
    }

    .time-slot {
        padding: 12px;
        border: 1px solid var(--border);
        border-radius: 6px;
        text-align: center;
        cursor: pointer;
        transition: all 0.3s;
        font-weight: 500;
    }

    .time-slot:hover {
        border-color: var(--primary);
        background-color: rgba(79, 209, 197, 0.05);
    }

    .time-slot.selected {
        background-color: var(--primary);
        color: white;
        border-color: var(--primary);
    }

    .time-slot.disabled {
        opacity: 0.5;
        cursor: not-allowed;
        background-color: #f1f5f9;
    }

    /* Booking Summary */
    .summary-row {
        display: flex;
        justify-content: space-between;
        padding: 12px 0;
        border-bottom: 1px solid var(--border);
    }

    .summary-row:last-child {
        border-bottom: none;
        font-weight: 600;
        font-size: 18px;
        margin-top: 10px;
        padding-top: 15px;
        border-top: 2px solid var(--border);
    }

    .summary-label {
        color: var(--secondary);
    }

    .summary-value {
        font-weight: 500;
        color: var(--dark);
    }

    .fee-breakdown {
        margin-top: 20px;
        padding: 15px;
        background-color: var(--light);
        border-radius: 8px;
    }

    .fee-breakdown h4 {
        font-size: 16px;
        margin-bottom: 10px;
        color: var(--dark);
    }

    .fee-item {
        display: flex;
        justify-content: space-between;
        margin-bottom: 8px;
        font-size: 14px;
    }

    .fee-item:last-child {
        margin-bottom: 0;
        font-weight: 500;
        padding-top: 8px;
        border-top: 1px solid var(--border);
        margin-top: 8px;
    }

    /* Cancellation Policy */
    .cancellation-policy {
        margin-top: 25px;
        padding: 15px;
        background-color: #fffbeb;
        border-left: 4px solid var(--warning);
        border-radius: 6px;
    }

    .cancellation-policy h4 {
        font-size: 16px;
        margin-bottom: 10px;
        color: var(--dark);
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .cancellation-policy p {
        color: var(--secondary);
        font-size: 14px;
        line-height: 1.6;
    }

    /* Buttons */
    .btn {
        padding: 12px 20px;
        border-radius: 6px;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.3s;
        border: none;
        text-decoration: none;
        display: inline-block;
        font-size: 16px;
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

    .btn-success {
        background-color: var(--success);
        color: white;
    }

    .btn-success:hover {
        background-color: #2f855a;
    }

    .btn-danger {
        background-color: var(--danger);
        color: white;
    }

    .btn-danger:hover {
        background-color: #c53030;
    }

    .btn-block {
        display: block;
        width: 100%;
    }

    /* Success Message */
    .success-message {
        background-color: var(--success);
        color: white;
        padding: 15px 20px;
        border-radius: 6px;
        margin-bottom: 20px;
        display: none;
    }

    .success-message.show {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    /* Error Message */
    .error-message {
        background-color: var(--danger);
        color: white;
        padding: 15px 20px;
        border-radius: 6px;
        margin-bottom: 20px;
        display: none;
    }

    .error-message.show {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    /* Form Actions */
    .form-actions {
        display: flex;
        justify-content: space-between;
        margin-top: 30px;
    }

    /* Toast Notification */
    .toast {
        position: fixed;
        top: 20px;
        right: 20px;
        padding: 15px 20px;
        border-radius: 8px;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        z-index: 1000;
        display: flex;
        align-items: center;
        gap: 10px;
        transform: translateX(150%);
        transition: transform 0.3s ease-in-out;
    }

    .toast.show {
        transform: translateX(0);
    }

    .toast-success {
        background-color: var(--success);
        color: white;
    }

    .toast-error {
        background-color: var(--danger);
        color: white;
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
        max-width: 1200px;
        margin: 0 auto 30px;
        padding: 0 20px;
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
        max-width: 1200px;
        margin: 0 auto;
        padding: 20px;
    }

    /* Responsive */
    @media (max-width: 992px) {
        .booking-container {
            grid-template-columns: 1fr;
        }

        .doctor-info {
            flex-direction: column;
            text-align: center;
        }

        .doctor-meta {
            justify-content: center;
        }
    }

    @media (max-width: 768px) {
        .header-content {
            flex-direction: column;
            gap: 15px;
        }

        .doctor-highlights {
            grid-template-columns: 1fr;
        }

        .date-selector {
            grid-template-columns: 1fr;
        }

        .time-slots {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    @media (max-width: 576px) {
        .doctor-header {
            padding: 20px;
        }

        .doctor-body,
        .booking-form-container,
        .booking-summary-container {
            padding: 20px;
        }

        .doctor-avatar {
            width: 100px;
            height: 100px;
        }

        .doctor-details-content h1 {
            font-size: 24px;
        }

        .time-slots {
            grid-template-columns: 1fr;
        }
    }
</style>


<!-- Main Content -->
<section class="main-content">
    <div class="container">
        <!-- Doctor Details Section -->
        <div class="doctor-details">
            <div class="doctor-header">
                <div class="doctor-info">
                    <div class="doctor-avatar">
                        <img src="assets/img/profile/<?= $doctor['profile_pic'] ?>" alt="Dr. <?= $doctor['first_name'] . ' ' . $doctor['last_name'] ?>">
                    </div>
                    <div class="doctor-details-content">
                        <h1>Dr. <?= $doctor['first_name'] . ' ' . $doctor['last_name'] ?></h1>
                        <div class="doctor-meta">
                            <div class="meta-item">
                                <i class="fas fa-user-md"></i>
                                <span><?= $doctor['doctor_type'] ?></span>
                            </div>
                            <div class="meta-item">
                                <i class="fas fa-hospital"></i>
                                <span><?= $doctor['hospital_name'] ?></span>
                            </div>
                            <div class="meta-item">
                                <i class="fas fa-map-marker-alt"></i>
                                <span><?= $doctor['hospital_address'] ?>, <?= $doctor['hospital_city'] ?></span>
                            </div>
                            <div class="meta-item">
                                <i class="fas fa-graduation-cap"></i>
                                <span><?= $doctor['years_of_experience'] ?> years experience</span>
                            </div>
                        </div>
                        <div class="doctor-rating">
                            <div class="stars">
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star-half-alt"></i>
                            </div>
                            <span>4.7 (<?= $doctor['reviews_count'] ?? 0 ?> reviews)</span>
                            <div class="verified-badge">
                                <i class="fas fa-check"></i> Verified
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="doctor-body">
                <div class="doctor-description">
                    <p><?= $doctor['bio'] ?></p>
                </div>

                <div class="doctor-highlights">
                    <div class="highlight-item">
                        <i class="fas fa-award"></i>
                        <div>
                            <strong>Top Doctor</strong>
                            <p>Recognized as Top Doctor</p>
                        </div>
                    </div>
                    <div class="highlight-item">
                        <i class="fas fa-user-friends"></i>
                        <div>
                            <strong><?= $doctor['patients_count'] ?? 0 ?>+ Patients</strong>
                            <p>Successfully treated</p>
                        </div>
                    </div>
                    <div class="highlight-item">
                        <i class="fas fa-clock"></i>
                        <div>
                            <strong>Quick Appointments</strong>
                            <p>Usually available within 2 days</p>
                        </div>
                    </div>
                    <div class="highlight-item">
                        <i class="fas fa-comment-medical"></i>
                        <div>
                            <strong>Excellent Reviews</strong>
                            <p>98% patient satisfaction</p>
                        </div>
                    </div>
                </div>

                <!-- Doctor Verification Details -->
                <div class="verification-details" style="margin-top: 30px; padding: 20px; background-color: #f0f9ff; border-radius: 8px; border-left: 4px solid #0ea5e9;">
                    <h3 style="color: #0c4a6e; margin-bottom: 15px; display: flex; align-items: center; gap: 8px;">
                        <i class="fas fa-certificate"></i> Professional Verification
                    </h3>
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 15px;">
                        <div>
                            <strong style="color: #0c4a6e;">NMC Number:</strong>
                            <p><?= $doctor['nmc_number'] ?></p>
                        </div>
                        <div>
                            <strong style="color: #0c4a6e;">Registration Number:</strong>
                            <p><?= $doctor['registration_number'] ?></p>
                        </div>
                        <div>
                            <strong style="color: #0c4a6e;">Specialty:</strong>
                            <p><?= $doctor['specialty'] ?></p>
                        </div>
                        <div>
                            <strong style="color: #0c4a6e;">Department:</strong>
                            <p><?= $doctor['department'] ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Booking Section -->
        <div class="booking-container">
            <!-- Booking Form -->
            <div class="booking-form-container">
                <h2 class="section-title">
                    <i class="fas fa-calendar-alt"></i> Book Appointment
                </h2>

                <div class="success-message" id="successMessage">
                    <i class="fas fa-check-circle"></i>
                    <span>Your appointment has been booked successfully! You will receive a confirmation email shortly.</span>
                </div>

                <div class="error-message" id="errorMessage">
                    <i class="fas fa-exclamation-circle"></i>
                    <span id="errorText">There was an error with your booking. Please try again.</span>
                </div>

                <form id="bookingForm">
                    <input type="hidden" id="doctorId" value="<?= $doctor_id ?>">

                    <div class="form-group">
                        <label>Select Date</label>
                        <div class="date-selector" id="dateSelector">
                            <!-- Date options will be populated by JavaScript -->
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Available Time Slots</label>
                        <div class="time-slots" id="timeSlots">
                            <!-- Time slots will be populated by JavaScript -->
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="appointmentType">Appointment Type</label>
                        <select class="form-control" id="appointmentType" required>
                            <option value="">Select appointment type</option>
                            <option value="in-person">In-Person Visit</option>
                            <option value="video">Video Consultation</option>
                            <option value="phone">Phone Consultation</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="reasonForVisit">Reason for Visit</label>
                        <select class="form-control" id="reasonForVisit" required>
                            <option value="">Select a reason</option>
                            <option value="General Consultation">General Consultation</option>
                            <option value="Follow-up Visit">Follow-up Visit</option>
                            <option value="Routine Check-up">Routine Check-up</option>
                            <option value="Vaccination">Vaccination</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="additionalNotes">Additional Notes</label>
                        <textarea class="form-control" id="additionalNotes" rows="4" placeholder="Please describe your symptoms or reason for consultation"></textarea>
                    </div>

                    <!-- Cancellation Policy -->
                    <div class="cancellation-policy">
                        <h4><i class="fas fa-exclamation-triangle"></i> Cancellation Policy</h4>
                        <p>Please note that if you cancel your appointment less than 1 hour before the scheduled time, you will not be eligible for a refund. Cancellations made at least 1 hour before the appointment will receive a full refund.</p>
                    </div>

                    <div class="form-actions">
                        <button type="button" class="btn btn-outline" onclick="window.location.href='?'">
                            <i class="fas fa-arrow-left"></i> Back to Search
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-lock"></i> Book Appointment
                        </button>
                    </div>
                </form>
            </div>

            <!-- Booking Summary -->
            <div class="booking-summary-container">
                <h2 class="section-title">
                    <i class="fas fa-file-invoice-dollar"></i> Booking Summary
                </h2>

                <div class="summary-row">
                    <span class="summary-label">Doctor:</span>
                    <span class="summary-value">Dr. <?= $doctor['first_name'] . ' ' . $doctor['last_name'] ?></span>
                </div>
                <div class="summary-row">
                    <span class="summary-label">Specialty:</span>
                    <span class="summary-value"><?= $doctor['specialty'] ?></span>
                </div>
                <div class="summary-row">
                    <span class="summary-label">Hospital:</span>
                    <span class="summary-value"><?= $doctor['hospital_name'] ?></span>
                </div>
                <div class="summary-row">
                    <span class="summary-label">Location:</span>
                    <span class="summary-value"><?= $doctor['hospital_address'] ?>, <?= $doctor['hospital_city'] ?></span>
                </div>
                <div class="summary-row">
                    <span class="summary-label">Date:</span>
                    <span class="summary-value" id="summaryDate">-</span>
                </div>
                <div class="summary-row">
                    <span class="summary-label">Time:</span>
                    <span class="summary-value" id="summaryTime">-</span>
                </div>
                <div class="summary-row">
                    <span class="summary-label">Appointment Type:</span>
                    <span class="summary-value" id="summaryType">-</span>
                </div>

                <div class="fee-breakdown">
                    <h4>Fee Breakdown</h4>
                    <div class="fee-item">
                        <span>Consultation Fee</span>
                        <span id="consultationFee">NPR<?= $doctor['consultation_fee'] ?? 100 ?></span>
                    </div>
                    <div class="fee-item">
                        <span>Platform Fee</span>
                        <span>NPR10.00</span>
                    </div>
                    <div class="fee-item">
                        <span>Tax</span>
                        <span id="taxAmount">NPR<?= number_format((($doctor['consultation_fee'] ?? 100) + 10) * 0.0825, 2) ?></span>
                    </div>
                    <div class="fee-item">
                        <span>Total Amount</span>
                        <span id="totalAmount">NPR<?= number_format((($doctor['consultation_fee'] ?? 100) + 10) * 1.0825, 2) ?></span>
                    </div>
                </div>

                <div class="cancellation-policy">
                    <h4><i class="fas fa-info-circle"></i> Important Information</h4>
                    <ul style="padding-left: 20px; margin-top: 10px;">
                        <li>Please arrive 15 minutes before your appointment time</li>
                        <li>Bring a valid ID and your insurance card if applicable</li>
                        <li>Cancellation policy: No refund if cancelled less than 1 hour before</li>
                        <li>For any questions, call our support at (415) 555-0123</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Toast Notification -->
<div id="toast" class="toast">
    <i class="fas fa-check-circle"></i>
    <span id="toastMessage">Appointment booked successfully!</span>
</div>

<script>
    // Global variables
    let selectedDate = null;
    let selectedTime = null;
    const bookedSlots = <?= json_encode($booked_appointments); ?>;
    const consultationFee = <?= $doctor['consultation_fee'] ?? 100 ?>;
    const platformFee = 10.00;
    const taxRate = 0.0825; // 8.25% tax

    // Time slots
    const timeSlots = [
        '09:00', '09:30', '10:00', '10:30', '11:00', '11:30',
        '14:00', '14:30', '15:00', '15:30', '16:00', '16:30',
        '18:00', '18:30', '19:00', '19:30'
    ];

    // Generate date options (next 7 days)
    function generateDateOptions() {
        const dateSelector = document.getElementById('dateSelector');
        dateSelector.innerHTML = '';

        const today = new Date();

        for (let i = 0; i < 7; i++) {
            const date = new Date();
            date.setDate(today.getDate() + i);

            const dateOption = document.createElement('div');
            dateOption.className = 'date-option';
            dateOption.dataset.date = date.toISOString().split('T')[0];

            const dayName = date.toLocaleDateString('en-US', {
                weekday: 'short'
            });
            const dayNum = date.getDate();
            const monthName = date.toLocaleDateString('en-US', {
                month: 'short'
            });

            dateOption.innerHTML = `
                    <div class="day">${dayName}</div>
                    <div class="date">${dayNum} ${monthName}</div>
                `;

            dateOption.addEventListener('click', function() {
                selectDate(this.dataset.date, this);
            });

            dateSelector.appendChild(dateOption);

            // Select today by default
            if (i === 0) {
                selectDate(dateOption.dataset.date, dateOption);
            }
        }
    }

    // Select date
    function selectDate(date, element) {
        // Remove selected class from all date options
        document.querySelectorAll('.date-option').forEach(option => {
            option.classList.remove('selected');
        });

        // Add selected class to clicked option
        element.classList.add('selected');
        selectedDate = date;

        // Update summary
        const formattedDate = new Date(date).toLocaleDateString('en-US', {
            weekday: 'long',
            year: 'numeric',
            month: 'long',
            day: 'numeric'
        });
        document.getElementById('summaryDate').textContent = formattedDate;

        // Populate time slots
        populateTimeSlots(date);
    }

    // Populate time slots
    function populateTimeSlots(date) {
        const timeSlotsContainer = document.getElementById('timeSlots');
        timeSlotsContainer.innerHTML = '';

        const now = new Date();
        const isToday = date === new Date().toISOString().split('T')[0];
        const booked = bookedSlots[date] || [];

        timeSlots.forEach(time => {
            const [hours, minutes] = time.split(':');
            const slotTime = new Date(date);
            slotTime.setHours(parseInt(hours), parseInt(minutes), 0, 0);

            const isPast = isToday && slotTime <= now;
            const isBooked = booked.includes(time);

            const timeSlot = document.createElement('div');
            timeSlot.className = 'time-slot';

            if (isPast || isBooked) {
                timeSlot.classList.add('disabled');
            }

            // Format time to AM/PM
            const formattedTime = formatAMPM(time);
            timeSlot.textContent = formattedTime;

            if (!isPast && !isBooked) {
                timeSlot.addEventListener('click', function() {
                    selectTime(time, this);
                });
            }

            timeSlotsContainer.appendChild(timeSlot);
        });
    }

    // Format time to AM/PM
    function formatAMPM(time) {
        let [hours, minutes] = time.split(':');
        hours = parseInt(hours);
        const ampm = hours >= 12 ? 'PM' : 'AM';
        hours = hours % 12 || 12;
        return `${hours}:${minutes} ${ampm}`;
    }

    // Select time
    function selectTime(time, element) {
        // Remove selected class from all time slots
        document.querySelectorAll('.time-slot').forEach(slot => {
            slot.classList.remove('selected');
        });

        // Add selected class to clicked slot
        element.classList.add('selected');
        selectedTime = time;

        // Update summary
        document.getElementById('summaryTime').textContent = formatAMPM(time);
    }

    // Update booking summary when appointment type changes
    document.getElementById('appointmentType').addEventListener('change', function() {
        const type = this.value;
        if (type) {
            const typeText = type.charAt(0).toUpperCase() + type.slice(1).replace('-', ' ');
            document.getElementById('summaryType').textContent = typeText;
        } else {
            document.getElementById('summaryType').textContent = '-';
        }
    });

    // Initialize date options
    generateDateOptions();

    // Form submission
    document.getElementById('bookingForm').addEventListener('submit', function(e) {
        e.preventDefault();

        // Validate selections
        if (!selectedDate) {
            showError('Please select a date for your appointment');
            return;
        }

        if (!selectedTime) {
            showError('Please select a time slot for your appointment');
            return;
        }

        const appointmentType = document.getElementById('appointmentType').value;
        if (!appointmentType) {
            showError('Please select an appointment type');
            return;
        }

        const reasonForVisit = document.getElementById('reasonForVisit').value;
        if (!reasonForVisit) {
            showError('Please select a reason for your visit');
            return;
        }

        // Prepare form data
        const formData = new FormData();
        formData.append('doctor_id', document.getElementById('doctorId').value);
        formData.append('date', selectedDate);
        formData.append('time', selectedTime);
        formData.append('reason', reasonForVisit);
        formData.append('desc', document.getElementById('additionalNotes').value);

        // Show processing state
        const submitButton = this.querySelector('button[type="submit"]');
        const originalText = submitButton.innerHTML;
        submitButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Booking Appointment...';
        submitButton.disabled = true;

        // Send booking request
        fetch('assets/php/actions.php?book_appointment', {
                method: 'POST',
                body: formData
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    // Show success message
                    showToast('Appointment booked successfully!', 'success');

                    // Redirect after a short delay
                    setTimeout(() => {
                        window.location.href = '?bookedappointment';
                    }, 2000);
                } else {
                    showError(data.message || 'Error booking appointment');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showError('Error booking appointment. Please try again.');
            })
            .finally(() => {
                // Reset button state
                submitButton.innerHTML = originalText;
                submitButton.disabled = false;
            });
    });

    // Show error message
    function showError(message) {
        const errorElement = document.getElementById('errorMessage');
        const errorText = document.getElementById('errorText');

        errorText.textContent = message;
        errorElement.classList.add('show');

        // Scroll to top of form
        document.querySelector('.booking-form-container').scrollTo({
            top: 0,
            behavior: 'smooth'
        });

        // Hide after 5 seconds
        setTimeout(() => {
            errorElement.classList.remove('show');
        }, 5000);
    }

    // Show toast notification
    function showToast(message, type = 'success') {
        const toast = document.getElementById('toast');
        const toastMessage = document.getElementById('toastMessage');

        toastMessage.textContent = message;
        toast.className = 'toast toast-' + type;
        toast.classList.add('show');

        // Hide after 3 seconds
        setTimeout(() => {
            toast.classList.remove('show');
        }, 3000);
    }
</script>