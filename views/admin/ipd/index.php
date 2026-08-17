<?php
$activePage = 'ipd';
include VIEWS_PATH . '/layout/admin_header.php';
?>

<!-- ============================================
     PAGE CSS
     ============================================ -->
<link rel="stylesheet" href="<?= asset('css/datatable.css') ?>">

<!-- ============================================
     FLASH MESSAGES
     ============================================ -->
<?php if ($success = \App\Helpers\Session::getFlash('success')): ?>

    <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">

        <i class="bi bi-check-circle-fill me-2"></i>

        <?= esc($success) ?>

        <button type="button"
                class="btn-close"
                data-bs-dismiss="alert">
        </button>

    </div>

<?php endif; ?>


<?php if ($error = \App\Helpers\Session::getFlash('error')): ?>

    <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">

        <i class="bi bi-exclamation-triangle-fill me-2"></i>

        <?= esc($error) ?>

        <button type="button"
                class="btn-close"
                data-bs-dismiss="alert">
        </button>

    </div>

<?php endif; ?>


<!-- ============================================
     ACTIVE ADMISSIONS
     ============================================ -->

<div class="datatable-wrapper mt-4">

    <!-- HEADER -->
    <div class="datatable-header">

        <h5>
            Active Admissions

            <small>
                <?= count($admissions ?? []) ?>
                patients admitted
            </small>
        </h5>


        <a href="<?= site_url('/admin/ipd/admit') ?>"
           class="btn-register"
           style="background:#10b981;border-color:#10b981;">

            <i class="bi bi-plus-circle-fill me-1"></i>

            Admit Patient

        </a>

    </div>


    <!-- TABLE -->
    <div class="table-responsive">

        <table id="activeAdmissionsTable"
               class="table-custom"
               style="width:100%;">

            <thead>

                <tr>

                    <th class="sno">
                        #
                    </th>

                    <th style="min-width:160px;">
                        Patient
                    </th>

                    <th>
                        Doctor
                    </th>

                    <th style="min-width:130px;">
                        Admission Date
                    </th>

                    <th style="min-width:100px;">
                        Diagnosis
                    </th>

                    <th style="width:120px;">
                        Branch
                    </th>

                    <th style="width:100px;">
                        Status
                    </th>

                    <th style="width:80px;">
                        Actions
                    </th>

                </tr>

            </thead>


            <tbody>

                <?php if (!empty($admissions)): ?>

                    <?php $sn = 1; ?>

                    <?php foreach ($admissions as $adm): ?>

                        <?php
                        $admissionDate = $adm['admission_date'] ?? null;

                        $formattedDate = 'N/A';
                        $formattedTime = 'N/A';

                        if (!empty($admissionDate)) {

                            $timestamp = strtotime($admissionDate);

                            if ($timestamp !== false) {

                                $formattedDate = date(
                                    'd M, Y',
                                    $timestamp
                                );

                                $formattedTime = date(
                                    'h:i A',
                                    $timestamp
                                );
                            }
                        }


                        $diagnosis = trim(
                            $adm['diagnosis'] ?? ''
                        );

                        $shortDiagnosis = strlen($diagnosis) > 30
                            ? substr($diagnosis, 0, 30) . '...'
                            : $diagnosis;
                        ?>


                        <tr>

                            <!-- # -->
                            <td class="sno">

                                <?= $sn++ ?>

                            </td>


                            <!-- PATIENT -->
                            <td>

                                <div class="fw-bold text-slate">

                                    <?= esc(
                                        $adm['patient_name'] ?? 'N/A'
                                    ) ?>

                                </div>


                                <span class="text-muted small"
                                      style="font-size:0.78rem;">

                                    <?= esc(
                                        $adm['patient_code'] ?? 'N/A'
                                    ) ?>

                                </span>

                            </td>


                            <!-- DOCTOR -->
                            <td class="fw-semibold text-slate">

                                Dr.

                                <?= esc(
                                    $adm['doctor_name'] ?? 'N/A'
                                ) ?>

                            </td>


                            <!-- ADMISSION DATE -->
                            <td>

                                <div>
                                    <?= esc($formattedDate) ?>
                                </div>


                                <?php if ($formattedTime !== 'N/A'): ?>

                                    <span class="text-muted small"
                                          style="font-size:0.75rem;">

                                        <i class="bi bi-clock me-1"></i>

                                        <?= esc($formattedTime) ?>

                                    </span>

                                <?php endif; ?>

                            </td>


                            <!-- DIAGNOSIS -->
                            <td>

                                <?= esc(
                                    $shortDiagnosis ?: 'N/A'
                                ) ?>

                            </td>


                            <!-- BRANCH -->
                            <td>

                                <span class="badge bg-secondary bg-opacity-10 text-dark">

                                    <?= esc(
                                        $adm['branch_name'] ?? 'Main'
                                    ) ?>

                                </span>

                            </td>


                            <!-- STATUS -->
                            <td>

                                <span class="badge-status active">

                                    Admitted

                                </span>

                            </td>


                            <!-- ACTION -->
                            <td>

                                <div class="action-group">

                                    <a href="<?= site_url(
                                        '/admin/ipd/nursing-logs/' .
                                        ($adm['id'] ?? '')
                                    ) ?>"
                                       class="btn-action"
                                       title="Nursing Logs"
                                       style="color:#6366f1;">

                                        <i class="bi bi-clipboard2-pulse"></i>

                                    </a>

                                </div>

                            </td>

                        </tr>

                    <?php endforeach; ?>

                <?php endif; ?>

            </tbody>

        </table>

    </div>

</div>



<!-- ============================================
     DISCHARGED HISTORY
     ============================================ -->

<div class="datatable-wrapper mt-5">

    <!-- HEADER -->
    <div class="datatable-header">

        <h5>

            Discharged History

            <small>

                <?= count($discharged ?? []) ?>

                patients discharged

            </small>

        </h5>

    </div>


    <!-- TABLE -->
    <div class="table-responsive">

        <table id="dischargedTable"
               class="table-custom"
               style="width:100%;">

            <thead>

                <tr>

                    <th class="sno">
                        #
                    </th>

                    <th style="min-width:160px;">
                        Patient
                    </th>

                    <th>
                        Doctor
                    </th>

                    <th style="min-width:120px;">
                        Admission Date
                    </th>

                    <th style="min-width:120px;">
                        Discharge Date
                    </th>

                    <th style="width:120px;">
                        Branch
                    </th>

                    <th style="width:100px;">
                        Status
                    </th>

                </tr>

            </thead>


            <tbody>

                <?php if (!empty($discharged)): ?>

                    <?php $sn = 1; ?>

                    <?php foreach ($discharged as $dis): ?>

                        <?php
                        $admissionDate = $dis['admission_date'] ?? null;
                        $dischargeDate = $dis['discharge_date'] ?? null;

                        $formattedAdmissionDate = 'N/A';
                        $formattedDischargeDate = '-';


                        if (!empty($admissionDate)) {

                            $timestamp = strtotime($admissionDate);

                            if ($timestamp !== false) {

                                $formattedAdmissionDate = date(
                                    'd M, Y',
                                    $timestamp
                                );
                            }
                        }


                        if (!empty($dischargeDate)) {

                            $timestamp = strtotime($dischargeDate);

                            if ($timestamp !== false) {

                                $formattedDischargeDate = date(
                                    'd M, Y',
                                    $timestamp
                                );
                            }
                        }
                        ?>


                        <tr>

                            <!-- # -->
                            <td class="sno">

                                <?= $sn++ ?>

                            </td>


                            <!-- PATIENT -->
                            <td>

                                <div class="fw-bold text-slate">

                                    <?= esc(
                                        $dis['patient_name'] ?? 'N/A'
                                    ) ?>

                                </div>


                                <span class="text-muted small"
                                      style="font-size:0.78rem;">

                                    <?= esc(
                                        $dis['patient_code'] ?? 'N/A'
                                    ) ?>

                                </span>

                            </td>


                            <!-- DOCTOR -->
                            <td class="fw-semibold text-slate">

                                Dr.

                                <?= esc(
                                    $dis['doctor_name'] ?? 'N/A'
                                ) ?>

                            </td>


                            <!-- ADMISSION DATE -->
                            <td>

                                <?= esc(
                                    $formattedAdmissionDate
                                ) ?>

                            </td>


                            <!-- DISCHARGE DATE -->
                            <td>

                                <?= esc(
                                    $formattedDischargeDate
                                ) ?>

                            </td>


                            <!-- BRANCH -->
                            <td>

                                <span class="badge bg-secondary bg-opacity-10 text-dark">

                                    <?= esc(
                                        $dis['branch_name'] ?? 'Main'
                                    ) ?>

                                </span>

                            </td>


                            <!-- STATUS -->
                            <td>

                                <span class="badge-status inactive">

                                    Discharged

                                </span>

                            </td>

                        </tr>

                    <?php endforeach; ?>

                <?php endif; ?>

            </tbody>

        </table>

    </div>

</div>



<!-- ============================================
     DATATABLES CSS
     ============================================ -->

<link rel="stylesheet"
      href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">

<link rel="stylesheet"
      href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css">



<!-- ============================================
     JQUERY
     ============================================ -->

<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>



<!-- ============================================
     DATATABLES CORE
     ============================================ -->

<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>



<!-- ============================================
     DATATABLE BUTTONS
     ============================================ -->

<script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>

<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>

<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>



<!-- ============================================
     EXPORT DEPENDENCIES
     ============================================ -->

<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>



<!-- ============================================
     DATATABLE INITIALIZATION
     ============================================ -->

<script>

$(document).ready(function () {


    /* ==========================================
       COMMON DATATABLE SETTINGS
       ========================================== */

    const commonSettings = {

        pageLength: 25,

        responsive: true,

        autoWidth: false,

        processing: true,

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

        language: {

            search: '',

            searchPlaceholder: 'Search...',

            lengthMenu: 'Show _MENU_ entries',

            info: 'Showing _START_ to _END_ of _TOTAL_ entries',

            infoEmpty: 'Showing 0 to 0 of 0 entries',

            zeroRecords: 'No matching records found',

            paginate: {

                first: 'First',

                last: 'Last',

                next: 'Next',

                previous: 'Previous'

            }

        }

    };


    /* ==========================================
       ACTIVE ADMISSIONS TABLE
       ========================================== */

    $('#activeAdmissionsTable').DataTable({

        ...commonSettings,

        language: {

            ...commonSettings.language,

            emptyTable: `
                <div style="
                    text-align:center;
                    padding:2.5rem 1rem;
                    color:#94a3b8;
                    width:100%;
                ">

                    <i class="bi bi-person-lines-fill"
                       style="
                           font-size:2rem;
                           display:block;
                           margin-bottom:10px;
                       ">
                    </i>

                    <div style="
                        font-size:14px;
                        font-weight:500;
                    ">

                        No active admissions found.

                    </div>

                </div>
            `

        }

    });


    /* ==========================================
       DISCHARGED HISTORY TABLE
       ========================================== */

    $('#dischargedTable').DataTable({

        ...commonSettings,

        language: {

            ...commonSettings.language,

            emptyTable: `
                <div style="
                    text-align:center;
                    padding:2.5rem 1rem;
                    color:#94a3b8;
                    width:100%;
                ">

                    <i class="bi bi-clock-history"
                       style="
                           font-size:2rem;
                           display:block;
                           margin-bottom:10px;
                       ">
                    </i>

                    <div style="
                        font-size:14px;
                        font-weight:500;
                    ">

                        No discharge history found.

                    </div>

                </div>
            `

        }

    });


});

</script>



<?php
include VIEWS_PATH . '/layout/admin_footer.php';
?>