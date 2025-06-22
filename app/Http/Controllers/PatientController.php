<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
// use App\Http\Controllers\Controller;
use Illuminate\Routing\Controller;
use Illuminate\Support\Str;
use App\Models\FollowUp;
use App\Models\User;
// use Knp\Snappy\Pdf;
use PDF;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;


class PatientController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Patient::query()->orderByLatestFollowUp();

        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where('patients.name', 'like', "%{$searchTerm}%")
                ->orWhere('patients.mobile_phone', 'like', "%{$searchTerm}%")
                ->orWhere('patients.patient_id', 'like', "%{$searchTerm}%");
        }
        $patients = $query->with('followUps')->paginate(10);
        return view('patients.index', compact('patients'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $patientId = Str::uuid();
        return view('patients.create', compact('patientId'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'address' => ['required', 'string', 'max:255'],
            'occupation' => ['nullable', 'string', 'max:255'],
            'mobile_phone' => ['required', 'string', 'max:20', 'regex:/^[0-9]{10}$/'],
            'remark' => ['nullable', 'string'],
            'gender' => ['nullable', 'string'],
            'birthdate' => ['nullable', 'date'],
            'email_id' => ['nullable', 'email', 'max:255'],
            'vishesh' => ['nullable', 'string'],
            'balance' => ['nullable', 'numeric'],
            'age' => ['nullable', 'integer', 'min:0', 'max:150'],
            // 'patient_id' => ['required', 'string', 'unique:patients,patient_id']
            'height' => ['nullable', 'numeric', 'min:1'],
            'weight' => ['nullable', 'numeric', 'min:1'],
        ]);

        // Manually handle the birthdate:
        // if ($request->filled('birthdate')) {
        //     $validatedData['birthdate'] = Carbon::parse($request->birthdate)->format('Y-m-d');
        // }

        // If birthdate is provided, format it
        if ($request->filled('birthdate')) {
            $validatedData['birthdate'] = Carbon::parse($request->birthdate)->format('Y-m-d');
        }
        // If age is provided but no birthdate, calculate approximate birthdate
        elseif ($request->filled('age')) {
            $birthYear = now()->year - $request->age;
            $validatedData['birthdate'] = Carbon::createFromDate($birthYear, 1, 1)->format('Y-m-d'); // Default: Jan 1st
        }

        // Generate Patient ID
        $patientId = $this->generatePatientId(
            $request->name,
            $request->birthdate,
            $request->mobile_phone
        );


        // Patient::create($request->all());
        // Save patient data along with generated patient_id
        $patient = Patient::create($request->all() + ['patient_id' => $patientId]);


        //return redirect()->route('patients.index')->with('success', 'Patient Created Successfully.');
        return redirect()->to('/patients/' . $patient->id);
    }


    /**
     * Display the specified resource.
     */
    public function show(Patient $patient)
    {

        $user = Auth::user();

        // Allow access if user has 'doctor' or 'admin' role
        if ($user && ($user->hasRole('doctor') || $user->hasRole('admin'))) {
            // Proceed with showing patient details
        } else {
            // Restrict access for users who don't have 'doctor' or 'admin' roles
            return redirect()->route('patients.index')->with('error', '⛔ You are not authorized to view patient details.');
        }

        $patient->fresh(); // Ensure latest data is loaded

        // Get total amount billed and total amount paid across all follow-ups
        $totalBilled = $patient->followUps()->sum('amount_billed');
        $totalPaid = $patient->followUps()->sum('amount_paid');

        // Calculate total outstanding balance (Total Due)
        $totalDueAll = $totalBilled - $totalPaid;

        // Load paginated follow-ups and reports
        $patient->followUps = $patient->followUps()->orderBy('created_at', 'desc')->paginate(5);
        $patient->reports = $patient->reports()->get();

        // Load follow-ups with their related uploads
        $followUps = $patient->followUps()->with('uploads')->orderBy('created_at', 'desc')->get();

        // Load all uploads for the patient, ordered by date
        $uploads = $patient->uploads()->orderBy('created_at', 'desc')->get();

        return view('patients.show', compact('patient', 'totalDueAll', 'followUps', 'uploads'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Patient $patient)
    {
        // dd($patient);
        return view('patients.edit', compact('patient'));
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Patient $patient)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'address' => ['required', 'string', 'max:255'],
            'occupation' => ['nullable', 'string', 'max:255'],
            'mobile_phone' => ['required', 'string', 'max:20', 'regex:/^[0-9]{10}$/'],
            'remark' => ['nullable', 'string'],
            'gender' => ['nullable', 'string'],
            'birthdate' => ['nullable', 'date'],
            'age' => ['nullable', 'integer', 'min:0', 'max:150'],
            'email_id' => ['nullable', 'email', 'max:255'],
            'vishesh' => ['nullable', 'string'],
            'balance' => ['nullable', 'numeric'],
            // 'patient_id' => ['required', 'string', 'unique:patients,patient_id,' . $patient->id]
            'height' => 'nullable|numeric|min:50|max:250', // Height in cm
            'weight' => 'nullable|numeric|min:10|max:300', // Weight in kg
        ]);
        $patient->update($request->all());

        return redirect()->route('patients.index')->with('success', 'Patient Updated Successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Patient $patient)
    {
        // if (auth::user()->hasRole('staff')) {
        //     return redirect()->route('patients.index')->with('error', 'Unauthorized: Staff cannot delete patients.');
        // }

        $patient->delete();
        return Redirect::route('patients.index')->with('success', 'Patient Deleted Successfully');
    }

    public function exportPdf(Patient $patient)
    {

        $pdf = PDF::loadView('patients.pdf', compact('patient'));

        return $pdf->inline($patient->name . '.pdf');
    }


    /**
     *  Generate Medical Certificate
     */

    public function generateCertificate(Patient $patient, Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date',
            'medical_condition' => 'required|string',
        ]);

        $checkUpInfo = json_decode($patient->followUps()->first()->check_up_info ?? '', true);

        $pdf = PDF::loadView('patients.certificate', [
            'patient' => $patient,
            'checkUpInfo' => $checkUpInfo,
            'startDate' => $request->start_date,
            'endDate' => $request->end_date,
            'medicalCondition' => $request->medical_condition,
        ]);

        return $pdf->inline('certificate_' . $patient->name . '.pdf');
    }

    private function generatePatientId($name, $dob, $mobile)
    {
        // Get the first letter of the name
        $initial = strtoupper(mb_substr($name, 0, 1, "UTF-8"));

        // Format DoB to DDMMYY
        $formattedDob = Carbon::parse($dob)->format('dmy');

        // Use the full mobile number
        $mobileNumber = $mobile;

        // Combine all parts to form the patient ID
        $patientId = $initial . '-' . $formattedDob . $mobileNumber;

        return $patientId;
    }

    /**
     * Export a single patient's data as JSON and email it
     */
    public function exportPatientJSON(Request $request, Patient $patient)
    {
        $request->validate(['email' => ['required', 'email', 'max:255']]);

        $followUps = $patient->followUps->map(function ($followUp) {
            $doctor = User::find($followUp->doctor_id);
            $followUp->doctor_name = $doctor ? $doctor->name : 'Unknown';
            $followUp->created_at = Carbon::parse($followUp->created_at)->setTimezone('Asia/Kolkata')->format('Y-m-d H:i:s');
            unset($followUp->doctor_id, $followUp->patient_id, $followUp->id);
            return $followUp;
        });

        $patientData = $patient->toArray();
        $patientData['updated_at'] = Carbon::parse($patientData['updated_at'])->setTimezone('Asia/Kolkata')->format('Y-m-d H:i:s');
        unset($patientData['id']);

        $exportData = ['timezone' => 'Asia/Kolkata', 'patient' => $patientData, 'follow_ups' => $followUps];

        Storage::disk('local')->makeDirectory('exports');
        $fileName = 'patient_' . $patient->patient_id . '_' . Carbon::now()->format('Ymd_His') . '.json';
        $filePath = "exports/{$fileName}";
        $jsonContent = json_encode($exportData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        Storage::disk('local')->put($filePath, $jsonContent);
        $fullPath = Storage::path($filePath);
        Log::info('Single patient JSON file created at: ' . $fullPath);

        if (!file_exists($fullPath)) {
            Log::error('JSON file not found at: ' . $fullPath);
            return redirect()->back()->with('error', 'Failed to create JSON file.');
        }

        if (!view()->exists('emails.patient_data_export')) {
            Log::error('View emails.patient_data_export not found');
            return redirect()->back()->with('error', 'Email template not found.');
        }

        Mail::send(['markdown' => 'emails.patient_data_export'], ['patient' => $patient], function ($message) use ($request, $fullPath, $fileName) {
            $message->to($request->email)
                ->subject('Patient Data Export')
                ->attach($fullPath, ['as' => $fileName, 'mime' => 'application/json']);
        });

        Storage::disk('local')->delete($filePath);
        Log::info('JSON file deleted: ' . $fullPath);

        return redirect()->back()->with('success', 'Patient data exported and emailed successfully.');
    }

    /**
     * Import a single patient's data from a JSON file
     */
    public function importPatientJSON(Request $request)
    {
        // Validate uploaded file
        $request->validate([
            'file' => ['required', 'file', 'mimes:json,application/octet-stream,text/plain,application/x-json', 'max:5000'],
        ]);

        // Log file details
        $file = $request->file('file');
        Log::info('Uploaded file details', [
            'original_name' => $file->getClientOriginalName(),
            'extension' => $file->getClientOriginalExtension(),
            'client_mime_type' => $file->getClientMimeType(),
            'server_mime_type' => mime_content_type($file->path()),
            'size' => $file->getSize(),
        ]);

        // Check if file has .json extension
        if (strtolower($file->getClientOriginalExtension()) !== 'json') {
            return redirect()->back()->with('error', 'The file must have a .json extension.');
        }

        // Read JSON file
        try {
            $jsonContent = file_get_contents($file->path());
            $data = json_decode($jsonContent, true);

            $systemTimezone = config('app.timezone'); // e.g., "Asia/Kolkata"

            // Check if JSON contains the correct timezone
            if (!isset($data['timezone']) || $data['timezone'] !== $systemTimezone) {
                Log::error('Timezone mismatch or missing. System: ' . $systemTimezone . ', JSON: ' . ($data['timezone'] ?? 'not set'));

                return redirect()->back()->with('error', 'Timezone mismatch detected! Please ensure the data was exported from a system using the timezone: ' . $systemTimezone);
            }

            // Validate JSON structure
            if (json_last_error() !== JSON_ERROR_NONE) {
                Log::error('Invalid JSON content: ' . json_last_error_msg());
                return redirect()->back()->with('error', 'Invalid JSON file content.');
            }

            if (!$data || !isset($data['patient']) || !isset($data['follow_ups'])) {
                Log::error('Invalid JSON structure: Missing patient or follow_ups');
                return redirect()->back()->with('error', 'Invalid JSON file structure.');
            }

            // Track import details
            $importDetails = [
                'created_patients' => [],
                'updated_patients' => [],
                'restored_patients' => [],
                'skipped_patients' => [],
                'total_follow_ups' => [],
                'total_patients_affected' => 0,
            ];

            // Validate patient data
            $patientValidator = \Validator::make($data['patient'], [
                'name' => ['required', 'string', 'max:255'],
                'address' => ['required', 'string', 'max:255'],
                'mobile_phone' => ['required', 'string', 'max:20', 'regex:/^[0-9]{10}$/'],
                'occupation' => ['nullable', 'string', 'max:255'],
                'remark' => ['nullable', 'string'],
                'gender' => ['nullable', 'string'],
                'birthdate' => ['nullable', 'date'],
                'email_id' => ['nullable', 'email', 'max:255'],
                'vishesh' => ['nullable', 'string'],
                'balance' => ['nullable', 'numeric'],
                'age' => ['nullable', 'integer', 'min:0', 'max:150'],
                'height' => ['nullable', 'numeric', 'min:50', 'max:250'],
                'weight' => ['nullable', 'numeric', 'min:10', 'max:300'],
            ]);

            if ($patientValidator->fails()) {
                Log::warning('Invalid patient data', [
                    'patient_id' => $data['patient']['patient_id'] ?? 'unknown',
                    'errors' => $patientValidator->errors()->toArray(),
                ]);
                return redirect()->back()->with('error', 'Invalid patient data: ' . implode(', ', $patientValidator->errors()->all()));
            }

            $patientData = $data['patient'];

            // Check for existing patient, including soft-deleted
            $patient = Patient::withTrashed()->where('patient_id', $patientData['patient_id'])->first();

            // Initialize follow-up count
            $followUpCount = 0;

            // Handle patient
            if (!$patient) {
                // Create new patient
                try {
                    $patientData['created_at'] = Carbon::parse($patientData['created_at'])->setTimezone('Asia/Kolkata')->format('Y-m-d H:i:s');
                    $patientData['updated_at'] = Carbon::parse($patientData['updated_at'])->setTimezone('Asia/Kolkata')->format('Y-m-d H:i:s');
                    $patient = Patient::create($patientData);
                    $importDetails['created_patients'][] = [
                        'patient_id' => $patientData['patient_id'],
                        'name' => $patientData['name'],
                    ];
                    $importDetails['total_patients_affected']++;
                    Log::info('Created new patient', [
                        'patient_id' => $patientData['patient_id'],
                        'created_at' => $patientData['created_at'],
                    ]);
                } catch (\Exception $e) {
                    Log::error('Invalid timestamp for new patient', [
                        'patient_id' => $patientData['patient_id'],
                        'error' => $e->getMessage(),
                    ]);
                    return redirect()->back()->with('error', 'Invalid timestamp in patient data.');
                }
            } else {
                // Restore soft-deleted patient
                if ($patient->trashed()) {
                    $patient->restore();
                    $importDetails['restored_patients'][] = [
                        'patient_id' => $patientData['patient_id'],
                        'name' => $patientData['name'],
                    ];
                    $importDetails['total_patients_affected']++;
                    Log::info('Restored soft-deleted patient', [
                        'patient_id' => $patientData['patient_id'],
                    ]);
                }

                // Update if imported data is newer
                try {
                    $importedUpdatedAt = Carbon::parse($patientData['updated_at']);
                    if ($importedUpdatedAt->gt($patient->updated_at)) {
                        $patientData['created_at'] = Carbon::parse($patientData['created_at'])->setTimezone('Asia/Kolkata')->format('Y-m-d H:i:s');
                        $patientData['updated_at'] = $importedUpdatedAt->setTimezone('Asia/Kolkata')->format('Y-m-d H:i:s');
                        $patient->update($patientData);
                        $importDetails['updated_patients'][] = [
                            'patient_id' => $patientData['patient_id'],
                            'name' => $patientData['name'],
                        ];
                        $importDetails['total_patients_affected']++;
                        Log::info('Updated patient', [
                            'patient_id' => $patientData['patient_id'],
                            'updated_at' => $patientData['updated_at'],
                        ]);
                    }
                } catch (\Exception $e) {
                    Log::error('Invalid timestamp for patient update', [
                        'patient_id' => $patientData['patient_id'],
                        'error' => $e->getMessage(),
                    ]);
                    return redirect()->back()->with('error', 'Invalid timestamp in patient data.');
                }
            }

            // Handle follow-ups
            foreach ($data['follow_ups'] as $followUpData) {
                try {
                    $importedCreatedAt = Carbon::parse($followUpData['created_at'])->setTimezone('Asia/Kolkata')->format('Y-m-d H:i:s');
                } catch (\Exception $e) {
                    Log::error('Invalid follow-up timestamp', [
                        'patient_id' => $patient->id,
                        'error' => $e->getMessage(),
                    ]);
                    continue;
                }

                // Check if follow-up exists
                $existingFollowUp = FollowUp::where('patient_id', $patient->id)
                    ->where('created_at', $importedCreatedAt)
                    ->exists();

                if ($existingFollowUp) {
                    Log::info('Skipped duplicate follow-up', [
                        'patient_id' => $patient->id,
                        'created_at' => $importedCreatedAt,
                    ]);
                    continue;
                }

                // Create new follow-up
                try {
                    $followUpData['patient_id'] = $patient->id;
                    $followUpData['doctor_id'] = User::where('name', $followUpData['doctor_name'])->first()->id ?? Auth::id();
                    $followUpData['created_at'] = $importedCreatedAt;
                    $followUpData['updated_at'] = isset($followUpData['updated_at'])
                        ? Carbon::parse($followUpData['updated_at'])->setTimezone('Asia/Kolkata')->format('Y-m-d H:i:s')
                        : $importedCreatedAt;
                    FollowUp::create($followUpData);
                    $followUpCount++;
                    Log::info('Created new follow-up', [
                        'patient_id' => $patient->id,
                        'created_at' => $importedCreatedAt,
                    ]);
                } catch (\Exception $e) {
                    Log::error('Failed to create follow-up', [
                        'patient_id' => $patient->id,
                        'error' => $e->getMessage(),
                    ]);
                    continue;
                }
            }

            // Track follow-ups
            if ($followUpCount > 0) {
                $importDetails['total_follow_ups'][] = [
                    'patient_id' => $patientData['patient_id'],
                    'name' => $patientData['name'],
                    'follow_ups_added' => $followUpCount,
                ];
            }

            // Log import summary
            Log::info('Single patient import summary', $importDetails);

            return redirect()->route('patients.index')->with([
                'success' => 'Patient data imported successfully.',
                'import_details' => $importDetails,
            ]);
        } catch (\Exception $e) {
            Log::error('Error importing JSON file: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to import JSON file: ' . $e->getMessage());
        }
    }
}
