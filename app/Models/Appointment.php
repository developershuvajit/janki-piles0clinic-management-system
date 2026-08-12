<?php
declare(strict_types=1);

namespace App\Models;

use App\Helpers\Database;
use App\Helpers\Logger;

class Appointment
{
    /**
     * Retrieve all appointments with patient, doctor, and branch details.
     */
    public static function all(?int $branchId = null): array
    {
        $sql = "SELECT a.*, p.name as patient_name, p.patient_id as patient_code, p.phone as patient_phone, 
                       u.username as doctor_name, b.name as branch_name 
                FROM appointments a
                JOIN patients p ON a.patient_id = p.id
                JOIN users u ON a.doctor_id = u.id
                JOIN branches b ON a.branch_id = b.id";
        
        $params = [];
        $sql = Database::scopeToBranch($sql, $params, $branchId, 'a.branch_id');

        $sql .= " ORDER BY a.date DESC, a.time_slot ASC";
        return Database::all($sql, $params);
    }

    /**
     * Find appointment by ID.
     */
    public static function find(int $id): ?array
    {
        $sql = "SELECT a.*, p.name as patient_name, p.patient_id as patient_code, p.phone as patient_phone, 
                       p.email as patient_email, u.username as doctor_name, b.name as branch_name 
                FROM appointments a
                JOIN patients p ON a.patient_id = p.id
                JOIN users u ON a.doctor_id = u.id
                JOIN branches b ON a.branch_id = b.id
                WHERE a.id = :id LIMIT 1";
        return Database::row($sql, ['id' => $id]);
    }

    /**
     * Create appointment, calculating daily sequential token number automatically.
     */
    public static function create(array $data): ?int
    {
        try {
            // Calculate next sequential token for the doctor on that date
            $tokenRow = Database::row(
                "SELECT COALESCE(MAX(token_number), 0) + 1 as next_token 
                 FROM appointments 
                 WHERE doctor_id = :doctor_id AND date = :date",
                [
                    'doctor_id' => $data['doctor_id'],
                    'date' => $data['date']
                ]
            );
            $token = (int)($tokenRow['next_token'] ?? 1);

            $sql = "INSERT INTO appointments (patient_id, doctor_id, branch_id, date, time_slot, status, type, token_number, queue_status, created_at, updated_at) 
                    VALUES (:patient_id, :doctor_id, :branch_id, :date, :time_slot, :status, :type, :token_number, :queue_status, NOW(), NOW())";
            
            $success = Database::execute($sql, [
                'patient_id' => $data['patient_id'],
                'doctor_id' => $data['doctor_id'],
                'branch_id' => $data['branch_id'],
                'date' => $data['date'],
                'time_slot' => $data['time_slot'],
                'status' => $data['status'] ?? 'pending',
                'type' => $data['type'] ?? 'walk-in',
                'token_number' => $token,
                'queue_status' => $data['queue_status'] ?? 'waiting'
            ]);

            return $success ? (int)Database::lastInsertId() : null;
        } catch (\Throwable $e) {
            Logger::error("Failed to create appointment: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Update an appointment record.
     */
    public static function update(int $id, array $data): bool
    {
        $sql = "UPDATE appointments SET 
                    patient_id = :patient_id, 
                    doctor_id = :doctor_id, 
                    branch_id = :branch_id, 
                    date = :date, 
                    time_slot = :time_slot, 
                    status = :status, 
                    type = :type,
                    queue_status = :queue_status,
                    updated_at = NOW() 
                WHERE id = :id";
        
        return Database::execute($sql, [
            'id' => $id,
            'patient_id' => $data['patient_id'],
            'doctor_id' => $data['doctor_id'],
            'branch_id' => $data['branch_id'],
            'date' => $data['date'],
            'time_slot' => $data['time_slot'],
            'status' => $data['status'],
            'type' => $data['type'],
            'queue_status' => $data['queue_status']
        ]);
    }

    /**
     * Delete an appointment.
     */
    public static function delete(int $id): bool
    {
        return Database::execute("DELETE FROM appointments WHERE id = :id", ['id' => $id]);
    }

    /**
     * Retrieve queue appointments list for a doctor on a specific date.
     */
    public static function getDoctorQueue(int $doctorId, string $date): array
    {
        $sql = "SELECT a.*, p.name as patient_name, p.patient_id as patient_code, p.phone as patient_phone, p.gender, p.dob, p.allergies 
                FROM appointments a
                JOIN patients p ON a.patient_id = p.id
                WHERE a.doctor_id = :doctor_id AND a.date = :date AND a.status = 'approved'
                ORDER BY a.token_number ASC";
        return Database::all($sql, [
            'doctor_id' => $doctorId,
            'date' => $date
        ]);
    }

    /**
     * Set active token status.
     */
    public static function updateQueueStatus(int $apptId, string $status): bool
    {
        return Database::execute(
            "UPDATE appointments SET queue_status = :status, updated_at = NOW() WHERE id = :id", 
            ['status' => $status, 'id' => $apptId]
        );
    }

    /**
     * Set core appointment status.
     */
    public static function updateStatus(int $apptId, string $status): bool
    {
        return Database::execute(
            "UPDATE appointments SET status = :status, updated_at = NOW() WHERE id = :id", 
            ['status' => $status, 'id' => $apptId]
        );
    }

    /**
     * Fetch configured day schedules of a doctor.
     */
    public static function getDoctorSchedules(int $doctorId): array
    {
        return Database::all("SELECT * FROM doctor_schedules WHERE doctor_id = :id AND status = 'active'", ['id' => $doctorId]);
    }

    /**
     * Save doctor schedule configurations.
     */
    public static function saveDoctorSchedule(int $doctorId, array $data): bool
    {
        // First delete existing schedule parameters for that day
        Database::execute(
            "DELETE FROM doctor_schedules WHERE doctor_id = :doctor_id AND day_of_week = :day",
            ['doctor_id' => $doctorId, 'day' => $data['day_of_week']]
        );

        $sql = "INSERT INTO doctor_schedules (doctor_id, day_of_week, start_time, end_time, slot_duration, max_patients, status) 
                VALUES (:doctor_id, :day_of_week, :start_time, :end_time, :slot_duration, :max_patients, :status)";
        
        return Database::execute($sql, [
            'doctor_id' => $doctorId,
            'day_of_week' => $data['day_of_week'],
            'start_time' => $data['start_time'],
            'end_time' => $data['end_time'],
            'slot_duration' => $data['slot_duration'] ?? 15,
            'max_patients' => $data['max_patients'] ?? 20,
            'status' => $data['status'] ?? 'active'
        ]);
    }

    /**
     * Get available time slots for a doctor on a specific date.
     * Uses doctor_schedules table for slot generation.
     */
    public static function getAvailableSlots(int $doctorId, string $date): array
    {
        $dayOfWeek = date('l', strtotime($date));
        
        // Get doctor's schedule for this day from doctor_schedules table
        $schedule = Database::row(
            "SELECT * FROM doctor_schedules 
             WHERE doctor_id = :doctor_id AND day_of_week = :day AND status = 'active'",
            ['doctor_id' => $doctorId, 'day' => $dayOfWeek]
        );

        // If no schedule found, doctor is not available on this day
        if (!$schedule) {
            return [];
        }

        $startTime = strtotime($schedule['start_time']);
        $endTime = strtotime($schedule['end_time']);
        $duration = (int)($schedule['slot_duration'] ?? 30) * 60;
        
        if ($endTime <= $startTime) {
            $endTime = strtotime('+1 day', $endTime);
        }
        
        // Get booked slots for this doctor on this date
        $bookedSlots = self::getBookedSlots($doctorId, $date);
        
        // Generate slots
        $slots = [];
        $current = $startTime;
        while ($current < $endTime) {
            $slotTime = date('H:i:s', $current);
            $slotTimeFormatted = date('h:i A', $current);
            
            $isBooked = in_array(date('H:i', $current), $bookedSlots);
            
            $slots[] = [
                'time' => $slotTime,
                'time_formatted' => $slotTimeFormatted,
                'booked' => $isBooked
            ];
            
            $current += $duration;
        }
        
        return $slots;
    }

    /**
     * Check if a time slot is available
     */
    public static function checkSlotAvailability(int $doctorId, string $date, string $timeSlot): bool
    {
        $existing = Database::row(
            "SELECT id FROM appointments 
             WHERE doctor_id = :doctor_id 
             AND date = :date 
             AND time_slot = :time_slot 
             AND status != 'cancelled'",
            [
                'doctor_id' => $doctorId, 
                'date' => $date, 
                'time_slot' => $timeSlot
            ]
        );
        
        return $existing === null;
    }

    /**
     * Get all booked slots for a doctor on a specific date
     */
    public static function getBookedSlots(int $doctorId, string $date): array
    {
        $bookings = Database::all(
            "SELECT time_slot FROM appointments 
             WHERE doctor_id = :doctor_id AND date = :date AND status != 'cancelled'",
            ['doctor_id' => $doctorId, 'date' => $date]
        );
        return array_map(function($b) {
            return date('H:i', strtotime($b['time_slot']));
        }, $bookings);
    }

    /**
     * Get doctor's schedule for a specific day
     */
    public static function getDoctorSchedule(int $doctorId, string $dayOfWeek): ?array
    {
        return Database::row(
            "SELECT * FROM doctor_schedules 
             WHERE doctor_id = :doctor_id AND day_of_week = :day AND status = 'active'",
            ['doctor_id' => $doctorId, 'day' => $dayOfWeek]
        );
    }

    /**
     * Get appointment count for a doctor on a specific date
     */
    public static function getAppointmentCount(int $doctorId, string $date): int
    {
        $result = Database::row(
            "SELECT COUNT(*) as count FROM appointments 
             WHERE doctor_id = :doctor_id AND date = :date AND status != 'cancelled'",
            ['doctor_id' => $doctorId, 'date' => $date]
        );
        return (int)($result['count'] ?? 0);
    }

    /**
     * Get upcoming appointments for a patient
     */
    public static function getPatientAppointments(int $patientId): array
    {
        return Database::all(
            "SELECT a.*, u.username as doctor_name, b.name as branch_name 
             FROM appointments a
             JOIN users u ON a.doctor_id = u.id
             JOIN branches b ON a.branch_id = b.id
             WHERE a.patient_id = :patient_id 
             ORDER BY a.date DESC, a.time_slot ASC",
            ['patient_id' => $patientId]
        );
    }

    /**
     * Get today's appointments for a doctor
     */
    public static function getTodayAppointments(int $doctorId): array
    {
        $today = date('Y-m-d');
        return self::getDoctorQueue($doctorId, $today);
    }

    /**
     * Cancel appointment and free up the slot
     */
    public static function cancelAppointment(int $apptId): bool
    {
        return self::updateStatus($apptId, 'cancelled');
    }

    /**
     * Reschedule appointment
     */
    public static function rescheduleAppointment(int $apptId, string $newDate, string $newTimeSlot): bool
    {
        $appointment = self::find($apptId);
        if (!$appointment) {
            return false;
        }

        if (!self::checkSlotAvailability($appointment['doctor_id'], $newDate, $newTimeSlot)) {
            return false;
        }

        return Database::execute(
            "UPDATE appointments SET date = :date, time_slot = :time_slot, updated_at = NOW() WHERE id = :id",
            [
                'id' => $apptId,
                'date' => $newDate,
                'time_slot' => $newTimeSlot
            ]
        );
    }
}