<?php

namespace App\Http\Controllers;

use App\Models\FollowUp;
use App\Models\Parameter;
use App\Models\Patient;
use App\Models\Branch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Routing\Controller;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Auth as FacadesAuth;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Log;

use Maatwebsite\Excel\Facades\Excel;
use App\Exports\FollowUpExport;
use App\Models\Upload;
use Illuminate\Support\Facades\Storage;




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
        // Calculate total due using simple subtraction
        $totalBilled = $patient->followUps()->sum('amount_billed');
        $totalPaid = $patient->followUps()->sum('amount_paid');
        $totalDueAll = $totalBilled - $totalPaid;

        $parameters = Parameter::orderBy('display_order')->get();

        $followUps = $patient->followUps()
            ->orderBy('created_at', 'desc')
            // ->take(2)
            ->get();

        return view('followups.create', compact('patient', 'parameters', 'followUps', 'totalDueAll'));
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
            'amount_billed' => ['required', 'numeric'],
            'amount_paid' => ['required', 'numeric'],
            'photos.*' => ['sometimes', 'image', 'mimes:jpeg,png,jpg', 'max:2048'],
            'photo_types' => ['sometimes', 'string', 'json'], // JSON string of types
        ]);

        // Decode photo types
        $photoTypes = $request->input('photo_types') ? json_decode($request->photo_types, true) : [];
        if (!is_array($photoTypes)) {
            $photoTypes = []; // Fallback if JSON decoding fails
        }
        $patient = Patient::findOrFail($request->patient_id);
        $patientName = str_replace(' ', '_', trim($patient->name));

        // // Handle multiple file uploads
        // $uploads = [];
        // if ($request->hasFile('photos')) {
        //     foreach ($request->file('photos') as $index => $photo) {
        //         $filePath = $photo->store('uploads', 'local');
        //         $photoType = $photoTypes[$index] ?? 'patient_photo'; // Fallback if type missing

        //         $upload = Upload::create([
        //             'patient_id' => $request->patient_id,
        //             'follow_up_id' => null, // Updated later
        //             'photo_type' => $photoType,
        //             'file_path' => $filePath,
        //         ]);
        //         $uploads[] = $upload;
        //     }
        // }

        // Get the last follow-up for the patient
        $lastFollowUp = FollowUp::where('patient_id', $request->patient_id)
            ->latest()
            ->first();

        // Calculate previous due
        $previous_due = $lastFollowUp ? $lastFollowUp->total_due : 0;

        // Ensure amount paid does not exceed total due
        // $amount_paid = min($request->amount_paid, ($request->amount_billed + $previous_due));

        $amount_paid = $request->amount_paid;  // Allow any amount to be paid


        $checkUpInfo = [];
        foreach ($request->except(['_token', 'patient_id', 'diagnosis', 'treatment', 'amount_billed', 'amount_paid']) as $key => $value) {
            $checkUpInfo[$key] = $value;
        }

        // Adding user and branch info to $checkUpInfo
        $checkUpInfo['user_id'] = Auth::id();
        $checkUpInfo['user_name'] = Auth::user()->name;
        $checkUpInfo['branch_id'] = session('branch_id');
        $checkUpInfo['branch_name'] = session('branch_name');

        $followUp = FollowUp::create([
            'patient_id' => $request->patient_id,
            'doctor_id' => Auth::id(),
            'check_up_info' => json_encode($checkUpInfo),
            'diagnosis' => $request->diagnosis,
            'treatment' => $request->treatment,
            'amount_billed' => $request->amount_billed,
            'amount_paid' => $amount_paid, // Ensuring it does not exceed the total_due

        ]);

        // Handling multiple file uploads after follow-up creation
        if ($request->hasFile('photos')) {
            foreach ($request->file('photos') as $index => $photo) {
                $photoType = $photoTypes[$index] ?? 'patient_photo';
                $extension = $photo->getClientOriginalExtension(); // e.g., "png"
                $baseName = "{$patientName}_{$photoType}"; // e.g., "JohnDoe_PatientPhoto"

                // Find the next available number
                $counter = 1;
                $fileName = "{$baseName}_{$counter}.{$extension}";
                while (Storage::disk('local')->exists("uploads/{$fileName}")) {
                    $counter++;
                    $fileName = "{$baseName}_{$counter}.{$extension}";
                }

                // Store the file with the custom name
                $filePath = $photo->storeAs('uploads', $fileName, 'local');
                $photoType = $photoTypes[$index] ?? 'patient_photo'; // Fallback

                // Creating upload with follow_up_id immediately
                Upload::create([
                    'patient_id' => $request->patient_id,
                    'follow_up_id' => $followUp->id, // Set directly
                    'photo_type' => $photoType,
                    'file_path' => $filePath,
                ]);
            }
        }

        // // Link upload to follow-up
        // if ($upload) {
        //     $upload->update(['follow_up_id' => $followUp->id]);
        // }

        return Redirect::route('patients.show', $request->patient_id)->with('success', 'Follow Up Created Successfully');
    }

    public function index(Request $request)
    {
        $branches = FollowUp::selectRaw("JSON_UNQUOTE(JSON_EXTRACT(check_up_info, '$.branch_name')) as branch_name")
            ->whereNotNull(DB::raw("JSON_UNQUOTE(JSON_EXTRACT(check_up_info, '$.branch_name'))"))
            ->where(DB::raw("JSON_UNQUOTE(JSON_EXTRACT(check_up_info, '$.branch_name'))"), '!=', '')
            ->distinct()
            ->pluck('branch_name'); // Get unique branch names from JSON

        // Default to "all"
        $selectedBranch = $request->input('branch_name', 'all');

        $query = FollowUp::whereHas('patient'); // Ensures only follow-ups with patients are fetched

        // Apply branch filter only if a specific branch is selected
        if ($selectedBranch !== 'all' && !empty($selectedBranch)) {
            $query->whereRaw("JSON_UNQUOTE(JSON_EXTRACT(check_up_info, '$.branch_name')) = ?", [$selectedBranch]);
        }
        if($request->input('doctor') != 'all'){
            $query->where('doctor_id', $request->input('doctor'));
        }

        // Apply date filter if selected
        if ($request->filled('from_date') && $request->filled('to_date')) {
            $from = Carbon::parse($request->from_date)->startOfDay();
            $to = Carbon::parse($request->to_date)->endOfDay();
            $query->whereBetween('created_at', [$from, $to]);
        }

        // Clone query for summary calculations (to keep totals constant across pagination)
        $queryClone = clone $query;

        // Total Income Calculationyy
        $totalIncome = $queryClone->sum('amount_paid');

        $totalPatients = FollowUp::whereIn('id', $queryClone->pluck('id'))
            ->distinct('patient_id')
            ->count('patient_id');
        $totalFollowUps = $queryClone->count();

        // Balance Calculation.. Total Outstanding Due**
        $totalBilled = $queryClone->sum('amount_billed');
        $totalPaid = $queryClone->sum('amount_paid');
        $totalDueAll = $totalBilled - $totalPaid; // Ensure negative balance

        // Apply pagination AFTER summary calculation
        $followUps = $query->latest()->paginate(10);

        return view('followups.index', compact('followUps', 'totalIncome', 'totalPatients', 'totalFollowUps', 'branches', 'selectedBranch', 'totalDueAll'));
    }






    public function show(FollowUp $followup)
    {
        return view('followups.show', compact('followups'));
    }



    // For editing followup

    public function edit(FollowUp $followup)
    {
        $checkUpInfo = json_decode($followup->check_up_info, true) ?? []; // Decode check_up_info

        // Fetch values directly from FollowUp model
        $totalDueAll = $followup->total_due_all ?? 0;
        $totalDue = $followup->balance ?? 0; // Assuming balance is stored here
        $amountBilled = $followup->amount_billed ?? '';
        $amountPaid = $followup->amount_paid ?? '';

        $parameters = Parameter::all();

        return view('followups.edit', compact(
            'followup',
            'parameters',
            'checkUpInfo',
            'totalDueAll',
            'totalDue',
            'amountBilled',
            'amountPaid'
        ));
    }


    public function update(Request $request, FollowUp $followup)
    {
        $request->validate([
            'patient_id' => ['required', 'exists:patients,id'],
            'diagnosis' => ['nullable', 'string'],
            'treatment' => ['nullable', 'string'],
            'amount_billed' => ['required', 'numeric'],
            'amount_paid' => ['required', 'numeric'],
        ]);

        // Decode
        $existingCheckUpInfo = json_decode($followup->check_up_info, true) ?? [];

        // Extract new check_up_info fields from the request
        $newCheckUpInfo = [];
        foreach ($request->except(['_token', 'patient_id', 'diagnosis', 'treatment', 'chikitsa_combo', 'amount_billed', 'amount_paid']) as $key => $value) {
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
            'amount_billed' => $request->amount_billed,
            'amount_paid' => $request->amount_paid,
        ]);

        return redirect()->route('patients.show', $request->patient_id)->with('success', 'Follow Up Updated Successfully');
    }


    public function destroy(FollowUp $followup)
    {
        $patientId = $followup->patient_id;
        $followup->delete();
        return redirect()->route('patients.show', $patientId)->with('success', 'Follow Up Deleted Successfully');
    }

    public function exportFollowUps(Request $request)
    {
        return Excel::download(new FollowUpExport($request), 'followups.csv', \Maatwebsite\Excel\Excel::CSV, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }
}
