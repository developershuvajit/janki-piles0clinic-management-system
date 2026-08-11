<?php 
include VIEWS_PATH . '/layout/header.php'; 
?>

<style>
    .success-icon {
        width: 100px;
        height: 100px;
        background: #e6f5ed;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 1.5rem;
        animation: pulse 1.5s ease-in-out infinite;
    }
    .success-icon i {
        font-size: 3.5rem;
        color: #0f7b4a;
    }
    @keyframes pulse {
        0%, 100% { transform: scale(1); }
        50% { transform: scale(1.05); }
    }
    .booking-details {
        background: #f8fafc;
        border-radius: 12px;
        padding: 1.2rem;
        text-align: left;
        margin: 1.2rem 0;
        border-left: 4px solid #0f7b4a;
    }
    .booking-details .detail-row {
        display: flex;
        justify-content: space-between;
        padding: 0.4rem 0;
        border-bottom: 1px solid #eef2f6;
    }
    .booking-details .detail-row:last-child {
        border-bottom: none;
    }
    .booking-details .label {
        color: #6b7a8f;
        font-size: 0.78rem;
    }
    .booking-details .value {
        font-weight: 600;
        color: #0b1a2b;
        font-size: 0.82rem;
    }
    .badge-status {
        display: inline-block;
        padding: 0.15rem 0.8rem;
        border-radius: 40px;
        font-size: 0.7rem;
        background: #fef7e8;
        color: #c5711e;
    }
    .btn-emerald {
        background: #0f7b4a;
        color: #fff;
        border: none;
        transition: all 0.15s;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 0.3rem;
        padding: 0.3rem 1.2rem;
        border-radius: 40px;
        font-size: 0.85rem;
    }
    .btn-emerald:hover {
        background: #0b6e44;
        color: #fff;
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(15,123,74,0.2);
    }
    .btn-outline-emerald {
        border: 1px solid #0f7b4a;
        color: #0f7b4a;
        background: transparent;
        transition: all 0.15s;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 0.3rem;
        padding: 0.3rem 1.2rem;
        border-radius: 40px;
        font-size: 0.85rem;
    }
    .btn-outline-emerald:hover {
        background: #0f7b4a;
        color: #fff;
        transform: translateY(-1px);
    }
</style>

<div class="row justify-content-center py-4">
    <div class="col-md-7 col-lg-6">
        <div class="card border-0 shadow-lg rounded-4 p-4 text-center">
            
            <!-- Success Icon -->
            <div class="success-icon">
                <i class="bi bi-check-circle-fill"></i>
            </div>
            
            <h4 class="fw-bold text-slate">Appointment Booked Successfully!</h4>
            <p class="text-muted small">
                Your appointment request has been submitted. You will receive confirmation shortly.
            </p>

            <?php if (!empty($appointmentDetails)): ?>
            <!-- Booking Details -->
            <div class="booking-details">
                <div class="detail-row">
                    <span class="label">Patient</span>
                    <span class="value"><?= esc($appointmentDetails['patient_name'] ?? 'N/A') ?></span>
                </div>
                <div class="detail-row">
                    <span class="label">Doctor</span>
                    <span class="value">Dr. <?= esc($appointmentDetails['doctor_name'] ?? 'N/A') ?></span>
                </div>
                <div class="detail-row">
                    <span class="label">Date</span>
                    <span class="value"><?= date('d M, Y', strtotime($appointmentDetails['date'] ?? date('Y-m-d'))) ?></span>
                </div>
                <div class="detail-row">
                    <span class="label">Time</span>
                    <span class="value"><?= date('h:i A', strtotime($appointmentDetails['time_slot'] ?? '09:00:00')) ?></span>
                </div>
                <div class="detail-row">
                    <span class="label">Token</span>
                    <span class="value">#<?= esc($appointmentDetails['token_number'] ?? 'Pending') ?></span>
                </div>
                <div class="detail-row">
                    <span class="label">Status</span>
                    <span class="value">
                        <span class="badge-status">Pending Approval</span>
                    </span>
                </div>
            </div>
            <?php else: ?>
            <div class="alert alert-success py-2 small text-center" style="background:#e6f5ed;border-color:#b8e0cf;color:#0f7b4a;border-radius:10px;">
                <i class="bi bi-check-circle me-1"></i> Your appointment has been booked successfully!
            </div>
            <?php endif; ?>

            <!-- Important Info -->
            <div class="alert alert-info py-2 small text-start" style="background:#e6f0ff;border-color:#d0e2ff;color:#1a6bc4;border-radius:10px;">
                <i class="bi bi-info-circle-fill me-1"></i> 
                <strong>Next Steps:</strong> You will receive a confirmation once your appointment is approved. Please arrive 15 minutes early.
            </div>

            <!-- Action Buttons -->
            <div class="d-flex gap-2 justify-content-center mt-3 flex-wrap">
                <!-- Direct link to booking page -->
                <a href="/appointments/book" class="btn btn-outline-emerald btn-sm rounded-pill px-4 py-2">
                    <i class="bi bi-arrow-left me-1"></i> Book Another
                </a>
                
                <a href="/" class="btn btn-emerald btn-sm rounded-pill px-4 py-2">
                    <i class="bi bi-house me-1"></i> Home
                </a>
            </div>
            
        </div>
    </div>
</div>

<!-- Sweet Alert -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const hasBooking = <?= !empty($appointmentDetails) ? 'true' : 'false' ?>;
    
    if (hasBooking && !sessionStorage.getItem('booking_alert_shown')) {
        Swal.fire({
            icon: 'success',
            title: '✅ Appointment Booked!',
            text: 'Your appointment has been submitted successfully.',
            timer: 3000,
            timerProgressBar: true,
            showConfirmButton: true,
            confirmButtonColor: '#0f7b4a',
            confirmButtonText: 'OK'
        });
        sessionStorage.setItem('booking_alert_shown', 'true');
    }
});
</script>

<?php include VIEWS_PATH . '/layout/footer.php'; ?>