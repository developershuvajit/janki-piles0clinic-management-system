<?php
declare(strict_types=1);

namespace App\Helpers;

use PDO;
use PDOException;

class Database
{
    private static ?PDO $instance = null;

    /**
     * Get the PDO database connection singleton.
     */
    public static function getConnection(): PDO
    {
        if (self::$instance === null) {
            $host = $_ENV['DB_HOST'] ?? '127.0.0.1';
            $port = $_ENV['DB_PORT'] ?? '8889';
            $db   = $_ENV['DB_NAME'] ?? 'clinic_db';
            $user = $_ENV['DB_USER'] ?? 'root';
            $pass = $_ENV['DB_PASS'] ?? 'root';
            $charset = 'utf8mb4';

            $dsn = "mysql:host={$host};port={$port};dbname={$db};charset={$charset}";
            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ];

            try {
                self::$instance = new PDO($dsn, $user, $pass, $options);
            } catch (PDOException $e) {
                Logger::error("Database Connection Failed: " . $e->getMessage(), [
                    'host' => $host,
                    'db' => $db
                ]);
                throw new PDOException("Database connection error. Please contact administrative support.", (int)$e->getCode());
            }
        }

        return self::$instance;
    }

    /**
     * Prepare and execute an SQL statement.
     */
    public static function query(string $sql, array $params = []): \PDOStatement
    {
        $stmt = self::getConnection()->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }

    /**
     * Fetch a single row matching query.
     */
    public static function row(string $sql, array $params = []): ?array
    {
        $stmt = self::query($sql, $params);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    /**
     * Fetch all rows matching query.
     */
    public static function all(string $sql, array $params = []): array
    {
        return self::query($sql, $params)->fetchAll();
    }

    /**
     * Execute an SQL statement (insert/update/delete).
     * Returns true when the statement ran; a failing statement raises PDOException.
     */
    public static function execute(string $sql, array $params = []): bool
    {
        self::query($sql, $params);
        return true;
    }

    /**
     * Return the last inserted ID.
     */
    public static function lastInsertId(): string
    {
        return self::getConnection()->lastInsertId();
    }

    /**
     * Begin transaction.
     */
    public static function beginTransaction(): bool
    {
        return self::getConnection()->beginTransaction();
    }

    /**
     * Commit transaction.
     */
    public static function commit(): bool
    {
        return self::getConnection()->commit();
    }

    /**
     * Rollback transaction.
     */
    public static function rollBack(): bool
    {
        return self::getConnection()->rollBack();
    }

    /**
     * Check whether a transaction is currently active.
     */
    public static function inTransaction(): bool
    {
        return self::getConnection()->inTransaction();
    }

    /**
     * Rollback only when a transaction is active, so that failing inside a catch
     * block never masks the original exception with "no active transaction".
     */
    public static function rollBackIfActive(): bool
    {
        try {
            if (!self::inTransaction()) {
                return false;
            }
            return self::rollBack();
        } catch (\Throwable $e) {
            Logger::error("Transaction rollback failed: " . $e->getMessage());
            return false;
        }
    }
}
