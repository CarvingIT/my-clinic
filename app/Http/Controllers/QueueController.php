<?php

namespace App\Http\Controllers;
use App\Models\Queue;
use App\Models\Patient;

use Illuminate\Http\Request;

class QueueController extends Controller
{
    // Show queue list
    public function index()
    {
        $queues = Queue::with('patient')->orderBy('in_queue_at')->get();
        return view('queue.index', compact('queues'));
    }

    // Add patient to queue
    public function store(Request $request)
    {
        $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'in_queue_at' => 'nullable|date_format:Y-m-d H:i:s',
        ]);

        Queue::create([
            'patient_id' => $request->patient_id,
            'in_queue_at' => $request->in_queue_at ?? now(),
        ]);

        return redirect()->back()->with('success', 'Patient added to queue.');
    }

    // Remove patient from queue
    public function destroy($id)
    {
        Queue::findOrFail($id)->delete();
        return redirect()->back()->with('success', 'Patient removed from queue.');
    }

    // Mark patient as “In” and remove from queue
    public function markIn($id)
    {
        $queue = Queue::findOrFail($id);
        $patientId = $queue->patient_id;
        $queue->delete();

        return redirect()->route('patients.show', $patientId)->with('success', 'Patient marked as In.');
    }
}
