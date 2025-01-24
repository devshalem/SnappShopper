<?php

class Shipping extends DatabaseObject
{
    // Table name
    static protected $table_name = "Shipping";

    // Database columns
    static protected $db_columns = [
        'shipping_id',
        'order_id',
        'address_id',
        'shipping_date',
        'shipping_status',
        'tracking_number',
        'created_at'
    ];

    // Class properties for each column
    public $shipping_id;
    public $order_id;
    public $address_id;
    public $shipping_date;
    public $shipping_status;
    public $tracking_number;
    public $created_at;

    // Constructor
    public function __construct($args = [])
    {
        $this->shipping_id = $args['shipping_id'] ?? null;
        $this->order_id = $args['order_id'] ?? null;
        $this->address_id = $args['address_id'] ?? null;
        $this->shipping_date = $args['shipping_date'] ?? null;
        $this->shipping_status = $args['shipping_status'] ?? 'Pending';
        $this->tracking_number = $args['tracking_number'] ?? '';
        $this->created_at = $args['created_at'] ?? null;
    }

    // Save or update shipping information
    public function saveShipping()
    {
        $errors = $this->validate();

        if (!empty($errors)) {
            return ['status' => 'error', 'message' => 'Validation failed', 'errors' => $errors];
        }

        $saveQuery = $this->save();

        return $saveQuery
            ? ['status' => 'success', 'message' => 'Shipping processed successfully']
            : ['status' => 'error', 'message' => 'Failed to process shipping'];
    }

    // Retrieve shipping information for a specific order
    static public function findShippingByOrderId($order_id)
    {
        $sql = "SELECT * FROM " . static::$table_name . " WHERE order_id = :order_id";
        $stmt = self::executeQuery($sql, ['order_id' => $order_id]);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return array_map([static::class, 'instantiate'], $results);
    }

    // Retrieve shipping information by shipping ID
    static public function findShippingById($shipping_id)
    {
        return self::findById($shipping_id);
    }

    // Update shipping status
    public function updateShippingStatus($status)
    {
        $validStatuses = ['Pending', 'Shipped', 'Delivered'];

        if (!in_array($status, $validStatuses)) {
            return ['status' => 'error', 'message' => 'Invalid shipping status'];
        }

        $this->shipping_status = $status;
        return $this->saveShipping();
    }

    // Validation for shipping fields
    protected function validate()
    {
        $this->errors = [];

        if (empty($this->order_id)) {
            $this->errors[] = "Order ID is required.";
        }

        if (empty($this->address_id)) {
            $this->errors[] = "Address ID is required.";
        }

        if (empty($this->shipping_status)) {
            $this->errors[] = "Shipping status is required.";
        } elseif (!in_array($this->shipping_status, ['Pending', 'Shipped', 'Delivered'])) {
            $this->errors[] = "Shipping status must be one of: Pending, Shipped, Delivered.";
        }

        return $this->errors;
    }

    // Helper function to retrieve address information for shipping
    public function getAddressDetails()
    {
        return Address::findAddressesByUserId($this->address_id);
    }
}

?>
