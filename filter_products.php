<?php
session_start();
require 'db_connect.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);

try {
    $category_id = isset($input['category']) && $input['category'] !== '' ? intval($input['category']) : null;
    
    if ($category_id) {
        // Get products for specific category
        $stmt = $conn->prepare("
            SELECT p.product_id, p.name, p.description, p.price, p.stock_quantity, c.category_name,
                   CASE WHEN p.product_image IS NOT NULL THEN 1 ELSE 0 END as has_image
            FROM products p 
            JOIN categories c ON p.category_id = c.category_id 
            WHERE p.category_id = ?
            ORDER BY p.name
        ");
        $stmt->bind_param("i", $category_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $products = $result->fetch_all(MYSQLI_ASSOC);
        
        // Get category name
        $category_name = $products[0]['category_name'] ?? 'Unknown Category';
        
    } else {
        // Get all products
        $stmt = $conn->prepare("
            SELECT p.product_id, p.name, p.description, p.price, p.stock_quantity, c.category_name,
                   CASE WHEN p.product_image IS NOT NULL THEN 1 ELSE 0 END as has_image
            FROM products p 
            JOIN categories c ON p.category_id = c.category_id 
            ORDER BY p.name
        ");
        $stmt->execute();
        $result = $stmt->get_result();
        $products = $result->fetch_all(MYSQLI_ASSOC);
        
        $category_name = 'All Products';
    }
    
    echo json_encode([
        'success' => true,
        'products' => $products,
        'category_name' => $category_name
    ]);
    
} catch (Exception $e) {
    error_log("Filter error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Filter failed']);
}
?>