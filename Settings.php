<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <link href="https://fonts.googleapis.com/css2?family=Merriweather:wght@400;700&family=Merriweather+Sans:wght@700&display=swap" rel="stylesheet" />
  <title>Settings - A&F</title>
  <link
    rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"
  />
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      font-family: Merriweather;
      background-color: #4b00b3;
    }

    .container {
      background-color: #fbcdfb;
      border-radius: 10px;
      max-width: 1200px;
      margin: 20px auto;
      padding-bottom: 50px;
      height: calc(100vh - 40px);
    }

    .header {
      background-color: #bca5a5;
      display: flex;
      align-items: center;
      padding: 25px 40px;
      border-bottom: 3px solid #555;
    }

    .header h1 {
      font-size: 32px;
      font-family: serif;
      font-weight: bold;
    }

    .header span {
      margin-left: 15px;
      font-size: 20px;
      font-weight: bold;
      color: #111;
    }

    .section-label {
      color: #7a5b8b;
      font-weight: bold;
      font-size: 18px;
      margin: 40px 60px 10px;
    }

    .settings-grid {
      display: flex;
      justify-content: space-between;
      padding: 0 60px;
      flex-wrap: wrap;
    }

    .column {
      display: flex;
      flex-direction: column;
      gap: 20px;
      width: 48%;
    }

    .setting-box {
      background-color: white;
      border-radius: 4px;
      box-shadow: 2px 2px 4px #999;
      padding: 15px 20px;
      font-weight: bold;
      font-size: 18px;
      display: flex;
      align-items: center;
      gap: 15px;
      cursor: pointer;
      transition: background 0.2s ease;
    }

    .setting-box:hover {
      background-color: #f1f1f1;
    }

    .setting-box i {
      font-size: 22px;
    }

    @media (max-width: 768px) {
      .settings-grid {
        flex-direction: column;
      }

      .column {
        width: 100%;
      }

      .section-label {
        margin: 30px 30px 10px;
      }

      .header {
        flex-direction: column;
        align-items: flex-start;
      }

      .header h1 {
        font-size: 28px;
      }

      .header span {
        margin-left: 0;
        margin-top: 5px;
      }
    }
  </style>
</head>
<body>
  <div class="container">
    <div class="header">
      <h1>A&F</h1>
      <span>Settings</span>
    </div>

    <div class="settings-grid">
      <div class="column">
        <div class="section-label">My Account</div>
        <div class="setting-box"><i class="fas fa-user-circle"></i> Profile</div>
        <div class="setting-box"><i class="fas fa-lock"></i> Change Password</div>
        <div class="setting-box"><i class="fas fa-credit-card"></i> Payment Method</div>
        <div class="setting-box"><i class="fas fa-user"></i> Privacy</div>
      </div>

      <div class="column">
        <div class="section-label">Other</div>
        <div class="setting-box"><i class="fas fa-bell"></i> Notification</div>
        <div class="setting-box"><i class="fas fa-globe"></i> Language</div>
        <div class="setting-box"><i class="fas fa-desktop"></i> Customer Service</div>
        <div class="setting-box"><i class="fas fa-question-circle"></i> About Us</div>
      </div>
    </div>
  </div>
</body>
</html>