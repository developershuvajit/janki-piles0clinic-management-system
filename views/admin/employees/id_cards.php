<?php 
$activePage = 'id_cards';
include VIEWS_PATH . '/layout/admin_header.php'; 
?>

<!-- ============================================
     PAGE CSS
     ============================================ -->
<link rel="stylesheet" href="<?= asset('css/datatable.css') ?>">

<style>
    .id-card-vertical {
        width: 340px;
        min-height: 490px;
        background: #ffffff;
        border-radius: 16px;
        border: 1px solid #e8ecf0;
        box-shadow: 0 4px 20px rgba(0,0,0,0.06);
        overflow: hidden;
        margin: 0 auto;
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
        transition: all 0.2s ease;
    }
    .id-card-vertical:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 30px rgba(0,0,0,0.1);
    }
    .id-card-header {
        background: linear-gradient(135deg, #0b1a2b, #1a365d);
        padding: 0.8rem 1.2rem 0.6rem;
        color: #fff;
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-bottom: 3px solid #3b82f6;
    }
    .id-card-clinic {
        display: flex;
        flex-direction: column;
        gap: 0.1rem;
    }
    .id-card-clinic .clinic-name {
        font-size: 0.7rem;
        font-weight: 700;
        letter-spacing: 0.5px;
        text-transform: uppercase;
        color: #e2e8f0;
        line-height: 1.2;
    }
    .id-card-clinic .clinic-sub {
        font-size: 0.45rem;
        font-weight: 400;
        color: #94a3b8;
        letter-spacing: 0.3px;
        text-transform: uppercase;
    }
    .id-card-type {
        font-size: 0.5rem;
        font-weight: 600;
        background: rgba(59,130,246,0.2);
        padding: 0.15rem 0.7rem;
        border-radius: 40px;
        color: #93bbfc;
        letter-spacing: 0.5px;
        border: 1px solid rgba(59,130,246,0.15);
        text-transform: uppercase;
    }
    .id-card-body {
        padding: 1rem 1.2rem 0.6rem;
        display: flex;
        flex-direction: column;
        align-items: center;
        text-align: center;
    }
    .id-card-photo {
        width: 85px;
        height: 85px;
        border-radius: 50%;
        background: #f1f5f9;
        border: 3px solid #e2e8f0;
        overflow: hidden;
        margin-bottom: 0.6rem;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        box-shadow: 0 2px 10px rgba(0,0,0,0.06);
    }
    .id-card-photo img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    .id-card-name {
        font-size: 1rem;
        font-weight: 700;
        color: #0b1a2b;
        margin-bottom: 0.05rem;
        letter-spacing: 0.3px;
        text-transform: uppercase;
    }
    .id-card-role {
        font-size: 0.7rem;
        color: #3b82f6;
        font-weight: 600;
        margin-bottom: 0.2rem;
        background: #e6f0ff;
        padding: 0.05rem 1rem;
        border-radius: 40px;
        display: inline-block;
    }
    .id-card-code {
        font-size: 0.6rem;
        color: #94a3b8;
        font-family: 'Courier New', monospace;
        background: #f1f5f9;
        padding: 0.05rem 0.8rem;
        border-radius: 40px;
        display: inline-block;
        letter-spacing: 0.5px;
        border: 1px solid #e2e8f0;
    }
    .id-card-footer {
        padding: 0.8rem 1.2rem 0.6rem;
        display: flex;
        flex-direction: column;
        align-items: center;
        border-top: 1px solid #f1f5f9;
        background: #fafcff;
    }
    .id-card-qr {
        width: 140px;
        height: 140px;
        background: #ffffff;
        border-radius: 10px;
        padding: 6px;
        border: 2px solid #e2e8f0;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 0.5rem;
        box-shadow: 0 2px 8px rgba(0,0,0,0.04);
    }
    .id-card-qr img {
        width: 100%;
        height: 100%;
        object-fit: contain;
    }
    .id-card-bottom {
        padding: 0.5rem 1.2rem 0.7rem;
        background: #f8fafc;
        border-top: 1px solid #f1f5f9;
        display: flex;
        justify-content: center;
        gap: 1.5rem;
        align-items: center;
        flex-wrap: wrap;
    }
    .id-card-bottom .info-item {
        display: flex;
        align-items: center;
        gap: 0.3rem;
        font-size: 0.65rem;
        color: #475569;
    }
    .id-card-bottom .info-item i {
        color: #3b82f6;
        font-size: 0.7rem;
    }
    .id-card-bottom .info-item .label {
        font-weight: 500;
        color: #0b1a2b;
    }
    .id-card-validity {
        padding: 0.3rem 1.2rem;
        background: #f1f5f9;
        border-top: 1px solid #e2e8f0;
        text-align: center;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .id-card-valid {
        font-size: 0.5rem;
        color: #6b7a8f;
        font-weight: 500;
        text-transform: uppercase;
        letter-spacing: 0.3px;
    }
    .id-card-valid i {
        margin-right: 3px;
        color: #0f7b4a;
        font-size: 0.45rem;
    }
    .id-card-issued {
        font-size: 0.45rem;
        color: #94a3b8;
        font-weight: 400;
    }
    .id-card-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(360px, 1fr));
        gap: 2rem;
        margin-top: 1.5rem;
        justify-items: center;
    }
    .btn-primary-clean {
        background: #2563eb;
        color: #fff;
        border: 1px solid #2563eb;
        padding: 0.4rem 1.2rem;
        border-radius: 40px;
        font-size: 0.78rem;
        font-weight: 500;
        transition: all 0.15s;
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        cursor: pointer;
        text-decoration: none;
    }
    .btn-primary-clean:hover {
        background: #1d4ed8;
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(37,99,235,0.25);
    }
    .btn-success-clean {
        background: #0f7b4a;
        color: #fff;
        border: 1px solid #0f7b4a;
        padding: 0.4rem 1.2rem;
        border-radius: 40px;
        font-size: 0.78rem;
        font-weight: 500;
        transition: all 0.15s;
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        cursor: pointer;
        text-decoration: none;
    }
    .btn-success-clean:hover {
        background: #0b6e44;
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(15,123,74,0.25);
    }
    .btn-soft-clean {
        background: #f1f5f9;
        color: #1e293b;
        border: 1px solid #e2e8f0;
        padding: 0.4rem 1.2rem;
        border-radius: 40px;
        font-size: 0.78rem;
        font-weight: 500;
        transition: all 0.15s;
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        cursor: pointer;
        text-decoration: none;
    }
    .btn-soft-clean:hover {
        background: #e2e8f0;
        transform: translateY(-1px);
    }
    .badge-active {
        background: #e6f5ed;
        color: #0f7b4a;
        border: 1px solid #b8e0cf;
        border-radius: 40px;
        padding: 0.1rem 0.6rem;
        font-size: 0.65rem;
        font-weight: 500;
    }
    .badge-inactive {
        background: #ffe9e9;
        color: #b33c3c;
        border: 1px solid #fad5d5;
        border-radius: 40px;
        padding: 0.1rem 0.6rem;
        font-size: 0.65rem;
        font-weight: 500;
    }
    .card-clean {
        background: #ffffff;
        border: 1px solid #f1f5f9;
        border-radius: 14px;
        padding: 0.8rem 1.2rem;
        box-shadow: 0 1px 4px rgba(0,0,0,0.04);
    }
    @media print {
        body * { visibility: hidden; }
        #idCardContainer, #idCardContainer * { visibility: visible; }
        #idCardContainer { position: absolute; left: 0; top: 0; width: 100%; }
        .id-card-grid {
            display: grid !important;
            grid-template-columns: repeat(3, 1fr) !important;
            gap: 8px !important;
            padding: 8px !important;
            margin: 0 !important;
            justify-items: center !important;
        }
        .id-card-vertical {
            width: 100% !important;
            max-width: 190px !important;
            min-height: 260px !important;
            margin: 0 !important;
            box-shadow: none !important;
            border: 1px solid #ddd !important;
            border-radius: 6px !important;
            page-break-inside: avoid !important;
            break-inside: avoid !important;
        }
        .id-card-vertical:nth-child(9n) { page-break-after: always !important; }
        .id-card-header { padding: 0.3rem 0.6rem 0.2rem !important; }
        .id-card-header .clinic-name { font-size: 0.45rem !important; }
        .id-card-header .clinic-sub { font-size: 0.3rem !important; }
        .id-card-type { font-size: 0.3rem !important; padding: 0.05rem 0.3rem !important; }
        .id-card-body { padding: 0.3rem 0.6rem 0.2rem !important; }
        .id-card-photo { width: 40px !important; height: 40px !important; margin-bottom: 0.2rem !important; }
        .id-card-name { font-size: 0.55rem !important; }
        .id-card-role { font-size: 0.4rem !important; padding: 0.02rem 0.4rem !important; }
        .id-card-code { font-size: 0.35rem !important; padding: 0.02rem 0.3rem !important; }
        .id-card-footer { padding: 0.3rem 0.6rem 0.2rem !important; }
        .id-card-qr { width: 80px !important; height: 80px !important; padding: 3px !important; }
        .id-card-bottom { padding: 0.2rem 0.6rem !important; gap: 0.5rem !important; }
        .id-card-bottom .info-item { font-size: 0.4rem !important; }
        .id-card-validity { padding: 0.1rem 0.6rem !important; }
        .id-card-valid { font-size: 0.3rem !important; }
        .id-card-issued { font-size: 0.3rem !important; }
        @page { size: A4 portrait; margin: 4mm 4mm !important; }
        .no-print { display: none !important; }
    }
    @media (max-width: 576px) {
        .id-card-grid { grid-template-columns: 1fr; }
        .id-card-vertical { width: 100%; max-width: 340px; }
        .id-card-qr { width: 120px; height: 120px; }
    }
</style>

<!-- ============================================
     PAGE HTML
     ============================================ -->
<div class="datatable-wrapper mt-4">
    <div class="datatable-header no-print">
        <h5>Employee ID Cards <small><?= count($employees ?? []) ?> employees</small></h5>
        <div class="d-flex gap-2">
            <button onclick="window.print()" class="btn-success-clean" id="printBtn" style="display:none;">
                <i class="bi bi-printer"></i> Print Cards
            </button>
            <button onclick="selectAllEmployees()" class="btn-soft-clean">
                <i class="bi bi-check-all"></i> Select All
            </button>
        </div>
    </div>

    <div class="card-clean mb-4 no-print">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
            <div class="d-flex align-items-center gap-3 flex-wrap">
                <span style="font-size:0.78rem;color:#6b7a8f;">
                    <i class="bi bi-people me-1"></i> <span id="selectedCount" style="font-weight:600;color:#0b1a2b;">0</span> selected
                </span>
                <button onclick="generateSelected()" class="btn-primary-clean">
                    <i class="bi bi-id-card me-1"></i> Generate Selected
                </button>
                <button onclick="generateAll()" class="btn-success-clean">
                    <i class="bi bi-files me-1"></i> Generate All
                </button>
                <button onclick="clearCards()" class="btn-soft-clean">
                    <i class="bi bi-x-circle me-1"></i> Clear
                </button>
            </div>
        </div>
    </div>

    <div class="table-responsive">
        <table id="employeesTable" class="table-custom" style="width:100%">
            <thead>
                <tr>
                    <th style="width:40px;"><input type="checkbox" id="selectAll" onchange="toggleAllEmployees(this)"></th>
                    <th>Employee</th>
                    <th>Role</th>
                    <th>Branch</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($employees)): ?>
                    <?php foreach ($employees as $emp): ?>
                        <tr class="employee-row" data-id="<?= $emp['id'] ?>">
                            <td><input type="checkbox" class="employee-checkbox" value="<?= $emp['id'] ?>" onchange="updateSelectedCount()"></td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <?php if (!empty($emp['photo']) && file_exists(PUBLIC_PATH . '/' . $emp['photo'])): ?>
                                        <img src="<?= site_url($emp['photo']) ?>" alt="" style="width:35px;height:35px;border-radius:50%;object-fit:cover;border:2px solid #e2e8f0;">
                                    <?php else: ?>
                                        <div style="width:35px;height:35px;border-radius:50%;background:#f1f5f9;display:flex;align-items:center;justify-content:center;border:2px solid #e2e8f0;">
                                            <i class="bi bi-person" style="color:#94a3b8;"></i>
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
                                <span class="<?= ($emp['user_status'] ?? 'active') === 'active' ? 'badge-active' : 'badge-inactive' ?>">
                                    <?= esc(ucfirst($emp['user_status'] ?? 'active')) ?>
                                </span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="5" style="text-align:center;padding:2.5rem 1rem;color:#94a3b8;">No employees found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<div id="idCardContainer" class="id-card-grid"></div>

<!-- ============================================
     DATATABLES LIBS + INIT
     ============================================ -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css">

<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>

<script>
$(document).ready(function() {
    if ($('#employeesTable').length) {
        var table = $('#employeesTable').DataTable({
            dom: '<"d-flex flex-wrap align-items-center justify-content-between gap-2 p-2"lBf>t<"d-flex flex-wrap align-items-center justify-content-between gap-2 p-2"ip>',
            buttons: [
                { extend: 'copy', text: '<i class="bi bi-copy"></i> Copy', className: 'btn btn-sm btn-outline-secondary' },
                { extend: 'csv', text: '<i class="bi bi-file-earmark-spreadsheet"></i> CSV', className: 'btn btn-sm btn-outline-secondary' },
                { extend: 'excel', text: '<i class="bi bi-file-earmark-excel"></i> Excel', className: 'btn btn-sm btn-outline-secondary' },
                { extend: 'pdf', text: '<i class="bi bi-file-earmark-pdf"></i> PDF', className: 'btn btn-sm btn-outline-secondary' },
                { extend: 'print', text: '<i class="bi bi-printer"></i> Print', className: 'btn btn-sm btn-outline-secondary' }
            ],
            pageLength: 25,
            lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "All"]],
            order: [[1, 'asc']],
            columnDefs: [
                { orderable: false, targets: [0, 4] },
                { searchable: false, targets: [0] }
            ],
            language: {
                search: "Search:",
                lengthMenu: "Show _MENU_",
                info: "_START_ – _END_ of _TOTAL_",
                infoEmpty: "No employees found",
                infoFiltered: "(filtered from _MAX_ total)",
                zeroRecords: "No matching employees found"
            }
        });
        
        table.on('draw', function() {
            updateSelectedCount();
        });
    }
});

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

function clearCards() {
    document.getElementById('idCardContainer').innerHTML = '';
    document.getElementById('printBtn').style.display = 'none';
}

function generateSelected() {
    if (!selectedEmployees.length) {
        alert('Please select at least one employee.');
        return;
    }
    generateIDCards(selectedEmployees);
}

function generateAll() {
    const allIds = [];
    document.querySelectorAll('.employee-checkbox').forEach(cb => allIds.push(cb.value));
    if (!allIds.length) {
        alert('No employees found.');
        return;
    }
    generateIDCards(allIds);
}

function generateIDCards(ids) {
    const container = document.getElementById('idCardContainer');
    container.innerHTML = '<div class="text-center py-5" style="grid-column:1/-1;">' +
        '<div class="spinner-border text-primary" role="status"></div>' +
        '<div class="mt-2 text-muted">Generating ID cards...</div>' +
    '</div>';
    
    // Use FormData instead of JSON
    const formData = new FormData();
    ids.forEach(id => {
        formData.append('employee_ids[]', id);
    });

    fetch('<?= site_url('/admin/employees/generate-id-cards') ?>', {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.text())
    .then(text => {
        try {
            const data = JSON.parse(text);
            if (data.success && data.html) {
                container.innerHTML = data.html;
                document.getElementById('printBtn').style.display = 'inline-flex';
            } else {
                container.innerHTML = '<div class="text-center py-5 text-danger" style="grid-column:1/-1;">' +
                    '<i class="bi bi-exclamation-triangle-fill" style="font-size:2rem;"></i>' +
                    '<div class="mt-2">' + (data.message || 'Failed to generate ID cards.') + '</div>' +
                '</div>';
            }
        } catch (e) {
            console.error('Parse error:', e);
            console.error('Response:', text);
            container.innerHTML = '<div class="text-center py-5 text-danger" style="grid-column:1/-1;">' +
                '<i class="bi bi-exclamation-triangle-fill" style="font-size:2rem;"></i>' +
                '<div class="mt-2">Error generating ID cards. Please try again.</div>' +
                '<div class="mt-1 text-muted" style="font-size:0.8rem;">' + text.substring(0, 100) + '</div>' +
            '</div>';
        }
    })
    .catch(err => {
        console.error('Error:', err);
        container.innerHTML = '<div class="text-center py-5 text-danger" style="grid-column:1/-1;">' +
            '<i class="bi bi-exclamation-triangle-fill" style="font-size:2rem;"></i>' +
            '<div class="mt-2">Error generating ID cards. Please try again.</div>' +
        '</div>';
    });
}

document.addEventListener('DOMContentLoaded', function() {
    updateSelectedCount();
    document.getElementById('printBtn').style.display = 'none';
});
</script>

<?php include VIEWS_PATH . '/layout/admin_footer.php'; ?>