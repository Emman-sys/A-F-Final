<?php
session_start();
$isLoggedIn = isset($_SESSION["user_id"]);
?>
<!DOCTYPE html>
<html lang="en">
  <head>
   <meta charset="utf-8" />
   <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
   <title>A&F</title>
   <link href="https://fonts.googleapis.com/css2?family=Merriweather:wght@400;700&family=Merriweather+Sans:wght@700&display=swap" rel="stylesheet" />
   <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css">
   <link rel="stylesheet" href="styles.css">

<style>
  body{
    background-image:url("https://cdn.glitch.global/585aee42-d89c-4ece-870c-5b01fc1bab61/image%203.png?v=1747320934399");
    background-size: cover;
    background-position: center;
    background-attachment: fixed;  
    margin: 0;
    padding: 0;
    overflow-x: hidden;  
    width: 90%;     
  }
  
  .navigation {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 2rem 4rem;
    position: relative;
    z-index: 10;
  }
  
  .nav-links-left,
  .nav-links-right {
    display: flex;
    gap: 2rem;
    color: #FFF;
    font-size: 1.2rem;
    font-family: Merriweather;
  }
  
  .nav-link {
    color: #FFF;
    text-decoration: none;
    cursor: pointer;
    transition: opacity 0.3s ease;
  }
  
  .nav-link:hover {
    opacity: 0.8;
  }
  
  .nav-logo {
    position:absolute;
    left: 760px;
    width: 6rem;
    height: 6rem;
  }
  
  .mobile-menu-toggle {
    display: none;
    color: #ffffff;
    background: none;
    border: none;
    cursor: pointer;
  }
  
  .mobile-menu-toggle i {
    font-size: 1.5rem;
  }
  
  .brandname-section {
    display: flex;
    flex-direction: column;
    padding: 2rem 4rem;
    position: relative;
    min-height: calc(100vh - 200px);
  }
  
  .hero-content {
    max-width: 50rem;
    z-index: 5;
  }
  
  h2 {
    font-size: clamp(1.5rem, 4vw, 2.5rem);
    color: #ffffff;
    font-family: Merriweather;
    font-weight: 700;
    margin-bottom: 1rem;
    margin-top: 2rem;
  }
  
  h1{
    font-size: clamp(3rem, 10vw, 8rem);
    color: #ffffff;
    font-family: Merriweather;
    font-weight: 700;
    line-height: 1;
    margin-bottom: 2rem;
  }
  
  .description {
    font-size: clamp(1rem, 2vw, 1.25rem);
    color: #ffffff;
    font-family: 'Merriweather Sans';
    font-weight: 700;
    margin-bottom: 2.5rem;
    max-width: 50rem;
    line-height: 1.4;
  }
  
  .cta-button {
    position: absolute;
    left: 345px;
    background-color: #3D0D0D;
    color: #ffffff;
    font-family: 'Merriweather Sans';
    font-size: clamp(1.2rem, 3vw, 2.25rem);
    font-weight: 700;
    padding: 1rem 2.5rem;
    border-radius: 21px;
    border: 1px solid #8C4545;
    cursor: pointer;
    transition: all 0.3s ease;
    display: inline-block;
  }
  
  .cta-button:hover {
    background-color: #5D1D1D;
    transform: translateY(-2px);
  }
  
  .chocolatesplash {
    position: absolute;
    right: -10%;
    bottom: 0;
    max-width: 45vw;
    height: auto;
    z-index: 1;
    /* Add animation properties */
    opacity: 0;
    transform: translateX(100px) scale(0.8);
    animation: chocolateEntry 1.5s ease-out forwards;
  }

  /* Animation keyframes */
  @keyframes chocolateEntry {
    0% {
      opacity: 0;
      transform: translateX(100px) scale(0.8) rotate(10deg);
    }
    50% {
      opacity: 0.7;
      transform: translateX(20px) scale(0.95) rotate(-2deg);
    }
    100% {
      opacity: 1;
      transform: translateX(0) scale(1) rotate(0deg);
    }
  }

  /* Optional: Add a subtle floating effect after initial animation */
  .chocolatesplash:hover {
    animation: chocolateFloat 2s ease-in-out infinite;
  }

  @keyframes chocolateFloat {
    0%, 100% {
      transform: translateY(0px) rotate(0deg);
    }
    50% {
      transform: translateY(-10px) rotate(1deg);
    }
  }

  /* Tablet styles */
  @media (max-width: 1024px) {
    .navigation {
      padding: 2rem;
    }
    
    .brandname-section {
      padding: 2rem;
    }
    
    .chocolatesplash {
      max-width: 50vw;
      right: -5%;
    }
  }

  /* Mobile styles */
  @media (max-width: 768px) {
    .navigation {
      padding: 1.5rem;
    }

    .nav-links-left,
    .nav-links-right {
      display: none;
    }

    .mobile-menu-toggle {
      display: block;
    }

    .brandname-section {
      padding: 1.5rem;
      text-align: center;
    }
    
    .chocolatesplash {
      position: relative;
      max-width: 80vw;
      margin-top: 2rem;
      right: auto;
    }
    
    .cta-button {
      padding: 0.8rem 2rem;
    }
  }

  /* Small mobile styles */
  @media (max-width: 480px) {
    .navigation {
      padding: 1rem;
    }
    
    .brandname-section {
      padding: 1rem;
    }
    
    .nav-logo {
      width: 4rem;
      height: 4rem;
    }
  }

  .popup-overlay {
  display: none;
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

.success-message {
  background-color: rgba(39, 174, 96, 0.7);
  color: white;
  padding: 10px;
  border-radius: 5px;
  margin-bottom: 15px;
  text-align: center;
}
</style>
     
<body>
  <div class="page-wrapper">
    <nav class="navigation">
      <div class="nav-links-left">
        <a href="#" class="nav-link">AdminDash</a>
        <a href="Contact.php" class="nav-link">Contact</a>
      </div>
       
      <img src="https://cdn.builder.io/api/v1/image/assets/TEMP/cbc700ac3a9cc70c2561f787dc7a724761a462ad" alt="Logo" class="nav-logo" />

      <div class="nav-links-right">
        <a href="About.php" class="nav-link">About</a>
        <?php if ($isLoggedIn): ?>
          <a href="Userdashboard.php" class="nav-link">Dashboard</a>
        <?php else: ?>
          <a onclick="openPopup('loginPopup')" class="nav-link">Login/Sign up</a>
        <?php endif; ?>
      </div>
      
      <button class="mobile-menu-toggle" aria-label="Toggle mobile menu">
        <i class="ti ti-menu-2"></i>
      </button>
    </nav>

    <main class="brandname-section">
      <div class="letters-content">
        <h2>More Than Just Sweets!</h2>
        <h1>A&F CHOCOLATE</h1>
        <p class="description">
          Discover a world of flavor with A&F Chocolate! From affordable chocolates to your favorite Korean snacks and classic Filipino treats — all in one place.
        </p>
        
        <button class="cta-button">Order</button>
      </div>
      
      <img src="https://cdn.glitch.global/585aee42-d89c-4ece-870c-5b01fc1bab61/chocolatesplash?v=1747319066989" alt="Chocolate splash" class="chocolatesplash" />
    </main>
  </div>

  <div id="loginPopup" class="popup-overlay">
  <div class="popup-container">
    <button class="popup-close" onclick="closePopup('loginPopup')">&times;</button>
    <h2 class="form-title">Login</h2>
    <div id="loginMessage"></div>
    <form class="login-form" id="loginForm">
      <div class="form-group">
        <label class="input-label">Email</label>
        <input type="email" class="form-input" id="loginEmail" name="email" required />
      </div>
      <div class="form-group">
        <label class="input-label">Password</label>
        <div style="position: relative;">
          <input type="password" class="form-input" name="password" id="loginPassword" required />
          <button type="button" onclick="togglePassword('loginPassword')" style="position:absolute;right:10px;top:50%;transform:translateY(-50%);background:none;border:none;color:#333;cursor:pointer;">Show</button>
        </div>
      </div>
      <button type="submit" class="login-button">Login</button>
      <p class="signup-text">
        Don't have an account? <a onclick="switchToSignup()">Sign up</a>
      </p>
    </form>
  </div>
</div>

<div id="signupPopup" class="popup-overlay">
  <div class="popup-container">
    <button class="popup-close" onclick="closePopup('signupPopup')">&times;</button>
    <h2 class="form-title">Sign Up</h2>
    <div id="signupMessage"></div>
    <form class="login-form" id="signupForm" style="gap: 15px;">
      <div class="form-group">
        <label class="input-label">Username</label>
        <input type="text" class="form-input" name="username" required />
      </div>
      <div class="form-group">
        <label class="input-label">Email</label>
        <input type="email" class="form-input" name="email" required />
      </div>
      <div class="form-group">
        <label class="input-label">Password</label>
        <div style="position: relative;">
          <input type="password" class="form-input" name="password" id="signupPassword" required />
          <button type="button" onclick="togglePassword('signupPassword')" style="position:absolute;right:10px;top:50%;transform:translateY(-50%);background:none;border:none;color:#333;cursor:pointer;">Show</button>
        </div>
      </div>
      <div class="form-group">
        <label class="input-label">Phone Number</label>
        <input type="text" class="form-input" name="phone" required />
      </div>
      <div class="form-group">
        <label class="input-label">Address</label>
        <input type="text" class="form-input" name="address" required />
      </div>
      <button type="submit" class="login-button">Sign Up</button>
      <p class="signup-text">
        Already have an account? <a onclick="switchToLogin()">Sign in</a>
      </p>
    </form>
  </div>
</div>

<script>
function openPopup(popupId) {
  document.getElementById(popupId).style.display = 'block';
}

function closePopup(popupId) {
  document.getElementById(popupId).style.display = 'none';
  document.getElementById('loginMessage').innerHTML = '';
  document.getElementById('signupMessage').innerHTML = '';
}

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

function switchToLogin() {
  closePopup('signupPopup');
  openPopup('loginPopup');
}

function switchToSignup() {
  closePopup('loginPopup');
  openPopup('signupPopup');
}

// Login form submission
document.getElementById('loginForm').addEventListener('submit', function(e) {
  e.preventDefault();
  
  const formData = new FormData(this);
  const submitButton = this.querySelector('button[type="submit"]');
  const originalText = submitButton.textContent;
  
  // Clear previous messages
  document.getElementById('loginMessage').innerHTML = '';
  
  submitButton.textContent = 'Signing In...';
  submitButton.disabled = true;
  
  fetch('login_popup.php', {  // Make sure this points to your working login file
    method: 'POST',
    body: formData
  })
  .then(response => {
    console.log('Response status:', response.status);
    
    if (!response.ok) {
      throw new Error(`HTTP error! status: ${response.status}`);
    }
    
    return response.text();
  })
  .then(text => {
    console.log('Raw response:', text);
    
    // Check if response is empty
    if (!text.trim()) {
      throw new Error('Empty response from server');
    }
    
    // Find the JSON part (in case there's extra content)
    const jsonStart = text.indexOf('{');
    const jsonEnd = text.lastIndexOf('}') + 1;
    
    if (jsonStart === -1) {
      throw new Error('No JSON found in response');
    }
    
    const jsonText = text.substring(jsonStart, jsonEnd);
    
    try {
      const data = JSON.parse(jsonText);
      
      if (data.success) {
        document.getElementById('loginMessage').innerHTML = '<div class="success-message">' + data.message + '</div>';
        setTimeout(() => {
          window.location.href = data.redirect;
        }, 1000);
      } else {
        document.getElementById('loginMessage').innerHTML = '<div class="error-message">' + data.message + '</div>';
      }
    } catch (parseError) {
      console.error('JSON parse error:', parseError);
      document.getElementById('loginMessage').innerHTML = '<div class="error-message">Server response error</div>';
    }
    
    submitButton.textContent = originalText;
    submitButton.disabled = false;
  })
  .catch(error => {
    console.error('Fetch error:', error);
    document.getElementById('loginMessage').innerHTML = '<div class="error-message">Connection error: ' + error.message + '</div>';
    submitButton.textContent = originalText;
    submitButton.disabled = false;
  });
});

// Signup form submission
document.getElementById('signupForm').addEventListener('submit', function(e) {
  e.preventDefault();
  
  const formData = new FormData(this);
  const submitButton = this.querySelector('button[type="submit"]');
  const originalText = submitButton.textContent;
  
  // Clear previous messages
  document.getElementById('signupMessage').innerHTML = '';
  
  submitButton.textContent = 'Creating Account...';
  submitButton.disabled = true;
  
  fetch('signup_popup.php', {
    method: 'POST',
    body: formData
  })
  .then(response => {
    console.log('Signup response status:', response.status);
    
    if (!response.ok) {
      throw new Error(`HTTP error! status: ${response.status}`);
    }
    
    return response.text();
  })
  .then(text => {
    console.log('Signup raw response:', text);
    
    // Check if response is empty
    if (!text.trim()) {
      throw new Error('Empty response from server');
    }
    
    // Find the JSON part (in case there's extra content)
    const jsonStart = text.indexOf('{');
    const jsonEnd = text.lastIndexOf('}') + 1;
    
    if (jsonStart === -1) {
      throw new Error('No JSON found in response');
    }
    
    const jsonText = text.substring(jsonStart, jsonEnd);
    
    try {
      const data = JSON.parse(jsonText);
      
      if (data.success) {
        document.getElementById('signupMessage').innerHTML = '<div class="success-message">' + data.message + '</div>';
        setTimeout(() => {
          closePopup('signupPopup');
          openPopup('loginPopup');
        }, 1500);
      } else {
        document.getElementById('signupMessage').innerHTML = '<div class="error-message">' + data.message + '</div>';
      }
    } catch (parseError) {
      console.error('JSON parse error:', parseError);
      document.getElementById('signupMessage').innerHTML = '<div class="error-message">Server response error</div>';
    }
    
    submitButton.textContent = originalText;
    submitButton.disabled = false;
  })
  .catch(error => {
    console.error('Signup error:', error);
    document.getElementById('signupMessage').innerHTML = '<div class="error-message">Network error: ' + error.message + '</div>';
    submitButton.textContent = originalText;
    submitButton.disabled = false;
  });
});

// Close popup when clicking outside
window.onclick = function(event) {
  var loginPopup = document.getElementById('loginPopup');
  var signupPopup = document.getElementById('signupPopup');
  
  if (event.target == loginPopup) {
    closePopup('loginPopup');
  }
  if (event.target == signupPopup) {
    closePopup('signupPopup');
  }
}
</script>
</body>
</html>