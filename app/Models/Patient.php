<?php
declare(strict_types=1);

namespace App\Models;

use App\Helpers\Database;
use App\Helpers\QRHelper;
use App\Helpers\Logger;

class Patient
{
    /**
     * Editable patient profile fields mapped to their default values.
     *
     * @var array<string, string>
     */
    public const PROFILE_FIELDS = [
        'name' => '',
        'email' => '',
        'phone' => '',
        'gender' => 'male',
        'dob' => '',
        'blood_group' => '',
        'address' => '',
        'emergency_contact' => '',
        'allergies' => '',
        'medical_history' => '',
        'family_history' => ''
    ];

    /**
     * Retrieve all patients.
     */
    public static function all(?int $branchId = null): array
    {
        $sql = "SELECT p.*, b.name as branch_name 
                FROM patients p 
                LEFT JOIN branches b ON p.branch_id = b.id";
        
        $params = [];
        $sql = Database::scopeToBranch($sql, $params, $branchId, 'p.branch_id');
        
        $sql .= " ORDER BY p.id DESC";
        return Database::all($sql, $params);
    }

    /**
     * Find a patient by database ID.
     */
    public static function find(int $id): ?array
    {
        $sql = "SELECT p.*, b.name as branch_name 
                FROM patients p 
                LEFT JOIN branches b ON p.branch_id = b.id 
                WHERE p.id = :id LIMIT 1";
        return Database::row($sql, ['id' => $id]);
    }

    /**
     * Find a patient by unique Patient ID string.
     */
    public static function findByPatientId(string $patientId): ?array
    {
        $sql = "SELECT p.*, b.name as branch_name 
                FROM patients p 
                LEFT JOIN branches b ON p.branch_id = b.id 
                WHERE p.patient_id = :patient_id LIMIT 1";
        return Database::row($sql, ['patient_id' => $patientId]);
    }

    /**
     * Search patient records by criteria.
     */
    public static function search(string $query, ?int $branchId = null): array
    {
        $sql = "SELECT p.*, b.name as branch_name 
                FROM patients p 
                LEFT JOIN branches b ON p.branch_id = b.id 
                WHERE (p.name LIKE :q 
                   OR p.phone LIKE :q 
                   OR p.email LIKE :q 
                   OR p.patient_id LIKE :q)";
        
        $params = ['q' => '%' . $query . '%'];
        $sql = Database::scopeToBranch($sql, $params, $branchId, 'p.branch_id');

        $sql .= " ORDER BY p.name ASC LIMIT 25";
        return Database::all($sql, $params);
    }

    /**
     * Create a new patient and generate a unique ID & QR code.
     */
    public static function create(array $data): ?int
    {
        // 1. Generate unique Patient ID (PAT-YYYY-XXXX)
        $year = date('Y');
        try {
            $countRow = Database::row("SELECT COUNT(*) as count FROM patients WHERE YEAR(created_at) = :year", ['year' => $year]);
            $seq = ($countRow['count'] ?? 0) + 1;
            $patientId = sprintf("PAT-%s-%04d", $year, $seq);

            // 2. Generate QR Code containing patient portal details link
            $qrFilename = 'patient_' . $patientId . '.png';
            $qrUrl = site_url('/admin/patients/history/' . $patientId);
            $qrCodePath = QRHelper::generate($qrUrl, $qrFilename);

            // 3. Insert record
            $sql = "INSERT INTO patients (patient_id, branch_id, name, email, phone, gender, dob, blood_group, address, emergency_contact, allergies, medical_history, family_history, qr_code_url, status, created_at, updated_at) 
                    VALUES (:patient_id, :branch_id, :name, :email, :phone, :gender, :dob, :blood_group, :address, :emergency_contact, :allergies, :medical_history, :family_history, :qr_code_url, :status, NOW(), NOW())";
            
            $success = Database::execute($sql, [
                'patient_id' => $patientId,
                'branch_id' => $data['branch_id'] ?? null,
                'name' => $data['name'],
                'email' => $data['email'] ?? null,
                'phone' => $data['phone'],
                'gender' => $data['gender'],
                'dob' => $data['dob'],
                'blood_group' => $data['blood_group'] ?? null,
                'address' => $data['address'],
                'emergency_contact' => $data['emergency_contact'] ?? null,
                'allergies' => $data['allergies'] ?? null,
                'medical_history' => $data['medical_history'] ?? null,
                'family_history' => $data['family_history'] ?? null,
                'qr_code_url' => $qrCodePath,
                'status' => $data['status'] ?? 'active'
            ]);

            return $success ? (int)Database::lastInsertId() : null;
        } catch (\Throwable $e) {
            Logger::error("Failed to register patient profile: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Update an existing patient's details.
     */
    public static function update(int $id, array $data): bool
    {
        $sql = "UPDATE patients SET 
                    branch_id = :branch_id,
                    name = :name, 
                    email = :email, 
                    phone = :phone, 
                    gender = :gender, 
                    dob = :dob, 
                    blood_group = :blood_group, 
                    address = :address, 
                    emergency_contact = :emergency_contact, 
                    allergies = :allergies, 
                    medical_history = :medical_history, 
                    family_history = :family_history, 
                    status = :status,
                    updated_at = NOW() 
                WHERE id = :id";
        
        return Database::execute($sql, [
            'id' => $id,
            'branch_id' => $data['branch_id'] ?? null,
            'name' => $data['name'],
            'email' => $data['email'] ?? null,
            'phone' => $data['phone'],
            'gender' => $data['gender'],
            'dob' => $data['dob'],
            'blood_group' => $data['blood_group'] ?? null,
            'address' => $data['address'],
            'emergency_contact' => $data['emergency_contact'] ?? null,
            'allergies' => $data['allergies'] ?? null,
            'medical_history' => $data['medical_history'] ?? null,
            'family_history' => $data['family_history'] ?? null,
            'status' => $data['status'] ?? 'active'
        ]);
    }

    /**
     * Delete a patient.
     */
    public static function delete(int $id): bool
    {
        return Database::execute("DELETE FROM patients WHERE id = :id", ['id' => $id]);
    }

    /**
     * Bind a health report document to a patient profile.
     */
    public static function addDocument(int $patientId, string $docName, string $filePath): bool
    {
        $sql = "INSERT INTO patient_documents (patient_id, document_name, file_path, uploaded_at) 
                VALUES (:patient_id, :document_name, :file_path, NOW())";
        return Database::execute($sql, [
            'patient_id' => $patientId,
            'document_name' => $docName,
            'file_path' => $filePath
        ]);
    }

    /**
     * Retrieve health report documents for a patient.
     */
    public static function getDocuments(int $patientId): array
    {
        return Database::all("SELECT * FROM patient_documents WHERE patient_id = :id ORDER BY id DESC", ['id' => $patientId]);
    }

    /**
     * Delete a patient document by ID.
     */
    public static function deleteDocument(int $docId): bool
    {
        return Database::execute("DELETE FROM patient_documents WHERE id = :id", ['id' => $docId]);
    }

    /**
     * Retrieve document details by ID.
     */
    public static function getDocument(int $docId): ?array
    {
        return Database::row("SELECT * FROM patient_documents WHERE id = :id LIMIT 1", ['id' => $docId]);
    }

    /**
     * Compile chronological patient visit timeline records.
     */
    public static function getTimeline(int $patientId): array
    {
        $timeline = [];

        // 1. Fetch Appointments
        $appts = Database::all(
            "SELECT a.id, a.date, a.time_slot, a.status, a.type, a.token_number, u.username as doctor_name 
             FROM appointments a 
             JOIN users u ON a.doctor_id = u.id 
             WHERE a.patient_id = :id ORDER BY a.date DESC, a.time_slot DESC", 
            ['id' => $patientId]
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
        $prescs = Database::all(
            "SELECT p.*, u.username as doctor_name 
             FROM prescriptions p 
             JOIN users u ON p.doctor_id = u.id 
             WHERE p.patient_id = :id ORDER BY p.created_at DESC", 
            ['id' => $patientId]
        );
        foreach ($prescs as $pr) {
            $timestamp = strtotime($pr['created_at']);
            // Fetch medicines
            $meds = Database::all("SELECT * FROM prescription_medicines WHERE prescription_id = :id", ['id' => $pr['id']]);
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
        $ipds = Database::all(
            "SELECT a.*, u.username as doctor_name, b.bed_number, r.room_number 
             FROM ipd_admissions a 
             JOIN users u ON a.doctor_id = u.id 
             JOIN ipd_beds b ON a.bed_id = b.id 
             JOIN ipd_rooms r ON b.room_id = r.id 
             WHERE a.patient_id = :id ORDER BY a.admission_date DESC", 
            ['id' => $patientId]
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
