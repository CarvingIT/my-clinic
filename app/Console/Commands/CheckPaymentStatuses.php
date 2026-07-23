<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CheckPaymentStatuses extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'MC:CheckPaymentStatuses';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check counts of payments grouped by status';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $statuses = DB::table('payments')
            ->select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->get();

        $rows = [];
        foreach ($statuses as $status) {
            $rows[] = [
                'status' => $status->status,
                'count' => $status->count,
            ];
            $this->line("Status: {$status->status}, Count: {$status->count}");
        }

        return self::SUCCESS;
    }
}
