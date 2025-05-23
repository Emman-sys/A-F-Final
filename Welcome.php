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
  html, body {
    height: 100%;
    margin: 0;
    padding: 0;
    overflow: hidden; /* Prevent scrolling */
  }
  .page-wrapper {
    position: relative;
    min-height: 100vh;
    height: 100vh;
    background-image: url("https://cdn.glitch.global/585aee42-d89c-4ece-870c-5b01fc1bab61/bgforA&F?v=1747045516807");
    background-size: cover;
    background-position: center;
    background-attachment: fixed;
    box-sizing: border-box;
    display: flex;
    flex-direction: column;
  }
  .navigation {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 32px;
    padding: 24px 32px;
    width: 100%;
    flex-shrink: 0;
  }
  .nav-link {
    color: #FFF;
    text-decoration: none;
    cursor: pointer;
    font-size: 18px;
    font-family: Merriweather;
    margin: 0 8px;
    white-space: nowrap;
  }
  .nav-logo {
    width: 64px;
    height: 64px;
    margin: 0 16px;
  }
  .mobile-menu-toggle {
    display: none;
    color: #ffffff;
    background: none;
    border: none;
    cursor: pointer;
  }
  .mobile-menu-toggle i {
    font-size: 24px;
  }
  .brandname-section {
    flex: 1 1 auto;
    display: flex;
    flex-direction: column;
    align-items: flex-start;
    justify-content: center;
    padding: 40px 32px 0;
    max-width: 700px;
    min-height: 0;
    overflow: hidden;
  }
  h2 {
    font-size: 28px;
    color: #ffffff;
    font-family: Merriweather;
    font-weight: 700;
    margin-bottom: 16px;
    position: static;
  }
  h1 {
    font-size: 64px;
    color: #ffffff;
    font-family: Merriweather;
    font-weight: 700;
    line-height: 1;
    margin-bottom: 24px;
  }
  .description {
    font-size: 18px;
    color: #ffffff;
    font-family: 'Merriweather Sans';
    font-weight: 700;
    margin-bottom: 32px;
    max-width: 600px;
  }
  .cta-button {
    background-color: #3D0D0D;
    color: #ffffff;
    font-family: 'Merriweather Sans';
    font-size: 24px;
    font-weight: 700;
    padding: 12px 32px;
    border-radius: 16px;
    border: 1px solid #8C4545;
    cursor: pointer;
    transition: background-color 200ms cubic-bezier(0.4, 0, 0.2, 1),
                border-color 200ms cubic-bezier(0.4, 0, 0.2, 1),
                color 200ms cubic-bezier(0.4, 0, 0.2, 1);
    margin-top: 16px;
  }
  .chocolatesplash {
    display: block;
    max-width: 350px;
    width: 100%;
    height: auto;
    margin-top: 32px;
  }
  @media (max-width: 991px) {
    .navigation {
      padding: 24px 16px;
      gap: 16px;
    }
    .brandname-section {
      padding: 40px 16px 0;
      max-width: 100%;
    }
    h1 {
      font-size: 40px;
    }
    .chocolatesplash {
      max-width: 250px;
    }
  }
  @media (max-width: 640px) {
    .navigation {
      padding: 16px 8px;
      gap: 8px;
      flex-wrap: wrap;
    }
    .nav-link {
      font-size: 16px;
      margin: 0 4px;
    }
    .mobile-menu-toggle {
      display: block;
    }
    .brandname-section {
      padding: 24px 8px 0;
    }
    h1 {
      font-size: 28px;
    }
    .chocolatesplash {
      max-width: 180px;
    }
  }
  </style>
  </head>
  <body>
    <div class="page-wrapper">
      <nav class="navigation">
        <a href="#" class="nav-link">Home</a>
        <a href="#" class="nav-link">Contact</a>
        <img src="https://cdn.builder.io/api/v1/image/assets/TEMP/cbc700ac3a9cc70c2561f787dc7a724761a462ad" alt="Logo" class="nav-logo" />
        <a href="About.html" class="nav-link">About</a>
        <a href="Login.html" class="nav-link">Login/Sign up</a>
        <button class="mobile-menu-toggle" aria-label="Toggle mobile menu">
          <i class="ti ti-menu-2"></i>
        </button>
      </nav>
      <main class="brandname-section">
        <h2>More Than Just Sweets!</h2>
        <h1>A&FCHOCOLATE</h1>
        <p class="description">
          Discover a world of flavor with A&F Chocolate! From affordable chocolates to your favorite Korean snacks and classic Filipino treats — all in one place.
        </p>
        <button class="cta-button">Order</button>
        <img src="https://cdn.glitch.global/585aee42-d89c-4ece-870c-5b01fc1bab61/chocolatesplash?v=1747319066989" alt="Chocolate splash" class="chocolatesplash" />
      </main>
    </div>
  </body>
</html>
