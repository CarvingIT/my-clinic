<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use App\Models\Patient;
use App\Models\FollowUp;
use Illuminate\Support\Facades\DB;

class ImportData extends Command
{
    protected $signature = 'MC:ImportData {--file=backup/backup.json}';
    protected $description = 'Import patients and follow-ups from JSON file';

    public function handle()
    {
        $filePath = $this->option('file') ?? 'backup.json';

        // Check if absolute path or not
        $isAbsolute = str_starts_with($filePath, '/') || preg_match('/^[A-Za-z]:\\\\/', $filePath); // Linux or Windows

        if ($isAbsolute) {
            // Absolute path: check if file exists
            if (!file_exists($filePath)) {
                $this->error("File not found: $filePath");
                return;
            }

            $this->info("Importing data from: $filePath");
            $json = file_get_contents($filePath);
        } else {
            // Relative path: assume it's inside Laravel storage (like 'backup/backup.json')
            if (!Storage::exists($filePath)) {
                $this->error("File not found in storage: $filePath");
                return;
            }

            $this->info("Importing data from storage: $filePath");
            $json = Storage::get($filePath);
        }

        $patients = json_decode($json, true);

        DB::transaction(function () use ($patients) {
            foreach ($patients as $patientData) {
                $followUps = $patientData['follow_ups'] ?? [];
                unset($patientData['follow_ups']);

                $existingPatient = Patient::withTrashed()->where('guid', $patientData['guid'])->first();

                if ($existingPatient) {
                    if ($existingPatient->trashed()) {
                        $existingPatient->restore(); // restores the soft-deleted record
                        $existingPatient->update($patientData); // updates with latest backup data
                        $this->info("Restored and updated soft-deleted patient: {$patientData['name']} ({$patientData['guid']})");
                    } else {
                        $this->warn("Skipped existing patient: {$patientData['name']} ({$patientData['guid']})");
                    }
                    continue;
                }

                // Create patient
                $patient = Patient::create($patientData);

                foreach ($followUps as $followUpData) {
                    $followUpData['patient_id'] = $patient->id;
                    FollowUp::create($followUpData);
                }
            }
        });

        $this->info('Import completed successfully.');
    }
}
