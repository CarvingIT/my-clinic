<?php

namespace App\Http\Controllers;

use App\Models\FollowUp;
use App\Models\Parameter;
use App\Models\Patient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Routing\Controller;

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
         if(!$patient){
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
          ]);

       $checkUpInfo = [];
       foreach ($request->except(['_token', 'patient_id', 'diagnosis', 'treatment']) as $key => $value)
       {
           $checkUpInfo[$key] = $value;
       }

        FollowUp::create([
             'patient_id' => $request->patient_id,
             'check_up_info' => json_encode($checkUpInfo),
             'diagnosis' => $request->diagnosis,
            'treatment' => $request->treatment,
         ]);

        return Redirect::route('patients.show', $request->patient_id)->with('success','Follow Up Created Successfully');

    }

}
