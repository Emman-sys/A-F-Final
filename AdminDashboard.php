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
      left:50px;
      font-size: clamp(2rem, 5vw, 3rem); /* Responsive font size */
      font-weight: bold;
      color: white;
    }

    .Admin{
      position:absolute;
      left:145px;
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
    .income{
        position:absolute;
        left: 164px;
        top:350px;
        color:black;
        font-size:36px;
        font-weight:600;
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
    .dsales{
        position:absolute;
        left: 410px;
        top:270px;
        color:black;
        font-size:30px;
        font-weight:300;
        z-index:2;
    }
    .sales{
        position:absolute;
        left: 450px;
        top:320px;
        color:#8D7D7D;
        font-size:12px;
        font-weight:300;
        z-index:2;
    }
    .income2{
        position:absolute;
        left: 415px;
        top:350px;
        color:black;
        font-size:36px;
        font-weight:600;
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
    .customers{
        position:absolute;
        left: 648px;
        top:270px;
        color:black;
        font-size:30px;
        font-weight:300;
        z-index:2;
    }
    .percent1{
        position:absolute;
        left: 670px;
        top:319px;
        color:#8D7D7D;
        font-size:12px;
        font-weight:300;
        z-index:2;
    }
    .newusers{
        position:absolute;
        left: 670px;
        top:351px;
        color:black;
        font-size:36px;
        font-weight:600;
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
    .cart{
      position: absolute;
      left: 906px;
      top: 200px;
      transform: translateY(-50%);
      width: 56px;
      height: 57px;
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
    .product{
        position:absolute;
        left: 906px;
        top:270px;
        color:black;
        font-size:30px;
        font-weight:300;
        z-index:2;
    }
    .percent2{
        position:absolute;
        left: 913px;
        top:319px;
        color:#8D7D7D;
        font-size:12px;
        font-weight:300;
        z-index:2;
    }
    .numberofp{
        position:absolute;
        left: 920px;
        top:351px;
        color:black;
        font-size:36px;
        font-weight:600;
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
     .delivery{
        position:absolute;
        left: 1159px;
        top:270px;
        color:black;
        font-size:30px;
        font-weight:300;
        z-index:2;
    }
    .percent3{
        position:absolute;
        left: 1169px;
        top:319px;
        color:#8D7D7D;
        font-size:12px;
        font-weight:300;
        z-index:2;
    }
    .ndelivery{
        position:absolute;
        left: 1178px;
        top:351px;
        color:black;
        font-size:36px;
        font-weight:600;
        z-index:2;
    }

    
    .rectangle1{
        position:absolute;
        left: 150px;
        top: 500px;
        width: 1170px;
        height: 364px;
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
    .sumsales{
        position:absolute;
        left:163px;
        top:496px;
        font-size:24px;
        font-weight:bold;
        color:black;
        z-index:2;
    }
    .number{
        position:absolute;
        left:185px;
        top:605px;
        font-size:15px;
        font-weight:medium;
        color:#8D7D7D;
        z-index:2;
    }
    .number1{
        position:absolute;
        left:185px;
        top:640px;
        font-size:15px;
        font-weight:medium;
        color:#8D7D7D;
        z-index:2;
    }
    .number2{
        position:absolute;
        left:185px;
        top:676px;
        font-size:15px;
        font-weight:medium;
        color:#8D7D7D;
        z-index:2;
    }
    .number3{
        position:absolute;
        left:185px;
        top:710px;
        font-size:15px;
        font-weight:medium;
        color:#8D7D7D;
        z-index:2;
    }
    .number4{
        position:absolute;
        left:185px;
        top:745px;
        font-size:15px;
        font-weight:medium;
        color:#8D7D7D;
        z-index:2;
    }
    .line{
       position: absolute;
       left: 234px;
       top: 830px;
       height: 2px;
       background-color:black;
       width: 57%; 
       z-index: 3;
      }
    .scalendar{
      position:absolute;
      right:215px;
      top:515px;
      width:96px;
      height:35px;
      background-color:white;
      border-radius:14px;
      border: 1px solid #8D7D7D;
      z-index:2;
    }
    .month{
      position:absolute;
      right:246px;
      top:523px;
      font-size:16px;
      font-weight:700;
      color:black;
      z-index:2;
    }
    .triangle{
      position:absolute;
      right:227px;
      top:529px;
      border-left: 10px solid transparent;
      border-right: 10px solid transparent;
      border-top: 17px solid black;
      color:black;
      z-index:2;
    }
    .tpsales{
      position:absolute;
      left:168px;
      top:880px;
      font-size:24px;
      font-weight:bold;
      color:black;
      z-index:2;
    }
    .top-product{
      position: absolute;
      top: 950px;
      width: 177px;
      height: 229px;
      background: #EEEFE8;
      border-radius: 8px;
      z-index: 3;
    }
    .top-product1 { 
        left: 180px;
     }
    .top-product2 {
         left: 380px;
     }
    .top-product3 { 
        left: 580px;
     }
    .top-product4 { 
        left: 780px; 
    }
    .top-product5 { 
        left: 980px; 
    }
     .square{
      position:absolute;
      width:155px;
      height:141px;
      border-radius:31px;
      background:#FFFFFF;
      top:975px;
      z-index:3;
    }
      
   .square1 { 
    left: 190px;
    }
   .square2 { 
    left: 390px; 
   }
   .square3 { 
    left: 590px;
   }
   .square4 {
     left: 788px;
   }
   .square5 { 
    left: 990px;
 }
 .topsales{
    position:absolute;
    left: 208px;
    top: 220px;
    width:59px;
    height: 55px;
    z-index:3;
 }
 .dailysales{
    position:absolute;
    left: 452px;
    top: 220px;
    width:59px;
    height: 55px;
    z-index:3;
 }
 .usr{
    position:absolute;
    left: 932px;
    top: 225px;
    width:59px;
    height:55px;
    z-index:3;
 }
 .cart{
    position:absolute;
    left: 687px;
    top: 248px;
    width:59px;
    height:55px;
    z-index:3;
 }
 .deliv{
    position:absolute;
    left: 1180px;
    top: 220px;
    width:59px;
    height:55px;
    z-index:3;
 }
 .notifications{
    position:absolute;
    left:1240px;
    top:65px;
    width:37px;
    height:40.23px;
    z-index:2;
 }
 .iconn{
    position:absolute;
    left:1295px;
    top:55px;
    width:37px;
    height:40.23px;
    z-index:2;
 }
   





        





    </style>







    <body>
        <div class="boxes">
     <div class="header-section"></div>
        <header class="header">
        <h2 class="brand-name">A&F</h2>
        <h2 class="Admin">Admin Dashboard</h2>
        <div class="nav-items">
        </div>

        <div class="search-bar">
        <img src="https://cdn.glitch.global/585aee42-d89c-4ece-870c-5b01fc1bab61/search.png?v=1747633330905" class="search-icon" alt="Search">
      </div>
        
         <div class="notifications">
        <img src="images/notif.png" alt="notif">
      </div>
      <div class="cart">
        <img src="images/cart.png" alt="cart">
      </div>
      <div class="topsales">
        <img src="images/topsales.png" alt="top">
      </div>
      <div class="dailysales">
        <img src="images/dailysales.png" alt="daily">
      </div>
      <div class="deliv">
        <img src="images/deliv.png" alt="deliveryyy">
      </div>
      <div class="usr">
        <img src="images/users.png" alt="newcustomers">
      </div>
       <div class="iconn">
        <img src="images/iconuser.png" alt="useracc">
      </div>
     


            <div class="box1"></div>
            <div class="box2"></div>
            <div class="box3"></div>
            <div class="box4"></div>
            <div class="box5"></div>
        </div>

         <div class="sum-sales, top-sales">
            <div class="rectangle1"></div>
            <div class="rectangle2"></div>

        </div>
            
            <div class="boxes-info">
        <h2 class="tsales">Total Sales</h2>
        <h2 class="percent">+50% Incomes</h2>
        <h2 class="income">₱100,000</h2>

         <h2 class="dsales">Daily Sales</h2>
        <h2 class="sales">-13% Sales</h2>
        <h2 class="income2">₱50,000</h2>

        <h2 class="customers">Customers</h2>
        <h2 class="percent1">+25% New Users</h2>
        <h2 class="newusers">4158</h2>

        <h2 class="product">Product</h2>
        <h2 class="percent2">+5% New Products</h2>
        <h2 class="numberofp">500</h2>

        <h2 class="delivery">Delivery</h2>
        <h2 class="percent3">Decrease by 2%</h2>
        <h2 class="ndelivery">1000</h2>
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

        <div class="summary-section">
            <h2 class="sumsales">Summary Sales</h2>
            <div class="line"></div>
            <div class="scalendar"></div>
            <div class="month">Month</div>
            <div class="triangle"></div>
            <div class="number">30+</div>
            <div class="number1">30</div>
            <div class="number2">20</div>
            <div class="number3">10</div>
            <div class="number4">0</div>
        </div>

        <div class="sales-section">
            <h2 class="tpsales">Top Sales</h2>
            <div class="top-product top-product1"></div>
            <div class="top-product top-product2"></div>
            <div class="top-product top-product3"></div>
            <div class="top-product top-product4"></div>
            <div class="top-product top-product5"></div>

            
            <div class="square square1"></div>
            <div class="square square2"></div>
            <div class="square square3"></div>
            <div class="square square4"></div>
            <div class="square square5"></div>
           
        </div>





       


        
        
      </header>

    </body>
  </head>
  </html>
