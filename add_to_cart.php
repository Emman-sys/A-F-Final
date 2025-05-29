<?php
session_start();
require 'db_connect.php';

header('Content-Type: application/json');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Please log in to add items to cart']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!$input) {
        // Handle form data instead of JSON
        $product_id = $_POST['product_id'] ?? null;
        $quantity = $_POST['quantity'] ?? 1;
    } else {
        $product_id = $input['product_id'] ?? null;
        $quantity = $input['quantity'] ?? 1;
    }
    
    $user_id = $_SESSION['user_id'];
    
    if (!$product_id) {
        echo json_encode(['success' => false, 'message' => 'Product ID is required']);
        exit;
    }
    
    // Validate product exists and has stock
    $stmt = $conn->prepare("SELECT name, stock_quantity, price FROM products WHERE product_id = ?");
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        echo json_encode(['success' => false, 'message' => 'Product not found']);
        exit;
    }
    
    $product = $result->fetch_assoc();
    
    if ($product['stock_quantity'] < $quantity) {
        echo json_encode(['success' => false, 'message' => 'Not enough stock available']);
        exit;
    }
    
    // Get or create user's cart
    $cart_stmt = $conn->prepare("SELECT cart_id FROM cart WHERE user_id = ?");
    $cart_stmt->bind_param("i", $user_id);
    $cart_stmt->execute();
    $cart_result = $cart_stmt->get_result();
    
    if ($cart_result->num_rows === 0) {
        // Create new cart for user
        $create_cart = $conn->prepare("INSERT INTO cart (user_id) VALUES (?)");
        $create_cart->bind_param("i", $user_id);
        $create_cart->execute();
        $cart_id = $conn->insert_id;
    } else {
        $cart_id = $cart_result->fetch_assoc()['cart_id'];
    }
    
    // Check if product already in cart
    $item_stmt = $conn->prepare("SELECT cart_item_id, quantity FROM cartitems WHERE cart_id = ? AND product_id = ?");
    $item_stmt->bind_param("ii", $cart_id, $product_id);
    $item_stmt->execute();
    $item_result = $item_stmt->get_result();
    
    if ($item_result->num_rows > 0) {
        // Update existing cart item
        $current_item = $item_result->fetch_assoc();
        $new_quantity = $current_item['quantity'] + $quantity;
        
        if ($new_quantity > $product['stock_quantity']) {
            echo json_encode(['success' => false, 'message' => 'Cannot add more items. Stock limit reached.']);
            exit;
        }
        
        $update_stmt = $conn->prepare("UPDATE cartitems SET quantity = ? WHERE cart_item_id = ?");
        $update_stmt->bind_param("ii", $new_quantity, $current_item['cart_item_id']);
        $success = $update_stmt->execute();
    } else {
        // Add new cart item
        $insert_stmt = $conn->prepare("INSERT INTO cartitems (cart_id, product_id, quantity) VALUES (?, ?, ?)");
        $insert_stmt->bind_param("iii", $cart_id, $product_id, $quantity);
        $success = $insert_stmt->execute();
    }
    
    if ($success) {
        // Get updated cart count
        $count_stmt = $conn->prepare("
            SELECT SUM(ci.quantity) as total 
            FROM cartitems ci 
            JOIN cart c ON ci.cart_id = c.cart_id 
            WHERE c.user_id = ?
        ");
        $count_stmt->bind_param("i", $user_id);
        $count_stmt->execute();
        $count_result = $count_stmt->get_result();
        $cart_count = $count_result->fetch_assoc()['total'] ?? 0;
        
        echo json_encode([
            'success' => true, 
            'message' => 'Product added to cart successfully!',
            'cart_count' => $cart_count,
            'product_name' => $product['name']
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error adding product to cart']);
    }
    
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
?>