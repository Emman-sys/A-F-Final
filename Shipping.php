<!DOCTYPE html>
<html lang="en">
  <head>
   <meta charset="utf-8" />
   <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
   <title>A&F</title>
   <link href="https://fonts.googleapis.com/css2?family=Merriweather:wght@400;700&family=Merriweather+Sans:wght@700&display=swap" rel="stylesheet" />
   <link rel="stylesheet" href="https://lucide.dev/">
   <link rel="stylesheet" href="styles.css">
    
    <style>
      body {
      background-image: url('images/bg.png'); 
      background-position: center;
      background-size: cover;
      background-attachment: fixed;
      font-family: 'Merriweather', serif;
      margin: 0;
      padding: 0;
      overflow-x: hidden;  
      min-height: calc(100vh + 180px);  /* Ensure full height */
      width: 100%;
      }
      .shape1{
        position:absolute;
        left:25px;
        width:2000px;
        top:2.20px;
        height:265px;
        background:linear-gradient(to top, #5127A3,#986C93, #E0B083);
        border-radius:9px;
        border:1px solid gray;
        z-index:1;
      }
      .shape2{
        position:absolute;
        right:150px;
        bottom:570px;
        width:990px;
        height:61px;
        background-color:#FFFFFF;
        border: 1px solid black;
        z-index:1;
      }
      .shape3{
        position:absolute;
        right:150px;
        bottom:350px;
        width:990px;
        height:215px;
        background-color:#FFFFFF;
        border: 1px solid black;
        z-index:1;
      }
      .shape4{
        position:absolute;
        right:150px;
        bottom:170px;
        width:990px;
        height:182px;
        background-color:#FFFFFF;
        border: 1px solid black;
        z-index:1;
      }
      .shape5{
        position:absolute;
        right:150px;
        bottom:80px;
        width:990px;
        height:100px;
        background-color:#FFFFFF;
        border: 1px solid black;
        z-index:1;
      }
       .shape6{
        position: absolute;
        left: 320px;
        bottom: 70px;
        width:97px;
        height:29px;
        background: #5127A3;
        border-radius:13px;
        border: 1px solid white;
        z-index:1;
      }
      
     
      
      .Spam{
        position:absolute;
        width:169px;
        height:169px;
        z-index:1;
        right:945px;
        bottom:355px;
      }
      .Melona{
        position:absolute;
        width:141px;
        height:160px;
        z-index:1;
        right:945px;
        bottom:190px;
      }
      
      .shipinfo{
        position:absolute;
        width:400px;
        height:650px;
        left:200px;
        bottom:2.20px;
        background:linear-gradient(to top,#371B70,#5127A3,#6A34D6);
        border-radius:16px;
      }
      .brand{
        position:absolute;
        font-size:65px;
        left:50px;
        bottom:769px;
        font-weight:bold;
        color:black;
        z-index:1;
      }
      .shipping{
        position:absolute;
        font-size:25px;
        left:230px;
        bottom:580px;
        font-weight:bold;
        color:white;
        z-index:1;
      }
      .line {
       position:absolute;
       left: 215px;
       bottom: 548px;
       height: 2px;
       background-color: white;
       width: 18%; 
       margin: 20px 0;
      }
      
      .nme{
        position:absolute;
        font-size:18px;
        left:230px;
        bottom:500px;
        font-weight:medium;
        color:white;
        z-index:1;
      }
      .line2{
       position:absolute;
       left: 210px;
       bottom: 460px;
       height: 2px;
       background-color: #C1ACAC;
       width: 18%; 
       margin: 10px 0;
      }
      .address{
       position:absolute;
       font-size:18px;
        left:230px;
        bottom:400px;
        font-weight:medium;
        color:white;
        z-index:1;
      }
      .line3{
       position:absolute;
       left: 210px;
       bottom: 350px;
       height: 2px;
       background-color: #C1ACAC;
       width: 18%; 
       margin: 10px 0;
      }
      .number{
       position:absolute;
        font-size:18px;
        left:230px;
        bottom:300px;
        font-weight:medium;
        color:white;
        z-index:1;
      }
      .line4{
       position:absolute;
       left: 210px;
       bottom: 254px;
       height: 2px;
       background-color: #C1ACAC;
       width: 18%; 
       margin: 10px 0;
      }
      .email{
        position:absolute;
        font-size:18px;
        left:230px;
        bottom:200px;
        font-weight:medium;
        color:white;
        z-index:1;
      }
       .line5{
       position:absolute;
       left: 210px;
       bottom: 155px;
       height: 2px;
       background-color: #C1ACAC;
       width: 18%; 
       margin: 10px 0;
      }
      .method{
        position:absolute;
        font-size:18px;
        left:230px;
        bottom:100px;
        font-weight:medium;
        color:white;
        z-index:1;
      }
     
      
      
      
      .price{
        position:absolute;
        left:1290px;
        bottom:520px;
        font-size:18px;
        font-weight:medium;
        color:#8D7D7D;
        z-index:1;
      }
      .quantity{
        position:absolute;
        right:500px;
        bottom:520px;
        font-size:18px;
        font-weight:medium;
        color:#8D7D7D;
        z-index:1;
      }
      .totalsub1{
        position:absolute;
        right:300px;
        bottom:520px;
        font-size:18px;
        font-weight:medium;
        color:#8D7D7D;
        z-index:1;
      }
       .price1{
        position:absolute;
        left:1290px;
        bottom:415px;
        font-size:25px;
        font-weight:medium;
        color:black;
        z-index:1;
      }
      .price2{
        position:absolute;
        left:1290px;
        bottom:245px;
        font-size:25px;
        font-weight:medium;
        color:black;
        z-index:1;
      }
      
    
      
      .spm{
        position:absolute;
        right:830px;
        bottom:420px;
        font-size:15px;
        font-weight:medium;
        color:black;
        z-index:1;
      }
      .mln{
        position:absolute;
        right:830px;
        bottom:250px;
        font-size:15px;
        font-weight:medium;
        color:black;
        z-index:2;
      }
      .quantity1{
        position:absolute;
        right:520px;
        bottom:415px;
        font-size:25px;
        font-weight:medium;
        color:black;
        z-index:10;
      }
      .quantity2{
        position:absolute;
        right:520px;
        bottom:250px;
        font-size:25px;
        font-weight:medium;
        color:black;
        z-index:10;
      }
      .itemsub1{
        position:absolute;
        right:320px;
        bottom:415px;
        font-size:25px;
        font-weight:medium;
        color:black;
        z-index:10;
      }
      .itemsub2{
        position:absolute;
        right:320px;
        bottom:250px;
        font-size:25px;
        font-weight:medium;
        color:black;
        z-index:10;
      }
      
     
 </style>
    </head>
  <body>
    <div class="shape1"></div>
    <div class="shape2"></div>
    <div class="shape3"></div>
    <div class="shape4"></div>
    <div class="shape5"></div>
    <div class="shape6"></div>
   
    <div class="shipinfo"></div>
    
    <h1 class="brand">A&F</h1>
    <h1 class="shipping">Shipping Information</h1>
    <h1 class="nme">Name:</h1>
    <h1 class="address">Address:</h1>
    <h1 class="number">Phone Number:</h1>
    <h1 class="email">Email:</h1>
    <h1 class="method">Payment Method:</h1>
    <h1 class="price">Unit Price</h1>
    <h1 class="quantity">Quantity</h1>
    <h1 class="spm">SPAM PORK</h1>
    <h1 class="mln">MELONA</h1>
    <h1 class="totalsub1">Item Subtotal</h1>
   
    <h1 class="price1">₱115</h1>
    <h1 class="price2">₱35</h1>
    <h1 class="quantity1">1</h1>
     <h1 class="quantity2">2</h1>
    <h1 class="itemsub1">₱115</h1>
    <h1 class="itemsub2">₱35</h1>
    
  
    
    <div class="prods">
      <img src="https://cdn.glitch.global/585aee42-d89c-4ece-870c-5b01fc1bab61/spam?v=1747830810539" class="Spam">
      <img src="https://cdn.glitch.global/585aee42-d89c-4ece-870c-5b01fc1bab61/icecream?v=1747830812291" class="Melona">
    </div>
    
    <div class="line"></div>
    <div class="line2"></div>
    <div class="line3"></div>
    <div class="line4"></div>
    <div class="line5"></div>
    
 </body>
</html>