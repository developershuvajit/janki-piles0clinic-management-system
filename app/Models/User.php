<?php
declare(strict_types=1);

namespace App\Models;

use App\Helpers\Database;
use App\Helpers\Security;

class User
{
    /**
     * Find a user profile by username.
     */
    public static function findByUsername(string $username): ?array
    {
        return Database::row("SELECT u.*, r.slug as role_slug, r.name as role_name FROM users u LEFT JOIN roles r ON u.role_id = r.id WHERE u.username = :username OR u.email = :email LIMIT 1", [
            'username' => $username,
            'email' => $username
        ]);
    }

    /**
     * Find a user profile by ID.
     */
    public static function findById(int $id): ?array
    {
        return Database::row("SELECT * FROM users WHERE id = :id LIMIT 1", [
            'id' => $id
        ]);
    }

    /**
     * Authenticate a user by username and password.
     * Returns user record if valid, null otherwise.
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
     * Update a user's password.
     */
    public static function updatePassword(int $userId, string $newPassword): bool
    {
        $hash = Security::hashPassword($newPassword);
        return Database::execute("UPDATE users SET password_hash = :hash WHERE id = :id", [
            'hash' => $hash,
            'id' => $userId
        ]);
    }
}
