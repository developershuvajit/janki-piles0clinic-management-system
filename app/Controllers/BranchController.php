<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Models\Branch;
use App\Helpers\Session;
use App\Helpers\Security;
use App\Helpers\Upload;
use App\Helpers\Permission;
use App\Helpers\ActivityLogger;
use App\Helpers\Database;

class BranchController
{
    /**
     * List all branches.
     */
    public function index(): void
    {
        Permission::check('manage_branches');
        
        $branches = Branch::all();
        view('admin.branches.index', [
            'title' => 'Manage Branches',
            'branches' => $branches
        ]);
    }

    /**
     * Show Branch Creation Form.
     */
    public function create(): void
    {
        Permission::check('manage_branches');
        view('admin.branches.create', ['title' => 'Add New Branch']);
    }

    /**
     * Store a new branch.
     */
    public function store(): void
    {
        Permission::check('manage_branches');

        if (!Security::verifyCsrfToken($_POST['csrf_token'] ?? null)) {
            Session::setFlash('error', 'Security verification expired.');
            redirect('/admin/branches/create');
        }

        $data = [
            'name' => Security::sanitize($_POST['name'] ?? ''),
            'address' => Security::sanitize($_POST['address'] ?? ''),
            'phone' => Security::sanitize($_POST['phone'] ?? ''),
            'emergency_number' => Security::sanitize($_POST['emergency_number'] ?? ''),
            'email' => Security::sanitize($_POST['email'] ?? ''),
            'google_map_link' => $_POST['google_map_link'] ?? '',
            'opening_hours' => Security::sanitize($_POST['opening_hours'] ?? ''),
            'status' => Security::sanitize($_POST['status'] ?? 'active')
        ];

        if (empty($data['name']) || empty($data['address']) || empty($data['phone']) || empty($data['email'])) {
            Session::setFlash('error', 'Please fill in all required fields.');
            redirect('/admin/branches/create');
        }

        if (!empty($_FILES['logo']['name'])) {
            $uploader = new Upload();
            $result = $uploader->file($_FILES['logo'], 'branch_logos');
            if ($result['success']) {
                $data['logo'] = $result['path'];
            } else {
                Session::setFlash('error', 'Logo upload failed: ' . $result['error']);
                redirect('/admin/branches/create');
            }
        }

        if (Branch::create($data)) {
            ActivityLogger::log('Branch Creation', "Created new clinic branch: {$data['name']}");
            Session::setFlash('success', 'Branch successfully created.');
            redirect('/admin/branches');
        } else {
            Session::setFlash('error', 'Unable to create branch. Please try again.');
            redirect('/admin/branches/create');
        }
    }

    /**
     * Show Edit Branch Form.
     */
    public function edit($params): void
    {
        Permission::check('manage_branches');
        
        $id = is_array($params) ? (int)($params['id'] ?? 0) : (int)$params;
        
        $branch = Branch::find($id);
        if (!$branch) {
            Session::setFlash('error', 'Branch not found.');
            redirect('/admin/branches');
        }

        view('admin.branches.edit', [
            'title' => 'Edit Branch - ' . $branch['name'],
            'branch' => $branch
        ]);
    }

    /**
     * Update an existing branch.
     */
    public function update($params): void
    {
        Permission::check('manage_branches');
        
        $id = is_array($params) ? (int)($params['id'] ?? 0) : (int)$params;
        
        $branch = Branch::find($id);
        if (!$branch) {
            Session::setFlash('error', 'Branch not found.');
            redirect('/admin/branches');
        }

        if (!Security::verifyCsrfToken($_POST['csrf_token'] ?? null)) {
            Session::setFlash('error', 'Security verification expired.');
            redirect("/admin/branches/edit/{$id}");
        }

        $data = [
            'name' => Security::sanitize($_POST['name'] ?? ''),
            'address' => Security::sanitize($_POST['address'] ?? ''),
            'phone' => Security::sanitize($_POST['phone'] ?? ''),
            'emergency_number' => Security::sanitize($_POST['emergency_number'] ?? ''),
            'email' => Security::sanitize($_POST['email'] ?? ''),
            'google_map_link' => $_POST['google_map_link'] ?? '', 
            'opening_hours' => Security::sanitize($_POST['opening_hours'] ?? ''),
            'status' => Security::sanitize($_POST['status'] ?? 'active'),
            'logo' => $branch['logo']
        ];

        if (empty($data['name']) || empty($data['address']) || empty($data['phone']) || empty($data['email'])) {
            Session::setFlash('error', 'Please fill in all required fields.');
            redirect("/admin/branches/edit/{$id}");
        }

        if (!empty($_FILES['logo']['name'])) {
            $uploader = new Upload();
            $result = $uploader->file($_FILES['logo'], 'branch_logos');
            if ($result['success']) {
                $data['logo'] = $result['path'];
                if ($branch['logo'] && file_exists(PUBLIC_PATH . '/' . $branch['logo'])) {
                    @unlink(PUBLIC_PATH . '/' . $branch['logo']);
                }
            } else {
                Session::setFlash('error', 'Logo upload failed: ' . $result['error']);
                redirect("/admin/branches/edit/{$id}");
            }
        }

        if (Branch::update($id, $data)) {
            ActivityLogger::log('Branch Update', "Updated details for clinic branch: {$data['name']}");
            Session::setFlash('success', 'Branch successfully updated.');
            redirect('/admin/branches');
        } else {
            Session::setFlash('error', 'Unable to update branch. Please try again.');
            redirect("/admin/branches/edit/{$id}");
        }
    }

    /**
     * Delete a branch.
     */
    public function delete($params): void
    {
        Permission::check('manage_branches');
        
        $id = is_array($params) ? (int)($params['id'] ?? 0) : (int)$params;
        
        $branch = Branch::find($id);
        if (!$branch) {
            Session::setFlash('error', 'Branch not found.');
            redirect('/admin/branches');
        }

        if ($branch['logo'] && file_exists(PUBLIC_PATH . '/' . $branch['logo'])) {
            @unlink(PUBLIC_PATH . '/' . $branch['logo']);
        }

        if (Branch::delete($id)) {
            ActivityLogger::log('Branch Deletion', "Deleted clinic branch: {$branch['name']}");
            Session::setFlash('success', 'Branch successfully deleted.');
        } else {
            Session::setFlash('error', 'Unable to delete branch. Check dependencies.');
        }

        redirect('/admin/branches');
    }

    /**
     * Show Branch Dashboard - Uses admin_header (auto-detects role)
     */
    public function dashboard($params): void
    {
        // Extract ID and cast to int
        $id = is_array($params) ? (int)($params['id'] ?? 0) : (int)$params;
        
        // Check if logged in
        if (!Session::isLoggedIn()) {
            redirect('/login');
        }
        
        $user = Session::user();
        $roleSlug = $user['role_slug'] ?? '';
        $userBranchId = $user['branch_id'] ?? null;
        
        // Security Check
        if ($roleSlug === 'branch_admin') {
            if ((int)$id !== (int)$userBranchId) {
                Session::setFlash('error', 'Access denied to this branch.');
                redirect('/admin/dashboard');
            }
        } elseif (!in_array($roleSlug, ['super_admin', 'admin'])) {
            Session::setFlash('error', 'Access denied.');
            redirect('/login');
        }
        
        // Get branch data
        $branch = Branch::find($id);
        if (!$branch) {
            Session::setFlash('error', 'Branch not found.');
            redirect('/admin/dashboard');
        }

        // Fetch stats
        $stats = [
            'total_revenue' => 0,
            'patient_count' => 0,
            'doctors' => []
        ];

        // Get patients count
        $patientCount = Database::row("SELECT COUNT(*) as total FROM patients WHERE branch_id = ?", [$id]);
        $stats['patient_count'] = $patientCount['total'] ?? 0;

        // Get revenue from billing table
        $revenue = Database::row("
            SELECT COALESCE(SUM(paid_amount), 0) as total 
            FROM billing 
            WHERE branch_id = ? AND payment_status = 'paid'
        ", [$id]);
        
        $stats['total_revenue'] = $revenue['total'] ?? 0;

        // Get doctors assigned to this branch
        $stats['doctors'] = Database::all("
            SELECT u.id, u.username, u.email, e.photo, e.shift_start, e.shift_end
            FROM users u
            JOIN roles r ON u.role_id = r.id
            LEFT JOIN employees e ON u.id = e.user_id
            WHERE u.branch_id = ? AND r.slug = 'doctor' AND u.status = 'active'
        ", [$id]);

        // Use the EXISTING view with admin_header
        // admin_header.php will auto-detect role and show appropriate sidebar
        view('admin.branches.dashboard', [
            'title' => $branch['name'] . ' Dashboard',
            'branch' => $branch,
            'stats' => $stats,
            'activePage' => 'branches'
        ]);
    }

    /**
     * Branch Patients (For Branch Admin)
     */
    public function patients($params): void
    {
        $id = is_array($params) ? (int)($params['id'] ?? 0) : (int)$params;
        $this->checkBranchAccess($id);
        
        $patients = Database::all("
            SELECT * FROM patients 
            WHERE branch_id = ? 
            ORDER BY id DESC
        ", [$id]);
        
        view('admin.patients.index', [
            'title' => 'Patients',
            'patients' => $patients,
            'branch_id' => $id,
            'activePage' => 'patients'
        ]);
    }

    /**
     * Branch Appointments (For Branch Admin)
     */
    public function appointments($params): void
    {
        $id = is_array($params) ? (int)($params['id'] ?? 0) : (int)$params;
        $this->checkBranchAccess($id);
        
        $appointments = Database::all("
            SELECT a.*, p.name as patient_name 
            FROM appointments a
            LEFT JOIN patients p ON a.patient_id = p.id
            WHERE a.branch_id = ? 
            ORDER BY a.date DESC, a.time DESC
        ", [$id]);
        
        view('admin.appointments.index', [
            'title' => 'Appointments',
            'appointments' => $appointments,
            'branch_id' => $id,
            'activePage' => 'appointments'
        ]);
    }

    /**
     * Branch Employees (For Branch Admin)
     */
    public function employees($params): void
    {
        $id = is_array($params) ? (int)($params['id'] ?? 0) : (int)$params;
        $this->checkBranchAccess($id);
        
        $employees = Database::all("
            SELECT e.*, u.username, u.email, r.name as role_name
            FROM employees e
            LEFT JOIN users u ON e.user_id = u.id
            LEFT JOIN roles r ON u.role_id = r.id
            WHERE u.branch_id = ? AND e.status = 'active'
            ORDER BY e.name ASC
        ", [$id]);
        
        view('admin.employees.index', [
            'title' => 'Employees',
            'employees' => $employees,
            'branch_id' => $id,
            'activePage' => 'employees'
        ]);
    }

    /**
     * Branch Reports (For Branch Admin)
     */
    public function reports($params): void
    {
        $id = is_array($params) ? (int)($params['id'] ?? 0) : (int)$params;
        $this->checkBranchAccess($id);
        
        $branch = Database::row("SELECT name FROM branches WHERE id = ?", [$id]);
        
        view('admin.reports.index', [
            'title' => 'Reports',
            'branch_id' => $id,
            'branch_name' => $branch['name'] ?? 'Branch',
            'activePage' => 'reports'
        ]);
    }

    /**
     * Branch Settings (For Branch Admin)
     */
    public function settings($params): void
    {
        $id = is_array($params) ? (int)($params['id'] ?? 0) : (int)$params;
        $this->checkBranchAccess($id);
        
        $branch = Database::row("SELECT * FROM branches WHERE id = ?", [$id]);
        
        view('admin.settings.index', [
            'title' => 'Settings',
            'branch' => $branch,
            'branch_id' => $id,
            'activePage' => 'settings'
        ]);
    }

    /**
     * Check Branch Access
     */
    private function checkBranchAccess(int $branchId): void
    {
        if (!Session::isLoggedIn()) {
            redirect('/login');
        }
        
        $user = Session::user();
        $roleSlug = $user['role_slug'] ?? '';
        $userBranchId = $user['branch_id'] ?? null;
        
        if ($roleSlug === 'branch_admin') {
            if ((int)$branchId !== (int)$userBranchId) {
                Session::setFlash('error', 'Access denied to this branch.');
                redirect('/admin/dashboard');
            }
        } elseif (!in_array($roleSlug, ['super_admin', 'admin'])) {
            Session::setFlash('error', 'Access denied.');
            redirect('/login');
        }
    }
}