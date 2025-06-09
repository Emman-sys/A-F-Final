<?php
// filepath: c:\Users\ceile\A-F-Final\test_product_details.php
// Create this file to test the API

$product_id = 4; // Test with product ID 4

$data = json_encode(['product_id' => $product_id]);

$ch = curl_init('http://localhost/AFFinal/get_product_details.php');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);

$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "HTTP Code: " . $http_code . "\n";
echo "Response: " . $response . "\n";
?>