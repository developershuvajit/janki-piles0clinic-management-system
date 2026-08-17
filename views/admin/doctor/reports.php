<?php 
$activePage = 'doctor_reports';
include VIEWS_PATH . '/layout/doctor_header.php'; 
?>

<!-- ============================================
     PAGE CSS
     ============================================ -->
<style>
.text-slate {
    color: #0b1a2b;
}

/* Stat Cards */
.stat-card {
    background: #ffffff;
    border-radius: 16px;
    padding: 1.5rem 1.5rem;
    box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    border: 1px solid #f0f2f5;
    transition: all 0.25s ease;
    display: flex;
    align-items: center;
    justify-content: space-between;
    text-decoration: none;
    color: inherit;
    height: 100%;
}
.stat-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 12px 28px rgba(0,0,0,0.08);
    border-color: #e2e8f0;
}

.stat-card .stat-icon {
    width: 52px;
    height: 52px;
    border-radius: 14px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    flex-shrink: 0;
}
.stat-card .stat-icon.primary {
    background: #eef2ff;
    color: #4f46e5;
}
.stat-card .stat-icon.info {
    background: #e6f7fe;
    color: #0e7c9e;
}
.stat-card .stat-icon.success {
    background: #d1fae5;
    color: #059669;
}
.stat-card .stat-icon.warning {
    background: #fef3c7;
    color: #d97706;
}

.stat-card .stat-label {
    font-size: 0.7rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.04em;
    color: #6b7a8f;
    margin-bottom: 0.2rem;
}
.stat-card .stat-value {
    font-size: 2rem;
    font-weight: 700;
    line-height: 1.2;
    color: #0b1a2b;
}
.stat-card .stat-sub {
    font-size: 0.7rem;
    color: #94a3b8;
    margin-top: 0.1rem;
}

/* Small stat cards */
.stat-card-sm {
    background: #ffffff;
    border-radius: 12px;
    padding: 1rem 1.2rem;
    box-shadow: 0 2px 8px rgba(0,0,0,0.04);
    border: 1px solid #f0f2f5;
    transition: all 0.2s ease;
    display: flex;
    align-items: center;
    gap: 0.8rem;
}
.stat-card-sm:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(0,0,0,0.06);
}
.stat-card-sm .stat-icon-sm {
    width: 40px;
    height: 40px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.1rem;
    flex-shrink: 0;
}
.stat-card-sm .stat-label-sm {
    font-size: 0.6rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.03em;
    color: #6b7a8f;
}
.stat-card-sm .stat-value-sm {
    font-size: 1.3rem;
    font-weight: 700;
    color: #0b1a2b;
}

/* ===== RESPONSIVE ===== */
@media (max-width: 768px) {
    .stat-card .stat-value {
        font-size: 1.5rem;
    }
    .stat-card .stat-icon {
        width: 44px;
        height: 44px;
        font-size: 1.2rem;
    }
}
@media (max-width: 576px) {
    .stat-card {
        padding: 1rem 1.2rem;
    }
    .stat-card .stat-value {
        font-size: 1.3rem;
    }
    .stat-card .stat-icon {
        width: 38px;
        height: 38px;
        font-size: 1rem;
    }
    .stat-card-sm {
        padding: 0.8rem 1rem;
    }
    .stat-card-sm .stat-value-sm {
        font-size: 1.1rem;
    }
}
</style>

<!-- ============================================
     HEADER
     ============================================ -->
<div class="d-flex justify-content-between align-items-center mb-4 mx-4 mt-4">
    <div>
        <h4 class="fw-bold text-slate mb-1"><i class="bi bi-graph-up-arrow text-success me-2"></i>Doctor Clinical Activity Reports</h4>
        <p class="text-muted small mb-0">Monthly OPD, IPD, and consultation metrics</p>
    </div>
    <div>
        <span class="badge bg-light text-dark border px-3 py-2">
            <i class="bi bi-calendar3 me-1"></i> <?= date('F Y') ?>
        </span>
    </div>
</div>

<!-- ============================================
     MAIN STATISTICS CARDS
     ============================================ -->
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="stat-card">
            <div>
                <div class="stat-label">Monthly OPD Consults</div>
                <div class="stat-value text-primary"><?= esc((string)$monthly_opd) ?></div>
                <div class="stat-sub">Outpatient consultations</div>
            </div>
            <div class="stat-icon primary">
                 <i class="bi bi-heart-pulse"></i>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="stat-card">
            <div>
                <div class="stat-label">Monthly IPD Admissions</div>
                <div class="stat-value text-info"><?= esc((string)$monthly_ipd) ?></div>
                <div class="stat-sub">Inpatient admissions</div>
            </div>
            <div class="stat-icon info">
                <i class="bi bi-hospital"></i>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="stat-card">
            <div>
                <div class="stat-label">Total Prescriptions</div>
                <div class="stat-value text-success"><?= esc((string)$total_consultations) ?></div>
                <div class="stat-sub">Prescriptions written</div>
            </div>
            <div class="stat-icon success">
                <i class="bi bi-file-earmark-medical"></i>
            </div>
        </div>
    </div>
</div>

<!-- ============================================
     SUMMARY ROW - Additional Metrics
     ============================================ -->
<div class="row g-3">
    <div class="col-md-3 col-6">
        <div class="stat-card-sm">
            <div class="stat-icon-sm" style="background: #eef2ff; color: #4f46e5;">
                <i class="bi bi-person-fill"></i>
            </div>
            <div>
                <div class="stat-label-sm">Total Patients</div>
                <div class="stat-value-sm"><?= esc((string)($total_patients ?? 0)) ?></div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-6">
        <div class="stat-card-sm">
            <div class="stat-icon-sm" style="background: #d1fae5; color: #059669;">
                <i class="bi bi-check-circle-fill"></i>
            </div>
            <div>
                <div class="stat-label-sm">Completed</div>
                <div class="stat-value-sm"><?= esc((string)($completed_consultations ?? 0)) ?></div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-6">
        <div class="stat-card-sm">
            <div class="stat-icon-sm" style="background: #fef3c7; color: #d97706;">
                <i class="bi bi-clock-history"></i>
            </div>
            <div>
                <div class="stat-label-sm">Pending</div>
                <div class="stat-value-sm"><?= esc((string)($pending_consultations ?? 0)) ?></div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-6">
        <div class="stat-card-sm">
            <div class="stat-icon-sm" style="background: #fee2e2; color: #dc2626;">
                <i class="bi bi-x-circle-fill"></i>
            </div>
            <div>
                <div class="stat-label-sm">Cancelled</div>
                <div class="stat-value-sm"><?= esc((string)($cancelled_consultations ?? 0)) ?></div>
            </div>
        </div>
    </div>
</div>

<!-- ============================================
     EMPTY STATE - If no data available
     ============================================ -->
<?php if (empty($monthly_opd) && empty($monthly_ipd) && empty($total_consultations)): ?>
    <div class="mt-5" style="text-align:center;padding:3rem 1rem;color:#94a3b8;">
        <i class="bi bi-bar-chart-fill fs-1 d-block mb-3" style="opacity:0.3;"></i>
        <h6 class="fw-bold text-slate">No Clinical Data Available</h6>
        <p class="small text-muted">Start consultations to see your activity metrics here.</p>
    </div>
<?php endif; ?>

<?php include VIEWS_PATH . '/layout/doctor_footer.php'; ?>