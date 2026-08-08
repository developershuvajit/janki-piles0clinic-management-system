<?php
declare(strict_types=1);

namespace App\Models;

use App\Helpers\Database;
use App\Helpers\Security;

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
}

