<?php

namespace App\Controllers;

use App\Models\Inventory;
use App\Helpers\Session;
use App\Helpers\Permission;

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
        
        $data = [
            'medicine_id' => (int)$_POST['medicine_id'],
            'batch_number' => trim($_POST['batch_number']),
            'expiry_date' => $_POST['expiry_date'],
            'quantity' => (int)$_POST['quantity'],
            'supplier_id' => !empty($_POST['supplier_id']) ? (int)$_POST['supplier_id'] : null,
            'purchase_price' => (float)$_POST['purchase_price'],
            'selling_price' => (float)$_POST['selling_price'],
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
        
        $data = [
            'name' => trim($_POST['name']),
            'phone' => trim($_POST['phone']),
            'email' => trim($_POST['email']),
            'address' => trim($_POST['address'])
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
