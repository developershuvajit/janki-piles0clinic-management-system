<?php
declare(strict_types=1);

namespace App\Helpers;

class Upload
{
    private array $allowedExtensions = ['jpg', 'jpeg', 'png', 'pdf', 'doc', 'docx'];
    private array $allowedMimeTypes = [
        'image/jpeg',
        'image/jpg',
        'image/png',
        'application/pdf',
        'application/msword',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
    ];
    private int $maxSize = 5 * 1024 * 1024; // Default: 5MB

    // Extensions that are always blocked regardless of config override
    private const ALWAYS_BLOCKED_EXTENSIONS = [
        'php', 'php3', 'php4', 'php5', 'phtml', 'phps', 'phar',
        'asp', 'aspx', 'jsp', 'py', 'rb', 'pl', 'sh', 'cgi', 'svg',
        'htaccess', 'htpasswd', 'ini', 'bat', 'cmd', 'exe', 'msi', 'dll'
    ];

    public function __construct(array $config = [])
    {
        if (isset($config['allowedExtensions'])) {
            $this->allowedExtensions = $config['allowedExtensions'];
        }
        if (isset($config['allowedMimeTypes'])) {
            $this->allowedMimeTypes = $config['allowedMimeTypes'];
        }
        if (isset($config['maxSize'])) {
            $this->maxSize = $config['maxSize'];
        }
    }

    /**
     * Safely upload a file.
     *
     * @param array $fileField Superglobal $_FILES element, e.g. $_FILES['document']
     * @param string $subDirectory Subdirectory folder under assets/uploads
     * @return array Response payload with status details
     */
    public function file(array $fileField, string $subDirectory = ''): array
    {
        if (!isset($fileField['error']) || is_array($fileField['error'])) {
            return ['success' => false, 'error' => 'Invalid file upload parameters.'];
        }

        switch ($fileField['error']) {
            case UPLOAD_ERR_OK:
                break;
            case UPLOAD_ERR_NO_FILE:
                return ['success' => false, 'error' => 'No file was uploaded.'];
            case UPLOAD_ERR_INI_SIZE:
            case UPLOAD_ERR_FORM_SIZE:
                return ['success' => false, 'error' => 'File size exceeds maximum upload limit.'];
            default:
                return ['success' => false, 'error' => 'An error occurred during file upload.'];
        }

        // Validate File Size
        if ($fileField['size'] > $this->maxSize) {
            return ['success' => false, 'error' => 'File size exceeds allowed maximum (' . round($this->maxSize / 1024 / 1024, 2) . 'MB).'];
        }

        // --- Security: Detect double-extension attacks (e.g., shell.php.jpg) ---
        if (Security::hasDoubleExtension($fileField['name'])) {
            Logger::warning("Upload blocked - double extension detected: " . $fileField['name']);
            return ['success' => false, 'error' => 'Invalid filename. Double extensions are not permitted.'];
        }

        // Validate File Extension
        $extension = strtolower(pathinfo($fileField['name'], PATHINFO_EXTENSION));

        // Always block dangerous extensions regardless of config
        if (in_array($extension, self::ALWAYS_BLOCKED_EXTENSIONS, true)) {
            Logger::warning("Upload blocked - dangerous extension: " . $fileField['name']);
            return ['success' => false, 'error' => 'File type not permitted for security reasons.'];
        }

        if (!in_array($extension, $this->allowedExtensions, true)) {
            return ['success' => false, 'error' => 'Invalid file extension. Allowed: ' . implode(', ', $this->allowedExtensions)];
        }

        // Verify MIME Type (actual file content, not just the name)
        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $mime = $finfo->file($fileField['tmp_name']);
        if (!in_array($mime, $this->allowedMimeTypes, true)) {
            Logger::warning("Upload blocked - MIME mismatch: {$mime} for file {$fileField['name']}");
            return ['success' => false, 'error' => 'Invalid file content type. The file content does not match the declared extension.'];
        }

        // Setup Paths
        $baseUploadDir = PUBLIC_PATH . '/assets/uploads';
        $targetDir = $baseUploadDir . ($subDirectory !== '' ? '/' . trim($subDirectory, '/') : '');

        if (!is_dir($targetDir)) {
            if (!mkdir($targetDir, 0755, true)) {
                Logger::error("Failed to create upload directory: " . $targetDir);
                return ['success' => false, 'error' => 'System error: Unable to create upload target folder.'];
            }
        }

        // Drop .htaccess to disable script execution in uploads directory
        $htaccessPath = $baseUploadDir . '/.htaccess';
        if (!file_exists($htaccessPath)) {
            $htaccessContent = "# Disable script execution for security\n"
                . "<Files ~ \"\\.(php|php3|php4|php5|phtml|phps|phar|pl|py|jsp|asp|sh|cgi|svg)$\">\n"
                . "    ForceType text/plain\n"
                . "    Deny from all\n"
                . "</Files>\n"
                . "Options -ExecCGI\n";
            file_put_contents($htaccessPath, $htaccessContent);
        }

        // Generate unique cryptographically secure filename (no original name preserved)
        $safeName = bin2hex(random_bytes(16)) . '.' . $extension;
        $targetFile = $targetDir . '/' . $safeName;

        if (!move_uploaded_file($fileField['tmp_name'], $targetFile)) {
            Logger::error("Failed to move uploaded file from '{$fileField['tmp_name']}' to '{$targetFile}'");
            return ['success' => false, 'error' => 'System error: Unable to complete file transfer.'];
        }

        $relativePath = 'assets/uploads' . ($subDirectory !== '' ? '/' . trim($subDirectory, '/') : '') . '/' . $safeName;

        return [
            'success'  => true,
            'filename' => $fileField['name'],
            'saved_as' => $safeName,
            'path'     => $relativePath,
            'url'      => site_url($relativePath)
        ];
    }
}
