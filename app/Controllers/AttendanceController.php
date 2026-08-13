<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Models\Attendance;
use App\Models\Employee;
use App\Helpers\Session;
use App\Helpers\Permission;
use App\Helpers\Database;
use App\Helpers\ActivityLogger;

class AttendanceController
{
    private function getBranchFilter(): array
    {
        $user = Session::user();
        $roleSlug = $user['role_slug'] ?? $user['role'] ?? '';
        $branchId = $user['branch_id'] ?? null;
        
        $isSuperAdmin = ($roleSlug === 'super_admin' || $roleSlug === 'admin');
        $hasBranchFilter = (!$isSuperAdmin && $branchId !== null);
        
        return [
            'isSuperAdmin' => $isSuperAdmin,
            'branchId' => $branchId,
            'hasFilter' => $hasBranchFilter
        ];
    }

    public function __construct()
    {
        if (!Session::get('user_id')) {
            redirect('/login');
        }
    }

    public function register()
    {
        Permission::check('record_attendance');
        
        $filter = $this->getBranchFilter();
        $date = $_GET['date'] ?? date('Y-m-d');
        $branchId = $filter['hasFilter'] ? $filter['branchId'] : null;
        
        $roster = Attendance::getDailyRoster($date, $branchId);
        
        view('admin.attendance.register', [
            'title' => 'Daily Attendance Register',
            'roster' => $roster,
            'date' => $date,
            'activePage' => 'attendance'
        ]);
    }

    public function saveAttendance()
    {
        Permission::check('record_attendance');
        
        $date = $_POST['date'] ?? date('Y-m-d');
        $attendanceData = $_POST['attendance'] ?? [];

        foreach ($attendanceData as $empId => $att) {
            $existing = Attendance::getToday((int)$empId, $date);
            
            if ($existing) {
                $sql = "UPDATE employee_attendance SET 
                            status = :status, 
                            check_in = :check_in, 
                            check_out = :check_out, 
                            notes = :notes,
                            updated_at = NOW() 
                        WHERE employee_id = :id AND date = :date";
                
                Database::execute($sql, [
                    'id' => (int)$empId,
                    'date' => $date,
                    'status' => $att['status'],
                    'check_in' => $att['check_in'] ?? null,
                    'check_out' => $att['check_out'] ?? null,
                    'notes' => $att['notes'] ?? null
                ]);
            } else {
                Attendance::logAttendance([
                    'employee_id' => (int)$empId,
                    'date' => $date,
                    'status' => $att['status'],
                    'check_in' => $att['check_in'] ?? null,
                    'check_out' => $att['check_out'] ?? null,
                    'notes' => $att['notes'] ?? null
                ]);
            }
        }

        ActivityLogger::log('Attendance Sheet Update', "Updated attendance for date: {$date}");
        Session::setFlash('success', 'Attendance records updated successfully.');
        redirect('/admin/attendance/register?date=' . $date);
    }

    public function leavesList()
    {
        Permission::check('record_attendance');
        
        $filter = $this->getBranchFilter();
        $branchId = $filter['hasFilter'] ? $filter['branchId'] : null;
        $leaves = Attendance::getLeavesList($branchId);
        
        view('admin.attendance.leaves', [
            'title' => 'Leave Management',
            'leaves' => $leaves,
            'activePage' => 'leaves'
        ]);
    }

    public function applyLeave()
    {
        Permission::check('record_attendance');
        
        $data = [
            'employee_id' => (int)Session::get('user_id'),
            'leave_type' => $_POST['leave_type'] ?? '',
            'start_date' => $_POST['start_date'] ?? '',
            'end_date' => $_POST['end_date'] ?? '',
            'reason' => trim($_POST['reason'] ?? '')
        ];

        $success = Attendance::applyLeave($data);
        if ($success) {
            ActivityLogger::log('Leave Request', "Leave request submitted for employee ID: {$data['employee_id']}");
            Session::setFlash('success', 'Leave request submitted.');
        } else {
            Session::setFlash('error', 'Failed submitting leave request.');
        }
        redirect('/admin/attendance/leaves');
    }

    public function approveLeave($id)
    {
        Permission::check('record_attendance');
        
        $success = Attendance::updateLeaveStatus((int)$id, 'approved');
        if ($success) {
            ActivityLogger::log('Leave Approved', "Leave request #{$id} approved");
            Session::setFlash('success', 'Leave approved.');
        } else {
            Session::setFlash('error', 'Failed approving leave.');
        }
        redirect('/admin/attendance/leaves');
    }

    public function rejectLeave($id)
    {
        Permission::check('record_attendance');
        
        $success = Attendance::updateLeaveStatus((int)$id, 'rejected');
        if ($success) {
            ActivityLogger::log('Leave Rejected', "Leave request #{$id} rejected");
            Session::setFlash('success', 'Leave request rejected.');
        } else {
            Session::setFlash('error', 'Failed rejecting leave.');
        }
        redirect('/admin/attendance/leaves');
    }

    public function scanAttendance()
    {
        Permission::check('record_attendance');
        
        view('admin.attendance.scan', [
            'title' => 'QR Code Attendance Scanner',
            'activePage' => 'attendance_scan'
        ]);
    }

    public function fetchEmployee()
    {
        error_log("fetchEmployee called with ID: " . ($_GET['id'] ?? 'none'));
        header('Content-Type: application/json');
        
        try {
            $id = (int)($_GET['id'] ?? 0);
            
            if (!$id) {
                echo json_encode(['success' => false, 'message' => 'Invalid employee ID']);
                return;
            }
            
            $filter = $this->getBranchFilter();
            
            $sql = "SELECT 
                        e.*,
                        u.username,
                        u.email,
                        u.status as user_status,
                        u.role_id,
                        u.branch_id,
                        r.name as role_name,
                        r.slug as role_slug,
                        b.name as branch_name
                    FROM employees e
                    JOIN users u ON e.user_id = u.id
                    LEFT JOIN roles r ON u.role_id = r.id
                    LEFT JOIN branches b ON u.branch_id = b.id
                    WHERE e.id = ? AND u.status = 'active'";
            
            $params = [$id];
            
            if ($filter['hasFilter']) {
                $sql .= " AND u.branch_id = ?";
                $params[] = $filter['branchId'];
            }
            
            $sql .= " LIMIT 1";
            
            $employee = Database::row($sql, $params);
            
            if ($employee) {
                $employee['role_name'] = $employee['role_name'] ?? 'Staff';
                $employee['shift_start'] = $employee['shift_start'] ?? '09:00:00';
                $employee['shift_end'] = $employee['shift_end'] ?? '17:00:00';
                $employee['employee_code'] = 'EMP-' . str_pad((string)$id, 5, '0', STR_PAD_LEFT);
                
                $today = date('Y-m-d');
                $existing = Attendance::getToday($id, $today);
                
                if ($existing) {
                    $employee['today_status'] = $existing['status'] ?? 'present';
                    $employee['today_check_in'] = $existing['check_in'] ?? null;
                    $employee['today_check_out'] = $existing['check_out'] ?? null;
                    $employee['already_marked'] = true;
                } else {
                    $employee['today_status'] = 'not_marked';
                    $employee['today_check_in'] = null;
                    $employee['today_check_out'] = null;
                    $employee['already_marked'] = false;
                }
                
                error_log("Employee found: " . $employee['username']);
                echo json_encode(['success' => true, 'employee' => $employee]);
            } else {
                error_log("Employee not found for ID: " . $id);
                echo json_encode(['success' => false, 'message' => 'Employee not found or access denied']);
            }
        } catch (Exception $e) {
            error_log("fetchEmployee error: " . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
    }

    public function markAttendance()
    {
        error_log("markAttendance called");
        header('Content-Type: application/json');
        
        try {
            $rawInput = file_get_contents('php://input');
            error_log("Raw input: " . $rawInput);
            
            $input = json_decode($rawInput, true);
            
            if ($input === null) {
                echo json_encode(['success' => false, 'message' => 'Invalid request data']);
                return;
            }
            
            $employeeId = isset($input['employee_id']) ? (int)$input['employee_id'] : 0;
            
            if (!$employeeId) {
                echo json_encode(['success' => false, 'message' => 'Invalid employee ID']);
                return;
            }
            
            $filter = $this->getBranchFilter();
            
            $sql = "SELECT e.*, u.branch_id, u.username 
                    FROM employees e 
                    JOIN users u ON e.user_id = u.id 
                    WHERE e.id = ?";
            $params = [$employeeId];
            
            if ($filter['hasFilter']) {
                $sql .= " AND u.branch_id = ?";
                $params[] = $filter['branchId'];
            }
            
            $employeeData = Database::row($sql, $params);
            
            if (!$employeeData) {
                echo json_encode(['success' => false, 'message' => 'Employee not found or access denied']);
                return;
            }
            
            $date = date('Y-m-d');
            $currentTime = date('H:i:s');
            $currentTimestamp = strtotime($currentTime);
            
            $employee = Employee::find($employeeId);
            if (!$employee) {
                echo json_encode(['success' => false, 'message' => 'Employee record not found']);
                return;
            }
            
            $shiftStart = $employee['shift_start'] ?? '09:00:00';
            $shiftEnd = $employee['shift_end'] ?? '17:00:00';
            $shiftStartTimestamp = strtotime($shiftStart);
            $shiftEndTimestamp = strtotime($shiftEnd);
            
            $existing = Attendance::getToday($employeeId, $date);
            
            if ($existing && !empty($existing['check_in']) && empty($existing['check_out'])) {
                $checkInTime = $existing['check_in'];
                $checkInTimestamp = strtotime($checkInTime);
                $timeDiff = ($currentTimestamp - $checkInTimestamp) / 60;
                
                if ($timeDiff < 5) {
                    echo json_encode([
                        'success' => false, 
                        'message' => 'Please wait ' . (5 - round($timeDiff)) . ' more minutes before checking out.'
                    ]);
                    return;
                }
                
                $result = Attendance::updateCheckOut($employeeId, $date, $currentTime);
                $message = '✅ Check-out recorded successfully!';
                $status = 'checkout';
                $isLate = false;
                $lateMinutes = 0;
                
            } else {
                $isWithinShift = ($currentTimestamp >= $shiftStartTimestamp && $currentTimestamp <= $shiftEndTimestamp);
                
                if (!$isWithinShift) {
                    if ($currentTimestamp > $shiftEndTimestamp) {
                        echo json_encode([
                            'success' => false, 
                            'message' => 'Shift ended at ' . date('h:i A', strtotime($shiftEnd))
                        ]);
                        return;
                    } else {
                        echo json_encode([
                            'success' => false, 
                            'message' => 'Shift starts at ' . date('h:i A', strtotime($shiftStart))
                        ]);
                        return;
                    }
                }
                
                $gracePeriod = 10;
                $graceTimestamp = $shiftStartTimestamp + ($gracePeriod * 60);
                
                if ($currentTimestamp > $graceTimestamp) {
                    $isLate = true;
                    $attStatus = 'late';
                    $lateMinutes = round(($currentTimestamp - $shiftStartTimestamp) / 60);
                    $message = '⚠️ Late check-in! (' . $lateMinutes . ' min late)';
                } else {
                    $isLate = false;
                    $attStatus = 'present';
                    $lateMinutes = 0;
                    $message = '✅ Check-in recorded successfully!';
                }
                
                $result = Attendance::logAttendance([
                    'employee_id' => $employeeId,
                    'date' => $date,
                    'status' => $attStatus,
                    'check_in' => $currentTime
                ]);
                $status = 'checkin';
            }
            
            if ($result) {
                $attendance = Attendance::getToday($employeeId, $date);
                
                ActivityLogger::log(
                    'QR Attendance', 
                    "Employee {$employeeData['username']} marked {$status} via QR code",
                    $employeeId
                );
                
                error_log("Attendance marked successfully for employee: " . $employeeId);
                echo json_encode([
                    'success' => true,
                    'message' => $message,
                    'status' => $status,
                    'is_late' => $isLate ?? false,
                    'late_minutes' => $lateMinutes ?? 0,
                    'shift_start' => $shiftStart,
                    'shift_end' => $shiftEnd,
                    'attendance' => $attendance
                ]);
            } else {
                error_log("Failed to record attendance for employee: " . $employeeId);
                echo json_encode(['success' => false, 'message' => 'Failed to record attendance']);
            }
            
        } catch (Exception $e) {
            error_log("markAttendance error: " . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
    }

    public function todayAttendance()
    {
        error_log("todayAttendance called");
        header('Content-Type: application/json');
        
        try {
            $filter = $this->getBranchFilter();
            $date = date('Y-m-d');
            
            $sql = "SELECT 
                        a.*,
                        u.username,
                        u.email,
                        e.photo,
                        e.shift_start,
                        e.shift_end,
                        r.name as role_name,
                        r.slug as role_slug
                    FROM employee_attendance a
                    LEFT JOIN employees e ON a.employee_id = e.id
                    LEFT JOIN users u ON e.user_id = u.id
                    LEFT JOIN roles r ON u.role_id = r.id
                    WHERE a.date = ?";
            $params = [$date];
            
            if ($filter['hasFilter']) {
                $sql .= " AND u.branch_id = ?";
                $params[] = $filter['branchId'];
            }
            
            $sql .= " ORDER BY a.check_in DESC";
            
            $attendance = Database::all($sql, $params);
            
            foreach ($attendance as &$row) {
                $row['late_minutes'] = 0;
                if (($row['status'] ?? '') === 'late' && !empty($row['check_in']) && !empty($row['shift_start'])) {
                    $checkInTime = strtotime($row['check_in']);
                    $shiftStartTime = strtotime($row['shift_start']);
                    $lateMin = round(($checkInTime - $shiftStartTime) / 60);
                    $row['late_minutes'] = $lateMin > 0 ? $lateMin : 0;
                }
            }
            
            echo json_encode(['success' => true, 'attendance' => $attendance]);
            
        } catch (Exception $e) {
            error_log("todayAttendance error: " . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
    }

    public function hrReports()
    {
        Permission::check('view_logs');
        
        $filter = $this->getBranchFilter();
        $date = $_GET['date'] ?? date('Y-m-d');
        $branchId = $filter['hasFilter'] ? $filter['branchId'] : null;
        
        $roster = Attendance::getDailyRoster($date, $branchId);
        $summary = Attendance::getTodaySummary($branchId);
        $leaves = Attendance::getLeavesList($branchId);
        
        view('admin.hr.reports', [
            'title' => 'HR Reports',
            'roster' => $roster,
            'summary' => $summary,
            'leaves' => $leaves,
            'date' => $date,
            'activePage' => 'reports'
        ]);
    }

    public function idCards()
    {
        Permission::check('manage_employees');
        
        $filter = $this->getBranchFilter();
        $branchId = $filter['hasFilter'] ? $filter['branchId'] : null;
        $employees = Employee::all($branchId);
        
        view('admin.employees.id_cards', [
            'title' => 'Employee ID Cards',
            'employees' => $employees,
            'activePage' => 'id_cards'
        ]);
    }

    public function generateQR()
    {
        $data = $_GET['data'] ?? '';
        if (empty($data)) {
            header('Content-Type: image/png');
            echo base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mN8//8/AwAI/AL+E9K3IwAAAABJRU5ErkJggg==');
            exit;
        }
        
        $qrUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=' . urlencode($data);
        header('Location: ' . $qrUrl);
        exit;
    }

    public function attendanceReport()
    {
        Permission::check('view_logs');
        
        $filter = $this->getBranchFilter();
        $date = $_GET['date'] ?? date('Y-m-d');
        $statusFilter = $_GET['status'] ?? '';
        $branchId = $filter['hasFilter'] ? $filter['branchId'] : null;
        
        $employeesSql = "SELECT 
                            e.id as employee_id,
                            u.username,
                            u.email,
                            e.photo,
                            r.name as role_name,
                            e.shift_start,
                            e.shift_end
                        FROM users u
                        JOIN roles r ON u.role_id = r.id
                        LEFT JOIN employees e ON u.id = e.user_id
                        WHERE u.status = 'active'";
        
        $employeesParams = [];
        if ($branchId !== null) {
            $employeesSql .= " AND u.branch_id = ?";
            $employeesParams[] = $branchId;
        }
        $employeesSql .= " ORDER BY u.username ASC";
        
        $allEmployees = Database::all($employeesSql, $employeesParams);
        
        $attendanceSql = "SELECT 
                            e.id as employee_id,
                            ea.date,
                            ea.check_in,
                            ea.check_out,
                            ea.status,
                            ea.notes
                        FROM employee_attendance ea
                        LEFT JOIN employees e ON ea.employee_id = e.id
                        WHERE ea.date = ?";
        
        $attendanceParams = [$date];
        
        if (!empty($statusFilter)) {
            $attendanceSql .= " AND ea.status = ?";
            $attendanceParams[] = $statusFilter;
        }
        
        if ($branchId !== null) {
            $attendanceSql .= " AND e.branch_id = ?";
            $attendanceParams[] = $branchId;
        }
        
        $attendanceSql .= " ORDER BY e.id ASC";
        $attendanceRecords = Database::all($attendanceSql, $attendanceParams);
        
        $attendanceLookup = [];
        foreach ($attendanceRecords as $att) {
            $attendanceLookup[$att['employee_id']] = $att;
        }
        
        $attendanceData = [];
        foreach ($allEmployees as $emp) {
            $empId = $emp['employee_id'];
            
            if (isset($attendanceLookup[$empId])) {
                $attendanceData[] = array_merge($emp, $attendanceLookup[$empId]);
            } else {
                $attendanceData[] = array_merge($emp, [
                    'date' => $date,
                    'check_in' => null,
                    'check_out' => null,
                    'status' => 'not_marked',
                    'notes' => null
                ]);
            }
        }
        
        $totalStaff = count($attendanceData);
        $present = 0;
        $absent = 0;
        $late = 0;
        $leave = 0;
        $halfDay = 0;
        $notMarked = 0;
        
        foreach ($attendanceData as $row) {
            $status = $row['status'] ?? 'not_marked';
            if ($status === 'present') $present++;
            elseif ($status === 'absent') $absent++;
            elseif ($status === 'late') $late++;
            elseif ($status === 'leave') $leave++;
            elseif ($status === 'half_day') $halfDay++;
            else $notMarked++;
        }
        
        view('admin.attendance.report', [
            'title' => 'Attendance Report',
            'attendanceData' => $attendanceData,
            'date' => $date,
            'totalStaff' => $totalStaff,
            'present' => $present,
            'absent' => $absent,
            'late' => $late,
            'leave' => $leave,
            'halfDay' => $halfDay,
            'notMarked' => $notMarked,
            'activePage' => 'attendance_report'
        ]);
    }

    public function generateIDCards()
    {
        if (ob_get_level()) ob_clean();
        
        header('Content-Type: application/json');
        
        try {
            Permission::check('manage_employees');
            
            $employeeIds = $_POST['employee_ids'] ?? [];
            
            if (empty($employeeIds)) {
                $rawInput = file_get_contents('php://input');
                if (!empty($rawInput)) {
                    $input = json_decode($rawInput, true);
                    $employeeIds = $input['employee_ids'] ?? [];
                }
            }
            
            if (empty($employeeIds)) {
                echo json_encode(['success' => false, 'message' => 'No employees selected']);
                return;
            }
            
            $filter = $this->getBranchFilter();
            $branchId = $filter['hasFilter'] ? $filter['branchId'] : null;
            
            $placeholders = implode(',', array_fill(0, count($employeeIds), '?'));
            $sql = "
                SELECT e.*, u.username, u.email, u.branch_id, u.status as user_status,
                       r.name as role_name, r.slug as role_slug,
                       b.name as branch_name
                FROM employees e
                JOIN users u ON e.user_id = u.id
                LEFT JOIN roles r ON u.role_id = r.id
                LEFT JOIN branches b ON u.branch_id = b.id
                WHERE e.id IN ({$placeholders}) AND u.status = 'active'
            ";
            
            $params = $employeeIds;
            
            if ($branchId !== null) {
                $sql .= " AND u.branch_id = ?";
                $params[] = $branchId;
            }
            
            $employees = Database::all($sql, $params);
            
            if (empty($employees)) {
                echo json_encode(['success' => false, 'message' => 'No employees found']);
                return;
            }
            
            $html = '';
            
            foreach ($employees as $emp) {
                $empId = isset($emp['id']) ? (int)$emp['id'] : 0;
                if ($empId <= 0) continue;
                
                $username = $emp['username'] ?? 'Unknown';
                $roleName = $emp['role_name'] ?? 'Staff';
                $branchName = $emp['branch_name'] ?? 'Main Branch';
                $photoPath = !empty($emp['photo']) ? $emp['photo'] : '';
                
                $qrData = json_encode([
                    'type' => 'employee_id',
                    'id' => $empId,
                    'name' => $username,
                    'code' => 'EMP-' . str_pad((string)$empId, 5, '0', STR_PAD_LEFT)
                ]);
                
                $qrCodeUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=' . urlencode($qrData);
                
                $photoHtml = '';
                if (!empty($photoPath) && file_exists(PUBLIC_PATH . '/' . $photoPath)) {
                    $photoHtml = '<img src="' . site_url($photoPath) . '" alt="' . htmlspecialchars($username) . '" style="width:85px;height:85px;object-fit:cover;border-radius:50%;border:3px solid #e2e8f0;">';
                } else {
                    $initial = strtoupper(substr($username, 0, 1));
                    $photoHtml = '<div style="width:85px;height:85px;border-radius:50%;background:linear-gradient(135deg,#3b82f6,#2563eb);display:flex;align-items:center;justify-content:center;font-size:32px;color:#fff;margin:0 auto;">' . $initial . '</div>';
                }
                
                $empCode = 'EMP-' . str_pad((string)$empId, 5, '0', STR_PAD_LEFT);
                $empName = strtoupper($username);
                
                $html .= '
                <div class="id-card-vertical">
                    <div class="id-card-header">
                        <div class="id-card-clinic">
                            <span class="clinic-name">Janki Piles Clinic</span>
                            <span class="clinic-sub">Laser Proctology Center</span>
                        </div>
                        <div class="id-card-type">Employee ID</div>
                    </div>
                    <div class="id-card-body">
                        <div class="id-card-photo">' . $photoHtml . '</div>
                        <div class="id-card-name">' . htmlspecialchars($empName) . '</div>
                        <div class="id-card-role">' . htmlspecialchars($roleName) . '</div>
                        <div class="id-card-code">' . $empCode . '</div>
                    </div>
                    <div class="id-card-footer">
                        <div class="id-card-qr">
                            <img src="' . $qrCodeUrl . '" alt="QR Code">
                        </div>
                    </div>
                    <div class="id-card-bottom">
                        <div class="info-item"><i class="bi bi-building"></i> <span class="label">Branch:</span> ' . htmlspecialchars($branchName) . '</div>
                    </div>
                    <div class="id-card-validity">
                        <span class="id-card-valid"><i class="bi bi-check-circle-fill"></i> Valid till: ' . date('d M Y', strtotime('+1 year')) . '</span>
                        <span class="id-card-issued">Issued: ' . date('d M Y') . '</span>
                    </div>
                </div>
                ';
            }
            
            if (empty($html)) {
                echo json_encode(['success' => false, 'message' => 'No valid employees found']);
                return;
            }
            
            echo json_encode(['success' => true, 'html' => $html]);
            
        } catch (Exception $e) {
            echo json_encode([
                'success' => false, 
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }
}