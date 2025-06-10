<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Contact</title>
  <link rel="stylesheet" href="style.css" />
  <style>
    * {
      box-sizing: border-box;
    }

    html, body {
      background-image:url('images/bg.png');
      background-size: cover;
      background-attachment: fixed;
      background-position: center;
      margin: 0;
      padding: 0;
      overflow-x: hidden;  
       
      font-family: 'Merriweather', serif;
    }

    body {
      min-height: 100vh;
      display: flex;
      flex-direction: column;
      justify-content: center;
      align-items: center;
    }

    nav{
      position: relative;
      padding: 1rem 0;
    }

    .nav-links {
      list-style: none;
      display: flex;
      justify-content: center;
      align-items: center;
      gap: 2rem;
      margin: 0;
      padding: 0;
    }

    .nav-links a {
      color: white;
      text-decoration: none;
      font-weight: bold;
      font-size: 1rem;
      transition: opacity 0.3s ease;
    }

    .nav-links a:hover {
      opacity: 0.8;
    }

    .pic {
      margin: 0 1rem;
    }

    .logo {
      width: 80px;
      height: 80px;
      border-radius: 50%;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
    }

    .contact-section {
      display: flex;
      justify-content: center;
      align-items: center;
      gap: 3rem;
      padding: 1rem 2rem;
      flex-wrap: nowrap;
      max-width: 1200px;
      margin: auto;
      min-height: 80vh;
    }

    .contact-info {
      display: flex;
      flex-direction: column;
      gap: 1.5rem;
    }

    .social-item {
      display: flex;
      align-items: center;
      gap: 1.5rem;
      background-color: #7447ac;
      padding: 1rem 1.5rem;
      border-radius: 50px;
      box-shadow: 0 4px 10px rgba(0, 0, 0, 0.3);
      width: 18rem;
      font-size: 0.95rem;
      color:white;
      transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .social-item:hover {
      transform: translateY(-3px);
      box-shadow: 0 6px 15px rgba(0, 0, 0, 0.4);
    }

    .social-item img {
      width: 24px;
      height: 24px;
      filter: brightness(0) invert(1);
    }

    .map-container {
      border-radius: 12px;
      overflow: hidden;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
      width: 450px;
      height: 400px;
    }

    .map-container iframe {
      width: 100%;
      height: 100%;
      border: 0;
    }

    @media (max-width: 1024px) {
      .contact-section {
        gap: 2rem;
        padding: 1rem;
      }
      
      .nav-links {
        gap: 1.5rem;
      }
      
      .map-container {
        width: 400px;
        height: 350px;
      }
    }

    @media (max-width: 768px) {
      .nav-links {
        gap: 1rem;
      }
      
      .nav-links a {
        font-size: 0.9rem;
      }
      
      .logo {
        width: 60px;
        height: 60px;
      }
      
      .contact-section {
        flex-direction: column;
        align-items: center;
        gap: 1rem;
        height: calc(100vh - 100px);
      }
      
      .contact-info {
        gap: 1rem;
      }
      
      .social-item {
        width: 15rem;
        padding: 0.8rem 1.2rem;
        font-size: 0.85rem;
        color:white;
        font-weight: bold;
        gap: 1rem;
      }
      
      .social-item img {
        width: 20px;
        height: 20px;
      }
      
      .map-container {
        width: 300px;
        height: 250px;
      }
    }

    @media (max-width: 480px) {
      nav {
        padding: 0.5rem 0;
      }
      
      .nav-links a {
        font-size: 0.8rem;
      }
      
      .logo {
        width: 50px;
        height: 50px;
      }
      
      .contact-section {
        padding: 0.5rem;
        height: calc(100vh - 80px);
      }
      
      .social-item {
        width: 16rem;
        padding: 0.6rem 1rem;
        font-size: 0.8rem;
      }
      
      .social-item img {
        width: 18px;
        height: 18px;
      }
      
      .map-container {
        width: 250px;
        height: 200px;
      }
    }
  </style>
</head>
<body>
  <nav>
    <ul class="nav-links">
      <li><a href="Welcome.php">Home</a></li>
      <li><a href="Contact.html">Contact</a></li>
      <li class="pic">
        <img src='https://cdn.builder.io/api/v1/image/assets/TEMP/cbc700ac3a9cc70c2561f787dc7a724761a462ad' class="logo" alt="A&F Logo"/>
      </li>
      <li><a href="About.html">About</a></li>
      <li><a href="Welcome.php">Login/Sign up</a></li>
    </ul>
  </nav>

  <main class="contact-section">
    <div class="contact-info">
      <div class="social-item">
        <img src="images/image 16.png" alt="Facebook Icon" />
        <span>@A&FCHOCS_</span>
      </div>
      <div class="social-item">
        <img src="images/image 17.png" alt="Instagram Icon" />
        <span>@A&FCHOCS_</span>
      </div>
      <div class="social-item">
        <img src="images/image 18.png" alt="Gmail Icon" />
        <span>A&FCHOCS@gmail.com</span>
      </div>
      <div class="social-item">
        <img src="images/image 19.png" alt="WhatsApp Icon" />
        <span>+639123456789</span>
      </div>
      <div class="social-item">
        <img src="icons/location.png" alt="Location Icon" />
        <span>W5R7+H8 Lipa, Batangas</span>
      </div>
    </div>

    <div class="map-container">
      <iframe
        src="https://www.google.com/maps/embed?pb=!1m14!1m8!1m3!1d3361.626355820903!2d121.16504304543996!3d13.942551206428174!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x33bd6ca3bb0645f1%3A0xd40d3fca7ba2337d!2sA%26F%20Chocolates!5e0!3m2!1sen!2sph!4v1747930058636!5m2!1sen!2sph"
        allowfullscreen=""
        loading="lazy">
      </iframe>
    </div>
  </main>
</body>
</html>