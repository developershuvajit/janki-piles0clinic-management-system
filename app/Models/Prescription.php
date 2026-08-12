<?php
declare(strict_types=1);

namespace App\Models;

use App\Helpers\Database;
use App\Helpers\Logger;

class Prescription
{
    /**
     * Create a patient diagnosis prescription file.
     */
    public static function create(array $data): ?int
    {
        try {
            $sql = "INSERT INTO prescriptions (appointment_id, patient_id, doctor_id, symptoms, diagnosis, treatment, advice, follow_up_date, created_at) 
                    VALUES (:appointment_id, :patient_id, :doctor_id, :symptoms, :diagnosis, :treatment, :advice, :follow_up_date, NOW())";
            
            $success = Database::execute($sql, [
                'appointment_id' => $data['appointment_id'] ?? null,
                'patient_id' => $data['patient_id'],
                'doctor_id' => $data['doctor_id'],
                'symptoms' => $data['symptoms'],
                'diagnosis' => $data['diagnosis'],
                'treatment' => $data['treatment'],
                'advice' => $data['advice'] ?? null,
                'follow_up_date' => !empty($data['follow_up_date']) ? $data['follow_up_date'] : null
            ]);

            return $success ? (int)Database::lastInsertId() : null;
        } catch (\Throwable $e) {
            Logger::error("Failed to create prescription record: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Bind a batch of prescribed medicines to a prescription.
     */
    public static function addMedicines(int $prescriptionId, array $medicines): bool
    {
        $sql = "INSERT INTO prescription_medicines (prescription_id, medicine_name, dosage, frequency, duration, instructions, issued_status) 
                VALUES (:prescription_id, :name, :dosage, :frequency, :duration, :instructions, 'pending')";
        
        $successCount = 0;
        foreach ($medicines as $med) {
            if (empty($med['medicine_name'])) {
                continue;
            }
            $success = Database::execute($sql, [
                'prescription_id' => $prescriptionId,
                'name' => $med['medicine_name'],
                'dosage' => $med['dosage'] ?? '',
                'frequency' => $med['frequency'] ?? '',
                'duration' => $med['duration'] ?? '',
                'instructions' => $med['instructions'] ?? ''
            ]);
            if ($success) {
                $successCount++;
            }
        }
        return $successCount > 0;
    }

    /**
     * Retrieve details for a prescription by ID.
     */
    public static function find(int $id): ?array
    {
        $sql = "SELECT pr.*, p.name as patient_name, p.patient_id as patient_code, p.phone as patient_phone, 
                       u.username as doctor_name 
                FROM prescriptions pr
                JOIN patients p ON pr.patient_id = p.id
                JOIN users u ON pr.doctor_id = u.id
                WHERE pr.id = :id LIMIT 1";
        return Database::row($sql, ['id' => $id]);
    }

    /**
     * Get medicine lists of a prescription.
     */
    public static function getMedicines(int $prescriptionId): array
    {
        return Database::all("SELECT * FROM prescription_medicines WHERE prescription_id = :id", ['id' => $prescriptionId]);
    }

    /**
     * Retrieve all prescriptions of a patient.
     */
    public static function getPatientPrescriptions(int $patientId): array
    {
        $sql = "SELECT pr.*, u.username as doctor_name 
                FROM prescriptions pr
                JOIN users u ON pr.doctor_id = u.id
                WHERE pr.patient_id = :id ORDER BY pr.created_at DESC";
        return Database::all($sql, ['id' => $patientId]);
    }

    /**
     * Alias for retrieving all prescriptions of a patient.
     */
    public static function getByPatient(int $patientId): array
    {
        return self::getPatientPrescriptions($patientId);
    }

    /**
     * Retrieve all prescriptions issued by a doctor.
     */
    public static function getByDoctor(int $doctorId): array
    {
        $sql = "SELECT pr.*, p.name as patient_name, p.patient_id as patient_code 
                FROM prescriptions pr
                JOIN patients p ON pr.patient_id = p.id
                WHERE pr.doctor_id = :doc ORDER BY pr.id DESC";
        return Database::all($sql, ['doc' => $doctorId]);
    }

    /**
     * Retrieve list of all pending medicine dispatches for reception desks.
     */
    public static function getPendingMedicineIssues(?int $branchId = null): array
    {
        $sql = "SELECT pm.*, pr.created_at as prescribed_at, p.name as patient_name, p.patient_id as patient_code, 
                       u.username as doctor_name 
                FROM prescription_medicines pm
                JOIN prescriptions pr ON pm.prescription_id = pr.id
                JOIN patients p ON pr.patient_id = p.id
                JOIN users u ON pr.doctor_id = u.id
                WHERE pm.issued_status = 'pending'";
        
        $params = [];
        $sql = Database::scopeToBranch($sql, $params, $branchId, 'p.branch_id');

        $sql .= " ORDER BY pm.id DESC";
        return Database::all($sql, $params);
    }

    /**
     * Dispatch prescribed medicines, marking status as issued.
     */
    public static function markMedicineAsIssued(int $medicineId): bool
    {
        return Database::execute(
            "UPDATE prescription_medicines SET issued_status = 'issued' WHERE id = :id", 
            ['id' => $medicineId]
        );
    }
}
