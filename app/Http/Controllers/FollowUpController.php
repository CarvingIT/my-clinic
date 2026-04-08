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
        // $this->middleware(['auth', 'doctor'])->except(['index']);
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

        // Explicitly handle reports field
        $checkUpInfo['reports'] = json_decode($request->input('reports', '[]'), true) ?? [];

        // Explicitly handle nadi_dots field
        $checkUpInfo['nadi_dots'] = json_decode($request->input('nadi_dots', '[[], [], []]'), true) ?? [[], [], []];

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

        $followUp->patient->update(['vishesh' => $request->vishesh]);

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
        // Fetching distinct branches from the master branches table.

        $branches = Branch::pluck('name'); // actual branch column

        // Get filter inputs with defaults
        $selectedBranch = $request->input('branch_name', 'all');
        $selectedDoctor = $request->input('doctor', 'all');
        $timePeriod = $request->input('time_period', 'all');
        $fromDate = $request->input('from_date');
        $toDate = $request->input('to_date');

        // Base query: only follow-ups with a related patient
        $query = FollowUp::whereHas('patient');

        // Apply branch filter (branch_name stored in JSON field)
        if ($selectedBranch !== 'all' && !empty($selectedBranch)) {
            $query->whereRaw("JSON_UNQUOTE(JSON_EXTRACT(check_up_info, '$.branch_name')) = ?", [$selectedBranch]);
        }

        // Apply doctor filter
        if ($selectedDoctor !== 'all') {
            $query->whereRaw("JSON_UNQUOTE(JSON_EXTRACT(check_up_info, '$.user_name')) = ?", [$selectedDoctor]);
        }

        // Apply time filters
        if ($timePeriod !== 'all') {
            switch ($timePeriod) {
                case 'today':
                    $query->whereDate('created_at', Carbon::today());
                    break;
                case 'last_week':
                    $query->whereBetween('created_at', [
                        Carbon::now()->subWeek()->startOfWeek(),
                        Carbon::now()->subWeek()->endOfWeek(),
                    ]);
                    break;
                case 'this_month':
                    $query->whereBetween('created_at', [
                        Carbon::now()->startOfMonth(),
                        Carbon::now()->endOfMonth(),
                    ]);
                    break;
                case 'last_month':
                    // Prevent Carbon overflow (e.g., Mar 31 -> Feb 28 instead of Mar 3)
                    $query->whereBetween('created_at', [
                        Carbon::now()->startOfMonth()->subMonth()->startOfMonth(),
                        Carbon::now()->startOfMonth()->subMonth()->endOfMonth(),
                    ]);
                    break;
                case 'last_3_months':
                    // Current month + previous 2 months (e.g., Jan 1 to Mar 31)
                    $query->whereBetween('created_at', [
                        Carbon::now()->startOfMonth()->subMonths(2)->startOfMonth(),
                        Carbon::now()->endOfMonth(),
                    ]);
                    break;
                case 'last_6_months':
                    $query->whereBetween('created_at', [
                        Carbon::now()->startOfMonth()->subMonths(5)->startOfMonth(),
                        Carbon::now()->endOfMonth(),
                    ]);
                    break;
                case 'last_12_months':
                    $query->whereBetween('created_at', [
                        Carbon::now()->startOfMonth()->subMonths(11)->startOfMonth(),
                        Carbon::now()->endOfMonth(),
                    ]);
                    break;
            }
        } else {
            if ($fromDate) {
                $query->whereDate('created_at', '>=', Carbon::parse($fromDate)->startOfDay());
            }
            if ($toDate) {
                $query->whereDate('created_at', '<=', Carbon::parse($toDate)->endOfDay());
            }
        }

        // Clone for summary data before pagination
        $summaryQuery = clone $query;

        // Calculate summary values
        $totalIncome = $summaryQuery->sum('amount_paid');

        // Use pluck ids to ensure consistency
        $followUpIds = $summaryQuery->pluck('id');

        $totalPatients = FollowUp::whereIn('id', $followUpIds)
            ->distinct('patient_id')
            ->count('patient_id');

        $totalFollowUps = $summaryQuery->count();

        $totalBilled = $summaryQuery->sum('amount_billed');
        $totalPaid = $summaryQuery->sum('amount_paid');
        // We will calculate later to only sum positive dues

        // Payment methods count grouped by method
        $paymentCounts = (clone $summaryQuery)
            ->selectRaw("LOWER(TRIM(JSON_UNQUOTE(JSON_EXTRACT(check_up_info, '$.payment_method')))) as method, COUNT(*) as count")
            ->groupBy('method')
            ->pluck('count', 'method');

        $cashPayments = $paymentCounts['cash'] ?? 0;
        $onlinePayments = $paymentCounts['online'] ?? 0;

        // Fetch detailed data for the modals
        $cashFollowUps = (clone $summaryQuery)
            ->whereRaw("LOWER(TRIM(JSON_UNQUOTE(JSON_EXTRACT(check_up_info, '$.payment_method')))) = 'cash'")
            ->with(['patient' => function($q) { $q->select('id', 'name'); }])
            ->latest()
            ->get(['id', 'patient_id', 'amount_paid', 'created_at']);

        $onlineFollowUps = (clone $summaryQuery)
            ->whereRaw("LOWER(TRIM(JSON_UNQUOTE(JSON_EXTRACT(check_up_info, '$.payment_method')))) = 'online'")
            ->with(['patient' => function($q) { $q->select('id', 'name'); }])
            ->latest()
            ->get(['id', 'patient_id', 'amount_paid', 'created_at']);
        $allFollowUpsList = (clone $summaryQuery)
            ->with(['patient' => function($q) { $q->select('id', 'name'); }])
            ->latest()
            ->get(['id', 'patient_id', 'amount_paid', 'amount_billed', 'created_at']);

        $patientIds = $allFollowUpsList->pluck('patient_id')->unique();
        $patientsList = \App\Models\Patient::withSum('followUps', 'amount_billed')
            ->withSum('followUps', 'amount_paid')
            ->whereIn('id', $patientIds)
            ->get(['id', 'name', 'mobile_phone', 'created_at']);

        $patientBalances = $patientsList->mapWithKeys(function($p) {
            $bal = ($p->follow_ups_sum_amount_billed ?? 0) - ($p->follow_ups_sum_amount_paid ?? 0);
            return [$p->id => $bal];
        });

        $paidFollowUpsList = $allFollowUpsList->filter(function($fu) {
            return $fu->amount_paid > 0;
        });

        $dueFollowUpsList = $allFollowUpsList->filter(function($fu) {
            return ($fu->amount_billed - $fu->amount_paid) > 0;
        });

        // Calculate "Real Due" considering patient's global net advances
        $patientRealDueAllocation = [];
        foreach ($patientBalances as $pid => $bal) {
            // A patient can never owe more than their global positive net balance
            $patientRealDueAllocation[$pid] = max(0, $bal);
        }

        $totalDueAll = 0;
        foreach ($dueFollowUpsList as $fu) {
            $visitDue = $fu->amount_billed - $fu->amount_paid;
            $owedGlobally = $patientRealDueAllocation[$fu->patient_id] ?? 0;

            $realDue = min($visitDue, $owedGlobally);

            if (isset($patientRealDueAllocation[$fu->patient_id])) {
                $patientRealDueAllocation[$fu->patient_id] -= $realDue;
            }

            $fu->real_due = $realDue; // Inject for the view
            $totalDueAll += $realDue;
        }


        // Paginate main data for display (15 items per page matches AJAX endpoint)
        $followUps = $query->latest()->paginate(15);

        // Prepare chart data (daily, monthly, yearly)
        $commonFilters = function ($q) use ($request, $selectedBranch, $selectedDoctor) {
            if ($request->filled('from_date')) {
                $q->whereDate('created_at', '>=', $request->from_date);
            }
            if ($request->filled('to_date')) {
                $q->whereDate('created_at', '<=', $request->to_date);
            }
            if ($selectedBranch !== 'all' && !empty($selectedBranch)) {
                $q->whereRaw("JSON_UNQUOTE(JSON_EXTRACT(check_up_info, '$.branch_name')) = ?", [$selectedBranch]);
            }
            if ($selectedDoctor !== 'all') {
                $q->whereRaw("JSON_UNQUOTE(JSON_EXTRACT(check_up_info, '$.user_name')) = ?", [$selectedDoctor]);
            }
        };

        $followUpFrequencyDaily = FollowUp::selectRaw('DATE(created_at) as raw_date, DATE_FORMAT(created_at, "%d-%m-%y") as date, COUNT(*) as count')
            ->whereHas('patient')
            ->when(true, $commonFilters)
            ->groupBy('raw_date', 'date')
            ->orderBy('raw_date', 'asc')
            ->get();

        $followUpFrequencyMonthly = FollowUp::selectRaw('DATE_FORMAT(created_at, "%m-%Y") as month, COUNT(*) as count')
            ->whereHas('patient')
            ->when(true, $commonFilters)
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        $followUpFrequencyYearly = FollowUp::selectRaw('YEAR(created_at) as year, COUNT(*) as count')
            ->whereHas('patient')
            ->when(true, $commonFilters)
            ->groupBy('year')
            ->orderBy('year')
            ->get();

        // Age Distribution Chart
        $ageDistribution = Patient::whereHas('followUps', function ($q) use ($commonFilters) {
            $commonFilters($q);
        })
            ->selectRaw('
        CASE
            WHEN birthdate IS NULL THEN "Unknown"
            WHEN TIMESTAMPDIFF(YEAR, birthdate, follow_ups.created_at) <= 18 THEN "0-18"
            WHEN TIMESTAMPDIFF(YEAR, birthdate, follow_ups.created_at) <= 45 THEN "19-45"
            ELSE "46+"
        END as age_group, COUNT(DISTINCT patients.id) as count
    ')
            ->join('follow_ups', function ($join) {
                $join->on('patients.id', '=', 'follow_ups.patient_id')
                    ->where('follow_ups.created_at', function ($q) {
                        $q->selectRaw('MAX(created_at)')
                            ->from('follow_ups')
                            ->whereColumn('patient_id', 'patients.id');
                    });
            })
            ->groupBy('age_group')
            ->orderByRaw('FIELD(age_group, "0-18", "19-45", "46+", "Unknown")')
            ->get();

        // Payment Status Chart
        $paymentStatus = FollowUp::selectRaw('DATE(created_at) as raw_date, DATE_FORMAT(created_at, "%d-%m-%y") as date, SUM(amount_billed) as billed, SUM(amount_paid) as paid, SUM(amount_billed - amount_paid) as due')
            ->whereHas('patient')
            ->when(true, $commonFilters)
            ->groupBy('raw_date', 'date')
            ->orderBy('raw_date', 'asc')
            ->get();

        // New vs Existing Patients
        $newVsExistingPatients = FollowUp::whereHas('patient')
            ->when(true, $commonFilters)
            ->groupBy('patient_id')
            ->selectRaw('COUNT(*) as followup_count')
            ->get()
            ->reduce(function ($carry, $item) {
                $carry[$item->followup_count == 1 ? 'new' : 'existing']++;
                return $carry;
            }, ['new' => 0, 'existing' => 0]);

        // Return the view with all variables
        return view('followups.index', compact(
            'followUps',
            'cashFollowUps',
            'onlineFollowUps',
            'allFollowUpsList',
            'patientsList',
            'patientBalances',
            'paidFollowUpsList',
            'dueFollowUpsList',
            'totalIncome',
            'totalPatients',
            'totalFollowUps',
            'branches',
            'selectedBranch',
            'totalDueAll',
            'followUpFrequencyDaily',
            'followUpFrequencyMonthly',
            'followUpFrequencyYearly',
            'ageDistribution',
            'paymentStatus',
            'newVsExistingPatients',
            'cashPayments',
            'onlinePayments'
        ));
    }

    /**
     * Fetch follow-ups for infinite scroll (AJAX endpoint)
     */
    public function fetchFollowUps(Request $request)
    {
        $page = $request->input('page', 1);
        $perPage = 15; // Items per scroll load

        // Get filter inputs
        $selectedBranch = $request->input('branch_name', 'all');
        $selectedDoctor = $request->input('doctor', 'all');
        $timePeriod = $request->input('time_period', 'all');
        $fromDate = $request->input('from_date');
        $toDate = $request->input('to_date');

        // Base query
        $query = FollowUp::whereHas('patient');

        // Apply filters
        if ($selectedBranch !== 'all' && !empty($selectedBranch)) {
            $query->whereRaw("JSON_UNQUOTE(JSON_EXTRACT(check_up_info, '$.branch_name')) = ?", [$selectedBranch]);
        }

        if ($selectedDoctor !== 'all') {
            $query->whereRaw("JSON_UNQUOTE(JSON_EXTRACT(check_up_info, '$.user_name')) = ?", [$selectedDoctor]);
        }

        if ($timePeriod !== 'all') {
            switch ($timePeriod) {
                case 'today':
                    $query->whereDate('created_at', Carbon::today());
                    break;
                case 'last_week':
                    $query->whereBetween('created_at', [
                        Carbon::now()->subWeek()->startOfWeek(),
                        Carbon::now()->subWeek()->endOfWeek(),
                    ]);
                    break;
                case 'this_month':
                    $query->whereBetween('created_at', [
                        Carbon::now()->startOfMonth(),
                        Carbon::now()->endOfMonth(),
                    ]);
                    break;
                case 'last_month':
                    $query->whereBetween('created_at', [
                        Carbon::now()->startOfMonth()->subMonth()->startOfMonth(),
                        Carbon::now()->startOfMonth()->subMonth()->endOfMonth(),
                    ]);
                    break;
                case 'last_3_months':
                    $query->whereBetween('created_at', [
                        Carbon::now()->startOfMonth()->subMonths(2)->startOfMonth(),
                        Carbon::now()->endOfMonth(),
                    ]);
                    break;
                case 'last_6_months':
                    $query->whereBetween('created_at', [
                        Carbon::now()->startOfMonth()->subMonths(5)->startOfMonth(),
                        Carbon::now()->endOfMonth(),
                    ]);
                    break;
                case 'last_12_months':
                    $query->whereBetween('created_at', [
                        Carbon::now()->startOfMonth()->subMonths(11)->startOfMonth(),
                        Carbon::now()->endOfMonth(),
                    ]);
                    break;
            }
        } else {
            if ($fromDate) {
                $query->whereDate('created_at', '>=', Carbon::parse($fromDate)->startOfDay());
            }
            if ($toDate) {
                $query->whereDate('created_at', '<=', Carbon::parse($toDate)->endOfDay());
            }
        }

        // Get paginated results
        $followUps = $query->latest()->paginate($perPage, ['*'], 'page', $page);

        // Transform data for JSON response
        $rows = $followUps->items();
        $html = '';

        $renderedCount = 0;
        foreach ($rows as $followUp) {
            if ($followUp->patient) {
            $renderedCount++;
                $checkUpInfo = json_decode($followUp->check_up_info, true);
                $paymentMethod = $checkUpInfo['payment_method'] ?? 'N/A';

                $colorClass = $followUp->amount_paid < $followUp->amount_billed
                    ? 'text-red-600 dark:text-red-400'
                    : 'text-indigo-700 dark:text-indigo-400';

                $amountColorClass = $followUp->amount_paid < $followUp->amount_billed
                    ? 'text-red-600 dark:text-red-400'
                    : ($followUp->amount_paid > $followUp->amount_billed
                        ? 'text-green-600 dark:text-green-300'
                        : 'text-blue-600 dark:text-blue-300');

                $html .= '<tr class="border-t border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-800 animate-fadeIn">';
                $html .= '<td class="text-left px-4 py-3">' . $followUp->created_at->format('d M Y, h:i A') . '</td>';
                $html .= '<td class="text-center px-4 py-3"><a href="' . route('patients.show', $followUp->patient->id) . '" class="font-semibold hover:underline ' . $colorClass . '">' . $followUp->patient->name . '</a></td>';
                $html .= '<td class="text-center px-4 py-3">' . ($checkUpInfo['user_name'] ?? 'N/A') . '</td>';
                $html .= '<td class="text-center px-4 py-3 font-semibold text-blue-600 dark:text-blue-300">₹' . $this->indFormat($followUp->amount_billed) . '</td>';
                $html .= '<td class="text-center px-4 py-3 font-semibold text-blue-600 dark:text-blue-300">' . $paymentMethod . '</td>';
                $html .= '<td class="text-right px-4 py-3 font-semibold ' . $amountColorClass . '">₹' . $this->indFormat($followUp->amount_paid) . '</td>';
                $html .= '</tr>';
            }
        }

        return response()->json([
            'html' => $html,
            'hasMore' => $followUps->hasMorePages(),
            'nextPage' => $page + 1,
            'currentPage' => $followUps->currentPage(),
            'total' => $followUps->total(),
            'shownCount' => (int) ($followUps->lastItem() ?? 0),
            'remainingCount' => max(0, (int) $followUps->total() - (int) ($followUps->lastItem() ?? 0)),
            'pageCount' => $renderedCount,
            'lastPage' => $followUps->lastPage(),
        ]);
    }

    private function indFormat($num) {
        $num = round((float)$num);
        return preg_replace('/(\d+?)(?=(\d\d)+(\d)(?!\d))/i', '\1,', (string)$num);
    }







    public function show(FollowUp $followup)
    {
        return view('followups.show', compact('followups'));
    }



    // For editing followup

    public function edit(FollowUp $followup)
    {
        $checkUpInfo = json_decode($followup->check_up_info, true) ?? []; // Decode check_up_info

        // Fetch the patient
        $patient = $followup->patient;

        // Fetch previous follow-ups for the patient, excluding the current follow-up
        $followUps = $patient->followUps()
            ->where('id', '!=', $followup->id)
            ->orderBy('created_at', 'desc')
            ->get();

        // Calculate total due
        $totalBilled = $patient->followUps()->sum('amount_billed');
        $totalPaid = $patient->followUps()->sum('amount_paid');
        $totalDueAll = $totalBilled - $totalPaid;

        // Fetch parameters
        $parameters = Parameter::all();

        // Fetch values directly from FollowUp model
        $totalDue = $totalDueAll; // as per create.blade.php
        $amountBilled = $followup->amount_billed ?? '';
        $amountPaid = $followup->amount_paid ?? '';

        return view('followups.edit', compact(
            'followup',
            'followUps',
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

        // Explicitly handle reports field
        $newCheckUpInfo['reports'] = json_decode($request->input('reports', '[]'), true) ?? [];

        // Explicitly handle nadi_dots field
        $newCheckUpInfo['nadi_dots'] = json_decode($request->input('nadi_dots', '[[], [], []]'), true) ?? [[], [], []];

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

        $followup->patient->update(['vishesh' => $request->vishesh]);

        return redirect()->route('patients.show', $request->patient_id)->with('success', 'Follow Up Updated Successfully');
    }


    public function destroy(FollowUp $followup)
    {
        $patientId = $followup->patient_id;
        $followup->delete();
        return redirect()->route('patients.show', $patientId)->with('success', 'Follow Up Deleted Successfully');
    }

    public function deleteReport(Request $request, FollowUp $followup, $reportIndex)
    {
        // Get the current check_up_info
        $checkUpInfo = json_decode($followup->check_up_info, true) ?? [];

        // Check if reports exist
        if (!isset($checkUpInfo['reports']) || !is_array($checkUpInfo['reports'])) {
            return response()->json(['success' => false, 'message' => 'No reports found'], 404);
        }

        // Check if the report index exists
        if (!isset($checkUpInfo['reports'][$reportIndex])) {
            return response()->json(['success' => false, 'message' => 'Report not found'], 404);
        }

        // Soft delete the report by adding deleted_at timestamp
        $checkUpInfo['reports'][$reportIndex]['deleted_at'] = now()->toISOString();

        // Save the updated check_up_info
        $followup->update([
            'check_up_info' => json_encode($checkUpInfo)
        ]);

        return response()->json(['success' => true, 'message' => 'Report deleted successfully']);
    }

    public function updateReport(Request $request, FollowUp $followup, $reportIndex)
    {
        $request->validate([
            'text' => 'required|string'
        ]);

        // Get the current check_up_info
        $checkUpInfo = json_decode($followup->check_up_info, true) ?? [];

        // Check if reports exist
        if (!isset($checkUpInfo['reports']) || !is_array($checkUpInfo['reports'])) {
            return response()->json(['success' => false, 'message' => 'No reports found'], 404);
        }

        // Check if the report index exists
        if (!isset($checkUpInfo['reports'][$reportIndex])) {
            return response()->json(['success' => false, 'message' => 'Report not found'], 404);
        }

        // Update the report text
        $checkUpInfo['reports'][$reportIndex]['text'] = $request->text;

        // Save the updated check_up_info
        $followup->update([
            'check_up_info' => json_encode($checkUpInfo)
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Report updated successfully',
            'report' => $checkUpInfo['reports'][$reportIndex]
        ]);
    }

    public function storeReport(Request $request, FollowUp $followup)
    {
        $request->validate([
            'text' => 'required|string',
            'timestamp' => 'nullable|string' // Optional timestamp from frontend
        ]);

        // Get the current check_up_info
        $checkUpInfo = json_decode($followup->check_up_info, true) ?? [];

        // Initialize reports array if it doesn't exist
        if (!isset($checkUpInfo['reports']) || !is_array($checkUpInfo['reports'])) {
            $checkUpInfo['reports'] = [];
        }

        // Create timestamp for the report
        // Use frontend timestamp if provided (from JavaScript), else create one
        if ($request->has('timestamp') && !empty($request->timestamp)) {
            // Frontend sends display format: use as-is for display
            $timestamp = $request->timestamp;
        } else {
            // Fallback: create from server (but send ISO format for data-timestamp)
            $now = now();
            $timestamp = $now->format('d/m/Y H:i:s');
        }

        // Create new report
        $newReport = [
            'text' => $request->text,
            'timestamp' => $timestamp
        ];

        // Add the report to the array
        $checkUpInfo['reports'][] = $newReport;

        // Get the index of the newly added report
        $reportIndex = count($checkUpInfo['reports']) - 1;

        // Save the updated check_up_info
        $followup->update([
            'check_up_info' => json_encode($checkUpInfo)
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Report created successfully',
            'reportIndex' => $reportIndex,
            'report' => $newReport
        ]);
    }

    public function exportFollowUps(Request $request)
    {
        // Validate the time_period input
        $request->validate([
            'time_period' => 'nullable|in:all,today,last_week,this_month,last_month,last_3_months,last_6_months,last_12_months',
        ]);

        return Excel::download(new FollowUpExport($request), 'followups.csv', \Maatwebsite\Excel\Excel::CSV, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }
}
