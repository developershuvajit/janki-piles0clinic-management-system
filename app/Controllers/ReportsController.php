<?php

namespace App\Controllers;

use App\Helpers\Database;
use App\Helpers\Session;
use App\Helpers\Permission;

class ReportsController
{
    public function __construct()
    {
        if (!Session::get('user_id')) {
            redirect('/login');
        }
    }

    /**
     * Analytical Dashboard.
     */
    public function dashboard()
    {
        Permission::check('view_logs');
        
        $branchId = Session::get('role') !== 'super_admin' ? (int)Session::get('branch_id') : null;
        
        // 1. Revenue by branch
        $revSql = "SELECT b.name as label, SUM(bi.paid_amount) as value 
                   FROM billing bi
                   JOIN branches b ON bi.branch_id = b.id
                   WHERE bi.payment_status = 'paid'";
        $revParams = [];
        $revSql = Database::scopeToBranch($revSql, $revParams, $branchId, 'bi.branch_id');
        $revSql .= " GROUP BY b.id, b.name";
        $revenueData = Database::all($revSql, $revParams);

        // 2. Patient visits grouped by doctor
        $docSql = "SELECT u.username as label, COUNT(a.id) as value 
                   FROM appointments a
                   JOIN users u ON a.doctor_id = u.id";
        $docParams = [];
        $docSql = Database::scopeToBranch($docSql, $docParams, $branchId, 'a.branch_id');
        $docSql .= " GROUP BY u.id, u.username ORDER BY value DESC LIMIT 5";
        $doctorStats = Database::all($docSql, $docParams);

        // 3. Medicine issues / Outflow
        $medSql = "SELECT m.name as label, SUM(t.quantity) as value 
                   FROM medicine_transactions t
                   JOIN medicines m ON t.medicine_id = m.id
                   WHERE t.type = 'stock_out'
                   GROUP BY m.id, m.name ORDER BY value DESC LIMIT 5";
        $medicineStats = Database::all($medSql);

        // 4. Monthly patient registry counts - FIXED
        $patSql = "SELECT 
                    DATE_FORMAT(created_at, '%b %Y') as label, 
                    COUNT(id) as value 
                   FROM patients";
        $patParams = [];
        $patSql = Database::scopeToBranch($patSql, $patParams, $branchId);
        $patSql .= " GROUP BY DATE_FORMAT(created_at, '%Y-%m'), DATE_FORMAT(created_at, '%b %Y') 
                     ORDER BY DATE_FORMAT(created_at, '%Y-%m') ASC LIMIT 6";
        $patientStats = Database::all($patSql, $patParams);

        include VIEWS_PATH . '/admin/reports/dashboard.php';
    }
}