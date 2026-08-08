<?php
declare(strict_types=1);

// Define Base Directory Constants
define('ROOT_PATH', dirname(__DIR__));
define('APP_PATH', ROOT_PATH . '/app');
define('VIEWS_PATH', ROOT_PATH . '/views');
define('PUBLIC_PATH', ROOT_PATH . '/public');
define('LOGS_PATH', ROOT_PATH . '/logs');

// Load environment variables from .env file
if (file_exists(ROOT_PATH . '/.env')) {
    $lines = file(ROOT_PATH . '/.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        $line = trim($line);
        // Skip comments and empty lines
        if ($line === '' || strpos($line, '#') === 0) {
            continue;
        }
        
        // Handle lines with no value or comments inline
        if (strpos($line, '=') !== false) {
            list($name, $value) = explode('=', $line, 2);
            $name = trim($name);
            $value = trim($value);
            
            // Remove surrounding quotes if any
            if (preg_match('/^"([^"]*)"$/', $value, $matches) || preg_match('/^\'([^\']*)\'$/', $value, $matches)) {
                $value = $matches[1];
            } else {
                // Split by hash if comment is inline
                $valParts = explode('#', $value, 2);
                $value = trim($valParts[0]);
            }

            if (!array_key_exists($name, $_SERVER) && !array_key_exists($name, $_ENV)) {
                putenv("{$name}={$value}");
                $_ENV[$name] = $value;
                $_SERVER[$name] = $value;
            }
        }
    }
}

// Set Application Environment Configurations
$appEnv = $_ENV['APP_ENV'] ?? 'production';
if ($appEnv === 'development') {
    ini_set('display_errors', '1');
    ini_set('display_startup_errors', '1');
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', '0');
    ini_set('display_startup_errors', '0');
    error_reporting(E_ALL); // Let PHP capture errors but handle them gracefully
}

// Set Timezone
date_default_timezone_set('Asia/Kolkata');

// Register custom global exception handler
set_exception_handler(function (\Throwable $exception) {
    if (class_exists('App\Helpers\Logger')) {
        \App\Helpers\Logger::error("Unhandled Exception: " . $exception->getMessage(), [
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
            'trace' => $exception->getTraceAsString()
        ]);
    } else {
        error_log($exception->getMessage());
    }
    
    // Display error view
    $appEnv = $_ENV['APP_ENV'] ?? 'production';
    if ($appEnv === 'development') {
        echo "<h1>Fatal Error</h1>";
        echo "<p><strong>Message:</strong> " . htmlspecialchars($exception->getMessage()) . "</p>";
        echo "<p><strong>File:</strong> " . htmlspecialchars($exception->getFile()) . " on line " . $exception->getLine() . "</p>";
        echo "<h3>Stack Trace:</h3><pre>" . htmlspecialchars($exception->getTraceAsString()) . "</pre>";
    } else {
        http_response_code(500);
        $errorPage = VIEWS_PATH . '/errors/500.php';
        if (file_exists($errorPage)) {
            include $errorPage;
        } else {
            echo "An internal server error occurred. Please contact the administrator.";
        }
    }
    exit();
});

// Register custom global error handler to convert notices/warnings into exceptions
set_error_handler(function (int $errno, string $errstr, string $errfile, int $errline) {
    if (!(error_reporting() & $errno)) {
        // This error code is not included in error_reporting
        return false;
    }

    // Ignore deprecation warnings
    if ($errno === E_DEPRECATED || $errno === E_USER_DEPRECATED || $errno === E_STRICT) {
        return false;
    }

    // Don't throw exceptions for third-party vendor code notices/warnings, just log them
    if (strpos($errfile, DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR) !== false) {
        if (class_exists('App\Helpers\Logger')) {
            \App\Helpers\Logger::warning("Vendor Warning [{$errno}]: {$errstr} in {$errfile} on line {$errline}");
        }
        return false;
    }
    
    throw new \ErrorException($errstr, 0, $errno, $errfile, $errline);
});

// Autoloader function for PSR-4 namespace mapping
spl_autoload_register(function ($class) {
    $prefix = 'App\\';
    $base_dir = APP_PATH . '/';
    $len = strlen($prefix);
    
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }
    
    $relative_class = substr($class, $len);
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';
    
    if (file_exists($file)) {
        require_once $file;
    }
});

// Require procedural helpers
if (file_exists(APP_PATH . '/Helpers/functions.php')) {
    require_once APP_PATH . '/Helpers/functions.php';
}

// Initialize session parameters, checking remember cookies and inactivity timeouts
if (class_exists('App\Helpers\Session')) {
    \App\Helpers\Session::init();
}
