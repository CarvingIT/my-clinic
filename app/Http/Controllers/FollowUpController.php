<?php

namespace App\Http\Controllers;

use App\Models\FollowUp;
use App\Models\Parameter;
use App\Models\Patient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Routing\Controller;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Auth as FacadesAuth;

class FollowUpController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $patientId = $request->patient;
        $patient = Patient::find($patientId);
        if (!$patient) {
            abort(404);
        }
        $parameters = Parameter::orderBy('display_order')->get();
        return view('followups.create', compact('patient', 'parameters'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'patient_id' => ['required', 'exists:patients,id'],
            'diagnosis' => ['nullable', 'string'],
            'treatment' => ['nullable', 'string'],
            // 'amount' => ['nullable', 'numeric'],
            // 'balance' => ['nullable', 'numeric'],
            // 'payment_method' => ['nullable', 'string'],
            // 'certificate' => ['nullable', 'string'],
            // 'drawing' => ['nullable', 'string'],
            // 'nidan' => ['nullable', 'string'],
            // 'upashay' => ['nullable', 'string'],
            // 'salla' => ['nullable', 'string'],
        ]);

        $checkUpInfo = [];
        foreach ($request->except(['_token', 'patient_id', 'diagnosis', 'treatment']) as $key => $value) {
            $checkUpInfo[$key] = $value;
        }

        // Adding user and branch info to $checkUpInfo
        $checkUpInfo['user_id'] = Auth::id();
        $checkUpInfo['user_name'] = Auth::user()->name;
        $checkUpInfo['branch_id'] = session('branch_id');
        $checkUpInfo['branch_name'] = session('branch_name');


        FollowUp::create([
            'patient_id' => $request->patient_id,
            'check_up_info' => json_encode($checkUpInfo),
            'diagnosis' => $request->diagnosis,
            'treatment' => $request->treatment,
            // 'nidan' => $request->nidan,
            // 'upashay' => $request->upashay,
            // 'salla' => $request->salla,
            // 'amount' => $request->amount,
            // 'balance' => $request->balance,
        ]);

        return Redirect::route('patients.show', $request->patient_id)->with('success', 'Follow Up Created Successfully');
    }

    public function index()
    {
        $followUps = FollowUp::with('patient')->orderBy('created_at', 'desc')->paginate(10); // Fetching follow-ups with patient details in desc order
        return view('followups.index', compact('followUps'));
    }
    public function show(FollowUp $followup)
    {



        return view('followups.show', compact('followup', 'followUps', 'totalDueAll', 'patient'));
    }



    // For editing followup

    public function edit(FollowUp $followup)
    {
        $checkUpInfo = json_decode($followup->check_up_info, true); // Decode
        $parameters = Parameter::all();
        return view('followups.edit', compact('followup', 'parameters', 'checkUpInfo'));
    }

    public function update(Request $request, FollowUp $followup)
    {
        $request->validate([
            'patient_id' => ['required', 'exists:patients,id'],
            'diagnosis' => ['nullable', 'string'],
            'treatment' => ['nullable', 'string'],
        ]);

        // Decode
        $existingCheckUpInfo = json_decode($followup->check_up_info, true) ?? [];

        // Extract new check_up_info fields from the request
        $newCheckUpInfo = [];
        foreach ($request->except(['_token', 'patient_id', 'diagnosis', 'treatment', 'chikitsa_combo']) as $key => $value) {
            $newCheckUpInfo[$key] = $value;
        }

        // Preserve existing username and branch unless updated
        if (!isset($newCheckUpInfo['user_name']) && isset($existingCheckUpInfo['user_name'])) {
            $newCheckUpInfo['user_name'] = $existingCheckUpInfo['user_name'];
        }
        if (!isset($newCheckUpInfo['branch_name']) && isset($existingCheckUpInfo['branch_name'])) {
            $newCheckUpInfo['branch_name'] = $existingCheckUpInfo['branch_name'];
        }


        // Merge existing and new check_up_info
        $updatedCheckUpInfo = array_merge($existingCheckUpInfo, $newCheckUpInfo);

        // Update the follow-up
        $followup->update([
            'check_up_info' => json_encode($updatedCheckUpInfo),
            'diagnosis' => $request->diagnosis,
            'treatment' => $request->treatment,
        ]);

        return redirect()->route('patients.show', $request->patient_id)->with('success', 'Follow Up Updated Successfully');
    }


    public function destroy(FollowUp $followup)
    {
        $patientId = $followup->patient_id;
        $followup->delete();
        return redirect()->route('patients.show', $patientId)->with('success', 'Follow Up Deleted Successfully');
    }
}
