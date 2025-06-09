<?php
// filepath: c:\Users\ceile\A-F-Final\get_cart_count.php
session_start();
require 'db_connect.php';

header('Content-Type: application/json');

$cart_count = 0;
if (isset($_SESSION['user_id'])) {
    try {
        $stmt = $conn->prepare("
            SELECT SUM(ci.quantity) as total 
            FROM cartitems ci 
            JOIN cart c ON ci.cart_id = c.cart_id 
            WHERE c.user_id = ?
        ");
        $stmt->bind_param("i", $_SESSION['user_id']);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $cart_count = $row['total'] ?? 0;
    } catch (Exception $e) {
        // Silent fail
    }
}

echo json_encode(['count' => (int)$cart_count]);
?>