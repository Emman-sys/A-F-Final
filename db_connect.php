<?php
$servername = "127.0.0.1";
$username = "root";
$password = ""; // XAMPP default
$dbname = "a&f chocolate"; // Use your actual database name

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}// else {
//  echo "Connection successful!";
//}
?>