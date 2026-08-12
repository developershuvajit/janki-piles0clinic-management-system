<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Models\Appointment;
use App\Models\Patient;
use App\Models\Branch;
use App\Models\Billing;
use App\Models\Prescription;
use App\Models\Ipd;
use App\Models\Discharge;
use App\Models\Inventory;
use App\Helpers\Session;
use App\Helpers\Security;
use App\Helpers\Permission;
use App\Helpers\Database;
use App\Helpers\ActivityLogger;
use App\Helpers\Upload;

class ReceptionController
{
    /**
     * Display the Receptionist dashboard panel.
     */
    public function dashboard(): void
    {
        Permission::checkPortal('reception');
        
        $user = Session::user();
        $branchId = $user['branch_id'] ? (int)$user['branch_id'] : null;
        $date = date('Y-m-d');
        
        // Retrieve statistics aggregates filtered by branch
        $patientSql = "SELECT COUNT(*) as count FROM patients WHERE DATE(created_at) = :date";
        $pParams = ['date' => $date];
        if ($branchId) {
            $patientSql .= " AND branch_id = :branch_id";
            $pParams['branch_id'] = $branchId;
        }
        $patientsCount = Database::row($patientSql, $pParams)['count'] ?? 0;

        $apptSql = "SELECT COUNT(*) as count FROM appointments WHERE date = :date";
        $aParams = ['date' => $date];
        if ($branchId) {
            $apptSql .= " AND branch_id = :branch_id";
            $aParams['branch_id'] = $branchId;
        }
        $apptsCount = Database::row($apptSql, $aParams)['count'] ?? 0;

        $opdCount = Database::row("SELECT COUNT(*) as count FROM appointments WHERE date = :date AND type = 'walk-in'" . ($branchId ? " AND branch_id = {$branchId}" : ""), ['date' => $date])['count'] ?? 0;
        
        $ipdCount = count(Ipd::getActiveAdmissions($branchId));

        $pendingIssues = count(Prescription::getPendingMedicineIssues($branchId));
        
        $revSql = "SELECT SUM(paid_amount) as total FROM billing WHERE DATE(created_at) = :date AND payment_status = 'paid'";
        $rParams = ['date' => $date];
        if ($branchId) {
            $revSql .= " AND branch_id = :branch_id";
            $rParams['branch_id'] = $branchId;
        }
        $revRow = Database::row($revSql, $rParams);
        $revenue = (float)($revRow['total'] ?? 0.00);

        $pendingBillsCount = Database::row("SELECT COUNT(*) as count FROM billing WHERE payment_status IN ('unpaid', 'partial')" . ($branchId ? " AND branch_id = {$branchId}" : ""))['count'] ?? 0;

        // Fetch active queue
        $qSql = "SELECT a.*, p.name as patient_name, u.username as doctor_name 
                 FROM appointments a 
                 JOIN patients p ON a.patient_id = p.id
                 JOIN users u ON a.doctor_id = u.id
                 WHERE a.date = :date AND a.status = 'approved' AND a.queue_status IN ('waiting', 'in_consultation')";
        $qParams = ['date' => $date];
        if ($branchId) {
            $qSql .= " AND a.branch_id = :branch_id";
            $qParams['branch_id'] = $branchId;
        }
        $qSql .= " ORDER BY a.token_number ASC LIMIT 10";
        
        $queue = Database::all($qSql, $qParams);
        $lowStockMeds = Inventory::getLowStockMedicines();

        view('admin.reception.dashboard', [
            'title' => 'Reception Console',
            'patients_today' => $patientsCount,
            'appointments_today' => $apptsCount,
            'opd_today' => $opdCount,
            'ipd_today' => $ipdCount,
            'pending_dispatches' => $pendingIssues,
            'revenue_today' => $revenue,
            'pending_bills_count' => $pendingBillsCount,
            'active_queue' => $queue,
            'low_stock_meds' => $lowStockMeds
        ]);
    }

    /**
     * Patient Directory Index.
     */
    public function patientsIndex(): void
    {
        Permission::checkPortal('reception');
        $user = Session::user();
        $branchId = $user['branch_id'] ? (int)$user['branch_id'] : null;

        $patients = Patient::all($branchId);
        view('admin.patients.index', [
            'title' => 'Patient Directory',
            'patients' => $patients
        ]);
    }

    /**
     * Register New Patient Form.
     */
    public function createPatientForm(): void
    {
        Permission::checkPortal('reception');
        $branches = Branch::all();
        view('admin.patients.create', [
            'title' => 'Register New Patient',
            'branches' => $branches
        ]);
    }

    /**
     * Save New Patient Record.
     */
    public function savePatient(): void
    {
        Permission::checkPortal('reception');

        if (!Security::verifyCsrfToken($_POST['csrf_token'] ?? null)) {
            Session::setFlash('error', 'Security token validation failed.');
            redirect('/reception/patients/create');
        }

        $user = Session::user();
        $userBranch = $user['branch_id'] ? (int)$user['branch_id'] : null;
        $postBranch = !empty($_POST['branch_id']) ? (int)$_POST['branch_id'] : 1;
        $branchId = $userBranch ?? $postBranch;

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
            'branch_id' => $branchId
        ];

        if (empty($data['name']) || empty($data['phone']) || empty($data['dob']) || empty($data['address'])) {
            Session::setFlash('error', 'Name, Phone, DOB, and Address are required.');
            redirect('/reception/patients/create');
        }

        $patientId = Patient::create($data);

        if ($patientId) {
            ActivityLogger::log('Patient Registered', "Registered new patient {$data['name']} (ID: {$patientId})");
            Session::setFlash('success', 'Patient registered successfully.');
            redirect('/reception/patients');
        } else {
            Session::setFlash('error', 'Failed to register patient record.');
            redirect('/reception/patients/create');
        }
    }

    /**
     * Edit Patient Record.
     */
    public function editPatientForm(array $params): void
    {
        Permission::checkPortal('reception');
        $id = (int)($params['id'] ?? 0);
        $patient = Patient::find($id);

        if (!$patient) {
            Session::setFlash('error', 'Patient not found.');
            redirect('/reception/patients');
        }

        $branches = Branch::all();
        view('admin.patients.edit', [
            'title' => 'Edit Patient - ' . $patient['name'],
            'patient' => $patient,
            'branches' => $branches
        ]);
    }

    /**
     * Update Patient Details.
     */
    public function updatePatient(array $params): void
    {
        Permission::checkPortal('reception');
        $id = (int)($params['id'] ?? 0);

        if (!Security::verifyCsrfToken($_POST['csrf_token'] ?? null)) {
            Session::setFlash('error', 'Security token expired.');
            redirect("/reception/patients/edit/{$id}");
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
            'family_history' => Security::sanitize($_POST['family_history'] ?? '')
        ];

        if (Patient::update($id, $data)) {
            ActivityLogger::log('Patient Updated', "Updated profile details for patient ID {$id}");
            Session::setFlash('success', 'Patient record updated successfully.');
            redirect('/reception/patients');
        } else {
            Session::setFlash('error', 'Error updating patient record.');
            redirect("/reception/patients/edit/{$id}");
        }
    }

    /**
     * View Patient Medical History.
     */
    public function patientHistory(array $params): void
    {
        Permission::checkPortal('reception');
        $id = (int)($params['patientId'] ?? $params['id'] ?? 0);
        $patient = Patient::find($id);

        if (!$patient) {
            Session::setFlash('error', 'Patient record not found.');
            redirect('/reception/patients');
        }

        $timeline = Patient::getTimeline($id);
        $documents = Patient::getDocuments($id);

        view('admin.patients.history', [
            'title' => 'Patient History - ' . $patient['name'],
            'patient' => $patient,
            'timeline' => $timeline,
            'documents' => $documents
        ]);
    }

    /**
     * Upload Patient Document.
     */
    public function uploadPatientDoc(array $params): void
    {
        Permission::checkPortal('reception');
        $patientId = (int)($params['id'] ?? 0);

        if (!empty($_FILES['document']['name'])) {
            $uploaded = Upload::file($_FILES['document'], 'uploads/patients');
            if ($uploaded) {
                $docName = Security::sanitize($_POST['document_name'] ?? $_FILES['document']['name']);
                Patient::addDocument($patientId, $docName, $uploaded);
                ActivityLogger::log('Patient Document Uploaded', "Uploaded document for patient ID {$patientId}");
                Session::setFlash('success', 'Document uploaded successfully.');
            } else {
                Session::setFlash('error', 'Failed to upload document file.');
            }
        }
        redirect("/reception/patients/history/{$patientId}");
    }

    /**
     * Show Book Walk-in Appointment form.
     */
    public function showWalkInForm(): void
    {
        Permission::checkPortal('reception');
        $user = Session::user();
        $branchId = $user['branch_id'] ? (int)$user['branch_id'] : null;

        $docSql = "SELECT u.id, u.username, b.name as branch_name 
                   FROM users u
                   JOIN roles r ON u.role_id = r.id
                   LEFT JOIN branches b ON u.branch_id = b.id
                   WHERE r.slug = 'doctor' AND u.status = 'active'";
        if ($branchId) {
            $docSql .= " AND u.branch_id = {$branchId}";
        }

        $doctors = Database::all($docSql);
        $branches = Branch::all();
        $patients = Patient::all($branchId);

        view('admin.reception.walk_in', [
            'title' => 'Register Walk-In Patient',
            'doctors' => $doctors,
            'branches' => $branches,
            'patients' => $patients
        ]);
    }

    /**
     * Save Walk-in booking and invoice initial OPD consultation bill.
     */
    public function saveWalkIn(): void
    {
        Permission::checkPortal('reception');

        if (!Security::verifyCsrfToken($_POST['csrf_token'] ?? null)) {
            Session::setFlash('error', 'Security token expired.');
            redirect('/reception/walk-in');
        }

        $patientId = (int)($_POST['patient_id'] ?? 0);
        $doctorId = (int)($_POST['doctor_id'] ?? 0);
        $user = Session::user();
        $branchId = $user['branch_id'] ? (int)$user['branch_id'] : (!empty($_POST['branch_id']) ? (int)$_POST['branch_id'] : 1);
        $consultationFee = (float)($_POST['consultation_fee'] ?? 500.00);

        if ($patientId === 0 || $doctorId === 0) {
            Session::setFlash('error', 'Please select a valid patient and doctor.');
            redirect('/reception/walk-in');
        }

        $date = date('Y-m-d');
        $timeSlot = date('H:i:s');

        $apptData = [
            'patient_id' => $patientId,
            'doctor_id' => $doctorId,
            'branch_id' => $branchId,
            'date' => $date,
            'time_slot' => $timeSlot,
            'status' => 'approved',
            'type' => 'walk-in',
            'queue_status' => 'waiting'
        ];

        $apptId = Appointment::create($apptData);

        if ($apptId) {
            ActivityLogger::log('Walk-In Booking', "Registered walk-in appointment (ID: {$apptId}) for patient ID {$patientId}");

            $billData = [
                'patient_id' => $patientId,
                'branch_id' => $branchId,
                'type' => 'opd',
                'reference_id' => $apptId,
                'subtotal' => $consultationFee,
                'discount' => 0.00,
                'tax' => 0.00,
                'total' => $consultationFee,
                'paid_amount' => 0.00,
                'payment_status' => 'unpaid',
                'payment_method' => 'none'
            ];

            Billing::createBilling($billData);

            Session::setFlash('success', 'Walk-in ticket created. Token assigned.');
            redirect('/reception/queues');
        } else {
            Session::setFlash('error', 'Error: Doctor daily schedule slots limit exceeded.');
            redirect('/reception/walk-in');
        }
    }

    /**
     * Show real-time doctor queue logs.
     */
    public function queuesList(): void
    {
        Permission::checkPortal('reception');
        $user = Session::user();
        $branchId = $user['branch_id'] ? (int)$user['branch_id'] : null;

        $date = date('Y-m-d');
        $sql = "SELECT a.*, p.name as patient_name, p.patient_id as patient_code, 
                       u.username as doctor_name, b.name as branch_name 
                FROM appointments a
                JOIN patients p ON a.patient_id = p.id
                JOIN users u ON a.doctor_id = u.id
                JOIN branches b ON a.branch_id = b.id
                WHERE a.date = :date AND a.status = 'approved'";
        $params = ['date' => $date];

        if ($branchId) {
            $sql .= " AND a.branch_id = :branch_id";
            $params['branch_id'] = $branchId;
        }

        $sql .= " ORDER BY u.username ASC, a.token_number ASC";
        
        $queues = Database::all($sql, $params);
        view('admin.reception.queues', [
            'title' => 'Roster Token Queues',
            'queues' => $queues
        ]);
    }

    /**
     * Change queue state (waiting -> in_consultation -> completed).
     */
    public function updateQueue(array $params): void
    {
        Permission::checkPortal('reception');
        $id = (int)($params['id'] ?? 0);
        $status = Security::sanitize($_GET['status'] ?? 'waiting');

        if ($id > 0) {
            $updated = Appointment::updateQueueStatus($id, $status);
            if ($updated && $status === 'completed') {
                $updated = Appointment::updateStatus($id, 'completed');
            }

            if ($updated) {
                Session::setFlash('success', 'Queue position updated successfully.');
            } else {
                Session::setFlash('error', 'Failed to update the queue position.');
            }
        }

        redirect('/reception/queues');
    }

    /**
     * IPD Admissions Roster view.
     */
    public function ipdIndex(): void
    {
        Permission::checkPortal('reception');
        $user = Session::user();
        $branchId = $user['branch_id'] ? (int)$user['branch_id'] : null;

        $admissions = Ipd::getActiveAdmissions($branchId);
        $discharged = Ipd::getDischargedHistory($branchId);

        view('admin.ipd.index', [
            'title' => 'IPD Ward Admissions',
            'admissions' => $admissions,
            'discharged' => $discharged
        ]);
    }

    /**
     * IPD Admission Form.
     */
    public function ipdAdmitForm(): void
    {
        Permission::checkPortal('reception');
        $user = Session::user();
        $branchId = $user['branch_id'] ? (int)$user['branch_id'] : null;

        $patients = Patient::all($branchId);
        $doctors = Database::all(
            "SELECT u.id, u.username FROM users u JOIN roles r ON u.role_id = r.id WHERE r.slug = 'doctor' AND u.status = 'active'"
        );
        $beds = Ipd::getAvailableBeds();

        view('admin.ipd.admit', [
            'title' => 'Admit Inpatient to Bed',
            'patients' => $patients,
            'doctors' => $doctors,
            'beds' => $beds
        ]);
    }

    /**
     * Save IPD Admission.
     */
    public function saveIpdAdmission(): void
    {
        Permission::checkPortal('reception');

        if (!Security::verifyCsrfToken($_POST['csrf_token'] ?? null)) {
            Session::setFlash('error', 'Security token expired.');
            redirect('/reception/ipd/admit');
        }

        $data = [
            'patient_id' => (int)($_POST['patient_id'] ?? 0),
            'doctor_id' => (int)($_POST['doctor_id'] ?? 0),
            'bed_id' => (int)($_POST['bed_id'] ?? 0),
            'admission_date' => Security::sanitize($_POST['admission_date'] ?? date('Y-m-d H:i:s')),
            'symptoms' => Security::sanitize($_POST['symptoms'] ?? ''),
            'diagnosis' => Security::sanitize($_POST['diagnosis'] ?? '')
        ];

        if (Ipd::admit($data)) {
            ActivityLogger::log('IPD Admission', "Admitted patient ID {$data['patient_id']} to bed ID {$data['bed_id']}");
            Session::setFlash('success', 'Patient admitted to ward bed successfully.');
            redirect('/reception/ipd');
        } else {
            Session::setFlash('error', 'Error processing bed admission. Bed may already be occupied.');
            redirect('/reception/ipd/admit');
        }
    }

    /**
     * View IPD Rooms and Beds grid.
     */
    public function ipdBedsView(): void
    {
        Permission::checkPortal('reception');
        $rooms = Ipd::getRooms();
        $beds = Database::all(
            "SELECT b.*, r.room_number, r.type as room_type, r.price_per_day 
             FROM ipd_beds b JOIN ipd_rooms r ON b.room_id = r.id 
             ORDER BY r.room_number ASC, b.bed_number ASC"
        );

        view('admin.ipd.beds', [
            'title' => 'Room & Bed Allocation Matrix',
            'rooms' => $rooms,
            'beds' => $beds
        ]);
    }

    /**
     * Display unpaid or partially paid billing invoices.
     */
    public function billingIndex(): void
    {
        Permission::checkPortal('reception');
        $user = Session::user();
        $branchId = $user['branch_id'] ? (int)$user['branch_id'] : null;

        $sql = "SELECT b.*, p.name as patient_name, p.patient_id as patient_code 
                FROM billing b
                JOIN patients p ON b.patient_id = p.id
                WHERE 1=1";
        $params = [];

        if ($branchId) {
            $sql .= " AND b.branch_id = :branch_id";
            $params['branch_id'] = $branchId;
        }

        $sql .= " ORDER BY b.id DESC";

        $bills = Database::all($sql, $params);
        view('admin.reception.billing', [
            'title' => 'Cashier Billing & Payments',
            'bills' => $bills
        ]);
    }

    /**
     * Show Invoice Collection page.
     */
    public function collectForm(array $params): void
    {
        Permission::checkPortal('reception');
        $id = (int)($params['id'] ?? 0);
        $bill = Billing::find($id);

        if (!$bill) {
            Session::setFlash('error', 'Invoice not found.');
            redirect('/reception/billing');
        }

        view('admin.reception.collect', [
            'title' => 'Process Bill Payment - #' . $bill['id'],
            'bill' => $bill
        ]);
    }

    /**
     * Complete payment transaction.
     */
    public function processPayment(): void
    {
        Permission::checkPortal('reception');

        if (!Security::verifyCsrfToken($_POST['csrf_token'] ?? null)) {
            Session::setFlash('error', 'Security token expired.');
            redirect('/reception/billing');
        }

        $billId = (int)($_POST['bill_id'] ?? 0);
        $method = Security::sanitize($_POST['payment_method'] ?? 'cash');
        
        $bill = Billing::find($billId);
        if (!$bill) {
            Session::setFlash('error', 'Invoice not found.');
            redirect('/reception/billing');
        }

        $paidAmount = (float)($bill['total']);

        $paymentData = [
            'paid_amount' => $paidAmount,
            'payment_status' => 'paid',
            'payment_method' => $method
        ];

        if (Billing::recordPayment($billId, $paymentData)) {
            ActivityLogger::log('Payment Collected', "Collected ₹{$paidAmount} for bill #{$billId} via {$method}");
            Session::setFlash('success', 'Payment recorded successfully.');
            redirect("/reception/billing/receipt/{$billId}");
        } else {
            Session::setFlash('error', 'Unable to complete payment transaction.');
            redirect("/reception/billing/collect/{$billId}");
        }
    }

    /**
     * Show HTML invoice receipt formatted for print view.
     */
    public function receiptPrint(array $params): void
    {
        Permission::checkPortal('reception');
        $id = (int)($params['id'] ?? 0);
        $bill = Billing::find($id);

        if (!$bill) {
            Session::setFlash('error', 'Receipt not found.');
            redirect('/reception/billing');
        }

        view('admin.reception.receipt', [
            'title' => 'Print Bill Receipt - #' . $bill['id'],
            'bill' => $bill
        ]);
    }

    /**
     * Refund Entry Form.
     */
    public function refundForm(array $params): void
    {
        Permission::checkPortal('reception');
        $id = (int)($params['id'] ?? 0);
        $bill = Billing::find($id);

        if (!$bill) {
            Session::setFlash('error', 'Bill not found.');
            redirect('/reception/billing');
        }

        view('admin.billing.refund', [
            'title' => 'Process Refund Entry - #' . $bill['id'],
            'bill' => $bill
        ]);
    }

    /**
     * Save Refund Process.
     */
    public function processRefund(): void
    {
        Permission::checkPortal('reception');

        if (!Security::verifyCsrfToken($_POST['csrf_token'] ?? null)) {
            Session::setFlash('error', 'Security token expired.');
            redirect('/reception/billing');
        }

        $billId = (int)($_POST['bill_id'] ?? 0);
        $refundAmount = (float)($_POST['refund_amount'] ?? 0.00);
        $reason = Security::sanitize($_POST['refund_reason'] ?? '');

        $sql = "UPDATE billing SET refunded_amount = :refund, refund_reason = :reason, payment_status = 'refunded' WHERE id = :id";
        if (Database::execute($sql, ['refund' => $refundAmount, 'reason' => $reason, 'id' => $billId])) {
            ActivityLogger::log('Payment Refunded', "Issued refund of ₹{$refundAmount} for bill #{$billId}. Reason: {$reason}");
            Session::setFlash('success', 'Refund processed successfully.');
        } else {
            Session::setFlash('error', 'Failed processing refund.');
        }

        redirect('/reception/billing');
    }

    /**
     * Show pending medicine issues from prescriptions.
     */
    public function medicineDispatchIndex(): void
    {
        Permission::checkPortal('reception');
        $user = Session::user();
        $branchId = $user['branch_id'] ? (int)$user['branch_id'] : null;

        $pending = Prescription::getPendingMedicineIssues($branchId);

        view('admin.reception.medicine_dispatch', [
            'title' => 'Dispense Prescribed Medicines',
            'pending' => $pending
        ]);
    }

    /**
     * Mark prescribed medicine as issued.
     */
    public function dispatchMedicine(array $params): void
    {
        Permission::checkPortal('reception');
        $id = (int)($params['id'] ?? 0);

        if ($id > 0) {
            Prescription::markMedicineAsIssued($id);
            ActivityLogger::log('Medicine Dispatch', "Dispensed prescription medicine (ID: {$id})");
            Session::setFlash('success', 'Medicine marked as issued.');
        }

        redirect('/reception/medicine-issue');
    }

    /**
     * View Medicine Inventory Stock (Read Only for Reception).
     */
    public function medicinesIndex(): void
    {
        Permission::checkPortal('reception');
        $medicines = Inventory::getMedicines();
        view('admin.inventory.index', [
            'title' => 'Medicine Stock View (Read Only)',
            'medicines' => $medicines,
            'is_read_only' => true
        ]);
    }

    /**
     * Low Medicine Stock Alerts.
     */
    public function lowStockMedicines(): void
    {
        Permission::checkPortal('reception');
        $lowStock = Inventory::getLowStockMedicines();
        view('admin.inventory.low_stock', [
            'title' => 'Low Medicine Stock Alert',
            'low_stock' => $lowStock,
            'is_read_only' => true
        ]);
    }

    /**
     * Discharge Processing Console.
     */
    public function dischargeIndex(): void
    {
        Permission::checkPortal('reception');
        $user = Session::user();
        $branchId = $user['branch_id'] ? (int)$user['branch_id'] : null;

        $approvedSql = "SELECT a.*, p.name as patient_name, p.patient_id as patient_code, 
                               u.username as doctor_name, b.bed_number, r.room_number 
                        FROM ipd_admissions a
                        JOIN patients p ON a.patient_id = p.id
                        JOIN users u ON a.doctor_id = u.id
                        JOIN ipd_beds b ON a.bed_id = b.id
                        JOIN ipd_rooms r ON b.room_id = r.id
                        WHERE a.status = 'admitted' AND a.discharge_approval = 'approved'";
        $params = [];
        if ($branchId) {
            $approvedSql .= " AND p.branch_id = :branch_id";
            $params['branch_id'] = $branchId;
        }

        $approvedDischarges = Database::all($approvedSql, $params);

        view('admin.ipd.discharge_list', [
            'title' => 'Doctor Approved Discharge List',
            'approved' => $approvedDischarges
        ]);
    }

    /**
     * Complete Final Billing and Patient Checkout.
     */
    public function completeCheckout(array $params): void
    {
        Permission::checkPortal('reception');
        $admissionId = (int)($params['id'] ?? 0);

        if (Ipd::discharge($admissionId)) {
            ActivityLogger::log('Patient Discharged', "Completed IPD discharge and generated final invoice for admission ID {$admissionId}");
            Session::setFlash('success', 'Patient checkout completed. Final invoice generated.');
        } else {
            Session::setFlash('error', 'Error completing patient checkout.');
        }

        redirect('/reception/discharge');
    }

    /**
     * Display today's collections financial splits report.
     */
    public function collectionsReport(): void
    {
        Permission::checkPortal('reception');
        $user = Session::user();
        $branchId = $user['branch_id'] ? (int)$user['branch_id'] : null;

        $report = Billing::getTodayCollectionsReport($branchId);

        view('admin.reception.report', [
            'title' => 'Daily Branch Collections & Reports',
            'report' => $report
        ]);
    }

    /**
     * Reports Dashboard view.
     */
    public function reportsDashboard(): void
    {
        Permission::checkPortal('reception');
        $user = Session::user();
        $branchId = $user['branch_id'] ? (int)$user['branch_id'] : null;

        $report = Billing::getTodayCollectionsReport($branchId);

        view('admin.reception.report', [
            'title' => 'Branch Operations & Revenue Reports',
            'report' => $report
        ]);
    }

    /**
     * My Profile View.
     */
    public function profile(): void
    {
        Permission::checkPortal('reception');
        $user = Session::user();
        $dbUser = Database::row("SELECT * FROM users WHERE id = :id", ['id' => (int)$user['id']]);

        view('admin.profile', [
            'title' => 'My Profile & Account Security',
            'user' => $dbUser
        ]);
    }

    /**
     * Update Profile / Change Password.
     */
    public function updateProfile(): void
    {
        Permission::checkPortal('reception');
        $userId = (int)Session::get('user_id');

        if (!Security::verifyCsrfToken($_POST['csrf_token'] ?? null)) {
            Session::setFlash('error', 'Security validation failed.');
            redirect('/reception/profile');
        }

        $password = $_POST['password'] ?? '';
        $confirm = $_POST['password_confirm'] ?? '';

        if (!empty($password)) {
            if (strlen($password) < 8) {
                Session::setFlash('error', 'New password must be at least 8 characters long.');
                redirect('/reception/profile');
            }
            if ($password !== $confirm) {
                Session::setFlash('error', 'Passwords do not match.');
                redirect('/reception/profile');
            }

            $hash = Security::hashPassword($password);
            Database::execute("UPDATE users SET password_hash = :hash WHERE id = :id", ['hash' => $hash, 'id' => $userId]);
            ActivityLogger::log('Password Updated', "User ID {$userId} updated their password.");
        } else {
            Session::setFlash('info', 'Profile information updated.');
        }

        redirect('/reception/profile');
    }

    /**
     * Follow-up Management Dashboard.
     */
    public function followupsIndex(): void
    {
        Permission::checkPortal('reception');
        $user = Session::user();
        $branchId = $user['branch_id'] ? (int)$user['branch_id'] : null;

        $tab = $_GET['tab'] ?? 'due';
        $followups = \App\Models\Followup::getList($branchId, $tab);
        $metrics = \App\Models\Followup::getMetrics($branchId);

        view('admin.reception.followups', [
            'title' => 'Patient Follow-up Management Tracker',
            'active_tab' => $tab,
            'followups' => $followups,
            'metrics' => $metrics
        ]);
    }

    /**
     * CRM Lead Management Pipeline.
     */
    public function leadsIndex(): void
    {
        Permission::checkPortal('reception');
        $user = Session::user();
        $branchId = $user['branch_id'] ? (int)$user['branch_id'] : null;

        $status = $_GET['status'] ?? 'all';
        $search = $_GET['search'] ?? null;

        $leads = \App\Models\Lead::all($branchId, $status, $search);
        $counts = \App\Models\Lead::getStatusCounts($branchId);

        view('admin.reception.leads', [
            'title' => 'CRM Lead Inquiries & Pipeline',
            'active_status' => $status,
            'leads' => $leads,
            'counts' => $counts
        ]);
    }

    /**
     * Save New Lead Inquiry.
     */
    public function saveLead(): void
    {
        Permission::checkPortal('reception');
        $user = Session::user();
        $branchId = $user['branch_id'] ? (int)$user['branch_id'] : null;

        if (!Security::verifyCsrfToken($_POST['csrf_token'] ?? null)) {
            Session::setFlash('error', 'Security token expired.');
            redirect('/reception/leads');
        }

        $data = [
            'branch_id' => $branchId,
            'name' => $_POST['name'] ?? '',
            'phone' => $_POST['phone'] ?? '',
            'email' => $_POST['email'] ?? '',
            'source' => $_POST['source'] ?? 'Walk-In',
            'status' => 'new',
            'follow_up_date' => $_POST['follow_up_date'] ?? null,
            'notes' => $_POST['notes'] ?? ''
        ];

        if (empty($data['name']) || empty($data['phone'])) {
            Session::setFlash('error', 'Lead Name and Mobile Number are required.');
            redirect('/reception/leads');
        }

        $leadId = \App\Models\Lead::create($data);
        ActivityLogger::log('Lead Inquiry Created', "New lead #{$leadId} registered for {$data['name']} ({$data['phone']})");
        Session::setFlash('success', "Lead inquiry for {$data['name']} registered successfully!");
        redirect('/reception/leads');
    }

    /**
     * Update Lead Status via GET/POST.
     */
    public function updateLeadStatus(array $params): void
    {
        Permission::checkPortal('reception');
        $id = (int)($params['id'] ?? 0);
        $status = Security::sanitize($_GET['status'] ?? $_POST['status'] ?? 'contacted');
        $notes = Security::sanitize($_GET['notes'] ?? $_POST['notes'] ?? null);

        if ($id > 0) {
            if (\App\Models\Lead::updateStatus($id, $status, $notes)) {
                ActivityLogger::log('Lead Status Updated', "Lead #{$id} status updated to '{$status}'");
                Session::setFlash('success', 'Lead status updated.');
            } else {
                Session::setFlash('error', 'Failed to update the lead status.');
            }
        }

        redirect('/reception/leads');
    }

    /**
     * WhatsApp & SMS Communication Center.
     */
    public function communicationIndex(): void
    {
        Permission::checkPortal('reception');
        $templates = \App\Models\Communication::getTemplates();
        $logs = \App\Models\Communication::getLogs(30);

        view('admin.reception.communication', [
            'title' => 'WhatsApp & SMS Communication Center',
            'templates' => $templates,
            'logs' => $logs
        ]);
    }

    /**
     * Daily Staff Attendance Register.
     */
    public function attendanceIndex(): void
    {
        Permission::checkPortal('reception');
        $user = Session::user();
        $branchId = $user['branch_id'] ? (int)$user['branch_id'] : null;

        $date = $_GET['date'] ?? date('Y-m-d');
        $roster = \App\Models\Attendance::getDailyRoster($date, $branchId);
        $summary = \App\Models\Attendance::getTodaySummary($branchId);

        view('admin.reception.attendance', [
            'title' => 'Daily Staff Attendance Register',
            'date' => $date,
            'roster' => $roster,
            'summary' => $summary
        ]);
    }

    /**
     * Save Staff Attendance Entry.
     */
    public function markAttendance(): void
    {
        Permission::checkPortal('reception');
        $user = Session::user();
        $branchId = $user['branch_id'] ? (int)$user['branch_id'] : null;

        if (!Security::verifyCsrfToken($_POST['csrf_token'] ?? null)) {
            Session::setFlash('error', 'Security token expired.');
            redirect('/reception/attendance');
        }

        $userId = (int)($_POST['user_id'] ?? 0);
        $date = $_POST['date'] ?? date('Y-m-d');
        $status = $_POST['status'] ?? 'present';
        $checkIn = $_POST['check_in'] ?? null;
        $checkOut = $_POST['check_out'] ?? null;
        $notes = $_POST['notes'] ?? '';

        if ($userId > 0) {
            \App\Models\Attendance::mark($userId, $date, $status, $checkIn, $checkOut, $notes, $branchId);
            ActivityLogger::log('Attendance Marked', "Marked attendance status '{$status}' for User ID {$userId} on {$date}");
            Session::setFlash('success', 'Attendance record updated.');
        }

        redirect('/reception/attendance?date=' . urlencode($date));
    }

    /**
     * Global Quick Search Endpoint (AJAX).
     */
    public function globalSearchAjax(): void
    {
        Permission::checkPortal('reception');
        $q = trim($_GET['q'] ?? '');

        if (strlen($q) < 2) {
            jsonResponse(['patients' => [], 'appointments' => [], 'leads' => []]);
        }

        $patients = Database::all(
            "SELECT id, patient_id as code, name, phone, email, gender, dob 
             FROM patients 
             WHERE name LIKE :q OR phone LIKE :q OR patient_id LIKE :q 
             LIMIT 5",
            ['q' => '%' . $q . '%']
        );

        $appointments = Database::all(
            "SELECT a.id, a.token_number, a.date, a.status, p.name as patient_name, u.username as doctor_name 
             FROM appointments a 
             JOIN patients p ON a.patient_id = p.id 
             JOIN users u ON a.doctor_id = u.id 
             WHERE p.name LIKE :q OR p.phone LIKE :q OR a.id = :id 
             LIMIT 5",
            ['q' => '%' . $q . '%', 'id' => is_numeric($q) ? (int)$q : 0]
        );

        $leads = Database::all(
            "SELECT id, name, phone, source, status 
             FROM leads 
             WHERE name LIKE :q OR phone LIKE :q 
             LIMIT 5",
            ['q' => '%' . $q . '%']
        );

        jsonResponse([
            'patients' => $patients,
            'appointments' => $appointments,
            'leads' => $leads
        ]);
    }
}

