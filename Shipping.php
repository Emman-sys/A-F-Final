<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="utf-8" />
   <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
   <title>A&F Shipping</title>
   <link href="https://fonts.googleapis.com/css2?family=Merriweather:wght@400;700&family=Merriweather+Sans:wght@700&display=swap" rel="stylesheet" />
   <link rel="stylesheet" href="styles.css">
    
   <style>
      * {
         box-sizing: border-box;
      }

      body {
         background-image:url("https://cdn.glitch.global/585aee42-d89c-4ece-870c-5b01fc1bab61/image%203.png?v=1747320934399");
         background-color: #fbcdfb; 
         background-position: center;
         background-size: cover;
         background-attachment: fixed;
         font-family: 'Merriweather', serif;
         margin: 0;
         padding: 0;
         overflow-x: hidden;  
         min-height: 100vh;
         width: 100%;
      }

      .orderedsum {
         display: flex;
         max-width: 1400px;
         height:200px;
         margin: 0 auto;
         padding: 2rem;
         gap: 2rem;
         align-items: flex-start;
         position: relative;
      }

      .header {
        position: absolute;
        left: 0;
        right: 0;
        top: 0;
        height: 100px;
        background: linear-gradient(to top, #5127A3,#986C93, #E0B083);
        border: 1px solid gray;
        z-index: 1;
        margin: 10px;
      }

      .header-right {
         position: absolute;
         right: 60px;
         top: 35px;
         z-index: 3;
         display: flex;
         align-items: center;
         height: 50px;
      }
      .header-right i {
         font-size: 32px;
         color: rgb(255, 255, 255);
         line-height: 1;
      }

      .brand {
         position: absolute;
         font-size: 36px;
         left: 30px;
         top: 28px;
         font-weight: bold;
         color: white;
         z-index: 2;
         margin: 0;
      }

      .shipping {
         width: 400px;
         min-height: 650px;
         background: linear-gradient(to top, #371B70, #5127A3, #6A34D6);
         border-radius: 16px;
         padding: 2rem;
         margin-top: 90px;
         position: relative;
         z-index: 2;
      }

      .shipping-title {
         font-size: 1.5rem;
         font-weight: bold;
         color: white;
         text-align: center;
         margin-bottom: 2rem;
         border-bottom: 5px solid white;
         padding-bottom: 1rem;
      }

      .nme {
        position: absolute;
        font-size: 18px;
        left: 30px;
        top: 120px;
        font-weight: medium;
        color: white;
        z-index: 3;
        margin: 0;
      }

      .line2 {
       position: absolute;
       left: 30px;
       top: 150px;
       height: 2px;
       background-color: #C1ACAC;
       width: 80%; 
       z-index: 3;
      }

      .address {
       position: absolute;
       font-size: 18px;
        left: 30px;
        top: 180px;
        font-weight: medium;
        color: white;
        z-index: 3;
        margin: 0;
      }

      .line3 {
       position: absolute;
       left: 30px;
       top: 210px;
       height: 2px;
       background-color: #C1ACAC;
       width: 80%; 
       z-index: 3;
      }

      .number {
       position: absolute;
        font-size: 18px;
        left: 30px;
        top: 240px;
        font-weight: medium;
        color: white;
        z-index: 3;
        margin: 0;
      }

      .line4 {
       position: absolute;
       left: 30px;
       top: 270px;
       height: 2px;
       background-color: #C1ACAC;
       width: 80%; 
       z-index: 3;
      }

      .email {
        position: absolute;
        font-size: 18px;
        left: 30px;
        top: 300px;
        font-weight: medium;
        color: white;
        z-index: 3;
        margin: 0;
      }

      .line5 {
       position: absolute;
       left: 30px;
       top: 330px;
       height: 2px;
       background-color: #C1ACAC;
       width: 80%; 
       z-index: 3;
      }

      .method {
        position: absolute;
        font-size: 18px;
        left: 30px;
        top: 360px;
        font-weight: medium;
        color: white;
        z-index: 3;
        margin: 0;
      }

      .paymentchosen {
        position: absolute;
        left: 30px;
        top: 420px;
        width: 97px;
        height: 29px;
        background: #5127A3;
        border-radius: 13px;
        border: 1px solid white;
        z-index: 3;
      }

      .paymentchosen::before {
        content: '₱ CASH';
        position: absolute;
        left: 10px;
        top: 5px;
        font-size: 14px;
        color: white;
        font-weight: bold;
      }
      .paymentchosen::after {
        content: '';
        position: absolute;
        right: 10px;
        top: 5px;
        width: 16px;
        height: 16px;
        background-image: url('https://cdn.glitch.global/585aee42-d89c-4ece-870c-5b01fc1bab61/check.png?v=1747830810539');
        background-size: cover;
      }
      .paymentchosen:hover {
        background-color: #6A34D6;
        cursor: pointer;
      }

      .check {
        position: absolute;
        left: 30px;
        top: 480px;
        width: 32px;
        height: 31px;
        border: 1px solid white;
        z-index: 3;
        border-radius: 6px;
      }

      .termsc {
         position: absolute;
         left: 80px;
         top: 488px;
         font-size: 12px;
         font-weight: medium;
         color: white;
         z-index: 3;
         margin: 0;
      }

      .place-order-btn {
         position: absolute;
         left: 50%;
         transform: translateX(-50%);
         bottom: 2rem;
         width: 80%;
         padding: 1rem;
         background: linear-gradient(to right, #32bbe4, #2786a3);
         color: white;
         font-size: 1.2rem;
         font-weight: bold;
         border: none;
         border-radius: 8px;
         cursor: pointer;
         transition: background 0.2s;
         box-sizing: border-box;
         z-index: 4;
      }
      .place-order-btn:hover {
         background: linear-gradient(to right, #2786a3, #32bbe4);
      }
      .place-order-btn:active {
         background: linear-gradient(to right, #1f6f8a, #1a5b6d);
      }

        /* Order Items Section */


      .orderitems {
         flex: 1;
         margin-top: 90px;
         background: white;
         border-radius: 10px;
         padding: 2rem;
         box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
          min-height: 650px; /* Match .shipping min-height */
          height: 650px;     /* Fixed height to match .shipping */
          display: flex;
          flex-direction: column;
          justify-content: space-between;
          box-sizing: border-box;
          overflow-y: auto;
        }

        .order-header {
          background: #f8f9fa;
          padding: 1rem;
          border-radius: 8px;
          margin-bottom: 1rem;
          display: grid;
          grid-template-columns: 2fr 1fr 1fr 1fr;
          gap: 1rem;
          font-weight: bold;
          color: #8D7D7D;
        }

        .order-item {
          display: grid;
          grid-template-columns: 2fr 1fr 1fr 1fr;
          gap: 1rem;
          align-items: center;
          padding: 1.5rem 1rem;
          border-bottom: 1px solid #eee;
        }

        .product-info {
          display: flex;
          align-items: center;
          gap: 1rem;
        }

        .product-image {
          width: 80px;
          height: 80px;
          object-fit: cover;
          border-radius: 8px;
        }

        .product-name {
          font-size: 1rem;
          font-weight: 500;
          color: black;
        }

        .price, .quantity-val, .subtotal {
          font-size: 1.2rem;
          font-weight: 500;
          color: black;
          text-align: center;
        }

        .total-section {
          margin-top: 2rem;
          padding-top: 1rem;
          border-top: 2px solid #5127A3;
        }

        .total-row {
          display: flex;
          justify-content: space-between;
          padding: 0.5rem 0;
          font-size: 1.1rem;
        }

        .total-final {
          font-weight: bold;
          font-size: 1.3rem;
          color: #5127A3;
        }

      /* Responsive Design */
      @media (max-width: 1200px) {
         .orderedsum {
            flex-direction: column;
            padding: 1rem;
         }
         
         .shipping {
            width: 100%;
            margin-top: 280px;
         }
         
         .orderitems {
            margin-top: 1rem;
         }
         
         .nme, .address, .number, .email, .method, .paymentchosen, .check, .termsc {
            position: relative;
            left: auto;
            top: auto;
         }
      }

      @media (max-width: 768px) {
         .header {
            margin: 10px;
            height: 200px;
         }
         
         .brand {
            font-size: 2rem;
            left: 20px;
            top: 30px;
         }
         
         .shipping {
            margin-top: 220px;
            padding: 1.5rem;
         }
         
         .order-header,
         .order-item {
            grid-template-columns: 1fr;
            text-align: center;
         }
         
         .product-info {
            justify-content: center;
         }
         
         .product-image {
            width: 60px;
            height: 60px;
         }
      }

      @media (max-width: 480px) {
         .orderedsum {
            padding: 0.5rem;
         }
         
         .shipping {
            padding: 1rem;
         }
         
         .orderitems {
            padding: 1rem;
         }
      }
   </style>
</head>
<body>
   <div class="header">
      <h1 class="brand">A&F</h1>
   </div>
   <div class="header-right">
      <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
      <i class="fas fa-user-circle"></i>
   </div>

   <div class="orderedsum">
      <div class="shipping">
         <h2 class="shipping-title">Shipping Information</h2>
         
         <h1 class="nme">Name:</h1>
         <div class="line2"></div>
         
         <h1 class="address">Address:</h1>
         <div class="line3"></div>
         
         <h1 class="number">Phone Number:</h1>
         <div class="line4"></div>
         
         <h1 class="email">Email:</h1>
         <div class="line5"></div>
         
         <h1 class="method">Payment Method:</h1>
         
         <div class="paymentchosen"></div>
         <div class="check"></div>
         <div class="termsc">I have read the terms and conditions</div>
         <button class="place-order-btn">Place Order</button>
      </div>
      
      <div class="orderitems">
         <h2 style="color: #5127A3; margin-bottom: 2rem;">Order Summary</h2>
         
         <div class="order-header">
            <div>Product</div>
            <div>Unit Price</div>
            <div>Quantity</div>
            <div>Subtotal</div>
         </div>
         
         <div class="order-item">
            <div class="product-info">
               <img src="https://cdn.glitch.global/585aee42-d89c-4ece-870c-5b01fc1bab61/spam?v=1747830810539" class="product-image" alt="SPAM">
               <span class="product-name">SPAM PORK</span>
            </div>
            <div class="price">₱115</div>
            <div class="quantity-val">1</div>
            <div class="subtotal">₱115</div>
         </div>
         
         <div class="order-item">
            <div class="product-info">
               <img src="https://cdn.glitch.global/585aee42-d89c-4ece-870c-5b01fc1bab61/icecream?v=1747830812291" class="product-image" alt="MELONA">
               <span class="product-name">MELONA</span>
            </div>
            <div class="price">₱35</div>
            <div class="quantity-val">2</div>
            <div class="subtotal">₱70</div>
         </div>
         
         <div class="total-section">
            <div class="total-row">
               <span>Subtotal:</span>
               <span>₱185</span>
            </div>
            <div class="total-row">
               <span>Shipping:</span>
               <span>₱50</span>
            </div>
            <div class="total-row total-final">
               <span>Total:</span>
               <span>₱235</span>
            </div>
         </div>
      </div>
   </div>
</body>
</html>