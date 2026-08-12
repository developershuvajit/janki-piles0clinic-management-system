<?php
declare(strict_types=1);

namespace App\Models;

use App\Helpers\Database;
use App\Helpers\Logger;

class Billing
{
    /**
     * Create a new billing record.
     */
    public static function createBilling(array $data): ?int
    {
        try {
            $sql = "INSERT INTO billing (patient_id, branch_id, type, reference_id, subtotal, discount, tax, gst, total, paid_amount, outstanding, payment_status, payment_method, created_at, updated_at) 
                    VALUES (:patient_id, :branch_id, :type, :reference_id, :subtotal, :discount, :tax, :gst, :total, :paid_amount, :outstanding, :payment_status, :payment_method, NOW(), NOW())";
            
            $subtotal = (float)$data['subtotal'];
            $discount = (float)($data['discount'] ?? 0.00);
            $tax = (float)($data['tax'] ?? 0.00);
            $gst = (float)($data['gst'] ?? 0.00);
            $total = (float)($data['total'] ?? ($subtotal - $discount + $tax + $gst));
            $paid = (float)($data['paid_amount'] ?? 0.00);
            $outstanding = $total - $paid;
            if ($outstanding < 0.00) {
                $outstanding = 0.00;
            }

            $status = $data['payment_status'] ?? ($outstanding <= 0.00 ? 'paid' : ($paid > 0.00 ? 'partial' : 'unpaid'));

            $success = Database::execute($sql, [
                'patient_id' => $data['patient_id'],
                'branch_id' => $data['branch_id'],
                'type' => $data['type'],
                'reference_id' => $data['reference_id'],
                'subtotal' => $subtotal,
                'discount' => $discount,
                'tax' => $tax,
                'gst' => $gst,
                'total' => $total,
                'paid_amount' => $paid,
                'outstanding' => $outstanding,
                'payment_status' => $status,
                'payment_method' => $data['payment_method'] ?? 'none'
            ]);
            
            return $success ? (int)Database::lastInsertId() : null;
        } catch (\Throwable $e) {
            Logger::error("Failed to create billing record: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Find a billing invoice by database ID.
     */
    public static function find(int $id): ?array
    {
        $sql = "SELECT b.*, p.name as patient_name, p.patient_id as patient_code, p.phone as patient_phone, p.address as patient_address,
                       br.name as branch_name, br.phone as branch_phone, br.address as branch_address, br.email as branch_email 
                FROM billing b
                JOIN patients p ON b.patient_id = p.id
                JOIN branches br ON b.branch_id = br.id
                WHERE b.id = :id LIMIT 1";
        return Database::row($sql, ['id' => $id]);
    }

    /**
     * Retrieve an invoice by reference ID and type.
     */
    public static function getInvoiceByReference(string $type, int $referenceId): ?array
    {
        $sql = "SELECT b.*, p.name as patient_name, p.patient_id as patient_code, p.phone as patient_phone,
                       br.name as branch_name 
                FROM billing b
                JOIN patients p ON b.patient_id = p.id
                JOIN branches br ON b.branch_id = br.id
                WHERE b.type = :type AND b.reference_id = :ref_id LIMIT 1";
        return Database::row($sql, ['type' => $type, 'ref_id' => $referenceId]);
    }

    /**
     * Record payment transaction details.
     */
    public static function recordPayment(int $billId, array $data): bool
    {
        $bill = self::find($billId);
        if (!$bill) {
            return false;
        }

        $total = (float)$bill['total'];
        $paid = (float)$data['paid_amount'];
        $outstanding = $total - $paid;
        if ($outstanding < 0.00) {
            $outstanding = 0.00;
        }

        $status = $data['payment_status'] ?? ($outstanding <= 0.00 ? 'paid' : ($paid > 0.00 ? 'partial' : 'unpaid'));

        $sql = "UPDATE billing SET 
                    paid_amount = :paid, 
                    outstanding = :outstanding,
                    payment_status = :status, 
                    payment_method = :method, 
                    updated_at = NOW() 
                WHERE id = :id";
        
        return Database::execute($sql, [
            'id' => $billId,
            'paid' => $paid,
            'outstanding' => $outstanding,
            'status' => $status,
            'method' => $data['payment_method']
        ]);
    }

    /**
     * Process invoice refund.
     */
    public static function recordRefund(int $billId, float $refundAmount, string $reason): bool
    {
        $bill = self::find($billId);
        if (!$bill) {
            return false;
        }

        $paid = (float)$bill['paid_amount'];
        if ($refundAmount > $paid) {
            return false; // Cannot refund more than paid
        }

        $sql = "UPDATE billing 
                SET refunded_amount = refunded_amount + :ref_amt1,
                    paid_amount = paid_amount - :ref_amt2,
                    outstanding = total - (paid_amount - :ref_amt3),
                    payment_status = CASE WHEN (paid_amount - :ref_amt4) <= 0.00 THEN 'refunded' ELSE 'partial' END,
                    refund_reason = :reason,
                    updated_at = NOW()
                WHERE id = :id";

        return Database::execute($sql, [
            'ref_amt1' => $refundAmount,
            'ref_amt2' => $refundAmount,
            'ref_amt3' => $refundAmount,
            'ref_amt4' => $refundAmount,
            'reason' => $reason,
            'id' => $billId
        ]);
    }

    /**
     * Retrieve all invoices for ledger.
     */
    public static function getInvoices(?int $branchId = null): array
    {
        $sql = "SELECT b.*, p.name as patient_name, p.patient_id as patient_code, br.name as branch_name 
                FROM billing b
                JOIN patients p ON b.patient_id = p.id
                JOIN branches br ON b.branch_id = br.id";
        
        $params = [];
        $sql = Database::scopeToBranch($sql, $params, $branchId, 'b.branch_id');

        $sql .= " ORDER BY b.created_at DESC";
        return Database::all($sql, $params);
    }

    /**
     * Retrieve financial collections summary logs.
     */
    public static function getTodayCollectionsReport(?int $branchId = null): array
    {
        $date = date('Y-m-d');
        
        // 1. Total revenue grouped by payment method
        $methodSql = "SELECT payment_method, SUM(paid_amount) as total 
                      FROM billing 
                      WHERE DATE(created_at) = :date AND payment_status = 'paid'";
        $params = ['date' => $date];
        $methodSql = Database::scopeToBranch($methodSql, $params, $branchId);
        $methodSql .= " GROUP BY payment_method";
        $methods = Database::all($methodSql, $params);

        // 2. Counts of OPD registrations vs IPD admissions today
        $opdCountSql = "SELECT COUNT(*) as count FROM appointments WHERE date = :date";
        $ipdCountSql = "SELECT COUNT(*) as count FROM ipd_admissions WHERE DATE(admission_date) = :date";
        
        $paramsCounts = ['date' => $date];
        
        $opdCount = Database::count($opdCountSql, $paramsCounts);
        $ipdCount = Database::count($ipdCountSql, $paramsCounts);

        // 3. List of invoices collected today
        $invoicesSql = "SELECT b.*, p.name as patient_name, p.patient_id as patient_code 
                        FROM billing b
                        JOIN patients p ON b.patient_id = p.id
                        WHERE DATE(b.created_at) = :date AND b.payment_status = 'paid'";
        if ($branchId !== null) {
            $invoicesSql .= " AND b.branch_id = :branch_id";
        }
        $invoicesSql .= " ORDER BY b.updated_at DESC";
        $invoices = Database::all($invoicesSql, $params);

        // Map cash/upi/card splits
        $splits = ['cash' => 0.00, 'card' => 0.00, 'upi' => 0.00];
        $totalCollected = 0.00;
        foreach ($methods as $m) {
            $method = strtolower($m['payment_method']);
            if (isset($splits[$method])) {
                $splits[$method] = (float)$m['total'];
            }
            $totalCollected += (float)$m['total'];
        }

        return [
            'date' => $date,
            'total_collected' => $totalCollected,
            'splits' => $splits,
            'opd_registrations' => (int)$opdCount,
            'ipd_admissions' => (int)$ipdCount,
            'invoices' => $invoices
        ];
    }
}



