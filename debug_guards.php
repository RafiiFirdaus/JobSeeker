<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';

try {
    $authManager = $app->make('auth');

    echo "Available guards:\n";
    foreach (config('auth.guards') as $name => $config) {
        echo "- $name\n";
    }

    echo "\nDefault guard: " . config('auth.defaults.guard') . "\n";

    // Try to see if any other guards are being registered
    $guards = $authManager->getGuards();
    echo "\nRegistered guards:\n";
    foreach ($guards as $name => $guard) {
        echo "- $name: " . get_class($guard) . "\n";
    }

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}
