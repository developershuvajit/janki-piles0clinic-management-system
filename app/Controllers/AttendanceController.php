<?php

namespace App\Controllers;

use App\Models\Attendance;
use App\Models\Employee;
use App\Helpers\Session;
use App\Helpers\Permission;

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
            Attendance::logAttendance([
                'employee_id' => (int)$empId,
                'date' => $date,
                'status' => $att['status'],
                'check_in' => $att['check_in'] ?? null,
                'check_out' => $att['check_out'] ?? null,
                'notes' => $att['notes'] ?? null
            ]);
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
            
            // Use QR Server API for QR code (larger size)
            $qrCodeUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=' . urlencode($qrData);
            
            $photoHtml = !empty($emp['photo']) 
                ? '<img src="' . site_url($emp['photo']) . '" style="width:100%;height:100%;object-fit:cover;">' 
                : '<i class="bi bi-person-fill" style="font-size:3rem;color:#94a3b8;"></i>';
            
            $branchName = $emp['branch_name'] ?? 'Main Branch';
            $roleName = $emp['role_name'] ?? 'Staff';
            $empCode = 'EMP-' . str_pad($emp['id'], 5, '0', STR_PAD_LEFT);
            
            $html .= '
            <div class="id-card-vertical">
                <!-- Card Header -->
                <div class="id-card-header">
                    <div class="id-card-clinic">
                        <span class="clinic-icon">🏥</span>
                        <span class="clinic-name">Janki Piles Clinic</span>
                    </div>
                    <div class="id-card-type">EMPLOYEE ID</div>
                </div>
                
                <!-- Card Body -->
                <div class="id-card-body">
                    <div class="id-card-photo">
                        ' . $photoHtml . '
                    </div>
                    <div class="id-card-info">
                        <div class="id-card-name">' . htmlspecialchars($emp['username']) . '</div>
                        <div class="id-card-role">' . htmlspecialchars($roleName) . '</div>
                        <div class="id-card-code">' . $empCode . '</div>
                    </div>
                </div>
                
                <!-- Card Footer with QR Code -->
                <div class="id-card-footer">
                    <div class="id-card-qr">
                        <img src="' . $qrCodeUrl . '" alt="QR Code" width="180" height="180">
                    </div>
                    <div class="id-card-branch">
                        <i class="bi bi-building"></i> ' . htmlspecialchars($branchName) . '
                    </div>
                </div>
                
                <!-- Card Bottom -->
                <div class="id-card-bottom">
                    <span class="id-card-valid">Valid till: ' . date('d M Y', strtotime('+1 year')) . '</span>
                </div>
            </div>
            ';
        }
        
        echo json_encode(['success' => true, 'html' => $html]);
    }

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
        
        $employee = Employee::find($id);
        if ($employee) {
            echo json_encode(['success' => true, 'employee' => $employee]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Employee not found']);
        }
    }

    /**
     * Mark attendance via QR scan
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
        $checkIn = date('H:i:s');
        
        // Check if already checked in today
        $existing = Attendance::getToday($employeeId, $date);
        if ($existing) {
            // Update check-out time
            $result = Attendance::updateCheckOut($employeeId, $date, $checkIn);
            $message = 'Check-out recorded successfully!';
        } else {
            // New check-in
            $result = Attendance::logAttendance([
                'employee_id' => $employeeId,
                'date' => $date,
                'status' => 'present',
                'check_in' => $checkIn
            ]);
            $message = 'Check-in recorded successfully!';
        }
        
        if ($result) {
            echo json_encode(['success' => true, 'message' => $message]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to record attendance']);
        }
    }

    /**
     * Get today's attendance
     */
    public function todayAttendance()
    {
        $date = date('Y-m-d');
        $attendance = Attendance::getTodayAll($date);
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
        
        // Use QR Server API
        $qrUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=' . urlencode($data);
        
        // Redirect to QR API
        header('Location: ' . $qrUrl);
        exit;
    }













    /**
 * Attendance Report
 */
        public function attendanceReport()
        {
            Permission::check('view_logs');
            
            $date = $_GET['date'] ?? date('Y-m-d');
            $branchId = Session::get('role') !== 'super_admin' ? (int)Session::get('branch_id') : null;
            
            // Get attendance data for the date
            $attendanceData = Attendance::getDailyRoster($date, $branchId);
            
            // Calculate statistics
            $totalStaff = count($attendanceData);
            $present = 0;
            $absent = 0;
            $late = 0;
            $leave = 0;
            
            foreach ($attendanceData as $row) {
                $status = $row['status'] ?? 'not_marked';
                if ($status === 'present') $present++;
                elseif ($status === 'absent') $absent++;
                elseif ($status === 'late') $late++;
                elseif ($status === 'leave') $leave++;
            }
            
            include VIEWS_PATH . '/admin/attendance/report.php';
        }
}