<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Models\Branch;
use App\Helpers\Session;
use App\Helpers\Security;
use App\Helpers\Upload;
use App\Helpers\Permission;
use App\Helpers\ActivityLogger;

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

        Security::requireCsrfToken('/admin/branches/create', 'Security verification expired.');

        $data = [
            'name' => Security::sanitize($_POST['name'] ?? ''),
            'address' => Security::sanitize($_POST['address'] ?? ''),
            'phone' => Security::sanitize($_POST['phone'] ?? ''),
            'emergency_number' => Security::sanitize($_POST['emergency_number'] ?? ''),
            'email' => Security::sanitize($_POST['email'] ?? ''),
            'google_map_link' => $_POST['google_map_link'] ?? '', // Don't strip maps markup
            'opening_hours' => Security::sanitize($_POST['opening_hours'] ?? ''),
            'status' => Security::sanitize($_POST['status'] ?? 'active')
        ];

        // Validate required fields
        if (empty($data['name']) || empty($data['address']) || empty($data['phone']) || empty($data['email'])) {
            Session::setFlash('error', 'Please fill in all required fields.');
            redirect('/admin/branches/create');
        }

        // Upload Logo if provided
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
    public function edit(array $params): void
    {
        Permission::check('manage_branches');
        $id = (int)($params['id'] ?? 0);
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
    public function update(array $params): void
    {
        Permission::check('manage_branches');
        $id = (int)($params['id'] ?? 0);
        $branch = Branch::find($id);

        if (!$branch) {
            Session::setFlash('error', 'Branch not found.');
            redirect('/admin/branches');
        }

        Security::requireCsrfToken("/admin/branches/edit/{$id}", 'Security verification expired.');

        $data = [
            'name' => Security::sanitize($_POST['name'] ?? ''),
            'address' => Security::sanitize($_POST['address'] ?? ''),
            'phone' => Security::sanitize($_POST['phone'] ?? ''),
            'emergency_number' => Security::sanitize($_POST['emergency_number'] ?? ''),
            'email' => Security::sanitize($_POST['email'] ?? ''),
            'google_map_link' => $_POST['google_map_link'] ?? '', 
            'opening_hours' => Security::sanitize($_POST['opening_hours'] ?? ''),
            'status' => Security::sanitize($_POST['status'] ?? 'active'),
            'logo' => $branch['logo'] // Default to old logo
        ];

        if (empty($data['name']) || empty($data['address']) || empty($data['phone']) || empty($data['email'])) {
            Session::setFlash('error', 'Please fill in all required fields.');
            redirect("/admin/branches/edit/{$id}");
        }

        // Upload new logo if provided
        if (!empty($_FILES['logo']['name'])) {
            $uploader = new Upload();
            $result = $uploader->file($_FILES['logo'], 'branch_logos');
            if ($result['success']) {
                $data['logo'] = $result['path'];
                
                // Delete old logo file if exists
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
    public function delete(array $params): void
    {
        Permission::check('manage_branches');
        $id = (int)($params['id'] ?? 0);
        $branch = Branch::find($id);

        if (!$branch) {
            Session::setFlash('error', 'Branch not found.');
            redirect('/admin/branches');
        }

        // Delete logo file if exists
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
     * Show Branch Specific Dashboard (Phase 3).
     */
    public function dashboard(array $params): void
    {
        Permission::check('view_branch_dashboard');
        $id = (int)($params['id'] ?? 0);
        $branch = Branch::find($id);

        if (!$branch) {
            Session::setFlash('error', 'Branch not found.');
            redirect('/admin/dashboard');
        }

        // Fetch dynamic metrics for patients, doctors, and revenues
        $stats = Branch::getBranchStats($id);

        view('admin.branches.dashboard', [
            'title' => 'Branch Dashboard - ' . $branch['name'],
            'branch' => $branch,
            'stats' => $stats
        ]);
    }
}
