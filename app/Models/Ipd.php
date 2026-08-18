<?php
declare(strict_types=1);

namespace App\Models;

use App\Helpers\Database;
use App\Helpers\Logger;

class Ipd
{
    /**
     * Get active IPD admissions
     */
    public static function getActiveAdmissions(?int $branchId = null): array
    {
        $sql = "SELECT a.*, p.name as patient_name, p.patient_id as patient_code, p.phone as patient_phone,
                       u.username as doctor_name, b.name as branch_name 
                FROM ipd_admissions a
                JOIN patients p ON a.patient_id = p.id
                JOIN users u ON a.doctor_id = u.id
                LEFT JOIN branches b ON a.branch_id = b.id
                WHERE a.status = 'admitted'";
        
        $params = [];
        if ($branchId !== null) {
            $sql .= " AND a.branch_id = ?";
            $params[] = $branchId;
        }

        $sql .= " ORDER BY a.admission_date DESC";
        return Database::all($sql, $params);
    }

    /**
     * Get discharged history
     */
    public static function getDischargedHistory(?int $branchId = null): array
    {
        $sql = "SELECT a.*, p.name as patient_name, p.patient_id as patient_code,
                       u.username as doctor_name, b.name as branch_name 
                FROM ipd_admissions a
                JOIN patients p ON a.patient_id = p.id
                JOIN users u ON a.doctor_id = u.id
                LEFT JOIN branches b ON a.branch_id = b.id
                WHERE a.status = 'discharged'";
        
        $params = [];
        if ($branchId !== null) {
            $sql .= " AND a.branch_id = ?";
            $params[] = $branchId;
        }

        $sql .= " ORDER BY a.discharge_date DESC";
        return Database::all($sql, $params);
    }

    /**
     * Find admission by ID
     */

    /**
 * Find admission by ID
 */
public static function findAdmission(int $id): ?array
{
    $sql = "SELECT a.*, p.name as patient_name, p.patient_id as patient_code, p.phone as patient_phone, 
                   p.dob, p.gender, p.blood_group, p.allergies, p.address, 
                   u.username as doctor_name, b.name as branch_name 
            FROM ipd_admissions a
            JOIN patients p ON a.patient_id = p.id
            JOIN users u ON a.doctor_id = u.id
            LEFT JOIN branches b ON a.branch_id = b.id
            WHERE a.id = ? LIMIT 1";
    
    $result = Database::row($sql, [$id]);
    
    // Log for debugging
    error_log("findAdmission result: " . print_r($result, true));
    
    return $result;
}



     
    /**
     * Admit a patient into IPD
     */
    public static function admit(array $data): ?int
    {
        try {
            error_log("=== IPD::admit START ===");
            error_log("Data: " . print_r($data, true));

            $patient = Database::row("SELECT id FROM patients WHERE id = ?", [$data['patient_id']]);
            if (!$patient) {
                error_log("Patient not found: " . $data['patient_id']);
                return null;
            }

            $doctor = Database::row("SELECT id FROM users WHERE id = ?", [$data['doctor_id']]);
            if (!$doctor) {
                error_log("Doctor not found: " . $data['doctor_id']);
                return null;
            }

            if (!empty($data['admission_date'])) {
                $data['admission_date'] = str_replace('T', ' ', $data['admission_date']);
                if (strlen($data['admission_date']) === 16) {
                    $data['admission_date'] .= ':00';
                }
            } else {
                $data['admission_date'] = date('Y-m-d H:i:s');
            }

            $sql = "INSERT INTO ipd_admissions (
                        patient_id, doctor_id, branch_id, admission_date, 
                        symptoms, diagnosis, status, created_at
                    ) VALUES (?, ?, ?, ?, ?, ?, 'admitted', NOW())";
            
            $params = [
                $data['patient_id'],
                $data['doctor_id'],
                $data['branch_id'] ?? null,
                $data['admission_date'],
                $data['symptoms'] ?? '',
                $data['diagnosis']
            ];

            $result = Database::exec($sql, $params);
            $insertId = Database::lastInsertId();

            if ($insertId && (int)$insertId > 0) {
                return (int)$insertId;
            }
            
            return null;
            
        } catch (\Throwable $e) {
            error_log("!!! IPD::admit EXCEPTION !!!");
            error_log("Message: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Discharge patient from IPD
     */
    public static function discharge(int $admissionId, float $discount = 0.00, float $tax = 0.00): bool
    {
        $admission = self::findAdmission($admissionId);
        if (!$admission || $admission['status'] !== 'admitted') {
            return false;
        }

        $db = Database::getInstance();
        $pdo = $db->getPdo();
        
        try {
            $pdo->beginTransaction();
            
            $dischargeDate = date('Y-m-d H:i:s');
            
            $stmt = $pdo->prepare("UPDATE ipd_admissions SET status = 'discharged', discharge_date = ? WHERE id = ?");
            $stmt->execute([$dischargeDate, $admissionId]);

            $admitTime = strtotime($admission['admission_date']);
            $dischargeTime = strtotime($dischargeDate);
            $secondsDiff = $dischargeTime - $admitTime;
            $days = (int)ceil($secondsDiff / 86400);
            if ($days < 1) {
                $days = 1;
            }

            $pricePerDay = 500.00;
            $roomRent = $days * $pricePerDay;

            $procSumRow = Database::row(
                "SELECT COALESCE(SUM(cost), 0) as total FROM ipd_procedures WHERE ipd_admission_id = ? AND status = 'completed'",
                [$admissionId]
            );
            $procCost = (float)($procSumRow['total'] ?? 0.00);

            $subtotal = $roomRent + $procCost;
            $total = $subtotal - $discount + $tax;

            $branchId = (int)($admission['branch_id'] ?? 0);
            $validBranch = Database::row("SELECT id FROM branches WHERE id = ?", [$branchId]);
            if (!$validBranch) {
                $firstBranch = Database::row("SELECT id FROM branches ORDER BY id ASC LIMIT 1");
                $branchId = $firstBranch ? (int)$firstBranch['id'] : 1;
            }

            $billSql = "INSERT INTO billing (patient_id, branch_id, type, reference_id, subtotal, discount, tax, total, paid_amount, payment_status, payment_method, created_at, updated_at) 
                        VALUES (?, ?, 'ipd', ?, ?, ?, ?, ?, 0.00, 'unpaid', 'none', NOW(), NOW())";
            
            $stmt = $pdo->prepare($billSql);
            $stmt->execute([
                $admission['patient_id'],
                $branchId,
                $admissionId,
                $subtotal,
                $discount,
                $tax,
                $total
            ]);

            $pdo->commit();
            return true;
            
        } catch (\Throwable $e) {
            $pdo->rollBack();
            error_log("Discharge ERROR: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Add nursing log - FIXED: ipd_admission_id ব্যবহার করুন
     */
    public static function addNursingLog(int $admissionId, array $data): bool
    {
        try {
            $sql = "INSERT INTO ipd_nursing_logs (ipd_admission_id, vit_temp, vit_bp, vit_pulse, notes, recorded_at) 
                    VALUES (?, ?, ?, ?, ?, NOW())";
            $result = Database::exec($sql, [
                $admissionId,
                $data['temp'] ?? null,
                $data['bp'] ?? null,
                $data['pulse'] ?? null,
                $data['notes'] ?? ''
            ]);
            return $result > 0;
        } catch (\Throwable $e) {
            error_log("addNursingLog ERROR: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get nursing logs - FIXED: ipd_admission_id ব্যবহার করুন
     */
    public static function getNursingLogs(int $admissionId): array
    {
        return Database::all("SELECT * FROM ipd_nursing_logs WHERE ipd_admission_id = ? ORDER BY recorded_at DESC", [$admissionId]);
    }

    /**
     * Add procedure - FIXED: ipd_admission_id ব্যবহার করুন
     */
    public static function addProcedure(int $admissionId, array $data): bool
    {
        try {
            $sql = "INSERT INTO ipd_procedures (ipd_admission_id, name, doctor_id, cost, status, created_at) 
                    VALUES (?, ?, ?, ?, 'completed', NOW())";
            $result = Database::exec($sql, [
                $admissionId,
                $data['name'],
                $data['doctor_id'],
                $data['cost']
            ]);
            return $result > 0;
        } catch (\Throwable $e) {
            error_log("addProcedure ERROR: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get procedures list - FIXED: ipd_admission_id ব্যবহার করুন
     */

    /**
 * Get procedures list
 */
public static function getProcedures(int $admissionId): array
{
    $sql = "SELECT p.*, u.username as doctor_name 
            FROM ipd_procedures p 
            JOIN users u ON p.doctor_id = u.id 
            WHERE p.ipd_admission_id = ? ORDER BY p.created_at DESC";
    
    $result = Database::all($sql, [$admissionId]);
    
    // Log for debugging
    error_log("getProcedures count: " . count($result));
    
    return $result;
}



     
}