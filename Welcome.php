<!DOCTYPE html>
<html lang="en">
  <head>
   <meta charset="utf-8" />
   <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
   <title>A&F</title>
   <link href="https://fonts.googleapis.com/css2?family=Merriweather:wght@400;700&family=Merriweather+Sans:wght@700&display=swap" rel="stylesheet" />
   <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css">
   <link rel="stylesheet" href="styles.css">

<<<<<<< HEAD:Welcome.php
    <style>
  .page-wrapper{
  position: relative;
  min-height: 100vh;
  background-image:url("https://cdn.glitch.global/585aee42-d89c-4ece-870c-5b01fc1bab61/bgforA&F?v=1747045516807");
  background-size: cover;
  background-position: center;
  background-attachment: fixed;
        
=======
<style>
  body{
  background-image:url("https://cdn.glitch.global/585aee42-d89c-4ece-870c-5b01fc1bab61/image%203.png?v=1747320934399");
  background-size: cover;
  background-position: center;
  background-attachment: fixed;        
>>>>>>> 5637e085f027e009f25ba4d3684a798ed8886f88:Welcome.html
}
.navigation {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 54px 72px;
}
.nav-links-left,
.nav-links-right {
  display: flex;
  gap: 40px;
  color: #FFF;
  font-size: 20px;
  font-family: Merriweather;
}
.nav-link {
  color: #FFF;
  text-decoration: none;
  cursor: pointer;
}
.nav-logo {
  width: 108px;
  height: 108px;
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
  display: flex;
  flex-direction: column;
  padding: 120px 110px 0;
}
.hero-content {
  max-width: 787px;
}
h2 {
  position:absolute;
  top: 300px;
  font-size: 40px;
  color: #ffffff;
  font-family: Merriweather;
  font-weight: 700;
  margin-bottom: 20px;
}
h1{
  font-size: 134px;
  color: #ffffff;
  font-family: Merriweather;
  font-weight: 700;
  line-height: 1;
  margin-bottom: 32px;
}
.description {
  font-size: 20px;
  color: #ffffff;
  font-family: 'Merriweather Sans';
  font-weight: 700;
  margin-bottom: 40px;
  max-width: 787px;
}
.cta-button {
  position:absolute;
  left: 330px;
  bottom: 150px;
  background-color: #3D0D0D;
  color: #ffffff;
  font-family: 'Merriweather Sans';
  font-size: 36px;
  font-weight: 700;
  padding: 16px 40px;
  border-radius: 21px;
  border: 1px solid #8C4545;
  cursor: pointer;
  transition: background-color 200ms cubic-bezier(0.4, 0, 0.2, 1),
              border-color 200ms cubic-bezier(0.4, 0, 0.2, 1),
              color 200ms cubic-bezier(0.4, 0, 0.2, 1);
}
.chocolatesplash {
  position: absolute;
  left: 1200px;
  bottom: 19;
  max-width: 750px;
  width: 120%;
}

@media (max-width: 991px) {
  .navigation {
    padding: 54px 40px;
  }

  .brandname-section {
    padding: 120px 40px 0;
  }

  h1 {
    font-size: 80px;
  }

  .chocimage {
    position: relative;
    margin-top: 40px;
  }
}

@media (max-width: 640px) {
  .navigation {
    padding: 54px 20px;
  }

  .nav-links-left,
  .nav-links-right {
    display: none;
  }

  .mobile-menu-toggle {
    display: block;
  }

  .brandname-section {
    padding: 60px 20px 0;
  }

  h1 {
    font-size: 50px;
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
     </nav>
    
    <img src="https://cdn.builder.io/api/v1/image/assets/TEMP/cbc700ac3a9cc70c2561f787dc7a724761a462ad" alt="Logo" class="nav-logo" />

    
    <div class="nav-links-right">
      <a href="About.html" class="nav-link">About</a>
      <div class="line"></div>
      <a href="Login.html" class="nav-link">Login/Sign up</a>
    </div>
    
   
    <button class="mobile-menu-toggle" aria-label="Toggle mobile menu">
      <i class="ti ti-menu-2"></i>
    </button>
 

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
  </head>
</html>
