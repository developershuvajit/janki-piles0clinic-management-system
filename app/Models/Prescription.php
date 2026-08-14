<?php
declare(strict_types=1);

namespace App\Models;

use App\Helpers\Database;
use App\Helpers\Logger;

class Prescription
{
    public static function create(array $data): ?int
    {
        try {
            error_log("=== Prescription::create START ===");
            error_log("Data: " . print_r($data, true));

            // Check if patient and doctor exist
            $patientCheck = Database::row("SELECT id FROM patients WHERE id = ?", [$data['patient_id']]);
            $doctorCheck = Database::row("SELECT id FROM users WHERE id = ?", [$data['doctor_id']]);
            
            if (!$patientCheck || !$doctorCheck) {
                error_log("Patient or Doctor not found!");
                return null;
            }

            // Insert prescription - using direct PDO for reliability
            $db = Database::getInstance();
            $pdo = $db->getPdo();
            
            $sql = "INSERT INTO prescriptions (
                        patient_id, doctor_id, symptoms, diagnosis, treatment, 
                        advice, follow_up_date, created_at
                    ) VALUES (?, ?, ?, ?, ?, ?, ?, NOW())";
            
            $stmt = $pdo->prepare($sql);
            $result = $stmt->execute([
                $data['patient_id'],
                $data['doctor_id'],
                $data['symptoms'],
                $data['diagnosis'],
                $data['treatment'],
                $data['advice'] ?? null,
                !empty($data['follow_up_date']) ? $data['follow_up_date'] : null
            ]);

            error_log("Execute result: " . ($result ? 'true' : 'false'));

            if ($result) {
                $insertId = $pdo->lastInsertId();
                error_log("Last Insert ID: " . $insertId);
                
                if ($insertId > 0) {
                    error_log("Prescription created successfully with ID: " . $insertId);
                    return (int)$insertId;
                }
            }
            
            return null;
            
        } catch (\Throwable $e) {
            error_log("Prescription::create ERROR: " . $e->getMessage());
            error_log("Prescription::create TRACE: " . $e->getTraceAsString());
            return null;
        }
    }

    /**
     * Add medicines to prescription - FIXED WORKING VERSION
     */
    public static function addMedicines(int $prescriptionId, array $medicines): bool
    {
        try {
            error_log("=== addMedicines START ===");
            error_log("Prescription ID: " . $prescriptionId);
            error_log("Medicines count: " . count($medicines));

            if (empty($medicines)) {
                error_log("No medicines to add");
                return true;
            }

            $db = Database::getInstance();
            $pdo = $db->getPdo();
            
            $sql = "INSERT INTO prescription_medicines (
                        prescription_id, medicine_name, dosage, frequency, 
                        duration, instructions, issued_status, created_at
                    ) VALUES (?, ?, ?, ?, ?, ?, 'pending', NOW())";
            
            $stmt = $pdo->prepare($sql);
            $added = 0;
            
            foreach ($medicines as $med) {
                $name = trim($med['medicine_name'] ?? '');
                if (empty($name)) {
                    continue;
                }
                
                error_log("Adding medicine: " . $name);
                
                $result = $stmt->execute([
                    $prescriptionId,
                    $name,
                    $med['dosage'] ?? '',
                    $med['frequency'] ?? '',
                    $med['duration'] ?? '',
                    $med['instructions'] ?? ''
                ]);
                
                if ($result) {
                    $added++;
                    error_log("Added: " . $name);
                } else {
                    error_log("Failed to add: " . $name);
                }
            }
            
            error_log("Total medicines added: " . $added);
            return $added > 0;

        } catch (\Throwable $e) {
            error_log("addMedicines ERROR: " . $e->getMessage());
            error_log("addMedicines TRACE: " . $e->getTraceAsString());
            return false;
        }
    }

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

    public static function getMedicines(int $prescriptionId): array
    {
        return Database::all("SELECT * FROM prescription_medicines WHERE prescription_id = ?", [$prescriptionId]);
    }

    public static function getPatientPrescriptions(int $patientId): array
    {
        return Database::all(
            "SELECT pr.*, u.username as doctor_name 
             FROM prescriptions pr
             JOIN users u ON pr.doctor_id = u.id
             WHERE pr.patient_id = ? ORDER BY pr.created_at DESC",
            [$patientId]
        );
    }

    public static function getByPatient(int $patientId): array
    {
        return self::getPatientPrescriptions($patientId);
    }

    public static function getByDoctor(int $doctorId): array
    {
        return Database::all(
            "SELECT pr.*, p.name as patient_name, p.patient_id as patient_code 
             FROM prescriptions pr
             JOIN patients p ON pr.patient_id = p.id
             WHERE pr.doctor_id = ? ORDER BY pr.id DESC",
            [$doctorId]
        );
    }

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

    public static function markMedicineAsIssued(int $medicineId): bool
    {
        try {
            $db = Database::getInstance();
            $pdo = $db->getPdo();
            $stmt = $pdo->prepare("UPDATE prescription_medicines SET issued_status = 'issued' WHERE id = ?");
            return $stmt->execute([$medicineId]);
        } catch (\Throwable $e) {
            error_log("markMedicineAsIssued ERROR: " . $e->getMessage());
            return false;
        }
    }
}