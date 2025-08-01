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
        $file = $this->option('file') ?? 'backup.json';

        if (!Storage::exists($file)) {
            $this->error("File not found: storage/app/$file");
            return;
        }

        $this->info("Importing data from: $file");

        $json = Storage::get($file);
        $patients = json_decode($json, true);

        DB::transaction(function () use ($patients) {
            foreach ($patients as $patientData) {
                $followUps = $patientData['follow_ups'] ?? [];
                unset($patientData['follow_ups']);

                // Avoid inserting duplicate patient (based on guid)
                // $existingPatient = Patient::where('guid', $patientData['guid'])->first();
                $existingPatient = Patient::withTrashed()->where('guid', $patientData['guid'])->first();

                // if ($existingPatient) {
                //     $this->warn("Skipped existing patient: {$patientData['name']} ({$patientData['guid']})");
                //     continue;
                // }

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
