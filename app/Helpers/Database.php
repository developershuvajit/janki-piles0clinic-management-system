<?php
declare(strict_types=1);

namespace App\Helpers;

use PDO;
use PDOException;

class Database
{
    private static ?PDO $pdoInstance = null;
    private static ?self $instance = null;
    private PDO $pdo;

    /**
     * Private constructor for singleton pattern
     */
    private function __construct()
    {
        $host = $_ENV['DB_HOST'] ?? '127.0.0.1';
        $port = $_ENV['DB_PORT'] ?? '3306';
        $db   = $_ENV['DB_NAME'] ?? 'clinic_db';
        $user = $_ENV['DB_USER'] ?? 'root';
        $pass = $_ENV['DB_PASS'] ?? '';
        $charset = 'utf8mb4';

        $dsn = "mysql:host={$host};port={$port};dbname={$db};charset={$charset}";
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
            PDO::ATTR_PERSISTENT         => false,
        ];

        try {
            $this->pdo = new PDO($dsn, $user, $pass, $options);
        } catch (PDOException $e) {
            error_log("Database Connection Failed: " . $e->getMessage());
            throw new PDOException("Database connection error. Please contact support.", (int)$e->getCode());
        }
    }

    /**
     * Get singleton instance of Database helper
     */
    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Get PDO connection (for backward compatibility)
     */
    public static function getConnection(): PDO
    {
        return self::getInstance()->getPdo();
    }

    /**
     * Get PDO instance
     */
    public function getPdo(): PDO
    {
        return $this->pdo;
    }

    /**
     * Prepare and execute an SQL statement
     */
    public static function query(string $sql, array $params = []): \PDOStatement
    {
        $stmt = self::getConnection()->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }

    /**
     * Fetch a single value from query (returns first column of first row)
     */
    public function getOne(string $sql, array $params = [])
    {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? reset($result) : null;
    }

    /**
     * Fetch a single row as associative array
     */
    public function getRow(string $sql, array $params = []): ?array
    {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    /**
     * Fetch all rows as associative array
     */
    public function getAll(string $sql, array $params = []): array
    {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Execute an SQL statement (INSERT, UPDATE, DELETE)
     * Returns number of affected rows
     */
    public function execute(string $sql, array $params = []): int
    {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->rowCount();
    }

    /**
     * Get last inserted ID
     */
    public function lastInsertId(): int
    {
        return (int) $this->pdo->lastInsertId();
    }

    /**
     * Begin a transaction
     */
    public function beginTransaction(): bool
    {
        return $this->pdo->beginTransaction();
    }

    /**
     * Commit a transaction
     */
    public function commit(): bool
    {
        return $this->pdo->commit();
    }

    /**
     * Rollback a transaction
     */
    public function rollBack(): bool
    {
        return $this->pdo->rollBack();
    }

    /**
     * Check if inside a transaction
     */
    public function inTransaction(): bool
    {
        return $this->pdo->inTransaction();
    }

    // ============================================================
    // STATIC METHODS FOR BACKWARD COMPATIBILITY
    // ============================================================

    /**
     * Static: Fetch a single row
     */
    public static function row(string $sql, array $params = []): ?array
    {
        return self::getInstance()->getRow($sql, $params);
    }

    /**
     * Static: Fetch a single value (first column of first row)
     */
    public static function value(string $sql, array $params = [])
    {
        return self::getInstance()->getOne($sql, $params);
    }

    /**
     * Static: Fetch all rows
     */
    public static function all(string $sql, array $params = []): array
    {
        return self::getInstance()->getAll($sql, $params);
    }

    /**
     * Static: Execute query
     */
    public static function exec(string $sql, array $params = []): int
    {
        return self::getInstance()->execute($sql, $params);
    }

    /**
     * Static: Get last inserted ID - FIXED
     */
    public static function lastInsertId(): int
    {
        return self::getInstance()->lastInsertId();
    }

    // ============================================================
    // BRANCH FILTER HELPER METHODS
    // ============================================================

    /**
     * Get branch filter conditions based on user role
     * Returns array with filter SQL and parameters
     */
    public static function getBranchFilter(): array
    {
        if (!class_exists('App\Helpers\Session')) {
            return [
                'isBranchAdmin' => false,
                'isSuperAdmin' => true,
                'branchId' => null,
                'hasFilter' => false,
                'sql' => '',
                'params' => []
            ];
        }
        
        $user = Session::user();
        $roleSlug = $user['role_slug'] ?? $user['role'] ?? '';
        $branchId = $user['branch_id'] ?? null;
        
        $isBranchAdmin = ($roleSlug === 'branch_admin');
        $isSuperAdmin = ($roleSlug === 'super_admin' || $roleSlug === 'admin');
        
        $result = [
            'isBranchAdmin' => $isBranchAdmin,
            'isSuperAdmin' => $isSuperAdmin,
            'branchId' => $branchId,
            'hasFilter' => ($isBranchAdmin && $branchId),
            'sql' => '',
            'params' => []
        ];
        
        if ($isBranchAdmin && $branchId) {
            $result['sql'] = ' AND branch_id = ? ';
            $result['params'][] = $branchId;
        }
        
        return $result;
    }

    /**
     * Build a WHERE clause with branch filter
     */
    public static function buildBranchWhere(string $tableAlias = '', bool $addWhere = true): array
    {
        $filter = self::getBranchFilter();
        $prefix = $tableAlias ? $tableAlias . '.' : '';
        
        if ($filter['hasFilter']) {
            $sql = ($addWhere ? ' WHERE ' : '') . $prefix . 'branch_id = ?';
            return [
                'sql' => $sql,
                'params' => [$filter['branchId']],
                'hasFilter' => true
            ];
        }
        
        return [
            'sql' => '',
            'params' => [],
            'hasFilter' => false
        ];
    }

    /**
     * Apply branch filter to an existing query
     */
    public static function applyBranchFilter(string $sql, array $params = []): array
    {
        $filter = self::getBranchFilter();
        
        if ($filter['hasFilter']) {
            if (stripos($sql, 'WHERE') !== false) {
                $sql .= ' AND branch_id = ?';
            } else {
                $sql .= ' WHERE branch_id = ?';
            }
            $params[] = $filter['branchId'];
        }
        
        return ['sql' => $sql, 'params' => $params];
    }

    /**
     * Execute a query with automatic branch filtering
     */
    public static function queryWithBranch(string $sql, array $params = [], string $tableAlias = ''): array
    {
        $result = self::applyBranchFilter($sql, $params);
        $stmt = self::query($result['sql'], $result['params']);
        return $stmt->fetchAll();
    }

    /**
     * Get a single row with branch filtering
     */
    public static function rowWithBranch(string $sql, array $params = [], string $tableAlias = ''): ?array
    {
        $result = self::applyBranchFilter($sql, $params);
        $stmt = self::query($result['sql'], $result['params']);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    /**
     * Get a single value with branch filtering
     */
    public static function valueWithBranch(string $sql, array $params = [], string $tableAlias = '')
    {
        $result = self::applyBranchFilter($sql, $params);
        $stmt = self::query($result['sql'], $result['params']);
        $row = $stmt->fetch(PDO::FETCH_NUM);
        return $row ? $row[0] : null;
    }
}