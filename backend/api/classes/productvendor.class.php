<?php

class ProductVendor extends DatabaseObject
{
    // Table name
    static protected $table_name = "Product_Vendor";

    // Database columns
    static protected $db_columns = [
        'product_vendor_id',
        'product_id',
        'vendor_id',
        'created_at'
    ];

    // Class properties for each column
    public $product_vendor_id;
    public $product_id;
    public $vendor_id;
    public $created_at;

    // Constructor
    public function __construct($args = [])
    {
        $this->product_vendor_id = $args['product_vendor_id'] ?? null;
        $this->product_id = $args['product_id'] ?? null;
        $this->vendor_id = $args['vendor_id'] ?? null;
        $this->created_at = $args['created_at'] ?? null;
    }

    // Save the product-vendor relationship to the database
    public function saveProductVendor()
    {
        // Validate the product-vendor data
        $errors = $this->validate();

        if (!empty($errors)) {
            return ['status' => 'error', 'message' => 'Validation failed', 'errors' => $errors];
        }

        $saveQuery = $this->save();

        return $saveQuery
            ? ['status' => 'success', 'message' => 'Product-Vendor relationship saved successfully']
            : ['status' => 'error', 'message' => 'Failed to save product-vendor relationship'];
    }

    // Validate the product-vendor data
    public function validate()
    {
        $errors = [];

        if (empty($this->product_id)) {
            $errors[] = "Product ID cannot be empty.";
        }

        if (empty($this->vendor_id)) {
            $errors[] = "Vendor ID cannot be empty.";
        }

        return $errors;
    }

    // Find a product-vendor relationship by product_id
    public static function findByProductId($product_id)
    {
        $sql = "SELECT * FROM " . self::$table_name . " WHERE product_id = :product_id";
        $stmt = self::executeQuery($sql, ['product_id' => $product_id]);

        return $stmt ? new self($stmt[0]) : null;
    }

    // Find a product-vendor relationship by vendor_id
    public static function findByVendorId($vendor_id)
    {
        $sql = "SELECT * FROM " . self::$table_name . " WHERE vendor_id = :vendor_id";
        $stmt = self::executeQuery($sql, ['vendor_id' => $vendor_id]);

        return $stmt ? new self($stmt[0]) : null;
    }

    // Delete a product-vendor relationship by product_vendor_id
    public static function deleteById($product_vendor_id)
    {
        $sql = "DELETE FROM " . self::$table_name . " WHERE product_vendor_id = :product_vendor_id";
        return self::executeQuery($sql, ['product_vendor_id' => $product_vendor_id]);
    }

    // Update the product-vendor relationship
    public function updateProductVendor()
    {
        $errors = $this->validate();

        if (!empty($errors)) {
            return ['status' => 'error', 'message' => 'Validation failed', 'errors' => $errors];
        }

        $updateQuery = $this->update();

        return $updateQuery
            ? ['status' => 'success', 'message' => 'Product-Vendor relationship updated successfully']
            : ['status' => 'error', 'message' => 'Failed to update product-vendor relationship'];
    }
}

?>
