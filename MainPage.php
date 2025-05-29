<?php
// Add this at the top of your MainPage.php
require 'db_connect.php';

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
            // Get all categories from database
            $stmt = $this->conn->prepare("SELECT category_id, category_name FROM categories ORDER BY category_id");
            $stmt->execute();
            $result = $stmt->get_result();
            
            $gradients = [
                1 => 'linear-gradient(to bottom, #55361A, #CDACB1)',
                2 => 'linear-gradient(to bottom, #BFB886, #F3E794)', 
                3 => 'linear-gradient(to bottom, #D97272, #F8DDDD)',
                4 => 'linear-gradient(to bottom, #71EEEC, #C1ACAC)',
                5 => 'linear-gradient(to bottom, #6B6060, #7C6F6F, #8D7D7D)',
                // Add more gradients as needed for new categories
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
            // Fallback if database fails
            error_log("CategoryManager: Failed to load categories from database - " . $e->getMessage());
            $this->initializeFallbackCategories();
        }
    }
    
    // Fallback categories if database is unavailable
    private function initializeFallbackCategories() {
        $this->categories = [
            1 => [
                'name' => 'Chocolate Bars',
                'gradient' => 'linear-gradient(to bottom, #55361A, #CDACB1)',
                'placeholder_letter' => 'C'
            ],
            2 => [
                'name' => 'Truffles',
                'gradient' => 'linear-gradient(to bottom, #BFB886, #F3E794)',
                'placeholder_letter' => 'T'
            ]
        ];
    }
    
    // Get all categories
    public function getAllCategories() {
        return $this->categories;
    }
    
    // Get specific category
    public function getCategory($id) {
        return isset($this->categories[$id]) ? $this->categories[$id] : null;
    }
    
    // Check if category exists
    public function categoryExists($id) {
        return isset($this->categories[$id]);
    }
    
    // Get sample product image for category
    public function getCategoryImage($category_id) {
        $stmt = $this->conn->prepare("SELECT product_id FROM products WHERE category_id = ? AND product_image IS NOT NULL LIMIT 1");
        $stmt->bind_param("i", $category_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $product = $result->fetch_assoc();
        
        if ($product) {
            return "display_image.php?id=" . $product['product_id'];
        }
        
        return null; // No image found, will use placeholder
    }
    
    // Get a random product image for "All Products" category
    public function getAllProductsImage() {
        $stmt = $this->conn->prepare("SELECT product_id FROM products WHERE product_image IS NOT NULL ORDER BY RAND() LIMIT 1");
        $stmt->execute();
        $result = $stmt->get_result();
        $product = $result->fetch_assoc();
        
        if ($product) {
            return "display_image.php?id=" . $product['product_id'];
        }
        
        return null; // No image found, will use placeholder
    }
    
    // Generate CSS for all categories including "All Products"
    public function generateCategoryCSS() {
        $css = "";
        
        // Add "All Products" category CSS
        $css .= ".category-item-all {\n";
        $css .= "  background: linear-gradient(white, white) padding-box,\n";
        $css .= "              linear-gradient(45deg, #FF6B6B, #4ECDC4, #45B7D1, #96CEB4, #FFEAA7, #DDA0DD) border-box;\n";
        $css .= "}\n\n";
        
        // Add existing categories CSS
        foreach ($this->categories as $id => $category) {
            $css .= ".category-item-{$id} {\n";
            $css .= "  background: linear-gradient(white, white) padding-box,\n";
            $css .= "              {$category['gradient']} border-box;\n";
            $css .= "}\n\n";
        }
        return $css;
    }
    
    // Generate HTML for all categories including "All Products"
    public function generateCategoryHTML() {
        $html = "";
        
        // Add "All Products" category first
        $all_products_image = $this->getAllProductsImage();
        
        $html .= "<a href='MainPage.php' class='category-item category-item-all'>";
        
        if ($all_products_image) {
            $html .= "<img src='{$all_products_image}' class='category-image' alt='All Products'>";
        } else {
            $html .= "<div class='product-placeholder' style='position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); width: 60px; height: 60px;'>";
            $html .= "ALL";
            $html .= "</div>";
        }
        
        $html .= "<div class='category-label'>All Products</div>";
        $html .= "</a>";
        
        // Add existing categories
        foreach ($this->categories as $id => $category) {
            $image_src = $this->getCategoryImage($id);
            
            $html .= "<a href='MainPage.php?category={$id}' class='category-item category-item-{$id}'>";
            
            if ($image_src) {
                $html .= "<img src='{$image_src}' class='category-image' alt='{$category['name']}'>";
            } else {
                $html .= "<div class='product-placeholder' style='position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); width: 60px; height: 60px;'>";
                $html .= $category['placeholder_letter'];
                $html .= "</div>";
            }
            
            $html .= "<div class='category-label'>{$category['name']}</div>";
            $html .= "</a>";
        }
        return $html;
    }
    
    // Refresh categories from database (useful for admin updates)
    public function refreshCategories() {
        $this->loadCategoriesFromDatabase();
    }
    
    // Get category count
    public function getCategoryCount() {
        return count($this->categories);
    }
    
    // Get categories with product counts
    public function getCategoriesWithProductCounts() {
        $categories_with_counts = [];
        
        foreach ($this->categories as $id => $category) {
            $stmt = $this->conn->prepare("SELECT COUNT(*) as product_count FROM products WHERE category_id = ?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $result = $stmt->get_result();
            $count = $result->fetch_assoc()['product_count'];
            
            $categories_with_counts[$id] = array_merge($category, ['product_count' => $count]);
        }
        
        return $categories_with_counts;
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
    // Redirect to main page if invalid category
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
    /* All your existing styles remain exactly the same until category styles */
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
    }

    /* All existing header, nav, hero styles remain exactly the same... */
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

    .search-bar {
      position:absolute;
      left:50px;
      top: -78px;
      width: 100%;
      max-width: min(600px, 90vw);
      height: 56px;
      border-radius: 55px;
      background-color: white;
      margin: 20px auto;
      position: relative;
    }

    .search-icon {
      position: absolute;
      left: 20px;
      top: 50%;
      transform: translateY(-50%);
      width: 24px;
      height: 24px;
    }

    .nav-icons {
      position: fixed;
      right: 30px;
      top: 25px;
      display: flex;
      gap: 15px;
      z-index: 11;
    }
    
    .nav-icon {
      width: 35px;
      height: 35px;
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
    }
    
    .nav-icon:hover {
      background: rgba(255, 255, 255, 0.2);
      transform: scale(1.1);
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
    }
    
    .nav-icon img {
      width: 20px;
      height: 20px;
      filter: brightness(0) invert(1);
    }
    
    .cart-badge {
      position: absolute;
      top: -5px;
      right: -5px;
      background: #ff4444;
      color: white;
      border-radius: 50%;
      width: 18px;
      height: 18px;
      font-size: 10px;
      font-weight: bold;
      display: flex;
      align-items: center;
      justify-content: center;
      box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
    }

    /* All existing hero content styles remain exactly the same... */
    .hero-content {
      flex: 1;
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: clamp(20px, 4vw, 40px);
      align-items: center;
      padding: clamp(20px, 4vw, 40px) 0;
    }

    .hero-left {
      position: relative;
    }

    .hero-rectangles {
      position: relative;
      margin-top: clamp(20px, 4vw, 40px);
    }

    .rectangle1 {
     top: -254px;
     left: 78px;
     width: 100%;
     max-width: min(620px, 90vw); 
     height: clamp(200px, 25vw, 250px);
     background: linear-gradient(to bottom, #ECC7ED, #C647CC);
     border-radius: 20px;
     position: absolute; 
    }

    .shop-now {
      left: 175px;
      top:-100px;
      font-size: clamp(2rem, 6vw, 3rem);
      font-weight: bold;
      color: white;
      margin-bottom: 20px;
      position:relative;
      z-index: 10;
    }

    .phone-image {
      position: absolute;
      top: 50%;
      left: 85%;
      transform: translate(-50%, -50%);
      width: 80%;
      max-width: min(400px, 80vw);
      height: auto;
    }

    .hero-right {
      display: flex;
      flex-direction: column;
      gap: 20px;
    }

    .rectangle2 {
      width: 100%;
      height: clamp(100px, 12vw, 120px);
      background: linear-gradient(to right, #FFFFFF, #CDACB1, #59D759);
      border-radius: 20px;
      position: relative;
      top: -87px;
      left: -50px;
    }

    .rectangle3 {
      width: 100%;
      height: clamp(100px, 12vw, 120px);
      background-color: #EEA7BD;
      border-radius: 20px;
      position: relative;
      top: -87px;
      left: -50px;
    }

    .pistachio-image{
      position:relative;
      top:-394px;
      left:755px;
      width:179px;
      height:135px;
      z-index:2;
    }

    .categories-section {
      position: relative;
      top:-123px;
      background: linear-gradient(to top, #592249, #BF489C);
      padding: clamp(40px, 8vw, 60px) clamp(10px, 2vw, 20px);
      margin-top: clamp(20px, 4vw, 40px);
      border-radius:10px;
    }

    .categories-title {
      text-align: center;
      font-size: clamp(1.5rem, 4vw, 2rem);
      font-weight: bold;
      color: white;
      margin-bottom: clamp(30px, 6vw, 40px);
    }

    .categories-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(min(200px, 40vw), 1fr));
      gap: clamp(20px, 4vw, 30px);
      max-width: min(1200px, 95vw);
      margin: 0 auto;
    }

    /* UPDATED: Category item with thicker gradient border */
    .category-item {
      position: relative;
      height: clamp(220px, 28vw, 280px);
      border-radius: 12px;
      overflow: hidden;
      cursor: pointer;
      transition: all 0.3s ease;
      text-decoration: none;
      display: block;
      background: white; /* White background for all categories */
      border: 6px solid transparent; /* THICKER: Increased from 4px to 6px */
      background-clip: padding-box; /* Ensures white background doesn't extend to border */
      padding: 12px; /* INCREASED: More inner padding for thicker border effect */
    }

    .category-item:hover {
      transform: scale(1.05);
      box-shadow: 0 8px 25px rgba(0, 0, 0, 0.3);
      border-width: 8px; /* THICKER: Increased from 6px to 8px on hover */
    }

    /* Special "All Products" category with thicker rainbow gradient border */
    .category-item-all {
      background: linear-gradient(white, white) padding-box, 
                  linear-gradient(45deg, #FF6B6B, #4ECDC4, #45B7D1, #96CEB4, #FFEAA7, #DDA0DD) border-box;
      animation: rainbow-border 3s ease-in-out infinite;
      border-width: 6px; /* Consistent with updated base thickness */
    }
    
    @keyframes rainbow-border {
      0%, 100% { 
        background: linear-gradient(white, white) padding-box, 
                    linear-gradient(45deg, #FF6B6B, #4ECDC4, #45B7D1, #96CEB4, #FFEAA7, #DDA0DD) border-box; 
      }
      50% { 
        background: linear-gradient(white, white) padding-box, 
                    linear-gradient(45deg, #DDA0DD, #FF6B6B, #4ECDC4, #45B7D1, #96CEB4, #FFEAA7) border-box; 
      }
    }
    
    .category-item-all:hover {
      transform: scale(1.08);
      box-shadow: 0 12px 30px rgba(0, 0, 0, 0.4);
      border-width: 8px; /* Thicker on hover */
    }
    
    /* Individual category gradient borders - Generated dynamically */
    .category-item-1 {
      background: linear-gradient(white, white) padding-box, 
                  linear-gradient(to bottom, #55361A, #CDACB1) border-box;
    }

    .category-item-2 {
      background: linear-gradient(white, white) padding-box, 
                  linear-gradient(to bottom, #BFB886, #F3E794) border-box;
    }

    .category-item-3 {
      background: linear-gradient(white, white) padding-box, 
                  linear-gradient(to bottom, #D97272, #F8DDDD) border-box;
    }

    .category-item-4 {
      background: linear-gradient(white, white) padding-box, 
                  linear-gradient(to bottom, #71EEEC, #C1ACAC) border-box;
    }

    .category-item-5 {
      background: linear-gradient(white, white) padding-box, 
                  linear-gradient(to bottom, #6B6060, #7C6F6F, #8D7D7D) border-box;
    }

    .category-item-6 {
      background: linear-gradient(white, white) padding-box, 
                  linear-gradient(to bottom, #E6E6FA, #DDA0DD) border-box;
    }

    .category-item-7 {
      background: linear-gradient(white, white) padding-box, 
                  linear-gradient(to bottom, #FFE4E1, #FFC0CB) border-box;
    }

    .category-item-8 {
      background: linear-gradient(white, white) padding-box, 
                  linear-gradient(to bottom, #F0E68C, #BDB76B) border-box;
    }

    /* Default gradient for new categories */
    .category-item:not([class*="category-item-"]) {
      background: linear-gradient(white, white) padding-box, 
                  linear-gradient(to bottom, #C647CC, #ECC7ED) border-box;
    }

    /* UPDATED: Smaller category images */
    .category-image {
      width: 65%; /* SMALLER: Reduced from 85% to 65% */
      height: 55%; /* SMALLER: Reduced from 75% to 55% */
      object-fit: cover; /* Better for JPEG display */
      position: absolute;
      top: 50%;
      left: 50%;
      transform: translate(-50%, -50%);
      border-radius: 8px; /* Slight rounding for images */
      box-shadow: 0 3px 12px rgba(0, 0, 0, 0.15); /* ENHANCED: Slightly stronger shadow */
    }

    .category-label {
      position: absolute;
      bottom: 15px;
      left: 50%;
      transform: translateX(-50%);
      background: rgba(0, 0, 0, 0.8);
      color: white;
      padding: 8px 16px;
      border-radius: 20px;
      font-size: 14px;
      font-weight: bold;
      backdrop-filter: blur(10px);
      -webkit-backdrop-filter: blur(10px);
      border: 1px solid rgba(255, 255, 255, 0.2);
      z-index: 2; /* Ensure label appears above image */
    }

    /* UPDATED: Smaller placeholder for consistency */
    .product-placeholder {
      width: 70px; /* SMALLER: Reduced from 80px to 70px */
      height: 70px; /* SMALLER: Reduced from 80px to 70px */
      background: linear-gradient(135deg, #ECC7ED, #C647CC);
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      color: white;
      font-weight: bold;
      font-size: 28px; /* SMALLER: Reduced from 32px to 28px */
      box-shadow: 0 4px 12px rgba(198, 71, 204, 0.3);
    }

    /* All other existing styles remain exactly the same... */
</style>
  </head>
  <body>
    <section class="main-section">
      <header class="header">
        <h2 class="brand-name">A&F</h2>
        <div class="nav-items">
          <span>New Product</span>
          <span>Announcements</span>
        </div>
        
        <div class="nav-icons">
          <div class="nav-icon" onclick="goToCart()" title="Cart">
            <img src="images/shopping-cart.png" alt="Cart">
            <?php if ($cart_count > 0): ?>
              <span class="cart-badge"><?php echo $cart_count; ?></span>
            <?php endif; ?>
          </div>
          
          <div class="nav-icon" onclick="goToProfile()" title="Profile">
            <img src="images/circle-user (1).png" alt="Profile">
          </div>
        </div>
      </header>

      <div class="search-bar">
        <img src="https://cdn.glitch.global/585aee42-d89c-4ece-870c-5b01fc1bab61/search.png?v=1747633330905" class="search-icon" alt="Search">
      </div>

      <div class="hero-content">
        <div class="hero-left">
          <h1 class="shop-now">SHOP NOW!</h1>
          <div class="hero-rectangles">
            <div class="rectangle1">
              <img src="https://cdn.glitch.global/585aee42-d89c-4ece-870c-5b01fc1bab61/cellphone.png?v=1747575037311" class="phone-image" alt="Phone">
            </div>
          </div>
        </div>
        
        <div class="hero-right">
          <div class="rectangle2"></div>
          <div class="rectangle3"></div>
        </div>
      </div>

      <div class="image-header">
        <img src="images/pistachio.png" class="pistachio-image">
      </div>

      <!-- DYNAMIC CATEGORIES - Generated from database -->
      <div class="categories-section">
        <h2 class="categories-title">Categories</h2>
        <div class="categories-grid">
          <?php echo $categoryManager->generateCategoryHTML(); ?>
        </div>
      </div>
    </section>

    <section class="products-section">
      <div class="products-header">
        <div class="products-title">
          <h2 class="brand-name-small">A&F</h2>
          <h2 class="section-title">Products</h2>
        </div>
      </div>

      <div class="sort-controls">
        <span class="sort-label">Sort by:</span>
        <div class="sort-buttons">
          <button class="sort-btn active" onclick="sortProducts('popular', this)">Popular</button>
          <button class="sort-btn" onclick="sortProducts('price_low', this)">Price: Low to High</button>
          <button class="sort-btn" onclick="sortProducts('price_high', this)">Price: High to Low</button>
        </div>
      </div>

      <div class="products-grid" id="productsGrid">
          <?php if (empty($products)): ?>
              <div style="grid-column: 1/-1; text-align: center; color: white; font-size: 18px; padding: 40px;">
                  <?php if ($category_filter): ?>
                      No products found in this category. <a href="MainPage.php" style="color: #8ADEF1; text-decoration: none;">← Back to All Products</a>
                  <?php else: ?>
                      No products found.
                  <?php endif; ?>
              </div>
          <?php else: ?>
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

                      <button class="add-to-cart-btn" onclick="addToCart(<?php echo $product['product_id']; ?>)" title="Add to Cart">
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
                                  echo "Out of Stock";
                              } elseif ($product['stock_quantity'] <= 10) {
                                  echo "Only " . $product['stock_quantity'] . " left";
                              } else {
                                  echo "In Stock";
                              }
                              ?>
                          </div>
                      </div>
                  </div>
              <?php endforeach; ?>
          <?php endif; ?>
      </div>
    </section>

    <script>
        function addToCart(productId) {
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
                    alert('Product added to cart!');
                    updateCartCount();
                } else {
                    alert('Error adding product to cart');
                }
            })
            .catch(error => {
                console.error('Error:', error);
            });
        }

        function sortProducts(sortType, buttonElement) {
            document.querySelectorAll('.sort-btn').forEach(btn => {
                btn.classList.remove('active');
            });
            
            buttonElement.classList.add('active');
            
            const grid = document.getElementById('productsGrid');
            const products = Array.from(grid.children);

            products.sort((a, b) => {
                switch(sortType) {
                    case 'price_low':
                        return parseFloat(a.dataset.price) - parseFloat(b.dataset.price);
                    case 'price_high':
                        return parseFloat(b.dataset.price) - parseFloat(a.dataset.price);
                    case 'popular':
                    default:
                        return a.dataset.name.localeCompare(b.dataset.name);
                }
            });

            grid.style.opacity = '0.5';
            setTimeout(() => {
                grid.innerHTML = '';
                products.forEach(product => grid.appendChild(product));
                grid.style.opacity = '1';
            }, 200);
        }

        function updateCartCount() {
            setTimeout(() => {
                location.reload();
            }, 1000);
        }

        document.addEventListener('DOMContentLoaded', function() {
            const productCards = document.querySelectorAll('.product-card');
            productCards.forEach(card => {
                card.addEventListener('click', function(e) {
                    if (!e.target.closest('.add-to-cart-btn')) {
                        const productId = this.dataset.productId;
                        window.location.href = `product_detail.php?id=${productId}`;
                    }
                });
            });
        });

        function goToCart() {
          window.location.href = 'Cart.php';
        }
        
        function goToProfile() {
          window.location.href = 'profile.php';
        }
    </script>
  </body>
</html>