<?php

namespace App\Controllers;

use App\Models\Salary;
use App\Helpers\Session;
use App\Helpers\Permission;
use App\Helpers\Security;

class SalaryController
{
    public function __construct()
    {
        if (!Session::get('user_id')) {
            redirect('/login');
        }
    }

    /**
     * Payroll checklist directory.
     */
    public function index()
    {
        Permission::check('manage_employees'); // Or payroll admin role
        
        $monthYear = $_GET['month_year'] ?? date('m-Y');
        $branchId = Session::get('role') !== 'super_admin' ? (int)Session::get('branch_id') : null;

        // Auto-generate baseline slips if missing
        Salary::generatePayroll($monthYear, $branchId);
        
        $salaries = Salary::getSalariesForMonth($monthYear, $branchId);
        
        include VIEWS_PATH . '/admin/salary/index.php';
    }

    /**
     * Settle salary voucher adjustments.
     */
    public function settleSalary()
    {
        Permission::check('manage_employees');

        if (!Security::verifyRequestToken()) {
            Session::setFlash('error', 'Security validation failed. Please refresh and try again.');
            redirect('/admin/salary');
        }

        $id = (int)($_POST['salary_id'] ?? 0);
        $adjustments = [
            'advance' => (float)($_POST['advance'] ?? 0.00),
            'bonus' => (float)($_POST['bonus'] ?? 0.00),
            'deduction' => (float)($_POST['deduction'] ?? 0.00)
        ];

        $success = Salary::settlePayroll($id, $adjustments);
        if ($success) {
            Session::setFlash('success', 'Salary payroll voucher settled.');
            redirect('/admin/salary/payslip/' . $id);
        } else {
            Session::setFlash('error', 'Failed settling salary voucher.');
            redirect('/admin/salary');
        }
    }

    /**
     * View and print pay slip.
     */
    public function paySlip($id)
    {
        Permission::check('manage_employees');
        
        $slip = Salary::getSalary((int)$id);
        if (!$slip) {
            Session::setFlash('error', 'Salary voucher not located.');
            redirect('/admin/salary');
        }

        include VIEWS_PATH . '/admin/salary/payslip.php';
    }
}
