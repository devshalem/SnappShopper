<?php

class Wishlist extends DatabaseObject
{
    // Table name
    static protected $table_name = "Wishlist";

    // Database columns
    static protected $db_columns = [
        'wishlist_id',
        'user_id',
        'product_id',
        'added_date'
    ];

    // Class properties for each column
    public $wishlist_id;
    public $user_id;
    public $product_id;
    public $added_date;

    // Constructor
    public function __construct($args = [])
    {
        $this->wishlist_id = $args['wishlist_id'] ?? null;
        $this->user_id = $args['user_id'] ?? null;
        $this->product_id = $args['product_id'] ?? null;
        $this->added_date = $args['added_date'] ?? null;
    }

    // Save or add item to wishlist
    public function addToWishlist()
    {
        $errors = $this->validate();

        if (!empty($errors)) {
            return ['status' => 'error', 'message' => 'Validation failed', 'errors' => $errors];
        }

        $saveQuery = $this->save();

        return $saveQuery
            ? ['status' => 'success', 'message' => 'Product added to wishlist']
            : ['status' => 'error', 'message' => 'Failed to add product to wishlist'];
    }

    // Remove item from wishlist
    public function removeFromWishlist()
    {
        $sql = "DELETE FROM " . static::$table_name . " WHERE user_id = :user_id AND product_id = :product_id";
        $stmt = self::executeQuery($sql, ['user_id' => $this->user_id, 'product_id' => $this->product_id]);

        return $stmt
            ? ['status' => 'success', 'message' => 'Product removed from wishlist']
            : ['status' => 'error', 'message' => 'Failed to remove product from wishlist'];
    }

    // Check if product is already in the wishlist
    static public function isProductInWishlist($user_id, $product_id)
    {
        $sql = "SELECT * FROM " . static::$table_name . " WHERE user_id = :user_id AND product_id = :product_id";
        $stmt = self::executeQuery($sql, ['user_id' => $user_id, 'product_id' => $product_id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return $result ? true : false;
    }

    // Retrieve all wishlist items for a user
    static public function findWishlistByUserId($user_id)
    {
        $sql = "SELECT * FROM " . static::$table_name . " WHERE user_id = :user_id";
        $stmt = self::executeQuery($sql, ['user_id' => $user_id]);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return array_map([static::class, 'instantiate'], $results);
    }

    // Validation for wishlist item
    protected function validate()
    {
        $this->errors = [];

        if (empty($this->user_id)) {
            $this->errors[] = "User ID is required.";
        }

        if (empty($this->product_id)) {
            $this->errors[] = "Product ID is required.";
        }

        return $this->errors;
    }

    // Helper function to get the user who added the product to the wishlist
    public function getUserDetails()
    {
        return Users::findById($this->user_id);
    }

    // Helper function to get the product in the wishlist
    public function getProductDetails()
    {
        return Products::findById($this->product_id);
    }
}

?>
