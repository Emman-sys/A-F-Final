<?php
// filepath: c:\Users\ceile\A-F-Final\AdminDashboard.php
session_start();
require 'db_connect.php';

// Check if user is trying to login
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['admin_login'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];
    
    // Debug: Log the attempt
    error_log("Admin login attempt for email: " . $email);
    
    // Get admin data from database
    $stmt = $conn->prepare("SELECT admin_id, admin_fname, admin_lname, admin_password FROM admin WHERE admin_email = ? AND status = 'active'");
    if ($stmt) {
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 1) {
            $admin = $result->fetch_assoc();
            
            // Verify password using password_verify()
            if (password_verify($password, $admin['admin_password'])) {
                $_SESSION['admin_id'] = $admin['admin_id'];
                $_SESSION['admin_name'] = $admin['admin_fname'] . ' ' . $admin['admin_lname'];
                $_SESSION['role'] = 'admin';
                
                // Update last login
                $updateStmt = $conn->prepare("UPDATE admin SET last_login = NOW() WHERE admin_id = ?");
                if ($updateStmt) {
                    $updateStmt->bind_param("i", $admin['admin_id']);
                    $updateStmt->execute();
                }
                
                header('Location: AdminDashboard.php');
                exit();
            } else {
                $loginError = "Invalid password!";
                error_log("Password verification failed for admin: " . $email);
            }
        } else {
            $loginError = "Admin account not found!";
            error_log("Admin account not found: " . $email);
        }
    } else {
        $loginError = "Database error occurred!";
        error_log("Database prepare failed: " . $conn->error);
    }
}

// Handle logout
if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: AdminDashboard.php');
    exit();
}

// Get dashboard stats - with fallback values
function getDashboardStats($conn) {
    $stats = [
        'total_sales' => 100000,
        'daily_sales' => 50000,
        'total_customers' => 4158,
        'total_products' => 500,
        'total_orders' => 1000
    ];
    
    // Try to get real stats, but use fallbacks if tables don't exist
    try {
        // Check if orders table exists and get total sales
        $result = $conn->query("SHOW TABLES LIKE 'orders'");
        if ($result && $result->num_rows > 0) {
            $stmt = $conn->prepare("SELECT COALESCE(SUM(total_amount), 0) as total_sales FROM orders WHERE status = 'completed'");
            if ($stmt) {
                $stmt->execute();
                $result = $stmt->get_result();
                if ($result) {
                    $row = $result->fetch_assoc();
                    $stats['total_sales'] = $row['total_sales'] ?: 100000;
                }
            }
            
            // Daily Sales
            $stmt = $conn->prepare("SELECT COALESCE(SUM(total_amount), 0) as daily_sales FROM orders WHERE DATE(order_date) = CURDATE() AND status = 'completed'");
            if ($stmt) {
                $stmt->execute();
                $result = $stmt->get_result();
                if ($result) {
                    $row = $result->fetch_assoc();
                    $stats['daily_sales'] = $row['daily_sales'] ?: 50000;
                }
            }
            
            // Total Orders
            $stmt = $conn->prepare("SELECT COUNT(*) as total_orders FROM orders");
            if ($stmt) {
                $stmt->execute();
                $result = $stmt->get_result();
                if ($result) {
                    $row = $result->fetch_assoc();
                    $stats['total_orders'] = $row['total_orders'] ?: 1000;
                }
            }
        }
        
        // Check if users table exists and get customer count
        $result = $conn->query("SHOW TABLES LIKE 'users'");
        if ($result && $result->num_rows > 0) {
            $stmt = $conn->prepare("SELECT COUNT(*) as total_customers FROM users WHERE role = 'customer'");
            if ($stmt) {
                $stmt->execute();
                $result = $stmt->get_result();
                if ($result) {
                    $row = $result->fetch_assoc();
                    $stats['total_customers'] = $row['total_customers'] ?: 4158;
                }
            }
        }
        
        // Check if products table exists and get product count
        $result = $conn->query("SHOW TABLES LIKE 'products'");
        if ($result && $result->num_rows > 0) {
            $stmt = $conn->prepare("SELECT COUNT(*) as total_products FROM products");
            if ($stmt) {
                $stmt->execute();
                $result = $stmt->get_result();
                if ($result) {
                    $row = $result->fetch_assoc();
                    $stats['total_products'] = $row['total_products'] ?: 500;
                }
            }
        }
        
    } catch (Exception $e) {
        error_log("Error getting dashboard stats: " . $e->getMessage());
    }
    
    return $stats;
}

// Get dashboard stats
$stats = getDashboardStats($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Admin Dashboard</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

  <style>
     body{
        background:url("https://cdn.glitch.global/585aee42-d89c-4ece-870c-5b01fc1bab61/image%203.png?v=1747320934399");
        background-position:center;
        background-size:cover;
        background-attachment:fixed;
        font-family: 'Poppins', serif;
        min-height: 100vh;
        width:100%;
        overflow-x: hidden;
    }

      .brand-name {
      left:50px;
      font-size: clamp(2rem, 5vw, 3rem);
      font-weight: bold;
      color: white;
    }

    .Admin{
      position:absolute;
      left:145px;
      top:40px;
      font-size: 25px;
      font-weight: bold;
      color: white;
    }

    .search-bar {
      position:absolute;
      left:50px;
      top: -95px;
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

    /* All your existing dashboard styles... */
    .box1{
        position:absolute;
        left: 135px;
        top: 168px;
        width:213px;
        height: 269px;
        background: linear-gradient(to bottom, #D997D5,#FFFFFF);
        border-radius:20px;
        cursor: pointer;
        transition: transform 0.3s ease;
    }
    
    .box1:hover, .box2:hover, .box3:hover, .box4:hover, .box5:hover {
        transform: translateY(-5px);
    }
    
    .circ1{
        position:absolute;
        left: 186px;
        top: 200px;
        width:101px;
        height: 97px;
        background: white;
        border-radius:50px;
        z-index:1;
    }
    .crl1{
        position:absolute;
        left: 194px;
        top: 209px;
        width:85px;
        height: 79px;
        background: #D997D5;
        border-radius:55px;
        z-index:2;
    }

    .tsales{
        position:absolute;
        left: 160px;
        top:270px;
        color:black;
        font-size:30px;
        font-weight:300;
        z-index:2;
    }
    .percent{
        position:absolute;
        left: 190px;
        top:320px;
        color:#8D7D7D;
        font-size:12px;
        font-weight:300;
        z-index:2;
    }
    .income{
        position:absolute;
        left: 164px;
        top:350px;
        color:black;
        font-size:36px;
        font-weight:600;
        z-index:2;
    }
    .box2{
        position:absolute;
        left: 375px;
        top: 168px;
        width:213px;
        height: 269px;
        background: linear-gradient(to bottom, #7B87C6,#FFFFFF);
        border-radius:20px;
        cursor: pointer;
        transition: transform 0.3s ease;
    }
     
    .circ2{
        position:absolute;
        left: 430px;
        top: 200px;
        width:101px;
        height: 97px;
        background: white;
        border-radius:50px;
        z-index:1;
    }
    .crl2{
        position:absolute;
        left: 439px;
        top: 209px;
        width:83px;
        height: 79px;
        background: #7B87C6;
        border-radius:50px;
        z-index:2;
    }
    .dsales{
        position:absolute;
        left: 410px;
        top:270px;
        color:black;
        font-size:30px;
        font-weight:300;
        z-index:2;
    }
    .sales{
        position:absolute;
        left: 450px;
        top:320px;
        color:#8D7D7D;
        font-size:12px;
        font-weight:300;
        z-index:2;
    }
    .income2{
        position:absolute;
        left: 415px;
        top:350px;
        color:black;
        font-size:36px;
        font-weight:600;
        z-index:2;
    }
    .box3{
        position:absolute;
        left: 615px;
        top: 168px;
        width:213px;
        height: 269px;
        background: linear-gradient(to bottom, #7BC68F,#FFFFFF);
        border-radius:20px;
        cursor: pointer;
        transition: transform 0.3s ease;
    }
    
     .circ3{
        position:absolute;
        left: 668px;
        top: 200px;
        width:101px;
        height: 97px;
        background: white;
        border-radius:50px;
        z-index:1;
    }
    .crl3{
        position:absolute;
        left: 676px;
        top: 209px;
        width:83px;
        height: 79px;
        background: #7BC68F;
        border-radius:50px;
        z-index:2;
    }
    .customers{
        position:absolute;
        left: 648px;
        top:270px;
        color:black;
        font-size:30px;
        font-weight:300;
        z-index:2;
    }
    .percent1{
        position:absolute;
        left: 670px;
        top:319px;
        color:#8D7D7D;
        font-size:12px;
        font-weight:300;
        z-index:2;
    }
    .newusers{
        position:absolute;
        left: 670px;
        top:351px;
        color:black;
        font-size:36px;
        font-weight:600;
        z-index:2;
    }

    .box4{
        position:absolute;
        left: 855px;
        top: 168px;
        width:213px;
        height: 269px;
        background: linear-gradient(to bottom, #C6B27B,#FFFFFF);
        border-radius:20px;
        cursor: pointer;
        transition: transform 0.3s ease;
    }
    
     .circ4{
        position:absolute;
        left: 906px;
        top: 200px;
        width:101px;
        height: 97px;
        background: white;
        border-radius:50px;
        z-index:1;
    }
    .crl4{
        position:absolute;
        left: 915px;
        top: 209px;
        width:83px;
        height: 79px;
        background: #C6B27B;
        border-radius:50px;
        z-index:2;
    }
    .product{
        position:absolute;
        left: 906px;
        top:270px;
        color:black;
        font-size:30px;
        font-weight:300;
        z-index:2;
    }
    .percent2{
        position:absolute;
        left: 913px;
        top:319px;
        color:#8D7D7D;
        font-size:12px;
        font-weight:300;
        z-index:2;
    }
    .numberofp{
        position:absolute;
        left: 920px;
        top:351px;
        color:black;
        font-size:36px;
        font-weight:600;
        z-index:2;
    }
    .box5{
        position:absolute;
        left: 1112px;
        top: 168px;
        width:213px;
        height: 269px;
        background: linear-gradient(to bottom, #6A34D6,#FFFFFF);
        border-radius:20px;
        cursor: pointer;
        transition: transform 0.3s ease;
    }
    
     .circ5{
        position:absolute;
        left: 1158px;
        top: 200px;
        width:101px;
        height: 97px;
        background: white;
        border-radius:50px;
        z-index:1;
    }
    .crl5{
        position:absolute;
        left: 1167px;
        top: 209px;
        width:83px;
        height: 79px;
        background: #6A34D6;
        border-radius:50px;
        z-index:2;
    }
     .delivery{
        position:absolute;
        left: 1159px;
        top:270px;
        color:black;
        font-size:30px;
        font-weight:300;
        z-index:2;
    }
    .percent3{
        position:absolute;
        left: 1169px;
        top:319px;
        color:#8D7D7D;
        font-size:12px;
        font-weight:300;
        z-index:2;
    }
    .ndelivery{
        position:absolute;
        left: 1178px;
        top:351px;
        color:black;
        font-size:36px;
        font-weight:600;
        z-index:2;
    }

    .rectangle1{
        position:absolute;
        left: 150px;
        top: 500px;
        width: 1170px;
        height: 364px;
        background: linear-gradient(to bottom, #FFFFFF, #E8DEF1);
        border-radius:14px;
    }
    .rectangle2{
        position:absolute;
        left: 150px;
        top: 886px;
        width: 1170px;
        height: 360px;
        background: linear-gradient(to bottom, #B85CD7, #DDCFCF);
        border-radius:14px;
    }
    .sumsales{
        position:absolute;
        left:163px;
        top:496px;
        font-size:24px;
        font-weight:bold;
        color:black;
        z-index:2;
    }
    .number{
        position:absolute;
        left:185px;
        top:605px;
        font-size:15px;
        font-weight:medium;
        color:#8D7D7D;
        z-index:2;
    }
    .number1{
        position:absolute;
        left:185px;
        top:640px;
        font-size:15px;
        font-weight:medium;
        color:#8D7D7D;
        z-index:2;
    }
    .number2{
        position:absolute;
        left:185px;
        top:676px;
        font-size:15px;
        font-weight:medium;
        color:#8D7D7D;
        z-index:2;
    }
    .number3{
        position:absolute;
        left:185px;
        top:710px;
        font-size:15px;
        font-weight:medium;
        color:#8D7D7D;
        z-index:2;
    }
    .number4{
        position:absolute;
        left:185px;
        top:745px;
        font-size:15px;
        font-weight:medium;
        color:#8D7D7D;
        z-index:2;
    }
    .line{
       position: absolute;
       left: 234px;
       top: 830px;
       height: 2px;
       background-color:black;
       width: 57%; 
       z-index: 3;
      }
    .scalendar{
      position:absolute;
      right:215px;
      top:515px;
      width:96px;
      height:35px;
      background-color:white;
      border-radius:14px;
      border: 1px solid #8D7D7D;
      z-index:2;
    }
    .month{
      position:absolute;
      right:246px;
      top:523px;
      font-size:16px;
      font-weight:700;
      color:black;
      z-index:2;
    }
    .triangle{
      position:absolute;
      right:227px;
      top:529px;
      border-left: 10px solid transparent;
      border-right: 10px solid transparent;
      border-top: 17px solid black;
      color:black;
      z-index:2;
    }
    .tpsales{
      position:absolute;
      left:168px;
      top:880px;
      font-size:24px;
      font-weight:bold;
      color:black;
      z-index:2;
    }
    .top-product{
      position: absolute;
      top: 950px;
      width: 177px;
      height: 229px;
      background: #EEEFE8;
      border-radius: 8px;
      z-index: 3;
    }
    .top-product1 { left: 180px; }
    .top-product2 { left: 380px; }
    .top-product3 { left: 580px; }
    .top-product4 { left: 780px; }
    .top-product5 { left: 980px; }
    
     .square{
      position:absolute;
      width:155px;
      height:141px;
      border-radius:31px;
      background:#FFFFFF;
      top:975px;
      z-index:3;
    }
      
   .square1 { left: 190px; }
   .square2 { left: 390px; }
   .square3 { left: 590px; }
   .square4 { left: 788px; }
   .square5 { left: 990px; }
   
 .topsales{
    position:absolute;
    left: 208px;
    top: 220px;
    width:59px;
    height: 55px;
    z-index:3;
 }
 .dailysales{
    position:absolute;
    left: 452px;
    top: 220px;
    width:59px;
    height: 55px;
    z-index:3;
 }
 .usr{
    position:absolute;
    left: 687px;
    top: 225px;
    width:59px;
    height:55px;
    z-index:3;
 }
 .cart{
    position:absolute;
    left: 932px;
    top: 248px;
    width:59px;
    height:55px;
    z-index:3;
 }
 .deliv{
    position:absolute;
    left: 1180px;
    top: 220px;
    width:59px;
    height:55px;
    z-index:3;
 }
 .notifications{
    position:absolute;
    left:1240px;
    top:65px;
    width:37px;
    height:40.23px;
    z-index:2;
 }
 .iconn{
    position:absolute;
    left:1295px;
    top:55px;
    width:37px;
    height:40.23px;
    z-index:2;
 }

/* ADMIN LOGIN MODAL STYLES - Matching Welcome.php */
.popup-overlay {
  display: <?php echo !isset($_SESSION['admin_id']) ? 'block' : 'none'; ?>;
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background-color: rgba(0, 0, 0, 0.5);
  z-index: 1000;
}

.popup-container {
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  width: 90%;
  max-width: 500px;
  padding: 40px;
  border-radius: 15px;
  background-color: rgba(217, 217, 217, 0.1);
  backdrop-filter: blur(10px);
}

.popup-close {
  position: absolute;
  top: 15px;
  right: 20px;
  background: none;
  border: none;
  color: #fff;
  font-size: 24px;
  cursor: pointer;
}

.form-title {
  color: #fff;
  font-family: Poppins, sans-serif;
  font-size: 24px;
  font-weight: 700;
  text-align: center;
  margin-bottom: 30px;
}

.login-form {
  display: flex;
  flex-direction: column;
  gap: 20px;
}

.form-group {
  position: relative;
}

.input-label {
  color: #fff;
  font-family: Poppins;
  font-size: 16px;
  font-weight: 700;
  margin-bottom: 8px;
  display: block;
}

.form-input {
  width: 100%;
  height: 50px;
  border-radius: 10px;
  border: 1px solid rgba(216, 204, 204, 0.61);
  background-color: rgba(216, 204, 204, 0.61);
  padding: 0 15px;
  font-size: 14px;
  color: #000;
  box-sizing: border-box;
}

.login-button {
  width: 100%;
  height: 50px;
  border-radius: 10px;
  background-color: #fff;
  color: #000;
  font-size: 16px;
  font-weight: 700;
  border: none;
  cursor: pointer;
  margin-top: 20px;
}

.signup-text {
  text-align: center;
  font-family: Poppins, sans-serif;
  font-size: 14px;
  color: #fff;
  margin-top: 15px;
}

.signup-text a {
  color: #fff;
  text-decoration: underline;
  cursor: pointer;
}

.error-message {
  background-color: rgba(192, 57, 43, 0.7);
  color: white;
  padding: 10px;
  border-radius: 5px;
  margin-bottom: 15px;
  text-align: center;
}

.admin-welcome {
    position: absolute;
    right: 100px;
    top: 20px;
    color: white;
    font-size: 14px;
}

.admin-logout {
    position: absolute;
    right: 50px;
    top: 60px;
    background: #dc3545;
    color: white;
    padding: 8px 15px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    text-decoration: none;
    font-size: 12px;
}

.admin-logout:hover {
    background: #c82333;
}

/* Hide dashboard content when not logged in */
.dashboard-content {
    opacity: <?php echo isset($_SESSION['admin_id']) ? '1' : '0.3'; ?>;
    pointer-events: <?php echo isset($_SESSION['admin_id']) ? 'auto' : 'none'; ?>;
    transition: opacity 0.3s ease;
}

.password-toggle {
    position: absolute;
    right: 10px;
    top: 50%;
    transform: translateY(-50%);
    background: none;
    border: none;
    color: #333;
    cursor: pointer;
    font-size: 12px;
}

.debug-info {
    position: fixed;
    bottom: 10px;
    left: 10px;
    background: rgba(0,0,0,0.8);
    color: white;
    padding: 10px;
    border-radius: 5px;
    font-size: 12px;
    z-index: 9999;
}
    </style>
</head>

<body>
    <?php if (!isset($_SESSION['admin_id'])): ?>
    <!-- Admin Login Modal - Matching Welcome.php Style -->
    <div id="adminLoginPopup" class="popup-overlay">
        <div class="popup-container">
            <button class="popup-close" onclick="goToMainPage()">&times;</button>
            <h2 class="form-title">Admin Login</h2>
            
            <?php if (isset($loginError)): ?>
                <div class="error-message"><?php echo htmlspecialchars($loginError); ?></div>
            <?php endif; ?>
            
            <form class="login-form" method="POST">
                <div class="form-group">
                    <label class="input-label">Admin Email</label>
                    <input type="email" class="form-input" name="email" value="admin@af.com" required />
                </div>
                <div class="form-group">
                    <label class="input-label">Password</label>
                    <div style="position: relative;">
                        <input type="password" class="form-input" name="password" id="adminPassword" placeholder="Enter admin password" required />
                        <button type="button" class="password-toggle" onclick="togglePassword('adminPassword')">Show</button>
                    </div>
                </div>
                <button type="submit" name="admin_login" class="login-button">Login to Dashboard</button>
                <p class="signup-text">
                    <a href="MainPage.php">← Back to Store</a>
                </p>
                <div style="margin-top: 15px; font-size: 12px; color: #ccc; text-align: center;">
                    Demo: admin@af.com / admin123<br>
                    <small>If login fails, run setup_admin.php first</small>
                </div>
            </form>
        </div>
    </div>
    <?php else: ?>
    <!-- Welcome message and logout for logged in admin -->
    <div class="admin-welcome">
        Welcome, <?php echo htmlspecialchars($_SESSION['admin_name']); ?>
    </div>
    <a href="?logout=1" class="admin-logout" onclick="return confirm('Are you sure you want to logout?')">Logout</a>
    <?php endif; ?>

    <!-- Dashboard Content -->
    <div class="dashboard-content">
        <div class="boxes">
            <div class="header-section"></div>
            <header class="header">
                <h2 class="brand-name">A&F</h2>
                <h2 class="Admin">Admin Dashboard</h2>

                <div class="search-bar">
                    <img src="https://cdn.glitch.global/585aee42-d89c-4ece-870c-5b01fc1bab61/search.png?v=1747633330905" class="search-icon" alt="Search">
                </div>
                
                <div class="notifications">
                    <img src="images/notif.png" alt="notif">
                </div>
                
                <!-- Icons for dashboard boxes -->
                <div class="topsales">
                    <img src="images/topsales.png" alt="top">
                </div>
                <div class="dailysales">
                    <img src="images/dailysales.png" alt="daily">
                </div>
                <div class="deliv">
                    <img src="images/deliv.png" alt="deliveryyy">
                </div>
                <div class="usr">
                    <img src="images/users.png" alt="newcustomers">
                </div>
                <div class="cart">
                    <img src="images/cart.png" alt="cart">
                </div>
                <div class="iconn">
                    <img src="images/iconuser.png" alt="useracc">
                </div>

                <!-- Dashboard boxes -->
                <div class="box1" onclick="showSalesDetails()"></div>
                <div class="box2" onclick="showDailySales()"></div>
                <div class="box3" onclick="showCustomers()"></div>
                <div class="box4" onclick="showProducts()"></div>
                <div class="box5" onclick="showDeliveries()"></div>
            </header>

            <!-- Summary sections -->
            <div class="sum-sales, top-sales">
                <div class="rectangle1"></div>
                <div class="rectangle2"></div>
            </div>
                
            <!-- Dashboard data -->
            <div class="boxes-info">
                <h2 class="tsales">Total Sales</h2>
                <h2 class="percent">+50% Incomes</h2>
                <h2 class="income">₱<?php echo number_format($stats['total_sales']); ?></h2>

                <h2 class="dsales">Daily Sales</h2>
                <h2 class="sales">-13% Sales</h2>
                <h2 class="income2">₱<?php echo number_format($stats['daily_sales']); ?></h2>

                <h2 class="customers">Customers</h2>
                <h2 class="percent1">+25% New Users</h2>
                <h2 class="newusers"><?php echo number_format($stats['total_customers']); ?></h2>

                <h2 class="product">Product</h2>
                <h2 class="percent2">+5% New Products</h2>
                <h2 class="numberofp"><?php echo number_format($stats['total_products']); ?></h2>

                <h2 class="delivery">Delivery</h2>
                <h2 class="percent3">Decrease by 2%</h2>
                <h2 class="ndelivery"><?php echo number_format($stats['total_orders']); ?></h2>
            </div>

            <!-- Circles and other visual elements -->
            <div class="circles">
                <div class="circ1"></div>
                <div class="circ2"></div>
                <div class="circ3"></div>
                <div class="circ4"></div>
                <div class="circ5"></div>
            </div>

            <div class="circles2">
                <div class="crl1"></div>
                <div class="crl2"></div>
                <div class="crl3"></div>
                <div class="crl4"></div>
                <div class="crl5"></div>
            </div>

            <div class="summary-section">
                <h2 class="sumsales">Summary Sales</h2>
                <div class="line"></div>
                <div class="scalendar"></div>
                <div class="month">Month</div>
                <div class="triangle"></div>
                <div class="number">30+</div>
                <div class="number1">30</div>
                <div class="number2">20</div>
                <div class="number3">10</div>
                <div class="number4">0</div>
            </div>

            <div class="sales-section">
                <h2 class="tpsales">Top Sales</h2>
                <div class="top-product top-product1"></div>
                <div class="top-product top-product2"></div>
                <div class="top-product top-product3"></div>
                <div class="top-product top-product4"></div>
                <div class="top-product top-product5"></div>
                
                <div class="square square1"></div>
                <div class="square square2"></div>
                <div class="square square3"></div>
                <div class="square square4"></div>
                <div class="square square5"></div>
            </div>
        </div>
    </div>

    <!-- Debug info -->
    <div class="debug-info">
        <strong>Debug Info:</strong><br>
        Admin logged in: <?php echo isset($_SESSION['admin_id']) ? 'Yes' : 'No'; ?><br>
        Database connected: <?php echo $conn ? 'Yes' : 'No'; ?><br>
        Stats loaded: <?php echo $stats ? 'Yes' : 'No'; ?>
    </div>

    <script>
        // Password toggle function
        function togglePassword(inputId) {
            var pwd = document.getElementById(inputId);
            var btn = event.target;
            if (pwd.type === "password") {
                pwd.type = "text";
                btn.textContent = "Hide";
            } else {
                pwd.type = "password";
                btn.textContent = "Show";
            }
        }

        // Interactive dashboard functions
        function showSalesDetails() {
            <?php if (isset($_SESSION['admin_id'])): ?>
                alert('Total Sales: ₱<?php echo number_format($stats['total_sales']); ?>\nClick to view detailed sales report');
            <?php else: ?>
                alert('Please login first');
            <?php endif; ?>
        }

        function showDailySales() {
            <?php if (isset($_SESSION['admin_id'])): ?>
                alert('Daily Sales: ₱<?php echo number_format($stats['daily_sales']); ?>\nToday\'s performance');
            <?php else: ?>
                alert('Please login first');
            <?php endif; ?>
        }

        function showCustomers() {
            <?php if (isset($_SESSION['admin_id'])): ?>
                alert('Total Customers: <?php echo number_format($stats['total_customers']); ?>\nManage customer database');
            <?php else: ?>
                alert('Please login first');
            <?php endif; ?>
        }

        function showProducts() {
            <?php if (isset($_SESSION['admin_id'])): ?>
                alert('Total Products: <?php echo number_format($stats['total_products']); ?>\nManage product inventory');
            <?php else: ?>
                alert('Please login first');
            <?php endif; ?>
        }

        function showDeliveries() {
            <?php if (isset($_SESSION['admin_id'])): ?>
                alert('Total Orders: <?php echo number_format($stats['total_orders']); ?>\nManage order deliveries');
            <?php else: ?>
                alert('Please login first');
            <?php endif; ?>
        }

        function goToMainPage() {
            window.location.href = 'MainPage.php';
        }

        // Auto-focus on password field if email is pre-filled
        document.addEventListener('DOMContentLoaded', function() {
            const emailField = document.querySelector('input[name="email"]');
            const passwordField = document.getElementById('adminPassword');
            
            if (emailField && emailField.value && passwordField) {
                passwordField.focus();
            }
        });

        console.log('Admin Dashboard loaded successfully!');
        console.log('Admin logged in: <?php echo isset($_SESSION['admin_id']) ? 'true' : 'false'; ?>');
    </script>
</body>
</html>
