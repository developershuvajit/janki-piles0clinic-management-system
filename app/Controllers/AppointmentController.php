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
use App\Helpers\Email;
use App\Helpers\ActivityLogger;

class AppointmentController
{
    /**
     * Display a list of all appointments.
     */
    public function index(): void
    {
        Permission::check('manage_appointments');
        $appointments = Appointment::all();
        view('admin.appointments.index', [
            'title' => 'Scheduled Appointments',
            'appointments' => $appointments
        ]);
    }

    /**
     * Show Pending Online Bookings for Admin Approval.
     */
    public function pendingList(): void
    {
        Permission::check('manage_appointments');
        
        $sql = "SELECT a.*, p.name as patient_name, p.patient_id as patient_code, p.phone as patient_phone, 
                       u.username as doctor_name, b.name as branch_name 
                FROM appointments a
                JOIN patients p ON a.patient_id = p.id
                JOIN users u ON a.doctor_id = u.id
                JOIN branches b ON a.branch_id = b.id
                WHERE a.status = 'pending'
                ORDER BY a.date ASC, a.time_slot ASC";
        
        $appointments = Database::all($sql);
        view('admin.appointments.pending', [
            'title' => 'Pending Online Approvals',
            'appointments' => $appointments
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
            // Trigger simulated alerts
            ActivityLogger::log('Appointment Approved', "Approved appointment token #{$appt['token_number']} for patient {$appt['patient_name']}");
            
            // Simulated Email Confirmation
            if (!empty($appt['patient_email'])) {
                $emailBody = "<h3>Appointment Confirmed</h3>"
                           . "<p>Dear {$appt['patient_name']},</p>"
                           . "<p>Your appointment with Dr. {$appt['doctor_name']} has been confirmed!</p>"
                           . "<p><strong>Date:</strong> {$appt['date']}<br><strong>Time:</strong> " . date('h:i A', strtotime($appt['time_slot'])) . "<br><strong>Token:</strong> #{$appt['token_number']}</p>";
                try {
                    Email::send($appt['patient_email'], "Appointment Confirmed - MedClinic", $emailBody);
                } catch (\Throwable $e) {
                    // Ignore email failures in local verification
                }
            }

            // Simulated WhatsApp Notification
            $whatsappMsg = "Hi {$appt['patient_name']}, your appointment with Dr. {$appt['doctor_name']} on {$appt['date']} at " 
                         . date('h:i A', strtotime($appt['time_slot'])) . " is CONFIRMED. Token: #{$appt['token_number']}.";
            ActivityLogger::log('WhatsApp Simulation Dispatch', "WhatsApp sent to {$appt['patient_phone']}: '{$whatsappMsg}'");

            Session::setFlash('success', 'Appointment successfully approved and notifications sent.');
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

        $userId = (int)Session::get('user_id');
        $role = Session::get('role');
        
        $doctorId = $userId;
        
        // If administrator is setting doctor schedule
        if (($role === 'super_admin' || $role === 'admin') && !empty($_GET['doctor_id'])) {
            $doctorId = (int)$_GET['doctor_id'];
        }

        $doctor = Database::row("SELECT id, username FROM users WHERE id = :id", ['id' => $doctorId]);
        if (!$doctor) {
            Session::setFlash('error', 'Doctor not found.');
            redirect('/admin/dashboard');
        }

        $schedules = Database::all("SELECT * FROM doctor_schedules WHERE doctor_id = :id", ['id' => $doctorId]);
        
        // Get list of all doctors for dropdown
        $doctors = Database::all(
            "SELECT u.id, u.username FROM users u 
             JOIN roles r ON u.role_id = r.id 
             WHERE r.slug = 'doctor' AND u.status = 'active'"
        );

        view('admin.appointments.schedule', [
            'title' => 'Configure Shift Schedules',
            'schedules' => $schedules,
            'selected_doctor' => $doctor,
            'doctors' => $doctors
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

        if (Appointment::saveDoctorSchedule($doctorId, $data)) {
            ActivityLogger::log('Schedule Configuration', "Updated schedule for day: {$data['day_of_week']} (Doctor ID: {$doctorId})");
            Session::setFlash('success', 'Doctor schedule configuration saved.');
        } else {
            Session::setFlash('error', 'Unable to save schedule configuration.');
        }

        redirect('/admin/appointments/schedule?doctor_id=' . $doctorId);
    }

    /**
     * JSON API Endpoint: Fetch available slots of a doctor on a specific date.
     */
    public function getSlotsAjax(): void
    {
        $doctorId = (int)($_GET['doctor_id'] ?? 0);
        $date = Security::sanitize($_GET['date'] ?? '');

        if ($doctorId === 0 || empty($date)) {
            jsonResponse(['success' => false, 'slots' => []], 400);
        }

        $slots = Appointment::getTimeSlots($doctorId, $date);
        jsonResponse(['success' => true, 'slots' => $slots]);
    }

    /**
     * Guest/Patient facing online booking panel.
     */
    public function showOnlineBooking(): void
    {
        // Fetch active doctors and branches
        $doctors = Database::all(
            "SELECT u.id, u.username, b.name as branch_name 
             FROM users u
             JOIN roles r ON u.role_id = r.id
             LEFT JOIN branches b ON u.branch_id = b.id
             WHERE r.slug = 'doctor' AND u.status = 'active'"
        );
        $branches = Branch::all();

        view('website.book_appointment', [
            'title' => 'Book Appointment Online',
            'doctors' => $doctors,
            'branches' => $branches
        ]);
    }

    /**
     * Dispatch OTP code to verify guest booking.
     */
    public function sendBookingOtp(): void
    {
        $email = Security::sanitize($_POST['email'] ?? '');
        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            jsonResponse(['success' => false, 'message' => 'Please enter a valid email address.'], 400);
        }

        // Generate 6-digit verification code
        $otp = sprintf("%06d", mt_rand(0, 999999));
        
        // Save in session context
        Session::set('booking_verification_otp', $otp);
        Session::set('booking_verification_email', $email);

        // Send simulated verification email
        $subject = "MedClinic Appointment Booking Verification";
        $body = "<h3>Verification Code</h3><p>Your OTP verification code for booking an online appointment is: <strong>{$otp}</strong></p>";
        try {
            Email::send($email, $subject, $body);
        } catch (\Throwable $e) {
            // Log local failure
        }

        ActivityLogger::log('Booking OTP Sent', "Appointment OTP verification code sent to {$email}.");
        jsonResponse(['success' => true, 'message' => 'Verification code sent to email.']);
    }

    /**
     * Verify booking OTP and submit appointment reservation.
     */
    public function submitOnlineBooking(): void
    {
        if (!Security::verifyCsrfToken($_POST['csrf_token'] ?? null)) {
            Session::setFlash('error', 'Security validation expired.');
            redirect('/appointments/book');
        }

        $email = Security::sanitize($_POST['email'] ?? '');
        $otp = trim($_POST['otp_code'] ?? '');
        
        $sessionOtp = Session::get('booking_verification_otp');
        $sessionEmail = Session::get('booking_verification_email');

        if (empty($otp) || $otp !== $sessionOtp || $email !== $sessionEmail) {
            Session::setFlash('error', 'Invalid verification code or email mismatch.');
            redirect('/appointments/book');
        }

        // OTP verified, check/register patient profile
        $name = Security::sanitize($_POST['name'] ?? '');
        $phone = Security::sanitize($_POST['phone'] ?? '');
        $gender = Security::sanitize($_POST['gender'] ?? 'male');
        $dob = Security::sanitize($_POST['dob'] ?? '');
        $address = Security::sanitize($_POST['address'] ?? '');

        if (empty($name) || empty($phone) || empty($dob)) {
            Session::setFlash('error', 'Please fill in all patient profile fields.');
            redirect('/appointments/book');
        }

        // Locate existing patient by phone
        $patient = Database::row("SELECT id FROM patients WHERE phone = :phone", ['phone' => $phone]);
        if ($patient) {
            $patientId = (int)$patient['id'];
        } else {
            // Register patient profile
            $patientData = [
                'name' => $name,
                'email' => $email,
                'phone' => $phone,
                'gender' => $gender,
                'dob' => $dob,
                'address' => $address,
                'branch_id' => !empty($_POST['branch_id']) ? (int)$_POST['branch_id'] : null
            ];
            $patientId = Patient::create($patientData);
        }

        if (!$patientId) {
            Session::setFlash('error', 'Database error registering patient profile.');
            redirect('/appointments/book');
        }

        // Save Appointment Reservation (Status: pending approval)
        $apptData = [
            'patient_id' => $patientId,
            'doctor_id' => (int)$_POST['doctor_id'],
            'branch_id' => !empty($_POST['branch_id']) ? (int)$_POST['branch_id'] : 1,
            'date' => Security::sanitize($_POST['date'] ?? ''),
            'time_slot' => Security::sanitize($_POST['time_slot'] ?? ''),
            'status' => 'pending',
            'type' => 'online',
            'queue_status' => 'waiting'
        ];

        $apptId = Appointment::create($apptData);

        if ($apptId) {
            // Clear verification codes
            Session::remove('booking_verification_otp');
            Session::remove('booking_verification_email');

            ActivityLogger::log('Online Booking Submission', "Patient {$name} submitted appointment request online (Appt ID: {$apptId}).");
            Session::setFlash('success', 'Appointment request submitted successfully! It is pending administrator approval.');
            redirect('/login');
        } else {
            Session::setFlash('error', 'Doctor schedule slots unavailable for selected date.');
            redirect('/appointments/book');
        }
    }
}
