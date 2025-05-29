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
$change = $input['change'];

// Get current quantity
$stmt = $conn->prepare("SELECT quantity FROM cartitems WHERE cart_item_id = ?");
$stmt->bind_param("i", $cart_item_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'Item not found']);
    exit;
}

$current_quantity = $result->fetch_assoc()['quantity'];
$new_quantity = $current_quantity + $change;

if ($new_quantity <= 0) {
    // Remove item if quantity becomes 0 or less
    $delete_stmt = $conn->prepare("DELETE FROM cartitems WHERE cart_item_id = ?");
    $delete_stmt->bind_param("i", $cart_item_id);
    $success = $delete_stmt->execute();
} else {
    // Update quantity
    $update_stmt = $conn->prepare("UPDATE cartitems SET quantity = ? WHERE cart_item_id = ?");
    $update_stmt->bind_param("ii", $new_quantity, $cart_item_id);
    $success = $update_stmt->execute();
}

echo json_encode(['success' => $success]);
?>