<?php

namespace App\Models;

use App\Helpers\Database;

class Blog
{
    /**
     * Retrieve all blogs, optionally filtered by status.
     */
    public static function getBlogs(?string $status = null): array
    {
        $sql = "SELECT b.*, c.name as category_name 
                FROM blogs b
                LEFT JOIN blog_categories c ON b.category_id = c.id";
        
        $params = [];
        if ($status !== null) {
            $sql .= " WHERE b.status = :status";
            $params['status'] = $status;
        }

        $sql .= " ORDER BY b.created_at DESC";
        return Database::all($sql, $params);
    }

    /**
     * Locate blog by database ID.
     */
    public static function getBlog(int $id): ?array
    {
        $sql = "SELECT b.*, c.name as category_name 
                FROM blogs b
                LEFT JOIN blog_categories c ON b.category_id = c.id
                WHERE b.id = :id LIMIT 1";
        return Database::row($sql, ['id' => $id]);
    }

    /**
     * Locate blog by unique URL slug.
     */
    public static function getBlogBySlug(string $slug): ?array
    {
        $sql = "SELECT b.*, c.name as category_name 
                FROM blogs b
                LEFT JOIN blog_categories c ON b.category_id = c.id
                WHERE b.slug = :slug LIMIT 1";
        return Database::row($sql, ['slug' => $slug]);
    }

    /**
     * Create new blog post.
     */
    public static function createBlog(array $data): int
    {
        $sql = "INSERT INTO blogs (title, slug, content, category_id, tags, image_url, status, seo_title, seo_description) 
                VALUES (:title, :slug, :content, :category_id, :tags, :img, :status, :seo_t, :seo_d)";
        Database::execute($sql, [
            'title' => $data['title'],
            'slug' => $data['slug'],
            'content' => $data['content'],
            'category_id' => $data['category_id'] ?? null,
            'tags' => $data['tags'] ?? null,
            'img' => $data['image_url'] ?? null,
            'status' => $data['status'] ?? 'draft',
            'seo_t' => $data['seo_title'] ?? null,
            'seo_d' => $data['seo_description'] ?? null
        ]);
        return (int)Database::lastInsertId();
    }

    /**
     * Update blog post.
     */
    public static function updateBlog(int $id, array $data): bool
    {
        $sql = "UPDATE blogs 
                SET title = :title, slug = :slug, content = :content, category_id = :category_id, 
                    tags = :tags, image_url = :img, status = :status, seo_title = :seo_t, seo_description = :seo_d 
                WHERE id = :id";
        return Database::execute($sql, [
            'title' => $data['title'],
            'slug' => $data['slug'],
            'content' => $data['content'],
            'category_id' => $data['category_id'] ?? null,
            'tags' => $data['tags'] ?? null,
            'img' => $data['image_url'] ?? null,
            'status' => $data['status'] ?? 'draft',
            'seo_t' => $data['seo_title'] ?? null,
            'seo_d' => $data['seo_description'] ?? null,
            'id' => $id
        ]);
    }

    /**
     * Fetch blog categories.
     */
    public static function getCategories(): array
    {
        return Database::all("SELECT * FROM blog_categories ORDER BY name ASC");
    }

    /**
     * Create category.
     */
    public static function createCategory(array $data): int
    {
        $sql = "INSERT INTO blog_categories (name, slug) VALUES (:name, :slug)";
        Database::execute($sql, [
            'name' => $data['name'],
            'slug' => $data['slug']
        ]);
        return (int)Database::lastInsertId();
    }

    /**
     * Fetch comments.
     */
    public static function getComments(int $blogId, ?string $status = null): array
    {
        $sql = "SELECT * FROM blog_comments WHERE blog_id = :blog_id";
        $params = ['blog_id' => $blogId];
        if ($status !== null) {
            $sql .= " AND status = :status";
            $params['status'] = $status;
        }
        $sql .= " ORDER BY created_at DESC";
        return Database::all($sql, $params);
    }

    /**
     * Add comment.
     */
    public static function addComment(array $data): int
    {
        $sql = "INSERT INTO blog_comments (blog_id, author_name, author_email, comment_text, status) 
                VALUES (:blog_id, :name, :email, :text, :status)";
        Database::execute($sql, [
            'blog_id' => $data['blog_id'],
            'name' => $data['author_name'],
            'email' => $data['author_email'],
            'text' => $data['comment_text'],
            'status' => $data['status'] ?? 'pending'
        ]);
        return (int)Database::lastInsertId();
    }

    /**
     * Moderate comment.
     */
    public static function updateCommentStatus(int $commentId, string $status): bool
    {
        return Database::execute("UPDATE blog_comments SET status = :status WHERE id = :id", [
            'status' => $status,
            'id' => $commentId
        ]);
    }

    /**
     * Get related blog posts.
     */
    public static function getRelatedPosts(int $blogId, ?int $categoryId, int $limit = 3): array
    {
        if ($categoryId === null) {
            return Database::all("SELECT * FROM blogs WHERE id != :id AND status = 'published' LIMIT :limit", [
                'id' => $blogId,
                'limit' => $limit
            ]);
        }
        $sql = "SELECT * FROM blogs WHERE id != :id AND category_id = :cat_id AND status = 'published' LIMIT :limit";
        return Database::all($sql, [
            'id' => $blogId,
            'cat_id' => $categoryId,
            'limit' => $limit
        ]);
    }
}
