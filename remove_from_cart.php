<?php
session_start();
require 'db_connect.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
$cart_item_id = $input['cart_item_id'];

$stmt = $conn->prepare("DELETE FROM cartitems WHERE cart_item_id = ?");
$stmt->bind_param("i", $cart_item_id);

echo json_encode(['success' => $stmt->execute()]);
?>