-- 1. Website Settings Table
CREATE TABLE IF NOT EXISTS website_settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    config_key VARCHAR(100) UNIQUE NOT NULL,
    config_value TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- 2. Blog Categories Table
CREATE TABLE IF NOT EXISTS blog_categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    slug VARCHAR(100) UNIQUE NOT NULL
);

-- 3. Blogs Table
CREATE TABLE IF NOT EXISTS blogs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(200) NOT NULL,
    slug VARCHAR(200) UNIQUE NOT NULL,
    content LONGTEXT NOT NULL,
    category_id INT NULL,
    tags VARCHAR(255) NULL,
    image_url VARCHAR(255) NULL,
    status VARCHAR(20) NOT NULL DEFAULT 'draft', -- 'draft', 'published'
    seo_title VARCHAR(150) NULL,
    seo_description TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES blog_categories(id) ON DELETE SET NULL
);

-- 4. Blog Comments Table
CREATE TABLE IF NOT EXISTS blog_comments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    blog_id INT NOT NULL,
    author_name VARCHAR(100) NOT NULL,
    author_email VARCHAR(100) NOT NULL,
    comment_text TEXT NOT NULL,
    status VARCHAR(20) NOT NULL DEFAULT 'pending', -- 'pending', 'approved', 'rejected'
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (blog_id) REFERENCES blogs(id) ON DELETE CASCADE
);

-- 5. Treatments Table
CREATE TABLE IF NOT EXISTS treatments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(150) NOT NULL,
    slug VARCHAR(150) UNIQUE NOT NULL,
    content TEXT NOT NULL,
    price DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    image_url VARCHAR(255) NULL,
    video_url VARCHAR(255) NULL,
    status VARCHAR(20) NOT NULL DEFAULT 'active',
    seo_title VARCHAR(150) NULL,
    seo_description TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- 6. Treatment Attending Doctors Mapping
CREATE TABLE IF NOT EXISTS treatment_doctors (
    treatment_id INT NOT NULL,
    doctor_id INT NOT NULL,
    PRIMARY KEY (treatment_id, doctor_id),
    FOREIGN KEY (treatment_id) REFERENCES treatments(id) ON DELETE CASCADE,
    FOREIGN KEY (doctor_id) REFERENCES users(id) ON DELETE CASCADE
);

-- 7. Gallery Albums
CREATE TABLE IF NOT EXISTS gallery_albums (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    slug VARCHAR(100) UNIQUE NOT NULL,
    description TEXT NULL
);

-- 8. Gallery Media Table
CREATE TABLE IF NOT EXISTS gallery_media (
    id INT AUTO_INCREMENT PRIMARY KEY,
    album_id INT NOT NULL,
    type VARCHAR(20) NOT NULL DEFAULT 'photo', -- 'photo', 'video'
    url VARCHAR(255) NOT NULL,
    caption VARCHAR(255) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (album_id) REFERENCES gallery_albums(id) ON DELETE CASCADE
);

-- 9. Testimonials Table
CREATE TABLE IF NOT EXISTS testimonials (
    id INT AUTO_INCREMENT PRIMARY KEY,
    type VARCHAR(20) NOT NULL DEFAULT 'patient', -- 'patient', 'google', 'video'
    author VARCHAR(100) NOT NULL,
    rating INT NOT NULL DEFAULT 5,
    review_text TEXT NOT NULL,
    video_url VARCHAR(255) NULL,
    status VARCHAR(20) NOT NULL DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 10. Contact Enquiries Lead CRM Table
CREATE TABLE IF NOT EXISTS contact_enquiries (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    subject VARCHAR(150) NOT NULL,
    message TEXT NOT NULL,
    status VARCHAR(20) NOT NULL DEFAULT 'new', -- 'new', 'contacted', 'resolved'
    notes TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- 11. Seed Baseline Website Settings
INSERT INTO website_settings (config_key, config_value) VALUES 
('site_name', 'MedClinic Healthcare'),
('meta_title', 'MedClinic - Multi-Specialty Clinic & Online Bookings'),
('meta_description', 'Book expert outpatient consultations and inpatient ward bed stays online at MedClinic Multi-Specialty Healthcare center.'),
('contact_email', 'support@medclinic.com'),
('contact_phone', '+91 98765 43210'),
('contact_address', '101 Healthcare Avenue, Sector 5, Mumbai, Maharashtra'),
('map_link', 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3770.7997931321074!2d72.89965151538356!3d19.073612887088185!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3be7c627a20bcaa9%3A0x1b2d354a8e2cb924!2sSomaiya%20Vidyavihar%20University!5e0!3m2!1sen!2sin!4v1627364843210!5m2!1sen!2sin'),
('faqs_json', '[{"q":"How do I book an online appointment?","a":"Select a doctor, pick an active date, verify your email via OTP code, and reserve a shift time slot."},{"q":"What are the IPD ward stay charges?","a":"Room and bed rents vary by room type: General (₹800/day), Semi-Private (₹1,500/day), Private (₹3,000/day), and ICU (₹6,000/day)."},{"q":"Can I pay invoices online?","a":"Yes, billing collections support Cash, Credit/Debit Cards, and direct UPI QR scan payments."}]'),
('menus_json', '[{"title":"Home","url":"/"},{"title":"About Us","url":"/about"},{"title":"Doctors","url":"/doctors"},{"title":"Treatments","url":"/treatments"},{"title":"Media Gallery","url":"/gallery"},{"title":"Health Blogs","url":"/blog"},{"title":"Contact Us","url":"/contact"}]')
ON DUPLICATE KEY UPDATE config_value=VALUES(config_value);

-- 12. Seed Blog Categories
INSERT INTO blog_categories (name, slug) VALUES 
('Cardiology', 'cardiology'),
('Pediatrics', 'pediatrics'),
('General Wellness', 'general-wellness'),
('Inpatient Care', 'inpatient-care')
ON DUPLICATE KEY UPDATE name=name;

-- 13. Seed Default Testimonials
INSERT INTO testimonials (type, author, rating, review_text, status) VALUES 
('patient', 'Amit Sharma', 5, 'MedClinic doctors are extremely experienced. The online appointment booking and queuing process is very fast!', 'active'),
('google', 'Pooja Patel', 5, 'Highly recommend the IPD nursing staff. The stay rent invoicing process is clear and automated without hidden charges.', 'active')
ON DUPLICATE KEY UPDATE author=author;
