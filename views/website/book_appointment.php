<?php 
include VIEWS_PATH . '/layout/header.php'; 
?>

<style>
    .slot-btn {
        transition: all 0.15s;
        min-width: 80px;
    }
    .slot-btn:hover:not(.booked) {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }
    .slot-btn.selected {
        background: #0f7b4a !important;
        color: #fff !important;
        border-color: #0f7b4a !important;
    }
    .slot-btn.booked {
        opacity: 0.5;
        cursor: not-allowed;
    }
    .btn-emerald {
        background: #0f7b4a;
        color: #fff;
        border: none;
        transition: all 0.15s;
    }
    .btn-emerald:hover {
        background: #0b6e44;
        color: #fff;
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(15,123,74,0.2);
    }
    .success-animation {
        animation: fadeInUp 0.5s ease;
    }
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
</style>

<div class="row justify-content-center py-4">
    <div class="col-md-8 col-lg-7">
        <div class="card border-0 shadow-lg rounded-4 p-4">
            
            <!-- Header -->
            <div class="text-center mb-4">
                <i class="bi bi-calendar-plus text-success" style="font-size:2.5rem;"></i>
                <h3 class="fw-bold mt-2 text-slate">Book Appointment Online</h3>
                <p class="text-muted small">Fill in your details and select a convenient time slot.</p>
            </div>

            <!-- Alerts -->
            <div id="booking-alert" class="alert d-none"></div>
            
            <?php if ($flashError = \App\Helpers\Session::getFlash('error')): ?>
                <div class="alert alert-danger py-2 small"><?= esc($flashError) ?></div>
            <?php endif; ?>

            <?php if ($flashSuccess = \App\Helpers\Session::getFlash('success')): ?>
                <div class="alert alert-success py-2 small success-animation">
                    <i class="bi bi-check-circle-fill me-1"></i> <?= esc($flashSuccess) ?>
                </div>
            <?php endif; ?>

            <form action="<?= site_url('/appointments/book/submit') ?>" method="POST" id="booking-form">
                <?= csrf_field() ?>

                <!-- Patient Details -->
                <h6 class="fw-bold text-slate mb-3"><i class="bi bi-person-badge text-success me-1"></i>Patient Details</h6>
                
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label class="form-label small fw-semibold">Full Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control form-control-sm" name="name" required placeholder="Enter full name">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-semibold">Email Address <span class="text-danger">*</span></label>
                        <input type="email" class="form-control form-control-sm" name="email" required placeholder="name@email.com">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-semibold">Phone Number <span class="text-danger">*</span></label>
                        <input type="text" class="form-control form-control-sm" name="phone" required placeholder="+91 98765 43210">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-semibold">Gender <span class="text-danger">*</span></label>
                        <select class="form-select form-select-sm" name="gender" required>
                            <option value="male">Male</option>
                            <option value="female">Female</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-semibold">Date of Birth <span class="text-danger">*</span></label>
                        <input type="date" class="form-control form-control-sm" name="dob" required max="<?= date('Y-m-d') ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-semibold">Address <span class="text-danger">*</span></label>
                        <input type="text" class="form-control form-control-sm" name="address" required placeholder="Street, City, Pincode">
                    </div>
                </div>

                <!-- Schedule -->
                <h6 class="fw-bold text-slate mb-3"><i class="bi bi-calendar-check text-success me-1"></i>Select Schedule</h6>
                
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label class="form-label small fw-semibold">Clinic Branch <span class="text-danger">*</span></label>
                        <select class="form-select form-select-sm" id="branch_id" name="branch_id" required>
                            <option value="">Choose Branch</option>
                            <?php foreach ($branches as $br): ?>
                                <option value="<?= $br['id'] ?>"><?= esc($br['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-semibold">Select Doctor <span class="text-danger">*</span></label>
                        <select class="form-select form-select-sm" id="doctor_id" name="doctor_id" required>
                            <option value="">Select Branch First</option>
                        </select>
                    </div>
                    <div class="col-md-12">
                        <label class="form-label small fw-semibold">Visit Date <span class="text-danger">*</span></label>
                        <input type="date" class="form-control form-control-sm" id="date" name="date" required min="<?= date('Y-m-d') ?>">
                    </div>
                </div>

                <!-- Time Slots -->
                <div class="mb-4">
                    <label class="form-label small fw-semibold d-block">Available Time Slots <span class="text-danger">*</span></label>
                    <div class="alert alert-light border small text-muted text-center py-3" id="slots-placeholder">
                        Please select a doctor and date to view available time slots.
                    </div>
                    <div class="d-flex flex-wrap gap-2" id="slots-container">
                        <!-- JS will inject slots here -->
                    </div>
                </div>

                <button type="submit" class="btn btn-emerald w-100 py-2 fw-semibold shadow-sm" id="submit-btn">
                    <i class="bi bi-calendar-check-fill me-1"></i> Confirm Appointment
                </button>
            </form>
            
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const branchSelect = document.getElementById('branch_id');
    const doctorSelect = document.getElementById('doctor_id');
    const dateInput = document.getElementById('date');
    const slotsContainer = document.getElementById('slots-container');
    const slotsPlaceholder = document.getElementById('slots-placeholder');
    const alertDiv = document.getElementById('booking-alert');
    const submitBtn = document.getElementById('submit-btn');

    // Store doctors data from PHP
    const allDoctors = <?= json_encode($doctors) ?>;
    console.log('All Doctors:', allDoctors);

    function showAlert(msg, isSuccess = false) {
        alertDiv.className = `alert ${isSuccess ? 'alert-success' : 'alert-danger'} py-2 small`;
        alertDiv.textContent = msg;
        alertDiv.classList.remove('d-none');
    }

    function hideAlert() {
        alertDiv.classList.add('d-none');
    }

    // Filter doctors by branch
    function filterDoctorsByBranch(branchId) {
        doctorSelect.innerHTML = '<option value="">Select Doctor</option>';
        
        const filtered = allDoctors.filter(function(doc) {
            return parseInt(doc.branch_id) === parseInt(branchId);
        });
        
        if (filtered.length === 0) {
            doctorSelect.innerHTML = '<option value="">No doctors available in this branch</option>';
            slotsPlaceholder.textContent = 'Please select a doctor and date to view available time slots.';
            slotsPlaceholder.classList.remove('d-none');
            slotsContainer.innerHTML = '';
            return;
        }
        
        filtered.forEach(function(doc) {
            const option = document.createElement('option');
            option.value = doc.id;
            option.textContent = 'Dr. ' + doc.username + (doc.branch_name ? ' (' + doc.branch_name + ')' : '');
            doctorSelect.appendChild(option);
        });
        
        if (filtered.length === 1) {
            doctorSelect.value = filtered[0].id;
            fetchAvailableSlots();
        }
    }

    function fetchAvailableSlots() {
        const doctorId = doctorSelect.value;
        const date = dateInput.value;

        if (!doctorId || !date) {
            slotsPlaceholder.textContent = 'Please select a doctor and date to view available time slots.';
            slotsPlaceholder.classList.remove('d-none');
            slotsContainer.innerHTML = '';
            return;
        }

        slotsPlaceholder.textContent = 'Checking availability...';
        slotsPlaceholder.classList.remove('d-none');
        slotsContainer.innerHTML = '';

        const url = '<?= site_url("/admin/appointments/slots") ?>?doctor_id=' + encodeURIComponent(doctorId) + '&date=' + encodeURIComponent(date);
        
        fetch(url)
            .then(function(response) {
                return response.json();
            })
            .then(function(data) {
                if (data.success && data.slots && data.slots.length > 0) {
                    slotsPlaceholder.classList.add('d-none');
                    
                    data.slots.forEach(function(slot) {
                        const btn = document.createElement('button');
                        btn.type = 'button';
                        btn.className = 'btn btn-sm btn-outline-primary slot-btn' + (slot.booked ? ' booked' : '');
                        btn.style.minWidth = '80px';
                        btn.innerHTML = '<i class="bi bi-clock me-1"></i> ' + slot.time_formatted;
                        btn.dataset.value = slot.time;
                        
                        if (!slot.booked) {
                            btn.onclick = function() {
                                document.querySelectorAll('.slot-btn').forEach(function(b) {
                                    b.classList.remove('selected');
                                });
                                this.classList.add('selected');
                                let hiddenInput = document.getElementById('selected-slot');
                                if (!hiddenInput) {
                                    hiddenInput = document.createElement('input');
                                    hiddenInput.type = 'hidden';
                                    hiddenInput.name = 'time_slot';
                                    hiddenInput.id = 'selected-slot';
                                    document.getElementById('booking-form').appendChild(hiddenInput);
                                }
                                hiddenInput.value = slot.time;
                                hideAlert();
                            };
                        } else {
                            btn.title = 'Slot already booked';
                        }
                        
                        slotsContainer.appendChild(btn);
                    });
                } else {
                    slotsPlaceholder.textContent = data.message || 'No available slots for this date.';
                    slotsPlaceholder.classList.remove('d-none');
                }
            })
            .catch(function(err) {
                console.error('Error:', err);
                slotsPlaceholder.textContent = '❌ Unable to check availability. Please try again.';
                slotsPlaceholder.classList.remove('d-none');
            });
    }

    // Form validation
    document.getElementById('booking-form').addEventListener('submit', function(e) {
        const selectedSlot = document.querySelector('.slot-btn.selected');
        if (!selectedSlot) {
            e.preventDefault();
            showAlert('Please select a time slot.', false);
            return false;
        }
        hideAlert();
        
        // Show loading state on button
        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Booking...';
        submitBtn.disabled = true;
    });

    // Branch change - filter doctors
    branchSelect.addEventListener('change', function() {
        const branchId = this.value;
        if (branchId) {
            filterDoctorsByBranch(branchId);
        } else {
            doctorSelect.innerHTML = '<option value="">Select Branch First</option>';
            slotsPlaceholder.textContent = 'Please select a doctor and date to view available time slots.';
            slotsPlaceholder.classList.remove('d-none');
            slotsContainer.innerHTML = '';
        }
    });

    // Doctor change - fetch slots
    doctorSelect.addEventListener('change', fetchAvailableSlots);
    dateInput.addEventListener('change', fetchAvailableSlots);

    if (branchSelect.value && doctorSelect.value && dateInput.value) {
        fetchAvailableSlots();
    }
});
</script>

<?php include VIEWS_PATH . '/layout/footer.php'; ?>