<?php

class Users extends DatabaseObject
{
    // Table name
    static protected $table_name = "Users";

    // Database columns
    static protected $db_columns = [
        'user_id',
        'first_name',
        'last_name',
        'email',
        'password_hash',
        'phone_number',
        'created_at',
        'updated_at',
        'last_loggedIn'
    ];

    // Class properties for each column
    public $user_id;
    public $first_name;
    public $last_name;
    public $email;
    public $password_hash;
    public $phone_number;
    public $created_at;
    public $updated_at;
    public $last_loggedIn;

    // Constructor
    public function __construct($args = [])
    {
        $this->user_id = $args['user_id'] ?? null;
        $this->first_name = $args['first_name'] ?? '';
        $this->last_name = $args['last_name'] ?? '';
        $this->email = $args['email'] ?? '';
        $this->password_hash = $args['password_hash'] ?? '';
        $this->phone_number = $args['phone_number'] ?? '';
        $this->created_at = $args['created_at'] ?? null;
        $this->updated_at = $args['updated_at'] ?? null;
        $this->last_loggedIn = $args['last_loggedIn'] ?? null;
    }

    // Register a new user
    static public function register($data)
    {
        $passwordHash = new PasswordHash();
        $hashedPassword = isset($data["password"]) ? $passwordHash->hash($data["password"]) : '';

        // Check if the user already exists
        $existingUser = self::findByEmail($data["email"]);
        if ($existingUser) {
            return ['status' => 'error', 'message' => 'Email already exists'];
        }

        $data['password_hash'] = $hashedPassword; // Assign hashed password
        unset($data["password"]); // Remove plain password from array
        $user = new self($data);
        $errors = $user->validate();

        if (!empty($errors)) {
            return ['status' => 'error', 'message' => 'Validation failed', 'errors' => $errors];
        }

        $saveQuery = $user->save();

        return $saveQuery
            ? ['status' => 'success', 'message' => 'User registered successfully']
            : ['status' => 'error', 'message' => 'Registration failed'];
    }

    // Verify user login
    static public function login($email, $password)
    {
        $sql = "SELECT * FROM " . static::$table_name . " WHERE email = :email";
        $stmt = self::executeQuery($sql, ['email' => $email]);
        $user_data = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user_data) {
            $user = static::instantiate($user_data);
            $passwordHash = new PasswordHash();

            if ($passwordHash->verify($password, $user->password_hash)) {
                $tokenData = [
                    'user_id' => $user->user_id,
                    'first_name' => $user->first_name,
                    'last_name' => $user->last_name
                ];
                $token = JWT::generateToken($tokenData);

                return [
                    'status' => 'success',
                    'message' => 'Login successful',
                    'user' => $user,
                    'token' => $token
                ];
            } else {
                return ['status' => 'error', 'message' => 'Invalid password'];
            }
        }

        return ['status' => 'error', 'message' => 'User not found'];
    }

    // Retrieve all users
    static public function allUsers()
    {
        return self::findAll();
    }

    // Retrieve user by ID
    static public function findUserById($id)
    {
        return self::findById($id);
    }

    // Validation for user fields
    protected function validate()
    {
        $this->errors = [];

        if ($this->is_blank($this->first_name)) {
            $this->errors[] = "First name cannot be blank.";
        }

        if ($this->is_blank($this->last_name)) {
            $this->errors[] = "Last name cannot be blank.";
        }

        if ($this->is_blank($this->email)) {
            $this->errors[] = "Email cannot be blank.";
        } elseif (!$this->has_valid_email_format($this->email)) {
            $this->errors[] = "Email must be a valid format.";
        }

        if (empty($this->user_id) && $this->is_blank($this->password_hash)) {
            $this->errors[] = "Password cannot be blank.";
        }

        return $this->errors;
    }

    // Helper functions
    private function is_blank($value)
    {
        return !isset($value) || trim($value) === '';
    }

    private function has_valid_email_format($email)
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }
}

?>
