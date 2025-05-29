<?php
require 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_id = $_POST['product_id'];
    
    if (isset($_FILES['product_image']) && $_FILES['product_image']['error'] === 0) {
        $image = $_FILES['product_image'];
        
        // Validate image
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        if (!in_array($image['type'], $allowed_types)) {
            die('Invalid image type. Only JPG, PNG, GIF, and WebP are allowed.');
        }
        
        // Check file size (5MB max)
        if ($image['size'] > 5 * 1024 * 1024) {
            die('Image too large. Maximum size is 5MB.');
        }
        
        // Read image data
        $image_data = file_get_contents($image['tmp_name']);
        $image_name = $image['name'];
        $image_type = $image['type'];
        $image_size = $image['size'];
        
        // Update database
        $stmt = $conn->prepare("UPDATE products SET product_image = ?, image_name = ?, image_type = ?, image_size = ? WHERE product_id = ?");
        $stmt->bind_param("bssii", $image_data, $image_name, $image_type, $image_size, $product_id);
        
        // Send the blob data
        $stmt->send_long_data(0, $image_data);
        
        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Image uploaded successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Database error: ' . $conn->error]);
        }
        
        $stmt->close();
    } else {
        echo json_encode(['success' => false, 'message' => 'No image uploaded or upload error']);
    }
}
?>