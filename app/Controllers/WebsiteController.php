<?php

namespace App\Controllers;

use App\Helpers\Session;
use App\Helpers\Security;
use App\Helpers\Logger;
use App\Models\Cms;
use App\Models\Blog;
use App\Models\Treatment;
use App\Models\Enquiry;

class WebsiteController
{
    /**
     * Public Homepage.
     */
    public static function home(): void
    {
        $settings = Cms::getSettings();
        $treatments = Treatment::all('active');
        $blogs = array_slice(Blog::getBlogs('published'), 0, 3);
        $testimonials = Cms::getTestimonials('active');
        
        $faqs = [];
        if (!empty($settings['faqs_json'])) {
            $faqs = json_decode($settings['faqs_json'], true) ?: [];
        }

        include VIEWS_PATH . '/website/home.php';
    }

    /**
     * About Us Page.
     */
    public static function about(): void
    {
        $settings = Cms::getSettings();
        include VIEWS_PATH . '/website/about.php';
    }

    /**
     * Active Consultant Doctors.
     */
    public static function doctors(): void
    {
        $settings = Cms::getSettings();
        $doctors = \App\Helpers\Database::all("
            SELECT u.*, e.photo, b.name as branch_name 
            FROM users u 
            JOIN roles r ON u.role_id = r.id 
            JOIN employees e ON u.id = e.user_id
            LEFT JOIN branches b ON u.branch_id = b.id
            WHERE r.slug = 'doctor' AND u.status = 'active'
        ");

        include VIEWS_PATH . '/website/doctors.php';
    }

    /**
     * Clinical Treatments Catalog.
     */
    public static function treatments(): void
    {
        $settings = Cms::getSettings();
        $treatments = Treatment::all('active');
        include VIEWS_PATH . '/website/treatments.php';
    }

    /**
     * Treatment Detailed profile.
     */
    public static function treatmentDetail($params): void
    {
        $slug = is_array($params) ? ($params['slug'] ?? '') : (string)$params;
        $settings = Cms::getSettings();
        $treatment = Treatment::findBySlug($slug);
        if (!$treatment) {
            redirect('/treatments');
        }

        $assignedDoctors = Treatment::getDoctors((int)$treatment['id']);
        include VIEWS_PATH . '/website/treatment_detail.php';
    }

    /**
     * FAQs Page.
     */
    public static function faqs(): void
    {
        $settings = Cms::getSettings();
        include VIEWS_PATH . '/website/faqs.php';
    }

    /**
     * Insurance & TPA Page.
     */
    public static function insurance(): void
    {
        $settings = Cms::getSettings();
        include VIEWS_PATH . '/website/insurance.php';
    }

    /**
     * Health Packages Page.
     */
    public static function healthPackages(): void
    {
        $settings = Cms::getSettings();
        include VIEWS_PATH . '/website/health_packages.php';
    }

    /**
     * Media Gallery.
     */
    public static function gallery(): void
    {
        $settings = Cms::getSettings();
        $albums = Cms::getAlbums();
        
        $albumMedia = [];
        foreach ($albums as $al) {
            $albumMedia[$al['id']] = Cms::getAlbumMedia((int)$al['id']);
        }

        include VIEWS_PATH . '/website/gallery.php';
    }

    /**
     * Published Blogs.
     */
    public static function blog(): void
    {
        $settings = Cms::getSettings();
        $blogs = Blog::getBlogs('published');
        include VIEWS_PATH . '/website/blog.php';
    }

    /**
     * Blog Detailed reading.
     */
    public static function blogDetail($params): void
    {
        $slug = is_array($params) ? ($params['slug'] ?? '') : (string)$params;
        $settings = Cms::getSettings();
        $blog = Blog::getBlogBySlug($slug);
        if (!$blog) {
            redirect('/blog');
        }

        $comments = Blog::getComments((int)$blog['id'], 'approved');
        $related = Blog::getRelatedPosts((int)$blog['id'], $blog['category_id'] ? (int)$blog['category_id'] : null, 3);
        
        include VIEWS_PATH . '/website/blog_detail.php';
    }

    /**
     * Submit comment on blog.
     */
    public static function saveComment(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $blogId = (int)($_POST['blog_id'] ?? 0);
            $blog = Blog::getBlog($blogId);
            if ($blog) {
                try {
                    Blog::addComment([
                        'blog_id' => $blogId,
                        'author_name' => trim($_POST['author_name'] ?? 'Anonymous'),
                        'author_email' => trim($_POST['author_email'] ?? ''),
                        'comment_text' => trim($_POST['comment_text'] ?? ''),
                        'status' => 'pending'
                    ]);
                    Session::setFlash('success', 'Your comment has been submitted and is pending moderation approval.');
                } catch (\Throwable $e) {
                    Logger::error("Failed saving blog comment: " . $e->getMessage(), ['blog_id' => $blogId]);
                    Session::setFlash('error', 'Sorry, your comment could not be submitted. Please try again.');
                }
                redirect('/blog/' . $blog['slug']);
            }
        }
        redirect('/blog');
    }

    /**
     * Contact Us Page.
     */
    public static function contact(): void
    {
        $settings = Cms::getSettings();
        $branches = \App\Models\Branch::all();
        include VIEWS_PATH . '/website/contact.php';
    }

    /**
     * Submit CRM Lead Enquiry.
     */
    public static function saveEnquiry(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                Enquiry::create([
                    'name' => trim($_POST['name'] ?? ''),
                    'email' => trim($_POST['email'] ?? ''),
                    'phone' => trim($_POST['phone'] ?? ''),
                    'subject' => trim($_POST['subject'] ?? 'Website Inquiry'),
                    'message' => trim($_POST['message'] ?? '')
                ]);
                Session::setFlash('success', 'Your inquiry has been received! Our clinical coordinator will reach out to you shortly.');
            } catch (\Throwable $e) {
                Logger::error("Failed saving website enquiry: " . $e->getMessage());
                Session::setFlash('error', 'Sorry, your inquiry could not be submitted. Please call the clinic or try again.');
            }
        }
        redirect('/contact');
    }
}
