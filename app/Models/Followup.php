<?php
declare(strict_types=1);

namespace App\Models;

use App\Helpers\Database;
use App\Helpers\Security;

class Followup
{
    /**
     * Get followups by tab filter (due, upcoming, missed, completed).
     */
    public static function getList(?int $branchId = null, string $tab = 'due'): array
    {
        $today = date('Y-m-d');
        $sql = "SELECT f.*, p.name as patient_name, p.patient_id as patient_code, p.phone as patient_phone, 
                       p.whatsapp_number, p.email as patient_email, b.name as branch_name 
                FROM patient_followups f
                JOIN patients p ON f.patient_id = p.id
                LEFT JOIN branches b ON f.branch_id = b.id
                WHERE 1=1";
        $params = [];

        if ($branchId !== null) {
            $sql .= " AND (f.branch_id = :branch_id OR f.branch_id IS NULL)";
            $params['branch_id'] = $branchId;
        }

        if ($tab === 'due') {
            $sql .= " AND f.next_visit_date = :today AND f.status != 'completed'";
            $params['today'] = $today;
        } elseif ($tab === 'upcoming') {
            $sql .= " AND f.next_visit_date > :today AND f.status != 'completed'";
            $params['today'] = $today;
        } elseif ($tab === 'missed') {
            $sql .= " AND f.next_visit_date < :today AND f.status != 'completed'";
            $params['today'] = $today;
        } elseif ($tab === 'completed') {
            $sql .= " AND f.status = 'completed'";
        }

        $sql .= " ORDER BY f.next_visit_date ASC LIMIT 100";

        return Database::all($sql, $params);
    }

    /**
     * Create a new patient follow-up.
     */
    public static function create(array $data): int
    {
        $sql = "INSERT INTO patient_followups (patient_id, branch_id, appointment_id, next_visit_date, status, channel, notes) 
                VALUES (:patient_id, :branch_id, :appointment_id, :next_visit_date, :status, :channel, :notes)";
        
        Database::execute($sql, [
            'patient_id' => (int)$data['patient_id'],
            'branch_id' => $data['branch_id'] ?? null,
            'appointment_id' => $data['appointment_id'] ?? null,
            'next_visit_date' => $data['next_visit_date'],
            'status' => $data['status'] ?? 'due',
            'channel' => $data['channel'] ?? 'whatsapp',
            'notes' => Security::sanitize($data['notes'] ?? '')
        ]);

        return (int)Database::lastInsertId();
    }

    /**
     * Update follow-up status.
     */
    public static function updateStatus(int $id, string $status, ?string $notes = null): bool
    {
        $sql = "UPDATE patient_followups SET status = :status";
        $params = ['status' => $status, 'id' => $id];

        if ($notes !== null) {
            $sql .= ", notes = :notes";
            $params['notes'] = Security::sanitize($notes);
        }

        $sql .= " WHERE id = :id";

        return Database::execute($sql, $params);
    }

    /**
     * Get follow-up metrics summary.
     */
    public static function getMetrics(?int $branchId = null): array
    {
        $today = date('Y-m-d');
        $params = ['today' => $today];
        $branchClause = "";

        if ($branchId !== null) {
            $branchClause = " AND (branch_id = :branch_id OR branch_id IS NULL)";
            $params['branch_id'] = $branchId;
        }

        $due = Database::row("SELECT COUNT(*) as c FROM patient_followups WHERE next_visit_date = :today AND status != 'completed'" . $branchClause, $params)['c'] ?? 0;
        $upcoming = Database::row("SELECT COUNT(*) as c FROM patient_followups WHERE next_visit_date > :today AND status != 'completed'" . $branchClause, $params)['c'] ?? 0;
        $missed = Database::row("SELECT COUNT(*) as c FROM patient_followups WHERE next_visit_date < :today AND status != 'completed'" . $branchClause, $params)['c'] ?? 0;
        $completedParams = $branchId !== null ? ['branch_id' => $branchId] : [];
        $completed = Database::row("SELECT COUNT(*) as c FROM patient_followups WHERE status = 'completed'" . $branchClause, $completedParams)['c'] ?? 0;

        return [
            'due' => (int)$due,
            'upcoming' => (int)$upcoming,
            'missed' => (int)$missed,
            'completed' => (int)$completed
        ];
    }
}
