<?php
session_start();
require 'db_connect.php';

// Set JSON content type
header('Content-Type: application/json');

// Check if user is admin
if (!isset($_SESSION['admin_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit();
}

// Handle product addition
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['product_name']);
    $description = trim($_POST['product_description']);
    $price = floatval($_POST['product_price']);
    $stock = intval($_POST['product_stock']);
    $category_id = intval($_POST['category_id']);
    
    // Validate inputs
    if (empty($name) || empty($description) || $price <= 0 || $stock < 0 || $category_id <= 0) {
        echo json_encode(['success' => false, 'message' => 'Please fill all fields with valid data.']);
        exit();
    }
    
    try {
        // Handle image upload
        $imageData = null;
        $imageType = null;
        
        if (isset($_FILES['product_image']) && $_FILES['product_image']['error'] === UPLOAD_ERR_OK) {
            $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
            $fileType = $_FILES['product_image']['type'];
            
            if (in_array($fileType, $allowedTypes) && $_FILES['product_image']['size'] <= 5000000) { // 5MB limit
                $imageData = file_get_contents($_FILES['product_image']['tmp_name']);
                $imageType = $fileType;
            } else {
                echo json_encode(['success' => false, 'message' => 'Invalid image file. Please use JPG, PNG, or GIF under 5MB.']);
                exit();
            }
        }
        
        // Insert product into database
        $stmt = $conn->prepare("INSERT INTO products (name, description, price, stock_quantity, category_id, product_image, image_type) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssdiibs", $name, $description, $price, $stock, $category_id, $imageData, $imageType);
        
        if ($stmt->execute()) {
            // Get updated product count
            $countStmt = $conn->prepare("SELECT COUNT(*) as total_products FROM products");
            $countStmt->execute();
            $result = $countStmt->get_result();
            $newCount = $result->fetch_assoc()['total_products'];
            
            echo json_encode([
                'success' => true, 
                'message' => 'Product added successfully!',
                'new_product_count' => $newCount
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to add product: ' . $conn->error]);
        }
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Error adding product: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
?>
