<?php
session_start();
require 'db_connect.php';
require 'product_carousel.php'; // Add this line

// CATEGORY CONSTRUCTOR - Truly dynamic category system
class CategoryManager {
    private $conn;
    private $categories;
    
    public function __construct($database_connection) {
        $this->conn = $database_connection;
        $this->loadCategoriesFromDatabase();
    }
    
    // Load categories dynamically from database
    private function loadCategoriesFromDatabase() {
        $this->categories = [];
        
        try {
            $stmt = $this->conn->prepare("SELECT category_id, category_name FROM categories ORDER BY category_id");
            $stmt->execute();
            $result = $stmt->get_result();
            
            $gradients = [
                1 => 'linear-gradient(to bottom, #55361A, #CDACB1)',
                2 => 'linear-gradient(to bottom, #BFB886, #F3E794)', 
                3 => 'linear-gradient(to bottom, #D97272, #F8DDDD)',
                4 => 'linear-gradient(to bottom, #71EEEC, #C1ACAC)',
                5 => 'linear-gradient(to bottom, #6B6060, #7C6F6F, #8D7D7D)',
                6 => 'linear-gradient(to bottom, #E6E6FA, #DDA0DD)',
                7 => 'linear-gradient(to bottom, #FFE4E1, #FFC0CB)',
                8 => 'linear-gradient(to bottom, #F0E68C, #BDB76B)',
            ];
            
            while ($row = $result->fetch_assoc()) {
                $category_id = $row['category_id'];
                $category_name = $row['category_name'];
                
                $this->categories[$category_id] = [
                    'name' => $category_name,
                    'gradient' => isset($gradients[$category_id]) ? $gradients[$category_id] : 'linear-gradient(to bottom, #C647CC, #ECC7ED)', // Default gradient
                    'placeholder_letter' => strtoupper(substr($category_name, 0, 1))
                ];
            }
        } catch (Exception $e) {
            error_log("CategoryManager: Failed to load categories from database - " . $e->getMessage());
        }
    }
    
    // Get category image
    public function getCategoryImage($category_id) {
        $stmt = $this->conn->prepare("SELECT product_id FROM products WHERE category_id = ? AND product_image IS NOT NULL LIMIT 1");
        $stmt->bind_param("i", $category_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $product = $result->fetch_assoc();
        
        if ($product) {
            return "display_image.php?id=" . $product['product_id'];
        }
        return null;
    }
    
    // Get all products image
    public function getAllProductsImage() {
        $stmt = $this->conn->prepare("SELECT product_id FROM products WHERE product_image IS NOT NULL ORDER BY RAND() LIMIT 1");
        $stmt->execute();
        $result = $stmt->get_result();
        $product = $result->fetch_assoc();
        
        if ($product) {
            return "display_image.php?id=" . $product['product_id'];
        }
        return null;
    }
    
    // Generate HTML for categories
    public function generateCategoryHTML() {
        $html = "";
        
        // Add "All Products" category first
        $all_products_image = $this->getAllProductsImage();
        
        $html .= "<div class='category-item category-item-all' data-category=''>";
        
        if ($all_products_image) {
            $html .= "<img src='{$all_products_image}' class='category-image' alt='All Products'>";
        } else {
            $html .= "<div class='product-placeholder' style='position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); width: 60px; height: 60px;'>";
            $html .= "ALL";
            $html .= "</div>";
        }
        
        $html .= "<div class='category-label'>All Products</div>";
        $html .= "</div>";
        
        // Add existing categories
        foreach ($this->categories as $id => $category) {
            $image_src = $this->getCategoryImage($id);
            
            $html .= "<div class='category-item category-item-{$id}' data-category='{$id}'>";
            
            if ($image_src) {
                $html .= "<img src='{$image_src}' class='category-image' alt='{$category['name']}'>";
            } else {
                $html .= "<div class='product-placeholder' style='position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); width: 60px; height: 60px;'>";
                $html .= $category['placeholder_letter'];
                $html .= "</div>";
            }
            
            $html .= "<div class='category-label'>{$category['name']}</div>";
            $html .= "</div>";
        }
        return $html;
    }
    
    // Check if category exists
    public function categoryExists($id) {
        return isset($this->categories[$id]);
    }
}

// Initialize the category manager
$categoryManager = new CategoryManager($conn);

// Fetch products from database
function getProducts($category_id = null) {
    global $conn;
    
    if ($category_id) {
        $stmt = $conn->prepare("SELECT p.product_id, p.name, p.description, p.price, p.stock_quantity, c.category_name,
                               CASE WHEN p.product_image IS NOT NULL THEN 1 ELSE 0 END as has_image
                               FROM products p 
                               JOIN categories c ON p.category_id = c.category_id 
                               WHERE p.category_id = ?");
        $stmt->bind_param("i", $category_id);
    } else {
        $stmt = $conn->prepare("SELECT p.product_id, p.name, p.description, p.price, p.stock_quantity, c.category_name,
                               CASE WHEN p.product_image IS NOT NULL THEN 1 ELSE 0 END as has_image
                               FROM products p 
                               JOIN categories c ON p.category_id = c.category_id");
    }
    
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_all(MYSQLI_ASSOC);
}

// Get all products or filter by category
$category_filter = isset($_GET['category']) ? $_GET['category'] : null;

// Validate category exists if filtering
if ($category_filter && !$categoryManager->categoryExists($category_filter)) {
    header("Location: MainPage.php");
    exit();
}

$products = getProducts($category_filter);

// Get cart count for logged in user
$cart_count = 0;
if (isset($_SESSION['user_id'])) {
    $count_stmt = $conn->prepare("
        SELECT SUM(ci.quantity) as total 
        FROM cartitems ci 
        JOIN cart c ON ci.cart_id = c.cart_id 
        WHERE c.user_id = ?
    ");
    $count_stmt->bind_param("i", $_SESSION['user_id']);
    $count_stmt->execute();
    $count_result = $count_stmt->get_result();
    $cart_count = $count_result->fetch_assoc()['total'] ?? 0;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="utf-8" />
   <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
   <title>A&F</title>
   <link href="https://fonts.googleapis.com/css2?family=Merriweather:wght@400;700&family=Merriweather+Sans:wght@700&display=swap" rel="stylesheet" />
   <link rel="stylesheet" href="styles.css">

<style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      background-image: url("https://cdn.glitch.global/585aee42-d89c-4ece-870c-5b01fc1bab61/image%203.png?v=1747320934399");
      background-position: center;
      background-attachment: fixed;
      background-size: cover;
      font-family: 'Merriweather Sans', 'Roboto';
      min-height: 100vh;
      overflow-x: hidden; 
      zoom: 0.8;
    }

    .main-section {
      min-height: 100vh;
      position: relative;
      display: flex;
      flex-direction: column;
      padding: min(20px, 2vw);
    }

    .header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 20px 0;
      position: relative;
      z-index: 10;
      flex-wrap: wrap;
      gap: 10px;
    }

    .brand-name {
      font-size: clamp(2rem, 5vw, 3rem);
      font-weight: bold;
      color: white;
    }

    .nav-items {
      position: absolute;
      left:125px;
      display: flex;
      gap: clamp(15px, 3vw, 30px);
      color: white;
      font-size: clamp(0.8rem, 2vw, 1rem);
    }

    .nav-icons {
      position: fixed;
      right: 20px;
      top: 35px;
      display: flex;
      gap: 15px;
      z-index: 11;
    }
    
    .nav-icon {
      width: 65px !important;
      height: 65px !important;
      background: rgba(255, 255, 255, 0.1);
      border: 1px solid rgba(255, 255, 255, 0.3);
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      cursor: pointer;
      transition: all 0.3s ease;
      backdrop-filter: blur(10px);
      -webkit-backdrop-filter: blur(10px);
      flex-shrink: 0;
    }
    
    .nav-icon:hover {
      background: rgba(255, 255, 255, 0.2);
      transform: scale(1.1);
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
    }
    
    .nav-icon img {
      width: 35px !important;
      height: 35px !important;
      filter: brightness(0) invert(1);
      flex-shrink: 0;
    }
    
    .cart-badge {
      position: absolute;
      top: -5px;
      right: -5px;
      background: #ff4444;
      color: white;
      border-radius: 50%;
      width: 20px;
      height: 20px;
      font-size: 12px;
      font-weight: bold;
      display: flex;
      align-items: center;
      justify-content: center;
      box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
    }

    .search-bar {
      position: relative;
      width: 100%;
      max-width: 400px;
      height: 40px;
      border-radius: 25px;
      background-color: white;
      display: flex;
      align-items: center;
      box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    }

    .search-input {
      width: 100%;
      height: 100%;
      border: none;
      outline: none;
      padding: 0 45px 0 15px;
      border-radius: 25px;
      font-size: 14px;
      background: transparent;
    }

    .search-input::placeholder {
      color: #999;
    }

    .search-btn {
      position: absolute;
      right: 5px;
      width: 30px;
      height: 30px;
      border: none;
      background: #C647CC;
      border-radius: 50%;
      cursor: pointer;
      display: flex;
      align-items: center;
      justify-content: center;
      transition: all 0.3s ease;
    }

    .search-btn:hover {
      background: #a63d9f;
      transform: scale(1.1);
    }

    .search-icon {
      width: 16px;
      height: 16px;
      filter: brightness(0) invert(1);
    }

    .hero-content {
      flex: 1;
      display: flex;
      justify-content: center;
      align-items: center;
      padding: clamp(20px, 4vw, 40px) 0;
      min-height: 400px;
    }

    .hero-left {
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
    }

    .shop-now {
      font-size: clamp(2rem, 6vw, 3rem);
      font-weight: bold;
      color: white;
      text-align: center;
      margin-bottom: 20px;
      z-index: 10;
    }

    /* PRODUCT CAROUSEL STYLES */
    .product-carousel {
        width: 100%;
        max-width: 400px;
        height: 350px;
        background: rgba(255, 255, 255, 0.95);
        border-radius: 15px;
        padding: 15px;
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.2);
        z-index: 2;
        backdrop-filter: blur(10px);
        position: relative;
    }

    .carousel-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 15px;
    }

    .carousel-header h3 {
        color: #C647CC;
        font-size: 16px;
        margin: 0;
        font-weight: bold;
    }

    .carousel-controls {
        display: flex;
        gap: 5px;
    }

    .carousel-btn {
        width: 25px;
        height: 25px;
        border: none;
        background: #C647CC;
        color: white;
        border-radius: 50%;
        cursor: pointer;
        font-size: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.3s ease;
    }

    .carousel-btn:hover {
        background: #a63d9f;
        transform: scale(1.1);
    }

    .carousel-container {
        position: relative;
        height: 200px;
        overflow: hidden;
        border-radius: 10px;
        margin-bottom: 15px;
    }

    .carousel-track {
        display: flex;
        transition: transform 0.5s ease;
        height: 100%;
    }

    .carousel-item {
        min-width: 100%;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: flex-start;
        text-align: center;
        padding: 15px;
        box-sizing: border-box;
    }

    .carousel-product-card {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: flex-start;
        width: 100%;
        height: 100%;
        cursor: pointer;
        transition: transform 0.2s ease;
        gap: 10px;
    }

    .carousel-product-card:hover {
        transform: scale(1.02);
    }

    .carousel-product-image {
        width: 100px;
        height: 100px;
        border-radius: 10px;
        overflow: hidden;
        margin-bottom: 10px;
        flex-shrink: 0;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }

    .carousel-product-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .carousel-placeholder-img {
        width: 100%;
        height: 100%;
        background: linear-gradient(135deg, #C647CC, #ECC7ED);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 28px;
        font-weight: bold;
    }

    .carousel-product-info {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        text-align: center;
        width: 100%;
        flex-grow: 1;
        gap: 5px;
    }

    .carousel-product-info h4 {
        font-size: 14px;
        margin: 0;
        color: #333;
        font-weight: bold;
        max-width: 200px;
        line-height: 1.2;
        overflow: hidden;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
    }

    .carousel-price {
        font-size: 16px;
        font-weight: bold;
        color: #C647CC;
        margin: 5px 0;
    }

    .stock-info {
        font-size: 12px;
        color: #666;
        margin-bottom: 8px;
    }

    .product-actions {
        display: flex;
        gap: 8px;
        margin-top: 8px;
        width: 100%;
        max-width: 200px;
    }

    .carousel-add-btn, .view-details-btn {
        background: #C647CC;
        color: white;
        border: none;
        padding: 6px 12px;
        border-radius: 15px;
        font-size: 11px;
        cursor: pointer;
        transition: all 0.3s ease;
        flex: 1;
        min-width: 0;
        font-weight: 500;
    }

    .carousel-add-btn:hover, .view-details-btn:hover {
        background: #a63d9f;
        transform: scale(1.05);
    }

    .view-details-btn {
        background: #666;
    }

    .view-details-btn:hover {
        background: #555;
    }

    .carousel-indicators {
        display: flex;
        justify-content: center;
        gap: 6px;
        margin-top: 10px;
    }

    .indicator {
        width: 10px;
        height: 10px;
        border-radius: 50%;
        background: rgba(198, 71, 204, 0.3);
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .indicator.active {
        background: #C647CC;
        transform: scale(1.2);
    }

    /* CATEGORIES SECTION STYLES */
    .categories-section {
        padding: 40px 20px;
        background: rgba(0, 0, 0, 0.3);
        backdrop-filter: blur(10px);
        margin: 20px 0;
    }

    .categories-title {
        color: white;
        font-size: clamp(1.8rem, 4vw, 2.5rem);
        text-align: center;
        margin-bottom: 30px;
        font-weight: bold;
    }

    .categories-scroll-container {
        position: relative;
        max-width: 1200px;
        margin: 0 auto;
    }

    .scroll-btn {
        position: absolute;
        top: 50%;
        transform: translateY(-50%);
        width: 40px;
        height: 40px;
        border: none;
        background: rgba(255, 255, 255, 0.2);
        color: white;
        border-radius: 50%;
        cursor: pointer;
        font-size: 18px;
        z-index: 2;
        transition: all 0.3s ease;
        backdrop-filter: blur(10px);
    }

    .scroll-btn:hover {
        background: rgba(255, 255, 255, 0.3);
        transform: translateY(-50%) scale(1.1);
    }

    .scroll-btn:disabled {
        opacity: 0.3;
        cursor: not-allowed;
    }

    .scroll-btn-left {
        left: -20px;
    }

    .scroll-btn-right {
        right: -20px;
    }

    .categories-grid {
        display: flex;
        gap: 20px;
        overflow-x: auto;
        scroll-behavior: smooth;
        padding: 10px 0;
        scrollbar-width: none;
        -ms-overflow-style: none;
    }

    .categories-grid::-webkit-scrollbar {
        display: none;
    }

    .category-item {
        min-width: 120px;
        height: 120px;
        background: linear-gradient(135deg, #C647CC, #ECC7ED);
        border-radius: 15px;
        position: relative;
        cursor: pointer;
        transition: all 0.3s ease;
        overflow: hidden;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
    }

    .category-item:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.3);
    }

    .category-image {
        width: 100%;
        height: 100%;
        object-fit: cover;
        border-radius: 15px;
    }

    .category-label {
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        background: linear-gradient(transparent, rgba(0, 0, 0, 0.8));
        color: white;
        padding: 10px 8px 8px 8px;
        font-size: 12px;
        font-weight: bold;
        text-align: center;
        border-radius: 0 0 15px 15px;
    }

    .product-placeholder {
        background: linear-gradient(135deg, #C647CC, #ECC7ED);
        color: white;
        font-size: 24px;
        font-weight: bold;
        display: flex;
        align-items: center;
        justify-content: center;
        width: 60px;
        height: 60px;
        border-radius: 8px;
    }

    /* PRODUCTS SECTION STYLES */
    .products-section {
        padding: 40px 20px;
        background: rgba(0, 0, 0, 0.2);
        backdrop-filter: blur(5px);
    }

    .products-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 30px;
        flex-wrap: wrap;
        gap: 20px;
    }

    .products-title {
        color: white;
        font-size: clamp(1.8rem, 4vw, 2.5rem);
        font-weight: bold;
    }

    .sort-options {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
    }

    .sort-btn {
        padding: 8px 16px;
        border: none;
        background: rgba(255, 255, 255, 0.1);
        color: white;
        border-radius: 20px;
        cursor: pointer;
        transition: all 0.3s ease;
        font-size: 14px;
    }

    .sort-btn:hover,
    .sort-btn.active {
        background: #C647CC;
        transform: scale(1.05);
    }

    .products-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
        gap: 20px;
        max-width: 1200px;
        margin: 0 auto;
    }

    .product-card {
        background: rgba(255, 255, 255, 0.95);
        border-radius: 15px;
        padding: 15px;
        transition: all 0.3s ease;
        cursor: pointer;
        position: relative;
        overflow: hidden;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    }

    .product-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.2);
    }

    .product-image-container {
        width: 100%;
        height: 200px;
        border-radius: 10px;
        overflow: hidden;
        margin-bottom: 15px;
        position: relative;
    }

    .product-image {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .add-to-cart-btn {
        position: absolute;
        top: 10px;
        right: 10px;
        width: 35px;
        height: 35px;
        border: none;
        background: #C647CC;
        color: white;
        border-radius: 50%;
        cursor: pointer;
        font-size: 18px;
        font-weight: bold;
        transition: all 0.3s ease;
        z-index: 2;
    }

    .add-to-cart-btn:hover {
        background: #a63d9f;
        transform: scale(1.1);
    }

    .product-info {
        text-align: center;
    }

    .product-name {
        font-size: 16px;
        font-weight: bold;
        color: #333;
        margin-bottom: 8px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .product-price {
        font-size: 18px;
        font-weight: bold;
        color: #C647CC;
        margin-bottom: 5px;
    }

    .product-stock {
        font-size: 12px;
        color: #666;
    }

    .product-stock.out-of-stock {
        color: #dc3545;
        font-weight: bold;
    }

    /* PRODUCT MODAL STYLES */
    .product-modal {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.8);
        z-index: 1000;
        backdrop-filter: blur(5px);
    }

    .product-modal.show {
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .modal-content {
        background: white;
        border-radius: 20px;
        padding: 30px;
        max-width: 500px;
        width: 90%;
        max-height: 80vh;
        overflow-y: auto;
        position: relative;
    }

    .modal-header {
        text-align: center;
        margin-bottom: 20px;
        position: relative;
    }

    .close-modal {
        position: absolute;
        top: -10px;
        right: -10px;
        background: #dc3545;
        color: white;
        border: none;
        width: 30px;
        height: 30px;
        border-radius: 50%;
        cursor: pointer;
        font-size: 18px;
    }

    .modal-product-image {
        width: 200px;
        height: 200px;
        object-fit: cover;
        border-radius: 15px;
    }

    .modal-product-placeholder {
        width: 200px;
        height: 200px;
        background: linear-gradient(135deg, #C647CC, #ECC7ED);
        border-radius: 15px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 48px;
        font-weight: bold;
        margin: 0 auto;
    }

    .modal-body {
        text-align: center;
    }

    .modal-product-name {
        font-size: 24px;
        font-weight: bold;
        color: #333;
        margin-bottom: 10px;
    }

    .modal-product-price {
        font-size: 20px;
        font-weight: bold;
        color: #C647CC;
        margin-bottom: 15px;
    }

    .modal-product-description {
        font-size: 14px;
        color: #666;
        margin-bottom: 15px;
        line-height: 1.5;
    }

    .modal-product-stock {
        font-size: 14px;
        margin-bottom: 20px;
    }

    .modal-stock-in {
        color: #28a745;
    }

    .modal-stock-low {
        color: #ffc107;
    }

    .modal-stock-out {
        color: #dc3545;
    }

    .modal-actions {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 20px;
        flex-wrap: wrap;
    }

    .quantity-controls {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .quantity-btn {
        width: 30px;
        height: 30px;
        border: none;
        background: #C647CC;
        color: white;
        border-radius: 50%;
        cursor: pointer;
        font-size: 16px;
    }

    .quantity-display {
        font-size: 18px;
        font-weight: bold;
        min-width: 30px;
        text-align: center;
    }

    .modal-add-to-cart {
        background: linear-gradient(135deg, #C647CC, #ECC7ED);
        color: white;
        border: none;
        padding: 12px 24px;
        border-radius: 25px;
        cursor: pointer;
        font-size: 16px;
        font-weight: bold;
        transition: all 0.3s ease;
    }

    .modal-add-to-cart:hover {
        transform: scale(1.05);
    }

    /* Mobile responsive adjustments */
    @media (max-width: 768px) {
        .header {
            flex-direction: column;
            gap: 15px;
        }

        .search-bar {
            max-width: 100%;
            order: 3;
        }

        .hero-content {
            justify-content: center;
            padding: clamp(20px, 4vw, 40px) 20px;
        }

        .hero-left {
            align-items: center;
        }

        .product-carousel {
            max-width: 100%;
            height: 280px;
        }

        .carousel-container {
            height: 160px;
        }

        .carousel-product-image {
            width: 80px;
            height: 80px;
        }

        .carousel-product-info h4 {
            font-size: 12px;
        }

        .carousel-price {
            font-size: 14px;
        }

        .shop-now {
            text-align: center;
            margin-bottom: 20px;
        }

        .categories-grid {
            padding: 10px 20px;
        }

        .products-grid {
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 15px;
        }

        .products-header {
            flex-direction: column;
            text-align: center;
        }
    }
    /* Add notification animations */
    @keyframes slideInRight {
        from {
            transform: translateX(100%);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }

    @keyframes slideOutRight {
        from {
            transform: translateX(0);
            opacity: 1;
        }
        to {
            transform: translateX(100%);
            opacity: 0;
        }
    }

    /* Active category styling */
    .category-item.active {
        transform: scale(1.1);
        box-shadow: 0 8px 25px rgba(198, 71, 204, 0.5);
    }

    /* Product card hover improvements */
    .product-card {
        cursor: pointer;
    }

    .product-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.2);
    }
</style>
</head>

<body>
<div class="main-section">
  <!-- HEADER -->
  <div class="header">
    <div class="brand-name">A&F</div>
    <div class="nav-items">
      <span onclick="window.location.href='Welcome.php'" style="cursor: pointer;">Home</span>
      <span>About</span>
      <span onclick="window.location.href='Contact.php'" style="cursor: pointer;">Contact</span>
    </div>
    
    <!-- NAVIGATION ICONS -->
    <div class="nav-icons">
      <!-- Cart Icon -->
      <div class="nav-icon" onclick="goToCart()" title="Shopping Cart">
        <img src="images/cart.png" alt="Cart">
        <?php if ($cart_count > 0): ?>
          <span class="cart-badge"><?php echo $cart_count; ?></span>
        <?php endif; ?>
      </div>
      
      <!-- Order History Icon -->
      <div class="nav-icon" onclick="goToOrderHistory()" title="Order History">
        <img src="images/history.png" alt="Orders">
      </div>
      
      <!-- User Profile Icon -->
      <div class="nav-icon" onclick="goToProfile()" title="Profile">
        <img src="images/users.png" alt="Profile">
      </div>
    </div>
  </div>

  <!-- SEARCH BAR -->
  <div class="search-bar">
    <input type="text" class="search-input" placeholder="Search products..." id="searchInput">
    <button class="search-btn" id="searchButton">
      <img src="https://cdn.glitch.global/585aee42-d89c-4ece-870c-5b01fc1bab61/search.png?v=1747320935333" alt="Search" class="search-icon">
    </button>
  </div>

  <!-- HERO SECTION -->
  <div class="hero-content">
    <div class="hero-left">
      <div class="shop-now">Shop now</div>
      <!-- Replace pistachio image with product carousel -->
      <?php echo generateProductCarousel($conn); ?>
    </div>
  </div>
</div>

<!-- CATEGORIES with Horizontal Scroll -->
<div class="categories-section">
  <h2 class="categories-title">Categories</h2>
  
  <div class="categories-scroll-container">
    <!-- Left Scroll Button -->
    <button class="scroll-btn scroll-btn-left" id="scrollLeft" onclick="scrollCategories('left')">
      &#8249;
    </button>
    
    <!-- Scrollable Categories -->
    <div class="categories-grid" id="categoriesGrid">
      <?php echo $categoryManager->generateCategoryHTML(); ?>
    </div>
    
    <!-- Right Scroll Button -->
    <button class="scroll-btn scroll-btn-right" id="scrollRight" onclick="scrollCategories('right')">
      &#8250;
    </button>
  </div>
</div>

<!-- PRODUCTS SECTION -->
<div class="products-section">
  <div class="products-header">
    <h2 class="products-title">
      <?php 
        if ($category_filter) {
          $stmt = $conn->prepare("SELECT category_name FROM categories WHERE category_id = ?");
          $stmt->bind_param("i", $category_filter);
          $stmt->execute();
          $result = $stmt->get_result();
          $category = $result->fetch_assoc();
          echo $category ? $category['category_name'] : 'Products';
        } else {
          echo 'All Products';
        }
      ?>
    </h2>
    
    <div class="sort-options">
      <button class="sort-btn active" onclick="sortProducts('popular', this)">Popular</button>
      <button class="sort-btn" onclick="sortProducts('price_low', this)">Price: Low to High</button>
      <button class="sort-btn" onclick="sortProducts('price_high', this)">Price: High to Low</button>
    </div>
  </div>
  
  <div class="products-grid" id="productsGrid">
    <?php foreach ($products as $product): ?>
      <div class="product-card" data-product-id="<?php echo $product['product_id']; ?>" 
           data-price="<?php echo $product['price']; ?>" 
           data-name="<?php echo htmlspecialchars($product['name']); ?>">
      
        <div class="product-image-container">
          <?php if ($product['has_image']): ?>
            <img src="display_image.php?id=<?php echo $product['product_id']; ?>" 
                 alt="<?php echo htmlspecialchars($product['name']); ?>" 
                 class="product-image"
                 loading="lazy">
          <?php else: ?>
            <div class="product-placeholder">
              <?php echo strtoupper(substr($product['name'], 0, 1)); ?>
            </div>
          <?php endif; ?>
        </div>

        <button class="add-to-cart-btn" onclick="addToCart(<?php echo $product['product_id']; ?>, event)" title="Add to Cart">
          +
        </button>

        <div class="product-info">
          <div class="product-name" title="<?php echo htmlspecialchars($product['name']); ?>">
            <?php echo htmlspecialchars($product['name']); ?>
          </div>
          <div class="product-price">
            $<?php echo number_format($product['price'], 2); ?>
          </div>
          <div class="product-stock <?php echo $product['stock_quantity'] <= 0 ? 'out-of-stock' : ''; ?>">
            <?php 
              if ($product['stock_quantity'] <= 0) {
                echo 'Out of Stock';
              } elseif ($product['stock_quantity'] <= 10) {
                echo 'Only ' . $product['stock_quantity'] . ' left';
              } else {
                echo 'In Stock';
              }
            ?>
          </div>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
</div>

<!-- Product Modal -->
<div id="productModal" class="product-modal">
  <div class="modal-content">
    <div class="modal-header">
      <button class="close-modal" onclick="closeProductModal()">&times;</button>
      <div id="modalImageContainer">
        <!-- Product image will be inserted here -->
      </div>
    </div>
    
    <div class="modal-body">
      <h2 id="modalProductName" class="modal-product-name"></h2>
      <div id="modalProductPrice" class="modal-product-price"></div>
      <div id="modalProductDescription" class="modal-product-description"></div>
      <div id="modalProductStock" class="modal-product-stock"></div>
      
      <div class="modal-actions">
        <div class="quantity-controls">
          <button class="quantity-btn" onclick="changeQuantity(-1)">-</button>
          <span id="modalQuantity" class="quantity-display">1</span>
          <button class="quantity-btn" onclick="changeQuantity(1)">+</button>
        </div>
        
        <button id="modalAddToCart" class="modal-add-to-cart" onclick="addToCartFromModal()">
          <span>Add to Cart</span>
        </button>
      </div>
    </div>
  </div>
</div>

<script>
// Product Modal Variables
let currentProduct = null;
let modalQuantity = 1;

// Categories click functionality
document.addEventListener('DOMContentLoaded', function() {
    initCategoriesScroll();
    initCategoryClicks();
    initProductClicks();
    initNavigationButtons();
    initSearchFunctionality();
});

function initNavigationButtons() {
    // Profile button
    const profileBtn = document.querySelector('.nav-icon[title="Profile"]');
    if (profileBtn) {
        profileBtn.addEventListener('click', goToProfile);
    }
    
    // Cart button
    const cartBtn = document.querySelector('.nav-icon[title="Cart"]');
    if (cartBtn) {
        cartBtn.addEventListener('click', goToCart);
    }
}

function initSearchFunctionality() {
    const searchInput = document.getElementById('searchInput');
    const searchButton = document.getElementById('searchButton');
    
    if (searchInput) {
        searchInput.addEventListener('keypress', handleSearchKeypress);
        searchInput.addEventListener('input', handleSearchInput);
    }
    
    if (searchButton) {
        searchButton.addEventListener('click', performSearch);
    }
}

function initCategoryClicks() {
    const categoryItems = document.querySelectorAll('.category-item');
    categoryItems.forEach(item => {
        item.addEventListener('click', function() {
            const categoryId = this.dataset.category;
            console.log('Category clicked:', categoryId);
            
            // Remove active class from all categories
            categoryItems.forEach(cat => cat.classList.remove('active'));
            // Add active class to clicked category
            this.classList.add('active');
            
            // Filter products
            filterByCategory(categoryId);
        });
    });
}

function initProductClicks() {
    console.log('Initializing product clicks...');
    
    // Use event delegation on the products grid
    const productsGrid = document.getElementById('productsGrid');
    if (productsGrid) {
        productsGrid.addEventListener('click', function(e) {
            const productCard = e.target.closest('.product-card');
            const addToCartBtn = e.target.closest('.add-to-cart-btn');
            
            console.log('Click detected:', {
                target: e.target,
                productCard: productCard,
                addToCartBtn: addToCartBtn
            });
            
            // Only open modal if clicking on product card but NOT on add to cart button
            if (productCard && !addToCartBtn) {
                const productId = productCard.getAttribute('data-product-id');
                console.log('Opening modal for product ID:', productId);
                
                if (productId) {
                    openProductModal(productId);
                } else {
                    console.error('No product ID found on card');
                }
            }
        });
    } else {
        console.error('Products grid not found');
    }
    
    // Add event delegation for CAROUSEL items
    const carouselContainer = document.querySelector('.product-carousel');
    if (carouselContainer) {
        carouselContainer.addEventListener('click', function(e) {
            const carouselItem = e.target.closest('.carousel-item');
            const addToCartBtn = e.target.closest('.carousel-add-btn');
            const viewDetailsBtn = e.target.closest('.view-details-btn');
            
            console.log('Carousel click detected:', {
                target: e.target,
                carouselItem: carouselItem,
                addToCartBtn: addToCartBtn,
                viewDetailsBtn: viewDetailsBtn
            });
            
            // Open modal if clicking view details button OR clicking on carousel item (but not add to cart)
            if (viewDetailsBtn || (carouselItem && !addToCartBtn)) {
                const productId = carouselItem ? carouselItem.getAttribute('data-product-id') : null;
                console.log('Opening modal for carousel product ID:', productId);
                
                if (productId) {
                    openProductModal(productId);
                } else {
                    console.error('No product ID found on carousel item');
                }
            }
        });
        console.log('✓ Carousel event listener added');
    } else {
        console.error('Carousel container not found');
    }
}

// Categories scroll functionality
function scrollCategories(direction) {
    const grid = document.getElementById('categoriesGrid');
    if (!grid) return;
    
    const scrollAmount = 300;
    
    if (direction === 'left') {
        grid.scrollLeft -= scrollAmount;
    } else {
        grid.scrollLeft += scrollAmount;
    }
    
    setTimeout(updateScrollButtons, 100);
}

function updateScrollButtons() {
    const grid = document.getElementById('categoriesGrid');
    const leftBtn = document.getElementById('scrollLeft');
    const rightBtn = document.getElementById('scrollRight');
    
    if (!grid || !leftBtn || !rightBtn) return;
    
    leftBtn.disabled = grid.scrollLeft <= 0;
    
    const maxScroll = grid.scrollWidth - grid.clientWidth;
    rightBtn.disabled = grid.scrollLeft >= maxScroll - 1;
}

function initCategoriesScroll() {
    const grid = document.getElementById('categoriesGrid');
    if (!grid) return;
    
    updateScrollButtons();
    
    let scrollTimeout;
    grid.addEventListener('scroll', function() {
        if (scrollTimeout) {
            clearTimeout(scrollTimeout);
        }
        scrollTimeout = setTimeout(updateScrollButtons, 50);
    });
}

// Search functionality
let searchTimeout;

function handleSearchKeypress(event) {
    if (event.key === 'Enter') {
        event.preventDefault();
        performSearch();
    }
}

function handleSearchInput() {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        const searchTerm = document.getElementById('searchInput').value.trim();
        if (searchTerm.length >= 2 || searchTerm.length === 0) {
            performSearch();
        }
    }, 300);
}

function performSearch() {
    const searchInput = document.getElementById('searchInput');
    const searchTerm = searchInput.value.trim();
    
    const grid = document.getElementById('productsGrid');
    const productsTitle = document.querySelector('.products-title');
    
    if (!grid) return;
    
    grid.style.opacity = '0.5';
    grid.style.pointerEvents = 'none';
    
    // Clear active category selection
    document.querySelectorAll('.category-item').forEach(cat => cat.classList.remove('active'));
    
    fetch('search_products.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            search_term: searchTerm
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            updateProductsGrid(data.products);
            
            // Update products section title
            if (productsTitle) {
                if (searchTerm) {
                    productsTitle.textContent = `Search results for "${searchTerm}"`;
                } else {
                    productsTitle.textContent = 'All Products';
                }
            }
            
            // Update URL
            const url = new URL(window.location);
            if (searchTerm) {
                url.searchParams.set('search', searchTerm);
                url.searchParams.delete('category');
            } else {
                url.searchParams.delete('search');
            }
            window.history.pushState({}, '', url);
            
            if (searchTerm) {
                showNotification(`Found ${data.products.length} products for "${searchTerm}"`, 'success');
            } else {
                showNotification('Showing all products', 'success');
            }
        } else {
            showNotification('Search failed. Please try again.', 'error');
        }
    })
    .catch(error => {
        console.error('Search error:', error);
        showNotification('Search failed. Please try again.', 'error');
    })
    .finally(() => {
        grid.style.opacity = '1';
        grid.style.pointerEvents = 'auto';
    });
}

// AJAX: Add to cart without page reload
function addToCart(productId, event) {
    if (event) {
        event.stopPropagation();
        event.preventDefault();
    }
    
    <?php if (!isset($_SESSION['user_id'])): ?>
        showNotification('Please login to add items to cart', 'error');
        setTimeout(() => {
            window.location.href = 'login.php';
        }, 1500);
        return;
    <?php endif; ?>
    
    const button = event ? event.target : null;
    if (!button) return;
    
    const originalText = button.innerHTML;
    button.innerHTML = '⏳';
    button.disabled = true;
    
    fetch('add_to_cart.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            product_id: productId,
            quantity: 1
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            button.innerHTML = '✓';
            button.style.background = '#28a745';
            showNotification('Product added to cart!', 'success');
            updateCartCountAjax();
            
            setTimeout(() => {
                button.innerHTML = originalText;
                button.style.background = '#C647CC';
                button.disabled = false;
            }, 1500);
        } else {
            button.innerHTML = '✗';
            button.style.background = '#dc3545';
            showNotification(data.message || 'Error adding product to cart', 'error');
            
            setTimeout(() => {
                button.innerHTML = originalText;
                button.style.background = '#C647CC';
                button.disabled = false;
            }, 1500);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        button.innerHTML = '✗';
        button.style.background = '#dc3545';
        showNotification('Network error. Please try again.', 'error');
        
        setTimeout(() => {
            button.innerHTML = originalText;
            button.style.background = '#C647CC';
            button.disabled = false;
        }, 1500);
    });
}

// AJAX: Update cart count
function updateCartCountAjax() {
    fetch('get_cart_count.php')
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const cartBadge = document.querySelector('.cart-badge');
            const cartIcon = document.querySelector('.nav-icon[title="Cart"]');
            
            if (!cartIcon) return;
            
            if (data.count > 0) {
                if (cartBadge) {
                    cartBadge.textContent = data.count;
                    cartBadge.style.transform = 'scale(1.3)';
                    setTimeout(() => {
                        cartBadge.style.transform = 'scale(1)';
                    }, 200);
                } else {
                    const newBadge = document.createElement('span');
                    newBadge.className = 'cart-badge';
                    newBadge.textContent = data.count;
                    cartIcon.appendChild(newBadge);
                }
            } else {
                if (cartBadge) {
                    cartBadge.remove();
                }
            }
        }
    })
    .catch(error => console.error('Error updating cart count:', error));
}

// Product sorting
function sortProducts(sortType, buttonElement) {
    if (!buttonElement) return;
    
    document.querySelectorAll('.sort-btn').forEach(btn => {
        btn.classList.remove('active');
    });
    buttonElement.classList.add('active');
    
    const grid = document.getElementById('productsGrid');
    if (!grid) return;
    
    grid.style.opacity = '0.5';
    
    const products = Array.from(grid.children);

    products.sort((a, b) => {
        switch(sortType) {
            case 'price_low':
                return parseFloat(a.dataset.price || 0) - parseFloat(b.dataset.price || 0);
            case 'price_high':
                return parseFloat(b.dataset.price || 0) - parseFloat(a.dataset.price || 0);
            case 'popular':
            default:
                return (a.dataset.name || '').localeCompare(b.dataset.name || '');
        }
    });

    grid.innerHTML = '';
    products.forEach(product => grid.appendChild(product));
    
    setTimeout(() => {
        grid.style.opacity = '1';
    }, 200);
    
    showNotification(`Sorted by ${sortType.replace('_', ' ')}`, 'success');
}

// AJAX: Category filtering
function filterByCategory(categoryId) {
    console.log('filterByCategory called with:', categoryId);
    
    const grid = document.getElementById('productsGrid');
    const productsTitle = document.querySelector('.products-title');
    
    if (!grid) return;
    
    grid.style.opacity = '0.5';
    grid.style.pointerEvents = 'none';
    
    const requestData = {
        category: categoryId || null
    };
    
    fetch('filter_products.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(requestData)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            updateProductsGrid(data.products);
            
            // Update products section title
            if (productsTitle) {
                productsTitle.textContent = data.category_name || 'All Products';
            }
            
            const url = new URL(window.location);
            if (categoryId && categoryId !== '') {
                url.searchParams.set('category', categoryId);
            } else {
                url.searchParams.delete('category');
            }
            window.history.pushState({}, '', url);
            
            showNotification(`Showing ${data.category_name || 'All Products'}`, 'success');
        } else {
            showNotification('Error filtering products', 'error');
        }
    })
    .catch(error => {
        console.error('Filter error:', error);
        showNotification('Filter failed. Please try again.', 'error');
    })
    .finally(() => {
        grid.style.opacity = '1';
        grid.style.pointerEvents = 'auto';
    });
}

// Update products grid
function updateProductsGrid(products) {
    const grid = document.getElementById('productsGrid');
    if (!grid) return;
    
    if (products.length === 0) {
        grid.innerHTML = `
            <div style="grid-column: 1/-1; text-align: center; color: white; font-size: 18px; padding: 40px;">
                No products found in this category.
            </div>
        `;
        return;
    }
    
    let html = '';
    products.forEach(product => {
        const productName = escapeHtml(product.name || '');
        const productPrice = parseFloat(product.price || 0);
        
        html += `
            <div class="product-card" data-product-id="${product.product_id}" 
                 data-price="${productPrice}" 
                 data-name="${productName}">
            
                <div class="product-image-container">
                    ${product.has_image ? 
                        `<img src="display_image.php?id=${product.product_id}" 
                             alt="${productName}" 
                             class="product-image"
                             loading="lazy">` :
                        `<div class="product-placeholder">
                            ${productName.charAt(0).toUpperCase()}
                        </div>`
                    }
                </div>

                <button class="add-to-cart-btn" onclick="addToCart(${product.product_id}, event)" title="Add to Cart">
                    +
                </button>

                <div class="product-info">
                  <div class="product-name" title="${productName}">
                      ${productName}
                  </div>
                  <div class="product-price">
                      $${productPrice.toFixed(2)}
                  </div>
                  <div class="product-stock ${product.stock_quantity <= 0 ? 'out-of-stock' : ''}">
                      ${product.stock_quantity <= 0 ? 'Out of Stock' : product.stock_quantity <= 10 ? 'Only ' + product.stock_quantity + ' left' : 'In Stock'}
                  </div>
                </div>
            </div>
        `;
    });
    
    grid.innerHTML = html;
}

// Product Modal Functions
function openProductModal(productId) {
    console.log('=== MODAL DEBUG START ===');
    console.log('1. Product ID received:', productId);
    
    const modal = document.getElementById('productModal');
    if (!modal) {
        console.error('❌ Modal not found');
        return;
    }
    console.log('2. ✓ Modal found');
    
    // Reset modal add-to-cart button state FIRST
    const modalAddToCartBtn = document.getElementById('modalAddToCart');
    if (modalAddToCartBtn) {
        modalAddToCartBtn.innerHTML = '<span>Add to Cart</span>';
        modalAddToCartBtn.style.background = 'linear-gradient(135deg, #C647CC, #ECC7ED)';
        modalAddToCartBtn.disabled = false;
        console.log('✓ Modal button reset');
    }
    
    // Show modal
    modal.classList.add('show');
    document.body.style.overflow = 'hidden';
    console.log('3. ✓ Modal shown');
    
    // Find the product card (could be carousel item or regular product card)
    const productCard = document.querySelector(`[data-product-id="${productId}"]`);
    console.log('4. Product card query result:', productCard);
    
    if (!productCard) {
        console.error('❌ Product card not found for ID:', productId);
        return;
    }
    
    // Debug: log the HTML structure
    console.log('Product card HTML:', productCard.innerHTML);
    
    // Try different selectors for carousel vs regular product cards
    let productNameElement, productPriceElement, productStockElement, productImageElement;
    
    // Check if this is a carousel item
    if (productCard.classList.contains('carousel-item')) {
        console.log('5. Detected: CAROUSEL ITEM');
        productNameElement = productCard.querySelector('.product-name') || productCard.querySelector('h4');
        productPriceElement = productCard.querySelector('.carousel-price');
        productStockElement = productCard.querySelector('.stock-info');
        productImageElement = productCard.querySelector('.carousel-product-image img') || productCard.querySelector('img');
    } else {
        console.log('5. Detected: REGULAR PRODUCT CARD');
        productNameElement = productCard.querySelector('.product-name');
        productPriceElement = productCard.querySelector('.product-price');
        productStockElement = productCard.querySelector('.product-stock');
        productImageElement = productCard.querySelector('.product-image');
    }
    
    console.log('6. Found elements:', {
        nameElement: !!productNameElement,
        priceElement: !!productPriceElement,
        stockElement: !!productStockElement,
        imageElement: !!productImageElement
    });
    
    // Extract data from the visible text
    const productName = productNameElement ? productNameElement.textContent.trim() : 'Unknown Product';
    const priceText = productPriceElement ? productPriceElement.textContent.trim() : '$0.00';
    const productPrice = parseFloat(priceText.replace('$', '') || 0);
    
    // Handle stock text differently for carousel vs regular cards
    let stockText = 'In Stock';
    if (productStockElement) {
        const stockRawText = productStockElement.textContent.trim();
        if (stockRawText.includes('Stock:')) {
            // Carousel format: "Stock: 150"
            const stockNumber = parseInt(stockRawText.replace('Stock:', '').trim());
            if (stockNumber <= 0) {
                stockText = 'Out of Stock';
            } else if (stockNumber <= 10) {
                stockText = `Only ${stockNumber} left`;
            } else {
                stockText = 'In Stock';
            }
        } else {
            // Regular card format: "In Stock", "Out of Stock", etc.
            stockText = stockRawText;
        }
    }
    
    const imageSrc = productImageElement ? productImageElement.src : null;
    
    console.log('7. Extracted data:', {
        name: productName,
        priceText: priceText,
        price: productPrice,
        stockText: stockText,
        imageSrc: imageSrc
    });
    
    // Get modal elements
    const modalName = document.getElementById('modalProductName');
    const modalPrice = document.getElementById('modalProductPrice');
    const modalStock = document.getElementById('modalProductStock');
    const imageContainer = document.getElementById('modalImageContainer');
    const modalQuantityDisplay = document.getElementById('modalQuantity');
    
    console.log('8. Modal elements found:', {
        modalName: !!modalName,
        modalPrice: !!modalPrice,
        modalStock: !!modalStock,
        imageContainer: !!imageContainer
    });
    
    // Set the content
    if (modalName) {
        modalName.innerHTML = '';
        modalName.textContent = productName;
        console.log('9. ✓ Name set to:', modalName.textContent);
    }
    
    if (modalPrice) {
        modalPrice.innerHTML = '';
        modalPrice.textContent = priceText;
        console.log('10. ✓ Price set to:', modalPrice.textContent);
    }
    
    // Set stock status
    if (modalStock) {
        modalStock.textContent = stockText;
        if (stockText.includes('Out of Stock')) {
            modalStock.className = 'modal-product-stock modal-stock-out';
        } else if (stockText.includes('Only')) {
            modalStock.className = 'modal-product-stock modal-stock-low';
        } else {
            modalStock.className = 'modal-product-stock modal-stock-in';
        }
        console.log('11. ✓ Stock set to:', stockText);
    }
    
    // Set product image
    if (imageContainer) {
        if (imageSrc) {
            imageContainer.innerHTML = `
                <img src="${imageSrc}" 
                     alt="${productName}" 
                     style="width: 200px; height: 200px; object-fit: cover; border-radius: 15px; margin: 0 auto; display: block;">
            `;
            console.log('12. ✓ Real image set');
        } else {
            imageContainer.innerHTML = `
                <div style="width: 200px; height: 200px; background: linear-gradient(135deg, #C647CC, #ECC7ED); border-radius: 15px; display: flex; align-items: center; justify-content: center; color: white; font-size: 48px; font-weight: bold; margin: 0 auto;">
                    ${productName.charAt(0).toUpperCase() || 'P'}
                </div>
            `;
            console.log('12. ✓ Placeholder image set');
        }
    }
    
    // Reset quantity
    if (modalQuantityDisplay) {
        modalQuantityDisplay.textContent = '1';
    }
    
    // Set current product data
    currentProduct = {
        id: productId,
        name: productName,
        price: productPrice,
        stock: stockText
    };
    
    modalQuantity = 1;
    
    // Fetch description
    fetchProductDescription(productId);
    
    console.log('=== MODAL DEBUG END ===');
}

function closeProductModal() {
    console.log('Closing modal...');
    const modal = document.getElementById('productModal');
    if (modal) {
        modal.classList.remove('show');
        document.body.style.overflow = 'auto';
        console.log('Modal closed');
    }
    
    // Reset modal add-to-cart button state when closing
    const modalAddToCartBtn = document.getElementById('modalAddToCart');
    if (modalAddToCartBtn) {
        modalAddToCartBtn.innerHTML = '<span>Add to Cart</span>';
        modalAddToCartBtn.style.background = 'linear-gradient(135deg, #C647CC, #ECC7ED)';
        modalAddToCartBtn.disabled = false;
    }
    
    currentProduct = null;
    modalQuantity = 1;
    const quantityDisplay = document.getElementById('modalQuantity');
    if (quantityDisplay) {
        quantityDisplay.textContent = '1';
    }
}

function changeQuantity(change) {
    const newQuantity = modalQuantity + change;
    if (newQuantity >= 1 && newQuantity <= 99) {
        modalQuantity = newQuantity;
        const quantityDisplay = document.getElementById('modalQuantity');
        if (quantityDisplay) {
            quantityDisplay.textContent = modalQuantity;
        }
    }
}

function addToCartFromModal() {
    if (!currentProduct) return;
    
    <?php if (!isset($_SESSION['user_id'])): ?>
        showNotification('Please login to add items to cart', 'error');
        setTimeout(() => {
            window.location.href = 'login.php';
        }, 1500);
        return;
    <?php endif; ?>
    
    const button = document.getElementById('modalAddToCart');
    if (!button) return;
    
    const originalText = button.innerHTML;
    button.innerHTML = '⏳ Adding...';
    button.disabled = true;
    
    fetch('add_to_cart.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            product_id: currentProduct.id,
            quantity: modalQuantity
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            button.innerHTML = '✓ Added to Cart!';
            button.style.background = '#28a745';
            showNotification(`${modalQuantity} item(s) added to cart!`, 'success');
            updateCartCountAjax();
            
            setTimeout(() => {
                closeProductModal();
            }, 1000);
        } else {
            button.innerHTML = '✗ Error';
            button.style.background = '#dc3545';
            showNotification(data.message || 'Error adding product to cart', 'error');
            
            setTimeout(() => {
                button.innerHTML = originalText;
                button.style.background = 'linear-gradient(135deg, #C647CC, #ECC7ED)';
                button.disabled = false;
            }, 2000);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        button.innerHTML = '✗ Network Error';
        button.style.background = '#dc3545';
        showNotification('Network error. Please try again.', 'error');
        
        setTimeout(() => {
            button.innerHTML = originalText;
            button.style.background = 'linear-gradient(135deg, #C647CC, #ECC7ED)';
            button.disabled = false;
        }, 2000);
    });
}

function fetchProductDescription(productId) {
    console.log('Fetching description for product ID:', productId);
    
    const descElement = document.getElementById('modalProductDescription');
    if (descElement) {
        descElement.textContent = 'Loading description...';
    }
    
    fetch(`get_product_details.php?id=${productId}`)
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        console.log('Description fetch successful:', data);
        if (data.success && data.product && descElement) {
            const description = data.product.description || 'No description available.';
            descElement.textContent = description;
            console.log('✓ Description set to:', description);
        } else if (descElement) {
            descElement.textContent = 'Description not available.';
        }
    })
    .catch(error => {
        console.error('Description fetch error:', error);
        if (descElement) {
            descElement.textContent = 'Error loading description.';
        }
    });
}

// Navigation functions
function goToProfile() {
    <?php if (isset($_SESSION['user_id'])): ?>
        window.location.href = 'Userdashboard.php';
    <?php else: ?>
        window.location.href = 'Welcome.php';
    <?php endif; ?>
}

function goToCart() {
    <?php if (isset($_SESSION['user_id'])): ?>
        window.location.href = 'Cart.php';
    <?php else: ?>
        showNotification('Please login to view cart', 'error');
        setTimeout(() => {
            window.location.href = 'Welcome.php';
        }, 1500);
    <?php endif; ?>
}

function goToOrderHistory() {
    <?php if (isset($_SESSION['user_id'])): ?>
        window.location.href = 'order_history.php';
    <?php else: ?>
        showNotification('Please login to view order history', 'error');
        setTimeout(() => {
            window.location.href = 'Welcome.php';
        }, 1500);
    <?php endif; ?>
}

// Carousel functions
let currentSlide = 0;

function moveCarousel(direction) {
    const track = document.getElementById('carouselTrack');
    const items = track.querySelectorAll('.carousel-item');
    const totalItems = items.length;
    
    if (totalItems === 0) return;
    
    currentSlide = (currentSlide + direction + totalItems) % totalItems;
    
    const translateX = -currentSlide * 100;
    track.style.transform = `translateX(${translateX}%)`;
    
    updateIndicators();
}

function goToSlide(index) {
    const track = document.getElementById('carouselTrack');
    const items = track.querySelectorAll('.carousel-item');
    
    if (index >= 0 && index < items.length) {
        currentSlide = index;
        const translateX = -currentSlide * 100;
        track.style.transform = `translateX(${translateX}%)`;
        updateIndicators();
    }
}

function updateIndicators() {
    const indicators = document.querySelectorAll('.indicator');
    indicators.forEach((indicator, index) => {
        indicator.classList.toggle('active', index === currentSlide);
    });
}

function viewProduct(productId) {
    openProductModal(productId);
}

// Utility functions
function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    notification.className = `notification notification-${type}`;
    notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        background: ${type === 'success' ? '#28a745' : type === 'error' ? '#dc3545' : '#17a2b8'};
        color: white;
        padding: 15px 20px;
        border-radius: 5px;
        z-index: 10000;
        font-weight: bold;
        box-shadow: 0 4px 12px rgba(0,0,0,0.3);
        animation: slideInRight 0.3s ease;
    `;
    notification.textContent = message;
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.style.animation = 'slideOutRight 0.3s ease';
        setTimeout(() => {
            document.body.removeChild(notification);
        }, 300);
    }, 3000);
}

// Close modal when clicking outside
document.addEventListener('click', function(e) {
    const modal = document.getElementById('productModal');
    if (modal && e.target === modal) {
        closeProductModal();
    }
});

// Close modal with Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeProductModal();
    }
});

</script>
</body>
</html>