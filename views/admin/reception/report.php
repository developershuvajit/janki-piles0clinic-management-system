<?php 
$activePage = 'reception_report';
include VIEWS_PATH . '/layout/reception_header.php'; 
?>

<!-- Statistics Summary -->
<div class="row mb-4">
    <!-- Total Today -->
    <div class="col-md-3 mb-3">
        <div class="card p-3 border-0 shadow-sm bg-white d-flex flex-row align-items-center justify-content-between text-slate">
            <div>
                <h6 class="text-muted text-uppercase mb-1 small fw-bold">Total Collections</h6>
                <h3 class="mb-0 fw-bold text-success">₹<?= esc(number_format($report['total_collected'], 2)) ?></h3>
            </div>
            <div class="bg-success bg-opacity-10 p-3 rounded text-success fs-4">
                <i class="bi bi-wallet2"></i>
            </div>
        </div>
    </div>

    <!-- Cash Split -->
    <div class="col-md-3 mb-3">
        <div class="card p-3 border-0 shadow-sm bg-white d-flex flex-row align-items-center justify-content-between">
            <div>
                <h6 class="text-muted text-uppercase mb-1 small fw-bold">Cash Payments</h6>
                <h3 class="mb-0 fw-bold text-slate">₹<?= esc(number_format($report['splits']['cash'], 2)) ?></h3>
            </div>
            <div class="bg-primary bg-opacity-10 p-3 rounded text-primary fs-4">
                <i class="bi bi-cash"></i>
            </div>
        </div>
    </div>

    <!-- Card Split -->
    <div class="col-md-3 mb-3">
        <div class="card p-3 border-0 shadow-sm bg-white d-flex flex-row align-items-center justify-content-between">
            <div>
                <h6 class="text-muted text-uppercase mb-1 small fw-bold">Card Payments</h6>
                <h3 class="mb-0 fw-bold text-slate">₹<?= esc(number_format($report['splits']['card'], 2)) ?></h3>
            </div>
            <div class="bg-info bg-opacity-10 p-3 rounded text-info fs-4">
                <i class="bi bi-credit-card"></i>
            </div>
        </div>
    </div>

    <!-- UPI Split -->
    <div class="col-md-3 mb-3">
        <div class="card p-3 border-0 shadow-sm bg-white d-flex flex-row align-items-center justify-content-between">
            <div>
                <h6 class="text-muted text-uppercase mb-1 small fw-bold">UPI Payments</h6>
                <h3 class="mb-0 fw-bold text-slate">₹<?= esc(number_format($report['splits']['upi'], 2)) ?></h3>
            </div>
            <div class="bg-warning bg-opacity-10 p-3 rounded text-warning fs-4">
                <i class="bi bi-qr-code"></i>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Left Column: Settled Invoices List -->
    <div class="col-lg-8 mb-4">
        <div class="card border-0 shadow-sm p-4">
            <h5 class="fw-bold text-slate mb-3"><i class="bi bi-list-check text-success me-2"></i>Invoices Settled Today</h5>
            
            <div class="table-responsive border-0 shadow-none">
                <table class="table table-hover align-middle mb-0" style="font-size: 0.85rem;">
                    <thead>
                        <tr>
                            <th>Invoice #</th>
                            <th>Patient</th>
                            <th>Service</th>
                            <th>Paid via</th>
                            <th class="text-end">Paid Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($report['invoices'])): ?>
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted">No collections recorded today.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($report['invoices'] as $inv): ?>
                                <tr>
                                    <td class="fw-bold text-slate">INV-<?= esc(sprintf("%05d", $inv['id'])) ?></td>
                                    <td><?= esc($inv['patient_name']) ?> <span class="text-muted small">(<?= esc($inv['patient_code']) ?>)</span></td>
                                    <td><span class="badge bg-light text-secondary border"><?= esc(strtoupper($inv['type'])) ?></span></td>
                                    <td><span class="badge bg-light text-slate border"><?= esc(strtoupper($inv['payment_method'])) ?></span></td>
                                    <td class="text-end fw-bold text-success">₹<?= esc(number_format((float)$inv['paid_amount'], 2)) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Right Column: Registration Volumes -->
    <div class="col-lg-4 mb-4">
        <div class="card border-0 shadow-sm p-4 text-slate">
            <h5 class="fw-bold text-slate mb-3"><i class="bi bi-graph-up text-success me-2"></i>Visitor Volumes</h5>
            
            <ul class="list-group list-group-flush mb-0">
                <li class="list-group-item px-0 py-3 d-flex justify-content-between align-items-center">
                    <div>
                        <strong class="d-block">OPD Appointments Today</strong>
                        <span class="text-muted small">Walk-ins and online confirmations.</span>
                    </div>
                    <span class="badge bg-primary fs-6 px-3 py-2 rounded-pill"><?= esc((string)$report['opd_registrations']) ?></span>
                </li>
                
                <li class="list-group-item px-0 py-3 d-flex justify-content-between align-items-center">
                    <div>
                        <strong class="d-block">IPD Bed Admissions Today</strong>
                        <span class="text-muted small">Patients admitted in ward rooms.</span>
                    </div>
                    <span class="badge bg-danger fs-6 px-3 py-2 rounded-pill"><?= esc((string)$report['ipd_admissions']) ?></span>
                </li>
            </ul>
        </div>
    </div>
</div>

<?php include VIEWS_PATH . '/layout/reception_footer.php'; ?>
