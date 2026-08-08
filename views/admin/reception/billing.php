<?php 
$activePage = 'billing';
include VIEWS_PATH . '/layout/reception_header.php'; 
?>

<!-- Billing Directory Table -->
<div class="table-responsive border-0 shadow-sm rounded-3">
    <table class="table table-hover align-middle mb-0">
        <thead class="bg-light text-slate">
            <tr>
                <th>Invoice #</th>
                <th>Patient Details</th>
                <th>Service Type</th>
                <th>Subtotal</th>
                <th>Tax & Discounts</th>
                <th>Total Payable</th>
                <th>Payment Status</th>
                <th class="text-end">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($bills)): ?>
                <tr>
                    <td colspan="8" class="text-center py-5 text-muted">
                        <i class="bi bi-receipt fs-3 d-block mb-2"></i>
                        No unpaid billing invoices located in active directory.
                    </td>
                </tr>
            <?php else: ?>
                <?php foreach ($bills as $bill): ?>
                    <tr>
                        <td class="fw-bold text-slate">INV-<?= esc(sprintf("%05d", $bill['id'])) ?></td>
                        <td>
                            <div class="fw-bold text-slate"><?= esc($bill['patient_name']) ?></div>
                            <span class="text-muted small" style="font-size: 0.78rem;">ID: <?= esc($bill['patient_code']) ?></span>
                        </td>
                        <td>
                            <?php if ($bill['type'] === 'opd'): ?>
                                <span class="badge bg-light text-primary border">OPD Consultation</span>
                            <?php elseif ($bill['type'] === 'ipd'): ?>
                                <span class="badge bg-light text-danger border">IPD Bed Stay</span>
                            <?php else: ?>
                                <span class="badge bg-light text-info border">Appointment booking</span>
                            <?php endif; ?>
                        </td>
                        <td>₹<?= esc(number_format((float)$bill['subtotal'], 2)) ?></td>
                        <td class="small text-slate">
                            Disc: ₹<?= esc(number_format((float)$bill['discount'], 2)) ?><br>
                            Tax: ₹<?= esc(number_format((float)$bill['tax'], 2)) ?>
                        </td>
                        <td class="fw-bold text-slate">₹<?= esc(number_format((float)$bill['total'], 2)) ?></td>
                        <td>
                            <span class="badge bg-warning bg-opacity-10 text-warning border border-warning border-opacity-25 px-2.5 py-1.5 rounded">Unpaid</span>
                        </td>
                        <td class="text-end">
                            <a href="<?= site_url('/admin/reception/billing/collect/' . $bill['id']) ?>" class="btn btn-sm btn-primary px-3 py-1 shadow-sm">
                                <i class="bi bi-cash-coin me-1"></i> Collect Payment
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php include VIEWS_PATH . '/layout/reception_footer.php'; ?>
