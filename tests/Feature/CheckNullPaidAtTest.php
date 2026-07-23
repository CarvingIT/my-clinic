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

it('can detect payments with null paid_at', function () {
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

    $this->artisan('MC:CheckNullPaidAt')
        ->expectsOutput('Total Payments: 1')
        ->expectsOutput('Payments with null paid_at: 0')
        ->expectsOutput('Done')
        ->assertExitCode(0);

    $this->assertFileExists(base_path('check_output.txt'));
    @unlink(base_path('check_output.txt')); // Clean up file
});
