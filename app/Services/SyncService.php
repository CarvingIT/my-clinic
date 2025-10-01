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
        $this->timezone = config('app.timezone', 'Asia/Kolkata');
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

        DB::transaction(function () use (
            $patientsData,
            &$importedPatientsCount,
            &$updatedPatientsCount,
            &$skippedPatientsCount,
            &$newFollowUpsCount,
            &$updatedFollowUpsCount,
            &$skippedFollowUpsCount,
            &$patientsRestored
        ) {
            foreach ($patientsData as $patientData) {
                // Patient-level validation
                if (
                    empty($patientData['guid']) ||
                    empty($patientData['created_at']) ||
                    empty($patientData['updated_at']) ||
                    !isset($patientData['name'])
                ) {
                    throw new \Exception("Skipping patient: missing required fields - " . json_encode($patientData));
                }

                try {
                    $patientData['created_at'] = Carbon::parse($patientData['created_at'])->setTimezone($this->timezone)->toDateTimeString();
                    $patientData['updated_at'] = Carbon::parse($patientData['updated_at'])->setTimezone($this->timezone)->toDateTimeString();
                } catch (\Exception $e) {
                    throw new \Exception("Skipping patient: invalid date format for GUID {$patientData['guid']}");
                }

                $followUps = $patientData['follow_ups'] ?? [];
                unset($patientData['follow_ups']);
                $name = $patientData['name'] ?? 'Unknown';

                $existingPatient = Patient::withTrashed()->where('patient_id', $patientData['patient_id'])->first();

                if ($existingPatient) {
                    try {
                        $existingPatientUpdatedAt = Carbon::parse($existingPatient->updated_at)->setTimezone($this->timezone);

                        if ($existingPatientUpdatedAt->lessThan(Carbon::parse($patientData['updated_at']))) {
                            $existingPatient->update($patientData);
                            $updatedPatientsCount++;
                        } else {
                            $skippedPatientsCount++;
                        }

                        if ($existingPatient->trashed()) {
                            $existingPatient->restore();
                            $patientsRestored++;
                        }
                    } catch (\Exception $e) {
                        throw new \Exception("Error updating/restoring patient {$name} (GUID: {$patientData['guid']}): " . $e->getMessage());
                    }

                    // Process follow-ups for existing patient
                    foreach ($followUps as $followUpData) {
                        // Follow-up validation
                        if (empty($followUpData['created_at']) || empty($followUpData['updated_at'])) {
                            $skippedFollowUpsCount++;
                            continue;
                        }

                        try {
                            $followUpData['created_at'] = Carbon::parse($followUpData['created_at'])->setTimezone($this->timezone)->toDateTimeString();
                            $followUpData['updated_at'] = Carbon::parse($followUpData['updated_at'])->setTimezone($this->timezone)->toDateTimeString();
                        } catch (\Exception $e) {
                            $skippedFollowUpsCount++;
                            continue;
                        }

                        $existingFollowUp = FollowUp::where('patient_id', $existingPatient->id)
                            ->where('created_at', $followUpData['created_at'])
                            ->first();

                        if ($existingFollowUp) {
                            try {
                                $existingFollowUpUpdatedAt = Carbon::parse($existingFollowUp->updated_at)->setTimezone($this->timezone);
                                if ($existingFollowUpUpdatedAt->lessThan(Carbon::parse($followUpData['updated_at']))) {
                                    $existingFollowUp->update($followUpData);
                                    $updatedFollowUpsCount++;
                                } else {
                                    $skippedFollowUpsCount++;
                                }
                            } catch (\Exception $e) {
                                $skippedFollowUpsCount++;
                            }
                        } else {
                            try {
                                $followUpData['patient_id'] = $existingPatient->id;
                                FollowUp::create($followUpData);
                                $newFollowUpsCount++;
                            } catch (\Exception $e) {
                                $skippedFollowUpsCount++;
                            }
                        }
                    }

                    continue;
                }

                // New patient
                try {
                    $patient = Patient::create($patientData);
                    $importedPatientsCount++;
                } catch (\Exception $e) {
                    throw new \Exception("Error creating patient {$name}: " . $e->getMessage());
                }

                // Process follow-ups for new patient
                foreach ($followUps as $followUpData) {
                    // Follow-up validation
                    if (empty($followUpData['created_at']) || empty($followUpData['updated_at'])) {
                        $skippedFollowUpsCount++;
                        continue;
                    }

                    try {
                        $followUpData['created_at'] = Carbon::parse($followUpData['created_at'])->setTimezone($this->timezone)->toDateTimeString();
                        $followUpData['updated_at'] = Carbon::parse($followUpData['updated_at'])->setTimezone($this->timezone)->toDateTimeString();
                    } catch (\Exception $e) {
                        $skippedFollowUpsCount++;
                        continue;
                    }

                    try {
                        $followUpData['patient_id'] = $patient->id;
                        FollowUp::create($followUpData);
                        $newFollowUpsCount++;
                    } catch (\Exception $e) {
                        $skippedFollowUpsCount++;
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
        ];
    }

    /**
     * Sync data from online API
     */
    public function syncFromApi($date, $username, $password)
    {
        $useMockData = config('services.online_api.use_mock_data', false);

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
                    'updated_at' => now()->toDateTimeString(),
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
                    'updated_at' => now()->toDateTimeString(),
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

            return [
                'message' => 'Mock data synced successfully (realistic clinic data).',
                'stats' => $stats
            ];
        }

        // Real API calls
        $apiUrl = config('services.online_api.url', 'https://kothrud.vaidyajategaonkar.com/api');

        // First, attempt login to get token
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
            throw new \Exception('Login failed: ' . $message);
        }

        $loginData = $loginResponse->json();
        if (!isset($loginData['token'])) {
            throw new \Exception('Login successful but no token received.');
        }

        $token = $loginData['token'];

        // Now fetch the data
        $response = Http::withToken($token)->get($apiUrl . '/export?date=' . $date);

        if (!$response->successful()) {
            $status = $response->status();
            $message = match($status) {
                401 => 'Authentication failed. Token may be invalid.',
                403 => 'Access forbidden. Check permissions.',
                404 => 'Data endpoint not found.',
                500 => 'Server error while fetching data.',
                default => 'Failed to fetch data with status ' . $status . '.',
            };
            throw new \Exception('Failed to fetch data from online API: ' . $message);
        }

        $data = $response->json();

        if (empty($data)) {
            return [
                'message' => 'No data available for the selected date.',
                'stats' => []
            ];
        }

        $stats = $this->importData($data);

        return [
            'message' => 'Data synced successfully from online server.',
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
     * Import data from file
     */
    public function importFromFile($filePath)
    {
        if (!Storage::exists($filePath)) {
            throw new \Exception("File not found in storage: $filePath");
        }

        $json = Storage::get($filePath);
        $patients = json_decode($json, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \Exception("Invalid JSON: " . json_last_error_msg());
        }

        return $this->importData($patients);
    }
}
