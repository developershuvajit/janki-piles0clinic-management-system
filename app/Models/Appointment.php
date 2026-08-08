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
     * Dynamic slots compiler calculating booked status against a date.
     */
    public static function getTimeSlots(int $doctorId, string $date): array
    {
        $dayOfWeek = date('l', strtotime($date)); // e.g. Monday
        
        // Find if doctor has a schedule for this day
        $schedule = Database::row(
            "SELECT * FROM doctor_schedules WHERE doctor_id = :doctor_id AND day_of_week = :day AND status = 'active' LIMIT 1",
            ['doctor_id' => $doctorId, 'day' => $dayOfWeek]
        );

        if (!$schedule) {
            return []; // No schedule set for this day
        }

        $startTime = strtotime($schedule['start_time']);
        $endTime = strtotime($schedule['end_time']);
        $duration = (int)$schedule['slot_duration'] * 60; // in seconds

        // Fetch existing bookings for this doctor on this day
        $bookings = Database::all(
            "SELECT time_slot FROM appointments WHERE doctor_id = :doctor_id AND date = :date AND status != 'cancelled'",
            ['doctor_id' => $doctorId, 'date' => $date]
        );
        $bookedSlots = array_map(function($b) {
            return date('H:i', strtotime($b['time_slot']));
        }, $bookings);

        $slots = [];
        for ($t = $startTime; $t < $endTime; $t += $duration) {
            $slotTime = date('H:i', $t);
            $slots[] = [
                'time' => $slotTime,
                'time_formatted' => date('h:i A', $t),
                'booked' => in_array($slotTime, $bookedSlots, true)
            ];
        }

        return $slots;
    }
}
