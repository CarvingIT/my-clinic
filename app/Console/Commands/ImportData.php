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

        // JSON validation
        if (json_last_error() !== JSON_ERROR_NONE) {
            $this->warn("Skipping file: $filePath (Invalid JSON: " . json_last_error_msg() . ")");
            return;
        }

        if (!is_array($patients)) {
            $this->warn("Skipping file: $filePath (Data is not an array of patients)");
            return;
        }

        $timezone = config('app.timezone', 'Asia/Kolkata');

        // Summary counters and name collectors
        $importedPatientsCount = $updatedPatientsCount = $skippedPatientsCount = 0;
        $newFollowUpsCount = $updatedFollowUpsCount = $skippedFollowUpsCount = 0;
        $patientsRestored = 0;

        $importedPatientNames = [];
        $updatedPatientNames = [];
        $skippedPatientNames = [];
        $addedFollowUpNames = [];
        $updatedFollowUpNames = [];
        $skippedFollowUpNames = [];
        $restoredPatientNames = [];

        DB::transaction(function () use (
            $patients,
            $timezone,
            &$importedPatientsCount,
            &$updatedPatientsCount,
            &$skippedPatientsCount,
            &$newFollowUpsCount,
            &$updatedFollowUpsCount,
            &$skippedFollowUpsCount,
            &$importedPatientNames,
            &$updatedPatientNames,
            &$skippedPatientNames,
            &$addedFollowUpNames,
            &$updatedFollowUpNames,
            &$skippedFollowUpNames,
            &$patientsRestored,
            &$restoredPatientNames
        ) {
            foreach ($patients as $patientData) {
                // Patient-level validation
                if (
                    empty($patientData['guid']) ||
                    empty($patientData['created_at']) ||
                    empty($patientData['updated_at']) ||
                    !isset($patientData['name'])
                ) {
                    $this->warn("Skipping patient: missing required fields - " . json_encode($patientData));
                    $skippedPatientsCount++;
                    $skippedPatientNames[] = $patientData['name'] ?? 'Unknown';
                    continue;
                }

                try {
                    $patientData['created_at'] = Carbon::parse($patientData['created_at'])->setTimezone($timezone)->toDateTimeString();
                    $patientData['updated_at'] = Carbon::parse($patientData['updated_at'])->setTimezone($timezone)->toDateTimeString();
                } catch (\Exception $e) {
                    $this->warn("Skipping patient: invalid date format for GUID {$patientData['guid']}");
                    $skippedPatientsCount++;
                    $skippedPatientNames[] = $patientData['name'] ?? 'Unknown';
                    continue;
                }

                $followUps = $patientData['follow_ups'] ?? [];
                unset($patientData['follow_ups']);
                $name = $patientData['name'] ?? 'Unknown';

                $existingPatient = Patient::withTrashed()->where('guid', $patientData['guid'])->first();

                if ($existingPatient) {
                    try {
                        $existingPatientUpdatedAt = Carbon::parse($existingPatient->updated_at)->setTimezone($timezone);

                        if ($existingPatientUpdatedAt->lessThan(Carbon::parse($patientData['updated_at']))) {
                            $existingPatient->update($patientData);
                            $updatedPatientsCount++;
                            $updatedPatientNames[] = $name;
                        } else {
                            $skippedPatientsCount++;
                            $skippedPatientNames[] = $name;
                        }

                        if ($existingPatient->trashed()) {
                            $existingPatient->restore();
                            $patientsRestored++;
                            $restoredPatientNames[] = $name;
                            // $this->info("Restored soft-deleted patient: {$name}");
                        }
                    } catch (\Exception $e) {
                        $this->warn("Error updating/restoring patient {$name} (GUID: {$patientData['guid']}): " . $e->getMessage());
                        $skippedPatientsCount++;
                        $skippedPatientNames[] = $name;
                        continue;
                    }

                    // Process follow-ups for existing patient
                    foreach ($followUps as $followUpData) {
                        // Follow-up validation
                        if (empty($followUpData['created_at']) || empty($followUpData['updated_at'])) {
                            $this->warn("Skipping follow-up for patient {$name}: missing date fields");
                            $skippedFollowUpsCount++;
                            $skippedFollowUpNames[] = $name;
                            continue;
                        }

                        try {
                            $followUpData['created_at'] = Carbon::parse($followUpData['created_at'])->setTimezone($timezone)->toDateTimeString();
                            $followUpData['updated_at'] = Carbon::parse($followUpData['updated_at'])->setTimezone($timezone)->toDateTimeString();
                        } catch (\Exception $e) {
                            $this->warn("Skipping follow-up for patient {$name}: invalid date format");
                            $skippedFollowUpsCount++;
                            $skippedFollowUpNames[] = $name;
                            continue;
                        }

                        $existingFollowUp = FollowUp::where('patient_id', $existingPatient->id)
                            ->where('created_at', $followUpData['created_at'])
                            ->first();

                        if ($existingFollowUp) {
                            try {
                                $existingFollowUpUpdatedAt = Carbon::parse($existingFollowUp->updated_at)->setTimezone($timezone);
                                if ($existingFollowUpUpdatedAt->lessThan(Carbon::parse($followUpData['updated_at']))) {
                                    $existingFollowUp->update($followUpData);
                                    $updatedFollowUpsCount++;
                                    $updatedFollowUpNames[] = $name;
                                } else {
                                    $skippedFollowUpsCount++;
                                    $skippedFollowUpNames[] = $name;
                                }
                            } catch (\Exception $e) {
                                $this->warn("Error updating follow-up for patient {$name}: " . $e->getMessage());
                                $skippedFollowUpsCount++;
                                $skippedFollowUpNames[] = $name;
                            }
                        } else {
                            try {
                                $followUpData['patient_id'] = $existingPatient->id;
                                FollowUp::create($followUpData);
                                $newFollowUpsCount++;
                                $addedFollowUpNames[] = $name;
                            } catch (\Exception $e) {
                                $this->warn("Error creating follow-up for patient {$name}: " . $e->getMessage());
                                $skippedFollowUpsCount++;
                                $skippedFollowUpNames[] = $name;
                            }
                        }
                    }

                    continue;
                }

                // New patient
                try {
                    $patient = Patient::create($patientData);
                    $importedPatientsCount++;
                    $importedPatientNames[] = $name;
                } catch (\Exception $e) {
                    $this->warn("Error creating patient {$name}: " . $e->getMessage());
                    $skippedPatientsCount++;
                    $skippedPatientNames[] = $name;
                    continue;
                }

                // Process follow-ups for new patient
                foreach ($followUps as $followUpData) {
                    // Follow-up validation
                    if (empty($followUpData['created_at']) || empty($followUpData['updated_at'])) {
                        $this->warn("Skipping follow-up for patient {$name}: missing date fields");
                        $skippedFollowUpsCount++;
                        $skippedFollowUpNames[] = $name;
                        continue;
                    }

                    try {
                        $followUpData['created_at'] = Carbon::parse($followUpData['created_at'])->setTimezone($timezone)->toDateTimeString();
                        $followUpData['updated_at'] = Carbon::parse($followUpData['updated_at'])->setTimezone($timezone)->toDateTimeString();
                    } catch (\Exception $e) {
                        $this->warn("Skipping follow-up for patient {$name}: invalid date format");
                        $skippedFollowUpsCount++;
                        $skippedFollowUpNames[] = $name;
                        continue;
                    }

                    try {
                        $followUpData['patient_id'] = $patient->id;
                        FollowUp::create($followUpData);
                        $newFollowUpsCount++;
                        $addedFollowUpNames[] = $name;
                    } catch (\Exception $e) {
                        $this->warn("Error creating follow-up for patient {$name}: " . $e->getMessage());
                        $skippedFollowUpsCount++;
                        $skippedFollowUpNames[] = $name;
                    }
                }
            }
        });

        $this->line("<options=bold>Summary:</>");

        $this->line(" <fg=yellow>Patients restored:</> {$patientsRestored} " . $this->formatNames($restoredPatientNames));
        $this->line(" <fg=green>Patients imported:</> $importedPatientsCount " . $this->formatNames($importedPatientNames));
        // $this->line(" <fg=yellow>Patients updated:</> $updatedPatientsCount " . $this->formatNames($updatedPatientNames));
        $this->line(" <fg=gray>Patients uchanged:</> $skippedPatientsCount ");
        $this->line(" <fg=green>Follow-ups added:</> $newFollowUpsCount " . $this->formatNames($addedFollowUpNames));
        // $this->line(" <fg=yellow>Follow-ups updated:</> $updatedFollowUpsCount " . $this->formatNames($updatedFollowUpNames));
        $this->line(" <fg=gray>Follow-ups unchanged:</> $skippedFollowUpsCount ");

        $this->line("\n<fg=white;bg=blue> Import completed successfully </>");

        $this->newLine();
    }

    private function formatNames(array $names): string
    {
        if (empty($names)) return '';

        $display = array_slice($names, 0, 5);
        $more = count($names) - count($display);

        return ' (' . implode(', ', $display) . ($more > 0 ? ", +$more more" : '') . ')';
    }
}
