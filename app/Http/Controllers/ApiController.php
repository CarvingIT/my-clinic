<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Patient;
use App\Models\FollowUp;
use Illuminate\Support\Facades\DB;

class ApiController extends Controller
{
    /**
     * Handle API login for sync
     */
    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        // Find user by email
        $user = User::where('email', $request->username)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['error' => 'Invalid credentials'], 401);
        }

        // Create token using Sanctum
        $token = $user->createToken('sync-api')->plainTextToken;

        return response()->json(['token' => $token]);
    }

    /**
     * Export patients and follow-ups updated on or after the given date
     */
    public function export(Request $request)
    {
        $request->validate([
            'date' => 'required|date',
        ]);

        $date = $request->date;

        // Get patients that have been updated recently OR have follow-ups updated recently
        $patients = Patient::with('followUps')  // Include ALL follow-ups for these patients
        ->where(function ($query) use ($date) {
            $query->where('updated_at', '>=', $date . ' 00:00:00')
                  ->orWhereHas('followUps', function ($subQuery) use ($date) {
                      $subQuery->where('updated_at', '>=', $date . ' 00:00:00');
                  });
        })
        ->get()
        ->map(function ($patient) {
            return [
                'guid' => $patient->guid,
                'name' => $patient->name,
                'email_id' => $patient->email_id,
                'mobile_phone' => $patient->mobile_phone,
                'address' => $patient->address,
                'birthdate' => $patient->birthdate,
                'gender' => $patient->gender,
                'patient_id' => $patient->patient_id,
                'vishesh' => $patient->vishesh,
                'height' => $patient->height,
                'weight' => $patient->weight,
                'occupation' => $patient->occupation,
                'reference' => $patient->reference,
                'created_at' => $patient->created_at->toDateTimeString(),
                'updated_at' => $patient->updated_at->toDateTimeString(),
                'follow_ups' => $patient->followUps->map(function ($followUp) {
                    return [
                        'check_up_info' => $followUp->check_up_info,
                        'diagnosis' => $followUp->diagnosis,
                        'treatment' => $followUp->treatment,
                        'amount_billed' => $followUp->amount_billed,
                        'amount_paid' => $followUp->amount_paid,
                        'doctor_id' => $followUp->doctor_id,
                        'created_at' => $followUp->created_at->toDateTimeString(),
                        'updated_at' => $followUp->updated_at->toDateTimeString(),
                    ];
                })->toArray(),
            ];
        });

        return response()->json($patients);
    }
}
