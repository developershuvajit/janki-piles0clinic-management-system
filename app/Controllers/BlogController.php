<?php

namespace App\Controllers;

use App\Helpers\Session;
use App\Helpers\Security;
use App\Models\Blog;

class BlogController
{
    /**
     * Display all blogs list.
     */
    public static function index(): void
    {
        if (!Session::isLoggedIn()) { redirect('/login'); exit; }

        $blogs = Blog::getBlogs();
        include VIEWS_PATH . '/admin/cms/blogs.php';
    }

    /**
     * Save blog post.
     */
    public static function save(): void
    {
        if (!Session::isLoggedIn()) { redirect('/login'); exit; }
        Security::requireCsrfToken('/admin/cms/blogs', 'Invalid security token. Please try again.');

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
            $title = trim($_POST['title'] ?? '');
            $slug = strtolower(preg_replace('/[^A-Za-z0-9-]+/', '-', $title));
            $content = trim($_POST['content'] ?? '');
            
            // Image Upload
            $imageUrl = $_POST['existing_image'] ?? '';
            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $destDir = __DIR__ . '/../../public/uploads/blog';
                if (!is_dir($destDir)) {
                    mkdir($destDir, 0777, true);
                }
                $fileName = time() . '_' . basename($_FILES['image']['name']);
                if (move_uploaded_file($_FILES['image']['tmp_name'], $destDir . '/' . $fileName)) {
                    $imageUrl = '/uploads/blog/' . $fileName;
                }
            }

            $blogData = [
                'title' => $title,
                'slug' => $slug,
                'content' => $content,
                'category_id' => !empty($_POST['category_id']) ? (int)$_POST['category_id'] : null,
                'tags' => trim($_POST['tags'] ?? ''),
                'image_url' => $imageUrl,
                'status' => trim($_POST['status'] ?? 'draft'),
                'seo_title' => trim($_POST['seo_title'] ?? ''),
                'seo_description' => trim($_POST['seo_description'] ?? '')
            ];

            if ($id > 0) {
                Blog::updateBlog($id, $blogData);
                Session::setFlash('success', 'Blog article updated successfully.');
            } else {
                Blog::createBlog($blogData);
                Session::setFlash('success', 'Blog article published successfully.');
            }
        }
        redirect('/admin/cms/blogs');
    }

    /**
     * Display comment moderation queue.
     */
    public static function comments(): void
    {
        if (!Session::isLoggedIn()) { redirect('/login'); exit; }

        // Fetch all comments in DB
        $comments = \App\Helpers\Database::all("
            SELECT c.*, b.title as blog_title 
            FROM blog_comments c 
            JOIN blogs b ON c.blog_id = b.id 
            ORDER BY c.created_at DESC
        ");
        
        include VIEWS_PATH . '/admin/cms/comments.php';
    }

    /**
     * Approve comment.
     */
    public static function approveComment(int $id): void
    {
        if (!Session::isLoggedIn()) { redirect('/login'); exit; }

        if (Blog::updateCommentStatus($id, 'approved')) {
            Session::setFlash('success', 'Comment approved.');
        }
        redirect('/admin/cms/comments');
    }

    /**
     * Reject comment.
     */
    public static function rejectComment(int $id): void
    {
        if (!Session::isLoggedIn()) { redirect('/login'); exit; }

        if (Blog::updateCommentStatus($id, 'rejected')) {
            Session::setFlash('success', 'Comment rejected.');
        }
        redirect('/admin/cms/comments');
    }
}
