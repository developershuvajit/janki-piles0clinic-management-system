-- Migration v6: Doctor & Reception Portal Extensions
USE `clinic_db`;

-- 1. Doctor Profiles Table
CREATE TABLE IF NOT EXISTS `doctor_profiles` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT NOT NULL UNIQUE,
    `qualification` VARCHAR(255) NULL,
    `experience` VARCHAR(100) NULL,
    `specialization` VARCHAR(150) NULL,
    `availability_schedule` TEXT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. Add Discharge Approval status to IPD Admissions
ALTER TABLE `ipd_admissions` 
    ADD COLUMN `discharge_approval` ENUM('none', 'pending', 'approved') DEFAULT 'none' AFTER `status`;

-- 3. Add Clinical Details to IPD Discharge Summaries
ALTER TABLE `ipd_discharge_summaries` 
    ADD COLUMN `procedure_summary` TEXT NULL AFTER `treatment_summary`,
    ADD COLUMN `operation_notes` TEXT NULL AFTER `procedure_summary`,
    ADD COLUMN `medicine_advice` TEXT NULL AFTER `advice`;
