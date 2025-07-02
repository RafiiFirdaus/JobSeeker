<?php
// Test script to directly access AJAX endpoints

session_start();

// Simulate logged-in session
$_SESSION['user_logged_in'] = true;
$_SESSION['user_id'] = 1;
$_SESSION['user_name'] = 'Test User';

// Test the applications endpoint directly
echo "Testing /ajax/applications endpoint...\n";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'http://127.0.0.1:8000/ajax/applications');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Accept: application/json',
    'X-Requested-With: XMLHttpRequest'
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "HTTP Code: $httpCode\n";
echo "Response: $response\n";
