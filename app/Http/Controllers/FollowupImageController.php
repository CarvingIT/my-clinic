<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;
use App\Models\FollowUp;
use App\Models\Patient;


class FollowupImageController extends Controller
{
    public function show($filename)
    {
        $followup = FollowUp::where('patient_photos', 'patient_photos/' . $filename)->firstOrFail();

        $path = 'patient_photos/' . $filename;

        if (!Storage::disk('local')->exists($path)) {
            abort(404);
        }

        $file = Storage::disk('local')->get($path);
        $type = Storage::disk('local')->mimeType($path);

        // return response($file, 200)->header('Content-Type', $type); //Alternative

        // Create a streamed response
        $response = new StreamedResponse(function () use ($path) {
            $stream = Storage::disk('local')->readStream($path);
            fpassthru($stream);
            if (is_resource($stream)) {
                fclose($stream);
            }
        });

        $response->headers->set('Content-Type', $type);
        $response->headers->set('Content-Disposition', 'inline; filename="' . $filename . '"');

        return $response;
    }


    public function showFollowUpImages($patientId)
    {
        $patient = Patient::findOrFail($patientId); // Get patient details

        // Get all images for this patient, ordered by date
        $patientPhotos = FollowUp::where('patient_id', $patientId)
            ->whereNotNull('patient_photos')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('patients.show', compact('patientPhotos', 'patient'));
    }
}
