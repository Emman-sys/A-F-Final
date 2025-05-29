<?php
session_start();
require 'db_connect.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: Welcome.php');
    exit;
}

$user_id = $_SESSION['user_id'];

// Get user info for the payment form
$user_stmt = $conn->prepare("SELECT name, email FROM users WHERE user_id = ?");
if (!$user_stmt) {
    die("Error preparing user query: " . $conn->error);
}
$user_stmt->bind_param("i", $user_id);
$user_stmt->execute();
$user_info = $user_stmt->get_result()->fetch_assoc();

// Check if cartitems table exists
$table_check = $conn->query("SHOW TABLES LIKE 'cartitems'");
if ($table_check->num_rows == 0) {
    $cart_items = [];
} else {
    // Get cart items
    $stmt = $conn->prepare("
        SELECT ci.cart_item_id, ci.quantity, p.product_id, p.name, p.price, p.stock_quantity,
               (ci.quantity * p.price) as subtotal
        FROM cartitems ci
        JOIN cart c ON ci.cart_id = c.cart_id
        JOIN products p ON ci.product_id = p.product_id
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

$total = 0;
foreach ($cart_items as $item) {
    $total += $item['subtotal'];
}
?>

<!DOCTYPE html>
<html lang="en">
  <head>
   <meta charset="utf-8" />
   <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
   <title>A&F - Your Cart</title>
   <link href="https://fonts.googleapis.com/css2?family=Merriweather:wght@400;700&family+Merriweather+Sans:wght@700&display=swap" rel="stylesheet" />
   <link rel="stylesheet" href="styles.css">
    
    <style>
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
        width: 100vw;
        height: 100px; /* DECREASED from 140px */
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
      
      /* Brand - make it smaller */
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
    </style>
  </head>
  <body>
    <div class="container">
      <!-- Header -->
      <div class="header-gradient"></div>
      <a href="MainPage.php" class="brand">A&F</a>
      
      <!-- ADD: Navigation Icons -->
    <div class="nav-icons">
      <!-- Cart Icon (current page - highlighted) -->
      <div class="nav-icon current" title="Cart (Current Page)">
        <img src="images/cart.png" alt="Cart">
        <span class="cart-badge"><?php echo count($cart_items); ?></span>
      </div>
      
      <!-- User Profile Icon -->
      <div class="nav-icon" onclick="goToProfile()" title="Profile">
        <img src="images/users.png" alt="Profile">
      </div>
    </div>
    
    <?php if (empty($cart_items)): ?>
        <div class="empty-cart">
          <h2>🛒 Your cart is empty</h2>
          <p><a href="MainPage.php">Continue Shopping</a></p>
        </div>
      <?php else: ?>
        
        <!-- Main Cart Content -->
        <div class="main-content">
          <!-- Table Header -->
          <div class="table-header">
            <div>Image</div>
            <div>Product</div>
            <div>Unit Price</div>
            <div>Quantity</div>
            <div>Total</div>
            <div>Action</div>
          </div>
          
          <!-- Products Container -->
          <div class="products-container" id="products-container">
            <?php foreach ($cart_items as $index => $item): ?>
              <div class="product-row" id="product-row-<?php echo $index; ?>">
                
                <!-- Product Image -->
                <div>
                  <img src="display_image.php?id=<?php echo $item['product_id']; ?>" 
                       alt="<?php echo htmlspecialchars($item['name']); ?>"
                       class="product-image"
                       id="product-image-<?php echo $index; ?>">
                </div>
                
                <!-- Product Name -->
                <div class="product-name" id="product-name-<?php echo $index; ?>">
                  <?php echo htmlspecialchars($item['name']); ?>
                </div>
                
                <!-- Unit Price -->
                <div class="price-display" id="unit-price-<?php echo $index; ?>">
                  ₱<?php echo number_format($item['price'], 0); ?>
                </div>
                
                <!-- Quantity Controls -->
                <div class="quantity-controls">
                  <button class="qty-btn" 
                          onclick="updateQuantity(<?php echo $item['cart_item_id']; ?>, -1, <?php echo $index; ?>)"
                          id="minus-btn-<?php echo $index; ?>">-</button>
                  
                  <span class="qty-display" id="quantity-display-<?php echo $index; ?>">
                    <?php echo $item['quantity']; ?>
                  </span>
                  
                  <button class="qty-btn" 
                          onclick="updateQuantity(<?php echo $item['cart_item_id']; ?>, 1, <?php echo $index; ?>)"
                          id="add-btn-<?php echo $index; ?>">+</button>
                </div>
                
                <!-- Total Price -->
                <div class="total-price" id="total-price-<?php echo $index; ?>">
                  ₱<?php echo number_format($item['subtotal'], 0); ?>
                </div>
                
                <!-- Delete Button -->
                <div>
                  <button class="delete-btn" 
                          onclick="removeFromCart(<?php echo $item['cart_item_id']; ?>, <?php echo $index; ?>)"
                          id="delete-btn-<?php echo $index; ?>">
                    Delete
                  </button>
                </div>
                
                <!-- Hidden data for AJAX -->
                <div id="item-data-<?php echo $index; ?>" 
                     data-cart-item-id="<?php echo $item['cart_item_id']; ?>"
                     data-product-id="<?php echo $item['product_id']; ?>"
                     data-price="<?php echo $item['price']; ?>"
                     data-name="<?php echo htmlspecialchars($item['name']); ?>"
                     style="display: none;"></div>
                
              </div>
            <?php endforeach; ?>
          </div>
        </div>
        
        <!-- Payment Sidebar -->
        <div class="payment-sidebar">
          <div class="payment-title">Payment Info</div>
          
          <div class="payment-section">
            <div class="payment-label">Name:</div>
            <input type="text" class="payment-input" 
                   value="<?php echo htmlspecialchars($user_info['name']); ?>" readonly>
          </div>
          
          <div class="payment-section">
            <div class="payment-label">Email:</div>
            <input type="email" class="payment-input" 
                   value="<?php echo htmlspecialchars($user_info['email']); ?>" readonly>
          </div>
          
          <div class="payment-section">
            <div class="payment-label">Address:</div>
            <input type="text" class="payment-input" placeholder="Enter your address">
          </div>
          
          <div class="payment-section">
            <div class="payment-label">Phone:</div>
            <input type="tel" class="payment-input" placeholder="Enter phone number">
          </div>
          
          <div class="payment-section">
            <div class="payment-label">Payment Method:</div>
            <div class="payment-methods">
              <div class="payment-method" title="Credit Card">
                <img src="images/credit-card.png" alt="Card">
              </div>
              <div class="payment-method" title="GCash">
                <img src="images/gcash.png" alt="GCash">
              </div>
              <div class="payment-method" title="Cash">
                <img src="images/peso.png" alt="Cash">
              </div>
            </div>
          </div>
          
          <button class="checkout-btn" onclick="checkout()" id="checkout-btn">
            Checkout - ₱<span id="total-amount"><?php echo number_format($total, 0); ?></span>
          </button>
        </div>
        
      <?php endif; ?>
    </div>
    
    <script>
      // Global variables
      let isUpdating = false;
      let cartTotal = <?php echo $total; ?>;
      
      // AJAX function to update quantity
      async function updateQuantity(cartItemId, change, itemIndex) {
        if (isUpdating) return;
        
        isUpdating = true;
        
        // Visual feedback
        const quantityDisplay = document.getElementById(`quantity-display-${itemIndex}`);
        const addBtn = document.getElementById(`add-btn-${itemIndex}`);
        const minusBtn = document.getElementById(`minus-btn-${itemIndex}`);
        const totalPriceElement = document.getElementById(`total-price-${itemIndex}`);
        
        // Add loading state
        [addBtn, minusBtn].forEach(btn => btn?.classList.add('loading'));
        
        try {
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
            
            if (newQuantity <= 0) {
              removeItemFromDisplay(itemIndex);
              showNotification('Item removed from cart', 'success');
            } else {
              quantityDisplay.textContent = newQuantity;
              
              const newSubtotal = newQuantity * price;
              totalPriceElement.textContent = `₱${Math.round(newSubtotal).toLocaleString()}`;
              
              cartTotal += (change * price);
              document.getElementById('total-amount').textContent = Math.round(cartTotal).toLocaleString();
              
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
          [addBtn, minusBtn].forEach(btn => btn?.classList.remove('loading'));
          isUpdating = false;
        }
      }
      
      // AJAX function to remove item - UPDATED: No confirmation dialog
      async function removeFromCart(cartItemId, itemIndex) {
        if (isUpdating) return;
        
        isUpdating = true;
        
        const deleteBtn = document.getElementById(`delete-btn-${itemIndex}`);
        deleteBtn?.classList.add('loading');
        
        // Add immediate visual feedback
        const productRow = document.getElementById(`product-row-${itemIndex}`);
        productRow.style.opacity = '0.5';
        productRow.style.transform = 'scale(0.98)';
        
        try {
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
            const itemData = document.getElementById(`item-data-${itemIndex}`);
            const quantityDisplay = document.getElementById(`quantity-display-${itemIndex}`);
            const price = parseFloat(itemData.dataset.price);
            const quantity = parseInt(quantityDisplay.textContent);
            const itemSubtotal = price * quantity;
            
            cartTotal -= itemSubtotal;
            document.getElementById('total-amount').textContent = Math.round(cartTotal).toLocaleString();
            
            removeItemFromDisplay(itemIndex);
            showNotification('Item removed from cart', 'success');
            
          } else {
            // Restore visual state if error
            productRow.style.opacity = '1';
            productRow.style.transform = 'scale(1)';
            showNotification(data.message || 'Error removing item', 'error');
          }
          
        } catch (error) {
          console.error('Error:', error);
          // Restore visual state if error
          productRow.style.opacity = '1';
          productRow.style.transform = 'scale(1)';
          showNotification('Network error. Please try again.', 'error');
        } finally {
          deleteBtn?.classList.remove('loading');
          isUpdating = false;
        }
      }
      
      // Function to remove item from display
      function removeItemFromDisplay(itemIndex) {
        const productRow = document.getElementById(`product-row-${itemIndex}`);
        if (productRow) {
          productRow.style.opacity = '0';
          productRow.style.transform = 'translateX(-100%)';
          productRow.style.transition = 'all 0.3s ease';
          
          setTimeout(() => {
            productRow.remove();
            
            // Check if cart is now empty
            const container = document.getElementById('products-container');
            if (container.children.length === 0) {
              setTimeout(() => {
                location.reload();
              }, 500);
            }
          }, 300);
        }
      }
      
      // Notification system
      function showNotification(message, type) {
        const existingNotifications = document.querySelectorAll('.notification');
        existingNotifications.forEach(notification => notification.remove());
        
        const notification = document.createElement('div');
        notification.className = `notification ${type}`;
        notification.innerHTML = `${type === 'success' ? '✅' : '❌'} ${message}`;
        
        document.body.appendChild(notification);
        
        setTimeout(() => notification.classList.add('show'), 100);
        
        setTimeout(() => {
          notification.classList.remove('show');
          setTimeout(() => {
            if (notification.parentNode) {
              notification.remove();
            }
          }, 300);
        }, 3000);
      }
      
      // Checkout function
      function checkout() {
        if (cartTotal <= 0) {
          showNotification('Your cart is empty', 'error');
          return;
        }
        
        const checkoutBtn = document.getElementById('checkout-btn');
        checkoutBtn.style.opacity = '0.7';
        checkoutBtn.style.pointerEvents = 'none';
        checkoutBtn.textContent = 'Processing...';
        
        setTimeout(() => {
          window.location.href = 'checkout.php';
        }, 500);
      }
      
      // Navigation function for profile
      function goToProfile() {
        // You can redirect to profile page or show profile dropdown
        window.location.href = 'profile.php'; // or whatever your profile page is called
        // Alternative: show a profile dropdown menu
        // showProfileDropdown();
      }
      
      // Optional: Add cart click function (since we're on cart page)
      function goToCart() {
        showNotification('You are already on the cart page!', 'success');
      }
      
      // Initialize page
      document.addEventListener('DOMContentLoaded', function() {
        console.log('Dynamic cart system initialized');
        showNotification('Cart loaded successfully', 'success');
      });
      
    </script>
  </body>
</html>