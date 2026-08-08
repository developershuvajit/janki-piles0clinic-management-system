<?php
declare(strict_types=1);

namespace App\Models;

use App\Helpers\Database;
use App\Helpers\Security;

class Communication
{
    /**
     * Pre-defined WhatsApp & SMS Message Templates.
     */
    public static function getTemplates(): array
    {
        return [
            'appointment_confirmation' => [
                'name' => 'Appointment Confirmation',
                'category' => 'OPD Booking',
                'body' => "Namaste {PATIENT_NAME}, your OPD appointment at Janki Piles Clinic is CONFIRMED for {DATE} at {TIME} with {DOCTOR_NAME}.\nToken No: {TOKEN}.\nLocation: {CLINIC_ADDRESS}.\nEmergency Helpline: +91 98765 43210."
            ],
            'appointment_reminder' => [
                'name' => 'Appointment Reminder',
                'category' => 'OPD Booking',
                'body' => "Dear {PATIENT_NAME}, reminder for your upcoming doctor consultation today ({DATE} at {TIME}) at Janki Piles Clinic. Please arrive 15 mins prior. Helpline: +91 98765 43210."
            ],
            'followup_reminder' => [
                'name' => 'Follow-up Due Reminder',
                'category' => 'Follow-up',
                'body' => "Namaste {PATIENT_NAME}, your post-treatment follow-up checkup at Janki Piles Clinic is DUE on {DATE}.\nProper follow-up ensures complete healing. Please call us to confirm your time slot."
            ],
            'payment_receipt' => [
                'name' => 'Payment Receipt',
                'category' => 'Billing',
                'body' => "Thank you {PATIENT_NAME}! Payment of Rs. {AMOUNT} received against Invoice #{INVOICE_NO} at Janki Piles Clinic. Wishing you good health!"
            ],
            'prescription_ready' => [
                'name' => 'Prescription & Medicine Ready',
                'category' => 'Pharmacy',
                'body' => "Dear {PATIENT_NAME}, your prescribed medicines & Ayurvedic dosage instructions are ready for pickup at Janki Piles Clinic Pharmacy Desk."
            ],
            'review_request' => [
                'name' => 'Patient Review Request',
                'category' => 'Feedback',
                'body' => "Dear {PATIENT_NAME}, thank you for visiting Janki Piles Clinic! Please share your valuable experience with us on Google Review: https://g.page/r/jankipilesclinic/review"
            ]
        ];
    }

    /**
     * Format WhatsApp template with dynamic variables.
     */
    public static function formatMessage(string $templateKey, array $vars): string
    {
        $templates = self::getTemplates();
        if (!isset($templates[$templateKey])) {
            return $vars['custom_message'] ?? '';
        }

        $body = $templates[$templateKey]['body'];
        foreach ($vars as $k => $v) {
            $body = str_replace('{' . strtoupper($k) . '}', (string)$v, $body);
        }

        return $body;
    }

    /**
     * Generate 1-Click WhatsApp web link (`https://wa.me/...`).
     */
    public static function getWhatsAppLink(string $phone, string $message): string
    {
        $cleanPhone = preg_replace('/[^0-9]/', '', $phone);
        if (!str_starts_with($cleanPhone, '91') && strlen($cleanPhone) === 10) {
            $cleanPhone = '91' . $cleanPhone;
        }

        return 'https://wa.me/' . $cleanPhone . '?text=' . urlencode($message);
    }

    /**
     * Log communication record.
     */
    public static function log(array $data): int
    {
        $sql = "INSERT INTO communication_logs (patient_id, type, channel, template_name, recipient_phone, message_body, sent_status) 
                VALUES (:patient_id, :type, :channel, :template_name, :recipient_phone, :message_body, :sent_status)";

        Database::execute($sql, [
            'patient_id' => $data['patient_id'] ?? null,
            'type' => $data['type'] ?? 'custom',
            'channel' => $data['channel'] ?? 'whatsapp',
            'template_name' => Security::sanitize($data['template_name'] ?? 'Manual'),
            'recipient_phone' => Security::sanitize($data['recipient_phone'] ?? ''),
            'message_body' => Security::sanitize($data['message_body'] ?? ''),
            'sent_status' => $data['sent_status'] ?? 'sent'
        ]);

        return (int)Database::lastInsertId();
    }

    /**
     * Fetch recent communication logs.
     */
    public static function getLogs(int $limit = 50): array
    {
        $sql = "SELECT c.*, p.name as patient_name 
                FROM communication_logs c
                LEFT JOIN patients p ON c.patient_id = p.id
                ORDER BY c.id DESC LIMIT " . (int)$limit;

        return Database::all($sql);
    }
}
