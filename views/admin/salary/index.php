<?php 
$activePage = 'salary';
include VIEWS_PATH . '/layout/admin_header.php'; 
?>

<!-- Payroll Header -->
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

<!-- Payroll Ledger Table -->
<div class="card border-0 shadow-sm p-4">
    <div class="table-responsive border-0 shadow-none">
        <table class="table table-hover align-middle mb-0" style="font-size: 0.85rem;">
            <thead>
                <tr>
                    <th>Employee Details</th>
                    <th>Role</th>
                    <th>Base Salary</th>
                    <th>Salary Advance</th>
                    <th>Bonus Additions</th>
                    <th>Deduction Subtractions</th>
                    <th>Net Payout</th>
                    <th>Payroll Status</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($salaries)): ?>
                    <tr>
                        <td colspan="9" class="text-center py-4 text-muted">No payroll entries logged for this month.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($salaries as $sal): ?>
                        <tr>
                            <td>
                                <div class="fw-semibold text-slate"><?= esc($sal['employee_name']) ?></div>
                                <span class="text-muted small">Month: <?= esc($sal['month_year']) ?></span>
                            </td>
                            <td><span class="small text-muted"><?= esc($sal['role_name']) ?></span></td>
                            <td class="fw-semibold">₹<?= esc(number_format((float)$sal['base_salary'], 2)) ?></td>
                            <td class="text-danger">- ₹<?= esc(number_format((float)$sal['advance'], 2)) ?></td>
                            <td class="text-success">+ ₹<?= esc(number_format((float)$sal['bonus'], 2)) ?></td>
                            <td class="text-danger">- ₹<?= esc(number_format((float)$sal['deduction'], 2)) ?></td>
                            <td class="fw-bold text-slate">₹<?= esc(number_format((float)$sal['net_salary'], 2)) ?></td>
                            <td>
                                <?php if ($sal['payment_status'] === 'paid'): ?>
                                    <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-2.5 py-1.5 rounded">Settled / Paid</span>
                                <?php else: ?>
                                    <span class="badge bg-warning bg-opacity-10 text-warning border border-warning border-opacity-25 px-2.5 py-1.5 rounded">Unpaid</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-end text-nowrap">
                                <?php if ($sal['payment_status'] !== 'paid'): ?>
                                    <button class="btn btn-sm btn-primary px-3 py-1 shadow-sm btn-pay-modal" 
                                            data-id="<?= $sal['id'] ?>" 
                                            data-name="<?= esc($sal['employee_name']) ?>" 
                                            data-base="<?= esc($sal['base_salary']) ?>"
                                            data-bs-toggle="modal" data-bs-target="#payModal">
                                        <i class="bi bi-wallet2 me-1"></i> Pay
                                    </button>
                                <?php else: ?>
                                    <a href="<?= site_url('/admin/salary/payslip/' . $sal['id']) ?>" class="btn btn-sm btn-light border px-2.5 py-1">
                                        <i class="bi bi-printer me-1"></i> Payslip
                                    </a>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal to Settle Salary Voucher -->
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

<script>
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
