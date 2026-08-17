<?php
declare(strict_types=1);

namespace App\Models;

use App\Helpers\Database;

class Branch
{
    /**
     * Get Database instance
     */
    private static function getDb(): Database
    {
        return Database::getInstance();
    }

    /**
     * Retrieve all branches.
     */
    public static function all(): array
    {
        return self::getDb()->all("SELECT * FROM branches ORDER BY id DESC");
    }

    /**
     * Find a branch by ID (accepts both int and string).
     */
    public static function find($id): ?array
    {
        // Cast to int if it's a string or numeric
        $id = is_numeric($id) ? (int)$id : 0;
        
        if ($id <= 0) {
            return null;
        }
        
        return self::getDb()->row("SELECT * FROM branches WHERE id = :id LIMIT 1", ['id' => $id]);
    }

    /**
     * Create a new branch.
     */
    public static function create(array $data): bool
    {
        $sql = "INSERT INTO branches (name, logo, address, phone, emergency_number, email, google_map_link, opening_hours, status, created_at, updated_at) 
                VALUES (:name, :logo, :address, :phone, :emergency_number, :email, :google_map_link, :opening_hours, :status, NOW(), NOW())";
        
        $result = self::getDb()->execute($sql, [
            'name' => $data['name'],
            'logo' => $data['logo'] ?? null,
            'address' => $data['address'],
            'phone' => $data['phone'],
            'emergency_number' => $data['emergency_number'],
            'email' => $data['email'],
            'google_map_link' => $data['google_map_link'] ?? null,
            'opening_hours' => $data['opening_hours'],
            'status' => $data['status'] ?? 'active'
        ]);
        
        return $result > 0;
    }

    /**
     * Update an existing branch.
     */
    public static function update(int $id, array $data): bool
    {
        $sql = "UPDATE branches SET 
                    name = :name, 
                    logo = :logo, 
                    address = :address, 
                    phone = :phone, 
                    emergency_number = :emergency_number, 
                    email = :email, 
                    google_map_link = :google_map_link, 
                    opening_hours = :opening_hours, 
                    status = :status, 
                    updated_at = NOW() 
                WHERE id = :id";
        
        $result = self::getDb()->execute($sql, [
            'id' => $id,
            'name' => $data['name'],
            'logo' => $data['logo'] ?? null,
            'address' => $data['address'],
            'phone' => $data['phone'],
            'emergency_number' => $data['emergency_number'],
            'email' => $data['email'],
            'google_map_link' => $data['google_map_link'] ?? null,
            'opening_hours' => $data['opening_hours'],
            'status' => $data['status'] ?? 'active'
        ]);
        
        return $result > 0;
    }

    /**
     * Delete a branch.
     */
    public static function delete(int $id): bool
    {
        $result = self::getDb()->execute("DELETE FROM branches WHERE id = :id", ['id' => $id]);
        return $result > 0;
    }

    /**
     * Compile statistical aggregates for a specific branch.
     */
    public static function getBranchStats(int $branchId): array
    {
        $db = self::getDb();
        
        // 1. Patient Count
        $patientCount = $db->row(
            "SELECT COUNT(*) as count FROM patients WHERE branch_id = :branch_id", 
            ['branch_id' => $branchId]
        )['count'] ?? 0;
        
        // 2. Doctor Count
        $doctorCount = $db->row(
            "SELECT COUNT(u.id) as count 
             FROM users u 
             JOIN roles r ON u.role_id = r.id 
             WHERE u.branch_id = :branch_id AND r.slug = 'doctor' AND u.status = 'active'", 
            ['branch_id' => $branchId]
        )['count'] ?? 0;
        
        // 3. Total Branch Revenue (aggregating paid billing)
        $revenue = $db->row(
            "SELECT SUM(paid_amount) as total FROM billing WHERE branch_id = :branch_id AND payment_status = 'paid'", 
            ['branch_id' => $branchId]
        )['total'] ?? 0.00;

        // 4. Doctors List for the branch
        $doctors = $db->all(
            "SELECT u.id, u.username, u.email, e.photo, e.shift_start, e.shift_end
             FROM users u
             JOIN roles r ON u.role_id = r.id
             LEFT JOIN employees e ON u.id = e.user_id
             WHERE u.branch_id = :branch_id AND r.slug = 'doctor' AND u.status = 'active'", 
            ['branch_id' => $branchId]
        );

        return [
            'patient_count' => (int)$patientCount,
            'doctor_count' => (int)$doctorCount,
            'total_revenue' => (float)$revenue,
            'doctors' => $doctors
        ];
    }
}