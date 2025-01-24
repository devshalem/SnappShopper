<?php

class CartItem extends DatabaseObject
{
    // Table name
    static protected $table_name = "Cart_Items";

    // Database columns
    static protected $db_columns = [
        'cart_item_id',
        'cart_id',
        'product_id',
        'quantity',
        'price_at_addition',
        'added_date'
    ];

    // Class properties for each column
    public $cart_item_id;
    public $cart_id;
    public $product_id;
    public $quantity;
    public $price_at_addition;
    public $added_date;

    // Constructor
    public function __construct($args = [])
    {
        $this->cart_item_id = $args['cart_item_id'] ?? null;
        $this->cart_id = $args['cart_id'] ?? null;
        $this->product_id = $args['product_id'] ?? null;
        $this->quantity = $args['quantity'] ?? 0;
        $this->price_at_addition = $args['price_at_addition'] ?? 0.00;
        $this->added_date = $args['added_date'] ?? null;
    }

    // Save the cart item to the database
    public function itemsave()
    {
        $sql = "INSERT INTO " . self::$table_name . " (cart_id, product_id, quantity, price_at_addition) 
                VALUES (:cart_id, :product_id, :quantity, :price_at_addition)";
        
        $stmt = self::executeQuery($sql, [
            'cart_id' => $this->cart_id,
            'product_id' => $this->product_id,
            'quantity' => $this->quantity,
            'price_at_addition' => $this->price_at_addition
        ]);

        return $stmt ? ['status' => 'success', 'message' => 'Product added to cart'] : ['status' => 'error', 'message' => 'Failed to add product to cart'];
    }

    // Update the quantity of a product in the cart
    public function updateQuantity($quantity)
    {
        $sql = "UPDATE " . self::$table_name . " 
                SET quantity = :quantity 
                WHERE cart_id = :cart_id AND product_id = :product_id";

        $stmt = self::executeQuery($sql, [
            'cart_id' => $this->cart_id,
            'product_id' => $this->product_id,
            'quantity' => $quantity
        ]);

        return $stmt ? ['status' => 'success', 'message' => 'Quantity updated'] : ['status' => 'error', 'message' => 'Failed to update quantity'];
    }

    // Get the total price for the cart item (quantity * price_at_addition)
    public function getTotalPrice()
    {
        return $this->quantity * $this->price_at_addition;
    }

    // Find a specific cart item by cart_id and product_id
    public static function findItemInCart($cart_id, $product_id)
    {
        $sql = "SELECT * FROM " . self::$table_name . " WHERE cart_id = :cart_id AND product_id = :product_id LIMIT 1";
        $stmt = self::executeQuery($sql, ['cart_id' => $cart_id, 'product_id' => $product_id]);

        return $stmt ? new self($stmt) : null;
    }

    // Find all items in a cart by cart_id
    public static function findItemsByCartId($cart_id)
    {
        $sql = "SELECT * FROM " . self::$table_name . " WHERE cart_id = :cart_id";
        $stmt = self::executeQuery($sql, ['cart_id' => $cart_id]);

        $cartItems = [];
        foreach ($stmt as $row) {
            $cartItems[] = new self($row);
        }

        return $cartItems;
    }
}

?>
