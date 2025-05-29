<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>A&F Chocolates</title>
  <link rel="stylesheet" href="style.css" />
  <style>
    * {
      box-sizing: border-box;
    }

    body {
      font-family: 'Merriweather', serif;
      margin: 0;
      padding: 0;
      background: linear-gradient(to bottom, #5127A3, #693375, #813F47);
      background-size: cover;
      background-position: center;
      color: white;
      min-height: 100vh;
    }

    nav {
      position: relative;
      padding: 2rem 0;
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
      font-size: 1.1rem;
      transition: opacity 0.3s ease;
    }

    .nav-links a:hover {
      opacity: 0.8;
    }

    .pic {
      margin: 0 1rem;
    }

    .logo {
      width: 108.2px;
      height: 108.2px;
      border-radius: 50%;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
    }

    .contact-section {
      display: flex;
      justify-content: center;
      align-items: flex-start;
      gap: 5rem;
      padding: 3rem 2rem;
      flex-wrap: wrap;
      max-width: 1200px;
      margin: 0 auto;
    }

    .contact-info {
      display: flex;
      flex-direction: column;
      gap: 2.8rem;
    }

    .social-item {
      display: flex;
      align-items: center;
      gap: 2rem;
      background-color: #4a148c;
      padding: 1.5rem 2rem;
      border-radius: 50px;
      box-shadow: 0 4px 10px rgba(0, 0, 0, 0.3);
      width: 16rem;
      font-size: 1.1rem;
      transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .social-item:hover {
      transform: translateY(-3px);
      box-shadow: 0 6px 15px rgba(0, 0, 0, 0.4);
    }

    .social-item img {
      width: 28px;
      height: 28px;
      filter: brightness(0) invert(1);
    }

    .map-container {
      border-radius: 12px;
      overflow: hidden;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
      width: 500px;
    }

    .map-container iframe {
      width: 100%;
      height: 550px;
      border: 0;
    }

    /* Responsive Design */
    @media (max-width: 1024px) {
      .contact-section {
        gap: 3rem;
        padding: 2rem 1rem;
      }
      
      .nav-links {
        flex-wrap: wrap;
        gap: 1.5rem;
      }
      
      .logo {
        width: 80px;
        height: 80px;
      }
    }

    @media (max-width: 768px) {
      .nav-links {
        flex-direction: column;
        gap: 1rem;
      }
      
      .nav-links li:nth-child(3) {
        order: -1;
      }
      
      .contact-section {
        flex-direction: column;
        align-items: center;
        gap: 2rem;
      }
      
      .social-item {
        width: 100%;
        max-width: 280px;
      }
      
      .map-container {
        max-width: 100%;
      }
      
      .map-container iframe {
        height: 300px;
      }
    }

    @media (max-width: 480px) {
      nav {
        padding: 1rem 0;
      }
      
      .nav-links a {
        font-size: 1rem;
      }
      
      .logo {
        width: 60px;
        height: 60px;
      }
      
      .contact-section {
        padding: 1.5rem 1rem;
      }
      
      .social-item {
        padding: 1rem 1.5rem;
        font-size: 1rem;
        gap: 1.5rem;
      }
      
      .social-item img {
        width: 24px;
        height: 24px;
      }
    }
  </style>
</head>
<body>
  <nav>
    <ul class="nav-links">
      <li><a href="Welcome.php">Home</a></li>
      <li><a href="Contact.php">Contact</a></li>
      <li class="pic">
        <img src='image/logoz.png' class="logo" alt="A&F Logo"/>
      </li>
      <li><a href="About.php">About</a></li>
      <li><a href="login_popup.php">Login/Sign up</a></li>
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
        <img src="icons/location.image" alt="Location Icon" />
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
