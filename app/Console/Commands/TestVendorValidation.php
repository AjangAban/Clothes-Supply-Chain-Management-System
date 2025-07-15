<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Jobs\ProcessVendorValidation;
use App\Models\VendorApplication;
use Illuminate\Support\Facades\Log;

class TestVendorValidation extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:vendor-validation {application_id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test vendor validation process for a specific application';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $applicationId = $this->argument('application_id');
        
        $application = VendorApplication::find($applicationId);
        
        if (!$application) {
            $this->error("Application with ID {$applicationId} not found.");
            return 1;
        }
        
        $this->info("Testing validation for application ID: {$applicationId}");
        $this->info("Vendor: " . ($application->vendor ? $application->vendor->name : 'No vendor'));
        $this->info("Current status: {$application->status}");
        
        // Dispatch the validation job
        ProcessVendorValidation::dispatch($applicationId);
        
        $this->info("Validation job dispatched. Check the logs for results.");
        $this->info("You can check the vendor dashboard to see if a facility visit was created.");
        
        return 0;
    }
} 