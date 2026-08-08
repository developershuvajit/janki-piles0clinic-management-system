<?php

namespace App\Controllers;

use App\Models\Billing;
use App\Helpers\Session;
use App\Helpers\Permission;

class BillingController
{
    public function __construct()
    {
        if (!Session::get('user_id')) {
            redirect('/login');
        }
    }

    /**
     * Outstanding Invoices Ledger.
     */
    public function index()
    {
        Permission::check('manage_reception_dashboard'); // Or billing manager role

        $branchId = Session::get('role') !== 'super_admin' ? (int)Session::get('branch_id') : null;
        $invoices = Billing::getInvoices($branchId);

        include VIEWS_PATH . '/admin/billing/index.php';
    }

    /**
     * Show checkout payment processing screen.
     */
    public function collectForm($id)
    {
        Permission::check('manage_reception_dashboard');
        
        $bill = Billing::find((int)$id);
        if (!$bill) {
            Session::setFlash('error', 'Invoice record not found.');
            redirect('/admin/billing');
        }

        include VIEWS_PATH . '/admin/billing/collect.php';
    }

    /**
     * Save payment collection.
     */
    public function processPayment()
    {
        Permission::check('manage_reception_dashboard');
        
        $billId = (int)$_POST['bill_id'];
        $paidAmt = (float)$_POST['paid_amount'];
        $method = $_POST['payment_method'];

        $bill = Billing::find($billId);
        if (!$bill) {
            Session::setFlash('error', 'Invoice not found.');
            redirect('/admin/billing');
        }

        $success = Billing::recordPayment($billId, [
            'paid_amount' => $paidAmt,
            'payment_method' => $method,
            'payment_status' => $paidAmt >= $bill['total'] ? 'paid' : 'partial'
        ]);

        if ($success) {
            Session::setFlash('success', 'Payment collections saved successfully.');
            redirect('/admin/billing/receipt/' . $billId);
        } else {
            Session::setFlash('error', 'Failed recording payment.');
            redirect('/admin/billing/collect/' . $billId);
        }
    }

    /**
     * Show refund panel.
     */
    public function refundForm($id)
    {
        Permission::check('manage_reception_dashboard');
        
        $bill = Billing::find((int)$id);
        if (!$bill) {
            Session::setFlash('error', 'Invoice not found.');
            redirect('/admin/billing');
        }

        include VIEWS_PATH . '/admin/billing/refund.php';
    }

    /**
     * Settle refund.
     */
    public function processRefund()
    {
        Permission::check('manage_reception_dashboard');
        
        $billId = (int)$_POST['bill_id'];
        $refAmt = (float)$_POST['refund_amount'];
        $reason = trim($_POST['refund_reason']);

        if ($refAmt <= 0.00) {
            Session::setFlash('error', 'Please enter a valid refund amount.');
            redirect('/admin/billing/refund/' . $billId);
        }

        $success = Billing::recordRefund($billId, $refAmt, $reason);
        if ($success) {
            Session::setFlash('success', 'Refund logged successfully.');
        } else {
            Session::setFlash('error', 'Failed to process refund (amount exceeds paid funds).');
        }
        redirect('/admin/billing');
    }

    /**
     * View Invoice Receipt.
     */
    public function receiptPrint($id)
    {
        $bill = Billing::find((int)$id);
        if (!$bill) {
            Session::setFlash('error', 'Receipt not found.');
            redirect('/admin/billing');
        }

        include VIEWS_PATH . '/admin/billing/receipt.php';
    }
}
