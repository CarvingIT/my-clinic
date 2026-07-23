<?php

use App\Models\User;
use App\Models\Patient;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->user->assignRoles(['admin']);
});

it('can run toBase test command successfully', function () {
    $patient = Patient::create([
        'name' => 'Test Patient',
        'gender' => 'Female',
        'mobile_phone' => '9876543210',
        'address' => 'Test Address',
    ]);

    $followUpId = DB::table('follow_ups')->insertGetId([
        'patient_id' => $patient->id,
        'doctor_id' => $this->user->id,
        'amount_billed' => 500.00,
        'amount_paid' => 300.00,
        'check_up_info' => '{}',
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $this->artisan("MC:TestToBase {$patient->id}")
        ->expectsOutput("Testing for Patient ID: {$patient->id}")
        ->expectsOutput("Type of followUp: App\Models\FollowUp")
        ->assertExitCode(0);
});
