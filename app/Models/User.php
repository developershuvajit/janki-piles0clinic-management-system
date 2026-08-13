<?php
declare(strict_types=1);

namespace App\Models;

use App\Helpers\Database;
use App\Helpers\Security;

class User
{
    /**
     * Find a user profile by username with branch info
     */
    public static function findByUsername(string $username): ?array
    {
        return Database::row("
            SELECT u.*, 
                   r.slug as role_slug, 
                   r.name as role_name,
                   b.id as branch_id,
                   b.name as branch_name
            FROM users u
            LEFT JOIN roles r ON u.role_id = r.id
            LEFT JOIN branches b ON u.branch_id = b.id
            WHERE (u.username = :username OR u.email = :email) 
            AND u.status = 'active'
            LIMIT 1
        ", [
            'username' => $username,
            'email' => $username
        ]);
    }

    /**
     * Find a user by ID
     */
    public static function findById(int $id): ?array
    {
        return Database::row("
            SELECT u.*, 
                   r.slug as role_slug,
                   r.name as role_name,
                   b.name as branch_name
            FROM users u
            LEFT JOIN roles r ON u.role_id = r.id
            LEFT JOIN branches b ON u.branch_id = b.id
            WHERE u.id = :id
            LIMIT 1
        ", ['id' => $id]);
    }

    /**
     * Authenticate user
     */
    public static function verifyCredentials(string $username, string $password): ?array
    {
        $user = self::findByUsername($username);
        
        if ($user && $user['status'] === 'active') {
            if (Security::verifyPassword($password, $user['password_hash'])) {
                return $user;
            }
        }
        
        return null;
    }

    /**
     * Create a new user
     */
    public static function create(array $data): int|false
    {
        $passwordHash = Security::hashPassword($data['password']);
        
        $result = Database::execute("
            INSERT INTO users (
                username, email, password_hash, role_id, 
                branch_id, status, created_at
            ) VALUES (
                :username, :email, :password_hash, :role_id,
                :branch_id, :status, NOW()
            )
        ", [
            'username' => $data['username'],
            'email' => $data['email'],
            'password_hash' => $passwordHash,
            'role_id' => $data['role_id'],
            'branch_id' => $data['branch_id'] ?? null,
            'status' => $data['status'] ?? 'active'
        ]);
        
        return $result ? Database::lastInsertId() : false;
    }

    /**
     * Update user
     */
    public static function update(int $id, array $data): bool
    {
        $sets = [];
        $params = ['id' => $id];
        
        $allowedFields = ['username', 'email', 'role_id', 'branch_id', 'status'];
        
        foreach ($allowedFields as $field) {
            if (isset($data[$field])) {
                $sets[] = "{$field} = :{$field}";
                $params[$field] = $data[$field];
            }
        }
        
        if (isset($data['password']) && !empty($data['password'])) {
            $sets[] = "password_hash = :password_hash";
            $params['password_hash'] = Security::hashPassword($data['password']);
        }
        
        if (empty($sets)) {
            return false;
        }
        
        $sql = "UPDATE users SET " . implode(', ', $sets) . " WHERE id = :id";
        
        return Database::execute($sql, $params);
    }

    /**
     * Get all users with role and branch info
     */
    public static function getAll(): array
    {
        return Database::query("
            SELECT u.*, 
                   r.name as role_name,
                   r.slug as role_slug,
                   b.name as branch_name
            FROM users u
            LEFT JOIN roles r ON u.role_id = r.id
            LEFT JOIN branches b ON u.branch_id = b.id
            ORDER BY u.id DESC
        ")->getResult();
    }

    /**
     * Get users by branch
     */
    public static function getByBranch(int $branchId): array
    {
        return Database::query("
            SELECT u.*, 
                   r.name as role_name,
                   r.slug as role_slug
            FROM users u
            LEFT JOIN roles r ON u.role_id = r.id
            WHERE u.branch_id = :branch_id
            ORDER BY u.id DESC
        ", ['branch_id' => $branchId])->getResult();
    }

    /**
     * Delete user (soft delete - set status inactive)
     */
    public static function delete(int $id): bool
    {
        return Database::execute(
            "UPDATE users SET status = 'inactive' WHERE id = :id",
            ['id' => $id]
        );
    }

    /**
     * Update last login time
     */
    public static function updateLastLogin(int $userId): bool
    {
        return Database::execute(
            "UPDATE users SET last_login_at = NOW() WHERE id = :id",
            ['id' => $userId]
        );
    }
}