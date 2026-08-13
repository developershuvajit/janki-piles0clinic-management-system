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
        Permission::check('manage_appointments');
        
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
     * Show Pending Online Bookings for Admin Approval.
     */
    public function pendingList(): void
    {
        Permission::check('manage_appointments');
        
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
        Permission::check('manage_appointments');
        $id = (int)($params['id'] ?? 0);
        $appt = Appointment::find($id);

        if (!$appt) {
            Session::setFlash('error', 'Appointment not found.');
            redirect('/admin/appointments/pending');
        }

        if (Appointment::updateStatus($id, 'approved')) {
            ActivityLogger::log('Appointment Approved', "Approved appointment token #{$appt['token_number']} for patient {$appt['patient_name']}");
            Session::setFlash('success', 'Appointment successfully approved.');
        } else {
            Session::setFlash('error', 'Unable to approve appointment.');
        }

        redirect('/admin/appointments/pending');
    }

    /**
     * Cancel an appointment.
     */
    public function cancel(array $params): void
    {
        Permission::check('manage_appointments');
        $id = (int)($params['id'] ?? 0);
        $appt = Appointment::find($id);

        if (!$appt) {
            Session::setFlash('error', 'Appointment not found.');
            redirect('/admin/appointments');
        }

        if (Appointment::updateStatus($id, 'cancelled')) {
            ActivityLogger::log('Appointment Cancellation', "Cancelled appointment token #{$appt['token_number']} for patient {$appt['patient_name']}");
            Session::setFlash('success', 'Appointment successfully cancelled.');
        } else {
            Session::setFlash('error', 'Unable to cancel appointment.');
        }

        redirect('/admin/appointments');
    }

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
        $role = Session::get('role');
        $roleSlug = Session::get('role_slug') ?? '';
        
        $doctorId = $userId;
        $isSuperAdmin = ($roleSlug === 'super_admin' || $roleSlug === 'admin');
        
        // Super Admin can select any doctor
        if ($isSuperAdmin && !empty($_GET['doctor_id'])) {
            $doctorId = (int)$_GET['doctor_id'];
        }
        
        // If not super admin, they can only see their own schedule
        if (!$isSuperAdmin) {
            $doctorId = $userId;
        }

        $doctor = Database::row("SELECT id, username, branch_id FROM users WHERE id = :id", ['id' => $doctorId]);
        if (!$doctor) {
            Session::setFlash('error', 'Doctor not found.');
            redirect('/admin/dashboard');
        }

        // Get schedules for the selected doctor
        $schedules = Database::all("SELECT * FROM doctor_schedules WHERE doctor_id = :id ORDER BY 
            FIELD(day_of_week, 'Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday')", 
            ['id' => $doctorId]
        );
        
        // Get all doctors for dropdown (only for super admin)
        $doctors = [];
        if ($isSuperAdmin) {
            $doctors = Database::all(
                "SELECT u.id, u.username, u.branch_id, b.name as branch_name 
                 FROM users u
                 JOIN roles r ON u.role_id = r.id 
                 LEFT JOIN branches b ON u.branch_id = b.id
                 WHERE r.slug = 'doctor' AND u.status = 'active'
                 ORDER BY u.username ASC"
            );
        } else {
            // For doctor role, just show themselves
            $doctors = [$doctor];
        }

        view('admin.appointments.schedule', [
            'title' => 'Configure Shift Schedules',
            'schedules' => $schedules,
            'selected_doctor' => $doctor,
            'doctors' => $doctors,
            'isSuperAdmin' => $isSuperAdmin,
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

        if (!Security::verifyCsrfToken($_POST['csrf_token'] ?? null)) {
            Session::setFlash('error', 'Security token expired.');
            redirect('/admin/appointments/schedule');
        }

        $doctorId = (int)($_POST['doctor_id'] ?? 0);
        if ($doctorId === 0) {
            Session::setFlash('error', 'Invalid doctor selection.');
            redirect('/admin/appointments/schedule');
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

        if (Appointment::saveDoctorSchedule($doctorId, $data)) {
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
            "SELECT u.id, u.username, b.name as branch_name 
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
        if (!Security::verifyCsrfToken($_POST['csrf_token'] ?? null)) {
            Session::setFlash('error', 'Security validation expired.');
            redirect('/appointments/book');
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
            Session::setFlash('error', implode(', ', $errors));
            redirect('/appointments/book');
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            Session::setFlash('error', 'Please enter a valid email address.');
            redirect('/appointments/book');
        }

        // Check if slot is still available
        if (!Appointment::checkSlotAvailability($doctorId, $date, $timeSlot)) {
            Session::setFlash('error', 'Selected time slot is no longer available. Please choose another slot.');
            redirect('/appointments/book');
        }

        // Find or create patient
        $patient = Database::row(
            "SELECT id FROM patients WHERE phone = :phone OR email = :email",
            ['phone' => $phone, 'email' => $email]
        );
        
        if ($patient) {
            $patientId = (int)$patient['id'];
        } else {
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
        }

        if (!$patientId) {
            Session::setFlash('error', 'Unable to register patient profile.');
            redirect('/appointments/book');
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

        $apptId = Appointment::create($apptData);

        if ($apptId) {
            $appointment = Appointment::find($apptId);
            
            Session::set('last_booking', [
                'patient_name' => $name,
                'doctor_name' => $appointment['doctor_name'] ?? 'Doctor',
                'date' => $date,
                'time_slot' => $timeSlot,
                'token_number' => $appointment['token_number'] ?? 'Pending'
            ]);
            
            ActivityLogger::log('Online Booking Submission', "Patient {$name} submitted appointment request (Appt ID: {$apptId}).");
            Session::setFlash('success', '✅ Appointment request submitted successfully!');
            redirect('/appointments/book/success');
        } else {
            Session::setFlash('error', 'Unable to book appointment. Please try again.');
            redirect('/appointments/book');
        }
    }

    /**
     * Booking success page
     */
    public function bookingSuccess(): void
    {
        $appointmentDetails = Session::get('last_booking') ?? [];
        
        view('website.booking_success', [
            'title' => 'Booking Confirmed',
            'appointmentDetails' => $appointmentDetails
        ]);
    }
}