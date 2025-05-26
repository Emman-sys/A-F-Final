<?php
require 'db_connect.php';

$message = "";

// Handle login form submission
if (isset($_POST["login_submit"])) {
  session_start();
  $email = trim($_POST["email"]);
  $password = $_POST["password"];

  $stmt = $conn->prepare("SELECT user_id, password_hash FROM users WHERE email = ?");
  if ($stmt === false) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $conn->error]);
    exit();
  }

  $stmt->bind_param("s", $email);
  $stmt->execute();
  $stmt->store_result();

  if ($stmt->num_rows > 0) {
    $stmt->bind_result($user_id, $stored_password);
    $stmt->fetch();
    
    // Check if password is hashed
    $is_hashed = strlen($stored_password) === 60 && substr($stored_password, 0, 4) === '$2y$';
    
    if ($is_hashed) {
      // Password is hashed, use password_verify
      if (password_verify($password, $stored_password)) {
        $_SESSION["user_id"] = $user_id;
        $_SESSION["login_success"] = true;
        echo json_encode(['success' => true, 'redirect' => 'Userdashboard.php']);
        exit();
      } else {
        $message = "Invalid password.";
      }
    } else {
      // Password is plain text, do direct comparison
      if ($password === $stored_password) {
        $_SESSION["user_id"] = $user_id;
        $_SESSION["login_success"] = true;
        echo json_encode(['success' => true, 'redirect' => 'Userdashboard.php']);
        exit();
      } else {
        $message = "Invalid password.";
      }
    }
  } else {
    $message = "User not found.";
  }
  $stmt->close();
  
  echo json_encode(['success' => false, 'message' => $message]);
  exit();
}
?>