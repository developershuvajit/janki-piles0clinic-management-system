<?php
declare(strict_types=1);

namespace App\Helpers;

class Backup
{
    /**
     * Create a MySQL dump of the clinic database and stream it as a download.
     * Requires mysqldump to be available in the system PATH.
     *
     * @param string $outputPath Optional path to save the file. If empty, streams to browser.
     * @return bool Returns true on success when saving to file.
     */
    public static function createDump(string $outputPath = ''): bool
    {
        $host   = $_ENV['DB_HOST']     ?? 'localhost';
        $db     = $_ENV['DB_NAME']     ?? 'clinic_db';
        $user   = $_ENV['DB_USER']     ?? 'root';
        $pass   = $_ENV['DB_PASS']     ?? '';
        $port   = $_ENV['DB_PORT']     ?? '3306';

        $filename = 'medclinic_backup_' . date('Y-m-d_H-i-s') . '.sql';

        // Build mysqldump command safely
        $passArg = !empty($pass) ? '-p' . escapeshellarg($pass) : '';
        $cmd = sprintf(
            'mysqldump --host=%s --port=%s --user=%s %s --single-transaction --routines --triggers --skip-lock-tables %s',
            escapeshellarg($host),
            escapeshellarg($port),
            escapeshellarg($user),
            $passArg,
            escapeshellarg($db)
        );

        if ($outputPath !== '') {
            // Save to file
            $cmd .= ' > ' . escapeshellarg($outputPath);
            exec($cmd, $output, $returnCode);
            return $returnCode === 0;
        }

        // Stream to browser as download
        if (!headers_sent()) {
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="' . $filename . '"');
            header('Pragma: no-cache');
            header('Expires: 0');
        }

        passthru($cmd);
        return true;
    }

    /**
     * List available backup files in the backups directory.
     */
    public static function listBackups(): array
    {
        $dir  = ROOT_PATH . '/storage/backups';
        if (!is_dir($dir)) {
            return [];
        }

        $files = glob($dir . '/medclinic_backup_*.sql');
        if (!$files) {
            return [];
        }

        $backups = [];
        foreach ($files as $file) {
            $backups[] = [
                'filename' => basename($file),
                'size'     => filesize($file),
                'size_mb'  => round(filesize($file) / 1024 / 1024, 2),
                'created'  => filemtime($file),
                'created_f' => date('d M Y H:i', filemtime($file)),
            ];
        }

        // Sort newest first
        usort($backups, fn($a, $b) => $b['created'] <=> $a['created']);
        return $backups;
    }

    /**
     * Create a scheduled automatic backup (for cron jobs).
     * Saves to storage/backups, keeping only last 7 files.
     */
    public static function scheduledBackup(): bool
    {
        $dir = ROOT_PATH . '/storage/backups';
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $filename  = $dir . '/medclinic_backup_' . date('Y-m-d_H-i-s') . '.sql';
        $success   = self::createDump($filename);

        if ($success) {
            // Rotate: keep only last 7 backups
            $files = glob($dir . '/medclinic_backup_*.sql');
            if ($files && count($files) > 7) {
                usort($files, fn($a, $b) => filemtime($a) <=> filemtime($b));
                $toDelete = array_slice($files, 0, count($files) - 7);
                foreach ($toDelete as $old) {
                    @unlink($old);
                }
            }
            Logger::info("Scheduled backup created: $filename");
        } else {
            Logger::error("Scheduled backup FAILED for database.");
        }

        return $success;
    }
}
