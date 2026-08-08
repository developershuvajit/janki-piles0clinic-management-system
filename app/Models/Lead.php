<?php
declare(strict_types=1);

namespace App\Models;

use App\Helpers\Database;
use App\Helpers\Security;

class Lead
{
    /**
     * Get all leads filtered by branch, status, or search term.
     */
    public static function all(?int $branchId = null, ?string $status = null, ?string $search = null): array
    {
        $sql = "SELECT l.*, b.name as branch_name, u.username as assigned_staff 
                FROM leads l
                LEFT JOIN branches b ON l.branch_id = b.id
                LEFT JOIN users u ON l.assigned_staff_id = u.id
                WHERE 1=1";
        $params = [];

        if ($branchId !== null) {
            $sql .= " AND (l.branch_id = :branch_id OR l.branch_id IS NULL)";
            $params['branch_id'] = $branchId;
        }

        if ($status !== null && $status !== '' && $status !== 'all') {
            $sql .= " AND l.status = :status";
            $params['status'] = $status;
        }

        if ($search !== null && trim($search) !== '') {
            $sql .= " AND (l.name LIKE :search OR l.phone LIKE :search OR l.email LIKE :search)";
            $params['search'] = '%' . trim($search) . '%';
        }

        $sql .= " ORDER BY l.id DESC LIMIT 100";

        return Database::all($sql, $params);
    }

    /**
     * Find lead by ID.
     */
    public static function find(int $id): ?array
    {
        return Database::row("SELECT * FROM leads WHERE id = :id LIMIT 1", ['id' => $id]);
    }

    /**
     * Create a new lead inquiry.
     */
    public static function create(array $data): int
    {
        $sql = "INSERT INTO leads (branch_id, name, phone, email, source, status, follow_up_date, notes) 
                VALUES (:branch_id, :name, :phone, :email, :source, :status, :follow_up_date, :notes)";
        
        Database::execute($sql, [
            'branch_id' => $data['branch_id'] ?? null,
            'name' => Security::sanitize($data['name'] ?? ''),
            'phone' => Security::sanitize($data['phone'] ?? ''),
            'email' => Security::sanitize($data['email'] ?? ''),
            'source' => $data['source'] ?? 'Walk-In',
            'status' => $data['status'] ?? 'new',
            'follow_up_date' => !empty($data['follow_up_date']) ? $data['follow_up_date'] : null,
            'notes' => Security::sanitize($data['notes'] ?? '')
        ]);

        return (int)Database::lastInsertId();
    }

    /**
     * Update lead status.
     */
    public static function updateStatus(int $id, string $status, ?string $notes = null): bool
    {
        $params = ['status' => $status, 'id' => $id];
        $sql = "UPDATE leads SET status = :status";
        
        if ($notes !== null) {
            $sql .= ", notes = CONCAT(COALESCE(notes, ''), '\n[', NOW(), '] ', :notes)";
            $params['notes'] = Security::sanitize($notes);
        }

        $sql .= " WHERE id = :id";

        return Database::execute($sql, $params);
    }

    /**
     * Get summary counts of leads grouped by status.
     */
    public static function getStatusCounts(?int $branchId = null): array
    {
        $sql = "SELECT status, COUNT(*) as count FROM leads";
        $params = [];
        if ($branchId !== null) {
            $sql .= " WHERE branch_id = :branch_id OR branch_id IS NULL";
            $params['branch_id'] = $branchId;
        }
        $sql .= " GROUP BY status";

        $rows = Database::all($sql, $params);
        $counts = [
            'total' => 0,
            'new' => 0,
            'contacted' => 0,
            'interested' => 0,
            'appointment_booked' => 0,
            'converted' => 0,
            'lost' => 0
        ];

        foreach ($rows as $r) {
            $counts[$r['status']] = (int)$r['count'];
            $counts['total'] += (int)$r['count'];
        }

        return $counts;
    }
}
