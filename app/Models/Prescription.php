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
            error_log("=== Prescription::create START ===");
            error_log("Data: " . print_r($data, true));

            // First, check if patient_id and doctor_id exist
            $patientCheck = Database::row("SELECT id FROM patients WHERE id = ?", [$data['patient_id']]);
            $doctorCheck = Database::row("SELECT id FROM users WHERE id = ?", [$data['doctor_id']]);
            
            error_log("Patient exists: " . ($patientCheck ? 'Yes' : 'No'));
            error_log("Doctor exists: " . ($doctorCheck ? 'Yes' : 'No'));

            if (!$patientCheck || !$doctorCheck) {
                error_log("Patient or Doctor not found!");
                return null;
            }

            // Simplified insert - only required columns
            $sql = "INSERT INTO prescriptions (
                        patient_id, 
                        doctor_id, 
                        symptoms, 
                        diagnosis, 
                        treatment, 
                        advice, 
                        follow_up_date,
                        created_at
                    ) VALUES (
                        ?, ?, ?, ?, ?, ?, ?, NOW()
                    )";
            
            error_log("SQL: " . $sql);
            
            $result = Database::exec($sql, [
                $data['patient_id'],
                $data['doctor_id'],
                $data['symptoms'],
                $data['diagnosis'],
                $data['treatment'],
                $data['advice'] ?? null,
                !empty($data['follow_up_date']) ? $data['follow_up_date'] : null
            ]);

            error_log("Database::exec result: " . ($result ? 'true' : 'false') . " (rows affected: " . $result . ")");

            // Get the inserted ID
            $insertId = Database::lastInsertId();
            error_log("Last Insert ID: " . $insertId);

            if ($insertId > 0) {
                error_log("Prescription created successfully with ID: " . $insertId);
                return (int)$insertId;
            }
            
            error_log("No insert ID returned");
            return null;
            
        } catch (\Throwable $e) {
            error_log("Prescription::create ERROR: " . $e->getMessage());
            error_log("Prescription::create TRACE: " . $e->getTraceAsString());
            Logger::error("Failed to create prescription record: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Bind a batch of prescribed medicines to a prescription.
     */
    public static function addMedicines(int $prescriptionId, array $medicines): bool
    {
        try {
            $sql = "INSERT INTO prescription_medicines (
                        prescription_id, 
                        medicine_name, 
                        dosage, 
                        frequency, 
                        duration, 
                        instructions, 
                        issued_status
                    ) VALUES (?, ?, ?, ?, ?, ?, 'pending')";
            
            foreach ($medicines as $med) {
                if (empty($med['medicine_name'])) {
                    continue;
                }
                Database::exec($sql, [
                    $prescriptionId,
                    $med['medicine_name'],
                    $med['dosage'] ?? '',
                    $med['frequency'] ?? '',
                    $med['duration'] ?? '',
                    $med['instructions'] ?? ''
                ]);
            }
            return true;
        } catch (\Throwable $e) {
            error_log("addMedicines ERROR: " . $e->getMessage());
            return false;
        }
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
                WHERE pr.id = ? LIMIT 1";
        return Database::row($sql, [$id]);
    }

    /**
     * Get medicine lists of a prescription.
     */
    public static function getMedicines(int $prescriptionId): array
    {
        return Database::all("SELECT * FROM prescription_medicines WHERE prescription_id = ?", [$prescriptionId]);
    }

    /**
     * Retrieve all prescriptions of a patient.
     */
    public static function getPatientPrescriptions(int $patientId): array
    {
        $sql = "SELECT pr.*, u.username as doctor_name 
                FROM prescriptions pr
                JOIN users u ON pr.doctor_id = u.id
                WHERE pr.patient_id = ? ORDER BY pr.created_at DESC";
        return Database::all($sql, [$patientId]);
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
                WHERE pr.doctor_id = ? ORDER BY pr.id DESC";
        return Database::all($sql, [$doctorId]);
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
        if ($branchId !== null) {
            $sql .= " AND p.branch_id = ?";
            $params[] = $branchId;
        }

        $sql .= " ORDER BY pm.id DESC";
        return Database::all($sql, $params);
    }

    /**
     * Dispatch prescribed medicines, marking status as issued.
     */
    public static function markMedicineAsIssued(int $medicineId): bool
    {
        try {
            Database::exec(
                "UPDATE prescription_medicines SET issued_status = 'issued' WHERE id = ?", 
                [$medicineId]
            );
            return true;
        } catch (\Throwable $e) {
            error_log("markMedicineAsIssued ERROR: " . $e->getMessage());
            return false;
        }
    }
}