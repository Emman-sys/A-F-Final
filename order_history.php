<?php
session_start();
require 'db_connect.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: Welcome.php');
    exit;
}

$user_id = $_SESSION['user_id'];

// Get user info
$user_stmt = $conn->prepare("SELECT name, email FROM users WHERE user_id = ?");
if (!$user_stmt) {
    die("Error preparing user query: " . $conn->error);
}
$user_stmt->bind_param("i", $user_id);
$user_stmt->execute();
$user_info = $user_stmt->get_result()->fetch_assoc();

// Get user's orders with payment info
$orders_stmt = $conn->prepare("
    SELECT o.order_id, o.order_date, o.status, o.total_amount, o.shipping_address,
           p.payment_method, p.payment_status,
           COUNT(oi.order_item_id) as item_count
    FROM orders o
    LEFT JOIN payments p ON o.order_id = p.order_id
    LEFT JOIN orderitems oi ON o.order_id = oi.order_id
    WHERE o.user_id = ?
    GROUP BY o.order_id
    ORDER BY o.order_date DESC
");

if (!$orders_stmt) {
    die("Error preparing orders query: " . $conn->error);
}

$orders_stmt->bind_param("i", $user_id);
$orders_stmt->execute();
$orders = $orders_stmt->get_result()->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <link href="https://fonts.googleapis.com/css2?family=Merriweather:wght@400;700&family=Merriweather+Sans:wght@700&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css">
  <link rel="stylesheet" href="styles.css">
  <title>Order History - A&F</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
  <style>
    * {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
    }

    body {
      font-family: 'Merriweather';
      background-image: url("https://cdn.glitch.global/585aee42-d89c-4ece-870c-5b01fc1bab61/image%203.png?v=1747320934399");
      background-size: cover;
      background-repeat: no-repeat;
    }

    .header {
      background: linear-gradient(to top, #5127A3,#986C93, #E0B083);
      color: black;
      padding: 20px 30px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin: 10px;
    }

    .header-left h1 {
      font-family: 'Merriweather';
      font-size: 32px;
      font-weight: bold;
      display: inline;
    }

    .header-left span {
      font-size: 20px;
      margin-left: 10px;
    }

    .header-right i {
      font-size: 26px;
    }

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
    
    /* Order history icon - highlight current page */
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
      width: 20px;
      height: 20px;
      font-size: 12px;
      font-weight: bold;
      display: flex;
      align-items: center;
      justify-content: center;
      box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
    }

    .back-btn {
      background: none;
      border: none;
      color: #000;
      font-size: 22px;
      margin-right: 16px;
      cursor: pointer;
      display: flex;
      align-items: center;
      transition: color 0.2s;
    }
    .back-btn:hover {
      color: #E0B083;
    }

    .main {
      display: flex;
      padding: 20px;
      height: calc(100vh - 90px);
    }

    .sidebar {
      background: linear-gradient(to top, #371B70, #5127A3, #6A34D6);
      color: white;
      width: 300px;
      border-radius: 10px;
      padding: 20px;
      margin-right: 20px;
    }

    .sidebar h2 {
      margin-bottom: 20px;
      font-size: 22px;
    }

    .sidebar label {
      font-family: 'Merriweather';
      display: block;
      margin-bottom: 15px;
      font-weight: bold;
      font-size: 14px;
    }

    .sidebar .info-value {
      color: #8ADEF1;
      font-weight: normal;
      margin-left: 5px;
    }

    .sidebar .stats {
      background: rgba(255,255,255,0.1);
      padding: 15px;
      border-radius: 8px;
      margin-top: 20px;
    }

    .sidebar .stats h3 {
      font-size: 16px;
      margin-bottom: 10px;
      color: #8ADEF1;
    }

    .sidebar .stats p {
      font-size: 14px;
      margin: 5px 0;
    }

    .content {
      flex: 1;
      background-color: #fff;
      border-radius: 10px;
      padding: 10px;
      display: flex;
      flex-direction: column;
    }

    .table-header {
      display: grid;
      grid-template-columns: 1fr 1fr 1fr 1fr;
      gap: 15px;
      padding: 15px;
      font-weight: bold;
      border-bottom: 2px solid #444;
      background-color: #fff;
      flex-shrink: 0;
    }

    .order-list {
      flex: 1;
      /* Remove overflow-y: auto; to prevent scrolling */
      min-height: 0;
      display: flex;
      flex-direction: column;
      gap: 5px;
    }

    .purchase-row {
      display: grid;
      grid-template-columns: 1fr 1fr 1fr 1fr;
      gap: 15px;
      align-items: center;
      padding: 20px 15px;
      border-bottom: 1px solid #ccc;
      background-color: #fff;
      transition: background-color 0.2s;
    }

    .purchase-row:hover {
      background-color: #f8f9fa;
    }

    .order-info {
      display: flex;
      align-items: center;
      gap: 15px;
    }

    .order-info i {
      font-size: 30px;
      color: #C647CC;
    }

    .order-details h4 {
      margin: 0;
      font-size: 16px;
      color: #333;
    }

    .order-details p {
      margin: 2px 0;
      font-size: 12px;
      color: #666;
    }

    .status-badge {
      padding: 4px 8px;
      border-radius: 4px;
      font-size: 12px;
      font-weight: bold;
      text-transform: uppercase;
    }

    .status-pending {
      background: #fff3cd;
      color: #856404;
    }

    .status-processing {
      background: #d1ecf1;
      color: #0c5460;
    }

    .status-shipped {
      background: #d4edda;
      color: #155724;
    }

    .status-completed {
      background: #d4edda;
      color: #155724;
    }

    .status-cancelled {
      background: #f8d7da;
      color: #721c24;
    }

    .payment-info {
      font-size: 14px;
    }

    .payment-method {
      font-weight: bold;
      color: #C647CC;
    }

    .payment-status {
      font-size: 12px;
      margin-top: 2px;
    }

    .amount {
      font-size: 16px;
      font-weight: bold;
      color: #C647CC;
    }

    .empty-orders {
      text-align: center;
      padding: 50px;
      color: #666;
    }

    .empty-orders i {
      font-size: 48px;
      margin-bottom: 20px;
      color: #ccc;
    }

    .view-details-btn {
      background: #C647CC;
      color: white;
      border: none;
      padding: 6px 12px;
      border-radius: 4px;
      cursor: pointer;
      font-size: 12px;
      margin-top: 5px;
      transition: background 0.2s;
    }

    .view-details-btn:hover {
      background: #a03aa3;
    }

    @media (max-width: 768px) {
      .main {
        flex-direction: column;
      }

      .sidebar {
        width: 100%;
        margin-bottom: 20px;
      }

      .table-header,
      .purchase-row {
        grid-template-columns: 1fr;
        gap: 10px;
      }

      .content {
        height: auto;
      }
      
      .order-list {
        max-height: 400px;
      }
    }
  </style>
</head>
<body>
  <div class="header">
    <div class="header-left" style="display: flex; align-items: center;">
      <button class="back-btn" onclick="window.history.back()">
        <i class="fas fa-arrow-left"></i>
      </button>
      <h1 style="margin: 0 0 0 0;">A&F</h1>
      <span style="margin-left: 10px;">| Order History</span>
    </div>
    <div class="header-right">
      <!-- Navigation Icons -->
      <div class="nav-icons">
        <!-- Cart Icon -->
        <div class="nav-icon" onclick="goToCart()" title="Shopping Cart">
          <img src="images/cart.png" alt="Cart">
        </div>
        
        <!-- Order History Icon (current page - highlighted) -->
        <div class="nav-icon current" title="Order History (Current Page)">
          <img src="images/deliv.png" alt="Orders">
        </div>
        
        <!-- User Profile Icon -->
        <div class="nav-icon" onclick="goToProfile()" title="Profile">
          <img src="images/users.png" alt="Profile">
        </div>
      </div>
    </div>
  </div>

  <div class="main">
    <div class="sidebar">
      <h2>👤 User Info</h2>
      <label><strong>Name:</strong><span class="info-value"><?php echo htmlspecialchars($user_info['name']); ?></span></label>
      <label><strong>Email:</strong><span class="info-value"><?php echo htmlspecialchars($user_info['email']); ?></span></label>
      
      <div class="stats">
        <h3>📊 Order Statistics</h3>
        <p><strong>Total Orders:</strong> <?php echo count($orders); ?></p>
        <p><strong>Total Spent:</strong> ₱<?php echo number_format(array_sum(array_column($orders, 'total_amount')), 2); ?></p>
        <?php
        $status_counts = array_count_values(array_column($orders, 'status'));
        foreach ($status_counts as $status => $count):
        ?>
          <p><strong><?php echo ucfirst($status); ?>:</strong> <?php echo $count; ?></p>
        <?php endforeach; ?>
      </div>
    </div>

    <div class="content">
      <div class="table-header">
        <div>📦 Order Details</div>
        <div>📋 Status</div>
        <div>💳 Payment</div>
        <div>💰 Amount</div>
      </div>

      <div class="order-list">
        <?php if (empty($orders)): ?>
          <div class="empty-orders">
            <i class="fas fa-shopping-cart"></i>
            <h3>No Orders Yet</h3>
            <p>You haven't placed any orders yet.</p>
            <button onclick="window.location.href='MainPage.php'" style="background: #C647CC; color: white; border: none; padding: 10px 20px; border-radius: 5px; margin-top: 15px; cursor: pointer;">
              Start Shopping
            </button>
          </div>
        <?php else: ?>
          <?php foreach ($orders as $order): ?>
            <div class="purchase-row">
              <div class="order-info">
                <i class="fas fa-box"></i>
                <div class="order-details">
                  <h4>Order #<?php echo $order['order_id']; ?></h4>
                  <p><?php echo date('M j, Y g:i A', strtotime($order['order_date'])); ?></p>
                  <p><?php echo $order['item_count']; ?> item(s)</p>
                  <button class="view-details-btn" onclick="viewOrderDetails(<?php echo $order['order_id']; ?>)">
                    View Details
                  </button>
                </div>
              </div>
              
              <div>
                <span class="status-badge status-<?php echo $order['status']; ?>">
                  <?php echo ucfirst($order['status']); ?>
                </span>
              </div>
              
              <div class="payment-info">
                <div class="payment-method"><?php echo htmlspecialchars($order['payment_method'] ?? 'N/A'); ?></div>
                <div class="payment-status">Status: <?php echo ucfirst($order['payment_status'] ?? 'pending'); ?></div>
              </div>
              
              <div class="amount">₱<?php echo number_format($order['total_amount'], 2); ?></div>
            </div>
          <?php endforeach; ?>
        <?php endif; ?>
      </div>
    </div>
  </div>

  <script>
    function viewOrderDetails(orderId) {
      // For now, show an alert. You can implement a modal or redirect to details page
      alert(`Viewing details for Order #${orderId}\n\nThis feature will show detailed order information including:\n- Items ordered\n- Quantities\n- Delivery address\n- Tracking information`);
      
      // Future implementation could be:
      // window.location.href = `order_details.php?id=${orderId}`;
      // or open a modal with detailed information
    }
    
    // Navigation functions
    function goToCart() {
      window.location.href = 'Cart.php';
    }
    
    function goToProfile() {
      window.location.href = 'Userdashboard.php';
    }
  </script>
</body>
</html>
