<?php
$activePage = 'reception_dashboard';
include VIEWS_PATH . '/layout/admin_header.php';
?>

<style>
    /* =========================================================
       RECEIPT SCREEN DESIGN
       ========================================================= */

    .receipt-page-wrapper {
        width: 100%;
        padding: 20px 0 50px;
    }

    .receipt-container {
        width: 100%;
        max-width: 800px;
        margin: 0 auto;
        background: #ffffff;
        padding: 48px 52px;
        border-radius: 12px;
        box-shadow: 0 4px 20px rgba(15, 23, 42, 0.08);
        color: #1f2937;
    }

    .receipt-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 30px;
        padding-bottom: 24px;
        border-bottom: 1px solid #dfe3e8;
    }

    .clinic-info {
        flex: 1;
    }

    .clinic-name {
        font-size: 23px;
        font-weight: 800;
        color: #14213d;
        margin: 0 0 5px;
        line-height: 1.2;
    }

    .clinic-name i {
        color: #159669;
        font-size: 22px;
        margin-right: 7px;
    }

    .clinic-details {
        font-size: 12px;
        line-height: 1.65;
        color: #6b7280;
        margin: 0;
    }

    .invoice-info {
        text-align: right;
        min-width: 220px;
    }

    .invoice-title {
        color: #159669;
        font-size: 20px;
        font-weight: 800;
        margin: 0 0 6px;
        line-height: 1.2;
    }

    .paid-badge {
        display: inline-block;
        color: #999;
        font-size: 11px;
        font-weight: 600;
        margin-bottom: 12px;
    }

    .invoice-meta {
        color: #6b7280;
        font-size: 12px;
        line-height: 1.8;
    }

    .invoice-meta strong {
        color: #374151;
    }

    /* =========================================================
       PATIENT / PAYMENT SECTION
       ========================================================= */

    .patient-section {
        display: flex;
        justify-content: space-between;
        gap: 40px;
        padding: 22px 0 24px;
    }

    .patient-box,
    .payment-box {
        flex: 1;
    }

    .payment-box {
        text-align: right;
    }

    .section-label {
        font-size: 12px;
        font-weight: 800;
        text-transform: uppercase;
        color: #4b5563;
        margin-bottom: 7px;
    }

    .patient-name {
        font-size: 14px;
        font-weight: 800;
        color: #202733;
        margin-bottom: 5px;
    }

    .patient-details,
    .payment-details {
        font-size: 12px;
        line-height: 1.75;
        color: #6b7280;
    }

    .payment-method {
        font-size: 14px;
        font-weight: 800;
        color: #202733;
        margin-bottom: 6px;
    }

    .amount-green {
        color: #159669;
        font-weight: 800;
    }

    /* =========================================================
       BILLING TABLE
       ========================================================= */

    .receipt-table {
        width: 100%;
        border-collapse: collapse;
        margin: 0 0 28px;
        font-size: 12px;
    }

    .receipt-table th {
        background: #f8f9fa;
        color: #111827;
        font-weight: 800;
        padding: 10px 9px;
        border: 1px solid #d9dee5;
        text-align: left;
        white-space: nowrap;
    }

    .receipt-table th.text-end,
    .receipt-table td.text-end {
        text-align: right;
    }

    .receipt-table td {
        padding: 11px 9px;
        border: 1px solid #d9dee5;
        color: #374151;
        vertical-align: top;
    }

    .service-title {
        display: block;
        font-size: 12px;
        font-weight: 800;
        color: #111827;
        margin-bottom: 3px;
    }

    .service-description {
        display: block;
        font-size: 10px;
        color: #6b7280;
        line-height: 1.5;
    }

    /* =========================================================
       BILL SUMMARY
       ========================================================= */

    .summary-wrapper {
        display: flex;
        justify-content: flex-end;
    }

    .summary-box {
        width: 52%;
        font-size: 12px;
    }

    .summary-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 9px;
        color: #6b7280;
    }

    .summary-row .value {
        color: #374151;
        text-align: right;
    }

    .summary-row.discount .value {
        color: #159669;
    }

    .summary-divider {
        border: 0;
        border-top: 1px solid #d9dee5;
        margin: 8px 0 12px;
    }

    .amount-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-size: 17px;
        font-weight: 800;
        color: #252b35;
    }

    .amount-row .amount {
        color: #159669;
        font-size: 20px;
    }

    /* =========================================================
       FOOTER MESSAGE
       ========================================================= */

    .receipt-message {
        margin-top: 38px;
        padding-top: 22px;
        border-top: 1px solid #dfe3e8;
        text-align: center;
        color: #6b7280;
    }

    .receipt-message p {
        margin: 0;
        line-height: 1.6;
    }

    .receipt-message .thank-you {
        font-size: 12px;
        margin-bottom: 5px;
    }

    .receipt-message .small-message {
        font-size: 10px;
    }

    /* =========================================================
       PRINT BUTTON AREA
       ========================================================= */

    .print-controls {
        max-width: 800px;
        margin: 0 auto 20px;
        text-align: right;
    }

    /* =========================================================
       PRINT DESIGN
       ========================================================= */

    @page {
        size: A4 portrait;
        margin: 0;
    }

    @media print {

        html,
        body {
            width: 100%;
            margin: 0 !important;
            padding: 0 !important;
            background: #ffffff !important;
        }

        /*
         * Hide complete application UI
         */
        body > * {
            visibility: hidden !important;
        }

        /*
         * Show only receipt
         */
        .receipt-page-wrapper,
        .receipt-page-wrapper .receipt-container,
        .receipt-page-wrapper .receipt-container * {
            visibility: visible !important;
        }

        /*
         * Receipt becomes the complete print page
         */
        .receipt-page-wrapper {
            position: absolute !important;
            left: 0 !important;
            top: 0 !important;

            width: 100% !important;
            margin: 0 !important;
            padding: 0 !important;

            background: #ffffff !important;
        }

        .receipt-container {
            position: relative !important;

            width: 100% !important;
            max-width: none !important;

            min-height: auto !important;

            margin: 0 !important;
            padding: 42px 52px !important;

            background: #ffffff !important;

            border: 0 !important;
            border-radius: 0 !important;
            box-shadow: none !important;

            box-sizing: border-box !important;
        }

        /*
         * Hide print controls
         */
        .print-controls,
        .d-print-none {
            display: none !important;
            visibility: hidden !important;
        }

        /*
         * Keep receipt sections together
         */
        .receipt-header,
        .patient-section,
        .receipt-table,
        .summary-wrapper,
        .receipt-message {
            page-break-inside: avoid !important;
            break-inside: avoid !important;
        }

        .receipt-table tr {
            page-break-inside: avoid !important;
            break-inside: avoid !important;
        }

        /*
         * Make sure table keeps borders in print
         */
        .receipt-table {
            width: 100% !important;
            border-collapse: collapse !important;
        }

        /*
         * Force white background
         */
        .receipt-container,
        .receipt-table th {
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }

        /*
         * Remove Bootstrap/card interference
         */
        .card {
            border: 0 !important;
            box-shadow: none !important;
        }

        /*
         * Prevent links from showing URLs
         */
        a {
            text-decoration: none !important;
            color: inherit !important;
        }
    }

    /* =========================================================
       MOBILE SCREEN
       ========================================================= */

    @media screen and (max-width: 768px) {

        .receipt-page-wrapper {
            padding: 10px;
        }

        .receipt-container {
            padding: 25px 20px;
        }

        .receipt-header {
            flex-direction: column;
        }

        .invoice-info {
            text-align: left;
        }

        .patient-section {
            flex-direction: column;
            gap: 20px;
        }

        .payment-box {
            text-align: left;
        }

        .summary-wrapper {
            justify-content: stretch;
        }

        .summary-box {
            width: 100%;
        }

        .receipt-table {
            font-size: 11px;
        }

        .receipt-table th,
        .receipt-table td {
            padding: 7px 5px;
        }
    }
</style>


<!-- =========================================================
     PRINT CONTROLS
     ========================================================= -->

<div class="print-controls d-print-none mt-4">

    <button
        type="button"
        onclick="window.print()"
        class="btn btn-primary btn-sm px-4 shadow-sm"
    >
        <i class="bi bi-printer-fill me-1"></i>
        Print Receipt
    </button>

    <a
        href="<?= site_url('/reception/billing') ?>"
        class="btn btn-outline-secondary btn-sm px-3 ms-2"
    >
        Back to Invoices
    </a>

</div>


<!-- =========================================================
     RECEIPT PAGE
     ========================================================= -->

<div class="receipt-page-wrapper">

    <div class="receipt-container">

        <!-- =====================================================
             HEADER
             ===================================================== -->

        <div class="receipt-header">

            <!-- Clinic -->
            <div class="clinic-info">

                <h3 class="clinic-name">
                    <i class="bi bi-heart-pulse-fill"></i>
                    <?= esc($bill['branch_name']) ?>
                </h3>

                <p class="clinic-details">
                    <?= esc($bill['branch_address']) ?><br>

                    Phone:
                    <?= esc($bill['branch_phone']) ?>

                    &bull;

                    Email:
                    <?= esc($bill['branch_email']) ?>
                </p>

            </div>


            <!-- Invoice -->
            <div class="invoice-info">

                <h4 class="invoice-title">
                    INVOICE RECEIPT
                </h4>

                <div class="paid-badge">
                    PAID IN FULL
                </div>

                <div class="invoice-meta">

                    Invoice No:
                    <strong>
                        INV-<?= esc(sprintf("%05d", $bill['id'])) ?>
                    </strong>

                    <br>

                    Date Issued:
                    <strong>
                        <?= esc(
                            date(
                                'Y-m-d H:i',
                                strtotime($bill['updated_at'])
                            )
                        ) ?>
                    </strong>

                </div>

            </div>

        </div>


        <!-- =====================================================
             PATIENT + PAYMENT
             ===================================================== -->

        <div class="patient-section">

            <!-- Patient -->
            <div class="patient-box">

                <div class="section-label">
                    Billed To:
                </div>

                <div class="patient-name">
                    <?= esc($bill['patient_name']) ?>
                </div>

                <div class="patient-details">

                    Patient Code:
                    <strong>
                        <?= esc($bill['patient_code']) ?>
                    </strong>

                    <br>

                    Phone:
                    <?= esc($bill['patient_phone']) ?>

                    <br>

                    Address:
                    <?= esc($bill['patient_address']) ?>

                </div>

            </div>


            <!-- Payment -->
            <div class="payment-box">

                <div class="section-label">
                    Payment Method:
                </div>

                <div class="payment-method">
                    <?= esc(
                        strtoupper(
                            $bill['payment_method']
                        )
                    ) ?>
                </div>

                <div class="payment-details">

                    Transaction Status:
                    <strong>Settled</strong>

                    <br>

                    Total Paid Amount:

                    <strong class="amount-green">
                        ₹<?= esc(
                            number_format(
                                (float)$bill['paid_amount'],
                                2
                            )
                        ) ?>
                    </strong>

                </div>

            </div>

        </div>


        <!-- =====================================================
             BILLING TABLE
             ===================================================== -->

        <table class="receipt-table">

            <thead>

                <tr>

                    <th>
                        Service Description
                    </th>

                    <th
                        class="text-end"
                        style="width: 55px;"
                    >
                        Units
                    </th>

                    <th
                        class="text-end"
                        style="width: 95px;"
                    >
                        Base Rate
                    </th>

                    <th
                        class="text-end"
                        style="width: 95px;"
                    >
                        Line Total
                    </th>

                </tr>

            </thead>

            <tbody>

                <tr>

                    <td>

                        <?php if ($bill['type'] === 'opd'): ?>

                            <span class="service-title">
                                Outpatient Consultation Fee
                            </span>

                            <span class="service-description">
                                Standard clinical visit and consultation.
                            </span>

                        <?php elseif ($bill['type'] === 'ipd'): ?>

                            <span class="service-title">
                                Inpatient Ward Bed Stay
                            </span>

                            <span class="service-description">
                                Stay charges + procedures logged during admission.
                            </span>

                        <?php else: ?>

                            <span class="service-title">
                                Appointment Slot Booking
                            </span>

                            <span class="service-description">
                                Online consultation booking.
                            </span>

                        <?php endif; ?>

                    </td>


                    <td class="text-end">
                        1
                    </td>


                    <td class="text-end">
                        ₹<?= esc(
                            number_format(
                                (float)$bill['subtotal'],
                                2
                            )
                        ) ?>
                    </td>


                    <td class="text-end fw-bold">

                        ₹<?= esc(
                            number_format(
                                (float)$bill['subtotal'],
                                2
                            )
                        ) ?>

                    </td>

                </tr>

            </tbody>

        </table>


        <!-- =====================================================
             BILL SUMMARY
             ===================================================== -->

        <div class="summary-wrapper">

            <div class="summary-box">

                <!-- Subtotal -->
                <div class="summary-row">

                    <span>
                        Subtotal:
                    </span>

                    <span class="value">
                        ₹<?= esc(
                            number_format(
                                (float)$bill['subtotal'],
                                2
                            )
                        ) ?>
                    </span>

                </div>


                <!-- Discount -->
                <div class="summary-row discount">

                    <span>
                        Discount Applied:
                    </span>

                    <span class="value">
                        - ₹<?= esc(
                            number_format(
                                (float)$bill['discount'],
                                2
                            )
                        ) ?>
                    </span>

                </div>


                <!-- Tax -->
                <div class="summary-row">

                    <span>
                        Tax Additions:
                    </span>

                    <span class="value">
                        + ₹<?= esc(
                            number_format(
                                (float)$bill['tax'],
                                2
                            )
                        ) ?>
                    </span>

                </div>


                <hr class="summary-divider">


                <!-- Total -->
                <div class="amount-row">

                    <span>
                        Amount Collected:
                    </span>

                    <span class="amount">
                        ₹<?= esc(
                            number_format(
                                (float)$bill['total'],
                                2
                            )
                        ) ?>
                    </span>

                </div>

            </div>

        </div>


        <!-- =====================================================
             FOOTER MESSAGE
             ===================================================== -->

        <div class="receipt-message">

            <p class="thank-you">
                Thank you for visiting MedClinic.
            </p>

            <p class="small-message">
                For medical queries or follow-up consult bookings,
                visit our portal or contact the clinic reception desks.
            </p>

        </div>

    </div>

</div>


<?php include VIEWS_PATH . '/layout/admin_footer.php'; ?>