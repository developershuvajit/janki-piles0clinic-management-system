<?php

namespace App\Models;

use App\Helpers\Database;
use App\Helpers\Logger;

class Discharge
{
    /**
     * Locate discharge summary by admission ID.
     */
    public static function find(int $admissionId): ?array
    {
        return Database::row("SELECT * FROM ipd_discharge_summaries WHERE ipd_admission_id = :id LIMIT 1", ['id' => $admissionId]);
    }

    /**
     * Create or update a discharge summary.
     */
    public static function save(array $data): bool
    {
        try {
            $existing = self::find($data['ipd_admission_id']);
            
            if ($existing) {
                $sql = "UPDATE ipd_discharge_summaries 
                        SET diagnosis = :diag, treatment_summary = :treat, 
                            procedure_summary = :proc, operation_notes = :opnotes,
                            advice = :adv, medicine_advice = :med, diet = :diet, 
                            follow_up_instructions = :fol, doctor_signature = :sig, 
                            hospital_seal = :seal, updated_at = NOW()
                        WHERE id = :id";
                
                $result = Database::exec($sql, [
                    'diag' => $data['diagnosis'] ?? '',
                    'treat' => $data['treatment_summary'] ?? '',
                    'proc' => $data['procedure_summary'] ?? null,
                    'opnotes' => $data['operation_notes'] ?? null,
                    'adv' => $data['advice'] ?? null,
                    'med' => $data['medicine_advice'] ?? null,
                    'diet' => $data['diet'] ?? null,
                    'fol' => $data['follow_up_instructions'] ?? null,
                    'sig' => $data['doctor_signature'] ?? $existing['doctor_signature'] ?? null,
                    'seal' => $data['hospital_seal'] ?? $existing['hospital_seal'] ?? null,
                    'id' => $existing['id']
                ]);
                
                return $result > 0;
            } else {
                $sql = "INSERT INTO ipd_discharge_summaries (
                            ipd_admission_id, diagnosis, treatment_summary, 
                            procedure_summary, operation_notes, advice, medicine_advice, 
                            diet, follow_up_instructions, doctor_signature, hospital_seal, created_at
                        ) VALUES (
                            :ref_id, :diag, :treat, :proc, :opnotes, :adv, :med, 
                            :diet, :fol, :sig, :seal, NOW()
                        )";
                
                $result = Database::exec($sql, [
                    'ref_id' => $data['ipd_admission_id'],
                    'diag' => $data['diagnosis'] ?? '',
                    'treat' => $data['treatment_summary'] ?? '',
                    'proc' => $data['procedure_summary'] ?? null,
                    'opnotes' => $data['operation_notes'] ?? null,
                    'adv' => $data['advice'] ?? null,
                    'med' => $data['medicine_advice'] ?? null,
                    'diet' => $data['diet'] ?? null,
                    'fol' => $data['follow_up_instructions'] ?? null,
                    'sig' => $data['doctor_signature'] ?? null,
                    'seal' => $data['hospital_seal'] ?? null
                ]);
                
                return $result > 0;
            }
        } catch (\Throwable $e) {
            error_log("Discharge::save ERROR: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Retrieve print file parameters joining patient and doctor tables - NO BED
     */
    public static function getPrintData(int $admissionId): ?array
    {
        $sql = "SELECT s.*, a.admission_date, a.discharge_date, 
                       p.name as patient_name, p.patient_id as patient_code, 
                       p.phone as patient_phone, p.dob, p.gender,
                       u.username as doctor_name, b.name as branch_name 
                FROM ipd_discharge_summaries s
                JOIN ipd_admissions a ON s.ipd_admission_id = a.id
                JOIN patients p ON a.patient_id = p.id
                JOIN users u ON a.doctor_id = u.id
                LEFT JOIN branches b ON a.branch_id = b.id
                WHERE s.ipd_admission_id = :id LIMIT 1";
        return Database::row($sql, ['id' => $admissionId]);
    }

    /**
     * Get discharge summary with all details - NO BED
     */
    public static function getSummary(int $admissionId): ?array
    {
        $sql = "SELECT s.*, a.admission_date, a.discharge_date, 
                       p.name as patient_name, p.patient_id as patient_code, 
                       p.phone as patient_phone, p.dob, p.gender,
                       u.username as doctor_name, b.name as branch_name 
                FROM ipd_discharge_summaries s
                JOIN ipd_admissions a ON s.ipd_admission_id = a.id
                JOIN patients p ON a.patient_id = p.id
                JOIN users u ON a.doctor_id = u.id
                LEFT JOIN branches b ON a.branch_id = b.id
                WHERE s.ipd_admission_id = :id LIMIT 1";
        return Database::row($sql, ['id' => $admissionId]);
    }
}