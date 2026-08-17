<?php
$activePage = 'reception_communication';
include VIEWS_PATH . '/layout/reception_header.php';
?>

<!-- ============================================
     PAGE CSS
     ============================================ -->
<link rel="stylesheet" href="<?= asset('css/datatable.css') ?>">

<style>
/* ============================================
   TEMPLATE ACCORDION
   ============================================ */
.template-item {
    border: 1px solid #e2e8f0;
    border-radius: 10px;
    margin-bottom: 0.5rem;
    overflow: hidden;
}

.template-item .header {
    padding: 0.65rem 1rem;
    background: #f8fafc;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: space-between;
    transition: background 0.2s ease;
}

.template-item .header:hover {
    background: #f1f5f9;
}

.template-item .body {
    padding: 1rem;
    background: #f8fafc;
    font-size: 0.85rem;
    font-family: monospace;
    white-space: pre-line;
    line-height: 1.6;
    color: #475569;
    display: none;
}

.template-item .body.open {
    display: block;
}

/* ============================================
   FORM
   ============================================ */
.form-control-sm-custom {
    border-radius: 8px;
    border: 1px solid #e2e8f0;
    padding: 0.5rem 0.75rem;
    font-size: 0.875rem;
    transition: all 0.2s ease;
}

.form-control-sm-custom:focus {
    border-color: #0f7b4a;
    box-shadow: 0 0 0 3px rgba(15, 123, 74, 0.1);
}

/* ============================================
   WHATSAPP BUTTON
   ============================================ */
.btn-whatsapp {
    background: #198754;
    border-color: #198754;
    color: #fff;
}

.btn-whatsapp:hover {
    background: #157347;
    border-color: #157347;
    color: #fff;
}

/* ============================================
   PAGE HEADER
   ============================================ */
.communication-header {
    margin-bottom: 1.5rem;
}

/* ============================================
   TABLE
   ============================================ */
#commTable {
    width: 100% !important;
}

#commTable th,
#commTable td {
    vertical-align: middle;
}

/* ============================================
   DATATABLE EMPTY MESSAGE
   ============================================ */
#commTable_wrapper .dataTables_empty {
    padding: 2.5rem 1rem !important;
    color: #94a3b8 !important;
    text-align: center !important;
}

/* ============================================
   MOBILE
   ============================================ */
@media (max-width: 768px) {

    .communication-header {
        flex-direction: column;
        align-items: flex-start !important;
        gap: 0.75rem;
    }

    .datatable-header {
        flex-direction: column;
        align-items: flex-start !important;
        gap: 0.75rem;
    }
}
</style>


<!-- ============================================
     PAGE HEADER
     ============================================ -->
<div class="communication-header d-flex justify-content-between align-items-center">

    <div>
        <h4 class="fw-bold text-slate mb-1">
            <i class="bi bi-whatsapp text-success me-2"></i>
            Communication Center
        </h4>

        <p class="text-muted small mb-0">
            Pre-configured WhatsApp and SMS message templates for patient reminders & notifications.
        </p>
    </div>

    <div>
        <a href="https://web.whatsapp.com"
           target="_blank"
           rel="noopener noreferrer"
           class="btn btn-success btn-sm rounded-pill px-3 shadow-sm">

            <i class="bi bi-whatsapp me-1"></i>
            Open WhatsApp Web

        </a>
    </div>

</div>


<!-- ============================================
     TEMPLATES + QUICK MESSAGE
     ============================================ -->
<div class="row g-4 mb-4">

    <!-- ========================================
         LEFT - MESSAGE TEMPLATES
         ======================================== -->
    <div class="col-lg-7">

        <div class="card border-0 shadow-sm p-4 rounded-4">

            <h5 class="fw-bold text-slate mb-3">
                <i class="bi bi-chat-quote-fill text-success me-2"></i>
                WhatsApp / SMS Templates
            </h5>

            <div id="templatesContainer">

                <?php if (!empty($templates)): ?>

                    <?php foreach ($templates as $key => $tmpl): ?>

                        <div class="template-item">

                            <div class="header"
                                 onclick="toggleTemplate(this)">

                                <div>

                                    <i class="bi bi-chat-left-text-fill text-success me-2"></i>

                                    <strong>
                                        <?= esc($tmpl['name'] ?? 'Message Template') ?>
                                    </strong>

                                    <?php if (!empty($tmpl['category'])): ?>

                                        <span class="badge bg-light text-secondary border ms-2">
                                            <?= esc($tmpl['category']) ?>
                                        </span>

                                    <?php endif; ?>

                                </div>

                                <i class="bi bi-chevron-down text-muted"></i>

                            </div>

                            <div class="body">
                                <?= esc($tmpl['body'] ?? '') ?>
                            </div>

                        </div>

                    <?php endforeach; ?>

                <?php else: ?>

                    <div class="text-center text-muted py-4">

                        <i class="bi bi-chat-square-text fs-3 d-block mb-2"></i>

                        No communication templates available.

                    </div>

                <?php endif; ?>

            </div>

        </div>

    </div>


    <!-- ========================================
         RIGHT - QUICK WHATSAPP MESSAGE
         ======================================== -->
    <div class="col-lg-5">

        <div class="card border-0 shadow-sm p-4 rounded-4">

            <h5 class="fw-bold text-slate mb-3">

                <i class="bi bi-send-fill text-success me-2"></i>

                Send Quick WhatsApp Message

            </h5>


            <form id="quick-wa-form"
                  onsubmit="event.preventDefault(); sendQuickWhatsApp();">


                <!-- PHONE -->
                <div class="mb-3">

                    <label for="wa_phone"
                           class="form-label small fw-bold">

                        Recipient Phone Number
                        <span class="text-danger">*</span>

                    </label>

                    <input type="text"
                           class="form-control form-control-sm-custom"
                           id="wa_phone"
                           required
                           autocomplete="tel"
                           placeholder="+91 98765 43210">

                </div>


                <!-- TEMPLATE -->
                <div class="mb-3">

                    <label for="wa_template"
                           class="form-label small fw-bold">

                        Select Template

                    </label>

                    <select class="form-select form-control-sm-custom"
                            id="wa_template"
                            onchange="populateTemplate(this.value)">

                        <option value="">
                            -- Custom Message --
                        </option>

                        <?php if (!empty($templates)): ?>

                            <?php foreach ($templates as $key => $tmpl): ?>

                                <option value="<?= esc($tmpl['body'] ?? '') ?>">

                                    <?= esc($tmpl['name'] ?? 'Message Template') ?>

                                </option>

                            <?php endforeach; ?>

                        <?php endif; ?>

                    </select>

                </div>


                <!-- MESSAGE -->
                <div class="mb-3">

                    <label for="wa_message"
                           class="form-label small fw-bold">

                        Message Content
                        <span class="text-danger">*</span>

                    </label>

                    <textarea
                        class="form-control form-control-sm-custom font-monospace"
                        id="wa_message"
                        rows="5"
                        required
                        placeholder="Type message or select a template above..."></textarea>

                </div>


                <!-- SEND -->
                <button type="submit"
                        class="btn btn-whatsapp w-100 py-2 fw-bold rounded-pill shadow-sm">

                    <i class="bi bi-whatsapp me-1"></i>

                    Open 1-Click WhatsApp Chat

                </button>

            </form>

        </div>

    </div>

</div>


<!-- ============================================
     COMMUNICATION HISTORY
     ============================================ -->
<div class="datatable-wrapper mt-4">

    <div class="datatable-header">

        <h5>
            Recent Sent Logs

            <small>
                <?= count($logs ?? []) ?> communications
            </small>
        </h5>

    </div>


    <div class="table-responsive">

        <table id="commTable"
               class="table-custom"
               style="width:100%">

            <!-- EXACTLY 7 HEADERS -->
            <thead>

                <tr>

                    <th class="sno">
                        #
                    </th>

                    <th style="width:100px;">
                        Log ID
                    </th>

                    <th style="min-width:160px;">
                        Recipient
                    </th>

                    <th style="min-width:150px;">
                        Template
                    </th>

                    <th style="width:100px;">
                        Channel
                    </th>

                    <th style="min-width:160px;">
                        Sent At
                    </th>

                    <th style="width:100px;">
                        Status
                    </th>

                </tr>

            </thead>


            <!-- ========================================
                 IMPORTANT:
                 DO NOT ADD EMPTY <tr> HERE.
                 DATATABLES WILL HANDLE EMPTY STATE.
                 ======================================== -->
            <tbody>

                <?php

                if (!empty($logs)):

                    $sn = 1;

                    foreach ($logs as $l):

                        $sentAt = $l['sent_at'] ?? null;

                ?>

                    <!-- EXACTLY 7 TD -->
                    <tr>

                        <!-- 1 -->
                        <td class="sno">
                            <?= $sn++ ?>
                        </td>


                        <!-- 2 -->
                        <td class="fw-bold text-slate">
                            #<?= esc((string)($l['id'] ?? '')) ?>
                        </td>


                        <!-- 3 -->
                        <td>

                            <div>
                                <?= esc($l['recipient_phone'] ?? '') ?>
                            </div>

                            <span class="text-muted small"
                                  style="font-size:0.75rem;">

                                <?= esc($l['patient_name'] ?? 'Guest') ?>

                            </span>

                        </td>


                        <!-- 4 -->
                        <td>

                            <span class="badge bg-light text-dark border">

                                <?= esc($l['template_name'] ?? 'Custom Message') ?>

                            </span>

                        </td>


                        <!-- 5 -->
                        <td>

                            <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25">

                                <?= esc(strtoupper($l['channel'] ?? 'WHATSAPP')) ?>

                            </span>

                        </td>


                        <!-- 6 -->
                        <td>

                            <?php if (!empty($sentAt)): ?>

                                <div>
                                    <?= esc(date('d M Y', strtotime($sentAt))) ?>
                                </div>

                                <span class="text-muted small"
                                      style="font-size:0.75rem;">

                                    <i class="bi bi-clock me-1"></i>

                                    <?= esc(date('h:i A', strtotime($sentAt))) ?>

                                </span>

                            <?php else: ?>

                                <span class="text-muted">
                                    N/A
                                </span>

                            <?php endif; ?>

                        </td>


                        <!-- 7 -->
                        <td>

                            <span class="badge-status active">
                                SENT
                            </span>

                        </td>

                    </tr>

                <?php

                    endforeach;

                endif;

                ?>

            </tbody>

        </table>

    </div>

</div>


<!-- ============================================
     TEMPLATE JAVASCRIPT
     ============================================ -->
<script>

function toggleTemplate(element) {

    if (!element) {
        return;
    }

    const body = element.nextElementSibling;

    const icon = element.querySelector(
        '.bi-chevron-down, .bi-chevron-up'
    );

    if (!body) {
        return;
    }

    body.classList.toggle('open');

    if (icon) {

        if (body.classList.contains('open')) {

            icon.className = 'bi bi-chevron-up text-muted';

        } else {

            icon.className = 'bi bi-chevron-down text-muted';

        }

    }

}


function populateTemplate(text) {

    const messageBox =
        document.getElementById('wa_message');

    if (!messageBox) {
        return;
    }

    messageBox.value = text || '';

    messageBox.focus();

}


function sendQuickWhatsApp() {

    const phoneInput =
        document.getElementById('wa_phone');

    const messageInput =
        document.getElementById('wa_message');


    if (!phoneInput || !messageInput) {
        return;
    }


    const phone =
        phoneInput.value.trim();

    const message =
        messageInput.value.trim();


    if (!phone || !message) {

        alert(
            'Please provide phone number and message text.'
        );

        return;
    }


    // Remove all non-numeric characters
    let cleanPhone =
        phone.replace(/\D/g, '');


    // Automatically add India country code
    if (cleanPhone.length === 10) {

        cleanPhone = '91' + cleanPhone;

    }


    if (cleanPhone.length < 10) {

        alert(
            'Please enter a valid phone number.'
        );

        return;
    }


    const whatsappURL =
        'https://wa.me/' +
        cleanPhone +
        '?text=' +
        encodeURIComponent(message);


    window.open(
        whatsappURL,
        '_blank',
        'noopener,noreferrer'
    );

}

</script>


<!-- ============================================
     DATATABLE LIBRARIES
     ============================================ -->
<link rel="stylesheet"
      href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">

<link rel="stylesheet"
      href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css">


<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>

<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

<script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>

<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>

<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>


<!-- ============================================
     DATATABLE INITIALIZATION
     ============================================ -->
<script>

$(document).ready(function () {

    const tableElement =
        $('#commTable');


    if (!tableElement.length) {
        return;
    }


    /*
     * Prevent duplicate initialization
     */
    if ($.fn.DataTable.isDataTable('#commTable')) {

        tableElement
            .DataTable()
            .destroy();

    }


    /*
     * Initialize DataTable
     */
    tableElement.DataTable({

        pageLength: 25,

        responsive: true,

        autoWidth: false,

        processing: false,

        dom: 'Bfrtip',

        buttons: [
            'copy',
            'csv',
            'excel',
            'pdf',
            'print'
        ],

        order: [
            [0, 'asc']
        ],

        columnDefs: [

            {
                targets: 0,
                searchable: false,
                orderable: true
            }

        ],

        language: {

            emptyTable:
                '<div style="padding:2.5rem 1rem;color:#94a3b8;text-align:center;">' +
                '<i class="bi bi-chat-dots fs-3 d-block mb-2"></i>' +
                'No communication logs recorded yet.' +
                '</div>',

            zeroRecords:
                'No matching communication logs found.',

            search:
                'Search:',

            lengthMenu:
                'Show _MENU_ entries',

            info:
                'Showing _START_ to _END_ of _TOTAL_ communications',

            infoEmpty:
                'No communications available',

            infoFiltered:
                '(filtered from _MAX_ total communications)'

        }

    });

});

</script>


<?php
include VIEWS_PATH . '/layout/reception_footer.php';
?>