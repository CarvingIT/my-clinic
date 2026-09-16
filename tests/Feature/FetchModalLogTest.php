<?php

namespace Tests\Feature;

use App\Models\FollowUp;
use App\Models\Patient;
use App\Models\Payment;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FetchModalLogTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleSeeder::class);
        $this->user = User::factory()->create();
        $this->user->assignRoles(['admin']);
    }

    public function test_it_can_fetch_patients_modal_log()
    {
        $patient = Patient::create([
            'name' => 'John Doe',
            'gender' => 'Male',
            'mobile_phone' => '1234567890',
            'address' => 'Test Address',
        ]);

        FollowUp::create([
            'patient_id' => $patient->id,
            'doctor_id' => $this->user->id,
            'amount_billed' => 500,
            'check_up_info' => json_encode(['user_name' => 'Dr. Smith', 'branch_name' => 'Main']),
        ]);

        $response = $this->actingAs($this->user)->getJson(route('followups.fetch-modal-log', [
            'log_type' => 'patients',
            'time_period' => 'all',
            'page' => 1,
            'per_page' => 10,
        ]));

        $response->assertOk();
        $response->assertJsonStructure([
            'success',
            'html',
            'hasMore',
            'page',
            'shownCount',
            'total',
            'count',
        ]);
        $this->assertEquals(1, $response->json('total'));
        $this->assertStringContainsString('John Doe', $response->json('html'));
    }

    public function test_it_can_fetch_followups_modal_log()
    {
        $patient = Patient::create([
            'name' => 'Jane Smith',
            'gender' => 'Female',
            'mobile_phone' => '9876543210',
            'address' => 'Test Address 2',
        ]);

        FollowUp::create([
            'patient_id' => $patient->id,
            'doctor_id' => $this->user->id,
            'amount_billed' => 1000,
            'check_up_info' => json_encode(['user_name' => 'Dr. Smith', 'branch_name' => 'Main']),
        ]);

        $response = $this->actingAs($this->user)->getJson(route('followups.fetch-modal-log', [
            'log_type' => 'followups',
            'time_period' => 'all',
            'page' => 1,
            'per_page' => 10,
        ]));

        $response->assertOk();
        $this->assertEquals(1, $response->json('total'));
        $this->assertStringContainsString('Jane Smith', $response->json('html'));
    }

    public function test_it_can_fetch_income_and_payment_modal_logs()
    {
        $patient = Patient::create([
            'name' => 'Bob Builder',
            'gender' => 'Male',
            'mobile_phone' => '5555555555',
            'address' => 'Test Address 3',
        ]);

        Payment::create([
            'patient_id' => $patient->id,
            'amount' => 500,
            'payment_method' => 'cash',
            'paid_at' => now(),
            'status' => 'posted',
            'source' => 'manual',
        ]);

        Payment::create([
            'patient_id' => $patient->id,
            'amount' => 300,
            'payment_method' => 'online',
            'paid_at' => now(),
            'status' => 'posted',
            'source' => 'manual',
        ]);

        $incomeRes = $this->actingAs($this->user)->getJson(route('followups.fetch-modal-log', [
            'log_type' => 'income',
            'time_period' => 'all',
        ]));
        $incomeRes->assertOk();
        $this->assertEquals(2, $incomeRes->json('total'));
        $this->assertStringContainsString('Bob Builder', $incomeRes->json('html'));

        $cashRes = $this->actingAs($this->user)->getJson(route('followups.fetch-modal-log', [
            'log_type' => 'cash',
            'time_period' => 'all',
        ]));
        $cashRes->assertOk();
        $this->assertEquals(1, $cashRes->json('total'));
        $this->assertStringContainsString('500', $cashRes->json('html'));

        $onlineRes = $this->actingAs($this->user)->getJson(route('followups.fetch-modal-log', [
            'log_type' => 'online',
            'time_period' => 'all',
        ]));
        $onlineRes->assertOk();
        $this->assertEquals(1, $onlineRes->json('total'));
        $this->assertStringContainsString('300', $onlineRes->json('html'));
    }
}
