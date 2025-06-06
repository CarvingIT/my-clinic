<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Upload;
use Illuminate\Support\Facades\Storage;

class UploadController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'doctor']);
    }

    public function store(Request $request)
    {
        $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'follow_up_id' => 'nullable|exists:follow_ups,id',
            'file' => 'required|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'photo_type' => 'required|string',
        ]);

        // Store the file
        $filePath = $request->file('file')->store('uploads');

        // Save the record in DB
        $upload = Upload::create([
            'patient_id' => $request->patient_id,
            'follow_up_id' => $request->follow_up_id,
            'photo_type' => $request->photo_type,
            'file_path' => $filePath,
        ]);

        return response()->json(['message' => 'File uploaded successfully!', 'data' => $upload], 201);
    }

    public function destroy($id)
    {
        $upload = Upload::findOrFail($id);

        // Delete the file from private storage
        Storage::delete($upload->file_path);
        // Delete the record from DB
        $upload->delete();

        return response()->json(['message' => 'File deleted successfully!'], 200);
    }

    // Serve private files through a controller method
    public function show($id)
    {
        $upload = Upload::findOrFail($id);

        // Ensure the file exists
        if (!Storage::exists($upload->file_path)) {
            abort(404);
        }

        // Serve the file as a response
        return response()->file(storage_path('app/private/' . $upload->file_path));
    }
}
