<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Helpers\Session;
use App\Helpers\Database;
use App\Helpers\Security;
use App\Helpers\Permission;
use App\Helpers\ActivityLogger;

class RoleController
{
    /**
     * List all roles
     */
    public function index(): void
    {
        Permission::check('manage_settings');
        
        $roles = Database::all("
            SELECT r.*, 
                   (SELECT COUNT(*) FROM users WHERE role_id = r.id AND status = 'active') as user_count,
                   (SELECT COUNT(*) FROM role_permissions WHERE role_id = r.id) as permission_count
            FROM roles r
            ORDER BY r.id
        ");
        
        view('admin.roles.index', [
            'title' => 'Role Management',
            'roles' => $roles
        ]);
    }

    /**
     * Show create role form
     */
    public function create(): void
    {
        Permission::check('manage_settings');
        
        $permissions = Database::all("
            SELECT * FROM permissions ORDER BY module, name
        ");
        
        view('admin.roles.create', [
            'title' => 'Create New Role',
            'permissions' => $permissions
        ]);
    }

    /**
     * Save new role
     */
    public function store(): void
    {
        Permission::check('manage_settings');
        
        if (!Security::verifyCsrfToken($_POST['csrf_token'] ?? '')) {
            Session::setFlash('error', 'Security validation failed.');
            redirect('/admin/roles/create');
        }
        
        $name = Security::sanitize($_POST['name'] ?? '');
        $slug = Security::sanitize($_POST['slug'] ?? '');
        $description = Security::sanitize($_POST['description'] ?? '');
        $permissions = $_POST['permissions'] ?? [];
        
        if (empty($name) || empty($slug)) {
            Session::setFlash('error', 'Role name and slug are required.');
            redirect('/admin/roles/create');
        }
        
        // Check if slug exists
        $exists = Database::row("SELECT id FROM roles WHERE slug = ?", [$slug]);
        if ($exists) {
            Session::setFlash('error', 'Role slug already exists.');
            redirect('/admin/roles/create');
        }
        
        try {
            $db = Database::getInstance();
            $db->beginTransaction();
            
            // Insert role
            $db->execute(
                "INSERT INTO roles (name, slug, description, created_at) VALUES (?, ?, ?, NOW())",
                [$name, $slug, $description]
            );
            
            $roleId = $db->lastInsertId();
            
            if ($roleId) {
                // Insert permissions
                if (!empty($permissions)) {
                    foreach ($permissions as $permId) {
                        $db->execute(
                            "INSERT INTO role_permissions (role_id, permission_id) VALUES (?, ?)",
                            [$roleId, $permId]
                        );
                    }
                }
                
                $db->commit();
                
                ActivityLogger::log('Role Created', "New role '{$name}' created with slug '{$slug}'");
                Session::setFlash('success', 'Role created successfully!');
                redirect('/admin/roles');
            } else {
                $db->rollBack();
                Session::setFlash('error', 'Failed to create role.');
                redirect('/admin/roles/create');
            }
        } catch (\Throwable $e) {
            $db = Database::getInstance();
            if ($db->inTransaction()) {
                $db->rollBack();
            }
            Session::setFlash('error', 'Error: ' . $e->getMessage());
            redirect('/admin/roles/create');
        }
    }

    /**
     * Show edit role form
     * FIXED: Handle both int and array parameters
     */
    public function edit($id): void
    {
        Permission::check('manage_settings');
        
        // Extract ID from array if needed
        if (is_array($id)) {
            $id = reset($id);
            if (is_array($id)) {
                $id = $_GET['id'] ?? $_POST['id'] ?? 0;
            }
        }
        
        $id = (int)$id;
        
        if ($id <= 0) {
            Session::setFlash('error', 'Invalid role ID.');
            redirect('/admin/roles');
        }
        
        $role = Database::row("SELECT * FROM roles WHERE id = ?", [$id]);
        if (!$role) {
            Session::setFlash('error', 'Role not found.');
            redirect('/admin/roles');
        }
        
        // Get role permissions
        $rolePermissions = Database::all("
            SELECT permission_id FROM role_permissions WHERE role_id = ?
        ", [$id]);
        
        $rolePermIds = array_column($rolePermissions, 'permission_id');
        
        $permissions = Database::all("
            SELECT * FROM permissions ORDER BY module, name
        ");
        
        view('admin.roles.edit', [
            'title' => 'Edit Role',
            'role' => $role,
            'permissions' => $permissions,
            'rolePermIds' => $rolePermIds
        ]);
    }

    /**
     * Update role
     * FIXED: Handle both int and array parameters
     */
    public function update($id): void
    {
        Permission::check('manage_settings');
        
        // Extract ID from array if needed
        if (is_array($id)) {
            $id = reset($id);
            if (is_array($id)) {
                $id = $_GET['id'] ?? $_POST['id'] ?? 0;
            }
        }
        
        $id = (int)$id;
        
        if ($id <= 0) {
            Session::setFlash('error', 'Invalid role ID.');
            redirect('/admin/roles');
        }
        
        if (!Security::verifyCsrfToken($_POST['csrf_token'] ?? '')) {
            Session::setFlash('error', 'Security validation failed.');
            redirect("/admin/roles/edit/{$id}");
        }
        
        $name = Security::sanitize($_POST['name'] ?? '');
        $description = Security::sanitize($_POST['description'] ?? '');
        $permissions = $_POST['permissions'] ?? [];
        
        if (empty($name)) {
            Session::setFlash('error', 'Role name is required.');
            redirect("/admin/roles/edit/{$id}");
        }
        
        try {
            $db = Database::getInstance();
            $db->beginTransaction();
            
            // Update role
            $db->execute(
                "UPDATE roles SET name = ?, description = ? WHERE id = ?",
                [$name, $description, $id]
            );
            
            // Delete old permissions
            $db->execute("DELETE FROM role_permissions WHERE role_id = ?", [$id]);
            
            // Insert new permissions
            if (!empty($permissions)) {
                foreach ($permissions as $permId) {
                    $db->execute(
                        "INSERT INTO role_permissions (role_id, permission_id) VALUES (?, ?)",
                        [$id, $permId]
                    );
                }
            }
            
            $db->commit();
            
            ActivityLogger::log('Role Updated', "Role ID {$id} updated.");
            Session::setFlash('success', 'Role updated successfully!');
            redirect('/admin/roles');
        } catch (\Throwable $e) {
            $db = Database::getInstance();
            if ($db->inTransaction()) {
                $db->rollBack();
            }
            Session::setFlash('error', 'Error: ' . $e->getMessage());
            redirect("/admin/roles/edit/{$id}");
        }
    }

    /**
     * Delete role
     * FIXED: Handle both int and array parameters
     */
    public function delete($id): void
    {
        Permission::check('manage_settings');
        
        // Extract ID from array if needed
        if (is_array($id)) {
            $id = reset($id);
            if (is_array($id)) {
                $id = $_GET['id'] ?? $_POST['id'] ?? 0;
            }
        }
        
        $id = (int)$id;
        
        if ($id <= 0) {
            Session::setFlash('error', 'Invalid role ID.');
            redirect('/admin/roles');
        }
        
        // Prevent deleting default roles
        $defaultRoles = [1, 2, 3, 4];
        if (in_array($id, $defaultRoles)) {
            Session::setFlash('error', 'Cannot delete system default roles.');
            redirect('/admin/roles');
        }
        
        // Check if users have this role
        $users = Database::row("SELECT COUNT(*) as count FROM users WHERE role_id = ?", [$id]);
        if ($users && $users['count'] > 0) {
            Session::setFlash('error', 'Cannot delete role. Users are assigned to this role.');
            redirect('/admin/roles');
        }
        
        try {
            $db = Database::getInstance();
            $db->beginTransaction();
            
            $db->execute("DELETE FROM role_permissions WHERE role_id = ?", [$id]);
            $db->execute("DELETE FROM roles WHERE id = ?", [$id]);
            
            $db->commit();
            
            ActivityLogger::log('Role Deleted', "Role ID {$id} deleted.");
            Session::setFlash('success', 'Role deleted successfully!');
        } catch (\Throwable $e) {
            $db = Database::getInstance();
            if ($db->inTransaction()) {
                $db->rollBack();
            }
            Session::setFlash('error', 'Error: ' . $e->getMessage());
        }
        
        redirect('/admin/roles');
    }

    /**
     * Show permissions list
     */
    public function permissionList(): void
    {
        Permission::check('manage_settings');
        
        $permissions = Database::all("
            SELECT * FROM permissions ORDER BY module, name
        ");
        
        view('admin.permissions.index', [
            'title' => 'Permissions Management',
            'permissions' => $permissions
        ]);
    }
}