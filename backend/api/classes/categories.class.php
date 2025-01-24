<?php

class Categories extends DatabaseObject
{
    // Table name
    static protected $table_name = "Categories";

    // Database columns
    static protected $db_columns = [
        'category_id',
        'name',
        'parent_category_id',
        'created_at',
        'updated_at'
    ];

    // Class properties for each column
    public $category_id;
    public $name;
    public $parent_category_id;
    public $created_at;
    public $updated_at;

    // Constructor
    public function __construct($args = [])
    {
        $this->category_id = $args['category_id'] ?? null;
        $this->name = $args['name'] ?? '';
        $this->parent_category_id = $args['parent_category_id'] ?? null;
        $this->created_at = $args['created_at'] ?? null;
        $this->updated_at = $args['updated_at'] ?? null;
    }

    // Create or update a category
    public function saveCategory()
    {
        $errors = $this->validate();

        if (!empty($errors)) {
            return ['status' => 'error', 'message' => 'Validation failed', 'errors' => $errors];
        }

        $saveQuery = $this->save();

        return $saveQuery
            ? ['status' => 'success', 'message' => 'Category saved successfully']
            : ['status' => 'error', 'message' => 'Failed to save category'];
    }

    // Retrieve all categories
    static public function allCategories()
    {
        return self::findAll();
    }

    // Find category by ID
    static public function findCategoryById($category_id)
    {
        return self::findById($category_id);
    }

    // Find subcategories of a category
    static public function findSubcategories($parent_category_id)
    {
        $sql = "SELECT * FROM " . static::$table_name . " WHERE parent_category_id = :parent_category_id";
        $stmt = self::executeQuery($sql, ['parent_category_id' => $parent_category_id]);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return array_map([static::class, 'instantiate'], $results);
    }

    // Get parent category
    public function getParentCategory()
    {
        if ($this->parent_category_id) {
            return self::findById($this->parent_category_id);
        }
        return null;
    }

    // Validation for category fields
    protected function validate()
    {
        $this->errors = [];

        if ($this->is_blank($this->name)) {
            $this->errors[] = "Name cannot be blank.";
        } elseif (strlen($this->name) > 100) {
            $this->errors[] = "Name cannot exceed 100 characters.";
        }

        if ($this->parent_category_id && !self::findById($this->parent_category_id)) {
            $this->errors[] = "Invalid parent category ID.";
        }

        return $this->errors;
    }

    // Retrieve all parent categories
    static public function findParentCategories()
    {
        $sql = "SELECT * FROM " . static::$table_name . " WHERE parent_category_id IS NULL";
        $stmt = self::executeQuery($sql);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return array_map([static::class, 'instantiate'], $results);
    }

    // Helper functions
    private function is_blank($value)
    {
        return !isset($value) || trim($value) === '';
    }
}

?>
