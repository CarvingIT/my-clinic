<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
// use CarvingIT\LaravelUserRoles\App\Models\Role;
use Illuminate\Support\Facades\DB;

class AssignRole extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'MC:AssignRole {user-email-address} {role-name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Assign a role to a user by email address';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('user-email-address');
        $roleName = $this->argument('role-name');

        // Find the user by email
        $user = User::where('email', $email)->first();

        if (!$user) {
            $this->error("User with email {$email} not found.");
            return 1; // Exit with error code
        }

        // Find the role by name
        $role = DB::table('roles')->where('name', $roleName)->first();

        if (!$role) {
            $this->error("Role {$roleName} not found.");
            return 1; // Exit with error code
        }

        // Check if the user already has the role
        $roleExists = DB::table('user_roles')
            ->where('user_id', $user->id)
            ->where('role_id', $role->id)
            ->exists();

        if ($roleExists) {
            $this->info("User {$email} already has the role {$roleName}.");
            return 0; // Exit successfully
        }

        // Assign the role to the user
        DB::table('user_roles')->insert([
            'user_id' => $user->id,
            'role_id' => $role->id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->info("Role {$roleName} successfully assigned to user {$email}.");
        return 0; // Exit successfully
    }
}
