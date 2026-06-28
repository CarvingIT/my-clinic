<?php

namespace App\Http\Controllers;

use App\Models\FollowUp;
use App\Models\Patient;
use App\Models\Payment;
use App\Models\PatientGroup;
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
        if ($request->input('tab') === 'groups') {
            $groupQuery = PatientGroup::with('members:id,name,patient_id,mobile_phone,patient_group_id');
            
            if ($request->filled('search')) {
                $search = $request->input('search');
                $groupQuery->where('name', 'like', "%{$search}%")
                           ->orWhere('description', 'like', "%{$search}%");
            }
            
            $groups = $groupQuery->latest()->paginate(20);
            $groups->appends($request->query());
            
            return view('payments.index', compact('groups'));
        }

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
        $selectedPatient = null;
        $followUps = collect();

        if ($selectedPatientId) {
            $selectedPatient = Patient::find($selectedPatientId);
            if ($selectedPatient) {
                $followUps = FollowUp::where('patient_id', $selectedPatientId)
                    ->latest('created_at')
                    ->get(['id', 'created_at']);
            } else {
                $selectedPatientId = null;
            }
        }

        return view('payments.create', compact('followUps', 'selectedPatientId', 'selectedPatient'));
    }

    public function searchPatients(Request $request)
    {
        $searchTerm = trim((string) $request->input('q', ''));

        if ($searchTerm === '') {
            return response()->json([]);
        }

        $searchTerms = array_filter(explode(' ', $searchTerm));

        $patients = Patient::query()
            ->where(function ($q) use ($searchTerms, $searchTerm) {
                if (!empty($searchTerms)) {
                    // For name, require all search terms to be present
                    $q->where(function ($nameQ) use ($searchTerms) {
                        foreach ($searchTerms as $t) {
                            $nameQ->where('name', 'like', "%{$t}%");
                        }
                    });
                }
                // For mobile and patient_id, match the full search term
                $q->orWhere('mobile_phone', 'like', "%{$searchTerm}%")
                  ->orWhere('patient_id', 'like', "%{$searchTerm}%");
            })
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

    public function groupMembersByPatient(Request $request)
    {
        $data = $request->validate([
            'patient_id' => ['required', 'exists:patients,id'],
        ]);

        $patient = Patient::findOrFail($data['patient_id']);
        
        if (!$patient->patient_group_id) {
            return response()->json(null);
        }

        $group = PatientGroup::with('members')->findOrFail($patient->patient_group_id);

        $members = $group->members->map(function ($member) {
            $totalBilled = $member->followUps()->sum('amount_billed');
            $totalPaid = $member->followUps()->sum('amount_paid');
            $standalonePaid = Payment::where('patient_id', $member->id)
                ->whereNull('follow_up_id')
                ->where('source', 'manual')
                ->where('status', 'posted')
                ->sum('amount');
            $due = ($totalBilled - $totalPaid) - $standalonePaid;

            return [
                'id' => $member->id,
                'name' => $member->name,
                'patient_id' => $member->patient_id,
                'mobile_phone' => $member->mobile_phone,
                'due' => $due,
            ];
        });

        return response()->json([
            'group_name' => $group->name,
            'members' => $members,
        ]);
    }

    public function store(Request $request)
    {
        // Handle Group/Family Payment
        if ($request->has('group_amounts') && is_array($request->input('group_amounts'))) {
            // Filter to find checked/selected members and validate their amounts
            $groupAmounts = [];
            if ($request->has('group_members') && is_array($request->input('group_members'))) {
                foreach ($request->input('group_members') as $memberId) {
                    $amount = $request->input("group_amounts.{$memberId}");
                    if (is_numeric($amount) && $amount > 0) {
                        $groupAmounts[$memberId] = $amount;
                    }
                }
            }

            if (empty($groupAmounts)) {
                return back()->withErrors(['amount' => 'At least one group member must have a valid payment amount.'])->withInput();
            }

            $validated = $request->validate([
                'patient_id' => ['required', 'exists:patients,id'],
                'follow_up_id' => ['nullable', 'exists:follow_ups,id'],
                'payment_method' => ['required', 'string', 'max:50'],
                'paid_at' => ['required', 'date'],
                'reference_no' => ['nullable', 'string', 'max:255'],
                'notes' => ['nullable', 'string'],
            ]);

            $primaryPatient = Patient::findOrFail($validated['patient_id']);
            $payerName = $primaryPatient->name;

            foreach ($groupAmounts as $pId => $amount) {
                // Link follow-up only for the primary patient
                $linkedFollowUpId = ((int)$pId === (int)$validated['patient_id']) ? ($validated['follow_up_id'] ?? null) : null;
                
                // Add a note referencing the group payment
                $note = trim($validated['notes'] ?? '');
                $groupNote = "Group payment. Paid by {$payerName} (" . $primaryPatient->patient_id . ")";
                $fullNote = $note !== '' ? $note . " | " . $groupNote : $groupNote;

                Payment::create([
                    'patient_id' => $pId,
                    'follow_up_id' => $linkedFollowUpId,
                    'received_by' => Auth::id(),
                    'amount' => $amount,
                    'payment_method' => strtolower(trim($validated['payment_method'])),
                    'paid_at' => $validated['paid_at'],
                    'status' => 'posted',
                    'reference_no' => $validated['reference_no'] ?? null,
                    'notes' => $fullNote,
                    'branch_id' => session('branch_id'),
                    'branch_name' => session('branch_name'),
                    'source' => 'manual',
                ]);
            }

            return redirect()->route('payments.index')->with('success', 'Group payment recorded successfully.');
        }

        // Original flow for single-patient payment
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
