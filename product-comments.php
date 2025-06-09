<?php
// filepath: c:\Users\ceile\A-F-Final\product-comments.php
session_start();
require 'db_connect.php';

header('Content-Type: application/json');

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

function getProductComments($conn, $input) {
    if (!isset($input['product_id'])) {
        echo json_encode(['success' => false, 'message' => 'Product ID required']);
        return;
    }
    
    $product_id = (int)$input['product_id'];
    
    try {
        // Check if table exists first
        $tableCheck = $conn->query("SHOW TABLES LIKE 'product_comments'");
        if ($tableCheck->num_rows == 0) {
            // Table doesn't exist, return empty comments
            echo json_encode([
                'success' => true,
                'comments' => []
            ]);
            return;
        }
        
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
        
        echo json_encode([
            'success' => true,
            'comments' => $comments
        ]);
        
    } catch (Exception $e) {
        error_log("Error fetching comments: " . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'Error loading comments']);
    }
}

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
    
    if (empty($comment_text) || strlen($comment_text) < 5) {
        echo json_encode(['success' => false, 'message' => 'Comment must be at least 5 characters']);
        return;
    }
    
    if ($rating < 1 || $rating > 5) {
        echo json_encode(['success' => false, 'message' => 'Rating must be between 1 and 5 stars']);
        return;
    }
    
    try {
        // Check if table exists, create if not
        $tableCheck = $conn->query("SHOW TABLES LIKE 'product_comments'");
        if ($tableCheck->num_rows == 0) {
            // Create the table
            $createTable = "
                CREATE TABLE product_comments (
                    comment_id INT PRIMARY KEY AUTO_INCREMENT,
                    product_id INT NOT NULL,
                    user_id INT NOT NULL,
                    comment_text TEXT NOT NULL,
                    rating INT NOT NULL CHECK (rating >= 1 AND rating <= 5),
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    INDEX idx_product_id (product_id),
                    INDEX idx_user_id (user_id)
                )
            ";
            
            if (!$conn->query($createTable)) {
                throw new Exception("Failed to create comments table");
            }
        }
        
        // Check if user already commented on this product
        $checkStmt = $conn->prepare("SELECT comment_id FROM product_comments WHERE product_id = ? AND user_id = ?");
        $checkStmt->bind_param("ii", $product_id, $user_id);
        $checkStmt->execute();
        $existing = $checkStmt->get_result()->fetch_assoc();
        
        if ($existing) {
            // Update existing comment
            $stmt = $conn->prepare("
                UPDATE product_comments 
                SET comment_text = ?, rating = ?, created_at = CURRENT_TIMESTAMP 
                WHERE product_id = ? AND user_id = ?
            ");
            $stmt->bind_param("siii", $comment_text, $rating, $product_id, $user_id);
            $message = 'Comment updated successfully';
        } else {
            // Insert new comment
            $stmt = $conn->prepare("
                INSERT INTO product_comments (product_id, user_id, comment_text, rating) 
                VALUES (?, ?, ?, ?)
            ");
            $stmt->bind_param("iisi", $product_id, $user_id, $comment_text, $rating);
            $message = 'Comment added successfully';
        }
        
        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => $message]);
        } else {
            throw new Exception("Failed to save comment");
        }
        
    } catch (Exception $e) {
        error_log("Error adding comment: " . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'Error saving comment. Please try again.']);
    }
}
?>