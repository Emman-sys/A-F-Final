<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: Welcome.php');
    exit;
}

// Database connection
$conn = new mysqli("localhost", "root", "", "a&f chocolate");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get user data
$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT * FROM users WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user_data = $result->fetch_assoc();
$stmt->close();

// Handle AJAX requests
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    header('Content-Type: application/json');
    
    switch ($_POST['action']) {
        case 'save_profile':
            $name = trim($_POST['fullName']);
            $email = trim($_POST['email']);
            $phone = trim($_POST['phone']);
            $address = trim($_POST['address']);
            
            if (empty($name) || empty($email)) {
                echo json_encode(['success' => false, 'message' => 'Name and email are required']);
                exit;
            }
            
            // Check if email exists for other users
            $stmt = $conn->prepare("SELECT user_id FROM users WHERE email = ? AND user_id != ?");
            $stmt->bind_param("si", $email, $user_id);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows > 0) {
                echo json_encode(['success' => false, 'message' => 'Email already exists']);
                exit;
            }
            
            // Update user profile
            $stmt = $conn->prepare("UPDATE users SET name = ?, email = ?, phone_number = ?, address = ? WHERE user_id = ?");
            $stmt->bind_param("ssssi", $name, $email, $phone, $address, $user_id);
            
            if ($stmt->execute()) {
                $_SESSION['user_name'] = $name;
                $_SESSION['user_email'] = $email;
                echo json_encode(['success' => true, 'message' => 'Profile updated successfully!']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Error updating profile']);
            }
            $stmt->close();
            break;
            
        case 'change_password':
            $current = $_POST['currentPassword'];
            $new_password = $_POST['newPassword'];
            $confirm = $_POST['confirmPassword'];
            
            if (empty($current) || empty($new_password) || empty($confirm)) {
                echo json_encode(['success' => false, 'message' => 'All fields are required']);
                exit;
            }
            
            if ($new_password !== $confirm) {
                echo json_encode(['success' => false, 'message' => 'New passwords do not match']);
                exit;
            }
            
            if (strlen($new_password) < 6) {
                echo json_encode(['success' => false, 'message' => 'Password must be at least 6 characters']);
                exit;
            }
            
            // Verify current password
            $stmt = $conn->prepare("SELECT password_hash FROM users WHERE user_id = ?");
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $user = $result->fetch_assoc();
            
            if (!password_verify($current, $user['password_hash'])) {
                echo json_encode(['success' => false, 'message' => 'Current password is incorrect']);
                exit;
            }
            
            // Update password
            $new_hash = password_hash($new_password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("UPDATE users SET password_hash = ? WHERE user_id = ?");
            $stmt->bind_param("si", $new_hash, $user_id);
            
            if ($stmt->execute()) {
                echo json_encode(['success' => true, 'message' => 'Password changed successfully!']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Error updating password']);
            }
            $stmt->close();
            break;
            
        case 'submit_support':
            $support_type = $_POST['supportType'];
            $message = trim($_POST['message']);
            
            if (empty($message)) {
                echo json_encode(['success' => false, 'message' => 'Message is required']);
                exit;
            }
            
            // Create support_requests table if it doesn't exist
            $conn->query("CREATE TABLE IF NOT EXISTS support_requests (
                id INT AUTO_INCREMENT PRIMARY KEY,
                user_id INT,
                support_type VARCHAR(50),
                message TEXT,
                status VARCHAR(20) DEFAULT 'pending',
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (user_id) REFERENCES users(user_id)
            )");
            
            // Insert support request
            $stmt = $conn->prepare("INSERT INTO support_requests (user_id, support_type, message) VALUES (?, ?, ?)");
            $stmt->bind_param("iss", $user_id, $support_type, $message);
            
            if ($stmt->execute()) {
                echo json_encode(['success' => true, 'message' => 'Support request submitted successfully!']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Error submitting request']);
            }
            $stmt->close();
            break;
            
        case 'save_notification_settings':
            $order_notifications = isset($_POST['orderNotifications']) ? 1 : 0;
            $promo_notifications = isset($_POST['promoNotifications']) ? 1 : 0;
            $email_notifications = isset($_POST['emailNotifications']) ? 1 : 0;
            
            // Update notification settings in users table
            $stmt = $conn->prepare("UPDATE users SET order_notifications = ?, promo_notifications = ?, email_notifications = ? WHERE user_id = ?");
            $stmt->bind_param("iiii", $order_notifications, $promo_notifications, $email_notifications, $user_id);
            
            if ($stmt->execute()) {
                echo json_encode(['success' => true, 'message' => 'Notification settings saved successfully!']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Error saving notification settings']);
            }
            $stmt->close();
            break;
            
        case 'save_language':
            $language = $_POST['language'];
            
            // Update language setting in users table
            $stmt = $conn->prepare("UPDATE users SET language = ? WHERE user_id = ?");
            $stmt->bind_param("si", $language, $user_id);
            
            if ($stmt->execute()) {
                echo json_encode(['success' => true, 'message' => 'Language changed successfully!']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Error changing language']);
            }
            $stmt->close();
            break;
            
        case 'get_user_settings':
            // Get all user settings from users table
            $stmt = $conn->prepare("SELECT * FROM users WHERE user_id = ?");
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows > 0) {
                $user = $result->fetch_assoc();
                $settings = [
                    'orderNotifications' => $user['order_notifications'] ?? 1,
                    'promoNotifications' => $user['promo_notifications'] ?? 0,
                    'emailNotifications' => $user['email_notifications'] ?? 1,
                    'language' => $user['language'] ?? 'en'
                ];
                echo json_encode(['success' => true, 'settings' => $settings]);
            } else {
                echo json_encode(['success' => false, 'message' => 'User not found']);
            }
            $stmt->close();
            break;
            
        default:
            echo json_encode(['success' => false, 'message' => 'Invalid action']);
    }
    exit;
}

// Get user settings for initial load - ALL FROM USERS TABLE NOW
$user_settings = [
    'orderNotifications' => $user_data['order_notifications'] ?? 1,
    'promoNotifications' => $user_data['promo_notifications'] ?? 0,
    'emailNotifications' => $user_data['email_notifications'] ?? 1,
    'language' => $user_data['language'] ?? 'en'
];

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <link href="https://fonts.googleapis.com/css2?family=Merriweather:wght@400;700&family=Merriweather+Sans:wght@700&display=swap" rel="stylesheet" />
  <title>Settings - A&F</title>
  <link
    rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"
  />
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      font-family: Merriweather;
      background-color: #4b00b3;
    }

    .container {
      background-color: #fbcdfb;
      border-radius: 10px;
      max-width: 1200px;
      margin: 20px auto;
      padding-bottom: 50px;
      height: calc(100vh - 40px);
    }

    .header {
      background-color: #bca5a5;
      display: flex;
      align-items: center;
      justify-content: space-between;
      padding: 25px 40px;
      border-bottom: 3px solid #555;
    }

    .header-left {
      display: flex;
      align-items: center;
    }

    .header h1 {
      font-size: 32px;
      font-family: serif;
      font-weight: bold;
    }

    .header span {
      margin-left: 15px;
      font-size: 20px;
      font-weight: bold;
      color: #111;
    }

    .back-btn {
      background: #4b00b3;
      color: white;
      border: none;
      padding: 10px 20px;
      border-radius: 5px;
      cursor: pointer;
      font-size: 16px;
      transition: background 0.3s ease;
    }

    .back-btn:hover {
      background: #6b16ac;
    }

    .section-label {
      color: #7a5b8b;
      font-weight: bold;
      font-size: 18px;
      margin: 40px 60px 10px;
    }

    .settings-grid {
      display: flex;
      justify-content: space-between;
      padding: 0 60px;
      flex-wrap: wrap;
    }

    .column {
      display: flex;
      flex-direction: column;
      gap: 20px;
      width: 48%;
    }

    .setting-box {
      background-color: white;
      border-radius: 4px;
      box-shadow: 2px 2px 4px #999;
      padding: 15px 20px;
      font-weight: bold;
      font-size: 18px;
      display: flex;
      align-items: center;
      gap: 15px;
      cursor: pointer;
      transition: background 0.2s ease;
    }

    .setting-box:hover {
      background-color: #f1f1f1;
    }

    .setting-box i {
      font-size: 22px;
    }

    /* Modal Styles */
    .modal {
      display: none;
      position: fixed;
      z-index: 1000;
      left: 0;
      top: 0;
      width: 100%;
      height: 100%;
      background-color: rgba(0,0,0,0.5);
    }

    .modal-content {
      background-color: #fefefe;
      margin: 5% auto;
      padding: 30px;
      border-radius: 10px;
      width: 90%;
      max-width: 500px;
      position: relative;
      box-shadow: 0 4px 20px rgba(0,0,0,0.3);
    }

    .close {
      color: #aaa;
      float: right;
      font-size: 28px;
      font-weight: bold;
      position: absolute;
      right: 20px;
      top: 15px;
      cursor: pointer;
    }

    .close:hover {
      color: #000;
    }

    .modal h2 {
      color: #4b00b3;
      margin-bottom: 20px;
      font-size: 24px;
    }

    .form-group {
      margin-bottom: 20px;
    }

    .form-group label {
      display: block;
      margin-bottom: 5px;
      font-weight: bold;
      color: #333;
    }

    .form-group input, .form-group select, .form-group textarea {
      width: 100%;
      padding: 10px;
      border: 1px solid #ddd;
      border-radius: 5px;
      font-size: 16px;
    }

    .btn {
      background: #4b00b3;
      color: white;
      border: none;
      padding: 12px 24px;
      border-radius: 5px;
      cursor: pointer;
      font-size: 16px;
      margin-right: 10px;
      transition: background 0.3s ease;
    }

    .btn:hover {
      background: #6b16ac;
    }

    .btn-secondary {
      background: #666;
    }

    .btn-secondary:hover {
      background: #888;
    }

    .alert {
      padding: 10px;
      margin: 10px 0;
      border-radius: 5px;
      display: none;
    }

    .alert-success {
      background-color: #d4edda;
      color: #155724;
      border: 1px solid #c3e6cb;
    }

    .alert-error {
      background-color: #f8d7da;
      color: #721c24;
      border: 1px solid #f5c6cb;
    }

    .toggle-switch {
      position: relative;
      display: inline-block;
      width: 60px;
      height: 34px;
      margin-left: 10px;
    }

    .toggle-switch input {
      opacity: 0;
      width: 0;
      height: 0;
    }

    .slider {
      position: absolute;
      cursor: pointer;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background-color: #ccc;
      transition: .4s;
      border-radius: 34px;
    }

    .slider:before {
      position: absolute;
      content: "";
      height: 26px;
      width: 26px;
      left: 4px;
      bottom: 4px;
      background-color: white;
      transition: .4s;
      border-radius: 50%;
    }

    input:checked + .slider {
      background-color: #4b00b3;
    }

    input:checked + .slider:before {
      transform: translateX(26px);
    }

    .setting-item {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 15px;
      padding: 10px 0;
      border-bottom: 1px solid #eee;
    }

    .setting-item:last-child {
      border-bottom: none;
    }

    .loading {
      opacity: 0.6;
      pointer-events: none;
    }

    .success-message {
      color: #28a745;
      font-weight: bold;
      margin-top: 10px;
    }

    .error-message {
      color: #dc3545;
      font-weight: bold;
      margin-top: 10px;
    }

    @media (max-width: 768px) {
      .settings-grid {
        flex-direction: column;
        padding: 0 30px;
      }

      .column {
        width: 100%;
      }

      .section-label {
        margin: 30px 30px 10px;
      }

      .header {
        flex-direction: column;
        align-items: flex-start;
      }

      .modal-content {
        margin: 10% auto;
        padding: 20px;
      }
    }
  </style>
</head>
<body>
  <div class="container">
    <div class="header">
      <div class="header-left">
        <h1>A&F</h1>
        <span>Settings - <?php echo htmlspecialchars($user_data['name']); ?></span>
      </div>
      <button class="back-btn" onclick="goBack()">
        <i class="fas fa-arrow-left"></i> Back
      </button>
    </div>

    <div class="settings-grid">
      <div class="column">
        <div class="section-label">My Account</div>
        <div class="setting-box" onclick="openModal('profileModal')">
          <i class="fas fa-user-circle"></i> Profile
        </div>
        <div class="setting-box" onclick="openModal('passwordModal')">
          <i class="fas fa-lock"></i> Change Password
        </div>
      </div>

      <div class="column">
        <div class="section-label">Other</div>
        <div class="setting-box" onclick="openModal('notificationModal')">
          <i class="fas fa-bell"></i> Notification
        </div>
        <div class="setting-box" onclick="openModal('languageModal')">
          <i class="fas fa-globe"></i> Language
        </div>
        <div class="setting-box" onclick="openModal('customerServiceModal')">
          <i class="fas fa-desktop"></i> Customer Service
        </div>
        <div class="setting-box" onclick="openModal('aboutModal')">
          <i class="fas fa-question-circle"></i> About Us
        </div>
      </div>
    </div>
  </div>

  <!-- Profile Modal -->
  <div id="profileModal" class="modal">
    <div class="modal-content">
      <span class="close" onclick="closeModal('profileModal')">&times;</span>
      <h2><i class="fas fa-user-circle"></i> Profile Settings</h2>
      <div id="profileAlert" class="alert"></div>
      <form id="profileForm">
        <div class="form-group">
          <label for="fullName">Full Name:</label>
          <input type="text" id="fullName" value="<?php echo htmlspecialchars($user_data['name']); ?>" required>
        </div>
        <div class="form-group">
          <label for="email">Email:</label>
          <input type="email" id="email" value="<?php echo htmlspecialchars($user_data['email']); ?>" required>
        </div>
        <div class="form-group">
          <label for="phone">Phone Number:</label>
          <input type="tel" id="phone" value="<?php echo htmlspecialchars($user_data['phone_number']); ?>">
        </div>
        <div class="form-group">
          <label for="address">Address:</label>
          <textarea id="address" rows="3"><?php echo htmlspecialchars($user_data['address']); ?></textarea>
        </div>
        <button type="submit" class="btn">Save Changes</button>
        <button type="button" class="btn btn-secondary" onclick="closeModal('profileModal')">Cancel</button>
      </form>
    </div>
  </div>

  <!-- Change Password Modal -->
  <div id="passwordModal" class="modal">
    <div class="modal-content">
      <span class="close" onclick="closeModal('passwordModal')">&times;</span>
      <h2><i class="fas fa-lock"></i> Change Password</h2>
      <div id="passwordAlert" class="alert"></div>
      <form id="passwordForm">
        <div class="form-group">
          <label for="currentPassword">Current Password:</label>
          <input type="password" id="currentPassword" required>
        </div>
        <div class="form-group">
          <label for="newPassword">New Password:</label>
          <input type="password" id="newPassword" required>
        </div>
        <div class="form-group">
          <label for="confirmPassword">Confirm Password:</label>
          <input type="password" id="confirmPassword" required>
        </div>
        <button type="submit" class="btn">Update Password</button>
        <button type="button" class="btn btn-secondary" onclick="closeModal('passwordModal')">Cancel</button>
      </form>
    </div>
  </div>

  <!-- Customer Service Modal -->
  <div id="customerServiceModal" class="modal">
    <div class="modal-content">
      <span class="close" onclick="closeModal('customerServiceModal')">&times;</span>
      <h2><i class="fas fa-desktop"></i> Customer Service</h2>
      <div id="supportAlert" class="alert"></div>
      <form id="supportForm">
        <div class="form-group">
          <label for="supportType">Support Type:</label>
          <select id="supportType" required>
            <option value="general">General Inquiry</option>
            <option value="order">Order Issue</option>
            <option value="payment">Payment Problem</option>
            <option value="technical">Technical Support</option>
          </select>
        </div>
        <div class="form-group">
          <label for="message">Message:</label>
          <textarea id="message" rows="4" placeholder="Describe your issue or question..." required></textarea>
        </div>
        <button type="submit" class="btn">Submit Request</button>
        <button type="button" class="btn btn-secondary" onclick="closeModal('customerServiceModal')">Cancel</button>
      </form>
    </div>
  </div>

  <!-- Enhanced Notification Modal -->
  <div id="notificationModal" class="modal">
    <div class="modal-content">
      <span class="close" onclick="closeModal('notificationModal')">&times;</span>
      <h2><i class="fas fa-bell"></i> Notification Settings</h2>
      <div id="notificationAlert" class="alert"></div>
      <form id="notificationForm">
        <div class="setting-item">
          <label>Order Updates:</label>
          <label class="toggle-switch">
            <input type="checkbox" id="orderNotifications" <?php echo $user_settings['orderNotifications'] ? 'checked' : ''; ?>>
            <span class="slider"></span>
          </label>
        </div>
        <small>Receive notifications about your order status</small>
        
        <div class="setting-item">
          <label>Promotional Offers:</label>
          <label class="toggle-switch">
            <input type="checkbox" id="promoNotifications" <?php echo $user_settings['promoNotifications'] ? 'checked' : ''; ?>>
            <span class="slider"></span>
          </label>
        </div>
        <small>Get notified about special offers and discounts</small>
        
        <div class="setting-item">
          <label>Email Notifications:</label>
          <label class="toggle-switch">
            <input type="checkbox" id="emailNotifications" <?php echo $user_settings['emailNotifications'] ? 'checked' : ''; ?>>
            <span class="slider"></span>
          </label>
        </div>
        <small>Receive notifications via email</small>
        
        <button type="submit" class="btn">Save Settings</button>
        <button type="button" class="btn btn-secondary" onclick="closeModal('notificationModal')">Cancel</button>
      </form>
    </div>
  </div>

  <!-- Enhanced Language Modal -->
  <div id="languageModal" class="modal">
    <div class="modal-content">
      <span class="close" onclick="closeModal('languageModal')">&times;</span>
      <h2><i class="fas fa-globe"></i> Language Settings</h2>
      <div id="languageAlert" class="alert"></div>
      <form id="languageForm">
        <div class="form-group">
          <label for="language">Select Language:</label>
          <select id="language" required>
            <option value="en" <?php echo $user_settings['language'] == 'en' ? 'selected' : ''; ?>>English</option>
            <option value="fil" <?php echo $user_settings['language'] == 'fil' ? 'selected' : ''; ?>>Filipino</option>
            <option value="es" <?php echo $user_settings['language'] == 'es' ? 'selected' : ''; ?>>Español</option>
            <option value="zh" <?php echo $user_settings['language'] == 'zh' ? 'selected' : ''; ?>>中文</option>
          </select>
        </div>
        <button type="submit" class="btn">Save Language</button>
        <button type="button" class="btn btn-secondary" onclick="closeModal('languageModal')">Cancel</button>
      </form>
    </div>
  </div>

  <!-- Static Modals (About) -->
  <div id="aboutModal" class="modal">
    <div class="modal-content">
      <span class="close" onclick="closeModal('aboutModal')">&times;</span>
      <h2><i class="fas fa-question-circle"></i> About A&F Chocolates</h2>
      <p><strong>Version:</strong> 1.0.0</p>
      <p><strong>Developer:</strong> A&F Development Team</p>
      <p><strong>Contact:</strong> A&FCHOCS@gmail.com</p>
      <p><strong>Address:</strong> W5R7+H8 Lipa, Batangas</p>
      <br>
      <p>A&F Chocolates is your premier destination for authentic chocolates, Korean snacks, and Filipino treats.</p>
      <button class="btn" onclick="closeModal('aboutModal')">Close</button>
    </div>
  </div>

  <script>
    // Modal Functions
    function openModal(modalId) {
      document.getElementById(modalId).style.display = 'block';
    }

    function closeModal(modalId) {
      document.getElementById(modalId).style.display = 'none';
      // Clear alerts when closing modals
      const alerts = document.querySelectorAll('.alert');
      alerts.forEach(alert => {
        alert.style.display = 'none';
        alert.className = 'alert';
      });
    }

    function showAlert(alertId, message, type) {
      const alert = document.getElementById(alertId);
      alert.textContent = message;
      alert.className = `alert alert-${type}`;
      alert.style.display = 'block';
    }

    // Close modal when clicking outside
    window.onclick = function(event) {
      if (event.target.classList.contains('modal')) {
        event.target.style.display = 'none';
      }
    }

    function goBack() {
      window.history.back();
    }

    // Profile Form Submission
    document.getElementById('profileForm').addEventListener('submit', function(e) {
      e.preventDefault();
      
      const formData = new FormData();
      formData.append('action', 'save_profile');
      formData.append('fullName', document.getElementById('fullName').value);
      formData.append('email', document.getElementById('email').value);
      formData.append('phone', document.getElementById('phone').value);
      formData.append('address', document.getElementById('address').value);

      fetch('Settings.php', {
        method: 'POST',
        body: formData
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          showAlert('profileAlert', data.message, 'success');
          setTimeout(() => {
            closeModal('profileModal');
          }, 1500);
        } else {
          showAlert('profileAlert', data.message, 'error');
        }
      })
      .catch(error => {
        showAlert('profileAlert', 'Network error occurred', 'error');
      });
    });

    // Password Form Submission
    document.getElementById('passwordForm').addEventListener('submit', function(e) {
      e.preventDefault();
      
      const formData = new FormData();
      formData.append('action', 'change_password');
      formData.append('currentPassword', document.getElementById('currentPassword').value);
      formData.append('newPassword', document.getElementById('newPassword').value);
      formData.append('confirmPassword', document.getElementById('confirmPassword').value);

      fetch('Settings.php', {
        method: 'POST',
        body: formData
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          showAlert('passwordAlert', data.message, 'success');
          document.getElementById('passwordForm').reset();
          setTimeout(() => {
            closeModal('passwordModal');
          }, 1500);
        } else {
          showAlert('passwordAlert', data.message, 'error');
        }
      })
      .catch(error => {
        showAlert('passwordAlert', 'Network error occurred', 'error');
      });
    });

    // Support Form Submission
    document.getElementById('supportForm').addEventListener('submit', function(e) {
      e.preventDefault();
      
      const formData = new FormData();
      formData.append('action', 'submit_support');
      formData.append('supportType', document.getElementById('supportType').value);
      formData.append('message', document.getElementById('message').value);

      fetch('Settings.php', {
        method: 'POST',
        body: formData
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          showAlert('supportAlert', data.message, 'success');
          document.getElementById('supportForm').reset();
          setTimeout(() => {
            closeModal('customerServiceModal');
          }, 1500);
        } else {
          showAlert('supportAlert', data.message, 'error');
        }
      })
      .catch(error => {
        showAlert('supportAlert', 'Network error occurred', 'error');
      });
    });

    // Notification Form Submission
    document.getElementById('notificationForm').addEventListener('submit', function(e) {
      e.preventDefault();
      
      const formData = new FormData();
      formData.append('action', 'save_notification_settings');
      formData.append('orderNotifications', document.getElementById('orderNotifications').checked ? '1' : '0');
      formData.append('promoNotifications', document.getElementById('promoNotifications').checked ? '1' : '0');
      formData.append('emailNotifications', document.getElementById('emailNotifications').checked ? '1' : '0');

      this.classList.add('loading');

      fetch('Settings.php', {
        method: 'POST',
        body: formData
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          showAlert('notificationAlert', data.message, 'success');
          setTimeout(() => {
            closeModal('notificationModal');
          }, 1500);
        } else {
          showAlert('notificationAlert', data.message, 'error');
        }
        this.classList.remove('loading');
      })
      .catch(error => {
        showAlert('notificationAlert', 'Network error occurred', 'error');
        this.classList.remove('loading');
      });
    });

    // Language Form Submission
    document.getElementById('languageForm').addEventListener('submit', function(e) {
      e.preventDefault();
      
      const formData = new FormData();
      formData.append('action', 'save_language');
      formData.append('language', document.getElementById('language').value);

      this.classList.add('loading');

      fetch('Settings.php', {
        method: 'POST',
        body: formData
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          showAlert('languageAlert', data.message, 'success');
          setTimeout(() => {
            closeModal('languageModal');
          }, 1500);
        } else {
          showAlert('languageAlert', data.message, 'error');
        }
        this.classList.remove('loading');
      })
      .catch(error => {
        showAlert('languageAlert', 'Network error occurred', 'error');
        this.classList.remove('loading');
      });
    });

    // Load user settings on page load
    document.addEventListener('DOMContentLoaded', function() {
      loadUserSettings();
    });

    function loadUserSettings() {
      const formData = new FormData();
      formData.append('action', 'get_user_settings');

      fetch('Settings.php', {
        method: 'POST',
        body: formData
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          const settings = data.settings;
        }
      })
      .catch(error => {
        console.error('Error loading settings:', error);
      });
    }
  </script>
</body>
</html>