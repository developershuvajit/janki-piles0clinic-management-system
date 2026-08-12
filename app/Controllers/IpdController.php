<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Models\Ipd;
use App\Models\Patient;
use App\Models\Branch;
use App\Models\Billing;
use App\Models\User;
use App\Helpers\Session;
use App\Helpers\Security;
use App\Helpers\Permission;
use App\Helpers\Database;
use App\Helpers\ActivityLogger;

class IpdController
{
    /**
     * Display a list of all active IPD patients.
     */
    public function index(): void
    {
        Permission::check('manage_ipd');
        
        $branchId = Session::get('branch_id') ? (int)Session::get('branch_id') : null;
        $admissions = Ipd::getActiveAdmissions($branchId);
        $discharged = Ipd::getDischargedHistory($branchId);

        view('admin.ipd.index', [
            'title' => 'Inpatient Department (IPD)',
            'admissions' => $admissions,
            'discharged' => $discharged
        ]);
    }

    /**
     * Show Admission Form.
     */
    public function admitForm(): void
    {
        Permission::check('manage_ipd');
        
        $patients = Patient::all();
        $beds = Ipd::getAvailableBeds();
        $doctors = User::activeDoctors();

        view('admin.ipd.admit', [
            'title' => 'Admit Inpatient',
            'patients' => $patients,
            'beds' => $beds,
            'doctors' => $doctors
        ]);
    }

    /**
     * Save Admission log and occupy bed.
     */
    public function saveAdmission(): void
    {
        Permission::check('manage_ipd');

        Security::requireCsrfToken('/admin/ipd/admit');

        $data = [
            'patient_id' => (int)($_POST['patient_id'] ?? 0),
            'doctor_id' => (int)($_POST['doctor_id'] ?? 0),
            'bed_id' => (int)($_POST['bed_id'] ?? 0),
            'admission_date' => Security::sanitize($_POST['admission_date'] ?? date('Y-m-d H:i:s')),
            'symptoms' => Security::sanitize($_POST['symptoms'] ?? ''),
            'diagnosis' => Security::sanitize($_POST['diagnosis'] ?? '')
        ];

        if ($data['patient_id'] === 0 || $data['doctor_id'] === 0 || $data['bed_id'] === 0 || empty($data['diagnosis'])) {
            Session::setFlash('error', 'Please fill in all required admission details.');
            redirect('/admin/ipd/admit');
        }

        if (Ipd::admit($data)) {
            ActivityLogger::log('IPD Patient Admitted', "Admitted patient ID {$data['patient_id']} to bed ID {$data['bed_id']}");
            Session::setFlash('success', 'Patient admitted successfully. Bed occupied.');
            redirect('/admin/ipd');
        } else {
            Session::setFlash('error', 'Unable to complete admission. The selected bed may have already been occupied.');
            redirect('/admin/ipd/admit');
        }
    }

    /**
     * Show Nursing Charts & logs details.
     */
    public function nursingLogs(array $params): void
    {
        Permission::check('manage_ipd');
        $id = (int)($params['id'] ?? 0);
        $admission = Ipd::findAdmission($id);

        if (!$admission) {
            Session::setFlash('error', 'Admission details not found.');
            redirect('/admin/ipd');
        }

        $logs = Ipd::getNursingLogs($id);
        $procedures = Ipd::getProcedures($id);
        
        // List of doctors for procedurals selector
        $doctors = User::activeDoctors();

        view('admin.ipd.nursing_logs', [
            'title' => 'Clinical Charts - Admission #' . $id,
            'admission' => $admission,
            'logs' => $logs,
            'procedures' => $procedures,
            'doctors' => $doctors
        ]);
    }

    /**
     * Add daily vital sign record.
     */
    public function saveNursingLog(array $params): void
    {
        Permission::check('manage_ipd');
        $id = (int)($params['id'] ?? 0);

        Security::requireCsrfToken("/admin/ipd/nursing-logs/{$id}");

        $data = [
            'temp' => Security::sanitize($_POST['temp'] ?? ''),
            'bp' => Security::sanitize($_POST['bp'] ?? ''),
            'pulse' => Security::sanitize($_POST['pulse'] ?? ''),
            'notes' => Security::sanitize($_POST['notes'] ?? '')
        ];

        if (empty($data['notes'])) {
            Session::setFlash('error', 'Notes/Remarks cannot be empty.');
            redirect("/admin/ipd/nursing-logs/{$id}");
        }

        if (Ipd::addNursingLog($id, $data)) {
            ActivityLogger::log('IPD Vitals Logged', "Recorded nurse vitals details for IPD admission #{$id}");
            Session::setFlash('success', 'Nursing vitals details added.');
        } else {
            Session::setFlash('error', 'Unable to add log.');
        }

        redirect("/admin/ipd/nursing-logs/{$id}");
    }

    /**
     * Save completed procedure cost log.
     */
    public function saveProcedure(array $params): void
    {
        Permission::check('manage_ipd');
        $id = (int)($params['id'] ?? 0);

        Security::requireCsrfToken("/admin/ipd/nursing-logs/{$id}");

        $data = [
            'name' => Security::sanitize($_POST['procedure_name'] ?? ''),
            'doctor_id' => (int)($_POST['doctor_id'] ?? 0),
            'cost' => (float)($_POST['cost'] ?? 0.00)
        ];

        if (empty($data['name']) || $data['doctor_id'] === 0 || $data['cost'] <= 0.00) {
            Session::setFlash('error', 'All fields are required and cost must be greater than zero.');
            redirect("/admin/ipd/nursing-logs/{$id}");
        }

        if (Ipd::addProcedure($id, $data)) {
            ActivityLogger::log('IPD Procedure Logged', "Recorded procedure {$data['name']} for admission #{$id}");
            Session::setFlash('success', 'Clinical procedure logged successfully.');
        } else {
            Session::setFlash('error', 'Unable to record procedure.');
        }

        redirect("/admin/ipd/nursing-logs/{$id}");
    }

    /**
     * Discharge patient and release bed.
     */
    public function discharge(array $params): void
    {
        Permission::check('manage_ipd');
        $id = (int)($params['id'] ?? 0);

        $discount = (float)($_POST['discount'] ?? 0.00);
        $tax = (float)($_POST['tax'] ?? 0.00);

        if (Ipd::discharge($id, $discount, $tax)) {
            ActivityLogger::log('IPD Patient Discharged', "Discharged patient from admission ID {$id}");
            
            // Get billing invoice generated automatically by model
            $bill = Billing::getInvoiceByReference('ipd', $id);
            Session::setFlash('success', 'Patient successfully discharged. Billing invoice created.');
            
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
