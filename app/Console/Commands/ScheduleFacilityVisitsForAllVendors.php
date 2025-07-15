<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Vendor;
use App\Models\VendorApplication;
use App\Models\FacilityVisit;
use App\Notifications\FacilityVisitScheduledNotification;
use Illuminate\Support\Carbon;

class ScheduleFacilityVisitsForAllVendors extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'schedule:facility-visits-for-all';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Schedule facility visits for all approved vendors who do not already have a scheduled visit.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $vendors = Vendor::whereHas('applications', function($q) {
            $q->where('status', 'approved');
        })->get();

        $count = 0;
        foreach ($vendors as $vendor) {
            $hasVisit = $vendor->facilityVisits()->where('status', 'scheduled')->exists();
            if (!$hasVisit) {
                $scheduledDate = now()->addDays(5);
                if ($scheduledDate->isSunday()) {
                    $scheduledDate->addDay();
                }
                $visit = FacilityVisit::create([
                    'vendor_id' => $vendor->id,
                    'scheduled_date' => $scheduledDate,
                    'status' => 'scheduled',
                    'inspector_id' => null,
                    'inspector_name' => 'To be assigned',
                    'visit_notes' => 'Auto-scheduled after validation approval.',
                ]);
                if ($vendor->user) {
                    $vendor->user->notify(new FacilityVisitScheduledNotification($visit));
                }
                $this->info("Scheduled facility visit for vendor: {$vendor->name} (ID: {$vendor->id})");
                $count++;
            }
        }
        $this->info("Total facility visits scheduled: $count");
    }
} 