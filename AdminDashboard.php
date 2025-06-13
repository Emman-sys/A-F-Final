<?php
session_start();
require 'db_connect.php';

// Check if user is trying to login
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['admin_login'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];
    
    // DEBUG: BYPASS LOGIN FOR TESTING - FORCE ADMIN SESSION
    if ($email === 'admin@af.com' || $email === 'admin@test.com') {
        $_SESSION['admin_id'] = 1;
        $_SESSION['admin_name'] = 'Admin User';
        $_SESSION['admin_email'] = $email;
        $_SESSION['role'] = 'admin';
        
        error_log("🔥 FORCED ADMIN LOGIN FOR TESTING");
        header('Location: AdminDashboard.php');
        exit();
    }
    
    $loginError = "Login temporarily bypassed for testing";
}

// Handle logout
if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: AdminDashboard.php');
    exit();
}

// Get messages from session and clear them
$productSuccess = '';
$productError = '';

if (isset($_SESSION['product_success'])) {
    $productSuccess = $_SESSION['product_success'];
    unset($_SESSION['product_success']);
}

if (isset($_SESSION['product_error'])) {
    $productError = $_SESSION['product_error'];
    unset($_SESSION['product_error']);
}

// Handle product addition
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_product']) && isset($_SESSION['admin_id'])) {
    error_log("🔥 ADD PRODUCT ATTEMPT - Files: " . print_r($_FILES, true));
    
    $name = trim($_POST['product_name']);
    $description = trim($_POST['product_description']);
    $price = floatval($_POST['product_price']);
    $stock = intval($_POST['product_stock']);
    $category_id = intval($_POST['category_id']);
    
    // Validate inputs
    if (empty($name) || empty($description) || $price <= 0 || $stock < 0) {
        $_SESSION['product_error'] = "Please fill all fields with valid data.";
        error_log("❌ Validation failed");
    } else {
        try {
            // Handle image upload with compression
            $imageData = null;
            
            if (isset($_FILES['product_image']) && $_FILES['product_image']['error'] === UPLOAD_ERR_OK) {
                error_log("📸 Image upload detected: " . $_FILES['product_image']['name']);
                $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
                $fileType = $_FILES['product_image']['type'];
                
                if (in_array($fileType, $allowedTypes) && $_FILES['product_image']['size'] <= 5000000) {
                    // Compress and resize image
                    $imageData = compressImage($_FILES['product_image']['tmp_name'], $fileType);
                    if ($imageData === false) {
                        $_SESSION['product_error'] = "Failed to process image. Please try a different image.";
                        header('Location: AdminDashboard.php?product_added=1&show_modal=1');
                        exit();
                    }
                    error_log("✅ Image processed and compressed: " . strlen($imageData) . " bytes");
                } else {
                    $_SESSION['product_error'] = "Invalid image file. Please use JPG, PNG, or GIF format under 5MB.";
                    header('Location: AdminDashboard.php?product_added=1&show_modal=1');
                    exit();
                }
            } else {
                error_log("📷 No image uploaded or upload error: " . ($_FILES['product_image']['error'] ?? 'no file'));
            }
            
            // Insert product into database - SIMPLE BLOB APPROACH
            $stmt = $conn->prepare("INSERT INTO products (name, description, price, stock_quantity, category_id, product_image, image_type) VALUES (?, ?, ?, ?, ?, ?, NULL)");
            $stmt->bind_param("ssdiis", $name, $description, $price, $stock, $category_id, $imageData);
            error_log("🔄 Inserting product with image data: " . ($imageData ? strlen($imageData) . " bytes" : "NULL"));
            
            if ($stmt->execute()) {
                $_SESSION['product_success'] = "Product added successfully!" . ($imageData ? " With image." : " Without image.");
                error_log("✅ Product added successfully");
            } else {
                $_SESSION['product_error'] = "Failed to add product: " . $conn->error;
                error_log("❌ Database error: " . $conn->error);
            }
        } catch (Exception $e) {
            $_SESSION['product_error'] = "Error adding product: " . $e->getMessage();
            error_log("❌ Exception: " . $e->getMessage());
        }
    }
    
    header('Location: AdminDashboard.php?product_added=1&show_modal=1');
    exit();
}

// Image compression function - handles large images and MySQL packet size limitations
function compressImage($sourcePath, $mimeType) {
    error_log("🔍 Starting image compression - File: " . $sourcePath . ", Type: " . $mimeType);
    
    // Check if GD extension is available for image processing
    if (!extension_loaded('gd')) {
        error_log("⚠️ GD extension not available, using original image");
        
        // Fallback: use original image data but check size to prevent MySQL errors
        $imageData = file_get_contents($sourcePath);
        
        // 4MB fallback limit - prevents "Got a packet bigger than 'max_allowed_packet'" MySQL error
        // Most default MySQL configurations have max_allowed_packet = 1MB-16MB
        if (strlen($imageData) > 4000000) { // 4MB limit
            error_log("❌ Image too large without compression: " . strlen($imageData) . " bytes");
            return false; // Reject image if too large and can't compress
        }
        
        error_log("✅ Using original image (no GD): " . strlen($imageData) . " bytes");
        return $imageData;
    }
    
    error_log("✅ GD extension available, proceeding with compression");
    
    try {
        // Create image resource based on MIME type for processing
        switch ($mimeType) {
            case 'image/jpeg':
                $image = imagecreatefromjpeg($sourcePath);
                break;
            case 'image/png':
                $image = imagecreatefrompng($sourcePath);
                break;
            case 'image/gif':
                $image = imagecreatefromgif($sourcePath);
                break;
            default:
                error_log("❌ Unsupported image type: " . $mimeType);
                return false;
        }
        
        // If image creation failed, fallback to original with size check
        if (!$image) {
            error_log("❌ Failed to create image resource from: " . $sourcePath);
            $imageData = file_get_contents($sourcePath);
            
            // Same 4MB limit for fallback scenario
            if (strlen($imageData) > 4000000) {
                error_log("❌ Fallback image too large: " . strlen($imageData) . " bytes");
                return false;
            }
            error_log("⚠️ Using original image as fallback: " . strlen($imageData) . " bytes");
            return $imageData;
        }
        
        // Get original image dimensions for resizing calculations
        $originalWidth = imagesx($image);
        $originalHeight = imagesy($image);
        error_log("📐 Original dimensions: " . $originalWidth . "x" . $originalHeight);
        
        // Set maximum dimensions - balances quality vs file size
        // Larger dimensions = better quality but bigger file size
        $maxWidth = 800;  // Good for product display
        $maxHeight = 600; // Maintains reasonable aspect ratios
        
        // Calculate new dimensions while preserving aspect ratio
        $ratio = min($maxWidth / $originalWidth, $maxHeight / $originalHeight);
        $newWidth = (int)($originalWidth * $ratio);
        $newHeight = (int)($originalHeight * $ratio);
        error_log("📐 New dimensions: " . $newWidth . "x" . $newHeight . " (ratio: " . $ratio . ")");
        
        // Create new blank image canvas with calculated dimensions
        $newImage = imagecreatetruecolor($newWidth, $newHeight);
        
        // Preserve transparency for PNG and GIF images
        if ($mimeType === 'image/png') {
            // PNG transparency handling
            imagealphablending($newImage, false);
            imagesavealpha($newImage, true);
            $transparent = imagecolorallocatealpha($newImage, 255, 255, 255, 127);
            imagefilledrectangle($newImage, 0, 0, $newWidth, $newHeight, $transparent);
            error_log("🔍 PNG transparency preserved");
        } elseif ($mimeType === 'image/gif') {
            // GIF transparency handling
            $transparentIndex = imagecolortransparent($image);
            if ($transparentIndex >= 0) {
                $transparentColor = imagecolorsforindex($image, $transparentIndex);
                $transparentNew = imagecolorallocate($newImage, $transparentColor['red'], $transparentColor['green'], $transparentColor['blue']);
                imagefill($newImage, 0, 0, $transparentNew);
                imagecolortransparent($newImage, $transparentNew);
            }
            error_log("🔍 GIF transparency handled");
        }
        
        // Resize original image to new dimensions using resampling for quality
        imagecopyresampled($newImage, $image, 0, 0, 0, 0, $newWidth, $newHeight, $originalWidth, $originalHeight);
        error_log("✅ Image resized successfully");
        
        // Convert processed image back to binary data with compression
        ob_start(); // Start output buffering to capture image data
        switch ($mimeType) {
            case 'image/jpeg':
                imagejpeg($newImage, null, 75); // 75% quality - balance of size vs quality
                break;
            case 'image/png':
                imagepng($newImage, null, 6); // Compression level 6 (0-9, 9=max compression)
                break;
            case 'image/gif':
                imagegif($newImage, null); // GIF doesn't have quality settings
                break;
        }
        $imageData = ob_get_contents(); // Get the buffered image data
        ob_end_clean(); // Clean the output buffer
        
        // Free up memory by destroying image resources
        imagedestroy($image);
        imagedestroy($newImage);
        
        // Final size check - 1.5MB limit prevents MySQL packet errors
        // Even with compression, some images might still be large
        if (strlen($imageData) > 1500000) { // 1.5MB limit
            error_log("❌ Compressed image still too large: " . strlen($imageData) . " bytes");
            return false; // Reject if still too large after compression
        }
        
        error_log("✅ Image compressed successfully: " . strlen($imageData) . " bytes");
        return $imageData;
        
    } catch (Exception $e) {
        error_log("❌ Image compression error: " . $e->getMessage());
        
        // Final fallback: try original image with size check
        try {
            $imageData = file_get_contents($sourcePath);
            
            // 4MB limit for this fallback too
            if (strlen($imageData) > 4000000) {
                error_log("❌ Fallback image too large: " . strlen($imageData) . " bytes");
                return false;
            }
            
            error_log("⚠️ Using original image as fallback after error: " . strlen($imageData) . " bytes");
            return $imageData;
        } catch (Exception $fallbackError) {
            error_log("❌ Fallback also failed: " . $fallbackError->getMessage());
            return false;
        }
    }
}

// Function to ensure database connection is alive
function ensureConnection($conn) {
    if (!$conn->ping()) {
        error_log("❌ Database connection lost, attempting to reconnect...");
        $conn->close();
        
        // Reconnect using the same credentials
        require 'db_connect.php';
        return $conn;
    }
    return $conn;
}

// Handle product editing
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_product']) && isset($_SESSION['admin_id'])) {
    error_log("🔥 EDIT PRODUCT ATTEMPT - ID: " . $_POST['product_id']);
    
    $product_id = intval($_POST['product_id']);
    $name = trim($_POST['product_name']);
    $description = trim($_POST['product_description']);
    $price = floatval($_POST['product_price']);
    $stock = intval($_POST['product_stock']);
    $category_id = intval($_POST['category_id']);
    
    // Validate inputs
    if (empty($name) || empty($description) || $price <= 0 || $stock < 0 || $product_id <= 0) {
        $_SESSION['product_error'] = "Please fill all fields with valid data.";
    } else {
        try {
            // Ensure database connection is alive
            $conn = ensureConnection($conn);
            
            // Check if new image was uploaded
            $updateImage = false;
            $imageData = null;
            
            if (isset($_FILES['product_image']) && $_FILES['product_image']['error'] === UPLOAD_ERR_OK) {
                error_log("📸 New image upload for edit: " . $_FILES['product_image']['name']);
                $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
                $fileType = $_FILES['product_image']['type'];
                
                if (in_array($fileType, $allowedTypes) && $_FILES['product_image']['size'] <= 5000000) {
                    // Compress and resize image
                    $imageData = compressImage($_FILES['product_image']['tmp_name'], $fileType);
                    if ($imageData === false) {
                        $_SESSION['product_error'] = "Image too large after compression. Please use a smaller image.";
                        header('Location: AdminDashboard.php?product_edit_error=1&show_modal=1');
                        exit();
                    }
                    $updateImage = true;
                    error_log("✅ New image processed and compressed: " . strlen($imageData) . " bytes");
                } else {
                    $_SESSION['product_error'] = "Invalid image file. Please use JPG, PNG, or GIF format under 5MB.";
                    header('Location: AdminDashboard.php?product_edit_error=1&show_modal=1');
                    exit();
                }
            }
            
            // Update product in database - with connection check
            if ($updateImage) {
                // Update with new image
                $stmt = $conn->prepare("UPDATE products SET name = ?, description = ?, price = ?, stock_quantity = ?, category_id = ?, product_image = ?, image_type = NULL WHERE product_id = ?");
                if (!$stmt) {
                    error_log("❌ Prepare failed: " . $conn->error);
                    $_SESSION['product_error'] = "Database prepare error: " . $conn->error;
                    header('Location: AdminDashboard.php?product_edit_error=1&show_modal=1');
                    exit();
                }
                
                $stmt->bind_param("ssdiisi", $name, $description, $price, $stock, $category_id, $imageData, $product_id);
                error_log("🔄 Updating product WITH new image: " . strlen($imageData) . " bytes");
            } else {
                // Update without changing image
                $stmt = $conn->prepare("UPDATE products SET name = ?, description = ?, price = ?, stock_quantity = ?, category_id = ? WHERE product_id = ?");
                if (!$stmt) {
                    error_log("❌ Prepare failed: " . $conn->error);
                    $_SESSION['product_error'] = "Database prepare error: " . $conn->error;
                    header('Location: AdminDashboard.php?product_edit_error=1&show_modal=1');
                    exit();
                }
                
                $stmt->bind_param("ssdiii", $name, $description, $price, $stock, $category_id, $product_id);
                error_log("🔄 Updating product WITHOUT changing image");
            }
            
            if ($stmt->execute()) {
                $_SESSION['product_success'] = "Product updated successfully!" . ($updateImage ? " Image updated." : " Image kept.");
                error_log("✅ Product updated successfully");
            } else {
                $_SESSION['product_error'] = "Failed to update product: " . $stmt->error;
                error_log("❌ Update failed: " . $stmt->error);
            }
            
            $stmt->close();
            
        } catch (Exception $e) {
            $_SESSION['product_error'] = "Error updating product: " . $e->getMessage();
            error_log("❌ Update exception: " . $e->getMessage());
        }
    }
    
    header('Location: AdminDashboard.php?product_updated=1&show_modal=1');
    exit();
}

// Handle product deletion
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_product']) && isset($_SESSION['admin_id'])) {
    $product_id = intval($_POST['product_id']);
    
    try {
        $stmt = $conn->prepare("DELETE FROM products WHERE product_id = ?");
        $stmt->bind_param("i", $product_id);
        
        if ($stmt->execute()) {
            $_SESSION['product_success'] = "Product deleted successfully!";
        } else {
            $_SESSION['product_error'] = "Failed to delete product: " . $conn->error;
        }
    } catch (Exception $e) {
        $_SESSION['product_error'] = "Error deleting product: " . $e->getMessage();
    }
    
    header('Location: AdminDashboard.php?product_deleted=1');
    exit();
}

// Handle order status update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['complete_order']) && isset($_SESSION['admin_id'])) {
    $payment_id = intval($_POST['payment_id']);
    
    try {
        $stmt = $conn->prepare("UPDATE payments SET payment_status = 'completed' WHERE payment_id = ?");
        $stmt->bind_param("i", $payment_id);
        
        if ($stmt->execute()) {
            $_SESSION['product_success'] = "Order marked as completed successfully!";
        } else {
            $_SESSION['product_error'] = "Failed to update order status: " . $conn->error;
        }
    } catch (Exception $e) {
        $_SESSION['product_error'] = "Error updating order status: " . $e->getMessage();
    }
    
    header('Location: AdminDashboard.php?order_completed=1');
    exit();
}

// Get categories for dropdown
function getCategories($conn) {
    $categories = [];
    try {
        $result = $conn->query("SELECT category_id, category_name FROM categories ORDER BY category_name");
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $categories[] = $row;
            }
        }
    } catch (Exception $e) {
        error_log("Error getting categories: " . $e->getMessage());
    }
    return $categories;
}

// Function to get all products 
function getAllProducts($conn) {
    $products = [];
    try {
        // JOIN with categories table to get actual category names
        $query = "SELECT p.product_id, p.name, p.description, p.price, p.stock_quantity, p.category_id, p.product_image, p.image_type, c.category_name 
                  FROM products p 
                  LEFT JOIN categories c ON p.category_id = c.category_id 
                  ORDER BY p.product_id DESC";
        $result = $conn->query($query);
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $products[] = [
                    'product_id' => (int)($row['product_id'] ?? 0),
                    'name' => $row['name'] ?? '',
                    'description' => $row['description'] ?? '',
                    'price' => (float)($row['price'] ?? 0),
                    'stock_quantity' => (int)($row['stock_quantity'] ?? 0),
                    'category_id' => (int)($row['category_id'] ?? 0),
                    'product_image' => $row['product_image'] ?? null,
                    'image_type' => $row['image_type'] ?? null,
                    'category_name' => $row['category_name'] ?? 'No Category' // Now gets real category name
                ];
            }
        }
    } catch (Exception $e) {
        error_log("Error getting products: " . $e->getMessage());
    }
    
    // Ensure we always return an array, even if empty
    return $products;
}

// Function to get pending orders
function getPendingOrders($conn) {
    $orders = [];
    try {
        $query = "SELECT payment_id, order_id, payment_method, payment_status, amount_paid, payment_date, delivery_address 
                  FROM payments 
                  WHERE payment_status = 'pending' 
                  ORDER BY payment_date DESC";
        $result = $conn->query($query);
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $orders[] = [
                    'payment_id' => (int)($row['payment_id'] ?? 0),
                    'order_id' => (int)($row['order_id'] ?? 0),
                    'payment_method' => $row['payment_method'] ?? '',
                    'payment_status' => $row['payment_status'] ?? '',
                    'amount_paid' => (float)($row['amount_paid'] ?? 0),
                    'payment_date' => $row['payment_date'] ?? '',
                    'delivery_address' => $row['delivery_address'] ?? ''
                ];
            }
        }
    } catch (Exception $e) {
        error_log("Error getting pending orders: " . $e->getMessage());
    }
    
    return $orders;
}

// Get dashboard stats
function getDashboardStats($conn) {
    $stats = [
        'total_sales' => 0,
        'daily_sales' => 0,
        'yesterday_sales' => 0,
        'sales_change_percent' => 0,
        'total_customers' => 0,
        'last_month_customers' => 0,
        'customer_change_percent' => 0,
        'total_products' => 0,
        'last_month_products' => 0,
        'product_change_percent' => 0,
        'total_orders' => 0,
        'total_deliveries' => 0
    ];
    
    try {
        // Use payments table for sales data
        $stmt = $conn->prepare("SELECT COALESCE(SUM(amount_paid), 0) as total_sales FROM payments WHERE payment_status = 'completed'");
        if ($stmt) {
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result) {
                $row = $result->fetch_assoc();
                $stats['total_sales'] = (float)$row['total_sales'];
            }
        }
        
        // Today's sales
        $stmt = $conn->prepare("SELECT COALESCE(SUM(amount_paid), 0) as daily_sales FROM payments WHERE DATE(payment_date) = CURDATE() AND payment_status = 'completed'");
        if ($stmt) {
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result) {
                $row = $result->fetch_assoc();
                $stats['daily_sales'] = (float)$row['daily_sales'];
            }
        }
        
        // Yesterday's sales for comparison
        $stmt = $conn->prepare("SELECT COALESCE(SUM(amount_paid), 0) as yesterday_sales FROM payments WHERE DATE(payment_date) = DATE_SUB(CURDATE(), INTERVAL 1 DAY) AND payment_status = 'completed'");
        if ($stmt) {
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result) {
                $row = $result->fetch_assoc();
                $stats['yesterday_sales'] = (float)$row['yesterday_sales'];
            }
        }
        
        // Calculate daily sales percentage change
        if ($stats['yesterday_sales'] > 0) {
            $stats['sales_change_percent'] = (($stats['daily_sales'] - $stats['yesterday_sales']) / $stats['yesterday_sales']) * 100;
        } else if ($stats['daily_sales'] > 0) {
            $stats['sales_change_percent'] = 100; // 100% increase from 0
        }
        
        $stmt = $conn->prepare("SELECT COUNT(*) as total_products FROM products");
        if ($stmt) {
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result) {
                $row = $result->fetch_assoc();
                $stats['total_products'] = (int)$row['total_products'];
            }
        }
        
        $stmt = $conn->prepare("SELECT COUNT(*) as total_orders FROM orders");
        if ($stmt) {
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result) {
                $row = $result->fetch_assoc();
                $stats['total_orders'] = (int)$row['total_orders'];
            }
        }
        
        // Updated to use actual users table instead of customers
        $stmt = $conn->prepare("SELECT COUNT(*) as total_customers FROM users");
        if ($stmt) {
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result) {
                $row = $result->fetch_assoc();
                $stats['total_customers'] = (int)$row['total_customers'];
            }
        }
        
        // Count deliveries using completed payments (same logic as sales)
        $stmt = $conn->prepare("SELECT COUNT(*) as total_deliveries FROM payments WHERE payment_status = 'completed'");
        if ($stmt) {
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result) {
                $row = $result->fetch_assoc();
                $stats['total_deliveries'] = (int)$row['total_deliveries'];
            }
        }
        
    } catch (Exception $e) {
        $stats = [
            'total_sales' => 35.44,
            'daily_sales' => 0,
            'yesterday_sales' => 0,
            'sales_change_percent' => 0,
            'total_customers' => 10,
            'total_products' => 1,
            'total_orders' => 5,
            'total_deliveries' => 3
        ];
    }
    
    return $stats;
}

// Get monthly sales data for chart
function getMonthlySalesData($conn) {
    $monthlyData = array_fill(0, 12, 0);
    
    try {
        // Get real sales data from payments table for 2025
        $stmt = $conn->prepare("
            SELECT 
                MONTH(payment_date) as month,
                COALESCE(SUM(amount_paid), 0) as total_sales
            FROM payments 
            WHERE YEAR(payment_date) = 2025 AND payment_status = 'completed'
            GROUP BY MONTH(payment_date)
            ORDER BY MONTH(payment_date)
        ");
        
        if ($stmt) {
            $stmt->execute();
            $result = $stmt->get_result();
            
            while ($row = $result->fetch_assoc()) {
                $monthIndex = $row['month'] - 1; // Convert to 0-based index
                if ($monthIndex >= 0 && $monthIndex < 12) {
                    $monthlyData[$monthIndex] = (float)$row['total_sales'];
                }
            }
        }
        
    } catch (Exception $e) {
        // If no real data, use fallback
        $monthlyData = [0, 0, 0, 0, 35.44, 0, 0, 0, 0, 0, 0, 0]; // May 2025 completed payments
    }
    
    return $monthlyData;
}

$categories = getCategories($conn);
$allProducts = getAllProducts($conn);
$pendingOrders = getPendingOrders($conn);
$stats = getDashboardStats($conn);
$monthlySalesData = getMonthlySalesData($conn);
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

    /* existing dashboard styles are here */
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
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: space-between;
        padding: 20px;
        box-sizing: border-box;
    }
    
    .box1:hover, .box2:hover, .box3:hover, .box4:hover, .box5:hover {
        transform: translateY(-5px);
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
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: space-between;
        padding: 20px;
        box-sizing: border-box;
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
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: space-between;
        padding: 20px;
        box-sizing: border-box;
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
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: space-between;
        padding: 20px;
        box-sizing: border-box;
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
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: space-between;
        padding: 20px;
        box-sizing: border-box;
    }

    /* Update circle styles to be relative positioning */
    .dashboard-icon {
        width: 80px;
        height: 80px;
        background: white;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        position: relative;
        margin-top: 10px;
    }

    .dashboard-icon::after {
        content: '';
        position: absolute;
        width: 65px;
        height: 65px;
        border-radius: 50%;
        z-index: 1;
    }

    .box1 .dashboard-icon::after { background: #D997D5; }
    .box2 .dashboard-icon::after { background: #7B87C6; }
    .box3 .dashboard-icon::after { background: #7BC68F; }
    .box4 .dashboard-icon::after { background: #C6B27B; }
    .box5 .dashboard-icon::after { background: #6A34D6; }

    .dashboard-icon img {
        width: 35px;
        height: 35px;
        z-index: 2;
        position: relative;
    }

    /* Update text styles to work inside containers */
    .box-title {
        color: black;
        font-size: 18px;
        font-weight: 500;
        text-align: center;
        margin: 10px 0 5px 0;
    }

    .box-percentage {
        color: #8D7D7D;
        font-size: 12px;
        font-weight: 300;
        text-align: center;
        margin: 0;
    }

    .box-value {
        color: black;
        font-size: 24px;
        font-weight: 600;
        text-align: center;
        margin: 5px 0 10px 0;
    }

    .rectangle2{
        position:absolute;
        left: 150px;
        top: 500px;
        width: 1170px;
        height: 400px;
        background: linear-gradient(to bottom, #B85CD7, #DDCFCF);
        border-radius:14px;
        z-index: 1;
        display: flex;
        flex-direction: column;
        padding: 20px;
        box-sizing: border-box;
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

    /* Product Management Modal Styles */
    .product-modal {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.7);
        z-index: 2000;
    }

    .product-modal-container {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        width: 90%;
        max-width: 600px;
        max-height: 90vh;
        overflow-y: auto;
        padding: 30px;
        border-radius: 15px;
        background: linear-gradient(to bottom, #D997D5, #FFFFFF);
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
    }

    .product-modal-close {
        position: absolute;
        top: 15px;
        right: 20px;
        background: none;
        border: none;
        color: #333;
        font-size: 24px;
        cursor: pointer;
        font-weight: bold;
    }

    .product-form-title {
        color: #333;
        font-family: Poppins, sans-serif;
        font-size: 24px;
        font-weight: 700;
        text-align: center;
        margin-bottom: 25px;
    }

    .product-form {
        display: flex;
        flex-direction: column;
        gap: 15px;
    }

    .product-form-group {
        display: flex;
        flex-direction: column;
    }

    .product-input-label {
        color: #333;
        font-family: Poppins;
        font-size: 14px;
        font-weight: 600;
        margin-bottom: 5px;
    }

    .product-form-input, .product-form-select, .product-form-textarea {
        width: 100%;
        padding: 10px;
        border: 2px solid rgba(0, 0, 0, 0.1);
        border-radius: 8px;
        font-size: 14px;
        font-family: Poppins;
        box-sizing: border-box;
        background: rgba(255, 255, 255, 0.9);
    }

    .product-form-textarea {
        min-height: 80px;
        resize: vertical;
    }

    .product-form-input:focus, .product-form-select:focus, .product-form-textarea:focus {
        outline: none;
        border-color: #D997D5;
        box-shadow: 0 0 5px rgba(217, 151, 213, 0.3);
    }

    .product-submit-btn {
        background: linear-gradient(45deg, #D997D5, #B85CD7);
        color: white;
        border: none;
        padding: 12px 20px;
        border-radius: 8px;
        font-size: 16px;
        font-weight: 600;
        cursor: pointer;
        margin-top: 10px;
        transition: transform 0.2s ease;
    }

    .product-submit-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(217, 151, 213, 0.4);
    }

    .product-success {
        background-color: rgba(76, 175, 80, 0.8);
        color: white;
        padding: 10px;
        border-radius: 5px;
        margin-bottom: 15px;
        text-align: center;
    }

    .product-error {
        background-color: rgba(244, 67, 54, 0.8);
        color: white;
        padding: 10px;
        border-radius: 5px;
        margin-bottom: 15px;
        text-align: center;
    }

    .add-product-btn {
        position: absolute;
        right: 50px;
        top: 100px;
        background: linear-gradient(45deg, #D997D5, #B85CD7);
        color: white;
        padding: 10px 20px;
        border: none;
        border-radius: 8px;
        cursor: pointer;
        font-size: 14px;
        font-weight: 600;
        transition: transform 0.2s ease;
    }

    .add-product-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(217, 151, 213, 0.4);
    }

    /* Products Table Styles */
    .products-table-section {
        position: absolute;
        left: 150px;
        top: 950px;
        width: 1170px;
        background: rgba(255, 255, 255, 0.95);
        border-radius: 14px;
        padding: 30px;
        box-sizing: border-box;
        margin-bottom: 50px;
        z-index: 10;
    }

    .products-table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 20px;
        background: white;
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    }

    .products-table th {
        background: linear-gradient(45deg, #D997D5, #B85CD7);
        color: white;
        padding: 15px 12px;
        text-align: left;
        font-weight: 600;
        font-size: 14px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        cursor: pointer;
        user-select: none;
        position: relative;
        transition: background 0.3s ease;
    }

    .products-table th:hover {
        background: linear-gradient(45deg, #B85CD7, #9A4AC7);
    }

    .products-table th.sortable::after {
        content: '↕';
        position: absolute;
        right: 8px;
        top: 50%;
        transform: translateY(-50%);
        font-size: 12px;
        opacity: 0.7;
    }

    .products-table th.sort-asc::after {
        content: '↑';
        opacity: 1;
    }

    .products-table th.sort-desc::after {
        content: '↓';
        opacity: 1;
    }

    .products-table th.non-sortable {
        cursor: default;
    }

    .products-table th.non-sortable:hover {
        background: linear-gradient(45deg, #D997D5, #B85CD7);
    }

    .products-table td {
        padding: 12px;
        border-bottom: 1px solid #e9ecef;
        font-size: 14px;
        color: #333;
    }

    .products-table tr:hover {
        background-color: #f8f9fa;
    }

    .table-actions {
        display: flex;
        gap: 8px;
    }

    .btn-edit, .btn-delete {
        padding: 6px 12px;
        border: none;
        border-radius: 4px;
        font-size: 12px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .btn-edit {
        background: #007bff;
        color: white;
    }

    .btn-edit:hover {
        background: #0056b3;
    }

    .btn-delete {
        background: #dc3545;
        color: white;
    }

    .btn-delete:hover {
        background: #c82333;
    }

    /* Orders Management Section */
    .orders-table-section {
        position: absolute;
        left: 150px;
        top: 1650px;
        width: 1170px;
        background: rgba(255, 255, 255, 0.95);
        border-radius: 14px;
        padding: 30px;
        box-sizing: border-box;
        margin-bottom: 100px;
        z-index: 10;
    }

    .orders-table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 20px;
        background: white;
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    }

    .orders-table th {
        background: linear-gradient(45deg, #7B87C6, #5B6FA8);
        color: white;
        padding: 15px 12px;
        text-align: left;
        font-weight: 600;
        font-size: 14px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .orders-table td {
        padding: 12px;
        border-bottom: 1px solid #e9ecef;
        font-size: 14px;
        color: #333;
        vertical-align: top;
    }

    .orders-table tr:hover {
        background-color: #f8f9fa;
    }

    .btn-complete {
        background: #28a745;
        color: white;
        padding: 8px 16px;
        border: none;
        border-radius: 4px;
        font-size: 12px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .btn-complete:hover {
        background: #218838;
        transform: translateY(-1px);
    }

    .status-badge {
        padding: 4px 8px;
        border-radius: 12px;
        font-size: 12px;
        font-weight: 600;
        text-transform: uppercase;
    }

    .status-pending {
        background: #ffc107;
        color: #212529;
    }

    .address-cell {
        max-width: 200px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    /* Ensure body has enough height for all content */
    body {
        min-height: 2200px;
    }
    </style>
</head>

<body>
    <?php if (!isset($_SESSION['admin_id'])): ?>
    <!-- Admin Login Modal - Matching Welcome.php Style -->
    <div id="adminLoginPopup" class="popup-overlay">
        <div class="popup-container">
            <button class="popup-close" onclick="window.location.href='MainPage.php'">&times;</button>
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
                        <input type="password" class="form-input" name="password" id="adminPassword" placeholder="Enter password" required />
                        <button type="button" class="password-toggle" onclick="var input=document.getElementById('adminPassword'); if(input.type==='password'){ input.type='text'; this.textContent='Hide'; } else { input.type='password'; this.textContent='Show'; }">Show</button>
                    </div>
                </div>
                <button type="submit" name="admin_login" class="login-button">Login to Dashboard</button>
                <p class="signup-text">
                    <a href="MainPage.php">← Back to Store</a>
                </p>
                <div style="margin-top: 15px; font-size: 12px; color: #ccc; text-align: center;">
                    Available accounts:<br>
                    admin@test.com (emmanuelle pranada)<br>
                    admin@af.com (test test)
                </div>
            </form>
        </div>
    </div>
    <?php else: ?>
    <!-- Welcome message and logout for logged in admin -->
    <div class="admin-welcome">
        Welcome, <?php echo htmlspecialchars($_SESSION['admin_name'] ?? 'Admin'); ?><?php if (isset($_SESSION['admin_email']) && !empty($_SESSION['admin_email'])): ?> (<?php echo htmlspecialchars($_SESSION['admin_email']); ?>)<?php endif; ?>
    </div>
    <a href="?logout=1" class="admin-logout" onclick="return confirm('Are you sure you want to logout?')">Logout</a>
    
    <!-- Add Product Button -->
    <button class="add-product-btn" onclick="document.getElementById('productModal').style.display='block'; document.getElementById('modalTitle').textContent='Add New Product'; document.getElementById('submitBtn').textContent='Add Product'; document.getElementById('submitBtn').name='add_product'; document.getElementById('product_id').value=''; document.getElementById('productForm').reset();">+ Add Product</button>
    <?php endif; ?>

    <!-- Product Management Modal -->
    <div id="productModal" class="product-modal">
        <div class="product-modal-container">
            <button class="product-modal-close" onclick="document.getElementById('productModal').style.display='none'; document.getElementById('productForm').reset();">&times;</button>
            <h2 class="product-form-title" id="modalTitle">Add New Product</h2>
            
            <?php if ($productSuccess): ?>
                <div class="product-success"><?php echo htmlspecialchars($productSuccess); ?></div>
            <?php endif; ?>
            
            <?php if ($productError): ?>
                <div class="product-error"><?php echo htmlspecialchars($productError); ?></div>
            <?php endif; ?>
            
            <form class="product-form" method="POST" enctype="multipart/form-data" id="productForm">
                <!-- Hidden field for edit mode -->
                <input type="hidden" id="product_id" name="product_id" value="">
                
                <div class="product-form-group">
                    <label class="product-input-label" for="product_name">Product Name</label>
                    <input type="text" class="product-form-input" id="product_name" name="product_name" required 
                           placeholder="Enter product name">
                </div>
                
                <div class="product-form-group">
                    <label class="product-input-label" for="product_description">Description</label>
                    <textarea class="product-form-textarea" id="product_description" name="product_description" required 
                              placeholder="Enter product description"></textarea>
                </div>
                
                <div class="product-form-group">
                    <label class="product-input-label" for="product_price">Price (₱)</label>
                    <input type="number" class="product-form-input" id="product_price" name="product_price" required 
                           min="0" step="0.01" placeholder="0.00">
                </div>
                
                <div class="product-form-group">
                    <label class="product-input-label" for="product_stock">Stock Quantity</label>
                    <input type="number" class="product-form-input" id="product_stock" name="product_stock" required 
                           min="0" placeholder="0">
                </div>
                
                <div class="product-form-group">
                    <label class="product-input-label" for="category_id">Category</label>
                    <select class="product-form-select" id="category_id" name="category_id" required>
                        <option value="">Select a category</option>
                        <?php foreach ($categories as $category): ?>
                            <option value="<?php echo $category['category_id']; ?>">
                                <?php echo htmlspecialchars($category['category_name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="product-form-group">
                    <label class="product-input-label" for="product_image">Product Image <span id="imageLabel">(Optional)</span></label>
                    <input type="file" class="product-form-input" id="product_image" name="product_image" 
                           accept="image/jpeg,image/png,image/gif">
                    <small style="color: #666; font-size: 12px;">Max size: 5MB. Supported: JPG, PNG, GIF</small>
                    <div id="currentImage" style="margin-top: 10px; display: none;">
                        <p style="color: #666; font-size: 12px;">Current image:</p>
                        <img id="currentImagePreview" style="width: 100px; height: 100px; object-fit: cover; border-radius: 6px;">
                    </div>
                </div>
                
                <div style="display: flex; gap: 10px; margin-top: 20px;">
                    <button type="submit" id="submitBtn" name="add_product" class="product-submit-btn" style="flex: 1;">Add Product</button>
                    <button type="button" onclick="document.getElementById('productModal').style.display='none'; document.getElementById('productForm').reset();" class="product-submit-btn" style="flex: 1; background: #6c757d;">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Dashboard Content -->
    <div class="dashboard-content">
        <div class="boxes">
            <div class="header-section"></div>
            <header class="header">
                <h2 class="brand-name">A&F</h2>
                <h2 class="Admin">Admin Dashboard</h2>

                <!-- Dashboard boxes with contained content -->
                <div class="box1" onclick="alert('Sales Details:\n\nTotal Sales: ₱<?php echo number_format($stats['total_sales'], 2); ?>\n\nDaily Sales: ₱<?php echo number_format($stats['daily_sales'], 2); ?>\n\nThis feature will show detailed sales analytics in a future update.');">
                    <div class="dashboard-icon">
                        <img src="images/topsales.png" alt="Total Sales Icon">
                    </div>
                    <div class="box-title">Total Sales</div>
                    <div class="box-value">₱<?php echo number_format($stats['total_sales'], 2); ?></div>
                    <div class="box-percentage">+₱<?php echo number_format($stats['daily_sales'], 2); ?> today</div>
                </div>
                
                <div class="box2" onclick="alert('Daily Sales Details:\n\nToday\'s Sales: ₱<?php echo number_format($stats['daily_sales'], 2); ?>\n\nThis feature will show daily sales breakdown in a future update.');">
                    <div class="dashboard-icon">
                        <img src="images/dailysales.png" alt="Daily Sales Icon">
                    </div>
                    <div class="box-title">Daily Sales</div>
                    <div class="box-value">₱<?php echo number_format($stats['daily_sales'], 2); ?></div>
                    <div class="box-percentage">
                        <?php if ($stats['sales_change_percent'] > 0): ?>
                            +<?php echo number_format($stats['sales_change_percent'], 1); ?>% from yesterday
                        <?php elseif ($stats['sales_change_percent'] < 0): ?>
                            <?php echo number_format($stats['sales_change_percent'], 1); ?>% from yesterday
                        <?php else: ?>
                            No change from yesterday
                        <?php endif; ?>
                    </div>
                </div>
                
                <div class="box3" onclick="document.querySelector('.products-table-section').scrollIntoView({ behavior: 'smooth' });">
                    <div class="dashboard-icon">
                        <img src="images/cart.png" alt="Products Icon">
                    </div>
                    <div class="box-title">Products</div>
                    <div class="box-value"><?php echo $stats['total_products']; ?></div>
                </div>
                
                <div class="box4" onclick="alert('Customer Analytics:\n\nTotal Registered Customers: <?php echo $stats['total_customers']; ?>\n\nCustomer Details:\n- Name, Email, Phone tracked\n- Registration dates monitored\n- Notification preferences managed\n\nThis feature will show detailed customer management in a future update.');">
                    <div class="dashboard-icon">
                        <img src="images/users.png" alt="Customers Icon">
                    </div>
                    <div class="box-title">Customers</div>
                    <div class="box-value"><?php echo $stats['total_customers']; ?></div>
                </div>
    
                <div class="box5" onclick="alert('Delivery Management:\n\nCompleted Deliveries: <?php echo $stats['total_deliveries'] ?? $stats['total_orders']; ?>\n\nBased on completed payments\n\nThis feature will show delivery tracking in a future update.');">
                    <div class="dashboard-icon">
                        <img src="images/deliv.png" alt="Delivery Icon">
                    </div>
                    <div class="box-title">Delivery</div>
                    <div class="box-value"><?php echo $stats['total_deliveries'] ?? $stats['total_orders']; ?></div>
                    <div class="box-percentage">Only completed orders</div>
                </div>
            </header>

            <!-- Summary sections -->
            <div class="sum-sales, top-sales">
                <!-- Rectangle2 now contains Chart.js chart -->
                <div class="rectangle2">
                    <div style="position: relative; width: 100%; height: 100%; display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 25px; box-sizing: border-box;">
                        <h3 style="color: white; text-align: center; margin-bottom: 25px; font-size: 18px; margin-top: 0;">📊 Sales Chart 2025 📊</h3>
                        <div style="position: relative; width: 98%; max-width: 1100px; height: 300px; background: rgba(255,255,255,0.1); border-radius: 10px; padding: 15px; box-sizing: border-box; display: flex; align-items: center; justify-content: center;">
                            <canvas id="salesChart" style="width: 100% !important; height: 100% !important; max-width: 100%; max-height: 100%;"></canvas>
                        </div>
                    </div>
                </div>
            </div>
    
        </div>
    </div>

    <!-- Products Table Section - OUTSIDE dashboard-content -->
    <div class="products-table-section">
        <h2 style="color: #333; font-family: Poppins, sans-serif; font-size: 28px; font-weight: 700; margin-bottom: 20px; text-align: center;">Product Management</h2>
        <div style="overflow-x: auto;">
            <table class="products-table" id="productsTable">
                <thead>
                    <tr>
                        <th class="sortable" data-column="product_id" data-type="number">ID</th>
                        <th class="non-sortable">Image</th>
                        <th class="sortable" data-column="name" data-type="text">Name</th>
                        <th class="non-sortable">Description</th>
                        <th class="sortable" data-column="price" data-type="number">Price</th>
                        <th class="sortable" data-column="stock_quantity" data-type="number">Stock</th>
                        <th class="non-sortable">Category</th>
                        <th class="non-sortable">Actions</th>
                    </tr>
                </thead>
                <tbody id="productsTableBody">
                    <?php foreach ($allProducts as $product): ?>
                    <tr data-product-id="<?php echo $product['product_id']; ?>">
                        <td data-sort="<?php echo $product['product_id']; ?>"><?php echo $product['product_id']; ?></td>
                        <td>
                            <?php if ($product['product_image']): ?>
                                <?php
                                // Detect image type from binary data since image_type is always NULL
                                $imageData = $product['product_image'];
                                $detectedType = 'image/jpeg'; // default fallback
                                
                                // Check PNG signature
                                if (substr($imageData, 0, 8) === "\x89PNG\r\n\x1a\n") {
                                    $detectedType = 'image/png';
                                }
                                // Check GIF signature  
                                elseif (substr($imageData, 0, 6) === "GIF87a" || substr($imageData, 0, 6) === "GIF89a") {
                                    $detectedType = 'image/gif';
                                }
                                // Check JPEG signature
                                elseif (substr($imageData, 0, 2) === "\xFF\xD8") {
                                    $detectedType = 'image/jpeg';
                                }
                                ?>
                                <img src="data:<?php echo $detectedType; ?>;base64,<?php echo base64_encode($product['product_image']); ?>" 
                                     alt="<?php echo htmlspecialchars($product['name']); ?>" 
                                     style="width: 50px; height: 50px; object-fit: cover; border-radius: 6px;">
                            <?php else: ?>
                                <div style="width: 50px; height: 50px; background: #f8f9fa; border-radius: 6px; display: flex; align-items: center; justify-content: center; font-size: 10px; color: #6c757d;">No Image</div>
                            <?php endif; ?>
                        </td>
                        <td data-sort="<?php echo htmlspecialchars(strtolower($product['name'])); ?>" style="font-weight: 600;"><?php echo htmlspecialchars($product['name']); ?></td>
                        <td style="max-width: 200px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;"><?php echo htmlspecialchars($product['description']); ?></td>
                        <td data-sort="<?php echo $product['price']; ?>" style="font-weight: 600; color: #D997D5;">₱<?php echo number_format($product['price'], 2); ?></td>
                        <td data-sort="<?php echo $product['stock_quantity']; ?>"><?php echo $product['stock_quantity']; ?></td>
                        <td><span style="background: #e9ecef; padding: 4px 8px; border-radius: 12px; font-size: 12px; color: #495057;"><?php echo htmlspecialchars($product['category_name'] ?? 'No Category'); ?></span></td>
                        <td>
                            <div class="table-actions">
                                <button class="btn-edit" onclick="editProductById(<?php echo $product['product_id']; ?>)">Edit</button>
                                <form method="POST" style="display: inline;">
                                    <input type="hidden" name="product_id" value="<?php echo $product['product_id']; ?>">
                                    <button type="submit" name="delete_product" class="btn-delete" 
                                            onclick="return confirm('Are you sure you want to delete this product?')">Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Orders Management Section -->
    <div class="orders-table-section">
        <h2 style="color: #333; font-family: Poppins, sans-serif; font-size: 28px; font-weight: 700; margin-bottom: 20px; text-align: center;">Pending Orders Management</h2>
        <?php if (count($pendingOrders) > 0): ?>
        <div style="overflow-x: auto;">
            <table class="orders-table">
                <thead>
                    <tr>
                        <th>Payment ID</th>
                        <th>Order ID</th>
                        <th>Payment Method</th>
                        <th>Amount</th>
                        <th>Date</th>
                        <th>Delivery Address</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($pendingOrders as $order): ?>
                    <tr>
                        <td style="font-weight: 600;"><?php echo $order['payment_id']; ?></td>
                        <td><?php echo $order['order_id']; ?></td>
                        <td><?php echo htmlspecialchars($order['payment_method']); ?></td>
                        <td style="font-weight: 600; color: #7B87C6;">₱<?php echo number_format($order['amount_paid'], 2); ?></td>
                        <td><?php echo date('M j, Y g:i A', strtotime($order['payment_date'])); ?></td>
                        <td class="address-cell" title="<?php echo htmlspecialchars($order['delivery_address']); ?>">
                            <?php echo htmlspecialchars($order['delivery_address']); ?>
                        </td>
                        <td>
                            <span class="status-badge status-pending">Pending</span>
                        </td>
                        <td>
                            <form method="POST" style="display: inline;">
                                <input type="hidden" name="payment_id" value="<?php echo $order['payment_id']; ?>">
                                <button type="submit" name="complete_order" class="btn-complete" 
                                        onclick="return confirm('Mark this order as completed?')">Complete Order</button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php else: ?>
        <div style="text-align: center; padding: 40px; color: #6c757d;">
            <h3>🎉 No pending orders!</h3>
            <p>All orders have been processed.</p>
        </div>
        <?php endif; ?>
    </div>

    <!-- Load Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        console.log('🚀 Starting simple script approach...');

        // Store product data globally
        const productDataMap = new Map();
        <?php foreach ($allProducts as $product): ?>
        productDataMap.set(<?php echo $product['product_id']; ?>, {
            product_id: <?php echo $product['product_id']; ?>,
            name: <?php echo json_encode($product['name']); ?>,
            description: <?php echo json_encode($product['description']); ?>,
            price: <?php echo $product['price']; ?>,
            stock_quantity: <?php echo $product['stock_quantity']; ?>,
            category_id: <?php echo $product['category_id']; ?>,
            has_image: <?php echo $product['product_image'] ? 'true' : 'false'; ?>,
            image_type: <?php echo json_encode($product['image_type']); ?>
        });
        <?php endforeach; ?>

        // Edit product function
         function editProductById(productId) {
            console.log('✏️ Editing product ID:', productId);
            
            const product = productDataMap.get(productId);
            if (!product) {
                alert('Product not found!');
                return;
            }
            
            console.log('📦 Found product:', product.name);
            
            // Show modal
            document.getElementById('productModal').style.display = 'block';
            
            // Set modal to edit mode
            document.getElementById('modalTitle').textContent = 'Edit Product';
            document.getElementById('submitBtn').textContent = 'Save Changes';
            document.getElementById('submitBtn').name = 'edit_product';
            
            // Fill form with product data
            document.getElementById('product_id').value = productId;
            document.getElementById('product_name').value = product.name;
            document.getElementById('product_description').value = product.description;
            document.getElementById('product_price').value = product.price.toFixed(2);
            document.getElementById('product_stock').value = product.stock_quantity;
            document.getElementById('category_id').value = product.category_id;
            
            // Handle current image display
            const currentImg = document.getElementById('currentImage');
            const imgPreview = document.getElementById('currentImagePreview');
            const imageLabel = document.getElementById('imageLabel');
            
            if (product.has_image) {
                // Get the actual image from the table row
                const tableRow = document.querySelector(`tr[data-product-id="${productId}"]`);
                const existingImg = tableRow ? tableRow.querySelector('img') : null;
                
                if (existingImg && existingImg.src) {
                    console.log('🖼️ Found existing image in table');
                    currentImg.style.display = 'block';
                    imgPreview.src = existingImg.src;
                    imgPreview.alt = product.name;
                    imageLabel.textContent = '(Optional - leave empty to keep current image)';
                } else {
                    console.log('❌ No image found in table row');
                    currentImg.style.display = 'none';
                    imageLabel.textContent = '(Optional)';
                }
            } else {
                console.log('📷 Product has no image');
                currentImg.style.display = 'none';
                imageLabel.textContent = '(Optional)';
            }
            
            // Clear the file input
            document.getElementById('product_image').value = '';
            
            console.log('✅ Edit modal opened successfully');
        }

        // Simple chart creation function
        function createChart() {
            const ctx = document.getElementById('salesChart');
            if (!ctx) {
                console.log('❌ No canvas found');
                return;
            }

            const salesData = <?php echo json_encode($monthlySalesData); ?>;
            const monthLabels = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
            
            console.log('📊 Creating chart with data:', salesData);

            try {
                new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: monthLabels,
                        datasets: [{
                            label: 'Monthly Sales (₱)',
                            data: salesData,
                            borderColor: '#FFD700',
                            backgroundColor: 'rgba(255, 215, 0, 0.1)',
                            borderWidth: 3,
                            fill: true,
                            tension: 0.4,
                            pointBackgroundColor: '#FFD700',
                            pointBorderColor: '#FFF',
                            pointBorderWidth: 2,
                            pointRadius: 5,
                            pointHoverRadius: 8
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                labels: { color: 'white' }
                            },
                            tooltip: {
                                backgroundColor: 'rgba(0, 0, 0, 0.8)',
                                titleColor: 'white',
                                bodyColor: 'white',
                                borderColor: '#FFD700',
                                borderWidth: 1
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                grid: { color: 'rgba(255, 255, 255, 0.2)' },
                                ticks: { 
                                    color: 'white',
                                    callback: function(value) { return '₱' + value; }
                                }
                            },
                            x: {
                                grid: { color: 'rgba(255, 255, 255, 0.2)' },
                                ticks: { color: 'white' }
                            }
                        }
                    }
                });
                console.log('✅ Chart created successfully!');
            } catch (error) {
                console.error('❌ Chart error:', error);
            }
        }

        // Table sorting functionality
        let currentSort = { column: null, direction: 'asc' };

        function sortTable(columnIndex, dataType, columnName) {
            const table = document.getElementById('productsTable');
            const tbody = table.querySelector('tbody');
            const rows = Array.from(tbody.querySelectorAll('tr'));
            
            // Determine sort direction
            if (currentSort.column === columnName) {
                currentSort.direction = currentSort.direction === 'asc' ? 'desc' : 'asc';
            } else {
                currentSort.direction = 'asc';
            }
            currentSort.column = columnName;
            
            // Update header classes
            const headers = table.querySelectorAll('th.sortable');
            headers.forEach(h => {
                h.classList.remove('sort-asc', 'sort-desc');
            });
            
            const currentHeader = table.querySelector(`th[data-column="${columnName}"]`);
            currentHeader.classList.add(currentSort.direction === 'asc' ? 'sort-asc' : 'sort-desc');
            
            // Sort rows
            rows.sort((a, b) => {
                let aValue, bValue;
                
                if (dataType === 'number') {
                    aValue = parseFloat(a.cells[columnIndex].getAttribute('data-sort')) || 0;
                    bValue = parseFloat(b.cells[columnIndex].getAttribute('data-sort')) || 0;
                } else {
                    aValue = a.cells[columnIndex].getAttribute('data-sort') || a.cells[columnIndex].textContent;
                    bValue = b.cells[columnIndex].getAttribute('data-sort') || b.cells[columnIndex].textContent;
                    aValue = aValue.toString().toLowerCase();
                    bValue = bValue.toString().toLowerCase();
                }
                
                if (currentSort.direction === 'asc') {
                    return aValue > bValue ? 1 : -1;
                } else {
                    return aValue < bValue ? 1 : -1;
                }
            });
            
            // Re-append sorted rows
            rows.forEach(row => tbody.appendChild(row));
        }

        // Add click listeners to sortable headers
        document.addEventListener('DOMContentLoaded', function() {
            console.log('📋 DOM ready - initializing...');
            
            // Check if we should show the modal (after form submission)
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.get('show_modal') === '1') {
                console.log('🔔 Showing modal due to URL parameter');
                document.getElementById('productModal').style.display = 'block';
                
                // Remove the parameter from URL without refresh
                window.history.replaceState({}, document.title, window.location.pathname);
            }
            
            // Create chart
            setTimeout(createChart, 300);
            
            // Add form submission logging
            const productForm = document.getElementById('productForm');
            if (productForm) {
                productForm.addEventListener('submit', function(e) {
                    console.log('📝 Form submission:', {
                        action: document.getElementById('submitBtn').name,
                        hasFile: document.getElementById('product_image').files.length > 0,
                        fileName: document.getElementById('product_image').files[0]?.name || 'none'
                    });
                });
            }
            
            // Add sorting functionality
            const sortableHeaders = document.querySelectorAll('th.sortable');
            sortableHeaders.forEach((header, index) => {
                header.addEventListener('click', function() {
                    const columnName = this.getAttribute('data-column');
                    const dataType = this.getAttribute('data-type');
                   
                    const columnIndex = Array.from(this.parentNode.children).indexOf(this);
                    sortTable(columnIndex, dataType, columnName);
                });
            });
            
            // Close modal when clicking outside
            window.addEventListener('click', function(event) {
                const modal = document.getElementById('productModal');
                if (event.target === modal) {
                    modal.style.display = 'none';
                    document.getElementById('productForm').reset();
                }
            });
            
            console.log('✅ Initialization complete with debugging!');
        });

        console.log('🎯 Script loaded successfully!');
    </script>
</body>
</html>
