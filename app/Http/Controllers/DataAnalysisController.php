<?php

namespace App\Http\Controllers;

use App\Models\FollowUp;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class DataAnalysisController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        // Initialize keywords array from session
        $keywords = $request->session()->get('analysis_keywords', []);

        // Add new keyword
        if ($request->has('add_keyword') && $request->filled('keyword')) {
            $newKeyword = trim($request->input('keyword'));
            if (!in_array($newKeyword, $keywords)) {
                $keywords[] = $newKeyword;
                $request->session()->put('analysis_keywords', $keywords);
            }
        }

        // Clear all keywords
        if ($request->has('clear_keywords')) {
            $keywords = [];
            $request->session()->put('analysis_keywords', $keywords);
        }

        // Remove a single keyword
        if ($request->has('remove_keyword')) {
            $removeKeyword = trim($request->input('remove_keyword'));
            $keywords = array_filter($keywords, fn($k) => strtolower($k) !== strtolower($removeKeyword));
            $request->session()->put('analysis_keywords', array_values($keywords)); // reindex
        }

        // Build the query
        $query = FollowUp::with(['patient', 'uploads'])
            ->whereHas('patient') // skip null patients
            ->orderBy('created_at', 'desc');

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->input('date_from'));
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->input('date_to'));
        }

        // Filter by gender
        if ($request->filled('gender')) {
            $gender = $request->input('gender');
            $query->whereHas('patient', function ($q) use ($gender) {
                $q->where('gender', $gender);
            });
        }

        // Filter by age group
        if ($request->filled('age_group')) {
            $ageGroup = $request->input('age_group');

            $query->whereHas('patient', function ($q) use ($ageGroup) {
                $q->whereNotNull('birthdate');

                if ($ageGroup === '80+') {
                    $q->whereRaw("TIMESTAMPDIFF(YEAR, birthdate, CURDATE()) >= 80");
                } else {
                    [$min, $max] = explode('-', $ageGroup);
                    $q->whereRaw("TIMESTAMPDIFF(YEAR, birthdate, CURDATE()) BETWEEN ? AND ?", [$min, $max - 1]);
                }
            });
        }

        // Filter by weight group
        if ($request->filled('weight_range')) {
            [$minWeight, $maxWeight] = explode('-', $request->input('weight_range'));
            $query->whereHas('patient', function ($q) use ($minWeight, $maxWeight) {
                $q->whereBetween('weight', [(int)$minWeight, (int)$maxWeight]);
            });
        }



        // Apply keyword search (OR)
        // if (!empty($keywords)) {
        //     $query->where(function ($q) use ($keywords) {
        //         foreach ($keywords as $keyword) {
        //             $q->orWhere(function ($subQuery) use ($keyword) {
        //                 $subQuery->whereHas('patient', function ($patientQuery) use ($keyword) {
        //                     $patientQuery->where('name', 'like', "%{$keyword}%")
        //                         ->orWhere('vishesh', 'like', "%{$keyword}%");
        //                 })
        //                 ->orWhere('diagnosis', 'like', "%{$keyword}%")
        //                 ->orWhere('treatment', 'like', "%{$keyword}%")
        //                 ->orWhereRaw("JSON_UNQUOTE(JSON_EXTRACT(check_up_info, '$.nadi')) LIKE ?", ["%{$keyword}%"])
        //                 ->orWhereRaw("JSON_UNQUOTE(JSON_EXTRACT(check_up_info, '$.chikitsa')) LIKE ?", ["%{$keyword}%"]);
        //             });
        //         }
        //     });
        // }

        // Apply keyword search (AND)
        if (!empty($keywords)) {
            foreach ($keywords as $keyword) {
                $query->where(function ($subQuery) use ($keyword) {
                    $subQuery->whereHas('patient', function ($patientQuery) use ($keyword) {
                        $patientQuery->where('name', 'like', "%{$keyword}%")
                            ->orWhere('vishesh', 'like', "%{$keyword}%")
                            ->orWhere('reference', 'like', "%{$keyword}%")
                            ->orWhere('height', 'like', "%{$keyword}%")
                            ->orWhere('weight', 'like', "%{$keyword}%");
                    })
                        ->orWhere('diagnosis', 'like', "%{$keyword}%")
                        ->orWhere('treatment', 'like', "%{$keyword}%")
                        ->orWhereRaw("JSON_UNQUOTE(JSON_EXTRACT(check_up_info, '$.nadi')) LIKE ?", ["%{$keyword}%"])
                        ->orWhereRaw("JSON_UNQUOTE(JSON_EXTRACT(check_up_info, '$.chikitsa')) LIKE ?", ["%{$keyword}%"])
                        ->orWhereRaw("JSON_UNQUOTE(JSON_EXTRACT(check_up_info, '$.nidan')) LIKE ?", ["%{$keyword}%"]);
                });
            }
        }


        // Count matches and paginate
        $matchCount = $query->count();
        $followUps = $query->paginate(10)->appends($request->except('page'));

        return view('analytics.data-analysis', compact('followUps', 'keywords', 'matchCount'));
    }
}
