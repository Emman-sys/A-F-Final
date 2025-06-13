<?php
// Start session and connect to database for cart functionality
session_start();
require 'db_connect.php';

// Security check - redirect to login if user not authenticated
if (!isset($_SESSION['user_id'])) {
    header('Location: Welcome.php');
    exit;
}

$user_id = $_SESSION['user_id'];

// Get user information for pre-filling checkout form
$user_stmt = $conn->prepare("SELECT name, email FROM users WHERE user_id = ?");
if (!$user_stmt) {
    die("Error preparing user query: " . $conn->error);
}
$user_stmt->bind_param("i", $user_id);
$user_stmt->execute();
$user_info = $user_stmt->get_result()->fetch_assoc();

// Get user's total order count for navigation badge display
$order_count_stmt = $conn->prepare("SELECT COUNT(*) as order_count FROM orders WHERE user_id = ?");
$order_count_stmt->bind_param("i", $user_id);
$order_count_stmt->execute();
$order_count = $order_count_stmt->get_result()->fetch_assoc()['order_count'];

// Check if cartitems table exists (graceful handling for new database installations)
$table_check = $conn->query("SHOW TABLES LIKE 'cartitems'");
if ($table_check->num_rows == 0) {
    $cart_items = [];
} else {
    // Get cart items with comprehensive stock checking and product details
    // JOIN multiple tables to get all necessary product information
    $stmt = $conn->prepare("
        SELECT ci.cart_item_id, ci.quantity, p.product_id, p.name, p.description, p.price, p.stock_quantity,
               (ci.quantity * p.price) as subtotal,
               cat.category_name,
               CASE WHEN p.product_image IS NOT NULL THEN 1 ELSE 0 END as has_image,
               CASE 
                   WHEN p.stock_quantity = 0 THEN 'out_of_stock'
                   WHEN p.stock_quantity < ci.quantity THEN 'insufficient_stock' 
                   WHEN p.stock_quantity <= 10 THEN 'low_stock'
                   ELSE 'in_stock'
               END as stock_status
        FROM cartitems ci
        JOIN cart c ON ci.cart_id = c.cart_id
        JOIN products p ON ci.product_id = p.product_id
        JOIN categories cat ON p.category_id = cat.category_id
        WHERE c.user_id = ?
        ORDER BY ci.cart_item_id DESC
    ");
    
    if (!$stmt) {
        die("Error preparing cart query: " . $conn->error);
    }
    
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $cart_items = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

// Calculate cart totals and delivery fee
$total = 0;
$total_items = 0;
foreach ($cart_items as $item) {
    $total += $item['subtotal'];
    $total_items += $item['quantity'];
}

// Simple delivery fee logic - free delivery for orders over ₱500
$delivery_fee = $total >= 500 ? 0 : 50;
$grand_total = $total + $delivery_fee;
?>

<!DOCTYPE html>
<html lang="en">
  <head>
   <meta charset="utf-8" />
   <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
   <title>A&F - Your Cart</title>
   <link href="https://fonts.googleapis.com/css2?family=Merriweather:wght@400;700&family+Merriweather+Sans:wght@700&display=swap" rel="stylesheet" />
   <link rel="stylesheet" href="styles.css">
   
   <!-- Free Map Libraries - No API Key Required -->
   <!-- Leaflet: Open-source interactive map library -->
   <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
   <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
   
   <!-- Optional: Free geocoding service for address search -->
   <script src="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.js"></script>
   <link rel="stylesheet" href="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.css" />

    <style>
      /* Cart page styling with responsive design and modern UI */
      body {
        background: linear-gradient(135deg, #5127A3, #986C93, #E0B083);
        background-image: url('bg.png');
        background-position: center;
        background-size: cover;
        background-attachment: fixed;
        font-family: 'Merriweather', serif;
        margin: 0;
        padding: 0;
        overflow-x: hidden; /* This should prevent horizontal scroll */
        min-height: 100vh;
        /* REMOVED: zoom property */
      }
      
      .container {
        max-width: 1200px; /* DECREASED from 1600px */
        margin: 0 auto;
        position: relative;
        height: 100vh;
      }
      
      /* Header gradient - make it smaller */
      .header-gradient {
        position: fixed;
        left: 0;
        right: 0;
        top: 0;
        width: 500vw;
        height: 100px; 
        background: linear-gradient(
          to bottom, 
          rgba(81, 39, 163, 0.9), 
          rgba(152, 108, 147, 0.8), 
          rgba(224, 176, 131, 0.7),
          rgba(224, 176, 131, 0.3)
        );
        backdrop-filter: blur(10px);
        -webkit-backdrop-filter: blur(10px);
        border-bottom: 1px solid rgba(255, 255, 255, 0.2);
        z-index: 10;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
      }
      
     
      .brand {
        position: fixed;
        font-size: 40px; /* DECREASED from 56px */
        left: 30px; /* DECREASED from 50px */
        top: 25px; /* ADJUSTED for new header height */
        font-weight: bold;
        color: white;
        z-index: 11;
        text-decoration: none;
        text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
        transition: all 0.3s ease;
      }
      
      .brand:hover {
        color: #8ADEF1;
        transform: scale(1.05);
      }
      
      /* Main content - make it smaller */
      .main-content {
        position: absolute;
        left: 50px; /* DECREASED from 80px */
        top: 130px; /* ADJUSTED for new header height */
        width: 650px; /* DECREASED from 820px */
        background: rgba(255, 255, 255, 0.95);
        border-radius: 10px; /* DECREASED border radius */
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
        backdrop-filter: blur(10px);
        -webkit-backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.2);
        overflow: hidden;
        z-index: 2;
      }
      
      /* Payment sidebar - make it smaller */
      .payment-sidebar {
        position: absolute;
        width: 300px; /* DECREASED from 380px */
        height: 450px; /* DECREASED from 520px */
        right: 50px; /* DECREASED from 80px */
        top: 130px; /* ADJUSTED for new header height */
        background: linear-gradient(
          to bottom, 
          rgba(55, 27, 112, 0.95), 
          rgba(81, 39, 163, 0.95), 
          rgba(106, 52, 214, 0.95)
        );
        border-radius: 15px; /* DECREASED border radius */
        padding: 20px; /* DECREASED from 30px */
        color: white;
        z-index: 2;
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.2);
        backdrop-filter: blur(10px);
        -webkit-backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.1);
      }
      
      /* Payment title - make it smaller */
      .payment-title {
        font-size: 22px; /* DECREASED from 28px */
        font-weight: bold;
        margin-bottom: 15px; /* DECREASED from 20px */
        text-align: center;
      }
      
      /* Payment sections - less spacing */
      .payment-section {
        margin-bottom: 12px; /* DECREASED from 18px */
      }
      
      .payment-label {
        font-size: 14px; /* DECREASED from 16px */
        margin-bottom: 6px; /* DECREASED from 8px */
        color: #e9ecef;
      }
      
      .payment-input {
        width: calc(100% - 20px); /* ADJUSTED for smaller padding */
        padding: 8px 10px; /* DECREASED padding */
        border: none;
        border-radius: 6px; /* DECREASED border radius */
        background: rgba(255,255,255,0.1);
        color: white;
        border-bottom: 2px solid #8ADEF1;
        font-size: 13px; /* DECREASED from 15px */
        box-sizing: border-box;
      }
      
      .payment-input::placeholder {
        color: rgba(255,255,255,0.7);
      }
      
      /* Payment methods - smaller buttons */
      .payment-methods {
        display: flex;
        gap: 8px; /* DECREASED from 12px */
        margin: 15px 0; /* DECREASED from 20px */
        justify-content: center;
      }
      
      .payment-method {
        width: 45px; /* DECREASED from 60px */
        height: 35px; /* DECREASED from 42px */
        background: rgba(255,255,255,0.1);
        border: 1px solid rgba(255,255,255,0.3);
        border-radius: 8px; /* DECREASED border radius */
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.2s ease;
      }
      
      .payment-method:hover {
        background: rgba(255,255,255,0.2);
        transform: scale(1.05);
      }
      
      .payment-method img {
        width: 20px; /* DECREASED from 24px */
        height: 20px;
        filter: brightness(0) invert(1);
      }
      
      /* Checkout button - make it smaller */
      .checkout-btn {
        width: 100%;
        padding: 12px; /* DECREASED from 16px */
        background: #8ADEF1;
        color: #371B70;
        border: none;
        border-radius: 10px; /* DECREASED border radius */
        font-size: 16px; /* DECREASED from 18px */
        font-weight: bold;
        cursor: pointer;
        margin-top: 15px; /* DECREASED from 20px */
        transition: all 0.2s ease;
        box-sizing: border-box;
      }
      
      .checkout-btn:hover {
        background: #7bc8e8;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(138, 222, 241, 0.3);
      }
      
      /* Table header - smaller columns */
      .table-header {
        background: rgba(248, 249, 250, 0.9);
        padding: 12px 16px; /* DECREASED padding */
        border-bottom: 2px solid #dee2e6;
        display: grid;
        grid-template-columns: 90px 150px 90px 70px 90px 70px; /* DECREASED all columns */
        gap: 8px; /* DECREASED gap */
        font-weight: bold;
        color: #6c757d;
        font-size: 13px; /* DECREASED from 15px */
        position: relative;
        z-index: 2;
      }
      
      /* Products container - smaller */
      .products-container {
        max-height: 350px; /* DECREASED from 450px */
        overflow-y: auto;
        background: rgba(255, 255, 255, 0.9);
        position: relative;
        z-index: 2;
      }
      
      /* Product rows - less spacing */
      .product-row {
        padding: 12px 16px; /* DECREASED padding */
        border-bottom: 1px solid #e9ecef;
        display: grid;
        grid-template-columns: 90px 150px 90px 70px 90px 70px; /* Match header */
        gap: 8px; /* DECREASED gap */
        align-items: center;
        transition: all 0.2s ease;
      }
      
      .product-row:hover {
        background: rgba(248, 249, 250, 0.9);
      }
      
      /* Product image - smaller */
      .product-image {
        width: 60px; /* DECREASED from 80px */
        height: 60px;
        object-fit: cover;
        border-radius: 8px; /* DECREASED border radius */
        border: 1px solid #dee2e6;
      }
      
      /* Product name - smaller font */
      .product-name {
        font-weight: 600;
        color: #343a40;
        font-size: 13px; /* DECREASED from 15px */
        line-height: 1.3;
      }
      
      /* Price display - smaller font */
      .price-display {
        font-weight: 600;
        color: #495057;
        font-size: 14px; /* DECREASED from 17px */
      }
      
      /* Quantity controls - smaller buttons */
      .quantity-controls {
        display: flex;
        align-items: center;
        gap: 6px; /* DECREASED from 10px */
      }
      
      .qty-btn {
        width: 22px; /* DECREASED from 28px */
        height: 22px;
        border: 1px solid #C647CC;
        background: white;
        color: #C647CC;
        border-radius: 4px; /* DECREASED border radius */
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        font-size: 12px; /* DECREASED from 15px */
        transition: all 0.2s ease;
      }
      
      .qty-btn:hover {
        background: #C647CC;
        color: white;
        transform: scale(1.1);
      }
      
      .qty-btn:active {
        transform: scale(0.9);
      }
      
      .qty-display {
        min-width: 25px; /* DECREASED from 35px */
        text-align: center;
        font-weight: 600;
        color: #495057;
        font-size: 13px; /* DECREASED font size */
      }
      
      /* Total price - smaller font */
      .total-price {
        font-weight: bold;
        color: #C647CC;
        font-size: 14px; /* DECREASED from 17px */
      }
      
      /* Delete button - smaller */
      .delete-btn {
        background: #dc3545;
        color: white;
        border: none;
        padding: 6px 10px; /* DECREASED padding */
        border-radius: 4px; /* DECREASED border radius */
        cursor: pointer;
        font-size: 11px; /* DECREASED from 13px */
        transition: all 0.2s ease;
      }
      
      .delete-btn:hover {
        background: #c82333;
        transform: scale(1.05);
      }
      
      /* Empty cart - adjusted position */
      .empty-cart {
        position: absolute;
        left: 50%;
        top: 60%;
        transform: translate(-50%, -50%);
        text-align: center;
        color: white;
        background: rgba(0, 0, 0, 0.8);
        backdrop-filter: blur(15px);
        -webkit-backdrop-filter: blur(15px);
        padding: 30px; /* DECREASED from 40px */
        border-radius: 12px; /* DECREASED border radius */
        border: 1px solid rgba(255, 255, 255, 0.1);
        z-index: 10;
      }
      
      .empty-cart a {
        color: #8ADEF1;
        text-decoration: none;
        font-weight: bold;
      }
      
      /* Loading and animations - keep the same */
      .loading {
        opacity: 0.5;
        pointer-events: none;
      }
      
      .success-flash {
        animation: successFlash 0.3s ease;
      }
      
      @keyframes successFlash {
        0% { background-color: transparent; }
        50% { background-color: rgba(40, 167, 69, 0.2); }
        100% { background-color: transparent; }
      }
      
      /* Notifications - keep the same */
      .notification {
        position: fixed;
        top: 20px;
        right: 20px;
        padding: 15px 20px;
        border-radius: 8px;
        color: white;
        font-weight: bold;
        z-index: 1000;
        transform: translateX(400px);
        transition: transform 0.3s ease;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
      }
      
      .notification.success {
        background: linear-gradient(135deg, #28a745, #20c997);
      }
      
      .notification.error {
        background: linear-gradient(135deg, #dc3545, #fd7e14);
      }
      
      .notification.show {
        transform: translateX(0);
      }
      
      /* Scrollbar styling - smaller */
      .products-container::-webkit-scrollbar {
        width: 4px; /* DECREASED from 6px */
      }
      
      .products-container::-webkit-scrollbar-track {
        background: #f1f1f1;
      }
      
      .products-container::-webkit-scrollbar-thumb {
        background: #C647CC;
        border-radius: 2px;
      }
      
      /* Responsive design - remove zoom references */
      @media (max-width: 1200px) {
        .container { max-width: 1000px; }
        .main-content { width: 550px; }
        .payment-sidebar { width: 280px; }
      }
      
      @media (max-width: 1000px) {
        .container { max-width: 900px; }
        .main-content { width: 500px; }
        .payment-sidebar { width: 260px; }
      }
      
      /* Add these styles to your existing CSS */
  
  /* Navigation icons */
  .nav-icons {
    position: fixed;
    right: 30px;
    top: 25px;
    display: flex;
    gap: 15px;
    z-index: 11;
  }
  
  .nav-icon {
    width: 50px;
    height: 50px;
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
    width: 28px;
    height: 28px;
    filter: brightness(0) invert(1);
  }
  
  /* Cart icon - highlight current page */
  .nav-icon.current {
    background: rgba(198, 71, 204, 0.4);
    border: 1px solid rgba(198, 71, 204, 0.6);
  }
  
  .nav-icon.current:hover {
    background: rgba(198, 71, 204, 0.5);
  }
  
  /* Cart badge for item count */
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
  
  /* Enhanced stock status styling */
  .product-row.out_of_stock {
    background: rgba(220, 53, 69, 0.1);
    opacity: 0.7;
  }
  
  .product-row.insufficient_stock {
    background: rgba(255, 193, 7, 0.1);
  }
  
  .product-row.low_stock {
    background: rgba(253, 126, 20, 0.1);
  }
  
  .qty-btn:disabled {
    background: #e9ecef;
    color: #6c757d;
    cursor: not-allowed;
    border-color: #dee2e6;
  }
  
  .qty-btn:disabled:hover {
    background: #e9ecef;
    color: #6c757d;
    transform: none;
  }
  
  /* Enhanced payment method selection */
  .payment-method.selected {
    background: rgba(198, 71, 204, 0.3) !important;
    border: 2px solid #C647CC !important;
    transform: scale(1.05);
  }
  
  .payment-method.selected::after {
    content: '✓';
    position: absolute;
    top: -5px;
    right: -5px;
    background: #C647CC;
    color: white;
    border-radius: 50%;
    width: 16px;
    height: 16px;
    font-size: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
  }
  
      /* Map container styling */
      .map-container {
        position: relative;
        margin-top: 8px;
        border-radius: 6px;
        overflow: hidden;
        border: 1px solid rgba(255,255,255,0.3);
        background: rgba(255,255,255,0.1);
      }
      
      .map-toggle {
        width: 100%;
        padding: 6px 10px;
        background: rgba(138, 222, 241, 0.8);
        color: #371B70;
        border: none;
        border-radius: 4px;
        font-size: 12px;
        font-weight: bold;
        cursor: pointer;
        transition: all 0.2s ease;
        margin-bottom: 8px;
      }
      
      .map-toggle:hover {
        background: rgba(138, 222, 241, 1);
        transform: translateY(-1px);
      }
      
      #map {
        height: 200px;
        width: 100%;
        border-radius: 6px;
        display: none;
      }
      
      .map-instructions {
        font-size: 11px;
        color: rgba(255,255,255,0.8);
        text-align: center;
        padding: 4px;
        margin-top: 4px;
      }
      
      .address-suggestions {
        max-height: 120px;
        overflow-y: auto;
        background: rgba(0,0,0,0.8);
        border-radius: 4px;
        margin-top: 4px;
        display: none;
      }
      
      .address-suggestion {
        padding: 8px 10px;
        cursor: pointer;
        font-size: 12px;
        border-bottom: 1px solid rgba(255,255,255,0.1);
        transition: background 0.2s ease;
      }
      
      .address-suggestion:hover {
        background: rgba(255,255,255,0.1);
      }
      
      .address-suggestion:last-child {
        border-bottom: none;
      }
      
      .location-icon {
        display: inline-block;
        margin-right: 4px;
        font-size: 10px;
      }
      
      /* Checkout Modal Styles */
      .checkout-modal {
        display: none;
        position: fixed;
        z-index: 10000;
        left: 0; top: 0; width: 100vw; height: 100vh;
        background: rgba(0,0,0,0.45);
        align-items: center;
        justify-content: center;
      }
      
      .checkout-modal.show {
        display: flex;
      }
      
      .checkout-modal-content {
        background: linear-gradient(to bottom, #371B70 90%, #8ADEF1 100%);
        color: white;
        border-radius: 18px;
        padding: 30px 30px 20px 30px;
        min-width: 320px;
        max-width: 95vw;
        width: 350px;
        box-shadow: 0 8px 32px rgba(0,0,0,0.25);
        position: relative;
        animation: modalPop 0.25s cubic-bezier(.4,2,.6,1) 1;
      }
      
      @keyframes modalPop {
        0% { transform: scale(0.8); opacity: 0; }
        100% { transform: scale(1); opacity: 1; }
      }
      
      .checkout-modal-close {
        position: absolute;
        right: 18px;
        top: 10px;
        font-size: 28px;
        color: #fff;
        cursor: pointer;
        font-weight: bold;
        z-index: 1;
        transition: color 0.2s;
      }
      
      .checkout-modal-close:hover {
        color: #C647CC;
      }
      
      @media (max-width: 600px) {
        .checkout-modal-content { width: 98vw; min-width: unset; padding: 12px; }
        #open-checkout-btn { width: 90vw !important; left: 5vw !important; right: unset !important; }
      }
    </style>
  </head>
  <body>
    <div class="container">
      <!-- Header -->
      <div class="header-gradient"></div>
      <a href="MainPage.php" class="brand">A&F</a>
      
      <!-- Navigation Icons with cart badge and order count -->
    <div class="nav-icons">
      <!-- Cart Icon (current page - highlighted with special styling) -->
      <div class="nav-icon current" title="Cart (Current Page)">
        <img src="images/cart.png" alt="Cart">
        <span class="cart-badge"><?php echo $total_items; ?></span>
      </div>
      
      <!-- Order History Icon with dynamic badge showing order count -->
      <div class="nav-icon" onclick="goToOrderHistory()" title="Order History (<?php echo $order_count; ?> orders)">
        <img src="images/deliv.png" alt="Orders">
        <?php if ($order_count > 0): ?>
          <span class="cart-badge" style="background: #28a745;"><?php echo $order_count; ?></span>
        <?php endif; ?>
      </div>
      
      <!-- User Profile Icon for account management -->
      <div class="nav-icon" onclick="goToProfile()" title="Profile">
        <img src="images/users.png" alt="Profile">
      </div>
    </div>
    
    <!-- Empty cart state - shows when no items in cart -->
    <?php if (empty($cart_items)): ?>
        <div class="empty-cart">
          <h2>🛒 Your cart is empty</h2>
          <p><a href="MainPage.php">Continue Shopping</a></p>
          <!-- Show order history link if user has previous orders -->
          <?php if ($order_count > 0): ?>
            <p><a href="#" onclick="goToOrderHistory()" style="color: #8ADEF1;">View Order History (<?php echo $order_count; ?> orders)</a></p>
          <?php endif; ?>
        </div>
      <?php else: ?>
        
        <!-- Main Cart Content - displays when items exist -->
        <div class="main-content">
          <!-- Cart Summary Header with item count -->
          <div style="background: linear-gradient(135deg, #C647CC, #ECC7ED); color: white; padding: 15px; text-align: center; font-weight: bold; border-radius: 10px 10px 0 0;">
            🛒 Shopping Cart - <?php echo $total_items; ?> item(s)
          </div>
          
          <!-- Table Header - defines column structure for cart items -->
          <div class="table-header">
            <div>Image</div>
            <div>Product</div>
            <div>Unit Price</div>
            <div>Quantity</div>
            <div>Total</div>
            <div>Action</div>
          </div>
          
          <!-- Products Container - scrollable area for cart items -->
          <div class="products-container" id="products-container">
            <?php foreach ($cart_items as $index => $item): ?>
              <!-- Product Row - each cart item with stock status styling -->
              <div class="product-row <?php echo $item['stock_status']; ?>" id="product-row-<?php echo $index; ?>">
                
                <!-- Product Image Column -->
                <div>
                  <?php if ($item['has_image']): ?>
                    <!-- Display actual product image from database -->
                    <img src="display_image.php?id=<?php echo $item['product_id']; ?>" 
                         alt="<?php echo htmlspecialchars($item['name']); ?>"
                         class="product-image"
                         id="product-image-<?php echo $index; ?>">
                  <?php else: ?>
                    <!-- Fallback placeholder for products without images -->
                    <div class="product-placeholder" style="width: 60px; height: 60px; background: linear-gradient(135deg, #C647CC, #ECC7ED); color: white; display: flex; align-items: center; justify-content: center; border-radius: 8px; font-weight: bold;">
                      <?php echo strtoupper(substr($item['name'], 0, 1)); ?>
                    </div>
                  <?php endif; ?>
                </div>
                
                <!-- Product Name with Category and Stock Status Indicators -->
                <div class="product-name" id="product-name-<?php echo $index; ?>">
                  <div style="font-weight: bold;"><?php echo htmlspecialchars($item['name']); ?></div>
                  <div style="font-size: 11px; color: #666; margin-top: 2px;">
                    📂 <?php echo htmlspecialchars($item['category_name']); ?>
                  </div>
                  <!-- Dynamic stock status warnings -->
                  <?php if ($item['stock_status'] == 'out_of_stock'): ?>
                    <div style="color: #dc3545; font-size: 10px; font-weight: bold;">⚠️ OUT OF STOCK</div>
                  <?php elseif ($item['stock_status'] == 'insufficient_stock'): ?>
                    <div style="color: #ffc107; font-size: 10px; font-weight: bold;">⚠️ Only <?php echo $item['stock_quantity']; ?> available</div>
                  <?php elseif ($item['stock_status'] == 'low_stock'): ?>
                    <div style="color: #fd7e14; font-size: 10px;">⚡ Low stock (<?php echo $item['stock_quantity']; ?> left)</div>
                  <?php endif; ?>
                </div>
                
                <!-- Unit Price Display -->
                <div class="price-display" id="unit-price-<?php echo $index; ?>">
                  ₱<?php echo number_format($item['price'], 2); ?>
                </div>
                
                <!-- Quantity Controls with Stock-aware Disable Logic -->
                <div class="quantity-controls">
                  <!-- Decrease quantity button -->
                  <button class="qty-btn" 
                          onclick="updateQuantity(<?php echo $item['cart_item_id']; ?>, -1, <?php echo $index; ?>)"
                          id="minus-btn-<?php echo $index; ?>"
                          <?php echo ($item['stock_status'] == 'out_of_stock') ? 'disabled' : ''; ?>>-</button>
                  
                  <!-- Current quantity display -->
                  <span class="qty-display" id="quantity-display-<?php echo $index; ?>">
                    <?php echo $item['quantity']; ?>
                  </span>
                  
                  <!-- Increase quantity button - disabled when at stock limit -->
                  <button class="qty-btn" 
                          onclick="updateQuantity(<?php echo $item['cart_item_id']; ?>, 1, <?php echo $index; ?>)"
                          id="add-btn-<?php echo $index; ?>"
                          <?php echo ($item['stock_status'] == 'out_of_stock' || $item['quantity'] >= $item['stock_quantity']) ? 'disabled' : ''; ?>>+</button>
                </div>
                
                <!-- Total Price for This Item (quantity × unit price) -->
                <div class="total-price" id="total-price-<?php echo $index; ?>">
                  ₱<?php echo number_format($item['subtotal'], 2); ?>
                </div>
                
                <!-- Delete/Remove Button -->
                <div>
                  <button class="delete-btn" 
                          onclick="removeFromCart(<?php echo $item['cart_item_id']; ?>, <?php echo $index; ?>)"
                          id="delete-btn-<?php echo $index; ?>">
                    🗑️ Remove
                  </button>
                </div>
                
                <!-- Hidden Data Container for AJAX Operations -->
                <!-- Stores item information for JavaScript access without exposing sensitive data -->
                <div id="item-data-<?php echo $index; ?>" 
                     data-cart-item-id="<?php echo $item['cart_item_id']; ?>"
                     data-product-id="<?php echo $item['product_id']; ?>"
                     data-price="<?php echo $item['price']; ?>"
                     data-name="<?php echo htmlspecialchars($item['name']); ?>"
                     data-stock="<?php echo $item['stock_quantity']; ?>"
                     data-stock-status="<?php echo $item['stock_status']; ?>"
                     style="display: none;"></div>
                
              </div>
            <?php endforeach; ?>
          </div>
          
          <!-- Cart Summary Footer with Totals and Free Delivery Notice -->
          <div style="background: #f8f9fa; padding: 15px; border-top: 2px solid #dee2e6;">
            <div style="display: flex; justify-content: space-between; margin-bottom: 10px;">
              <span>Subtotal (<span id="total-items-count"><?php echo $total_items; ?></span> items):</span>
              <span style="font-weight: bold;" id="subtotal-display">₱<?php echo number_format($total, 2); ?></span>
            </div>
            <!-- Dynamic free delivery notice - shows remaining amount needed -->
            <div id="free-delivery-notice" style="font-size: 12px; color: #28a745; margin-bottom: 10px; <?php echo ($total >= 500 || $delivery_fee == 0) ? 'display: none;' : ''; ?>">
              💡 Add ₱<span id="free-delivery-amount"><?php echo number_format(500 - $total, 2); ?></span> more for FREE delivery!
            </div>
          </div>
        </div>
        
        <!-- Checkout Modal - Full checkout form with map integration -->
        <div id="checkout-modal" class="checkout-modal">
          <div class="checkout-modal-content">
            <span class="checkout-modal-close" onclick="closeCheckoutModal()">&times;</span>
            <div class="payment-title">💳 Checkout Details</div>
            
            <!-- Pre-filled Customer Information -->
            <div class="payment-section">
              <div class="payment-label">👤 Customer Name:</div>
              <input type="text" class="payment-input" id="customer-name"
                     value="<?php echo htmlspecialchars($user_info['name']); ?>" readonly>
            </div>
            
            <div class="payment-section">
              <div class="payment-label">📧 Email Address:</div>
              <input type="email" class="payment-input" id="customer-email"
                     value="<?php echo htmlspecialchars($user_info['email']); ?>" readonly>
            </div>
            
            <!-- Delivery Address with Map Integration -->
            <div class="payment-section">
              <div class="payment-label">🏠 Delivery Address:</div>
              <textarea class="payment-input" id="delivery-address"
                        placeholder="Enter your complete delivery address or use map below"
                        rows="3"
                        style="resize: vertical; min-height: 60px; line-height: 1.4;"
                        required></textarea>
              
              <!-- Free Map Integration using Leaflet + OpenStreetMap -->
              <div class="map-container">
                <button type="button" class="map-toggle" onclick="toggleMap()">
                  📍 Pick Location on Map
                </button>
                <!-- Interactive map for location selection -->
                <div id="map"></div>
                <div class="map-instructions" id="map-instructions" style="display: none;">
                  🎯 Click anywhere on the map to set your delivery location
                </div>
              </div>
              
              <!-- Address Suggestions from free geocoding service -->
              <div class="address-suggestions" id="address-suggestions"></div>
            </div>
            
            <!-- Contact Information -->
            <div class="payment-section">
              <div class="payment-label">📱 Contact Number:</div>
              <input type="tel" class="payment-input" id="customer-phone"
                     placeholder="09XXXXXXXXX" pattern="[0-9]{11}" required>
            </div>
            
            <!-- Payment Method Selection -->
            <div class="payment-section">
              <div class="payment-label">💰 Payment Method:</div>
              <div class="payment-methods">
                <div class="payment-method" data-method="credit_card" title="Credit Card">
                  <img src="images/4341764.png" alt="Card">
                </div>
                <div class="payment-method" data-method="gcash" title="GCash">
                  <img src="images/gcash-logo_brandlogos.net_kiaqh.png" alt="GCash">
                </div>
                <div class="payment-method" data-method="cash_on_delivery" title="Cash on Delivery">
                  <img src="images/32724.png" alt="COD">
                </div>
              </div>
            </div>
            
            <!-- Final Checkout Button -->
            <button class="checkout-btn" onclick="checkout()" id="checkout-btn">
              🛒 Confirm Checkout - ₱<span id="total-amount"><?php echo number_format($grand_total, 2); ?></span>
            </button>
            
            <!-- Clear Cart Option -->
            <div style="text-align: center; margin-top: 10px;">
              <button onclick="clearCart()" style="background: #dc3545; color: white; border: none; padding: 8px 16px; border-radius: 6px; font-size: 12px; cursor: pointer;">
                🗑️ Clear All Items
              </button>
            </div>
          </div>
        </div>
        
        <!-- Floating Checkout Trigger Button -->
        <button class="checkout-btn" style="position: absolute; right: 60px; top: 600px; width: 300px; z-index: 10;" onclick="openCheckoutModal()" id="open-checkout-btn">
          🛒 Proceed to Checkout - ₱<span id="total-amount"><?php echo number_format($grand_total, 2); ?></span>
        </button>
        
      <?php endif; ?>
    </div>
    
    <script>
      // Global Variables for Cart State Management
      let isUpdating = false; // Prevents concurrent AJAX requests
      let cartTotal = <?php echo $total; ?>; // Current subtotal
      let deliveryFee = <?php echo $delivery_fee; ?>; // Delivery fee (₱50 or free)
      let grandTotal = <?php echo $grand_total; ?>; // Total including delivery
      let totalItems = <?php echo $total_items; ?>; // Total item count
      
      // Map Integration Variables
      let map; // Leaflet map instance
      let marker; // Delivery location marker
      let isMapVisible = false; // Map visibility state
      let isMapLoaded = false; // Map initialization state
      
      // Initialize Free Map using Leaflet + OpenStreetMap (No API Key Required)
      function initMap() {
        try {
          // Default location (Manila, Philippines) - you can change this
          const defaultLocation = [14.5995, 120.9842];
          
          // Create map instance with OpenStreetMap tiles (completely free)
          map = L.map('map', {
            center: defaultLocation,
            zoom: 13,
            zoomControl: true,
            scrollWheelZoom: true
          });
          
          // Add free OpenStreetMap tiles - no API key needed
          L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors',
            maxZoom: 19
          }).addTo(map);
          
          // Alternative free tile providers you can use:
          // Satellite view: https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}
          // Dark theme: https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png
          
          isMapLoaded = true;
          
          // Add click listener for delivery location selection
          map.on('click', function(e) {
            setMarker(e.latlng);
            reverseGeocode(e.latlng); // Convert coordinates to address
          });
          
          // Try to get user's current location using browser geolocation
          if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(
              function(position) {
                const userLocation = [position.coords.latitude, position.coords.longitude];
                map.setView(userLocation, 15);
                
                // Add "You are here" marker
                L.marker(userLocation)
                  .addTo(map)
                  .bindPopup('📍 Your current location')
                  .openPopup();
              },
              function() {
                console.log('Geolocation not available, using default location');
              }
            );
          }
          
          // Setup address autocomplete functionality
          setupAddressAutocomplete();
          
        } catch (error) {
          console.error('Error initializing map:', error);
          hideMapFeatures(); // Graceful fallback
        }
      }
      
      // Hide map features if map fails to load - graceful degradation
      function hideMapFeatures() {
        const mapContainer = document.querySelector('.map-container');
        if (mapContainer) {
          mapContainer.style.display = 'none';
        }
        
        // Add fallback message for users
        const addressSection = document.querySelector('.payment-section:has(#delivery-address)');
        if (addressSection && !document.getElementById('map-fallback-notice')) {
          const fallbackNotice = document.createElement('div');
          fallbackNotice.id = 'map-fallback-notice';
          fallbackNotice.style.cssText = 'font-size: 11px; color: rgba(255,255,255,0.7); margin-top: 8px; text-align: center;';
          fallbackNotice.textContent = '📍 Map feature unavailable - please enter address manually';
          addressSection.appendChild(fallbackNotice);
        }
      }
      
      // Toggle map visibility with proper loading checks
      function toggleMap() {
        if (!isMapLoaded) {
          showNotification('Map not available - please enter address manually', 'error');
          return;
        }
        
        const mapElement = document.getElementById('map');
        const mapInstructions = document.getElementById('map-instructions');
        const toggleButton = document.querySelector('.map-toggle');
        
        if (isMapVisible) {
          // Hide map
          mapElement.style.display = 'none';
          mapInstructions.style.display = 'none';
          toggleButton.textContent = '📍 Pick Location on Map';
          isMapVisible = false;
        } else {
          // Show map
          mapElement.style.display = 'block';
          mapInstructions.style.display = 'block';
          toggleButton.textContent = '🔼 Hide Map';
          isMapVisible = true;
          
          // Trigger map resize to ensure proper rendering after CSS display change
          setTimeout(() => {
            if (map) {
              map.invalidateSize();
            }
          }, 100);
        }
      }
      
      // Set delivery location marker on map
      function setMarker(location) {
        // Remove existing marker if present
        if (marker) {
          map.removeLayer(marker);
        }
        
        // Create custom delivery marker icon
        const deliveryIcon = L.divIcon({
          html: '📦',
          className: 'delivery-marker',
          iconSize: [30, 30],
          iconAnchor: [15, 15]
        });
        
        // Add new marker at clicked location
        marker = L.marker([location.lat, location.lng], {
          icon: deliveryIcon,
          title: 'Delivery Location'
        }).addTo(map);
        
        // Add popup with delivery information
        marker.bindPopup('🚚 Delivery Location<br><small>Click elsewhere to change</small>').openPopup();
      }
      
      // Reverse geocode coordinates to address using free Nominatim service
      async function reverseGeocode(location) {
        try {
          // Use OpenStreetMap's free Nominatim geocoding service
          const response = await fetch(
            `https://nominatim.openstreetmap.org/reverse?format=json&lat=${location.lat}&lon=${location.lng}&zoom=18&addressdetails=1`
          );
          
          const data = await response.json();
          
          if (data && data.display_name) {
            const address = data.display_name;
            // Auto-fill the address field
            document.getElementById('delivery-address').value = address;
            
            // Visual feedback for successful address selection
            const addressField = document.getElementById('delivery-address');
            addressField.style.background = 'rgba(40, 167, 69, 0.2)';
            addressField.style.borderColor = '#28a745';
            
            // Reset visual feedback after 2 seconds
            setTimeout(() => {
              addressField.style.background = 'rgba(255,255,255,0.1)';
              addressField.style.borderColor = '#8ADEF1';
            }, 2000);
            
            showNotification('📍 Location selected from map!', 'success');
          } else {
            showNotification('Could not get address for this location', 'error');
          }
        } catch (error) {
          console.error('Geocoding error:', error);
          showNotification('Could not get address for this location', 'error');
        }
      }
      
      // Address autocomplete using free Nominatim search service
      function setupAddressAutocomplete() {
        if (!isMapLoaded) return;
        
        const addressInput = document.getElementById('delivery-address');
        const suggestionsContainer = document.getElementById('address-suggestions');
        
        let timeout;
        
        // Listen for address input changes
        addressInput.addEventListener('input', function() {
          const query = this.value.trim();
          
          clearTimeout(timeout);
          
          // Only search for addresses with 3+ characters
          if (query.length < 3) {
            suggestionsContainer.style.display = 'none';
            return;
          }
          
          // Debounce requests to be respectful to free service
          timeout = setTimeout(() => {
            getAddressSuggestions(query);
          }, 500);
        });
        
        // Hide suggestions when clicking outside
        document.addEventListener('click', function(event) {
          if (!addressInput.contains(event.target) && !suggestionsContainer.contains(event.target)) {
            suggestionsContainer.style.display = 'none';
          }
        });
      }
      
      // Get address suggestions using free Nominatim search service
      async function getAddressSuggestions(query) {
        const suggestionsContainer = document.getElementById('address-suggestions');
        
        try {
          // Use Nominatim search API (free OpenStreetMap geocoding)
          const response = await fetch(
            `https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(query)}&countrycodes=ph&limit=5&addressdetails=1`
          );
          
          const suggestions = await response.json();
          displayAddressSuggestions(suggestions);
          
        } catch (error) {
          console.error('Error getting address suggestions:', error);
          suggestionsContainer.style.display = 'none';
        }
      }
      
      // Display address suggestions in dropdown
      function displayAddressSuggestions(suggestions) {
        const suggestionsContainer = document.getElementById('address-suggestions');
        suggestionsContainer.innerHTML = '';
        
        if (!suggestions || suggestions.length === 0) {
          suggestionsContainer.style.display = 'none';
          return;
        }
        
        // Create suggestion elements
        suggestions.forEach(function(suggestion) {
          const suggestionElement = document.createElement('div');
          suggestionElement.className = 'address-suggestion';
          
          suggestionElement.innerHTML = `
            <span class="location-icon">📍</span>
            ${suggestion.display_name}
          `;
          
          // Handle suggestion selection
          suggestionElement.addEventListener('click', function() {
            document.getElementById('delivery-address').value = suggestion.display_name;
            suggestionsContainer.style.display = 'none';
            
            // Center map on selected location
            const lat = parseFloat(suggestion.lat);
            const lon = parseFloat(suggestion.lon);
            
            if (lat && lon) {
              map.setView([lat, lon], 16);
              setMarker({ lat: lat, lng: lon });
              
              // Show map if hidden
              if (!isMapVisible) {
                toggleMap();
              }
              
              showNotification('📍 Address selected!', 'success');
            }
          });
          
          suggestionsContainer.appendChild(suggestionElement);
        });
        
        suggestionsContainer.style.display = 'block';
      }
      
      // Function to dynamically update all cart totals and displays
      function updateCartTotals() {
        // Recalculate subtotal from all visible cart items
        cartTotal = 0;
        totalItems = 0;
        
        const productRows = document.querySelectorAll('.product-row');
        productRows.forEach((row, index) => {
          const quantityElement = document.getElementById(`quantity-display-${index}`);
          const itemData = document.getElementById(`item-data-${index}`);
          
          if (quantityElement && itemData) {
            const quantity = parseInt(quantityElement.textContent);
            const price = parseFloat(itemData.dataset.price);
            cartTotal += (quantity * price);
            totalItems += quantity;
          }
        });
        
        // Calculate delivery fee dynamically based on subtotal
        deliveryFee = cartTotal >= 500 ? 0 : 50;
        grandTotal = cartTotal + deliveryFee;
        
        // Update all total displays throughout the page
        document.getElementById('subtotal-display').textContent = `₱${cartTotal.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})}`;
        document.getElementById('total-amount').textContent = cartTotal.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
        document.getElementById('total-items-count').textContent = totalItems;
        
        // Update navigation cart badge
        const cartBadge = document.querySelector('.cart-badge');
        if (cartBadge) {
          cartBadge.textContent = totalItems;
        }
        
        // Update free delivery notice
        const freeDeliveryNotice = document.getElementById('free-delivery-notice');
        const freeDeliveryAmount = document.getElementById('free-delivery-amount');
        
        if (cartTotal < 500 && deliveryFee > 0) {
          freeDeliveryNotice.style.display = 'block';
          freeDeliveryAmount.textContent = (500 - cartTotal).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
        } else {
          freeDeliveryNotice.style.display = 'none';
        }
        
        // Add visual feedback animation for total updates
        const subtotalDisplay = document.getElementById('subtotal-display');
        const totalAmountDisplay = document.getElementById('total-amount');
        
        [subtotalDisplay, totalAmountDisplay].forEach(element => {
          element.style.transform = 'scale(1.05)';
          element.style.transition = 'transform 0.2s ease';
          
          setTimeout(() => {
            element.style.transform = 'scale(1)';
          }, 200);
        });
      }
      
      // AJAX function to update item quantity with optimistic UI updates
      async function updateQuantity(cartItemId, change, itemIndex) {
        // Prevent concurrent quantity updates
        if (isUpdating) return;
        
        isUpdating = true;
        
        // Get UI elements for this item
        const quantityDisplay = document.getElementById(`quantity-display-${itemIndex}`);
        const addBtn = document.getElementById(`add-btn-${itemIndex}`);
        const minusBtn = document.getElementById(`minus-btn-${itemIndex}`);
        const totalPriceElement = document.getElementById(`total-price-${itemIndex}`);
        
        // Add loading state visual feedback
        [addBtn, minusBtn].forEach(btn => btn?.classList.add('loading'));
        
        try {
          // Send AJAX request to update quantity in database
          const response = await fetch('update_cart.php', {
            method: 'POST',
            headers: { 
              'Content-Type': 'application/json',
              'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({ 
              cart_item_id: cartItemId, 
              change: change 
            })
          });
          
          const data = await response.json();
          
          if (data.success) {
            const currentQuantity = parseInt(quantityDisplay.textContent);
            const newQuantity = currentQuantity + change;
            const itemData = document.getElementById(`item-data-${itemIndex}`);
            const price = parseFloat(itemData.dataset.price);
            
            // Handle item removal (quantity <= 0)
            if (newQuantity <= 0) {
              removeItemFromDisplay(itemIndex);
              showNotification('Item removed from cart', 'success');
            } else {
              // Update quantity display
              quantityDisplay.textContent = newQuantity;
              
              // Update item total price
              const newSubtotal = newQuantity * price;
              totalPriceElement.textContent = `₱${newSubtotal.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})}`;
              
              // Update stock-aware button states
              const stock = parseInt(itemData.dataset.stock);
              const addButton = document.getElementById(`add-btn-${itemIndex}`);
              
              if (newQuantity >= stock) {
                addButton.disabled = true; // Disable add button when at stock limit
              } else {
                addButton.disabled = false;
              }
              
              // Update all cart totals dynamically
              updateCartTotals();
              
              // Visual success feedback
              quantityDisplay.classList.add('success-flash');
              setTimeout(() => {
                quantityDisplay.classList.remove('success-flash');
              }, 300);
              
              showNotification(`Quantity ${change > 0 ? 'increased' : 'decreased'}`, 'success');
            }
          } else {
            showNotification(data.message || 'Error updating quantity', 'error');
          }
          
        } catch (error) {
          console.error('Error:', error);
          showNotification('Network error. Please try again.', 'error');
        } finally {
          // Remove loading state
          [addBtn, minusBtn].forEach(btn => btn?.classList.remove('loading'));
          isUpdating = false;
        }
      }
      
      // AJAX function to remove item from cart with optimistic UI updates
      async function removeFromCart(cartItemId, itemIndex) {
        // Prevent concurrent operations
        if (isUpdating) return;
        
        isUpdating = true;
        
        const deleteBtn = document.getElementById(`delete-btn-${itemIndex}`);
        deleteBtn?.classList.add('loading');
        
        // Add immediate visual feedback (optimistic update)
        const productRow = document.getElementById(`product-row-${itemIndex}`);
        productRow.style.opacity = '0.5';
        productRow.style.transform = 'scale(0.98)';
        
        try {
          // Send AJAX request to remove item from database
          const response = await fetch('remove_from_cart.php', {
            method: 'POST',
            headers: { 
              'Content-Type': 'application/json',
              'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({ cart_item_id: cartItemId })
          });
          
          const data = await response.json();
          
          if (data.success) {
            removeItemFromDisplay(itemIndex);
            showNotification('Item removed from cart', 'success');
            
            // Update totals after item removal
            setTimeout(() => {
              updateCartTotals();
            }, 100);
            
          } else {
            // Restore visual state if removal failed
            productRow.style.opacity = '1';
            productRow.style.transform = 'scale(1)';
            showNotification(data.message || 'Error removing item', 'error');
          }
          
        } catch (error) {
          console.error('Error:', error);
          // Restore visual state on network error
          productRow.style.opacity = '1';
          productRow.style.transform = 'scale(1)';
          showNotification('Network error. Please try again.', 'error');
        } finally {
          deleteBtn?.classList.remove('loading');
          isUpdating = false;
        }
      }
      
      // Function to animate item removal from display
      function removeItemFromDisplay(itemIndex) {
        const productRow = document.getElementById(`product-row-${itemIndex}`);
        if (productRow) {
          // Smooth removal animation
          productRow.style.opacity = '0';
          productRow.style.transform = 'translateX(-100%)';
          productRow.style.transition = 'all 0.3s ease';
          
          setTimeout(() => {
            productRow.remove();
            
            // Update totals after removal animation completes
            updateCartTotals();
            
            // Check if cart is now empty and reload page
            const container = document.getElementById('products-container');
            if (container.children.length === 0) {
              setTimeout(() => {
                location.reload(); // Reload to show empty cart state
              }, 500);
            }
          }, 300);
        }
      }
      
      // Universal notification system for user feedback
      function showNotification(message, type) {
        // Remove any existing notifications
        const existingNotifications = document.querySelectorAll('.notification');
        existingNotifications.forEach(notification => notification.remove());
        
        // Create new notification element
        const notification = document.createElement('div');
        notification.className = `notification ${type}`;
        notification.innerHTML = `${type === 'success' ? '✅' : '❌'} ${message}`;
        
        document.body.appendChild(notification);
        
        // Trigger slide-in animation
        setTimeout(() => notification.classList.add('show'), 100);
        
        // Auto-remove notification after 3 seconds
        setTimeout(() => {
          notification.classList.remove('show');
          setTimeout(() => {
            if (notification.parentNode) {
              notification.remove();
            }
          }, 300);
        }, 3000);
      }
      
      // Enhanced checkout function - processes order and saves to database
      async function checkout() {
        // Ensure totals are current before processing
        updateCartTotals();
        
        // Validate cart is not empty
        if (cartTotal <= 0) {
          showNotification('Your cart is empty', 'error');
          return;
        }
        
        // Check for out of stock items before checkout
        const outOfStockItems = document.querySelectorAll('.product-row.out_of_stock');
        if (outOfStockItems.length > 0) {
          showNotification('Please remove out of stock items before checkout', 'error');
          return;
        }
        
        // Validate required form fields
        const deliveryAddress = document.getElementById('delivery-address').value.trim();
        const customerPhone = document.getElementById('customer-phone').value.trim();
        
        if (!deliveryAddress) {
          showNotification('Please enter your delivery address', 'error');
          document.getElementById('delivery-address').focus();
          return;
        }
        
        if (!customerPhone) {
          showNotification('Please enter your phone number', 'error');
          document.getElementById('customer-phone').focus();
          return;
        }
        
        // Validate phone number format (11 digits for Philippine numbers)
        const phoneRegex = /^[0-9]{11}$/;
        if (!phoneRegex.test(customerPhone)) {
          showNotification('Please enter a valid 11-digit phone number', 'error');
          document.getElementById('customer-phone').focus();
          return;
        }
        
        // Validate payment method selection
        const selectedPaymentMethod = document.querySelector('.payment-method.selected');
        if (!selectedPaymentMethod) {
          showNotification('Please select a payment method', 'error');
          return;
        }
        
        // Disable checkout button during processing
        const checkoutBtn = document.getElementById('checkout-btn');
        checkoutBtn.style.opacity = '0.7';
        checkoutBtn.style.pointerEvents = 'none';
        checkoutBtn.textContent = '🔄 Processing Order...';
        
        // Collect all cart items data for order processing
        const cartItems = [];
        const productRows = document.querySelectorAll('.product-row');
        productRows.forEach((row, index) => {
          const itemData = document.getElementById(`item-data-${index}`);
          const quantityElement = document.getElementById(`quantity-display-${index}`);
          
          if (itemData && quantityElement) {
            cartItems.push({
              product_id: itemData.dataset.productId,
              quantity: parseInt(quantityElement.textContent),
              price: parseFloat(itemData.dataset.price),
              name: itemData.dataset.name
            });
          }
        });
        
        // Create comprehensive checkout data object
        const checkoutData = {
          delivery_address: deliveryAddress,
          phone: customerPhone,
          payment_method: selectedPaymentMethod.dataset.method,
          subtotal: cartTotal,
          delivery_fee: deliveryFee,
          total_amount: grandTotal,
          item_count: totalItems,
          cart_items: cartItems
        };
        
        try {
          // Send order data to backend for processing
          const response = await fetch('process_checkout.php', {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json',
              'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify(checkoutData)
          });
          
          const result = await response.json();
          
          if (result.success) {
            // Show success notification
            showNotification('🎉 Order placed successfully!', 'success');
            
            // Close checkout modal
            closeCheckoutModal();
            
            // Show order confirmation modal
            showOrderConfirmation(result.order_id, result.total_amount);
            
            // Clear cart after successful order
            setTimeout(() => {
              clearCartAfterOrder();
            }, 2000);
            
          } else {
            throw new Error(result.message || 'Failed to process order');
          }
          
        } catch (error) {
          console.error('Checkout error:', error);
          showNotification('❌ Order failed: ' + error.message, 'error');
          
          // Restore checkout button functionality
          checkoutBtn.style.opacity = '1';
          checkoutBtn.style.pointerEvents = 'auto';
          checkoutBtn.textContent = '🛒 Confirm Checkout - ₱' + grandTotal.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
        }
      }
      
      // Show order confirmation modal after successful checkout
      function showOrderConfirmation(orderId, totalAmount) {
        const confirmationModal = document.createElement('div');
        confirmationModal.className = 'checkout-modal show';
        confirmationModal.innerHTML = `
          <div class="checkout-modal-content" style="text-align: center; background: linear-gradient(135deg, #28a745, #20c997);">
            <h2 style="margin: 0 0 20px 0; color: white;">🎉 Order Confirmed!</h2>
            <div style="background: rgba(255,255,255,0.1); padding: 20px; border-radius: 10px; margin: 20px 0;">
              <p style="margin: 5px 0; color: white;"><strong>Order ID:</strong> #${orderId}</p>
              <p style="margin: 5px 0; color: white;"><strong>Total Amount:</strong> ₱${totalAmount.toLocaleString('en-US', {minimumFractionDigits: 2})}</p>
              <p style="margin: 15px 0 5px 0; color: white; font-size: 14px;">📧 Confirmation email sent!</p>
              <p style="margin: 5px 0; color: white; font-size: 14px;">🚚 Estimated delivery: 3-5 business days</p>
            </div>
            <button onclick="this.parentElement.parentElement.remove(); location.reload();" 
                    style="background: white; color: #28a745; border: none; padding: 12px 24px; border-radius: 8px; font-weight: bold; cursor: pointer; font-size: 16px;">
              Continue Shopping
            </button>
          </div>
        `;
        document.body.appendChild(confirmationModal);
      }
      
      // Clear cart from database after successful order
      async function clearCartAfterOrder() {
        try {
          await fetch('clear_cart.php', {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json',
              'X-Requested-With': 'XMLHttpRequest'
            }
          });
        } catch (error) {
          console.error('Error clearing cart:', error);
        }
      }

      // Navigation Functions
      
      // Navigate to order history page
      function goToOrderHistory() {
        window.location.href = 'order_history.php';
      }
      
      // Navigate to user profile page
      function goToProfile() {
        window.location.href = 'UserDashboard.php';
      }
      
      // Enhanced DOM Ready Event Handler
      document.addEventListener('DOMContentLoaded', function() {
        console.log('Enhanced cart system with free map initialized');
        showNotification('Cart loaded successfully', 'success');
        
        // Initialize all cart totals
        updateCartTotals();
        
        // Initialize free map (no API key required)
        initMap();
        
        // Enhanced payment method selection with visual feedback
        const paymentMethods = document.querySelectorAll('.payment-method');
        paymentMethods.forEach(method => {
          method.addEventListener('click', function() {
            // Remove selected class from all payment methods
            paymentMethods.forEach(m => m.classList.remove('selected'));
            // Add selected class to clicked method
            this.classList.add('selected');
            
            const methodName = this.getAttribute('title');
            showNotification(`${methodName} selected`, 'success');
          });
        });
        
        // Add periodic total refresh to catch any discrepancies
        // Runs every 30 seconds when not actively updating
        setInterval(() => {
          if (!isUpdating) {
            updateCartTotals();
          }
        }, 30000);
      });
      
      // Modal Management Functions
      
      // Open checkout modal with accessibility focus
      function openCheckoutModal() {
        document.getElementById('checkout-modal').classList.add('show');
        document.body.style.overflow = 'hidden'; // Prevent background scrolling
        
        // Focus on delivery address field for accessibility
        setTimeout(() => {
          const addr = document.getElementById('delivery-address');
          if (addr) addr.focus();
        }, 200);
      }
      
      // Close checkout modal and restore scrolling
      function closeCheckoutModal() {
        document.getElementById('checkout-modal').classList.remove('show');
        document.body.style.overflow = ''; // Restore scrolling
      }
      
      // Close modal on ESC key press for accessibility
      document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') closeCheckoutModal();
      });
      
      // Clean up any old payment sidebar elements (compatibility)
      const oldSidebar = document.querySelector('.payment-sidebar');
      if (oldSidebar) {
        oldSidebar.remove();
      }
    </script>
    
    <style>
      /* Custom styling for map markers and popups */
      
      /* Delivery marker styling */
      .delivery-marker {
        background: #C647CC;
        border: 2px solid white;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 16px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.3);
      }
      
      /* Leaflet popup customization to match app theme */
      .leaflet-popup-content-wrapper {
        background: linear-gradient(135deg, #C647CC, #ECC7ED);
        color: white;
        border-radius: 8px;
      }
      
      .leaflet-popup-content {
        margin: 8px 12px;
        font-size: 12px;
        font-weight: bold;
      }
      
      .leaflet-popup-tip {
        background: #C647CC;
      }
      
      /* Map container visual enhancements */
      #map {
        border: 2px solid rgba(198, 71, 204, 0.3);
        border-radius: 6px;
      }
      
      /* Address suggestions dropdown styling */
      .address-suggestion {
        background: rgba(0,0,0,0.8);
        color: white;
        border-bottom: 1px solid rgba(255,255,255,0.1);
      }
      
      .address-suggestion:hover {
        background: rgba(198, 71, 204, 0.8);
      }
    </style>
  </body>
</html>