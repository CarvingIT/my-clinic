<?php

namespace App\Http\Controllers;

use App\Models\FollowUp;
use App\Models\Patient;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AnalyticsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        // Fetch unique branches from follow-ups
        $branches = FollowUp::selectRaw("JSON_UNQUOTE(JSON_EXTRACT(check_up_info, '$.branch_name')) as branch_name")
            ->whereNotNull(DB::raw("JSON_UNQUOTE(JSON_EXTRACT(check_up_info, '$.branch_name'))"))
            ->where(DB::raw("JSON_UNQUOTE(JSON_EXTRACT(check_up_info, '$.branch_name'))"), '!=', '')
            ->distinct()
            ->pluck('branch_name');

        // Default to "all"
        $selectedBranch = $request->input('branch_name', 'all');
        $selectedDoctor = $request->input('doctor', 'all');

        // Fetch doctors
        $doctors = User::all();

        // Base query for follow-ups
        $query = FollowUp::whereHas('patient');

        // Apply branch filter if a specific branch is selected
        if ($selectedBranch !== 'all' && !empty($selectedBranch)) {
            $query->whereRaw("JSON_UNQUOTE(JSON_EXTRACT(check_up_info, '$.branch_name')) = ?", [$selectedBranch]);
        }

        // Apply doctor filter if a specific doctor is selected
        if ($selectedDoctor !== 'all' && !empty($selectedDoctor)) {
            $query->where('doctor_id', $selectedDoctor);
        }

        // Apply date filter if provided
        if ($request->filled('from_date') && $request->filled('to_date')) {
            $from = Carbon::parse($request->from_date)->startOfDay();
            $to = Carbon::parse($request->to_date)->endOfDay();
            $query->whereBetween('created_at', [$from, $to]);
        }

        // Chart 1: Follow-Up Frequency (Daily)
        $followUpFrequencyDaily = FollowUp::selectRaw('DATE(created_at) as raw_date, DATE_FORMAT(created_at, "%d-%m-%y") as date, COUNT(*) as count')
            ->when($request->filled('from_date'), fn($q) => $q->whereDate('created_at', '>=', $request->from_date))
            ->when($request->filled('to_date'), fn($q) => $q->whereDate('created_at', '<=', $request->to_date))
            ->when($selectedBranch !== 'all' && !empty($selectedBranch), fn($q) => $q->whereRaw("JSON_UNQUOTE(JSON_EXTRACT(check_up_info, '$.branch_name')) = ?", [$selectedBranch]))
            ->when($selectedDoctor !== 'all' && !empty($selectedDoctor), fn($q) => $q->where('doctor_id', $selectedDoctor))
            ->groupBy('raw_date', 'date')
            ->orderBy('raw_date', 'asc')
            ->get();

        // Chart 2: Follow-Up Frequency (Monthly)
        $followUpFrequencyMonthly = FollowUp::selectRaw('DATE_FORMAT(created_at, "%m-%Y") as month, COUNT(*) as count')
            ->when($request->filled('from_date'), fn($q) => $q->whereDate('created_at', '>=', $request->from_date))
            ->when($request->filled('to_date'), fn($q) => $q->whereDate('created_at', '<=', $request->to_date))
            ->when($selectedBranch !== 'all' && !empty($selectedBranch), fn($q) => $q->whereRaw("JSON_UNQUOTE(JSON_EXTRACT(check_up_info, '$.branch_name')) = ?", [$selectedBranch]))
            ->when($selectedDoctor !== 'all' && !empty($selectedDoctor), fn($q) => $q->where('doctor_id', $selectedDoctor))
            ->groupBy('month')
            ->orderByRaw('MIN(created_at)')
            ->get();

        // Chart 3: Follow-Up Frequency (Yearly)
        $followUpFrequencyYearly = FollowUp::selectRaw('YEAR(created_at) as year, COUNT(*) as count')
            ->when($request->filled('from_date'), fn($q) => $q->whereDate('created_at', '>=', $request->from_date))
            ->when($request->filled('to_date'), fn($q) => $q->whereDate('created_at', '<=', $request->to_date))
            ->when($selectedBranch !== 'all' && !empty($selectedBranch), fn($q) => $q->whereRaw("JSON_UNQUOTE(JSON_EXTRACT(check_up_info, '$.branch_name')) = ?", [$selectedBranch]))
            ->when($selectedDoctor !== 'all' && !empty($selectedDoctor), fn($q) => $q->where('doctor_id', $selectedDoctor))
            ->groupBy('year')
            ->orderBy('year')
            ->get();

        // Chart 4: Age Distribution
        $ageDistribution = Patient::whereHas('followUps', function ($query) use ($request, $selectedBranch, $selectedDoctor) {
            $query->when($request->filled('from_date'), fn($q) => $q->whereDate('created_at', '>=', $request->from_date))
                ->when($request->filled('to_date'), fn($q) => $q->whereDate('created_at', '<=', $request->to_date))
                ->when($selectedBranch !== 'all' && !empty($selectedBranch), fn($q) => $q->whereRaw("JSON_UNQUOTE(JSON_EXTRACT(check_up_info, '$.branch_name')) = ?", [$selectedBranch]))
                ->when($selectedDoctor !== 'all' && !empty($selectedDoctor), fn($q) => $q->where('doctor_id', $selectedDoctor));
        })
            ->selectRaw('
                CASE
                    WHEN birthdate IS NULL THEN "Unknown"
                    WHEN TIMESTAMPDIFF(YEAR, birthdate, follow_ups.created_at) <= 18 THEN "0-18"
                    WHEN TIMESTAMPDIFF(YEAR, birthdate, follow_ups.created_at) <= 45 THEN "19-45"
                    ELSE "46+"
                END as age_group, COUNT(DISTINCT patients.id) as count')
            ->join('follow_ups', function ($join) {
                $join->on('patients.id', '=', 'follow_ups.patient_id')
                    ->where('follow_ups.created_at', function ($query) {
                        $query->selectRaw('MAX(created_at)')
                            ->from('follow_ups')
                            ->whereColumn('patient_id', 'patients.id');
                    });
            })
            ->groupBy('age_group')
            ->orderByRaw('FIELD(age_group, "0-18", "19-45", "46+", "Unknown")')
            ->get();

        // Chart 5: Payment Status
        $paymentStatus = FollowUp::selectRaw('DATE(created_at) as raw_date, DATE_FORMAT(created_at, "%d-%m-%y") as date, SUM(amount_billed) as billed, SUM(amount_paid) as paid, SUM(amount_billed - amount_paid) as due')
            ->when($request->filled('from_date'), fn($q) => $q->whereDate('created_at', '>=', $request->from_date))
            ->when($request->filled('to_date'), fn($q) => $q->whereDate('created_at', '<=', $request->to_date))
            ->when($selectedBranch !== 'all' && !empty($selectedBranch), fn($q) => $q->whereRaw("JSON_UNQUOTE(JSON_EXTRACT(check_up_info, '$.branch_name')) = ?", [$selectedBranch]))
            ->when($selectedDoctor !== 'all' && !empty($selectedDoctor), fn($q) => $q->where('doctor_id', $selectedDoctor))
            ->groupBy('raw_date', 'date')
            ->orderBy('raw_date', 'asc')
            ->get();

        // Chart 6: New vs. Existing Patients
        $newVsExistingPatients = FollowUp::whereHas('patient')
            ->when($request->filled('from_date'), fn($q) => $q->whereDate('created_at', '>=', $request->from_date))
            ->when($request->filled('to_date'), fn($q) => $q->whereDate('created_at', '<=', $request->to_date))
            ->when($selectedBranch !== 'all' && !empty($selectedBranch), fn($q) => $q->whereRaw("JSON_UNQUOTE(JSON_EXTRACT(check_up_info, '$.branch_name')) = ?", [$selectedBranch]))
            ->when($selectedDoctor !== 'all' && !empty($selectedDoctor), fn($q) => $q->where('doctor_id', $selectedDoctor))
            ->groupBy('patient_id')
            ->selectRaw('COUNT(*) as followup_count')
            ->get()
            ->reduce(function ($carry, $item) {
                $carry[$item->followup_count == 1 ? 'new' : 'existing']++;
                return $carry;
            }, ['new' => 0, 'existing' => 0]);

        return view('analytics.index', compact(
            'followUpFrequencyDaily',
            'followUpFrequencyMonthly',
            'followUpFrequencyYearly',
            'ageDistribution',
            'paymentStatus',
            'newVsExistingPatients',
            'branches',
            'selectedBranch',
            'doctors',
            'selectedDoctor'
        ));
    }
}
