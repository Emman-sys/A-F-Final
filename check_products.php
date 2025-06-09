<?php
// filepath: c:\Users\ceile\A-F-Final\check_products.php
require 'db_connect.php';

echo "=== CHECKING PRODUCTS IN DATABASE ===\n";

try {
    $stmt = $conn->prepare("SELECT product_id, name, price FROM products LIMIT 10");
    $stmt->execute();
    $result = $stmt->get_result();
    
    echo "Available products:\n";
    while ($row = $result->fetch_assoc()) {
        echo "ID: " . $row['product_id'] . " - Name: " . $row['name'] . " - Price: $" . $row['price'] . "\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>