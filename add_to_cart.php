<?php
session_start();
require 'db_connect.php';

header('Content-Type: application/json');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Please log in to add items to cart'
    ]);
    exit();
}

$input = json_decode(file_get_contents('php://input'), true);
$product_id = $input['product_id'] ?? null;
$quantity = $input['quantity'] ?? 1;
$user_id = $_SESSION['user_id'];

if (!$product_id) {
    echo json_encode([
        'success' => false,
        'message' => 'Product ID is required'
    ]);
    exit();
}

try {
    // Check if product exists and has stock
    $product_stmt = $conn->prepare("SELECT stock_quantity FROM products WHERE product_id = ?");
    $product_stmt->bind_param("i", $product_id);
    $product_stmt->execute();
    $product_result = $product_stmt->get_result();
    $product = $product_result->fetch_assoc();
    
    if (!$product) {
        echo json_encode([
            'success' => false,
            'message' => 'Product not found'
        ]);
        exit();
    }
    
    if ($product['stock_quantity'] < $quantity) {
        echo json_encode([
            'success' => false,
            'message' => 'Not enough stock available'
        ]);
        exit();
    }
    
    // Get or create cart for user
    $cart_stmt = $conn->prepare("SELECT cart_id FROM cart WHERE user_id = ?");
    $cart_stmt->bind_param("i", $user_id);
    $cart_stmt->execute();
    $cart_result = $cart_stmt->get_result();
    
    if ($cart_result->num_rows === 0) {
        // Create new cart
        $create_cart_stmt = $conn->prepare("INSERT INTO cart (user_id) VALUES (?)");
        $create_cart_stmt->bind_param("i", $user_id);
        $create_cart_stmt->execute();
        $cart_id = $conn->insert_id;
    } else {
        $cart_id = $cart_result->fetch_assoc()['cart_id'];
    }
    
    // Check if item already exists in cart
    $item_stmt = $conn->prepare("SELECT quantity FROM cartitems WHERE cart_id = ? AND product_id = ?");
    $item_stmt->bind_param("ii", $cart_id, $product_id);
    $item_stmt->execute();
    $item_result = $item_stmt->get_result();
    
    if ($item_result->num_rows > 0) {
        // Update existing item
        $existing_quantity = $item_result->fetch_assoc()['quantity'];
        $new_quantity = $existing_quantity + $quantity;
        
        if ($new_quantity > $product['stock_quantity']) {
            echo json_encode([
                'success' => false,
                'message' => 'Cannot add more items. Stock limit reached.'
            ]);
            exit();
        }
        
        $update_stmt = $conn->prepare("UPDATE cartitems SET quantity = ? WHERE cart_id = ? AND product_id = ?");
        $update_stmt->bind_param("iii", $new_quantity, $cart_id, $product_id);
        $update_stmt->execute();
    } else {
        // Add new item
        $insert_stmt = $conn->prepare("INSERT INTO cartitems (cart_id, product_id, quantity) VALUES (?, ?, ?)");
        $insert_stmt->bind_param("iii", $cart_id, $product_id, $quantity);
        $insert_stmt->execute();
    }
    
    echo json_encode([
        'success' => true,
        'message' => 'Product added to cart successfully'
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error adding product to cart: ' . $e->getMessage()
    ]);
}
?>