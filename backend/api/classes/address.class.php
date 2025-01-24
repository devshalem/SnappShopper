<?php

class Address extends DatabaseObject
{
    // Table name
    static protected $table_name = "Addresses";

    // Database columns
    static protected $db_columns = [
        'address_id',
        'user_id',
        'address_line1',
        'address_line2',
        'city',
        'state',
        'zip_code',
        'country',
        'is_default',
        'created_at'
    ];

    // Class properties for each column
    public $address_id;
    public $user_id;
    public $address_line1;
    public $address_line2;
    public $city;
    public $state;
    public $zip_code;
    public $country;
    public $is_default = false;
    public $created_at;

    // Constructor
    public function __construct($args = [])
    {
        $this->address_id = $args['address_id'] ?? null;
        $this->user_id = $args['user_id'] ?? null;
        $this->address_line1 = $args['address_line1'] ?? '';
        $this->address_line2 = $args['address_line2'] ?? '';
        $this->city = $args['city'] ?? '';
        $this->state = $args['state'] ?? '';
        $this->zip_code = $args['zip_code'] ?? '';
        $this->country = $args['country'] ?? '';
        $this->is_default = $args['is_default'] ?? false;
        $this->created_at = $args['created_at'] ?? null;
    }

    // Save the address to the database
    public function saveAddress()
    {
        // Validate the address data
        $errors = $this->validate();

        if (!empty($errors)) {
            return ['status' => 'error', 'message' => 'Validation failed', 'errors' => $errors];
        }

        $saveQuery = $this->save();

        return $saveQuery
            ? ['status' => 'success', 'message' => 'Address saved successfully']
            : ['status' => 'error', 'message' => 'Failed to save address'];
    }

    // Validate the address data
    public function validate()
    {
        $errors = [];

        if (empty($this->user_id)) {
            $errors[] = "User ID cannot be empty.";
        }
        if (empty($this->address_line1)) {
            $errors[] = "Address line 1 cannot be empty.";
        }
        if (empty($this->city)) {
            $errors[] = "City cannot be empty.";
        }
        if (empty($this->country)) {
            $errors[] = "Country cannot be empty.";
        }

        return $errors;
    }

    // Set a specific address as the default address for the user
    public function setDefaultAddress()
    {
        // First, unset any existing default address for this user
        $sql = "UPDATE " . self::$table_name . " SET is_default = 0 WHERE user_id = :user_id";
        self::executeQuery($sql, ['user_id' => $this->user_id]);

        // Now, set this address as the default
        $this->is_default = true;
        $this->save(); // Update the database
    }

    // Retrieve all addresses for a user
    public static function findAddressesByUserId($user_id)
    {
        $sql = "SELECT * FROM " . self::$table_name . " WHERE user_id = :user_id ORDER BY created_at DESC";
        $stmt = self::executeQuery($sql, ['user_id' => $user_id]);

        $addresses = [];
        foreach ($stmt as $row) {
            $addresses[] = new self($row);
        }

        return $addresses;
    }

    // Retrieve the default address for a user
    public static function findDefaultAddressByUserId($user_id)
    {
        $sql = "SELECT * FROM " . self::$table_name . " WHERE user_id = :user_id AND is_default = 1 LIMIT 1";
        $stmt = self::executeQuery($sql, ['user_id' => $user_id]);

        return $stmt ? new self($stmt[0]) : null;
    }
}

?>
