<?php

namespace App\Console\Commands;

use App\Models\FollowUp;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class RemoveDuplicateFollowUps extends Command
{
    protected $signature = 'followups:remove-duplicates';
    protected $description = 'Remove duplicate follow-ups based on patient_id and created_at in Asia/Kolkata timezone';

    public function handle()
    {
        // Find duplicates by grouping on patient_id and created_at (normalized to IST)
        $duplicates = FollowUp::select('patient_id', 'created_at')
            ->groupBy('patient_id', 'created_at')
            ->havingRaw('COUNT(*) > 1')
            ->get();

        $deletedCount = 0;

        foreach ($duplicates as $duplicate) {
            // Normalize database created_at to Asia/Kolkata
            $normalizedCreatedAt = Carbon::parse($duplicate->created_at)->setTimezone('Asia/Kolkata')->format('Y-m-d H:i:s');

            // Log the duplicate group
            Log::info('Found duplicate follow-up group', [
                'patient_id' => $duplicate->patient_id,
                'created_at' => $normalizedCreatedAt,
            ]);

            // Get all follow-ups for this patient_id and created_at
            $followUps = FollowUp::where('patient_id', $duplicate->patient_id)
                ->where('created_at', $normalizedCreatedAt)
                ->orderBy('id', 'asc')
                ->get();

            // Keep the first record, delete the rest
            $keepFirst = true;
            foreach ($followUps as $followUp) {
                if ($keepFirst) {
                    Log::info('Keeping follow-up', [
                        'id' => $followUp->id,
                        'patient_id' => $followUp->patient_id,
                        'created_at' => $normalizedCreatedAt,
                    ]);
                    $keepFirst = false;
                    continue;
                }

                Log::info('Deleting duplicate follow-up', [
                    'id' => $followUp->id,
                    'patient_id' => $followUp->patient_id,
                    'created_at' => $normalizedCreatedAt,
                ]);
                $followUp->delete();
                $deletedCount++;
            }
        }

        Log::info('Duplicate follow-up removal completed', [
            'duplicates_found' => $duplicates->count(),
            'deleted_count' => $deletedCount,
        ]);

        $this->info("Removed {$deletedCount} duplicate follow-ups. Found {$duplicates->count()} duplicate groups.");
    }
}

// php artisan followups:remove-duplicates
