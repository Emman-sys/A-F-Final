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
      body{
        background-color:#C59DD4;
        background-position:center;
        background-size:cover;
        background-attachment:fixed;
        font-family:'Merriweather';
      }
      .shape1{
        position:absolute;
        left:25px;
        width:2000px;
        top:2.20px;
        height:265px;
        background-color:#CDACB1;
        border-radius:9px;
        border:1px solid gray;
        z-index:1;
      }
      .shape2{
        position:absolute;
        left:150px;
        bottom:570px;
        width:990px;
        height:61px;
        background-color:#FFFFFF;
        border: 1px solid black;
        z-index:1;
      }
      .shape3{
        position:absolute;
        left:150px;
        bottom:350px;
        width:990px;
        height:182px;
        background-color:#FFFFFF;
        border: 1px solid black;
        z-index:1;
      }
      .shape4{
        position:absolute;
        left:150px;
        bottom:170px;
        width:990px;
        height:182px;
        background-color:#FFFFFF;
        border: 1px solid black;
        z-index:1;
      }
      .shape5{
        position:absolute;
        left:150px;
        bottom:5.10px;
        width:990px;
        height:182px;
        background-color:#FFFFFF;
        border: 1px solid black;
        z-index:1;
      }
      .Lsquare1{
        position:absolute;
        left:180px;
        bottom:250px;
        width:32px;
        height:31px;
        border: 1px solid black;
        z-index:1;
      }
      .Lsquare2{
        position:absolute;
        left:180px;
        bottom:420px;
        width:32px;
        height:31px;
        border: 1px solid black;
        z-index:1;
      }
      .Lsquare3{
        position:absolute;
        left:180px;
        bottom:80px;
        width:32px;
        height:31px;
        border: 1px solid black;
        z-index:1;
      }
      .Spam{
        position:absolute;
        width:169px;
        height:169px;
        z-index:1;
        left:280px;
        bottom:355px;
      }
      .Melona{
        position:absolute;
        width:141px;
        height:160px;
        z-index:1;
        left:282px;
        bottom:190px;
      }
      .Mars{
        position:absolute;
        width:162px;
        height:162px;
        z-index:1;
        left:280px;
        bottom:6.10px;
      }
      .Payment{
        position:absolute;
        width:400px;
        height:650px;
        left:1300px;
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
      .pyment{
        position:absolute;
        font-size:40px;
        left:1320px;
        bottom:550px;
        font-weight:bold;
        color:white;
        z-index:1;
      }
      .line {
       position:absolute;
       left: 1320px;
       bottom: 542px;
       height: 2px;
       background-color: white;
       width: 18%; 
       margin: 20px 0;
      }
      .method{
        position:absolute;
        font-size:18px;
        left:1320px;
        bottom:490px;
        font-weight:medium;
        color:white;
        z-index:1;
      }
      .shape6{
        position: absolute;
        left: 1320px;
        bottom: 430px;
        width:97px;
        height:29px;
        background: #5127A3;
        border-radius:13px;
        border: 1px solid white;
      }
      .shape7{
        position: absolute;
        left: 1430px;
        bottom: 430px;
        width:97px;
        height:29px;
        background: #5127A3;
        border-radius:13px;
        border: 1px solid white;
      }
      .shape8{
        position: absolute;
        left: 1545px;
        bottom: 430px;
        width:97px;
        height:29px;
        background: #5127A3;
        border-radius:13px;
        border: 1px solid white;
      }
      .gcash{
        position:absolute;
        width:24px;
        height:24px;
        left: 1548px;
        bottom: 435px;
        filter: brightness(0) invert(1);
        z-index:1;
      }
      .card{
        position:absolute;
        width:24px;
        height:24px;
        left: 1330px;
        bottom: 435px;
        filter: brightness(0) invert(1);
        z-index:1;
      }
      .peso{
        position:absolute;
        width:22px;
        height:21px;
        left: 1437px;
        bottom: 436px;
        filter: brightness(0) invert(1);
        z-index:1;
      }
      .nme{
        position:absolute;
        font-size:18px;
        left:1320px;
        bottom:350px;
        font-weight:medium;
        color:white;
        z-index:1;
      }
      .line2{
       position:absolute;
       left: 1320px;
       bottom: 320px;
       height: 2px;
       background-color: #C1ACAC;
       width: 18%; 
       margin: 10px 0;
      }
      .address{
       position:absolute;
        font-size:18px;
        left:1320px;
        bottom:278px;
        font-weight:medium;
        color:white;
        z-index:1;
      }
      .line3{
       position:absolute;
       left: 1320px;
       bottom: 258px;
       height: 2px;
       background-color: #C1ACAC;
       width: 18%; 
       margin: 10px 0;
      }
      .number{
       position:absolute;
        font-size:18px;
        left:1320px;
        bottom:220px;
        font-weight:medium;
        color:white;
        z-index:1;
      }
      .line4{
       position:absolute;
       left: 1320px;
       bottom: 200px;
       height: 2px;
       background-color: #C1ACAC;
       width: 18%; 
       margin: 10px 0;
      }
      .email{
        position:absolute;
        font-size:18px;
        left:1320px;
        bottom:155px;
        font-weight:medium;
        color:white;
        z-index:1;
      }
       .line5{
       position:absolute;
       left: 1320px;
       bottom: 140px;
       height: 2px;
       background-color: #C1ACAC;
       width: 18%; 
       margin: 10px 0;
      }
      .shape9{
        position:absolute;
        left:1350px;
        bottom:50px;
        width:292px;
        height:56px;
        border-radius:15px;
        background-color:#8ADEF1; 
      }
      
      
      .prd{
        position:absolute;
        left:320px;
        bottom:575px;
        font-size:18px;
        font-weight:medium;
        color:#8D7D7D;
        z-index:1;
      }
      .price{
        position:absolute;
        left:580px;
        bottom:575px;
        font-size:18px;
        font-weight:medium;
        color:#8D7D7D;
        z-index:1;
      }
      .quantity{
        position:absolute;
        left:700px;
        bottom:575px;
        font-size:18px;
        font-weight:medium;
        color:#8D7D7D;
        z-index:1;
      }
      .totalp{
        position:absolute;
        left:830px;
        bottom:575px;
        font-size:18px;
        font-weight:medium;
        color:#8D7D7D;
        z-index:1;
      }
      .action{
        position:absolute;
        left:980px;
        bottom:575px;
        font-size:18px;
        font-weight:medium;
        color:#8D7D7D;
        z-index:1;
      }
      .spm{
        position:absolute;
        left:460px;
        bottom:420px;
        font-size:15px;
        font-weight:medium;
        color:black;
        z-index:1;
      }
      .mln{
        position:absolute;
        left:470px;
        bottom:250px;
        font-size:15px;
        font-weight:medium;
        color:black;
        z-index:2;
      }
      .mrs{
        position:absolute;
        left:480px;
        bottom:76px;
        font-size:15px;
        font-weight:medium;
        color:black;
        z-index:2;
      }
      .price1{
        position:absolute;
        left:600px;
        bottom:415px;
        font-size:25px;
        font-weight:medium;
        color:black;
        z-index:1;
      }
      .price2{
        position:absolute;
        left:600px;
        bottom:245px;
        font-size:25px;
        font-weight:medium;
        color:black;
        z-index:1;
      }
      .price3{
        position:absolute;
        left:600px;
        bottom:76px;
        font-size:25px;
        font-weight:medium;
        color:black;
        z-index:2;
      }
      .shapeq1{
        position:absolute;
        left:695px;
        bottom: 430px;
        width:85px;
        height:29px;
        border: 1px solid black;
        z-index:1;
        background-color:#FFFFFF;
        
      }
      .boxq1{
        position:absolute;
        left:695px;
        bottom: 430px;
        width:27px;
        height:29px;
        border: 1px solid black;
        z-index:1;
        background-color:#FFFFFF;
      }
      .boxq2{
        position:absolute;
        left:760px;
        bottom: 430px;
        width:27px;
        height:29px;
        border: 1px solid black;
        z-index:1;
        background-color:#FFFFFF;
      }
       .shapeq2{
        position:absolute;
        left:695px;
        bottom: 260px;
        width:85px;
        height:29px;
        border: 1px solid black;
        z-index:1;
        background-color:#FFFFFF;
        
      }
       .boxq3{
        position:absolute;
        left:695px;
        bottom: 260px;
        width:27px;
        height:29px;
        border: 1px solid black;
        z-index:1;
        background-color:#FFFFFF;
      }
      .boxq4{
        position:absolute;
        left:760px;
        bottom: 260px;
        width:27px;
        height:29px;
        border: 1px solid black;
        z-index:1;
        background-color:#FFFFFF;
      }
      .shapeq3{
        position:absolute;
        left:695px;
        bottom:90px;
        width:85px;
        height:29px;
        border: 1px solid black;
        z-index:1;
        background-color:#FFFFFF;
      }
      .boxq5{
        position:absolute;
        left:695px;
        bottom: 90px;
        width:27px;
        height:29px;
        border: 1px solid black;
        z-index:1;
        background-color:#FFFFFF;
      }
      .boxq6{
        position:absolute;
        left:760px;
        bottom: 90px;
        width:27px;
        height:29px;
        border: 1px solid black;
        z-index:1;
        background-color:#FFFFFF;
      }
      .tprice1{
        position:absolute;
        left:850px;
        bottom:415px;
        font-size:25px;
        font-weight:medium;
        color:#BF25F2;
        z-index:1;
      }
      .tprice2{
        position:absolute;
        left:850px;
        bottom:245px;
        font-size:25px;
        font-weight:medium;
        color:#BF25F2;
        z-index:1;
      }
      .tprice3{
        position:absolute;
        left:850px;
        bottom:76px;
        font-size:25px;
        font-weight:medium;
        color:#BF25F2;
        z-index:2;
      }
      .action1{
        position:absolute;
        left:970px;
        bottom:420px;
        font-size:18px;
        font-weight:medium;
        color:#443D3D;
        z-index:1;
      }
      .action2{
        position:absolute;
        left:970px;
        bottom:250px;
        font-size:18px;
        font-weight:medium;
        color:#443D3D;
        z-index:1;
      }
       .action3{
        position:absolute;
        left:970px;
        bottom:80px;
        font-size:18px;
        font-weight:medium;
        color:#443D3D;
        z-index:1;
      }
      .chk{
       position:absolute;
       left: 1430px;
       bottom: 50px;
       font-size:25px;
       font-weight:extra bold;
       color:#FFFFFF;
       z-index:1;
     }
     .add1{
      position:absolute;
      left:705px;
      bottom: 434px;
      font-size: 15px;
      font-weight:bold;
      width: 11px;px;
      height:23px;
      z-index:1;
      color:black;
     }
    .minus1{
      position:absolute;
      left:773px;
      bottom: 430px;
      font-size:15px;
      font-weight:bold;
      bottom: 430px;
      width:11px;
      height:23px;
      z-index:1;
      color:black;
    }
    .add2{
      position:absolute;
      left: 692px;
      bottom: 260px;
      font-size: 15px;
      font-weight: bold;
      width:11px;
      height:23px;
      z-index: 2;
      color:black;
    }
    .minus2{
      position:absolute;
      left:770px;
      bottom: 260px;
      font-size: 15px;
      font-weight: bold;
      width:11px;
      height:23px;
      z-index: 1;
      color:black;
    }
    
      
      
    
    
    
    
    
    </style>
    </head>
  <body>
    <div class="shape1"></div>
    <div class="shape2"></div>
    <div class="shape3"></div>
    <div class="shape4"></div>
    <div class="shape5"></div>
    <div class="Lsquare1"></div>
    <div class="Lsquare2"></div>
    <div class="Lsquare3"></div>
    <div class="Payment"></div>
    
    <h1 class="brand">A&F</h1>
    <h1 class="pyment">Payment Info.</h1>
    <h1 class="method">Payment Method:</h1>
    <h1 class="nme">Name:</h1>
    <h1 class="address">Address:</h1>
    <h1 class="number">Phone Number:</h1>
    <h1 class="email">Email:</h1>
    <h1 class="prd">Product</h1>
    <h1 class="price">Unit Price</h1>
    <h1 class="quantity">Quantity</h1>
    <h1 class="totalp">Total Price</h1>
    <h1 class="action">Action</h1>
    <h1 class="spm">SPAM PORK</h1>
    <h1 class="mln">MELONA</h1>
    <h1 class="mrs">MARS</h1>
    <h1 class="chk">Checkout</h1>
    <h1 class="price1">₱115</h1>
    <h1 class="price2">₱35</h1>
    <h1 class="price3">₱30</h1>
    
     <h1 class="tprice1">₱115</h1>
    <h1 class="tprice2">₱35</h1>
    <h1 class="tprice3">₱30</h1>
    
     <h1 class="action1">Delete</h1>
    <h1 class="action2">Delete</h1>
    <h1 class="action3">Delete</h1>
    
    
    
    
    
    <div class="prods">
      <img src="https://cdn.glitch.global/585aee42-d89c-4ece-870c-5b01fc1bab61/spam?v=1747830810539" class="Spam">
      <img src="https://cdn.glitch.global/585aee42-d89c-4ece-870c-5b01fc1bab61/icecream?v=1747830812291" class="Melona">
      <img src="https://cdn.glitch.global/585aee42-d89c-4ece-870c-5b01fc1bab61/mars?v=1747830818383" class="Mars">
      <img src="https://cdn.glitch.global/585aee42-d89c-4ece-870c-5b01fc1bab61/gcash.png?v=1747888258597" class="gcash">
      <img src="https://cdn.glitch.global/585aee42-d89c-4ece-870c-5b01fc1bab61/credit-card.png?v=1747888252715" class="card">
      <img src="https://cdn.glitch.global/585aee42-d89c-4ece-870c-5b01fc1bab61/philippine-peso.png?v=1747888253819" class="peso">
    </div>
    
    
    
    <div class="line"></div>
    <div class="line2"></div>
    <div class="line3"></div>
    <div class="line4"></div>
    <div class="line5"></div>
    
    <div class="shape6"></div>
    <div class="shape7"></div>
    <div class="shape8"></div>
    <div class="shape9"></div>
    <div class="shapeq1"></div>
    <div class="boxq1"></div>
    <div class="boxq2"></div>
    <div class="shapeq2"></div>
    <div class="boxq3"></div>
    <div class="boxq4"></div>
    <div class="shapeq3"></div>
    <div class="boxq5"></div>
    <div class="boxq6"></div>

    <div class="add1">+</div>
    <div class="minus1">-</div>
    <div class="add2">+</div>
    <div class="minus2">-</div>
  
  
  </body>
</html>