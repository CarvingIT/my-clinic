<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use App\Models\Patient;
use App\Models\FollowUp;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

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

        $timezone = config('app.timezone', 'Asia/Kolkata');

        DB::transaction(function () use ($patients, $timezone) {
            foreach ($patients as $patientData) {
                $followUps = $patientData['follow_ups'] ?? [];
                unset($patientData['follow_ups']);

                // Normalize patient timestamps before comparison
                $patientData['updated_at'] = Carbon::parse($patientData['updated_at'])->setTimezone($timezone)->toDateTimeString();

                $existingPatient = Patient::withTrashed()->where('guid', $patientData['guid'])->first();

                if ($existingPatient) {
                    $wasUpdated = false;

                    // Normalize existing patient updated_at
                    $existingPatientUpdatedAt = Carbon::parse($existingPatient->updated_at)->setTimezone($timezone);

                    if ($existingPatientUpdatedAt->lessThan(Carbon::parse($patientData['updated_at']))) {
                        $existingPatient->update($patientData);
                        $wasUpdated = true;
                    }

                    if ($existingPatient->trashed()) {
                        $existingPatient->restore();
                        $this->info("Restored soft-deleted patient: {$patientData['name']}");
                    }

                    if ($wasUpdated) {
                        $this->info("Updated patient: {$patientData['name']} ({$patientData['guid']})");
                    } else {
                        $this->info("Patient already up-to-date: {$patientData['name']} ({$patientData['guid']})");
                    }

                    // Process follow-ups
                    foreach ($followUps as $followUpData) {
                        // Normalize follow-up timestamps before comparison
                        $followUpData['created_at'] = Carbon::parse($followUpData['created_at'])->setTimezone($timezone)->toDateTimeString();
                        $followUpData['updated_at'] = Carbon::parse($followUpData['updated_at'])->setTimezone($timezone)->toDateTimeString();

                        $existingFollowUp = FollowUp::where('patient_id', $existingPatient->id)
                            ->where('created_at', $followUpData['created_at'])
                            ->first();

                        if ($existingFollowUp) {
                            $existingFollowUpUpdatedAt = Carbon::parse($existingFollowUp->updated_at)->setTimezone($timezone);

                            if ($existingFollowUpUpdatedAt->lessThan(Carbon::parse($followUpData['updated_at']))) {
                                $existingFollowUp->update($followUpData);
                                $this->info("Updated follow-up for patient: {$patientData['name']}");
                            } else {
                                $this->info("Follow-up already up-to-date for patient: {$patientData['name']}");
                            }
                        } else {
                            $followUpData['patient_id'] = $existingPatient->id;
                            FollowUp::create($followUpData);
                            $this->info("Added new follow-up for existing patient: {$patientData['name']}");
                        }
                    }

                    continue; // Next patient
                }

                // New patient: normalize timestamps before create
                $patientData['created_at'] = Carbon::parse($patientData['created_at'])->setTimezone($timezone)->toDateTimeString();
                $patientData['updated_at'] = Carbon::parse($patientData['updated_at'])->setTimezone($timezone)->toDateTimeString();

                $patient = Patient::create($patientData);

                foreach ($followUps as $followUpData) {
                    $followUpData['created_at'] = Carbon::parse($followUpData['created_at'])->setTimezone($timezone)->toDateTimeString();
                    $followUpData['updated_at'] = Carbon::parse($followUpData['updated_at'])->setTimezone($timezone)->toDateTimeString();

                    $followUpData['patient_id'] = $patient->id;
                    FollowUp::create($followUpData);
                }
            }
        });

        $this->info('Import completed successfully.');
    }
}
