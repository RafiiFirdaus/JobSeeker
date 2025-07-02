<?php

// Test the AJAX job vacancies endpoint
echo "Testing /ajax/job-vacancies endpoint...\n";

// Start a session to simulate logged-in user
session_start();
$_SESSION['user_logged_in'] = true;
$_SESSION['user_id'] = 1;

// Test with curl
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'http://127.0.0.1:8000/ajax/job-vacancies');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Accept: application/json',
    'X-Requested-With: XMLHttpRequest',
    'X-CSRF-TOKEN: test' // Usually would be from meta tag
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "HTTP Code: $httpCode\n";
echo "Response: $response\n";
