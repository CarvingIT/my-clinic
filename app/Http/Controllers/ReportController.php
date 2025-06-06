<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Report;
use Illuminate\Support\Facades\Storage;

class ReportController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'doctor']);
    }

    public function store(Request $request)
    {

        $validated = $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'report' => 'required|array',
            'report.*' => 'file|mimes:pdf,jpg,jpeg,png|max:204800',
        ]);


        if ($request->hasFile('report')) {
            foreach ($request->file('report') as $file) {
                $path = $file->store('reports', 'public'); // Storage in the 'reports' directory
                Report::create([
                    'patient_id' => $request->patient_id,
                    'name' => $file->getClientOriginalName(),
                    'path' => $path,
                ]);
            }
        }
        return back()->with('success', 'Reports uploaded successfully.');
    }


    public function destroy(Report $report)
    {
        Storage::disk('public')->delete($report->path); // Delete from storage


        $report->delete();  // Delete from database record


        return back()->with('success', 'Report deleted successfully.');
    }
}
