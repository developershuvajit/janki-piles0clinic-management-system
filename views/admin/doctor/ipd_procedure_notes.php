<?php 
$activePage = 'doctor_ipd';
include VIEWS_PATH . '/layout/doctor_header.php'; 
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold text-slate mb-1">Procedure & Surgery Notes</h4>
        <p class="text-muted small mb-0">Patient: <strong><?= esc($admission['patient_name']) ?></strong> (Room: <?= esc($admission['room_number']) ?>)</p>
    </div>
    <a href="<?= site_url('/doctor/ipd') ?>" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
        <i class="bi bi-arrow-left me-1"></i> Back to IPD List
    </a>
</div>

<div class="row">
    <!-- Procedure Entry Form -->
    <div class="col-lg-5 mb-4">
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-header bg-info text-white py-3">
                <h6 class="fw-bold mb-0"><i class="bi bi-tools me-2"></i> Record Performed Procedure / Surgery</h6>
            </div>
            <div class="card-body p-4">
                <form action="<?= site_url('/doctor/ipd/procedure-notes/' . $admission['id'] . '/save') ?>" method="POST">
                    <?= csrf_field() ?>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Procedure / Surgery Name</label>
                        <input type="text" class="form-control" name="name" placeholder="e.g. Kshar Sutra Procedure, Minor Debridement" required>
                    </div>

                    <div class="mb-4">
                        <label class="form-label small fw-bold">Procedure / Surgery Cost (₹)</label>
                        <input type="number" step="0.01" class="form-control" name="cost" placeholder="0.00" required>
                    </div>

                    <button type="submit" class="btn btn-info text-white w-100 rounded-3 py-2 fw-bold">
                        <i class="bi bi-plus-lg me-1"></i> Record Procedure Note
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- History List -->
    <div class="col-lg-7 mb-4">
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-header bg-light py-3">
                <h6 class="fw-bold mb-0"><i class="bi bi-list-check me-2"></i> Performed Procedures History</h6>
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
                                    <td colspan="4" class="text-center py-4 text-muted">No procedures recorded for this admission.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($procedures as $proc): ?>
                                    <tr>
                                        <td class="fw-bold text-slate"><?= esc($proc['name']) ?></td>
                                        <td>Dr. <?= esc($proc['doctor_name']) ?></td>
                                        <td class="fw-bold text-success">₹<?= number_format((float)$proc['cost'], 2) ?></td>
                                        <td class="small text-muted"><?= date('d M Y, h:i A', strtotime($proc['created_at'])) ?></td>
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

<?php include VIEWS_PATH . '/layout/doctor_footer.php'; ?>
