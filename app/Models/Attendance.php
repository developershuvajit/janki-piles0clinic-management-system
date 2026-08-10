<?php
declare(strict_types=1);

namespace App\Models;

use App\Helpers\Database;
use App\Helpers\Security;
use App\Helpers\Logger;

class Attendance
{
    /**
     * Get staff attendance records for a specific date.
     */
    public static function getDailyRoster(string $date, ?int $branchId = null): array
    {
        $sql = "SELECT u.id as user_id, e.id as employee_id, u.username, u.email, r.name as role_name, r.slug as role_slug, 
                       ea.id as attendance_id, ea.date, ea.check_in, ea.check_out, ea.status, ea.notes 
                FROM users u
                JOIN roles r ON u.role_id = r.id
                LEFT JOIN employees e ON u.id = e.user_id
                LEFT JOIN employee_attendance ea ON (e.id = ea.employee_id OR u.id = ea.employee_id) AND ea.date = :date
                WHERE u.status = 'active'";
        $params = ['date' => $date];

        if ($branchId !== null) {
            $sql .= " AND (u.branch_id = :branch_id OR u.branch_id IS NULL)";
            $params['branch_id'] = $branchId;
        }

        $sql .= " ORDER BY u.username ASC";

        return Database::all($sql, $params);
    }

    /**
     * Mark or log attendance record.
     */
    public static function logAttendance(array $data): bool
    {
        $employeeId = (int)$data['employee_id'];
        $date = $data['date'];
        $status = $data['status'];
        $checkIn = $data['check_in'] ?? null;
        $checkOut = $data['check_out'] ?? null;
        $notes = isset($data['notes']) ? Security::sanitize($data['notes']) : null;

        $sql = "INSERT INTO employee_attendance (employee_id, date, status, check_in, check_out, notes) 
                VALUES (:employee_id, :date, :status, :check_in, :check_out, :notes)
                ON DUPLICATE KEY UPDATE 
                status = VALUES(status), 
                check_in = VALUES(check_in), 
                check_out = VALUES(check_out), 
                notes = VALUES(notes)";

        return Database::execute($sql, [
            'employee_id' => $employeeId,
            'date' => $date,
            'status' => $status,
            'check_in' => $checkIn ?: null,
            'check_out' => $checkOut ?: null,
            'notes' => $notes
        ]);
    }

    /**
     * Legacy mark method alias.
     */
    public static function mark(int $userId, string $date, string $status, ?string $checkIn = null, ?string $checkOut = null, ?string $notes = null, ?int $branchId = null): bool
    {
        return self::logAttendance([
            'employee_id' => $userId,
            'date' => $date,
            'status' => $status,
            'check_in' => $checkIn,
            'check_out' => $checkOut,
            'notes' => $notes
        ]);
    }

    /**
     * Get attendance summary counts for today.
     */
    public static function getTodaySummary(?int $branchId = null): array
    {
        $today = date('Y-m-d');
        $roster = self::getDailyRoster($today, $branchId);

        $summary = [
            'total_staff' => count($roster),
            'present' => 0,
            'late' => 0,
            'half_day' => 0,
            'leave' => 0,
            'absent' => 0,
            'not_marked' => 0
        ];

        foreach ($roster as $r) {
            if (!empty($r['status'])) {
                $status = $r['status'];
                if (isset($summary[$status])) {
                    $summary[$status]++;
                }
            } else {
                $summary['not_marked']++;
            }
        }

        return $summary;
    }

    /**
     * Get leave applications list.
     */
    public static function getLeavesList(?int $branchId = null): array
    {
        $sql = "SELECT l.*, u.username as employee_name, r.name as role_name 
                FROM employee_leaves l
                LEFT JOIN employees e ON l.employee_id = e.id
                LEFT JOIN users u ON (e.user_id = u.id OR l.employee_id = u.id)
                LEFT JOIN roles r ON u.role_id = r.id";
        $params = [];

        if ($branchId !== null) {
            $sql .= " WHERE u.branch_id = :branch_id";
            $params['branch_id'] = $branchId;
        }

        $sql .= " ORDER BY l.created_at DESC";
        return Database::all($sql, $params);
    }

    /**
     * Apply for leave.
     */
    public static function applyLeave(array $data): bool
    {
        $empId = (int)$data['employee_id'];
        $emp = Database::row("SELECT id FROM employees WHERE id = :id OR user_id = :uid LIMIT 1", [
            'id' => $empId,
            'uid' => $empId
        ]);
        if ($emp) {
            $empId = (int)$emp['id'];
        }

        $sql = "INSERT INTO employee_leaves (employee_id, leave_type, start_date, end_date, reason, status) 
                VALUES (:employee_id, :leave_type, :start_date, :end_date, :reason, 'pending')";

        return Database::execute($sql, [
            'employee_id' => $empId,
            'leave_type' => $data['leave_type'],
            'start_date' => $data['start_date'],
            'end_date' => $data['end_date'],
            'reason' => Security::sanitize($data['reason'] ?? '')
        ]);
    }

    /**
     * Update leave approval status.
     */
    public static function updateLeaveStatus(int $id, string $status): bool
    {
        $sql = "UPDATE employee_leaves SET status = :status WHERE id = :id";
        return Database::execute($sql, ['status' => $status, 'id' => $id]);
    }

    // ============================================
    // NEW METHODS FOR QR CODE ATTENDANCE
    // ============================================

    /**
     * Get today's attendance for a specific employee
     * 
     * @param int $employeeId
     * @param string $date
     * @return array|null
     */
    public static function getToday(int $employeeId, string $date): ?array
    {
        return Database::row(
            "SELECT * FROM employee_attendance 
             WHERE employee_id = :id AND date = :date",
            ['id' => $employeeId, 'date' => $date]
        );
    }

    /**
     * Get all today's attendance with employee details
     * 
     * @param string $date
     * @return array
     */
    public static function getTodayAll(string $date): array
    {
        return Database::all(
            "SELECT a.*, u.username, e.photo 
             FROM employee_attendance a
             LEFT JOIN employees e ON a.employee_id = e.id
             LEFT JOIN users u ON e.user_id = u.id
             WHERE a.date = :date 
             ORDER BY a.check_in DESC",
            ['date' => $date]
        );
    }

    /**
     * Update check-out time for an employee
     * 
     * @param int $employeeId
     * @param string $date
     * @param string $time
     * @return bool
     */
    public static function updateCheckOut(int $employeeId, string $date, string $time): bool
    {
        return Database::execute(
            "UPDATE employee_attendance 
             SET check_out = :time, updated_at = NOW() 
             WHERE employee_id = :id AND date = :date",
            ['time' => $time, 'id' => $employeeId, 'date' => $date]
        );
    }

    /**
     * Get attendance by date range
     * 
     * @param int $employeeId
     * @param string $startDate
     * @param string $endDate
     * @return array
     */
    public static function getByDateRange(int $employeeId, string $startDate, string $endDate): array
    {
        return Database::all(
            "SELECT * FROM employee_attendance 
             WHERE employee_id = :id 
             AND date BETWEEN :start AND :end 
             ORDER BY date DESC",
            ['id' => $employeeId, 'start' => $startDate, 'end' => $endDate]
        );
    }

    /**
     * Get attendance statistics for an employee
     * 
     * @param int $employeeId
     * @param string $month
     * @return array
     */
    public static function getMonthlyStats(int $employeeId, string $month): array
    {
        $startDate = $month . '-01';
        $endDate = date('Y-m-t', strtotime($startDate));
        
        $records = self::getByDateRange($employeeId, $startDate, $endDate);
        
        $stats = [
            'total_days' => date('t', strtotime($startDate)),
            'present' => 0,
            'absent' => 0,
            'late' => 0,
            'half_day' => 0,
            'leave' => 0
        ];
        
        foreach ($records as $record) {
            if (isset($stats[$record['status']])) {
                $stats[$record['status']]++;
            }
        }
        
        $stats['absent'] = $stats['total_days'] - $stats['present'] - $stats['leave'];
        
        return $stats;
    }

    /**
     * Check if employee is already checked in today
     * 
     * @param int $employeeId
     * @param string $date
     * @return bool
     */
    public static function isCheckedIn(int $employeeId, string $date): bool
    {
        $record = self::getToday($employeeId, $date);
        return $record !== null && !empty($record['check_in']);
    }

    /**
     * Get all employees who are currently checked in
     * 
     * @param string $date
     * @return array
     */
    public static function getCurrentlyCheckedIn(string $date): array
    {
        return Database::all(
            "SELECT a.*, u.username, e.photo 
             FROM employee_attendance a
             LEFT JOIN employees e ON a.employee_id = e.id
             LEFT JOIN users u ON e.user_id = u.id
             WHERE a.date = :date 
             AND a.check_in IS NOT NULL 
             AND a.check_out IS NULL
             ORDER BY a.check_in DESC",
            ['date' => $date]
        );
    }

    /**
     * Get today's attendance with status counts
     * 
     * @param string $date
     * @return array
     */
    public static function getTodayWithStats(string $date): array
    {
        $attendance = self::getTodayAll($date);
        
        $stats = [
            'total' => count($attendance),
            'present' => 0,
            'absent' => 0,
            'late' => 0,
            'half_day' => 0,
            'leave' => 0
        ];
        
        foreach ($attendance as $record) {
            if (isset($stats[$record['status']])) {
                $stats[$record['status']]++;
            }
        }
        
        return [
            'stats' => $stats,
            'records' => $attendance
        ];
    }
}