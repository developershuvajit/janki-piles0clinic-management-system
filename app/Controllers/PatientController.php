<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Models\Patient;
use App\Models\Branch;
use App\Helpers\Session;
use App\Helpers\Security;
use App\Helpers\Upload;
use App\Helpers\Permission;
use App\Helpers\ActivityLogger;

class PatientController
{
    /**
     * Display a list of all patients with branch filter.
     * Super Admin - সব দেখবে
     * বাকি সবাই - নিজের ব্রাঞ্চের দেখবে
     */
    public function index(): void
    {
        Permission::check('manage_patients');
        
        $query = $_GET['q'] ?? '';
        if ($query !== '') {
            $patients = Patient::search(Security::sanitize($query));
        } else {
            $patients = Patient::all();
        }
        
        view('admin.patients.index', [
            'title' => 'Patient Directory',
            'patients' => $patients,
            'query' => $query
        ]);
    }

    /**
     * Show Patient Registration Form.
     */
    /**
 * Show Patient Registration Form.
 */
public function create(): void
{
    Permission::check('manage_patients');
    
    $user = Session::user();
    $roleSlug = $user['role_slug'] ?? $user['role'] ?? '';
    $branchId = $user['branch_id'] ?? null;
    $isSuperAdmin = ($roleSlug === 'super_admin' || $roleSlug === 'admin');
    $isBranchAdmin = ($roleSlug === 'branch_admin');
    $isReceptionist = ($roleSlug === 'receptionist');
    $hasBranchFilter = (!$isSuperAdmin && $branchId !== null);
    
    $branches = [];
    if ($isSuperAdmin) {
        // Super Admin - সব ব্রাঞ্চ দেখাবে
        $branches = Branch::all();
    } elseif ($hasBranchFilter && $branchId) {
        // বাকি সবাই - শুধু নিজের ব্রাঞ্চ দেখাবে
        $branch = Branch::find($branchId);
        if ($branch) {
            $branches = [$branch];
        }
    }
    
    view('admin.patients.create', [
        'title' => 'Register New Patient',
        'branches' => $branches,
        'isSuperAdmin' => $isSuperAdmin,
        'isBranchAdmin' => $isBranchAdmin,
        'isReceptionist' => $isReceptionist,
        'hasBranchFilter' => $hasBranchFilter,
        'branchId' => $branchId
    ]);
}

    /**
     * Store new patient profile.
     */
    public function store(): void
    {
        Permission::check('manage_patients');

        if (!Security::verifyCsrfToken($_POST['csrf_token'] ?? null)) {
            Session::setFlash('error', 'Security token expired.');
            redirect('/admin/patients/create');
        }

        $user = Session::user();
        $roleSlug = $user['role_slug'] ?? $user['role'] ?? '';
        $branchId = $user['branch_id'] ?? null;
        $isSuperAdmin = ($roleSlug === 'super_admin' || $roleSlug === 'admin');
        $hasBranchFilter = (!$isSuperAdmin && $branchId !== null);
        
        // Super Admin - ফর্ম থেকে ব্রাঞ্চ নেবে
        // বাকি সবাই - নিজের ব্রাঞ্চ ফোর্স সেট
        $selectedBranch = !empty($_POST['branch_id']) ? (int)$_POST['branch_id'] : null;
        if ($hasBranchFilter && $branchId) {
            $selectedBranch = $branchId;
        }

        $data = [
            'name' => Security::sanitize($_POST['name'] ?? ''),
            'email' => Security::sanitize($_POST['email'] ?? ''),
            'phone' => Security::sanitize($_POST['phone'] ?? ''),
            'gender' => Security::sanitize($_POST['gender'] ?? 'male'),
            'dob' => Security::sanitize($_POST['dob'] ?? ''),
            'blood_group' => Security::sanitize($_POST['blood_group'] ?? ''),
            'address' => Security::sanitize($_POST['address'] ?? ''),
            'emergency_contact' => Security::sanitize($_POST['emergency_contact'] ?? ''),
            'allergies' => Security::sanitize($_POST['allergies'] ?? ''),
            'medical_history' => Security::sanitize($_POST['medical_history'] ?? ''),
            'family_history' => Security::sanitize($_POST['family_history'] ?? ''),
            'branch_id' => $selectedBranch
        ];

        if (empty($data['name']) || empty($data['phone']) || empty($data['dob']) || empty($data['address'])) {
            Session::setFlash('error', 'Please fill in all required fields.');
            redirect('/admin/patients/create');
        }

        $patientId = Patient::create($data);

        if ($patientId) {
            ActivityLogger::log(
                'Patient Registration',
                "Registered patient: {$data['name']} (ID: {$patientId})",
                null,
                $selectedBranch
            );
            Session::setFlash('success', 'Patient registered successfully.');
            redirect('/admin/patients');
        } else {
            Session::setFlash('error', 'Unable to register patient. Email/Phone may be duplicate.');
            redirect('/admin/patients/create');
        }
    }

    /**
     * Show Edit Patient Form.
     */
    /**
 * Show Edit Patient Form.
 */
public function edit(array $params): void
{
    Permission::check('manage_patients');
    $id = (int)($params['id'] ?? 0);
    $patient = Patient::find($id);

    if (!$patient) {
        Session::setFlash('error', 'Patient not found or access denied.');
        redirect('/admin/patients');
    }

    $user = Session::user();
    $roleSlug = $user['role_slug'] ?? $user['role'] ?? '';
    $branchId = $user['branch_id'] ?? null;
    $isSuperAdmin = ($roleSlug === 'super_admin' || $roleSlug === 'admin');
    $isBranchAdmin = ($roleSlug === 'branch_admin');
    $isReceptionist = ($roleSlug === 'receptionist');
    $hasBranchFilter = (!$isSuperAdmin && $branchId !== null);
    
    $branches = [];
    if ($isSuperAdmin) {
        $branches = Branch::all();
    } elseif ($hasBranchFilter && $branchId) {
        $branch = Branch::find($branchId);
        if ($branch) {
            $branches = [$branch];
        }
    }

    view('admin.patients.edit', [
        'title' => 'Edit Patient Details',
        'patient' => $patient,
        'branches' => $branches,
        'isSuperAdmin' => $isSuperAdmin,
        'isBranchAdmin' => $isBranchAdmin,
        'isReceptionist' => $isReceptionist,
        'hasBranchFilter' => $hasBranchFilter,
        'branchId' => $branchId
    ]);
}

    /**
     * Update patient details.
     */
    public function update(array $params): void
    {
        Permission::check('manage_patients');
        $id = (int)($params['id'] ?? 0);
        $patient = Patient::find($id);

        if (!$patient) {
            Session::setFlash('error', 'Patient not found or access denied.');
            redirect('/admin/patients');
        }

        if (!Security::verifyCsrfToken($_POST['csrf_token'] ?? null)) {
            Session::setFlash('error', 'Security token expired.');
            redirect("/admin/patients/edit/{$id}");
        }

        $user = Session::user();
        $roleSlug = $user['role_slug'] ?? $user['role'] ?? '';
        $branchId = $user['branch_id'] ?? null;
        $isSuperAdmin = ($roleSlug === 'super_admin' || $roleSlug === 'admin');
        $hasBranchFilter = (!$isSuperAdmin && $branchId !== null);
        
        // Super Admin - ফর্ম থেকে ব্রাঞ্চ নেবে
        // বাকি সবাই - নিজের ব্রাঞ্চ ফোর্স সেট
        $selectedBranch = !empty($_POST['branch_id']) ? (int)$_POST['branch_id'] : null;
        if ($hasBranchFilter && $branchId) {
            $selectedBranch = $branchId;
        }

        $data = [
            'name' => Security::sanitize($_POST['name'] ?? ''),
            'email' => Security::sanitize($_POST['email'] ?? ''),
            'phone' => Security::sanitize($_POST['phone'] ?? ''),
            'gender' => Security::sanitize($_POST['gender'] ?? 'male'),
            'dob' => Security::sanitize($_POST['dob'] ?? ''),
            'blood_group' => Security::sanitize($_POST['blood_group'] ?? ''),
            'address' => Security::sanitize($_POST['address'] ?? ''),
            'emergency_contact' => Security::sanitize($_POST['emergency_contact'] ?? ''),
            'allergies' => Security::sanitize($_POST['allergies'] ?? ''),
            'medical_history' => Security::sanitize($_POST['medical_history'] ?? ''),
            'family_history' => Security::sanitize($_POST['family_history'] ?? ''),
            'branch_id' => $selectedBranch,
            'status' => Security::sanitize($_POST['status'] ?? 'active')
        ];

        if (empty($data['name']) || empty($data['phone']) || empty($data['dob']) || empty($data['address'])) {
            Session::setFlash('error', 'Please fill in all required fields.');
            redirect("/admin/patients/edit/{$id}");
        }

        if (Patient::update($id, $data)) {
            ActivityLogger::log(
                'Patient Record Update',
                "Updated patient profile: {$data['name']} (ID: {$id})",
                null,
                $selectedBranch
            );
            Session::setFlash('success', 'Patient updated successfully.');
            redirect('/admin/patients');
        } else {
            Session::setFlash('error', 'Unable to update patient records.');
            redirect("/admin/patients/edit/{$id}");
        }
    }

    /**
     * Delete patient.
     */
    public function delete(array $params): void
    {
        Permission::check('manage_patients');
        $id = (int)($params['id'] ?? 0);
        $patient = Patient::find($id);

        if (!$patient) {
            Session::setFlash('error', 'Patient not found or access denied.');
            redirect('/admin/patients');
        }

        if (Patient::delete($id)) {
            ActivityLogger::log(
                'Patient Deletion',
                "Deleted patient profile: {$patient['name']} (ID: {$id})",
                null,
                $patient['branch_id'] ?? null
            );
            Session::setFlash('success', 'Patient deleted successfully.');
        } else {
            Session::setFlash('error', 'Unable to delete patient records.');
        }
        redirect('/admin/patients');
    }

    /**
     * Display a comprehensive patient history timeline.
     */
    public function history(array $params): void
    {
        if (!Session::isLoggedIn()) {
            Session::setFlash('error', 'Please log in to view patient timeline records.');
            redirect('/login');
        }

        $patientCode = Security::sanitize($params['patientId'] ?? '');
        $patient = Patient::findByPatientId($patientCode);

        if (!$patient) {
            Session::setFlash('error', 'Patient not found or access denied.');
            redirect('/admin/patients');
        }

        $timeline = Patient::getTimeline((int)$patient['id']);
        $documents = Patient::getDocuments((int)$patient['id']);

        view('admin.patients.history', [
            'title' => 'Patient Timeline - ' . $patient['name'],
            'patient' => $patient,
            'timeline' => $timeline,
            'documents' => $documents
        ]);
    }

    /**
     * Upload medical files/reports to patient timeline.
     */
    public function uploadDoc(array $params): void
    {
        if (!Session::isLoggedIn()) {
            redirect('/login');
        }

        $patientId = (int)($params['id'] ?? 0);
        $patient = Patient::find($patientId);
        if (!$patient) {
            Session::setFlash('error', 'Patient not found or access denied.');
            redirect('/admin/patients');
        }

        if (!empty($_FILES['report']['name'])) {
            $uploader = new Upload([
                'allowedExtensions' => ['pdf', 'png', 'jpg', 'jpeg', 'doc', 'docx'],
                'maxSize' => 5 * 1024 * 1024
            ]);
            $res = $uploader->file($_FILES['report'], 'patients/reports');
            if ($res['success']) {
                Patient::addDocument($patientId, $_FILES['report']['name'], $res['path']);
                ActivityLogger::log(
                    'Medical Document Upload',
                    "Uploaded document for patient: {$patient['name']}",
                    null,
                    $patient['branch_id'] ?? null
                );
                Session::setFlash('success', 'Medical report uploaded successfully.');
            } else {
                Session::setFlash('error', 'Upload failed: ' . $res['error']);
            }
        } else {
            Session::setFlash('error', 'No file selected.');
        }

        redirect('/admin/patients/history/' . $patient['patient_id']);
    }

    /**
     * Delete medical document file.
     */
    public function deleteDoc(array $params): void
    {
        if (!Session::isLoggedIn()) {
            redirect('/login');
        }

        $docId = (int)($params['id'] ?? 0);
        $doc = Patient::getDocument($docId);
        if (!$doc) {
            Session::setFlash('error', 'Document not found.');
            redirect('/admin/patients');
        }

        $patient = Patient::find((int)$doc['patient_id']);
        
        if (file_exists(PUBLIC_PATH . '/' . $doc['file_path'])) {
            @unlink(PUBLIC_PATH . '/' . $doc['file_path']);
        }

        if (Patient::deleteDocument($docId)) {
            Session::setFlash('success', 'Medical document successfully removed.');
        } else {
            Session::setFlash('error', 'Unable to remove document records.');
        }

        redirect('/admin/patients/history/' . ($patient ? $patient['patient_id'] : ''));
    }
}