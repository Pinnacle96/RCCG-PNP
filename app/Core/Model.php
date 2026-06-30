<?php
/**
 * Base Model
 * Parent class for all models with common PDO CRUD operations.
 */

abstract class Model {
    protected string $table;
    protected string $primaryKey = 'id';

    /**
     * Find a row by primary key
     */
    public function find(int|string $id): ?array {
        $sql = "SELECT * FROM `{$this->table}` WHERE `{$this->primaryKey}` = ? LIMIT 1";
        return Database::fetchOne($sql, [$id]);
    }

    /**
     * Find a row by a column
     */
    public function findBy(string $column, mixed $value): ?array {
        $sql = "SELECT * FROM `{$this->table}` WHERE `{$column}` = ? LIMIT 1";
        return Database::fetchOne($sql, [$value]);
    }

    /**
     * Find all rows, optionally ordered/limited
     */
    public function findAll(string $orderBy = null, int $limit = 0, int $offset = 0): array {
        $sql = "SELECT * FROM `{$this->table}`";
        if ($orderBy) $sql .= " ORDER BY {$orderBy}";
        if ($limit)  $sql .= " LIMIT {$limit} OFFSET {$offset}";
        return Database::fetchAll($sql);
    }

    /**
     * Where clause wrapper
     */
    public function where(array $conditions, string $orderBy = null, int $limit = 0, int $offset = 0): array {
        $sql = "SELECT * FROM `{$this->table}`";
        $params = [];
        if (!empty($conditions)) {
            $clauses = [];
            foreach ($conditions as $col => $val) {
                $clauses[] = "`{$col}` " . (is_array($val) ? '' : '=') . ' ?';
                $params[] = $val;
            }
            $sql .= ' WHERE ' . implode(' AND ', $clauses);
        }
        if ($orderBy) $sql .= " ORDER BY {$orderBy}";
        if ($limit)  $sql .= " LIMIT {$limit} OFFSET {$offset}";
        return Database::fetchAll($sql, $params);
    }

    /**
     * Insert a new row from $data array. Returns last insert id.
     */
    public function create(array $data): string {
        return Database::insert($this->table, $data);
    }

    /**
     * Update a row by primary key
     */
    public function update(int|string $id, array $data): int {
        return Database::update($this->table, $data, "`{$this->primaryKey}` = :where_id", [':where_id' => $id]);
    }

    /**
     * Delete a row by primary key
     */
    public function delete(int|string $id): int {
        return Database::delete($this->table, "`{$this->primaryKey}` = :where_id", [':where_id' => $id]);
    }

    /**
     * Count all records
     */
    public function count(array $conditions = []): int {
        $sql = "SELECT COUNT(*) FROM `{$this->table}`";
        $params = [];
        if (!empty($conditions)) {
            $clauses = [];
            foreach ($conditions as $col => $val) {
                $clauses[] = "`{$col}` = ?";
                $params[] = $val;
            }
            $sql .= ' WHERE ' . implode(' AND ', $clauses);
        }
        return (int) Database::fetchColumn($sql, $params);
    }

    /**
     * Custom raw query (escape hatches)
     */
    protected function query(string $sql, array $params = []): array {
        return Database::fetchAll($sql, $params);
    }

    protected function queryOne(string $sql, array $params = []): ?array {
        return Database::fetchOne($sql, $params);
    }
}
