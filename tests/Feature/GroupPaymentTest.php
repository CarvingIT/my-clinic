<?php

use App\Models\User;
use App\Models\Patient;
use App\Models\PatientGroup;
use App\Models\FollowUp;
use App\Models\Payment;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->user->assignRoles(['admin']);
});

it('can list groups', function () {
    $group = PatientGroup::create(['name' => 'Test Family Group']);

    // Accessing groups index directly redirects to payments.index
    $response = $this->actingAs($this->user)->get(route('groups.index'));
    $response->assertRedirect(route('payments.index', ['tab' => 'groups']));

    // Accessing payments index with tab=groups displays the group
    $response2 = $this->actingAs($this->user)->get(route('payments.index', ['tab' => 'groups']));
    $response2->assertStatus(200);
    $response2->assertSee('Test Family Group');
});

it('can create a group and associate patients', function () {
    $patient1 = Patient::create([
        'name' => 'John Doe',
        'gender' => 'Male',
        'mobile_phone' => '1234567890',
        'address' => 'Test Address 1',
    ]);
    
    $patient2 = Patient::create([
        'name' => 'Jane Doe',
        'gender' => 'Female',
        'mobile_phone' => '0987654321',
        'address' => 'Test Address 2',
    ]);

    $response = $this->actingAs($this->user)->post(route('groups.store'), [
        'name' => 'Doe Family',
        'description' => 'A test family group',
        'patient_ids' => [$patient1->id, $patient2->id]
    ]);

    $response->assertRedirect(route('payments.index', ['tab' => 'groups']));
    $this->assertDatabaseHas('patient_groups', [
        'name' => 'Doe Family',
        'description' => 'A test family group',
    ]);

    $group = PatientGroup::where('name', 'Doe Family')->first();
    expect($patient1->refresh()->patient_group_id)->toBe($group->id);
    expect($patient2->refresh()->patient_group_id)->toBe($group->id);
});

it('can fetch group members and calculate their dues correctly via AJAX', function () {
    $group = PatientGroup::create(['name' => 'Doe Family']);
    
    $patient1 = Patient::create([
        'name' => 'John Doe',
        'gender' => 'Male',
        'mobile_phone' => '1234567890',
        'address' => 'Test Address 1',
        'patient_group_id' => $group->id
    ]);
    
    $patient2 = Patient::create([
        'name' => 'Jane Doe',
        'gender' => 'Female',
        'mobile_phone' => '0987654321',
        'address' => 'Test Address 2',
        'patient_group_id' => $group->id
    ]);

    // Create follow-up and billed amount for John
    $followUp = FollowUp::create([
        'patient_id' => $patient1->id,
        'doctor_id' => $this->user->id,
        'amount_billed' => 1000,
        'amount_paid' => 200,
        'check_up_info' => json_encode(['diagnosis' => 'fever']),
        'guid' => (string) \Illuminate\Support\Str::uuid(),
    ]);

    $response = $this->actingAs($this->user)
        ->getJson(route('payments.group-members', ['patient_id' => $patient1->id]));

    $response->assertStatus(200);
    $response->assertJsonPath('group_name', 'Doe Family');
    
    $members = $response->json('members');
    expect($members)->toHaveCount(2);

    $john = collect($members)->firstWhere('name', 'John Doe');
    $jane = collect($members)->firstWhere('name', 'Jane Doe');

    // Dues calculation: 1000 billed - 200 paid = 800 outstanding
    expect(floatval($john['due']))->toBe(800.0);
    expect(floatval($jane['due']))->toBe(0.0);
});

it('splits group payment into individual payment records on store', function () {
    $group = PatientGroup::create(['name' => 'Doe Family']);
    
    $patient1 = Patient::create([
        'name' => 'John Doe',
        'gender' => 'Male',
        'mobile_phone' => '1234567890',
        'address' => 'Test Address 1',
        'patient_group_id' => $group->id
    ]);
    
    $patient2 = Patient::create([
        'name' => 'Jane Doe',
        'gender' => 'Female',
        'mobile_phone' => '0987654321',
        'address' => 'Test Address 2',
        'patient_group_id' => $group->id
    ]);

    $response = $this->actingAs($this->user)->post(route('payments.store'), [
        'patient_id' => $patient1->id,
        'payment_method' => 'cash',
        'paid_at' => now()->format('Y-m-d H:i:s'),
        'notes' => 'Sponsor payment',
        'group_members' => [$patient1->id, $patient2->id],
        'group_amounts' => [
            $patient1->id => '500.00',
            $patient2->id => '300.00',
        ]
    ]);

    $response->assertRedirect(route('payments.index'));

    $this->assertDatabaseHas('payments', [
        'patient_id' => $patient1->id,
        'amount' => 500.00,
        'source' => 'manual',
    ]);

    $this->assertDatabaseHas('payments', [
        'patient_id' => $patient2->id,
        'amount' => 300.00,
        'source' => 'manual',
    ]);
});

it('pre-populates patient details when patient_id is passed to create', function () {
    $patient = Patient::create([
        'name' => 'Alice Doe',
        'gender' => 'Female',
        'mobile_phone' => '1112223333',
        'address' => 'Test Address 3',
    ]);

    $response = $this->actingAs($this->user)->get(route('payments.create', ['patient_id' => $patient->id]));
    $response->assertStatus(200);
    $response->assertViewHas('selectedPatient', function ($loadedPatient) use ($patient) {
        return $loadedPatient->id === $patient->id;
    });
});
