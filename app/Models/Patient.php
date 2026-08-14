<?php
declare(strict_types=1);

namespace App\Models;

use App\Helpers\Database;
use App\Helpers\QRHelper;
use App\Helpers\Logger;
use App\Helpers\Session;

class Patient
{
    /**
     * Get branch filter for current user
     * Super Admin ছাড়া সবাই ব্রাঞ্চ ফিল্টার পাবে
     */
    private static function getBranchFilter(): array
    {
        $user = Session::user();
        $roleSlug = $user['role_slug'] ?? $user['role'] ?? '';
        $branchId = $user['branch_id'] ?? null;
        
        $isSuperAdmin = ($roleSlug === 'super_admin' || $roleSlug === 'admin');
        $hasBranchFilter = (!$isSuperAdmin && $branchId !== null);
        
        return [
            'isSuperAdmin' => $isSuperAdmin,
            'branchId' => $branchId,
            'hasFilter' => $hasBranchFilter
        ];
    }

    /**
     * Retrieve all patients with branch filter.
     * Super Admin - সব দেখবে
     * বাকি সবাই - শুধু নিজের ব্রাঞ্চের দেখবে
     */
    public static function all(): array
    {
        $db = Database::getInstance();
        $filter = self::getBranchFilter();
        
        $sql = "SELECT p.*, b.name as branch_name 
                FROM patients p 
                LEFT JOIN branches b ON p.branch_id = b.id";
        $params = [];
        
        if ($filter['hasFilter']) {
            $sql .= " WHERE p.branch_id = ?";
            $params[] = $filter['branchId'];
        }
        
        $sql .= " ORDER BY p.id DESC";
        
        return $db->getAll($sql, $params);
    }

    /**
     * Search patient records by criteria with branch filter.
     */
    public static function search(string $query): array
    {
        $db = Database::getInstance();
        $filter = self::getBranchFilter();
        
        $sql = "SELECT p.*, b.name as branch_name 
                FROM patients p 
                LEFT JOIN branches b ON p.branch_id = b.id 
                WHERE (p.name LIKE ? 
                   OR p.phone LIKE ? 
                   OR p.email LIKE ? 
                   OR p.patient_id LIKE ?)";
        $params = ["%{$query}%", "%{$query}%", "%{$query}%", "%{$query}%"];
        
        if ($filter['hasFilter']) {
            $sql .= " AND p.branch_id = ?";
            $params[] = $filter['branchId'];
        }

        $sql .= " ORDER BY p.name ASC LIMIT 25";
        return $db->getAll($sql, $params);
    }

    /**
     * Find a patient by database ID with branch check.
     */
    public static function find(int $id): ?array
    {
        $db = Database::getInstance();
        $filter = self::getBranchFilter();
        
        $sql = "SELECT p.*, b.name as branch_name 
                FROM patients p 
                LEFT JOIN branches b ON p.branch_id = b.id 
                WHERE p.id = ?";
        $params = [$id];
        
        if ($filter['hasFilter']) {
            $sql .= " AND p.branch_id = ?";
            $params[] = $filter['branchId'];
        }
        
        return $db->getRow($sql, $params);
    }

    /**
     * Find a patient by unique Patient ID string with branch check.
     */
    public static function findByPatientId(string $patientId): ?array
    {
        $db = Database::getInstance();
        $filter = self::getBranchFilter();
        
        $sql = "SELECT p.*, b.name as branch_name 
                FROM patients p 
                LEFT JOIN branches b ON p.branch_id = b.id 
                WHERE p.patient_id = ?";
        $params = [$patientId];
        
        if ($filter['hasFilter']) {
            $sql .= " AND p.branch_id = ?";
            $params[] = $filter['branchId'];
        }
        
        return $db->getRow($sql, $params);
    }

    /**
     * Create a new patient.
     * Super Admin - যে কোন ব্রাঞ্চে তৈরি করতে পারে
     * বাকি সবাই - নিজের ব্রাঞ্চে ফোর্স তৈরি হবে
     */
    public static function create(array $data): ?int
    {
        $db = Database::getInstance();
        $filter = self::getBranchFilter();
        
        // Super Admin ছাড়া বাকি সবাই নিজের ব্রাঞ্চ ফোর্স সেট
        if ($filter['hasFilter']) {
            $data['branch_id'] = $filter['branchId'];
        }
        
        try {
            // 1. Generate unique Patient ID (PAT-YYYY-XXXX)
            $year = date('Y');
            $countRow = $db->getOne("SELECT COUNT(*) as count FROM patients WHERE YEAR(created_at) = ?", [$year]);
            $seq = ((int)$countRow) + 1;
            $patientId = sprintf("PAT-%s-%04d", $year, $seq);

            // 2. Generate QR Code
            $qrFilename = 'patient_' . $patientId . '.png';
            $qrUrl = site_url('/admin/patients/history/' . $patientId);
            $qrCodePath = QRHelper::generate($qrUrl, $qrFilename);

            // 3. Insert record - FIXED: Using $db->execute() properly
            $result = $db->execute(
                "INSERT INTO patients (
                    patient_id, branch_id, name, email, phone, gender, dob, 
                    blood_group, address, emergency_contact, allergies, 
                    medical_history, family_history, qr_code_url, status, created_at, updated_at
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'active', NOW(), NOW())",
                [
                    $patientId,
                    $data['branch_id'] ?? null,
                    $data['name'],
                    $data['email'] ?? null,
                    $data['phone'],
                    $data['gender'] ?? 'male',
                    $data['dob'],
                    $data['blood_group'] ?? null,
                    $data['address'] ?? '',
                    $data['emergency_contact'] ?? null,
                    $data['allergies'] ?? null,
                    $data['medical_history'] ?? null,
                    $data['family_history'] ?? null,
                    $qrCodePath
                ]
            );
            
            // execute() returns number of affected rows
            if ($result > 0) {
                return (int) $db->lastInsertId();
            }
            return null;
            
        } catch (\PDOException $e) {
            if ($e->getCode() == 23000) {
                Logger::error("Duplicate entry for patient: " . ($data['email'] ?? $data['phone']));
                return null;
            }
            Logger::error("Failed to register patient: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Update an existing patient's details with branch check.
     */
    public static function update(int $id, array $data): bool
    {
        $db = Database::getInstance();
        $filter = self::getBranchFilter();
        
        if ($filter['hasFilter']) {
            $data['branch_id'] = $filter['branchId'];
        }
        
        try {
            $sql = "UPDATE patients SET 
                        branch_id = ?,
                        name = ?, 
                        email = ?, 
                        phone = ?, 
                        gender = ?, 
                        dob = ?, 
                        blood_group = ?, 
                        address = ?, 
                        emergency_contact = ?, 
                        allergies = ?, 
                        medical_history = ?, 
                        family_history = ?, 
                        status = ?,
                        updated_at = NOW() 
                    WHERE id = ?";
            $params = [
                $data['branch_id'] ?? null,
                $data['name'],
                $data['email'] ?? null,
                $data['phone'],
                $data['gender'] ?? 'male',
                $data['dob'],
                $data['blood_group'] ?? null,
                $data['address'] ?? '',
                $data['emergency_contact'] ?? null,
                $data['allergies'] ?? null,
                $data['medical_history'] ?? null,
                $data['family_history'] ?? null,
                $data['status'] ?? 'active',
                $id
            ];
            
            if ($filter['hasFilter']) {
                $sql .= " AND branch_id = ?";
                $params[] = $filter['branchId'];
            }
            
            $result = $db->execute($sql, $params);
            return $result > 0;
            
        } catch (\PDOException $e) {
            Logger::error("Failed to update patient: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Delete a patient with branch check.
     */
    public static function delete(int $id): bool
    {
        $db = Database::getInstance();
        $filter = self::getBranchFilter();
        
        $sql = "DELETE FROM patients WHERE id = ?";
        $params = [$id];
        
        if ($filter['hasFilter']) {
            $sql .= " AND branch_id = ?";
            $params[] = $filter['branchId'];
        }
        
        $result = $db->execute($sql, $params);
        return $result > 0;
    }

    /**
     * Bind a health report document to a patient profile.
     */
    public static function addDocument(int $patientId, string $docName, string $filePath): bool
    {
        $db = Database::getInstance();
        $result = $db->execute(
            "INSERT INTO patient_documents (patient_id, document_name, file_path, uploaded_at) 
             VALUES (?, ?, ?, NOW())",
            [$patientId, $docName, $filePath]
        );
        return $result > 0;
    }

    /**
     * Retrieve health report documents for a patient.
     */
    public static function getDocuments(int $patientId): array
    {
        $db = Database::getInstance();
        return $db->getAll(
            "SELECT * FROM patient_documents WHERE patient_id = ? ORDER BY id DESC",
            [$patientId]
        );
    }

    /**
     * Delete a patient document by ID.
     */
    public static function deleteDocument(int $docId): bool
    {
        $db = Database::getInstance();
        $result = $db->execute("DELETE FROM patient_documents WHERE id = ?", [$docId]);
        return $result > 0;
    }

    /**
     * Retrieve document details by ID.
     */
    public static function getDocument(int $docId): ?array
    {
        $db = Database::getInstance();
        return $db->getRow("SELECT * FROM patient_documents WHERE id = ? LIMIT 1", [$docId]);
    }

    /**
     * Compile chronological patient visit timeline records.
     */
    public static function getTimeline(int $patientId): array
    {
        $db = Database::getInstance();
        $timeline = [];

        // 1. Fetch Appointments
        $appts = $db->getAll(
            "SELECT a.id, a.date, a.time_slot, a.status, a.type, a.token_number, u.username as doctor_name 
             FROM appointments a 
             JOIN users u ON a.doctor_id = u.id 
             WHERE a.patient_id = ? 
             ORDER BY a.date DESC, a.time_slot DESC",
            [$patientId]
        );
        foreach ($appts as $ap) {
            $timestamp = strtotime($ap['date'] . ' ' . $ap['time_slot']);
            $timeline[] = [
                'timestamp' => $timestamp,
                'date_display' => date('M d, Y h:i A', $timestamp),
                'type' => 'appointment',
                'title' => 'OPD Appointment Booked',
                'doctor' => $ap['doctor_name'],
                'detail' => sprintf("Token #%d, Type: %s, Status: %s", $ap['token_number'], ucfirst($ap['type']), ucfirst($ap['status'])),
                'badge' => $ap['status'] === 'completed' ? 'success' : 'primary'
            ];
        }

        // 2. Fetch Prescriptions
        $prescs = $db->getAll(
            "SELECT p.*, u.username as doctor_name 
             FROM prescriptions p 
             JOIN users u ON p.doctor_id = u.id 
             WHERE p.patient_id = ? 
             ORDER BY p.created_at DESC",
            [$patientId]
        );
        foreach ($prescs as $pr) {
            $timestamp = strtotime($pr['created_at']);
            $meds = $db->getAll(
                "SELECT * FROM prescription_medicines WHERE prescription_id = ?",
                [$pr['id']]
            );
            $medDetails = [];
            foreach ($meds as $m) {
                $medDetails[] = sprintf("%s (%s, %s, %s)", $m['medicine_name'], $m['dosage'], $m['frequency'], $m['duration']);
            }
            
            $timeline[] = [
                'timestamp' => $timestamp,
                'date_display' => date('M d, Y h:i A', $timestamp),
                'type' => 'prescription',
                'title' => 'Prescription Issued',
                'doctor' => $pr['doctor_name'],
                'detail' => sprintf("Diagnosis: %s\nAdvice: %s\nMedicines:\n - %s", $pr['diagnosis'], $pr['advice'] ?? 'None', implode("\n - ", $medDetails)),
                'badge' => 'info'
            ];
        }

        // 3. Fetch IPD Admissions
        $ipds = $db->getAll(
            "SELECT a.*, u.username as doctor_name, b.bed_number, r.room_number 
             FROM ipd_admissions a 
             JOIN users u ON a.doctor_id = u.id 
             JOIN ipd_beds b ON a.bed_id = b.id 
             JOIN ipd_rooms r ON b.room_id = r.id 
             WHERE a.patient_id = ? 
             ORDER BY a.admission_date DESC",
            [$patientId]
        );
        foreach ($ipds as $ip) {
            $timestamp = strtotime($ip['admission_date']);
            $discharge = $ip['discharge_date'] ? date('M d, Y h:i A', strtotime($ip['discharge_date'])) : 'Still Admitted';
            $timeline[] = [
                'timestamp' => $timestamp,
                'date_display' => date('M d, Y h:i A', $timestamp),
                'type' => 'ipd',
                'title' => 'IPD Bed Admission',
                'doctor' => $ip['doctor_name'],
                'detail' => sprintf("Diagnosis: %s\nRoom/Bed: %s / %s\nStatus: %s\nDischarged: %s", $ip['diagnosis'], $ip['room_number'], $ip['bed_number'], ucfirst($ip['status']), $discharge),
                'badge' => $ip['status'] === 'discharged' ? 'secondary' : 'danger'
            ];
        }

        // 4. Fetch Patient Documents
        $docs = self::getDocuments($patientId);
        foreach ($docs as $d) {
            $timestamp = strtotime($d['uploaded_at']);
            $timeline[] = [
                'timestamp' => $timestamp,
                'date_display' => date('M d, Y h:i A', $timestamp),
                'type' => 'document',
                'title' => 'Medical Report Uploaded',
                'doctor' => 'System Uploader',
                'detail' => sprintf("File: <a href='%s' target='_blank'>%s</a>", site_url($d['file_path']), esc($d['document_name'])),
                'badge' => 'warning'
            ];
        }

        // Sort descending by timestamp
        usort($timeline, function($a, $b) {
            return $b['timestamp'] <=> $a['timestamp'];
        });

        return $timeline;
    }
}