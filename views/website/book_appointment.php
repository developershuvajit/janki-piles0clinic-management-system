<?php 
include VIEWS_PATH . '/layout/header.php'; 
?>

<!-- Booking Container -->
<div class="row justify-content-center py-5">
    <div class="col-md-8">
        <div class="card p-4 border-0 shadow-lg">
            
            <!-- Header -->
            <div class="text-center mb-4">
                <i class="bi bi-calendar-plus text-success display-4"></i>
                <h3 class="fw-bold mt-2 text-slate">Book Appointment Online</h3>
                <p class="text-muted small">Verify your email to explore doctor availability schedules and book slots.</p>
            </div>

            <!-- Alerts Container -->
            <div id="booking-alert" class="alert d-none"></div>

            <form action="<?= site_url('/appointments/book/submit') ?>" method="POST" id="booking-form">
                <?= csrf_field() ?>

                <!-- Step 1: Email Verification -->
                <div class="card bg-light border-0 p-3 mb-4" id="otp-section">
                    <h6 class="fw-bold text-slate mb-2">Step 1: Contact Verification</h6>
                    <div class="row g-2 align-items-end">
                        <div class="col-md-8">
                            <label for="email" class="form-label small fw-semibold">Email Address <span class="text-danger">*</span></label>
                            <input type="email" class="form-control form-control-sm" id="email" name="email" required placeholder="e.g. name@email.com">
                        </div>
                        <div class="col-md-4">
                            <button type="button" class="btn btn-success btn-sm w-100 py-2 fw-semibold" id="btn-send-otp">
                                Send OTP Code
                            </button>
                        </div>
                    </div>

                    <!-- OTP Code Input (hidden by default) -->
                    <div class="mt-3 d-none" id="otp-input-wrapper">
                        <label for="otp_code" class="form-label small fw-semibold text-success">Enter 6-Digit OTP Sent to Email <span class="text-danger">*</span></label>
                        <input type="text" class="form-control form-control-sm text-center fw-bold fs-5" id="otp_code" name="otp_code" maxlength="6" placeholder="000000" style="letter-spacing: 5px;">
                    </div>
                </div>

                <!-- Step 2: Patient Demographics & Slot (hidden by default until OTP is sent) -->
                <div id="booking-details-wrapper" class="d-none">
                    <h6 class="fw-bold text-slate mb-3"><i class="bi bi-person-badge text-success me-1"></i>Step 2: Patient Registration Info</h6>
                    
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label for="name" class="form-label small fw-semibold">Full Patient Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control form-control-sm" id="name" name="name" required placeholder="Enter full name">
                        </div>
                        <div class="col-md-6">
                            <label for="phone" class="form-label small fw-semibold">Contact Phone Number <span class="text-danger">*</span></label>
                            <input type="text" class="form-control form-control-sm" id="phone" name="phone" required placeholder="e.g. +91 98765 43210">
                        </div>
                        <div class="col-md-6">
                            <label for="gender" class="form-label small fw-semibold">Gender <span class="text-danger">*</span></label>
                            <select class="form-control form-control-sm form-select" id="gender" name="gender" required>
                                <option value="male">Male</option>
                                <option value="female">Female</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="dob" class="form-label small fw-semibold">Date of Birth <span class="text-danger">*</span></label>
                            <input type="date" class="form-control form-control-sm" id="dob" name="dob" required max="<?= date('Y-m-d') ?>">
                        </div>
                        <div class="col-md-12">
                            <label for="address" class="form-label small fw-semibold">Full Address <span class="text-danger">*</span></label>
                            <input type="text" class="form-control form-control-sm" id="address" name="address" required placeholder="Street name, City, Pincode">
                        </div>
                    </div>

                    <h6 class="fw-bold text-slate mb-3"><i class="bi bi-calendar-check text-success me-1"></i>Step 3: Select Schedule & Slots</h6>
                    
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label for="branch_id" class="form-label small fw-semibold">Select Clinic Branch</label>
                            <select class="form-control form-control-sm form-select" id="branch_id" name="branch_id">
                                <?php foreach ($branches as $br): ?>
                                    <option value="<?= $br['id'] ?>"><?= esc($br['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="doctor_id" class="form-label small fw-semibold">Select Doctor <span class="text-danger">*</span></label>
                            <select class="form-control form-control-sm form-select" id="doctor_id" name="doctor_id" required>
                                <option value="" disabled selected>Choose Doctor</option>
                                <?php foreach ($doctors as $doc): ?>
                                    <option value="<?= $doc['id'] ?>">Dr. <?= esc($doc['username']) ?> (<?= esc($doc['branch_name'] ?? 'General') ?>)</option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-12">
                            <label for="date" class="form-label small fw-semibold">Select Visit Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control form-control-sm" id="date" name="date" required min="<?= date('Y-m-d') ?>">
                        </div>
                    </div>

                    <!-- Available Slots Dynamic Rendering Area -->
                    <div class="mb-4">
                        <label class="form-label small fw-semibold d-block">Available Time Slots</label>
                        <div class="alert alert-light border small text-muted text-center py-3" id="slots-placeholder">
                            Please select a doctor and date to view available time slots.
                        </div>
                        <div class="row g-2" id="slots-container">
                            <!-- JS will inject slots radio cards here -->
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 py-2.5 fw-semibold shadow-sm">
                        <i class="bi bi-calendar-check-fill me-1"></i> Confirm Appointment Booking
                    </button>
                </div>
            </form>
            
            <div class="text-center mt-3 pt-3 border-top">
                <a href="<?= site_url('/login') ?>" class="text-decoration-none small text-success fw-semibold">
                    <i class="bi bi-arrow-left me-1"></i> Staff Console Login
                </a>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript Ajax Handlers -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const btnSendOtp = document.getElementById('btn-send-otp');
    const otpInputWrapper = document.getElementById('otp-input-wrapper');
    const bookingDetailsWrapper = document.getElementById('booking-details-wrapper');
    const emailInput = document.getElementById('email');
    const alertDiv = document.getElementById('booking-alert');
    
    const doctorSelect = document.getElementById('doctor_id');
    const dateInput = document.getElementById('date');
    const slotsContainer = document.getElementById('slots-container');
    const slotsPlaceholder = document.getElementById('slots-placeholder');

    function showAlert(msg, isSuccess = false) {
        alertDiv.className = `alert ${isSuccess ? 'alert-success' : 'alert-danger'} py-2 small`;
        alertDiv.innerText = msg;
    }

    // 1. Send OTP Code AJAX
    btnSendOtp.addEventListener('click', function() {
        const email = emailInput.value.trim();
        if (!email || !email.includes('@')) {
            showAlert('Please enter a valid email address.');
            return;
        }

        btnSendOtp.disabled = true;
        btnSendOtp.innerText = 'Sending...';

        const formData = new FormData();
        formData.append('email', email);

        fetch('<?= site_url("/appointments/book/otp") ?>', {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showAlert(data.message, true);
                otpInputWrapper.classList.remove('d-none');
                bookingDetailsWrapper.classList.remove('d-none');
                btnSendOtp.innerText = 'Resend Code';
                btnSendOtp.disabled = false;
            } else {
                showAlert(data.message || 'OTP dispatch failed.');
                btnSendOtp.disabled = false;
                btnSendOtp.innerText = 'Send OTP Code';
            }
        })
        .catch(err => {
            showAlert('Failed connecting to OTP services.');
            btnSendOtp.disabled = false;
            btnSendOtp.innerText = 'Send OTP Code';
        });
    });

    // 2. Fetch Time Slots AJAX
    function fetchAvailableSlots() {
        const doctorId = doctorSelect.value;
        const date = dateInput.value;

        if (!doctorId || !date) {
            return;
        }

        slotsPlaceholder.innerText = 'Checking availability slots...';
        slotsPlaceholder.classList.remove('d-none');
        slotsContainer.innerHTML = '';

        fetch(`<?= site_url("/admin/appointments/slots") ?>?doctor_id=${doctorId}&date=${date}`)
        .then(res => res.json())
        .then(data => {
            if (data.success && data.slots.length > 0) {
                slotsPlaceholder.classList.add('d-none');
                data.slots.forEach(slot => {
                    const col = document.createElement('div');
                    col.className = 'col-md-3 col-sm-4 col-6';
                    
                    const disabledAttr = slot.booked ? 'disabled' : '';
                    const labelClass = slot.booked ? 'btn-outline-secondary opacity-50' : 'btn-outline-primary';
                    const activeText = slot.booked ? 'Booked' : slot.time_formatted;

                    col.innerHTML = `
                        <input type="radio" class="btn-check" name="time_slot" id="slot-${slot.time}" value="${slot.time}" required ${disabledAttr}>
                        <label class="btn btn-sm ${labelClass} w-100 py-2.5 fw-semibold" for="slot-${slot.time}">
                            <i class="bi bi-clock me-1"></i> ${activeText}
                        </label>
                    `;
                    slotsContainer.appendChild(col);
                });
            } else {
                slotsPlaceholder.innerText = 'No consultation shift slots configured for selected doctor on this day.';
                slotsPlaceholder.classList.remove('d-none');
            }
        })
        .catch(err => {
            slotsPlaceholder.innerText = 'Unable to check availability slots.';
        });
    }

    doctorSelect.addEventListener('change', fetchAvailableSlots);
    dateInput.addEventListener('change', fetchAvailableSlots);
});
</script>

<?php include VIEWS_PATH . '/layout/footer.php'; ?>
