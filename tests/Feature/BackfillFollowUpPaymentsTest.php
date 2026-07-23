<?php

use App\Models\User;
use App\Models\Patient;
use App\Models\FollowUp;
use App\Models\Payment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->user->assignRoles(['admin']);
});

it('can backfill legacy follow-up payments successfully and resolve via accessor', function () {
    $patient = Patient::create([
        'name' => 'Legacy Patient',
        'gender' => 'Male',
        'mobile_phone' => '1112223333',
        'address' => 'Legacy Address',
    ]);

    // Insert legacy follow-up directly into database using DB facade to bypass Model fillable/accessors
    $followUpId = DB::table('follow_ups')->insertGetId([
        'patient_id' => $patient->id,
        'doctor_id' => $this->user->id,
        'amount_billed' => 500.00,
        'amount_paid' => 350.00, // Legacy paid amount
        'check_up_info' => '{}',
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    // Retrieve the FollowUp model
    $followUp = FollowUp::find($followUpId);

    // Assert that getRawOriginal retrieves the original database value
    expect($followUp->getRawOriginal('amount_paid'))->toEqual(350.00);

    // Run the artisan command
    $this->artisan('MC:BackfillFollowUpPayments')
        ->expectsOutput('Running backfill...')
        ->expectsOutput('Processed: 1')
        ->expectsOutput('Inserted: 1')
        ->expectsOutput('Skipped (already exists): 0')
        ->assertExitCode(0);

    // Assert payment was created in the database
    $this->assertDatabaseHas('payments', [
        'patient_id' => $patient->id,
        'follow_up_id' => $followUpId,
        'amount' => 350.00,
        'source' => 'followup_legacy',
    ]);

    // Clear the cache of the model or retrieve a fresh instance to test the accessor
    $freshFollowUp = FollowUp::find($followUpId);
    
    // The accessor should now dynamically compute 350.00 using the payments table
    expect($freshFollowUp->amount_paid)->toEqual(350.00);
});
