<?php

use App\Models\User;

beforeEach(function () {
    $this->seed(\Database\Seeders\RoleSeeder::class);
    $this->user = User::factory()->create();
    $this->user->assignRoles(['admin']);
});

test('profile page is displayed', function () {
    $response = $this
        ->actingAs($this->user)
        ->get('/profile');

    $response->assertOk();
});

test('profile information can be updated', function () {
    $response = $this
        ->actingAs($this->user)
        ->patch('/profile', [
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

    $response
        ->assertSessionHasNoErrors()
        ->assertRedirect('/profile');

    $this->user->refresh();

    $this->assertSame('Test User', $this->user->name);
    $this->assertSame('test@example.com', $this->user->email);
    $this->assertNull($this->user->email_verified_at);
});

test('email verification status is unchanged when the email address is unchanged', function () {
    $response = $this
        ->actingAs($this->user)
        ->patch('/profile', [
            'name' => 'Test User',
            'email' => $this->user->email,
        ]);

    $response
        ->assertSessionHasNoErrors()
        ->assertRedirect('/profile');

    $this->assertNotNull($this->user->refresh()->email_verified_at);
});

test('user can delete their account', function () {
    $response = $this
        ->actingAs($this->user)
        ->delete('/profile', [
            'password' => 'password',
        ]);

    $response
        ->assertSessionHasNoErrors()
        ->assertRedirect('/');

    $this->assertGuest();
    $this->assertNotNull($this->user->fresh());
    $this->assertTrue($this->user->fresh()->trashed());
});

test('correct password must be provided to delete account', function () {
    $response = $this
        ->actingAs($this->user)
        ->from('/profile')
        ->delete('/profile', [
            'password' => 'wrong-password',
        ]);

    $response
        ->assertSessionHasErrorsIn('userDeletion', 'password')
        ->assertRedirect('/profile');

    $this->assertNotNull($this->user->fresh());
});
