<?php
// Simple script to check database data
require_once 'vendor/autoload.php';

use Illuminate\Database\Capsule\Manager as Capsule;

// Load Laravel app
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

try {
    echo "Checking societies table...\n";

    $societies = \App\Models\Society::take(3)->get(['id', 'name', 'id_card_number']);

    if ($societies->count() > 0) {
        echo "Found " . $societies->count() . " societies:\n";
        foreach ($societies as $society) {
            echo "- ID: {$society->id}, Name: {$society->name}, ID Card: {$society->id_card_number}\n";
        }
    } else {
        echo "No societies found in database.\n";
    }

    echo "\nChecking job vacancies...\n";
    $vacancies = \App\Models\JobVacancy::take(3)->get(['id', 'company', 'description']);

    if ($vacancies->count() > 0) {
        echo "Found " . $vacancies->count() . " job vacancies:\n";
        foreach ($vacancies as $vacancy) {
            echo "- ID: {$vacancy->id}, Company: {$vacancy->company}\n";
        }
    } else {
        echo "No job vacancies found in database.\n";
    }

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
