<?php
require 'db_connect.php';
$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $name = trim($_POST["username"]);
  $email = trim($_POST["email"]);
  $password = password_hash($_POST["password"], PASSWORD_DEFAULT);
  $phone = trim($_POST["phone"]);
  $address = trim($_POST["address"]);

  $stmt = $conn->prepare("INSERT INTO users (name, email, password_hash, phone_number, address) VALUES (?, ?, ?, ?, ?)");
  $stmt->bind_param("sssss", $name, $email, $password, $phone, $address);

  if ($stmt->execute()) {
    $message = "Registration successful! <a href='Login.php'>Login here</a>.";
  } else {
    $message = "Error: " . $stmt->error;
  }
  $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Sign Up | A&F</title>
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
        overflow: hidden;
      }

      .signup-page {
        display: flex;
        flex-direction: column;
        min-height: screen;
        align-items: center;
        justify-content: center;
        min-height: 100vh;
        width: 100%;
        background: linear-gradient(180deg,#6b16ac 28.36%,#371b70 65.29%,#4e317a 80.43%,#8d7d7d 100%);
        box-sizing: border-box;
        overflow: hidden;
      }

      .brand-logo {
        position: absolute;
        top: -20px;
        left: 20px;
        color: #fff;
        font-family: Merriweather;
        font-size: 60px;
        font-weight: 700;
      }

      .form-container {
        width: 100%;
        max-width: 500px;
        padding: 20px;
        border-radius: 15px;
        background-color: rgba(217, 217, 217, 0.1);
        backdrop-filter: blur(10px);
        margin: 0 auto;
        
      }

      .form-content {
        width: 100%;
        padding: 0px;
        height: 580px;
      }

      .form-title {
        color: #fff;
        font-family: Poppins, sans-serif;
        font-size: 20px;
        font-weight: 700;
        text-align: center;
        margin-bottom: 15px;
      }

      .signup-form {
        display: flex;
        flex-direction: column;
        gap: 40px;
      }

      .form-group {
        position: relative;
      }

      .input-label {
        color: #fff;
        font-family: Poppins;
        font-size: 18px;
        font-weight: 700;
        margin-bottom: 8px;
        display: block;
      }

      .input-wrapper {
        position: relative;
      }

      .form-input {
        width: 465px;
        height: 60px;
        border-radius: 10px;
        border: 1px solid rgba(216, 204, 204, 0.61);
        background-color: rgba(216, 204, 204, 0.61);
        padding: 0 1rem;
        font-size: 14px;
        line-height: 40px;
        color: #000;
        margin: 0 auto;
      }

      .icon-wrapper {
        position: absolute;
        right: 24px;
        top: 50%;
        transform: translateY(-50%);
      }

      .signup-button {
        width: 100%;
        height: 40px;
        border-radius: 10px;
        background-color: #fff;
        color: #000;
        font-size: 14px;
        font-weight: 700;
        border: none;
        cursor: pointer;
      }

      .signin-link {
        color: #fff;
        font-family: Poppins;
        font-size: 14px;
        text-align: center;
        margin-top: 16px;
      }

      .signin-link a {
        color: #fff;
        text-decoration: underline;
      }

      .decorative-image {
        position: absolute;
        z-index: 1;
      }

      .image-1 {
        left: 0;
        top: 128px;
        width: 300px;
        transform: rotate(-11.691deg);
      }

      .image-2 {
        right: 0;
        top: 94px;
        width: 300px;
        transform: rotate(-8.397deg);
      }

      .image-3 {
        left: 63px;
        bottom: 0;
        width: 200px;
      }

      .image-4 {
        right: 100px;
        bottom: 0;
        width: 150px;
        transform: rotate(-18.198deg);
      }

      .image-5 {
        right: 100px;
        top: 66px;
        width: 100px;
        transform: rotate(33.9deg);
      }

      @media (max-width: 991px) {
        .brand-logo {
          font-size: 48px;
        }
        .form-container {
          width: 90%;
          padding: 32px;
        }
        .image-1, .image-2 { width: 200px; }
        .image-3 { width: 120px; }
        .image-4 { width: 100px; }
        .image-5 { width: 80px; }
      }

      @media (max-width: 991px) {
        .brand-logo {
          font-size: 48px;
        }
        .form-container {
          margin-top: 32px;
        }
        .form-title {
          font-size: 24px;
        }
        .image-1, .image-2, .image-3, .image-4, .image-5 {
          display: none;
        }
      }
    </style>
  </head>
  <body>
    <main class="signup-page">
      <h1 class="brand-logo">A&F</h1>
      <div class="form-container">
        <div class="form-content">
          <h2 class="form-title">Sign up</h2>
          <form class="signup-form" method="POST" action="">
            <div class="form-group">
              <label class="input-label" for="username">Username</label>
              <div class="input-wrapper">
                <input type="text" id="username" name="username" class="form-input" required />
              </div>
            </div>
            <div class="form-group">
              <label class="input-label" for="email">Email</label>
              <div class="input-wrapper">
                <input type="email" id="email" name="email" class="form-input" required />
              </div>
            </div>
            <div class="form-group">
              <label class="input-label" for="password">Password</label>
              <div class="input-wrapper">
                <input type="password" id="password" name="password" class="form-input" required />
              </div>
            </div>
            <div class="form-group">
              <label class="input-label" for="phone">Phone Number</label>
              <div class="input-wrapper">
                <input type="text" id="phone" name="phone" class="form-input" required />
              </div>
            </div>
            <div class="form-group">
              <label class="input-label" for="address">Address</label>
              <div class="input-wrapper">
                <input type="text" id="address" name="address" class="form-input" required />
              </div>
            </div>
            <button type="submit" class="signup-button">Sign up</button>
            <p class="signin-link">
              Already have an account? <a href="Login.php">Sign in</a>
            </p>
            <?php if ($message) echo "<p style='color:white;text-align:center;'>$message</p>"; ?>
          </form>
        </div>
      </div>
      <img src="https://cdn.builder.io/api/v1/image/assets/TEMP/1192fe4c4e19ec325cebdf6ab93326d7b756d1b2" alt="" class="decorative-image image-1" />
      <img src="https://cdn.builder.io/api/v1/image/assets/TEMP/a7a048e7493fbf1c6595beb3de45ac8c2fa42df3" alt="" class="decorative-image image-2" />
      <img src="https://cdn.builder.io/api/v1/image/assets/TEMP/63fbad6ef4e9aeac26ad69824aef8f4a040fb95b" alt="" class="decorative-image image-3" />
      <img src="https://cdn.builder.io/api/v1/image/assets/TEMP/63eccd7e09789e7777e24681264391d14aeea6f2" alt="" class="decorative-image image-4" />
      <img src="https://cdn.builder.io/api/v1/image/assets/TEMP/a5d04448581cd8ed933e22b0732f56e6c980a7b2" alt="" class="decorative-image image-5" />
    </main>
  </body>
</html>