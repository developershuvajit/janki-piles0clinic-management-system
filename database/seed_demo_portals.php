<?php
declare(strict_types=1);

require_once __DIR__ . '/../config/config.php';

use App\Helpers\Database;
use App\Helpers\Security;

echo "=== Seeding MedClinic Demo Portals Data ===\n\n";

try {
    // 1. Ensure Roles Exist
    $roles = [
        ['id' => 1, 'name' => 'Super Administrator', 'slug' => 'super_admin'],
        ['id' => 2, 'name' => 'Doctor', 'slug' => 'doctor'],
        ['id' => 3, 'name' => 'Receptionist', 'slug' => 'receptionist']
    ];
    foreach ($roles as $r) {
        Database::execute(
            "INSERT INTO roles (id, name, slug) VALUES (:id, :name, :slug) ON DUPLICATE KEY UPDATE name=VALUES(name)",
            $r
        );
    }

    // 2. Ensure Main Branch Exists
    $branch = Database::row("SELECT id FROM branches LIMIT 1");
    if (!$branch) {
        Database::execute(
            "INSERT INTO branches (id, name, address, phone, emergency_number, email, opening_hours, status) 
             VALUES (1, 'Dehradun Main Clinic', '101 Rajpur Road, Dehradun, Uttarakhand', '+91 98765 43210', '108', 'dehradun@medclinic.com', '08:00 AM - 09:00 PM', 'active')"
        );
        $branchId = 1;
    } else {
        $branchId = (int)$branch['id'];
    }

    // 3. Create Demo Password Hash ("Password123")
    $passHash = Security::hashPassword('Password123');

    // 4. Seed / Reset Demo Users
    $demoUsers = [
        [
            'username' => 'admin',
            'email' => 'admin@medclinic.com',
            'role_str' => 'admin',
            'role_id' => 1,
            'branch_id' => null
        ],
        [
            'username' => 'receptionist',
            'email' => 'reception@medclinic.com',
            'role_str' => 'staff',
            'role_id' => 3,
            'branch_id' => $branchId
        ],
        [
            'username' => 'doctor',
            'email' => 'doctor@medclinic.com',
            'role_str' => 'staff',
            'role_id' => 2,
            'branch_id' => $branchId
        ]
    ];

    $userIds = [];
    foreach ($demoUsers as $u) {
        $existing = Database::row("SELECT id FROM users WHERE username = :username", ['username' => $u['username']]);
        if ($existing) {
            Database::execute(
                "UPDATE users SET email = :email, password_hash = :hash, role = :role, role_id = :role_id, branch_id = :branch_id, status = 'active' WHERE id = :id",
                [
                    'email' => $u['email'],
                    'hash' => $passHash,
                    'role' => $u['role_str'],
                    'role_id' => $u['role_id'],
                    'branch_id' => $u['branch_id'],
                    'id' => $existing['id']
                ]
            );
            $userIds[$u['username']] = (int)$existing['id'];
        } else {
            Database::execute(
                "INSERT INTO users (username, email, password_hash, role, role_id, branch_id, status) 
                 VALUES (:username, :email, :hash, :role, :role_id, :branch_id, 'active')",
                [
                    'username' => $u['username'],
                    'email' => $u['email'],
                    'hash' => $passHash,
                    'role' => $u['role_str'],
                    'role_id' => $u['role_id'],
                    'branch_id' => $u['branch_id']
                ]
            );
            $userIds[$u['username']] = (int)Database::lastInsertId();
        }
    }

    echo "Demo User Accounts Ready:\n";
    echo " - Super Admin : admin / Password123\n";
    echo " - Reception   : receptionist / Password123\n";
    echo " - Doctor      : doctor / Password123\n\n";

    // 5. Seed Doctor Profile
    $doctorId = $userIds['doctor'];
    $docProfile = Database::row("SELECT id FROM doctor_profiles WHERE user_id = :id", ['id' => $doctorId]);
    if (!$docProfile) {
        Database::execute(
            "INSERT INTO doctor_profiles (user_id, qualification, experience, specialization, availability_schedule) 
             VALUES (:id, 'MBBS, MS (General Surgery)', '14 Years', 'Senior Proctologist & General Surgeon', 'Mon-Sat: 09:00 AM - 02:00 PM & 05:00 PM - 08:00 PM')",
            ['id' => $doctorId]
        );
    }

    // 6. Seed Demo Patients
    $demoPatients = [
        [
            'patient_id' => 'PAT-2026-0001',
            'branch_id' => $branchId,
            'name' => 'Rajesh Sharma',
            'email' => 'rajesh.sharma@example.com',
            'phone' => '+91 98111 22334',
            'gender' => 'male',
            'dob' => '1985-06-15',
            'blood_group' => 'B+',
            'address' => '45 Canal Road, Jakhan, Dehradun',
            'emergency_contact' => 'Sunita Sharma (+91 98111 22335)',
            'allergies' => 'Penicillin',
            'medical_history' => 'Chronic Piles, Mild Hypertension'
        ],
        [
            'patient_id' => 'PAT-2026-0002',
            'branch_id' => $branchId,
            'name' => 'Priya Verma',
            'email' => 'priya.v@example.com',
            'phone' => '+91 98222 33445',
            'gender' => 'female',
            'dob' => '1992-11-20',
            'blood_group' => 'O+',
            'address' => '12 Rajpur Road, Dehradun',
            'emergency_contact' => 'Amit Verma (+91 98222 33446)',
            'allergies' => 'Sulfa drugs',
            'medical_history' => 'Anal Fissure'
        ],
        [
            'patient_id' => 'PAT-2026-0003',
            'branch_id' => $branchId,
            'name' => 'Vikram Joshi',
            'email' => 'vikram.j@example.com',
            'phone' => '+91 98333 44556',
            'gender' => 'male',
            'dob' => '1978-03-10',
            'blood_group' => 'A+',
            'address' => '88 Clock Tower Avenue, Dehradun',
            'emergency_contact' => 'Kavita Joshi (+91 98333 44557)',
            'allergies' => 'None',
            'medical_history' => 'Fistula in Ano (Post-op recovery)'
        ]
    ];

    $patientIds = [];
    foreach ($demoPatients as $p) {
        $existP = Database::row("SELECT id FROM patients WHERE patient_id = :pid", ['pid' => $p['patient_id']]);
        if ($existP) {
            $patientIds[] = (int)$existP['id'];
        } else {
            Database::execute(
                "INSERT INTO patients (patient_id, branch_id, name, email, phone, gender, dob, blood_group, address, emergency_contact, allergies, medical_history, created_at) 
                 VALUES (:patient_id, :branch_id, :name, :email, :phone, :gender, :dob, :blood_group, :address, :emergency_contact, :allergies, :medical_history, NOW())",
                $p
            );
            $patientIds[] = (int)Database::lastInsertId();
        }
    }
    echo "Demo Patients Seeded: " . count($patientIds) . "\n";

    // 7. Seed OPD Appointments & Token Queue for Today
    $today = date('Y-m-d');
    foreach ($patientIds as $idx => $pid) {
        $tokenNum = $idx + 1;
        $existAppt = Database::row(
            "SELECT id FROM appointments WHERE doctor_id = :doc AND date = :date AND token_number = :tok",
            ['doc' => $doctorId, 'date' => $today, 'tok' => $tokenNum]
        );
        if (!$existAppt) {
            $queueStat = ($tokenNum === 1) ? 'in_consultation' : (($tokenNum === 2) ? 'waiting' : 'completed');
            $status = ($tokenNum === 3) ? 'completed' : 'approved';
            Database::execute(
                "INSERT INTO appointments (patient_id, doctor_id, branch_id, date, time_slot, status, type, token_number, queue_status, created_at) 
                 VALUES (:pid, :doc, :bid, :date, '10:30:00', :status, 'walk-in', :token, :queue, NOW())",
                [
                    'pid' => $pid,
                    'doc' => $doctorId,
                    'bid' => $branchId,
                    'date' => $today,
                    'status' => $status,
                    'token' => $tokenNum,
                    'queue' => $queueStat
                ]
            );
        }
    }
    echo "Demo OPD Appointments & Queue Seeded for Today ({$today}).\n";

    // 8. Seed Demo IPD Admission
    $ipdPatientId = $patientIds[2] ?? $patientIds[0];
    $bed = Database::row("SELECT id FROM ipd_beds WHERE status = 'available' LIMIT 1");
    $bedId = $bed ? (int)$bed['id'] : 1;

    $existIpd = Database::row("SELECT id FROM ipd_admissions WHERE patient_id = :pid AND status = 'admitted'", ['pid' => $ipdPatientId]);
    if (!$existIpd) {
        Database::execute(
            "INSERT INTO ipd_admissions (patient_id, doctor_id, bed_id, admission_date, symptoms, diagnosis, status, discharge_approval, created_at) 
             VALUES (:pid, :doc, :bed, NOW() - INTERVAL 2 DAY, 'Severe anal pain, bleeding, swelling', 'Gr-III Internal Hemorrhoids & Fissure', 'admitted', 'approved', NOW())",
            ['pid' => $ipdPatientId, 'doc' => $doctorId, 'bed' => $bedId]
        );
        $ipdId = (int)Database::lastInsertId();
        Database::execute("UPDATE ipd_beds SET status = 'occupied' WHERE id = :id", ['id' => $bedId]);

        // Seed Nursing Vitals Log
        Database::execute(
            "INSERT INTO ipd_nursing_logs (ipd_admission_id, vit_temp, vit_bp, vit_pulse, notes, recorded_at) 
             VALUES (:ipd, '98.6', '120/80', '74', 'Patient comfortable post Ksharsutra procedure. Vitals stable.', NOW())",
            ['ipd' => $ipdId]
        );

        // Seed Procedure Note
        Database::execute(
            "INSERT INTO ipd_procedures (ipd_admission_id, name, doctor_id, cost, status, created_at) 
             VALUES (:ipd, 'Kshar Sutra Ligation & Fissurectomy', :doc, 3500.00, 'completed', NOW())",
            ['ipd' => $ipdId, 'doc' => $doctorId]
        );

        // Seed Discharge Summary Draft
        Database::execute(
            "INSERT INTO ipd_discharge_summaries (ipd_admission_id, diagnosis, treatment_summary, procedure_summary, operation_notes, advice, medicine_advice, diet, follow_up_instructions) 
             VALUES (:ipd, 'Gr-III Internal Hemorrhoids & Fissure in Ano', 'Admitted for Ksharsutra therapy. Post-op recovery uneventful.', 'Ksharsutra Application', 'Local anesthesia applied. Primary thread tied.', 'Avoid constipation. Take Sitz bath twice daily.', 'Tab Paracetamol 650mg BD, Ointment Anovate', 'High fiber diet, 3L water daily.', 'Follow up on Monday for thread inspection.')",
            ['ipd' => $ipdId]
        );
    }
    echo "Demo IPD Admission & Doctor Approved Discharge Summary Seeded.\n";

    // 9. Seed Demo Billing & Medicines Issue
    $existBill = Database::row("SELECT id FROM billing WHERE patient_id = :pid LIMIT 1", ['pid' => $patientIds[0]]);
    if (!$existBill) {
        Database::execute(
            "INSERT INTO billing (patient_id, branch_id, type, reference_id, subtotal, discount, tax, total, paid_amount, payment_status, payment_method, created_at) 
             VALUES (:pid, :bid, 'opd', 1, 500.00, 0.00, 0.00, 500.00, 500.00, 'paid', 'cash', NOW())",
            ['pid' => $patientIds[0], 'bid' => $branchId]
        );
    }

    echo "\n=== DEMO DATA SEEDING COMPLETE ===\n";
} catch (\Throwable $e) {
    echo "Error Seeding Data: " . $e->getMessage() . "\n";
}
