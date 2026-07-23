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

it('can detect legacy follow-ups with missing payments', function () {
    $patient = Patient::create([
        'name' => 'Test Patient',
        'gender' => 'Male',
        'mobile_phone' => '1234567890',
        'address' => 'Test Address',
    ]);

    // 1. Follow-up with missing payment
    $fuMissingId = DB::table('follow_ups')->insertGetId([
        'patient_id' => $patient->id,
        'doctor_id' => $this->user->id,
        'amount_billed' => 400.00,
        'amount_paid' => 200.00,
        'check_up_info' => '{}',
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    // 2. Follow-up with an existing payment
    $fuPaidId = DB::table('follow_ups')->insertGetId([
        'patient_id' => $patient->id,
        'doctor_id' => $this->user->id,
        'amount_billed' => 300.00,
        'amount_paid' => 300.00,
        'check_up_info' => '{}',
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    Payment::create([
        'patient_id' => $patient->id,
        'follow_up_id' => $fuPaidId,
        'amount' => 300.00,
        'status' => 'posted',
        'paid_at' => now(),
        'source' => 'followup_legacy',
    ]);

    // Run command
    $this->artisan('MC:CheckMissingPayments')
        ->expectsOutput('Total legacy follow-ups with amount_paid > 0 but no corresponding payments: 1')
        ->assertExitCode(0);
});
