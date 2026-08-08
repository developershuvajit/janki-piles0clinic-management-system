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
        $existing = self::find($data['ipd_admission_id']);
        if ($existing) {
            $sql = "UPDATE ipd_discharge_summaries 
                    SET diagnosis = :diag, treatment_summary = :treat, advice = :adv, diet = :diet, 
                        follow_up_instructions = :fol, doctor_signature = :sig, hospital_seal = :seal 
                    WHERE id = :id";
            return Database::execute($sql, [
                'diag' => $data['diagnosis'],
                'treat' => $data['treatment_summary'],
                'adv' => $data['advice'] ?? null,
                'diet' => $data['diet'] ?? null,
                'fol' => $data['follow_up_instructions'] ?? null,
                'sig' => $data['doctor_signature'] ?? $existing['doctor_signature'],
                'seal' => $data['hospital_seal'] ?? $existing['hospital_seal'],
                'id' => $existing['id']
            ]);
        } else {
            $sql = "INSERT INTO ipd_discharge_summaries (ipd_admission_id, diagnosis, treatment_summary, advice, diet, follow_up_instructions, doctor_signature, hospital_seal) 
                    VALUES (:ref_id, :diag, :treat, :adv, :diet, :fol, :sig, :seal)";
            return Database::execute($sql, [
                'ref_id' => $data['ipd_admission_id'],
                'diag' => $data['diagnosis'],
                'treat' => $data['treatment_summary'],
                'adv' => $data['advice'] ?? null,
                'diet' => $data['diet'] ?? null,
                'fol' => $data['follow_up_instructions'] ?? null,
                'sig' => $data['doctor_signature'] ?? null,
                'seal' => $data['hospital_seal'] ?? null
            ]);
        }
    }

    /**
     * Retrieve print file parameters joining patient, doctor, and room tables.
     */
    public static function getPrintData(int $admissionId): ?array
    {
        $sql = "SELECT s.*, a.admission_date, a.discharge_date, p.name as patient_name, p.patient_id as patient_code, 
                       p.phone as patient_phone, p.dob, p.gender, u.username as doctor_name, 
                       b.bed_number, r.room_number, r.type as room_type 
                FROM ipd_discharge_summaries s
                JOIN ipd_admissions a ON s.ipd_admission_id = a.id
                JOIN patients p ON a.patient_id = p.id
                JOIN users u ON a.doctor_id = u.id
                JOIN ipd_beds b ON a.bed_id = b.id
                JOIN ipd_rooms r ON b.room_id = r.id
                WHERE s.ipd_admission_id = :id LIMIT 1";
        return Database::row($sql, ['id' => $admissionId]);
    }
}
