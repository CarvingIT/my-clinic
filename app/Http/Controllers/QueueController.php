<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use App\Models\Queue;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class QueueController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'staff']);
    }

    public function listPatients()
    {
        $patients = Patient::all();
        return view('patients.index', compact('patients'));
    }

    public function addToQueue(Request $request, Patient $patient)
    {
        $request->validate([
            'in_queue_at' => 'nullable|date',
        ]);

        $inQueueAt = $request->input('in_queue_at') ? \Carbon\Carbon::parse($request->input('in_queue_at')) : now();

        Queue::create([
            'patient_id' => $patient->id,
            'in_queue_at' => $inQueueAt,
            'added_by' => Auth::id(),
        ]);

        return redirect()->route('patients.index')->with('success', 'Patient added to queue.');
    }

    public function showQueue()
    {
        $queue = Queue::with('patient', 'addedBy')->orderBy('in_queue_at')->get();
        return view('queue.index', compact('queue'));
    }

    public function removeFromQueue(Queue $queue)
    {
        $queue->delete();
        return redirect()->route('queue.index')->with('success', 'Patient removed from queue.');
    }

    public function markIn(Queue $queue)
    {
        $patientId = $queue->patient_id;
        $queue->delete();
        return redirect()->route('patients.show', $patientId)->with('success', 'Patient marked as in.');
    }
}
