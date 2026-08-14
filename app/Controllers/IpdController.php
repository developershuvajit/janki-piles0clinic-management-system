<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Models\Ipd;
use App\Models\Patient;
use App\Models\Billing;
use App\Helpers\Session;
use App\Helpers\Security;
use App\Helpers\Permission;
use App\Helpers\Database;
use App\Helpers\ActivityLogger;

class IpdController
{
    private function getBranchFilter(): array
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

    public function index(): void
    {
        Permission::check('manage_ipd');
        
        $filter = $this->getBranchFilter();
        $branchId = $filter['hasFilter'] ? $filter['branchId'] : null;
        
        $admissions = Ipd::getActiveAdmissions($branchId);
        $discharged = Ipd::getDischargedHistory($branchId);

        view('admin.ipd.index', [
            'title' => 'Inpatient Department (IPD)',
            'admissions' => $admissions,
            'discharged' => $discharged,
            'activePage' => 'ipd'
        ]);
    }

    public function admitForm(): void
    {
        Permission::check('manage_ipd');
        
        $filter = $this->getBranchFilter();
        $branchId = $filter['hasFilter'] ? $filter['branchId'] : null;
        
        $patientsSql = "SELECT id, name, patient_id FROM patients WHERE status = 'active'";
        $patientsParams = [];
        
        if ($branchId !== null) {
            $patientsSql .= " AND branch_id = ?";
            $patientsParams[] = $branchId;
        }
        $patientsSql .= " ORDER BY name ASC";
        $patients = Database::all($patientsSql, $patientsParams);
        
        $doctorsSql = "SELECT u.id, u.username 
                       FROM users u
                       JOIN roles r ON u.role_id = r.id
                       WHERE r.slug = 'doctor' AND u.status = 'active'";
        $doctorsParams = [];
        
        if ($branchId !== null) {
            $doctorsSql .= " AND u.branch_id = ?";
            $doctorsParams[] = $branchId;
        }
        $doctorsSql .= " ORDER BY u.username ASC";
        $doctors = Database::all($doctorsSql, $doctorsParams);

        view('admin.ipd.admit', [
            'title' => 'Admit Inpatient',
            'patients' => $patients,
            'doctors' => $doctors,
            'isSuperAdmin' => $filter['isSuperAdmin'],
            'hasBranchFilter' => $filter['hasFilter'],
            'activePage' => 'ipd'
        ]);
    }

     /**
 * Save Admission - Complete Working
 */
      public function saveAdmission(): void
{
    Permission::check('manage_ipd');

    if (!Security::verifyCsrfToken($_POST['csrf_token'] ?? null)) {
        Session::setFlash('error', 'Security token expired.');
        redirect('/admin/ipd/admit');
        return;
    }

    $filter = $this->getBranchFilter();
    $branchId = $filter['hasFilter'] ? $filter['branchId'] : null;

    $patientId = (int)($_POST['patient_id'] ?? 0);
    $doctorId = (int)($_POST['doctor_id'] ?? 0);
    $admissionDate = Security::sanitize($_POST['admission_date'] ?? date('Y-m-d H:i:s'));
    $symptoms = Security::sanitize($_POST['symptoms'] ?? '');
    $diagnosis = Security::sanitize($_POST['diagnosis'] ?? '');

    if ($patientId === 0 || $doctorId === 0 || empty($diagnosis)) {
        Session::setFlash('error', 'Please fill in all required admission details.');
        redirect('/admin/ipd/admit');
        return;
    }

    $patient = Database::row("SELECT id, branch_id, name FROM patients WHERE id = ?", [$patientId]);
    if (!$patient) {
        Session::setFlash('error', 'Patient not found.');
        redirect('/admin/ipd/admit');
        return;
    }

    if ($branchId !== null && (int)$patient['branch_id'] !== (int)$branchId) {
        Session::setFlash('error', 'Patient not found in your branch.');
        redirect('/admin/ipd/admit');
        return;
    }

    $data = [
        'patient_id' => $patientId,
        'doctor_id' => $doctorId,
        'branch_id' => $patient['branch_id'] ?? null,
        'admission_date' => $admissionDate,
        'symptoms' => $symptoms,
        'diagnosis' => $diagnosis
    ];

    error_log("=== IPD DATA SENT TO MODEL ===");
    error_log(print_r($data, true));

    $admissionId = Ipd::admit($data);

    error_log("=== IPD ADMISSION RESULT ===");
    error_log("Admission ID: " . ($admissionId ?? 'NULL'));

    if ($admissionId) {
        ActivityLogger::log('IPD Patient Admitted', "Admitted patient ID {$patientId} (Admission ID: {$admissionId})");
        Session::setFlash('success', '✅ Patient admitted successfully.');
        redirect('/admin/ipd');
        return;
    } else {
        Session::setFlash('error', 'Unable to complete admission. Please try again.');
        redirect('/admin/ipd/admit');
        return;
    }
}

    public function nursingLogs(array $params): void
    {
        Permission::check('manage_ipd');
        $id = (int)($params['id'] ?? 0);
        $admission = Ipd::findAdmission($id);

        if (!$admission) {
            Session::setFlash('error', 'Admission details not found.');
            redirect('/admin/ipd');
            return;
        }

        $logs = Ipd::getNursingLogs($id);
        $procedures = Ipd::getProcedures($id);
        
        $doctors = Database::all(
            "SELECT u.id, u.username FROM users u 
             JOIN roles r ON u.role_id = r.id 
             WHERE r.slug = 'doctor' AND u.status = 'active'"
        );

        view('admin.ipd.nursing_logs', [
            'title' => 'Clinical Charts - Admission #' . $id,
            'admission' => $admission,
            'logs' => $logs,
            'procedures' => $procedures,
            'doctors' => $doctors,
            'activePage' => 'ipd'
        ]);
    }

    public function saveNursingLog(array $params): void
    {
        Permission::check('manage_ipd');
        $id = (int)($params['id'] ?? 0);

        if (!Security::verifyCsrfToken($_POST['csrf_token'] ?? null)) {
            Session::setFlash('error', 'Security token expired.');
            redirect("/admin/ipd/nursing-logs/{$id}");
            return;
        }

        $data = [
            'temp' => Security::sanitize($_POST['temp'] ?? ''),
            'bp' => Security::sanitize($_POST['bp'] ?? ''),
            'pulse' => Security::sanitize($_POST['pulse'] ?? ''),
            'notes' => Security::sanitize($_POST['notes'] ?? '')
        ];

        if (empty($data['notes'])) {
            Session::setFlash('error', 'Notes/Remarks cannot be empty.');
            redirect("/admin/ipd/nursing-logs/{$id}");
            return;
        }

        if (Ipd::addNursingLog($id, $data)) {
            ActivityLogger::log('IPD Vitals Logged', "Recorded nurse vitals details for IPD admission #{$id}");
            Session::setFlash('success', '✅ Nursing vitals details added.');
        } else {
            Session::setFlash('error', 'Unable to add log.');
        }

        redirect("/admin/ipd/nursing-logs/{$id}");
    }

    public function saveProcedure(array $params): void
    {
        Permission::check('manage_ipd');
        $id = (int)($params['id'] ?? 0);

        if (!Security::verifyCsrfToken($_POST['csrf_token'] ?? null)) {
            Session::setFlash('error', 'Security token expired.');
            redirect("/admin/ipd/nursing-logs/{$id}");
            return;
        }

        $data = [
            'name' => Security::sanitize($_POST['procedure_name'] ?? ''),
            'doctor_id' => (int)($_POST['doctor_id'] ?? 0),
            'cost' => (float)($_POST['cost'] ?? 0.00)
        ];

        if (empty($data['name']) || $data['doctor_id'] === 0 || $data['cost'] <= 0.00) {
            Session::setFlash('error', 'All fields are required and cost must be greater than zero.');
            redirect("/admin/ipd/nursing-logs/{$id}");
            return;
        }

        if (Ipd::addProcedure($id, $data)) {
            ActivityLogger::log('IPD Procedure Logged', "Recorded procedure {$data['name']} for admission #{$id}");
            Session::setFlash('success', '✅ Clinical procedure logged successfully.');
        } else {
            Session::setFlash('error', 'Unable to record procedure.');
        }

        redirect("/admin/ipd/nursing-logs/{$id}");
    }

    public function discharge(array $params): void
    {
        Permission::check('manage_ipd');
        $id = (int)($params['id'] ?? 0);

        $discount = (float)($_POST['discount'] ?? 0.00);
        $tax = (float)($_POST['tax'] ?? 0.00);

        if (Ipd::discharge($id, $discount, $tax)) {
            ActivityLogger::log('IPD Patient Discharged', "Discharged patient from admission ID {$id}");
            
            $bill = Billing::getInvoiceByReference('ipd', $id);
            Session::setFlash('success', '✅ Patient successfully discharged. Billing invoice created.');
            
            if ($bill) {
                redirect("/admin/reception/billing/collect/{$bill['id']}");
            } else {
                redirect('/admin/ipd');
            }
        } else {
            Session::setFlash('error', 'Failed to complete discharge operation.');
            redirect("/admin/ipd/nursing-logs/{$id}");
        }
    }
}