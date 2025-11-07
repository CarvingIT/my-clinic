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
        $request->validate([
            'import_source' => 'required|in:upload,storage',
        ]);

        $importSource = $request->input('import_source');

        if ($importSource === 'upload') {
            $request->validate([
                'file' => 'required|file|mimes:json|max:20480', // 20MB max
            ]);
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
                    $skippedPatientsCount++;
                    $skippedPatientNames[] = $patientData['name'] ?? 'Unknown';
                    continue;
                }

                try {
                    $patientData['created_at'] = Carbon::parse($patientData['created_at'])->setTimezone($timezone)->toDateTimeString();
                    $patientData['updated_at'] = Carbon::parse($patientData['updated_at'])->setTimezone($timezone)->toDateTimeString();
                } catch (\Exception $e) {
                    $skippedPatientsCount++;
                    $skippedPatientNames[] = $patientData['name'] ?? 'Unknown';
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
                        continue;
                    }

                    // Process follow-ups for existing patient
                    foreach ($followUps as $followUpData) {
                        // Follow-up validation
                        if (empty($followUpData['created_at']) || empty($followUpData['updated_at'])) {
                            $skippedFollowUpsCount++;
                            $skippedFollowUpNames[] = $name;
                            continue;
                        }

                        try {
                            $followUpData['created_at'] = Carbon::parse($followUpData['created_at'])->setTimezone($timezone)->toDateTimeString();
                            $followUpData['updated_at'] = Carbon::parse($followUpData['updated_at'])->setTimezone($timezone)->toDateTimeString();
                        } catch (\Exception $e) {
                            $skippedFollowUpsCount++;
                            $skippedFollowUpNames[] = $name;
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
                            }
                        }
                    }

                    continue;
                }

                // New patient
                try {
                    $patient = \App\Models\Patient::create($patientData);
                    $importedPatientsCount++;
                    $importedPatientNames[] = $name;
                } catch (\Exception $e) {
                    $skippedPatientsCount++;
                    $skippedPatientNames[] = $name;
                    continue;
                }

                // Process follow-ups for new patient
                foreach ($followUps as $followUpData) {
                    // Follow-up validation
                    if (empty($followUpData['created_at']) || empty($followUpData['updated_at'])) {
                        $skippedFollowUpsCount++;
                        $skippedFollowUpNames[] = $name;
                        continue;
                    }

                    try {
                        $followUpData['created_at'] = Carbon::parse($followUpData['created_at'])->setTimezone($timezone)->toDateTimeString();
                        $followUpData['updated_at'] = Carbon::parse($followUpData['updated_at'])->setTimezone($timezone)->toDateTimeString();
                    } catch (\Exception $e) {
                        $skippedFollowUpsCount++;
                        $skippedFollowUpNames[] = $name;
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

        return back()->with('success', 'Import completed successfully.')->with('show_import_details', true);
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
