<?php
declare(strict_types=1);

namespace App\Models;

use App\Helpers\Database;
use App\Helpers\Security;

class Attendance
{
    /**
     * Get staff attendance records for a specific date with branch filter
     */
    public static function getDailyRoster(string $date, ?int $branchId = null): array
    {
        $sql = "SELECT 
                    e.id as employee_id,
                    u.username as employee_name,
                    u.email,
                    r.name as role_name,
                    ea.id as attendance_id,
                    ea.date,
                    ea.check_in,
                    ea.check_out,
                    ea.status,
                    ea.notes,
                    e.shift_start,
                    e.shift_end,
                    e.photo
                FROM users u
                JOIN roles r ON u.role_id = r.id
                LEFT JOIN employees e ON u.id = e.user_id
                LEFT JOIN employee_attendance ea ON e.id = ea.employee_id AND ea.date = :date
                WHERE u.status = 'active'";
        $params = ['date' => $date];

        if ($branchId !== null) {
            $sql .= " AND u.branch_id = :branch_id";
            $params['branch_id'] = $branchId;
        }

        $sql .= " ORDER BY u.username ASC";
        return Database::all($sql, $params);
    }

    /**
     * Log attendance record (Insert)
     */
    public static function logAttendance(array $data): bool
    {
        try {
            $employeeId = (int)$data['employee_id'];
            $date = $data['date'];
            $status = $data['status'] ?? 'present';
            $checkIn = $data['check_in'] ?? null;
            $checkOut = $data['check_out'] ?? null;
            $notes = isset($data['notes']) ? Security::sanitize($data['notes']) : null;

            // Check if record exists
            $existing = self::getToday($employeeId, $date);
            
            if ($existing) {
                // Update existing
                $sql = "UPDATE employee_attendance SET 
                            status = :status, 
                            check_in = COALESCE(:check_in, check_in),
                            check_out = :check_out,
                            notes = COALESCE(:notes, notes),
                            updated_at = NOW() 
                        WHERE employee_id = :employee_id AND date = :date";
                
                return Database::execute($sql, [
                    'employee_id' => $employeeId,
                    'date' => $date,
                    'status' => $status,
                    'check_in' => $checkIn,
                    'check_out' => $checkOut,
                    'notes' => $notes
                ]);
            } else {
                // Insert new
                $sql = "INSERT INTO employee_attendance (employee_id, date, status, check_in, check_out, notes, created_at) 
                        VALUES (:employee_id, :date, :status, :check_in, :check_out, :notes, NOW())";
                
                return Database::execute($sql, [
                    'employee_id' => $employeeId,
                    'date' => $date,
                    'status' => $status,
                    'check_in' => $checkIn,
                    'check_out' => $checkOut,
                    'notes' => $notes
                ]);
            }
        } catch (\Exception $e) {
            error_log("logAttendance Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get today's attendance for a specific employee
     */
    public static function getToday(int $employeeId, string $date): ?array
    {
        try {
            return Database::row(
                "SELECT * FROM employee_attendance 
                 WHERE employee_id = :id AND date = :date",
                ['id' => $employeeId, 'date' => $date]
            );
        } catch (\Exception $e) {
            error_log("getToday Error: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Update check-out time for an employee
     */
    public static function updateCheckOut(int $employeeId, string $date, string $time): bool
    {
        try {
            // First check if record exists
            $exists = self::getToday($employeeId, $date);
            if (!$exists) {
                return false;
            }
            
            // Update check-out time
            $sql = "UPDATE employee_attendance 
                    SET check_out = :time, updated_at = NOW() 
                    WHERE employee_id = :id AND date = :date";
            
            return Database::execute($sql, [
                'time' => $time,
                'id' => $employeeId,
                'date' => $date
            ]);
            
        } catch (\Exception $e) {
            error_log("updateCheckOut Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get all today's attendance with employee details
     */
    public static function getTodayAll(string $date, ?int $branchId = null): array
    {
        try {
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
                    WHERE a.date = :date";
            
            $params = ['date' => $date];
            
            if ($branchId !== null) {
                $sql .= " AND u.branch_id = :branch_id";
                $params['branch_id'] = $branchId;
            }
            
            $sql .= " ORDER BY a.check_in DESC";
            
            return Database::all($sql, $params);
        } catch (\Exception $e) {
            error_log("getTodayAll Error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Get today's summary with branch filter
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
     * Get leaves list with branch filter
     */
    public static function getLeavesList(?int $branchId = null): array
    {
        $sql = "SELECT 
                    l.*, 
                    u.username as employee_name, 
                    r.name as role_name,
                    e.id as employee_id
                FROM employee_leaves l
                LEFT JOIN employees e ON l.employee_id = e.id
                LEFT JOIN users u ON e.user_id = u.id
                LEFT JOIN roles r ON u.role_id = r.id
                WHERE 1=1";
        $params = [];

        if ($branchId !== null) {
            $sql .= " AND u.branch_id = :branch_id";
            $params['branch_id'] = $branchId;
        }

        $sql .= " ORDER BY l.created_at DESC";
        return Database::all($sql, $params);
    }

    /**
     * Apply for leave
     */
    public static function applyLeave(array $data): bool
    {
        try {
            $empId = (int)$data['employee_id'];
            $emp = Database::row("SELECT id FROM employees WHERE id = :id OR user_id = :uid LIMIT 1", [
                'id' => $empId,
                'uid' => $empId
            ]);
            if ($emp) {
                $empId = (int)$emp['id'];
            }

            $sql = "INSERT INTO employee_leaves (employee_id, leave_type, start_date, end_date, reason, status, created_at) 
                    VALUES (:employee_id, :leave_type, :start_date, :end_date, :reason, 'pending', NOW())";

            return Database::execute($sql, [
                'employee_id' => $empId,
                'leave_type' => $data['leave_type'],
                'start_date' => $data['start_date'],
                'end_date' => $data['end_date'],
                'reason' => Security::sanitize($data['reason'] ?? '')
            ]);
        } catch (\Exception $e) {
            error_log("applyLeave Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Update leave status
     */
    public static function updateLeaveStatus(int $id, string $status): bool
    {
        try {
            return Database::execute(
                "UPDATE employee_leaves SET status = :status, updated_at = NOW() WHERE id = :id",
                ['status' => $status, 'id' => $id]
            );
        } catch (\Exception $e) {
            error_log("updateLeaveStatus Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get attendance by date range with branch filter
     */
    public static function getByDateRange(string $startDate, string $endDate, ?int $branchId = null): array
    {
        $sql = "SELECT 
                    a.*,
                    u.username,
                    u.email,
                    e.photo,
                    r.name as role_name
                FROM employee_attendance a
                LEFT JOIN employees e ON a.employee_id = e.id
                LEFT JOIN users u ON e.user_id = u.id
                LEFT JOIN roles r ON u.role_id = r.id
                WHERE a.date BETWEEN :start_date AND :end_date";
        
        $params = [
            'start_date' => $startDate,
            'end_date' => $endDate
        ];
        
        if ($branchId !== null) {
            $sql .= " AND u.branch_id = :branch_id";
            $params['branch_id'] = $branchId;
        }
        
        $sql .= " ORDER BY a.date DESC, a.check_in DESC";
        
        return Database::all($sql, $params);
    }

    /**
     * Get attendance status for an employee by date range
     */
    public static function getEmployeeAttendance(int $employeeId, string $startDate, string $endDate): array
    {
        try {
            return Database::all(
                "SELECT * FROM employee_attendance 
                 WHERE employee_id = :id 
                 AND date BETWEEN :start_date AND :end_date
                 ORDER BY date DESC",
                [
                    'id' => $employeeId,
                    'start_date' => $startDate,
                    'end_date' => $endDate
                ]
            );
        } catch (\Exception $e) {
            error_log("getEmployeeAttendance Error: " . $e->getMessage());
            return [];
        }
    }
}