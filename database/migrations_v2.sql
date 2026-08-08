-- Clinic Database Migrations - Phases 2, 3 & 4
USE `clinic_db`;

-- 1. Create Roles and Permissions Tables
CREATE TABLE IF NOT EXISTS `roles` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(50) NOT NULL UNIQUE,
  `slug` VARCHAR(50) NOT NULL UNIQUE,
  `description` TEXT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `permissions` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(50) NOT NULL UNIQUE,
  `slug` VARCHAR(50) NOT NULL UNIQUE,
  `description` TEXT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `role_permissions` (
  `role_id` INT NOT NULL,
  `permission_id` INT NOT NULL,
  PRIMARY KEY (`role_id`, `permission_id`),
  FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE,
  FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. Create Branches Table
CREATE TABLE IF NOT EXISTS `branches` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(100) NOT NULL UNIQUE,
  `logo` VARCHAR(255) NULL,
  `address` TEXT NOT NULL,
  `phone` VARCHAR(20) NOT NULL,
  `emergency_number` VARCHAR(20) NOT NULL,
  `email` VARCHAR(100) NOT NULL,
  `google_map_link` TEXT NULL,
  `opening_hours` VARCHAR(255) NOT NULL,
  `status` ENUM('active', 'inactive') NOT NULL DEFAULT 'active',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3. Alter Users Table to Support Roles, Branches, Remember Me, OTP, and Timestamps
ALTER TABLE `users` 
  ADD COLUMN `role_id` INT NULL AFTER `role`,
  ADD COLUMN `branch_id` INT NULL AFTER `role_id`,
  ADD COLUMN `remember_token` VARCHAR(255) NULL AFTER `password_hash`,
  ADD COLUMN `otp_code` VARCHAR(6) NULL AFTER `remember_token`,
  ADD COLUMN `otp_expires_at` TIMESTAMP NULL AFTER `otp_code`,
  ADD COLUMN `last_login_at` TIMESTAMP NULL AFTER `otp_expires_at`,
  ADD COLUMN `last_active_at` TIMESTAMP NULL AFTER `last_login_at`,
  ADD CONSTRAINT `fk_user_role` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_user_branch` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE SET NULL;

-- 4. Create Employees, Documents, Attendance and Log Tables
CREATE TABLE IF NOT EXISTS `employees` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT NOT NULL UNIQUE,
  `photo` VARCHAR(255) NULL,
  `salary` DECIMAL(10,2) NOT NULL DEFAULT '0.00',
  `shift_start` TIME NOT NULL DEFAULT '09:00:00',
  `shift_end` TIME NOT NULL DEFAULT '17:00:00',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `employee_documents` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `employee_id` INT NOT NULL,
  `document_name` VARCHAR(150) NOT NULL,
  `file_path` VARCHAR(255) NOT NULL,
  `uploaded_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `attendance` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `employee_id` INT NOT NULL,
  `date` DATE NOT NULL,
  `status` ENUM('present', 'absent', 'late', 'leave') NOT NULL DEFAULT 'present',
  `check_in_time` TIME NULL,
  `check_out_time` TIME NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY `idx_emp_date` (`employee_id`, `date`),
  FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `login_history` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT NOT NULL,
  `ip_address` VARCHAR(45) NOT NULL,
  `user_agent` VARCHAR(255) NOT NULL,
  `logged_in_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `logged_out_at` TIMESTAMP NULL,
  FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 5. Create Auxiliary Patients and Billing Tables for Dashboard Analytics
CREATE TABLE IF NOT EXISTS `patients` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(100) NOT NULL,
  `email` VARCHAR(100) NOT NULL UNIQUE,
  `branch_id` INT NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `billing` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `patient_id` INT NOT NULL,
  `branch_id` INT NOT NULL,
  `amount` DECIMAL(10,2) NOT NULL DEFAULT '0.00',
  `status` ENUM('paid', 'unpaid') NOT NULL DEFAULT 'unpaid',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`patient_id`) REFERENCES `patients` (`id`) ON DELETE CASCADE,
  FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 6. Seeding Default Roles
INSERT INTO `roles` (`id`, `name`, `slug`, `description`) VALUES
(1, 'Super Administrator', 'super_admin', 'Full access to all modules and configurations'),
(2, 'Doctor', 'doctor', 'Medical staff role for clinic branches'),
(3, 'Receptionist', 'receptionist', 'Front office desk registration and logs management'),
(4, 'Nurse', 'nurse', 'Nursing and patient assistance staff'),
(5, 'Lab Staff', 'lab_staff', 'Laboratory report and diagnostics technician'),
(6, 'Pharmacist', 'pharmacist', 'Pharmacy and medicine inventory control'),
(7, 'Manager', 'manager', 'Branch manager role for operations control'),
(8, 'HR', 'hr', 'Human resources coordinator for employee payroll and shifts'),
(9, 'Accounts', 'accounts', 'Financial books and billing dashboard viewer')
ON DUPLICATE KEY UPDATE `name`=`name`;

-- 7. Seeding Core Permissions
INSERT INTO `permissions` (`id`, `name`, `slug`, `description`) VALUES
(1, 'Manage Branches', 'manage_branches', 'Allows CRUD operations on clinic branches'),
(2, 'Manage Employees', 'manage_employees', 'Allows employee enrollment, payroll, and document uploads'),
(3, 'Manage Settings', 'manage_settings', 'Allows updating SMTP and WhatsApp configuration keys'),
(4, 'View Audit Logs', 'view_logs', 'Allows viewing administrator activity logs'),
(5, 'Record Attendance', 'record_attendance', 'Allows check-in and check-out attendance recording'),
(6, 'View Branch Analytics', 'view_branch_dashboard', 'Allows viewing patient counts, revenue metrics, and reports')
ON DUPLICATE KEY UPDATE `name`=`name`;

-- 8. Map All Permissions to Super Admin
INSERT IGNORE INTO `role_permissions` (`role_id`, `permission_id`) VALUES
(1, 1), (1, 2), (1, 3), (1, 4), (1, 5), (1, 6);

-- 9. Update existing Admin User to link role_id = 1 (Super Admin)
UPDATE `users` SET `role_id` = 1 WHERE `username` = 'admin';
