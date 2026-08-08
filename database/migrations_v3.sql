-- Clinic Management System - Phase 5-10 Schema Migration

-- Drop existing clinical tables to recreate with clean expanded schemas
DROP TABLE IF EXISTS patient_documents;
DROP TABLE IF EXISTS ipd_nursing_logs;
DROP TABLE IF EXISTS ipd_procedures;
DROP TABLE IF EXISTS ipd_admissions;
DROP TABLE IF EXISTS ipd_beds;
DROP TABLE IF EXISTS ipd_rooms;
DROP TABLE IF EXISTS prescription_medicines;
DROP TABLE IF EXISTS prescriptions;
DROP TABLE IF EXISTS appointments;
DROP TABLE IF EXISTS doctor_schedules;
DROP TABLE IF EXISTS billing;
DROP TABLE IF EXISTS patients;

-- 1. Create Patients Table
CREATE TABLE patients (
    id INT AUTO_INCREMENT PRIMARY KEY,
    patient_id VARCHAR(30) NOT NULL UNIQUE,
    branch_id INT NULL,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NULL,
    phone VARCHAR(20) NOT NULL,
    gender ENUM('male', 'female', 'other') NOT NULL,
    dob DATE NOT NULL,
    blood_group VARCHAR(5) NULL,
    address TEXT NOT NULL,
    emergency_contact VARCHAR(100) NULL,
    allergies TEXT NULL,
    medical_history TEXT NULL,
    family_history TEXT NULL,
    qr_code_url VARCHAR(255) NULL,
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (branch_id) REFERENCES branches(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. Create Doctor Schedules Table
CREATE TABLE doctor_schedules (
    id INT AUTO_INCREMENT PRIMARY KEY,
    doctor_id INT NOT NULL,
    day_of_week ENUM('Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday') NOT NULL,
    start_time TIME NOT NULL,
    end_time TIME NOT NULL,
    slot_duration INT DEFAULT 15, -- Duration in minutes
    max_patients INT DEFAULT 20,
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (doctor_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3. Create Appointments (Queue & Token tracking) Table
CREATE TABLE appointments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    patient_id INT NOT NULL,
    doctor_id INT NOT NULL,
    branch_id INT NOT NULL,
    date DATE NOT NULL,
    time_slot TIME NOT NULL,
    status ENUM('pending', 'approved', 'cancelled', 'completed') DEFAULT 'pending',
    type ENUM('walk-in', 'online') DEFAULT 'walk-in',
    token_number INT NOT NULL,
    queue_status ENUM('waiting', 'in_consultation', 'completed', 'skipped') DEFAULT 'waiting',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (patient_id) REFERENCES patients(id) ON DELETE CASCADE,
    FOREIGN KEY (doctor_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (branch_id) REFERENCES branches(id) ON DELETE CASCADE,
    UNIQUE KEY unique_token (doctor_id, date, token_number)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 4. Create Prescriptions Table
CREATE TABLE prescriptions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    appointment_id INT NULL,
    patient_id INT NOT NULL,
    doctor_id INT NOT NULL,
    symptoms TEXT NOT NULL,
    diagnosis TEXT NOT NULL,
    treatment TEXT NOT NULL,
    advice TEXT NULL,
    follow_up_date DATE NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (appointment_id) REFERENCES appointments(id) ON DELETE SET NULL,
    FOREIGN KEY (patient_id) REFERENCES patients(id) ON DELETE CASCADE,
    FOREIGN KEY (doctor_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 5. Create Prescription Medicines Table
CREATE TABLE prescription_medicines (
    id INT AUTO_INCREMENT PRIMARY KEY,
    prescription_id INT NOT NULL,
    medicine_name VARCHAR(150) NOT NULL,
    dosage VARCHAR(100) NOT NULL, -- e.g., 500 mg
    frequency VARCHAR(100) NOT NULL, -- e.g., 1-0-1
    duration VARCHAR(50) NOT NULL, -- e.g., 5 Days
    instructions VARCHAR(255) NULL, -- e.g., After food
    issued_status ENUM('pending', 'issued') DEFAULT 'pending',
    FOREIGN KEY (prescription_id) REFERENCES prescriptions(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 6. Create Medical Certificates Table
CREATE TABLE medical_certificates (
    id INT AUTO_INCREMENT PRIMARY KEY,
    patient_id INT NOT NULL,
    doctor_id INT NOT NULL,
    diagnosis TEXT NOT NULL,
    start_date DATE NOT NULL,
    end_date DATE NOT NULL,
    reason TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (patient_id) REFERENCES patients(id) ON DELETE CASCADE,
    FOREIGN KEY (doctor_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 7. Create IPD Rooms Table
CREATE TABLE ipd_rooms (
    id INT AUTO_INCREMENT PRIMARY KEY,
    room_number VARCHAR(20) NOT NULL UNIQUE,
    type ENUM('general', 'semi-private', 'private', 'ICU') NOT NULL,
    price_per_day DECIMAL(10,2) NOT NULL,
    status ENUM('active', 'inactive') DEFAULT 'active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 8. Create IPD Beds Table
CREATE TABLE ipd_beds (
    id INT AUTO_INCREMENT PRIMARY KEY,
    room_id INT NOT NULL,
    bed_number VARCHAR(20) NOT NULL,
    status ENUM('available', 'occupied') DEFAULT 'available',
    FOREIGN KEY (room_id) REFERENCES ipd_rooms(id) ON DELETE CASCADE,
    UNIQUE KEY unique_room_bed (room_id, bed_number)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 9. Create IPD Admissions Table
CREATE TABLE ipd_admissions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    patient_id INT NOT NULL,
    doctor_id INT NOT NULL,
    bed_id INT NOT NULL,
    admission_date DATETIME NOT NULL,
    discharge_date DATETIME NULL,
    symptoms TEXT NOT NULL,
    diagnosis TEXT NOT NULL,
    status ENUM('admitted', 'discharged') DEFAULT 'admitted',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (patient_id) REFERENCES patients(id) ON DELETE CASCADE,
    FOREIGN KEY (doctor_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (bed_id) REFERENCES ipd_beds(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 10. Create IPD Nursing Logs Table
CREATE TABLE ipd_nursing_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ipd_admission_id INT NOT NULL,
    vit_temp VARCHAR(10) NULL,
    vit_bp VARCHAR(15) NULL,
    vit_pulse VARCHAR(10) NULL,
    notes TEXT NOT NULL,
    recorded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (ipd_admission_id) REFERENCES ipd_admissions(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 11. Create IPD Procedures Table
CREATE TABLE ipd_procedures (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ipd_admission_id INT NOT NULL,
    name VARCHAR(150) NOT NULL,
    doctor_id INT NOT NULL,
    cost DECIMAL(10,2) NOT NULL,
    status ENUM('scheduled', 'completed', 'cancelled') DEFAULT 'completed',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (ipd_admission_id) REFERENCES ipd_admissions(id) ON DELETE CASCADE,
    FOREIGN KEY (doctor_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 12. Create Billing Table
CREATE TABLE billing (
    id INT AUTO_INCREMENT PRIMARY KEY,
    patient_id INT NOT NULL,
    branch_id INT NOT NULL,
    type ENUM('opd', 'ipd', 'appointment') NOT NULL,
    reference_id INT NOT NULL, -- appointment_id or ipd_admission_id
    subtotal DECIMAL(10,2) NOT NULL,
    discount DECIMAL(10,2) DEFAULT 0.00,
    tax DECIMAL(10,2) DEFAULT 0.00,
    total DECIMAL(10,2) NOT NULL,
    paid_amount DECIMAL(10,2) DEFAULT 0.00,
    payment_status ENUM('paid', 'unpaid', 'partial') DEFAULT 'unpaid',
    payment_method ENUM('cash', 'card', 'upi', 'none') DEFAULT 'none',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (patient_id) REFERENCES patients(id) ON DELETE CASCADE,
    FOREIGN KEY (branch_id) REFERENCES branches(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 13. Create Patient Documents Table
CREATE TABLE patient_documents (
    id INT AUTO_INCREMENT PRIMARY KEY,
    patient_id INT NOT NULL,
    document_name VARCHAR(150) NOT NULL,
    file_path VARCHAR(255) NOT NULL,
    uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (patient_id) REFERENCES patients(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 14. Expand System Permissions List
INSERT INTO permissions (name, slug, description) VALUES
('Manage Patients', 'manage_patients', 'Create and search patient clinical records'),
('Manage Appointments', 'manage_appointments', 'Schedule time slots and approve appointments'),
('Record Attendance', 'record_attendance', 'Log employee daily shift check-ins'),
('Manage IPD', 'manage_ipd', 'Manage IPD admissions, bed mapping, nursing, and discharge'),
('View Doctor Dashboard', 'view_doctor_dashboard', 'Access doctor consultation queues and prescriptions'),
('View Reception Dashboard', 'view_reception_dashboard', 'Access reception dashboard and billing tools')
ON DUPLICATE KEY UPDATE description=VALUES(description);

-- 15. Seed Map Rooms & Beds
INSERT INTO ipd_rooms (room_number, type, price_per_day, status) VALUES
('Room-101', 'general', 800.00, 'active'),
('Room-102', 'semi-private', 1500.00, 'active'),
('Room-201', 'private', 3000.00, 'active'),
('ICU-01', 'ICU', 7500.00, 'active')
ON DUPLICATE KEY UPDATE price_per_day=VALUES(price_per_day);

INSERT INTO ipd_beds (room_id, bed_number, status) VALUES
(1, 'G-Bed-1', 'available'),
(1, 'G-Bed-2', 'available'),
(1, 'G-Bed-3', 'available'),
(2, 'SP-Bed-1', 'available'),
(2, 'SP-Bed-2', 'available'),
(3, 'P-Bed-1', 'available'),
(4, 'ICU-Bed-1', 'available')
ON DUPLICATE KEY UPDATE status=VALUES(status);

-- 16. Map permissions to Role Mappings (1 = Super Admin, 2 = Doctor, 3 = Receptionist, 4 = Nurse)
INSERT IGNORE INTO role_permissions (role_id, permission_id) VALUES
-- Super Admin maps to all (will also bypass programmatically)
(1, 1), (1, 2), (1, 3), (1, 4), (1, 5), (1, 6),
-- Doctor Mappings
(2, 1), (2, 5),
-- Receptionist Mappings
(3, 1), (3, 2), (3, 6), (3, 4),
-- Nurse Mappings
(4, 4);
