<?php

class Notification extends DatabaseObject
{
    // Table name
    static protected $table_name = "Notifications";

    // Database columns
    static protected $db_columns = [
        'notification_id',
        'user_id',
        'message',
        'status',
        'created_at'
    ];

    // Class properties for each column
    public $notification_id;
    public $user_id;
    public $message;
    public $status = 'Unseen';
    public $created_at;

    // Constructor
    public function __construct($args = [])
    {
        $this->notification_id = $args['notification_id'] ?? null;
        $this->user_id = $args['user_id'] ?? null;
        $this->message = $args['message'] ?? '';
        $this->status = $args['status'] ?? 'Unseen';
        $this->created_at = $args['created_at'] ?? null;
    }

    // Save the notification to the database
    public function saveNotification()
    {
        // Validate the message and user_id
        $errors = $this->validate();

        if (!empty($errors)) {
            return ['status' => 'error', 'message' => 'Validation failed', 'errors' => $errors];
        }

        $saveQuery = $this->save();

        return $saveQuery
            ? ['status' => 'success', 'message' => 'Notification saved successfully']
            : ['status' => 'error', 'message' => 'Failed to save notification'];
    }

    // Validate the notification data
    public function validate()
    {
        $errors = [];

        if (empty($this->user_id)) {
            $errors[] = "User ID cannot be empty.";
        }
        if (empty($this->message)) {
            $errors[] = "Message cannot be empty.";
        }

        return $errors;
    }

    // Mark notification as seen
    public function markAsSeen()
    {
        $this->status = 'Seen';
        $this->save(); // Update the status in the database
    }

    // Retrieve notifications by user_id
    public static function findNotificationsByUserId($user_id)
    {
        $sql = "SELECT * FROM " . self::$table_name . " WHERE user_id = :user_id ORDER BY created_at DESC";
        $stmt = self::executeQuery($sql, ['user_id' => $user_id]);

        $notifications = [];
        foreach ($stmt as $row) {
            $notifications[] = new self($row);
        }

        return $notifications;
    }

    // Mark all notifications as seen for a user
    public static function markAllAsSeen($user_id)
    {
        $sql = "UPDATE " . self::$table_name . " SET status = 'Seen' WHERE user_id = :user_id";
        $stmt = self::executeQuery($sql, ['user_id' => $user_id]);

        return $stmt ? ['status' => 'success', 'message' => 'All notifications marked as seen'] : ['status' => 'error', 'message' => 'Failed to mark notifications as seen'];
    }
}

?>
