<?php

class Reviews extends DatabaseObject
{
    // Table name
    static protected $table_name = "Reviews";

    // Database columns
    static protected $db_columns = [
        'review_id',
        'user_id',
        'product_id',
        'rating',
        'review_text',
        'created_at',
        'updated_at'
    ];

    // Class properties for each column
    public $review_id;
    public $user_id;
    public $product_id;
    public $rating;
    public $review_text;
    public $created_at;
    public $updated_at;

    // Constructor
    public function __construct($args = [])
    {
        $this->review_id = $args['review_id'] ?? null;
        $this->user_id = $args['user_id'] ?? null;
        $this->product_id = $args['product_id'] ?? null;
        $this->rating = $args['rating'] ?? null;
        $this->review_text = $args['review_text'] ?? '';
        $this->created_at = $args['created_at'] ?? null;
        $this->updated_at = $args['updated_at'] ?? null;
    }

    // Save or update the review
    public function saveReview()
    {
        $errors = $this->validate();

        if (!empty($errors)) {
            return ['status' => 'error', 'message' => 'Validation failed', 'errors' => $errors];
        }

        $saveQuery = $this->save();

        return $saveQuery
            ? ['status' => 'success', 'message' => 'Review saved successfully']
            : ['status' => 'error', 'message' => 'Failed to save review'];
    }

    // Retrieve all reviews for a specific product
    static public function findReviewsByProductId($product_id)
    {
        $sql = "SELECT * FROM " . static::$table_name . " WHERE product_id = :product_id";
        $stmt = self::executeQuery($sql, ['product_id' => $product_id]);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return array_map([static::class, 'instantiate'], $results);
    }

    // Retrieve reviews by a specific user
    static public function findReviewsByUserId($user_id)
    {
        $sql = "SELECT * FROM " . static::$table_name . " WHERE user_id = :user_id";
        $stmt = self::executeQuery($sql, ['user_id' => $user_id]);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return array_map([static::class, 'instantiate'], $results);
    }

    // Update review text or rating
    public function updateReview($rating, $review_text)
    {
        $this->rating = $rating;
        $this->review_text = $review_text;
        return $this->saveReview();
    }

    // Validation for review fields
    protected function validate()
    {
        $this->errors = [];

        if (empty($this->user_id)) {
            $this->errors[] = "User ID is required.";
        }

        if (empty($this->product_id)) {
            $this->errors[] = "Product ID is required.";
        }

        if (!isset($this->rating) || $this->rating < 1 || $this->rating > 5) {
            $this->errors[] = "Rating must be between 1 and 5.";
        }

        return $this->errors;
    }

    // Helper function to get the user who left the review
    public function getUserDetails()
    {
        return Users::findUserById($this->user_id);
    }

    // Helper function to get the product being reviewed
    public function getProductDetails()
    {
        return Products::findProductById($this->product_id);
    }
}

?>
