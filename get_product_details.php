<?php
// filepath: c:\Users\ceile\A-F-Final\get_product_details.php
session_start();
require 'db_connect.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $raw_input = file_get_contents('php://input');
    
    if (empty($raw_input)) {
        echo json_encode(['success' => false, 'message' => 'No input data received']);
        exit;
    }
    
    $input = json_decode($raw_input, true);
    
    if (json_last_error() !== JSON_ERROR_NONE) {
        echo json_encode(['success' => false, 'message' => 'Invalid JSON']);
        exit;
    }
    
    if (!isset($input['product_id'])) {
        echo json_encode(['success' => false, 'message' => 'Product ID is required']);
        exit;
    }
    
    $product_id = (int)$input['product_id'];
    
    if ($product_id <= 0) {
        echo json_encode(['success' => false, 'message' => 'Invalid product ID']);
        exit;
    }
    
    try {
        $stmt = $conn->prepare("
            SELECT 
                product_id,
                name,
                price,
                stock_quantity
            FROM products 
            WHERE product_id = ?
        ");
        
        $stmt->bind_param("i", $product_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $product = $result->fetch_assoc();
        
        if ($product) {
            $product['description'] = 'This is a premium quality ' . $product['name'] . ' - perfect for chocolate lovers!';
            $product['has_image'] = 1;
            $product['category_name'] = 'Chocolate';
            
            echo json_encode([
                'success' => true,
                'product' => $product
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Product not found']);
        }
        
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Database error']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Only POST method allowed']);
}
?>
