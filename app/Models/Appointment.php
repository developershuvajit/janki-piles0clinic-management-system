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
        if ($branchId !== null) {
            $sql .= " WHERE a.branch_id = :branch_id";
            $params['branch_id'] = $branchId;
        }

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
        error_log("=== Appointment::create START ===");
        error_log("Data: " . print_r($data, true));

        $doctorId = (int)$data['doctor_id'];
        $date = $data['date'];
        
        // =========================================================
        // SCHEDULE LIMIT CHECK - সম্পূর্ণ সরিয়ে দিন
        // =========================================================
        // NO LIMIT CHECK - সব সময় appointment তৈরি হবে
        
        // Generate daily token
        $tokenRow = Database::row(
            "SELECT COALESCE(MAX(token_number), 0) + 1 AS next_token 
             FROM appointments 
             WHERE doctor_id = :doctor_id AND date = :date",
            ['doctor_id' => $doctorId, 'date' => $date]
        );

        $token = (int)($tokenRow['next_token'] ?? 1);
        error_log("Generated Token: " . $token);

        $sql = "INSERT INTO appointments (
                    patient_id, doctor_id, branch_id, date, time_slot, 
                    status, type, token_number, queue_status, created_at, updated_at
                ) VALUES (
                    :patient_id, :doctor_id, :branch_id, :date, :time_slot,
                    :status, :type, :token_number, :queue_status, NOW(), NOW()
                )";

        $params = [
            'patient_id'   => (int)$data['patient_id'],
            'doctor_id'    => (int)$data['doctor_id'],
            'branch_id'    => $data['branch_id'] !== null ? (int)$data['branch_id'] : null,
            'date'         => $data['date'],
            'time_slot'    => $data['time_slot'],
            'status'       => $data['status'] ?? 'pending',
            'type'         => $data['type'] ?? 'walk-in',
            'token_number' => $token,
            'queue_status' => $data['queue_status'] ?? 'waiting'
        ];

        error_log("Insert Params: " . print_r($params, true));

        Database::exec($sql, $params);

        $insertId = (int)Database::lastInsertId();
        error_log("lastInsertId: " . $insertId);

        if ($insertId > 0) {
            error_log("✅ Appointment created successfully. ID: " . $insertId . " | Token: " . $token);
            return $insertId;
        }

        // Fallback: find the created appointment
        $created = Database::row(
            "SELECT id FROM appointments 
             WHERE patient_id = :patient_id 
               AND doctor_id = :doctor_id 
               AND date = :date 
               AND time_slot = :time_slot 
               AND token_number = :token 
             ORDER BY id DESC LIMIT 1",
            [
                'patient_id' => (int)$data['patient_id'],
                'doctor_id'  => (int)$data['doctor_id'],
                'date'       => $data['date'],
                'time_slot'  => $data['time_slot'],
                'token'      => $token
            ]
        );

        if ($created && !empty($created['id'])) {
            $fallbackId = (int)$created['id'];
            error_log("✅ Appointment found using fallback ID: " . $fallbackId);
            return $fallbackId;
        }

        error_log("❌ INSERT completed but appointment ID could not be resolved.");
        return null;

    } catch (\Throwable $e) {
        error_log("❌ Appointment::create ERROR: " . $e->getMessage());
        error_log("Trace: " . $e->getTraceAsString());
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
        
        $result = Database::exec($sql, [
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
        
        return $result > 0;
    }

    /**
     * Delete an appointment.
     */
    public static function delete(int $id): bool
    {
        $result = Database::exec("DELETE FROM appointments WHERE id = :id", ['id' => $id]);
        return $result > 0;
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
        $result = Database::exec(
            "UPDATE appointments SET queue_status = :status, updated_at = NOW() WHERE id = :id", 
            ['status' => $status, 'id' => $apptId]
        );
        return $result > 0;
    }

    /**
     * Set core appointment status.
     */
    public static function updateStatus(int $apptId, string $status): bool
    {
        $result = Database::exec(
            "UPDATE appointments SET status = :status, updated_at = NOW() WHERE id = :id", 
            ['status' => $status, 'id' => $apptId]
        );
        return $result > 0;
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
        try {
            // First delete existing schedule parameters for that day
            Database::exec(
                "DELETE FROM doctor_schedules WHERE doctor_id = :doctor_id AND day_of_week = :day",
                ['doctor_id' => $doctorId, 'day' => $data['day_of_week']]
            );

            $sql = "INSERT INTO doctor_schedules (doctor_id, day_of_week, start_time, end_time, slot_duration, max_patients, status, created_at, updated_at) 
                    VALUES (:doctor_id, :day_of_week, :start_time, :end_time, :slot_duration, :max_patients, :status, NOW(), NOW())";
            
            $result = Database::exec($sql, [
                'doctor_id' => $doctorId,
                'day_of_week' => $data['day_of_week'],
                'start_time' => $data['start_time'],
                'end_time' => $data['end_time'],
                'slot_duration' => $data['slot_duration'] ?? 15,
                'max_patients' => $data['max_patients'] ?? 20,
                'status' => $data['status'] ?? 'active'
            ]);
            
            return $result > 0;
            
        } catch (\Throwable $e) {
            Logger::error("Failed to save doctor schedule: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get available time slots for a doctor on a specific date.
     */
    public static function getAvailableSlots(int $doctorId, string $date): array
    {
        $dayOfWeek = date('l', strtotime($date));
        
        $schedule = Database::row(
            "SELECT * FROM doctor_schedules 
             WHERE doctor_id = :doctor_id AND day_of_week = :day AND status = 'active'",
            ['doctor_id' => $doctorId, 'day' => $dayOfWeek]
        );

        if (!$schedule) {
            return [];
        }

        $startTime = strtotime($schedule['start_time']);
        $endTime = strtotime($schedule['end_time']);
        $duration = (int)($schedule['slot_duration'] ?? 30) * 60;
        
        if ($endTime <= $startTime) {
            $endTime = strtotime('+1 day', $endTime);
        }
        
        $bookedSlots = self::getBookedSlots($doctorId, $date);
        
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

        $result = Database::exec(
            "UPDATE appointments SET date = :date, time_slot = :time_slot, updated_at = NOW() WHERE id = :id",
            [
                'id' => $apptId,
                'date' => $newDate,
                'time_slot' => $newTimeSlot
            ]
        );
        return $result > 0;
    }
}