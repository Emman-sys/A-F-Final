<?php
// filepath: c:\Users\ceile\A-F-Final\add_to_cart.php
session_start();
require 'db_connect.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Please login to add items to cart']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);

if (!isset($input['product_id']) || !isset($input['quantity'])) {
    echo json_encode(['success' => false, 'message' => 'Missing required fields']);
    exit;
}

$user_id = $_SESSION['user_id'];
$product_id = (int)$input['product_id'];
$quantity = (int)$input['quantity'];

try {
    // Check if product exists
    $stmt = $conn->prepare("SELECT stock_quantity FROM products WHERE product_id = ?");
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $product = $result->fetch_assoc();
    
    if (!$product) {
        echo json_encode(['success' => false, 'message' => 'Product not found']);
        exit;
    }
    
    if ($product['stock_quantity'] < $quantity) {
        echo json_encode(['success' => false, 'message' => 'Not enough stock']);
        exit;
    }
    
    // Get or create cart
    $stmt = $conn->prepare("SELECT cart_id FROM cart WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $cart = $result->fetch_assoc();
    
    if (!$cart) {
        $stmt = $conn->prepare("INSERT INTO cart (user_id) VALUES (?)");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $cart_id = $conn->insert_id;
    } else {
        $cart_id = $cart['cart_id'];
    }
    
    // Check if product already in cart
    $stmt = $conn->prepare("SELECT quantity FROM cartitems WHERE cart_id = ? AND product_id = ?");
    $stmt->bind_param("ii", $cart_id, $product_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $existing = $result->fetch_assoc();
    
    if ($existing) {
        $new_quantity = $existing['quantity'] + $quantity;
        $stmt = $conn->prepare("UPDATE cartitems SET quantity = ? WHERE cart_id = ? AND product_id = ?");
        $stmt->bind_param("iii", $new_quantity, $cart_id, $product_id);
        $stmt->execute();
    } else {
        $stmt = $conn->prepare("INSERT INTO cartitems (cart_id, product_id, quantity) VALUES (?, ?, ?)");
        $stmt->bind_param("iii", $cart_id, $product_id, $quantity);
        $stmt->execute();
    }
    
    echo json_encode(['success' => true, 'message' => 'Added to cart']);
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Database error']);
}
?>