<?php

namespace App\Controllers;

use App\Helpers\Session;
use App\Helpers\Security;
use App\Helpers\Logger;
use App\Models\Cms;

class CmsController
{
    /**
     * View and modify website global settings.
     */
    public static function index(): void
    {
        if (!Session::isLoggedIn()) { redirect('/login'); exit; }
        
        $settings = Cms::getSettings();
        include VIEWS_PATH . '/admin/cms/settings.php';
    }

    /**
     * Save updated settings keys.
     */
    public static function saveSettings(): void
    {
        if (!Session::isLoggedIn()) { redirect('/login'); exit; }
        if (!Security::verifyCsrfToken($_POST['csrf_token'] ?? null)) {
            Session::setFlash('error', 'Invalid security token. Please try again.');
            redirect('/admin/cms/settings');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = $_POST['settings'] ?? [];
            
            // Format FAQs from inputs if present
            if (isset($_POST['faqs']) && is_array($_POST['faqs'])) {
                $faqs = [];
                foreach ($_POST['faqs'] as $index => $faq) {
                    if (!empty($faq['q']) && !empty($faq['a'])) {
                        $faqs[] = [
                            'q' => trim($faq['q']),
                            'a' => trim($faq['a'])
                        ];
                    }
                }
                $data['faqs_json'] = json_encode($faqs);
            }

            if (Cms::saveSettings($data)) {
                Session::setFlash('success', 'Website settings updated successfully.');
            } else {
                Session::setFlash('error', 'Failed to update website settings.');
            }
        }
        redirect('/admin/cms/settings');
    }

    /**
     * Display media albums & gallery items.
     */
    public static function gallery(): void
    {
        if (!Session::isLoggedIn()) { redirect('/login'); exit; }

        $albums = Cms::getAlbums();
        $selectedAlbumId = isset($_GET['album_id']) ? (int)$_GET['album_id'] : (count($albums) ? (int)$albums[0]['id'] : 0);
        
        $media = [];
        if ($selectedAlbumId > 0) {
            $media = Cms::getAlbumMedia($selectedAlbumId);
        }

        include VIEWS_PATH . '/admin/cms/gallery.php';
    }

    /**
     * Create gallery album.
     */
    public static function saveAlbum(): void
    {
        if (!Session::isLoggedIn()) { redirect('/login'); exit; }
        if (!Security::verifyCsrfToken($_POST['csrf_token'] ?? null)) {
            Session::setFlash('error', 'Invalid security token. Please try again.');
            redirect('/admin/cms/gallery');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = trim($_POST['name'] ?? '');
            $slug = strtolower(preg_replace('/[^A-Za-z0-9-]+/', '-', $name));

            if (!empty($name)) {
                try {
                    Cms::createAlbum([
                        'name' => $name,
                        'slug' => $slug,
                        'description' => trim($_POST['description'] ?? '')
                    ]);
                    Session::setFlash('success', 'Gallery album created.');
                } catch (\Throwable $e) {
                    Logger::error("Failed creating gallery album: " . $e->getMessage(), ['name' => $name]);
                    Session::setFlash('error', 'Failed to create the gallery album.');
                }
            }
        }
        redirect('/admin/cms/gallery');
    }

    /**
     * Add media item.
     */
    public static function saveMedia(): void
    {
        if (!Session::isLoggedIn()) { redirect('/login'); exit; }
        if (!Security::verifyCsrfToken($_POST['csrf_token'] ?? null)) {
            Session::setFlash('error', 'Invalid security token. Please try again.');
            redirect('/admin/cms/gallery');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $albumId = (int)($_POST['album_id'] ?? 0);
            $type = trim($_POST['type'] ?? 'photo');
            $caption = trim($_POST['caption'] ?? '');
            $url = '';

            if ($type === 'photo' && isset($_FILES['file']) && $_FILES['file']['error'] === UPLOAD_ERR_OK) {
                // Save photo upload
                $destDir = __DIR__ . '/../../public/uploads/gallery';
                if (!is_dir($destDir) && !mkdir($destDir, 0777, true) && !is_dir($destDir)) {
                    Logger::error("Unable to create gallery upload directory: {$destDir}");
                }
                $fileName = time() . '_' . basename($_FILES['file']['name']);
                if (move_uploaded_file($_FILES['file']['tmp_name'], $destDir . '/' . $fileName)) {
                    $url = '/uploads/gallery/' . $fileName;
                } else {
                    Logger::error("Failed moving uploaded gallery file to {$destDir}/{$fileName}");
                }
            } else {
                // Save video link
                $url = trim($_POST['video_url'] ?? '');
            }

            if (!empty($url) && $albumId > 0) {
                try {
                    Cms::addMedia([
                        'album_id' => $albumId,
                        'type' => $type,
                        'url' => $url,
                        'caption' => $caption
                    ]);
                    Session::setFlash('success', 'Media asset added successfully.');
                } catch (\Throwable $e) {
                    Logger::error("Failed adding gallery media: " . $e->getMessage(), ['album_id' => $albumId]);
                    Session::setFlash('error', 'Failed to add the media asset.');
                }
            } else {
                Session::setFlash('error', 'Invalid media file or link.');
            }
        }
        redirect('/admin/cms/gallery?album_id=' . ($_POST['album_id'] ?? ''));
    }

    /**
     * Manage website testimonials.
     */
    public static function testimonials(): void
    {
        if (!Session::isLoggedIn()) { redirect('/login'); exit; }

        $testimonials = Cms::getTestimonials('active');
        include VIEWS_PATH . '/admin/cms/testimonials.php';
    }

    /**
     * Save testimonial.
     */
    public static function saveTestimonial(): void
    {
        if (!Session::isLoggedIn()) { redirect('/login'); exit; }
        if (!Security::verifyCsrfToken($_POST['csrf_token'] ?? null)) {
            Session::setFlash('error', 'Invalid security token. Please try again.');
            redirect('/admin/cms/testimonials');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                Cms::addTestimonial([
                    'type' => trim($_POST['type'] ?? 'patient'),
                    'author' => trim($_POST['author'] ?? ''),
                    'rating' => (int)($_POST['rating'] ?? 5),
                    'review_text' => trim($_POST['review_text'] ?? ''),
                    'video_url' => trim($_POST['video_url'] ?? ''),
                    'status' => 'active'
                ]);
                Session::setFlash('success', 'Testimonial review logged successfully.');
            } catch (\Throwable $e) {
                Logger::error("Failed saving testimonial: " . $e->getMessage());
                Session::setFlash('error', 'Failed to save the testimonial.');
            }
        }
        redirect('/admin/cms/testimonials');
    }
}
