<?php
/**
 * Database Connection Singleton Class
 * PDO wrapper for secure database access
 */

class Database {
    private static ?PDO $pdo = null;
    private static array $config = [];

    /**
     * Get PDO connection (lazy-loaded)
     */
    public static function pdo(): PDO {
        if (self::$pdo === null) {
            self::$config = [
                'host' => DB_HOST,
                'name' => DB_NAME,
                'user' => DB_USER,
                'pass' => DB_PASS,
                'charset' => DB_CHARSET,
            ];

            $config = self::$config;
            $dsn = "mysql:host={$config['host']};dbname={$config['name']};charset={$config['charset']}";

            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ];

            try {
                self::$pdo = new PDO($dsn, $config['user'], $config['pass'], $options);
            } catch (PDOException $e) {
                throw new RuntimeException('Database connection failed: ' . $e->getMessage(), 500, $e);
            }
        }

        return self::$pdo;
    }

    /**
     * Prepare SQL statement
     */
    public static function prepare(string $sql): PDOStatement {
        return self::pdo()->prepare($sql);
    }

    /**
     * Execute a query with parameters and return PDOStatement
     */
    public static function execute(string $sql, array $params = []): PDOStatement {
        $stmt = self::pdo()->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }

    /**
     * Fetch one row as associative array
     */
    public static function fetchOne(string $sql, array $params = []): ?array {
        $stmt = self::execute($sql, $params);
        $row = $stmt->fetch();
        return $row !== false ? $row : null;
    }

    /**
     * Fetch all rows as associative arrays
     */
    public static function fetchAll(string $sql, array $params = []): array {
        $stmt = self::execute($sql, $params);
        return $stmt->fetchAll();
    }

    /**
     * Fetch a single column value
     */
    public static function fetchColumn(string $sql, array $params = [], int $col = 0): mixed {
        $stmt = self::execute($sql, $params);
        return $stmt->fetchColumn($col);
    }

    /**
     * Insert row and return last insert ID
     */
    public static function insert(string $table, array $data): string {
        $cols = array_keys($data);
        $placeholders = array_map(fn($c) => ':' . $c, $cols);
        $sql = 'INSERT INTO `' . $table . '` (`' . implode('`, `', $cols) . '`) VALUES (' . implode(', ', $placeholders) . ')';
        $params = [];
        foreach ($data as $key => $value) {
            $params[':' . $key] = $value;
        }
        self::execute($sql, $params);
        return self::pdo()->lastInsertId();
    }

    /**
     * Update row(s)
     */
    public static function update(string $table, array $data, string $where, array $whereParams = []): int {
        $sets = [];
        $params = [];
        foreach ($data as $key => $value) {
            $sets[] = "`{$key}` = :set_{$key}";
            $params[':set_' . $key] = $value;
        }
        $sql = 'UPDATE `' . $table . '` SET ' . implode(', ', $sets) . ' WHERE ' . $where;
        foreach ($whereParams as $key => $value) {
            $params[':' . ltrim($key, ':')] = $value;
        }
        // Rename where param placeholders to avoid clashes
        $cleanedParams = [];
        foreach ($params as $k => $v) {
            $cleanedParams[$k] = $v;
        }
        $stmt = self::execute($sql, $cleanedParams);
        return $stmt->rowCount();
    }

    /**
     * Delete row(s)
     */
    public static function delete(string $table, string $where, array $params = []): int {
        $sql = 'DELETE FROM `' . $table . '` WHERE ' . $where;
        $stmt = self::execute($sql, $params);
        return $stmt->rowCount();
    }

    /**
     * Begin transaction
     */
    public static function begin(): void {
        self::pdo()->beginTransaction();
    }

    /**
     * Commit transaction
     */
    public static function commit(): void {
        self::pdo()->commit();
    }

    /**
     * Roll back transaction
     */
    public static function rollback(): void {
        self::pdo()->rollBack();
    }

    /**
     * Run within a transaction (auto-rollback on exception)
     */
    public static function transaction(callable $callback): mixed {
        self::begin();
        try {
            $result = $callback();
            self::commit();
            return $result;
        } catch (\Throwable $e) {
            self::rollback();
            throw $e;
        }
    }
}
