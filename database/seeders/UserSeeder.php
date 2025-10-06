<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'admin@myclinic.com'], // <- condition to find existing user
            [ // <- fields to fill if user is not found
                'name' => 'Admin User',
                'email_verified_at' => now(),
                'password' => Hash::make('password'),
                'remember_token' => Str::random(10),
            ]
        );

        // Assign admin role to the admin user
        $adminUser = User::where('email', 'admin@myclinic.com')->first();
        if ($adminUser) {
            $adminUser->assignRoles(['admin']);
        }

        User::firstOrCreate(
            ['email' => 'test@myclinic.com'],
            [
                'name' => 'Test User',
                'email_verified_at' => now(),
                'password' => Hash::make('password'),
                'remember_token' => Str::random(10),
            ]
        );
    }
}
