<?php 
include VIEWS_PATH . '/layout/header.php'; 
$details = $appointmentDetails ?? [];
?>

<div class="row justify-content-center py-5">
    <div class="col-md-7 col-lg-6">
        <div class="card border-0 shadow-lg rounded-4 p-5 text-center">
            
            <!-- Success Icon -->
            <div class="mb-4">
                <div style="width: 80px; height: 80px; background: #d1fae5; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto;">
                    <i class="bi bi-check-circle-fill text-success" style="font-size: 3rem;"></i>
                </div>
            </div>
            
            <h3 class="fw-bold text-slate mb-2">✅ Appointment Confirmed!</h3>
            <p class="text-muted small mb-4">Your appointment has been submitted successfully. Please wait for approval.</p>
            
            <!-- Flash Success Message -->
            <?php if ($flashSuccess = \App\Helpers\Session::getFlash('success')): ?>
                <div class="alert alert-success py-2 small success-animation">
                    <i class="bi bi-check-circle-fill me-1"></i> <?= esc($flashSuccess) ?>
                </div>
            <?php endif; ?>
            
            <!-- Appointment Details -->
            <div class="bg-light rounded-3 p-4 mb-4 text-start">
                <div class="row g-3">
                    <div class="col-6">
                        <div class="small text-muted">Patient Name</div>
                        <div class="fw-semibold"><?= esc($details['patient_name'] ?? 'N/A') ?></div>
                    </div>
                    <div class="col-6">
                        <div class="small text-muted">Doctor</div>
                        <div class="fw-semibold">Dr. <?= esc($details['doctor_name'] ?? 'N/A') ?></div>
                    </div>
                    <div class="col-6">
                        <div class="small text-muted">Date</div>
                        <div class="fw-semibold"><?= esc($details['date'] ?? 'N/A') ?></div>
                    </div>
                    <div class="col-6">
                        <div class="small text-muted">Time</div>
                        <div class="fw-semibold"><?= esc($details['time_slot'] ?? 'N/A') ?></div>
                    </div>
                    <div class="col-12">
                        <div class="small text-muted">Token Number</div>
                        <div class="fw-bold text-success" style="font-size: 1.2rem;">#<?= esc($details['token_number'] ?? 'Pending') ?></div>
                    </div>
                </div>
            </div>
            
            <div class="alert alert-info py-2 small">
                <i class="bi bi-info-circle me-1"></i> Your appointment is pending approval. You will receive a confirmation soon.
            </div>
            
            <div class="d-flex gap-2 justify-content-center flex-wrap">
                <a href="<?= site_url('/appointments/book') ?>" class="btn btn-outline-success btn-sm">
                    <i class="bi bi-plus-circle me-1"></i> Book Another
                </a>
                <a href="<?= site_url('/') ?>" class="btn btn-emerald btn-sm">
                    <i class="bi bi-house me-1"></i> Back to Home
                </a>
            </div>
            
            <!-- REMOVED: Staff Console Login button -->
            
        </div>
    </div>
</div>

<style>
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
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
</style>

<?php include VIEWS_PATH . '/layout/footer.php'; ?>