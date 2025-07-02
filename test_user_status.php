<?php
// Test script for user status endpoint
echo "Testing User Status AJAX Endpoint...\n";

// URL endpoint
$url = 'http://localhost/ajax/user-status';

// Test 1: Without login (should return false)
echo "\nTest 1: Without login\n";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Accept: application/json',
    'X-Requested-With: XMLHttpRequest'
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "HTTP Code: " . $httpCode . "\n";
echo "Response: " . $response . "\n";

// Test 2: With specific vacancy ID
echo "\nTest 2: With vacancy ID\n";
$urlWithVacancy = $url . '?vacancy_id=1';
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $urlWithVacancy);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Accept: application/json',
    'X-Requested-With: XMLHttpRequest'
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "HTTP Code: " . $httpCode . "\n";
echo "Response: " . $response . "\n";

echo "\nDone!\n";
?>
