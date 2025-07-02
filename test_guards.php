<?php

// Simple test to check if guards are working
echo "Testing guard configuration:\n";

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';

$config = $app['config'];
$guards = $config->get('auth.guards');

echo "Available guards:\n";
foreach ($guards as $name => $guard) {
    echo "- $name: " . json_encode($guard) . "\n";
}

echo "\nTesting Auth::guard('society'):\n";
try {
    $societyGuard = app('auth')->guard('society');
    echo "Society guard created successfully: " . get_class($societyGuard) . "\n";
} catch (Exception $e) {
    echo "Error creating society guard: " . $e->getMessage() . "\n";
}
