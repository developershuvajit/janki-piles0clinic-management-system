<?php 
$activePage = 'reception_followups';
include VIEWS_PATH . '/layout/reception_header.php'; 
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold text-slate mb-1"><i class="bi bi-calendar2-check-fill text-success me-2"></i>Patient Follow-up Management</h4>
        <p class="text-muted small mb-0">Track due, upcoming, missed, and completed patient post-treatment visit schedules.</p>
    </div>
    <div>
        <a href="https://web.whatsapp.com" target="_blank" class="btn btn-outline-success btn-sm rounded-pill px-3">
            <i class="bi bi-whatsapp me-1"></i> Open WhatsApp Web
        </a>
    </div>
</div>

<!-- Metrics Cards -->
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <a href="<?= site_url('/reception/followups?tab=due') ?>" class="text-decoration-none">
            <div class="card p-3 border-0 shadow-sm rounded-4 border-start border-4 border-warning <?= ($active_tab === 'due') ? 'bg-warning bg-opacity-10' : '' ?>">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-muted small fw-bold text-uppercase">Due Today</div>
                        <h2 class="mb-0 fw-bold text-warning"><?= esc((string)($metrics['due'] ?? 0)) ?></h2>
                    </div>
                    <div class="bg-warning bg-opacity-20 p-3 rounded-circle text-warning fs-4">
                        <i class="bi bi-clock-history"></i>
                    </div>
                </div>
            </div>
        </a>
    </div>
    
    <div class="col-md-3">
        <a href="<?= site_url('/reception/followups?tab=missed') ?>" class="text-decoration-none">
            <div class="card p-3 border-0 shadow-sm rounded-4 border-start border-4 border-danger <?= ($active_tab === 'missed') ? 'bg-danger bg-opacity-10' : '' ?>">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-muted small fw-bold text-uppercase">Missed Follow-ups</div>
                        <h2 class="mb-0 fw-bold text-danger"><?= esc((string)($metrics['missed'] ?? 0)) ?></h2>
                    </div>
                    <div class="bg-danger bg-opacity-20 p-3 rounded-circle text-danger fs-4">
                        <i class="bi bi-exclamation-triangle-fill"></i>
                    </div>
                </div>
            </div>
        </a>
    </div>

    <div class="col-md-3">
        <a href="<?= site_url('/reception/followups?tab=upcoming') ?>" class="text-decoration-none">
            <div class="card p-3 border-0 shadow-sm rounded-4 border-start border-4 border-info <?= ($active_tab === 'upcoming') ? 'bg-info bg-opacity-10' : '' ?>">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-muted small fw-bold text-uppercase">Upcoming Visits</div>
                        <h2 class="mb-0 fw-bold text-info"><?= esc((string)($metrics['upcoming'] ?? 0)) ?></h2>
                    </div>
                    <div class="bg-info bg-opacity-20 p-3 rounded-circle text-info fs-4">
                        <i class="bi bi-calendar-event"></i>
                    </div>
                </div>
            </div>
        </a>
    </div>

    <div class="col-md-3">
        <a href="<?= site_url('/reception/followups?tab=completed') ?>" class="text-decoration-none">
            <div class="card p-3 border-0 shadow-sm rounded-4 border-start border-4 border-success <?= ($active_tab === 'completed') ? 'bg-success bg-opacity-10' : '' ?>">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-muted small fw-bold text-uppercase">Completed</div>
                        <h2 class="mb-0 fw-bold text-success"><?= esc((string)($metrics['completed'] ?? 0)) ?></h2>
                    </div>
                    <div class="bg-success bg-opacity-20 p-3 rounded-circle text-success fs-4">
                        <i class="bi bi-check-circle-fill"></i>
                    </div>
                </div>
            </div>
        </a>
    </div>
</div>

<!-- Follow-ups Table Card -->
<div class="card border-0 shadow-sm p-4 rounded-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="fw-bold text-slate mb-0">
            <i class="bi bi-list-stars text-success me-2"></i>
            <?= ucfirst($active_tab) ?> Patient Follow-up List
        </h5>
        <div class="btn-group btn-group-sm" role="group">
            <a href="<?= site_url('/reception/followups?tab=due') ?>" class="btn btn-outline-secondary <?= ($active_tab === 'due') ? 'active' : '' ?>">Due Today</a>
            <a href="<?= site_url('/reception/followups?tab=missed') ?>" class="btn btn-outline-secondary <?= ($active_tab === 'missed') ? 'active' : '' ?>">Missed</a>
            <a href="<?= site_url('/reception/followups?tab=upcoming') ?>" class="btn btn-outline-secondary <?= ($active_tab === 'upcoming') ? 'active' : '' ?>">Upcoming</a>
            <a href="<?= site_url('/reception/followups?tab=completed') ?>" class="btn btn-outline-secondary <?= ($active_tab === 'completed') ? 'active' : '' ?>">Completed</a>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0" style="font-size:0.88rem;">
            <thead class="bg-light">
                <tr>
                    <th>Patient Code</th>
                    <th>Patient Name</th>
                    <th>Contact Phone</th>
                    <th>Scheduled Date</th>
                    <th>Channel</th>
                    <th>Clinical Notes</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($followups)): ?>
                    <tr>
                        <td colspan="7" class="text-center py-5 text-muted">
                            <i class="bi bi-calendar-x display-6 d-block mb-2 text-muted"></i>
                            No <?= esc($active_tab) ?> follow-ups found in system.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($followups as $f): ?>
                        <?php 
                            $waMsg = "Namaste " . $f['patient_name'] . ", reminder for your post-treatment follow-up checkup at Janki Piles Clinic scheduled for " . date('d M Y', strtotime($f['next_visit_date'])) . ". Please call helpline to confirm your appointment time.";
                            $waLink = \App\Models\Communication::getWhatsAppLink($f['patient_phone'], $waMsg);
                        ?>
                        <tr>
                            <td class="fw-bold text-slate"><?= esc($f['patient_code']) ?></td>
                            <td class="fw-bold text-dark"><?= esc($f['patient_name']) ?></td>
                            <td>
                                <a href="tel:<?= esc($f['patient_phone']) ?>" class="text-decoration-none text-dark">
                                    <i class="bi bi-telephone me-1 text-muted"></i><?= esc($f['patient_phone']) ?>
                                </a>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border">
                                    <i class="bi bi-calendar-event me-1"></i><?= esc(date('d M Y', strtotime($f['next_visit_date']))) ?>
                                </span>
                            </td>
                            <td><span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25"><?= esc(strtoupper($f['channel'])) ?></span></td>
                            <td class="small text-muted" style="max-width:200px;"><?= esc($f['notes'] ?: 'Follow-up review') ?></td>
                            <td>
                                <div class="d-flex gap-1">
                                    <a href="<?= $waLink ?>" target="_blank" class="btn btn-sm btn-success px-2 py-1" style="font-size:0.75rem;" title="1-Click WhatsApp Reminder">
                                        <i class="bi bi-whatsapp me-1"></i> Reminder
                                    </a>
                                    <a href="<?= site_url('/reception/walk-in?patient_id=' . $f['patient_id']) ?>" class="btn btn-sm btn-outline-primary px-2 py-1" style="font-size:0.75rem;" title="Book Walk-in Check-in">
                                        <i class="bi bi-box-arrow-in-right me-1"></i> Book Slot
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include VIEWS_PATH . '/layout/reception_footer.php'; ?>
