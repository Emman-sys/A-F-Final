<?php
session_start();
require 'db_connect.php';

// Set content type to JSON
header('Content-Type: application/json');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'User not logged in']);
    exit;
}

// Check if request is POST and has JSON data
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

$input = file_get_contents('php://input');
$data = json_decode($input, true);

if (!$data) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid JSON data']);
    exit;
}

$user_id = $_SESSION['user_id'];

// Validate required fields
$required_fields = ['delivery_address', 'phone', 'payment_method', 'total_amount', 'cart_items'];
foreach ($required_fields as $field) {
    if (!isset($data[$field]) || empty($data[$field])) {
        echo json_encode(['success' => false, 'message' => "Missing required field: $field"]);
        exit;
    }
}

// Validate phone number
if (!preg_match('/^[0-9]{11}$/', $data['phone'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid phone number format']);
    exit;
}

// Validate payment method
$valid_payment_methods = ['credit_card', 'gcash', 'cash_on_delivery'];
if (!in_array($data['payment_method'], $valid_payment_methods)) {
    echo json_encode(['success' => false, 'message' => 'Invalid payment method']);
    exit;
}

// Start transaction
$conn->begin_transaction();

try {
    // Get current timestamp
    $order_date = date('Y-m-d H:i:s');
    
    // Insert into orders table
    $order_stmt = $conn->prepare("
        INSERT INTO orders (user_id, order_date, status, total_amount, shipping_address) 
        VALUES (?, ?, 'pending', ?, ?)
    ");
    
    if (!$order_stmt) {
        throw new Exception("Error preparing order statement: " . $conn->error);
    }
    
    $order_stmt->bind_param("isds", 
        $user_id, 
        $order_date, 
        $data['total_amount'], 
        $data['delivery_address']
    );
    
    if (!$order_stmt->execute()) {
        throw new Exception("Error inserting order: " . $order_stmt->error);
    }
    
    $order_id = $conn->insert_id;
    
    // Insert into payments table
    $payment_method_display = ucwords(str_replace('_', ' ', $data['payment_method']));
    $payment_status = 'pending'; // Always set to pending for all payment methods
    
    $payment_stmt = $conn->prepare("
        INSERT INTO payments (order_id, payment_method, payment_status, amount_paid, payment_date, delivery_address) 
        VALUES (?, ?, ?, ?, ?, ?)
    ");
    
    if (!$payment_stmt) {
        throw new Exception("Error preparing payment statement: " . $conn->error);
    }
    
    $payment_stmt->bind_param("issdss", 
        $order_id,
        $payment_method_display,
        $payment_status,
        $data['total_amount'],
        $order_date,
        $data['delivery_address']
    );
    
    if (!$payment_stmt->execute()) {
        throw new Exception("Error inserting payment: " . $payment_stmt->error);
    }
    
    // Insert order items
    $item_stmt = $conn->prepare("
        INSERT INTO orderitems (order_id, product_id, quantity, price_at_purchase) 
        VALUES (?, ?, ?, ?)
    ");
    
    if (!$item_stmt) {
        throw new Exception("Error preparing order items statement: " . $conn->error);
    }
    
    foreach ($data['cart_items'] as $item) {
        // Validate item data
        if (!isset($item['product_id']) || !isset($item['quantity']) || !isset($item['price'])) {
            throw new Exception("Invalid cart item data");
        }
        
        // Check product stock
        $stock_check = $conn->prepare("SELECT stock_quantity FROM products WHERE product_id = ?");
        $stock_check->bind_param("i", $item['product_id']);
        $stock_check->execute();
        $stock_result = $stock_check->get_result();
        
        if ($stock_result->num_rows === 0) {
            throw new Exception("Product not found: " . $item['product_id']);
        }
        
        $product = $stock_result->fetch_assoc();
        if ($product['stock_quantity'] < $item['quantity']) {
            throw new Exception("Insufficient stock for product: " . ($item['name'] ?? $item['product_id']));
        }
        
        // Insert order item
        $item_stmt->bind_param("iiid", 
            $order_id,
            $item['product_id'],
            $item['quantity'],
            $item['price']
        );
        
        if (!$item_stmt->execute()) {
            throw new Exception("Error inserting order item: " . $item_stmt->error);
        }
        
        // Update product stock
        $update_stock = $conn->prepare("UPDATE products SET stock_quantity = stock_quantity - ? WHERE product_id = ?");
        $update_stock->bind_param("ii", $item['quantity'], $item['product_id']);
        
        if (!$update_stock->execute()) {
            throw new Exception("Error updating product stock: " . $update_stock->error);
        }
    }
    
    // Clear user's cart
    $clear_cart_stmt = $conn->prepare("
        DELETE ci FROM cartitems ci 
        JOIN cart c ON ci.cart_id = c.cart_id 
        WHERE c.user_id = ?
    ");
    
    if ($clear_cart_stmt) {
        $clear_cart_stmt->bind_param("i", $user_id);
        $clear_cart_stmt->execute();
    }
    
    // Commit transaction
    $conn->commit();
    
    // Return success response
    echo json_encode([
        'success' => true,
        'message' => 'Order placed successfully',
        'order_id' => $order_id,
        'total_amount' => $data['total_amount'],
        'payment_method' => $payment_method_display,
        'delivery_address' => $data['delivery_address']
    ]);
    
} catch (Exception $e) {
    // Rollback transaction on error
    $conn->rollback();
    
    error_log("Checkout error: " . $e->getMessage());
    
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}

// Close statements
if (isset($order_stmt)) $order_stmt->close();
if (isset($payment_stmt)) $payment_stmt->close();
if (isset($item_stmt)) $item_stmt->close();
if (isset($stock_check)) $stock_check->close();
if (isset($update_stock)) $update_stock->close();
if (isset($clear_cart_stmt)) $clear_cart_stmt->close();

$conn->close();
?>
