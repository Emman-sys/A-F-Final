<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>A&F Chocolates</title>
    <link rel="stylesheet" href="style.css">
  <style>
    body {
    font-family: Merriweather Bold;
    margin: 0;
    padding: 0;
    
    background-image: url('images/Aboutuzbackground.jpeg'); 
    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;
    background-attachment: fixed; 
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
                <li><a href="#">Home</a></li>
                <li><a href="#">Contact</a></li>
<div class="social-item">
              <img src="" alt="A&F Chocolates logo" />
              </div>
                <li><a href="#">About</a></li>
                <li><a href="#">Login/Sign up</a></li>
            </ul>
        </nav>
    </header>

    
<section class="about-us">
    <h1>About Us</h1>
    <div class="about-container">
