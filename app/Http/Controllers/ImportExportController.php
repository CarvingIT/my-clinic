<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ImportExportController extends Controller
{
    public function index()
    {
        try {
            $files = Storage::files('exports');

            $exportFiles = collect($files)->map(function ($file) {
                $fileName = basename($file);

                // Parse filename to extract date/time
                // Format: backup-YYYY-MM-DD_HH-MM-SS.json
                if (preg_match('/backup-(\d{4}-\d{2}-\d{2})_(\d{2}-\d{2}-\d{2})\.json/', $fileName, $matches)) {
                    $date = $matches[1];
                    $time = str_replace('-', ':', $matches[2]);

                    return [
                        'name' => $fileName,
                        'path' => $file,
                        'size' => Storage::size($file),
                        'size_human' => $this->formatBytes(Storage::size($file)),
                        'date' => $date,
                        'time' => $time,
                        'created_at' => Carbon::createFromFormat('Y-m-d H:i:s', $date . ' ' . $time),
                        'download_url' => route('admin.download-export', $fileName),
                    ];
                }

                return null;
            })->filter()->sortByDesc('created_at')->values();

        } catch (\Exception $e) {
            $exportFiles = collect();
        }

        return view('admin.import-export', compact('exportFiles'));
    }

    public function export(Request $request)
    {
        $request->validate([
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        $timezone = config('app.timezone', 'Asia/Kolkata');

        $query = \App\Models\Patient::with(['followUps' => function ($q) use ($startDate, $endDate) {
            if ($startDate) {
                $q->where('updated_at', '>=', Carbon::parse($startDate)->startOfDay());
            }
            if ($endDate) {
                $q->where('updated_at', '<=', Carbon::parse($endDate)->endOfDay());
            }
        }]);

        if ($startDate || $endDate) {
            $query->where(function ($q) use ($startDate, $endDate) {
                if ($startDate) {
                    $q->where('updated_at', '>=', Carbon::parse($startDate)->startOfDay());
                }
                if ($endDate) {
                    $q->where('updated_at', '<=', Carbon::parse($endDate)->endOfDay());
                }
            });
        }

        $patients = $query->get();

        if ($patients->isEmpty()) {
            return back()->with('error', 'No patients found for the given criteria.');
        }

        // Generate filename once to ensure consistency
        $fileName = 'backup-' . now()->format('Y-m-d_H-i-s') . '.json';

        // Collect export stats
        $totalFollowUps = $patients->sum(function($patient) {
            return $patient->followUps->count();
        });

        $stats = [
            'total_patients' => $patients->count(),
            'total_follow_ups' => $totalFollowUps,
            'export_date' => now()->toDateTimeString(),
            'date_range' => [
                'start' => $startDate,
                'end' => $endDate,
            ],
            'file_name' => $fileName,
        ];

        session(['export_stats' => $stats]);

        // Normalize follow-up timestamps
        $exported = $patients->map(function ($patient) use ($timezone) {
            $data = $patient->toArray();

            $data['follow_ups'] = collect($data['follow_ups'])->map(function ($fup) use ($timezone) {
                $fup['created_at'] = Carbon::parse($fup['created_at'])->setTimezone($timezone)->toDateTimeString();
                $fup['updated_at'] = Carbon::parse($fup['updated_at'])->setTimezone($timezone)->toDateTimeString();
                return $fup;
            });

            return $data;
        });

        $jsonData = json_encode($exported, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

        // Save to storage for download
        Storage::put("exports/{$fileName}", $jsonData);

        return redirect()->back()->with('success', 'Export completed successfully.')->with('show_export_details', true);
    }

    public function download($file)
    {
        try {
            if (!Storage::exists("exports/{$file}")) {
                return back()->with('error', 'Export file not found. It may have been deleted or expired.');
            }

            return Storage::download("exports/{$file}");
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to download file: ' . $e->getMessage());
        }
    }

    public function import(Request $request)
    {
        try {
            $request->validate([
                'import_source' => 'required|in:upload,storage',
            ]);

            $importSource = $request->input('import_source');

            if ($importSource === 'upload') {
                $request->validate([
                    'file' => 'required|file|max:40960', // 40MB max, removed mimes:json as it's unreliable
                ]);

                $uploadedFile = $request->file('file');
                if (!$uploadedFile->isValid()) {
                    return back()->with('error', 'File upload failed: ' . $uploadedFile->getErrorMessage());
                }

                // Check file extension
                $extension = strtolower($uploadedFile->getClientOriginalExtension());
                if ($extension !== 'json') {
                    return back()->with('error', 'Only JSON files are allowed for import.');
                }

                $file = $request->file('file');
                $path = $file->storeAs('temp', 'import-' . time() . '.json'); // Store temporarily
                $originalName = $file->getClientOriginalName();
            } else {
                $request->validate([
                    'storage_file' => 'required|string',
                ]);
                // Import from storage
                $storageFile = $request->input('storage_file');
                if (!Storage::exists("exports/{$storageFile}")) {
                    return back()->with('error', 'Selected export file not found in storage.');
                }
                $path = "exports/{$storageFile}";
                $originalName = $storageFile;
            }

            $json = Storage::get($path);
            $patients = json_decode($json, true);

            // JSON validation
            if (json_last_error() !== JSON_ERROR_NONE) {
                if ($importSource === 'upload') {
                    Storage::delete($path);
                }
                return back()->with('error', 'Invalid JSON file: ' . json_last_error_msg());
            }

            if (!is_array($patients)) {
                if ($importSource === 'upload') {
                    Storage::delete($path);
                }
                return back()->with('error', 'Data is not an array of patients');
            }

            // Validate first patient structure
            if (!empty($patients) && is_array($patients[0])) {
                $firstPatient = $patients[0];
                if (empty($firstPatient['guid']) || empty($firstPatient['name']) || empty($firstPatient['address']) || empty($firstPatient['mobile_phone'])) {
                    if ($importSource === 'upload') {
                        Storage::delete($path);
                    }
                    return back()->with('error', 'Invalid patient data structure. Required fields: guid, name, address, mobile_phone');
                }
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

            $errors = [];
            $skippedDuplicates = [];

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
                &$restoredPatientNames,
                &$errors
            ) {
                foreach ($patients as $index => $patientData) {
                    // Patient-level validation
                    if (
                        empty($patientData['guid']) ||
                        empty($patientData['created_at']) ||
                        empty($patientData['updated_at']) ||
                        empty($patientData['name']) ||
                        empty($patientData['address']) ||
                        empty($patientData['mobile_phone'])
                    ) {
                        $skippedPatientsCount++;
                        $skippedPatientNames[] = $patientData['name'] ?? 'Unknown (missing required fields)';
                        $errors[] = "Patient #{$index}: Missing required fields (guid, name, address, mobile_phone)";
                        continue;
                    }

                    try {
                        $patientData['created_at'] = Carbon::parse($patientData['created_at'])->setTimezone($timezone)->toDateTimeString();
                        $patientData['updated_at'] = Carbon::parse($patientData['updated_at'])->setTimezone($timezone)->toDateTimeString();
                    } catch (\Exception $e) {
                        $skippedPatientsCount++;
                        $skippedPatientNames[] = $patientData['name'] ?? 'Unknown';
                        $errors[] = "Patient #{$index} ({$patientData['name']}): Invalid date format - " . $e->getMessage();
                        continue;
                    }

                    $followUps = $patientData['follow_ups'] ?? [];
                    unset($patientData['follow_ups']);
                    $name = $patientData['name'] ?? 'Unknown';

                    $existingPatient = \App\Models\Patient::withTrashed()->where('guid', $patientData['guid'])->first();

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
                            }
                        } catch (\Exception $e) {
                            $skippedPatientsCount++;
                            $skippedPatientNames[] = $name;
                            $errors[] = "Patient #{$index} ({$name}): Update failed - " . $e->getMessage();
                            continue;
                        }

                        // Process follow-ups for existing patient
                        foreach ($followUps as $followUpIndex => $followUpData) {
                            // Follow-up validation
                            if (empty($followUpData['created_at']) || empty($followUpData['updated_at']) || empty($followUpData['check_up_info'])) {
                                $skippedFollowUpsCount++;
                                $skippedFollowUpNames[] = $name;
                                $errors[] = "Follow-up #{$followUpIndex} for patient {$name}: Missing required fields (created_at, updated_at, check_up_info)";
                                continue;
                            }

                            try {
                                $followUpData['created_at'] = Carbon::parse($followUpData['created_at'])->setTimezone($timezone)->toDateTimeString();
                                $followUpData['updated_at'] = Carbon::parse($followUpData['updated_at'])->setTimezone($timezone)->toDateTimeString();
                            } catch (\Exception $e) {
                                $skippedFollowUpsCount++;
                                $skippedFollowUpNames[] = $name;
                                $errors[] = "Follow-up #{$followUpIndex} for patient {$name}: Invalid date format - " . $e->getMessage();
                                continue;
                            }

                            $existingFollowUp = \App\Models\FollowUp::where('patient_id', $existingPatient->id)
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
                                    $skippedFollowUpsCount++;
                                    $skippedFollowUpNames[] = $name;
                                    $errors[] = "Follow-up #{$followUpIndex} for patient {$name}: Update failed - " . $e->getMessage();
                                }
                            } else {
                                try {
                                    $followUpData['patient_id'] = $existingPatient->id;
                                    \App\Models\FollowUp::create($followUpData);
                                    $newFollowUpsCount++;
                                    $addedFollowUpNames[] = $name;
                                } catch (\Exception $e) {
                                    $skippedFollowUpsCount++;
                                    $skippedFollowUpNames[] = $name;
                                    $errors[] = "Follow-up #{$followUpIndex} for patient {$name}: Create failed - " . $e->getMessage();
                                }
                            }
                        }

                        continue;
                    }

                    // New patient
                    try {
                        // Check if patient with same patient_id already exists
                        $existingByPatientId = \App\Models\Patient::withTrashed()->where('patient_id', $patientData['patient_id'])->first();
                        if ($existingByPatientId) {
                            $skippedPatientsCount++;
                            $skippedPatientNames[] = $name;
                            $skippedDuplicates[] = "Patient '{$name}' (ID: {$patientData['patient_id']}) already exists - skipped";
                            continue;
                        }

                        $patient = \App\Models\Patient::create($patientData);
                        $importedPatientsCount++;
                        $importedPatientNames[] = $name;
                    } catch (\Exception $e) {
                        $skippedPatientsCount++;
                        $skippedPatientNames[] = $name;
                        $errors[] = "Patient #{$index} ({$name}): Create failed - " . $e->getMessage();
                        continue;
                    }

                    // Process follow-ups for new patient
                    foreach ($followUps as $followUpIndex => $followUpData) {
                        // Follow-up validation
                        if (empty($followUpData['created_at']) || empty($followUpData['updated_at']) || empty($followUpData['check_up_info'])) {
                            $skippedFollowUpsCount++;
                            $skippedFollowUpNames[] = $name;
                            $errors[] = "Follow-up #{$followUpIndex} for patient {$name}: Missing required fields (created_at, updated_at, check_up_info)";
                            continue;
                        }

                        try {
                            $followUpData['created_at'] = Carbon::parse($followUpData['created_at'])->setTimezone($timezone)->toDateTimeString();
                            $followUpData['updated_at'] = Carbon::parse($followUpData['updated_at'])->setTimezone($timezone)->toDateTimeString();
                        } catch (\Exception $e) {
                            $skippedFollowUpsCount++;
                            $skippedFollowUpNames[] = $name;
                            $errors[] = "Follow-up #{$followUpIndex} for patient {$name}: Invalid date format - " . $e->getMessage();
                            continue;
                        }

                        try {
                            $followUpData['patient_id'] = $patient->id;
                            \App\Models\FollowUp::create($followUpData);
                            $newFollowUpsCount++;
                            $addedFollowUpNames[] = $name;
                        } catch (\Exception $e) {
                            $skippedFollowUpsCount++;
                            $skippedFollowUpNames[] = $name;
                            $errors[] = "Follow-up #{$followUpIndex} for patient {$name}: Create failed - " . $e->getMessage();
                        }
                    }
                }
            });

            // Clean up temp file only if uploaded
            if ($importSource === 'upload') {
                Storage::delete($path);
            }

            // Prepare stats
            $stats = [
                'import_date' => now()->toDateTimeString(),
                'file_name' => $originalName,
                'patients_restored' => $patientsRestored,
                'patients_imported' => $importedPatientsCount,
                'patients_updated' => $updatedPatientsCount,
                'patients_unchanged' => $skippedPatientsCount,
                'follow_ups_added' => $newFollowUpsCount,
                'follow_ups_updated' => $updatedFollowUpsCount,
                'follow_ups_unchanged' => $skippedFollowUpsCount,
                'patient_names' => [
                    'restored' => $restoredPatientNames,
                    'imported' => $importedPatientNames,
                    'updated' => $updatedPatientNames,
                    'unchanged' => $skippedPatientNames,
                ],
                'follow_up_patient_names' => [
                    'added' => $addedFollowUpNames,
                    'updated' => $updatedFollowUpNames,
                    'unchanged' => $skippedFollowUpNames,
                ],
            ];

            session(['import_stats' => $stats]);

            // Store skipped duplicates as informational messages
            if (!empty($skippedDuplicates)) {
                session(['import_skipped' => $skippedDuplicates]);
            }

            // If there were actual errors (not duplicates), show them
            if (!empty($errors)) {
                session(['import_errors' => array_slice($errors, 0, 10)]); // Show first 10 errors
            }

            return back()->with('success', 'Import completed successfully.')->with('show_import_details', true);

        } catch (\Exception $e) {
            // Clean up temp file if it exists
            if (isset($path) && isset($importSource) && $importSource === 'upload') {
                Storage::delete($path);
            }
            return back()->with('error', 'Import failed: ' . $e->getMessage());
        }
    }

    public function listExportFiles()
    {
        try {
            $files = Storage::files('exports');

            $exportFiles = collect($files)->map(function ($file) {
                $fileName = basename($file);
                $filePath = Storage::path($file);

                // Parse filename to extract date/time
                // Format: backup-YYYY-MM-DD_HH-MM-SS.json
                if (preg_match('/backup-(\d{4}-\d{2}-\d{2})_(\d{2}-\d{2}-\d{2})\.json/', $fileName, $matches)) {
                    $date = $matches[1];
                    $time = str_replace('-', ':', $matches[2]);

                    return [
                        'name' => $fileName,
                        'path' => $file,
                        'size' => Storage::size($file),
                        'size_human' => $this->formatBytes(Storage::size($file)),
                        'date' => $date,
                        'time' => $time,
                        'created_at' => Carbon::createFromFormat('Y-m-d H:i:s', $date . ' ' . $time),
                        'download_url' => route('admin.download-export', $fileName),
                        'delete_url' => route('admin.export-files.delete', $fileName),
                    ];
                }

                return null;
            })->filter()->sortByDesc('created_at')->values();

            return view('admin.export-files', compact('exportFiles'));
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to load export files: ' . $e->getMessage());
        }
    }

    public function deleteExportFile($file)
    {
        try {
            if (!Storage::exists("exports/{$file}")) {
                return back()->with('error', 'Export file not found.');
            }

            Storage::delete("exports/{$file}");

            return back()->with('success', 'Export file deleted successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to delete export file: ' . $e->getMessage());
        }
    }

    private function formatBytes($bytes)
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $i = 0;
        while ($bytes >= 1024 && $i < count($units) - 1) {
            $bytes /= 1024;
            $i++;
        }
        return round($bytes, 2) . ' ' . $units[$i];
    }
}
