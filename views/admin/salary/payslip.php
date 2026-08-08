<?php 
$activePage = 'salary';
include VIEWS_PATH . '/layout/admin_header.php'; 
?>

<!-- Print Controls -->
<div class="d-print-none text-end mb-4">
    <button onclick="window.print()" class="btn btn-primary btn-sm px-4 shadow-sm">
        <i class="bi bi-printer-fill me-1"></i> Print Payslip
    </button>
    <a href="<?= site_url('/admin/salary') ?>" class="btn btn-outline-secondary btn-sm px-3 ms-2">
        Back to Payrolls
    </a>
</div>

<!-- Payslip Container -->
<div class="card border-0 shadow-sm p-5 payslip-print-container mx-auto text-slate" style="max-width: 750px; background: #fff; border: 3px double #dee2e6 !important;">
    
    <!-- Clinic Header -->
    <div class="text-center mb-4 pb-3 border-bottom">
        <h3 class="fw-bold mb-1 text-slate"><i class="bi bi-heart-pulse-fill text-success me-1"></i>MEDCLINIC HEALTHCARE</h3>
        <p class="text-muted small mb-0">Clinic Branch Office: <?= esc($slip['branch_name']) ?></p>
    </div>

    <!-- Title -->
    <div class="text-center mb-4">
        <h4 class="fw-bold text-uppercase" style="letter-spacing: 1px; text-decoration: underline;">Salary Pay Slip</h4>
        <div class="text-muted small mt-1">Salary Month: <strong><?= esc($slip['month_year']) ?></strong></div>
    </div>

    <!-- Employee Metadata Table -->
    <div class="row g-2 mb-4 bg-light p-3 rounded-3 mx-0 small">
        <div class="col-6">
            <strong>Employee Name:</strong> <?= esc($slip['employee_name']) ?><br>
            <strong>Role/Designation:</strong> <?= esc($slip['role_name']) ?>
        </div>
        <div class="col-6 text-end">
            <strong>Payment Status:</strong> <strong class="text-success">PAID</strong><br>
            <strong>Payment Settle Date:</strong> <?= esc($slip['payment_date']) ?>
        </div>
    </div>

    <!-- Earnings & Deductions Tables -->
    <div class="row g-3 mb-4">
        <!-- Earnings -->
        <div class="col-6">
            <table class="table table-bordered small mb-0">
                <thead class="bg-light">
                    <tr>
                        <th>Earnings Details</th>
                        <th class="text-end">Amount (INR)</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Basic Base Salary</td>
                        <td class="text-end">₹<?= esc(number_format((float)$slip['base_salary'], 2)) ?></td>
                    </tr>
                    <tr>
                        <td>Bonus / Incentives</td>
                        <td class="text-end text-success">+ ₹<?= esc(number_format((float)$slip['bonus'], 2)) ?></td>
                    </tr>
                    <tr class="fw-bold">
                        <td>Gross Earnings</td>
                        <td class="text-end">₹<?= esc(number_format((float)$slip['base_salary'] + (float)$slip['bonus'], 2)) ?></td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Deductions -->
        <div class="col-6">
            <table class="table table-bordered small mb-0">
                <thead class="bg-light">
                    <tr>
                        <th>Deductions Details</th>
                        <th class="text-end">Amount (INR)</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Salary Advance Adjusted</td>
                        <td class="text-end text-danger">- ₹<?= esc(number_format((float)$slip['advance'], 2)) ?></td>
                    </tr>
                    <tr>
                        <td>Penalty / Loss of Pay (LOP)</td>
                        <td class="text-end text-danger">- ₹<?= esc(number_format((float)$slip['deduction'], 2)) ?></td>
                    </tr>
                    <tr class="fw-bold">
                        <td>Total Deductions</td>
                        <td class="text-end text-danger">₹<?= esc(number_format((float)$slip['advance'] + (float)$slip['deduction'], 2)) ?></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Net Payout block -->
    <div class="row justify-content-end mb-5">
        <div class="col-md-5">
            <div class="card bg-success bg-opacity-10 border border-success border-opacity-25 p-3 d-flex flex-row justify-content-between align-items-center">
                <div>
                    <h6 class="mb-1 small fw-bold text-success text-uppercase">Net Settle Payout:</h6>
                    <h3 class="mb-0 fw-bold text-slate">₹<?= esc(number_format((float)$slip['net_salary'], 2)) ?></h3>
                </div>
                <div class="text-success fs-3">
                    <i class="bi bi-wallet2"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Signatures -->
    <div class="row pt-5 mt-5">
        <div class="col-6">
            <div class="d-inline-block text-center border-top border-secondary pt-2" style="width: 200px;">
                <div class="small text-muted">Employee Signature / Acknowledgment</div>
            </div>
        </div>
        <div class="col-6 text-end">
            <div class="d-inline-block text-center border-top border-secondary pt-2" style="width: 200px;">
                <div class="small text-muted">Authorized Signature & Stamp</div>
            </div>
        </div>
    </div>
</div>

<?php include VIEWS_PATH . '/layout/admin_footer.php'; ?>
