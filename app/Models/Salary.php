<?php

namespace App\Models;

use App\Helpers\Database;
use App\Helpers\Logger;

class Salary
{
    /**
     * Retrieve salary records for a specific month (e.g. '07-2026').
     */
    public static function getSalariesForMonth(string $monthYear, ?int $branchId = null): array
    {
        $sql = "SELECT s.*, u.username as employee_name, r.name as role_name, e.salary as master_salary
                FROM employee_salaries s
                JOIN employees e ON s.employee_id = e.id
                JOIN users u ON e.user_id = u.id
                JOIN roles r ON u.role_id = r.id
                WHERE s.month_year = :month_year";
        
        $params = ['month_year' => $monthYear];
        $sql = Database::scopeToBranch($sql, $params, $branchId, 'u.branch_id');

        $sql .= " ORDER BY u.username ASC";
        return Database::all($sql, $params);
    }

    /**
     * Generate baseline payroll records for all active employees for a month if not already generated.
     */
    public static function generatePayroll(string $monthYear, ?int $branchId = null): bool
    {
        Database::beginTransaction();
        try {
            // Find all active employees not processed for this month
            $sql = "SELECT e.id, e.salary 
                    FROM employees e
                    JOIN users u ON e.user_id = u.id
                    WHERE u.status = 'active'
                      AND e.id NOT IN (SELECT employee_id FROM employee_salaries WHERE month_year = :month_year)";
            
            $params = ['month_year' => $monthYear];
            $sql = Database::scopeToBranch($sql, $params, $branchId, 'u.branch_id');

            $employees = Database::all($sql, $params);
            
            $insertSql = "INSERT INTO employee_salaries (employee_id, month_year, base_salary, advance, bonus, deduction, net_salary, payment_status) 
                          VALUES (:emp_id, :month_year, :base1, 0.00, 0.00, 0.00, :base2, 'unpaid')";
            
            foreach ($employees as $emp) {
                Database::execute($insertSql, [
                    'emp_id' => $emp['id'],
                    'month_year' => $monthYear,
                    'base1' => $emp['salary'],
                    'base2' => $emp['salary']
                ]);
            }

            Database::commit();
            return true;
        } catch (\Throwable $e) {
            Database::rollBack();
            Logger::error("Failed generating monthly payroll: " . $e->getMessage());
            echo "   [generatePayroll EXCEPTION] " . $e->getMessage() . "\n";
            return false;
        }
    }

    /**
     * Find specific salary record by ID.
     */
    public static function getSalary(int $id): ?array
    {
        $sql = "SELECT s.*, u.username as employee_name, r.name as role_name, b.name as branch_name 
                FROM employee_salaries s
                JOIN employees e ON s.employee_id = e.id
                JOIN users u ON e.user_id = u.id
                JOIN roles r ON u.role_id = r.id
                JOIN branches b ON u.branch_id = b.id
                WHERE s.id = :id LIMIT 1";
        return Database::row($sql, ['id' => $id]);
    }

    /**
     * Update adjustments and settle payment for a salary voucher.
     */
    public static function settlePayroll(int $id, array $adjustments): bool
    {
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
                SET advance = :adv, bonus = :bon, deduction = :ded, net_salary = :net, 
                    payment_status = 'paid', payment_date = :pay_date 
                WHERE id = :id";
        
        return Database::execute($sql, [
            'adv' => $advance,
            'bon' => $bonus,
            'ded' => $deduction,
            'net' => $net,
            'pay_date' => date('Y-m-d'),
            'id' => $id
        ]);
    }
}
