<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
// use App\Http\Controllers\Controller;
use Illuminate\Routing\Controller;
use Illuminate\Support\Str;
use App\Models\FollowUp;
// use Knp\Snappy\Pdf;
use PDF;
use Carbon\Carbon;


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
        $query = Patient::query();

        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where('name', 'like', "%{$searchTerm}%")
                ->orWhere('mobile_phone', 'like', "%{$searchTerm}%")
                ->orWhere('patient_id', 'like', "%{$searchTerm}%");
        }
        $patients = $query->orderBy('name')->paginate(10);
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
            // 'patient_id' => ['required', 'string', 'unique:patients,patient_id']
        ]);

        // Manually handle the birthdate:
        if ($request->filled('birthdate')) {
            $validatedData['birthdate'] = Carbon::parse($request->birthdate)->format('Y-m-d');
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


        return redirect()->route('patients.index')->with('success', 'Patient Created Successfully.');
    }


    /**
     * Display the specified resource.
     */
    public function show(Patient $patient)
    {

        // $patient->followUps = $patient->followUps->sortByDesc('created_at');

        $patient->followUps = $patient->followUps()->orderBy('created_at', 'desc')->paginate(5); // Paginate follow-ups

        $patient->reports = $patient->reports()->get(); // Add reports

        // Calculate outstanding balance
        $latestFollowUp = $patient->followUps()->orderBy('created_at', 'desc')->first();

        if ($latestFollowUp) {
            $checkUpInfo = json_decode($latestFollowUp->check_up_info, true);
            $totalDueAll = $checkUpInfo['balance'] ?? 0;
        } else {
            $totalDueAll = 0;
        }


        // dd($patient);
        return view('patients.show', compact('patient', 'totalDueAll'));
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
            'email_id' => ['nullable', 'email', 'max:255'],
            'vishesh' => ['nullable', 'string'],
            'balance' => ['nullable', 'numeric'],
            // 'patient_id' => ['required', 'string', 'unique:patients,patient_id,' . $patient->id]
        ]);
        $patient->update($request->all());

        return redirect()->route('patients.index')->with('success', 'Patient Updated Successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Patient $patient)
    {
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
}
