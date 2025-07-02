<?php

require_once 'vendor/autoload.php';

// Test the API endpoints
$baseUrl = 'http://127.0.0.1:8000/api/v1';

// First, let's get a valid token by logging in a society
$loginData = [
    'nik' => '1234567890123456',  // From SocietySeeder
    'password' => 'password123'
];

echo "=== Testing A3 - Job Vacancy API Endpoints ===\n\n";

// Test login to get token
echo "1. Testing Society Login...\n";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $baseUrl . '/auth/login');
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($loginData));
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$loginResponse = curl_exec($ch);
$loginData = json_decode($loginResponse, true);

if (isset($loginData['token'])) {
    echo "✓ Login successful\n";
    echo "Token: " . $loginData['token'] . "\n\n";

    $token = $loginData['token'];

    // Test A3a - Get all job vacancies
    echo "2. Testing A3a - Get all job vacancies...\n";
    curl_setopt($ch, CURLOPT_URL, $baseUrl . '/job_vacancies?token=' . $token);
    curl_setopt($ch, CURLOPT_POST, false);
    curl_setopt($ch, CURLOPT_HTTPHEADER, []);
    $vacanciesResponse = curl_exec($ch);
    $vacanciesData = json_decode($vacanciesResponse, true);

    if ($vacanciesData && isset($vacanciesData['vacancies'])) {
        echo "✓ Successfully retrieved " . count($vacanciesData['vacancies']) . " job vacancies\n";
        if (count($vacanciesData['vacancies']) > 0) {
            echo "First vacancy: " . $vacanciesData['vacancies'][0]['company'] . "\n";
            echo "Available positions: " . count($vacanciesData['vacancies'][0]['available_position']) . "\n";
        }
    } else {
        echo "✗ Failed to retrieve job vacancies\n";
        echo "Response: " . $vacanciesResponse . "\n";
    }
    echo "\n";

    // Test A3c - Get job vacancy detail by ID
    echo "3. Testing A3c - Get job vacancy detail by ID...\n";
    curl_setopt($ch, CURLOPT_URL, $baseUrl . '/job_vacancies/1?token=' . $token);
    $vacancyDetailResponse = curl_exec($ch);
    $vacancyDetailData = json_decode($vacancyDetailResponse, true);

    if ($vacancyDetailData && isset($vacancyDetailData['vacancy'])) {
        echo "✓ Successfully retrieved job vacancy detail\n";
        echo "Company: " . $vacancyDetailData['vacancy']['company'] . "\n";
        echo "Available positions: " . count($vacancyDetailData['vacancy']['available_position']) . "\n";
    } else {
        echo "✗ Failed to retrieve job vacancy detail\n";
        echo "Response: " . $vacancyDetailResponse . "\n";
    }
    echo "\n";

    // Test getting job vacancies by category
    echo "4. Testing - Get job vacancies by category (ID=1)...\n";
    curl_setopt($ch, CURLOPT_URL, $baseUrl . '/job_vacancies/category/1?token=' . $token);
    $categoriesResponse = curl_exec($ch);
    $categoriesData = json_decode($categoriesResponse, true);

    if ($categoriesData && isset($categoriesData['vacancies'])) {
        echo "✓ Successfully retrieved job vacancies by category\n";
        echo "Total vacancies in category 1: " . count($categoriesData['vacancies']) . "\n";
    } else {
        echo "✗ Failed to retrieve job vacancies by category\n";
        echo "Response: " . $categoriesResponse . "\n";
    }
    echo "\n";

    // Test A3b/A3d - Invalid token
    echo "5. Testing A3b/A3d - Invalid token...\n";
    curl_setopt($ch, CURLOPT_URL, $baseUrl . '/job_vacancies?token=invalid_token');
    $invalidResponse = curl_exec($ch);
    $invalidData = json_decode($invalidResponse, true);

    if ($invalidData && isset($invalidData['message']) && $invalidData['message'] === 'Unauthorized user') {
        echo "✓ Invalid token properly rejected\n";
    } else {
        echo "✗ Invalid token not properly handled\n";
        echo "Response: " . $invalidResponse . "\n";
    }

} else {
    echo "✗ Login failed\n";
    echo "Response: " . $loginResponse . "\n";
}

curl_close($ch);

echo "\n=== Test completed ===\n";
