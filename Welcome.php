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
    min-height: 100vh;
    width: 100%;      
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
</style>
     
<body>
  <div class="page-wrapper">
    <nav class="navigation">
      <div class="nav-links-left">
        <a href="#" class="nav-link">Home</a>
        <a href="#" class="nav-link">Contact</a>
      </div>
       
      <img src="https://cdn.builder.io/api/v1/image/assets/TEMP/cbc700ac3a9cc70c2561f787dc7a724761a462ad" alt="Logo" class="nav-logo" />

      <div class="nav-links-right">
        <a href="About.html" class="nav-link">About</a>
        <a href="Login.php" class="nav-link">Login/Sign up</a>
      </div>
      
      <button class="mobile-menu-toggle" aria-label="Toggle mobile menu">
        <i class="ti ti-menu-2"></i>
      </button>
    </nav>

    <main class="brandname-section">
      <div class="letters-content">
        <h2>More Than Just Sweets!</h2>
        <h1>A&FCHOCOLATE</h1>
        <p class="description">
          Discover a world of flavor with A&F Chocolate! From affordable chocolates to your favorite Korean snacks and classic Filipino treats — all in one place.
        </p>
        
        <button class="cta-button">Order</button>
      </div>
      
      <img src="https://cdn.glitch.global/585aee42-d89c-4ece-870c-5b01fc1bab61/chocolatesplash?v=1747319066989" alt="Chocolate splash" class="chocolatesplash" />
    </main>
  </div>
</body>   
</html>