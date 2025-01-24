<?php

class Inventory extends DatabaseObject
{
    // Table name
    static protected $table_name = "Inventory";

    // Database columns
    static protected $db_columns = [
        'inventory_id',
        'product_id',
        'quantity_available',
        'quantity_sold',
        'last_updated'
    ];

    // Class properties for each column
    public $inventory_id;
    public $product_id;
    public $quantity_available;
    public $quantity_sold;
    public $last_updated;

    // Constructor
    public function __construct($args = [])
    {
        $this->inventory_id = $args['inventory_id'] ?? null;
        $this->product_id = $args['product_id'] ?? null;
        $this->quantity_available = $args['quantity_available'] ?? 0;
        $this->quantity_sold = $args['quantity_sold'] ?? 0;
        $this->last_updated = $args['last_updated'] ?? null;
    }

    // Add new inventory or update existing inventory
    public function saveInventory()
    {
        $errors = $this->validate();

        if (!empty($errors)) {
            return ['status' => 'error', 'message' => 'Validation failed', 'errors' => $errors];
        }

        $saveQuery = $this->save();

        return $saveQuery
            ? ['status' => 'success', 'message' => 'Inventory saved successfully']
            : ['status' => 'error', 'message' => 'Failed to save inventory'];
    }

    // Retrieve inventory for a product
    static public function findByProductId($product_id)
    {
        $sql = "SELECT * FROM " . static::$table_name . " WHERE product_id = :product_id";
        $stmt = self::executeQuery($sql, ['product_id' => $product_id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return $result ? static::instantiate($result) : null;
    }

    // Adjust quantity available
    public function adjustQuantity($quantity_change)
    {
        $this->quantity_available += $quantity_change;

        if ($this->quantity_available < 0) {
            $this->quantity_available = 0; // Ensure quantity doesn't go negative
        }

        return $this->save();
    }

    // Increment quantity sold
    public function incrementQuantitySold($quantity)
    {
        $this->quantity_sold += $quantity;
        return $this->save();
    }

    // Validation for inventory fields
    protected function validate()
    {
        $this->errors = [];

        if (empty($this->product_id) || !Products::findById($this->product_id)) {
            $this->errors[] = "Invalid product ID.";
        }

        if (!is_numeric($this->quantity_available) || $this->quantity_available < 0) {
            $this->errors[] = "Quantity available must be a non-negative number.";
        }

        if (!is_numeric($this->quantity_sold) || $this->quantity_sold < 0) {
            $this->errors[] = "Quantity sold must be a non-negative number.";
        }

        return $this->errors;
    }

    // Retrieve all inventory records
    static public function allInventory()
    {
        return self::findAll();
    }

    // Helper functions
    private function is_blank($value): bool
    {
        return !isset($value) || trim($value) === '';
    }
}

?>
