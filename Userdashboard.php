<?php
session_start();
if (!isset($_SESSION["user_id"])) {
  header("Location: Login.php");
  exit();
}

// Fetch user's name from database
require 'db_connect.php';
$username = "User"; // Default fallback

if ($conn) {
  $stmt = $conn->prepare("SELECT name FROM users WHERE user_id = ?");
  if ($stmt) {
    $stmt->bind_param("i", $_SESSION["user_id"]);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
      $user = $result->fetch_assoc();
      $username = $user['name'];
    }
    $stmt->close();
  }
}
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>User Dashboard | A&F</title>
    <link href="https://fonts.googleapis.com/css2?family=Merriweather:wght@400;700&family=Merriweather+Sans:wght@700&display=swap" rel="stylesheet" />
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
      body {
        font-family: 'Poppins', 'Merriweather', sans-serif;
      }
      .dashboard-page {
        min-height: 100vh;
        width: 100vw;
        background: linear-gradient(180deg,#6b16ac 28.36%,#371b70 65.29%,#4e317a 80.43%,#8d7d7d 100%);
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        position: relative;
        overflow: hidden;
      }
      .brand-logo {
        position: absolute;
        top: 32px;
        left: 40px;
        color: #fff;
        font-family: Merriweather;
        font-size: 48px;
        font-weight: 700;
        letter-spacing: 2px;
        z-index: 2;
        text-decoration: none; /* Remove underline */
      }
      
      .brand-logo:hover {
        text-decoration: none; /* Ensure no underline on hover */
        opacity: 0.8; /* Optional: slight fade effect on hover */
      }
      .dashboard-container {
        background: rgba(217, 217, 217, 0.12);
        backdrop-filter: blur(10px);
        border-radius: 18px;
        padding: 48px 64px 32px 64px;
        box-shadow: 0 8px 32px rgba(0,0,0,0.18);
        display: flex;
        flex-direction: column;
        align-items: center;
        min-width: 900px;   /* Wider min-width */
        max-width: 1000px;  /* Wider max-width */
        width: 90vw;        /* Responsive width */
        z-index: 2;
      }
      .dashboard-title {
        color: #fff;
        font-size: 32px;
        font-weight: 700;
        margin-bottom: 32px;
        letter-spacing: 1px;
        text-align: center;
      }
      .dashboard-buttons {
        display: flex;
        flex-direction: column;
        gap: 24px;
        width: 100%;
        max-width: 800px; 
        margin: 0 auto;
        align-items: center; 
      }
      .dashboard-btn {
        width: 100%;        
        max-width: 800px;   
        min-width: 140px;
        padding: 16px 0;     
        border-radius: 10px;
        background: #fff;
        color: #371b70;
        font-size: 18px;
        font-weight: 700;
        border: none;
        cursor: pointer;
        transition: background 0.2s, color 0.2s;
        box-shadow: 0 2px 8px rgba(0,0,0,0.07);
        letter-spacing: 0.5px;
        display: block;
      }
      .dashboard-btn:hover {
        background: #6b16ac;
        color: #fff;
      }
      .logout-btn {
        background: #e74c3c;
        color: #fff;
        margin-left: 0; 
      }
      .logout-btn:hover {
        background: #c0392b;
      }

      .decorative-image {
        position: absolute;
        z-index: 1;
        opacity: 0.4; /* Reduced opacity for better visibility of content */
        pointer-events: none; /* Prevent images from interfering with clicks */
      }
      
      /* Better distributed positioning for all screen sizes */
      .image-1 { 
        left: 5%; 
        top: 10%; 
        width: 280px; 
        transform: rotate(-15deg);
      }
      
      .image-2 { 
        right: 5%; 
        top: 15%; 
        width: 320px; 
        transform: rotate(12deg);
      }
      
      .image-3 { 
        left: 8%; 
        bottom: 15%; 
        width: 300px;
        transform: rotate(8deg);
      }
      
      .image-4 { 
        right: 8%; 
        bottom: 10%; 
        width: 250px; 
        transform: rotate(-20deg);
      }
      
      .image-5 { 
        left: 50%; 
        top: 5%; 
        width: 200px; 
        transform: translateX(-50%) rotate(25deg);
      }

      /* Add new image positioning for better coverage */
      .image-6 { 
        left: 15%; 
        top: 60%; 
        width: 180px; 
        transform: rotate(-45deg);
        opacity: 0.3;
      }
      
      .image-7 { 
        right: 15%; 
        top: 45%; 
        width: 200px; 
        transform: rotate(35deg);
        opacity: 0.3;
      }

      @media (max-width: 1000px) {
        .dashboard-container {
          min-width: 90vw;
          padding: 32px 8px 24px 8px;
        }
        .dashboard-buttons {
          max-width: 100%;
        }
        
        /* Adjust image sizes for tablets */
        .image-1, .image-2 { width: 200px; }
        .image-3, .image-4 { width: 180px; }
        .image-5 { width: 150px; }
        .image-6, .image-7 { width: 120px; }
      }
      
      @media (max-width: 600px) {
        .brand-logo { font-size: 32px; left: 16px; top: 12px;}
        .dashboard-container { min-width: unset; width: 98vw; }
        .dashboard-title { font-size: 22px; }
        .dashboard-btn { font-size: 15px; padding: 14px 0;}
        
        /* Keep smaller images on mobile for subtle background effect */
        .image-1, .image-2, .image-3, .image-4, .image-5 { 
          width: 100px;
          opacity: 0.2;
        }
        .image-6, .image-7 { 
          width: 80px;
          opacity: 0.15;
        }
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
    </style>
  </head>
  <body>
    <main class="dashboard-page">
      <a href="Welcome.php" class="brand-logo">A&F</a>
      <div class="dashboard-container">
        <div class="dashboard-title">Welcome, <?php echo htmlspecialchars($username); ?>!</div>
        <div class="dashboard-buttons">
          <button class="dashboard-btn" onclick="location.href='MainPage.php'">Proceed to Shopping Center</button>
          <button class="dashboard-btn" onclick="location.href='Settings.php'">Settings</button>
          <button class="dashboard-btn logout-btn" onclick="location.href='logout.php'">Log Out</button>
        </div>
      </div>

      <!-- Existing decorative images with better positioning -->
      <img src="https://cdn.builder.io/api/v1/image/assets/TEMP/1192fe4c4e19ec325cebdf6ab93326d7b756d1b2" alt="" class="decorative-image image-1" />
      <img src="https://cdn.builder.io/api/v1/image/assets/TEMP/a7a048e7493fbf1c6595beb3de45ac8c2fa42df3" alt="" class="decorative-image image-2" />
      <img src="https://cdn.builder.io/api/v1/image/assets/TEMP/63fbad6ef4e9aeac26ad69824aef8f4a040fb95b" alt="" class="decorative-image image-3" />
      <img src="https://cdn.builder.io/api/v1/image/assets/TEMP/63eccd7e09789e7777e24681264391d14aeea6f2" alt="" class="decorative-image image-4" />
      <img src="https://cdn.builder.io/api/v1/image/assets/TEMP/a5d04448581cd8ed933e22b0732f56e6c980a7b2" alt="" class="decorative-image image-5" />
      
      <!-- Additional decorative images for better coverage -->
      <img src="https://cdn.builder.io/api/v1/image/assets/TEMP/1192fe4c4e19ec325cebdf6ab93326d7b756d1b2" alt="" class="decorative-image image-6" />
      <img src="https://cdn.builder.io/api/v1/image/assets/TEMP/a7a048e7493fbf1c6595beb3de45ac8c2fa42df3" alt="" class="decorative-image image-7" />

    </main>
  </body>
</html>