<?php

class Products extends DatabaseObject
{
    // Table name
    static protected $table_name = "Products";

    // Database columns
    static protected $db_columns = [
        'product_id',
        'name',
        'description',
        'price',
        'category_id',
        'stock',
        'created_at',
        'updated_at'
    ];

    // Class properties for each column
    public $product_id;
    public $name;
    public $description;
    public $price;
    public $category_id;
    public $stock;
    public $created_at;
    public $updated_at;

    // Constructor
    public function __construct($args = [])
    {
        $this->product_id = $args['product_id'] ?? null;
        $this->name = $args['name'] ?? '';
        $this->description = $args['description'] ?? '';
        $this->price = $args['price'] ?? 0.00;
        $this->category_id = $args['category_id'] ?? null;
        $this->stock = $args['stock'] ?? 0;
        $this->created_at = $args['created_at'] ?? null;
        $this->updated_at = $args['updated_at'] ?? null;
    }

    // Create or update a product
    public function saveProduct()
    {
        $errors = $this->validate();

        if (!empty($errors)) {
            return ['status' => 'error', 'message' => 'Validation failed', 'errors' => $errors];
        }

        $saveQuery = $this->save();

        return $saveQuery
            ? ['status' => 'success', 'message' => 'Product saved successfully']
            : ['status' => 'error', 'message' => 'Failed to save product'];
    }

    // Find a product by ID
    static public function findProductById($id)
    {
        return self::findById($id);
    }

    // Retrieve all products
    static public function allProducts()
    {
        return self::findAll();
    }

    // Retrieve products by category
    static public function findProductsByCategory($category_id)
    {
        $sql = "SELECT * FROM " . static::$table_name . " WHERE category_id = :category_id";
        $stmt = self::executeQuery($sql, ['category_id' => $category_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Update product stock
    static public function updateStock($product_id, $new_stock)
    {
        $product = self::findById($product_id);
        if (!$product) {
            return ['status' => 'error', 'message' => 'Product not found'];
        }

        $product->stock = $new_stock;

        if ($product->save()) {
            return ['status' => 'success', 'message' => 'Stock updated successfully'];
        }

        return ['status' => 'error', 'message' => 'Failed to update stock'];
    }

    // Validation for product fields
    protected function validate()
    {
        $this->errors = [];

        if ($this->is_blank($this->name)) {
            $this->errors[] = "Product name cannot be blank.";
        }

        if ($this->price <= 0) {
            $this->errors[] = "Price must be greater than zero.";
        }

        if (!is_null($this->category_id) && !Categories::findById($this->category_id)) {
            $this->errors[] = "Invalid category ID.";
        }

        if ($this->stock < 0) {
            $this->errors[] = "Stock cannot be negative.";
        }

        return $this->errors;
    }

    // Helper functions
    private function is_blank($value)
    {
        return !isset($value) || trim($value) === '';
    }
}

?>
