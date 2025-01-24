<?php

class AuditLog extends DatabaseObject
{
    // Table name
    static protected $table_name = "Audit_Logs";

    // Database columns
    static protected $db_columns = [
        'log_id',
        'user_id',
        'action',
        'action_date',
        'description'
    ];

    // Class properties for each column
    public $log_id;
    public $user_id;
    public $action;
    public $action_date;
    public $description;

    // Constructor
    public function __construct($args = [])
    {
        $this->log_id = $args['log_id'] ?? null;
        $this->user_id = $args['user_id'] ?? null;
        $this->action = $args['action'] ?? '';
        $this->action_date = $args['action_date'] ?? null;
        $this->description = $args['description'] ?? '';
    }

    // Save the audit log entry to the database
    public function saveAuditLog()
    {
        // Validate the log data
        $errors = $this->validate();

        if (!empty($errors)) {
            return ['status' => 'error', 'message' => 'Validation failed', 'errors' => $errors];
        }

        $saveQuery = $this->save();

        return $saveQuery
            ? ['status' => 'success', 'message' => 'Audit log saved successfully']
            : ['status' => 'error', 'message' => 'Failed to save audit log'];
    }

    // Validate the log data
    public function validate()
    {
        $errors = [];

        if (empty($this->user_id)) {
            $errors[] = "User ID cannot be empty.";
        }
        if (empty($this->action)) {
            $errors[] = "Action cannot be empty.";
        }
        if (empty($this->description)) {
            $errors[] = "Description cannot be empty.";
        }

        return $errors;
    }

    // Retrieve audit logs by user ID
    public static function findLogsByUserId($user_id)
    {
        $sql = "SELECT * FROM " . self::$table_name . " WHERE user_id = :user_id ORDER BY action_date DESC";
        $stmt = self::executeQuery($sql, ['user_id' => $user_id]);

        $logs = [];
        foreach ($stmt as $row) {
            $logs[] = new self($row);
        }

        return $logs;
    }

    // Retrieve a specific audit log entry by log ID
    public static function findLogById($log_id)
    {
        $sql = "SELECT * FROM " . self::$table_name . " WHERE log_id = :log_id LIMIT 1";
        $stmt = self::executeQuery($sql, ['log_id' => $log_id]);

        return $stmt ? new self($stmt[0]) : null;
    }

    // Retrieve all audit logs (for admin purposes)
    public static function findAllLogs()
    {
        $sql = "SELECT * FROM " . self::$table_name . " ORDER BY action_date DESC";
        $stmt = self::executeQuery($sql);

        $logs = [];
        foreach ($stmt as $row) {
            $logs[] = new self($row);
        }

        return $logs;
    }
}

?>
