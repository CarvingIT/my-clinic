<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Branch;

class BranchSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Branch::truncate();
        Branch::create(['name' => __('messages.Kothrud')]);
        Branch::create(['name' => __('messages.Paud Road')]);
        Branch::create(['name' => __('messages.Shukrawar Peth 1')]);
        Branch::create(['name' => __('messages.Shukrawar Peth 2')]);
        Branch::create(['name' => __('messages.Shukrawar Peth 3')]);
    }
}
