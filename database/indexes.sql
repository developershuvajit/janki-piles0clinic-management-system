-- =============================================================
-- MedClinic — Performance Database Indexes
-- Phase 35: Query Optimization
-- Run once on the clinic_db database
-- =============================================================

-- Patients
ALTER TABLE patients
    ADD INDEX IF NOT EXISTS idx_patients_branch    (branch_id),
    ADD INDEX IF NOT EXISTS idx_patients_phone     (phone),
    ADD INDEX IF NOT EXISTS idx_patients_created   (created_at);

-- OPD Consultations
ALTER TABLE opd_consultations
    ADD INDEX IF NOT EXISTS idx_opd_patient        (patient_id),
    ADD INDEX IF NOT EXISTS idx_opd_doctor         (doctor_id),
    ADD INDEX IF NOT EXISTS idx_opd_date           (created_at),
    ADD INDEX IF NOT EXISTS idx_opd_branch         (branch_id);

-- Appointments
ALTER TABLE appointments
    ADD INDEX IF NOT EXISTS idx_appt_doctor_date   (doctor_id, date),
    ADD INDEX IF NOT EXISTS idx_appt_patient       (patient_id),
    ADD INDEX IF NOT EXISTS idx_appt_status        (status),
    ADD INDEX IF NOT EXISTS idx_appt_branch        (branch_id);

-- IPD Admissions
ALTER TABLE ipd_admissions
    ADD INDEX IF NOT EXISTS idx_ipd_patient        (patient_id),
    ADD INDEX IF NOT EXISTS idx_ipd_status         (status),
    ADD INDEX IF NOT EXISTS idx_ipd_branch         (branch_id),
    ADD INDEX IF NOT EXISTS idx_ipd_admit_date     (admit_date);

-- Billing
ALTER TABLE billing
    ADD INDEX IF NOT EXISTS idx_billing_patient    (patient_id),
    ADD INDEX IF NOT EXISTS idx_billing_status     (payment_status),
    ADD INDEX IF NOT EXISTS idx_billing_branch     (branch_id),
    ADD INDEX IF NOT EXISTS idx_billing_updated    (updated_at);

-- Medicine Stock / Transactions
ALTER TABLE medicine_transactions
    ADD INDEX IF NOT EXISTS idx_med_tx_medicine    (medicine_id),
    ADD INDEX IF NOT EXISTS idx_med_tx_type        (type),
    ADD INDEX IF NOT EXISTS idx_med_tx_date        (created_at);

ALTER TABLE medicines
    ADD INDEX IF NOT EXISTS idx_med_branch         (branch_id),
    ADD INDEX IF NOT EXISTS idx_med_status         (status),
    ADD INDEX IF NOT EXISTS idx_med_stock          (current_stock);

-- Activity Logs (for dashboard queries)
ALTER TABLE activity_logs
    ADD INDEX IF NOT EXISTS idx_logs_user          (user_id),
    ADD INDEX IF NOT EXISTS idx_logs_created       (created_at);

-- Login History
ALTER TABLE login_history
    ADD INDEX IF NOT EXISTS idx_login_user         (user_id),
    ADD INDEX IF NOT EXISTS idx_login_date         (logged_in_at);

-- Users
ALTER TABLE users
    ADD INDEX IF NOT EXISTS idx_users_role         (role_id),
    ADD INDEX IF NOT EXISTS idx_users_branch       (branch_id),
    ADD INDEX IF NOT EXISTS idx_users_status       (status);

-- Employee Attendance
ALTER TABLE employee_attendance
    ADD INDEX IF NOT EXISTS idx_att_employee       (employee_id),
    ADD INDEX IF NOT EXISTS idx_att_date           (date),
    ADD INDEX IF NOT EXISTS idx_att_branch         (branch_id);

-- Salary
ALTER TABLE employee_salaries
    ADD INDEX IF NOT EXISTS idx_sal_employee       (employee_id),
    ADD INDEX IF NOT EXISTS idx_sal_month          (month_year);
