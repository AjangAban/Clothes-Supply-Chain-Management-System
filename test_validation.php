<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Jobs\ProcessVendorValidation;

echo "Testing vendor validation for application ID: 49\n";

$job = new ProcessVendorValidation(49);
$job->handle();

echo "Job completed. Check the database for results.\n"; 