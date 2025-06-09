<?php
// filepath: c:\Users\ceile\A-F-Final\create_admin.php
require 'db_connect.php';

// Create admin with hashed password
$admin_fname = 'Admin';
$admin_lname = 'User';
$admin_email = 'admin@af.com';
$admin_password = password_hash('admin123', PASSWORD_DEFAULT);

$stmt = $conn->prepare("INSERT INTO admin (admin_fname, admin_lname, admin_email, admin_password) VALUES (?, ?, ?, ?) ON DUPLICATE KEY UPDATE admin_password = ?");
$stmt->bind_param("sssss", $admin_fname, $admin_lname, $admin_email, $admin_password, $admin_password);

if ($stmt->execute()) {
    echo "Admin user created successfully!<br>";
    echo "Email: admin@af.com<br>";
    echo "Password: admin123<br>";
    echo "Hashed password: " . $admin_password;
} else {
    echo "Error creating admin user: " . $conn->error;
}
?>