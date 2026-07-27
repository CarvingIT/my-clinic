<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CheckMismatchedFollowups extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'MC:CheckMismatchedFollowups';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check for mismatched follow-ups where amount_paid doesn\'t match sum of payments';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $followUps = DB::table('follow_ups')->get();
        $mismatchCount = 0;

        foreach ($followUps as $fu) {
            $dbAmountPaid = (float)$fu->amount_paid;
            $paymentsSum = (float)DB::table('payments')
                ->where('follow_up_id', $fu->id)
                ->where('status', 'posted')
                ->sum('amount');

            if (abs($dbAmountPaid - $paymentsSum) > 0.01) {
                $mismatchCount++;
                if ($mismatchCount <= 20) {
                    $this->line("FollowUp ID: {$fu->id}, Patient ID: {$fu->patient_id}");
                    $this->line("  DB Column amount_paid: {$dbAmountPaid}");
                    $this->line("  Sum of Linked Payments: {$paymentsSum}");
                }
            }
        }

        $this->info("Total mismatched follow-ups: {$mismatchCount}");

        return self::SUCCESS;
    }
}
