<?php

class RecentlyViewedItem extends DatabaseObject
{
    // Table name
    static protected $table_name = "Recently_Viewed_Items";

    // Database columns
    static protected $db_columns = [
        'view_id',
        'user_id',
        'product_id',
        'viewed_at'
    ];

    // Class properties for each column
    public $view_id;
    public $user_id;
    public $product_id;
    public $viewed_at;

    // Constructor
    public function __construct($args = [])
    {
        $this->view_id = $args['view_id'] ?? null;
        $this->user_id = $args['user_id'] ?? 0;
        $this->product_id = $args['product_id'] ?? 0;
        $this->viewed_at = $args['viewed_at'] ?? null;
    }

    // Save the recently viewed item to the database
    public function saveRecentlyViewedItem()
    {
        // Validate the recently viewed item data
        $errors = $this->validate();

        if (!empty($errors)) {
            return ['status' => 'error', 'message' => 'Validation failed', 'errors' => $errors];
        }

        $saveQuery = $this->save();

        return $saveQuery
            ? ['status' => 'success', 'message' => 'Recently viewed item saved successfully']
            : ['status' => 'error', 'message' => 'Failed to save recently viewed item'];
    }

    // Validate the recently viewed item data
    public function validate()
    {
        $errors = [];

        if (empty($this->user_id)) {
            $errors[] = "User ID cannot be empty.";
        }

        if (empty($this->product_id)) {
            $errors[] = "Product ID cannot be empty.";
        }

        return $errors;
    }

    // Find all recently viewed items by user ID
    public static function findByUserId($user_id)
    {
        $sql = "SELECT * FROM " . self::$table_name . " WHERE user_id = :user_id ORDER BY viewed_at DESC";
        $stmt = self::executeQuery($sql, ['user_id' => $user_id]);

        $recently_viewed_items = [];
        foreach ($stmt as $row) {
            $recently_viewed_items[] = new self($row);
        }

        return $recently_viewed_items;
    }

    // Find a specific recently viewed item by user ID and product ID
    public static function findByUserAndProduct($user_id, $product_id)
    {
        $sql = "SELECT * FROM " . self::$table_name . " WHERE user_id = :user_id AND product_id = :product_id LIMIT 1";
        $stmt = self::executeQuery($sql, ['user_id' => $user_id, 'product_id' => $product_id]);

        return $stmt ? new self($stmt[0]) : null;
    }

    // Delete a recently viewed item by user ID and product ID
    public static function deleteByUserAndProduct($user_id, $product_id)
    {
        $sql = "DELETE FROM " . self::$table_name . " WHERE user_id = :user_id AND product_id = :product_id";
        return self::executeQuery($sql, ['user_id' => $user_id, 'product_id' => $product_id]);
    }
}

?>
