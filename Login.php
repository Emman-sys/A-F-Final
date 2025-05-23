<?php
require 'db_connect.php';
session_start();

// Redirect if already logged in
if (isset($_SESSION["user_id"])) {
  header("Location: Userdashboard.php");
  exit();
}

$message = "";

// Handle login form submission
if (isset($_SERVER["REQUEST_METHOD"]) && $_SERVER["REQUEST_METHOD"] == "POST") {
  $email = trim($_POST["email"]);
  $password = $_POST["password"];

  // Prepare and execute query
  $stmt = $conn->prepare("SELECT user_id, password_hash FROM users WHERE email = ?");
  $stmt->bind_param("s", $email);
  $stmt->execute();
  $stmt->store_result();

  if ($stmt->num_rows > 0) {
    $stmt->bind_result($user_id, $stored_password);
    $stmt->fetch();
    
    // For plain text passwords use direct comparison
    if ($password === $stored_password) {
      $_SESSION["user_id"] = $user_id;
      $_SESSION["login_success"] = true;
      header("Location: Userdashboard.php");
      exit();
    } else {
      $message = "Invalid password.";
    }
  } else {
    $message = "User not found.";
  }
  $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <link href="https://fonts.googleapis.com/css2?family=Merriweather:wght@400;700&family=Merriweather+Sans:wght@700&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css">
    <link rel="stylesheet" href="styles.css">
    <style>
    html, body {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
      width: 100%;
      height: 100%;
    }

    .login-page {
      display: flex;
      flex-direction: column;
      min-height: 100vh;
      align-items: center;
      justify-content: center;
      width: 100%;
      background: linear-gradient(180deg,#6b16ac 28.36%,#371b70 65.29%,#4e317a 80.43%,#8d7d7d 100%);
      box-sizing: border-box;
      overflow: hidden;
    }

    .login-wrapper {
      padding: 20px;
    }

    .brand-logo {
      position: absolute;
      top: 20px;
      left: 20px;
      color: #fff;
      font-family: Merriweather;
      font-size: 60px;
      font-weight: 700;
    }

    .form-container {
      width: 100%;
      max-width: 500px;
      padding: 40px;
      border-radius: 15px;
      background-color: rgba(217, 217, 217, 0.1);
      backdrop-filter: blur(10px);
      margin: 0 auto;
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

    .input-wrapper {
      position: relative;
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

    .login-button:hover {
      background-color: #f0f0f0;
    }

    .signup-text,
    .back-to-welcome {
      text-align: center;
      font-family: Poppins, sans-serif;
      font-size: 14px;
      color: #fff;
      margin-top: 15px;
    }

    .signup-text a,
    .back-to-welcome a {
      color: #fff;
      text-decoration: underline;
    }

    .button-message {
      margin-top: 10px;
      text-align: center;
      font-family: Poppins, sans-serif;
      font-size: 14px;
      padding: 10px;
      border-radius: 5px;
      display: none;
    }

    .success-message {
      background-color: rgba(39, 174, 96, 0.7);
      color: white;
    }

    .error-message {
      background-color: rgba(192, 57, 43, 0.7);
      color: white;
    }
    </style>
    <script>
    function togglePassword() {
      var pwd = document.getElementById("password");
      var btn = event.target;
      if (pwd.type === "password") {
        pwd.type = "text";
        btn.textContent = "Hide";
      } else {
        pwd.type = "password";
        btn.textContent = "Show";
      }
    }

    function validateForm() {
      var email = document.getElementById("email").value;
      var password = document.getElementById("password").value;
      var buttonMessage = document.getElementById("button-message");
      
      if (!email || !password) {
        buttonMessage.textContent = "Please fill in all fields";
        buttonMessage.className = "button-message error-message";
        buttonMessage.style.display = "block";
        return false;
      }
      
      buttonMessage.textContent = "Logging in...";
      buttonMessage.className = "button-message";
      buttonMessage.style.display = "block";
      return true;
    }
    </script>
</head>
<body>
    <main class="login-page">
      <div class="login-wrapper">
        <header>
          <h1 class="brand-logo">A&amp;F</h1>
        </header>
        <div class="form-container">
          <div class="form-content">
            <h2 class="form-title">Login</h2>
            <?php if (!empty($message)): ?>
              <div style="color: #fff; background: #c0392b; padding: 10px; border-radius: 8px; margin-bottom: 16px; text-align: center;">
                <?php echo htmlspecialchars($message); ?>
              </div>
            <?php endif; ?>
            <form class="login-form" method="post" action="" onsubmit="return validateForm()">
              <div class="form-group">
                <label class="input-label">Email</label>
                <div class="input-wrapper">
                  <input type="email" class="form-input" id="email" name="email" required />
                </div>
              </div>
              <div class="form-group">
                <label class="input-label">Password</label>
                <div class="input-wrapper">
                  <input type="password" class="form-input" name="password" id="password" required />
                  <button type="button" onclick="togglePassword()" style="position:absolute;right:10px;top:50%;transform:translateY(-50%);background:none;border:none;color:#333;cursor:pointer;">Show</button>
                </div>
              </div>
              <button type="submit" class="login-button">Login</button>
              <div id="button-message" class="button-message"></div>
              <p class="signup-text">
                Don't have an account? <a href="Sign-Up.php">Sign up</a>
              </p>
              <p class="back-to-welcome">
                <a href="Welcome.php">Back to Welcome Page</a>
              </p>
            </form>
          </div>
        </div>
      </div>
    </main>
</body>
</html>