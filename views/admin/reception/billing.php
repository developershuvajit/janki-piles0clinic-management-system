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
                        No billing invoices located in active directory.
                    </td>
                </tr>
            <?php else: ?>
                <?php foreach ($bills as $bill): ?>
                    <?php 
                        // Determine payment status badge class
                        $status = strtolower($bill['payment_status'] ?? 'unpaid');
                        $badgeClass = 'bg-warning bg-opacity-10 text-warning border-warning';
                        $statusText = 'Unpaid';
                        
                        if ($status === 'paid') {
                            $badgeClass = 'bg-success bg-opacity-10 text-success border-success';
                            $statusText = 'Paid';
                        } elseif ($status === 'partial') {
                            $badgeClass = 'bg-info bg-opacity-10 text-info border-info';
                            $statusText = 'Partial';
                        } elseif ($status === 'refunded') {
                            $badgeClass = 'bg-danger bg-opacity-10 text-danger border-danger';
                            $statusText = 'Refunded';
                        } elseif ($status === 'cancelled') {
                            $badgeClass = 'bg-secondary bg-opacity-10 text-secondary border-secondary';
                            $statusText = 'Cancelled';
                        }
                        
                        // Calculate outstanding amount
                        $outstanding = (float)($bill['outstanding'] ?? 0);
                        $total = (float)($bill['total'] ?? 0);
                        $paid = (float)($bill['paid_amount'] ?? 0);
                        
                        // Show action button based on status
                        $showCollectBtn = ($status === 'unpaid' || $status === 'partial');
                        $showReceiptBtn = ($status === 'paid' || $status === 'partial');
                    ?>
                    <tr>
                        <td class="fw-bold text-slate">INV-<?= esc(sprintf("%05d", $bill['id'])) ?></td>
                        <td>
                            <div class="fw-bold text-slate"><?= esc($bill['patient_name'] ?? 'N/A') ?></div>
                            <span class="text-muted small" style="font-size: 0.78rem;">ID: <?= esc($bill['patient_code'] ?? 'N/A') ?></span>
                        </td>
                        <td>
                            <?php if (($bill['type'] ?? '') === 'opd'): ?>
                                <span class="badge bg-light text-primary border">OPD Consultation</span>
                            <?php elseif (($bill['type'] ?? '') === 'ipd'): ?>
                                <span class="badge bg-light text-danger border">IPD Bed Stay</span>
                            <?php elseif (($bill['type'] ?? '') === 'pharmacy'): ?>
                                <span class="badge bg-light text-success border">Pharmacy</span>
                            <?php else: ?>
                                <span class="badge bg-light text-info border">Other</span>
                            <?php endif; ?>
                        </td>
                        <td>₹<?= esc(number_format((float)($bill['subtotal'] ?? 0), 2)) ?></td>
                        <td class="small text-slate">
                            Disc: ₹<?= esc(number_format((float)($bill['discount'] ?? 0), 2)) ?><br>
                            Tax: ₹<?= esc(number_format((float)($bill['tax'] ?? 0), 2)) ?>
                        </td>
                        <td class="fw-bold text-slate">₹<?= esc(number_format($total, 2)) ?></td>
                        <td>
                            <span class="badge <?= $badgeClass ?> border px-2.5 py-1.5 rounded">
                                <?php if ($status === 'paid'): ?>
                                    <i class="bi bi-check-circle-fill me-1"></i>
                                <?php elseif ($status === 'partial'): ?>
                                    <i class="bi bi-clock-history me-1"></i>
                                <?php elseif ($status === 'refunded'): ?>
                                    <i class="bi bi-arrow-counterclockwise me-1"></i>
                                <?php elseif ($status === 'cancelled'): ?>
                                    <i class="bi bi-x-circle-fill me-1"></i>
                                <?php else: ?>
                                    <i class="bi bi-hourglass-split me-1"></i>
                                <?php endif; ?>
                                <?= $statusText ?>
                                <?php if ($status === 'partial'): ?>
                                    <span class="d-block small">Paid: ₹<?= esc(number_format($paid, 2)) ?></span>
                                    <span class="d-block small">Due: ₹<?= esc(number_format($outstanding, 2)) ?></span>
                                <?php endif; ?>
                            </span>
                        </td>
                        <td class="text-end">
                            <?php if ($showCollectBtn): ?>
                                <a href="<?= site_url('/reception/billing/collect/' . $bill['id']) ?>" class="btn btn-sm btn-primary px-3 py-1 shadow-sm">
                                    <i class="bi bi-cash-coin me-1"></i> Collect Payment
                                </a>
                            <?php elseif ($showReceiptBtn): ?>
                                <a href="<?= site_url('/reception/billing/receipt/' . $bill['id']) ?>" class="btn btn-sm btn-success px-3 py-1 shadow-sm">
                                    <i class="bi bi-printer me-1"></i> Receipt
                                </a>
                            <?php elseif ($status === 'refunded'): ?>
                                <span class="badge bg-secondary">Refunded</span>
                            <?php else: ?>
                                <span class="badge bg-secondary">N/A</span>
                            <?php endif; ?>
                            
                            <?php if ($status === 'paid' || $status === 'partial'): ?>
                                <a href="<?= site_url('/reception/billing/refund/' . $bill['id']) ?>" class="btn btn-sm btn-outline-danger px-2 py-1 ms-1">
                                    <i class="bi bi-arrow-counterclockwise"></i>
                                </a>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php include VIEWS_PATH . '/layout/reception_footer.php'; ?>