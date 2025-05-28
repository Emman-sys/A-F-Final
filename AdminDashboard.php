<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>A&F Chocolates</title>
  <link rel="stylesheet" href="style.css" />
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

  <head>
    <style>
     body{
        background:url("https://cdn.glitch.global/585aee42-d89c-4ece-870c-5b01fc1bab61/image%203.png?v=1747320934399");
        background-position:center;
        background-size:cover;
        background-attachment:fixed;
        font-family: 'Poppins', serif;
        min-height: 100vh;
        width:100%;
        overflow-x: hidden;
    }

      .brand-name {
      font-size: clamp(2rem, 5vw, 3rem); /* Responsive font size */
      font-weight: bold;
      color: white;
    }

    .Admin{
      position:absolute;
      left:128px;
      top:40px;
      font-size: 25px; /* Responsive font size */
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
      top: -95px;
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
    .box1{
        position:absolute;
        left: 135px;
        top: 168px;
        width:213px;
        height: 269px;
        background: linear-gradient(to bottom, #D997D5,#FFFFFF);
        border-radius:20px;
    }
    .circ1{
        position:absolute;
        left: 186px;
        top: 200px;
        width:101px;
        height: 97px;
        background: white;
        border-radius:50px;
        z-index:1;

    }
    .crl1{
        position:absolute;
        left: 194px;
        top: 209px;
        width:85px;
        height: 79px;
        background: #D997D5;
        border-radius:55px;
        z-index:2;
    }

    .tsales{
        position:absolute;
        left: 160px;
        top:270px;
        color:black;
        font-size:30px;
        font-weight:300;
        z-index:2;
    }
    .percent{
        position:absolute;
        left: 190px;
        top:320px;
        color:#8D7D7D;
        font-size:12px;
        font-weight:300;
        z-index:2;
    }
    .box2{
        position:absolute;
        left: 375px;
        top: 168px;
        width:213px;
        height: 269px;
        background: linear-gradient(to bottom, #7B87C6,#FFFFFF);
        border-radius:20px;
    }
    .circ2{
        position:absolute;
        left: 430px;
        top: 200px;
        width:101px;
        height: 97px;
        background: white;
        border-radius:50px;
        z-index:1;

    }
    .crl2{
        position:absolute;
        left: 439px;
        top: 209px;
        width:83px;
        height: 79px;
        background: #7B87C6;
        border-radius:50px;
        z-index:2;
    }
    .box3{
        position:absolute;
        left: 615px;
        top: 168px;
        width:213px;
        height: 269px;
        background: linear-gradient(to bottom, #7BC68F,#FFFFFF);
        border-radius:20px;
    }
     .circ3{
        position:absolute;
        left: 668px;
        top: 200px;
        width:101px;
        height: 97px;
        background: white;
        border-radius:50px;
        z-index:1;

    }
    .crl3{
        position:absolute;
        left: 676px;
        top: 209px;
        width:83px;
        height: 79px;
        background: #7BC68F;
        border-radius:50px;
        z-index:2;
    }
    .box4{
        position:absolute;
        left: 855px;
        top: 168px;
        width:213px;
        height: 269px;
        background: linear-gradient(to bottom, #C6B27B,#FFFFFF);
        border-radius:20px;
    }
     .circ4{
        position:absolute;
        left: 906px;
        top: 200px;
        width:101px;
        height: 97px;
        background: white;
        border-radius:50px;
        z-index:1;

    }
    .crl4{
        position:absolute;
        left: 915px;
        top: 209px;
        width:83px;
        height: 79px;
        background: #C6B27B;
        border-radius:50px;
        z-index:2;
    }
    .box5{
        position:absolute;
        left: 1112px;
        top: 168px;
        width:213px;
        height: 269px;
        background: linear-gradient(to bottom, #6A34D6,#FFFFFF);
        border-radius:20px;
    }
     .circ5{
        position:absolute;
        left: 1158px;
        top: 200px;
        width:101px;
        height: 97px;
        background: white;
        border-radius:50px;
        z-index:1;
    }
    .crl5{
        position:absolute;
        left: 1167px;
        top: 209px;
        width:83px;
        height: 79px;
        background: #6A34D6;
        border-radius:50px;
        z-index:2;
    }

    
    
    
    .rectangle1{
        position:absolute;
        left: 150px;
        top: 500px;
        width: 1170px;
        height: 359px;
        background: linear-gradient(to bottom, #FFFFFF, #E8DEF1);
        border-radius:14px;
    }
    .rectangle2{
        position:absolute;
        left: 150px;
        top: 886px;
        width: 1170px;
        height: 360px;
        background: linear-gradient(to bottom, #B85CD7, #DDCFCF);
        border-radius:14px;
    }



        





    </style>







    <body>
        <div class="boxes">
            <div class="box1"></div>
            <div class="box2"></div>
            <div class="box3"></div>
            <div class="box4"></div>
            <div class="box5"></div>
        </div>
            
            <div class="boxes-section"></div>
        <h2 class="tsales">Total Sales</h2>
        <h2 class="percent">+50% Incomes</h2>
        <div class="income">100,00</h2>
        </div>



        <div class="circles">
            <div class="circ1"></div>
            <div class="circ2"></div>
            <div class="circ3"></div>
            <div class="circ4"></div>
            <div class="circ5"></div>
        </div>

        <div class="circles2">
            <div class="crl1"></div>
            <div class="crl2"></div>
            <div class="crl3"></div>
            <div class="crl4"></div>
            <div class="crl5"></div>
        </div>



        <div class="header-section"></div>
        <header class="header">
        <h2 class="brand-name">A&F</h2>
        <h2 class="Admin">Admin Dashboard</h2>
        <div class="nav-items">
        </div>

        <div class="sum-sales, top-sales">
            <div class="rectangle1"></div>
            <div class="rectangle2"></div>

        </div>


        <div class="search-bar">
        <img src="https://cdn.glitch.global/585aee42-d89c-4ece-870c-5b01fc1bab61/search.png?v=1747633330905" class="search-icon" alt="Search">
      </div>
        
      </header>

    </body>
  </head>
  </html>
