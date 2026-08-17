<?php 
$activePage = 'salary';
include VIEWS_PATH . '/layout/admin_header.php'; 
?>

<!-- ============================================
     PAGE CSS
     ============================================ -->
<link rel="stylesheet" href="<?= asset('css/datatable.css') ?>">

<!-- ============================================
     PAYROLL HEADER
     ============================================ -->
<div class="d-flex justify-content-between align-items-center mb-4 text-slate">
    <div>
        <h5 class="fw-bold mb-1"><i class="bi bi-cash-stack text-success me-2"></i>Employee Payroll Manager</h5>
        <p class="text-muted small mb-0">Generate monthly baseline payouts and apply bonuses/deductions to generate employee payslips.</p>
    </div>
    
    <!-- Month Selector -->
    <div style="max-width: 250px;">
        <label class="form-label small fw-semibold">Select Month-Year</label>
        <input type="month" class="form-control form-control-sm" value="<?= esc(date('Y-m', strtotime('01-' . $monthYear))) ?>" onchange="let parts = this.value.split('-'); window.location.href='<?= site_url('/admin/salary?month_year=') ?>' + parts[1] + '-' + parts[0]">
    </div>
</div>

<!-- ============================================
     PAYROLL LEDGER TABLE
     ============================================ -->
<div class="datatable-wrapper mt-4">
    <div class="datatable-header">
        <h5>Payroll Ledger <small><?= count($salaries ?? []) ?> employees</small></h5>
    </div>

    <div class="table-responsive">
        <table id="salaryTable" class="table-custom" style="width:100%">
            <thead>
                <tr>
                    <th class="sno">#</th>
                    <th style="min-width:160px;">Employee Details</th>
                    <th style="width:120px;">Role</th>
                    <th style="width:120px;">Base Salary</th>
                    <th style="width:120px;">Salary Advance</th>
                    <th style="width:120px;">Bonus Additions</th>
                    <th style="width:120px;">Deduction Subtractions</th>
                    <th style="width:120px;">Net Payout</th>
                    <th style="width:130px;">Payroll Status</th>
                    <th style="width:150px;">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($salaries)):
                    $sn = 1;
                    foreach ($salaries as $sal): ?>
                        <tr>
                            <td class="sno"><?= $sn++ ?></td>
                            <td>
                                <div class="fw-bold text-slate"><?= esc($sal['employee_name']) ?></div>
                                <span class="text-muted small" style="font-size: 0.78rem;">Month: <?= esc($sal['month_year']) ?></span>
                            </td>
                            <td>
                                <span class="badge bg-secondary bg-opacity-10 text-dark">
                                    <?= esc($sal['role_name']) ?>
                                </span>
                            </td>
                            <td class="fw-semibold">₹<?= esc(number_format((float)$sal['base_salary'], 2)) ?></td>
                            <td class="text-danger">- ₹<?= esc(number_format((float)$sal['advance'], 2)) ?></td>
                            <td class="text-success">+ ₹<?= esc(number_format((float)$sal['bonus'], 2)) ?></td>
                            <td class="text-danger">- ₹<?= esc(number_format((float)$sal['deduction'], 2)) ?></td>
                            <td class="fw-bold text-slate">₹<?= esc(number_format((float)$sal['net_salary'], 2)) ?></td>
                            <td>
                                <?php if ($sal['payment_status'] === 'paid'): ?>
                                    <span class="badge-status active">Settled / Paid</span>
                                <?php else: ?>
                                    <span class="badge bg-warning bg-opacity-10 text-warning border border-warning border-opacity-25 px-2.5 py-1.5 rounded">Unpaid</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="action-group">
                                    <?php if ($sal['payment_status'] !== 'paid'): ?>
                                        <button class="btn-action btn-pay-modal" 
                                                data-id="<?= $sal['id'] ?>" 
                                                data-name="<?= esc($sal['employee_name']) ?>" 
                                                data-base="<?= esc($sal['base_salary']) ?>"
                                                data-bs-toggle="modal" data-bs-target="#payModal"
                                                title="Pay Salary" style="color: #10b981;">
                                            <i class="bi bi-wallet2"></i>
                                        </button>
                                    <?php else: ?>
                                        <a href="<?= site_url('/admin/salary/payslip/' . $sal['id']) ?>" class="btn-action" title="Payslip" style="color: #6366f1;">
                                            <i class="bi bi-printer"></i>
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach;
                else: ?>
                    <tr>
                        <td colspan="10" style="text-align:center;padding:2.5rem 1rem;color:#94a3b8;">
                            <i class="bi bi-cash-stack fs-3 d-block mb-2"></i>
                            No payroll entries logged for this month.
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- ============================================
     MODAL TO SETTLE SALARY VOUCHER
     ============================================ -->
<div class="modal fade" id="payModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content border-0 shadow-lg text-slate">
            <form action="<?= site_url('/admin/salary/settle') ?>" method="POST">
                <?= csrf_field() ?>
                <input type="hidden" name="salary_id" id="modal-salary-id">

                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title fw-bold"><i class="bi bi-cash-coin me-2"></i>Process Salary Payroll</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                
                <div class="modal-body p-4">
                    <div class="bg-light p-3 rounded-3 mb-3 small">
                        <strong>Employee:</strong> <span id="modal-emp-name"></span><br>
                        <strong>Baseline Base Salary:</strong> ₹<span id="modal-base-salary"></span>
                    </div>

                    <div class="mb-3">
                        <label for="modal-advance" class="form-label small fw-semibold">Deduct Advance Paid (INR)</label>
                        <input type="number" class="form-control form-control-sm" id="modal-advance" name="advance" value="0.00" step="100.00" min="0.00">
                    </div>
                    
                    <div class="mb-3">
                        <label for="modal-bonus" class="form-label small fw-semibold">Add Bonus (INR)</label>
                        <input type="number" class="form-control form-control-sm text-success" id="modal-bonus" name="bonus" value="0.00" step="100.00" min="0.00">
                    </div>

                    <div class="mb-3">
                        <label for="modal-deduction" class="form-label small fw-semibold">Deduct Penalty / LOP (INR)</label>
                        <input type="number" class="form-control form-control-sm text-danger" id="modal-deduction" name="deduction" value="0.00" step="100.00" min="0.00">
                    </div>
                </div>

                <div class="modal-footer pt-0 border-0">
                    <button type="button" class="btn btn-outline-secondary btn-sm px-3" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary btn-sm px-4 shadow-sm">Complete Payroll & Settle</button>
                </div>
            </form>
        </div>
    </div>
</div>

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
    $('#salaryTable').DataTable({
        pageLength: 25,
        responsive: true,
        dom: 'Bfrtip',
        buttons: [
            'copy', 'csv', 'excel', 'pdf', 'print'
        ],
        order: [[0, 'asc']]
    });
});

// Modal handler
document.addEventListener('DOMContentLoaded', function() {
    const payButtons = document.querySelectorAll('.btn-pay-modal');
    
    payButtons.forEach(btn => {
        btn.addEventListener('click', function() {
            document.getElementById('modal-salary-id').value = this.dataset.id;
            document.getElementById('modal-emp-name').innerText = this.dataset.name;
            document.getElementById('modal-base-salary').innerText = parseFloat(this.dataset.base).toFixed(2);
        });
    });
});
</script>

<?php include VIEWS_PATH . '/layout/admin_footer.php'; ?>