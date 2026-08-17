<?php

namespace App\Http\Controllers;

use App\Models\Exemption;
use App\Models\Patient;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ExemptionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function store(Request $request, Patient $patient)
    {
        $request->validate([
            'amount' => ['required', 'numeric', 'gt:0'],
            'reason' => ['nullable', 'string', 'max:1000'],
            'exempted_at' => ['nullable', 'date'],
        ]);

        $exemptedAt = now();
        if ($request->filled('exempted_at')) {
            $parsed = Carbon::parse($request->exempted_at);
            if ($parsed->format('H:i:s') === '00:00:00') {
                $parsed->setTimeFrom(now());
            }
            $exemptedAt = $parsed;
        }

        Exemption::create([
            'patient_id' => $patient->id,
            'user_id' => auth()->id(),
            'amount' => $request->amount,
            'reason' => $request->reason,
            'exempted_at' => $exemptedAt,
        ]);

        return redirect()->back()->with('success', 'Amount exempted successfully.');
    }

    public function destroy(Exemption $exemption)
    {
        $exemption->delete();

        return redirect()->back()->with('success', 'Exemption removed successfully.');
    }
}
