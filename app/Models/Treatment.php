<?php

namespace App\Models;

use App\Helpers\Database;
use App\Helpers\Logger;

class Treatment
{
    /**
     * Fetch all treatments.
     */
    public static function all(?string $status = null): array
    {
        $sql = "SELECT * FROM treatments";
        $params = [];
        if ($status !== null) {
            $sql .= " WHERE status = :status";
            $params['status'] = $status;
        }
        $sql .= " ORDER BY title ASC";
        return Database::all($sql, $params);
    }

    /**
     * Find specific treatment by ID.
     */
    public static function find(int $id): ?array
    {
        return Database::row("SELECT * FROM treatments WHERE id = :id LIMIT 1", ['id' => $id]);
    }

    /**
     * Find treatment by URL slug.
     */
    public static function findBySlug(string $slug): ?array
    {
        return Database::row("SELECT * FROM treatments WHERE slug = :slug LIMIT 1", ['slug' => $slug]);
    }

    /**
     * Create treatment entry.
     */
    public static function create(array $data): int
    {
        $sql = "INSERT INTO treatments (title, slug, content, price, image_url, video_url, status, seo_title, seo_description) 
                VALUES (:title, :slug, :content, :price, :img, :vid, :status, :seo_t, :seo_d)";
        Database::execute($sql, [
            'title' => $data['title'],
            'slug' => $data['slug'],
            'content' => $data['content'],
            'price' => (float)$data['price'],
            'img' => $data['image_url'] ?? null,
            'vid' => $data['video_url'] ?? null,
            'status' => $data['status'] ?? 'active',
            'seo_t' => $data['seo_title'] ?? null,
            'seo_d' => $data['seo_description'] ?? null
        ]);
        return (int)Database::lastInsertId();
    }

    /**
     * Update treatment details.
     */
    public static function update(int $id, array $data): bool
    {
        $sql = "UPDATE treatments 
                SET title = :title, slug = :slug, content = :content, price = :price, 
                    image_url = :img, video_url = :vid, status = :status, seo_title = :seo_t, seo_description = :seo_d 
                WHERE id = :id";
        return Database::execute($sql, [
            'title' => $data['title'],
            'slug' => $data['slug'],
            'content' => $data['content'],
            'price' => (float)$data['price'],
            'img' => $data['image_url'] ?? null,
            'vid' => $data['video_url'] ?? null,
            'status' => $data['status'] ?? 'active',
            'seo_t' => $data['seo_title'] ?? null,
            'seo_d' => $data['seo_description'] ?? null,
            'id' => $id
        ]);
    }

    /**
     * Retrieve assigned doctors for a treatment.
     */
    public static function getDoctors(int $treatmentId): array
    {
        $sql = "SELECT u.id, u.username, u.email 
                FROM treatment_doctors td
                JOIN users u ON td.doctor_id = u.id
                WHERE td.treatment_id = :id";
        return Database::all($sql, ['id' => $treatmentId]);
    }

    /**
     * Map attending doctors to treatment.
     */
    public static function assignDoctors(int $treatmentId, array $doctorIds): bool
    {
        Database::beginTransaction();
        try {
            Database::execute("DELETE FROM treatment_doctors WHERE treatment_id = :id", ['id' => $treatmentId]);
            
            $sql = "INSERT INTO treatment_doctors (treatment_id, doctor_id) VALUES (:treat_id, :doc_id)";
            foreach ($doctorIds as $docId) {
                Database::execute($sql, [
                    'treat_id' => $treatmentId,
                    'doc_id' => (int)$docId
                ]);
            }
            
            Database::commit();
            return true;
        } catch (\Throwable $e) {
            Database::rollBackIfActive();
            Logger::error("Failed assigning doctors to treatment {$treatmentId}: " . $e->getMessage());
            return false;
        }
    }
}
