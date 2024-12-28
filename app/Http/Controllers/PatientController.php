<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
// use App\Http\Controllers\Controller;
use Illuminate\Routing\Controller;
use Illuminate\Support\Str;

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
                ->orWhere('mobile_phone', 'like', "%{$searchTerm}%");
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
            'occupation' => ['required', 'string', 'max:255'],
            'mobile_phone' => ['required', 'string', 'max:20', 'regex:/^[0-9]{10}$/'],
            'remark' => ['nullable', 'string'],
            'gender' => ['nullable', 'string'],
            'birthdate' => ['nullable', 'date'],
            'email_id' => ['nullable', 'email', 'max:255'],
            'vishesh' => ['nullable', 'string'],
            'balance' => ['nullable', 'numeric'],
            'patient_id' => ['nullable', 'string', 'unique:patients,patient_id']
        ]);


        Patient::create($request->all());

        return redirect()->route('patients.index')->with('success', 'Patient Created Successfully.');
    }


    /**
     * Display the specified resource.
     */
    public function show(Patient $patient)
    {
        // dd($patient);
        return view('patients.show', compact('patient'));
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
            'occupation' => ['required', 'string', 'max:255'],
            'mobile_phone' => ['required', 'string', 'max:20', 'regex:/^[0-9]{10}$/'],
            'remark' => ['nullable', 'string'],
            'gender' => ['nullable', 'string'],
            'birthdate' => ['nullable', 'date'],
            'email_id' => ['nullable', 'email', 'max:255'],
            'vishesh' => ['nullable', 'string'],
            'balance' => ['nullable', 'numeric'],
            'patient_id' => ['nullable', 'string', 'unique:patients,patient_id']
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
}
