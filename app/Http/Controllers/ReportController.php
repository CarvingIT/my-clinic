<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Report;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller
{
    public function store(Request $request)
    {

        $validated = $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'report' => 'required|array',
            'report.*' => 'file|mimes:pdf,jpg,jpeg,png|max:204800',
        ]);


        if ($request->hasFile('report')) {
            foreach ($request->file('report') as $file) {

                // Generate a Unix timestamp + original filename
                $timestamp = time(); // Get the current Unix timestamp
                $originalName = $file->getClientOriginalName();
                $newFileName = $timestamp . '_' . $originalName;

                // Store the file in storage/app/private/reports
                $filePath = $file->storeAs('reports', $newFileName);

                 // Save in database as:
                Report::create([
                    'patient_id' => $request->patient_id,
                    'name' => $newFileName,
                    'path' => $filePath,
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

    public function download(Report $report): StreamedResponse
    {
        if (!Storage::exists($report->path)) {
            abort(404, 'File not found.');
        }

        return Storage::download($report->path, $report->name);
    }



    public function view(Report $report)
    {
        if (!Storage::exists($report->path)) {
            abort(404, 'File not found.');
        }

        // Get the file's MIME type
        $mimeType = Storage::mimeType($report->path);

        // Return a response to stream the file
        return response()->file(storage_path('app/private/' . $report->path), [
            'Content-Type' => $mimeType,
        ]);
    }
}
