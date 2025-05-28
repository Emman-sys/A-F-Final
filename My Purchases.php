<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <link href="https://fonts.googleapis.com/css2?family=Merriweather:wght@400;700&family=Merriweather+Sans:wght@700&display=swap" rel="stylesheet" />
   <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css">
   <link rel="stylesheet" href="styles.css">
  <title>My Purchases</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
  <style>
    * {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
    }

    body {
      font-family: Merriweather;
      background-color: #fbcdfb;
    }

    .header {
      background-color: #efc7cb;
      padding: 20px 30px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      border-bottom: 2px solid #333;
    }

    .header-left h1 {
      font-family: Merriweather;
      font-size: 32px;
      font-weight: bold;
      display: inline;
    }

    .header-left span {
      font-size: 20px;
      margin-left: 10px;
    }

    .header-right i {
      font-size: 26px;
    }

    .main {
      display: flex;
      padding: 20px;
      height: calc(100vh - 90px); /* Full height minus header */
    }

    .sidebar {
      background-color: #4b00b3;
      color: white;
      width: 300px;
      border-radius: 10px;
      padding: 20px;
      margin-right: 20px;
    }

    .sidebar h2 {
      margin-bottom: 20px;
      font-size: 22px;
    }

    .sidebar label {
      font-family: Merriweather;
      display: block;
      margin-bottom: 100px;
      font-weight: bold;
    }

    .sidebar input {
      width: 100%;
      padding: 6px 10px;
      margin-bottom: 15px;
      border: none;
      border-bottom: 2px solid cyan;
      background-color: transparent;
      color: white;
      font-size: 14px;
    }

    .sidebar input:focus {
      outline: none;
    }

    .content {
      flex: 1;
      background-color: #fff;
      border-radius: 10px;
      overflow-y: auto;
      padding: 10px;
    }

    .table-header {
      display: flex;
      justify-content: space-between;
      padding: 15px;
      font-weight: bold;
      border-bottom: 2px solid #444;
      background-color: #fff;
    }

    .purchase-row {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 20px 15px;
      border-bottom: 1px solid #ccc;
      background-color: #fff;
    }

    .purchase-left {
      display: flex;
      align-items: center;
      gap: 15px;
    }

    .purchase-left i {
      font-size: 40px;
      color: purple;
    }

    .order-number {
      margin-top: 5px;
      font-size: 14px;
      color: #333;
    }

    .items,
    .summary {
      font-weight: bold;
      font-size: 16px;
    }

    @media (max-width: 768px) {
      .main {
        flex-direction: column;
      }

      .sidebar {
        width: 100%;
        margin-bottom: 20px;
      }

      .table-header,
      .purchase-row {
        flex-direction: column;
        align-items: flex-start;
      }

      .purchase-left {
        margin-bottom: 10px;
      }
    }
  </style>
</head>
<body>
  <div class="header">
    <div class="header-left">
      <h1>A&F</h1>
      <span>| My Purchases</span>
    </div>
    <div class="header-right">
      <i class="fas fa-user-circle"></i>
    </div>
  </div>

  <div class="main">
    <div class="sidebar">
      <h2>User Info</h2>
        <label><strong>Name:</strong> Ceile Guce</label>
        <label><strong>Address:</strong> (Placeholder)</label>
        <label><strong>Phone Number:</strong> (Placeholder)</label>
        <label><strong>Email:</strong> placeholder@gmail.com</label>
    </div>

    <div class="content">
      <div class="table-header">
        <div>Purchases</div>
        <div>ITEMS</div>
        <div>ORDER SUMMARY</div>
      </div>

      <div class="purchase-row">
        <div class="purchase-left">
          <i class="fas fa-cart-shopping"></i>
          <div class="order-number">Order No. 20</div>
        </div>
        <div class="items">ITEMS</div>
        <div class="summary">ORDER SUMMARY</div>
      </div>

      <div class="purchase-row">
        <div class="purchase-left">
          <i class="fas fa-cart-shopping"></i>
          <div class="order-number">Order No. 19</div>
        </div>
        <div class="items">ITEMS</div>
        <div class="summary">ORDER SUMMARY</div>
      </div>

      <div class="purchase-row">
        <div class="purchase-left">
          <i class="fas fa-cart-shopping"></i>
          <div class="order-number">Order No. 18</div>
        </div>
        <div class="items">ITEMS</div>
        <div class="summary">ORDER SUMMARY</div>
      </div>

      <div class="purchase-row">
        <div class="purchase-left">
          <i class="fas fa-cart-shopping"></i>
          <div class="order-number">Order No. 17</div>
        </div>
        <div class="items">ITEMS</div>
        <div class="summary">ORDER SUMMARY</div>
      </div>

      <div class="purchase-row">
        <div class="purchase-left">
          <i class="fas fa-cart-shopping"></i>
          <div class="order-number">Order No. 16</div>
        </div>
        <div class="items">ITEMS</div>
        <div class="summary">ORDER SUMMARY</div>
      </div>

      <div class="purchase-row">
        <div class="purchase-left">
          <i class="fas fa-cart-shopping"></i>
          <div class="order-number">Order No. 15</div>
        </div>
        <div class="items">ITEMS</div>
        <div class="summary">ORDER SUMMARY</div>
      </div>

      <div class="purchase-row">
        <div class="purchase-left">
          <i class="fas fa-cart-shopping"></i>
          <div class="order-number">Order No. 14</div>
        </div>
        <div class="items">ITEMS</div>
        <div class="summary">ORDER SUMMARY</div>
      </div>

      <div class="purchase-row">
        <div class="purchase-left">
          <i class="fas fa-cart-shopping"></i>
          <div class="order-number">Order No. 13</div>
        </div>
        <div class="items">ITEMS</div>
        <div class="summary">ORDER SUMMARY</div>
      </div>
      
    </div>
  </div>
</body>
</html>
