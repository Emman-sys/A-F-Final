<?php
session_start();
require 'db_connect.php';

header('Content-Type: application/json');

// Log the request
error_log("get_product_details.php called with ID: " . ($_GET['id'] ?? 'none'));

if (!isset($_GET['id'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Product ID is required']);
    exit();
}

$product_id = intval($_GET['id']);
error_log("Processing product ID: " . $product_id);

try {
    $stmt = $conn->prepare("
        SELECT 
            p.product_id, 
            p.name, 
            p.description, 
            p.price, 
            p.stock_quantity,
            c.category_name,
            CASE WHEN p.product_image IS NOT NULL THEN 1 ELSE 0 END as has_image
        FROM products p 
        JOIN categories c ON p.category_id = c.category_id 
        WHERE p.product_id = ?
    ");
    
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($product = $result->fetch_assoc()) {
        error_log("Product found: " . json_encode($product));
        echo json_encode([
            'success' => true,
            'product' => $product
        ]);
    } else {
        error_log("Product not found for ID: " . $product_id);
        echo json_encode([
            'success' => false,
            'message' => 'Product not found'
        ]);
    }
    
} catch (Exception $e) {
    error_log("Database error in get_product_details.php: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Database error: ' . $e->getMessage()
    ]);
}
?>
