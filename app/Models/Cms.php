<?php

namespace App\Models;

use App\Helpers\Database;

class Cms
{
    /**
     * Retrieve all config settings as a mapped key-value array.
     */
    public static function getSettings(): array
    {
        $rows = Database::all("SELECT config_key, config_value FROM website_settings");
        $settings = [];
        foreach ($rows as $row) {
            $settings[$row['config_key']] = $row['config_value'];
        }
        return $settings;
    }

    /**
     * Bulk save website config settings.
     */
    public static function saveSettings(array $settings): bool
    {
        $sql = "INSERT INTO website_settings (config_key, config_value) 
                VALUES (:key, :val) 
                ON DUPLICATE KEY UPDATE config_value = VALUES(config_value)";
        
        Database::beginTransaction();
        try {
            foreach ($settings as $key => $val) {
                Database::execute($sql, [
                    'key' => $key,
                    'val' => is_array($val) ? json_encode($val) : (string)$val
                ]);
            }
            Database::commit();
            return true;
        } catch (\Throwable $e) {
            Database::rollBack();
            return false;
        }
    }

    /**
     * Fetch media albums.
     */
    public static function getAlbums(): array
    {
        return Database::all("SELECT * FROM gallery_albums ORDER BY name ASC");
    }

    /**
     * Create album.
     */
    public static function createAlbum(array $data): int
    {
        $sql = "INSERT INTO gallery_albums (name, slug, description) VALUES (:name, :slug, :desc)";
        return Database::execute($sql, [
            'name' => $data['name'],
            'slug' => $data['slug'],
            'desc' => $data['description'] ?? null
        ]);
    }

    /**
     * Fetch media entries inside an album.
     */
    public static function getAlbumMedia(int $albumId): array
    {
        return Database::all("SELECT * FROM gallery_media WHERE album_id = :id ORDER BY id DESC", ['id' => $albumId]);
    }

    /**
     * Add media photo / video link to album.
     */
    public static function addMedia(array $data): int
    {
        $sql = "INSERT INTO gallery_media (album_id, type, url, caption) VALUES (:album_id, :type, :url, :caption)";
        return Database::execute($sql, [
            'album_id' => $data['album_id'],
            'type' => $data['type'] ?? 'photo',
            'url' => $data['url'],
            'caption' => $data['caption'] ?? null
        ]);
    }

    /**
     * Fetch active testimonials.
     */
    public static function getTestimonials(string $status = 'active'): array
    {
        return Database::all("SELECT * FROM testimonials WHERE status = :status ORDER BY created_at DESC", ['status' => $status]);
    }

    /**
     * Add testimonial review.
     */
    public static function addTestimonial(array $data): int
    {
        $sql = "INSERT INTO testimonials (type, author, rating, review_text, video_url, status) 
                VALUES (:type, :author, :rating, :text, :video_url, :status)";
        return Database::execute($sql, [
            'type' => $data['type'] ?? 'patient',
            'author' => $data['author'],
            'rating' => (int)($data['rating'] ?? 5),
            'text' => $data['review_text'],
            'video_url' => $data['video_url'] ?? null,
            'status' => $data['status'] ?? 'active'
        ]);
    }
}
