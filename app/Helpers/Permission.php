<?php
declare(strict_types=1);

namespace App\Helpers;

class Permission
{
    private static ?array $userPermissions = null;

    /**
     * Load permissions of the logged-in user context.
     */
    private static function loadUserPermissions(): void
    {
        if (self::$userPermissions !== null) {
            return;
        }

        self::$userPermissions = [];
        if (!Session::isLoggedIn()) {
            return;
        }

        $user = Session::user();
        $userId = (int)$user['id'];

        try {
            // Retrieve current database user role assignment
            $dbUser = Database::row("SELECT role_id, role FROM users WHERE id = :id", ['id' => $userId]);
            if (!$dbUser) {
                return;
            }

            // Super Administrator bypass check (role = admin or role_id = 1)
            if ($dbUser['role'] === 'admin' || (int)$dbUser['role_id'] === 1) {
                $allPerms = Database::all("SELECT slug FROM permissions");
                foreach ($allPerms as $p) {
                    self::$userPermissions[] = $p['slug'];
                }
                return;
            }

            // Retrieve role-associated permissions
            $roleId = (int)$dbUser['role_id'];
            $sql = "SELECT p.slug 
                    FROM permissions p 
                    JOIN role_permissions rp ON p.id = rp.permission_id 
                    WHERE rp.role_id = :role_id";
            
            $perms = Database::all($sql, ['role_id' => $roleId]);
            foreach ($perms as $p) {
                self::$userPermissions[] = $p['slug'];
            }
        } catch (\Throwable $e) {
            Logger::error("Failed to load user permissions context: " . $e->getMessage(), [
                'user_id' => $userId
            ]);
        }
    }

    /**
     * Check if the active user possesses a permission.
     */
    public static function has(string $permissionSlug): bool
    {
        if (!Session::isLoggedIn()) {
            return false;
        }
        self::loadUserPermissions();
        return in_array($permissionSlug, self::$userPermissions, true);
    }

    /**
     * Assert if the active user possesses a permission, blocking unauthorized execution.
     */
    public static function check(string $permissionSlug): void
    {
        if (!Session::isLoggedIn()) {
            Session::setFlash('error', 'Access restricted. Please log in first.');
            redirect('/login');
        }

        if (!self::has($permissionSlug)) {
            $deniedMsg = 'Access Denied: You do not possess the required permission level.';
            
            if (Request::isAjax()) {
                jsonResponse(['success' => false, 'message' => $deniedMsg], 403);
            }

            Session::setFlash('error', $deniedMsg);
            redirect('/admin/dashboard');
        }
    }

    /**
     * Enforce portal access isolation for admin, reception, and doctor portals.
     */
    public static function checkPortal(string $portal): void
    {
        if (!Session::isLoggedIn()) {
            Session::setFlash('error', 'Access restricted. Please log in first.');
            redirect('/login');
        }

        $user = Session::user();
        $role = $user['role_slug'] ?? $user['role'] ?? '';
        $roleId = (int)($user['role_id'] ?? 0);

        // Super Admin (admin / super_admin / role_id 1) has access to all portals
        if ($role === 'admin' || $role === 'super_admin' || $roleId === 1) {
            return;
        }

        if ($portal === 'reception' && ($role === 'receptionist' || $roleId === 3)) {
            return;
        }

        if ($portal === 'doctor' && ($role === 'doctor' || $roleId === 2)) {
            return;
        }

        $deniedMsg = 'Access Denied: You do not have authorization to access the ' . ucfirst($portal) . ' portal.';

        if (Request::isAjax()) {
            jsonResponse(['success' => false, 'message' => $deniedMsg], 403);
        }

        Session::setFlash('error', $deniedMsg);

        // Redirect user to their own portal
        if ($role === 'doctor' || $roleId === 2) {
            redirect('/doctor');
        } elseif ($role === 'receptionist' || $roleId === 3) {
            redirect('/reception');
        } else {
            redirect('/admin/dashboard');
        }
    }
}
