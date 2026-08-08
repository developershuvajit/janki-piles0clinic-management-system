<?php
require_once __DIR__ . '/../config/config.php';

try {
    $sql = file_get_contents(__DIR__ . '/migrations_v6.sql');
    // Split by semicolons for multiple queries
    $queries = array_filter(array_map('trim', explode(';', $sql)));
    foreach ($queries as $q) {
        if (!empty($q)) {
            \App\Helpers\Database::execute($q);
        }
    }
    echo "Migration v6 applied successfully!\n";
} catch (\Throwable $e) {
    echo "Migration error: " . $e->getMessage() . "\n";
}
