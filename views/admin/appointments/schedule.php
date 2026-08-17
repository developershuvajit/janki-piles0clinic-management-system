<?php 
$activePage = 'appointments';
$userRole = \App\Helpers\Session::get('role_slug') ?? '';
$isReceptionist = ($userRole === 'receptionist');
$isSuperAdmin = ($userRole === 'super_admin' || $userRole === 'admin');

// Receptionist হলে admin_header এর পরিবর্তে reception_header ব্যবহার করবে
if ($isReceptionist) {
    include VIEWS_PATH . '/layout/reception_header.php'; 
} else {
    include VIEWS_PATH . '/layout/admin_header.php'; 
}
?>

<div class="row mt-4">
    <!-- Schedule Planner Form -->
    <div class="col-lg-5 mb-4">
        <div class="card border-0 shadow-sm p-4">
            <h5 class="fw-bold text-slate mb-3">
                <i class="bi bi-calendar-range text-success me-2"></i>
                <?= $isReceptionist ? 'Manage Shift Schedules' : 'Configure Shift Slots' ?>
            </h5>
            
            <!-- Doctor Selection Filter - সবার জন্য visible -->
            <div class="mb-3">
                <label class="form-label small fw-semibold">Select Practitioner / Doctor</label>
                <select class="form-control form-control-sm form-select" onchange="window.location.href='<?= site_url('/admin/appointments/schedule?doctor_id=') ?>' + this.value">
                    <?php foreach ($doctors as $doc): ?>
                        <option value="<?= $doc['id'] ?>" <?= (int)$selected_doctor['id'] === (int)$doc['id'] ? 'selected' : '' ?>>
                            Dr. <?= esc($doc['username']) ?>
                            <?php if (!empty($doc['branch_name'])): ?>
                                (<?= esc($doc['branch_name']) ?>)
                            <?php endif; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Receptionist: শুধু নিজের branch এর ডাক্তারদের schedule করতে পারে -->
            <form action="<?= site_url('/admin/appointments/schedule/save') ?>" method="POST">
                <?= csrf_field() ?>
                <input type="hidden" name="doctor_id" value="<?= $selected_doctor['id'] ?>">

                <div class="mb-3">
                    <label for="day_of_week" class="form-label small fw-semibold">Weekday</label>
                    <select class="form-control form-control-sm form-select" id="day_of_week" name="day_of_week" required>
                        <option value="Monday">Monday</option>
                        <option value="Tuesday">Tuesday</option>
                        <option value="Wednesday">Wednesday</option>
                        <option value="Thursday">Thursday</option>
                        <option value="Friday">Friday</option>
                        <option value="Saturday">Saturday</option>
                        <option value="Sunday">Sunday</option>
                    </select>
                </div>

                <div class="row g-2 mb-3">
                    <div class="col-6">
                        <label for="start_time" class="form-label small fw-semibold">Start Time</label>
                        <input type="time" class="form-control form-control-sm" id="start_time" name="start_time" value="09:00" required>
                    </div>
                    <div class="col-6">
                        <label for="end_time" class="form-label small fw-semibold">End Time</label>
                        <input type="time" class="form-control form-control-sm" id="end_time" name="end_time" value="17:00" required>
                    </div>
                </div>

                <div class="row g-2 mb-3">
                    <div class="col-6">
                        <label for="slot_duration" class="form-label small fw-semibold">Slot Duration (Mins)</label>
                        <select class="form-control form-control-sm form-select" id="slot_duration" name="slot_duration">
                            <option value="10">10 mins</option>
                            <option value="15" selected>15 mins</option>
                            <option value="20">20 mins</option>
                            <option value="30">30 mins</option>
                            <option value="45">45 mins</option>
                            <option value="60">60 mins</option>
                        </select>
                    </div>
                    <div class="col-6">
                        <label for="max_patients" class="form-label small fw-semibold">Max Daily Patients</label>
                        <input type="number" class="form-control form-control-sm" id="max_patients" name="max_patients" value="25" min="1" required>
                    </div>
                </div>

                <div class="mb-4">
                    <label for="status" class="form-label small fw-semibold">Status</label>
                    <select class="form-control form-control-sm form-select" id="status" name="status">
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div>

                <button type="submit" class="btn btn-primary btn-sm w-100 shadow-sm">
                    <i class="bi bi-save me-1"></i> Save Day Schedule
                </button>
            </form>
        </div>
    </div>

    <!-- Configured Shifts List -->
    <div class="col-lg-7 mb-4">
        <div class="card border-0 shadow-sm p-4">
            <h5 class="fw-bold text-slate mb-3">
                <i class="bi bi-list-stars text-success me-2"></i>
                Active Schedules for Dr. <?= esc($selected_doctor['username']) ?>
            </h5>
            
            <div class="table-responsive border-0 shadow-none">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Day</th>
                            <th>Shift Hours</th>
                            <th>Slot duration</th>
                            <th>Max limits</th>
                            <th class="text-end">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($schedules)): ?>
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted">
                                    <i class="bi bi-calendar-x fs-3 d-block mb-2"></i>
                                    No shifts configured for this doctor yet.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($schedules as $sch): ?>
                                <tr>
                                    <td class="fw-bold text-slate"><?= esc($sch['day_of_week']) ?></td>
                                    <td><?= esc(date('h:i A', strtotime($sch['start_time']))) ?> - <?= esc(date('h:i A', strtotime($sch['end_time']))) ?></td>
                                    <td><?= esc((string)$sch['slot_duration']) ?> Mins</td>
                                    <td><?= esc((string)$sch['max_patients']) ?> Patients</td>
                                    <td class="text-end">
                                        <span class="badge <?= $sch['status'] === 'active' ? 'bg-success bg-opacity-10 text-success border border-success border-opacity-25' : 'bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-25' ?> rounded px-2.5 py-1.5">
                                            <?= esc(ucfirst($sch['status'])) ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php 
if ($isReceptionist) {
    include VIEWS_PATH . '/layout/reception_footer.php'; 
} else {
    include VIEWS_PATH . '/layout/admin_footer.php'; 
}
?>