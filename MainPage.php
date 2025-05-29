<?php
// Add this at the top of your MainPage.php
require 'db_connect.php';

// Fetch products from database
function getProducts($category_id = null) {
    global $conn;
    
    if ($category_id) {
        $stmt = $conn->prepare("SELECT p.product_id, p.name, p.description, p.price, p.stock_quantity, c.category_name 
                               FROM products p 
                               JOIN categories c ON p.category_id = c.category_id 
                               WHERE p.category_id = ?");
        $stmt->bind_param("i", $category_id);
    } else {
        $stmt = $conn->prepare("SELECT p.product_id, p.name, p.description, p.price, p.stock_quantity, c.category_name 
                               FROM products p 
                               JOIN categories c ON p.category_id = c.category_id");
    }
    
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_all(MYSQLI_ASSOC);
}

// Get all products or filter by category
$category_filter = isset($_GET['category']) ? $_GET['category'] : null;
$products = getProducts($category_filter);
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
    }

    .main-section {
      min-height: 100vh;
      position: relative;
      display: flex;
      flex-direction: column;
      padding: min(20px, 2vw); /* Responsive padding */
    }

    .header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 20px 0;
      position: relative;
      z-index: 10;
      flex-wrap: wrap; /* Allow wrapping on small screens */
      gap: 10px;
    }

    .brand-name {
      font-size: clamp(2rem, 5vw, 3rem); /* Responsive font size */
      font-weight: bold;
      color: white;
    }

    .nav-items {
      position: absolute;
      left:125px;
      display: flex;
      gap: clamp(15px, 3vw, 30px); /* Responsive gap */
      color: white;
      font-size: clamp(0.8rem, 2vw, 1rem); /* Responsive font size */

    }

    .search-bar {
      position:absolute;
      left:50px;
      top: -78px;
      width: 100%;
      max-width: min(600px, 90vw); /* Responsive max-width */
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

    .notifications {
      position:absolute;
      right:289px;
      top:57px;
      width: clamp(35px, 6vw, 49px); /* Responsive size */
      height: clamp(35px, 6vw, 49px);
    }

    .cart{
      position:absolute;
      right:220px;
      top:57px;
      width: clamp(35px, 6vw, 49px); /* Responsive size */
      height: clamp(35px, 6vw, 49px);
    }

    .user{
      position:absolute;
      right:150px;
      top:57px;
      width: clamp(35px, 6vw, 49px); /* Responsive size */
      height: clamp(35px, 6vw, 49px);
    }

    .hero-content {
      flex: 1;
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: clamp(20px, 4vw, 40px); /* Responsive gap */
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
      font-size: clamp(2rem, 6vw, 3rem); /* Responsive font size */
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
      max-width: min(400px, 80vw); /* Responsive max-width */
      height: auto;
    }

    .hero-right {
      display: flex;
      flex-direction: column;
      gap: 20px;
    }

    .rectangle2 {
      width: 100%;
      height: clamp(100px, 12vw, 120px); /* Responsive height */
      background: linear-gradient(to right, #FFFFFF, #CDACB1, #59D759);
      border-radius: 20px;
      position: relative;
      top: -87px;
      left: -50px;
    }

    .rectangle3 {
      width: 100%;
      height: clamp(100px, 12vw, 120px); /* Responsive height */
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
      padding: clamp(40px, 8vw, 60px) clamp(10px, 2vw, 20px); /* Responsive padding */
      margin-top: clamp(20px, 4vw, 40px);
      border-radius:10px;
    }

    .categories-title {
      text-align: center;
      font-size: clamp(1.5rem, 4vw, 2rem); /* Responsive font size */
      font-weight: bold;
      color: white;
      margin-bottom: clamp(30px, 6vw, 40px);
    }

    .categories-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(min(200px, 40vw), 1fr)); /* Responsive columns */
      gap: clamp(20px, 4vw, 30px); /* Responsive gap */
      max-width: min(1200px, 95vw); /* Responsive max-width */
      margin: 0 auto;
    }

    .category-item {
      position: relative;
      height: clamp(220px, 28vw, 280px); /* Responsive height */
      border-radius: 8px;
      overflow: hidden;
      cursor: pointer;
      transition: transform 0.3s ease;
    }

    .category-item:hover {
      transform: scale(1.05);
    }

    .category-chocolate {
      background: linear-gradient(to bottom, #55361A, #CDACB1);
    }

    .category-noodles {
      background: linear-gradient(to bottom, #BFB886, #F3E794);
    }

    .category-chips {
      background: linear-gradient(to bottom, #D97272, #F8DDDD);
    }

    .category-melona {
      background: linear-gradient(to bottom, #71EEEC, #C1ACAC);
    }

    .category-chocnut {
      background: linear-gradient(to bottom, #6B6060, #7C6F6F, #8D7D7D);
    }

    .category-image {
      width: 80%;
      height: 80%;
      object-fit: contain;
      position: absolute;
      top: 50%;
      left: 50%;
      transform: translate(-50%, -50%);
    }

    .products-section {
      background:url('images/bg.png');
      background-position: center;
      background-attachment: fixed;
      background-size: cover;
      min-height: 100vh;
      overflow-x: hidden; 
      min-height: 100vh;
      padding: clamp(40px, 8vw, 60px) clamp(10px, 2vw, 20px); /* Responsive padding */
    }

    .products-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: clamp(30px, 6vw, 40px);
      flex-wrap: wrap;
      gap: 20px;
    }

    .products-title {
      position:absolute;
      bottom:-380px;
      display: flex;
      align-items: center;
      gap: clamp(15px, 3vw, 20px); /* Responsive gap */
    }

    .brand-name-small {
      position:absolute;
      left:19px;
      bottom: -150px;
      font-size: clamp(2rem, 5vw, 2.5rem); /* Responsive font size */
      font-weight: bold;
      color: white;
    }

    .section-title {
      position:absolute;
      left:155px;
      bottom: -150px;
      font-size: clamp(1.5rem, 4vw, 2rem); /* Responsive font size */
      font-weight: bold;
      color: white;
    }

    .sort-controls {
      display: flex;
      align-items: center;
      justify-content: center;
      margin-bottom:30px;
    }

    .sort-label {
      position:absolute;
      left:300px;
      bottom:-650px;
      font-size: clamp(1.2rem, 3vw, 1.5rem); /* Responsive font size */
      color: white;
    }

    .sort-buttons {
       position: absolute;
       bottom: -660px;
       left: 450px;
       display: flex;
       gap: 10px;
       flex-wrap: wrap;
    }

    .sort-btn {
      width: clamp(97px, 15vw, 120px);
      height: clamp(40px, 6vw, 50px);
      background: #FFFFFF;
      border: none;
      cursor: pointer;
      font-size: clamp(0.8rem, 2vw, 1rem);
     
    }

    .products-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(min(180px, 40vw), 1fr)); /* Responsive columns */
      gap: clamp(15px, 3vw, 20px); /* Responsive gap */
      max-width: min(1400px, 95vw); /* Responsive max-width */
      margin: 0 auto;
    }

    .product-card {
      background: #EEEFE8;
      border-radius: 12px;
      height: 320px; /* Increased height for content */
      position: relative;
      overflow: hidden;
      cursor: pointer;
      transition: all 0.3s ease;
      border: 2px solid transparent;
      box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }

    .product-card:hover {
      transform: translateY(-8px);
      border-color: #C647CC;
      box-shadow: 0 8px 25px rgba(198, 71, 204, 0.3);
    }

    .product-image-container {
      width: 90%;
      height: 60%;
      background: #FFFFFF;
      border-radius: 15px;
      position: absolute;
      top: 10px;
      left: 50%;
      transform: translateX(-50%);
      display: flex;
      align-items: center;
      justify-content: center;
      overflow: hidden;
    }

    .product-image {
      width: 100%;
      height: 100%;
      object-fit: cover;
      border-radius: 15px;
    }

    .product-placeholder {
      width: 60px;
      height: 60px;
      background: linear-gradient(135deg, #ECC7ED, #C647CC);
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      color: white;
      font-weight: bold;
      font-size: 24px;
    }

    .product-info {
      position: absolute;
      bottom: 0;
      left: 0;
      right: 0;
      padding: 12px;
      background: linear-gradient(to top, rgba(255,255,255,0.95), rgba(255,255,255,0.8));
      backdrop-filter: blur(5px);
    }

    .product-name {
      font-size: 14px;
      font-weight: 700;
      color: #371b70;
      margin-bottom: 4px;
      line-height: 1.3;
      overflow: hidden;
      text-overflow: ellipsis;
      white-space: nowrap;
    }

    .product-price {
      font-size: 16px;
      font-weight: 800;
      color: #C647CC;
      margin-bottom: 4px;
    }

    .product-stock {
      font-size: 11px;
      color: #666;
      font-weight: 500;
    }

    .out-of-stock {
      color: #e74c3c;
      font-weight: 600;
    }

    .add-to-cart-btn {
      position: absolute;
      top: 10px;
      right: 10px;
      background: #C647CC;
      color: white;
      border: none;
      width: 35px;
      height: 35px;
      border-radius: 50%;
      cursor: pointer;
      display: flex;
      align-items: center;
      justify-content: center;
      opacity: 0;
      transition: all 0.3s ease;
      font-size: 18px;
      font-weight: bold;
    }

    .product-card:hover .add-to-cart-btn {
      opacity: 1;
    }

    .add-to-cart-btn:hover {
      background: #a63d9f;
      transform: scale(1.1);
    }

    .category-filter {
      margin: 20px 0;
      text-align: center;
    }

    .filter-btn {
      background: #fff;
      border: 2px solid #C647CC;
      color: #C647CC;
      padding: 8px 16px;
      margin: 0 5px;
      border-radius: 20px;
      cursor: pointer;
      transition: all 0.3s ease;
      text-decoration: none;
      display: inline-block;
      font-weight: 600;
    }

    .filter-btn:hover,
    .filter-btn.active {
      background: #C647CC;
      color: white;
    }

    /* Responsive breakpoints */
    @media (max-width: 1200px) {
      .hero-content {
        grid-template-columns: 1fr;
        text-align: center;
      }
      
      .categories-grid {
        grid-template-columns: repeat(auto-fit, minmax(min(150px, 45vw), 1fr));
      }
    }

    @media (max-width: 768px) {
      .main-section {
        padding: 10px;
      }
      
      .header {
        flex-direction: column;
        gap: 15px;
      }
      
      .nav-items {
        order: 1;
      }
      
      .notifications {
        order: 2;
      }
      
      .products-header {
        flex-direction: column;
        align-items: stretch;
        text-align: center;
      }
      
      .sort-controls {
        justify-content: center;
      }

      .product-card {
        height: 280px;
      }
      
      .product-name {
        font-size: 12px;
      }
      
      .product-price {
        font-size: 14px;
      }
    }

    @media (max-width: 480px) {
      .categories-grid {
        grid-template-columns: repeat(2, 1fr);
      }
      
      .products-grid {
        grid-template-columns: repeat(2, 1fr);
      }
      
      .sort-buttons {
        justify-content: center;
      }
    }
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
        
      </header>

      <div class="search-bar">
        <img src="https://cdn.glitch.global/585aee42-d89c-4ece-870c-5b01fc1bab61/search.png?v=1747633330905" class="search-icon" alt="Search">
      </div>

       <div class="icons">
        <img src="images/bell.png" class="notifications" alt="notify">
        <img src="images/shopping-cart.png" class="cart" alt="carty">
        <img src="images/circle-user (1).png" class="user" alt="person">
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

      <div class="categories-section">
        <h2 class="categories-title">Categories</h2>
        <div class="categories-grid">
          <div class="category-item category-chocolate">
            <img src="https://cdn.glitch.global/585aee42-d89c-4ece-870c-5b01fc1bab61/choc.png?v=1747707953400" class="category-image" alt="Chocolate">
          </div>
          <div class="category-item category-noodles">
            <img src="https://cdn.glitch.global/585aee42-d89c-4ece-870c-5b01fc1bab61/noodle.png?v=1747708218689" class="category-image" alt="Noodles">
          </div>
          <div class="category-item category-chips">
            <img src="https://cdn.glitch.global/585aee42-d89c-4ece-870c-5b01fc1bab61/chips.png?v=1747708271881" class="category-image" alt="Chips">
          </div>
          <div class="category-item category-melona">
            <img src="https://cdn.glitch.global/585aee42-d89c-4ece-870c-5b01fc1bab61/melona.png?v=1747708272936" class="category-image" alt="Melona">
          </div>
          <div class="category-item category-chocnut">
            <img src="https://cdn.glitch.global/585aee42-d89c-4ece-870c-5b01fc1bab61/chocnut?v=1747708276502" class="category-image" alt="Chocnut">
          </div>
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

      <!-- Category Filter -->
      <div class="category-filter">
          <a href="MainPage.php" class="filter-btn <?php echo !$category_filter ? 'active' : ''; ?>">All Products</a>
          <a href="MainPage.php?category=1" class="filter-btn <?php echo $category_filter == 1 ? 'active' : ''; ?>">Chocolate Bars</a>
          <a href="MainPage.php?category=2" class="filter-btn <?php echo $category_filter == 2 ? 'active' : ''; ?>">Truffles</a>
          <a href="MainPage.php?category=3" class="filter-btn <?php echo $category_filter == 3 ? 'active' : ''; ?>">Gift Boxes</a>
          <a href="MainPage.php?category=4" class="filter-btn <?php echo $category_filter == 4 ? 'active' : ''; ?>">Dark Chocolate</a>
      </div>

      <div class="search-products"></div>

      <div class="sort-controls">
        <span class="sort-label">Sort by:</span>
        <div class="sort-buttons">
          <button class="sort-btn" onclick="sortProducts('popular')">Popular</button>
          <button class="sort-btn" onclick="sortProducts('price_low')">Price: Low</button>
          <button class="sort-btn" onclick="sortProducts('price_high')">Price: High</button>
        </div>
      </div>

      <!-- Dynamic Products Grid -->
      <div class="products-grid" id="productsGrid">
          <?php if (empty($products)): ?>
              <div style="grid-column: 1/-1; text-align: center; color: white; font-size: 18px; padding: 40px;">
                  No products found in this category.
              </div>
          <?php else: ?>
              <?php foreach ($products as $product): ?>
                  <div class="product-card" data-product-id="<?php echo $product['product_id']; ?>" 
                       data-price="<?php echo $product['price']; ?>" 
                       data-name="<?php echo htmlspecialchars($product['name']); ?>">
                  
                      <div class="product-image-container">
                          <?php 
                          // Check if product image exists
                          $imagePath = "images/products/product_" . $product['product_id'] . ".jpg";
                          if (file_exists($imagePath)): 
                          ?>
                              <img src="<?php echo $imagePath; ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" class="product-image">
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
        // Add to cart functionality
        function addToCart(productId) {
            // You can implement AJAX call to add product to cart
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
                    // You can update cart count here
                } else {
                    alert('Error adding product to cart');
                }
            })
            .catch(error => {
                console.error('Error:', error);
            });
        }

        // Sort products functionality
        function sortProducts(sortType) {
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

            // Clear grid and re-append sorted products
            grid.innerHTML = '';
            products.forEach(product => grid.appendChild(product));
        }

        // Product click to view details
        document.addEventListener('DOMContentLoaded', function() {
            const productCards = document.querySelectorAll('.product-card');
            productCards.forEach(card => {
                card.addEventListener('click', function(e) {
                    // Don't trigger if clicking the add to cart button
                    if (!e.target.closest('.add-to-cart-btn')) {
                        const productId = this.dataset.productId;
                        // Redirect to product detail page
                        window.location.href = `product_detail.php?id=${productId}`;
                    }
                });
            });
        });
    </script>
  </body>
</html>