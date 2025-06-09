<?php
// filepath: c:\Users\ceile\A-F-Final\test_product_details.php
// Test with product ID 4 (70% Dark Chocolate Bar)

$product_id = 4;

echo "=== TESTING GET_PRODUCT_DETAILS.PHP ===\n";
echo "Testing with Product ID: $product_id (70% Dark Chocolate Bar)\n\n";

$data = json_encode(['product_id' => $product_id]);

$ch = curl_init('http://localhost/AFFinal/get_product_details.php');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);

$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curl_error = curl_error($ch);
curl_close($ch);

echo "HTTP Code: " . $http_code . "\n";

if ($curl_error) {
    echo "CURL Error: " . $curl_error . "\n";
}

echo "Response: " . $response . "\n";

// Try to decode the JSON response
if ($response) {
    $decoded = json_decode($response, true);
    if ($decoded) {
        echo "\nDecoded Response:\n";
        print_r($decoded);
    } else {
        echo "\nFailed to decode JSON. Raw response:\n";
        echo $response . "\n";
    }
}
?>