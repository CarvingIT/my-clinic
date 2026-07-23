<?php

use App\Models\User;
use App\Models\Patient;
use App\Models\Payment;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->user->assignRoles(['admin']);
});

it('can group and count payment statuses', function () {
    $patient = Patient::create([
        'name' => 'Test Patient',
        'gender' => 'Male',
        'mobile_phone' => '1234567890',
        'address' => 'Test Address',
    ]);

    Payment::create([
        'patient_id' => $patient->id,
        'amount' => 100.00,
        'status' => 'posted',
        'paid_at' => now(),
        'source' => 'followup_legacy',
    ]);

    Payment::create([
        'patient_id' => $patient->id,
        'amount' => 150.00,
        'status' => 'posted',
        'paid_at' => now(),
        'source' => 'followup_legacy',
    ]);

    Payment::create([
        'patient_id' => $patient->id,
        'amount' => 200.00,
        'status' => 'refunded',
        'paid_at' => now(),
        'source' => 'followup_legacy',
    ]);

    $this->artisan('MC:CheckPaymentStatuses')
        ->expectsOutput('Status: posted, Count: 2')
        ->expectsOutput('Status: refunded, Count: 1')
        ->assertExitCode(0);
});
