<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;

class test extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:test';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $user = User::find(2);
        //$user->assignRoles(['admin','staff']);     

        $roles = $user->roles();
        foreach($roles as $r){
            echo $r->name."\n";
        }
    }
}
