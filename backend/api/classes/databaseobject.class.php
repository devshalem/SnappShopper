<?php

class DatabaseObject
{
    protected $errors = [];
    protected static $database; // PDO instance

    // Set the database connection
    public static function setDatabase($pdo)
    {
        self::$database = $pdo;
    }

    // Execute a query with prepared statements
    static protected function executeQuery($sql, $params = [])
    {
        $stmt = self::$database->prepare($sql);
        if (!$stmt->execute($params)) {
            error_log("Database query failed: " . implode(", ", $stmt->errorInfo()));
            return false;
        }
        return $stmt;
    }


    // Fetch records from a SQL query
    public static function findBySql(string $sql, array $params = []): array
    {
        $stmt = self::executeQuery($sql, $params);
        return array_map([static::class, 'instantiate'], $stmt->fetchAll(PDO::FETCH_ASSOC));
    }

    // Fetch all records
    public static function findAll(): array
    {
        $sql = "SELECT * FROM " . static::$table_name;
        return static::findBySql($sql);
    }

    // Count all records
    public static function countAll(): int
    {
        $sql = "SELECT COUNT(*) AS count FROM " . static::$table_name;
        $stmt = self::executeQuery($sql);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return (int)($result['count'] ?? 0);
    }

    // Save the current record (create)
    public function save(): bool
    {
        return $this->create();
    }

    // Create a new record
    protected function create(): bool
    {
        $attributes = $this->sanitizedAttributes();
        $columns = array_keys($attributes);
        $placeholders = array_map(fn($col) => ":$col", $columns);

        $sql = "INSERT INTO " . static::$table_name . " (" . implode(', ', $columns) . ")";
        $sql .= " VALUES (" . implode(', ', $placeholders) . ")";

        $stmt = self::executeQuery($sql, $attributes);

        if ($stmt) {
            $this->id = self::$database->lastInsertId();
            return true;
        }
        return false;
    }

    // Delete a record by ID
    public static function delete(int $id): bool
    {
        $sql = "DELETE FROM " . static::$table_name . " WHERE id = :id LIMIT 1";
        return (bool)self::executeQuery($sql, ['id' => $id]);
    }

    // Merge attributes from an array
    public function mergeAttributes(array $args = []): void
    {
        foreach ($args as $key => $value) {
            if (property_exists($this, $key) && !is_null($value)) {
                $this->$key = $value;
            }
        }
    }

    // Get object attributes (excluding ID)
    public function attributes()
    {
        $attributes = [];
        foreach (static::$db_columns as $column) {
            if ($column === 'id') continue; // Skip ID for inserts
            $attributes[$column] = $this->$column;
        }
        return $attributes;
    }


    // Sanitize attributes before saving
    protected function sanitizedAttributes()
    {
        $sanitized = [];
        foreach ($this->attributes() as $key => $value) {
            $sanitized[$key] = htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
        }
        return $sanitized;
    }


    // Instantiate an object from a record
    protected static function instantiate(array $record): static
    {
        $object = new static();
        foreach ($record as $property => $value) {
            if (property_exists($object, $property)) {
                $object->$property = $value;
            }
        }
        return $object;
    }
}
