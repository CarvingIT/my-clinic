<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use App\Models\Queue;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class QueueController extends Controller
{
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

        try{
        Queue::create([
            'patient_id' => $patient->id,
            'in_queue_at' => $inQueueAt,
            'added_by' => Auth::id(),
        ]);
        }
        catch(\Exception $e){
            if($request->wantsJson()){
                return ['status'=>0, 'message'=>'Patient was not added to the queue. Already in the queue ?'];
            }
            return redirect()->route('patients.index')->with('failure', 'Patient not added to queue.');
        }

        if($request->wantsJson()){
            return ['status'=>1, 'message'=>'Patient added to queue.'];
        }
        return redirect()->route('patients.index')->with('success', 'Patient added to queue.');
    }

    public function showQueue(Request $request)
    {
        $queue = Queue::with('patient', 'addedBy')->orderBy('in_queue_at')->get();
        if($request->wantsJson()){
            return $queue;
        }
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
