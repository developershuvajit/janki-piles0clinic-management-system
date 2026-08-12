<?php
declare(strict_types=1);

namespace App\Helpers;

class Upload
{
    private array $allowedExtensions = [
        'jpg',
        'jpeg',
        'png',
        'pdf',
        'doc',
        'docx'
    ];

    private array $allowedMimeTypes = [
        'image/jpeg',
        'image/png',
        'application/pdf',
        'application/msword',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
    ];

    private int $maxSize = 5 * 1024 * 1024; // 5MB

    /**
     * Extensions that are always blocked.
     */
    private const ALWAYS_BLOCKED_EXTENSIONS = [
        'php',
        'php3',
        'php4',
        'php5',
        'phtml',
        'phps',
        'phar',
        'asp',
        'aspx',
        'jsp',
        'py',
        'rb',
        'pl',
        'sh',
        'cgi',
        'svg',
        'htaccess',
        'htpasswd',
        'ini',
        'bat',
        'cmd',
        'exe',
        'msi',
        'dll'
    ];

    public function __construct(array $config = [])
    {
        if (isset($config['allowedExtensions'])) {
            $this->allowedExtensions = array_map(
                'strtolower',
                $config['allowedExtensions']
            );
        }

        if (isset($config['allowedMimeTypes'])) {
            $this->allowedMimeTypes = $config['allowedMimeTypes'];
        }

        if (isset($config['maxSize'])) {
            $this->maxSize = (int) $config['maxSize'];
        }
    }

    /**
     * Safely upload a file.
     *
     * @param array $fileField $_FILES element
     * @param string $subDirectory Subdirectory under assets/uploads
     */
    public function file(
        array $fileField,
        string $subDirectory = ''
    ): array {

        /*
        |--------------------------------------------------------------------------
        | Validate upload parameters
        |--------------------------------------------------------------------------
        */

        if (
            !isset($fileField['error']) ||
            !isset($fileField['tmp_name']) ||
            !isset($fileField['name']) ||
            !isset($fileField['size']) ||
            is_array($fileField['error'])
        ) {
            return [
                'success' => false,
                'error' => 'Invalid file upload parameters.'
            ];
        }

        /*
        |--------------------------------------------------------------------------
        | Upload Error Check
        |--------------------------------------------------------------------------
        */

        switch ($fileField['error']) {

            case UPLOAD_ERR_OK:
                break;

            case UPLOAD_ERR_NO_FILE:
                return [
                    'success' => false,
                    'error' => 'No file was uploaded.'
                ];

            case UPLOAD_ERR_INI_SIZE:
            case UPLOAD_ERR_FORM_SIZE:
                return [
                    'success' => false,
                    'error' => 'File size exceeds maximum upload limit.'
                ];

            default:
                return [
                    'success' => false,
                    'error' => 'An error occurred during file upload.'
                ];
        }

        /*
        |--------------------------------------------------------------------------
        | Verify Uploaded File
        |--------------------------------------------------------------------------
        */

        if (!is_uploaded_file($fileField['tmp_name'])) {

            Logger::warning(
                'Upload blocked - invalid uploaded file: ' .
                $fileField['name']
            );

            return [
                'success' => false,
                'error' => 'Invalid uploaded file.'
            ];
        }

        /*
        |--------------------------------------------------------------------------
        | File Size Validation
        |--------------------------------------------------------------------------
        */

        if ((int) $fileField['size'] > $this->maxSize) {

            return [
                'success' => false,
                'error' =>
                    'File size exceeds allowed maximum (' .
                    round($this->maxSize / 1024 / 1024, 2) .
                    'MB).'
            ];
        }

        /*
        |--------------------------------------------------------------------------
        | Empty File Check
        |--------------------------------------------------------------------------
        */

        if ((int) $fileField['size'] <= 0) {

            return [
                'success' => false,
                'error' => 'Uploaded file is empty.'
            ];
        }

        /*
        |--------------------------------------------------------------------------
        | Original Filename
        |--------------------------------------------------------------------------
        */

        $originalName = basename(
            (string) $fileField['name']
        );

        /*
        |--------------------------------------------------------------------------
        | Double Extension Protection
        |--------------------------------------------------------------------------
        */

        if (Security::hasDoubleExtension($originalName)) {

            Logger::warning(
                'Upload blocked - double extension detected: ' .
                $originalName
            );

            return [
                'success' => false,
                'error' =>
                    'Invalid filename. Double extensions are not permitted.'
            ];
        }

        /*
        |--------------------------------------------------------------------------
        | File Extension
        |--------------------------------------------------------------------------
        */

        $extension = strtolower(
            pathinfo($originalName, PATHINFO_EXTENSION)
        );

        /*
        |--------------------------------------------------------------------------
        | Extension Required
        |--------------------------------------------------------------------------
        */

        if ($extension === '') {

            return [
                'success' => false,
                'error' => 'File extension is missing.'
            ];
        }

        /*
        |--------------------------------------------------------------------------
        | Always Block Dangerous Extensions
        |--------------------------------------------------------------------------
        */

        if (
            in_array(
                $extension,
                self::ALWAYS_BLOCKED_EXTENSIONS,
                true
            )
        ) {

            Logger::warning(
                'Upload blocked - dangerous extension: ' .
                $originalName
            );

            return [
                'success' => false,
                'error' =>
                    'File type not permitted for security reasons.'
            ];
        }

        /*
        |--------------------------------------------------------------------------
        | Allowed Extension Check
        |--------------------------------------------------------------------------
        */

        if (
            !in_array(
                $extension,
                $this->allowedExtensions,
                true
            )
        ) {

            return [
                'success' => false,
                'error' =>
                    'Invalid file extension. Allowed: ' .
                    implode(', ', $this->allowedExtensions)
            ];
        }

        /*
        |--------------------------------------------------------------------------
        | File Content Validation
        |--------------------------------------------------------------------------
        |
        | No finfo/Fileinfo required.
        |
        */

        $validationResult = $this->validateFileContent(
            $fileField['tmp_name'],
            $extension,
            $originalName
        );

        if (!$validationResult['valid']) {

            Logger::warning(
                'Upload blocked - invalid file content: ' .
                $originalName
            );

            return [
                'success' => false,
                'error' => $validationResult['error']
            ];
        }

        /*
        |--------------------------------------------------------------------------
        | Setup Upload Directory
        |--------------------------------------------------------------------------
        */

        $baseUploadDir = PUBLIC_PATH . '/assets/uploads';

        $cleanSubDirectory = trim(
            str_replace('\\', '/', $subDirectory),
            '/'
        );

        /*
        |--------------------------------------------------------------------------
        | Prevent Directory Traversal
        |--------------------------------------------------------------------------
        */

        if (
            $cleanSubDirectory !== '' &&
            (
                str_contains($cleanSubDirectory, '..') ||
                str_contains($cleanSubDirectory, "\0")
            )
        ) {

            Logger::warning(
                'Upload blocked - invalid upload directory: ' .
                $subDirectory
            );

            return [
                'success' => false,
                'error' => 'Invalid upload directory.'
            ];
        }

        /*
        |--------------------------------------------------------------------------
        | Target Directory
        |--------------------------------------------------------------------------
        */

        $targetDir = $baseUploadDir;

        if ($cleanSubDirectory !== '') {
            $targetDir .= '/' . $cleanSubDirectory;
        }

        /*
        |--------------------------------------------------------------------------
        | Create Directory
        |--------------------------------------------------------------------------
        */

        if (!is_dir($targetDir)) {

            if (!mkdir($targetDir, 0755, true)) {

                Logger::error(
                    'Failed to create upload directory: ' .
                    $targetDir
                );

                return [
                    'success' => false,
                    'error' =>
                        'System error: Unable to create upload target folder.'
                ];
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Protect Upload Directory
        |--------------------------------------------------------------------------
        */

        $htaccessPath = $baseUploadDir . '/.htaccess';

        if (!file_exists($htaccessPath)) {

            $htaccessContent =
                "# Disable script execution for security\n" .
                "<FilesMatch \"\\.(php|php3|php4|php5|phtml|phps|phar|pl|py|jsp|asp|aspx|sh|cgi|svg)$\">\n" .
                "    ForceType text/plain\n" .
                "    Deny from all\n" .
                "</FilesMatch>\n" .
                "Options -ExecCGI\n";

            $written = @file_put_contents(
                $htaccessPath,
                $htaccessContent
            );

            if ($written === false) {

                Logger::error(
                    'Unable to write upload directory protection file: ' .
                    $htaccessPath
                );

                return [
                    'success' => false,
                    'error' =>
                        'System error: Unable to secure the upload directory.'
                ];
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Generate Secure Filename
        |--------------------------------------------------------------------------
        */

        try {

            $safeName =
                bin2hex(random_bytes(16)) .
                '.' .
                $extension;

        } catch (\Throwable $e) {

            Logger::error(
                'Unable to generate secure filename: ' .
                $e->getMessage()
            );

            return [
                'success' => false,
                'error' =>
                    'System error: Unable to generate secure filename.'
            ];
        }

        /*
        |--------------------------------------------------------------------------
        | Target File
        |--------------------------------------------------------------------------
        */

        $targetFile =
            $targetDir .
            '/' .
            $safeName;

        /*
        |--------------------------------------------------------------------------
        | Move Uploaded File
        |--------------------------------------------------------------------------
        */

        if (
            !move_uploaded_file(
                $fileField['tmp_name'],
                $targetFile
            )
        ) {

            Logger::error(
                "Failed to move uploaded file from '" .
                $fileField['tmp_name'] .
                "' to '" .
                $targetFile .
                "'"
            );

            return [
                'success' => false,
                'error' =>
                    'System error: Unable to complete file transfer.'
            ];
        }

        /*
        |--------------------------------------------------------------------------
        | Set File Permissions
        |--------------------------------------------------------------------------
        */

        if (!@chmod($targetFile, 0644)) {

            Logger::warning(
                'Unable to set permissions on uploaded file: ' .
                $targetFile
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Relative Path
        |--------------------------------------------------------------------------
        */

        $relativePath =
            'assets/uploads' .
            (
                $cleanSubDirectory !== ''
                    ? '/' . $cleanSubDirectory
                    : ''
            ) .
            '/' .
            $safeName;

        /*
        |--------------------------------------------------------------------------
        | Success Response
        |--------------------------------------------------------------------------
        */

        return [
            'success'  => true,
            'filename' => $originalName,
            'saved_as' => $safeName,
            'path'     => $relativePath,
            'url'      => site_url($relativePath)
        ];
    }

    /**
     * Validate actual file content without Fileinfo extension.
     */
    private function validateFileContent(
        string $tmpFile,
        string $extension,
        string $originalName
    ): array {

        /*
        |--------------------------------------------------------------------------
        | Images
        |--------------------------------------------------------------------------
        */

        if (
            in_array(
                $extension,
                ['jpg', 'jpeg', 'png'],
                true
            )
        ) {

            $imageInfo = @getimagesize($tmpFile);

            if ($imageInfo === false) {

                return [
                    'valid' => false,
                    'error' => 'Invalid image file.'
                ];
            }

            /*
            | Check actual image MIME
            */

            $actualMime = $imageInfo['mime'] ?? '';

            $allowedImageMimes = [
                'image/jpeg',
                'image/png'
            ];

            if (
                !in_array(
                    $actualMime,
                    $allowedImageMimes,
                    true
                )
            ) {

                return [
                    'valid' => false,
                    'error' => 'Invalid image content type.'
                ];
            }

            /*
            | JPEG extension must contain JPEG
            */

            if (
                in_array(
                    $extension,
                    ['jpg', 'jpeg'],
                    true
                ) &&
                $actualMime !== 'image/jpeg'
            ) {

                return [
                    'valid' => false,
                    'error' => 'The file is not a valid JPEG image.'
                ];
            }

            /*
            | PNG extension must contain PNG
            */

            if (
                $extension === 'png' &&
                $actualMime !== 'image/png'
            ) {

                return [
                    'valid' => false,
                    'error' => 'The file is not a valid PNG image.'
                ];
            }

            return [
                'valid' => true,
                'mime' => $actualMime
            ];
        }

        /*
        |--------------------------------------------------------------------------
        | PDF
        |--------------------------------------------------------------------------
        */

        if ($extension === 'pdf') {

            $handle = @fopen($tmpFile, 'rb');

            if ($handle === false) {

                return [
                    'valid' => false,
                    'error' => 'Unable to read PDF file.'
                ];
            }

            $header = fread($handle, 5);

            fclose($handle);

            if ($header !== '%PDF-') {

                return [
                    'valid' => false,
                    'error' => 'Invalid PDF file.'
                ];
            }

            return [
                'valid' => true,
                'mime' => 'application/pdf'
            ];
        }

        /*
        |--------------------------------------------------------------------------
        | DOC
        |--------------------------------------------------------------------------
        */

        if ($extension === 'doc') {

            $handle = @fopen($tmpFile, 'rb');

            if ($handle === false) {

                return [
                    'valid' => false,
                    'error' => 'Unable to read DOC file.'
                ];
            }

            $header = fread($handle, 8);

            fclose($handle);

            /*
            | Old Microsoft Office DOC files
            | usually start with OLE Compound File signature:
            |
            | D0 CF 11 E0 A1 B1 1A E1
            */

            $expectedHeader =
                "\xD0\xCF\x11\xE0\xA1\xB1\x1A\xE1";

            if (
                substr(
                    $header,
                    0,
                    8
                ) !== $expectedHeader
            ) {

                return [
                    'valid' => false,
                    'error' => 'Invalid DOC file.'
                ];
            }

            return [
                'valid' => true,
                'mime' => 'application/msword'
            ];
        }

        /*
        |--------------------------------------------------------------------------
        | DOCX
        |--------------------------------------------------------------------------
        */

        if ($extension === 'docx') {

            $handle = @fopen($tmpFile, 'rb');

            if ($handle === false) {

                return [
                    'valid' => false,
                    'error' => 'Unable to read DOCX file.'
                ];
            }

            $header = fread($handle, 4);

            fclose($handle);

            /*
            | DOCX is a ZIP archive.
            | ZIP files start with:
            |
            | PK
            */

            if (
                substr(
                    $header,
                    0,
                    2
                ) !== 'PK'
            ) {

                return [
                    'valid' => false,
                    'error' => 'Invalid DOCX file.'
                ];
            }

            /*
            | If ZipArchive exists, verify that the archive
            | actually contains a Word document structure.
            */

            if (class_exists('\ZipArchive')) {

                $zip = new \ZipArchive();

                if (
                    $zip->open($tmpFile) !== true
                ) {

                    return [
                        'valid' => false,
                        'error' => 'Invalid DOCX archive.'
                    ];
                }

                $hasContentTypes =
                    $zip->locateName(
                        '[Content_Types].xml'
                    ) !== false;

                $hasWordDocument =
                    $zip->locateName(
                        'word/document.xml'
                    ) !== false;

                $zip->close();

                if (
                    !$hasContentTypes ||
                    !$hasWordDocument
                ) {

                    return [
                        'valid' => false,
                        'error' => 'Invalid Word document.'
                    ];
                }
            }

            return [
                'valid' => true,
                'mime' =>
                    'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
            ];
        }

        /*
        |--------------------------------------------------------------------------
        | Unknown File
        |--------------------------------------------------------------------------
        */

        return [
            'valid' => false,
            'error' => 'Unable to validate file content.'
        ];
    }
}