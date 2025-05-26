<?php
require 'db_connect.php';

$message = "";

// Handle signup form submission
if (isset($_POST["signup_submit"])) {
  $name = trim($_POST["username"]);
  $email = trim($_POST["email"]);
  $password = password_hash($_POST["password"], PASSWORD_DEFAULT);
  $phone = trim($_POST["phone"]);
  $address = trim($_POST["address"]);

  // Check if connection exists
  if (!$conn) {
    echo json_encode(['success' => false, 'message' => 'Database connection failed.']);
    exit();
  }

  $stmt = $conn->prepare("INSERT INTO users (name, email, password_hash, phone_number, address) VALUES (?, ?, ?, ?, ?)");
  
  if ($stmt === false) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $conn->error]);
    exit();
  }

  $stmt->bind_param("sssss", $name, $email, $password, $phone, $address);

  if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Registration successful!']);
  } else {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $stmt->error]);
  }
  $stmt->close();
  exit();
}
?>