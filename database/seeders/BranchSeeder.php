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
        Branch::create(['name' => __('messages.Paud Road')]);
        Branch::create(['name' => __('messages.Shukrawar Peth')]);
        Branch::create(['name' => __('messages.Kothrud')]);
    }
}
