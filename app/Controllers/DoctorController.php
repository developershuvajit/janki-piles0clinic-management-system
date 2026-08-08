<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Models\Appointment;
use App\Models\Patient;
use App\Models\Prescription;
use App\Models\Ipd;
use App\Models\Discharge;
use App\Models\Inventory;
use App\Models\Billing;
use App\Helpers\Session;
use App\Helpers\Security;
use App\Helpers\Permission;
use App\Helpers\Database;
use App\Helpers\ActivityLogger;

class DoctorController
{
    /**
     * Display today's consult appointments queue and doctor dashboard.
     */
    public function dashboard(): void
    {
        Permission::checkPortal('doctor');
        
        $doctorId = (int)Session::get('user_id');
        $user = Session::user();
        $branchId = $user['branch_id'] ? (int)$user['branch_id'] : null;
        $date = date('Y-m-d');
        
        $queue = Appointment::getDoctorQueue($doctorId, $date);
        
        // Retrieve statistics aggregates
        $opdTodayCount = Database::row(
            "SELECT COUNT(*) as count FROM appointments WHERE doctor_id = :doc AND date = :date", 
            ['doc' => $doctorId, 'date' => $date]
        )['count'] ?? 0;

        $completedCount = Database::row(
            "SELECT COUNT(*) as count FROM appointments WHERE doctor_id = :doc AND date = :date AND status = 'completed'", 
            ['doc' => $doctorId, 'date' => $date]
        )['count'] ?? 0;
        
        $ipdCount = count(Database::all(
            "SELECT id FROM ipd_admissions WHERE doctor_id = :doc AND status = 'admitted'",
            ['doc' => $doctorId]
        ));

        $pendingDischarges = Database::row(
            "SELECT COUNT(*) as count FROM ipd_admissions WHERE doctor_id = :doc AND status = 'admitted' AND discharge_approval = 'pending'",
            ['doc' => $doctorId]
        )['count'] ?? 0;

        $followUpsCount = Database::row(
            "SELECT COUNT(*) as count FROM prescriptions WHERE doctor_id = :doc AND follow_up_date = :date",
            ['doc' => $doctorId, 'date' => $date]
        )['count'] ?? 0;

        $pendingCount = count(array_filter($queue, function($q) {
            return $q['queue_status'] === 'waiting' || $q['queue_status'] === 'in_consultation';
        }));

        $recentPatients = Database::all(
            "SELECT p.* 
             FROM patients p 
             JOIN (
                 SELECT patient_id, MAX(id) as max_app_id 
                 FROM appointments 
                 WHERE doctor_id = :doc 
                 GROUP BY patient_id
             ) latest_app ON p.id = latest_app.patient_id 
             ORDER BY latest_app.max_app_id DESC LIMIT 5",
            ['doc' => $doctorId]
        );

        view('admin.doctor.dashboard', [
            'title' => 'Doctor Console',
            'queue' => $queue,
            'opd_today' => $opdTodayCount,
            'completed_today' => $completedCount,
            'ipd_admitted' => $ipdCount,
            'pending_discharges' => $pendingDischarges,
            'follow_ups_today' => $followUpsCount,
            'pending_today' => $pendingCount,
            'recent_patients' => $recentPatients
        ]);
    }

    /**
     * Doctor Patient Search & History Directory.
     */
    public function patientsIndex(): void
    {
        Permission::checkPortal('doctor');
        $user = Session::user();
        $branchId = $user['branch_id'] ? (int)$user['branch_id'] : null;

        $patients = Patient::all($branchId);
        view('admin.doctor.patients', [
            'title' => 'Patient Medical Records Directory',
            'patients' => $patients
        ]);
    }

    /**
     * View Patient Medical History, Timeline, Visits, Prescriptions, Read-Only Bills.
     */
    public function patientHistory(array $params): void
    {
        Permission::checkPortal('doctor');
        $id = (int)($params['id'] ?? 0);
        $patient = Patient::find($id);

        if (!$patient) {
            Session::setFlash('error', 'Patient not found.');
            redirect('/doctor/patients');
        }

        $timeline = Patient::getTimeline($id);
        $prescriptions = Prescription::getByPatient($id);
        $bills = Database::all("SELECT * FROM billing WHERE patient_id = :id ORDER BY id DESC", ['id' => $id]);

        view('admin.doctor.patient_history', [
            'title' => 'Patient Clinical Timeline - ' . $patient['name'],
            'patient' => $patient,
            'timeline' => $timeline,
            'prescriptions' => $prescriptions,
            'bills' => $bills
        ]);
    }

    /**
     * OPD Patient Queue view.
     */
    public function opdQueue(): void
    {
        Permission::checkPortal('doctor');
        $doctorId = (int)Session::get('user_id');
        $date = date('Y-m-d');

        $queue = Appointment::getDoctorQueue($doctorId, $date);

        view('admin.doctor.opd_queue', [
            'title' => 'OPD Patient Roster Queue',
            'queue' => $queue
        ]);
    }

    /**
     * Show consultation worksheet with patient timeline history.
     */
    public function consultForm(array $params): void
    {
        Permission::checkPortal('doctor');
        
        $apptId = (int)($params['id'] ?? 0);
        $appt = Appointment::find($apptId);

        if (!$appt || (int)$appt['doctor_id'] !== (int)Session::get('user_id')) {
            Session::setFlash('error', 'Appointment not found or unauthorized.');
            redirect('/doctor');
        }

        Appointment::updateQueueStatus($apptId, 'in_consultation');

        $patientId = (int)$appt['patient_id'];
        $patient = Patient::find($patientId);
        $timeline = Patient::getTimeline($patientId);

        view('admin.doctor.consult', [
            'title' => 'Consult Patient - ' . $appt['patient_name'],
            'appointment' => $appt,
            'patient' => $patient,
            'timeline' => $timeline
        ]);
    }

    /**
     * Save patient prescription and diagnosis details.
     */
    public function saveConsultation(): void
    {
        Permission::checkPortal('doctor');

        if (!Security::verifyCsrfToken($_POST['csrf_token'] ?? null)) {
            Session::setFlash('error', 'Security token expired.');
            redirect('/doctor');
        }

        $apptId = (int)($_POST['appointment_id'] ?? 0);
        $appt = Appointment::find($apptId);

        if (!$appt || (int)$appt['doctor_id'] !== (int)Session::get('user_id')) {
            Session::setFlash('error', 'Appointment validation failed.');
            redirect('/doctor');
        }

        $patientId = (int)$appt['patient_id'];
        $doctorId = (int)Session::get('user_id');

        $data = [
            'appointment_id' => $apptId,
            'patient_id' => $patientId,
            'doctor_id' => $doctorId,
            'symptoms' => Security::sanitize($_POST['symptoms'] ?? ''),
            'diagnosis' => Security::sanitize($_POST['diagnosis'] ?? ''),
            'treatment' => Security::sanitize($_POST['treatment'] ?? ''),
            'advice' => Security::sanitize($_POST['advice'] ?? ''),
            'follow_up_date' => Security::sanitize($_POST['follow_up_date'] ?? '')
        ];

        if (empty($data['symptoms']) || empty($data['diagnosis']) || empty($data['treatment'])) {
            Session::setFlash('error', 'Symptoms, Diagnosis, and Treatment details are required.');
            redirect("/doctor/opd/consult/{$apptId}");
        }

        $prescId = Prescription::create($data);

        if ($prescId) {
            ActivityLogger::log('OPD Consultation Completed', "Completed consultation for patient {$appt['patient_name']} (Prescription ID: {$prescId})");

            $medicines = [];
            if (!empty($_POST['medicines'])) {
                foreach ($_POST['medicines'] as $med) {
                    $medicines[] = [
                        'medicine_name' => Security::sanitize($med['name'] ?? ''),
                        'dosage' => Security::sanitize($med['dosage'] ?? ''),
                        'frequency' => Security::sanitize($med['frequency'] ?? ''),
                        'duration' => Security::sanitize($med['duration'] ?? ''),
                        'instructions' => Security::sanitize($med['instructions'] ?? '')
                    ];
                }
                Prescription::addMedicines($prescId, $medicines);
            }

            Appointment::updateQueueStatus($apptId, 'completed');
            Appointment::updateStatus($apptId, 'completed');

            Session::setFlash('success', 'Consultation saved successfully.');
            redirect('/doctor');
        } else {
            Session::setFlash('error', 'Database error saving prescription details.');
            redirect("/doctor/opd/consult/{$apptId}");
        }
    }

    /**
     * IPD Admitted Patient List.
     */
    public function ipdIndex(): void
    {
        Permission::checkPortal('doctor');
        $doctorId = (int)Session::get('user_id');

        $admissions = Database::all(
            "SELECT a.*, p.name as patient_name, p.patient_id as patient_code, 
                    b.bed_number, r.room_number 
             FROM ipd_admissions a
             JOIN patients p ON a.patient_id = p.id
             JOIN ipd_beds b ON a.bed_id = b.id
             JOIN ipd_rooms r ON b.room_id = r.id
             WHERE a.doctor_id = :doc AND a.status = 'admitted'
             ORDER BY a.admission_date DESC",
            ['doc' => $doctorId]
        );

        view('admin.doctor.ipd_list', [
            'title' => 'My Admitted IPD Patients',
            'admissions' => $admissions
        ]);
    }

    /**
     * Daily Visit Notes & Vitals Review Form.
     */
    public function visitNotesForm(array $params): void
    {
        Permission::checkPortal('doctor');
        $id = (int)($params['id'] ?? 0);
        $admission = Ipd::findAdmission($id);

        if (!$admission || (int)$admission['doctor_id'] !== (int)Session::get('user_id')) {
            Session::setFlash('error', 'Admission record not found or unauthorized.');
            redirect('/doctor/ipd');
        }

        $nursingLogs = Ipd::getNursingLogs($id);

        view('admin.doctor.ipd_visit_notes', [
            'title' => 'Daily Visit Notes - ' . $admission['patient_name'],
            'admission' => $admission,
            'nursing_logs' => $nursingLogs
        ]);
    }

    /**
     * Save Visit Notes.
     */
    public function saveVisitNotes(array $params): void
    {
        Permission::checkPortal('doctor');
        $id = (int)($params['id'] ?? 0);

        if (!Security::verifyCsrfToken($_POST['csrf_token'] ?? null)) {
            Session::setFlash('error', 'Security token expired.');
            redirect("/doctor/ipd/visit-notes/{$id}");
        }

        $data = [
            'temp' => Security::sanitize($_POST['vit_temp'] ?? ''),
            'bp' => Security::sanitize($_POST['vit_bp'] ?? ''),
            'pulse' => Security::sanitize($_POST['vit_pulse'] ?? ''),
            'notes' => Security::sanitize($_POST['notes'] ?? '')
        ];

        if (Ipd::addNursingLog($id, $data)) {
            ActivityLogger::log('Doctor Visit Note', "Recorded daily visit note for admission ID {$id}");
            Session::setFlash('success', 'Visit note recorded successfully.');
        } else {
            Session::setFlash('error', 'Failed saving visit note.');
        }

        redirect("/doctor/ipd/visit-notes/{$id}");
    }

    /**
     * Procedure & Surgery Notes Form.
     */
    public function procedureNotesForm(array $params): void
    {
        Permission::checkPortal('doctor');
        $id = (int)($params['id'] ?? 0);
        $admission = Ipd::findAdmission($id);

        if (!$admission) {
            Session::setFlash('error', 'Admission record not found.');
            redirect('/doctor/ipd');
        }

        $procedures = Ipd::getProcedures($id);

        view('admin.doctor.ipd_procedure_notes', [
            'title' => 'Procedure & Surgery Notes - ' . $admission['patient_name'],
            'admission' => $admission,
            'procedures' => $procedures
        ]);
    }

    /**
     * Save Procedure / Surgery Note.
     */
    public function saveProcedureNotes(array $params): void
    {
        Permission::checkPortal('doctor');
        $id = (int)($params['id'] ?? 0);
        $doctorId = (int)Session::get('user_id');

        if (!Security::verifyCsrfToken($_POST['csrf_token'] ?? null)) {
            Session::setFlash('error', 'Security token expired.');
            redirect("/doctor/ipd/procedure-notes/{$id}");
        }

        $data = [
            'name' => Security::sanitize($_POST['name'] ?? ''),
            'doctor_id' => $doctorId,
            'cost' => (float)($_POST['cost'] ?? 0.00)
        ];

        if (Ipd::addProcedure($id, $data)) {
            ActivityLogger::log('Procedure Note Added', "Added procedure note for admission ID {$id}");
            Session::setFlash('success', 'Procedure / Surgery note recorded.');
        } else {
            Session::setFlash('error', 'Failed adding procedure note.');
        }

        redirect("/doctor/ipd/procedure-notes/{$id}");
    }

    /**
     * Discharge Approval & Summary Module.
     */
    public function dischargeIndex(): void
    {
        Permission::checkPortal('doctor');
        $doctorId = (int)Session::get('user_id');

        $admissions = Database::all(
            "SELECT a.*, p.name as patient_name, p.patient_id as patient_code, 
                    b.bed_number, r.room_number 
             FROM ipd_admissions a
             JOIN patients p ON a.patient_id = p.id
             JOIN ipd_beds b ON a.bed_id = b.id
             JOIN ipd_rooms r ON b.room_id = r.id
             WHERE a.doctor_id = :doc AND a.status = 'admitted'
             ORDER BY a.admission_date DESC",
            ['doc' => $doctorId]
        );

        view('admin.doctor.discharge_list', [
            'title' => 'IPD Discharge Approvals',
            'admissions' => $admissions
        ]);
    }

    /**
     * Approve Discharge Request.
     */
    public function approveDischarge(array $params): void
    {
        Permission::checkPortal('doctor');
        $id = (int)($params['id'] ?? 0);

        Database::execute(
            "UPDATE ipd_admissions SET discharge_approval = 'approved' WHERE id = :id",
            ['id' => $id]
        );

        ActivityLogger::log('Discharge Approved', "Approved IPD discharge for admission ID {$id}");
        Session::setFlash('success', 'Discharge approved. Ready to generate summary & send to reception.');

        redirect("/doctor/discharge/summary/{$id}");
    }

    /**
     * Generate Discharge Summary Form.
     */
    public function dischargeSummaryForm(array $params): void
    {
        Permission::checkPortal('doctor');
        $id = (int)($params['id'] ?? 0);
        $admission = Ipd::findAdmission($id);

        if (!$admission) {
            Session::setFlash('error', 'Admission record not found.');
            redirect('/doctor/discharge');
        }

        $summary = Discharge::find($id);

        view('admin.doctor.discharge_summary_form', [
            'title' => 'Generate Discharge Summary - ' . $admission['patient_name'],
            'admission' => $admission,
            'summary' => $summary
        ]);
    }

    /**
     * Save Discharge Summary.
     */
    public function saveDischargeSummary(): void
    {
        Permission::checkPortal('doctor');

        if (!Security::verifyCsrfToken($_POST['csrf_token'] ?? null)) {
            Session::setFlash('error', 'Security token expired.');
            redirect('/doctor/discharge');
        }

        $admissionId = (int)($_POST['ipd_admission_id'] ?? 0);

        $data = [
            'ipd_admission_id' => $admissionId,
            'diagnosis' => Security::sanitize($_POST['diagnosis'] ?? ''),
            'treatment_summary' => Security::sanitize($_POST['treatment_summary'] ?? ''),
            'procedure_summary' => Security::sanitize($_POST['procedure_summary'] ?? ''),
            'operation_notes' => Security::sanitize($_POST['operation_notes'] ?? ''),
            'advice' => Security::sanitize($_POST['advice'] ?? ''),
            'medicine_advice' => Security::sanitize($_POST['medicine_advice'] ?? ''),
            'diet' => Security::sanitize($_POST['diet'] ?? ''),
            'follow_up_instructions' => Security::sanitize($_POST['follow_up_instructions'] ?? '')
        ];

        if (Discharge::save($data)) {
            Database::execute("UPDATE ipd_admissions SET discharge_approval = 'approved' WHERE id = :id", ['id' => $admissionId]);
            ActivityLogger::log('Discharge Summary Saved', "Saved discharge summary for admission ID {$admissionId}");
            Session::setFlash('success', 'Discharge summary saved. Ready to send to reception.');
            redirect("/doctor/discharge/summary-print/{$admissionId}");
        } else {
            Session::setFlash('error', 'Failed to save discharge summary.');
            redirect("/doctor/discharge/summary/{$admissionId}");
        }
    }

    /**
     * Print Discharge Summary view.
     */
    public function printDischargeSummary(array $params): void
    {
        Permission::checkPortal('doctor');
        $id = (int)($params['id'] ?? 0);
        $summary = Discharge::getPrintData($id);

        if (!$summary) {
            Session::setFlash('error', 'Discharge summary not found.');
            redirect('/doctor/discharge');
        }

        view('admin.doctor.discharge_summary_print', [
            'title' => 'Print Discharge Summary - #' . $summary['id'],
            'summary' => $summary
        ]);
    }

    /**
     * Prescriptions Directory.
     */
    public function prescriptionsIndex(): void
    {
        Permission::checkPortal('doctor');
        $doctorId = (int)Session::get('user_id');

        $prescriptions = Prescription::getByDoctor($doctorId);

        view('admin.doctor.prescriptions', [
            'title' => 'Physician Prescriptions Directory',
            'prescriptions' => $prescriptions
        ]);
    }

    /**
     * Create Prescription Form.
     */
    public function createPrescriptionForm(): void
    {
        Permission::checkPortal('doctor');
        $user = Session::user();
        $branchId = $user['branch_id'] ? (int)$user['branch_id'] : null;

        $patients = Patient::all($branchId);
        $medicines = Inventory::getMedicines();

        view('admin.doctor.prescription_create', [
            'title' => 'Write New Prescription',
            'patients' => $patients,
            'medicines' => $medicines
        ]);
    }

    /**
     * Save Prescription Record.
     */
    public function savePrescription(): void
    {
        Permission::checkPortal('doctor');

        if (!Security::verifyCsrfToken($_POST['csrf_token'] ?? null)) {
            Session::setFlash('error', 'Security token expired.');
            redirect('/doctor/prescriptions/create');
        }

        $patientId = (int)($_POST['patient_id'] ?? 0);
        $doctorId = (int)Session::get('user_id');

        $data = [
            'appointment_id' => null,
            'patient_id' => $patientId,
            'doctor_id' => $doctorId,
            'symptoms' => Security::sanitize($_POST['symptoms'] ?? ''),
            'diagnosis' => Security::sanitize($_POST['diagnosis'] ?? ''),
            'treatment' => Security::sanitize($_POST['treatment'] ?? ''),
            'advice' => Security::sanitize($_POST['advice'] ?? ''),
            'follow_up_date' => Security::sanitize($_POST['follow_up_date'] ?? '')
        ];

        $prescId = Prescription::create($data);

        if ($prescId) {
            if (!empty($_POST['medicines'])) {
                $medicines = [];
                foreach ($_POST['medicines'] as $med) {
                    $medicines[] = [
                        'medicine_name' => Security::sanitize($med['name'] ?? ''),
                        'dosage' => Security::sanitize($med['dosage'] ?? ''),
                        'frequency' => Security::sanitize($med['frequency'] ?? ''),
                        'duration' => Security::sanitize($med['duration'] ?? ''),
                        'instructions' => Security::sanitize($med['instructions'] ?? '')
                    ];
                }
                Prescription::addMedicines($prescId, $medicines);
            }

            ActivityLogger::log('Prescription Created', "Created prescription ID {$prescId} for patient ID {$patientId}");
            Session::setFlash('success', 'Prescription created successfully.');
            redirect('/doctor/prescriptions');
        } else {
            Session::setFlash('error', 'Failed saving prescription.');
            redirect('/doctor/prescriptions/create');
        }
    }

    /**
     * Print Prescription.
     */
    public function printPrescription(array $params): void
    {
        Permission::checkPortal('doctor');
        $id = (int)($params['id'] ?? 0);
        $presc = Prescription::find($id);

        if (!$presc) {
            Session::setFlash('error', 'Prescription not found.');
            redirect('/doctor/prescriptions');
        }

        $medicines = Prescription::getMedicines($id);

        view('admin.doctor.prescription_print', [
            'title' => 'Print Prescription - #' . $presc['id'],
            'prescription' => $presc,
            'medicines' => $medicines
        ]);
    }

    /**
     * Medicine Directory & Stock View (Read Only for Doctor).
     */
    public function medicinesIndex(): void
    {
        Permission::checkPortal('doctor');
        $medicines = Inventory::getMedicines();
        view('admin.inventory.index', [
            'title' => 'Medicine Directory & Stock Search (Read Only)',
            'medicines' => $medicines,
            'is_read_only' => true
        ]);
    }

    /**
     * Read-Only Bill Summary Index for Doctors.
     */
    public function billingSummaryIndex(): void
    {
        Permission::checkPortal('doctor');
        $doctorId = (int)Session::get('user_id');

        $bills = Database::all(
            "SELECT b.*, p.name as patient_name, p.patient_id as patient_code 
             FROM billing b
             JOIN patients p ON b.patient_id = p.id
             ORDER BY b.id DESC LIMIT 50"
        );

        view('admin.doctor.billing_summary', [
            'title' => 'Patient Billing Summaries (Read Only)',
            'bills' => $bills
        ]);
    }

    /**
     * View Single Patient Bill Breakdown (Read Only).
     */
    public function viewBillSummary(array $params): void
    {
        Permission::checkPortal('doctor');
        $id = (int)($params['id'] ?? 0);
        $bill = Billing::find($id);

        if (!$bill) {
            Session::setFlash('error', 'Bill record not found.');
            redirect('/doctor/billing-summary');
        }

        view('admin.doctor.bill_detail', [
            'title' => 'Bill Summary Breakdown - #' . $bill['id'],
            'bill' => $bill
        ]);
    }

    /**
     * Doctor Clinical Reports Dashboard.
     */
    public function reportsDashboard(): void
    {
        Permission::checkPortal('doctor');
        $doctorId = (int)Session::get('user_id');

        $monthlyOpd = Database::row(
            "SELECT COUNT(*) as count FROM appointments WHERE doctor_id = :doc AND MONTH(date) = MONTH(CURRENT_DATE())",
            ['doc' => $doctorId]
        )['count'] ?? 0;

        $monthlyIpd = Database::row(
            "SELECT COUNT(*) as count FROM ipd_admissions WHERE doctor_id = :doc AND MONTH(created_at) = MONTH(CURRENT_DATE())",
            ['doc' => $doctorId]
        )['count'] ?? 0;

        $totalConsultations = Database::row(
            "SELECT COUNT(*) as count FROM prescriptions WHERE doctor_id = :doc",
            ['doc' => $doctorId]
        )['count'] ?? 0;

        view('admin.doctor.reports', [
            'title' => 'Doctor Clinical Activity Reports',
            'monthly_opd' => $monthlyOpd,
            'monthly_ipd' => $monthlyIpd,
            'total_consultations' => $totalConsultations
        ]);
    }

    /**
     * Doctor Profile & Availability Schedule.
     */
    public function profile(): void
    {
        Permission::checkPortal('doctor');
        $userId = (int)Session::get('user_id');
        $user = Database::row("SELECT * FROM users WHERE id = :id", ['id' => $userId]);
        $profile = Database::row("SELECT * FROM doctor_profiles WHERE user_id = :id", ['id' => $userId]);

        view('admin.doctor.profile', [
            'title' => 'My Doctor Profile & Availability',
            'user' => $user,
            'profile' => $profile
        ]);
    }

    /**
     * Update Doctor Profile details.
     */
    public function updateProfile(): void
    {
        Permission::checkPortal('doctor');
        $userId = (int)Session::get('user_id');

        if (!Security::verifyCsrfToken($_POST['csrf_token'] ?? null)) {
            Session::setFlash('error', 'Security token expired.');
            redirect('/doctor/profile');
        }

        $qualification = Security::sanitize($_POST['qualification'] ?? '');
        $experience = Security::sanitize($_POST['experience'] ?? '');
        $specialization = Security::sanitize($_POST['specialization'] ?? '');
        $schedule = Security::sanitize($_POST['availability_schedule'] ?? '');

        $existing = Database::row("SELECT id FROM doctor_profiles WHERE user_id = :id", ['id' => $userId]);
        if ($existing) {
            Database::execute(
                "UPDATE doctor_profiles SET qualification = :q, experience = :e, specialization = :s, availability_schedule = :a WHERE user_id = :uid",
                ['q' => $qualification, 'e' => $experience, 's' => $specialization, 'a' => $schedule, 'uid' => $userId]
            );
        } else {
            Database::execute(
                "INSERT INTO doctor_profiles (user_id, qualification, experience, specialization, availability_schedule) VALUES (:uid, :q, :e, :s, :a)",
                ['uid' => $userId, 'q' => $qualification, 'e' => $experience, 's' => $specialization, 'a' => $schedule]
            );
        }

        $password = $_POST['password'] ?? '';
        $confirm = $_POST['password_confirm'] ?? '';

        if (!empty($password)) {
            if (strlen($password) < 8 || $password !== $confirm) {
                Session::setFlash('error', 'Password must be at least 8 characters and match confirmation.');
                redirect('/doctor/profile');
            }
            $hash = Security::hashPassword($password);
            Database::execute("UPDATE users SET password_hash = :hash WHERE id = :id", ['hash' => $hash, 'id' => $userId]);
        }

        ActivityLogger::log('Doctor Profile Updated', "Updated doctor qualification and availability profile for user ID {$userId}");
        Session::setFlash('success', 'Profile and schedule updated successfully.');
        redirect('/doctor/profile');
    }

    /**
     * AJAX Endpoint for AI Clinical Diagnostics Recommendations.
     */
    public function aiAssistAjax(): void
    {
        Permission::checkPortal('doctor');
        
        $symptoms = $_GET['symptoms'] ?? '';
        $recommendations = \App\Models\AiAssistant::recommend($symptoms);
        
        header('Content-Type: application/json');
        echo json_encode($recommendations);
        exit;
    }
}
