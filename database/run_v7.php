<?php
declare(strict_types=1);

require_once __DIR__ . '/../config/config.php';

use App\Helpers\Database;

try {
    echo "Executing Migration v7 for Reception & OPD Operations Module...\n";
    
    $sql = file_get_contents(__DIR__ . '/migrations_v7.sql');
    
    $pdo = Database::getConnection();
    $pdo->exec($sql);

    echo "Migration v7 executed successfully!\n";
} catch (\Throwable $e) {
    echo "Migration error: " . $e->getMessage() . "\n";
}
