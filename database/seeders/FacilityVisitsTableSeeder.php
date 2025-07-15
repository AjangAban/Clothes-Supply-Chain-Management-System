<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\FacilityVisit;
use App\Models\User;
use App\Models\Vendor;

class FacilityVisitsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // $inspector = User::where('email', 'inspector@genzfashionz.com')->first();
        $vendors = Vendor::all();

        if ($vendors->isEmpty()) {
            return;
        }

        $visitTypes = ['initial', 'follow_up', 'compliance', 'quality_audit'];
        $statuses = ['scheduled', 'in_progress', 'completed'];
        $findings = ['compliant', 'non_compliant', 'minor_issues'];

        for ($i = 0; $i < 10; $i++) {
            $vendor = $vendors->random();
            $scheduledDate = now()->addDays(rand(-30, 30));
            $actualDate = rand(0, 1) ? $scheduledDate->copy()->addDays(rand(-2, 2)) : null;
            $status = $actualDate ? 'completed' : ($scheduledDate->isPast() ? 'in_progress' : 'scheduled');

            FacilityVisit::create([
                'vendor_id' => $vendor->id,
                'inspector_id' => null,
                'inspector_name' => 'System Inspector',
                'scheduled_date' => $scheduledDate,
                'actual_visit_date' => $actualDate,
                'status' => $status,
                'visit_notes' => $status === 'completed' ? 'Facility inspection completed. ' . 
                    ($findings[array_rand($findings)] === 'compliant' ? 'All standards met.' : 'Some issues found.') : null,
                'inspection_results' => $status === 'completed' ? [
                    'cleanliness' => rand(70, 100),
                    'safety' => rand(80, 100),
                    'quality_control' => rand(75, 100),
                    'documentation' => rand(70, 100)
                ] : null,
                'passed_inspection' => $status === 'completed' ? rand(0, 1) : null,
            ]);
        }
    }
} 