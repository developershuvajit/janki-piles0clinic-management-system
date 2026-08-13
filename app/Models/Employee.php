<?php
declare(strict_types=1);

namespace App\Models;

use App\Helpers\Database;
use App\Helpers\Security;
use App\Helpers\Logger;
use App\Helpers\Session;

class Employee
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
     * Get all employees, optionally filtered by branch.
     */
    public static function all(?int $branchId = null): array
    {
        $db = Database::getInstance();
        $filter = self::getBranchFilter();
        $useBranchId = $branchId ?? $filter['branchId'];
        $hasFilter = ($branchId !== null) || $filter['hasFilter'];
        
        $sql = "SELECT e.*, u.username, u.email, u.status as user_status, 
                       r.name as role_name, r.slug as role_slug, 
                       b.name as branch_name 
                FROM employees e
                JOIN users u ON e.user_id = u.id
                LEFT JOIN roles r ON u.role_id = r.id
                LEFT JOIN branches b ON u.branch_id = b.id";
        
        $params = [];
        if ($hasFilter && $useBranchId) {
            $sql .= " WHERE u.branch_id = ?";
            $params[] = $useBranchId;
        }

        $sql .= " ORDER BY e.id DESC";
        return $db->getAll($sql, $params);
    }

    /**
     * Find employee details by employee ID with branch check.
     */
    public static function find(int $id): ?array
    {
        $db = Database::getInstance();
        $filter = self::getBranchFilter();
        
        $sql = "SELECT e.*, u.username, u.email, u.status as user_status, 
                       u.role_id, u.branch_id, 
                       r.name as role_name, r.slug as role_slug, 
                       b.name as branch_name 
                FROM employees e
                JOIN users u ON e.user_id = u.id
                LEFT JOIN roles r ON u.role_id = r.id
                LEFT JOIN branches b ON u.branch_id = b.id
                WHERE e.id = ?";
        $params = [$id];
        
        // Super Admin ছাড়া বাকি সবাই ব্রাঞ্চ ফিল্টার পাবে
        if ($filter['hasFilter']) {
            $sql .= " AND u.branch_id = ?";
            $params[] = $filter['branchId'];
        }
        
        $result = $db->getRow($sql, $params);
        
        if ($result) {
            $result['shift_start'] = $result['shift_start'] ?? '09:00:00';
            $result['shift_end'] = $result['shift_end'] ?? '17:00:00';
            $result['role_name'] = $result['role_name'] ?? 'Staff';
        }
        
        return $result;
    }

    /**
     * Find employee details by linked User ID with branch check.
     */
    public static function findByUserId(int $userId): ?array
    {
        $db = Database::getInstance();
        $filter = self::getBranchFilter();
        
        $sql = "SELECT e.*, u.username, u.email, u.status as user_status, 
                       u.role_id, u.branch_id, 
                       r.name as role_name, r.slug as role_slug, 
                       b.name as branch_name 
                FROM employees e
                JOIN users u ON e.user_id = u.id
                LEFT JOIN roles r ON u.role_id = r.id
                LEFT JOIN branches b ON u.branch_id = b.id
                WHERE e.user_id = ?";
        $params = [$userId];
        
        if ($filter['hasFilter']) {
            $sql .= " AND u.branch_id = ?";
            $params[] = $filter['branchId'];
        }
        
        return $db->getRow($sql, $params);
    }

    /**
     * Get multiple employees by their IDs (for ID card generation, etc.)
     */
    public static function getByIds(array $ids): array
    {
        if (empty($ids)) {
            return [];
        }
        
        $db = Database::getInstance();
        $filter = self::getBranchFilter();
        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        
        $sql = "SELECT e.*, u.username, u.email, u.status as user_status, 
                       r.name as role_name, r.slug as role_slug, 
                       b.name as branch_name 
                FROM employees e
                JOIN users u ON e.user_id = u.id
                LEFT JOIN roles r ON u.role_id = r.id
                LEFT JOIN branches b ON u.branch_id = b.id
                WHERE e.id IN ($placeholders)";
        $params = $ids;
        
        if ($filter['hasFilter']) {
            $sql .= " AND u.branch_id = ?";
            $params[] = $filter['branchId'];
        }
        
        $sql .= " ORDER BY e.id DESC";
        
        return $db->getAll($sql, $params);
    }

    /**
     * Atomic transaction to register a user account and construct employee profiles.
     */
    public static function create(array $data): ?int
    {
        $db = Database::getInstance();
        $filter = self::getBranchFilter();
        
        // Super Admin ছাড়া বাকি সবাই নিজের ব্রাঞ্চ ফোর্স সেট
        if ($filter['hasFilter']) {
            $data['branch_id'] = $filter['branchId'];
        }
        
        $db->beginTransaction();
        try {
            // 1. Create linked User credentials account
            $passwordHash = Security::hashPassword($data['password']);
            $userSql = "INSERT INTO users (username, email, password_hash, role_id, branch_id, status, created_at, updated_at) 
                        VALUES (?, ?, ?, ?, ?, 'active', NOW(), NOW())";
            
            $db->execute($userSql, [
                $data['username'],
                $data['email'],
                $passwordHash,
                $data['role_id'],
                $data['branch_id']
            ]);

            $userId = (int)$db->lastInsertId();

            // 2. Create the core Employee details record
            $empSql = "INSERT INTO employees (user_id, photo, salary, shift_start, shift_end, created_at, updated_at) 
                       VALUES (?, ?, ?, ?, ?, NOW(), NOW())";
            
            $db->execute($empSql, [
                $userId,
                $data['photo'] ?? null,
                $data['salary'],
                $data['shift_start'] ?? '09:00:00',
                $data['shift_end'] ?? '17:00:00'
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
        $db = Database::getInstance();
        $employee = self::find($id);
        if (!$employee) {
            return false;
        }

        $filter = self::getBranchFilter();
        
        // Super Admin ছাড়া বাকি সবাই ব্রাঞ্চ পরিবর্তন করতে পারবে না
        if ($filter['hasFilter']) {
            $data['branch_id'] = $filter['branchId'];
        }

        $db->beginTransaction();
        try {
            $userId = (int)$employee['user_id'];

            // 1. Update user fields
            $userSql = "UPDATE users SET 
                            username = ?, 
                            email = ?, 
                            role_id = ?, 
                            branch_id = ? 
                        WHERE id = ?";
            
            $db->execute($userSql, [
                $data['username'],
                $data['email'],
                $data['role_id'],
                $data['branch_id'],
                $userId
            ]);

            // Update user password if provided
            if (!empty($data['password'])) {
                $hash = Security::hashPassword($data['password']);
                $db->execute("UPDATE users SET password_hash = ? WHERE id = ?", [
                    $hash,
                    $userId
                ]);
            }

            // 2. Update employee records
            $empSql = "UPDATE employees SET 
                            photo = ?, 
                            salary = ?, 
                            shift_start = ?, 
                            shift_end = ?, 
                            updated_at = NOW() 
                       WHERE id = ?";
            
            $db->execute($empSql, [
                $data['photo'] ?? $employee['photo'],
                $data['salary'],
                $data['shift_start'] ?? $employee['shift_start'],
                $data['shift_end'] ?? $employee['shift_end'],
                $id
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
        $db = Database::getInstance();
        $employee = self::find($id);
        if (!$employee) {
            return false;
        }

        $db->beginTransaction();
        try {
            $userId = (int)$employee['user_id'];
            
            // Delete user record (foreign keys clean cascades automatic user employees and logs)
            $db->execute("DELETE FROM users WHERE id = ?", [$userId]);
            $db->execute("DELETE FROM employees WHERE id = ?", [$id]);
            
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
        $db = Database::getInstance();
        $sql = "INSERT INTO employee_documents (employee_id, document_name, file_path, uploaded_at) 
                VALUES (?, ?, ?, NOW())";
        return $db->execute($sql, [$employeeId, $docName, $filePath]);
    }

    /**
     * Retrieve document uploads for an employee.
     */
    public static function getDocuments(int $employeeId): array
    {
        $db = Database::getInstance();
        return $db->getAll("SELECT * FROM employee_documents WHERE employee_id = ? ORDER BY id DESC", [$employeeId]);
    }

    /**
     * Delete a credential document by ID.
     */
    public static function deleteDocument(int $docId): bool
    {
        $db = Database::getInstance();
        return $db->execute("DELETE FROM employee_documents WHERE id = ?", [$docId]);
    }

    /**
     * Retrieve document info by ID.
     */
    public static function getDocument(int $docId): ?array
    {
        $db = Database::getInstance();
        return $db->getRow("SELECT * FROM employee_documents WHERE id = ? LIMIT 1", [$docId]);
    }

    /**
     * Retrieve attendance records for all employees on a specific date.
     */
    public static function getAttendanceByDate(string $date): array
    {
        $db = Database::getInstance();
        $filter = self::getBranchFilter();
        
        $sql = "SELECT e.id as employee_id, u.username, r.name as role_name, b.name as branch_name, 
                       a.status, a.check_in_time, a.check_out_time
                FROM employees e
                JOIN users u ON e.user_id = u.id
                LEFT JOIN roles r ON u.role_id = r.id
                LEFT JOIN branches b ON u.branch_id = b.id
                LEFT JOIN attendance a ON e.id = a.employee_id AND a.date = ?";
        $params = [$date];
        
        if ($filter['hasFilter']) {
            $sql .= " WHERE u.branch_id = ?";
            $params[] = $filter['branchId'];
        }
        
        $sql .= " ORDER BY e.id ASC";
        return $db->getAll($sql, $params);
    }

    /**
     * Record check-in / check-out log sheets for attendance.
     */
    public static function recordAttendance(int $employeeId, string $date, string $status, ?string $checkIn, ?string $checkOut): bool
    {
        $db = Database::getInstance();
        $sql = "INSERT INTO attendance (employee_id, date, status, check_in_time, check_out_time, created_at) 
                VALUES (?, ?, ?, ?, ?, NOW())
                ON DUPLICATE KEY UPDATE 
                    status = ?, 
                    check_in_time = ?, 
                    check_out_time = ?";
        
        return $db->execute($sql, [
            $employeeId,
            $date,
            $status,
            !empty($checkIn) ? $checkIn : null,
            !empty($checkOut) ? $checkOut : null,
            $status,
            !empty($checkIn) ? $checkIn : null,
            !empty($checkOut) ? $checkOut : null
        ]);
    }
}