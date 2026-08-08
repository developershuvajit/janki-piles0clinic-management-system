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
            'employee_id' => (int)Session::get('user_id'), // maps logged employee
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
}
