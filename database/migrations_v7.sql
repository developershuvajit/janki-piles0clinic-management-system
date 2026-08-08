-- Phase 7 Migration: Reception & OPD Operations Module Extensions
USE `clinic_db`;

-- 1. Extend Patients Table with Lead Source, Reference & Additional Contact Info
ALTER TABLE `patients` 
  ADD COLUMN IF NOT EXISTS `lead_source` VARCHAR(50) DEFAULT 'Walk-In',
  ADD COLUMN IF NOT EXISTS `referred_by` VARCHAR(100) NULL,
  ADD COLUMN IF NOT EXISTS `aadhaar_number` VARCHAR(20) NULL,
  ADD COLUMN IF NOT EXISTS `whatsapp_number` VARCHAR(20) NULL;

-- 2. CRM Lead Management Table
CREATE TABLE IF NOT EXISTS `leads` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `branch_id` INT NULL,
  `name` VARCHAR(100) NOT NULL,
  `phone` VARCHAR(20) NOT NULL,
  `email` VARCHAR(100) NULL,
  `source` ENUM('Walk-In', 'Website', 'Google', 'Facebook', 'WhatsApp', 'Existing Patient', 'Doctor Referral') NOT NULL DEFAULT 'Walk-In',
  `status` ENUM('new', 'contacted', 'interested', 'appointment_booked', 'converted', 'lost') NOT NULL DEFAULT 'new',
  `follow_up_date` DATE NULL,
  `notes` TEXT NULL,
  `assigned_staff_id` INT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX `idx_lead_status` (`status`),
  INDEX `idx_lead_phone` (`phone`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3. Patient Follow-up Tracker Table
CREATE TABLE IF NOT EXISTS `patient_followups` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `patient_id` INT NOT NULL,
  `branch_id` INT NULL,
  `appointment_id` INT NULL,
  `next_visit_date` DATE NOT NULL,
  `status` ENUM('due', 'upcoming', 'missed', 'completed') NOT NULL DEFAULT 'due',
  `channel` ENUM('whatsapp', 'sms', 'call', 'in_person') NOT NULL DEFAULT 'whatsapp',
  `notes` TEXT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`patient_id`) REFERENCES `patients` (`id`) ON DELETE CASCADE,
  INDEX `idx_next_visit` (`next_visit_date`, `status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 4. Communication Center Logs Table
CREATE TABLE IF NOT EXISTS `communication_logs` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `patient_id` INT NULL,
  `type` ENUM('appointment_confirmation', 'appointment_reminder', 'followup_reminder', 'payment_receipt', 'prescription_ready', 'medicine_reminder', 'review_request', 'custom') NOT NULL,
  `channel` ENUM('whatsapp', 'sms') NOT NULL DEFAULT 'whatsapp',
  `template_name` VARCHAR(100) NOT NULL,
  `recipient_phone` VARCHAR(20) NOT NULL,
  `message_body` TEXT NOT NULL,
  `sent_status` ENUM('sent', 'failed', 'pending') NOT NULL DEFAULT 'sent',
  `sent_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 5. Staff Attendance Register Table
CREATE TABLE IF NOT EXISTS `staff_attendance` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT NOT NULL,
  `branch_id` INT NULL,
  `date` DATE NOT NULL,
  `check_in` TIME NULL,
  `check_out` TIME NULL,
  `status` ENUM('present', 'late', 'half_day', 'leave', 'absent') NOT NULL DEFAULT 'present',
  `notes` VARCHAR(255) NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY `uniq_user_date` (`user_id`, `date`),
  FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 6. Seed Initial Demo Leads
INSERT INTO `leads` (`branch_id`, `name`, `phone`, `email`, `source`, `status`, `follow_up_date`, `notes`) VALUES
(1, 'Suresh Chandra', '+91 98765 11111', 'suresh@example.com', 'Google', 'new', CURDATE(), 'Inquired about Ksharsutra piles treatment cost.'),
(1, 'Anita Rawat', '+91 98765 22222', 'anita@example.com', 'Facebook', 'interested', CURDATE() + INTERVAL 1 DAY, 'Wants consultation with Senior Surgeon on Saturday.'),
(1, 'Manish Kapoor', '+91 98765 33333', 'manish@example.com', 'WhatsApp', 'contacted', CURDATE(), 'Suffering from anal fissure pain, sent clinic location.')
ON DUPLICATE KEY UPDATE `phone` = `phone`;
