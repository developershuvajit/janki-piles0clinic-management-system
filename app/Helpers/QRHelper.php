<?php
declare(strict_types=1);

namespace App\Helpers;

// Attempt importing PHPQRCode library
$qrLibPath = ROOT_PATH . '/vendor/phpqrcode/qrlib.php';
if (file_exists($qrLibPath)) {
    require_once $qrLibPath;
}

class QRHelper
{
    /**
     * Generate a QR Code.
     * If local phpqrcode library is loaded, generates the PNG file locally.
     * Otherwise, falls back to a secure online API generator.
     * 
     * @param string $data Text/URL payload for the QR code
     * @param string $filename Custom filename for local generation
     * @param int $size Relative pixel matrix size (1-10)
     * @return string URL of the generated QR code image
     */
    public static function generate(string $data, string $filename = '', int $size = 4): string
    {
        $qrDir = PUBLIC_PATH . '/assets/uploads/qr_codes';
        if (!is_dir($qrDir)) {
            mkdir($qrDir, 0755, true);
        }

        if (empty($filename)) {
            $filename = md5($data) . '.png';
        }

        $filePath = $qrDir . '/' . $filename;
        $relativeUrl = 'assets/uploads/qr_codes/' . $filename;

        // Check if phpqrcode library class exists
        if (class_exists('QRcode')) {
            try {
                if (!file_exists($filePath)) {
                    \QRcode::png($data, $filePath, 'L', $size, 2);
                }
                return site_url($relativeUrl);
            } catch (\Throwable $e) {
                Logger::error("Failed local QR code generation: " . $e->getMessage());
            }
        }

        // Online API Fallback (returns image URL directly)
        return "https://api.qrserver.com/v1/create-qr-code/?size=250x250&data=" . urlencode($data);
    }
}
