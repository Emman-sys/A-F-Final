<?php
require 'db_connect.php';

if (isset($_GET['id'])) {
    $product_id = intval($_GET['id']);
    
    $stmt = $conn->prepare("SELECT product_image, image_type FROM products WHERE product_id = ?");
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $stmt->bind_result($image_data, $image_type);
    
    if ($stmt->fetch() && $image_data) {
        // Set appropriate headers
        header("Content-Type: " . ($image_type ?: 'image/jpeg'));
        header("Content-Length: " . strlen($image_data));
        header("Cache-Control: max-age=3600"); // Cache for 1 hour
        
        // Output image
        echo $image_data;
    } else {
        // Return default placeholder image (SVG)
        header("Content-Type: image/svg+xml");
        echo '<svg width="200" height="200" xmlns="http://www.w3.org/2000/svg">
                <rect width="200" height="200" fill="#f0f0f0" stroke="#ddd"/>
                <text x="50%" y="50%" text-anchor="middle" dy=".3em" font-family="Arial" font-size="16" fill="#666">No Image</text>
              </svg>';
    }
    
    $stmt->close();
} else {
    http_response_code(400);
    echo "Product ID required";
}
?>