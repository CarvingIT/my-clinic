<?php

namespace App\Http\Controllers;

use App\Models\FollowUp;
use App\Models\Patient;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PaymentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $query = Payment::with(['patient:id,name,patient_id,mobile_phone', 'followUp:id', 'receiver:id,name'])
            ->where('status', 'posted');

        if ($request->filled('patient_id')) {
            $query->where('patient_id', $request->input('patient_id'));
        }

        if ($request->filled('payment_method') && $request->input('payment_method') !== 'all') {
            $query->where('payment_method', $request->input('payment_method'));
        }

        if ($request->filled('from_date')) {
            $query->whereDate('paid_at', '>=', $request->input('from_date'));
        }

        if ($request->filled('to_date')) {
            $query->whereDate('paid_at', '<=', $request->input('to_date'));
        }

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->whereHas('patient', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('mobile_phone', 'like', "%{$search}%")
                    ->orWhere('patient_id', 'like', "%{$search}%");
            });
        }

        $payments = (clone $query)->latest('paid_at')->paginate(20);
        $payments->appends($request->query());
        $totalAmount = (clone $query)->sum('amount');

        return view('payments.index', compact('payments', 'totalAmount'));
    }

    public function create(Request $request)
    {
        $selectedPatientId = $request->input('patient_id');
        $followUps = collect();

        if ($selectedPatientId) {
            $followUps = FollowUp::where('patient_id', $selectedPatientId)
                ->latest('created_at')
                ->get(['id', 'created_at']);
        }

        return view('payments.create', compact('followUps', 'selectedPatientId'));
    }

    public function searchPatients(Request $request)
    {
        $term = trim((string) $request->input('q', ''));

        if ($term === '') {
            return response()->json([]);
        }

        $patients = Patient::query()
            ->where('name', 'like', "%{$term}%")
            ->orWhere('mobile_phone', 'like', "%{$term}%")
            ->orWhere('patient_id', 'like', "%{$term}%")
            ->orderBy('name')
            ->limit(20)
            ->get(['id', 'name', 'patient_id', 'mobile_phone']);

        return response()->json($patients);
    }

    public function followUpsByPatient(Request $request)
    {
        $data = $request->validate([
            'patient_id' => ['required', 'exists:patients,id'],
        ]);

        $followUps = FollowUp::where('patient_id', $data['patient_id'])
            ->latest('created_at')
            ->get(['id', 'created_at']);

        return response()->json($followUps);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'patient_id' => ['required', 'exists:patients,id'],
            'follow_up_id' => ['nullable', 'exists:follow_ups,id'],
            'amount' => ['required', 'numeric', 'gt:0'],
            'payment_method' => ['required', 'string', 'max:50'],
            'paid_at' => ['required', 'date'],
            'reference_no' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
        ]);

        if (!empty($validated['follow_up_id'])) {
            $followUp = FollowUp::findOrFail($validated['follow_up_id']);
            if ((int) $followUp->patient_id !== (int) $validated['patient_id']) {
                return back()->withErrors(['follow_up_id' => 'Selected follow-up does not belong to the patient.'])->withInput();
            }
        }

        Payment::create([
            'patient_id' => $validated['patient_id'],
            'follow_up_id' => $validated['follow_up_id'] ?? null,
            'received_by' => Auth::id(),
            'amount' => $validated['amount'],
            'payment_method' => strtolower(trim($validated['payment_method'])),
            'paid_at' => $validated['paid_at'],
            'status' => 'posted',
            'reference_no' => $validated['reference_no'] ?? null,
            'notes' => $validated['notes'] ?? null,
            'branch_id' => session('branch_id'),
            'branch_name' => session('branch_name'),
            'source' => 'manual',
        ]);

        return redirect()->route('payments.index')->with('success', 'Payment recorded successfully.');
    }

    public function edit(Payment $payment)
    {
        $patients = Patient::orderBy('name')->get(['id', 'name', 'patient_id', 'mobile_phone']);

        $followUps = FollowUp::where('patient_id', $payment->patient_id)
            ->latest('created_at')
            ->get(['id', 'created_at']);

        return view('payments.edit', compact('payment', 'patients', 'followUps'));
    }

    public function update(Request $request, Payment $payment)
    {
        $validated = $request->validate([
            'patient_id' => ['required', 'exists:patients,id'],
            'follow_up_id' => ['nullable', 'exists:follow_ups,id'],
            'amount' => ['required', 'numeric', 'gt:0'],
            'payment_method' => ['required', 'string', 'max:50'],
            'paid_at' => ['required', 'date'],
            'reference_no' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
        ]);

        if (!empty($validated['follow_up_id'])) {
            $followUp = FollowUp::findOrFail($validated['follow_up_id']);
            if ((int) $followUp->patient_id !== (int) $validated['patient_id']) {
                return back()->withErrors(['follow_up_id' => 'Selected follow-up does not belong to the patient.'])->withInput();
            }
        }

        $payment->update([
            'patient_id' => $validated['patient_id'],
            'follow_up_id' => $validated['follow_up_id'] ?? null,
            'amount' => $validated['amount'],
            'payment_method' => strtolower(trim($validated['payment_method'])),
            'paid_at' => $validated['paid_at'],
            'reference_no' => $validated['reference_no'] ?? null,
            'notes' => $validated['notes'] ?? null,
        ]);

        return redirect()->route('payments.index')->with('success', 'Payment updated successfully.');
    }

    public function destroy(Payment $payment)
    {
        $payment->update([
            'status' => 'void',
            'notes' => trim(($payment->notes ? $payment->notes . ' | ' : '') . 'Voided by user #' . Auth::id() . ' at ' . now()->toDateTimeString()),
        ]);

        return redirect()->route('payments.index')->with('success', 'Payment voided successfully.');
    }
}
