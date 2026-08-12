<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Models\Employee;
use App\Models\Branch;
use App\Helpers\Session;
use App\Helpers\Security;
use App\Helpers\Upload;
use App\Helpers\Permission;
use App\Helpers\ActivityLogger;
use App\Helpers\Database;

class EmployeeController
{
    /**
     * Display listing of all employees.
     */
    public function index(): void
    {
        Permission::check('manage_employees');
        
        $employees = Employee::all();
        view('admin.employees.index', [
            'title' => 'Employee Roster',
            'employees' => $employees
        ]);
    }

    /**
     * Show Employee Creation Form.
     */
    public function create(): void
    {
        Permission::check('manage_employees');
        
        $branches = Branch::all();
        $roles = Database::all("SELECT * FROM roles ORDER BY id ASC");
        
        view('admin.employees.create', [
            'title' => 'Enroll Employee',
            'branches' => $branches,
            'roles' => $roles
        ]);
    }

    /**
     * Store new employee profile.
     */
    public function store(): void
    {
        Permission::check('manage_employees');

        Security::requireCsrfToken('/admin/employees/create');

        $data = [
            'username' => Security::sanitize($_POST['username'] ?? ''),
            'email' => Security::sanitize($_POST['email'] ?? ''),
            'password' => $_POST['password'] ?? '',
            'role_id' => (int)($_POST['role_id'] ?? 0),
            'branch_id' => !empty($_POST['branch_id']) ? (int)$_POST['branch_id'] : null,
            'salary' => (float)($_POST['salary'] ?? 0.00),
            'shift_start' => Security::sanitize($_POST['shift_start'] ?? '09:00:00'),
            'shift_end' => Security::sanitize($_POST['shift_end'] ?? '17:00:00'),
            'photo' => null
        ];

        // Validation
        if (empty($data['username']) || empty($data['email']) || empty($data['password']) || $data['role_id'] === 0) {
            Session::setFlash('error', 'Please fill in all required credentials fields.');
            redirect('/admin/employees/create');
        }

        // Upload Profile Photo
        if (!empty($_FILES['photo']['name'])) {
            $uploader = new Upload();
            $photoRes = $uploader->file($_FILES['photo'], 'employees/photos');
            if ($photoRes['success']) {
                $data['photo'] = $photoRes['path'];
            } else {
                Session::setFlash('error', 'Photo upload failed: ' . $photoRes['error']);
                redirect('/admin/employees/create');
            }
        }

        // Create Profile Transaction
        $employeeId = Employee::create($data);

        if ($employeeId) {
            ActivityLogger::log('Employee Enrollment', "Enrolled employee user account: {$data['username']}");

            // Process Multiple Document uploads
            if (!empty($_FILES['documents']['name'][0])) {
                $uploader = new Upload([
                    'allowedExtensions' => ['pdf', 'doc', 'docx', 'png', 'jpg'],
                    'maxSize' => 5 * 1024 * 1024
                ]);

                $fileCount = count($_FILES['documents']['name']);
                for ($i = 0; $i < $fileCount; $i++) {
                    $file = [
                        'name' => $_FILES['documents']['name'][$i],
                        'type' => $_FILES['documents']['type'][$i],
                        'tmp_name' => $_FILES['documents']['tmp_name'][$i],
                        'error' => $_FILES['documents']['error'][$i],
                        'size' => $_FILES['documents']['size'][$i]
                    ];

                    if ($file['error'] === UPLOAD_ERR_OK) {
                        $docRes = $uploader->file($file, 'employees/documents');
                        if ($docRes['success']) {
                            Employee::addDocument($employeeId, $file['name'], $docRes['path']);
                        }
                    }
                }
            }

            Session::setFlash('success', 'Employee enrolled successfully.');
            redirect('/admin/employees');
        } else {
            Session::setFlash('error', 'Error: Username or Email is already registered.');
            redirect('/admin/employees/create');
        }
    }

    /**
     * Show Edit Employee Form.
     */
    public function edit(array $params): void
    {
        Permission::check('manage_employees');
        $id = (int)($params['id'] ?? 0);
        $employee = Employee::find($id);

        if (!$employee) {
            Session::setFlash('error', 'Employee not found.');
            redirect('/admin/employees');
        }

        $branches = Branch::all();
        $roles = Database::all("SELECT * FROM roles ORDER BY id ASC");
        $documents = Employee::getDocuments($id);

        view('admin.employees.edit', [
            'title' => 'Edit Employee - ' . $employee['username'],
            'employee' => $employee,
            'branches' => $branches,
            'roles' => $roles,
            'documents' => $documents
        ]);
    }

    /**
     * Update employee credentials.
     */
    public function update(array $params): void
    {
        Permission::check('manage_employees');
        $id = (int)($params['id'] ?? 0);
        $employee = Employee::find($id);

        if (!$employee) {
            Session::setFlash('error', 'Employee not found.');
            redirect('/admin/employees');
        }

        Security::requireCsrfToken("/admin/employees/edit/{$id}");

        $data = [
            'username' => Security::sanitize($_POST['username'] ?? ''),
            'email' => Security::sanitize($_POST['email'] ?? ''),
            'password' => $_POST['password'] ?? '', // Empty if not changing
            'role_id' => (int)($_POST['role_id'] ?? 0),
            'branch_id' => !empty($_POST['branch_id']) ? (int)$_POST['branch_id'] : null,
            'salary' => (float)($_POST['salary'] ?? 0.00),
            'shift_start' => Security::sanitize($_POST['shift_start'] ?? '09:00:00'),
            'shift_end' => Security::sanitize($_POST['shift_end'] ?? '17:00:00'),
            'photo' => $employee['photo']
        ];

        if (empty($data['username']) || empty($data['email']) || $data['role_id'] === 0) {
            Session::setFlash('error', 'Please fill in all required fields.');
            redirect("/admin/employees/edit/{$id}");
        }

        // Upload new profile photo if provided
        if (!empty($_FILES['photo']['name'])) {
            $uploader = new Upload();
            $photoRes = $uploader->file($_FILES['photo'], 'employees/photos');
            if ($photoRes['success']) {
                $data['photo'] = $photoRes['path'];
                
                // Delete old photo file
                if ($employee['photo'] && file_exists(PUBLIC_PATH . '/' . $employee['photo'])) {
                    @unlink(PUBLIC_PATH . '/' . $employee['photo']);
                }
            } else {
                Session::setFlash('error', 'Photo upload failed: ' . $photoRes['error']);
                redirect("/admin/employees/edit/{$id}");
            }
        }

        if (Employee::update($id, $data)) {
            ActivityLogger::log('Employee Update', "Updated employee details for user: {$data['username']}");

            // Process new document uploads
            if (!empty($_FILES['documents']['name'][0])) {
                $uploader = new Upload([
                    'allowedExtensions' => ['pdf', 'doc', 'docx', 'png', 'jpg'],
                    'maxSize' => 5 * 1024 * 1024
                ]);

                $fileCount = count($_FILES['documents']['name']);
                for ($i = 0; $i < $fileCount; $i++) {
                    $file = [
                        'name' => $_FILES['documents']['name'][$i],
                        'type' => $_FILES['documents']['type'][$i],
                        'tmp_name' => $_FILES['documents']['tmp_name'][$i],
                        'error' => $_FILES['documents']['error'][$i],
                        'size' => $_FILES['documents']['size'][$i]
                    ];

                    if ($file['error'] === UPLOAD_ERR_OK) {
                        $docRes = $uploader->file($file, 'employees/documents');
                        if ($docRes['success']) {
                            Employee::addDocument($id, $file['name'], $docRes['path']);
                        }
                    }
                }
            }

            Session::setFlash('success', 'Employee profile updated successfully.');
            redirect('/admin/employees');
        } else {
            Session::setFlash('error', 'Unable to update employee. Username or Email already in use.');
            redirect("/admin/employees/edit/{$id}");
        }
    }

    /**
     * Delete an employee.
     */
    public function delete(array $params): void
    {
        Permission::check('manage_employees');
        $id = (int)($params['id'] ?? 0);
        $employee = Employee::find($id);

        if (!$employee) {
            Session::setFlash('error', 'Employee not found.');
            redirect('/admin/employees');
        }

        // Clean up uploaded documents
        $docs = Employee::getDocuments($id);
        foreach ($docs as $doc) {
            if (file_exists(PUBLIC_PATH . '/' . $doc['file_path'])) {
                @unlink(PUBLIC_PATH . '/' . $doc['file_path']);
            }
        }

        // Clean up profile photo
        if ($employee['photo'] && file_exists(PUBLIC_PATH . '/' . $employee['photo'])) {
            @unlink(PUBLIC_PATH . '/' . $employee['photo']);
        }

        if (Employee::delete($id)) {
            ActivityLogger::log('Employee Deletion', "Deleted employee profile: {$employee['username']}");
            Session::setFlash('success', 'Employee deleted successfully.');
        } else {
            Session::setFlash('error', 'Unable to delete employee.');
        }

        redirect('/admin/employees');
    }

    /**
     * Delete employee contract document.
     */
    public function deleteDoc(array $params): void
    {
        Permission::check('manage_employees');
        $docId = (int)($params['id'] ?? 0);
        $doc = Employee::getDocument($docId);

        if (!$doc) {
            Session::setFlash('error', 'Document not found.');
            redirect('/admin/employees');
        }

        // Delete physical file
        if (file_exists(PUBLIC_PATH . '/' . $doc['file_path'])) {
            @unlink(PUBLIC_PATH . '/' . $doc['file_path']);
        }

        if (Employee::deleteDocument($docId)) {
            Session::setFlash('success', 'Document successfully deleted.');
        } else {
            Session::setFlash('error', 'Unable to delete document records.');
        }

        redirect("/admin/employees/edit/{$doc['employee_id']}");
    }

    /**
     * Show Attendance Logging Dashboard (Phase 4).
     */
    public function attendance(): void
    {
        Permission::check('record_attendance');
        
        $date = $_GET['date'] ?? date('Y-m-d');
        $attendance = Employee::getAttendanceByDate($date);
        
        view('admin.employees.attendance', [
            'title' => 'Daily Attendance Sheet',
            'date' => $date,
            'records' => $attendance
        ]);
    }

    /**
     * Save daily check-in / check-out logs.
     */
    public function saveAttendance(): void
    {
        Permission::check('record_attendance');

        Security::requireCsrfToken('/admin/employees/attendance');

        $date = $_POST['date'] ?? date('Y-m-d');
        $status = $_POST['status'] ?? [];
        $checkIn = $_POST['check_in'] ?? [];
        $checkOut = $_POST['check_out'] ?? [];

        $saved = 0;
        foreach ($status as $empId => $stat) {
            $empId = (int)$empId;
            $stat = Security::sanitize($stat);
            $inTime = !empty($checkIn[$empId]) ? Security::sanitize($checkIn[$empId]) : null;
            $outTime = !empty($checkOut[$empId]) ? Security::sanitize($checkOut[$empId]) : null;

            if (Employee::recordAttendance($empId, $date, $stat, $inTime, $outTime)) {
                $saved++;
            }
        }

        ActivityLogger::log('Attendance Sheet Update', "Recorded attendance for {$saved} employees on date: {$date}");
        Session::setFlash('success', "Attendance logs updated for {$saved} staff members.");
        redirect('/admin/employees/attendance?date=' . $date);
    }
}
