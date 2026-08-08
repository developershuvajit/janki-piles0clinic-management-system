-- 1. Suppliers Table
CREATE TABLE IF NOT EXISTS suppliers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    email VARCHAR(100) NULL,
    address TEXT NULL,
    status VARCHAR(20) NOT NULL DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- 2. Medicines Table
CREATE TABLE IF NOT EXISTS medicines (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    generic_name VARCHAR(150) NULL,
    sku VARCHAR(50) UNIQUE NOT NULL,
    category VARCHAR(50) NOT NULL,
    unit VARCHAR(20) NOT NULL DEFAULT 'pcs',
    min_stock_level INT NOT NULL DEFAULT 10,
    status VARCHAR(20) NOT NULL DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- 3. Medicine Stocks (Batches & Expiries)
CREATE TABLE IF NOT EXISTS medicine_stocks (
    id INT AUTO_INCREMENT PRIMARY KEY,
    medicine_id INT NOT NULL,
    batch_number VARCHAR(50) NOT NULL,
    expiry_date DATE NOT NULL,
    quantity INT NOT NULL DEFAULT 0,
    supplier_id INT NULL,
    purchase_price DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    selling_price DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (medicine_id) REFERENCES medicines(id) ON DELETE CASCADE,
    FOREIGN KEY (supplier_id) REFERENCES suppliers(id) ON DELETE SET NULL
);

-- 4. Medicine Stock Transactions Audit Log
CREATE TABLE IF NOT EXISTS medicine_transactions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    medicine_id INT NOT NULL,
    type VARCHAR(20) NOT NULL, -- 'stock_in', 'stock_out'
    quantity INT NOT NULL,
    reason TEXT NULL,
    created_by INT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (medicine_id) REFERENCES medicines(id) ON DELETE CASCADE,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
);

-- 5. IPD Discharge Summary
CREATE TABLE IF NOT EXISTS ipd_discharge_summaries (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ipd_admission_id INT NOT NULL UNIQUE,
    diagnosis TEXT NOT NULL,
    treatment_summary TEXT NOT NULL,
    advice TEXT NULL,
    diet TEXT NULL,
    follow_up_instructions TEXT NULL,
    doctor_signature VARCHAR(255) NULL,
    hospital_seal VARCHAR(255) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (ipd_admission_id) REFERENCES ipd_admissions(id) ON DELETE CASCADE
);

-- 6. Employee Attendance Logs
CREATE TABLE IF NOT EXISTS employee_attendance (
    id INT AUTO_INCREMENT PRIMARY KEY,
    employee_id INT NOT NULL,
    date DATE NOT NULL,
    status VARCHAR(20) NOT NULL, -- 'present', 'absent', 'leave', 'late', 'holiday'
    check_in TIME NULL,
    check_out TIME NULL,
    notes TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (employee_id) REFERENCES employees(id) ON DELETE CASCADE,
    UNIQUE KEY emp_date_unique (employee_id, date)
);

-- 7. Employee Leaves Table
CREATE TABLE IF NOT EXISTS employee_leaves (
    id INT AUTO_INCREMENT PRIMARY KEY,
    employee_id INT NOT NULL,
    leave_type VARCHAR(50) NOT NULL, -- 'sick', 'casual', 'annual', 'unpaid'
    start_date DATE NOT NULL,
    end_date DATE NOT NULL,
    reason TEXT NULL,
    status VARCHAR(20) NOT NULL DEFAULT 'pending', -- 'pending', 'approved', 'rejected'
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (employee_id) REFERENCES employees(id) ON DELETE CASCADE
);

-- 8. Employee Salary Payrolls
CREATE TABLE IF NOT EXISTS employee_salaries (
    id INT AUTO_INCREMENT PRIMARY KEY,
    employee_id INT NOT NULL,
    month_year VARCHAR(10) NOT NULL, -- 'MM-YYYY'
    base_salary DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    advance DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    bonus DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    deduction DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    net_salary DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    payment_status VARCHAR(20) NOT NULL DEFAULT 'unpaid', -- 'unpaid', 'paid'
    payment_date DATE NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (employee_id) REFERENCES employees(id) ON DELETE CASCADE,
    UNIQUE KEY emp_month_unique (employee_id, month_year)
);

-- 9. Add GST and Refund Fields to Billing Table
ALTER TABLE billing ADD COLUMN IF NOT EXISTS gst DECIMAL(10,2) NOT NULL DEFAULT 0.00 AFTER tax;
ALTER TABLE billing ADD COLUMN IF NOT EXISTS outstanding DECIMAL(10,2) NOT NULL DEFAULT 0.00 AFTER total;
ALTER TABLE billing ADD COLUMN IF NOT EXISTS refunded_amount DECIMAL(10,2) NOT NULL DEFAULT 0.00 AFTER paid_amount;
ALTER TABLE billing ADD COLUMN IF NOT EXISTS refund_reason TEXT NULL AFTER refunded_amount;

-- 10. Seed Default Suppliers
INSERT INTO suppliers (name, phone, email, address, status) VALUES 
('Acme Pharma Distributors', '022-25556677', 'orders@acmepharma.com', 'Mumbai, MH', 'active'),
('MediLife Wholesale', '011-28889900', 'contact@medilife.in', 'New Delhi, DL', 'active'),
('Surya Surgicals & Drugs', '080-24445555', 'sales@suryasurgicals.com', 'Bengaluru, KA', 'active')
ON DUPLICATE KEY UPDATE name=name;

-- 11. Seed 50 Master Medicines List
INSERT INTO medicines (name, generic_name, sku, category, unit, min_stock_level) VALUES
('Paracetamol 650mg', 'Paracetamol', 'MED-PCM-650', 'Analgesics', 'tablets', 20),
('Ibuprofen 400mg', 'Ibuprofen', 'MED-IBU-400', 'Analgesics', 'tablets', 15),
('Amoxicillin 500mg', 'Amoxicillin', 'MED-AMX-500', 'Antibiotics', 'capsules', 10),
('Azithromycin 500mg', 'Azithromycin', 'MED-AZI-500', 'Antibiotics', 'tablets', 10),
('Ciprofloxacin 500mg', 'Ciprofloxacin', 'MED-CIP-500', 'Antibiotics', 'tablets', 15),
('Metformin 500mg', 'Metformin', 'MED-MET-500', 'Antidiabetics', 'tablets', 20),
('Atorvastatin 10mg', 'Atorvastatin', 'MED-ATO-10', 'Antihyperlipidemics', 'tablets', 20),
('Amlodipine 5mg', 'Amlodipine', 'MED-AML-5', 'Antihypertensives', 'tablets', 25),
('Losartan 50mg', 'Losartan', 'MED-LOS-50', 'Antihypertensives', 'tablets', 15),
('Omeprazole 20mg', 'Omeprazole', 'MED-OME-20', 'Antacids', 'capsules', 30),
('Pantoprazole 40mg', 'Pantoprazole', 'MED-PAN-40', 'Antacids', 'tablets', 25),
('Cetirizine 10mg', 'Cetirizine', 'MED-CET-10', 'Antihistamines', 'tablets', 30),
('Levocetirizine 5mg', 'Levocetirizine', 'MED-LEV-5', 'Antihistamines', 'tablets', 20),
('Montelukast 10mg', 'Montelukast', 'MED-MON-10', 'Antiasthmatics', 'tablets', 15),
('Ranitidine 150mg', 'Ranitidine', 'MED-RAN-150', 'Antacids', 'tablets', 20),
('Domperidone 10mg', 'Domperidone', 'MED-DOM-10', 'Antiemetics', 'tablets', 25),
('Ondansetron 4mg', 'Ondansetron', 'MED-OND-4', 'Antiemetics', 'tablets', 15),
('Pantoprazole D', 'Pantoprazole + Domperidone', 'MED-PAND-CAP', 'Antacids', 'capsules', 20),
('Rabeprazole 20mg', 'Rabeprazole', 'MED-RAB-20', 'Antacids', 'tablets', 15),
('Metronidazole 400mg', 'Metronidazole', 'MED-MTZ-400', 'Antiprotozoals', 'tablets', 20),
('Albendazole 400mg', 'Albendazole', 'MED-ALB-400', 'Anthelmintics', 'tablets', 10),
('Diclofenac 50mg', 'Diclofenac', 'MED-DIC-50', 'Analgesics', 'tablets', 20),
('Aceclofenac 100mg', 'Aceclofenac', 'MED-ACE-100', 'Analgesics', 'tablets', 20),
('Tramadol 50mg', 'Tramadol', 'MED-TRA-50', 'Analgesics', 'capsules', 10),
('Gabapentin 300mg', 'Gabapentin', 'MED-GAB-300', 'Anticonvulsants', 'capsules', 10),
('Pregabalin 75mg', 'Pregabalin', 'MED-PRE-75', 'Anticonvulsants', 'capsules', 15),
('Alprazolam 0.25mg', 'Alprazolam', 'MED-ALP-025', 'Anxiolytics', 'tablets', 10),
('Clonazepam 0.5mg', 'Clonazepam', 'MED-CLO-05', 'Anxiolytics', 'tablets', 15),
('Diazepam 5mg', 'Diazepam', 'MED-DIA-5', 'Anxiolytics', 'tablets', 10),
('Sertraline 50mg', 'Sertraline', 'MED-SER-50', 'Antidepressants', 'tablets', 10),
('Fluoxetine 20mg', 'Fluoxetine', 'MED-FLU-20', 'Antidepressants', 'capsules', 10),
('Amitriptyline 10mg', 'Amitriptyline', 'MED-AMI-10', 'Antidepressants', 'tablets', 15),
('Salbutamol Inhaler', 'Salbutamol', 'MED-SAL-INH', 'Bronchodilators', 'inhalers', 5),
('Fluticasone Nasal Spray', 'Fluticasone', 'MED-FLU-NS', 'Corticosteroids', 'sprays', 5),
('Hydrocortisone 1% Cream', 'Hydrocortisone', 'MED-HYD-CRM', 'Topical Corticosteroids', 'tubes', 10),
('Betamethasone Cream', 'Betamethasone', 'MED-BET-CRM', 'Topical Corticosteroids', 'tubes', 10),
('Clotrimazole 1% Cream', 'Clotrimazole', 'MED-CLO-CRM', 'Antifungals', 'tubes', 10),
('Fluconazole 150mg', 'Fluconazole', 'MED-FLC-150', 'Antifungals', 'tablets', 15),
('Terbinafine 250mg', 'Terbinafine', 'MED-TER-250', 'Antifungals', 'tablets', 10),
('Acyclovir 400mg', 'Acyclovir', 'MED-ACY-400', 'Antivirals', 'tablets', 15),
('Oseltamivir 75mg', 'Oseltamivir', 'MED-OSE-75', 'Antivirals', 'capsules', 5),
('Multivitamin Tablets', 'Vitamins & Minerals', 'MED-MVI-TAB', 'Supplements', 'tablets', 40),
('Calcium & Vitamin D3', 'Calcium + Vitamin D3', 'MED-CAD-TAB', 'Supplements', 'tablets', 30),
('B-Complex Capsules', 'B-Complex', 'MED-BCP-CAP', 'Supplements', 'capsules', 30),
('Vitamin C 500mg', 'Ascorbic Acid', 'MED-VIC-500', 'Supplements', 'tablets', 40),
('Iron Supplement', 'Ferrous Ascorbate + Folic Acid', 'MED-IRO-TAB', 'Supplements', 'tablets', 20),
('ORS Sachet', 'Oral Rehydration Salts', 'MED-ORS-SAC', 'Supplements', 'packets', 50),
('Dextrose 5% 500ml', 'Dextrose IV Fluid', 'MED-DEX-IV', 'IV Fluids', 'bottles', 15),
('Normal Saline 500ml', 'Normal Saline IV Fluid', 'MED-NS-IV', 'IV Fluids', 'bottles', 15),
('Ringer Lactate 500ml', 'Ringer Lactate IV Fluid', 'MED-RL-IV', 'IV Fluids', 'bottles', 15)
ON DUPLICATE KEY UPDATE name=name;
