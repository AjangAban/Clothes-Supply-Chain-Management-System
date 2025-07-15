<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Vendor;
use App\Models\FacilityVisit;

class CheckFacilityVisits extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check:facility-visits {vendor_id?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check facility visits for a vendor';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $vendorId = $this->argument('vendor_id');
        
        if ($vendorId) {
            $vendor = Vendor::find($vendorId);
            
            if (!$vendor) {
                $this->error("Vendor with ID {$vendorId} not found.");
                return 1;
            }
            
            $this->info("Facility visits for vendor: {$vendor->name}");
            $visits = $vendor->facilityVisits()->orderBy('created_at', 'desc')->get();
        } else {
            $this->info("All facility visits:");
            $visits = FacilityVisit::with('vendor')->orderBy('created_at', 'desc')->get();
        }
        
        if ($visits->isEmpty()) {
            $this->warn("No facility visits found.");
            return 0;
        }
        
        $headers = ['ID', 'Vendor', 'Status', 'Scheduled Date', 'Inspector', 'Created At'];
        $rows = [];
        
        foreach ($visits as $visit) {
            $rows[] = [
                $visit->id,
                $visit->vendor ? $visit->vendor->name : 'N/A',
                $visit->status,
                $visit->scheduled_date ? $visit->scheduled_date->format('M d, Y') : 'N/A',
                $visit->inspector_name ?: 'N/A',
                $visit->created_at->format('M d, Y H:i:s')
            ];
        }
        
        $this->table($headers, $rows);
        
        return 0;
    }
} 