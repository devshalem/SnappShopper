<?php

class Order_Item extends DatabaseObject
{
    // Table name
    static protected $table_name = "Order_Items";

    // Database columns
    static protected $db_columns = [
        'order_item_id',
        'order_id',
        'product_id',
        'quantity',
        'price_at_purchase',
        'created_at'
    ];

    // Class properties for each column
    public $order_item_id;
    public $order_id;
    public $product_id;
    public $quantity;
    public $price_at_purchase;
    public $created_at;

    // Constructor
    public function __construct($args = [])
    {
        $this->order_item_id = $args['order_item_id'] ?? null;
        $this->order_id = $args['order_id'] ?? null;
        $this->product_id = $args['product_id'] ?? null;
        $this->quantity = $args['quantity'] ?? 0;
        $this->price_at_purchase = $args['price_at_purchase'] ?? 0.00;
        $this->created_at = $args['created_at'] ?? null;
    }

    // Create or update an order item
    public function saveOrderItem()
    {
        $errors = $this->validate();

        if (!empty($errors)) {
            return ['status' => 'error', 'message' => 'Validation failed', 'errors' => $errors];
        }

        $saveQuery = $this->save();

        return $saveQuery
            ? ['status' => 'success', 'message' => 'Order item saved successfully']
            : ['status' => 'error', 'message' => 'Failed to save order item'];
    }

    // Retrieve all order items for a specific order
    static public function findOrderItemsByOrderId($order_id)
    {
        $sql = "SELECT * FROM " . static::$table_name . " WHERE order_id = :order_id";
        $stmt = self::executeQuery($sql, ['order_id' => $order_id]);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return array_map([static::class, 'instantiate'], $results);
    }

    // Retrieve a specific order item by its ID
    static public function findOrderItemById($order_item_id)
    {
        return self::findById($order_item_id);
    }

    // Validation for order item fields
    protected function validate()
    {
        $this->errors = [];

        if (empty($this->order_id)) {
            $this->errors[] = "Order ID is required.";
        }

        if (empty($this->product_id)) {
            $this->errors[] = "Product ID is required.";
        }

        if (empty($this->quantity) || $this->quantity <= 0) {
            $this->errors[] = "Quantity must be a positive integer.";
        }

        if (empty($this->price_at_purchase) || $this->price_at_purchase <= 0) {
            $this->errors[] = "Price at purchase must be a positive amount.";
        }

        return $this->errors;
    }

    // Calculate the total price for this order item
    public function calculateTotalPrice()
    {
        return $this->quantity * $this->price_at_purchase;
    }
}

?>
