<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use CarvingIT\LaravelUserRoles\App\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // add sample roles 
        Role::create(
            [
                'name'=>'admin'
            ]
        );
        Role::create(
            [
                'name'=>'staff'
            ]
        );
    }
}

