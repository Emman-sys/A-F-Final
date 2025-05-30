<?php
// filepath: c:\Users\ceile\A-F-Final\includes\ProductManager.php
class ProductManager {
    private $conn;
    
    public function __construct($database_connection) {
        $this->conn = $database_connection;
    }
    
    public function getProducts($category_id = null) {
        if ($category_id) {
            $stmt = $this->conn->prepare("SELECT p.product_id, p.name, p.description, p.price, p.stock_quantity, c.category_name,
                                   CASE WHEN p.product_image IS NOT NULL THEN 1 ELSE 0 END as has_image
                                   FROM products p 
                                   JOIN categories c ON p.category_id = c.category_id 
                                   WHERE p.category_id = ?");
            $stmt->bind_param("i", $category_id);
        } else {
            $stmt = $this->conn->prepare("SELECT p.product_id, p.name, p.description, p.price, p.stock_quantity, c.category_name,
                                   CASE WHEN p.product_image IS NOT NULL THEN 1 ELSE 0 END as has_image
                                   FROM products p 
                                   JOIN categories c ON p.category_id = c.category_id");
        }
        
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }
    
    public function getCartCount($user_id) {
        $count_stmt = $this->conn->prepare("
            SELECT SUM(ci.quantity) as total 
            FROM cartitems ci 
            JOIN cart c ON ci.cart_id = c.cart_id 
            WHERE c.user_id = ?
        ");
        $count_stmt->bind_param("i", $user_id);
        $count_stmt->execute();
        $count_result = $count_stmt->get_result();
        return $count_result->fetch_assoc()['total'] ?? 0;
    }
}
?>