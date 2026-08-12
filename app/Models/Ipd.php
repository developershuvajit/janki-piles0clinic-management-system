<?php
declare(strict_types=1);

namespace App\Models;

use App\Helpers\Database;
use App\Helpers\Logger;

class Ipd
{
    /**
     * Retrieve all active rooms and their associated bed capacities.
     */
    public static function getRooms(): array
    {
        $sql = "SELECT r.*, 
                       (SELECT COUNT(*) FROM ipd_beds WHERE room_id = r.id) as total_beds,
                       (SELECT COUNT(*) FROM ipd_beds WHERE room_id = r.id AND status = 'occupied') as occupied_beds 
                FROM ipd_rooms r 
                WHERE r.status = 'active'";
        return Database::all($sql);
    }

    /**
     * Retrieve list of all available beds in active rooms.
     */
    public static function getAvailableBeds(): array
    {
        $sql = "SELECT b.id, b.bed_number, r.room_number, r.type, r.price_per_day 
                FROM ipd_beds b
                JOIN ipd_rooms r ON b.room_id = r.id
                WHERE b.status = 'available' AND r.status = 'active'
                ORDER BY r.room_number ASC, b.bed_number ASC";
        return Database::all($sql);
    }

    /**
     * Retrieve active IPD patient admissions.
     */
    public static function getActiveAdmissions(?int $branchId = null): array
    {
        $sql = "SELECT a.*, p.name as patient_name, p.patient_id as patient_code, p.phone as patient_phone,
                       u.username as doctor_name, b.bed_number, r.room_number, r.type as room_type 
                FROM ipd_admissions a
                JOIN patients p ON a.patient_id = p.id
                JOIN users u ON a.doctor_id = u.id
                JOIN ipd_beds b ON a.bed_id = b.id
                JOIN ipd_rooms r ON b.room_id = r.id
                WHERE a.status = 'admitted'";
        
        $params = [];
        if ($branchId !== null) {
            $sql .= " AND p.branch_id = :branch_id";
            $params['branch_id'] = $branchId;
        }

        $sql .= " ORDER BY a.admission_date DESC";
        return Database::all($sql, $params);
    }

    /**
     * Retrieve discharge list history.
     */
    public static function getDischargedHistory(?int $branchId = null): array
    {
        $sql = "SELECT a.*, p.name as patient_name, p.patient_id as patient_code,
                       u.username as doctor_name, b.bed_number, r.room_number 
                FROM ipd_admissions a
                JOIN patients p ON a.patient_id = p.id
                JOIN users u ON a.doctor_id = u.id
                JOIN ipd_beds b ON a.bed_id = b.id
                JOIN ipd_rooms r ON b.room_id = r.id
                WHERE a.status = 'discharged'";
        
        $params = [];
        if ($branchId !== null) {
            $sql .= " AND p.branch_id = :branch_id";
            $params['branch_id'] = $branchId;
        }

        $sql .= " ORDER BY a.discharge_date DESC";
        return Database::all($sql, $params);
    }

    /**
     * Find specific IPD admission details by ID.
     */
    public static function findAdmission(int $id): ?array
    {
        $sql = "SELECT a.*, p.name as patient_name, p.patient_id as patient_code, p.phone as patient_phone, 
                       p.dob, p.gender, p.blood_group, p.allergies, p.address, p.branch_id as branch_id,
                       u.username as doctor_name, b.bed_number, r.room_number, r.price_per_day, r.type as room_type 
                FROM ipd_admissions a
                JOIN patients p ON a.patient_id = p.id
                JOIN users u ON a.doctor_id = u.id
                JOIN ipd_beds b ON a.bed_id = b.id
                JOIN ipd_rooms r ON b.room_id = r.id
                WHERE a.id = :id LIMIT 1";
        return Database::row($sql, ['id' => $id]);
    }

    /**
     * Admit a patient into IPD, locking the bed.
     */
    public static function admit(array $data): bool
    {
        Database::beginTransaction();
        try {
            // Verify if bed is available
            $bed = Database::row("SELECT status FROM ipd_beds WHERE id = :id", ['id' => $data['bed_id']]);
            if (!$bed || $bed['status'] !== 'available') {
                throw new \Exception("Selected bed is occupied or invalid.");
            }

            // Insert admission
            $sql = "INSERT INTO ipd_admissions (patient_id, doctor_id, bed_id, admission_date, symptoms, diagnosis, status, created_at) 
                    VALUES (:patient_id, :doctor_id, :bed_id, :admission_date, :symptoms, :diagnosis, 'admitted', NOW())";
            
            Database::execute($sql, [
                'patient_id' => $data['patient_id'],
                'doctor_id' => $data['doctor_id'],
                'bed_id' => $data['bed_id'],
                'admission_date' => $data['admission_date'],
                'symptoms' => $data['symptoms'],
                'diagnosis' => $data['diagnosis']
            ]);

            // Set bed status to occupied
            Database::execute("UPDATE ipd_beds SET status = 'occupied' WHERE id = :id", ['id' => $data['bed_id']]);

            Database::commit();
            return true;
        } catch (\Throwable $e) {
            Database::rollBackIfActive();
            Logger::error("Failed to execute IPD admission transaction: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Discharge patient from IPD, calculate stay lengths, and invoice billing.
     */
    public static function discharge(int $admissionId, float $discount = 0.00, float $tax = 0.00): bool
    {
        $admission = self::findAdmission($admissionId);
        if (!$admission || $admission['status'] !== 'admitted') {
            return false;
        }

        Database::beginTransaction();
        try {
            $dischargeDate = date('Y-m-d H:i:s');
            
            // 1. Update Admission status to discharged
            Database::execute(
                "UPDATE ipd_admissions SET status = 'discharged', discharge_date = :discharge WHERE id = :id",
                ['discharge' => $dischargeDate, 'id' => $admissionId]
            );

            // 2. Set bed status back to available
            Database::execute(
                "UPDATE ipd_beds SET status = 'available' WHERE id = :id", 
                ['id' => $admission['bed_id']]
            );

            // 3. Stay Cost Aggregations (Minimum 1 Day)
            $admitTime = strtotime($admission['admission_date']);
            $dischargeTime = strtotime($dischargeDate);
            $secondsDiff = $dischargeTime - $admitTime;
            $days = (int)ceil($secondsDiff / 86400);
            if ($days < 1) {
                $days = 1;
            }

            $roomRent = $days * (float)$admission['price_per_day'];

            // 4. Summarize Procedures Cost
            $procSumRow = Database::row(
                "SELECT COALESCE(SUM(cost), 0) as total FROM ipd_procedures WHERE ipd_admission_id = :id AND status = 'completed'",
                ['id' => $admissionId]
            );
            $procCost = (float)($procSumRow['total'] ?? 0.00);

            // 5. Total aggregations
            $subtotal = $roomRent + $procCost;
            $total = $subtotal - $discount + $tax;

            // 6. Generate Inpatient Invoice
            $branchId = (int)($admission['branch_id'] ?? 0);
            $validBranch = Database::row("SELECT id FROM branches WHERE id = :bid", ['bid' => $branchId]);
            if (!$validBranch) {
                $firstBranch = Database::row("SELECT id FROM branches ORDER BY id ASC LIMIT 1");
                $branchId = $firstBranch ? (int)$firstBranch['id'] : 1;
            }

            $billSql = "INSERT INTO billing (patient_id, branch_id, type, reference_id, subtotal, discount, tax, total, paid_amount, payment_status, payment_method, created_at, updated_at) 
                        VALUES (:patient_id, :branch_id, 'ipd', :ref_id, :sub, :disc, :tax, :tot, 0.00, 'unpaid', 'none', NOW(), NOW())";
            
            Database::execute($billSql, [
                'patient_id' => $admission['patient_id'],
                'branch_id' => $branchId,
                'ref_id' => $admissionId,
                'sub' => $subtotal,
                'disc' => $discount,
                'tax' => $tax,
                'tot' => $total
            ]);

            Database::commit();
            return true;
        } catch (\Throwable $e) {
            Database::rollBackIfActive();
            Logger::error("Failed discharging IPD patient transaction: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Add vital signs check-in records.
     */
    public static function addNursingLog(int $admissionId, array $data): bool
    {
        $sql = "INSERT INTO ipd_nursing_logs (ipd_admission_id, vit_temp, vit_bp, vit_pulse, notes, recorded_at) 
                VALUES (:admission_id, :temp, :bp, :pulse, :notes, NOW())";
        return Database::execute($sql, [
            'admission_id' => $admissionId,
            'temp' => $data['temp'] ?? null,
            'bp' => $data['bp'] ?? null,
            'pulse' => $data['pulse'] ?? null,
            'notes' => $data['notes']
        ]);
    }

    /**
     * Retrieve nursing history for an admission.
     */
    public static function getNursingLogs(int $admissionId): array
    {
        return Database::all("SELECT * FROM ipd_nursing_logs WHERE ipd_admission_id = :id ORDER BY recorded_at DESC", ['id' => $admissionId]);
    }

    /**
     * Record completed medical procedures during stay.
     */
    public static function addProcedure(int $admissionId, array $data): bool
    {
        $sql = "INSERT INTO ipd_procedures (ipd_admission_id, name, doctor_id, cost, status, created_at) 
                VALUES (:admission_id, :name, :doctor_id, :cost, 'completed', NOW())";
        return Database::execute($sql, [
            'admission_id' => $admissionId,
            'name' => $data['name'],
            'doctor_id' => $data['doctor_id'],
            'cost' => $data['cost']
        ]);
    }

    /**
     * Get procedures list for an admission.
     */
    public static function getProcedures(int $admissionId): array
    {
        $sql = "SELECT p.*, u.username as doctor_name 
                FROM ipd_procedures p 
                JOIN users u ON p.doctor_id = u.id 
                WHERE p.ipd_admission_id = :id ORDER BY p.created_at DESC";
        return Database::all($sql, ['id' => $admissionId]);
    }
}
