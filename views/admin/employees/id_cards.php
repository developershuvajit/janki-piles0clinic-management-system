<?php 
$activePage = 'id_cards';
include VIEWS_PATH . '/layout/admin_header.php'; 
?>

<style>
    /* ===== ID CARD STYLES ===== */
    .id-card-vertical {
        width: 320px;
        min-height: 480px;
        background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
        border-radius: 16px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 8px 30px rgba(0,0,0,0.1);
        overflow: hidden;
        margin: 0 auto;
        font-family: 'Inter', sans-serif;
        transition: transform 0.2s;
    }
    .id-card-vertical:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 40px rgba(0,0,0,0.15);
    }

    /* Card Header */
    .id-card-header {
        background: linear-gradient(135deg, #0b1a2b, #1a365d);
        padding: 1rem 1.2rem 0.8rem;
        color: #fff;
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-bottom: 3px solid #3b82f6;
    }
    .id-card-clinic {
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    .id-card-clinic .clinic-icon {
        font-size: 1.2rem;
    }
    .id-card-clinic .clinic-name {
        font-size: 0.75rem;
        font-weight: 700;
        letter-spacing: 0.3px;
        text-transform: uppercase;
        color: #e2e8f0;
    }
    .id-card-type {
        font-size: 0.55rem;
        font-weight: 600;
        background: rgba(59,130,246,0.3);
        padding: 0.15rem 0.6rem;
        border-radius: 40px;
        color: #93bbfc;
        letter-spacing: 0.5px;
        border: 1px solid rgba(59,130,246,0.2);
    }

    /* Card Body */
    .id-card-body {
        padding: 1.2rem 1.2rem 0.8rem;
        display: flex;
        flex-direction: column;
        align-items: center;
        text-align: center;
    }
    .id-card-photo {
        width: 90px;
        height: 90px;
        border-radius: 50%;
        background: #f1f5f9;
        border: 3px solid #e2e8f0;
        overflow: hidden;
        margin-bottom: 0.8rem;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        box-shadow: 0 4px 12px rgba(0,0,0,0.08);
    }
    .id-card-photo img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    .id-card-name {
        font-size: 1.1rem;
        font-weight: 700;
        color: #0b1a2b;
        margin-bottom: 0.1rem;
    }
    .id-card-role {
        font-size: 0.78rem;
        color: #3b82f6;
        font-weight: 500;
        margin-bottom: 0.2rem;
    }
    .id-card-code {
        font-size: 0.7rem;
        color: #94a3b8;
        font-family: monospace;
        background: #f1f5f9;
        padding: 0.1rem 0.8rem;
        border-radius: 40px;
        display: inline-block;
    }

    /* Card Footer with QR */
    .id-card-footer {
        padding: 0.8rem 1.2rem 1rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-top: 1px solid #f1f5f9;
        background: #fafcff;
    }
    .id-card-qr {
        width: 120px;
        height: 120px;
        background: #fff;
        border-radius: 8px;
        padding: 4px;
        border: 1px solid #e2e8f0;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }
    .id-card-qr img {
        width: 100%;
        height: 100%;
        object-fit: contain;
    }
    .id-card-branch {
        font-size: 0.7rem;
        color: #475569;
        font-weight: 500;
        display: flex;
        align-items: center;
        gap: 0.3rem;
        flex: 1;
        justify-content: flex-end;
    }
    .id-card-branch i {
        color: #3b82f6;
        font-size: 0.9rem;
    }

    /* Card Bottom */
    .id-card-bottom {
        padding: 0.4rem 1.2rem;
        background: #f8fafc;
        border-top: 1px solid #f1f5f9;
        text-align: center;
    }
    .id-card-valid {
        font-size: 0.55rem;
        color: #94a3b8;
        font-weight: 500;
        letter-spacing: 0.3px;
    }

    /* ===== SELECTION PAGE STYLES ===== */
    .id-card-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(340px, 1fr));
        gap: 2rem;
        margin-top: 1.5rem;
        justify-items: center;
    }
    @media print {
        .no-print { display: none !important; }
        .id-card-grid { display: block; }
        .id-card-vertical { 
            page-break-inside: avoid; 
            margin-bottom: 2rem;
            box-shadow: none;
            border: 1px solid #ddd;
        }
    }
</style>

<!-- Header -->
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4 no-print">
    <div>
        <h5 class="fw-bold text-slate mb-0" style="font-size:1rem;">
            <i class="bi bi-id-card text-primary"></i> Employee ID Cards
        </h5>
        <span style="font-size:0.72rem;color:#94a3b8;">Generate and print employee identification cards with QR codes</span>
    </div>
    <div class="d-flex gap-2">
        <button onclick="window.print()" class="btn-soft-clean no-print">
            <i class="bi bi-printer"></i> Print All
        </button>
        <button onclick="selectAllEmployees()" class="btn-soft-clean no-print">
            <i class="bi bi-check-all"></i> Select All
        </button>
    </div>
</div>

<!-- Employee Selection -->
<div class="card-clean mb-4 no-print">
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
        <div class="d-flex align-items-center gap-3">
            <span style="font-size:0.78rem;color:#6b7a8f;">
                <i class="bi bi-people me-1"></i> <span id="selectedCount">0</span> employees selected
            </span>
            <button onclick="generateSelected()" class="btn-primary-clean">
                <i class="bi bi-id-card me-1"></i> Generate ID Cards
            </button>
            <button onclick="generateAll()" class="btn-soft-clean" style="background:#0f7b4a;color:#fff;border-color:#0f7b4a;">
                <i class="bi bi-files me-1"></i> Generate All
            </button>
        </div>
        <div class="input-group" style="max-width:300px;">
            <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-muted"></i></span>
            <input type="text" id="searchEmployee" class="form-control border-start-0" placeholder="Search employees..." style="border-radius:0 40px 40px 0;">
        </div>
    </div>
</div>

<!-- Employee List -->
<div class="table-responsive border-0 shadow-sm rounded-3 no-print">
    <table class="table table-hover align-middle mb-0">
        <thead class="bg-light text-slate">
            <tr>
                <th style="width:40px;">
                    <input type="checkbox" id="selectAll" onchange="toggleAllEmployees(this)">
                </th>
                <th>Employee</th>
                <th>Role</th>
                <th>Branch</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody id="employeeList">
            <?php if (empty($employees)): ?>
                <tr><td colspan="5" class="text-center py-5 text-muted">No employees found.</td></tr>
            <?php else: ?>
                <?php foreach ($employees as $emp): ?>
                    <tr class="employee-row" data-id="<?= $emp['id'] ?>" data-name="<?= esc($emp['username']) ?>">
                        <td>
                            <input type="checkbox" class="employee-checkbox" value="<?= $emp['id'] ?>" onchange="updateSelectedCount()">
                        </td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <?php if ($emp['photo']): ?>
                                    <img src="<?= site_url($emp['photo']) ?>" alt="Photo" style="width:35px;height:35px;border-radius:50%;object-fit:cover;border:1px solid #e2e8f0;">
                                <?php else: ?>
                                    <div style="width:35px;height:35px;border-radius:50%;background:#f1f4f8;display:flex;align-items:center;justify-content:center;">
                                        <i class="bi bi-person text-secondary"></i>
                                    </div>
                                <?php endif; ?>
                                <div>
                                    <div style="font-size:0.82rem;font-weight:500;color:#0b1a2b;"><?= esc($emp['username']) ?></div>
                                    <div style="font-size:0.65rem;color:#94a3b8;"><?= 'EMP-' . str_pad($emp['id'], 5, '0', STR_PAD_LEFT) ?></div>
                                </div>
                            </div>
                        </td>
                        <td style="font-size:0.78rem;"><?= esc($emp['role_name'] ?? 'Staff') ?></td>
                        <td style="font-size:0.78rem;"><?= esc($emp['branch_name'] ?? 'Main Branch') ?></td>
                        <td>
                            <span class="<?= ($emp['user_status'] ?? 'active') === 'active' ? 'badge-active' : 'badge-inactive' ?>" style="font-size:0.65rem;padding:0.15rem 0.7rem;">
                                <?= esc(ucfirst($emp['user_status'] ?? 'active')) ?>
                            </span>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- ID Card Preview Area -->
<div id="idCardContainer" class="id-card-grid">
    <!-- ID cards will be generated here -->
</div>

<script>
let selectedEmployees = [];

function toggleAllEmployees(master) {
    document.querySelectorAll('.employee-checkbox').forEach(cb => {
        cb.checked = master.checked;
    });
    updateSelectedCount();
}

function updateSelectedCount() {
    selectedEmployees = [];
    document.querySelectorAll('.employee-checkbox:checked').forEach(cb => {
        selectedEmployees.push(cb.value);
    });
    document.getElementById('selectedCount').textContent = selectedEmployees.length;
}

function selectAllEmployees() {
    document.querySelectorAll('.employee-checkbox').forEach(cb => {
        cb.checked = true;
    });
    updateSelectedCount();
}

function generateSelected() {
    if (selectedEmployees.length === 0) {
        alert('Please select at least one employee.');
        return;
    }
    generateIDCards(selectedEmployees);
}

function generateAll() {
    const allIds = [];
    document.querySelectorAll('.employee-checkbox').forEach(cb => {
        allIds.push(cb.value);
    });
    if (allIds.length === 0) {
        alert('No employees found to generate ID cards.');
        return;
    }
    generateIDCards(allIds);
}

function generateIDCards(ids) {
    const container = document.getElementById('idCardContainer');
    container.innerHTML = '<div class="text-center py-5" style="grid-column:1/-1;"><div class="spinner-border text-primary" role="status"></div><div class="mt-2 text-muted">Generating ID cards...</div></div>';
    
    fetch('<?= site_url('/admin/employees/generate-id-cards') ?>', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ employee_ids: ids })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            container.innerHTML = data.html;
        } else {
            container.innerHTML = '<div class="text-center py-5 text-danger" style="grid-column:1/-1;">Failed to generate ID cards. Please try again.</div>';
        }
    })
    .catch(err => {
        container.innerHTML = '<div class="text-center py-5 text-danger" style="grid-column:1/-1;">Error generating ID cards.</div>';
        console.error(err);
    });
}

// Search filter
document.getElementById('searchEmployee').addEventListener('input', function() {
    const query = this.value.toLowerCase();
    document.querySelectorAll('.employee-row').forEach(row => {
        const name = row.dataset.name.toLowerCase();
        row.style.display = name.includes(query) ? '' : 'none';
    });
});
</script>

<?php include VIEWS_PATH . '/layout/admin_footer.php'; ?>