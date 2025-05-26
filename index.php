<!DOCTYPE html>
<html lang="en">
  <head>
   <meta charset="utf-8" />
   <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
   <title>A&F</title>
   <link href="https://fonts.googleapis.com/css2?family=Merriweather:wght@400;700&family=Merriweather+Sans:wght@700&display=swap" rel="stylesheet" />
   <link rel="stylesheet" href="styles.css">

  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      background-image: url("https://cdn.glitch.global/585aee42-d89c-4ece-870c-5b01fc1bab61/image%203.png?v=1747320934399");
      background-position: center;
      background-attachment: fixed;
      background-size: cover;
      font-family: 'Merriweather Sans', 'Roboto';
      min-height: 100vh;
      overflow-x: hidden; 
    }

    .main-section {
      min-height: 100vh;
      position: relative;
      display: flex;
      flex-direction: column;
      padding: min(20px, 2vw); /* Responsive padding */
    }

    .header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 20px 0;
      position: relative;
      z-index: 10;
      flex-wrap: wrap; /* Allow wrapping on small screens */
      gap: 10px;
    }

    .brand-name {
      font-size: clamp(2rem, 5vw, 3rem); /* Responsive font size */
      font-weight: bold;
      color: white;
    }

    .nav-items {
      position: absolute;
      left:125px;
      display: flex;
      gap: clamp(15px, 3vw, 30px); /* Responsive gap */
      color: white;
      font-size: clamp(0.8rem, 2vw, 1rem); /* Responsive font size */
    }

    .search-bar {
      position:absolute;
      left:50px;
      top: -78px;
      width: 100%;
      max-width: min(600px, 90vw); /* Responsive max-width */
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

    .notifications {
      width: clamp(35px, 6vw, 49px); /* Responsive size */
      height: clamp(35px, 6vw, 49px);
    }

    .hero-content {
      flex: 1;
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: clamp(20px, 4vw, 40px); /* Responsive gap */
      align-items: center;
      padding: clamp(20px, 4vw, 40px) 0;
    }

    .hero-left {
      position: relative;
    }

    .shop-now {
      font-size: clamp(2rem, 6vw, 3rem); /* Responsive font size */
      font-weight: bold;
      color: white;
      margin-bottom: 20px;
      
    }

    .hero-rectangles {
      position: relative;
      margin-top: clamp(20px, 4vw, 40px);
    }

    .rectangle1 {
      position: absolute;
      left: 85px;
      top: -87px;
      width: 100%;
      max-width: min(500px, 90vw); /* Responsive max-width */
      height: clamp(200px, 25vw, 250px); /* Responsive height */
      background: linear-gradient(to bottom, #ECC7ED, #C647CC);
      border-radius: 20px;
      position: relative;
    }

    .phone-image {
      position: absolute;
      top: 50%;
      left: 50%;
      transform: translate(-50%, -50%);
      width: 80%;
      max-width: min(400px, 80vw); /* Responsive max-width */
      height: auto;
    }

    .hero-right {
      display: flex;
      flex-direction: column;
      gap: 20px;
    }

    .rectangle2 {
      width: 100%;
      height: clamp(100px, 12vw, 120px); /* Responsive height */
      background: linear-gradient(to right, #FFFFFF, #CDACB1, #59D759);
      border-radius: 20px;
    }

    .rectangle3 {
      width: 100%;
      height: clamp(100px, 12vw, 120px); /* Responsive height */
      background-color: #EEA7BD;
      border-radius: 20px;
    }

    .categories-section {
      background: linear-gradient(to top, #592249, #BF489C);
      padding: clamp(40px, 8vw, 60px) clamp(10px, 2vw, 20px); /* Responsive padding */
      border-radius: 27px 27px 0 0;
      margin-top: clamp(20px, 4vw, 40px);
    }

    .categories-title {
      text-align: center;
      font-size: clamp(1.5rem, 4vw, 2rem); /* Responsive font size */
      font-weight: bold;
      color: white;
      margin-bottom: clamp(30px, 6vw, 40px);
    }

    .categories-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(min(200px, 40vw), 1fr)); /* Responsive columns */
      gap: clamp(20px, 4vw, 30px); /* Responsive gap */
      max-width: min(1200px, 95vw); /* Responsive max-width */
      margin: 0 auto;
    }

    .category-item {
      position: relative;
      height: clamp(220px, 28vw, 280px); /* Responsive height */
      border-radius: 8px;
      overflow: hidden;
      cursor: pointer;
      transition: transform 0.3s ease;
    }

    .category-item:hover {
      transform: scale(1.05);
    }

    .category-chocolate {
      background: linear-gradient(to bottom, #55361A, #CDACB1);
    }

    .category-noodles {
      background: linear-gradient(to bottom, #BFB886, #F3E794);
    }

    .category-chips {
      background: linear-gradient(to bottom, #D97272, #F8DDDD);
    }

    .category-melona {
      background: linear-gradient(to bottom, #71EEEC, #C1ACAC);
    }

    .category-chocnut {
      background: linear-gradient(to bottom, #6B6060, #7C6F6F, #8D7D7D);
    }

    .category-image {
      width: 80%;
      height: 80%;
      object-fit: contain;
      position: absolute;
      top: 50%;
      left: 50%;
      transform: translate(-50%, -50%);
    }

    .products-section {
      background: linear-gradient(to bottom, #5127A3, #986C93, #E0B083);
      min-height: 100vh;
      padding: clamp(40px, 8vw, 60px) clamp(10px, 2vw, 20px); /* Responsive padding */
    }

    .products-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: clamp(30px, 6vw, 40px);
      flex-wrap: wrap;
      gap: 20px;
    }

    .products-title {
      display: flex;
      align-items: center;
      gap: clamp(15px, 3vw, 20px); /* Responsive gap */
    }

    .brand-name-small {
      font-size: clamp(2rem, 5vw, 2.5rem); /* Responsive font size */
      font-weight: bold;
      color: white;
    }

    .section-title {
      font-size: clamp(1.5rem, 4vw, 2rem); /* Responsive font size */
      font-weight: bold;
      color: white;
    }

    .search-products {
      width: 100%;
      max-width: min(800px, 95vw); /* Responsive max-width */
      height: clamp(50px, 8vw, 60px); /* Responsive height */
      background: #D8D0D9;
      border-radius: 10px;
      margin: 20px 0;
    }

    .sort-controls {
      display: flex;
      align-items: center;
      gap: clamp(15px, 3vw, 20px); /* Responsive gap */
      flex-wrap: wrap;
      justify-content: center;
    }

    .sort-label {
      font-size: clamp(1.2rem, 3vw, 1.5rem); /* Responsive font size */
      color: white;
    }

    .sort-buttons {
      display: flex;
      gap: 10px;
      flex-wrap: wrap;
    }

    .sort-btn {
      width: clamp(100px, 15vw, 120px); /* Responsive width */
      height: clamp(40px, 6vw, 50px); /* Responsive height */
      background: #FFFFFF;
      border: none;
      border-radius: 5px;
      cursor: pointer;
      font-size: clamp(0.8rem, 2vw, 1rem); /* Responsive font size */
    }

    .products-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(min(180px, 40vw), 1fr)); /* Responsive columns */
      gap: clamp(15px, 3vw, 20px); /* Responsive gap */
      max-width: min(1400px, 95vw); /* Responsive max-width */
      margin: 0 auto;
    }

    .product-card {
      background: #EEEFE8;
      border-radius: 8px;
      height: clamp(200px, 25vw, 250px); /* Responsive height */
      position: relative;
      overflow: hidden;
      cursor: pointer;
      transition: transform 0.3s ease;
    }

    .product-card:hover {
      transform: translateY(-5px);
    }

    .product-image-container {
      width: 90%;
      height: 70%;
      background: #FFFFFF;
      border-radius: 20px;
      position: absolute;
      top: 10px;
      left: 50%;
      transform: translateX(-50%);
    }

    /* Responsive breakpoints */
    @media (max-width: 1200px) {
      .hero-content {
        grid-template-columns: 1fr;
        text-align: center;
      }
      
      .categories-grid {
        grid-template-columns: repeat(auto-fit, minmax(min(150px, 45vw), 1fr));
      }
    }

    @media (max-width: 768px) {
      .main-section {
        padding: 10px;
      }
      
      .header {
        flex-direction: column;
        gap: 15px;
      }
      
      .nav-items {
        order: 1;
      }
      
      .notifications {
        order: 2;
      }
      
      .products-header {
        flex-direction: column;
        align-items: stretch;
        text-align: center;
      }
      
      .sort-controls {
        justify-content: center;
      }
    }

    @media (max-width: 480px) {
      .categories-grid {
        grid-template-columns: repeat(2, 1fr);
      }
      
      .products-grid {
        grid-template-columns: repeat(2, 1fr);
      }
      
      .sort-buttons {
        justify-content: center;
      }
    }
  </style>
  </head>
  <body>
    <section class="main-section">
      <header class="header">
        <h2 class="brand-name">A&F</h2>
        <div class="nav-items">
          <span>New Product</span>
          <span>Announcements</span>
        </div>
        <img src="https://cdn.glitch.global/585aee42-d89c-4ece-870c-5b01fc1bab61/bell%20(1).png?v=1747633687046" class="notifications" alt="Notifications">
      </header>

      <div class="search-bar">
        <img src="https://cdn.glitch.global/585aee42-d89c-4ece-870c-5b01fc1bab61/search.png?v=1747633330905" class="search-icon" alt="Search">
      </div>

      <div class="hero-content">
        <div class="hero-left">
          <h1 class="shop-now">SHOP NOW!</h1>
          <div class="hero-rectangles">
            <div class="rectangle1">
              <img src="https://cdn.glitch.global/585aee42-d89c-4ece-870c-5b01fc1bab61/cellphone.png?v=1747575037311" class="phone-image" alt="Phone">
            </div>
          </div>
        </div>
        
        <div class="hero-right">
          <div class="rectangle2"></div>
          <div class="rectangle3"></div>
        </div>
      </div>

      <div class="categories-section">
        <h2 class="categories-title">Categories</h2>
        <div class="categories-grid">
          <div class="category-item category-chocolate">
            <img src="https://cdn.glitch.global/585aee42-d89c-4ece-870c-5b01fc1bab61/choc.png?v=1747707953400" class="category-image" alt="Chocolate">
          </div>
          <div class="category-item category-noodles">
            <img src="https://cdn.glitch.global/585aee42-d89c-4ece-870c-5b01fc1bab61/noodle.png?v=1747708218689" class="category-image" alt="Noodles">
          </div>
          <div class="category-item category-chips">
            <img src="https://cdn.glitch.global/585aee42-d89c-4ece-870c-5b01fc1bab61/chips.png?v=1747708271881" class="category-image" alt="Chips">
          </div>
          <div class="category-item category-melona">
            <img src="https://cdn.glitch.global/585aee42-d89c-4ece-870c-5b01fc1bab61/melona.png?v=1747708272936" class="category-image" alt="Melona">
          </div>
          <div class="category-item category-chocnut">
            <img src="https://cdn.glitch.global/585aee42-d89c-4ece-870c-5b01fc1bab61/chocnut?v=1747708276502" class="category-image" alt="Chocnut">
          </div>
        </div>
      </div>
    </section>

    <section class="products-section">
      <div class="products-header">
        <div class="products-title">
          <h2 class="brand-name-small">A&F</h2>
          <h2 class="section-title">Chocolates</h2>
        </div>
      </div>

      <div class="search-products"></div>

      <div class="sort-controls">
        <span class="sort-label">Sort by:</span>
        <div class="sort-buttons">
          <button class="sort-btn">Price</button>
          <button class="sort-btn">Name</button>
          <button class="sort-btn">Rating</button>
        </div>
      </div>

      <div class="products-grid">
        <div class="product-card">
          <div class="product-image-container"></div>
        </div>
        <div class="product-card">
          <div class="product-image-container"></div>
        </div>
        <div class="product-card">
          <div class="product-image-container"></div>
        </div>
        <div class="product-card">
          <div class="product-image-container"></div>
        </div>
        <div class="product-card">
          <div class="product-image-container"></div>
        </div>
        <div class="product-card">
          <div class="product-image-container"></div>
        </div>
        <div class="product-card">
          <div class="product-image-container"></div>
        </div>
        <div class="product-card">
          <div class="product-image-container"></div>
        </div>
        <div class="product-card">
          <div class="product-image-container"></div>
        </div>
        <div class="product-card">
          <div class="product-image-container"></div>
        </div>
        <div class="product-card">
          <div class="product-image-container"></div>
        </div>
        <div class="product-card">
          <div class="product-image-container"></div>
        </div>
        <div class="product-card">
          <div class="product-image-container"></div>
        </div>
        <div class="product-card">
          <div class="product-image-container"></div>
        </div>
        <div class="product-card">
          <div class="product-image-container"></div>
        </div>
        <div class="product-card">
          <div class="product-image-container"></div>
        </div>
        <div class="product-card">
          <div class="product-image-container"></div>
        </div>
        <div class="product-card">
          <div class="product-image-container"></div>
        </div>
        <div class="product-card">
          <div class="product-image-container"></div>
        </div>
        <div class="product-card">
          <div class="product-image-container"></div>
        </div>
        <div class="product-card">
          <div class="product-image-container"></div>
        </div>
        <div class="product-card">
          <div class="product-image-container"></div>
        </div>
        <div class="product-card">
          <div class="product-image-container"></div>
        </div>
        <div class="product-card">
          <div class="product-image-container"></div>
        </div>
        <div class="product-card">
          <div class="product-image-container"></div>
        </div>
        <div class="product-card">
          <div class="product-image-container"></div>
        </div>
        <div class="product-card">
          <div class="product-image-container"></div>
        </div>
      </div>
    </section>
  </body>
</html>