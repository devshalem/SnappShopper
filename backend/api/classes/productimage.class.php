<?php

class ProductImage extends DatabaseObject
{
    // Table name
    static protected $table_name = "Product_Images";

    // Database columns
    static protected $db_columns = [
        'image_id',
        'product_id',
        'image_url',
        'alt_text',
        'created_at'
    ];

    // Class properties for each column
    public $image_id;
    public $product_id;
    public $image_url;
    public $alt_text;
    public $created_at;

    // Constructor
    public function __construct($args = [])
    {
        $this->image_id = $args['image_id'] ?? null;
        $this->product_id = $args['product_id'] ?? null;
        $this->image_url = $args['image_url'] ?? '';
        $this->alt_text = $args['alt_text'] ?? '';
        $this->created_at = $args['created_at'] ?? null;
    }

    // Save or update an image
    public function saveImage()
    {
        $errors = $this->validate();

        if (!empty($errors)) {
            return ['status' => 'error', 'message' => 'Validation failed', 'errors' => $errors];
        }

        $saveQuery = $this->save();

        return $saveQuery
            ? ['status' => 'success', 'message' => 'Image saved successfully']
            : ['status' => 'error', 'message' => 'Failed to save image'];
    }

    // Retrieve all images for a product
    static public function findImagesByProduct($product_id)
    {
        $sql = "SELECT * FROM " . static::$table_name . " WHERE product_id = :product_id";
        $stmt = self::executeQuery($sql, ['product_id' => $product_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Delete an image
    static public function deleteImage($image_id)
    {
        $sql = "DELETE FROM " . static::$table_name . " WHERE image_id = :image_id";
        $stmt = self::executeQuery($sql, ['image_id' => $image_id]);
        return $stmt->rowCount() > 0
            ? ['status' => 'success', 'message' => 'Image deleted successfully']
            : ['status' => 'error', 'message' => 'Failed to delete image'];
    }

    // Validation for image fields
    protected function validate()
    {
        $this->errors = [];

        if (empty($this->product_id) || !Products::findById($this->product_id)) {
            $this->errors[] = "Invalid product ID.";
        }

        if ($this->is_blank($this->image_url)) {
            $this->errors[] = "Image URL cannot be blank.";
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
