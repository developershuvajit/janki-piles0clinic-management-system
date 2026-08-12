<?php 
$activePage = 'id_cards';
include VIEWS_PATH . '/layout/admin_header.php'; 
?>

<style>
    /* ===== ID CARD STYLES ===== */
    .id-card-vertical {
        width: 340px;
        min-height: 490px;
        background: linear-gradient(145deg, #ffffff 0%, #f4f9f4 100%);
        border-radius: 18px;
        border: 1px solid #c8e6c9;
        box-shadow: 0 10px 35px rgba(46, 125, 50, 0.12);
        overflow: hidden;
        margin: 0 auto;
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
        transition: all 0.3s ease;
        position: relative;
    }
    .id-card-vertical:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 45px rgba(46, 125, 50, 0.18);
    }

    .id-card-header {
        background: linear-gradient(135deg, #1b5e20, #2e7d32, #388e3c);
        padding: 1.2rem 1.5rem 0.9rem;
        color: #fff;
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-bottom: 3px solid #66bb6a;
        position: relative;
    }
    .id-card-header::after {
        content: '';
        position: absolute;
        bottom: -8px;
        left: 0;
        right: 0;
        height: 8px;
        background: linear-gradient(90deg, transparent, #66bb6a, #a5d6a7, #66bb6a, transparent);
        opacity: 0.5;
    }
    .id-card-clinic {
        display: flex;
        align-items: center;
        gap: 0.6rem;
    }
    .id-card-clinic .clinic-icon {
        font-size: 1.4rem;
        color: #a5d6a7;
    }
    .id-card-clinic .clinic-name {
        font-size: 0.8rem;
        font-weight: 700;
        letter-spacing: 0.5px;
        text-transform: uppercase;
        color: #e8f5e9;
        line-height: 1.2;
    }
    .id-card-clinic .clinic-sub {
        font-size: 0.55rem;
        font-weight: 400;
        color: #a5d6a7;
        letter-spacing: 0.3px;
    }
    .id-card-type {
        font-size: 0.55rem;
        font-weight: 600;
        background: rgba(102, 187, 106, 0.25);
        padding: 0.2rem 0.8rem;
        border-radius: 40px;
        color: #c8e6c9;
        letter-spacing: 0.8px;
        border: 1px solid rgba(102, 187, 106, 0.3);
        text-transform: uppercase;
    }

    .id-card-body {
        padding: 1.4rem 1.5rem 0.8rem;
        display: flex;
        flex-direction: column;
        align-items: center;
        text-align: center;
    }
    .id-card-photo {
        width: 100px;
        height: 100px;
        border-radius: 50%;
        background: linear-gradient(135deg, #e8f5e9, #c8e6c9);
        border: 4px solid #66bb6a;
        overflow: hidden;
        margin-bottom: 0.9rem;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        box-shadow: 0 4px 15px rgba(46, 125, 50, 0.2);
        position: relative;
    }
    .id-card-photo::after {
        content: '●';
        position: absolute;
        bottom: 2px;
        right: 4px;
        color: #4caf50;
        font-size: 14px;
        text-shadow: 0 0 8px rgba(76, 175, 80, 0.5);
    }
    .id-card-photo img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    .id-card-photo .no-photo {
        font-size: 2.5rem;
        color: #66bb6a;
    }
    .id-card-name {
        font-size: 1.15rem;
        font-weight: 700;
        color: #1b5e20;
        margin-bottom: 0.1rem;
        letter-spacing: 0.3px;
    }
    .id-card-role {
        font-size: 0.8rem;
        color: #388e3c;
        font-weight: 600;
        margin-bottom: 0.2rem;
        background: #e8f5e9;
        padding: 0.1rem 1.2rem;
        border-radius: 40px;
        display: inline-block;
    }
    .id-card-code {
        font-size: 0.7rem;
        color: #6d8f6d;
        font-family: 'Courier New', monospace;
        background: #e8f5e9;
        padding: 0.15rem 1rem;
        border-radius: 40px;
        display: inline-block;
        letter-spacing: 0.5px;
        border: 1px solid #c8e6c9;
    }

    .id-card-footer {
        padding: 0.8rem 1.5rem 1rem;
        display: flex;
        flex-direction: column;
        align-items: center;
        border-top: 1px solid #e8f5e9;
        background: linear-gradient(0deg, #fafffa, #ffffff);
        gap: 0.6rem;
    }
    .id-card-qr-wrapper {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 1.5rem;
        width: 100%;
    }
    .id-card-qr {
        width: 180px;
        height: 180px;
        background: #ffffff;
        border-radius: 12px;
        padding: 6px;
        border: 2px solid #c8e6c9;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        box-shadow: 0 2px 10px rgba(46, 125, 50, 0.08);
    }
    .id-card-qr img {
        width: 100%;
        height: 100%;
        object-fit: contain;
    }
    .id-card-branch-info {
        display: flex;
        flex-direction: column;
        align-items: flex-start;
        gap: 0.2rem;
    }
    .id-card-branch {
        font-size: 0.72rem;
        color: #2e7d32;
        font-weight: 500;
        display: flex;
        align-items: center;
        gap: 0.4rem;
    }
    .id-card-branch i {
        color: #4caf50;
        font-size: 0.9rem;
    }
    .id-card-branch-detail {
        font-size: 0.6rem;
        color: #6d8f6d;
        font-weight: 400;
        display: flex;
        align-items: center;
        gap: 0.8rem;
    }
    .id-card-branch-detail span {
        display: flex;
        align-items: center;
        gap: 0.2rem;
    }
    .id-card-branch-detail i {
        color: #81c784;
        font-size: 0.7rem;
    }

    .id-card-bottom {
        padding: 0.4rem 1.5rem;
        background: #f4f9f4;
        border-top: 1px solid #e8f5e9;
        text-align: center;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .id-card-valid {
        font-size: 0.55rem;
        color: #81c784;
        font-weight: 600;
        letter-spacing: 0.5px;
        text-transform: uppercase;
    }
    .id-card-valid i {
        margin-right: 4px;
        color: #4caf50;
    }
    .id-card-issued {
        font-size: 0.5rem;
        color: #a5d6a7;
        font-weight: 400;
    }

    /* ===== UI STYLES ===== */
    .id-card-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(360px, 1fr));
        gap: 2rem;
        margin-top: 1.5rem;
        justify-items: center;
    }

    .btn-primary-clean {
        background: #2e7d32;
        color: #fff;
        border: 1px solid #2e7d32;
        padding: 0.45rem 1.2rem;
        border-radius: 40px;
        font-size: 0.8rem;
        font-weight: 500;
        transition: all 0.2s;
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        cursor: pointer;
    }
    .btn-primary-clean:hover {
        background: #1b5e20;
        border-color: #1b5e20;
        color: #fff;
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(46, 125, 50, 0.3);
    }
    .btn-soft-clean {
        background: #e8f5e9;
        color: #2e7d32;
        border: 1px solid #c8e6c9;
        padding: 0.45rem 1.2rem;
        border-radius: 40px;
        font-size: 0.8rem;
        font-weight: 500;
        transition: all 0.2s;
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        cursor: pointer;
    }
    .btn-soft-clean:hover {
        background: #c8e6c9;
        border-color: #66bb6a;
        transform: translateY(-1px);
    }
    .btn-success-clean {
        background: #43a047;
        color: #fff;
        border: 1px solid #43a047;
        padding: 0.45rem 1.2rem;
        border-radius: 40px;
        font-size: 0.8rem;
        font-weight: 500;
        transition: all 0.2s;
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        cursor: pointer;
    }
    .btn-success-clean:hover {
        background: #2e7d32;
        border-color: #2e7d32;
        color: #fff;
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(46, 125, 50, 0.3);
    }

    .badge-active {
        background: #e8f5e9;
        color: #2e7d32;
        border: 1px solid #a5d6a7;
        border-radius: 40px;
        padding: 0.15rem 0.7rem;
        font-size: 0.65rem;
        font-weight: 500;
    }
    .badge-inactive {
        background: #fce4ec;
        color: #c62828;
        border: 1px solid #ef9a9a;
        border-radius: 40px;
        padding: 0.15rem 0.7rem;
        font-size: 0.65rem;
        font-weight: 500;
    }

    .card-clean {
        background: #ffffff;
        border: 1px solid #e8f5e9;
        border-radius: 16px;
        padding: 1rem 1.5rem;
        box-shadow: 0 2px 10px rgba(0,0,0,0.04);
    }

    /* ===== PRINT STYLES - FIXED ===== */
    @media print {
        /* Hide everything except cards */
        body * {
            visibility: hidden;
        }
        
        /* Show only card container and cards */
        #idCardContainer, 
        #idCardContainer * {
            visibility: visible;
        }
        
        #idCardContainer {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
            margin: 0;
            padding: 0;
        }
        
        /* 9 cards per page (3x3 grid) */
        .id-card-grid {
            display: grid !important;
            grid-template-columns: repeat(3, 1fr) !important;
            gap: 10px !important;
            padding: 10px !important;
            margin: 0 !important;
            justify-items: center !important;
            align-items: start !important;
        }
        
        /* Each card in print */
        .id-card-vertical {
            width: 100% !important;
            max-width: 200px !important;
            min-height: 280px !important;
            margin: 0 !important;
            box-shadow: none !important;
            border: 1px solid #ccc !important;
            border-radius: 8px !important;
            page-break-inside: avoid !important;
            break-inside: avoid !important;
            transform: none !important;
        }
        
        /* Force page break after every 9th card */
        .id-card-vertical:nth-child(9n) {
            page-break-after: always !important;
            break-after: page !important;
        }
        
        /* Scale down content for print */
        .id-card-header {
            padding: 0.5rem 0.8rem 0.4rem !important;
        }
        .id-card-header .clinic-icon {
            font-size: 0.9rem !important;
        }
        .id-card-header .clinic-name {
            font-size: 0.55rem !important;
        }
        .id-card-header .clinic-sub {
            font-size: 0.4rem !important;
        }
        .id-card-type {
            font-size: 0.4rem !important;
            padding: 0.1rem 0.4rem !important;
        }
        
        .id-card-body {
            padding: 0.6rem 0.8rem 0.3rem !important;
        }
        .id-card-photo {
            width: 55px !important;
            height: 55px !important;
            border-width: 2px !important;
            margin-bottom: 0.4rem !important;
        }
        .id-card-photo::after {
            font-size: 8px !important;
        }
        .id-card-name {
            font-size: 0.75rem !important;
        }
        .id-card-role {
            font-size: 0.5rem !important;
            padding: 0.05rem 0.6rem !important;
        }
        .id-card-code {
            font-size: 0.45rem !important;
            padding: 0.05rem 0.5rem !important;
        }
        
        .id-card-footer {
            padding: 0.3rem 0.8rem 0.4rem !important;
            gap: 0.2rem !important;
        }
        .id-card-qr-wrapper {
            gap: 0.6rem !important;
        }
        .id-card-qr {
            width: 150px !important;
            height: 150px !important;
            padding: 3px !important;
        }
        .id-card-branch {
            font-size: 0.5rem !important;
        }
        .id-card-branch-detail {
            font-size: 0.4rem !important;
            gap: 0.3rem !important;
        }
        
        .id-card-bottom {
            padding: 0.15rem 0.8rem !important;
        }
        .id-card-valid {
            font-size: 0.35rem !important;
        }
        .id-card-issued {
            font-size: 0.35rem !important;
        }
        
        /* Page setup */
        @page {
            size: A4 portrait;
            margin: 5mm 5mm !important;
        }
        
        /* Hide empty pages */
        .id-card-grid:empty {
            display: none !important;
        }
    }

    @media (max-width: 576px) {
        .id-card-grid {
            grid-template-columns: 1fr;
        }
        .id-card-vertical {
            width: 100%;
            max-width: 340px;
        }
        .id-card-qr-wrapper {
            flex-direction: column;
            gap: 0.5rem;
        }
        .id-card-branch-info {
            align-items: center;
        }
    }
</style>

<!-- UI - Hidden in print -->
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4 no-print">
    <div>
        <h5 class="fw-bold mb-0" style="font-size:1rem;color:#1b5e20;">
            <i class="bi bi-id-card text-success"></i> Employee ID Cards
        </h5>
        <span style="font-size:0.72rem;color:#6d8f6d;">Generate and print professional ID cards</span>
    </div>
    <div class="d-flex gap-2 flex-wrap">
        <button onclick="window.print()" class="btn-soft-clean">
            <i class="bi bi-printer"></i> Print Cards
        </button>
        <button onclick="selectAllEmployees()" class="btn-soft-clean">
            <i class="bi bi-check-all"></i> Select All
        </button>
    </div>
</div>

<!-- Selection Controls -->
<div class="card-clean mb-4 no-print">
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
        <div class="d-flex align-items-center gap-3 flex-wrap">
            <span style="font-size:0.78rem;color:#2e7d32;">
                <i class="bi bi-people me-1"></i> <span id="selectedCount" style="font-weight:600;">0</span> selected
            </span>
            <button onclick="generateSelected()" class="btn-primary-clean">
                <i class="bi bi-id-card me-1"></i> Generate
            </button>
            <button onclick="generateAll()" class="btn-success-clean">
                <i class="bi bi-files me-1"></i> Generate All
            </button>
        </div>
        <div class="input-group" style="max-width:280px;">
            <span class="input-group-text bg-white border-end-0" style="border-radius:40px 0 0 40px;border-color:#c8e6c9;">
                <i class="bi bi-search text-muted" style="color:#6d8f6d;"></i>
            </span>
            <input type="text" id="searchEmployee" class="form-control border-start-0" placeholder="Search..." style="border-radius:0 40px 40px 0;border-color:#c8e6c9;font-size:0.8rem;">
        </div>
    </div>
</div>

<!-- Employee Table -->
<div class="table-responsive border-0 shadow-sm rounded-3 no-print" style="border:1px solid #e8f5e9;">
    <table class="table table-hover align-middle mb-0">
        <thead style="background:#f4f9f4;color:#1b5e20;">
            <tr>
                <th style="width:40px;">
                    <input type="checkbox" id="selectAll" onchange="toggleAllEmployees(this)" style="accent-color:#2e7d32;">
                </th>
                <th>Employee</th>
                <th>Role</th>
                <th>Branch</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody id="employeeList">
            <?php if (empty($employees)): ?>
                <tr><td colspan="5" class="text-center py-5 text-muted" style="color:#6d8f6d;">No employees found.</td></tr>
            <?php else: ?>
                <?php foreach ($employees as $emp): ?>
                    <tr class="employee-row" data-id="<?= $emp['id'] ?>" data-name="<?= esc($emp['username']) ?>">
                        <td>
                            <input type="checkbox" class="employee-checkbox" value="<?= $emp['id'] ?>" onchange="updateSelectedCount()" style="accent-color:#2e7d32;">
                        </td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <?php if ($emp['photo']): ?>
                                    <img src="<?= site_url($emp['photo']) ?>" alt="" style="width:35px;height:35px;border-radius:50%;object-fit:cover;border:2px solid #c8e6c9;">
                                <?php else: ?>
                                    <div style="width:35px;height:35px;border-radius:50%;background:#e8f5e9;display:flex;align-items:center;justify-content:center;border:2px solid #c8e6c9;">
                                        <i class="bi bi-person" style="color:#66bb6a;"></i>
                                    </div>
                                <?php endif; ?>
                                <div>
                                    <div style="font-size:0.82rem;font-weight:500;color:#1b5e20;"><?= esc($emp['username']) ?></div>
                                    <div style="font-size:0.65rem;color:#6d8f6d;"><?= 'EMP-' . str_pad($emp['id'], 5, '0', STR_PAD_LEFT) ?></div>
                                </div>
                            </div>
                        </td>
                        <td style="font-size:0.78rem;color:#2e7d32;"><?= esc($emp['role_name'] ?? 'Staff') ?></td>
                        <td style="font-size:0.78rem;color:#2e7d32;"><?= esc($emp['branch_name'] ?? 'Main Branch') ?></td>
                        <td>
                            <span class="<?= ($emp['user_status'] ?? 'active') === 'active' ? 'badge-active' : 'badge-inactive' ?>">
                                <?= esc(ucfirst($emp['user_status'] ?? 'active')) ?>
                            </span>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- ID Cards -->
<div id="idCardContainer" class="id-card-grid"></div>

<script>
let selectedEmployees = [];

function toggleAllEmployees(master) {
    document.querySelectorAll('.employee-checkbox').forEach(cb => cb.checked = master.checked);
    updateSelectedCount();
}

function updateSelectedCount() {
    selectedEmployees = [];
    document.querySelectorAll('.employee-checkbox:checked').forEach(cb => selectedEmployees.push(cb.value));
    document.getElementById('selectedCount').textContent = selectedEmployees.length;
}

function selectAllEmployees() {
    document.querySelectorAll('.employee-checkbox').forEach(cb => cb.checked = true);
    updateSelectedCount();
}

function generateSelected() {
    if (!selectedEmployees.length) return alert('Please select at least one employee.');
    generateIDCards(selectedEmployees);
}

function generateAll() {
    const allIds = [];
    document.querySelectorAll('.employee-checkbox').forEach(cb => allIds.push(cb.value));
    if (!allIds.length) return alert('No employees found.');
    generateIDCards(allIds);
}

function generateIDCards(ids) {
    const container = document.getElementById('idCardContainer');
    container.innerHTML = `<div class="text-center py-5" style="grid-column:1/-1;">
        <div class="spinner-border text-success" role="status"></div>
        <div class="mt-2 text-muted" style="color:#6d8f6d;">Generating...</div>
    </div>`;
    
    fetch('<?= site_url('/admin/employees/generate-id-cards') ?>', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ employee_ids: ids })
    })
    .then(res => res.json())
    .then(data => {
        container.innerHTML = data.success ? data.html : 
            '<div class="text-center py-5 text-danger" style="grid-column:1/-1;color:#c62828;">Failed to generate cards.</div>';
    })
    .catch(() => {
        container.innerHTML = '<div class="text-center py-5 text-danger" style="grid-column:1/-1;color:#c62828;">Error generating cards.</div>';
    });
}

// Search
document.getElementById('searchEmployee').addEventListener('input', function() {
    const q = this.value.toLowerCase().trim();
    document.querySelectorAll('.employee-row').forEach(row => {
        row.style.display = row.dataset.name.toLowerCase().includes(q) ? '' : 'none';
    });
});

document.addEventListener('DOMContentLoaded', updateSelectedCount);
</script>

<?php include VIEWS_PATH . '/layout/admin_footer.php'; ?>