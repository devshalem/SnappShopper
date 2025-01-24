<?php

class Orders extends DatabaseObject
{
    // Table name
    static protected $table_name = "Orders";

    // Database columns
    static protected $db_columns = [
        'order_id',
        'user_id',
        'order_date',
        'status',
        'total_amount',
        'created_at',
        'updated_at'
    ];

    // Class properties for each column
    public $order_id;
    public $user_id;
    public $order_date;
    public $status;
    public $total_amount;
    public $created_at;
    public $updated_at;

    // Constructor
    public function __construct($args = [])
    {
        $this->order_id = $args['order_id'] ?? null;
        $this->user_id = $args['user_id'] ?? null;
        $this->order_date = $args['order_date'] ?? null;
        $this->status = $args['status'] ?? 'Pending';
        $this->total_amount = $args['total_amount'] ?? 0.00;
        $this->created_at = $args['created_at'] ?? null;
        $this->updated_at = $args['updated_at'] ?? null;
    }

    // Create or update an order
    public function saveOrder()
    {
        $errors = $this->validate();

        if (!empty($errors)) {
            return ['status' => 'error', 'message' => 'Validation failed', 'errors' => $errors];
        }

        $saveQuery = $this->save();

        return $saveQuery
            ? ['status' => 'success', 'message' => 'Order saved successfully']
            : ['status' => 'error', 'message' => 'Failed to save order'];
    }

    // Retrieve all orders
    static public function allOrders()
    {
        return self::findAll();
    }

    // Retrieve orders by user ID
    static public function findOrdersByUserId($user_id)
    {
        $sql = "SELECT * FROM " . static::$table_name . " WHERE user_id = :user_id";
        $stmt = self::executeQuery($sql, ['user_id' => $user_id]);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return array_map([static::class, 'instantiate'], $results);
    }

    // Retrieve order by ID
    static public function findOrderById($order_id)
    {
        return self::findById($order_id);
    }

    // Update order status
    public function updateStatus($new_status)
    {
        $valid_statuses = ['Pending', 'Shipped', 'Delivered', 'Cancelled'];

        if (!in_array($new_status, $valid_statuses)) {
            return ['status' => 'error', 'message' => 'Invalid order status'];
        }

        $this->status = $new_status;

        if ($this->save()) {
            return ['status' => 'success', 'message' => 'Order status updated successfully'];
        }

        return ['status' => 'error', 'message' => 'Failed to update order status'];
    }

    // Validation for order fields
    protected function validate()
    {
        $this->errors = [];

        if (empty($this->user_id)) {
            $this->errors[] = "User ID is required.";
        }

        if (empty($this->total_amount) || $this->total_amount <= 0) {
            $this->errors[] = "Total amount must be greater than 0.";
        }

        return $this->errors;
    }

    // Retrieve pending orders
    static public function findPendingOrders()
    {
        $sql = "SELECT * FROM " . static::$table_name . " WHERE status = 'Pending'";
        $stmt = self::executeQuery($sql);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return array_map([static::class, 'instantiate'], $results);
    }

    // Retrieve orders within a date range
    static public function findOrdersByDateRange($start_date, $end_date)
    {
        $sql = "SELECT * FROM " . static::$table_name . " WHERE order_date BETWEEN :start_date AND :end_date";
        $stmt = self::executeQuery($sql, ['start_date' => $start_date, 'end_date' => $end_date]);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return array_map([static::class, 'instantiate'], $results);
    }
}

?>
