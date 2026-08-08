<?php

namespace App\Controllers;

use App\Helpers\Session;
use App\Helpers\Security;
use App\Models\Treatment;

class TreatmentController
{
    /**
     * Display all treatments.
     */
    public static function index(): void
    {
        if (!Session::isLoggedIn()) { redirect('/login'); exit; }

        $treatments = Treatment::all();
        
        // Fetch all doctors to assign
        $doctors = \App\Helpers\Database::all("
            SELECT u.id, u.username 
            FROM users u 
            JOIN roles r ON u.role_id = r.id 
            WHERE r.slug = 'doctor' AND u.status = 'active'
        ");

        include VIEWS_PATH . '/admin/cms/treatments.php';
    }

    /**
     * Create or edit a treatment catalog entry and map attending consultants.
     */
    public static function save(): void
    {
        if (!Session::isLoggedIn()) { redirect('/login'); exit; }
        if (!Security::verifyCsrfToken($_POST['csrf_token'] ?? null)) {
            Session::setFlash('error', 'Invalid security token. Please try again.');
            redirect('/admin/cms/treatments');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
            $title = trim($_POST['title'] ?? '');
            $slug = strtolower(preg_replace('/[^A-Za-z0-9-]+/', '-', $title));
            $content = trim($_POST['content'] ?? '');
            $price = (float)($_POST['price'] ?? 0.00);
            
            // Image Upload
            $imageUrl = $_POST['existing_image'] ?? '';
            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $destDir = __DIR__ . '/../../public/uploads/treatments';
                if (!is_dir($destDir)) {
                    mkdir($destDir, 0777, true);
                }
                $fileName = time() . '_' . basename($_FILES['image']['name']);
                if (move_uploaded_file($_FILES['image']['tmp_name'], $destDir . '/' . $fileName)) {
                    $imageUrl = '/uploads/treatments/' . $fileName;
                }
            }

            $treatmentData = [
                'title' => $title,
                'slug' => $slug,
                'content' => $content,
                'price' => $price,
                'image_url' => $imageUrl,
                'video_url' => trim($_POST['video_url'] ?? ''),
                'status' => trim($_POST['status'] ?? 'active'),
                'seo_title' => trim($_POST['seo_title'] ?? ''),
                'seo_description' => trim($_POST['seo_description'] ?? '')
            ];

            if ($id > 0) {
                Treatment::update($id, $treatmentData);
                $treatmentId = $id;
                Session::setFlash('success', 'Treatment catalog updated.');
            } else {
                $treatmentId = Treatment::create($treatmentData);
                Session::setFlash('success', 'New treatment catalog created.');
            }

            // Assign doctors
            $doctorIds = $_POST['doctor_ids'] ?? [];
            if ($treatmentId > 0) {
                Treatment::assignDoctors($treatmentId, $doctorIds);
            }
        }
        redirect('/admin/cms/treatments');
    }
}
