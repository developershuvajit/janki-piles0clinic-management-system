<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Debug - দেখুন ডেটা আসছে কিনা
echo "<!-- DEBUG: admission = " . print_r($admission ?? 'NOT SET', true) . " -->\n";
echo "<!-- DEBUG: procedures = " . print_r($procedures ?? 'NOT SET', true) . " -->\n";

if (!isset($admission)) {
    die('ERROR: $admission variable is not set!');
}

$activePage = 'doctor_ipd';
include VIEWS_PATH . '/layout/doctor_header.php';
?>

<div class="container-fluid px-4 py-3">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold text-slate mb-1">Procedure & Surgery Notes</h4>
            <p class="text-muted small mb-0">
                Patient: <strong><?= esc($admission['patient_name'] ?? 'N/A') ?></strong> 
                (<?= esc($admission['patient_code'] ?? 'N/A') ?>)
                <?php if (!empty($admission['branch_name'])): ?>
                    | Branch: <?= esc($admission['branch_name']) ?>
                <?php endif; ?>
            </p>
        </div>
        <a href="<?= site_url('/doctor/ipd') ?>" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
            <i class="bi bi-arrow-left me-1"></i> Back to IPD List
        </a>
    </div>

    <!-- Patient Info Card -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm rounded-4 bg-light">
                <div class="card-body py-2 px-4">
                    <div class="row text-center">
                        <div class="col-3">
                            <small class="text-muted d-block">Diagnosis</small>
                            <span class="fw-semibold"><?= esc($admission['diagnosis'] ?? 'N/A') ?></span>
                        </div>
                        <div class="col-3">
                            <small class="text-muted d-block">Admission Date</small>
                            <span class="fw-semibold"><?= date('d M Y', strtotime($admission['admission_date'] ?? 'now')) ?></span>
                        </div>
                        <div class="col-3">
                            <small class="text-muted d-block">Attending Doctor</small>
                            <span class="fw-semibold">Dr. <?= esc($admission['doctor_name'] ?? 'N/A') ?></span>
                        </div>
                        <div class="col-3">
                            <small class="text-muted d-block">Status</small>
                            <span class="badge bg-warning bg-opacity-10 text-warning">Admitted</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Procedure Entry Form -->
        <div class="col-lg-5 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-info text-white py-3">
                    <h6 class="fw-bold mb-0"><i class="bi bi-tools me-2"></i> Record Procedure / Surgery</h6>
                </div>
                <div class="card-body">
                     <form action="<?= site_url('/doctor/ipd/procedure-notes/' . $admission['id'] . '/save') ?>" method="POST">

    <?= csrf_field() ?>

    <!-- Procedure Name -->
    <div class="mb-3">
        <label class="form-label small fw-bold">
            Procedure / Surgery Name
            <span class="text-danger">*</span>
        </label>

        <input
            type="text"
            class="form-control"
            name="name"
            placeholder="e.g. Kshar Sutra Procedure, Minor Debridement"
            required
        >
    </div>

    <!-- Performing Doctor -->
    <div class="mb-3">
        <label class="form-label small fw-bold">
            Performing Doctor
        </label>

        <div class="p-2 bg-light rounded border">
            <i class="bi bi-person-badge me-1"></i>
            Dr. <?= esc($user['username'] ?? 'You') ?>
        </div>

        <small class="text-muted">
            Procedure will be recorded under your name
        </small>
    </div>

    <!-- Cost -->
    <div class="mb-4">
        <label class="form-label small fw-bold">
            Cost (₹)
            <span class="text-danger">*</span>
        </label>

        <input
            type="number"
            step="0.01"
            min="0"
            class="form-control"
            name="cost"
            placeholder="0.00"
            required
        >
    </div>

    <!-- Submit -->
    <button
        type="submit"
        class="btn btn-info w-100 text-white fw-bold"
    >
        <i class="bi bi-plus-lg me-1"></i>
        Record Procedure Note
    </button>

</form>
                </div>
            </div>
        </div>

        <!-- History List -->
        <div class="col-lg-7 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-light py-3 d-flex justify-content-between align-items-center">
                    <h6 class="fw-bold mb-0"><i class="bi bi-list-check me-2"></i> Procedure History</h6>
                    <span class="badge bg-secondary"><?= count($procedures ?? []) ?> records</span>
                </div>
                <div class="card-body p-4">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Procedure Name</th>
                                    <th>Performed By</th>
                                    <th>Cost (₹)</th>
                                    <th>Date Recorded</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($procedures)): ?>
                                    <tr>
                                        <td colspan="4" class="text-center py-4 text-muted">No procedures recorded yet.</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($procedures as $proc): ?>
                                        <tr>
                                            <td class="fw-bold"><?= esc($proc['name'] ?? 'N/A') ?></td>
                                            <td>Dr. <?= esc($proc['doctor_name'] ?? 'N/A') ?></td>
                                            <td class="fw-bold text-success">₹<?= number_format((float)($proc['cost'] ?? 0), 2) ?></td>
                                            <td class="small text-muted"><?= date('d M Y, h:i A', strtotime($proc['created_at'] ?? 'now')) ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include VIEWS_PATH . '/layout/doctor_footer.php'; ?>