<?php

class Cart extends DatabaseObject
{
    // Table name
    static protected $table_name = "Cart";

    // Database columns
    static protected $db_columns = [
        'cart_id',
        'user_id',
        'created_at',
        'updated_at'
    ];

    // Class properties for each column
    public $cart_id;
    public $user_id;
    public $created_at;
    public $updated_at;

    // Constructor
    public function __construct($args = [])
    {
        $this->cart_id = $args['cart_id'] ?? null;
        $this->user_id = $args['user_id'] ?? null;
        $this->created_at = $args['created_at'] ?? null;
        $this->updated_at = $args['updated_at'] ?? null;
    }

    // Add a product to the cart
    public function addToCart($product_id, $quantity)
    {
        // Check if the product is already in the cart
        $existingItem = CartItem::findItemInCart($this->cart_id, $product_id);

        if ($existingItem) {
            // If the product is already in the cart, update the quantity
            return $existingItem->updateQuantity($quantity);
        } else {
            // Add the new product to the cart
            $cartItem = new CartItem([
                'cart_id' => $this->cart_id,
                'product_id' => $product_id,
                'quantity' => $quantity
            ]);
            return $cartItem->save();
        }
    }

    // Remove a product from the cart
    public function removeFromCart($product_id)
    {
        $sql = "DELETE FROM Cart_Items WHERE cart_id = :cart_id AND product_id = :product_id";
        $stmt = self::executeQuery($sql, ['cart_id' => $this->cart_id, 'product_id' => $product_id]);

        return $stmt
            ? ['status' => 'success', 'message' => 'Product removed from cart']
            : ['status' => 'error', 'message' => 'Failed to remove product from cart'];
    }

    // Retrieve all items in the cart
    public function getItemsInCart()
    {
        return CartItem::findItemsByCartId($this->cart_id);
    }

    // Update the quantity of a product in the cart
    public function updateItemQuantity($product_id, $quantity)
    {
        $cartItem = CartItem::findItemInCart($this->cart_id, $product_id);
        
        if ($cartItem) {
            return $cartItem->updateQuantity($quantity);
        }

        return ['status' => 'error', 'message' => 'Product not found in cart'];
    }

    // Get the total price of all items in the cart
    public function getTotalPrice()
    {
        $total = 0;
        $items = $this->getItemsInCart();
        
        foreach ($items as $item) {
            $total += $item->getTotalPrice();
        }

        return $total;
    }

    // Check if the cart is empty
    public function isEmpty()
    {
        return count($this->getItemsInCart()) === 0;
    }
}

?>
