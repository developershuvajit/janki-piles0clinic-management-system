<?php

namespace App\Controllers;

use App\Models\Attendance;
use App\Models\Employee;
use App\Helpers\Session;
use App\Helpers\Permission;
use App\Helpers\Database;

class AttendanceController
{
    public function __construct()
    {
        if (!Session::get('user_id')) {
            redirect('/login');
        }
    }

    /**
     * Daily manual attendance logging sheet.
     */

    






    public function register()
    {
        Permission::check('record_attendance');
        
        $date = $_GET['date'] ?? date('Y-m-d');
        $branchId = Session::get('role') !== 'super_admin' ? (int)Session::get('branch_id') : null;
        
        // Get roster with existing attendance
        $roster = Attendance::getDailyRoster($date, $branchId);
        
        include VIEWS_PATH . '/admin/attendance/register.php';
    }

    /**
     * Save daily check-ins roster.
     */
    public function saveAttendance()
    {
        Permission::check('record_attendance');
        
        $date = $_POST['date'];
        $attendanceData = $_POST['attendance'] ?? [];

        foreach ($attendanceData as $empId => $att) {
            // Check if attendance already exists
            $existing = Attendance::getToday((int)$empId, $date);
            
            if ($existing) {
                // Update existing
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
                // Insert new
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

        Session::setFlash('success', 'Attendance records updated successfully.');
        redirect('/admin/employees/attendance?date=' . $date);
    }

    /**
     * Manage leaves and approvals.
     */
    public function leavesList()
    {
        Permission::check('record_attendance');
        
        $branchId = Session::get('role') !== 'super_admin' ? (int)Session::get('branch_id') : null;
        $leaves = Attendance::getLeavesList($branchId);
        
        include VIEWS_PATH . '/admin/attendance/leaves.php';
    }

    /**
     * Submit leave request.
     */
    public function applyLeave()
    {
        Permission::check('record_attendance');
        
        $data = [
            'employee_id' => (int)Session::get('user_id'),
            'leave_type' => $_POST['leave_type'],
            'start_date' => $_POST['start_date'],
            'end_date' => $_POST['end_date'],
            'reason' => trim($_POST['reason'])
        ];

        $success = Attendance::applyLeave($data);
        if ($success) {
            Session::setFlash('success', 'Leave request submitted.');
        } else {
            Session::setFlash('error', 'Failed submitting leave request.');
        }
        redirect('/admin/employees/attendance/leaves');
    }

    /**
     * Approve leave.
     */
    public function approveLeave($id)
    {
        Permission::check('record_attendance');
        
        $success = Attendance::updateLeaveStatus((int)$id, 'approved');
        if ($success) {
            Session::setFlash('success', 'Leave approved and calendar updated.');
        } else {
            Session::setFlash('error', 'Failed approving leave.');
        }
        redirect('/admin/employees/attendance/leaves');
    }

    /**
     * Reject leave.
     */
    public function rejectLeave($id)
    {
        Permission::check('record_attendance');
        
        $success = Attendance::updateLeaveStatus((int)$id, 'rejected');
        if ($success) {
            Session::setFlash('success', 'Leave request rejected.');
        } else {
            Session::setFlash('error', 'Failed rejecting leave.');
        }
        redirect('/admin/employees/attendance/leaves');
    }

    /**
     * QR Code Attendance Scan View
     */
    public function scanAttendance()
    {
        Permission::check('record_attendance');
        include VIEWS_PATH . '/admin/attendance/scan.php';
    }

    /**
     * Fetch employee by ID for attendance scanning
     */
   /**
 * Fetch employee by ID for attendance scanning
 */
   /**
 * Fetch employee by ID for attendance scanning
 */
public function fetchEmployee()
{
    $id = (int)($_GET['id'] ?? 0);
    if (!$id) {
        echo json_encode(['success' => false, 'message' => 'Invalid employee ID']);
        return;
    }
    
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
            WHERE e.id = :id LIMIT 1";
    
    $employee = Database::row($sql, ['id' => $id]);
    
    if ($employee) {
        // Set default values if null
        $employee['role_name'] = $employee['role_name'] ?? 'Staff';
        $employee['shift_start'] = $employee['shift_start'] ?? '09:00:00';
        $employee['shift_end'] = $employee['shift_end'] ?? '17:00:00';
        $employee['employee_code'] = 'EMP-' . str_pad($id, 5, '0', STR_PAD_LEFT);
        
        echo json_encode(['success' => true, 'employee' => $employee]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Employee not found']);
    }
}

    /**
     * Mark attendance via QR scan - Checkin/Checkout
     */
    /**
 * Mark attendance via QR scan - Checkin/Checkout
 */
   /**
 * Mark attendance via QR scan - Checkin/Checkout with Late detection
 */
public function markAttendance()
{
    $input = json_decode(file_get_contents('php://input'), true);
    $employeeId = (int)($input['employee_id'] ?? 0);
    
    if (!$employeeId) {
        echo json_encode(['success' => false, 'message' => 'Invalid employee']);
        return;
    }
    
    $date = date('Y-m-d');
    $currentTime = date('H:i:s');
    $currentTimestamp = strtotime($currentTime);
    
    // Get employee details with shift timings
    $employee = Employee::find($employeeId);
    if (!$employee) {
        echo json_encode(['success' => false, 'message' => 'Employee not found']);
        return;
    }
    
    $shiftStart = $employee['shift_start'] ?? '09:00:00';
    $shiftEnd = $employee['shift_end'] ?? '17:00:00';
    $shiftStartTimestamp = strtotime($shiftStart);
    $shiftEndTimestamp = strtotime($shiftEnd);
    
    // Check if already checked in today
    $existing = Attendance::getToday($employeeId, $date);
    
    // ============================================
    // SCENARIO 1: Check-out (Already checked in)
    // ============================================
    if ($existing && !empty($existing['check_in']) && empty($existing['check_out'])) {
        // Check-in time
        $checkInTime = $existing['check_in'];
        $checkInTimestamp = strtotime($checkInTime);
        
        // Check if 5 minutes have passed since check-in
        $timeDiff = ($currentTimestamp - $checkInTimestamp) / 60; // in minutes
        
        if ($timeDiff < 5) {
            echo json_encode([
                'success' => false, 
                'message' => 'Please wait ' . (5 - round($timeDiff)) . ' more minutes before checking out.'
            ]);
            return;
        }
        
        // Proceed with check-out
        $result = Attendance::updateCheckOut($employeeId, $date, $currentTime);
        $message = 'Check-out recorded successfully!';
        $status = 'checkout';
        $isLate = false;
        
    // ============================================
    // SCENARIO 2: Check-in (New attendance)
    // ============================================
    } else {
        // Check if employee is checking in within shift time
        $isWithinShift = ($currentTimestamp >= $shiftStartTimestamp && $currentTimestamp <= $shiftEndTimestamp);
        
        if (!$isWithinShift) {
            // Check if current time is after shift end
            if ($currentTimestamp > $shiftEndTimestamp) {
                echo json_encode([
                    'success' => false, 
                    'message' => 'Shift ended at ' . date('h:i A', strtotime($shiftEnd)) . '. Please contact admin.'
                ]);
                return;
            } else {
                // Before shift start
                echo json_encode([
                    'success' => false, 
                    'message' => 'Shift starts at ' . date('h:i A', strtotime($shiftStart)) . '. Please wait.'
                ]);
                return;
            }
        }
        
        // Check if employee is late (10 minutes grace period)
        $gracePeriod = 10; // minutes
        $graceTimestamp = $shiftStartTimestamp + ($gracePeriod * 60);
        
        if ($currentTimestamp > $graceTimestamp) {
            $isLate = true;
            $attStatus = 'late';
            $message = '⚠️ Late check-in recorded! (' . round(($currentTimestamp - $shiftStartTimestamp) / 60) . ' min late)';
        } else {
            $isLate = false;
            $attStatus = 'present';
            $message = 'Check-in recorded successfully!';
        }
        
        // Save attendance
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
        echo json_encode([
            'success' => true,
            'message' => $message,
            'status' => $status,
            'is_late' => $isLate ?? false,
            'shift_start' => $shiftStart,
            'shift_end' => $shiftEnd,
            'attendance' => $attendance
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to record attendance']);
    }
}

    /**
     * Get today's attendance
     */
     /**
 * Get today's attendance with employee details including role and shift
 */
public function todayAttendance()
{
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
            WHERE a.date = :date
            ORDER BY a.check_in DESC";
    
    $attendance = Database::all($sql, ['date' => $date]);
    
    // Process data to add late minutes
    foreach ($attendance as &$row) {
        // Set default role if null
        $row['role_name'] = $row['role_name'] ?? 'Staff';
        
        // Set default shift if null
        $row['shift_start'] = $row['shift_start'] ?? '09:00:00';
        $row['shift_end'] = $row['shift_end'] ?? '17:00:00';
        
        // Calculate late minutes if status is late
        if (($row['status'] ?? '') === 'late' && !empty($row['check_in']) && !empty($row['shift_start'])) {
            $checkInTime = strtotime($row['check_in']);
            $shiftStartTime = strtotime($row['shift_start']);
            $lateMin = round(($checkInTime - $shiftStartTime) / 60);
            $row['late_minutes'] = $lateMin > 0 ? $lateMin : 0;
        } else {
            $row['late_minutes'] = 0;
        }
    }
    
    echo json_encode(['success' => true, 'attendance' => $attendance]);
}

    /**
     * HR Reports
     */
    public function hrReports()
    {
        Permission::check('view_logs');
        
        $date = $_GET['date'] ?? date('Y-m-d');
        $branchId = Session::get('role') !== 'super_admin' ? (int)Session::get('branch_id') : null;
        
        $roster = Attendance::getDailyRoster($date, $branchId);
        $summary = Attendance::getTodaySummary($branchId);
        $leaves = Attendance::getLeavesList($branchId);
        
        include VIEWS_PATH . '/admin/hr/reports.php';
    }

    /**
     * Employee ID Cards View
     */
    public function idCards()
    {
        Permission::check('manage_employees');
        
        $branchId = Session::get('role') !== 'super_admin' ? (int)Session::get('branch_id') : null;
        $employees = Employee::all($branchId);
        
        include VIEWS_PATH . '/admin/employees/id_cards.php';
    }

    /**
     * Generate QR Code for employee
     */
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

    /**
     * Attendance Report
     */
     /**
 * Attendance Report
 */

     /**
 * Attendance Report
 */
/**
 * Attendance Report
 */
/**
 * Attendance Report
 */
public function attendanceReport()
{
    Permission::check('view_logs');
    
    $date = $_GET['date'] ?? date('Y-m-d');
    $month = $_GET['month'] ?? '';
    $statusFilter = $_GET['status'] ?? '';
    
    $branchId = Session::get('role') !== 'super_admin' ? (int)Session::get('branch_id') : null;
    
    // Get all active employees with their shift timings
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
        $employeesSql .= " AND u.branch_id = :branch_id";
        $employeesParams['branch_id'] = $branchId;
    }
    $employeesSql .= " ORDER BY u.username ASC";
    
    $allEmployees = Database::all($employeesSql, $employeesParams);
    
    // Get attendance data ONLY for the selected date
    $attendanceSql = "SELECT 
                        e.id as employee_id,
                        ea.date,
                        ea.check_in,
                        ea.check_out,
                        ea.status,
                        ea.notes
                    FROM employee_attendance ea
                    LEFT JOIN employees e ON ea.employee_id = e.id
                    WHERE ea.date = :date";
    
    $attendanceParams = ['date' => $date];
    
    // Status filter
    if (!empty($statusFilter)) {
        $attendanceSql .= " AND ea.status = :status";
        $attendanceParams['status'] = $statusFilter;
    }
    
    // Branch filter for attendance
    if ($branchId !== null) {
        $attendanceSql .= " AND e.branch_id = :branch_id";
        $attendanceParams['branch_id'] = $branchId;
    }
    
    $attendanceSql .= " ORDER BY e.id ASC";
    $attendanceRecords = Database::all($attendanceSql, $attendanceParams);
    
    // Create a lookup array for attendance records
    $attendanceLookup = [];
    foreach ($attendanceRecords as $att) {
        $attendanceLookup[$att['employee_id']] = $att;
    }
    
    // Merge employee data with attendance data
    $attendanceData = [];
    foreach ($allEmployees as $emp) {
        $empId = $emp['employee_id'];
        
        if (isset($attendanceLookup[$empId])) {
            // Employee has attendance record for this date
            $attendanceData[] = array_merge($emp, $attendanceLookup[$empId]);
        } else {
            // Employee has NO attendance record for this date
            $attendanceData[] = array_merge($emp, [
                'date' => $date,
                'check_in' => null,
                'check_out' => null,
                'status' => 'not_marked',
                'notes' => null,
                'has_attendance' => false
            ]);
        }
    }
    
    // Calculate statistics
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
    
    include VIEWS_PATH . '/admin/attendance/report.php';
}
    

/**
 * Generate employee ID cards (AJAX)
 */
public function generateIDCards()
{
    Permission::check('manage_employees');
    
    $input = json_decode(file_get_contents('php://input'), true);
    $employeeIds = $input['employee_ids'] ?? [];
    
    if (empty($employeeIds)) {
        echo json_encode(['success' => false, 'message' => 'No employees selected']);
        return;
    }
    
    $employees = Employee::getByIds($employeeIds);
    $html = '';
    
    foreach ($employees as $emp) {
        // Generate QR code with proper data
        $qrData = json_encode([
            'type' => 'employee_id',
            'id' => $emp['id'],
            'name' => $emp['username'],
            'code' => 'EMP-' . str_pad($emp['id'], 5, '0', STR_PAD_LEFT)
        ]);
        
        // Use QR Server API for QR code
        $qrCodeUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=180x180&data=' . urlencode($qrData);
        
        $photoHtml = !empty($emp['photo']) 
            ? '<img src="' . site_url($emp['photo']) . '" alt="' . htmlspecialchars($emp['username']) . '">' 
            : '<span class="no-photo">👤</span>';
        
        $branchName = $emp['branch_name'] ?? 'Main Branch';
        $roleName = $emp['role_name'] ?? 'Staff';
        $empCode = 'EMP-' . str_pad($emp['id'], 5, '0', STR_PAD_LEFT);
        $empName = strtoupper($emp['username']);
        
        $html .= '
        <div class="id-card-vertical">
            <div class="id-card-header">
                <div class="id-card-clinic">
                    <span class="clinic-icon">🏥</span>
                    <div>
                        <div class="clinic-name">Janki Piles Clinic</div>
                        <div class="clinic-sub">Laser Proctology Center</div>
                    </div>
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
                <div class="id-card-qr-wrapper">
                    <div class="id-card-qr">
                        <img src="' . $qrCodeUrl . '" alt="QR Code">
                    </div>
                    <div class="id-card-branch-info">
                        <div class="id-card-branch">
                            <i class="bi bi-building"></i> ' . htmlspecialchars($branchName) . '
                        </div>
                        <div class="id-card-branch-detail">
                            <span><i class="bi bi-telephone"></i> +91 98765 43210</span>
                            <span><i class="bi bi-envelope"></i> info@jankipiles.com</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="id-card-bottom">
                <span class="id-card-valid"><i class="bi bi-check-circle-fill"></i> Valid till: ' . date('d M Y', strtotime('+1 year')) . '</span>
                <span class="id-card-issued">Issued: ' . date('d M Y') . '</span>
            </div>
        </div>
        ';
    }
    
    echo json_encode(['success' => true, 'html' => $html]);
}










}