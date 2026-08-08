<?php

namespace App\Models;

use App\Helpers\Database;
use App\Helpers\Logger;

class Inventory
{
    /**
     * Fetch all medicines.
     */
    public static function getMedicines(): array
    {
        return Database::all("SELECT * FROM medicines ORDER BY name ASC");
    }

    /**
     * Find specific medicine by ID.
     */
    public static function getMedicine(int $id): ?array
    {
        return Database::row("SELECT * FROM medicines WHERE id = :id", ['id' => $id]);
    }

    /**
     * Create new medicine master.
     */
    public static function createMedicine(array $data): int
    {
        $sql = "INSERT INTO medicines (name, generic_name, sku, category, unit, min_stock_level, status) 
                VALUES (:name, :gen, :sku, :cat, :unit, :min_level, :status)";
        return Database::execute($sql, [
            'name' => $data['name'],
            'gen' => $data['generic_name'] ?? null,
            'sku' => $data['sku'],
            'cat' => $data['category'],
            'unit' => $data['unit'] ?? 'pcs',
            'min_level' => (int)($data['min_stock_level'] ?? 10),
            'status' => $data['status'] ?? 'active'
        ]);
    }

    /**
     * Retrieve all batch stocks.
     */
    public static function getStocks(): array
    {
        $sql = "SELECT s.*, m.name as medicine_name, m.sku, m.unit, m.category, sup.name as supplier_name 
                FROM medicine_stocks s
                JOIN medicines m ON s.medicine_id = m.id
                LEFT JOIN suppliers sup ON s.supplier_id = sup.id
                ORDER BY s.expiry_date ASC";
        return Database::all($sql);
    }

    /**
     * Retrieve low stock medicines.
     */
    public static function getLowStockItems(): array
    {
        $sql = "SELECT m.*, COALESCE(SUM(s.quantity), 0) as total_qty 
                FROM medicines m
                LEFT JOIN medicine_stocks s ON m.id = s.medicine_id
                GROUP BY m.id
                HAVING total_qty <= m.min_stock_level
                ORDER BY total_qty ASC";
        return Database::all($sql);
    }

    /**
     * Alias for retrieving low stock medicines.
     */
    public static function getLowStockMedicines(): array
    {
        return self::getLowStockItems();
    }

    /**
     * Create a purchase / Add stock batch.
     */
    public static function addStock(array $data): bool
    {
        Database::beginTransaction();
        try {
            // 1. Insert into stocks
            $sql = "INSERT INTO medicine_stocks (medicine_id, batch_number, expiry_date, quantity, supplier_id, purchase_price, selling_price) 
                    VALUES (:med_id, :batch, :expiry, :qty, :sup_id, :pur, :sel)";
            Database::execute($sql, [
                'med_id' => $data['medicine_id'],
                'batch' => $data['batch_number'],
                'expiry' => $data['expiry_date'],
                'qty' => $data['quantity'],
                'sup_id' => $data['supplier_id'] ?? null,
                'pur' => $data['purchase_price'],
                'sel' => $data['selling_price']
            ]);

            // 2. Log transaction
            $txSql = "INSERT INTO medicine_transactions (medicine_id, type, quantity, reason, created_by) 
                      VALUES (:med_id, 'stock_in', :qty, :reason, :user)";
            Database::execute($txSql, [
                'med_id' => $data['medicine_id'],
                'qty' => $data['quantity'],
                'reason' => "Purchase batch #{$data['batch_number']}",
                'user' => $data['created_by'] ?? null
            ]);

            Database::commit();
            return true;
        } catch (\Throwable $e) {
            Database::rollBack();
            Logger::error("Failed adding inventory stock: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Stock Out (FIFO deduction).
     */
    public static function stockOut(int $medicineId, int $qtyToDeduct, string $reason, ?int $userId = null): bool
    {
        Database::beginTransaction();
        try {
            // Fetch available batches for the medicine order by oldest expiry
            $batches = Database::all(
                "SELECT * FROM medicine_stocks WHERE medicine_id = :id AND quantity > 0 ORDER BY expiry_date ASC",
                ['id' => $medicineId]
            );

            $totalAvailable = array_sum(array_column($batches, 'quantity'));
            if ($totalAvailable < $qtyToDeduct) {
                throw new \Exception("Insufficient stock for medicine ID {$medicineId}. Request: {$qtyToDeduct}, Available: {$totalAvailable}");
            }

            $remaining = $qtyToDeduct;
            foreach ($batches as $batch) {
                if ($remaining <= 0) break;

                $batchId = (int)$batch['id'];
                $batchQty = (int)$batch['quantity'];

                if ($batchQty >= $remaining) {
                    // This batch covers the rest
                    Database::execute(
                        "UPDATE medicine_stocks SET quantity = quantity - :deduct WHERE id = :id",
                        ['deduct' => $remaining, 'id' => $batchId]
                    );
                    $remaining = 0;
                } else {
                    // Drain this batch entirely
                    Database::execute(
                        "UPDATE medicine_stocks SET quantity = 0 WHERE id = :id",
                        ['id' => $batchId]
                    );
                    $remaining -= $batchQty;
                }
            }

            // Record transaction audit log
            $txSql = "INSERT INTO medicine_transactions (medicine_id, type, quantity, reason, created_by) 
                      VALUES (:med_id, 'stock_out', :qty, :reason, :user)";
            Database::execute($txSql, [
                'med_id' => $medicineId,
                'qty' => $qtyToDeduct,
                'reason' => $reason,
                'user' => $userId
            ]);

            Database::commit();
            return true;
        } catch (\Throwable $e) {
            Database::rollBack();
            Logger::error("Failed stock deduction: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Fetch suppliers list.
     */
    public static function getSuppliers(): array
    {
        return Database::all("SELECT * FROM suppliers ORDER BY name ASC");
    }

    /**
     * Create supplier record.
     */
    public static function createSupplier(array $data): int
    {
        $sql = "INSERT INTO suppliers (name, phone, email, address, status) 
                VALUES (:name, :phone, :email, :addr, 'active')";
        return Database::execute($sql, [
            'name' => $data['name'],
            'phone' => $data['phone'],
            'email' => $data['email'] ?? null,
            'addr' => $data['address'] ?? null
        ]);
    }
}
