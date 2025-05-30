<?php
require 'db_connect.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);

if (!isset($input['search_term'])) {
    echo json_encode(['success' => false, 'message' => 'Missing search term']);
    exit;
}

$search_term = trim($input['search_term']);

try {
    if (empty($search_term)) {
        // Return all products if search term is empty
        $stmt = $conn->prepare("
            SELECT p.product_id, p.name, p.description, p.price, p.stock_quantity, c.category_name,
                   CASE WHEN p.product_image IS NOT NULL THEN 1 ELSE 0 END as has_image
            FROM products p 
            JOIN categories c ON p.category_id = c.category_id 
            ORDER BY p.name
        ");
    } else {
        // Search in product name, description, and category name
        $search_pattern = '%' . $search_term . '%';
        $stmt = $conn->prepare("
            SELECT p.product_id, p.name, p.description, p.price, p.stock_quantity, c.category_name,
                   CASE WHEN p.product_image IS NOT NULL THEN 1 ELSE 0 END as has_image
            FROM products p 
            JOIN categories c ON p.category_id = c.category_id 
            WHERE p.name LIKE ? 
               OR p.description LIKE ? 
               OR c.category_name LIKE ?
            ORDER BY 
                CASE 
                    WHEN p.name LIKE ? THEN 1
                    WHEN c.category_name LIKE ? THEN 2
                    WHEN p.description LIKE ? THEN 3
                    ELSE 4
                END,
                p.name
        ");
        $stmt->bind_param("ssssss", $search_pattern, $search_pattern, $search_pattern, 
                         $search_pattern, $search_pattern, $search_pattern);
    }
    
    $stmt->execute();
    $result = $stmt->get_result();
    $products = $result->fetch_all(MYSQLI_ASSOC);
    
    echo json_encode([
        'success' => true, 
        'products' => $products,
        'search_term' => $search_term
    ]);
    
} catch (Exception $e) {
    error_log("Search error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Search failed']);
}
?>
