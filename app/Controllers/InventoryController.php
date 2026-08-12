<?php

namespace App\Controllers;

use App\Models\Inventory;
use App\Helpers\Session;
use App\Helpers\Permission;
use App\Helpers\Security;

class InventoryController
{
    public function __construct()
    {
        if (!Session::get('user_id')) {
            redirect('/login');
        }
    }

    /**
     * Active inventory stock roster.
     */
    public function index()
    {
        Permission::check('manage_reception_dashboard'); // Receptionist/pharmacist/admin can view inventory
        
        $stocks = Inventory::getStocks();
        $medicines = Inventory::getMedicines();
        $suppliers = Inventory::getSuppliers();
        
        include VIEWS_PATH . '/admin/inventory/index.php';
    }

    /**
     * Low and expired stocks alerts list.
     */
    public function lowStockList()
    {
        Permission::check('manage_reception_dashboard');
        
        $lowStock = Inventory::getLowStockItems();
        
        include VIEWS_PATH . '/admin/inventory/low_stock.php';
    }

    /**
     * Form to log incoming supply batches.
     */
    public function purchaseForm()
    {
        Permission::check('manage_reception_dashboard');
        
        $medicines = Inventory::getMedicines();
        $suppliers = Inventory::getSuppliers();
        
        include VIEWS_PATH . '/admin/inventory/purchase.php';
    }

    /**
     * Save supply batch.
     */
    public function savePurchase()
    {
        Permission::check('manage_reception_dashboard');

        if (!Security::verifyRequestToken()) {
            Session::setFlash('error', 'Security validation failed. Please refresh and try again.');
            redirect('/admin/inventory/purchase');
        }

        $data = [
            'medicine_id' => (int)($_POST['medicine_id'] ?? 0),
            'batch_number' => Security::sanitize(trim($_POST['batch_number'] ?? '')),
            'expiry_date' => Security::sanitize($_POST['expiry_date'] ?? ''),
            'quantity' => (int)($_POST['quantity'] ?? 0),
            'supplier_id' => !empty($_POST['supplier_id']) ? (int)$_POST['supplier_id'] : null,
            'purchase_price' => (float)($_POST['purchase_price'] ?? 0),
            'selling_price' => (float)($_POST['selling_price'] ?? 0),
            'created_by' => (int)Session::get('user_id')
        ];

        $success = Inventory::addStock($data);
        if ($success) {
            Session::setFlash('success', 'Medicine stock batch logged successfully.');
            redirect('/admin/inventory');
        } else {
            Session::setFlash('error', 'Failed adding supply batch.');
            redirect('/admin/inventory/purchase');
        }
    }

    /**
     * Manage suppliers directory.
     */
    public function saveSupplier()
    {
        Permission::check('manage_reception_dashboard');

        if (!Security::verifyRequestToken()) {
            Session::setFlash('error', 'Security validation failed. Please refresh and try again.');
            redirect('/admin/inventory');
        }

        $data = [
            'name' => Security::sanitize(trim($_POST['name'] ?? '')),
            'phone' => Security::sanitize(trim($_POST['phone'] ?? '')),
            'email' => Security::sanitize(trim($_POST['email'] ?? '')),
            'address' => Security::sanitize(trim($_POST['address'] ?? ''))
        ];

        if (empty($data['name']) || empty($data['phone'])) {
            Session::setFlash('error', 'Supplier name and contact phone are required.');
        } else {
            Inventory::createSupplier($data);
            Session::setFlash('success', 'Supplier created successfully.');
        }
        redirect('/admin/inventory');
    }
}
