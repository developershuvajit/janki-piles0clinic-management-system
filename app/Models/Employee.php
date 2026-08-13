<?php
declare(strict_types=1);

namespace App\Models;

use App\Helpers\Database;
use App\Helpers\Security;
use App\Helpers\Logger;

class Employee
{
    private static ?Database $db = null;
    
    /**
     * Get Database instance
     */
    private static function getDb(): Database
    {
        if (self::$db === null) {
            self::$db = Database::getInstance(); // বা new Database()
        }
        return self::$db;
    }

    /**
     * Get all employees, optionally filtered by branch.
     */
     // app/Models/Employee.php - Fix the all() method

/**
 * Get all employees with optional branch filter
 */
  public static function all(?int $branchId = null): array
{
    $sql = "
        SELECT e.*, u.username, u.email, u.status as user_status,
               r.name as role_name, r.slug as role_slug,
               b.name as branch_name
        FROM employees e
        JOIN users u ON e.user_id = u.id
        LEFT JOIN roles r ON u.role_id = r.id
        LEFT JOIN branches b ON u.branch_id = b.id
        WHERE u.status = 'active'
    ";
    
    $params = [];
    
    if ($branchId !== null) {
        $sql .= " AND u.branch_id = ?";
        $params[] = $branchId;
    }
    
    $sql .= " ORDER BY u.username ASC";
    
    return Database::all($sql, $params);
}

/**
 * Get employee by ID
 */
// app/Models/Employee.php - Add this method if not exists
public static function find(int $id): ?array
{
    return Database::row("
        SELECT e.*, u.username, u.email, u.status as user_status,
               r.name as role_name, r.slug as role_slug,
               b.name as branch_name
        FROM employees e
        JOIN users u ON e.user_id = u.id
        LEFT JOIN roles r ON u.role_id = r.id
        LEFT JOIN branches b ON u.branch_id = b.id
        WHERE e.id = ? AND u.status = 'active'
    ", [$id]);
}

/**
 * Get employees by IDs with branch filter
 */
    // app/Models/Employee.php

/**
 * Get employees by IDs with branch filter
 */
public static function getByIds(array $ids, ?int $branchId = null): array
{
    if (empty($ids)) {
        return [];
    }
    
    // Filter out invalid IDs
    $validIds = array_filter($ids, function($id) {
        return is_numeric($id) && $id > 0;
    });
    
    if (empty($validIds)) {
        return [];
    }
    
    $placeholders = implode(',', array_fill(0, count($validIds), '?'));
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
    
    $params = $validIds;
    
    if ($branchId !== null) {
        $sql .= " AND u.branch_id = ?";
        $params[] = $branchId;
    }
    
    return Database::all($sql, $params);
}

    /**
     * Find employee details by linked User ID.
     */
    public static function findByUserId(int $userId): ?array
    {
        $sql = "SELECT e.*, u.username, u.email, u.status as user_status, u.role_id, u.branch_id, 
                       r.name as role_name, r.slug as role_slug, b.name as branch_name 
                FROM employees e
                JOIN users u ON e.user_id = u.id
                LEFT JOIN roles r ON u.role_id = r.id
                LEFT JOIN branches b ON u.branch_id = b.id
                WHERE e.user_id = :user_id LIMIT 1";
        
        return self::getDb()->row($sql, ['user_id' => $userId]);
    }

    /**
     * Get multiple employees by their IDs (for ID card generation, etc.)
     * 
     * @param array $ids Array of employee IDs
     * @return array
     */
     // app/Models/Employee.php - Add this method

/**
 * Get employees by multiple IDs with branch filter
 */
 

    /**
     * Atomic transaction to register a user account and construct employee profiles.
     */
    public static function create(array $data): ?int
    {
        $db = self::getDb();
        $db->beginTransaction();
        try {
            // 1. Create linked User credentials account
            $passwordHash = Security::hashPassword($data['password']);
            $userSql = "INSERT INTO users (username, email, password_hash, role_id, branch_id, status, created_at, updated_at) 
                        VALUES (:username, :email, :password_hash, :role_id, :branch_id, 'active', NOW(), NOW())";
            
            $db->execute($userSql, [
                'username' => $data['username'],
                'email' => $data['email'],
                'password_hash' => $passwordHash,
                'role_id' => $data['role_id'],
                'branch_id' => $data['branch_id']
            ]);

            $userId = (int)$db->lastInsertId();

            // 2. Create the core Employee details record
            $empSql = "INSERT INTO employees (user_id, photo, salary, shift_start, shift_end, created_at, updated_at) 
                       VALUES (:user_id, :photo, :salary, :shift_start, :shift_end, NOW(), NOW())";
            
            $db->execute($empSql, [
                'user_id' => $userId,
                'photo' => $data['photo'] ?? null,
                'salary' => $data['salary'],
                'shift_start' => $data['shift_start'] ?? '09:00:00',
                'shift_end' => $data['shift_end'] ?? '17:00:00'
            ]);

            $employeeId = (int)$db->lastInsertId();
            $db->commit();

            return $employeeId;
        } catch (\Throwable $e) {
            $db->rollBack();
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

        $db = self::getDb();
        $db->beginTransaction();
        try {
            $userId = (int)$employee['user_id'];

            // 1. Update user fields
            $userSql = "UPDATE users SET 
                            username = :username, 
                            email = :email, 
                            role_id = :role_id, 
                            branch_id = :branch_id 
                        WHERE id = :id";
            
            $db->execute($userSql, [
                'id' => $userId,
                'username' => $data['username'],
                'email' => $data['email'],
                'role_id' => $data['role_id'],
                'branch_id' => $data['branch_id']
            ]);

            // Update user password if provided
            if (!empty($data['password'])) {
                $hash = Security::hashPassword($data['password']);
                $db->execute("UPDATE users SET password_hash = :hash WHERE id = :id", [
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
            
            $db->execute($empSql, [
                'id' => $id,
                'photo' => $data['photo'] ?? $employee['photo'],
                'salary' => $data['salary'],
                'shift_start' => $data['shift_start'] ?? $employee['shift_start'],
                'shift_end' => $data['shift_end'] ?? $employee['shift_end']
            ]);

            $db->commit();
            return true;
        } catch (\Throwable $e) {
            $db->rollBack();
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

        $db = self::getDb();
        $db->beginTransaction();
        try {
            $userId = (int)$employee['user_id'];
            
            // Delete user record (foreign keys clean cascades automatic user employees and logs)
            $db->execute("DELETE FROM users WHERE id = :id", ['id' => $userId]);
            $db->execute("DELETE FROM employees WHERE id = :id", ['id' => $id]);
            
            $db->commit();
            return true;
        } catch (\Throwable $e) {
            $db->rollBack();
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
        return self::getDb()->execute($sql, [
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
        return self::getDb()->all("SELECT * FROM employee_documents WHERE employee_id = :id ORDER BY id DESC", ['id' => $employeeId]);
    }

    /**
     * Delete a credential document by ID.
     */
    public static function deleteDocument(int $docId): bool
    {
        return self::getDb()->execute("DELETE FROM employee_documents WHERE id = :id", ['id' => $docId]);
    }

    /**
     * Retrieve document info by ID.
     */
    public static function getDocument(int $docId): ?array
    {
        return self::getDb()->row("SELECT * FROM employee_documents WHERE id = :id LIMIT 1", ['id' => $docId]);
    }

    /**
     * Retrieve attendance records for all employees on a specific date.
     */
     public static function getAttendanceByDate(string $date, ?int $branchId = null): array
{
    $sql = "
        SELECT a.*, e.*, u.username, u.email,
               r.name as role_name
        FROM employee_attendance a
        LEFT JOIN employees e ON a.employee_id = e.id
        LEFT JOIN users u ON e.user_id = u.id
        LEFT JOIN roles r ON u.role_id = r.id
        WHERE a.date = ?
    ";
    
    $params = [$date];
    
    if ($branchId !== null) {
        $sql .= " AND u.branch_id = ?";
        $params[] = $branchId;
    }
    
    return Database::all($sql, $params);
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
        
        return self::getDb()->execute($sql, [
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