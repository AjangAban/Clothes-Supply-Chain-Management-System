<?php

namespace App\Jobs;

use App\Models\VendorApplication;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use App\Notifications\FacilityVisitScheduledNotification;

class ProcessVendorValidation implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $applicationId;

    /**
     * Create a new job instance.
     */
    public function __construct($applicationId)
    {
        $this->applicationId = $applicationId;
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        $application = VendorApplication::find($this->applicationId);
        if (!$application) {
            Log::error('VendorApplication not found for validation: ' . $this->applicationId);
            return;
        }

        // Set status to validating
        $application->status = 'validating';
        $application->save();

        $pdfPath = storage_path('app/public/' . $application->pdf_path);

        // --- Real HTTP call to Java server (endpoint is a placeholder) ---
        try {
            $response = Http::attach(
                'pdf', fopen($pdfPath, 'r'), 'application.pdf'
            )->post('http://localhost:8080/validate-vendor'); // TODO: Replace with real endpoint

            if ($response->successful()) {
                $result = $response->json();
                // Expected response: [
                //   'financial_stability' => float,
                //   'reputation' => float,
                //   'compliance' => float,
                //   'notes' => string,
                //   'status' => 'approved'|'rejected'
                // ]
                $financial = floatval($result['financialStability'] ?? 0);
                $reputation = floatval($result['reputation'] ?? 0);
                $compliance = floatval($result['compliance'] ?? 0);
                $allPassed = $financial >= 0.7 && $reputation >= 0.7 && $compliance >= 0.7;
                Log::info('Parsed values:', ['financial' => $financial, 'reputation' => $reputation, 'compliance' => $compliance, 'allPassed' => $allPassed]);
                $application->validation_results = json_encode([
                    'financial_stability' => $financial,
                    'reputation' => $reputation,
                    'compliance' => $compliance,
                ]);
                $application->validation_notes = $result['notes'] ?? null;
                // Add debug logging
                Log::info('Validation result from Java:', $result);
                $application->status = $allPassed ? 'approved' : 'rejected';
                $application->validated_at = Carbon::now();
                $application->save();
            } else {
                // Java server error
                $application->status = 'rejected';
                $application->validation_notes = 'Validation server error: ' . $response->body();
                $application->validated_at = Carbon::now();
                $application->save();
                Log::error('Java validation server error: ' . $response->body());
                return;
            }
        } catch (\Exception $e) {
            // Do not simulate approval. Mark as pending and log the error.
            Log::error('Java validation server unreachable, cannot validate application: ' . $e->getMessage());

            $application->validation_results = null;
            $application->validation_notes = 'Validation failed: Java validation server is unavailable. Please try again later.';
            $application->status = 'pending'; // Or 'rejected' if you prefer
            $application->validated_at = null;
            $application->save();
        }

        // Auto-schedule facility visit if approved and no visit exists
        if ($application->status === 'approved') {
            $vendor = $application->vendor;
            if (!$vendor) {
                Log::error('No vendor found for application ID: ' . $application->id);
                return;
            }
            $existingVisit = $vendor->facilityVisits()->where('status', 'scheduled')->first();
            if (!$existingVisit) {
                $scheduledDate = now()->addDays(5);
                if ($scheduledDate->isSunday()) {
                    $scheduledDate->addDay(); // Move to Monday
                }
                Log::info('Attempting to create facility visit', [
                    'vendor_id' => $vendor ? $vendor->id : null,
                    'application_id' => $application->id,
                    'scheduled_date' => $scheduledDate
                ]);
                
                try {
                $visit = \App\Models\FacilityVisit::create([
                    'vendor_id' => $vendor->id,
                    'scheduled_date' => $scheduledDate,
                    'status' => 'scheduled',
                    'inspector_id' => null,
                        'inspector_name' => 'To be assigned',
                    'visit_notes' => 'Auto-scheduled after validation approval.',
                ]);
                    Log::info('Facility visit created successfully', [
                        'vendor_id' => $vendor->id, 
                        'visit_id' => $visit->id,
                        'scheduled_date' => $scheduledDate
                    ]);
                    
                    // Send notification to vendor
                    if ($vendor->user) {
                        $vendor->user->notify(new FacilityVisitScheduledNotification($visit));
                        Log::info('Facility visit notification sent to vendor', [
                            'vendor_id' => $vendor->id,
                            'user_id' => $vendor->user->id
                        ]);
                    }
                } catch (\Exception $e) {
                    Log::error('Failed to create facility visit', [
                        'vendor_id' => $vendor->id,
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString()
                    ]);
                }
            } else {
                Log::info('Facility visit already exists for vendor', [
                    'vendor_id' => $vendor->id,
                    'existing_visit_id' => $existingVisit->id
                ]);
            }
        }
    }
} 