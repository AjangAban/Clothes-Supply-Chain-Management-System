<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\QualityCheck;
use App\Models\User;
use App\Models\ProductionOrder;

class QualityChecksTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // No inspector user exists, so we'll create quality checks without inspector

        // Get actual production order IDs that exist
        $productionOrderIds = ProductionOrder::pluck('id')->toArray();
        
        if (empty($productionOrderIds)) {
            return; // No production orders exist
        }

        $checkTypes = ['in_process', 'final', 'random', 'customer_return'];
        $checkPoints = ['cutting', 'sewing', 'finishing', 'packaging'];
        $passFailOptions = ['pass', 'fail', 'conditional_pass', 'pending'];

        // Create multiple quality checks for the inspector
        for ($i = 0; $i < 15; $i++) {
            $checkType = $checkTypes[array_rand($checkTypes)];
            $checkPoint = $checkPoints[array_rand($checkPoints)];
            $passFail = $passFailOptions[array_rand($passFailOptions)];
            $sampleSize = rand(10, 50);
            $defectsFound = $passFail === 'pass' ? rand(0, 2) : rand(3, 8);
            $qualityScore = $passFail === 'pass' ? rand(85, 100) : rand(60, 84);
            
            $defectTypes = [];
            if ($defectsFound > 0) {
                $defectTypes = [
                    'stitching_error' => rand(1, $defectsFound),
                    'fabric_defect' => rand(0, $defectsFound),
                    'measurement_error' => rand(0, $defectsFound)
                ];
            }

        QualityCheck::create([
                'production_order_id' => $productionOrderIds[array_rand($productionOrderIds)],
                'check_type' => $checkType,
                'check_point' => $checkPoint,
                'inspector_id' => null,
                'sample_size' => $sampleSize,
                'defects_found' => $defectsFound,
                'defect_types' => json_encode($defectTypes),
                'quality_score' => $qualityScore,
                'pass_fail' => $passFail,
                'check_date' => now()->subDays(rand(0, 30)),
                'notes' => $this->generateNotes($checkType, $checkPoint, $passFail),
                'corrective_actions' => $passFail === 'fail' ? 'Immediate corrective action required' : null,
                'recheck_required' => $passFail === 'fail',
                'recheck_date' => $passFail === 'fail' ? now()->addDays(rand(1, 7)) : null,
            'recheck_inspector_id' => null,
            'recheck_result' => null,
                'is_critical' => rand(0, 1),
                'vendor_id' => rand(1, 2),
            ]);
        }
    }

    private function generateNotes($checkType, $checkPoint, $passFail)
    {
        $notes = [
            'pass' => [
                'Quality standards met. All samples passed inspection.',
                'Excellent work quality. No defects found.',
                'Production meets all quality requirements.'
            ],
            'fail' => [
                'Multiple defects found. Production halted until issues resolved.',
                'Quality standards not met. Immediate action required.',
                'Significant quality issues detected.'
            ],
            'conditional_pass' => [
                'Minor issues found but within acceptable limits.',
                'Passed with minor corrections needed.',
                'Conditional approval with follow-up required.'
            ],
            'pending' => [
                'Inspection in progress.',
                'Awaiting final quality assessment.',
                'Quality check scheduled.'
            ]
        ];

        return $notes[$passFail][array_rand($notes[$passFail])];
    }
}
