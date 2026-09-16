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
use App\Models\Payment;
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
        $totalPaid = \App\Models\Payment::where('patient_id', $patient->id)->where('status', 'posted')->sum('amount');
        $totalExempted = \App\Models\Exemption::where('patient_id', $patient->id)->sum('amount');
        $totalDueAll = $totalBilled - $totalPaid - $totalExempted;

        $parameters = Parameter::orderBy('display_order')->get();

        $followUps = $patient->followUps()
            ->orderBy('created_at', 'desc')
            // ->take(2)
            ->get();

        $latestFollowUp = $followUps->first();
        $previousChikitsa = $latestFollowUp
            ? (json_decode($latestFollowUp->check_up_info, true)['chikitsa'] ?? '')
            : '';

        return view('followups.create', compact('patient', 'parameters', 'followUps', 'totalDueAll', 'previousChikitsa'));
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

        $followUp = DB::transaction(function () use ($request, $checkUpInfo) {
            $fu = FollowUp::create([
                'patient_id' => $request->patient_id,
                'doctor_id' => Auth::id(),
                'check_up_info' => json_encode($checkUpInfo),
                'diagnosis' => $request->diagnosis,
                'treatment' => $request->treatment,
                'amount_billed' => $request->amount_billed,
            ]);

            if ($request->filled('amount_paid') && $request->amount_paid > 0) {
                Payment::create([
                    'patient_id' => $fu->patient_id,
                    'follow_up_id' => $fu->id,
                    'amount' => $request->amount_paid,
                    'payment_method' => strtolower(trim($checkUpInfo['payment_method'] ?? 'cash')),
                    'paid_at' => $fu->created_at,
                    'status' => 'posted',
                    'source' => 'manual',
                    'received_by' => Auth::id(),
                    'branch_id' => $checkUpInfo['branch_id'] ?? null,
                    'branch_name' => $checkUpInfo['branch_name'] ?? null,
                ]);
            }

            return $fu;
        });

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

        // Get filter inputs with defaults (default to 'this_month' for performance)
        $selectedBranch = $request->input('branch_name', 'all');
        $selectedDoctor = $request->input('doctor', 'all');
        $timePeriod = $request->input('time_period', 'this_month');
        $fromDate = $request->input('from_date');
        $toDate = $request->input('to_date');

        // Fetch distinct doctor names cleanly from Users table (no full-table scans)
        $doctorNames = \App\Models\User::whereNotNull('name')->orderBy('name')->pluck('name');

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

        // Use pluck ids to ensure consistency
        $followUpIds = $summaryQuery->pluck('id');

        // Base query for payments
        $paymentsQuery = \App\Models\Payment::where('status', 'posted')
            ->when($selectedBranch !== 'all' && !empty($selectedBranch), function ($q) use ($selectedBranch) {
                $q->where('branch_name', $selectedBranch);
            })
            ->when($selectedDoctor !== 'all', function ($q) use ($selectedDoctor) {
                $q->where(function ($subQ) use ($selectedDoctor) {
                    $subQ->whereHas('followUp', function ($fuQ) use ($selectedDoctor) {
                        $fuQ->whereRaw("JSON_UNQUOTE(JSON_EXTRACT(check_up_info, '$.user_name')) = ?", [$selectedDoctor]);
                    })->orWhereHas('receiver', function ($sq) use ($selectedDoctor) {
                        $sq->where('name', $selectedDoctor);
                    });
                });
            })
            ->when($timePeriod !== 'all', function ($q) use ($timePeriod) {
                switch ($timePeriod) {
                    case 'today':
                        $q->whereDate('paid_at', Carbon::today());
                        break;
                    case 'last_week':
                        $q->whereBetween('paid_at', [
                            Carbon::now()->subWeek()->startOfWeek(),
                            Carbon::now()->subWeek()->endOfWeek(),
                        ]);
                        break;
                    case 'this_month':
                        $q->whereBetween('paid_at', [
                            Carbon::now()->startOfMonth(),
                            Carbon::now()->endOfMonth(),
                        ]);
                        break;
                    case 'last_month':
                        $q->whereBetween('paid_at', [
                            Carbon::now()->startOfMonth()->subMonth()->startOfMonth(),
                            Carbon::now()->startOfMonth()->subMonth()->endOfMonth(),
                        ]);
                        break;
                    case 'last_3_months':
                        $q->whereBetween('paid_at', [
                            Carbon::now()->startOfMonth()->subMonths(2)->startOfMonth(),
                            Carbon::now()->endOfMonth(),
                        ]);
                        break;
                    case 'last_6_months':
                        $q->whereBetween('paid_at', [
                            Carbon::now()->startOfMonth()->subMonths(5)->startOfMonth(),
                            Carbon::now()->endOfMonth(),
                        ]);
                        break;
                    case 'last_12_months':
                        $q->whereBetween('paid_at', [
                            Carbon::now()->startOfMonth()->subMonths(11)->startOfMonth(),
                            Carbon::now()->endOfMonth(),
                        ]);
                        break;
                }
            }, function ($q) use ($fromDate, $toDate) {
                if ($fromDate) {
                    $q->whereDate('paid_at', '>=', Carbon::parse($fromDate)->startOfDay());
                }
                if ($toDate) {
                    $q->whereDate('paid_at', '<=', Carbon::parse($toDate)->endOfDay());
                }
            });

        // Calculate summary values from payments table
        $totalIncome = (clone $paymentsQuery)->sum('amount');

        $totalPatients = FollowUp::whereIn('id', $followUpIds)
            ->distinct('patient_id')
            ->count('patient_id');

        $totalFollowUps = $summaryQuery->count();

        $totalBilled = $summaryQuery->sum('amount_billed');
        $totalPaid = $totalIncome;

        $cashPayments = (clone $paymentsQuery)->where('payment_method', 'cash')->sum('amount');
        $onlinePayments = (clone $paymentsQuery)->where('payment_method', 'online')->sum('amount');

        // Fetch detailed data for the modals from payments table (bounded to latest 100 for memory performance)
        $cashFollowUps = (clone $paymentsQuery)
            ->where('payment_method', 'cash')
            ->with(['patient' => function($q) { $q->select('id', 'name'); }])
            ->latest()
            ->take(100)
            ->get(['id', 'patient_id', 'amount', 'created_at']);

        $onlineFollowUps = (clone $paymentsQuery)
            ->where('payment_method', 'online')
            ->with(['patient' => function($q) { $q->select('id', 'name'); }])
            ->latest()
            ->take(100)
            ->get(['id', 'patient_id', 'amount', 'created_at']);

        $allFollowUpsList = (clone $summaryQuery)
            ->with(['patient' => function($q) { $q->select('id', 'name'); }, 'payments'])
            ->latest()
            ->take(100)
            ->get(['id', 'patient_id', 'amount_billed', 'created_at']);

        // Base query for exemptions
        $exemptionsQuery = \App\Models\Exemption::query()
            ->when($timePeriod !== 'all', function ($q) use ($timePeriod) {
                switch ($timePeriod) {
                    case 'today':
                        $q->whereDate('exempted_at', Carbon::today());
                        break;
                    case 'last_week':
                        $q->whereBetween('exempted_at', [
                            Carbon::now()->subWeek()->startOfWeek(),
                            Carbon::now()->subWeek()->endOfWeek(),
                        ]);
                        break;
                    case 'this_month':
                        $q->whereBetween('exempted_at', [
                            Carbon::now()->startOfMonth(),
                            Carbon::now()->endOfMonth(),
                        ]);
                        break;
                    case 'last_month':
                        $q->whereBetween('exempted_at', [
                            Carbon::now()->startOfMonth()->subMonth()->startOfMonth(),
                            Carbon::now()->startOfMonth()->subMonth()->endOfMonth(),
                        ]);
                        break;
                    case 'last_3_months':
                        $q->whereBetween('exempted_at', [
                            Carbon::now()->startOfMonth()->subMonths(2)->startOfMonth(),
                            Carbon::now()->endOfMonth(),
                        ]);
                        break;
                    case 'last_6_months':
                        $q->whereBetween('exempted_at', [
                            Carbon::now()->startOfMonth()->subMonths(5)->startOfMonth(),
                            Carbon::now()->endOfMonth(),
                        ]);
                        break;
                    case 'last_12_months':
                        $q->whereBetween('exempted_at', [
                            Carbon::now()->startOfMonth()->subMonths(11)->startOfMonth(),
                            Carbon::now()->endOfMonth(),
                        ]);
                        break;
                }
            }, function ($q) use ($fromDate, $toDate) {
                if ($fromDate) {
                    $q->whereDate('exempted_at', '>=', Carbon::parse($fromDate)->startOfDay());
                }
                if ($toDate) {
                    $q->whereDate('exempted_at', '<=', Carbon::parse($toDate)->endOfDay());
                }
            });

        $totalExemptedAmount = (clone $exemptionsQuery)->sum('amount');
        $exemptedPatientsCount = (clone $exemptionsQuery)->distinct('patient_id')->count('patient_id');
        $exemptionsList = (clone $exemptionsQuery)
            ->with(['patient:id,name,mobile_phone', 'user:id,name'])
            ->latest('exempted_at')
            ->take(100)
            ->get();

        $patientIds = $allFollowUpsList->pluck('patient_id')->unique();
        $patientsList = \App\Models\Patient::withSum('followUps', 'amount_billed')
            ->whereIn('id', $patientIds)
            ->take(100)
            ->get(['id', 'name', 'mobile_phone', 'created_at']);

        // Fetch total paid for each of these patients from the payments table
        $patientPayments = \App\Models\Payment::whereIn('patient_id', $patientIds)
            ->where('status', 'posted')
            ->groupBy('patient_id')
            ->selectRaw('patient_id, SUM(amount) as total_paid')
            ->pluck('total_paid', 'patient_id');

        $patientExemptions = \App\Models\Exemption::whereIn('patient_id', $patientIds)
            ->groupBy('patient_id')
            ->selectRaw('patient_id, SUM(amount) as total_exempted')
            ->pluck('total_exempted', 'patient_id');

        $patientBalances = $patientsList->mapWithKeys(function($p) use ($patientPayments, $patientExemptions) {
            $totalBilled = $p->follow_ups_sum_amount_billed ?? 0;
            $totalPaid = $patientPayments[$p->id] ?? 0;
            $totalExempted = $patientExemptions[$p->id] ?? 0;
            $bal = $totalBilled - $totalPaid - $totalExempted;
            return [$p->id => $bal];
        });

        $paidFollowUpsList = (clone $paymentsQuery)
            ->with(['patient' => function($q) { $q->select('id', 'name'); }])
            ->latest()
            ->take(100)
            ->get(['id', 'patient_id', 'amount', 'created_at']);

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

        // Filter out follow-ups that have 0 real due
        $dueFollowUpsList = $dueFollowUpsList->filter(function($fu) {
            return $fu->real_due > 0;
        });

        // Paginate combined ledger (follow-ups + standalone payments)
        $combinedLedger = $this->getCombinedLedgerEntries($request);
        $perPage = 15;
        $page = (int) $request->input('page', 1);
        $currentItems = $combinedLedger->slice(($page - 1) * $perPage, $perPage)->values();

        $followUps = new \Illuminate\Pagination\LengthAwarePaginator(
            $currentItems,
            $combinedLedger->count(),
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

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
        $dailyBilled = FollowUp::selectRaw('DATE(created_at) as raw_date, DATE_FORMAT(created_at, "%d-%m-%y") as date, SUM(amount_billed) as billed')
            ->whereHas('patient')
            ->when(true, $commonFilters)
            ->groupBy('raw_date', 'date')
            ->orderBy('raw_date', 'asc')
            ->get()
            ->keyBy('raw_date');

        $dailyPaid = (clone $paymentsQuery)
            ->selectRaw('DATE(paid_at) as raw_date, DATE_FORMAT(paid_at, "%d-%m-%y") as date, SUM(amount) as paid')
            ->groupBy('raw_date', 'date')
            ->orderBy('raw_date', 'asc')
            ->get()
            ->keyBy('raw_date');

        $allDates = $dailyBilled->keys()->merge($dailyPaid->keys())->unique()->sort();
        $paymentStatus = $allDates->map(function ($date) use ($dailyBilled, $dailyPaid) {
            $billedObj = $dailyBilled->get($date);
            $paidObj = $dailyPaid->get($date);
            $billed = $billedObj ? $billedObj->billed : 0;
            $paid = $paidObj ? $paidObj->paid : 0;
            return (object)[
                'raw_date' => $date,
                'date' => $billedObj ? $billedObj->date : ($paidObj ? $paidObj->date : Carbon::parse($date)->format('d-m-y')),
                'billed' => $billed,
                'paid' => $paid,
                'due' => $billed - $paid,
            ];
        });

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

        $totalIncomeCount = (clone $paymentsQuery)->count();
        $totalCashCount = (clone $paymentsQuery)->where('payment_method', 'cash')->count();
        $totalOnlineCount = (clone $paymentsQuery)->where('payment_method', 'online')->count();
        $totalExemptionsCount = (clone $exemptionsQuery)->count();
        $totalDueCount = $dueFollowUpsList->count();

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
            'selectedDoctor',
            'timePeriod',
            'fromDate',
            'toDate',
            'totalDueAll',
            'totalExemptedAmount',
            'exemptedPatientsCount',
            'exemptionsList',
            'followUpFrequencyDaily',
            'followUpFrequencyMonthly',
            'followUpFrequencyYearly',
            'ageDistribution',
            'paymentStatus',
            'newVsExistingPatients',
            'cashPayments',
            'onlinePayments',
            'doctorNames',
            'totalIncomeCount',
            'totalCashCount',
            'totalOnlineCount',
            'totalExemptionsCount',
            'totalDueCount'
        ));
    }

    /**
     * Get combined chronological ledger items (Follow-ups and Standalone Payments)
     */
    private function getCombinedLedgerEntries(Request $request)
    {
        $selectedBranch = $request->input('branch_name', 'all');
        $selectedDoctor = $request->input('doctor', 'all');
        $timePeriod = $request->input('time_period', 'this_month');
        $fromDate = $request->input('from_date');
        $toDate = $request->input('to_date');

        // 1. Follow-ups
        $fuQuery = FollowUp::with(['patient', 'payments'])->whereHas('patient');

        if ($selectedBranch !== 'all' && !empty($selectedBranch)) {
            $fuQuery->whereRaw("JSON_UNQUOTE(JSON_EXTRACT(check_up_info, '$.branch_name')) = ?", [$selectedBranch]);
        }
        if ($selectedDoctor !== 'all') {
            $fuQuery->whereRaw("JSON_UNQUOTE(JSON_EXTRACT(check_up_info, '$.user_name')) = ?", [$selectedDoctor]);
        }

        if ($timePeriod !== 'all') {
            switch ($timePeriod) {
                case 'today':
                    $fuQuery->whereDate('created_at', Carbon::today());
                    break;
                case 'last_week':
                    $fuQuery->whereBetween('created_at', [
                        Carbon::now()->subWeek()->startOfWeek(),
                        Carbon::now()->subWeek()->endOfWeek(),
                    ]);
                    break;
                case 'this_month':
                    $fuQuery->whereBetween('created_at', [
                        Carbon::now()->startOfMonth(),
                        Carbon::now()->endOfMonth(),
                    ]);
                    break;
                case 'last_month':
                    $fuQuery->whereBetween('created_at', [
                        Carbon::now()->startOfMonth()->subMonth()->startOfMonth(),
                        Carbon::now()->startOfMonth()->subMonth()->endOfMonth(),
                    ]);
                    break;
                case 'last_3_months':
                    $fuQuery->whereBetween('created_at', [
                        Carbon::now()->startOfMonth()->subMonths(2)->startOfMonth(),
                        Carbon::now()->endOfMonth(),
                    ]);
                    break;
                case 'last_6_months':
                    $fuQuery->whereBetween('created_at', [
                        Carbon::now()->startOfMonth()->subMonths(5)->startOfMonth(),
                        Carbon::now()->endOfMonth(),
                    ]);
                    break;
                case 'last_12_months':
                    $fuQuery->whereBetween('created_at', [
                        Carbon::now()->startOfMonth()->subMonths(11)->startOfMonth(),
                        Carbon::now()->endOfMonth(),
                    ]);
                    break;
            }
        } else {
            if ($fromDate) {
                $fuQuery->whereDate('created_at', '>=', Carbon::parse($fromDate)->startOfDay());
            }
            if ($toDate) {
                $fuQuery->whereDate('created_at', '<=', Carbon::parse($toDate)->endOfDay());
            }
        }

        $fuItems = $fuQuery->get()->map(function ($fu) {
            $checkUpInfo = json_decode($fu->check_up_info, true) ?? [];
            return (object) [
                'type' => 'followup',
                'id' => $fu->id,
                'date' => $fu->created_at,
                'patient' => $fu->patient,
                'patient_id' => $fu->patient_id,
                'doctor_name' => $checkUpInfo['user_name'] ?? optional($fu->doctor)->name ?? 'N/A',
                'amount_billed' => (float) ($fu->amount_billed ?? 0),
                'payment_method' => $fu->payment_method,
                'amount_paid' => (float) $fu->amount_paid,
                'branch_name' => $checkUpInfo['branch_name'] ?? null,
                'model' => $fu,
            ];
        });

        // 2. Standalone Payments
        $payQuery = \App\Models\Payment::with(['patient', 'receiver'])
            ->whereNull('follow_up_id')
            ->where('status', 'posted')
            ->whereHas('patient');

        if ($selectedBranch !== 'all' && !empty($selectedBranch)) {
            $payQuery->where('branch_name', $selectedBranch);
        }
        if ($selectedDoctor !== 'all') {
            $payQuery->where(function ($subQ) use ($selectedDoctor) {
                $subQ->whereHas('receiver', function ($sq) use ($selectedDoctor) {
                    $sq->where('name', $selectedDoctor);
                });
            });
        }

        if ($timePeriod !== 'all') {
            switch ($timePeriod) {
                case 'today':
                    $payQuery->whereDate('paid_at', Carbon::today());
                    break;
                case 'last_week':
                    $payQuery->whereBetween('paid_at', [
                        Carbon::now()->subWeek()->startOfWeek(),
                        Carbon::now()->subWeek()->endOfWeek(),
                    ]);
                    break;
                case 'this_month':
                    $payQuery->whereBetween('paid_at', [
                        Carbon::now()->startOfMonth(),
                        Carbon::now()->endOfMonth(),
                    ]);
                    break;
                case 'last_month':
                    $payQuery->whereBetween('paid_at', [
                        Carbon::now()->startOfMonth()->subMonth()->startOfMonth(),
                        Carbon::now()->startOfMonth()->subMonth()->endOfMonth(),
                    ]);
                    break;
                case 'last_3_months':
                    $payQuery->whereBetween('paid_at', [
                        Carbon::now()->startOfMonth()->subMonths(2)->startOfMonth(),
                        Carbon::now()->endOfMonth(),
                    ]);
                    break;
                case 'last_6_months':
                    $payQuery->whereBetween('paid_at', [
                        Carbon::now()->startOfMonth()->subMonths(5)->startOfMonth(),
                        Carbon::now()->endOfMonth(),
                    ]);
                    break;
                case 'last_12_months':
                    $payQuery->whereBetween('paid_at', [
                        Carbon::now()->startOfMonth()->subMonths(11)->startOfMonth(),
                        Carbon::now()->endOfMonth(),
                    ]);
                    break;
            }
        } else {
            if ($fromDate) {
                $payQuery->whereDate('paid_at', '>=', Carbon::parse($fromDate)->startOfDay());
            }
            if ($toDate) {
                $payQuery->whereDate('paid_at', '<=', Carbon::parse($toDate)->endOfDay());
            }
        }

        $payItems = $payQuery->get()->map(function ($p) {
            return (object) [
                'type' => 'payment',
                'id' => $p->id,
                'date' => $p->paid_at ?? $p->created_at,
                'patient' => $p->patient,
                'patient_id' => $p->patient_id,
                'doctor_name' => optional($p->receiver)->name ?? 'Standalone Payment',
                'amount_billed' => null,
                'payment_method' => ucfirst($p->payment_method),
                'amount_paid' => (float) $p->amount,
                'branch_name' => $p->branch_name,
                'model' => $p,
            ];
        });

        return $fuItems->concat($payItems)->sortByDesc('date')->values();
    }

    /**
     * Fetch follow-ups for infinite scroll (AJAX endpoint)
     */
    public function fetchFollowUps(Request $request)
    {
        $page = (int) $request->input('page', 1);
        $perPage = 15; // Items per scroll load

        $combinedLedger = $this->getCombinedLedgerEntries($request);
        $currentItems = $combinedLedger->slice(($page - 1) * $perPage, $perPage)->values();

        $followUps = new \Illuminate\Pagination\LengthAwarePaginator(
            $currentItems,
            $combinedLedger->count(),
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        $html = '';
        $renderedCount = 0;

        foreach ($followUps->items() as $item) {
            if ($item->patient) {
                $renderedCount++;
                $dateFormatted = optional($item->date)->format('d M Y, h:i A');
                $patientUrl = route('patients.show', $item->patient->id);

                if ($item->type === 'payment') {
                    $colorClass = 'text-emerald-600 dark:text-emerald-400';
                    $amountColorClass = 'text-emerald-600 dark:text-emerald-400';
                    $doctorHtml = '<span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800 dark:bg-emerald-900/50 dark:text-emerald-300">💳 Direct Payment (' . htmlspecialchars($item->doctor_name) . ')</span>';
                    $billedHtml = '<span class="text-gray-400 dark:text-gray-500 font-normal">—</span>';
                } else {
                    $colorClass = $item->amount_paid < $item->amount_billed
                        ? 'text-red-600 dark:text-red-400'
                        : 'text-indigo-700 dark:text-indigo-400';

                    $amountColorClass = $item->amount_paid < $item->amount_billed
                        ? 'text-red-600 dark:text-red-400'
                        : ($item->amount_paid > $item->amount_billed
                            ? 'text-green-600 dark:text-green-300'
                            : 'text-blue-600 dark:text-blue-300');

                    $doctorHtml = htmlspecialchars($item->doctor_name);
                    $billedHtml = '₹' . $this->indFormat($item->amount_billed);
                }

                $html .= '<tr class="border-t border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-800 animate-fadeIn">';
                $html .= '<td class="text-left px-4 py-3">' . $dateFormatted . '</td>';
                $html .= '<td class="text-center px-4 py-3"><a href="' . $patientUrl . '" class="font-semibold hover:underline ' . $colorClass . '">' . htmlspecialchars($item->patient->name) . '</a></td>';
                $html .= '<td class="text-center px-4 py-3">' . $doctorHtml . '</td>';
                $html .= '<td class="text-center px-4 py-3 font-semibold text-blue-600 dark:text-blue-300">' . $billedHtml . '</td>';
                $html .= '<td class="text-center px-4 py-3 font-semibold text-blue-600 dark:text-blue-300">' . htmlspecialchars($item->payment_method) . '</td>';
                $html .= '<td class="text-right px-4 py-3 font-semibold ' . $amountColorClass . '">₹' . $this->indFormat($item->amount_paid) . '</td>';
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

    /**
     * Fetch modal log items for on-demand pagination / Load More (AJAX endpoint)
     */
    public function fetchModalLog(Request $request)
    {
        $logType = $request->input('log_type', 'patients');
        $page = (int) $request->input('page', 1);
        $perPage = (int) $request->input('per_page', 50);
        $offset = ($page - 1) * $perPage;

        $selectedBranch = $request->input('branch_name', 'all');
        $selectedDoctor = $request->input('doctor', 'all');
        $timePeriod = $request->input('time_period', 'this_month');
        $fromDate = $request->input('from_date');
        $toDate = $request->input('to_date');

        // Helper closures for query filtering
        $applyFuFilters = function ($q) use ($selectedBranch, $selectedDoctor, $timePeriod, $fromDate, $toDate) {
            $q->whereHas('patient');
            if ($selectedBranch !== 'all' && !empty($selectedBranch)) {
                $q->whereRaw("JSON_UNQUOTE(JSON_EXTRACT(check_up_info, '$.branch_name')) = ?", [$selectedBranch]);
            }
            if ($selectedDoctor !== 'all') {
                $q->whereRaw("JSON_UNQUOTE(JSON_EXTRACT(check_up_info, '$.user_name')) = ?", [$selectedDoctor]);
            }
            if ($timePeriod !== 'all') {
                switch ($timePeriod) {
                    case 'today':
                        $q->whereDate('created_at', Carbon::today());
                        break;
                    case 'last_week':
                        $q->whereBetween('created_at', [
                            Carbon::now()->subWeek()->startOfWeek(),
                            Carbon::now()->subWeek()->endOfWeek(),
                        ]);
                        break;
                    case 'this_month':
                        $q->whereBetween('created_at', [
                            Carbon::now()->startOfMonth(),
                            Carbon::now()->endOfMonth(),
                        ]);
                        break;
                    case 'last_month':
                        $q->whereBetween('created_at', [
                            Carbon::now()->startOfMonth()->subMonth()->startOfMonth(),
                            Carbon::now()->startOfMonth()->subMonth()->endOfMonth(),
                        ]);
                        break;
                    case 'last_3_months':
                        $q->whereBetween('created_at', [
                            Carbon::now()->startOfMonth()->subMonths(2)->startOfMonth(),
                            Carbon::now()->endOfMonth(),
                        ]);
                        break;
                    case 'last_6_months':
                        $q->whereBetween('created_at', [
                            Carbon::now()->startOfMonth()->subMonths(5)->startOfMonth(),
                            Carbon::now()->endOfMonth(),
                        ]);
                        break;
                    case 'last_12_months':
                        $q->whereBetween('created_at', [
                            Carbon::now()->startOfMonth()->subMonths(11)->startOfMonth(),
                            Carbon::now()->endOfMonth(),
                        ]);
                        break;
                }
            } else {
                if ($fromDate) {
                    $q->whereDate('created_at', '>=', Carbon::parse($fromDate)->startOfDay());
                }
                if ($toDate) {
                    $q->whereDate('created_at', '<=', Carbon::parse($toDate)->endOfDay());
                }
            }
        };

        $applyPaymentFilters = function ($q) use ($selectedBranch, $selectedDoctor, $timePeriod, $fromDate, $toDate) {
            $q->where('status', 'posted')
                ->when($selectedBranch !== 'all' && !empty($selectedBranch), function ($subQ) use ($selectedBranch) {
                    $subQ->where('branch_name', $selectedBranch);
                })
                ->when($selectedDoctor !== 'all', function ($subQ) use ($selectedDoctor) {
                    $subQ->where(function ($nestedQ) use ($selectedDoctor) {
                        $nestedQ->whereHas('followUp', function ($fuQ) use ($selectedDoctor) {
                            $fuQ->whereRaw("JSON_UNQUOTE(JSON_EXTRACT(check_up_info, '$.user_name')) = ?", [$selectedDoctor]);
                        })->orWhereHas('receiver', function ($sq) use ($selectedDoctor) {
                            $sq->where('name', $selectedDoctor);
                        });
                    });
                })
                ->when($timePeriod !== 'all', function ($subQ) use ($timePeriod) {
                    switch ($timePeriod) {
                        case 'today':
                            $subQ->whereDate('paid_at', Carbon::today());
                            break;
                        case 'last_week':
                            $subQ->whereBetween('paid_at', [
                                Carbon::now()->subWeek()->startOfWeek(),
                                Carbon::now()->subWeek()->endOfWeek(),
                            ]);
                            break;
                        case 'this_month':
                            $subQ->whereBetween('paid_at', [
                                Carbon::now()->startOfMonth(),
                                Carbon::now()->endOfMonth(),
                            ]);
                            break;
                        case 'last_month':
                            $subQ->whereBetween('paid_at', [
                                Carbon::now()->startOfMonth()->subMonth()->startOfMonth(),
                                Carbon::now()->startOfMonth()->subMonth()->endOfMonth(),
                            ]);
                            break;
                        case 'last_3_months':
                            $subQ->whereBetween('paid_at', [
                                Carbon::now()->startOfMonth()->subMonths(2)->startOfMonth(),
                                Carbon::now()->endOfMonth(),
                            ]);
                            break;
                        case 'last_6_months':
                            $subQ->whereBetween('paid_at', [
                                Carbon::now()->startOfMonth()->subMonths(5)->startOfMonth(),
                                Carbon::now()->endOfMonth(),
                            ]);
                            break;
                        case 'last_12_months':
                            $subQ->whereBetween('paid_at', [
                                Carbon::now()->startOfMonth()->subMonths(11)->startOfMonth(),
                                Carbon::now()->endOfMonth(),
                            ]);
                            break;
                    }
                }, function ($subQ) use ($fromDate, $toDate) {
                    if ($fromDate) {
                        $subQ->whereDate('paid_at', '>=', Carbon::parse($fromDate)->startOfDay());
                    }
                    if ($toDate) {
                        $subQ->whereDate('paid_at', '<=', Carbon::parse($toDate)->endOfDay());
                    }
                });
        };

        $applyExemptionFilters = function ($q) use ($timePeriod, $fromDate, $toDate) {
            $q->when($timePeriod !== 'all', function ($subQ) use ($timePeriod) {
                switch ($timePeriod) {
                    case 'today':
                        $subQ->whereDate('exempted_at', Carbon::today());
                        break;
                    case 'last_week':
                        $subQ->whereBetween('exempted_at', [
                            Carbon::now()->subWeek()->startOfWeek(),
                            Carbon::now()->subWeek()->endOfWeek(),
                        ]);
                        break;
                    case 'this_month':
                        $subQ->whereBetween('exempted_at', [
                            Carbon::now()->startOfMonth(),
                            Carbon::now()->endOfMonth(),
                        ]);
                        break;
                    case 'last_month':
                        $subQ->whereBetween('exempted_at', [
                            Carbon::now()->startOfMonth()->subMonth()->startOfMonth(),
                            Carbon::now()->startOfMonth()->subMonth()->endOfMonth(),
                        ]);
                        break;
                    case 'last_3_months':
                        $subQ->whereBetween('exempted_at', [
                            Carbon::now()->startOfMonth()->subMonths(2)->startOfMonth(),
                            Carbon::now()->endOfMonth(),
                        ]);
                        break;
                    case 'last_6_months':
                        $subQ->whereBetween('exempted_at', [
                            Carbon::now()->startOfMonth()->subMonths(5)->startOfMonth(),
                            Carbon::now()->endOfMonth(),
                        ]);
                        break;
                    case 'last_12_months':
                        $subQ->whereBetween('exempted_at', [
                            Carbon::now()->startOfMonth()->subMonths(11)->startOfMonth(),
                            Carbon::now()->endOfMonth(),
                        ]);
                        break;
                }
            }, function ($subQ) use ($fromDate, $toDate) {
                if ($fromDate) {
                    $subQ->whereDate('exempted_at', '>=', Carbon::parse($fromDate)->startOfDay());
                }
                if ($toDate) {
                    $subQ->whereDate('exempted_at', '<=', Carbon::parse($toDate)->endOfDay());
                }
            });
        };

        $html = '';
        $total = 0;
        $count = 0;

        switch ($logType) {
            case 'patients':
                $fuQuery = FollowUp::query();
                $applyFuFilters($fuQuery);
                $allPatientIds = $fuQuery->pluck('patient_id')->unique()->values();
                $total = $allPatientIds->count();
                $pagedIds = $allPatientIds->slice($offset, $perPage)->values();

                $patients = \App\Models\Patient::whereIn('id', $pagedIds)
                    ->get(['id', 'name', 'mobile_phone'])
                    ->keyBy('id');

                foreach ($pagedIds as $idx => $pid) {
                    $patient = $patients->get($pid);
                    if ($patient) {
                        $count++;
                        $rowNum = $offset + $count;
                        $phone = htmlspecialchars($patient->mobile_phone ?? 'N/A');
                        $name = htmlspecialchars($patient->name);
                        $patientUrl = route('patients.show', $patient->id);
                        $html .= '<tr class="hover:bg-gray-50 dark:hover:bg-gray-800 transition text-gray-800 dark:text-gray-200">';
                        $html .= '<td class="px-5 py-3 text-gray-500 text-center">' . $rowNum . '</td>';
                        $html .= '<td class="px-5 py-3 font-medium"><a target="_blank" href="' . $patientUrl . '" class="text-blue-500 hover:text-blue-700 hover:underline dark:text-blue-400 dark:hover:text-blue-300">' . $name . '</a></td>';
                        $html .= '<td class="px-5 py-3 text-right">' . $phone . '</td>';
                        $html .= '</tr>';
                    }
                }
                break;

            case 'followups':
                $fuQuery = FollowUp::query();
                $applyFuFilters($fuQuery);
                $total = $fuQuery->count();
                $items = $fuQuery->with(['patient' => function ($q) { $q->select('id', 'name'); }])
                    ->latest()
                    ->skip($offset)
                    ->take($perPage)
                    ->get(['id', 'patient_id', 'created_at']);

                foreach ($items as $item) {
                    $count++;
                    $rowNum = $offset + $count;
                    $date = $item->created_at->format('d M Y');
                    $patientName = htmlspecialchars(optional($item->patient)->name ?? 'Unknown');
                    $patientUrl = route('patients.show', $item->patient_id);
                    $html .= '<tr class="hover:bg-gray-50 dark:hover:bg-gray-800 transition text-gray-800 dark:text-gray-200">';
                    $html .= '<td class="px-5 py-3 text-gray-500 text-center">' . $rowNum . '</td>';
                    $html .= '<td class="px-5 py-3">' . $date . '</td>';
                    $html .= '<td class="px-5 py-3 font-medium"><a target="_blank" href="' . $patientUrl . '" class="text-blue-500 hover:text-blue-700 hover:underline dark:text-blue-400 dark:hover:text-blue-300">' . $patientName . '</a></td>';
                    $html .= '</tr>';
                }
                break;

            case 'income':
                $payQuery = \App\Models\Payment::query();
                $applyPaymentFilters($payQuery);
                $total = $payQuery->count();
                $items = $payQuery->with(['patient' => function ($q) { $q->select('id', 'name'); }])
                    ->latest('paid_at')
                    ->skip($offset)
                    ->take($perPage)
                    ->get(['id', 'patient_id', 'amount', 'paid_at', 'created_at']);

                foreach ($items as $item) {
                    $count++;
                    $rowNum = $offset + $count;
                    $date = optional($item->paid_at ?? $item->created_at)->format('d M Y');
                    $patientName = htmlspecialchars(optional($item->patient)->name ?? 'Unknown');
                    $patientUrl = route('patients.show', $item->patient_id);
                    $amount = $this->indFormat($item->amount);
                    $html .= '<tr class="hover:bg-gray-50 dark:hover:bg-gray-800 transition text-gray-800 dark:text-gray-200">';
                    $html .= '<td class="px-5 py-3 text-gray-500 text-center">' . $rowNum . '</td>';
                    $html .= '<td class="px-5 py-3">' . $date . '</td>';
                    $html .= '<td class="px-5 py-3 font-medium"><a target="_blank" href="' . $patientUrl . '" class="text-blue-500 hover:text-blue-700 hover:underline dark:text-blue-400 dark:hover:text-blue-300">' . $patientName . '</a></td>';
                    $html .= '<td class="px-5 py-3 text-right font-bold text-green-600 dark:text-green-400">₹' . $amount . '</td>';
                    $html .= '</tr>';
                }
                break;

            case 'cash':
                $payQuery = \App\Models\Payment::query();
                $applyPaymentFilters($payQuery);
                $payQuery->where('payment_method', 'cash');
                $total = $payQuery->count();
                $items = $payQuery->with(['patient' => function ($q) { $q->select('id', 'name'); }])
                    ->latest('paid_at')
                    ->skip($offset)
                    ->take($perPage)
                    ->get(['id', 'patient_id', 'amount', 'paid_at', 'created_at']);

                foreach ($items as $item) {
                    $count++;
                    $rowNum = $offset + $count;
                    $date = optional($item->paid_at ?? $item->created_at)->format('d M Y');
                    $patientName = htmlspecialchars(optional($item->patient)->name ?? 'Unknown');
                    $patientUrl = route('patients.show', $item->patient_id);
                    $amount = $this->indFormat($item->amount);
                    $html .= '<tr class="hover:bg-gray-50 dark:hover:bg-gray-800 transition text-gray-800 dark:text-gray-200">';
                    $html .= '<td class="px-5 py-3 text-gray-500 text-center">' . $rowNum . '</td>';
                    $html .= '<td class="px-5 py-3">' . $date . '</td>';
                    $html .= '<td class="px-5 py-3 font-medium"><a target="_blank" href="' . $patientUrl . '" class="text-blue-500 hover:text-blue-700 hover:underline dark:text-blue-400 dark:hover:text-blue-300">' . $patientName . '</a></td>';
                    $html .= '<td class="px-5 py-3 text-right font-bold text-teal-600 dark:text-teal-400">₹' . $amount . '</td>';
                    $html .= '</tr>';
                }
                break;

            case 'online':
                $payQuery = \App\Models\Payment::query();
                $applyPaymentFilters($payQuery);
                $payQuery->where('payment_method', 'online');
                $total = $payQuery->count();
                $items = $payQuery->with(['patient' => function ($q) { $q->select('id', 'name'); }])
                    ->latest('paid_at')
                    ->skip($offset)
                    ->take($perPage)
                    ->get(['id', 'patient_id', 'amount', 'paid_at', 'created_at']);

                foreach ($items as $item) {
                    $count++;
                    $rowNum = $offset + $count;
                    $date = optional($item->paid_at ?? $item->created_at)->format('d M Y');
                    $patientName = htmlspecialchars(optional($item->patient)->name ?? 'Unknown');
                    $patientUrl = route('patients.show', $item->patient_id);
                    $amount = $this->indFormat($item->amount);
                    $html .= '<tr class="hover:bg-gray-50 dark:hover:bg-gray-800 transition text-gray-800 dark:text-gray-200">';
                    $html .= '<td class="px-5 py-3 text-gray-500 text-center">' . $rowNum . '</td>';
                    $html .= '<td class="px-5 py-3">' . $date . '</td>';
                    $html .= '<td class="px-5 py-3 font-medium"><a target="_blank" href="' . $patientUrl . '" class="text-blue-500 hover:text-blue-700 hover:underline dark:text-blue-400 dark:hover:text-blue-300">' . $patientName . '</a></td>';
                    $html .= '<td class="px-5 py-3 text-right font-bold text-pink-600 dark:text-pink-400">₹' . $amount . '</td>';
                    $html .= '</tr>';
                }
                break;

            case 'exemptions':
                $exQuery = \App\Models\Exemption::query();
                $applyExemptionFilters($exQuery);
                $total = $exQuery->count();
                $items = $exQuery->with(['patient:id,name,mobile_phone', 'user:id,name'])
                    ->latest('exempted_at')
                    ->skip($offset)
                    ->take($perPage)
                    ->get();

                foreach ($items as $item) {
                    $count++;
                    $rowNum = $offset + $count;
                    $date = optional($item->exempted_at ?? $item->created_at)->format('d M Y');
                    $patientName = htmlspecialchars(optional($item->patient)->name ?? 'Unknown');
                    $patientUrl = route('patients.show', $item->patient_id);
                    $reason = htmlspecialchars($item->reason ?? '-');
                    $userName = htmlspecialchars(optional($item->user)->name ?? 'Admin');
                    $amount = $this->indFormat($item->amount);
                    $html .= '<tr class="hover:bg-gray-50 dark:hover:bg-gray-800 transition text-gray-800 dark:text-gray-200">';
                    $html .= '<td class="px-5 py-3 text-gray-500 text-center">' . $rowNum . '</td>';
                    $html .= '<td class="px-5 py-3">' . $date . '</td>';
                    $html .= '<td class="px-5 py-3 font-medium"><a target="_blank" href="' . $patientUrl . '" class="text-blue-500 hover:text-blue-700 hover:underline dark:text-blue-400 dark:hover:text-blue-300">' . $patientName . '</a></td>';
                    $html .= '<td class="px-5 py-3 text-gray-600 dark:text-gray-400">' . $reason . '</td>';
                    $html .= '<td class="px-5 py-3 text-xs text-gray-500">' . $userName . '</td>';
                    $html .= '<td class="px-5 py-3 text-right font-bold text-amber-600 dark:text-amber-400">₹' . $amount . '</td>';
                    $html .= '</tr>';
                }
                break;

            case 'due':
                $fuQuery = FollowUp::query();
                $applyFuFilters($fuQuery);
                $allDues = $fuQuery->with(['patient', 'payments'])
                    ->latest()
                    ->get()
                    ->filter(function ($fu) {
                        return ($fu->amount_billed - $fu->amount_paid) > 0;
                    });

                $total = $allDues->count();
                $pagedDues = $allDues->slice($offset, $perPage)->values();

                $duePatientIds = $pagedDues->pluck('patient_id')->unique();
                $patientPayments = \App\Models\Payment::whereIn('patient_id', $duePatientIds)
                    ->where('status', 'posted')
                    ->groupBy('patient_id')
                    ->selectRaw('patient_id, SUM(amount) as total_paid')
                    ->pluck('total_paid', 'patient_id');

                $patientExemptions = \App\Models\Exemption::whereIn('patient_id', $duePatientIds)
                    ->groupBy('patient_id')
                    ->selectRaw('patient_id, SUM(amount) as total_exempted')
                    ->pluck('total_exempted', 'patient_id');

                $patientPatients = \App\Models\Patient::withSum('followUps', 'amount_billed')
                    ->whereIn('id', $duePatientIds)
                    ->get(['id', 'name'])
                    ->keyBy('id');

                $patientBalances = [];
                foreach ($duePatientIds as $pid) {
                    $p = $patientPatients->get($pid);
                    $totalBilled = $p->follow_ups_sum_amount_billed ?? 0;
                    $totalPaid = $patientPayments[$pid] ?? 0;
                    $totalExempted = $patientExemptions[$pid] ?? 0;
                    $patientBalances[$pid] = $totalBilled - $totalPaid - $totalExempted;
                }

                foreach ($pagedDues as $fu) {
                    $count++;
                    $rowNum = $offset + $count;
                    $date = $fu->created_at->format('d M Y');
                    $patientName = htmlspecialchars(optional($fu->patient)->name ?? 'Unknown');
                    $patientUrl = route('patients.show', $fu->patient_id);
                    $visitDue = $fu->amount_billed - $fu->amount_paid;
                    $netBalance = $patientBalances[$fu->patient_id] ?? 0;
                    $realDue = max(0, min($visitDue, $netBalance));

                    $html .= '<tr class="hover:bg-gray-50 dark:hover:bg-gray-800 transition text-gray-800 dark:text-gray-200">';
                    $html .= '<td class="px-5 py-3 text-gray-500 text-center">' . $rowNum . '</td>';
                    $html .= '<td class="px-5 py-3">' . $date . '</td>';
                    $html .= '<td class="px-5 py-3 font-medium"><a target="_blank" href="' . $patientUrl . '" class="text-blue-500 hover:text-blue-700 hover:underline dark:text-blue-400 dark:hover:text-blue-300">' . $patientName . '</a></td>';
                    $html .= '<td class="px-5 py-3 text-right font-medium text-gray-600 dark:text-gray-400">₹' . $this->indFormat($visitDue) . '</td>';
                    $html .= '<td class="px-5 py-3 text-right font-medium text-blue-600 dark:text-blue-400">₹' . $this->indFormat($netBalance) . '</td>';
                    $html .= '<td class="px-5 py-3 text-right font-bold text-red-600 dark:text-red-500 text-base">₹' . $this->indFormat($realDue) . '</td>';
                    $html .= '</tr>';
                }
                break;
        }

        $shownCount = min($offset + $count, $total);
        $hasMore = $shownCount < $total;

        return response()->json([
            'success' => true,
            'html' => $html,
            'hasMore' => $hasMore,
            'page' => $page,
            'shownCount' => $shownCount,
            'total' => $total,
            'count' => $count,
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
        $totalPaid = \App\Models\Payment::where('patient_id', $patient->id)->where('status', 'posted')->sum('amount');
        $totalDueAll = $totalBilled - $totalPaid;

        // Fetch parameters
        $parameters = Parameter::all();

        // Fetch values
        $totalDue = $totalDueAll; // as per create.blade.php
        $amountBilled = $followup->amount_billed ?? '';
        $amountPaid = \App\Models\Payment::where('follow_up_id', $followup->id)->where('status', 'posted')->sum('amount');

        $latestFollowUp = $followUps->first();
        $previousChikitsa = $latestFollowUp
            ? (json_decode($latestFollowUp->check_up_info, true)['chikitsa'] ?? '')
            : '';

        return view('followups.edit', compact(
            'patient',
            'followup',
            'followUps',
            'parameters',
            'checkUpInfo',
            'totalDueAll',
            'totalDue',
            'amountBilled',
            'amountPaid',
            'previousChikitsa'
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

        DB::transaction(function () use ($request, $followup, $updatedCheckUpInfo) {
            $followup->update([
                'check_up_info' => json_encode($updatedCheckUpInfo),
                'diagnosis' => $request->diagnosis,
                'treatment' => $request->treatment,
                'amount_billed' => $request->amount_billed,
            ]);

            $payment = Payment::where('follow_up_id', $followup->id)->first();
            if ($payment) {
                if ($request->amount_paid <= 0) {
                    $payment->delete();
                } else {
                    $payment->update([
                        'amount' => $request->amount_paid,
                        'payment_method' => strtolower(trim($updatedCheckUpInfo['payment_method'] ?? 'cash')),
                        'paid_at' => $followup->created_at,
                        'branch_id' => $updatedCheckUpInfo['branch_id'] ?? null,
                        'branch_name' => $updatedCheckUpInfo['branch_name'] ?? null,
                    ]);
                }
            } else {
                if ($request->amount_paid > 0) {
                    Payment::create([
                        'patient_id' => $followup->patient_id,
                        'follow_up_id' => $followup->id,
                        'amount' => $request->amount_paid,
                        'payment_method' => strtolower(trim($updatedCheckUpInfo['payment_method'] ?? 'cash')),
                        'paid_at' => $followup->created_at,
                        'status' => 'posted',
                        'source' => 'manual',
                        'received_by' => Auth::id(),
                        'branch_id' => $updatedCheckUpInfo['branch_id'] ?? null,
                        'branch_name' => $updatedCheckUpInfo['branch_name'] ?? null,
                    ]);
                }
            }
        });

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

        $export = new FollowUpExport($request);
        $collection = $export->collection();

        return response()->streamDownload(function () use ($export, $collection) {
            $handle = fopen('php://output', 'w');
            // UTF-8 BOM for Excel / Marathi compatibility
            fputs($handle, "\xEF\xBB\xBF");
            fputcsv($handle, $export->headings());

            foreach ($collection as $item) {
                fputcsv($handle, $export->map($item));
            }
            fclose($handle);
        }, 'ledger_' . now()->format('Y_m_d_His') . '.csv', [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }
}
