<?php

namespace App\Services;

use App\Models\Patient;
use App\Models\FollowUp;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

class SyncService
{
    protected $timezone;

    public function __construct()
    {
        $this->timezone = env('APP_TIMEZONE', 'Asia/Kolkata');
    }

    /**
     * Export patients and follow-ups to JSON data (for file or API)
     */
    public function exportData($startDate = null, $endDate = null)
    {
        $query = Patient::with(['followUps' => function ($q) use ($startDate, $endDate) {
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
            return null;
        }

        // Normalize follow-up timestamps
        $exported = $patients->map(function ($patient) {
            $data = $patient->toArray();

            $data['follow_ups'] = collect($data['follow_ups'])->map(function ($fup) {
                $fup['created_at'] = Carbon::parse($fup['created_at'])->setTimezone($this->timezone)->toDateTimeString();
                $fup['updated_at'] = Carbon::parse($fup['updated_at'])->setTimezone($this->timezone)->toDateTimeString();
                return $fup;
            });

            return $data;
        });

        return $exported;
    }

    /**
     * Import patients and follow-ups from JSON data
     */
    public function importData($patientsData)
    {
        if (!is_array($patientsData)) {
            throw new \Exception("Data is not an array of patients");
        }

        $importedPatientsCount = $updatedPatientsCount = $skippedPatientsCount = 0;
        $newFollowUpsCount = $updatedFollowUpsCount = $skippedFollowUpsCount = 0;
        $patientsRestored = 0;

        // Track patient names for detailed reporting
        $restoredPatientNames = $importedPatientNames = $updatedPatientNames = $skippedPatientNames = [];
        $addedFollowUpPatientNames = $updatedFollowUpPatientNames = [];

        // Track errors and failed operations
        $patientErrors = $followUpErrors = [];
        $backgroundOperations = [];
        $syncLogs = [];

        DB::transaction(function () use (
            $patientsData,
            &$importedPatientsCount,
            &$updatedPatientsCount,
            &$skippedPatientsCount,
            &$newFollowUpsCount,
            &$updatedFollowUpsCount,
            &$skippedFollowUpsCount,
            &$patientsRestored,
            &$restoredPatientNames,
            &$importedPatientNames,
            &$updatedPatientNames,
            &$skippedPatientNames,
            &$addedFollowUpPatientNames,
            &$updatedFollowUpPatientNames,
            &$patientErrors,
            &$followUpErrors,
            &$backgroundOperations,
            &$syncLogs
        ) {
            foreach ($patientsData as $patientData) {
                $syncLogs[] = "Processing patient: " . (isset($patientData['name']) ? $patientData['name'] : 'Unknown') . " (GUID: " . (isset($patientData['guid']) ? $patientData['guid'] : 'N/A') . ")";
                $backgroundOperations[] = "Validating patient data structure";

                // Patient-level validation
                if (
                    empty($patientData['guid']) ||
                    empty($patientData['created_at']) ||
                    empty($patientData['updated_at']) ||
                    !isset($patientData['name'])
                ) {
                    $error = "Skipping patient: missing required fields - " . json_encode($patientData);
                    $patientErrors[] = $error;
                    $syncLogs[] = "ERROR: " . $error;
                    $skippedPatientsCount++;
                    $skippedPatientNames[] = $patientData['name'] ?? 'Unknown';
                    continue;
                }

                $backgroundOperations[] = "Parsing timestamps for patient {$patientData['name']}";
                try {
                    $patientData['created_at'] = Carbon::parse($patientData['created_at'])->setTimezone($this->timezone)->toDateTimeString();
                    $patientData['updated_at'] = Carbon::parse($patientData['updated_at'])->setTimezone($this->timezone)->toDateTimeString();
                } catch (\Exception $e) {
                    $error = "Skipping patient: invalid date format for GUID {$patientData['guid']}: " . $e->getMessage();
                    $patientErrors[] = $error;
                    $syncLogs[] = "ERROR: " . $error;
                    $skippedPatientsCount++;
                    $skippedPatientNames[] = $patientData['name'] ?? 'Unknown';
                    continue;
                }

                $followUps = $patientData['follow_ups'] ?? [];
                unset($patientData['follow_ups']);
                $name = $patientData['name'] ?? 'Unknown';

                $syncLogs[] = "Patient {$name} has " . count($followUps) . " follow-ups in API data";

                $backgroundOperations[] = "Checking for existing patient with patient_id: {$patientData['patient_id']}";
                $existingPatient = Patient::withTrashed()->where('patient_id', $patientData['patient_id'])->first();

                if ($existingPatient) {
                    $backgroundOperations[] = "Found existing patient, checking update requirements";
                    try {
                        $existingPatientUpdatedAt = Carbon::parse($existingPatient->updated_at)->setTimezone($this->timezone);

                        $wasUpdated = false;
                        if ($existingPatientUpdatedAt->lessThan(Carbon::parse($patientData['updated_at']))) {
                            $backgroundOperations[] = "Patient data is newer, updating patient record";
                            $patientData['updated_at'] = now()->setTimezone($this->timezone)->toDateTimeString();
                            $existingPatient->update($patientData);
                            $wasUpdated = true;
                            $syncLogs[] = "Updated patient: {$name}";
                        }

                        if ($existingPatient->trashed()) {
                            $backgroundOperations[] = "Restoring soft-deleted patient";
                            $existingPatient->restore();
                            $patientsRestored++;
                            $restoredPatientNames[] = $name;
                            $syncLogs[] = "Restored patient: {$name}";
                        } elseif ($wasUpdated) {
                            $updatedPatientsCount++;
                            $updatedPatientNames[] = $name;
                        } else {
                            $backgroundOperations[] = "Patient data is up to date, skipping update";
                            $skippedPatientsCount++;
                            $skippedPatientNames[] = $name;
                            $syncLogs[] = "Skipped patient (up to date): {$name}";
                        }
                    } catch (\Exception $e) {
                        $error = "Error updating/restoring patient {$name} (GUID: {$patientData['guid']}): " . $e->getMessage();
                        $patientErrors[] = $error;
                        $syncLogs[] = "ERROR: " . $error;
                        $skippedPatientsCount++;
                        $skippedPatientNames[] = $name;
                        continue;
                    }

                    // Process follow-ups for existing patient
                    $backgroundOperations[] = "Processing " . count($followUps) . " follow-ups for patient {$name}";
                    foreach ($followUps as $followUpData) {
                        $backgroundOperations[] = "Validating follow-up data for patient {$name}";

                        // Follow-up validation
                        if (empty($followUpData['created_at']) || empty($followUpData['updated_at'])) {
                            $error = "Skipping follow-up for patient {$name}: missing required timestamp fields";
                            $followUpErrors[] = $error;
                            $syncLogs[] = "ERROR: " . $error;
                            $skippedFollowUpsCount++;
                            continue;
                        }

                        $backgroundOperations[] = "Parsing follow-up timestamps for patient {$name}";
                        try {
                            $followUpData['created_at'] = Carbon::parse($followUpData['created_at'])->setTimezone($this->timezone)->toDateTimeString();
                            $followUpData['updated_at'] = Carbon::parse($followUpData['updated_at'])->setTimezone($this->timezone)->toDateTimeString();
                        } catch (\Exception $e) {
                            $error = "Skipping follow-up for patient {$name}: invalid date format";
                            $followUpErrors[] = $error;
                            $syncLogs[] = "ERROR: " . $error;
                            $skippedFollowUpsCount++;
                            continue;
                        }

                        $backgroundOperations[] = "Checking for existing follow-up records for patient {$name}";
                        // Try to find existing follow-up by unique content first
                        $existingFollowUp = FollowUp::where('patient_id', $existingPatient->id)
                            ->where('check_up_info', $followUpData['check_up_info'])
                            ->first();

                        // Fallback to timestamp-based matching if content doesn't match
                        if (!$existingFollowUp) {
                            $existingFollowUp = FollowUp::where('patient_id', $existingPatient->id)
                                ->where('doctor_id', $followUpData['doctor_id'])
                                ->whereRaw("DATE_FORMAT(created_at, '%Y-%m-%d %H:%i') = DATE_FORMAT(?, '%Y-%m-%d %H:%i')", [$followUpData['created_at']])
                                ->first();
                        }

                        if ($existingFollowUp) {
                            $backgroundOperations[] = "Found existing follow-up, checking if update needed for patient {$name}";
                            try {
                                $existingFollowUpUpdatedAt = Carbon::parse($existingFollowUp->updated_at)->setTimezone($this->timezone);
                                if ($existingFollowUpUpdatedAt->lessThan(Carbon::parse($followUpData['updated_at']))) {
                                    $backgroundOperations[] = "Updating existing follow-up for patient {$name}";
                                    $followUpData['updated_at'] = now()->setTimezone($this->timezone)->toDateTimeString();
                                    $existingFollowUp->update($followUpData);
                                    $updatedFollowUpsCount++;
                                    $updatedFollowUpPatientNames[] = $name;
                                    $syncLogs[] = "Updated follow-up for patient: {$name}";
                                } else {
                                    $backgroundOperations[] = "Follow-up is up to date for patient {$name}";
                                    $skippedFollowUpsCount++;
                                    $syncLogs[] = "Skipped follow-up (up to date) for patient: {$name}";
                                }
                            } catch (\Exception $e) {
                                $error = "Error updating follow-up for patient {$name}: " . $e->getMessage();
                                $followUpErrors[] = $error;
                                $syncLogs[] = "ERROR: " . $error;
                                $skippedFollowUpsCount++;
                            }
                        } else {
                            $backgroundOperations[] = "Creating new follow-up for patient {$name}";
                            try {
                                $followUpData['patient_id'] = $existingPatient->id;
                                FollowUp::create($followUpData);
                                $newFollowUpsCount++;
                                $addedFollowUpPatientNames[] = $name;
                                $syncLogs[] = "Added new follow-up for patient: {$name}";
                            } catch (\Exception $e) {
                                $error = "Error creating follow-up for patient {$name}: " . $e->getMessage();
                                $followUpErrors[] = $error;
                                $syncLogs[] = "ERROR: " . $error;
                                $skippedFollowUpsCount++;
                            }
                        }
                    }

                    continue;
                }

                // New patient
                $backgroundOperations[] = "Creating new patient record";
                try {
                    $patient = Patient::create($patientData);
                    $importedPatientsCount++;
                    $importedPatientNames[] = $name;
                    $syncLogs[] = "Created new patient: {$name}";
                } catch (\Exception $e) {
                    $error = "Error creating patient {$name}: " . $e->getMessage();
                    $patientErrors[] = $error;
                    $syncLogs[] = "ERROR: " . $error;
                    $skippedPatientsCount++;
                    $skippedPatientNames[] = $name;
                    continue;
                }

                // Process follow-ups for new patient
                $backgroundOperations[] = "Processing " . count($followUps) . " follow-ups for new patient {$name}";
                foreach ($followUps as $followUpData) {
                    $backgroundOperations[] = "Validating follow-up data for new patient {$name}";

                    // Follow-up validation
                    if (empty($followUpData['created_at']) || empty($followUpData['updated_at'])) {
                        $error = "Skipping follow-up for new patient {$name}: missing required timestamp fields";
                        $followUpErrors[] = $error;
                        $syncLogs[] = "ERROR: " . $error;
                        $skippedFollowUpsCount++;
                        continue;
                    }

                    $backgroundOperations[] = "Parsing follow-up timestamps for new patient {$name}";
                    try {
                        $followUpData['created_at'] = Carbon::parse($followUpData['created_at'])->setTimezone($this->timezone)->toDateTimeString();
                        $followUpData['updated_at'] = Carbon::parse($followUpData['updated_at'])->setTimezone($this->timezone)->toDateTimeString();
                    } catch (\Exception $e) {
                        $error = "Skipping follow-up for new patient {$name}: invalid date format";
                        $followUpErrors[] = $error;
                        $syncLogs[] = "ERROR: " . $error;
                        $skippedFollowUpsCount++;
                        continue;
                    }

                    // Check if follow-up already exists for new patient (in case of partial syncs)
                    $existingFollowUp = FollowUp::where('patient_id', $patient->id)
                        ->where('check_up_info', $followUpData['check_up_info'])
                        ->first();

                    if (!$existingFollowUp) {
                        // Fallback check
                        $existingFollowUp = FollowUp::where('patient_id', $patient->id)
                            ->where('doctor_id', $followUpData['doctor_id'])
                            ->whereRaw("DATE_FORMAT(created_at, '%Y-%m-%d %H:%i') = DATE_FORMAT(?, '%Y-%m-%d %H:%i')", [$followUpData['created_at']])
                            ->first();
                    }

                    if (!$existingFollowUp) {
                        $backgroundOperations[] = "Creating follow-up for new patient {$name}";
                        try {
                            $followUpData['patient_id'] = $patient->id;
                            FollowUp::create($followUpData);
                            $newFollowUpsCount++;
                            $addedFollowUpPatientNames[] = $name;
                            $syncLogs[] = "Added follow-up for new patient: {$name}";
                        } catch (\Exception $e) {
                            $error = "Error creating follow-up for new patient {$name}: " . $e->getMessage();
                            $followUpErrors[] = $error;
                            $syncLogs[] = "ERROR: " . $error;
                            $skippedFollowUpsCount++;
                        }
                    } else {
                        $backgroundOperations[] = "Follow-up already exists for new patient {$name}, checking for updates";
                        // Update if newer
                        try {
                            $existingFollowUpUpdatedAt = Carbon::parse($existingFollowUp->updated_at)->setTimezone($this->timezone);
                            if ($existingFollowUpUpdatedAt->lessThan(Carbon::parse($followUpData['updated_at']))) {
                                $existingFollowUp->update($followUpData);
                                $updatedFollowUpsCount++;
                                $syncLogs[] = "Updated existing follow-up for new patient: {$name}";
                            } else {
                                $skippedFollowUpsCount++;
                                $syncLogs[] = "Skipped follow-up (up to date) for new patient: {$name}";
                            }
                        } catch (\Exception $e) {
                            $error = "Error updating follow-up for new patient {$name}: " . $e->getMessage();
                            $followUpErrors[] = $error;
                            $syncLogs[] = "ERROR: " . $error;
                            $skippedFollowUpsCount++;
                        }
                    }
                }
            }
        });

        return [
            'patients_restored' => $patientsRestored,
            'patients_imported' => $importedPatientsCount,
            'patients_updated' => $updatedPatientsCount,
            'patients_skipped' => $skippedPatientsCount,
            'follow_ups_added' => $newFollowUpsCount,
            'follow_ups_updated' => $updatedFollowUpsCount,
            'follow_ups_skipped' => $skippedFollowUpsCount,
            'patient_names' => [
                'restored' => $restoredPatientNames,
                'imported' => $importedPatientNames,
                'updated' => $updatedPatientNames,
                'skipped' => $skippedPatientNames,
            ],
            'follow_up_patient_names' => [
                'added' => $addedFollowUpPatientNames,
                'updated' => $updatedFollowUpPatientNames,
            ],
            'errors' => [
                'patients' => $patientErrors,
                'follow_ups' => $followUpErrors,
            ],
            'background_operations' => array_unique($backgroundOperations),
            'sync_logs' => $syncLogs,
        ];
    }

    /**
     * Sync data from online API
     */
    public function syncFromApi($date, $username, $password, $syncAll = false)
    {
        $useMockData = config('services.online_api.use_mock_data', false);

        // Initialize logging arrays
        $backgroundOperations = [];
        $syncLogs = [];

        if ($useMockData) {
            // Realistic mock data matching real clinic backup structure
            $data = [
                [
                    'guid' => 'patient-arjun-sharma',
                    'name' => 'Arjun Sharma',
                    'email_id' => 'arjun.sharma@example.com',
                    'mobile_phone' => '9876543210',
                    'address' => '456 MG Road, Pune, Maharashtra',
                    'birthdate' => '1995-05-15',
                    'gender' => 'Male',
                    'patient_id' => 'A-1505959876543210',
                    'vishesh' => 'samanya',
                    'height' => '175.00',
                    'weight' => '70.00',
                    'occupation' => 'Engineer',
                    'reference' => 'by friend',
                    'created_at' => $date . ' 09:00:00',
                    'updated_at' => $date . ' 09:00:00', // Fixed date for consistent testing
                    'follow_ups' => [
                        [
                            'check_up_info' => '{"nadi":"वात","chikitsa":"महासुदर्शन, वैदेही, बिभितक, यष्टी, तालीसादी","days":null,"packets":null,"payment_method":"cash","amount":"1000","balance":null,"user_id":7,"user_name":"Dhananjay","branch_id":"1","branch_name":"Paud Road"}',
                            'diagnosis' => 'Viral infection',
                            'treatment' => 'Prescribed antibiotics and rest.',
                            'amount_billed' => 1000.00,
                            'amount_paid' => 1000.00,
                            'doctor_id' => 7,
                            'created_at' => $date . ' 10:00:00',
                            'updated_at' => $date . ' 10:00:00',
                        ],
                        [
                            'check_up_info' => '{"nadi":"वात, सूक्ष्म","chikitsa":"वरा, गुग्गुल, विश्व, अश्वकपी, वत्स, गोक्षुर, गोदंती","days":null,"packets":null,"payment_method":"cash","amount":"1000","balance":null,"user_id":7,"user_name":"Dhananjay","branch_id":"1","branch_name":"Paud Road"}',
                            'diagnosis' => 'Improved condition',
                            'treatment' => 'Continue medication.',
                            'amount_billed' => 500.00,
                            'amount_paid' => 500.00,
                            'doctor_id' => 7,
                            'created_at' => $date . ' 14:00:00',
                            'updated_at' => $date . ' 14:00:00',
                        ],
                    ],
                ],
                [
                    'guid' => 'patient-meera-patel',
                    'name' => 'Meera Patel',
                    'email_id' => 'meera.patel@example.com',
                    'mobile_phone' => '8765432109',
                    'address' => '789 Shivaji Nagar, Pune, Maharashtra',
                    'birthdate' => '1990-08-22',
                    'gender' => 'Female',
                    'patient_id' => 'M-2208908765432109',
                    'vishesh' => '<p>santulan vishesh</p>',
                    'height' => '165.00',
                    'weight' => '55.00',
                    'occupation' => 'Teacher',
                    'reference' => 'by relative',
                    'created_at' => $date . ' 11:00:00',
                    'updated_at' => $date . ' 11:00:00', // Fixed date for consistent testing
                    'follow_ups' => [
                        [
                            'check_up_info' => '{"photo_types":"[]","nadi":"वात, पित्त","nidan":"Diagnosis","chikitsa":"महासुदर्शन, वैदेही, बिभितक, यष्टी, तालीसादी","vishesh":"santulan","days":"10","packets":"5","total_due":"500.00","payment_method":"cash","all_dues":"0","photos":[{}],"user_id":7,"user_name":"Dhananjay","branch_id":"3","branch_name":"Kothrud"}',
                            'diagnosis' => 'निद्रा - ↓',
                            'treatment' => 'Prescribed medication.',
                            'amount_billed' => 1000.00,
                            'amount_paid' => 500.00,
                            'doctor_id' => 7,
                            'created_at' => $date . ' 12:00:00',
                            'updated_at' => $date . ' 12:00:00',
                        ],
                    ],
                ],
            ];

            $stats = $this->importData($data);

            // Merge the background operations and sync logs from importData
            $stats['background_operations'] = array_merge($backgroundOperations, $stats['background_operations'] ?? []);
            $stats['sync_logs'] = array_merge($syncLogs, $stats['sync_logs'] ?? []);

            return [
                'message' => 'Mock data synced successfully (realistic clinic data).',
                'stats' => $stats
            ];
        }

        // Real API calls
        $apiUrl = config('services.online_api.url', 'http://dev.vaidyajategaonkar.com/api');

        // First, attempt login to get token
        $backgroundOperations[] = "Attempting login to API server";
        $loginResponse = Http::post($apiUrl . '/login', [
            'username' => $username,
            'password' => $password,
        ]);

        if (!$loginResponse->successful()) {
            $status = $loginResponse->status();
            $message = match($status) {
                401 => 'Invalid username or password.',
                404 => 'API endpoint not found. Please check if the API is available.',
                500 => 'Server error. Please try again later.',
                default => 'Login failed with status ' . $status . '.',
            };
            $error = 'Login failed: ' . $message;
            $syncLogs[] = "ERROR: " . $error;
            throw new \Exception($error);
        }

        $backgroundOperations[] = "Login successful, extracting token";
        $loginData = $loginResponse->json();
        if (!isset($loginData['token'])) {
            $error = 'Login successful but no token received.';
            $syncLogs[] = "ERROR: " . $error;
            throw new \Exception($error);
        }

        $token = $loginData['token'];
        $backgroundOperations[] = "Token received, fetching data from API";

        // Now fetch the data
        $apiEndpoint = $apiUrl . '/export';
        if ($syncAll) {
            // For full sync, send a very old date to get all historical data
            $apiEndpoint .= '?date=1900-01-01';
            $syncLogs[] = "Fetching ALL data (using very old date to get all records)";
        } else {
            $apiEndpoint .= '?date=' . $date;
            $syncLogs[] = "Fetching data for date: {$date}";
        }

        $syncLogs[] = "API Endpoint: {$apiEndpoint}";

        $response = Http::withToken($token)->get($apiEndpoint);

        if (!$response->successful()) {
            $status = $response->status();
            $message = match($status) {
                401 => 'Authentication failed. Token may be invalid.',
                403 => 'Access forbidden. Check permissions.',
                404 => 'Data endpoint not found.',
                500 => 'Server error while fetching data.',
                default => 'Failed to fetch data with status ' . $status . '.',
            };
            $error = 'Failed to fetch data from online API: ' . $message;
            $syncLogs[] = "ERROR: " . $error;
            throw new \Exception($error);
        }

        $backgroundOperations[] = "Data received from API, parsing JSON response";
        $data = $response->json();

        // Check if response is actually JSON
        $contentType = $response->header('Content-Type');
        if (!$contentType || !str_contains($contentType, 'application/json')) {
            $error = 'API returned non-JSON response. Expected JSON data but got: ' . ($contentType ?? 'unknown content type') . '. Response body: ' . substr($response->body(), 0, 500) . '...';
            $syncLogs[] = "ERROR: " . $error;
            throw new \Exception($error);
        }

        $syncLogs[] = "API Response Status: " . $response->status();
        $syncLogs[] = "API Response Body Length: " . strlen($response->body());
        $syncLogs[] = "Parsed Data Count: " . (is_array($data) ? count($data) : 'Not an array');

        if (empty($data)) {
            $syncLogs[] = "WARNING: No data available for the selected date.";
            $syncLogs[] = "API Response Body: " . $response->body();
            $message = $syncAll ? 'No data available from the online server.' : 'No data available for the selected date.';
            return [
                'message' => $message,
                'stats' => [
                    'sync_logs' => $syncLogs,
                    'background_operations' => $backgroundOperations
                ]
            ];
        }

        $syncLogs[] = "Successfully received " . count($data) . " patient records from API";

        // Count total follow-ups in API data
        $totalApiFollowUps = 0;
        foreach ($data as $patient) {
            $followUps = $patient['follow_ups'] ?? [];
            $totalApiFollowUps += count($followUps);
        }
        $syncLogs[] = "Total follow-ups in API data: {$totalApiFollowUps}";

        // Log sample of received data for debugging
        if (!empty($data)) {
            $samplePatient = $data[0];
            $syncLogs[] = "Sample patient data: GUID=" . ($samplePatient['guid'] ?? 'N/A') .
                         ", Name=" . ($samplePatient['name'] ?? 'N/A') .
                         ", Updated=" . ($samplePatient['updated_at'] ?? 'N/A');
        }
        $stats = $this->importData($data);

        // Merge the background operations and sync logs from importData
        $stats['background_operations'] = array_merge($backgroundOperations, $stats['background_operations'] ?? []);
        $stats['sync_logs'] = array_merge($syncLogs, $stats['sync_logs'] ?? []);

        $syncType = $syncAll ? 'ALL data' : "data for date {$date}";
        return [
            'message' => 'Data synced successfully from online server (' . $syncType . ').',
            'stats' => $stats
        ];
    }

    /**
     * Export data to file
     */
    public function exportToFile($startDate = null, $endDate = null)
    {
        $data = $this->exportData($startDate, $endDate);

        if (!$data) {
            return 'No patients found for given criteria.';
        }

        $jsonData = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        $fileName = 'backup-' . now()->format('Y-m-d_H-i-s') . '.json';

        Storage::put("backup/{$fileName}", $jsonData);

        return "Data exported to: storage/app/private/backup/$fileName";
    }

    /**
     * Clean up duplicate follow-ups, keeping only the most recent one for each unique combination
     */
    public function cleanupDuplicateFollowUps()
    {
        $duplicatesRemoved = 0;

        // Find follow-ups with duplicate patient_id + check_up_info combinations
        $duplicates = DB::select("
            SELECT patient_id, check_up_info, COUNT(*) as count, MAX(updated_at) as latest_update
            FROM follow_ups
            GROUP BY patient_id, check_up_info
            HAVING COUNT(*) > 1
        ");

        foreach ($duplicates as $duplicate) {
            // Keep the most recently updated follow-up, delete the others
            $followUpsToDelete = FollowUp::where('patient_id', $duplicate->patient_id)
                ->where('check_up_info', $duplicate->check_up_info)
                ->where('updated_at', '<', $duplicate->latest_update)
                ->delete();

            $duplicatesRemoved += $followUpsToDelete;
        }

        return $duplicatesRemoved;
    }
}
