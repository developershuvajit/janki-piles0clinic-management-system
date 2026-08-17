<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Models\Appointment;
use App\Models\Patient;
use App\Models\Branch;
use App\Helpers\Session;
use App\Helpers\Security;
use App\Helpers\Permission;
use App\Helpers\Database;
use App\Helpers\ActivityLogger;

class AppointmentController
{
    /**
     * Get branch filter for current user
     */
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

    /**
     * Display a list of all appointments.
     */
    public function index(): void
    {
        $user = Session::user();
        $roleSlug = $user['role_slug'] ?? $user['role'] ?? '';
        
        if (!in_array($roleSlug, ['super_admin', 'admin', 'receptionist'])) {
            Permission::check('manage_appointments');
        }
        
        $filter = $this->getBranchFilter();
        $branchId = $filter['hasFilter'] ? $filter['branchId'] : null;
        
        $appointments = Appointment::all($branchId);
        
        view('admin.appointments.index', [
            'title' => 'Scheduled Appointments',
            'appointments' => $appointments,
            'activePage' => 'appointments'
        ]);
    }

    /**
     * Show Pending Online Bookings for Approval.
     */
    public function pendingList(): void
    {
        $user = Session::user();
        $roleSlug = $user['role_slug'] ?? $user['role'] ?? '';
        
        if (!in_array($roleSlug, ['super_admin', 'admin', 'receptionist'])) {
            Permission::check('manage_appointments');
        }
        
        $filter = $this->getBranchFilter();
        
        $sql = "SELECT a.*, p.name as patient_name, p.patient_id as patient_code, p.phone as patient_phone, 
                       u.username as doctor_name, b.name as branch_name 
                FROM appointments a
                JOIN patients p ON a.patient_id = p.id
                JOIN users u ON a.doctor_id = u.id
                JOIN branches b ON a.branch_id = b.id
                WHERE a.status = 'pending'";
        
        $params = [];
        
        if ($filter['hasFilter']) {
            $sql .= " AND a.branch_id = ?";
            $params[] = $filter['branchId'];
        }
        
        $sql .= " ORDER BY a.date ASC, a.time_slot ASC";
        
        $appointments = Database::all($sql, $params);
        
        view('admin.appointments.pending', [
            'title' => 'Pending Online Approvals',
            'appointments' => $appointments,
            'activePage' => 'appointments_pending'
        ]);
    }

    /**
     * Approve a pending online appointment.
     */
    public function approve(array $params): void
    {
        $user = Session::user();
        $roleSlug = $user['role_slug'] ?? $user['role'] ?? '';
        
        if (!in_array($roleSlug, ['super_admin', 'admin', 'receptionist'])) {
            Permission::check('manage_appointments');
        }
        
        $id = (int)($params['id'] ?? 0);
        $appt = Appointment::find($id);

        if (!$appt) {
            Session::setFlash('error', 'Appointment not found.');
            if ($roleSlug === 'receptionist') {
                redirect('/reception/appointments/pending');
            } else {
                redirect('/admin/appointments/pending');
            }
            return;
        }

        // Receptionist check - শুধু নিজের branch এর appointment approve করতে পারে
        if ($roleSlug === 'receptionist') {
            $userBranchId = $user['branch_id'] ?? null;
            if ($userBranchId && $appt['branch_id'] != $userBranchId) {
                Session::setFlash('error', 'You can only approve appointments from your branch.');
                redirect('/reception/appointments/pending');
                return;
            }
        }

        if (Appointment::updateStatus($id, 'approved')) {
            ActivityLogger::log('Appointment Approved', "Approved appointment token #{$appt['token_number']} for patient {$appt['patient_name']}");
            Session::setFlash('success', '✅ Appointment approved successfully.');
        } else {
            Session::setFlash('error', 'Unable to approve appointment.');
        }

        if ($roleSlug === 'receptionist') {
            redirect('/reception/appointments/pending');
        } else {
            redirect('/admin/appointments/pending');
        }
    }

    /**
     * Cancel an appointment.
     */
    public function cancel(array $params): void
    {
        $user = Session::user();
        $roleSlug = $user['role_slug'] ?? $user['role'] ?? '';
        
        if (!in_array($roleSlug, ['super_admin', 'admin', 'receptionist'])) {
            Permission::check('manage_appointments');
        }
        
        $id = (int)($params['id'] ?? 0);
        $appt = Appointment::find($id);

        if (!$appt) {
            Session::setFlash('error', 'Appointment not found.');
            if ($roleSlug === 'receptionist') {
                redirect('/reception/appointments');
            } else {
                redirect('/admin/appointments');
            }
            return;
        }

        // Receptionist check - শুধু নিজের branch এর appointment cancel করতে পারে
        if ($roleSlug === 'receptionist') {
            $userBranchId = $user['branch_id'] ?? null;
            if ($userBranchId && $appt['branch_id'] != $userBranchId) {
                Session::setFlash('error', 'You can only cancel appointments from your branch.');
                redirect('/reception/appointments');
                return;
            }
        }

        if (Appointment::updateStatus($id, 'cancelled')) {
            ActivityLogger::log('Appointment Cancellation', "Cancelled appointment token #{$appt['token_number']} for patient {$appt['patient_name']}");
            Session::setFlash('success', '✅ Appointment cancelled successfully.');
        } else {
            Session::setFlash('error', 'Unable to cancel appointment.');
        }

        if ($roleSlug === 'receptionist') {
            redirect('/reception/appointments');
        } else {
            redirect('/admin/appointments');
        }
    }

    /**
     * Show Doctor Schedule Configuration panel.
     */
     /**
 * Show Doctor Schedule Configuration panel.
 */
public function schedule(): void
{
    if (!Session::isLoggedIn()) {
        redirect('/login');
    }

    $user = Session::user();
    $userId = (int)($user['id'] ?? 0);
    $roleSlug = Session::get('role_slug') ?? '';
    $branchId = $user['branch_id'] ?? null;
    
    $doctorId = $userId;
    $isSuperAdmin = ($roleSlug === 'super_admin' || $roleSlug === 'admin');
    $isReceptionist = ($roleSlug === 'receptionist');
    
    // Super Admin বা Receptionist ডাক্তার সিলেক্ট করতে পারে
    if (($isSuperAdmin || $isReceptionist) && !empty($_GET['doctor_id'])) {
        $doctorId = (int)$_GET['doctor_id'];
    }
    
    // If doctor role, they can only see their own schedule
    if ($roleSlug === 'doctor') {
        $doctorId = $userId;
    }

    $doctor = Database::row("SELECT id, username, branch_id FROM users WHERE id = :id", ['id' => $doctorId]);
    if (!$doctor) {
        Session::setFlash('error', 'Doctor not found.');
        if ($isReceptionist) {
            redirect('/reception/dashboard');
        } else {
            redirect('/admin/dashboard');
        }
        return;
    }

    // Receptionist check - শুধু নিজের branch এর ডাক্তার দেখতে পারে
    if ($isReceptionist && $branchId && $doctor['branch_id'] != $branchId) {
        Session::setFlash('error', 'You can only view schedules for doctors in your branch.');
        $firstDoctor = Database::row(
            "SELECT id FROM users WHERE branch_id = :branch_id AND status = 'active' AND role_id IN (SELECT id FROM roles WHERE slug = 'doctor') LIMIT 1",
            ['branch_id' => $branchId]
        );
        if ($firstDoctor) {
            redirect('/reception/appointments/schedule?doctor_id=' . $firstDoctor['id']);
        } else {
            redirect('/reception/dashboard');
        }
        return;
    }

    // Get schedules for the selected doctor
    $schedules = Database::all("SELECT * FROM doctor_schedules WHERE doctor_id = :id ORDER BY 
        FIELD(day_of_week, 'Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday')", 
        ['id' => $doctorId]
    );
    
    // Get all doctors for dropdown
    $doctors = [];
    
    // Super Admin - সব ডাক্তার দেখাবে
    if ($isSuperAdmin) {
        $doctors = Database::all(
            "SELECT u.id, u.username, u.branch_id, b.name as branch_name 
             FROM users u
             JOIN roles r ON u.role_id = r.id 
             LEFT JOIN branches b ON u.branch_id = b.id
             WHERE r.slug = 'doctor' AND u.status = 'active'
             ORDER BY u.username ASC"
        );
    } 
    // Receptionist - শুধু নিজের branch এর ডাক্তার দেখাবে
    else if ($isReceptionist) {
        $doctors = Database::all(
            "SELECT u.id, u.username, u.branch_id, b.name as branch_name 
             FROM users u
             JOIN roles r ON u.role_id = r.id 
             LEFT JOIN branches b ON u.branch_id = b.id
             WHERE r.slug = 'doctor' AND u.status = 'active' AND u.branch_id = :branch_id
             ORDER BY u.username ASC",
            ['branch_id' => $branchId]
        );
    } 
    // Doctor - শুধু নিজেকে দেখাবে
    else {
        $doctors = [$doctor];
    }

    // সবাই একই view ব্যবহার করবে (admin.appointments.schedule)
    view('admin.appointments.schedule', [
        'title' => $isReceptionist ? 'Manage Doctor Schedules' : 'Configure Shift Schedules',
        'schedules' => $schedules,
        'selected_doctor' => $doctor,
        'doctors' => $doctors,
        'isSuperAdmin' => $isSuperAdmin,
        'isReceptionist' => $isReceptionist,
        'activePage' => 'appointments'
    ]);
}

    /**
     * Save doctor schedule configurations.
     */
    public function saveSchedule(): void
    {
        if (!Session::isLoggedIn()) {
            redirect('/login');
        }

        $user = Session::user();
        $roleSlug = Session::get('role_slug') ?? '';
        $branchId = $user['branch_id'] ?? null;

        if (!Security::verifyCsrfToken($_POST['csrf_token'] ?? null)) {
            Session::setFlash('error', 'Security token expired.');
            redirect('/admin/appointments/schedule');
        }

        $doctorId = (int)($_POST['doctor_id'] ?? 0);
        if ($doctorId === 0) {
            Session::setFlash('error', 'Invalid doctor selection.');
            redirect('/admin/appointments/schedule');
        }

        // Check if doctor exists
        $doctor = Database::row("SELECT id, branch_id FROM users WHERE id = :id AND status = 'active'", ['id' => $doctorId]);
        if (!$doctor) {
            Session::setFlash('error', 'Doctor not found.');
            redirect('/admin/appointments/schedule');
        }

        // Receptionist check - শুধু নিজের branch এর ডাক্তারের schedule করতে পারে
        if ($roleSlug === 'receptionist') {
            if ($branchId && $doctor['branch_id'] != $branchId) {
                Session::setFlash('error', 'You can only manage schedules for doctors in your branch.');
                redirect('/reception/appointments');
                return;
            }
        }

        $data = [
            'day_of_week' => Security::sanitize($_POST['day_of_week'] ?? 'Monday'),
            'start_time' => Security::sanitize($_POST['start_time'] ?? '09:00:00'),
            'end_time' => Security::sanitize($_POST['end_time'] ?? '17:00:00'),
            'slot_duration' => (int)($_POST['slot_duration'] ?? 15),
            'max_patients' => (int)($_POST['max_patients'] ?? 20),
            'status' => Security::sanitize($_POST['status'] ?? 'active')
        ];

        // Validate times
        if ($data['start_time'] >= $data['end_time']) {
            Session::setFlash('error', 'Start time must be before end time.');
            redirect('/admin/appointments/schedule?doctor_id=' . $doctorId);
        }

        // Check if doctor_schedules table exists
        $tableCheck = Database::row("SHOW TABLES LIKE 'doctor_schedules'");
        if (!$tableCheck) {
            Database::exec("
                CREATE TABLE IF NOT EXISTS doctor_schedules (
                    id INT PRIMARY KEY AUTO_INCREMENT,
                    doctor_id INT NOT NULL,
                    day_of_week VARCHAR(20) NOT NULL,
                    start_time TIME NOT NULL,
                    end_time TIME NOT NULL,
                    slot_duration INT DEFAULT 15,
                    max_patients INT DEFAULT 20,
                    status ENUM('active', 'inactive') DEFAULT 'active',
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                    FOREIGN KEY (doctor_id) REFERENCES users(id) ON DELETE CASCADE,
                    UNIQUE KEY unique_schedule (doctor_id, day_of_week)
                )
            ");
        }

        $result = Appointment::saveDoctorSchedule($doctorId, $data);
        
        if ($result) {
            ActivityLogger::log('Schedule Configuration', "Updated schedule for day: {$data['day_of_week']} (Doctor ID: {$doctorId})");
            Session::setFlash('success', '✅ Doctor schedule configuration saved successfully.');
        } else {
            Session::setFlash('error', 'Unable to save schedule configuration. Please try again.');
        }

        redirect('/admin/appointments/schedule?doctor_id=' . $doctorId);
    }

    /**
     * JSON API Endpoint: Fetch available slots of a doctor on a specific date.
     */
    public function getSlotsAjax(): void
    {
        header('Content-Type: application/json');
        
        $doctorId = (int)($_GET['doctor_id'] ?? 0);
        $date = Security::sanitize($_GET['date'] ?? '');

        if ($doctorId === 0 || empty($date)) {
            echo json_encode(['success' => false, 'slots' => [], 'message' => 'Doctor ID and date required']);
            return;
        }

        // Get day of week
        $dayOfWeek = date('l', strtotime($date));

        // Direct database query to check schedule
        $schedule = Database::row(
            "SELECT * FROM doctor_schedules 
             WHERE doctor_id = :doctor_id AND day_of_week = :day AND status = 'active'",
            ['doctor_id' => $doctorId, 'day' => $dayOfWeek]
        );

        // If no schedule found, return empty
        if (!$schedule) {
            echo json_encode([
                'success' => true, 
                'slots' => [], 
                'message' => 'No schedule found for this doctor on ' . $dayOfWeek
            ]);
            return;
        }

        // Get slots using the model
        $slots = Appointment::getAvailableSlots($doctorId, $date);
        
        echo json_encode([
            'success' => true,
            'slots' => $slots,
            'debug' => [
                'day' => $dayOfWeek,
                'schedule' => $schedule,
                'slots_count' => count($slots)
            ]
        ]);
    }

    /**
     * Guest/Patient facing online booking panel.
     */
    public function showOnlineBooking(): void
    {
        // Clear session data for fresh booking
        Session::remove('last_booking');
        Session::remove('last_booking_id');
        
        $doctors = Database::all(
            "SELECT u.id, u.username, u.branch_id, b.name as branch_name 
             FROM users u
             JOIN roles r ON u.role_id = r.id
             LEFT JOIN branches b ON u.branch_id = b.id
             WHERE r.slug = 'doctor' AND u.status = 'active'
             ORDER BY u.username ASC"
        );
        
        $branches = Branch::all();

        view('website.book_appointment', [
            'title' => 'Book Appointment Online',
            'doctors' => $doctors,
            'branches' => $branches
        ]);
    }

    /**
     * Submit appointment reservation.
     */
    public function submitOnlineBooking(): void
    {
        error_log("=== submitOnlineBooking START ===");
        
        if (!Security::verifyCsrfToken($_POST['csrf_token'] ?? null)) {
            error_log("CSRF validation failed");
            Session::setFlash('error', 'Security validation expired.');
            redirect('/appointments/book');
            return;
        }

        // Get all form data
        $name = Security::sanitize($_POST['name'] ?? '');
        $email = Security::sanitize($_POST['email'] ?? '');
        $phone = Security::sanitize($_POST['phone'] ?? '');
        $gender = Security::sanitize($_POST['gender'] ?? 'male');
        $dob = Security::sanitize($_POST['dob'] ?? '');
        $address = Security::sanitize($_POST['address'] ?? '');
        $doctorId = (int)($_POST['doctor_id'] ?? 0);
        $branchId = (int)($_POST['branch_id'] ?? 0);
        $date = Security::sanitize($_POST['date'] ?? '');
        $timeSlot = Security::sanitize($_POST['time_slot'] ?? '');

        error_log("Form data: name=$name, email=$email, phone=$phone, doctorId=$doctorId, date=$date, timeSlot=$timeSlot");

        // Validate required fields
        $errors = [];
        if (empty($name)) $errors[] = 'Name is required';
        if (empty($email)) $errors[] = 'Email is required';
        if (empty($phone)) $errors[] = 'Phone is required';
        if (empty($dob)) $errors[] = 'Date of birth is required';
        if (empty($doctorId)) $errors[] = 'Doctor selection is required';
        if (empty($date)) $errors[] = 'Date is required';
        if (empty($timeSlot)) $errors[] = 'Time slot is required';
        
        if (!empty($errors)) {
            error_log("Validation errors: " . implode(', ', $errors));
            Session::setFlash('error', implode(', ', $errors));
            redirect('/appointments/book');
            return;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            error_log("Invalid email: $email");
            Session::setFlash('error', 'Please enter a valid email address.');
            redirect('/appointments/book');
            return;
        }

        // Check if slot is still available
        if (!Appointment::checkSlotAvailability($doctorId, $date, $timeSlot)) {
            error_log("Slot not available");
            Session::setFlash('error', 'Selected time slot is no longer available.');
            redirect('/appointments/book');
            return;
        }

        // Find or create patient
        $patient = Database::row(
            "SELECT id FROM patients WHERE phone = :phone OR email = :email",
            ['phone' => $phone, 'email' => $email]
        );
        
        if ($patient) {
            $patientId = (int)$patient['id'];
            error_log("Existing patient found: $patientId");
        } else {
            error_log("Creating new patient...");
            $patientData = [
                'name' => $name,
                'email' => $email,
                'phone' => $phone,
                'gender' => $gender,
                'dob' => $dob,
                'address' => $address,
                'branch_id' => $branchId ?: null,
                'status' => 'active'
            ];
            $patientId = Patient::create($patientData);
            error_log("Patient created with ID: " . ($patientId ?? 'null'));
        }

        if (!$patientId) {
            error_log("Failed to create/find patient");
            Session::setFlash('error', 'Unable to register patient profile.');
            redirect('/appointments/book');
            return;
        }

        // Create appointment
        $apptData = [
            'patient_id' => $patientId,
            'doctor_id' => $doctorId,
            'branch_id' => $branchId ?: 1,
            'date' => $date,
            'time_slot' => $timeSlot,
            'status' => 'pending',
            'type' => 'online',
            'queue_status' => 'waiting'
        ];

        error_log("Creating appointment with data: " . print_r($apptData, true));
        
        $apptId = Appointment::create($apptData);

        $appointment = null;

        if ($apptId) {
            $appointment = Appointment::find((int)$apptId);
        }

        // Fallback: find the appointment that was just created
        if (!$appointment) {
            $appointment = Database::row(
                "SELECT a.*,
                        p.name AS patient_name,
                        u.username AS doctor_name,
                        b.name AS branch_name
                 FROM appointments a
                 JOIN patients p ON a.patient_id = p.id
                 JOIN users u ON a.doctor_id = u.id
                 JOIN branches b ON a.branch_id = b.id
                 WHERE a.patient_id = :patient_id
                   AND a.doctor_id = :doctor_id
                   AND a.date = :date
                   AND a.time_slot = :time_slot
                 ORDER BY a.id DESC
                 LIMIT 1",
                [
                    'patient_id' => $patientId,
                    'doctor_id' => $doctorId,
                    'date' => $date,
                    'time_slot' => $timeSlot
                ]
            );

            if ($appointment) {
                $apptId = (int)$appointment['id'];
            }
        }

        if ($appointment) {
            $bookingDetails = [
                'patient_name' => $name,
                'doctor_name' => $appointment['doctor_name'] ?? 'Doctor',
                'date' => date('d M, Y', strtotime($date)),
                'time_slot' => date('h:i A', strtotime($timeSlot)),
                'token_number' => $appointment['token_number'] ?? 'Pending'
            ];

            Session::set('last_booking', $bookingDetails);

            ActivityLogger::log(
                'Online Booking Submission',
                "Patient {$name} submitted appointment request (Appt ID: {$apptId})."
            );

            Session::setFlash(
                'success',
                '✅ Appointment booked successfully! Token #' .
                ($appointment['token_number'] ?? 'Pending')
            );

            redirect('/appointments/book/success');
            return;
        }

        Session::setFlash(
            'error',
            'Unable to book appointment. Please try again.'
        );

        redirect('/appointments/book');
        return;
    }

    /**
     * Booking success page
     */
    public function bookingSuccess(): void
    {
        $appointmentDetails = Session::get('last_booking') ?? [];
        
        if (empty($appointmentDetails)) {
            error_log("bookingSuccess: No appointment details in session, redirecting to booking page");
            redirect('/appointments/book');
            return;
        }
        
        error_log("bookingSuccess: Displaying success page with details: " . print_r($appointmentDetails, true));
        
        view('website.booking_success', [
            'title' => 'Booking Confirmed',
            'appointmentDetails' => $appointmentDetails
        ]);
    }
}