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
        if (auth::user()->hasRole('staff')) {
            return redirect()->route('patients.index')->with('error', 'Unauthorized: Staff cannot delete patients.');
        }

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

    // public function exportPatientJSON(Patient $p){
    //     $patient = $p->first();
    //     $follow_ups = $patient->followUps;
    //     foreach($follow_ups as $f){
    //         $doctor = User::find($f->doctor_id);
    //         $f->doctor_name = $doctor->name;
    //         unset($f->doctor_id);
    //         unset($f->patient_id);
    //         unset($f->id);
    //         $follow_ups[] = $f;
    //     }
    //     unset($patient->id);
    //     return ['patient'=>$patient, 'follow_ups' => $follow_ups ];
    // We should send an email with attachment of json file ${PID}.json
    // The email should go to the email address given in the pop up
    // }

    // public function importPatientJSON(Request $req){
    // get uploaded json file
    // validate
    // take the contents in a variable (json)
    // check if the patient_id exists
    // if not, create a new patient
    // get the patient model, update if (updated_at) is later than the system's updated_at
    // save patient
    //
    // get follow ups array from the uploaded file
    // foreach follow up,
    // Get OPD id based on the branch_name
    // Use that branch id when you create the follow ups.
    // check if a follow up exists for the current patient where(created_at) matches the data in the file.
    // store new follow up if created_at does not exist.
    // }


    public function exportAllPatientsJSON(Request $request)
    {
        // Validate email input from the pop-up
        $request->validate([
            'email' => ['required', 'email', 'max:255'],
        ]);

        // Fetch all patients with their follow-ups
        $patients = Patient::with('followUps')->get()->map(function ($patient) {
            $followUps = $patient->followUps->map(function ($followUp) {
                $doctor = User::find($followUp->doctor_id);
                $followUp->doctor_name = $doctor ? $doctor->name : 'Unknown';
                // Convert created_at to Asia/Kolkata
                $followUp->created_at = Carbon::parse($followUp->created_at)->setTimezone('Asia/Kolkata')->format('Y-m-d H:i:s');
                unset($followUp->doctor_id, $followUp->patient_id, $followUp->id);
                return $followUp;
            });

            // Remove sensitive fields from patient
            $patientData = $patient->toArray();
            // Convert updated_at to Asia/Kolkata
            $patientData['updated_at'] = Carbon::parse($patientData['updated_at'])->setTimezone('Asia/Kolkata')->format('Y-m-d H:i:s');
            unset($patientData['id']);

            return [
                'patient' => $patientData,
                'follow_ups' => $followUps,
            ];
        });

        // Ensure the exports directory exists
        Storage::disk('local')->makeDirectory('exports');

        // Generate JSON file
        $fileName = 'clinic_data_' . Carbon::now()->format('Ymd_His') . '.json';
        $filePath = "exports/{$fileName}";
        $jsonContent = json_encode(['patients' => $patients], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

        // Write the file and log the path
        Storage::disk('local')->put($filePath, $jsonContent);
        $fullPath = Storage::path($filePath);
        Log::info('JSON file created at: ' . $fullPath);

        // Verify file exists
        if (!file_exists($fullPath)) {
            Log::error('JSON file not found at: ' . $fullPath);
            return redirect()->route('patients.index')->with('error', 'Failed to create JSON file.');
        }

        // Send email with attachment
        Mail::send('emails.clinic_data_export', [], function ($message) use ($request, $fullPath, $fileName) {
            $message->to($request->email)
                ->subject('Clinic Data Export')
                ->attach($fullPath, [
                    'as' => $fileName,
                    'mime' => 'application/json',
                ]);
        });

        // Clean up temporary file
        Storage::disk('local')->delete($filePath);
        Log::info('JSON file deleted: ' . $fullPath);

        return redirect()->route('patients.index')->with('success', 'Clinic data exported and emailed successfully.');
    }


    public function importAllPatientsJSON(Request $request)
    {
        // Validate uploaded file
        $request->validate([
            'file' => ['required', 'file', 'max:2048'],
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

            // Validate JSON structure
            if (json_last_error() !== JSON_ERROR_NONE) {
                Log::error('Invalid JSON content: ' . json_last_error_msg());
                return redirect()->back()->with('error', 'Invalid JSON file content.');
            }

            if (!$data || !isset($data['patients']) || !is_array($data['patients'])) {
                Log::error('Invalid JSON structure: Missing patients array');
                return redirect()->back()->with('error', 'Invalid JSON file structure.');
            }

            // Process each patient
            foreach ($data['patients'] as $patientEntry) {
                if (!isset($patientEntry['patient']) || !isset($patientEntry['follow_ups'])) {
                    Log::warning('Skipping invalid patient entry', $patientEntry);
                    continue;
                }

                $patientData = $patientEntry['patient'];
                $patient = Patient::where('patient_id', $patientData['patient_id'])->first();

                // Handle patient
                if (!$patient) {
                    $patient = Patient::create($patientData);
                    Log::info('Created new patient', ['patient_id' => $patientData['patient_id']]);
                } elseif (Carbon::parse($patientData['updated_at'])->gt($patient->updated_at)) {
                    $patient->update($patientData);
                    Log::info('Updated patient', ['patient_id' => $patientData['patient_id']]);
                } else {
                    Log::info('Skipped patient update (not newer)', ['patient_id' => $patientData['patient_id']]);
                }

                // Handle follow-ups
                foreach ($patientEntry['follow_ups'] as $followUpData) {
                    // Normalize JSON created_at to Asia/Kolkata and second precision
                    $importedCreatedAt = Carbon::parse($followUpData['created_at'])->setTimezone('Asia/Kolkata')->format('Y-m-d H:i:s');

                    // Log JSON timestamp
                    Log::info('Processing follow-up', [
                        'patient_id' => $patient->id,
                        'json_created_at' => $followUpData['created_at'],
                        'normalized_created_at' => $importedCreatedAt,
                    ]);

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

                    // Log existing follow-ups for debugging
                    $existingTimestamps = FollowUp::where('patient_id', $patient->id)
                        ->pluck('created_at')
                        ->map(fn($date) => Carbon::parse($date)->setTimezone('Asia/Kolkata')->format('Y-m-d H:i:s'))
                        ->toArray();
                    Log::info('Existing follow-up timestamps', [
                        'patient_id' => $patient->id,
                        'timestamps' => $existingTimestamps,
                    ]);

                    // Create new follow-up
                    $followUpData['patient_id'] = $patient->id;
                    $followUpData['doctor_id'] = User::where('name', $followUpData['doctor_name'])->first()->id ?? Auth::id();
                    $followUpData['created_at'] = $importedCreatedAt;
                    FollowUp::create($followUpData);
                    Log::info('Created new follow-up', [
                        'patient_id' => $patient->id,
                        'created_at' => $importedCreatedAt,
                    ]);
                }
            }

            return redirect()->route('patients.index')->with('success', 'Clinic data imported successfully.');
        } catch (\Exception $e) {
            Log::error('Error importing JSON file: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to import JSON file: ' . $e->getMessage());
        }
    }
}
