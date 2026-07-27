<?php

use App\Models\User;
use App\Models\Patient;
use App\Models\Payment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->user->assignRoles(['admin']);
});

it('can detect mismatched follow-ups where amount_paid doesn\'t match sum of payments', function () {
    $patient = Patient::create([
        'name' => 'Mismatched Patient',
        'gender' => 'Male',
        'mobile_phone' => '9876543210',
        'address' => 'Test Address',
    ]);

    // 1. Matched follow-up (amount_paid: 200, payment: 200)
    $fuMatchedId = DB::table('follow_ups')->insertGetId([
        'patient_id' => $patient->id,
        'doctor_id' => $this->user->id,
        'amount_billed' => 500.00,
        'amount_paid' => 200.00,
        'check_up_info' => '{}',
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    Payment::create([
        'patient_id' => $patient->id,
        'follow_up_id' => $fuMatchedId,
        'amount' => 200.00,
        'status' => 'posted',
        'paid_at' => now(),
        'source' => 'followup_legacy',
    ]);

    // 2. Mismatched follow-up (amount_paid: 300, payment: 150)
    $fuMismatchedId = DB::table('follow_ups')->insertGetId([
        'patient_id' => $patient->id,
        'doctor_id' => $this->user->id,
        'amount_billed' => 500.00,
        'amount_paid' => 300.00,
        'check_up_info' => '{}',
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    Payment::create([
        'patient_id' => $patient->id,
        'follow_up_id' => $fuMismatchedId,
        'amount' => 150.00,
        'status' => 'posted',
        'paid_at' => now(),
        'source' => 'followup_legacy',
    ]);

    $this->artisan('MC:CheckMismatchedFollowups')
        ->expectsOutput("FollowUp ID: {$fuMismatchedId}, Patient ID: {$patient->id}")
        ->expectsOutput("  DB Column amount_paid: 300")
        ->expectsOutput("  Sum of Linked Payments: 150")
        ->expectsOutput('Total mismatched follow-ups: 1')
        ->assertExitCode(0);
});
