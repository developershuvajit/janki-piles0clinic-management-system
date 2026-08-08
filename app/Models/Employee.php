<?php
declare(strict_types=1);

namespace App\Models;

use App\Helpers\Database;
use App\Helpers\Security;
use App\Helpers\Logger;

class Employee
{
    /**
     * Get all employees, optionally filtered by branch.
     */
    public static function all(?int $branchId = null): array
    {
        $sql = "SELECT e.*, u.username, u.email, u.status as user_status, r.name as role_name, r.slug as role_slug, b.name as branch_name 
                FROM employees e
                JOIN users u ON e.user_id = u.id
                LEFT JOIN roles r ON u.role_id = r.id
                LEFT JOIN branches b ON u.branch_id = b.id";
        
        $params = [];
        if ($branchId !== null) {
            $sql .= " WHERE u.branch_id = :branch_id";
            $params['branch_id'] = $branchId;
        }

        $sql .= " ORDER BY e.id DESC";
        return Database::all($sql, $params);
    }

    /**
     * Find employee details by employee ID.
     */
    public static function find(int $id): ?array
    {
        $sql = "SELECT e.*, u.username, u.email, u.status as user_status, u.role_id, u.branch_id, r.name as role_name, r.slug as role_slug, b.name as branch_name 
                FROM employees e
                JOIN users u ON e.user_id = u.id
                LEFT JOIN roles r ON u.role_id = r.id
                LEFT JOIN branches b ON u.branch_id = b.id
                WHERE e.id = :id LIMIT 1";
        
        return Database::row($sql, ['id' => $id]);
    }

    /**
     * Find employee details by linked User ID.
     */
    public static function findByUserId(int $userId): ?array
    {
        $sql = "SELECT e.*, u.username, u.email, u.status as user_status, u.role_id, u.branch_id, r.name as role_name, r.slug as role_slug, b.name as branch_name 
                FROM employees e
                JOIN users u ON e.user_id = u.id
                LEFT JOIN roles r ON u.role_id = r.id
                LEFT JOIN branches b ON u.branch_id = b.id
                WHERE e.user_id = :user_id LIMIT 1";
        
        return Database::row($sql, ['user_id' => $userId]);
    }

    /**
     * Atomic transaction to register a user account and construct employee profiles.
     */
    public static function create(array $data): ?int
    {
        Database::beginTransaction();
        try {
            // 1. Create linked User credentials account
            $passwordHash = Security::hashPassword($data['password']);
            $userSql = "INSERT INTO users (username, email, password_hash, role_id, branch_id, status, created_at, updated_at) 
                        VALUES (:username, :email, :password_hash, :role_id, :branch_id, 'active', NOW(), NOW())";
            
            Database::execute($userSql, [
                'username' => $data['username'],
                'email' => $data['email'],
                'password_hash' => $passwordHash,
                'role_id' => $data['role_id'],
                'branch_id' => $data['branch_id']
            ]);

            $userId = (int)Database::lastInsertId();

            // 2. Create the core Employee details record
            $empSql = "INSERT INTO employees (user_id, photo, salary, shift_start, shift_end, created_at, updated_at) 
                       VALUES (:user_id, :photo, :salary, :shift_start, :shift_end, NOW(), NOW())";
            
            Database::execute($empSql, [
                'user_id' => $userId,
                'photo' => $data['photo'] ?? null,
                'salary' => $data['salary'],
                'shift_start' => $data['shift_start'] ?? '09:00:00',
                'shift_end' => $data['shift_end'] ?? '17:00:00'
            ]);

            $employeeId = (int)Database::lastInsertId();
            Database::commit();

            return $employeeId;
        } catch (\Throwable $e) {
            Database::rollBack();
            Logger::error("Failed to commit employee creation transaction: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Atomic transaction to update employee and user credentials.
     */
    public static function update(int $id, array $data): bool
    {
        $employee = self::find($id);
        if (!$employee) {
            return false;
        }

        Database::beginTransaction();
        try {
            $userId = (int)$employee['user_id'];

            // 1. Update user fields
            $userSql = "UPDATE users SET 
                            username = :username, 
                            email = :email, 
                            role_id = :role_id, 
                            branch_id = :branch_id 
                        WHERE id = :id";
            
            Database::execute($userSql, [
                'id' => $userId,
                'username' => $data['username'],
                'email' => $data['email'],
                'role_id' => $data['role_id'],
                'branch_id' => $data['branch_id']
            ]);

            // Update user password if provided
            if (!empty($data['password'])) {
                $hash = Security::hashPassword($data['password']);
                Database::execute("UPDATE users SET password_hash = :hash WHERE id = :id", [
                    'hash' => $hash,
                    'id' => $userId
                ]);
            }

            // 2. Update employee records
            $empSql = "UPDATE employees SET 
                            photo = :photo, 
                            salary = :salary, 
                            shift_start = :shift_start, 
                            shift_end = :shift_end, 
                            updated_at = NOW() 
                       WHERE id = :id";
            
            Database::execute($empSql, [
                'id' => $id,
                'photo' => $data['photo'] ?? $employee['photo'],
                'salary' => $data['salary'],
                'shift_start' => $data['shift_start'] ?? $employee['shift_start'],
                'shift_end' => $data['shift_end'] ?? $employee['shift_end']
            ]);

            Database::commit();
            return true;
        } catch (\Throwable $e) {
            Database::rollBack();
            Logger::error("Failed to commit employee update transaction: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Delete an employee.
     */
    public static function delete(int $id): bool
    {
        $employee = self::find($id);
        if (!$employee) {
            return false;
        }

        Database::beginTransaction();
        try {
            $userId = (int)$employee['user_id'];
            
            // Delete user record (foreign keys clean cascades automatic user employees and logs)
            Database::execute("DELETE FROM users WHERE id = :id", ['id' => $userId]);
            Database::execute("DELETE FROM employees WHERE id = :id", ['id' => $id]);
            
            Database::commit();
            return true;
        } catch (\Throwable $e) {
            Database::rollBack();
            Logger::error("Failed to delete employee: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Bind credential document uploads to an employee.
     */
    public static function addDocument(int $employeeId, string $docName, string $filePath): bool
    {
        $sql = "INSERT INTO employee_documents (employee_id, document_name, file_path, uploaded_at) 
                VALUES (:employee_id, :document_name, :file_path, NOW())";
        return Database::execute($sql, [
            'employee_id' => $employeeId,
            'document_name' => $docName,
            'file_path' => $filePath
        ]);
    }

    /**
     * Retrieve document uploads for an employee.
     */
    public static function getDocuments(int $employeeId): array
    {
        return Database::all("SELECT * FROM employee_documents WHERE employee_id = :id ORDER BY id DESC", ['id' => $employeeId]);
    }

    /**
     * Delete a credential document by ID.
     */
    public static function deleteDocument(int $docId): bool
    {
        return Database::execute("DELETE FROM employee_documents WHERE id = :id", ['id' => $docId]);
    }

    /**
     * Retrieve document info by ID.
     */
    public static function getDocument(int $docId): ?array
    {
        return Database::row("SELECT * FROM employee_documents WHERE id = :id LIMIT 1", ['id' => $docId]);
    }

    /**
     * Retrieve attendance records for all employees on a specific date.
     */
    public static function getAttendanceByDate(string $date): array
    {
        $sql = "SELECT e.id as employee_id, u.username, r.name as role_name, b.name as branch_name, a.status, a.check_in_time, a.check_out_time
                FROM employees e
                JOIN users u ON e.user_id = u.id
                LEFT JOIN roles r ON u.role_id = r.id
                LEFT JOIN branches b ON u.branch_id = b.id
                LEFT JOIN attendance a ON e.id = a.employee_id AND a.date = :date
                ORDER BY e.id ASC";
        return Database::all($sql, ['date' => $date]);
    }

    /**
     * Record check-in / check-out log sheets for attendance.
     */
    public static function recordAttendance(int $employeeId, string $date, string $status, ?string $checkIn, ?string $checkOut): bool
    {
        $sql = "INSERT INTO attendance (employee_id, date, status, check_in_time, check_out_time, created_at) 
                VALUES (:employee_id, :date, :status, :check_in, :check_out, NOW())
                ON DUPLICATE KEY UPDATE 
                    status = :status_update, 
                    check_in_time = :check_in_update, 
                    check_out_time = :check_out_update";
        
        return Database::execute($sql, [
            'employee_id' => $employeeId,
            'date' => $date,
            'status' => $status,
            'check_in' => !empty($checkIn) ? $checkIn : null,
            'check_out' => !empty($checkOut) ? $checkOut : null,
            'status_update' => $status,
            'check_in_update' => !empty($checkIn) ? $checkIn : null,
            'check_out_update' => !empty($checkOut) ? $checkOut : null
        ]);
    }
}
