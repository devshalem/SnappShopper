<?php

class SubCategory extends DatabaseObject
{
    // Table name
    static protected $table_name = "Sub_Category";

    // Database columns
    static protected $db_columns = [
        'sub_category_id',
        'name',
        'category_id',
        'created_at',
        'updated_at'
    ];

    // Class properties for each column
    public $sub_category_id;
    public $name;
    public $category_id;
    public $created_at;
    public $updated_at;

    // Constructor
    public function __construct($args = [])
    {
        $this->sub_category_id = $args['sub_category_id'] ?? null;
        $this->name = $args['name'] ?? '';
        $this->category_id = $args['category_id'] ?? 0;
        $this->created_at = $args['created_at'] ?? null;
        $this->updated_at = $args['updated_at'] ?? null;
    }

    // Save the subcategory to the database
    public function saveSubCategory()
    {
        // Validate the subcategory data
        $errors = $this->validate();

        if (!empty($errors)) {
            return ['status' => 'error', 'message' => 'Validation failed', 'errors' => $errors];
        }

        $saveQuery = $this->save();

        return $saveQuery
            ? ['status' => 'success', 'message' => 'Sub-category saved successfully']
            : ['status' => 'error', 'message' => 'Failed to save sub-category'];
    }

    // Validate the subcategory data
    public function validate()
    {
        $errors = [];

        if (empty($this->name)) {
            $errors[] = "Sub-category name cannot be empty.";
        }
        if (empty($this->category_id)) {
            $errors[] = "Category ID cannot be empty.";
        }

        return $errors;
    }

    // Find all subcategories by category ID
    public static function findByCategoryId($category_id)
    {
        $sql = "SELECT * FROM " . self::$table_name . " WHERE category_id = :category_id";
        $stmt = self::executeQuery($sql, ['category_id' => $category_id]);

        $sub_categories = [];
        foreach ($stmt as $row) {
            $sub_categories[] = new self($row);
        }

        return $sub_categories;
    }

    // Retrieve a specific subcategory by ID
    public static function findSubById($sub_category_id)
    {
        $sql = "SELECT * FROM " . self::$table_name . " WHERE sub_category_id = :sub_category_id LIMIT 1";
        $stmt = self::executeQuery($sql, ['sub_category_id' => $sub_category_id]);

        return $stmt ? new self($stmt[0]) : null;
    }

    // Update the subcategory name
    public function updateName($new_name)
    {
        $this->name = $new_name;

        $sql = "UPDATE " . self::$table_name . " SET name = :name WHERE sub_category_id = :sub_category_id";
        return self::executeQuery($sql, ['name' => $this->name, 'sub_category_id' => $this->sub_category_id]);
    }
}

?>
