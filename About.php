<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>A&F Chocolates</title>
    <link rel="stylesheet" href="style.css">
  <style>
    body {
    background-image:url('images/aboutbg.png'); 
    background-size: cover;
    background-position: center;
    background-attachment: fixed; 
    font-family: Merriweather Bold;
    margin: 0;
    padding: 0; 
}

.header {
    background-color: #6a1b9a; /* Purple */
    padding: 20px 0;
}

.navbar {
    display: flex;
    justify-content: center;
}

.nav-links {
    list-style: none;
    display: flex;
    gap: 30px;
    padding: 0;
    margin: 0;
}

.nav-links li a {
    color: white;
    text-decoration: none;
    font-weight: bold;
    font-size: 16px;
}

.nav-links li a:hover {
    text-decoration: underline;
}


.about-us {
    padding: 40px 20px;
    max-width: 800px;
    margin: auto;
    text-align: center;
}

.about-us h1 {
    font-size: 60px;
    color: white;
    margin-bottom: 10px;
}

.about-container {
    background-color: rgba(255, 255, 255, 0.6);
    padding: 60px;
    border-radius: 10px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);

}

.about-container h2 {
     width: 630px;
  height: 100px;
}

.about-container p {
    font-size: 18px;
    font-weight:bold;
    line-height: 1.6;
}

.social-item img {
  width: 30px;
  height: 30px;
}
 </style>   
</head>
 
<body>
    <header class="header">
        <nav class="navbar">
            <ul class="nav-links">
    <li><a href="Welcome.php">Home</a></li>
    <li><a href="Contact.php">Contact</a></li>
    <div class="social-item">
        <img src="https://cdn.builder.io/api/v1/image/assets/TEMP/cbc700ac3a9cc70c2561f787dc7a724761a462ad" alt="A&F Chocolates logo" />
    </div>
    <li><a href="About.php">About</a></li>
    <li><a href="Welcome.php">Login/Sign up</a></li>
            </ul>
        </nav>
    </header>

    
<section class="about-us">
    <h1>About Us</h1>
    <div class="about-container">
        <h2><img src="https://cdn.builder.io/api/v1/image/assets/TEMP/cbc700ac3a9cc70c2561f787dc7a724761a462ad" alt="A&F Chocolates logo" /></h2>
       <p>Welcome to A&F CHOCOLATES, where the world’s finest flavors meet your neighborhood!</p>
       <p>We're your local passport to global indulgence, specializing in exported chocolates, snacks, and beverages you won’t find on typical shelves. From Exported Chocolates and Different Flavored Chips to exotic flavors, rare snacks, and fizzy drinks from far-off lands, we bring the globe’s culinary treasures right to Lipa City.</p>
    </div>
</section>
</body>
</html>

