<?php

class Payment extends DatabaseObject
{
    // Table name
    static protected $table_name = "Payments";

    // Database columns
    static protected $db_columns = [
        'payment_id',
        'order_id',
        'amount',
        'payment_date',
        'payment_method',
        'status',
        'created_at'
    ];

    // Class properties for each column
    public $payment_id;
    public $order_id;
    public $amount;
    public $payment_date;
    public $payment_method;
    public $status;
    public $created_at;

    // Constructor
    public function __construct($args = [])
    {
        $this->payment_id = $args['payment_id'] ?? null;
        $this->order_id = $args['order_id'] ?? null;
        $this->amount = $args['amount'] ?? 0.00;
        $this->payment_date = $args['payment_date'] ?? null;
        $this->payment_method = $args['payment_method'] ?? '';
        $this->status = $args['status'] ?? 'Pending';
        $this->created_at = $args['created_at'] ?? null;
    }

    // Save or update payment information
    public function savePayment()
    {
        $errors = $this->validate();

        if (!empty($errors)) {
            return ['status' => 'error', 'message' => 'Validation failed', 'errors' => $errors];
        }

        $saveQuery = $this->save();

        return $saveQuery
            ? ['status' => 'success', 'message' => 'Payment processed successfully']
            : ['status' => 'error', 'message' => 'Failed to process payment'];
    }

    // Retrieve all payments for a specific order
    static public function findPaymentsByOrderId($order_id)
    {
        $sql = "SELECT * FROM " . static::$table_name . " WHERE order_id = :order_id";
        $stmt = self::executeQuery($sql, ['order_id' => $order_id]);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return array_map([static::class, 'instantiate'], $results);
    }

    // Retrieve a specific payment by its ID
    static public function findPaymentById($payment_id)
    {
        return self::findById($payment_id);
    }

    // Validation for payment fields
    protected function validate()
    {
        $this->errors = [];

        if (empty($this->order_id)) {
            $this->errors[] = "Order ID is required.";
        }

        if (empty($this->amount) || $this->amount <= 0) {
            $this->errors[] = "Amount must be a positive value.";
        }

        if (empty($this->payment_method)) {
            $this->errors[] = "Payment method is required.";
        } elseif (!in_array($this->payment_method, ['Credit Card', 'PayPal', 'Bank Transfer'])) {
            $this->errors[] = "Payment method must be one of: Credit Card, PayPal, Bank Transfer.";
        }

        if (!in_array($this->status, ['Completed', 'Pending', 'Failed'])) {
            $this->errors[] = "Status must be one of: Completed, Pending, Failed.";
        }

        return $this->errors;
    }

    // Mark the payment as completed
    public function markAsCompleted()
    {
        $this->status = 'Completed';
        return $this->savePayment();
    }

    // Mark the payment as failed
    public function markAsFailed()
    {
        $this->status = 'Failed';
        return $this->savePayment();
    }
}

?>
