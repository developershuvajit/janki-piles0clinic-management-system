<?php
declare(strict_types=1);

namespace App\Models;

use App\Helpers\Database;
use App\Helpers\Logger;
use App\Helpers\Session;

class Salary
{
    /**
     * Get branch filter for current user
     * Super Admin ছাড়া সবাই ব্রাঞ্চ ফিল্টার পাবে
     */
    private static function getBranchFilter(): array
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

    /**
     * Retrieve salary records for a specific month (e.g. '07-2026').
     */
    public static function getSalariesForMonth(string $monthYear, ?int $branchId = null): array
    {
        $db = Database::getInstance();
        $filter = self::getBranchFilter();
        $useBranchId = $branchId ?? $filter['branchId'];
        $hasFilter = ($branchId !== null) || $filter['hasFilter'];
        
        $sql = "SELECT s.*, u.username as employee_name, r.name as role_name, e.salary as master_salary,
                       b.name as branch_name
                FROM employee_salaries s
                JOIN employees e ON s.employee_id = e.id
                JOIN users u ON e.user_id = u.id
                JOIN roles r ON u.role_id = r.id
                LEFT JOIN branches b ON u.branch_id = b.id
                WHERE s.month_year = ?";
        
        $params = [$monthYear];
        if ($hasFilter && $useBranchId) {
            $sql .= " AND u.branch_id = ?";
            $params[] = $useBranchId;
        }

        $sql .= " ORDER BY u.username ASC";
        return $db->getAll($sql, $params);
    }

    /**
     * Generate baseline payroll records for all active employees for a month if not already generated.
     */
    public static function generatePayroll(string $monthYear, ?int $branchId = null): bool
    {
        $db = Database::getInstance();
        $filter = self::getBranchFilter();
        $useBranchId = $branchId ?? $filter['branchId'];
        $hasFilter = ($branchId !== null) || $filter['hasFilter'];
        
        $db->beginTransaction();
        try {
            // Find all active employees not processed for this month
            $sql = "SELECT e.id, e.salary 
                    FROM employees e
                    JOIN users u ON e.user_id = u.id
                    WHERE u.status = 'active'
                      AND e.id NOT IN (SELECT employee_id FROM employee_salaries WHERE month_year = ?)";
            
            $params = [$monthYear];
            if ($hasFilter && $useBranchId) {
                $sql .= " AND u.branch_id = ?";
                $params[] = $useBranchId;
            }

            $employees = $db->getAll($sql, $params);
            
            $insertSql = "INSERT INTO employee_salaries (employee_id, month_year, base_salary, advance, bonus, deduction, net_salary, payment_status) 
                          VALUES (?, ?, ?, 0.00, 0.00, 0.00, ?, 'unpaid')";
            
            foreach ($employees as $emp) {
                $db->execute($insertSql, [
                    $emp['id'],
                    $monthYear,
                    $emp['salary'],
                    $emp['salary']
                ]);
            }

            $db->commit();
            return true;
        } catch (\Throwable $e) {
            $db->rollBack();
            Logger::error("Failed generating monthly payroll: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Find specific salary record by ID.
     */
    public static function getSalary(int $id): ?array
    {
        $db = Database::getInstance();
        $sql = "SELECT s.*, u.username as employee_name, r.name as role_name, b.name as branch_name 
                FROM employee_salaries s
                JOIN employees e ON s.employee_id = e.id
                JOIN users u ON e.user_id = u.id
                JOIN roles r ON u.role_id = r.id
                LEFT JOIN branches b ON u.branch_id = b.id
                WHERE s.id = ? LIMIT 1";
        return $db->getRow($sql, [$id]);
    }

    /**
     * Update adjustments and settle payment for a salary voucher.
     */
    public static function settlePayroll(int $id, array $adjustments): bool
    {
        $db = Database::getInstance();
        $salary = self::getSalary($id);
        if (!$salary || $salary['payment_status'] === 'paid') {
            return false;
        }

        $base = (float)$salary['base_salary'];
        $advance = (float)($adjustments['advance'] ?? 0.00);
        $bonus = (float)($adjustments['bonus'] ?? 0.00);
        $deduction = (float)($adjustments['deduction'] ?? 0.00);
        
        $net = $base + $bonus - $advance - $deduction;
        if ($net < 0.00) {
            $net = 0.00;
        }

        $sql = "UPDATE employee_salaries 
                SET advance = ?, bonus = ?, deduction = ?, net_salary = ?, 
                    payment_status = 'paid', payment_date = ? 
                WHERE id = ?";
        
        return $db->execute($sql, [
            $advance,
            $bonus,
            $deduction,
            $net,
            date('Y-m-d'),
            $id
        ]);
    }

    /**
     * Get salary summary for a month
     */
    public static function getSummary(string $monthYear, ?int $branchId = null): array
    {
        $db = Database::getInstance();
        $filter = self::getBranchFilter();
        $useBranchId = $branchId ?? $filter['branchId'];
        $hasFilter = ($branchId !== null) || $filter['hasFilter'];
        
        $sql = "SELECT 
                    COUNT(*) as total_employees,
                    SUM(base_salary) as total_base_salary,
                    SUM(advance) as total_advance,
                    SUM(bonus) as total_bonus,
                    SUM(deduction) as total_deduction,
                    SUM(net_salary) as total_net_salary,
                    COUNT(CASE WHEN payment_status = 'paid' THEN 1 END) as paid_count,
                    COUNT(CASE WHEN payment_status = 'unpaid' THEN 1 END) as unpaid_count
                FROM employee_salaries s
                JOIN employees e ON s.employee_id = e.id
                JOIN users u ON e.user_id = u.id
                WHERE s.month_year = ?";
        
        $params = [$monthYear];
        if ($hasFilter && $useBranchId) {
            $sql .= " AND u.branch_id = ?";
            $params[] = $useBranchId;
        }
        
        $result = $db->getRow($sql, $params);
        return $result ?? [
            'total_employees' => 0,
            'total_base_salary' => 0,
            'total_advance' => 0,
            'total_bonus' => 0,
            'total_deduction' => 0,
            'total_net_salary' => 0,
            'paid_count' => 0,
            'unpaid_count' => 0
        ];
    }

    /**
     * Get employee salary history
     */
    public static function getEmployeeHistory(int $employeeId): array
    {
        $db = Database::getInstance();
        $sql = "SELECT * FROM employee_salaries 
                WHERE employee_id = ? 
                ORDER BY month_year DESC 
                LIMIT 12";
        return $db->getAll($sql, [$employeeId]);
    }

    /**
     * Check if payroll already generated for a month
     */
    public static function isPayrollGenerated(string $monthYear, ?int $branchId = null): bool
    {
        $db = Database::getInstance();
        $filter = self::getBranchFilter();
        $useBranchId = $branchId ?? $filter['branchId'];
        $hasFilter = ($branchId !== null) || $filter['hasFilter'];
        
        $sql = "SELECT COUNT(*) as count 
                FROM employee_salaries s
                JOIN employees e ON s.employee_id = e.id
                JOIN users u ON e.user_id = u.id
                WHERE s.month_year = ?";
        
        $params = [$monthYear];
        if ($hasFilter && $useBranchId) {
            $sql .= " AND u.branch_id = ?";
            $params[] = $useBranchId;
        }
        
        $result = $db->getOne($sql, $params);
        return ($result ?? 0) > 0;
    }
}