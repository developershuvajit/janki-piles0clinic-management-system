<?php
declare(strict_types=1);

require_once dirname(__DIR__) . '/config/config.php';

use App\Helpers\Database;
use App\Models\Cms;

echo "=== Seeding Janki Piles Clinic Website Content & Settings ===\n";

try {
    // 1. Website Settings
    $settings = [
        'site_name' => 'Janki Piles Clinic',
        'tagline' => 'Advanced Laser Piles & Proctology Center',
        'meta_title' => 'Best Piles Doctor & Laser Surgery Clinic | Janki Piles Clinic',
        'meta_description' => '100% Painless Laser Piles, Fissure, Fistula & Hernia Surgery at Janki Piles Clinic. 15+ years experience, same-day discharge & 100% cashless insurance.',
        'contact_phone' => '+91 98765 43210',
        'contact_email' => 'info@jankipilesclinic.com',
        'contact_address' => 'Rajpur Road, Near EC Road Junction, Dehradun, Uttarakhand - 248001',
        'emergency_phone' => '+91 98765 43210',
        'whatsapp_number' => '+919876543210',
        'working_hours' => 'Mon - Sat: 09:00 AM - 08:00 PM | Sun: 10:00 AM - 02:00 PM',
        'menus_json' => json_encode([
            ['title' => 'Home', 'url' => '/'],
            ['title' => 'About Us', 'url' => '/about'],
            ['title' => 'Our Doctors', 'url' => '/doctors'],
            ['title' => 'Treatments', 'url' => '/treatments'],
            ['title' => 'FAQs', 'url' => '/faqs'],
            ['title' => 'Blogs', 'url' => '/blog'],
            ['title' => 'Contact', 'url' => '/contact'],
        ]),
        'faqs_json' => json_encode([
            [
                'q' => 'Is laser piles surgery completely painless?',
                'a' => 'Yes! Laser piles treatment (LHP) shrinks hemorrhoidal nodes from within using 1470nm German laser energy without scalpel cuts or open stitches, reducing pain by 90-95% compared to open surgery.'
            ],
            [
                'q' => 'How many days of bed rest are required after laser surgery?',
                'a' => 'No prolonged bed rest is needed. Patients can walk independently 2-3 hours after the procedure and resume normal office duties within 24 to 48 hours.'
            ],
            [
                'q' => 'Does health insurance cover laser piles and fistula surgery?',
                'a' => 'Yes! All laser proctology procedures are medically necessary surgical treatments covered by major health insurance policies and TPAs. We offer 100% cashless approvals.'
            ],
            [
                'q' => 'What is the recurrence rate after laser surgery?',
                'a' => 'The recurrence rate after German laser surgery is under 1% when following recommended post-operative dietary fiber and bowel habit guidelines.'
            ],
            [
                'q' => 'Are female chaperones available for female patients?',
                'a' => 'Yes, Janki Piles Clinic prioritizes patient privacy and provides dedicated female medical staff and chaperones for all female patient examinations.'
            ]
        ])
    ];

    Cms::saveSettings($settings);
    echo "1. Website Settings Updated Successfully.\n";

    // 2. Treatments Seeding
    $treatments = [
        [
            'title' => 'Laser Piles Surgery (LHP)',
            'slug' => 'piles-treatment',
            'price' => 25000.00,
            'content' => 'German 1470nm Laser Hemorrhoidoplasty (LHP) for Grade 1 to 4 internal and external piles. 100% painless, zero scalpel cuts, zero stitches, same-day discharge in 4 hours.',
            'seo_title' => 'Best Laser Piles Surgery & Doctor | Janki Piles Clinic',
            'seo_description' => 'Painless laser piles treatment for hemorrhoids with zero cuts, zero stitches, and same-day discharge.',
            'status' => 'active'
        ],
        [
            'title' => 'Laser Anal Fissure Surgery',
            'slug' => 'fissure-treatment',
            'price' => 22000.00,
            'content' => 'Painless Laser Sphincterotomy for chronic anal fissure cuts. Relieves severe anal spasm and pain instantly, promoting rapid 24-hour ulcer healing.',
            'seo_title' => 'Painless Anal Fissure Laser Surgery | Janki Piles Clinic',
            'seo_description' => 'Fast 24-hour relief from sharp anal fissure pain with advanced laser sphincterotomy.',
            'status' => 'active'
        ],
        [
            'title' => 'FiLaC Laser Fistula Surgery',
            'slug' => 'fistula-treatment',
            'price' => 30000.00,
            'content' => 'Fistula-tract Laser Closure (FiLaC) destroys infected fistula lining while preserving 100% anal sphincter muscle control, preventing fecal incontinence.',
            'seo_title' => 'FiLaC Laser Fistula Surgery | Janki Piles Clinic',
            'seo_description' => 'Sphincter-preserving FiLaC laser surgery for complex anal fistula with zero incontinence risk.',
            'status' => 'active'
        ],
        [
            'title' => 'SiLaC Pilonidal Sinus Laser',
            'slug' => 'pilonidal-sinus-treatment',
            'price' => 28000.00,
            'content' => 'Sinus Laser Closure (SiLaC) cleans and seals tailbone sinus tracts without open excision wounds or painful daily dressing changes.',
            'seo_title' => 'Pilonidal Sinus Laser Surgery (SiLaC) | Janki Piles Clinic',
            'seo_description' => 'Stitchless tailbone pilonidal sinus laser treatment with fast healing and zero open wounds.',
            'status' => 'active'
        ],
        [
            'title' => 'ZSR Stapler Circumcision',
            'slug' => 'circumcision',
            'price' => 20000.00,
            'content' => 'Stitchless 15-minute ZSR Stapler & Laser Circumcision for Phimosis and Balanitis. Clean aesthetic finish, zero pain, fast 48-hour recovery.',
            'seo_title' => 'ZSR Stapler Circumcision | Janki Piles Clinic',
            'seo_description' => 'Stitchless 15-minute ZSR stapler circumcision for phimosis with aesthetic finish and rapid healing.',
            'status' => 'active'
        ],
        [
            'title' => 'Minimally Invasive Hydrocele Surgery',
            'slug' => 'hydrocele-treatment',
            'price' => 22000.00,
            'content' => 'Minimally invasive Hydrocelectomy for scrotal fluid swelling. Small cosmetic incision, zero recurrence, same-day discharge.',
            'seo_title' => 'Minimally Invasive Hydrocele Surgery | Janki Piles Clinic',
            'seo_description' => 'Safe daycare hydrocele surgery for scrotal swelling relief.',
            'status' => 'active'
        ],
        [
            'title' => 'Laparoscopic Hernia Repair',
            'slug' => 'hernia-surgery',
            'price' => 35000.00,
            'content' => 'Keyhole Laparoscopic Hernia Repair with 3D anatomical mesh placement for Inguinal, Umbilical, and Ventral Hernias. Minimal scars, fast 48-hour recovery.',
            'seo_title' => 'Laparoscopic Hernia Repair Surgery | Janki Piles Clinic',
            'seo_description' => 'Keyhole laparoscopic mesh hernia surgery for inguinal and umbilical hernias.',
            'status' => 'active'
        ],
        [
            'title' => 'Constipation & Pelvic Floor Care',
            'slug' => 'constipation-treatment',
            'price' => 1500.00,
            'content' => 'Comprehensive 3D Anoscopy diagnostic evaluation, dietary fiber planning, and medical bowel retraining for chronic constipation.',
            'seo_title' => 'Chronic Constipation & Pelvic Floor Evaluation | Janki Piles Clinic',
            'seo_description' => 'Expert evaluation and medical cure for chronic constipation and bowel irregularity.',
            'status' => 'active'
        ]
    ];

    foreach ($treatments as $tr) {
        $existing = Database::row("SELECT id FROM treatments WHERE slug = :slug", ['slug' => $tr['slug']]);
        if ($existing) {
            Database::execute("UPDATE treatments SET title = :title, price = :price, content = :content, seo_title = :seo_title, seo_description = :seo_desc, status = 'active' WHERE id = :id", [
                'title' => $tr['title'],
                'price' => $tr['price'],
                'content' => $tr['content'],
                'seo_title' => $tr['seo_title'],
                'seo_desc' => $tr['seo_description'],
                'id' => $existing['id']
            ]);
        } else {
            Database::execute("INSERT INTO treatments (title, slug, price, content, seo_title, seo_description, status) VALUES (:title, :slug, :price, :content, :seo_title, :seo_desc, 'active')", [
                'title' => $tr['title'],
                'slug' => $tr['slug'],
                'price' => $tr['price'],
                'content' => $tr['content'],
                'seo_title' => $tr['seo_title'],
                'seo_desc' => $tr['seo_description']
            ]);
        }
    }
    echo "2. Treatments Seeded Successfully.\n";

    // 3. Testimonials Seeding
    $testimonials = [
        [
            'author' => 'Ramesh Verma (Dehradun)',
            'rating' => 5,
            'review_text' => 'Life-changing experience after 4 years of piles suffering! Dr. at Janki Piles Clinic performed laser surgery. Discharged same evening and back to office in 2 days with zero pain.',
            'status' => 'active'
        ],
        [
            'author' => 'Priya Sharma (Haridwar)',
            'rating' => 5,
            'review_text' => 'Suffered from severe bleeding fissure pain for months. Laser fissure surgery took just 15 minutes. Pain vanished almost completely by next morning. Highly recommend!',
            'status' => 'active'
        ],
        [
            'author' => 'Vikram Singh (Haldwani)',
            'rating' => 5,
            'review_text' => 'Had a complex recurrent fistula operated twice elsewhere with open surgery. FiLaC laser closure at Janki Piles Clinic cured it completely in a single session with zero sphincter damage.',
            'status' => 'active'
        ],
        [
            'author' => 'Amit Patel (Mohali)',
            'rating' => 5,
            'review_text' => 'Got ZSR stapler circumcision done. 100% painless, stitchless, and cosmetic finish. Processed cashless insurance within 2 hours.',
            'status' => 'active'
        ]
    ];

    foreach ($testimonials as $t) {
        $existing = Database::row("SELECT id FROM testimonials WHERE author = :author", ['author' => $t['author']]);
        if (!$existing) {
            Database::execute("INSERT INTO testimonials (type, author, rating, review_text, status) VALUES ('patient', :author, :rating, :text, 'active')", [
                'author' => $t['author'],
                'rating' => $t['rating'],
                'text' => $t['review_text']
            ]);
        }
    }
    echo "3. Testimonials Seeded Successfully.\n";

    // 4. Branches Seeding
    $branches = [
        ['name' => 'Dehradun Main Clinic', 'phone' => '+91 98765 43210', 'emergency' => '+91 98765 43210', 'email' => 'dehradun@jankipilesclinic.com', 'address' => 'Rajpur Road, Near EC Road Junction, Dehradun'],
        ['name' => 'Haridwar Clinic', 'phone' => '+91 98765 43210', 'emergency' => '+91 98765 43210', 'email' => 'haridwar@jankipilesclinic.com', 'address' => 'Near Ranipur More Flyover, Main Highway, Haridwar'],
        ['name' => 'Roorkee Clinic', 'phone' => '+91 98765 43210', 'emergency' => '+91 98765 43210', 'email' => 'roorkee@jankipilesclinic.com', 'address' => 'Civil Lines, Near IIT Roorkee, Roorkee'],
        ['name' => 'Bhaniyawala Clinic', 'phone' => '+91 98765 43210', 'emergency' => '+91 98765 43210', 'email' => 'bhaniyawala@jankipilesclinic.com', 'address' => 'Jolly Grant Airport Road, Bhaniyawala'],
        ['name' => 'Srinagar Garhwal Clinic', 'phone' => '+91 98765 43210', 'emergency' => '+91 98765 43210', 'email' => 'srinagar@jankipilesclinic.com', 'address' => 'Main Market, Medical College Road, Srinagar Garhwal'],
        ['name' => 'Haldwani Clinic', 'phone' => '+91 98765 43210', 'emergency' => '+91 98765 43210', 'email' => 'haldwani@jankipilesclinic.com', 'address' => 'Bareilly-Nainital Road, Haldwani'],
        ['name' => 'Mohali Branch', 'phone' => '+91 98765 43210', 'emergency' => '+91 98765 43210', 'email' => 'mohali@jankipilesclinic.com', 'address' => 'Sector 62, Phase 7, Mohali (Tricity)']
    ];

    foreach ($branches as $b) {
        $existing = Database::row("SELECT id FROM branches WHERE name = :name", ['name' => $b['name']]);
        if (!$existing) {
            Database::execute("INSERT INTO branches (name, address, phone, emergency_number, email, opening_hours, status) VALUES (:name, :address, :phone, :emergency, :email, 'Mon-Sat 9AM-8PM', 'active')", [
                'name' => $b['name'],
                'address' => $b['address'],
                'phone' => $b['phone'],
                'emergency' => $b['emergency'],
                'email' => $b['email']
            ]);
        }
    }
    echo "4. Branches Seeded Successfully.\n";

    echo "=== SEEDING COMPLETED SUCCESSFULLY ===\n";

} catch (\Throwable $e) {
    echo "Error Seeding: " . $e->getMessage() . "\n";
}
