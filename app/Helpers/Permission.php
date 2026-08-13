<?php
declare(strict_types=1);

namespace App\Helpers;

class Permission
{
    private static ?array $userPermissions = null;
    private static ?bool $isSuperAdminCache = null;

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
        $roleSlug = $user['role_slug'] ?? '';

        try {
            // Retrieve current database user role assignment
            $dbUser = Database::row("SELECT role_id, role FROM users WHERE id = :id", ['id' => $userId]);
            if (!$dbUser) {
                return;
            }

            // ** FIX: Super Administrator - Give ALL permissions **
            if ($dbUser['role'] === 'admin' || $dbUser['role'] === 'super_admin' || (int)$dbUser['role_id'] === 1) {
                self::$isSuperAdminCache = true;
                
                // Try to get permissions from database
                $allPerms = Database::all("SELECT slug FROM permissions");
                
                // If permissions table is empty, use default all permissions
                if (empty($allPerms)) {
                    self::$userPermissions = [
                        // Dashboard
                        'view_dashboard', 'view_reception_dashboard', 'view_doctor_dashboard', 'view_branch_dashboard',
                        // Patients
                        'view_patients', 'manage_patients', 'manage_branch_patients',
                        // Appointments
                        'view_appointments', 'manage_appointments', 'manage_branch_appointments',
                        // HR
                        'view_employees', 'manage_employees', 'record_attendance', 'manage_salary',
                        // System
                        'manage_branches', 'manage_settings', 'view_logs',
                        // Billing & Inventory
                        'manage_billing', 'manage_inventory',
                        // IPD
                        'view_ipd', 'manage_ipd', 'manage_ipd_admission',
                        // CMS
                        'manage_cms',
                        // Reports
                        'view_reports',
                        // Doctor
                        'manage_prescriptions', 'view_consultations', 'manage_discharge',
                        // Reception
                        'manage_walk_in', 'manage_medicine_issue',
                        // Branch
                        'manage_branch_settings'
                    ];
                    return;
                }
                
                foreach ($allPerms as $p) {
                    self::$userPermissions[] = $p['slug'];
                }
                return;
            }

            // Branch Admin - assign branch-specific permissions
            if ($roleSlug === 'branch_admin' || (int)$dbUser['role_id'] === 4) {
                self::$isSuperAdminCache = false;
                $branchPermissions = [
                    'view_dashboard',
                    'view_patients',
                    'manage_patients',
                    'view_appointments',
                    'manage_appointments',
                    'view_employees',
                    'view_reports',
                    'manage_branch_settings',
                    'view_branch_dashboard',
                    'manage_branch_patients',
                    'manage_branch_appointments'
                ];
                self::$userPermissions = $branchPermissions;
                return;
            }

            // Doctor - assign doctor permissions
            if ($roleSlug === 'doctor' || (int)$dbUser['role_id'] === 2) {
                self::$isSuperAdminCache = false;
                $doctorPermissions = [
                    'view_dashboard',
                    'view_patients',
                    'view_appointments',
                    'manage_prescriptions',
                    'view_consultations',
                    'view_ipd',
                    'manage_discharge',
                    'view_doctor_dashboard'
                ];
                self::$userPermissions = $doctorPermissions;
                return;
            }

            // Receptionist - assign reception permissions
            if ($roleSlug === 'receptionist' || (int)$dbUser['role_id'] === 3) {
                self::$isSuperAdminCache = false;
                $receptionPermissions = [
                    'view_dashboard',
                    'view_patients',
                    'manage_patients',
                    'view_appointments',
                    'manage_appointments',
                    'manage_billing',
                    'view_ipd',
                    'manage_ipd_admission',
                    'manage_walk_in',
                    'manage_medicine_issue',
                    'view_reports',
                    'view_reception_dashboard'
                ];
                self::$userPermissions = $receptionPermissions;
                return;
            }

            // Retrieve role-associated permissions from database
            $roleId = (int)$dbUser['role_id'];
            if ($roleId > 0) {
                $sql = "SELECT p.slug 
                        FROM permissions p 
                        JOIN role_permissions rp ON p.id = rp.permission_id 
                        WHERE rp.role_id = :role_id";
                
                $perms = Database::all($sql, ['role_id' => $roleId]);
                foreach ($perms as $p) {
                    self::$userPermissions[] = $p['slug'];
                }
            }
        } catch (\Throwable $e) {
            Logger::error("Failed to load user permissions context: " . $e->getMessage(), [
                'user_id' => $userId
            ]);
        }
    }

    /**
     * Check if the active user possesses a permission.
     * For branch-specific permissions, you can pass branch_id to check branch access.
     */
    public static function has(string $permissionSlug, ?int $branchId = null): bool
    {
        if (!Session::isLoggedIn()) {
            return false;
        }

        // ** FIX: Super Admin has ALL permissions - no need to check **
        if (self::isSuperAdmin()) {
            return true;
        }

        // Check if user is branch admin and trying to access other branch
        $user = Session::user();
        $roleSlug = $user['role_slug'] ?? '';
        
        if ($roleSlug === 'branch_admin' && $branchId !== null) {
            $userBranchId = Session::getBranchId();
            if ((int)$branchId !== (int)$userBranchId) {
                return false; // Can't access other branches
            }
        }

        self::loadUserPermissions();
        return in_array($permissionSlug, self::$userPermissions, true);
    }

    /**
     * Check if user has any of the given permissions.
     */
    public static function hasAny(array $permissionSlugs, ?int $branchId = null): bool
    {
        // ** FIX: Super Admin has ALL permissions **
        if (self::isSuperAdmin()) {
            return true;
        }

        foreach ($permissionSlugs as $perm) {
            if (self::has($perm, $branchId)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Check if user has all of the given permissions.
     */
    public static function hasAll(array $permissionSlugs, ?int $branchId = null): bool
    {
        // ** FIX: Super Admin has ALL permissions **
        if (self::isSuperAdmin()) {
            return true;
        }

        foreach ($permissionSlugs as $perm) {
            if (!self::has($perm, $branchId)) {
                return false;
            }
        }
        return true;
    }

    /**
     * Assert if the active user possesses a permission, blocking unauthorized execution.
     */
    public static function check(string $permissionSlug, ?int $branchId = null): void
    {
        if (!Session::isLoggedIn()) {
            Session::setFlash('error', 'Access restricted. Please log in first.');
            redirect('/login');
        }

        // ** FIX: Super Admin bypass - no need to check **
        if (self::isSuperAdmin()) {
            return;
        }

        if (!self::has($permissionSlug, $branchId)) {
            $isAjax = (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') 
                || ($_POST['ajax'] ?? '') === '1';

            $deniedMsg = 'Access Denied: You do not possess the required permission level.';
            
            if ($isAjax) {
                jsonResponse(['success' => false, 'message' => $deniedMsg], 403);
            }

            Session::setFlash('error', $deniedMsg);
            
            // Redirect based on role
            $user = Session::user();
            $role = $user['role_slug'] ?? '';
            if ($role === 'doctor') {
                redirect('/doctor');
            } elseif ($role === 'receptionist') {
                redirect('/reception');
            } elseif ($role === 'branch_admin') {
                $branchId = Session::getBranchId();
                redirect("/branch/dashboard/{$branchId}");
            } else {
                redirect('/admin/dashboard');
            }
        }
    }

    /**
     * Enforce portal access isolation for admin, reception, doctor, and branch portals.
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

        // ** FIX: Super Admin has access to all portals **
        if (self::isSuperAdmin()) {
            return;
        }

        // Branch Admin - can access branch portal
        if ($portal === 'branch' && ($role === 'branch_admin' || $roleId === 4)) {
            return;
        }

        // Reception portal
        if ($portal === 'reception' && ($role === 'receptionist' || $roleId === 3)) {
            return;
        }

        // Doctor portal
        if ($portal === 'doctor' && ($role === 'doctor' || $roleId === 2)) {
            return;
        }

        $isAjax = (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') 
            || ($_POST['ajax'] ?? '') === '1';

        $deniedMsg = 'Access Denied: You do not have authorization to access the ' . ucfirst($portal) . ' portal.';

        if ($isAjax) {
            jsonResponse(['success' => false, 'message' => $deniedMsg], 403);
        }

        Session::setFlash('error', $deniedMsg);

        // Redirect user to their own portal
        if ($role === 'doctor' || $roleId === 2) {
            redirect('/doctor');
        } elseif ($role === 'receptionist' || $roleId === 3) {
            redirect('/reception');
        } elseif ($role === 'branch_admin' || $roleId === 4) {
            $branchId = Session::getBranchId();
            redirect("/branch/dashboard/{$branchId}");
        } else {
            redirect('/admin/dashboard');
        }
    }

    /**
     * Get all permissions for the current user.
     */
    public static function getAll(): array
    {
        self::loadUserPermissions();
        return self::$userPermissions ?? [];
    }

    /**
     * Check if user is super admin.
     */
    public static function isSuperAdmin(): bool
    {
        if (!Session::isLoggedIn()) {
            return false;
        }
        
        // Use cache for performance
        if (self::$isSuperAdminCache !== null) {
            return self::$isSuperAdminCache;
        }
        
        $user = Session::user();
        $role = $user['role_slug'] ?? $user['role'] ?? '';
        $roleId = (int)($user['role_id'] ?? 0);
        
        // Also check database directly if needed
        if ($role === 'admin' || $role === 'super_admin' || $roleId === 1) {
            self::$isSuperAdminCache = true;
            return true;
        }
        
        // Double check from database
        try {
            $userId = (int)$user['id'];
            $dbUser = Database::row("SELECT role_id, role FROM users WHERE id = :id", ['id' => $userId]);
            if ($dbUser && ($dbUser['role'] === 'admin' || $dbUser['role'] === 'super_admin' || (int)$dbUser['role_id'] === 1)) {
                self::$isSuperAdminCache = true;
                return true;
            }
        } catch (\Throwable $e) {
            // Fail silently
        }
        
        self::$isSuperAdminCache = false;
        return false;
    }

    /**
     * Check if user is branch admin.
     */
    public static function isBranchAdmin(): bool
    {
        if (!Session::isLoggedIn()) {
            return false;
        }
        $user = Session::user();
        $role = $user['role_slug'] ?? $user['role'] ?? '';
        $roleId = (int)($user['role_id'] ?? 0);
        
        return ($role === 'branch_admin' || $roleId === 4);
    }

    /**
     * Check if user is doctor.
     */
    public static function isDoctor(): bool
    {
        if (!Session::isLoggedIn()) {
            return false;
        }
        $user = Session::user();
        $role = $user['role_slug'] ?? $user['role'] ?? '';
        $roleId = (int)($user['role_id'] ?? 0);
        
        return ($role === 'doctor' || $roleId === 2);
    }

    /**
     * Check if user is receptionist.
     */
    public static function isReceptionist(): bool
    {
        if (!Session::isLoggedIn()) {
            return false;
        }
        $user = Session::user();
        $role = $user['role_slug'] ?? $user['role'] ?? '';
        $roleId = (int)($user['role_id'] ?? 0);
        
        return ($role === 'receptionist' || $roleId === 3);
    }

    /**
     * Get user's role slug.
     */
    public static function getRole(): string
    {
        if (!Session::isLoggedIn()) {
            return '';
        }
        $user = Session::user();
        return $user['role_slug'] ?? $user['role'] ?? '';
    }

    /**
     * Get user's role ID.
     */
    public static function getRoleId(): int
    {
        if (!Session::isLoggedIn()) {
            return 0;
        }
        $user = Session::user();
        return (int)($user['role_id'] ?? 0);
    }
}