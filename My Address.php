<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <link href="https://fonts.googleapis.com/css2?family=Merriweather:wght@400;700&family=Merriweather+Sans:wght@700&display=swap" rel="stylesheet" />
   <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css">
   <link rel="stylesheet" href="styles.css">
  <title>My Address</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/> 
  <style>
    body {
      margin: 0;
      font-family: 'Merriweather';
      background-color: #4b00b3;
      background-image: url("https://cdn.glitch.global/585aee42-d89c-4ece-870c-5b01fc1bab61/image%203.png?v=1747320934399");
      background-size: cover;
      background-repeat: no-repeat;
    }

    .container {
      background-color: #fbcdfb;
      width: 90vw;
      max-width: 1200px;
      margin: 40px auto; /* Top margin to move it down a bit */
      border-radius: 8px;
      overflow: hidden;
      box-shadow: 0 4px 10px rgba(0,0,0,0.2);
    }

    .header {
      background: linear-gradient(to top, #5127A3,#986C93, #E0B083);
      padding: 20px;
      display: flex;
      align-items: center;
    }

    .header h1 {
      margin: 0;
      font-size: 28px;
      font-weight: bold;
      font-family: 'Merriweather';
      
    }

    .header span {
      margin-left: 10px;
      font-size: 18px;
      font-weight: bold;
    }

    .back-btn {
      background: none;
      border: none;
      color: #000000;
      font-size: 22px;
      margin-right: 16px;
      cursor: pointer;
      display: flex;
      align-items: center;
      transition: color 0.2s;
    }
    .back-btn:hover {
      color: #E0B083;
    }
    .back-btn i {
      font-size: 22px;
    }
    .back-btn:hover i {
      color: #E0B083;
    }
    .back-btn:focus {
      outline: none;
    }

    .address-card {
      background: #fff;
      padding: 20px;
      margin: 20px;
      border-radius: 5px;
      cursor: pointer;
    }
    
    .address-card:hover {
      background-color: #eee;
      box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }

    .location-icon {
      color: purple;
      font-size: 22px;
      display: inline-block;
      vertical-align: middle;
    }

    .default-label {
      color: purple;
      font-size: 14px;
      margin-top: 5px;
    }

    .name-phone {
      font-weight: bold;
      margin-top: 10px;
    }

    .phone {
      font-style: italic;
      font-weight: normal;
    }

    .add-address {
      background: #fff;
      text-align: center;
      padding: 15px;
      margin: 0 20px 20px 20px;
      border-radius: 5px;
      cursor: pointer;
      font-weight: bold;
      font-size: 16px;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 8px;
    }

    .add-address:hover {
      background-color: #eee;
      box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }

    @media (max-width: 768px) {
      .header h1 {
        font-size: 24px;
      }

      .header span {
        font-size: 16px;
      }

      .add-address {
        flex-direction: column;
      }
    }
  </style>
  <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
</head>
<body>
  <div class="container">
    <div class="header">
  <button class="back-btn" onclick="window.history.back()">
    <i class="fas fa-arrow-left"></i>
  </button>
  <h1>A&F</h1>
  <span>| My Address</span>
</div>

    <div class="address-card">
      <div class="location-icon"><i class="fas fa-map-marker-alt"></i></div>
      <div class="default-label">Default</div>
      <div class="name-phone">
        Ceile Guce | <span class="phone">Phone Number (PlaceHolder)</span>
      </div>
      <div>Full Address (Place Holder)</div>
    </div>

    <div class="add-address">
      <i class="fas fa-plus-circle"></i>
      Add New Address
    </div>
  </div>
</body>
</html>
