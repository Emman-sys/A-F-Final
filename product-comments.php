<?php
// filepath: c:\Users\ceile\A-F-Final\product-comments.php
session_start();
require 'db_connect.php';

header('Content-Type: application/json');

// Handle AJAX requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    $action = $input['action'] ?? '';
    
    switch ($action) {
        case 'get_comments':
            getProductComments($conn, $input);
            break;
            
        case 'add_comment':
            addProductComment($conn, $input);
            break;
            
        default:
            echo json_encode(['success' => false, 'message' => 'Invalid action']);
    }
    exit;
}

// Get comments for a product
function getProductComments($conn, $input) {
    if (!isset($input['product_id'])) {
        echo json_encode(['success' => false, 'message' => 'Product ID required']);
        return;
    }
    
    $product_id = (int)$input['product_id'];
    
    try {
        $stmt = $conn->prepare("
            SELECT 
                pc.comment_id,
                pc.comment_text,
                pc.rating,
                pc.created_at,
                pc.user_id,
                u.name as user_name
            FROM product_comments pc
            JOIN users u ON pc.user_id = u.user_id
            WHERE pc.product_id = ?
            ORDER BY pc.created_at DESC
            LIMIT 20
        ");
        
        $stmt->bind_param("i", $product_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $comments = [];
        while ($row = $result->fetch_assoc()) {
            $comments[] = [
                'comment_id' => $row['comment_id'],
                'text' => $row['comment_text'],
                'rating' => (int)$row['rating'],
                'date' => $row['created_at'],
                'user_name' => $row['user_name'],
                'user_id' => $row['user_id']
            ];
        }
        
        // Get average rating
        $avg_stmt = $conn->prepare("
            SELECT AVG(rating) as avg_rating, COUNT(*) as total_comments
            FROM product_comments 
            WHERE product_id = ?
        ");
        $avg_stmt->bind_param("i", $product_id);
        $avg_stmt->execute();
        $avg_result = $avg_stmt->get_result();
        $avg_data = $avg_result->fetch_assoc();
        
        echo json_encode([
            'success' => true,
            'comments' => $comments,
            'average_rating' => round($avg_data['avg_rating'], 1),
            'total_comments' => (int)$avg_data['total_comments']
        ]);
        
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Error loading comments']);
    }
}

// Add a new comment
function addProductComment($conn, $input) {
    if (!isset($_SESSION['user_id'])) {
        echo json_encode(['success' => false, 'message' => 'Please login to add comments']);
        return;
    }
    
    $required_fields = ['product_id', 'comment_text', 'rating'];
    foreach ($required_fields as $field) {
        if (!isset($input[$field])) {
            echo json_encode(['success' => false, 'message' => 'Missing required fields']);
            return;
        }
    }
    
    $user_id = $_SESSION['user_id'];
    $product_id = (int)$input['product_id'];
    $comment_text = trim($input['comment_text']);
    $rating = (int)$input['rating'];
    
    // Validation
    if (empty($comment_text)) {
        echo json_encode(['success' => false, 'message' => 'Comment cannot be empty']);
        return;
    }
    
    if (strlen($comment_text) < 5) {
        echo json_encode(['success' => false, 'message' => 'Comment must be at least 5 characters']);
        return;
    }
    
    if ($rating < 1 || $rating > 5) {
        echo json_encode(['success' => false, 'message' => 'Rating must be between 1 and 5 stars']);
        return;
    }
    
    try {
        // Check if product exists
        $check_stmt = $conn->prepare("SELECT product_id FROM products WHERE product_id = ?");
        $check_stmt->bind_param("i", $product_id);
        $check_stmt->execute();
        if ($check_stmt->get_result()->num_rows === 0) {
            echo json_encode(['success' => false, 'message' => 'Product not found']);
            return;
        }
        
        // Insert new comment
        $stmt = $conn->prepare("
            INSERT INTO product_comments (product_id, user_id, comment_text, rating) 
            VALUES (?, ?, ?, ?)
        ");
        $stmt->bind_param("iisi", $product_id, $user_id, $comment_text, $rating);
        
        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Comment added successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error adding comment']);
        }
        
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Database error']);
    }
}
?>