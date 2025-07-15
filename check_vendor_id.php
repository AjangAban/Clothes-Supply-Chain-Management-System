<?php
require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\\Contracts\\Console\\Kernel')->bootstrap();

use App\Models\VendorApplication;

$app49 = VendorApplication::find(49);
if (!$app49) {
    echo "Application 49 not found.\n";
    exit(1);
}
echo "Application 49 vendor_id: ".$app49->vendor_id."\n";
if ($app49->vendor) {
    echo "Vendor name: ".$app49->vendor->name."\n";
} else {
    echo "No vendor found for this application.\n";
} 