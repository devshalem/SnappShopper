<?php

class Discount extends DatabaseObject
{
    // Table name
    static protected $table_name = "Discounts";

    // Database columns
    static protected $db_columns = [
        'discount_id',
        'code',
        'discount_percentage',
        'start_date',
        'end_date',
        'usage_limit'
    ];

    // Class properties for each column
    public $discount_id;
    public $code;
    public $discount_percentage;
    public $start_date;
    public $end_date;
    public $usage_limit;

    // Constructor
    public function __construct($args = [])
    {
        $this->discount_id = $args['discount_id'] ?? null;
        $this->code = $args['code'] ?? '';
        $this->discount_percentage = $args['discount_percentage'] ?? 0.00;
        $this->start_date = $args['start_date'] ?? null;
        $this->end_date = $args['end_date'] ?? null;
        $this->usage_limit = $args['usage_limit'] ?? 0;
    }

    // Save the discount to the database
    public function saveDiscount()
    {
        // Validate the discount data
        $errors = $this->validate();

        if (!empty($errors)) {
            return ['status' => 'error', 'message' => 'Validation failed', 'errors' => $errors];
        }

        $saveQuery = $this->save();

        return $saveQuery
            ? ['status' => 'success', 'message' => 'Discount saved successfully']
            : ['status' => 'error', 'message' => 'Failed to save discount'];
    }

    // Validate the discount data
    public function validate()
    {
        $errors = [];

        if (empty($this->code)) {
            $errors[] = "Discount code cannot be empty.";
        }
        if ($this->discount_percentage < 0 || $this->discount_percentage > 100) {
            $errors[] = "Discount percentage must be between 0 and 100.";
        }
        if (empty($this->start_date)) {
            $errors[] = "Start date cannot be empty.";
        }
        if (empty($this->end_date)) {
            $errors[] = "End date cannot be empty.";
        }

        return $errors;
    }

    // Check if the discount code is valid and not expired
    public static function isValidDiscount($code)
    {
        $sql = "SELECT * FROM " . self::$table_name . " WHERE code = :code AND CURRENT_DATE BETWEEN start_date AND end_date AND usage_limit > 0 LIMIT 1";
        $stmt = self::executeQuery($sql, ['code' => $code]);

        return $stmt ? new self($stmt[0]) : null;
    }

    // Update the usage limit after a discount is used
    public function decrementUsageLimit()
    {
        $sql = "UPDATE " . self::$table_name . " SET usage_limit = usage_limit - 1 WHERE discount_id = :discount_id";
        return self::executeQuery($sql, ['discount_id' => $this->discount_id]);
    }

    // Retrieve all active discounts
    public static function findAllActiveDiscounts()
    {
        $sql = "SELECT * FROM " . self::$table_name . " WHERE CURRENT_DATE BETWEEN start_date AND end_date AND usage_limit > 0 ORDER BY start_date ASC";
        $stmt = self::executeQuery($sql);

        $discounts = [];
        foreach ($stmt as $row) {
            $discounts[] = new self($row);
        }

        return $discounts;
    }

    // Retrieve a specific discount by code
    public static function findDiscountByCode($code)
    {
        $sql = "SELECT * FROM " . self::$table_name . " WHERE code = :code LIMIT 1";
        $stmt = self::executeQuery($sql, ['code' => $code]);

        return $stmt ? new self($stmt[0]) : null;
    }
}

?>
