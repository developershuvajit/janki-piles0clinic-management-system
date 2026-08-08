<?php

namespace App\Models;

use App\Helpers\Database;

class Enquiry
{
    /**
     * Fetch all lead enquiries.
     */
    public static function all(): array
    {
        return Database::all("SELECT * FROM contact_enquiries ORDER BY id DESC");
    }

    /**
     * Find specific lead details.
     */
    public static function find(int $id): ?array
    {
        return Database::row("SELECT * FROM contact_enquiries WHERE id = :id LIMIT 1", ['id' => $id]);
    }

    /**
     * Create lead enquiry from public contact form.
     */
    public static function create(array $data): int
    {
        $sql = "INSERT INTO contact_enquiries (name, email, phone, subject, message, status) 
                VALUES (:name, :email, :phone, :sub, :msg, 'new')";
        Database::execute($sql, [
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'],
            'sub' => $data['subject'],
            'msg' => $data['message']
        ]);
        return (int)Database::lastInsertId();
    }

    /**
     * Update lead pipeline status and follow-up notes.
     */
    public static function updateStatus(int $id, string $status, ?string $notes = null): bool
    {
        $sql = "UPDATE contact_enquiries SET status = :status, notes = :notes, updated_at = NOW() WHERE id = :id";
        return Database::execute($sql, [
            'status' => $status,
            'notes' => $notes,
            'id' => $id
        ]);
    }
}
