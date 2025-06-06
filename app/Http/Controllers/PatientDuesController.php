<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use Illuminate\Http\Request;

class PatientDuesController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $query = Patient::query();

        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where('name', 'like', "%{$searchTerm}%")
                ->orWhere('mobile_phone', 'like', "%{$searchTerm}%")
                ->orWhere('patient_id', 'like', "%{$searchTerm}%");
        }

        $patients = $query->with('followUps')
            ->get()
            ->map(function ($patient) {
                $totalBilled = $patient->followUps()->sum('amount_billed');
                $totalPaid = $patient->followUps()->sum('amount_paid');
                $patient->total_due = $totalBilled - $totalPaid;

                $latestFollowUp = $patient->followUps()->latest('created_at')->first();
                $patient->last_follow_up_date = $latestFollowUp ? $latestFollowUp->created_at : null;
                return $patient;
            })
            ->filter(function ($patient) {
                return $patient->total_due != 0;
            })
            // ->sortBy('name');
            // ->sortByDesc(function ($patient) {
            // return $patient->last_follow_up_date ? $patient->last_follow_up_date->timestamp : 0;});
            ->sortByDesc('total_due');

        $perPage = 10;
        $currentPage = $request->input('page', 1);
        $paginatedPatients = new \Illuminate\Pagination\LengthAwarePaginator(
            $patients->forPage($currentPage, $perPage),
            $patients->count(),
            $perPage,
            $currentPage,
            ['path' => route('patient-dues.index')]
        );

        return view('patient-dues.index', compact('paginatedPatients'));
    }
}
